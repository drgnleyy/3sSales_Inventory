<?php

session_start();

require_once dirname(__DIR__, 2)
. '/config/database.php';

if($_SESSION['role'] != 'admin'){
    header("Location: ../../dashboard.php");
    exit();
}

$query = mysqli_query($conn,

    "SELECT product_requests.*,
            users.username

     FROM product_requests

     INNER JOIN users
     ON product_requests.requested_by = users.id

     ORDER BY request_id DESC"

);

?>

<!DOCTYPE html>
<html>
<head>

    <title>Requests</title>

    <link rel="stylesheet"
          href="../../assets/css/style.css">

</head>
<body>

<div class="main-content">

    <div class="table-container">

        <h2>Product Requests</h2>

        <table>

            <tr>

                <th>ID</th>
                <th>Requested By</th>
                <th>Product</th>
                <th>Category</th>
                <th>Qty</th>
                <th>Status</th>
                <th>Action</th>

            </tr>

            <?php while($row = mysqli_fetch_assoc($query)){ ?>

            <tr>

                <td><?php echo $row['request_id']; ?></td>

                <td><?php echo $row['username']; ?></td>

                <td><?php echo $row['product_name']; ?></td>

                <td><?php echo $row['category']; ?></td>

                <td><?php echo $row['quantity']; ?></td>

                <td><?php echo $row['status']; ?></td>

                <td>

                    <?php if($row['status'] == 'pending'){ ?>

                        <a href="approve_request.php?id=<?php echo $row['request_id']; ?>"
                           class="btn btn-success">

                           Approve

                        </a>

                        <a href="reject_request.php?id=<?php echo $row['request_id']; ?>"
                           class="btn btn-danger">

                           Reject

                        </a>

                    <?php } ?>

                </td>

            </tr>

            <?php } ?>

        </table>

    </div>

</div>

</body>
</html>