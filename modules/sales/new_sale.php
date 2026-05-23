<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../../login.php");
    exit();
}

// Read current active filters from URL parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$subcategory = isset($_GET['subcategory']) ? $_GET['subcategory'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

if (isset($_POST['process_sale'])) {
    $payment = floatval($_POST['payment']);
    $checked_products = isset($_POST['checked_products']) ? $_POST['checked_products'] : [];
    $quantities = isset($_POST['quantity']) ? $_POST['quantity'] : [];

    if (!empty($checked_products)) {
        $total_amount = 0;
        $items_to_insert = [];

        // Loop through checked products to calculate subtotals and verify stock
        foreach ($checked_products as $product_id) {
            $product_id = mysqli_real_escape_string($conn, $product_id);
            $qty = intval($quantities[$product_id]);

            $product_query = mysqli_query($conn, "SELECT * FROM products WHERE product_id='$product_id'");
            if ($product = mysqli_fetch_assoc($product_query)) {
                $price = floatval($product['selling_price']);
                $subtotal = $price * $qty;
                $total_amount += $subtotal;

                $items_to_insert[] = [
                    'product_id' => $product_id,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal
                ];
            }
        }

        $change = $payment - $total_amount;
        $sold_by = $_SESSION['user_id'];

        // Register the overall sale transaction
        mysqli_query($conn, 
            "INSERT INTO sales (total_amount, payment, change_amount, sold_by) 
             VALUES ('$total_amount', '$payment', '$change', '$sold_by')"
        );

        $sale_id = mysqli_insert_id($conn);

        // Register each purchased item and reduce inventory
        foreach ($items_to_insert as $item) {
            $p_id = $item['product_id'];
            $qty = $item['quantity'];
            $price = $item['price'];
            $subtotal = $item['subtotal'];

            mysqli_query($conn, 
                "INSERT INTO sale_items (sale_id, product_id, quantity, price, subtotal) 
                 VALUES ('$sale_id', '$p_id', '$qty', '$price', '$subtotal')"
            );

            mysqli_query($conn, 
                "UPDATE products SET stock = stock - '$qty' WHERE product_id='$p_id'"
            );
        }

        // Redirect to receipt printing
        header("Location: receipt.php?id=$sale_id");
        exit();
    }
}

// Fetch all available products to display on the Right Section sale checklist
$all_products_query = mysqli_query($conn, "SELECT * FROM products ORDER BY product_name ASC");
$all_products_list = [];
while ($row = mysqli_fetch_assoc($all_products_query)) {
    $all_products_list[] = $row;
}

// Build query for Left Section visual catalog filtering
$query_filters = ["1=1"];
if (!empty($category)) {
    $escaped_cat = mysqli_real_escape_string($conn, $category);
    $query_filters[] = "category = '$escaped_cat'";
}
if (!empty($subcategory)) {
    $escaped_subcat = mysqli_real_escape_string($conn, $subcategory);
    $query_filters[] = "subcategory = '$escaped_subcat'";
}
if (!empty($search)) {
    $escaped_search = mysqli_real_escape_string($conn, $search);
    $query_filters[] = "(product_name LIKE '%$escaped_search%' OR brand LIKE '%$escaped_search%')";
}

$where_clause = implode(" AND ", $query_filters);
$catalog_products = mysqli_query($conn, "SELECT * FROM products WHERE $where_clause");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Process Sale</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<?php include dirname(__DIR__, 2) . '/includes/navbar.php'; ?>
<?php include dirname(__DIR__, 2) . '/includes/sidebar.php'; ?>

<div class="main-content">

<div class="sales-page">

    <div class="sales-header">

        <div>
            <h1 class="sales-title">Process Sale Dashboard</h1>
            <p class="sales-subtitle">
                Create and process customer transactions
            </p>
        </div>

        <form method="post" action="sales_history.php">
            <button type="submit" class="history-btn">
                View Sales History
            </button>
        </form>

    </div>

<form method="POST" id="sale_form">
    <div class="sales-layout">

        <!-- LEFT SECTION: Catalog -->
        <div class="catalog-card">

    <div class="card-title-area">
    <h2>Product Catalog</h2>
    <p>Browse and select available products</p>
</div>

<div class="search-row">

    <input
        type="text"
        id="catalog_search_input"
        placeholder="Search product or brand..."
        value="<?php echo htmlspecialchars($search); ?>"
    >

    <button
        type="button"
        class="btn-primary"
        onclick="applySearch()">
        Search
    </button>

    <button
        type="button"
        class="btn-secondary"
        onclick="clearAllFilters()">
        Reset
    </button>

