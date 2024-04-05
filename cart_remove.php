<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['item_index'])) {
    $itemIndex = $_POST['item_index'];

    if (isset($_SESSION['cart'][$itemIndex])) {
        unset($_SESSION['cart'][$itemIndex]);

        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

header("Location: cart.php");
exit();
?>