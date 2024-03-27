<?php
// Include database connection file
include('condb.php');

include 'navbar-user.php';

// Check if user is logged in
if (!isset($_SESSION['user_ID'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Check if orderID is provided
if(isset($_GET['orderID'])) {
    $orderID = $_GET['orderID'];

    // Fetch address details associated with the order
    $address_sql = "SELECT * FROM address WHERE user_ID = ? AND address_ID IN (SELECT address_ID FROM orders WHERE order_ID = ?)";
    $address_stmt = $conn->prepare($address_sql);
    $address_stmt->bind_param("ii", $_SESSION['user_ID'], $orderID);
    $address_stmt->execute();
    $address_result = $address_stmt->get_result();

    // Fetch order details from the database, including product name and cover image
    $order_sql = "SELECT orders_details.*, products_phone.product_name, products_phone.product_cover_image 
            FROM orders_details 
            INNER JOIN products_phone ON orders_details.product_ID = products_phone.product_ID 
            WHERE orders_details.order_ID = ?";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("i", $orderID);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Order Details</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        /* Add any custom styles here */
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mt-5">Order Details</h1>
        
        <!-- Display address information -->
        <div class="mt-4">
            <h2>Address Information</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Address ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Address Information</th>
                        <th>Tumbon</th>
                        <th>Amphoe</th>
                        <th>Province</th>
                        <th>Zipcode</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Check if there are any address details
                    if ($address_result->num_rows > 0) {
                        while ($row = $address_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['address_ID'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['phone'] . "</td>";
                            echo "<td>" . $row['Address_information'] . "</td>";
                            echo "<td>" . $row['tumbon'] . "</td>";
                            echo "<td>" . $row['amphoe'] . "</td>";
                            echo "<td>" . $row['province'] . "</td>";
                            echo "<td>" . $row['Zipcode'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No address details found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <!-- Display order details -->
        <div class="table-responsive mt-4">
            <h2>Order Items</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Check if there are any order details
                    if ($order_result->num_rows > 0) {
                        // Loop through each order detail
                        while ($row = $order_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><img src='../project/png/" . $row['product_cover_image'] . "' alt='" . $row['product_name']. "' width='100' height='100'> "."</td>";
                            echo "<td>" . $row['product_name'] . "</td>";
                            echo "<td>" . $row['quantity'] . "</td>";
                            echo "<td>$" . $row['product_price'] . "</td>";
                            echo "<td>$" . $row['total_price'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No order details found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>

<?php
} else {
    // No orderID provided
    echo "No order ID provided.";
}
?>
