<?php

session_start();
include 'config/database.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>

    <title>Dashboard</title>

    <style>

        body{
            margin:0;
            font-family:Arial;
        }

        .navbar{
            background:#333;
            color:white;
            padding:15px;
            display:flex;
            justify-content:space-between;
        }

        .sidebar{
            width:200px;
            height:100vh;
            background:#222;
            position:fixed;
            padding-top:20px;
        }

        .sidebar a{
            display:block;
            color:white;
            padding:10px;
            text-decoration:none;
        }

        .sidebar a:hover{
            background:#444;
        }

        .content{
            margin-left:210px;
            padding:20px;
        }

        .cards{
            display:flex;
            gap:20px;
            flex-wrap:wrap;
        }

        .card{
            width:220px;
            background:#f1f1f1;
            padding:20px;
            border-radius:10px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        table, th, td{
            border:1px solid #ccc;
        }

        th, td{
            padding:10px;
        }

    </style>

</head>

<body>

<!-- NAVBAR -->
<?php 
// Call your components
include 'includes/navbar.php'; 
include 'includes/sidebar.php'; 
?>

<!-- CONTENT -->
<div class="content">

    <h1>Admin Dashboard</h1>

    <?php

    /* TOTAL SALES */
    $sales_query = mysqli_query($conn,
        "SELECT SUM(total_amount) AS total_sales
         FROM sales");

    $sales = mysqli_fetch_assoc($sales_query);

    /* TOTAL PRODUCTS */
    $products_query = mysqli_query($conn,
        "SELECT COUNT(*) AS total_products
         FROM products");

    $products = mysqli_fetch_assoc($products_query);

    /* LOW STOCKS */
    $lowstocks_query = mysqli_query($conn,
        "SELECT COUNT(*) AS low_stock
         FROM products
         WHERE stock <= 5");

    $lowstocks = mysqli_fetch_assoc($lowstocks_query);

    /* TOTAL TRANSACTIONS */
    $transactions_query = mysqli_query($conn,
        "SELECT COUNT(*) AS total_transactions
         FROM sales");

    $transactions = mysqli_fetch_assoc($transactions_query);

    ?>

    <!-- DASHBOARD CARDS -->
    <div class="cards">

        <div class="card">

            <h3>Total Sales</h3>

            <p>
                ₱<?php echo number_format($sales['total_sales'],2); ?>
            </p>

        </div>

        <div class="card">

            <h3>Total Products</h3>

            <p>
                <?php echo $products['total_products']; ?>
            </p>

        </div>

        <div class="card">

            <h3>Low Stocks</h3>

            <p>
                <?php echo $lowstocks['low_stock']; ?>
            </p>

        </div>

        <div class="card">

            <h3>Total Transactions</h3>

            <p>
                <?php echo $transactions['total_transactions']; ?>
            </p>

        </div>

    </div>

    <!-- RECENT TRANSACTIONS -->
    <h2>Recent Transactions</h2>

    <table>

        <tr>
            <th>Sale ID</th>
            <th>Total Amount</th>
            <th>Date</th>
        </tr>

        <?php

        $recent_query = mysqli_query($conn,
            "SELECT *
             FROM sales
             ORDER BY sale_id DESC
             LIMIT 5");

        while($row = mysqli_fetch_assoc($recent_query)){

        ?>

        <tr>

            <td><?php echo $row['sale_id']; ?></td>

            <td>
                ₱<?php echo number_format($row['total_amount'],2); ?>
            </td>

            <td><?php echo $row['created_at']; ?></td>

        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>