<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../dashboard.php");
    exit();
}

/* FETCH INVENTORY HISTORY */
$query = mysqli_query($conn,
    "SELECT inventory.*, products.product_name
     FROM inventory
     INNER JOIN products ON inventory.product_id = products.product_id
     ORDER BY inventory.inventory_id DESC"
);

/* FETCH PRODUCTS FOR STOCK IN MODAL */
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY product_name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory History</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<?php include dirname(__DIR__, 2) . '/includes/navbar.php'; ?>
<?php include dirname(__DIR__, 2) . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="sales-page">

      <div>
            <h1 class="sales-title">Inventory History</h1>
            <p class="sales-subtitle">
                Track all stock movements, including stock in and out transactions
            </p>
        </div>

   
    </div>

    <div class="table-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        
       
        <h2>Inventory Records</h2>
        <div class="page-actions">
            <button type="button" class="btn btn-primary" onclick="openStockInModal()">
                Stock In
            </button>
        </div>
        </div>
        <table class="inventory-history-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Remarks</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                <?php if (mysqli_num_rows($query) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                        <tr>
                            <td>#<?php echo $row['inventory_id']; ?></td>

                            <td>
                                <strong><?php echo htmlspecialchars($row['product_name']); ?></strong>
                            </td>

                            <td>
                                <?php if ($row['type'] == 'stock_in') { ?>
                                    <span class="badge badge-success">Stock In</span>
                                <?php } else { ?>
                                    <span class="badge badge-warning">
                                        <?php echo htmlspecialchars($row['type']); ?>
                                    </span>
                                <?php } ?>
                            </td>

                            <td><?php echo $row['quantity']; ?></td>

                            <td>
                                <?php echo htmlspecialchars($row['remarks'] ?: 'N/A'); ?>
                            </td>

                            <td>
                                <?php echo date("m/d/Y", strtotime($row['created_at'])); ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6" class="empty-table-message">
                            No inventory history found.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<!-- STOCK IN MODAL -->
<div class="modal-overlay" id="stockInModal">

    <div class="add-product-card">

        <div class="modal-header">
            <h2>Stock In</h2>

            <button type="button" class="modal-close" onclick="closeStockInModal()">
                &times;
            </button>
        </div>

        <form method="POST" action="stock_in.php" class="add-product-form">

            <select name="product_id" required>
                <option value="">Select Product</option>

                <?php while ($product = mysqli_fetch_assoc($products)) { ?>
                    <option value="<?php echo $product['product_id']; ?>">
                        <?php echo htmlspecialchars($product['product_name']); ?>
                    </option>
                <?php } ?>
            </select>

            <input
                type="number"
                name="quantity"
                placeholder="Quantity"
                min="1"
                required
            >

            <input
                type="text"
                name="remarks"
                placeholder="Remarks"
            >

            <button type="submit" name="stock_in" class="submit-product-btn">
                Save Stock In
            </button>

        </form>

    </div>

</div>

<script>
function openStockInModal() {
    document.getElementById('stockInModal').style.display = 'flex';
}

function closeStockInModal() {
    document.getElementById('stockInModal').style.display = 'none';
}

window.onclick = function(event) {
    var modal = document.getElementById('stockInModal');

    if (event.target === modal) {
        closeStockInModal();
    }
}
</script>

</body>
</html>