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
/* ADMIN PENDING REQUEST LIST */
$admin_pending_requests = null;

if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    $admin_pending_requests = mysqli_query($conn,
        "SELECT pr.*, users.username
         FROM product_requests pr
         INNER JOIN users ON pr.requested_by = users.id
         WHERE pr.status = 'pending'
         ORDER BY pr.created_at DESC
         LIMIT 5"
    );
}

/* CASHIER ACTION NOTIFICATIONS */
$cashier_request_updates = null;

if (isset($_SESSION['role']) && $_SESSION['role'] == 'cashier') {
    $user_id = $_SESSION['user_id'];

    $cashier_request_updates = mysqli_query($conn,
        "SELECT *
         FROM product_requests
         WHERE requested_by = '$user_id'
         AND status != 'pending'
         AND viewed = 0
         ORDER BY created_at DESC
         LIMIT 5"
    );
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
            <div class="notif-wrapper">
    <button type="button" class="notif-btn" onclick="toggleNotifDropdown('adminNotifDropdown')">
        🔔 <span id="adminNotifCount"><?php echo $request['total_requests']; ?></span>
    </button>

    <div class="notif-dropdown" id="adminNotifDropdown">
        <div class="notif-title">Pending Requests</div>

        <?php if ($admin_pending_requests && mysqli_num_rows($admin_pending_requests) > 0) { ?>
            <?php while ($req = mysqli_fetch_assoc($admin_pending_requests)) { ?>
                <div class="notif-item" id="request_item_<?php echo $req['request_id']; ?>">
                    <p>
                        <strong><?php echo htmlspecialchars($req['username']); ?></strong>
                        wants to request
                        <strong><?php echo htmlspecialchars($req['quantity']); ?> pcs</strong>
                        of
                        <strong><?php echo htmlspecialchars($req['product_name']); ?></strong>.
                    </p>

                    <div class="notif-actions">
                        <button
                            type="button"
                            class="notif-approve"
                            onclick="handleRequestAction(<?php echo $req['request_id']; ?>, 'approved')">
                            Approve
                        </button>

                        <button
                            type="button"
                            class="notif-reject"
                            onclick="handleRequestAction(<?php echo $req['request_id']; ?>, 'rejected')">
                            Reject
                        </button>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="notif-empty">No pending requests.</div>
        <?php } ?>

        <a class="notif-view-all" href="<?php echo $base_url; ?>modules/requests/view_requests.php">
            View all requests
        </a>
    </div>
</div>
        <?php } ?>

        <!-- CASHIER ALERT -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'cashier'){ ?>
            <div class="notif-wrapper">
    <button type="button" class="notif-btn" onclick="toggleNotifDropdown('cashierNotifDropdown')">
        🔔 <?php echo $cashier_notifications; ?>
    </button>

    <div class="notif-dropdown" id="cashierNotifDropdown">
        <div class="notif-title">Request Updates</div>

        <?php if ($cashier_request_updates && mysqli_num_rows($cashier_request_updates) > 0) { ?>
            <?php while ($notif_row = mysqli_fetch_assoc($cashier_request_updates)) { ?>
                <div class="notif-item">
                    <p>
                        Your request for
                        <strong><?php echo htmlspecialchars($notif_row['quantity']); ?> pcs</strong>
                        of
                        <strong><?php echo htmlspecialchars($notif_row['product_name']); ?></strong>
                        was
                        <strong><?php echo htmlspecialchars($notif_row['status']); ?></strong>.
                    </p>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="notif-empty">No new updates.</div>
        <?php } ?>

        <a class="notif-view-all" href="<?php echo $base_url; ?>modules/requests/my_requests.php">
            View my requests
        </a>
    </div>
</div>
        <?php } ?>

        <!-- LOGOUT -->
        <a href="<?php echo $base_url; ?>logout.php">
            Logout
        </a>

    </div>

</div>
<script>
function toggleNotifDropdown(id) {
    var dropdown = document.getElementById(id);
    dropdown.classList.toggle('show');
}

function handleRequestAction(requestId, action) {
    var formData = new FormData();
    formData.append('request_id', requestId);
    formData.append('action', action);

    fetch('<?php echo $base_url; ?>modules/requests/request_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            var item = document.getElementById('request_item_' + requestId);
            var count = document.getElementById('adminNotifCount');

            if (item) {
                item.remove();
            }

            if (count) {
                var current = parseInt(count.innerText);
                count.innerText = Math.max(current - 1, 0);
            }
        }
    });
}

window.addEventListener('click', function(event) {
    if (!event.target.closest('.notif-wrapper')) {
        document.querySelectorAll('.notif-dropdown').forEach(function(dropdown) {
            dropdown.classList.remove('show');
        });
    }
});
</script>