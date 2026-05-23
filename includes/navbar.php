<div style="background:#333; color:white; padding:10px; display:flex; justify-content:space-between;">

    <div>
        Welcome, <?php echo $_SESSION['username']; ?>
    </div>

    <div>
        Role: <?php echo $_SESSION['role']; ?>
        |
        <a href="logout.php" style="color:white;">Logout</a>
    </div>

</div>