</div>

            <div class="category-section">

    <h4>Categories</h4>

    <div class="category-list">
                <?php
                $categories = ['Writing & Drawing', 'Paper Products', 'Organization & Filing', 'Art & Craft Materials', 'Desk & Stationery Accessories', 'Electronics'];
                $first = true;
                foreach ($categories as $cat) {
                     echo '
            <a
                class="category-chip"
                href="?category='.urlencode($cat).'">
                '.htmlspecialchars($cat).'
            </a>';
        }
        ?>

    </div>

</div>

            <div class="section-header">
    <h3>Available Products</h3>
</div>
            <div class="product-grid">
                <?php
                if (mysqli_num_rows($catalog_products) > 0) {
                    while ($row = mysqli_fetch_assoc($catalog_products)) {
                        echo '
<div class="product-card">

    <h3>'.
    htmlspecialchars($row['product_name']).
    '</h3>

    <p>
        Brand:
        '.htmlspecialchars($row['brand'] ?: 'N/A').'
    </p>

    <div class="product-price">
        ₱'.number_format($row['selling_price'],2).'
    </div>

    <div class="stock-badge">
        '.$row['stock'].' in stock
    </div>

    <button
        type="button"
        class="select-btn"
        onclick="selectProduct(
        \''.$row['product_id'].'\',
        \''.htmlspecialchars($row['product_name']).'\',
        \''.$row['selling_price'].'\',
        \''.$row['stock'].'\')">

        Select Item

    </button>

</div>';

                    }
                } else {
                    echo '<p>No products found matching filters.</p>';
                }
                ?>
            </div>

            <div class="transaction-card">

    <div class="section-header">
        <h3>Transaction Details</h3>
    </div>

    <div class="selected-product-box">

    <label>Selected Product</label>

    <div id="selected_display_name">
        No product selected
    </div>

</div>

    <div class="form-group">
        <label>Quantity</label>
        <input
            type="number"
            id="left_quantity"
            class="form-control"
            value="1"
            min="1"
            disabled
        >
    </div>

    <button
        class="btn btn-success"
        type="button"
        id="add_item_btn"
        onclick="addSelectedToRightSection()"
        disabled>
        Add Item
    </button>

    <div class="form-group">
        <label>Cash Received</label>

        <input
            type="number"
            step="0.01"
            class="form-control"
            name="payment"
            id="left_payment"
            placeholder="0.00"
            oninput="calculateSaleChange()"
            required
        >
    </div>

</div>
            
            
            <input type="hidden" id="selected_product_id">
            <input type="hidden" id="selected_product_price">
            <input type="hidden" id="selected_product_stock">
           
            
        </div>

        <!-- RIGHT SECTION: CURRENT SALE DETAIL -->
        <div class="sale-summary-card"
     id="right_section_cell"
     style="display:none;">

    <div class="summary-header">
        <h2>Current Sale</h2>

        <button
            type="button"
            class="cancel-btn"
            onclick="cancelTransaction()">
            Cancel
        </button>
    </div>

    <table class="sale-table" id="sale_detail_table">
                <thead>
                    <tr>
                        <th>Buy</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_products_list as $prod): ?>
                        <tr id="row_<?php echo $prod['product_id']; ?>" style="display: none;">
                            <td align="center"><input type="checkbox" name="checked_products[]" value="<?php echo $prod['product_id']; ?>" id="chk_<?php echo $prod['product_id']; ?>" onchange="toggleItemActivation('<?php echo $prod['product_id']; ?>', <?php echo $prod['selling_price']; ?>)"></td>
                            <td><?php echo htmlspecialchars($prod['product_name']); ?></td>
                            <td align="right">₱<?php echo number_format($prod['selling_price'], 2); ?></td>
                            <td><input type="number" name="quantity[<?php echo $prod['product_id']; ?>]" id="qty_<?php echo $prod['product_id']; ?>" min="1" max="<?php echo $prod['stock']; ?>" value="1" style="width: 40px;" oninput="recalculateRow('<?php echo $prod['product_id']; ?>', <?php echo $prod['selling_price']; ?>)" disabled></td>
                            <td align="right" id="sub_<?php echo $prod['product_id']; ?>">₱0.00</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="sale-totals">

    <div class="total-row">
        <span>Total</span>
        <strong>₱<span id="total_sale_display">0.00</span></strong>
    </div>

    <div class="total-row">
        <span>Change</span>
        <strong>₱<span id="change_due_display">0.00</span></strong>
    </div>

</div>
            <button
type="button"
id="trigger_checkout_btn"
class="checkout-btn"
onclick="initiateCheckoutVerification()">

Proceed & Print Receipt

</button>

            <div id="checkout_warning_panel" style="display: none; border: 2px solid red; margin-top: 15px; padding: 10px; background-color: #fffff0;">
                <h4>⚠️ CONFIRM TRANSACTION</h4>
                <p>Checked Items: <span id="warn_items_count">0</span><br>
                Total: ₱<span id="warn_total_val">0.00</span><br>
                Cash: ₱<span id="warn_cash_val">0.00</span><br>
                Change: ₱<span id="warn_change_val">0.00</span></p>
                <input type="checkbox" id="warning_verify_chk" onchange="toggleFinalCheckoutButton()"> I verify this is correct.<br><br>
                <button type="submit" name="process_sale" id="final_checkout_submit" disabled>Confirm</button>
                <button type="button" onclick="cancelCheckoutProcess()">Cancel</button>
            </div>
        </div>
    </div>
