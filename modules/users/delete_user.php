<?php

session_start();
include __DIR__ . '/../../config/database.php';

if($_SESSION['role'] != 'admin'){
    header("Location: ../../dashboard.php");
    exit();
}

$id = $_GET['id'];

mysqli_query($conn,

    "DELETE FROM users
     WHERE id='$id'"

);

header("Location: view_users.php");
exit();

?>