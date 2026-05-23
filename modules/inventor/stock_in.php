<?php

session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if($_SESSION['role'] != 'admin'){
    header("Location: ../../dashboard.php");
    exit();
}

/* FETCH PRODUCTS */
$products = mysqli_query($conn,
    "SELECT * FROM products");

if(isset($_POST['stock_in'])){

    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $remarks = $_POST['remarks'];

    /* UPDATE PRODUCT STOCK */
    mysqli_query($conn,

        "UPDATE products
         SET stock = stock + '$quantity'
         WHERE product_id='$product_id'"

    );

    /* SAVE INVENTORY HISTORY */
    mysqli_query($conn,

        "INSERT INTO inventory
        (product_id, type, quantity, remarks)

        VALUES

        ('$product_id',
         'stock_in',
         '$quantity',
         '$remarks')"

    );

    header("Location: inventor_history.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Stock In</title>
</head>
<body>

<h2>Stock In</h2>

<form method="POST">

    <select name="product_id" required>

        <option value="">Select Product</option>

        <?php while($row = mysqli_fetch_assoc($products)){ ?>

        <option value="<?php echo $row['product_id']; ?>">

            <?php echo $row['product_name']; ?>

        </option>

        <?php } ?>

    </select>

    <br><br>

    <input type="number"
           name="quantity"
           placeholder="Quantity"
           required>

    <br><br>

    <input type="text"
           name="remarks"
           placeholder="Remarks">

    <br><br>

    <button type="submit" name="stock_in">
        Stock In
    </button>

</form>

</body>
</html>