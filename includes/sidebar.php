<div style="width:200px; height:100vh; background:#222; color:white; float:left; padding:10px;">

    <h3>Menu</h3>

    <!-- COMMON (ADMIN + CASHIER) -->
    <a href="dashboard.php" style="color:white; display:block;">Dashboard</a>
    <a href="modules/products/view_products.php" style="color:white; display:block;">Products</a>
    <a href="modules/sales/new_sale.php" style="color:white; display:block;">Sales</a>

    <!-- ADMIN ONLY -->
    <?php if($_SESSION['role'] == 'admin'){ ?>

        <a href="modules/inventor/stock_in.php" style="color:white; display:block;">Inventory</a>
        <a href="modules/rep/sales_report.php" style="color:white; display:block;">Reports</a>
        <a href="modules/users/view_users.php" style="color:white; display:block;">Users</a>

    <?php } ?>

</div>