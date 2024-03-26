<?php
ob_start(); // Start output buffering

include 'navbar-user.php';
include('condb.php');

include 'checkuser.php';

$user_type_ID = getUserTypeID();
if ($user_type_ID == 1) {
    include 'navbar-admin.php';
}
if ($user_type_ID != 1) {
    header("Location: index.php"); // Redirect to index.php
    exit(); // Ensure script execution stops after redirection
}

// Function to set session message
function setSessionMessage($message) {
    $_SESSION['message'] = $message;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>All Orders</title>
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
        <h1 class="mt-5">All Orders</h1>
        <div class="table-responsive mt-4">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User ID</th>
                        <th>Order Status</th>
                        <th>Shipping Status</th>
                        <th>Total Price</th>
                        <th>View Details</th>
                        <th>Delete</th><!-- New column for delete button -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch all orders from the database with joined tables
                    $sql = "SELECT orders.order_ID, orders.user_ID, orders_status.order_status, shipping_status.status_name, orders.net_price 
                            FROM orders 
                            INNER JOIN orders_status ON orders.orderstatus_ID = orders_status.orderstatus_ID 
                            INNER JOIN shipping_status ON orders.shipping_status_ID = shipping_status.status_ID";
                    $result = $conn->query($sql);

                    // Check if there are any orders
                    if ($result->num_rows > 0) {
                        // Loop through each order
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['order_ID'] . "</td>";
                            echo "<td>" . $row['user_ID'] . "</td>";
                            echo "<td>" . $row['order_status'] . "</td>";
                            echo "<td>" . $row['status_name'] . "</td>";
                            echo "<td>$" . $row['net_price'] . "</td>";
                            echo "<td><a href='user_orderDetail.php?orderID=" . $row['order_ID'] . "' class='btn btn-primary'>View Details</a></td>";
                            echo "<td>
                                    <form method='post'>
                                        <input type='hidden' name='orderID' value='" . $row['order_ID'] . "' />
                                        <button type='submit' name='deleteOrder' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this order?\")'>Delete</button>
                                    </form>
                                </td>"; // Delete button
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No orders found.</td></tr>";
                    }

                    // Handle order deletion
                    if (isset($_POST['deleteOrder'])) {
                        $orderID = $_POST['orderID'];

                        // Delete associated details first
                        $delete_details_sql = "DELETE FROM orders_details WHERE order_ID = $orderID";
                        if ($conn->query($delete_details_sql) === TRUE) {
                            // Proceed with deleting the order
                            $delete_sql = "DELETE FROM orders WHERE order_ID = $orderID";
                            if ($conn->query($delete_sql) === TRUE) {
                                setSessionMessage("Order $orderID has been deleted."); // Set success message
                                // Redirect to avoid resubmission
                                header("Location: {$_SERVER['PHP_SELF']}");
                                exit();
                            } else {
                                echo "<script>alert('Error deleting order: " . $conn->error . "');</script>";
                            }
                        } else {
                            echo "<script>alert('Error deleting associated details: " . $conn->error . "');</script>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <!-- Display session message -->
        <?php
        // Check if there is a session message
        if (isset($_SESSION['message'])) {
            echo "<div class='alert alert-success' role='alert'>" . $_SESSION['message'] . "</div>";
            unset($_SESSION['message']); // Clear the session message
        }
        ?>
    </div>
</body>

</html>
<?php
ob_end_flush(); // Flush the output buffer
?>
