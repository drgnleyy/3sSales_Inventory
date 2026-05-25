<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$script_name = $_SERVER['SCRIPT_NAME'];
$base_url = '';

if (($pos = strpos($script_name, '/modules/')) !== false) {
    $base_url = substr($script_name, 0, $pos) . '/';
}

$query = "
    SELECT
        s.sale_id,
        GROUP_CONCAT(p.product_name SEPARATOR ', ') AS product_names,
        s.payment,
        s.change_amount,
        u.username AS sold_by_name,
        DATE_FORMAT(s.created_at, '%m/%d/%Y') AS sale_date
    FROM sales s
    LEFT JOIN sale_items si ON s.sale_id = si.sale_id
    LEFT JOIN products p ON si.product_id = p.product_id
    LEFT JOIN users u ON s.sold_by = u.id
    GROUP BY
        s.sale_id,
        s.payment,
        s.change_amount,
        u.username,
        s.created_at
    ORDER BY s.sale_id DESC
";

$result = mysqli_query($conn, $query);

if (isset($_GET['download']) && $_GET['download'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sales_history.csv"');

    $output = fopen('php://output', 'w');

    fputcsv($output, [
        'Product Name',
        'Payment',
        'Change',
        'Sold by',
        'Date',
        'Receipt'
    ]);

    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['product_names'],
            number_format($row['payment'], 2),
            number_format($row['change_amount'], 2),
            $row['sold_by_name'] ?? 'N/A',
            $row['sale_date'],
            $base_url . 'modules/sales/receipt.php?id=' . $row['sale_id']
        ]);
    }

    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales History</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<?php include dirname(__DIR__, 2) . '/includes/navbar.php'; ?>
<?php include dirname(__DIR__, 2) . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="page-title page-title-with-actions">

    <div class="page-title-left">
        <a href="new_sale.php" class="icon-back-btn" aria-label="Back to new sale">
            &#8592;
        </a>

        <div>
            <h1>Sales History</h1>
            <p>View completed transactions and receipt records</p>
        </div>
    </div>

    <div class="page-actions">
        <a href="sales_history.php?download=csv" class="btn btn-success">
            Download
        </a>
    </div>

</div>
    <div class="table-container sales-history-card">

        <h2>Transaction Records</h2>

        <table class="sales-history-table">
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Product Name</th>
                    <th>Payment</th>
                    <th>Change</th>
                    <th>Sold by</th>
                    <th>Date</th>
                    <th>Receipt</th>
                </tr>
            </thead>

            <tbody>
                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td>#<?php echo $row['sale_id']; ?></td>

                            <td>
                                <?php echo htmlspecialchars($row['product_names'] ?? 'N/A'); ?>
                            </td>

                            <td>
                                ₱<?php echo number_format($row['payment'], 2); ?>
                            </td>

                            <td>
                                ₱<?php echo number_format($row['change_amount'], 2); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['sold_by_name'] ?? 'N/A'); ?>
                            </td>

                            <td>
                                <?php echo $row['sale_date']; ?>
                            </td>

                            <td>
                                <a
                                    href="receipt.php?id=<?php echo $row['sale_id']; ?>"
                                    class="receipt-link">
                                    View Receipt
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7" class="empty-table-message">
                            No sales history found.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

</div>

</body>
</html>