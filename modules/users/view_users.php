<?php
session_start();
include __DIR__ . '/../../config/database.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../../dashboard.php");
    exit();
}

$query = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Users</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<?php include dirname(__DIR__, 2) . '/includes/navbar.php'; ?>
<?php include dirname(__DIR__, 2) . '/includes/sidebar.php'; ?>

<div class="main-content">

    <div class="sales-page">

      <div>
            <h1 class="sales-title">User List</h1>
            <p class="sales-subtitle">
                Manage system users and account roles
            </p>
        </div>

    </div>

    <div class="table-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>User Accounts</h2>
        <div class="page-actions">
            <button type="button" class="btn btn-primary" onclick="openAddUserModal()">
                Add User
            </button>
    </div>
</div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (mysqli_num_rows($query) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>

                            <td>
                                <strong><?php echo htmlspecialchars($row['fullname']); ?></strong>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['username']); ?>
                            </td>

                            <td>
                                <?php if ($row['role'] == 'admin') { ?>
                                    <span class="badge badge-success">Admin</span>
                                <?php } else { ?>
                                    <span class="badge badge-warning">
                                        <?php echo htmlspecialchars(ucfirst($row['role'])); ?>
                                    </span>
                                <?php } ?>
                            </td>

                            <td style="text-align: center;">
                                <a
                                    href="edit_user.php?id=<?php echo $row['id']; ?>"
                                    class="btn btn-warning"
                                    style="padding: 6px 12px; font-size: 13px;">
                                    Edit
                                </a>

                                <a
                                    href="delete_user.php?id=<?php echo $row['id']; ?>"
                                    class="btn btn-danger"
                                    style="padding: 6px 12px; font-size: 13px;"
                                    onclick="return confirmDeleteUser('<?php echo htmlspecialchars($row['username'], ENT_QUOTES); ?>');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="empty-table-message">
                            No users found.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<!-- ADD USER MODAL -->
<div class="modal-overlay" id="addUserModal">

    <div class="add-product-card">

        <div class="modal-header">
            <h2>Add User</h2>

            <button type="button" class="modal-close" onclick="closeAddUserModal()">
                &times;
            </button>
        </div>

        <form method="POST" action="add_user.php" class="add-product-form">

            <input
                type="text"
                name="fullname"
                placeholder="Full Name"
                required
            >

            <input
                type="text"
                name="username"
                placeholder="Username"
                required
            >

            <input
                type="password"
                name="password"
                placeholder="Password"
                required
            >

            <select name="role" required>
                <option value="">Select Role</option>
                <option value="admin">Admin</option>
                <option value="cashier">Cashier</option>
            </select>

            <button type="submit" name="add_user" class="submit-product-btn">
                Save User
            </button>

        </form>

    </div>

</div>

<script>
function openAddUserModal() {
    document.getElementById('addUserModal').style.display = 'flex';
}

function closeAddUserModal() {
    document.getElementById('addUserModal').style.display = 'none';
}

function confirmDeleteUser(username) {
    return confirm(
        'Warning: You are about to delete user "' + username + '".\n\n' +
        'This action cannot be undone. Do you want to continue?'
    );
}

window.onclick = function(event) {
    var modal = document.getElementById('addUserModal');

    if (event.target === modal) {
        closeAddUserModal();
    }
}
</script>

</body>
</html>