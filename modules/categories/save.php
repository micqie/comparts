<?php
// Handle create / update category

require_once __DIR__ . '/../../db.php';

$id             = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$category_name  = trim($_POST['category_name'] ?? '');
$description    = trim($_POST['description'] ?? '');

if ($category_name === '') {
    header('Location: index.php?module=categories&action=list');
    exit;
}

if ($id > 0) {
    // Update existing category
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE categories SET category_name = ?, description = ? WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'ssi', $category_name, $description, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} else {
    // Insert new category
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO categories (category_name, description) VALUES (?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'ss', $category_name, $description);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header('Location: index.php?module=categories&action=list');
exit;
