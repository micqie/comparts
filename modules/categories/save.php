<?php
// Handle create / update category

require_once __DIR__ . '/../../db.php';

$id             = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$category_name  = trim($_POST['category_name'] ?? '');
$description    = trim($_POST['description'] ?? '');

// Validation
if (empty($category_name)) {
    header('Location: index.php?module=categories&action=list&error=' . urlencode('Category name is required'));
    exit;
}

if (strlen($category_name) > 100) {
    header('Location: index.php?module=categories&action=list&error=' . urlencode('Category name must be 100 characters or less'));
    exit;
}

// Check if category name already exists (excluding current category if editing)
if ($id > 0) {
    $checkStmt = mysqli_prepare($conn, "SELECT id FROM categories WHERE category_name = ? AND id != ?");
    mysqli_stmt_bind_param($checkStmt, 'si', $category_name, $id);
} else {
    $checkStmt = mysqli_prepare($conn, "SELECT id FROM categories WHERE category_name = ?");
    mysqli_stmt_bind_param($checkStmt, 's', $category_name);
}
mysqli_stmt_execute($checkStmt);
$result = mysqli_stmt_get_result($checkStmt);
if (mysqli_fetch_assoc($result)) {
    mysqli_stmt_close($checkStmt);
    header('Location: index.php?module=categories&action=list&error=' . urlencode('Category name already exists'));
    exit;
}
mysqli_stmt_close($checkStmt);

$success = false;
if ($id > 0) {
    // Update existing category
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE categories SET category_name = ?, description = ? WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'ssi', $category_name, $description, $id);
    if (mysqli_stmt_execute($stmt)) {
        $success = true;
    }
    mysqli_stmt_close($stmt);
} else {
    // Insert new category
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO categories (category_name, description) VALUES (?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'ss', $category_name, $description);
    if (mysqli_stmt_execute($stmt)) {
        $success = true;
    }
    mysqli_stmt_close($stmt);
}

if ($success) {
    $message = $id > 0 ? 'Category updated successfully!' : 'Category created successfully!';
    header('Location: index.php?module=categories&action=list&success=' . urlencode($message));
} else {
    header('Location: index.php?module=categories&action=list&error=' . urlencode('Failed to save category. Please try again.'));
}
exit;
