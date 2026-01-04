<?php
// List all categories

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

$result = mysqli_query(
    $conn,
    "SELECT id, category_name, description
     FROM categories
     ORDER BY category_name ASC"
);

$error = '';
$success = '';
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}
if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}
?>
<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<div class="page-header">
    <h2><i class="bi bi-tags"></i> Categories</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="openCategoryModal(0)">
        <i class="bi bi-plus-circle"></i> Add Category
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th style="width: 140px;">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['category_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['description'] ?? 'N/A'); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning"
                                        onclick="openCategoryModal(<?php echo $row['id']; ?>)">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <a href="index.php?module=categories&action=delete&id=<?php echo $row['id']; ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Delete this category? This will fail if products are using this category.');">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>No categories found. Add your first category!</p>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalTitle">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="index.php?module=categories&action=save" id="categoryForm" data-modal="categoryModal">
                <div class="modal-body">
                    <input type="hidden" name="id" id="category_id">

                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="category_name" id="category_name" class="form-control" required>
                        <small class="text-muted">Unique category name (e.g., CPU, GPU, RAM, Storage)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="3" placeholder="Optional description for this category"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCategoryModal(id) {
    const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
    const form = document.getElementById('categoryForm');
    const title = document.getElementById('categoryModalTitle');

    form.reset();
    document.getElementById('category_id').value = '';

    if (id > 0) {
        fetch(`index.php?module=categories&action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('category_id').value = data.id;
                document.getElementById('category_name').value = data.category_name;
                document.getElementById('description').value = data.description || '';
                title.textContent = 'Edit Category';
            })
            .catch(() => {
                window.location.href = `index.php?module=categories&action=list`;
            });
    } else {
        title.textContent = 'Add Category';
    }

    modal.show();
}
</script>
