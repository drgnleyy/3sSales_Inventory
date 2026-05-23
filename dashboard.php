<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>

    <style>
        body {
            margin: 0;
            font-family: Arial;
        }

        /* TOP NAVBAR */
        .navbar {
            background: #333;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
        }

        /* SIDEBAR */
        .sidebar {
            width: 200px;
            height: 100vh;
            background: #222;
            color: white;
            position: fixed;
            top: 40px;
            left: 0;
            padding: 10px;
        }

        .sidebar a {
            color: white;
            display: block;
            padding: 10px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: #444;
        }

        /* CONTENT */
        .content {
            margin-left: 210px;
            padding: 20px;
        }
    </style>

</head>
<body>

<!-- TOP NAVBAR -->
<div class="navbar">
    <div>
        Welcome, <?php echo $_SESSION['username']; ?>
    </div>

    <div>
        Role: <?php echo $_SESSION['role']; ?>
        | <a href="logout.php" style="color:white;">Logout</a>
    </div>
</div>

<!-- SIDEBAR -->
<div class="sidebar">

    <h3>Menu</h3>

    <a href="dashboard.php">Dashboard</a>
    <a href="modules/products/view_products.php">Products</a>
    <a href="modules/sales/new_sale.php">Sales</a>

    <!-- ADMIN ONLY -->
    <?php if($_SESSION['role'] == 'admin'){ ?>

        <a href="modules/inventory/stock_in.php">Inventory</a>
        <a href="modules/reports/sales_report.php">Reports</a>
        <a href="modules/users/view_users.php">Users</a>

    <?php } ?>

</div>

<!-- MAIN CONTENT -->
<div class="content">

    <h1>Dashboard</h1>

    <p>This is your admin/cashier dashboard.</p>

    <!-- SAMPLE CARDS -->
    <div style="display:flex; gap:10px;">

        <div style="background:#eee; padding:20px; width:150px;">
            Products
        </div>

        <div style="background:#eee; padding:20px; width:150px;">
            Sales
        </div>

        <div style="background:#eee; padding:20px; width:150px;">
            Reports
        </div>

    </div>

</div>

</body>
</html>