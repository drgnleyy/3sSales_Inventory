<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if($_SESSION['role'] != 'cashier'){
    header("Location: ../../dashboard.php");
    exit();
}

$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Products</title>
    <!-- UNIVERSAL CSS -->
    <link rel="stylesheet" href="../../assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<!-- NAVBAR -->
<?php include dirname(__DIR__, 2) . '/includes/navbar.php'; ?>

<!-- SIDEBAR -->
<?php include dirname(__DIR__, 2) . '/includes/sidebar.php'; ?>

<!-- MAIN CONTENT CONTAINER (This prevents the sidebar from overlapping) -->
<div class="main-content">

   <div class="sales-page">

      <div>
            <h1 class="sales-title">Product List</h1>
            <p class="sales-subtitle">
                Manage and track your inventory stock levels
            </p>
        </div>

   
    </div>

    <!-- TABLE CONTAINER (Using your exact style system classes) -->
    <div class="table-container">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Products Inventory</h2>
            <!-- Styling the Add Product link using your theme's primary button class -->
            <button type="button" class="btn btn-primary" onclick="openRequestProductModal()">
    Request Product
</button>   
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Buying Price</th>
                    <th>Selling Price</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)){ ?>
                <tr>
                    <td><?php echo $row['product_id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['product_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['brand']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    
                    <td>
                        <?php if($row['stock'] <= 5) { ?>
                            <span class="badge badge-danger"><?php echo $row['stock']; ?> (Low)</span>
                        <?php } else { ?>
                            <span class="badge badge-success"><?php echo $row['stock']; ?></span>
                        <?php } ?>
                    </td>
                    
                    <td>₱<?php echo number_format($row['buying_price'], 2); ?></td>
                    <td>₱<?php echo number_format($row['selling_price'], 2); ?></td>
                    
                
                </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

</div>
<!-- ADD PRODUCT MODAL -->
<!-- REQUEST PRODUCT MODAL -->
<div class="modal-overlay" id="RequestProductModal">

    <div class="add-product-card">

        <div class="modal-header">
            <h2>Request Product</h2>

            <button type="button" class="modal-close" onclick="closeRequestProductModal()">
                &times;
            </button>
        </div>

        <form method="POST" action="../requests/request_product.php" class="add-product-form">

            <input
                type="text"
                name="product_name"
                placeholder="Product Name"
                required
            >

            <select name="category" required>
                <option value="">Select Category</option>
                <option value="Writing & Drawing">Writing & Drawing</option>
                <option value="Paper Products">Paper Products</option>
                <option value="Organization & Filing">Organization & Filing</option>
                <option value="Art & Craft Materials">Art & Craft Materials</option>
                <option value="Desk & Stationery Accessories">Desk & Stationery Accessories</option>
                <option value="Electronics">Electronics</option>
            </select>

            <input
                type="number"
                name="quantity"
                placeholder="Quantity"
                min="1"
                required
            >

            <button type="submit" name="send_request" class="submit-product-btn">
                Submit Request
            </button>

        </form>

    </div>

</div>

<script>
function openRequestProductModal() {
    document.getElementById('RequestProductModal').style.display = 'flex';
}

function closeRequestProductModal() {
    document.getElementById('RequestProductModal').style.display = 'none';
}

window.onclick = function(event) {
    var requestModal = document.getElementById('RequestProductModal');

    if (event.target === requestModal) {
        closeRequestProductModal();
    }
}
</script>
</body>
</html>