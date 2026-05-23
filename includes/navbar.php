<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Dynamically locate config/database.php absolute path relative to this file
require_once dirname(__DIR__) . '/config/database.php';

// Automatically calculate the project's root web folder path if not already set
if (!isset($base_url)) {
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
}

/* ADMIN PENDING REQUESTS */
$request_query = mysqli_query($conn,
    "SELECT COUNT(*) AS total_requests
     FROM product_requests
     WHERE status='pending'"
);
$request = mysqli_fetch_assoc($request_query);

/* CASHIER NOTIFICATIONS */
$cashier_notifications = 0;
if (isset($_SESSION['role']) && $_SESSION['role'] == 'cashier') {
    $user_id = $_SESSION['user_id'];
    $notif_query = mysqli_query($conn,
        "SELECT COUNT(*) AS total_notif
         FROM product_requests
         WHERE requested_by='$user_id'
         AND status != 'pending'
         AND viewed=0"
    );
    $notif = mysqli_fetch_assoc($notif_query);
    $cashier_notifications = $notif['total_notif'];
}
?>

<div class="navbar">

    <div>
        Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
    </div>

    <div class="admin-info">

        <!-- ROLE -->
        <span>
            Role: <?php echo htmlspecialchars($_SESSION['role'] ?? ''); ?>
        </span>

        <!-- ADMIN ALERT -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'){ ?>
            <a href="<?php echo $base_url; ?>modules/requests/view_requests.php">
                🔔 <?php echo $request['total_requests']; ?>
            </a>
        <?php } ?>

        <!-- CASHIER ALERT -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'cashier'){ ?>
            <a href="<?php echo $base_url; ?>modules/requests/my_requests.php">
                🔔 <?php echo $cashier_notifications; ?>
            </a>
        <?php } ?>

        <!-- LOGOUT -->
        <a href="<?php echo $base_url; ?>logout.php">
            Logout
        </a>

    </div>

</div>