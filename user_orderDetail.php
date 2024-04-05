<?php
include('condb.php');
include 'navbar-user.php';

if (!isset($_SESSION['user_ID'])) {
    header("Location: login.php");
    exit();
}

if(isset($_GET['orderID'])) {
    $orderID = $_GET['orderID'];

    $address_sql = "SELECT * FROM address WHERE user_ID = ? AND address_ID IN (SELECT address_ID FROM orders WHERE order_ID = ?)";
    $address_stmt = $conn->prepare($address_sql);
    $address_stmt->bind_param("ii", $_SESSION['user_ID'], $orderID);
    $address_stmt->execute();
    $address_result = $address_stmt->get_result();

    $order_sql = "SELECT orders_details.*, products_phone.product_name, products_phone.product_cover_image 
            FROM orders_details 
            INNER JOIN products_phone ON orders_details.product_ID = products_phone.product_ID 
            WHERE orders_details.order_ID = ?";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("i", $orderID);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();

    $payment_sql = "SELECT * FROM payment WHERE order_ID = ?";
    $payment_stmt = $conn->prepare($payment_sql);
    $payment_stmt->bind_param("i", $orderID);
    $payment_stmt->execute();
    $payment_result = $payment_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Order Details</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="css/styles.css" rel="stylesheet" />

</head>

<body>
    <div class="container">
        <h1 class="mt-5">รายละเอียดข้อมูล</h1>
        
        <div class="mt-4">
            <h2>ข้อมูลที่อยู่</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ชื่อ</th>
                        <th>เบอร์โทรศัพท์</th>
                        <th>ข้อมูลที่อยู่</th>
                        <th>ตำบล</th>
                        <th>อำเภอ</th>
                        <th>จังหวัด</th>
                        <th>รหัสไปรษณีย์</th><br>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($address_result->num_rows > 0) {
                        while ($row = $address_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['address_ID'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['phone'] . "</td>";
                            echo "<td>" . $row['Address_information'] . "</td>";
                            echo "<td>" . $row['tumbon'] . "</td>";
                            echo "<td>" . $row['amphoe'] . "</td>";
                            echo "<td>" . $row['province'] . "</td>";
                            echo "<td>" . $row['Zipcode'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No address details found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="table-responsive mt-4">
            <br><h2>รายละเอียดสินค้า</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>ชื่อ</th>
                        <th>จำนวน</th>
                        <th>ราคา</th>
                        <th>ราคารวม</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($order_result->num_rows > 0) {
                        while ($row = $order_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><img src='../project/png/" . $row['product_cover_image'] . "' alt='" . $row['product_name']. "' width='100' height='100'> "."</td>";
                            echo "<td>" . $row['product_name'] . "</td>";
                            echo "<td>" . $row['quantity'] . "</td>";
                            echo "<td>฿ " . number_format($row['product_price'],2) . "</td>";
                            echo "<td>฿ " . number_format($row['total_price'],2) . "</td>";

                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No order details found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <h2>การชำระเงิน</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>ราคารวม</th>
                        <th>สลิปการโอนเงิน</th>
                        <th>เวลาการจ่ายเงิน</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($payment_result->num_rows > 0) {
                        while ($row = $payment_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['order_ID'] . "</td>";
                            echo "<td>฿ " . number_format($row['net_price'],2) . "</td>";
                            echo "<td><a href='../project/slip/" . $row['transfer_slip'] . "' target='_blank'><img src='../project/slip/" . $row['transfer_slip'] . "' alt='Transfer Slip' width='100' height='100'></a></td>";
                            echo "<td>" . $row['paid_date_time'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>ยังไม่ทำการจ่ายเงิน</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>

<?php
} else {
    echo "No order ID provided.";
}
?>