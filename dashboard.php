<?php

session_start();

require_once 'config/database.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>

    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<!-- NAVBAR -->
<?php include 'includes/navbar.php'; ?>

<!-- SIDEBAR -->
<?php include 'includes/sidebar.php'; ?>

<!-- MAIN CONTENT -->
<div class="main-content">
    
<div class="sales-page">

      <div>
            <h1 class="sales-title">Admin Dashboard</h1>
            <p class="sales-subtitle">
                Welcome to Sales and Inventory System
            </p>
        </div>

   
    </div>

    <?php

    /* TOTAL SALES */
    $sales_query = mysqli_query($conn,

        "SELECT SUM(total_amount) AS total_sales
         FROM sales"

    );

    $sales = mysqli_fetch_assoc($sales_query);

    /* TOTAL PRODUCTS */
    $products_query = mysqli_query($conn,

        "SELECT COUNT(*) AS total_products
         FROM products"

    );

    $products = mysqli_fetch_assoc($products_query);

    /* LOW STOCKS */
    $lowstocks_query = mysqli_query($conn,

        "SELECT COUNT(*) AS low_stock
         FROM products
         WHERE stock <= 5"

    );

    $lowstocks = mysqli_fetch_assoc($lowstocks_query);

    /* TOTAL TRANSACTIONS */
    $transactions_query = mysqli_query($conn,

        "SELECT COUNT(*) AS total_transactions
         FROM sales"

    );

    $transactions = mysqli_fetch_assoc($transactions_query);

    ?>

    <!-- DASHBOARD CARDS -->
    <div class="cards">

        <!-- TOTAL SALES -->
        <div class="card blue">

            <h3>Total Sales</h3>

            <div class="number">

                ₱<?php echo number_format($sales['total_sales'],2); ?>

            </div>

        </div>

        <!-- TOTAL PRODUCTS -->
        <div class="card teal">

            <h3>Total Products</h3>

            <div class="number">

                <?php echo $products['total_products']; ?>

            </div>

        </div>

        <!-- LOW STOCKS -->
        <div class="card orange">

            <h3>Low Stocks</h3>

            <div class="number">

                <?php echo $lowstocks['low_stock']; ?>

            </div>

        </div>

        <!-- TOTAL TRANSACTIONS -->
        <div class="card sky">

            <h3>Total Transactions</h3>

            <div class="number">

                <?php echo $transactions['total_transactions']; ?>

            </div>

        </div>

    </div>

    <!-- RECENT TRANSACTIONS -->
    <div class="table-container">

        <h2>Recent Transactions</h2>

        <table>

            <thead>

                <tr>

                    <th>Sale ID</th>
                    <th>Total Amount</th>
                    <th>Date</th>

                </tr>

            </thead>

            <tbody>

            <?php

            $recent_query = mysqli_query($conn,

                "SELECT *
                 FROM sales
                 ORDER BY sale_id DESC
                 LIMIT 5"

            );

            while($row = mysqli_fetch_assoc($recent_query)){

            ?>

                <tr>

                    <td>
                        <?php echo $row['sale_id']; ?>
                    </td>

                    <td>

                        ₱<?php echo number_format($row['total_amount'],2); ?>

                    </td>

                    <td>

                        <?php echo $row['created_at']; ?>

                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>
</div>
</body>
</html>
