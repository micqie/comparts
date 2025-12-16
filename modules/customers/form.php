<?php
// Create / edit customer form

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$customer = [
    'full_name'      => '',
    'email'          => '',
    'contact_number' => '',
    'address'        => '',
];

if ($id > 0) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, full_name, email, contact_number, address FROM customers WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $customer = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}
?>
<div class="container mt-4">
    <h2><?php echo $id > 0 ? 'Edit Customer' : 'Add Customer'; ?></h2>

    <form method="post" action="index.php?module=customers&action=save">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars((string)$id); ?>">

        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" required
                   value="<?php echo htmlspecialchars((string)($customer['full_name'] ?? '')); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required
                   value="<?php echo htmlspecialchars((string)($customer['email'] ?? '')); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Contact Number</label>
            <input type="text" name="contact_number" class="form-control"
                   value="<?php echo htmlspecialchars((string)($customer['contact_number'] ?? '')); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="3"><?php
                echo htmlspecialchars((string)($customer['address'] ?? ''));
            ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
        <a href="index.php?module=customers&action=list" class="btn btn-secondary">Cancel</a>
    </form>
</div>


