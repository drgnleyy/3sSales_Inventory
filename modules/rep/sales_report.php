<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../dashboard.php");
    exit();
}

/* DAILY SALES */
$daily_sales_query = mysqli_query($conn,
    "SELECT sale_id, total_amount, payment, change_amount, created_at
     FROM sales
     WHERE DATE(created_at) = CURDATE()
     ORDER BY sale_id DESC"
);

$daily_total_query = mysqli_query($conn,
    "SELECT SUM(total_amount) AS daily_total
     FROM sales
     WHERE DATE(created_at) = CURDATE()"
);
$daily_total = mysqli_fetch_assoc($daily_total_query);

/* MONTHLY SALES */
$monthly_query = mysqli_query($conn,
    "SELECT
        MONTHNAME(created_at) AS month_name,
        MONTH(created_at) AS month_number,
        SUM(total_amount) AS monthly_total
     FROM sales
     WHERE YEAR(created_at) = YEAR(CURDATE())
     GROUP BY MONTH(created_at), MONTHNAME(created_at)
     ORDER BY month_number ASC"
);

/* ALL TRANSACTIONS */
$history_query = mysqli_query($conn,
    "SELECT sales.*, users.username
     FROM sales
     LEFT JOIN users ON sales.sold_by = users.id
     ORDER BY sales.sale_id DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<?php include dirname(__DIR__, 2) . '/includes/navbar.php'; ?>
<?php include dirname(__DIR__, 2) . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="sales-page">

      <div>
            <h1 class="sales-title">Sales Report</h1>
            <p class="sales-subtitle">
                View transactions and income summaries
            </p>
        </div>

   
    </div>

    <div class="report-nav">
        <a href="sales_report.php" class="report-nav-btn active">Sales Report</a>
        <a href="inventory_report.php" class="report-nav-btn">Inventory Report</a>
        <a href="best_selling.php" class="report-nav-btn">Best Selling</a>
    </div>

    <div class="reports-grid">

        <div class="report-card">
            <div class="report-card-header">
                <h2>Daily Sales</h2>
                <span><?php echo date("m/d/Y"); ?></span>
            </div>

            <div class="report-total">
                ₱<?php echo number_format($daily_total['daily_total'] ?? 0, 2); ?>
            </div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th>Sale ID</th>
                        <th>Total</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($daily_sales_query) > 0) { ?>
                        <?php while ($row = mysqli_fetch_assoc($daily_sales_query)) { ?>
                            <tr>
                                <td>#<?php echo $row['sale_id']; ?></td>
                                <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                                <td><?php echo date("m/d/Y", strtotime($row['created_at'])); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr><td colspan="3">No sales today.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="report-card">
            <div class="report-card-header">
                <h2>Monthly Sales</h2>
                <span><?php echo date("Y"); ?></span>
            </div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Total Income</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($monthly_query) > 0) { ?>
                        <?php while ($row = mysqli_fetch_assoc($monthly_query)) { ?>
                            <tr>
                                <td><?php echo $row['month_name']; ?></td>
                                <td>₱<?php echo number_format($row['monthly_total'], 2); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr><td colspan="2">No monthly sales found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="report-card report-card-wide">
            <div class="report-card-header">
                <h2>Transaction History</h2>
                <span>All records</span>
            </div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th>Sale ID</th>
                        <th>Total Amount</th>
                        <th>Payment</th>
                        <th>Change</th>
                        <th>Sold By</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($history_query) > 0) { ?>
                        <?php while ($row = mysqli_fetch_assoc($history_query)) { ?>
                            <tr>
                                <td>#<?php echo $row['sale_id']; ?></td>
                                <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                                <td>₱<?php echo number_format($row['payment'], 2); ?></td>
                                <td>₱<?php echo number_format($row['change_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['username'] ?? 'N/A'); ?></td>
                                <td><?php echo date("m/d/Y", strtotime($row['created_at'])); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr><td colspan="6">No transactions found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

</body>
</html>