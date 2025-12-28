<?php
// Get category data as JSON for modal
header('Content-Type: application/json');

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT id, category_name, description FROM categories WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $category = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($category) {
        echo json_encode($category);
        exit;
    }
}

echo json_encode(['error' => 'Category not found']);
