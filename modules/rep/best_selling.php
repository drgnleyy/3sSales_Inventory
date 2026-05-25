<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../dashboard.php");
    exit();
}

$best_selling_query = mysqli_query($conn,
    "SELECT
        products.product_name,
        products.brand,
        SUM(sale_items.quantity) AS total_sold,
        SUM(sale_items.subtotal) AS total_income
     FROM sale_items
     INNER JOIN products ON sale_items.product_id = products.product_id
     GROUP BY sale_items.product_id, products.product_name, products.brand
     ORDER BY total_sold DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Best Selling Products</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<?php include dirname(__DIR__, 2) . '/includes/navbar.php'; ?>
<?php include dirname(__DIR__, 2) . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="sales-page">

      <div>
            <h1 class="sales-title">Best Selling Products</h1>
            <p class="sales-subtitle">
                Identify top products based on quantity sold
            </p>
        </div>

   
    </div>

    <div class="report-nav">
        <a href="sales_report.php" class="report-nav-btn">Sales Report</a>
        <a href="inventory_report.php" class="report-nav-btn">Inventory Report</a>
        <a href="best_selling.php" class="report-nav-btn active">Best Selling</a>
    </div>

    <div class="table-container">
        <h2>Top Products</h2>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Product</th>
                    <th>Brand</th>
                    <th>Total Sold</th>
                    <th>Total Income</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; ?>
                <?php if (mysqli_num_rows($best_selling_query) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($best_selling_query)) { ?>
                        <tr>
                            <td>#<?php echo $rank++; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['product_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['brand']); ?></td>
                            <td><?php echo $row['total_sold']; ?></td>
                            <td>₱<?php echo number_format($row['total_income'], 2); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5">No selling data found.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>