<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if($_SESSION['role'] != 'admin'){
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
            <button type="button" class="btn btn-primary" onclick="openAddProductModal()">
    Add Product
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
                    <th style="text-align: center;">Actions</th>
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
                    
                    <td style="text-align: center;">
    <button
        type="button"
        class="btn btn-warning"
        style="padding: 6px 12px; font-size: 13px;"
        onclick="openEditProductModal(
            '<?php echo $row['product_id']; ?>',
            '<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES); ?>',
            '<?php echo htmlspecialchars($row['brand'], ENT_QUOTES); ?>',
            '<?php echo htmlspecialchars($row['category'], ENT_QUOTES); ?>',
            '<?php echo $row['stock']; ?>',
            '<?php echo $row['buying_price']; ?>',
            '<?php echo $row['selling_price']; ?>'
        )">
        Edit
    </button>

    <a
        href="delete_product.php?id=<?php echo $row['product_id']; ?>"
        class="btn btn-danger"
        style="padding: 6px 12px; font-size: 13px;"
        onclick="return confirmDeleteProduct('<?php echo htmlspecialchars($row['product_name'], ENT_QUOTES); ?>');">
        Delete
    </a>
</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

</div>
<!-- ADD PRODUCT MODAL -->
<div class="modal-overlay" id="addProductModal">

    <div class="add-product-card">

        <div class="modal-header">
            <h2>Add Product</h2>

            <button type="button" class="modal-close" onclick="closeAddProductModal()">
                &times;
            </button>
        </div>

        <form method="POST" action="add_product.php" class="add-product-form">

            <input
                type="text"
                name="product_name"
                placeholder="Product Name"
                required
            >

            <input
                type="text"
                name="brand"
                placeholder="Brand"
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
                name="stock"
                placeholder="Stock"
                min="0"
                required
            >

            <input
                type="number"
                step="0.01"
                name="buying_price"
                placeholder="Buying Price"
                min="0"
                required
            >

            <input
                type="number"
                step="0.01"
                name="selling_price"
                placeholder="Selling Price"
                min="0"
                required
            >

            <button type="submit" name="add_product" class="submit-product-btn">
                Save Product
            </button>

        </form>

    </div>

</div>
<!-- EDIT PRODUCT MODAL -->
<div class="modal-overlay" id="editProductModal">

    <div class="add-product-card">

        <div class="modal-header">
            <h2>Edit Product</h2>

            <button type="button" class="modal-close" onclick="closeEditProductModal()">
                &times;
            </button>
        </div>

        <form method="POST" id="editProductForm" class="add-product-form">

            <input type="text" name="product_name" id="edit_product_name" placeholder="Product Name" required>

            <input type="text" name="brand" id="edit_brand" placeholder="Brand" required>

            <select name="category" id="edit_category" required>
                <option value="">Select Category</option>
                <option value="Writing & Drawing">Writing & Drawing</option>
                <option value="Paper Products">Paper Products</option>
                <option value="Organization & Filing">Organization & Filing</option>
                <option value="Art & Craft Materials">Art & Craft Materials</option>
                <option value="Desk & Stationery Accessories">Desk & Stationery Accessories</option>
                <option value="Electronics">Electronics</option>
            </select>

            <input type="number" name="stock" id="edit_stock" placeholder="Stock" min="0" required>

            <input type="number" step="0.01" name="buying_price" id="edit_buying_price" placeholder="Buying Price" min="0" required>

            <input type="number" step="0.01" name="selling_price" id="edit_selling_price" placeholder="Selling Price" min="0" required>

            <button type="submit" name="update_product" class="submit-product-btn">
                Save Changes
            </button>

        </form>

    </div>

</div>

<script>
function openAddProductModal() {
    document.getElementById('addProductModal').style.display = 'flex';
}

function closeAddProductModal() {
    document.getElementById('addProductModal').style.display = 'none';
}

function openEditProductModal(id, productName, brand, category, stock, buyingPrice, sellingPrice) {
    document.getElementById('editProductForm').action = 'edit_product.php?id=' + encodeURIComponent(id);

    document.getElementById('edit_product_name').value = productName;
    document.getElementById('edit_brand').value = brand;
    document.getElementById('edit_category').value = category;
    document.getElementById('edit_stock').value = stock;
    document.getElementById('edit_buying_price').value = buyingPrice;
    document.getElementById('edit_selling_price').value = sellingPrice;

    document.getElementById('editProductModal').style.display = 'flex';
}

function closeEditProductModal() {
    document.getElementById('editProductModal').style.display = 'none';
}

function confirmDeleteProduct(productName) {
    return confirm(
        'Warning: You are about to delete "' + productName + '".\n\n' +
        'This action cannot be undone. Do you want to continue?'
    );
}

window.onclick = function(event) {
    var addModal = document.getElementById('addProductModal');
    var editModal = document.getElementById('editProductModal');

    if (event.target === addModal) {
        closeAddProductModal();
    }

    if (event.target === editModal) {
        closeEditProductModal();
    }
}
</script>
</script>
</body>
</html>