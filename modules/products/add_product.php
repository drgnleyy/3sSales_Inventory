<?php

session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if(isset($_POST['add_product'])){

    $product_name = $_POST['product_name'];
    $brand = $_POST['brand'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $buying_price = $_POST['buying_price'];
    $selling_price = $_POST['selling_price'];

    $query = "INSERT INTO products
    (product_name, brand, category, stock, buying_price, selling_price)

    VALUES

    ('$product_name', '$brand', '$category', '$stock',
    '$buying_price', '$selling_price')";

    mysqli_query($conn, $query);

    header("Location: view_products.php");
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
</head>
<body>

<h2>Add Product</h2>

<form method="POST">

    <input type="text"
           name="product_name"
           placeholder="Product Name"
           required>

    <br><br>

    <input type="text"
           name="brand"
           placeholder="Brand"
           required>

    <br><br>

       <select name="category" required>
              <option value="">Select Category</option>
              <option value="Writing & Drawing" <?php if(isset($_POST['category']) && $_POST['category']=="Writing & Drawing") echo 'selected'; ?>>Writing & Drawing</option>
              <option value="Paper Products" <?php if(isset($_POST['category']) && $_POST['category']=="Paper Products") echo 'selected'; ?>>Paper Products</option>
              <option value="Organization & Filing" <?php if(isset($_POST['category']) && $_POST['category']=="Organization & Filing") echo 'selected'; ?>>Organization & Filing</option>
              <option value="Art & Craft Materials" <?php if(isset($_POST['category']) && $_POST['category']=="Art & Craft Materials") echo 'selected'; ?>>Art & Craft Materials</option>
              <option value="Desk & Stationery Accessories" <?php if(isset($_POST['category']) && $_POST['category']=="Desk & Stationery Accessories") echo 'selected'; ?>>Desk & Stationery Accessories</option>
              <option value="Electronics" <?php if(isset($_POST['category']) && $_POST['category']=="Electronics") echo 'selected'; ?>>Electronics</option>
       </select>

    <br><br>

    <input type="number"
           name="stock"
           placeholder="Stock"
           required>

    <br><br>

    <input type="number"
           step="0.01"
           name="buying_price"
           placeholder="Buying Price"
           required>

    <br><br>

    <input type="number"
           step="0.01"
           name="selling_price"
           placeholder="Selling Price"
           required>

    <br><br>

    <button type="submit" name="add_product">
        Add Product
    </button>

</form>

</body>
</html>