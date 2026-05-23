<?php

session_start();

require_once dirname(__DIR__, 2)
. '/config/database.php';

$id = $_GET['id'];

mysqli_query($conn,

    "UPDATE product_requests

     SET status='rejected'

     WHERE request_id='$id'"

);

header("Location: view_requests.php");
exit();

?>