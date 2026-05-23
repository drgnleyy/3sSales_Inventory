<div class="navbar">

    <div>
        Welcome, <?php echo $_SESSION['username']; ?>
    </div>

    <div class="admin-info">

        <span>
            Role: <?php echo $_SESSION['role']; ?>
        </span>

        <a href="logout.php">
            Logout
        </a>

    </div>

</div>