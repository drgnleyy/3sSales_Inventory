<?php

session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if($_SESSION['role'] != 'admin'){
    header("Location: ../../dashboard.php");
    exit();
}

/* DAILY SALES */
$daily_query = mysqli_query($conn,

    "SELECT SUM(total_amount) AS daily_sales

     FROM sales

     WHERE DATE(created_at) = CURDATE()"

);

$daily = mysqli_fetch_assoc($daily_query);

/* MONTHLY SALES */
$monthly_query = mysqli_query($conn,

    "SELECT SUM(total_amount) AS monthly_sales

     FROM sales

     WHERE MONTH(created_at) = MONTH(CURDATE())
     AND YEAR(created_at) = YEAR(CURDATE())"

);

$monthly = mysqli_fetch_assoc($monthly_query);

/* SALES HISTORY */
$history = mysqli_query($conn,

    "SELECT * FROM sales
     ORDER BY sale_id DESC"

);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
</head>
<body>

<h2>Sales Report</h2>

<h3>
Daily Sales:
₱<?php echo number_format($daily['daily_sales'],2); ?>
</h3>

<h3>
Monthly Sales:
₱<?php echo number_format($monthly['monthly_sales'],2); ?>
</h3>

<hr>

<h3>Sales History</h3>

<table border="1" cellpadding="10">

<tr>

    <th>Sale ID</th>
    <th>Total Amount</th>
    <th>Date</th>

</tr>

<?php while($row = mysqli_fetch_assoc($history)){ ?>

<tr>

    <td><?php echo $row['sale_id']; ?></td>

    <td>
        ₱<?php echo number_format($row['total_amount'],2); ?>
    </td>

    <td><?php echo $row['created_at']; ?></td>

</tr>

<?php } ?>

</table>

</body>
</html>