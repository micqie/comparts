<?php
// Delete category

require_once __DIR__ . '/../../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
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
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header('Location: index.php?module=categories&action=list');
exit;
