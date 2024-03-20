<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['item_index'])) {
    $itemIndex = $_POST['item_index'];

    // Check if the item index exists in the cart
    if (isset($_SESSION['cart'][$itemIndex])) {
        // Remove the item from the cart
        unset($_SESSION['cart'][$itemIndex]);

        // Reset array keys to ensure continuous indexing
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

// Redirect back to the shopping cart page
header("Location: cart.php");
exit();
?>