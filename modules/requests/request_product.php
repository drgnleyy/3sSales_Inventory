<?php

session_start();

require_once dirname(__DIR__, 2)
. '/config/database.php';

if($_SESSION['role'] != 'cashier'){
    header("Location: ../../dashboard.php");
    exit();
}

if(isset($_POST['send_request'])){

    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];

    $requested_by = $_SESSION['user_id'];

    mysqli_query($conn,

        "INSERT INTO product_requests
        (requested_by,
         product_name,
         category,
         quantity)

         VALUES

         ('$requested_by',
          '$product_name',
          '$category',
          '$quantity')"

    );

    header("Location: my_requests.php? success=1");
    exit();
}

?>

