<?php
include('condb.php');
session_start();

if(isset($_SESSION['cart']) && !empty($_SESSION['cart']) && isset($_SESSION['user_ID'])) {
    $cartItems = $_SESSION['cart'];
} else {
    exit("Error: Cart is empty or user is not logged in.");
}

$totalPrice = 0;
foreach($cartItems as &$item) {
    $item['totalPrice'] = $item['quantity'] * $item['price'];
    $totalPrice += $item['totalPrice'];
}
unset($item);

$sql = "SELECT * FROM address WHERE user_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_ID']);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if(isset($_POST['address'])) {
    $address_ID = $_POST['address'];
    $user_ID = $_SESSION['user_ID'];
    $orderstatus_ID = 1; 
    $shipping_status_ID = 1; 
    $net_price = $totalPrice;

    $insertOrderSql = "INSERT INTO orders (address_ID, user_ID, orderstatus_ID, shipping_status_ID, net_price) VALUES (?, ?, ?, ?, ?)";
    $stmtInsertOrder = $conn->prepare($insertOrderSql);
    $stmtInsertOrder->bind_param("iiidd", $address_ID, $user_ID, $orderstatus_ID, $shipping_status_ID, $net_price);
    $stmtInsertOrder->execute();
    $order_ID = $stmtInsertOrder->insert_id;
    $stmtInsertOrder->close();

    foreach($cartItems as $item) {
        $productID = $item['productId']; 
        $quantity = $item['quantity'];
        $product_price = $item['price'];
        $total_price = $item['totalPrice'];

        $insertDetailSql = "INSERT INTO orders_details (order_ID, product_ID, quantity, product_price, total_price) VALUES (?, ?, ?, ?, ?)";
        $stmtInsertDetail = $conn->prepare($insertDetailSql);
        $stmtInsertDetail->bind_param("iiidd", $order_ID, $productID, $quantity, $product_price, $total_price);
        $stmtInsertDetail->execute();
        $stmtInsertDetail->close();

        $selectStockSql = "SELECT product_stock FROM products_phone WHERE product_ID = ?";
        $stmtSelectStock = $conn->prepare($selectStockSql);
        $stmtSelectStock->bind_param("i", $productID);
        $stmtSelectStock->execute();
        $stmtSelectStock->store_result();
        
        if($stmtSelectStock->num_rows > 0) {
            $stmtSelectStock->bind_result($currentStock);
            $stmtSelectStock->fetch();
            
            $newStock = $currentStock - $quantity;
            
            $updateStockSql = "UPDATE products_phone SET product_stock = ? WHERE product_ID = ?";
            $stmtUpdateStock = $conn->prepare($updateStockSql);
            $stmtUpdateStock->bind_param("ii", $newStock, $productID);
            $stmtUpdateStock->execute();
            $stmtUpdateStock->close();
        }
        
        $stmtSelectStock->close();
    }

    unset($_SESSION['cart']);

    header("Location: index.php");
    exit();
} else {
}
?>
