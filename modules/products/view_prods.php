<?php

session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if($_SESSION['role'] != 'cashier'){
    header("Location: ../../dashboard.php");
    exit();
}

$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Products</title>
    <!-- UNIVERSAL CSS -->
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<?php include dirname(__DIR__, 2) . '/includes/navbar.php'; ?>

<!-- SIDEBAR -->
<?php include dirname(__DIR__, 2) . '/includes/sidebar.php'; ?>

<h2>Products List</h2>



<br><br>
<a href="../requests/request_product.php"
       class="btn btn-primary">

       Request Product

    </a>
<table border="1" cellpadding="10">

<tr>
    <th>ID</th>
    <th>Product Name</th>
    <th>Brand</th>
    <th>Category</th>
    <th>Stock</th>
    <th>Buying Price</th>
    <th>Selling Price</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<tr>

    <td><?php echo $row['product_id']; ?></td>

    <td><?php echo $row['product_name']; ?></td>

    <td><?php echo $row['brand']; ?></td>

    <td><?php echo $row['category']; ?></td>

    <td><?php echo $row['stock']; ?></td>

    <td><?php echo $row['buying_price']; ?></td>

    <td><?php echo $row['selling_price']; ?></td>


</tr>

<?php } ?>

</table>

</body>
</html>