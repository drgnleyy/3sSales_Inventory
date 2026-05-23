<?php

session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

/* ADMIN ONLY */
if($_SESSION['role'] != 'admin'){
    header("Location: ../../dashboard.php");
    exit();
}

/* GET PRODUCT ID */
$id = $_GET['id'];

/* FETCH PRODUCT */
$query = "SELECT * FROM products WHERE product_id='$id'";
$result = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($result);

/* UPDATE PRODUCT */
if(isset($_POST['update_product'])){

    $product_name = $_POST['product_name'];
    $brand = $_POST['brand'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $buying_price = $_POST['buying_price'];
    $selling_price = $_POST['selling_price'];

    $update = "UPDATE products SET

    product_name='$product_name',
    brand='$brand',
    category='$category',
    stock='$stock',
    buying_price='$buying_price',
    selling_price='$selling_price'

    WHERE product_id='$id'";

    mysqli_query($conn, $update);

    header("Location: view_products.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
</head>
<body>

<h2>Edit Product</h2>

<form method="POST">

    <input type="text"
           name="product_name"
           value="<?php echo $row['product_name']; ?>">

    <br><br>

    <input type="text"
           name="category"
           value="<?php echo $row['category']; ?>">

    <br><br>

    <input type="number"
           name="stock"
           value="<?php echo $row['stock']; ?>">

    <br><br>

    <input type="number"
           step="0.01"
           name="buying_price"
           value="<?php echo $row['buying_price']; ?>">

    <br><br>

    <input type="number"
           step="0.01"
           name="selling_price"
           value="<?php echo $row['selling_price']; ?>">

    <br><br>

    <button type="submit" name="update_product">
        Update Product
    </button>

</form>

</body>
</html>