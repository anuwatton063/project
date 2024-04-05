<?php
session_start();

include('condb.php');

if(isset($_POST['productId']) && isset($_POST['productName']) && isset($_POST['quantity']) && isset($_POST['productPrice']) && isset($_POST['productImage'])) {
    $productId = $_POST['productId'];
    $productName = $_POST['productName'];
    $quantity = $_POST['quantity'];
    $productPrice = $_POST['productPrice'];
    $productImage = $_POST['productImage'];

    $base_dir = "../project/png/";
    $product_image_path = $base_dir . $productImage;

    $cartItem = array(
        'productId' => $productId,
        'productName' => $productName,
        'quantity' => $quantity,
        'price' => $productPrice,
        'image' => $product_image_path 
    );

    if(isset($_SESSION['cart'])) {
        $itemExists = false;
        foreach($_SESSION['cart'] as $key => $item) {
            if($item['productId'] === $productId) {
                $_SESSION['cart'][$key]['quantity'] += $quantity;
                $itemExists = true;
                break;
            }
        }
        if(!$itemExists) {
            $_SESSION['cart'][] = $cartItem;
        }
    } else {
        $_SESSION['cart'] = array($cartItem);
    }

    echo json_encode(array('success' => true));
} else {
    echo json_encode(array('error' => 'Incomplete data sent'));
}
?>
