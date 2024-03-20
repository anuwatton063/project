<?php
// Start the session
session_start();

// Include database connection file
include('condb.php');

// Check if the necessary data is sent via POST
if(isset($_POST['productId']) && isset($_POST['productName']) && isset($_POST['quantity']) && isset($_POST['productPrice']) && isset($_POST['productImage'])) {
    // Assign posted data to variables
    $productId = $_POST['productId'];
    $productName = $_POST['productName'];
    $quantity = $_POST['quantity'];
    $productPrice = $_POST['productPrice'];
    $productImage = $_POST['productImage'];

    // Define base directory for images
    $base_dir = "../project/png/";
    // Construct path to product image
    $product_image_path = $base_dir . $productImage;

    // Construct the cart item including product image URL
    $cartItem = array(
        'productId' => $productId,
        'productName' => $productName,
        'quantity' => $quantity,
        'price' => $productPrice,
        'image' => $product_image_path // Add the product image URL
    );

    // Check if 'cart' session variable exists
    if(isset($_SESSION['cart'])) {
        // Check if the item already exists in the cart
        $itemExists = false;
        foreach($_SESSION['cart'] as $key => $item) {
            if($item['productId'] === $productId) {
                // Update the quantity
                $_SESSION['cart'][$key]['quantity'] += $quantity;
                $itemExists = true;
                break;
            }
        }
        // If the item doesn't exist, add it to the cart
        if(!$itemExists) {
            $_SESSION['cart'][] = $cartItem;
        }
    } else {
        // Create new cart and add item
        $_SESSION['cart'] = array($cartItem);
    }

    // Return success response
    echo json_encode(array('success' => true));
} else {
    // Return error response if data is incomplete
    echo json_encode(array('error' => 'Incomplete data sent'));
}
?>
