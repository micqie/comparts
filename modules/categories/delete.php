<?php
// Delete category

require_once __DIR__ . '/../../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php?module=categories&action=list&error=' . urlencode('Invalid category ID'));
    exit;
}

// Check if category exists
$checkStmt = mysqli_prepare($conn, "SELECT category_name FROM categories WHERE id = ?");
mysqli_stmt_bind_param($checkStmt, 'i', $id);
mysqli_stmt_execute($checkStmt);
$result = mysqli_stmt_get_result($checkStmt);
$category = mysqli_fetch_assoc($result);
mysqli_stmt_close($checkStmt);

if (!$category) {
    header('Location: index.php?module=categories&action=list&error=' . urlencode('Category not found'));
    exit;
}

// Check if category is being used by any products
$checkStmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM products WHERE category_id = ?");
mysqli_stmt_bind_param($checkStmt, 'i', $id);
mysqli_stmt_execute($checkStmt);
$result = mysqli_stmt_get_result($checkStmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($checkStmt);

if ($row['count'] > 0) {
    // Category is in use, redirect with error
    header('Location: index.php?module=categories&action=list&error=' . urlencode('Cannot delete category: It is being used by ' . $row['count'] . ' product(s).'));
    exit;
}

// Safe to delete
$stmt = mysqli_prepare($conn, "DELETE FROM categories WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
if (mysqli_stmt_execute($stmt)) {
    $categoryName = htmlspecialchars($category['category_name']);
    header('Location: index.php?module=categories&action=list&success=' . urlencode("Category '{$categoryName}' deleted successfully!"));
} else {
    header('Location: index.php?module=categories&action=list&error=' . urlencode('Failed to delete category. Please try again.'));
}
mysqli_stmt_close($stmt);
exit;
