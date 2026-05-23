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
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<?php include dirname(__DIR__, 2) . '/includes/navbar.php'; ?>

<!-- SIDEBAR -->
<?php include dirname(__DIR__, 2) . '/includes/sidebar.php'; ?>

<!-- MAIN CONTENT CONTAINER (This prevents the sidebar from overlapping) -->
<div class="main-content">

    <div class="page-title">
        <h1>Products List</h1>
        <p>Manage and track your inventory stock levels</p>
    </div>

    <!-- TABLE CONTAINER (Using your exact style system classes) -->
    <div class="table-container">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Products Inventory</h2>
            <!-- Styling the Add Product link using your theme's primary button class -->
            <a href="add_product.php" class="btn btn-primary">Add Product</a>
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
                        <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-warning" style="padding: 6px 12px; font-size: 13px;">
                            Edit
                        </a>
                        <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-danger" style="padding: 6px 12px; font-size: 13px;">
                            Delete
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

</div>

</body>
</html>