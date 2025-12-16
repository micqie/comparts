<?php
// Delete customer

require_once __DIR__ . '/../../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "DELETE FROM customers WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header('Location: index.php?module=customers&action=list');
exit;


