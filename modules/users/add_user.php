<?php

session_start();
include __DIR__ . '/../../config/database.php';

if($_SESSION['role'] != 'admin'){
    header("Location: ../../dashboard.php");
    exit();
}

if(isset($_POST['add_user'])){

    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];

    mysqli_query($conn,

        "INSERT INTO users
        (fullname, username, password, role)

        VALUES

        ('$fullname',
         '$username',
         '$password',
         '$role')"

    );

    header("Location: view_users.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
</head>
<body>

<h2>Add User</h2>

<form method="POST">

    <input type="text"
           name="fullname"
           placeholder="Full Name"
           required>

    <br><br>

    <input type="text"
           name="username"
           placeholder="Username"
           required>

    <br><br>

    <input type="password"
           name="password"
           placeholder="Password"
           required>

    <br><br>

    <select name="role" required>

        <option value="">Select Role</option>

        <option value="admin">Admin</option>

        <option value="cashier">Cashier</option>

    </select>

    <br><br>

    <button type="submit" name="add_user">
        Add User
    </button>

</form>

</body>
</html>