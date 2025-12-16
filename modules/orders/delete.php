<?php
// Delete order (does not restore stock quantities)

require_once __DIR__ . '/../../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Delete order details first because of FK constraint
    $stmt = mysqli_prepare($conn, "DELETE FROM order_details WHERE order_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn, "DELETE FROM orders WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header('Location: index.php?module=orders&action=list');
exit;


