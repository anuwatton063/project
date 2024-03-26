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

    // Fetch order details from the database, including product name and cover image
    $sql = "SELECT orders_details.*, products_phone.product_name, products_phone.product_cover_image 
            FROM orders_details 
            INNER JOIN products_phone ON orders_details.product_ID = products_phone.product_ID 
            WHERE orders_details.order_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $result = $stmt->get_result();
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
        <div class="table-responsive mt-4">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Check if there are any order details
                    if ($result->num_rows > 0) {
                        // Loop through each order detail
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><img src='../project/png/" . $row['product_cover_image'] . "' alt='" . $row['product_name']. "' width='100' height='100'> "."</td>";
                            echo "<td>" . $row['product_name'] . "</td>";
                            echo "<td>" . $row['quantity'] . "</td>";
                            echo "<td>$" . $row['product_price'] . "</td>";
                            echo "<td>$" . $row['total_price'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No order details found.</td></tr>";
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