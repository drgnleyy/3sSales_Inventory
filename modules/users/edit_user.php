<?php

session_start();
include __DIR__ . '/../../config/database.php';

if($_SESSION['role'] != 'admin'){
    header("Location: ../../dashboard.php");
    exit();
}

$id = $_GET['id'];

$query = mysqli_query($conn,

    "SELECT * FROM users
     WHERE id='$id'"

);

$row = mysqli_fetch_assoc($query);

if(isset($_POST['update_user'])){

    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    mysqli_query($conn,

        "UPDATE users SET

        fullname='$fullname',
        username='$username',
        role='$role'

        WHERE id='$id'"

    );

    header("Location: view_users.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
</head>
<body>

<h2>Edit User</h2>

<form method="POST">

    <input type="text"
           name="fullname"
           value="<?php echo $row['fullname']; ?>">

    <br><br>

    <input type="text"
           name="username"
           value="<?php echo $row['username']; ?>">

    <br><br>

    <select name="role">

        <option value="admin"
        <?php if($row['role']=='admin') echo 'selected'; ?>>
            Admin
        </option>

        <option value="cashier"
        <?php if($row['role']=='cashier') echo 'selected'; ?>>
            Cashier
        </option>

    </select>

    <br><br>

    <button type="submit" name="update_user">
        Update User
    </button>

</form>

</body>
</html>