<?php

session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if($_SESSION['role'] != 'admin'){
    header("Location: ../../dashboard.php");
    exit();
}

$query = mysqli_query($conn,

    "SELECT inventory.*,
            products.product_name

     FROM inventory

     INNER JOIN products
     ON inventory.product_id = products.product_id

     ORDER BY inventory.inventory_id DESC"

);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory Report</title>
</head>
<body>

<h2>Inventory Report</h2>

<table border="1" cellpadding="10">

<tr>

    <th>ID</th>
    <th>Product</th>
    <th>Type</th>
    <th>Quantity</th>
    <th>Remarks</th>
    <th>Date</th>

</tr>

<?php while($row = mysqli_fetch_assoc($query)){ ?>

<tr>

    <td><?php echo $row['inventory_id']; ?></td>

    <td><?php echo $row['product_name']; ?></td>

    <td><?php echo $row['type']; ?></td>

    <td><?php echo $row['quantity']; ?></td>

    <td><?php echo $row['remarks']; ?></td>

    <td><?php echo $row['created_at']; ?></td>

</tr>

<?php } ?>

</table>

</body>
</html>