<?php

session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../../login.php");
    exit();
}

$query = mysqli_query($conn,

    "SELECT sales.*,
            users.username

     FROM sales

     INNER JOIN users
     ON sales.sold_by = users.id

     ORDER BY sale_id DESC"

);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales History</title>
</head>
<body>

<h2>Sales History</h2>

<table border="1" cellpadding="10">

<tr>

    <th>Sale ID</th>
    <th>Total</th>
    <th>Payment</th>
    <th>Change</th>
    <th>Sold By</th>
    <th>Date</th>
    <th>Receipt</th>

</tr>

<?php while($row = mysqli_fetch_assoc($query)){ ?>

<tr>

    <td><?php echo $row['sale_id']; ?></td>

    <td>
        ₱<?php echo number_format($row['total_amount'],2); ?>
    </td>

    <td>
        ₱<?php echo number_format($row['payment'],2); ?>
    </td>

    <td>
        ₱<?php echo number_format($row['change_amount'],2); ?>
    </td>

    <td><?php echo $row['username']; ?></td>

    <td><?php echo $row['created_at']; ?></td>

    <td>

        <a href="receipt.php?id=<?php echo $row['sale_id']; ?>">

            View Receipt

        </a>

    </td>

</tr>

<?php } ?>

</table>

</body>
</html>