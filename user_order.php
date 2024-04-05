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

// Check if order cancellation request is made
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        /* Add any custom styles here */
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mt-5">ข้อมูลการสั่งซื้อ</h1>
        <div class="table-responsive mt-4">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>สถานะสินค้า</th>
                        <th>การจัดส่ง</th>
                        <th>ราคา</th>
                        <th>วันที่สั่งซื้อ</th>
                        <th>รายละเอียด</th>
                        <th>ชำระเงิน</th>
                        <th>ยกเลิกคำสั่งซื้อ</th> <!-- New column for Cancel Order button -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch user's orders from the database with joined tables
                    $sql = "SELECT orders.order_ID, orders.orderstatus_ID, orders_status.order_status, shipping_status.status_name, orders.net_price, orders.date_time
                            FROM orders 
                            INNER JOIN orders_status ON orders.orderstatus_ID = orders_status.orderstatus_ID 
                            INNER JOIN shipping_status ON orders.shipping_status_ID = shipping_status.status_ID
                            WHERE orders.user_ID = ?
                            ORDER BY orders.order_ID DESC"; // Added ORDER BY clause
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
                            echo "<td>฿ " . number_format($row['net_price'],2) . "</td>";
                            echo "<td>" . date('d/m/Y H:i:s', strtotime($row['date_time'])) . "</td>";
                            echo "<td><a href='user_orderDetail.php?orderID=" . $row['order_ID'] . "' class='btn btn-primary'>รายระเอียด</a></td>";

                            echo "<td>";
                            if ($row['orderstatus_ID'] == 1 or $row['orderstatus_ID'] == 2) {
                                echo "<a href='user_payment.php?orderID=" . $row['order_ID'] . "' class='btn btn-success'>ชำระงิน</a>";
                            } else {
                                echo "<button class='btn btn-success' disabled>ชำระงิน</button>";
                            }
                            echo "</td>";

                            // Display Cancel Order button only if order status is 'Pending'
                            if ($row['orderstatus_ID'] == 1) {
                                echo "<td>
                                        <form method='POST' onsubmit='return confirm(\"Are you sure you want to cancel this order?\");'>
                                            <input type='hidden' name='cancelOrder' value='1'>
                                            <input type='hidden' name='orderID' value='" . $row['order_ID'] . "'>
                                            <button type='submit' class='btn btn-danger'>ยกเลิกคำสั่งซื้อ</button>
                                        </form>
                                      </td>";
                            } else {
                                echo "<td>
                                <form method='POST' onsubmit='return confirm(\"Are you sure you want to cancel this order?\");'>
                                    <input type='hidden' name='cancelOrder' value='1'>
                                    <input type='hidden' name='orderID' value='" . $row['order_ID'] . "'>
                                    <button type='submit' class='btn btn-danger'disabled>ยกเลิกคำสั่งซื้อ</button>
                                </form>
                              </td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No orders found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
<?php 
if(isset($_POST['cancelOrder']) && isset($_POST['orderID'])) {
    $orderID = $_POST['orderID'];
    // Start a transaction to ensure data consistency
    $conn->begin_transaction();

    // Update order status to 5 (Cancelled)
    $update_sql = "UPDATE orders SET orderstatus_ID = 5 WHERE order_ID = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $orderID);
    if($update_stmt->execute()) {
        // Update product quantities in stock
        $return_stock_sql = "UPDATE products_phone 
                             INNER JOIN orders_details ON products_phone.product_ID = orders_details.product_ID
                             SET products_phone.product_stock = products_phone.product_stock + orders_details.quantity
                             WHERE orders_details.order_ID = ?";
        $return_stock_stmt = $conn->prepare($return_stock_sql);
        $return_stock_stmt->bind_param("i", $orderID);
        if($return_stock_stmt->execute()) {
            // Commit the transaction if all queries succeed
            $conn->commit();
            // Redirect back to the same page
            echo "<meta http-equiv='refresh' content='0'>";
            exit();
        } else {
            // Rollback the transaction if updating stock fails
            $conn->rollback();
            echo json_encode(array('status' => 'error', 'message' => 'Error returning stock.'));
            exit();
        }
    } else {
        // Rollback the transaction if updating order status fails
        $conn->rollback();
        echo json_encode(array('status' => 'error', 'message' => 'Error cancelling order.'));
        exit();
    }
}
?>
</html>
