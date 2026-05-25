<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Automatically calculate the project's root web folder path
$script_name = $_SERVER['SCRIPT_NAME'];
if (($pos = strpos($script_name, '/modules/')) !== false) {
    $base_url = substr($script_name, 0, $pos) . '/';
} elseif (($pos = strpos($script_name, '/includes/')) !== false) {
    $base_url = substr($script_name, 0, $pos) . '/';
} else {
    $base_url = dirname($script_name);
    if ($base_url == '\\' || $base_url == '/') {
        $base_url = '/';
    } else {
        $base_url = rtrim($base_url, '/') . '/';
    }
}

// Find current page file name to dynamically set the ".active" link style from your CSS
$current_page = basename($script_name);
?>

<div class="sidebar">

    <div class="logo">
        SALES SYSTEM
    </div>

    <!-- COMMON / CASHIER -->
    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'cashier'){ ?>
        <a href="<?php echo $base_url; ?>dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            Dashboard
        </a>

        <a href="<?php echo $base_url; ?>modules/products/view_prods.php" class="<?php echo ($current_page == 'view_prods.php') ? 'active' : ''; ?>">
            Products
        </a>

        <a href="<?php echo $base_url; ?>modules/sales/new_sale.php" class="<?php echo ($current_page == 'new_sale.php') ? 'active' : ''; ?>">
            Sales
        </a>
    <?php } ?>

    <!-- ADMIN ONLY -->
    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'){ ?>
        <a href="<?php echo $base_url; ?>dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            Dashboard
        </a>

        <a href="<?php echo $base_url; ?>modules/products/view_products.php" class="<?php echo ($current_page == 'view_products.php' || $current_page == 'add_product.php' || $current_page == 'edit_product.php') ? 'active' : ''; ?>">
            Products
        </a>

        <a href="<?php echo $base_url; ?>modules/sales/new_sale.php" class="<?php echo ($current_page == 'new_sale.php') ? 'active' : ''; ?>">
            Sales
        </a>

        <a href="<?php echo $base_url; ?>modules/inventor/inventor_history.php" class="<?php echo ($current_page == 'stock_in.php') ? 'active' : ''; ?>">
            Inventory
        </a>

        <a href="<?php echo $base_url; ?>modules/rep/sales_report.php" class="<?php echo ($current_page == 'sales_report.php') ? 'active' : ''; ?>">
            Reports
        </a>

        <a href="<?php echo $base_url; ?>modules/users/view_users.php" class="<?php echo ($current_page == 'view_users.php') ? 'active' : ''; ?>">
            Users
        </a>
    <?php } ?>

</div>