<?php

session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if($_SESSION['role'] != 'admin'){
    header("Location: ../../dashboard.php");
    exit();
}

$query = mysqli_query($conn,

    "SELECT products.product_name,

            SUM(sale_items.quantity) AS total_sold

     FROM sale_items

     INNER JOIN products
     ON sale_items.product_id = products.product_id

     GROUP BY sale_items.product_id

     ORDER BY total_sold DESC"

);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Best Selling Products</title>
</head>
<body>

<h2>Best Selling Products</h2>

<table border="1" cellpadding="10">

<tr>

    <th>Product</th>
    <th>Total Sold</th>

</tr>

<?php while($row = mysqli_fetch_assoc($query)){ ?>

<tr>

    <td><?php echo $row['product_name']; ?></td>

    <td><?php echo $row['total_sold']; ?></td>

</tr>

<?php } ?>

</table>

</body>
</html>