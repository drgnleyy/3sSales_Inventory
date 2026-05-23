<?php

session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';
if($_SESSION['role'] != 'admin'){
    header("Location: ../../dashboard.php");
    exit();
}
/* ADMIN ONLY */
if($_SESSION['role'] != 'admin'){
    header("Location: ../../dashboard.php");
    exit();
}
$id = $_GET['id'];

$query = "DELETE FROM products
          WHERE product_id='$id'";

mysqli_query($conn, $query);

header("Location: view_products.php");

?>