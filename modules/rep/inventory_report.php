<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../dashboard.php");
    exit();
}

$inventory_query = mysqli_query($conn,
    "SELECT product_name, brand, category, stock
     FROM products
     ORDER BY product_name ASC"
);

$low_stock_query = mysqli_query($conn,
    "SELECT product_name, stock
     FROM products
     WHERE stock < 5
     ORDER BY stock ASC"
);

$history_query = mysqli_query($conn,
    "SELECT inventory.*, products.product_name
     FROM inventory
     INNER JOIN products ON inventory.product_id = products.product_id
     ORDER BY inventory.inventory_id DESC
     LIMIT 20"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory Report</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<?php include dirname(__DIR__, 2) . '/includes/navbar.php'; ?>
<?php include dirname(__DIR__, 2) . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="sales-page">

      <div>
            <h1 class="sales-title">Inventory Report</h1>
            <p class="sales-subtitle">
                Monitor current stocks, low stock items, and stock movement
            </p>
        </div>

   
    </div>

    <div class="report-nav">
        <a href="sales_report.php" class="report-nav-btn">Sales Report</a>
        <a href="inventory_report.php" class="report-nav-btn active">Inventory Report</a>
        <a href="best_selling.php" class="report-nav-btn">Best Selling</a>
    </div>

    <div class="reports-grid">

        <div class="report-card report-card-wide">
            <div class="report-card-header">
                <h2>Current Stocks</h2>
                <span>Inventory</span>
            </div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($inventory_query)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['brand']); ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td><?php echo $row['stock']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="report-card">
            <div class="report-card-header">
                <h2>Low Stock</h2>
                <span>Below 5</span>
            </div>

            <div class="report-list">
                <?php if (mysqli_num_rows($low_stock_query) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($low_stock_query)) { ?>
                        <div class="report-list-item danger">
                            <span><?php echo htmlspecialchars($row['product_name']); ?></span>
                            <strong><?php echo $row['stock']; ?> left</strong>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <p>No low stock products.</p>
                <?php } ?>
            </div>
        </div>

        <div class="report-card">
            <div class="report-card-header">
                <h2>Stock Movement</h2>
                <span>Latest 20</span>
            </div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Qty</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($history_query)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['type']); ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td><?php echo date("m/d/Y", strtotime($row['created_at'])); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

</body>
</html>