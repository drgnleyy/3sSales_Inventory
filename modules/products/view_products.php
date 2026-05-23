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
</head>
<body>

<h2>Products List</h2>

<a href="add_product.php">Add Product</a>

<br><br>

<table border="1" cellpadding="10">

<tr>
    <th>ID</th>
    <th>Product Name</th>
    <th>Category</th>
    <th>Stock</th>
    <th>Buying Price</th>
    <th>Selling Price</th>
    <th>Actions</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<tr>

    <td><?php echo $row['product_id']; ?></td>

    <td><?php echo $row['product_name']; ?></td>

    <td><?php echo $row['category']; ?></td>

    <td><?php echo $row['stock']; ?></td>

    <td><?php echo $row['buying_price']; ?></td>

    <td><?php echo $row['selling_price']; ?></td>

    <td>

        <a href="edit_product.php?id=<?php echo $row['product_id']; ?>">
            Edit
        </a>

        |

        <a href="delete_product.php?id=<?php echo $row['product_id']; ?>">
            Delete
        </a>

    </td>

</tr>

<?php } ?>

</table>

</body>
</html>