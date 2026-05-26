<?php

session_start();

require_once dirname(__DIR__, 2)
. '/config/database.php';

if($_SESSION['role'] != 'cashier'){
    header("Location: ../../dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];

mysqli_query($conn,
    "UPDATE product_requests
     SET viewed = 1
     WHERE requested_by = '$user_id'
     AND status != 'pending'
     AND viewed = 0"
);

/* FETCH REQUESTS */
$query = mysqli_query($conn,

    "SELECT *

     FROM product_requests

     WHERE requested_by='$user_id'

     ORDER BY request_id DESC"

);

?>

<!DOCTYPE html>
<html>
<head>

    <title>My Requests</title>

    <link rel="stylesheet"
          href="../../assets/css/style.css">

</head>
<body>

<?php include dirname(__DIR__, 2) . '/includes/navbar.php'; ?>

<!-- SIDEBAR -->
<?php include dirname(__DIR__, 2) . '/includes/sidebar.php'; ?>

<div class="main-content">

<?php if (isset($_GET['success']) && $_GET['success'] == 1) { ?>
    <div class="alert alert-success">
        Product request submitted successfully.
    </div>
<?php } ?>


<div class="sales-page">

      <div>
            <h1 class="sales-title">Request Board</h1>
            <p class="sales-subtitle">
                Monitor the status of your product requests and stay informed about inventory needs
            </p>
        </div>

   
    </div>

    <div class="table-container">

        <h2>My Product Requests</h2>

        <table>

            <tr>

                <th>Product</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Date</th>

            </tr>

            <?php while($row = mysqli_fetch_assoc($query)){ ?>

            <tr>

                <td><?php echo $row['product_name']; ?></td>

                <td><?php echo $row['category']; ?></td>

                <td><?php echo $row['quantity']; ?></td>

                <td>

                    <?php

                    if($row['status'] == 'approved'){

                        echo '<span class="badge badge-success">
                              Approved
                              </span>';

                    }elseif($row['status'] == 'rejected'){

                        echo '<span class="badge badge-danger">
                              Rejected
                              </span>';

                    }else{

                        echo '<span class="badge badge-warning">
                              Pending
                              </span>';
                    }

                    ?>

                </td>

                <td><?php echo $row['created_at']; ?></td>

            </tr>

            <?php } ?>

        </table>

    </div>

</div>

</body>
</html>