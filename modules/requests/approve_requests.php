<?php
session_start();

require_once dirname(__DIR__, 2) . '/config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../dashboard.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    mysqli_query($conn,
        "UPDATE product_requests
         SET status = 'approved',
             viewed = 0
         WHERE request_id = '$id'"
    );
}

header("Location: view_requests.php");
exit();
?>