<?php
// Create order form (multiple items)

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

// Load customers and products
$customers = mysqli_query($conn, "SELECT id, full_name FROM customers ORDER BY full_name");
$products  = mysqli_query($conn, "SELECT id, product_name, price, stock_quantity FROM products ORDER BY product_name");

// Build an array of products for JS
$productData = [];
mysqli_data_seek($products, 0);
while ($p = mysqli_fetch_assoc($products)) {
    $productData[$p['id']] = [
        'price' => (float)$p['price'],
        'stock' => (int)$p['stock_quantity'],
    ];
}
mysqli_data_seek($products, 0);
?>
<div class="page-header">
    <h2><i class="bi bi-cart-plus"></i> Create Order</h2>
    <a href="index.php?module=orders&action=list" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<form method="post" action="index.php?module=orders&action=save">
<div class="card">
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label">Customer</label>
            <select name="customer_id" class="form-select" required>
                <option value="">-- Select Customer --</option>
                <?php while ($c = mysqli_fetch_assoc($customers)) : ?>
                    <option value="<?php echo $c['id']; ?>">
                        <?php echo htmlspecialchars($c['full_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <h5>Items</h5>
        <table class="table table-bordered" id="items-table">
            <thead class="table-light">
            <tr>
                <th style="width: 40%;">Product</th>
                <th style="width: 15%;">Price</th>
                <th style="width: 15%;">Stock</th>
                <th style="width: 15%;">Quantity</th>
                <th style="width: 15%;">Line Total</th>
                <th style="width: 10%;"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <select name="product_id[]" class="form-select item-product" required>
                        <option value="">-- Select --</option>
                        <?php while ($p = mysqli_fetch_assoc($products)) : ?>
                            <option value="<?php echo $p['id']; ?>">
                                <?php echo htmlspecialchars($p['product_name']); ?>
                            </option>
                        <?php endwhile; ?>
                        <?php mysqli_data_seek($products, 0); ?>
                    </select>
                </td>
                <td><input type="text" class="form-control item-price" readonly></td>
                <td><input type="text" class="form-control item-stock" readonly></td>
                <td><input type="number" name="quantity[]" class="form-control item-qty" min="1" value="1" required></td>
                <td><input type="text" class="form-control item-total" readonly></td>
                <td><button type="button" class="btn btn-sm btn-danger btn-remove-row">&times;</button></td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="4" class="text-end fw-bold">Grand Total:</td>
                <td><input type="text" id="grand-total" class="form-control" readonly></td>
                <td></td>
            </tr>
            </tfoot>
        </table>

        <div class="mt-3">
            <button type="button" id="btn-add-row" class="btn btn-secondary">
                <i class="bi bi-plus-circle"></i> Add Item
            </button>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Save Order
        </button>
        <a href="index.php?module=orders&action=list" class="btn btn-secondary">Cancel</a>
    </div>
</div>
</form>

<script>
    (function () {
        const productData = <?php echo json_encode($productData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

        const tableBody = document.querySelector('#items-table tbody');
        const addRowBtn = document.getElementById('btn-add-row');
        const grandTotalInput = document.getElementById('grand-total');

        function recalcRow(row) {
            const productSelect = row.querySelector('.item-product');
            const priceInput = row.querySelector('.item-price');
            const stockInput = row.querySelector('.item-stock');
            const qtyInput = row.querySelector('.item-qty');
            const totalInput = row.querySelector('.item-total');

            const productId = productSelect.value;
            const qty = parseInt(qtyInput.value || '0', 10);

            if (productId && productData[productId]) {
                const price = productData[productId].price;
                const stock = productData[productId].stock;
                priceInput.value = price.toFixed(2);
                stockInput.value = stock.toString();
                const lineTotal = price * qty;
                totalInput.value = lineTotal.toFixed(2);
            } else {
                priceInput.value = '';
                stockInput.value = '';
                totalInput.value = '';
            }

            recalcGrandTotal();
        }

        function recalcGrandTotal() {
            let grand = 0;
            tableBody.querySelectorAll('tr').forEach(function (row) {
                const totalInput = row.querySelector('.item-total');
                const val = parseFloat(totalInput.value || '0');
                if (!isNaN(val)) {
                    grand += val;
                }
            });
            grandTotalInput.value = grand.toFixed(2);
        }

        function bindRowEvents(row) {
            row.querySelector('.item-product').addEventListener('change', function () {
                recalcRow(row);
            });
            row.querySelector('.item-qty').addEventListener('input', function () {
                recalcRow(row);
            });
            row.querySelector('.btn-remove-row').addEventListener('click', function () {
                if (tableBody.querySelectorAll('tr').length > 1) {
                    row.remove();
                    recalcGrandTotal();
                }
            });
        }

        // Initial row
        bindRowEvents(tableBody.querySelector('tr'));

        addRowBtn.addEventListener('click', function () {
            const firstRow = tableBody.querySelector('tr');
            const newRow = firstRow.cloneNode(true);

            newRow.querySelectorAll('input').forEach(function (input) {
                input.value = '';
                if (input.classList.contains('item-qty')) {
                    input.value = '1';
                }
            });
            newRow.querySelector('.item-product').value = '';

            tableBody.appendChild(newRow);
            bindRowEvents(newRow);
        });
    })();
</script>