</form>
</div>
</div>
<script type="text/javascript">
    function applySearch() {
        var query = document.getElementById('catalog_search_input').value;
        window.location.href = "?category=<?php echo urlencode($category); ?>&subcategory=<?php echo urlencode($subcategory); ?>&search=" + encodeURIComponent(query);
    }
    function clearAllFilters() { window.location.href = "?"; }
    function selectProduct(id, name, price, stock) {
        document.getElementById('selected_product_id').value = id;
        document.getElementById('selected_product_price').value = price;
        document.getElementById('selected_product_stock').value = stock;
        document.getElementById('selected_display_name').innerText = name + " (Stock: " + stock + ")";
        var leftQtyInput = document.getElementById('left_quantity');
        leftQtyInput.disabled = false;
        leftQtyInput.value = 1;
        leftQtyInput.max = stock;
        document.getElementById('add_item_btn').disabled = false;
    }
    function addSelectedToRightSection() {
        var id = document.getElementById('selected_product_id').value;
        var qty = parseInt(document.getElementById('left_quantity').value);
        var price = parseFloat(document.getElementById('selected_product_price').value);
        var maxStock = parseInt(document.getElementById('selected_product_stock').value);
        if (qty > maxStock) { alert("Insufficient inventory!"); return; }
        var chk = document.getElementById('chk_' + id);
        var qtyInput = document.getElementById('qty_' + id);
        var row = document.getElementById('row_' + id);
        var rightCell = document.getElementById('right_section_cell');
        rightCell.style.display = 'block';
        row.style.display = 'table-row';
        chk.checked = true;
        qtyInput.disabled = false;
        qtyInput.value = qty;
        recalculateRow(id, price);
    }
    function toggleItemActivation(id, price) {
        var chk = document.getElementById('chk_' + id);
        var qtyInput = document.getElementById('qty_' + id);
        var row = document.getElementById('row_' + id);
        var rightCell = document.getElementById('right_section_cell');
        if (chk.checked) { rightCell.style.display = 'block'; row.style.display = 'table-row'; qtyInput.disabled = false; }
        else { row.style.display = 'none'; qtyInput.disabled = true; }
        recalculateRow(id, price);
    }
    function recalculateRow(id, price) {
        var chk = document.getElementById('chk_' + id);
        var qty = parseInt(document.getElementById('qty_' + id).value);
        var subtotal = (chk.checked) ? (price * (isNaN(qty) || qty < 1 ? 1 : qty)) : 0;
        document.getElementById('sub_' + id).innerText = "₱" + subtotal.toFixed(2);
        calculateOverallSaleTotal();
    }
    function calculateOverallSaleTotal() {
        var total = 0.00;
        document.querySelectorAll('input[name="checked_products[]"]:checked').forEach(function(chk) {
            var id = chk.value;
            var price = parseFloat(document.getElementById('row_' + id).cells[2].innerText.replace('₱', ''));
            var qty = parseInt(document.getElementById('qty_' + id).value);
            total += (price * (isNaN(qty) ? 1 : qty));
        });
        document.getElementById('total_sale_display').innerText = total.toFixed(2);
        calculateSaleChange();
    }
    function calculateSaleChange() {
        var total = parseFloat(document.getElementById('total_sale_display').innerText);
        var payment = parseFloat(document.getElementById('left_payment').value);
        document.getElementById('change_due_display').innerText = (isNaN(payment) ? 0 : payment - total).toFixed(2);
    }
    function initiateCheckoutVerification() {
        var total = parseFloat(document.getElementById('total_sale_display').innerText);
        var payment = parseFloat(document.getElementById('left_payment').value);
        if (total === 0) { alert("Select at least 1 item."); return; }
        if (isNaN(payment) || payment < total) { alert("Insufficient payment."); return; }
        document.getElementById('warn_total_val').innerText = total.toFixed(2);
        document.getElementById('warn_cash_val').innerText = payment.toFixed(2);
        document.getElementById('warn_change_val').innerText = (payment - total).toFixed(2);
        document.getElementById('checkout_warning_panel').style.display = 'block';
    }
    function toggleFinalCheckoutButton() { document.getElementById('final_checkout_submit').disabled = !document.getElementById('warning_verify_chk').checked; }
    function cancelCheckoutProcess() { document.getElementById('checkout_warning_panel').style.display = 'none'; }
    function cancelTransaction() { location.reload(); }
</script>





</body>
</html>