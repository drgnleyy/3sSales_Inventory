<?php
session_start();

require_once dirname(__DIR__, 2) . '/config/database.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../dashboard.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    mysqli_query($conn, "DELETE FROM product_requests WHERE request_id = '$id'");
}

header("Location: view_requests.php");
exit();