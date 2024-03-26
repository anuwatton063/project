<?php
// Include database connection file
include('condb.php');

include 'navbar-user.php';

// Check if user is logged in
if (!isset($_SESSION['user_ID'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Your Orders</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        /* Add any custom styles here */
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mt-5">Your Orders</h1>
        <div class="table-responsive mt-4">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Status</th>
                        <th>Shipping Status</th>
                        <th>Total Price</th>
                        <th>View Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch user's orders from the database with joined tables
                    $sql = "SELECT orders.order_ID, orders_status.order_status, shipping_status.status_name, orders.net_price 
                            FROM orders 
                            INNER JOIN orders_status ON orders.orderstatus_ID = orders_status.orderstatus_ID 
                            INNER JOIN shipping_status ON orders.shipping_status_ID = shipping_status.status_ID
                            WHERE orders.user_ID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $_SESSION['user_ID']);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Check if there are any orders
                    if ($result->num_rows > 0) {
                        // Loop through each order
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['order_ID'] . "</td>";
                            echo "<td>" . $row['order_status'] . "</td>";
                            echo "<td>" . $row['status_name'] . "</td>";
                            echo "<td>$" . $row['net_price'] . "</td>";
                            echo "<td><a href='user_orderDetail.php?orderID=" . $row['order_ID'] . "' class='btn btn-primary'>View Details</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No orders found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>