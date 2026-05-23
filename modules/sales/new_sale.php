<?php

session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../../login.php");
    exit();
}

$products = mysqli_query($conn,
    "SELECT * FROM products");

if(isset($_POST['process_sale'])){

    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $payment = $_POST['payment'];

    /* FETCH PRODUCT */
    $product_query = mysqli_query($conn,

        "SELECT * FROM products
         WHERE product_id='$product_id'"

    );

    $product = mysqli_fetch_assoc($product_query);

    $price = $product['selling_price'];

    $subtotal = $price * $quantity;

    $change = $payment - $subtotal;

    /* INSERT SALE */
    mysqli_query($conn,

        "INSERT INTO sales
        (total_amount, payment, change_amount, sold_by)

        VALUES

        ('$subtotal',
         '$payment',
         '$change',
         '{$_SESSION['user_id']}')"

    );

    /* GET SALE ID */
    $sale_id = mysqli_insert_id($conn);

    /* INSERT SALE ITEM */
    mysqli_query($conn,

        "INSERT INTO sale_items
        (sale_id, product_id, quantity, price, subtotal)

        VALUES

        ('$sale_id',
         '$product_id',
         '$quantity',
         '$price',
         '$subtotal')"

    );

    /* REDUCE STOCK */
    mysqli_query($conn,

        "UPDATE products
         SET stock = stock - '$quantity'
         WHERE product_id='$product_id'"

    );

    header("Location: receipt.php?id=$sale_id");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>New Sale</title>
</head>
<body>

<h2>Process Sale</h2>
<br><br>

<form method="post" action="sales_history.php">
    <button type="submit" name="view_sales">View Sales History</button>
</form>
<br><br>

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

    <input type="number"
           step="0.01"
           name="payment"
           placeholder="Payment"
           required>

    <br><br>

    <button type="submit" name="process_sale">
        Process Sale
    </button>

</form>

</body>
</html>