<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false]);
    exit();
}

$request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($request_id <= 0 || !in_array($action, ['approved', 'rejected'])) {
    echo json_encode(['success' => false]);
    exit();
}

mysqli_query($conn,
    "UPDATE product_requests
     SET status = '$action',
         viewed = 0
     WHERE request_id = '$request_id'"
);

echo json_encode(['success' => true]);
exit();