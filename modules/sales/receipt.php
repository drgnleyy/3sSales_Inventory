<?php

session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../../login.php");
    exit();
}

$id = $_GET['id'];

/* FETCH SALE */
$sale_query = mysqli_query($conn,

    "SELECT sales.*,
            users.username

     FROM sales

     INNER JOIN users
     ON sales.sold_by = users.id

     WHERE sale_id='$id'"

);

$sale = mysqli_fetch_assoc($sale_query);

/* FETCH ITEMS */
$items_query = mysqli_query($conn,

    "SELECT sale_items.*,
            products.product_name

     FROM sale_items

     INNER JOIN products
     ON sale_items.product_id = products.product_id

     WHERE sale_items.sale_id='$id'"

);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
</head>
<body>

<h2>Sales Receipt</h2>

<p>Receipt #: <?php echo $sale['sale_id']; ?></p>

<p>Cashier: <?php echo $sale['username']; ?></p>

<p>Date: <?php echo $sale['created_at']; ?></p>

<hr>

<table border="1" cellpadding="10">

<tr>

    <th>Product</th>
    <th>Qty</th>
    <th>Price</th>
    <th>Subtotal</th>

</tr>

<?php while($row = mysqli_fetch_assoc($items_query)){ ?>

<tr>

    <td><?php echo $row['product_name']; ?></td>

    <td><?php echo $row['quantity']; ?></td>

    <td>
        ₱<?php echo number_format($row['price'],2); ?>
    </td>

    <td>
        ₱<?php echo number_format($row['subtotal'],2); ?>
    </td>

</tr>

<?php } ?>

</table>

<hr>

<p>
Total:
₱<?php echo number_format($sale['total_amount'],2); ?>
</p>

<p>
Payment:
₱<?php echo number_format($sale['payment'],2); ?>
</p>

<p>
Change:
₱<?php echo number_format($sale['change_amount'],2); ?>
</p>

</body>
</html>