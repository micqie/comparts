<?php
// Logout handler
require_once __DIR__ . '/../../config/auth.php';

logoutUser();

header('Location: index.php?module=auth&action=login');
exit;

