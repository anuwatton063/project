<?php
// Include database connection file
include('condb.php');
session_start();

// Check if the form has been submitted
if(isset($_SESSION['cart']) && !empty($_SESSION['cart']) && isset($_SESSION['user_ID'])) {
    $cartItems = $_SESSION['cart'];
} else {
    // Redirect to error page or handle error accordingly
    exit("Error: Cart is empty or user is not logged in.");
}

// Calculate the total price
$totalPrice = 0;
foreach($cartItems as &$item) {
    $item['totalPrice'] = $item['quantity'] * $item['price'];
    $totalPrice += $item['totalPrice'];
}
unset($item);

// Retrieve the user's address
$sql = "SELECT * FROM address WHERE user_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_ID']);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Insert data into the orders table
if(isset($_POST['address'])) {
    $address_ID = $_POST['address'];
    $user_ID = $_SESSION['user_ID'];
    $orderstatus_ID = 5; // Assume 1 represents the order status as pending
    $shipping_status_ID = 1; // Assume 1 represents the shipping status as pending
    $net_price = $totalPrice;

    $insertOrderSql = "INSERT INTO orders (address_ID, user_ID, orderstatus_ID, shipping_status_ID, net_price) VALUES (?, ?, ?, ?, ?)";
    $stmtInsertOrder = $conn->prepare($insertOrderSql);
    $stmtInsertOrder->bind_param("iiidd", $address_ID, $user_ID, $orderstatus_ID, $shipping_status_ID, $net_price);
    $stmtInsertOrder->execute();
    $order_ID = $stmtInsertOrder->insert_id;
    $stmtInsertOrder->close();

    // Insert data into the orders_details table
    foreach($cartItems as $item) {
        $productID = $item['productId']; // Assuming 'product_ID' is already stored in the cart
        $quantity = $item['quantity'];
        $product_price = $item['price'];
        $total_price = $item['totalPrice'];

        $insertDetailSql = "INSERT INTO orders_details (order_ID, product_ID, quantity, product_price, total_price) VALUES (?, ?, ?, ?, ?)";
        $stmtInsertDetail = $conn->prepare($insertDetailSql);
        $stmtInsertDetail->bind_param("iiidd", $order_ID, $productID, $quantity, $product_price, $total_price);
        $stmtInsertDetail->execute();
        $stmtInsertDetail->close();

        // Reduce product stock
        // Retrieve current stock of the product
        $selectStockSql = "SELECT product_stock FROM products_phone WHERE product_ID = ?";
        $stmtSelectStock = $conn->prepare($selectStockSql);
        $stmtSelectStock->bind_param("i", $productID);
        $stmtSelectStock->execute();
        $stmtSelectStock->store_result();
        
        if($stmtSelectStock->num_rows > 0) {
            $stmtSelectStock->bind_result($currentStock);
            $stmtSelectStock->fetch();
            
            // Calculate new stock
            $newStock = $currentStock - $quantity;
            
            // Update product stock in the database
            $updateStockSql = "UPDATE products_phone SET product_stock = ? WHERE product_ID = ?";
            $stmtUpdateStock = $conn->prepare($updateStockSql);
            $stmtUpdateStock->bind_param("ii", $newStock, $productID);
            $stmtUpdateStock->execute();
            $stmtUpdateStock->close();
        }
        
        $stmtSelectStock->close();
    }

    // Clear the session after placing the order
    unset($_SESSION['cart']);

    // Redirect to the thank you page or any desired page after placing the order
    header("Location: index.php");
    exit();
} else {
    // Handle error if address is not provided
    // Redirect to error page or display error message
}
?>
