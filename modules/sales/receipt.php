<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

/* FETCH SALE */
$sale_query = mysqli_query($conn,
    "SELECT sales.*, users.username
     FROM sales
     INNER JOIN users ON sales.sold_by = users.id
     WHERE sales.sale_id = '$id'"
);

$sale = mysqli_fetch_assoc($sale_query);

if (!$sale) {
    echo "Receipt not found.";
    exit();
}

/* FETCH ITEMS */
$items_query = mysqli_query($conn,
    "SELECT sale_items.*, products.product_name
     FROM sale_items
     INNER JOIN products ON sale_items.product_id = products.product_id
     WHERE sale_items.sale_id = '$id'"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<?php include dirname(__DIR__, 2) . '/includes/navbar.php'; ?>
<?php include dirname(__DIR__, 2) . '/includes/sidebar.php'; ?>

<div class="main-content">


<a href="sales_history.php" class="icon-back-btn" aria-label="Back to new sale">
            &#8592;
        </a>
    <div class="receipt-page">

        <div class="receipt-modal">

            <div class="receipt-header">
                <h2>Sales System</h2>
                <p>Official Sales Receipt</p>
            </div>

            <div class="receipt-meta">
                <div>
                    <span>Receipt #</span>
                    <strong><?php echo $sale['sale_id']; ?></strong>
                </div>

                <div>
                    <span>Cashier</span>
                    <strong><?php echo htmlspecialchars($sale['username']); ?></strong>
                </div>

                <div>
                    <span>Date</span>
                    <strong><?php echo date("m/d/Y", strtotime($sale['created_at'])); ?></strong>
                </div>
            </div>

            <div class="receipt-divider"></div>

            <div class="receipt-items">

                <div class="receipt-items-head">
                    <span>Item</span>
                    <span>Qty</span>
                    <span>Price</span>
                    <span>Total</span>
                </div>

                <?php while ($row = mysqli_fetch_assoc($items_query)) { ?>
                    <div class="receipt-item-row">
                        <span class="receipt-product-name">
                            <?php echo htmlspecialchars($row['product_name']); ?>
                        </span>

                        <span><?php echo $row['quantity']; ?></span>

                        <span>₱<?php echo number_format($row['price'], 2); ?></span>

                        <span>₱<?php echo number_format($row['subtotal'], 2); ?></span>
                    </div>
                <?php } ?>

            </div>

            <div class="receipt-divider"></div>

            <div class="receipt-totals">
                <div>
                    <span>Total</span>
                    <strong>₱<?php echo number_format($sale['total_amount'], 2); ?></strong>
                </div>

                <div>
                    <span>Payment</span>
                    <strong>₱<?php echo number_format($sale['payment'], 2); ?></strong>
                </div>

                <div class="receipt-change">
                    <span>Change</span>
                    <strong>₱<?php echo number_format($sale['change_amount'], 2); ?></strong>
                </div>
            </div>

            <div class="receipt-footer">
                <p>Thank you for your purchase!</p>
                <small>Please keep this receipt for your records.</small>
            </div>

            <div class="receipt-actions">
                <a href="new_sale.php" class="receipt-back-btn">Back</a>
                <button type="button" class="receipt-print-btn" onclick="window.print()">
                    Print Receipt
                </button>
            </div>

        </div>

    </div>

</div>

</body>
</html>