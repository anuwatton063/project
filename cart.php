<?php include('condb.php'); ?>
<?php include 'navbar-user.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Shop Homepage - Start Bootstrap Template</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@5.3.0/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    
<body>
    <div class="cart-container">
        <h2>Shopee Cart</h2>
        <div class="cart-items">
            <!-- Product items will be displayed here -->
        </div>
        <div class="cart-total">
            <h3>Total: <span id="cart-total">0.00</span></h3>
            <button id="checkout-button">Checkout</button>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>


