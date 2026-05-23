<div class="sidebar">

    <div class="logo">
        SALES SYSTEM
    </div>

    <!-- COMMON -->
    <a href="dashboard.php">
        Dashboard
    </a>

    <a href="modules/products/view_products.php">
        Products
    </a>

    <a href="modules/sales/new_sale.php">
        Sales
    </a>

    <!-- ADMIN ONLY -->
    <?php if($_SESSION['role'] == 'admin'){ ?>

        <a href="modules/inventor/stock_in.php">
            Inventory
        </a>

        <a href="modules/rep/sales_report.php">
            Reports
        </a>

        <a href="modules/users/view_users.php">
            Users
        </a>

    <?php } ?>

</div>