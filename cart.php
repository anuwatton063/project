<?php
// Include navbar
include('condb.php');
include 'navbar-user.php';

// Check if the cart session exists
if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cartItems = $_SESSION['cart'];
} else {
    $cartItems = array();
}

// Calculate total price for each item and overall total price
$totalPrice = 0;
foreach($cartItems as &$item) {
    $item['totalPrice'] = $item['quantity'] * $item['price'];
    $totalPrice += $item['totalPrice'];
}
unset($item); // Unset reference variable
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card {
            width: 100%;
            margin-bottom: 20px;
            position: relative; /* Added */
        }
        .delete-button {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>

    <!-- Cart items -->
    <div class="container">
        <h2 class="mt-5 mb-4">Shopping Cart</h2>
        <?php if(empty($cartItems)): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 g-4">
                <?php foreach($cartItems as $key => $item): ?>
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $item['productName']; ?></h5>
                                <img src="<?php echo $item['image']; ?>" alt="Product Image" class="card-img-top" style="max-width: 200px; max-height: 200px;">
                                <p class="card-text">Quantity: <?php echo $item['quantity']; ?></p>
                                <p class="card-text">Price: <?php echo $item['price']; ?></p>
                                <p class="card-text">Total Price: <?php echo $item['totalPrice']; ?></p>
                                <form action="cart_remove.php" method="post">
                                    <input type="hidden" name="item_index" value="<?php echo $key; ?>">
                                    <button type="submit" class="btn btn-danger delete-button"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-4">
                <p><strong>Total:</strong> <?php echo $totalPrice; ?></p>
            </div>
            <div class="mt-4">
                <a href="checkout.php" class="btn btn-primary me-3">Proceed to Checkout</a>
                <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>