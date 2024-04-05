<?php
include('condb.php');
include 'navbar-user.php';

if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cartItems = $_SESSION['cart'];
} else {
    $cartItems = array();
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

if(isset($_POST['quantity']) && isset($_POST['item_index'])) {
    $quantity = $_POST['quantity'];
    $item_index = $_POST['item_index'];

    $_SESSION['cart'][$item_index]['quantity'] = $quantity;

    header("Location: cart.php");
    exit();
}

if(isset($_POST['address'])) {
    $address_ID = $_POST['address'];
    $user_ID = $_SESSION['user_ID'];
    $orderstatus_ID = 1; 
    $shipping_status = 1; 
    $net_price = $totalPrice;

    $sql = "INSERT INTO orders (user_ID, address_ID, orderstatus_ID, shipping_status, net_price) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiidd", $user_ID, $address_ID, $orderstatus_ID, $shipping_status, $net_price);
    $stmt->execute();
    $stmt->close();

    foreach($cartItems as $item) {
        $productId = $item['productId'];
        $quantity = $item['quantity'];
        $sql = "UPDATE products_phone SET product_stock = product_stock - ? WHERE product_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $quantity, $productId);
        $stmt->execute();
        $stmt->close();
    }

    unset($_SESSION['cart']);

    header("Location: checkout_success.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card {
            width: 100%;
            margin-bottom: 20px;
            position: relative;
        }
        .delete-button {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>

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
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td style="width: 25%;">
                                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Product Image" class="img-fluid rounded" style="max-width: 100%; height: 200px; object-fit: contain;">
                                        </td>
                                        <td style="width: 25%;">
                                            <h5 class="card-title"><?php echo htmlspecialchars($item['productName']); ?></h5>
                                            <p class="card-text">
                                                Quantity: 
                                                <form id="updateForm<?php echo $key; ?>" action="cart.php" method="post">
                                                    <input type="hidden" name="item_index" value="<?php echo $key; ?>">
                                                    <?php
                                                        $productId = $item['productId'];
                                                        $sql = "SELECT product_stock FROM products_phone WHERE product_ID = ?";
                                                        $stmt = $conn->prepare($sql);
                                                        $stmt->bind_param("i", $productId);
                                                        $stmt->execute();
                                                        $stmt->bind_result($productStock);
                                                        $stmt->fetch();
                                                        $stmt->close();

                                                        $maxQuantity = $productStock > 0 ? $productStock : 1;
                                                    ?>
                                                    <input type="number" id="inputQuantity_<?php echo $key; ?>" class="input-quantity" name="quantity" value="<?php echo min($item['quantity'], $maxQuantity); ?>" min="1" max="<?php echo $maxQuantity; ?>" step="1">
                                                    <br><span class="text-muted">(Stock: <?php echo number_format($productStock); ?>)</span>
                                                </form>
                                            </p>
                                            <p class="card-text">Total price : ฿ <?php echo number_format($item['totalPrice'], 2); ?></p>
                                        </td>
                                        <td style="width: 25%; vertical-align: middle;">
                                            <form action="cart_remove.php" method="post">
                                                <input type="hidden" name="item_index" value="<?php echo $key; ?>">
                                                <button type="submit" class="btn btn-danger delete-button"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                     </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
             <?php endforeach; ?>
        </div>
    <div class="mt-4">
        <p><strong>Net Total:</strong> ฿ <?php echo number_format($totalPrice, 2); ?></p>
    </div>
    
    <div class="mt-4">
        <form id="checkoutForm" action="cartCheckout.php" method="POST">
            <label for="address">Select Address:</label>
            <select name="address" id="address" class="form-select mb-3">
                <option value="" selected disabled>-- Select Address --</option>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <option value="<?php echo htmlspecialchars($row['address_ID']); ?>"><?php echo htmlspecialchars($row['name'] . ' - ' . $row['Address_information'] . ', ' . $row['tumbon'] . ', ' . $row['amphoe'] . ', ' . $row['province'] . ', ' . $row['Zipcode']); ?></option>
                <?php endwhile; ?>
            </select>
            <button type="button" class="btn btn-primary" id="checkoutBtn" disabled onclick="confirmCheckout()">Proceed to Checkout</button>
        </form>
    </div>

<?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    function confirmCheckout() {
        if (confirm("Are you sure you want to proceed with checkout?")) {
            document.getElementById("checkoutForm").submit(); 
        }
    }

    $(document).ready(function() {
        $('input[name="quantity"]').on('change', function() {
            var quantity = parseInt($(this).val());
            if (quantity >= 1) { 
                $(this).closest('form').submit(); 
            } else {
                $(this).val(1); 
            }
        });

        $('#address').on('change', function() {
            if ($(this).val() !== "") {
                $('#checkoutBtn').prop('disabled', false);
            } else {
                $('#checkoutBtn').prop('disabled', true);
            }
        });
    });
</script>
</body>
</html>

