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

    header("Location: request_product.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>

    <title>Request Product</title>

    <link rel="stylesheet"
          href="../../assets/css/style.css">

</head>
<body>

<div class="main-content">

    <div class="form-container">

        <h2>Request Product</h2>

        <form method="POST">

            <div class="form-group">

                <label>Product Name</label>

                <input type="text"
                       name="product_name"
                       class="form-control"
                       required>

            </div>

            <div class="form-group">

                <label>Category</label>

                <input type="text"
                       name="category"
                       class="form-control"
                       required>

            </div>

            <div class="form-group">

                <label>Quantity</label>

                <input type="number"
                       name="quantity"
                       class="form-control"
                       required>

            </div>

            <button type="submit"
                    name="send_request"
                    class="btn btn-primary">

                Send Request

            </button>

        </form>

    </div>

</div>

</body>
</html>