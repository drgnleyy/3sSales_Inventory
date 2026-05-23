<?php

session_start();
require_once dirname(__DIR__, 2) . '/config/database.php';

/* SECURITY: only admin can access */
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../../dashboard.php");
    exit();
}

/* FETCH USERS */
$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Users</title>
</head>
<body>

<h2>Users List</h2>

<a href="add_user.php">+ Add User</a>

<br><br>

<table border="1" cellpadding="10">

    <tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Username</th>
        <th>Role</th>
        <th>Actions</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($result)){ ?>

    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['fullname']; ?></td>
        <td><?php echo $row['username']; ?></td>
        <td><?php echo $row['role']; ?></td>
        <td>
            <a href="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a>
            |
            <a href="delete_user.php?id=<?php echo $row['id']; ?>"
               onclick="return confirm('Delete this user?')">
               Delete
            </a>
        </td>
    </tr>

    <?php } ?>

</table>

</body>
</html>