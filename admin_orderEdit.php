<?php
include('condb.php'); // Include your database connection file
include 'navbar-user.php';
include 'checkuser.php';

        $user_type_ID = getUserTypeID();
        if ($user_type_ID == 1) {
            include 'navbar-admin.php';
            
        }
        if ($user_type_ID != 1){
            header("Location: index.php"); // Redirect to index.php
            exit(); // Ensure script execution stops after redirection
        }
        
// Fetch order details based on order ID
if(isset($_GET['orderID']) && !empty($_GET['orderID'])) {
    $orderID = $_GET['orderID'];

    // Fetch order details
    $sql = "SELECT orders.order_ID, orders_status.orderstatus_ID, orders_status.order_status, shipping_status.status_ID, shipping_status.status_name
            FROM orders
            INNER JOIN orders_status ON orders.orderstatus_ID = orders_status.orderstatus_ID
            INNER JOIN shipping_status ON orders.shipping_status_ID = shipping_status.status_ID
            WHERE orders.order_ID = $orderID";
    $result = $conn->query($sql);

    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $order_status_ID = $row['orderstatus_ID'];
        $order_status = $row['order_status'];
        $shipping_status_ID = $row['status_ID'];
        $shipping_status = $row['status_name'];
    } else {
        echo "Order not found.";
        exit();
    }
} else {
    echo "Invalid order ID.";
    exit();
}

// Handle status update form submission
if(isset($_POST['updateStatus'])) {
    $new_order_status = $_POST['newOrderStatus'];
    $new_shipping_status = $_POST['newShippingStatus'];

    // Update order status and shipping status in the database
    $update_sql = "UPDATE orders SET orderstatus_ID = $new_order_status, shipping_status_ID = $new_shipping_status WHERE order_ID = $orderID";
    if($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Order status and shipping status updated successfully.'); window.location.href = 'admin_order.php';</script>";
    } else {
        echo "Error updating order status and shipping status: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Update Order </title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        /* Add any custom styles here */
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mt-5">สถานะการสั่งซื้อ</h1>
        <div class="mt-4">
            <form method="post">
                <div class="mb-3">
                    <label for="currentOrderStatus" class="form-label">สถานะ:</label>
                    <input type="text" class="form-control" id="currentOrderStatus" value="<?php echo $order_status; ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="newOrderStatus" class="form-label">สถานนะหลังเปลี่ยนแปลง:</label>
                    <select class="form-select" id="newOrderStatus" name="newOrderStatus" required>
                        <option value="" selected disabled>Select New Order Status</option>
                        <!-- Fetch and display available order statuses from the database -->
                        <?php
                        $status_sql = "SELECT orderstatus_ID, order_status FROM orders_status";
                        $status_result = $conn->query($status_sql);
                        if($status_result->num_rows > 0) {
                            while($status_row = $status_result->fetch_assoc()) {
                                echo "<option value='".$status_row['orderstatus_ID']."'>".$status_row['order_status']."</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="currentShippingStatus" class="form-label">การจัดส่ง:</label>
                    <input type="text" class="form-control" id="currentShippingStatus" value="<?php echo $shipping_status; ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="newShippingStatus" class="form-label">สถานนะหลังเปลี่ยนแปลง:</label>
                    <select class="form-select" id="newShippingStatus" name="newShippingStatus" required>
                        <option value="" selected disabled>Select New Shipping Status</option>
                        <!-- Fetch and display available shipping statuses from the database -->
                        <?php
                        $shipping_status_sql = "SELECT status_ID, status_name FROM shipping_status";
                        $shipping_status_result = $conn->query($shipping_status_sql);
                        if($shipping_status_result->num_rows > 0) {
                            while($shipping_status_row = $shipping_status_result->fetch_assoc()) {
                                echo "<option value='".$shipping_status_row['status_ID']."'>".$shipping_status_row['status_name']."</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" name="updateStatus" class="btn btn-primary">Update Status</button>
            </form>
        </div>
    </div>
</body>

</html>