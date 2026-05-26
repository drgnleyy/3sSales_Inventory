<?php
session_start();

require_once dirname(__DIR__, 2) . '/config/database.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../dashboard.php");
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';

if (!empty($search)) {
    $escaped_search = mysqli_real_escape_string($conn, $search);
    $where = "WHERE users.username LIKE '%$escaped_search%'";
}

$query_sql = "
    SELECT product_requests.*, users.username
    FROM product_requests
    INNER JOIN users ON product_requests.requested_by = users.id
    $where
    ORDER BY product_requests.request_id DESC
";

$query = mysqli_query($conn, $query_sql);

/* DOWNLOAD CSV */
if (isset($_GET['download']) && $_GET['download'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="product_requests.csv"');

    $output = fopen('php://output', 'w');

    fputcsv($output, [
        'Requested By',
        'Date',
        'Product',
        'Category',
        'Quantity',
        'Status'
    ]);

    while ($row = mysqli_fetch_assoc($query)) {
        fputcsv($output, [
            $row['username'],
            date("m/d/Y", strtotime($row['created_at'])),
            $row['product_name'],
            $row['category'],
            $row['quantity'],
            $row['status']
        ]);
    }

    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Requests</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<?php include dirname(__DIR__, 2) . '/includes/navbar.php'; ?>
<?php include dirname(__DIR__, 2) . '/includes/sidebar.php'; ?>

<div class="main-content">

<div class="sales-page">

      <div>
            <h1 class="sales-title">Product Request</h1>
            <p class="sales-subtitle">
                Review cashier product requests and request history
            </p>
        </div>

   
    </div>
    <div class="page-title page-title-with-actions">

    </div>

    <div class="table-container">

        <div class="request-toolbar">
            <form method="GET" class="request-search-form">
                <input
                    type="text"
                    name="search"
                    placeholder="Search requested by..."
                    value="<?php echo htmlspecialchars($search); ?>"
                >

                <button type="submit" class="btn btn-primary">
                    Search
                </button>

                <a href="view_requests.php" class="btn btn-secondary">
                    Reset
                </a>

                 <a
                href="view_requests.php?download=csv&search=<?php echo urlencode($search); ?>"
                class="btn btn-success">
                Download
            </a>
            </form>
        </div>

        <table class="request-table">
            <thead>
                <tr>
                    <th>Requested By</th>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th style="text-align: center;">Action</th>
                </tr>
            </thead>

            <tbody>
                <?php if (mysqli_num_rows($query) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($row['username']); ?></strong>
                            </td>

                            <td>
                                <?php echo date("m/d/Y", strtotime($row['created_at'])); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['product_name']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['category']); ?>
                            </td>

                            <td>
                                <?php echo $row['quantity']; ?>
                            </td>

                            <td>
                                <?php if ($row['status'] == 'pending') { ?>
                                    <span class="badge badge-warning">Pending</span>
                                <?php } elseif ($row['status'] == 'approved') { ?>
                                    <span class="badge badge-success">Approved</span>
                                <?php } else { ?>
                                    <span class="badge badge-danger">Rejected</span>
                                <?php } ?>
                            </td>

                            <td style="text-align: center;">
                                <?php if ($row['status'] == 'pending') { ?>
                                    <a
                                        href="approve_requests.php?id=<?php echo $row['request_id']; ?>"
                                        class="btn btn-success request-action-btn">
                                        Approve
                                    </a>

                                    <a
                                        href="reject_request.php?id=<?php echo $row['request_id']; ?>"
                                        class="btn btn-danger request-action-btn">
                                        Reject
                                    </a>
                                <?php } ?>

                                <a
                                    href="delete_request.php?id=<?php echo $row['request_id']; ?>"
                                    class="btn btn-danger request-action-btn"
                                    onclick="return confirm('Delete this request permanently?');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7" class="empty-table-message">
                            No product requests found.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

</div>

</body>
</html>