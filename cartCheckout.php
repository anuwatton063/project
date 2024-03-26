<?php
// เรียกใช้งานไฟล์เชื่อมต่อกับฐานข้อมูล
include('condb.php');
session_start();

// ตรวจสอบว่าฟอร์มถูกส่งมาหรือไม่
if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cartItems = $_SESSION['cart'];
} else {
    $cartItems = array();
}

// คำนวณราคารวมทั้งหมด
$totalPrice = 0;
foreach($cartItems as &$item) {
    $item['totalPrice'] = $item['quantity'] * $item['price'];
    $totalPrice += $item['totalPrice'];
}
unset($item);

// เรียกดึงที่อยู่ของผู้ใช้
$sql = "SELECT * FROM address WHERE user_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_ID']);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// เพิ่มข้อมูลในตาราง orders
if(isset($_POST['address'])) {
    $address_ID = $_POST['address'];
    $user_ID = $_SESSION['user_ID'];
    $orderstatus_ID = 5; // สมมติว่า 1 แทนสถานะการสั่งซื้อที่รอดำเนินการ
    $shipping_status_ID = 1; // สมมติว่า 1 แทนสถานะการจัดส่งที่รอดำเนินการ
    $net_price = $totalPrice;

    $insertOrderSql = "INSERT INTO orders (address_ID, user_ID, orderstatus_ID, shipping_status_ID, net_price) VALUES (?, ?, ?, ?, ?)";
    $stmtInsertOrder = $conn->prepare($insertOrderSql);
    $stmtInsertOrder->bind_param("iiidd", $address_ID, $user_ID, $orderstatus_ID, $shipping_status_ID, $net_price);
    $stmtInsertOrder->execute();
    $order_ID = $stmtInsertOrder->insert_id;
    $stmtInsertOrder->close();

    // เพิ่มข้อมูลในตาราง orders_detail
    foreach($cartItems as $item) {
        $productName = $item['productName'];
        $getProductIDSql = "SELECT product_ID FROM products_phone WHERE product_name = ?";
        $stmtGetProductID = $conn->prepare($getProductIDSql);
        $stmtGetProductID->bind_param("s", $productName);
        $stmtGetProductID->execute();
        $productIDResult = $stmtGetProductID->get_result();

        if($productIDResult->num_rows > 0) {
            $productIDRow = $productIDResult->fetch_assoc();
            $product_ID = $productIDRow['product_ID'];
            $quantity = $item['quantity'];
            $product_price = $item['price'];
            $total_price = $item['totalPrice'];

            $insertDetailSql = "INSERT INTO orders_details (order_ID, product_ID, quantity, product_price, total_price) VALUES (?, ?, ?, ?, ?)";
            $stmtInsertDetail = $conn->prepare($insertDetailSql);
            $stmtInsertDetail->bind_param("iiidd", $order_ID, $product_ID, $quantity, $product_price, $total_price);
            $stmtInsertDetail->execute();
            $stmtInsertDetail->close();
        } else {
            echo "Product not found.";
        }
    }

    // ล้างเซสชันหลังจากทำการสั่งซื้อ
    unset($_SESSION['cart']);

    // แปลงหน้าไปยังหน้าขอบคุณหรือหน้าที่ต้องการหลังจากการสั่งซื้อ
    header("Location: index.php");
    exit();
}
?>
