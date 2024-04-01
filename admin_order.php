<?php
ob_start(); // Start output buffering

include 'navbar-user.php';
include('condb.php');
include 'checkuser.php';

$user_type_ID = getUserTypeID();
if ($user_type_ID == 1) {
    include 'navbar-admin.php';
}
if ($user_type_ID != 1) {
    header("Location: index.php"); // Redirect to index.php
    exit(); // Ensure script execution stops after redirection
}

// Function to set session message
function setSessionMessage($message) {
    $_SESSION['message'] = $message;
}

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>All Orders</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        /* Add any custom styles here */
        /* Increase the width of the table columns */
        .table th,
        .table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>

    <!-- Add JavaScript for sorting -->
    <script>
        // Function to toggle sorting order and submit form
        function toggleSort() {
            var currentSort = document.getElementById('sortOrder').value;
            var newSort = currentSort === 'ASC' ? 'DESC' : 'ASC';
            document.getElementById('sortOrder').value = newSort;
            document.getElementById('sortForm').submit();
        }
    </script>
</head>

<body>
    <!-- Add sorting form -->
    <form id="sortForm" action="" method="GET" style="display: none;">
        <input type="hidden" name="search" value="<?= isset($_GET['search']) ? htmlentities($_GET['search']) : '' ?>">
        <input type="hidden" id="sortOrder" name="sortOrder" value="<?= isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC' ?>">
    </form>

    <div class="container">
        <h1 class="mt-5">All Orders</h1>

        <div class="row mb-3">
            <!-- Search form -->
            <div class="col-md-6">
                <form action="" method="GET" class="form-inline">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control mr-2" placeholder="Search" value="<?= isset($_GET['search']) ? htmlentities($_GET['search']) : '' ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary mr-3">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-striped table-bordered">
                <col width="7%">
                <col width="7%">
                <col width="7%">
                <col width="7%">
                <col width="7%">
                <col width="7%">
                <col width="7%">
                <col width="10%">
                <col width="7%">
                <col width="7%">
                <col width="7%">
                <col width="7%">
                <col width="10%">
                <col width="7%">
                <col width="8%">
                <col width="15%"> <!-- Adjusted width for date_time column -->
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User ID</th>
                        <th>Order Status</th>
                        <th>Shipping Status</th>
                        <th>Total Price</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Address Information</th>
                        <th>Tumbon</th>
                        <th>Amphoe</th>
                        <th>Province</th>
                        <th>Zipcode</th>
                        <th>Date and Time</th> <!-- New column header -->
                        <th>View Details</th>
                        <th>Delete</th>
                        <th>Change Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // PHP code for sorting
                   // PHP code for sorting
                        $sortColumn = isset($_GET['sortColumn']) && in_array($_GET['sortColumn'], ['order_ID', 'user_ID', 'order_status', 'status_name', 'net_price', 'name', 'phone', 'Address_information', 'tumbon', 'amphoe', 'province', 'zipcode', 'date_time']) ? $_GET['sortColumn'] : 'date_time';
                        $sortOrder = isset($_GET['sortOrder']) && in_array($_GET['sortOrder'], ['ASC', 'DESC']) ? $_GET['sortOrder'] : 'ASC';
                        $orderBy = "$sortColumn $sortOrder";

                        // Fetch all orders from the database with joined tables
                        $sql = "SELECT orders.order_ID, orders.user_ID, orders_status.order_status, shipping_status.status_name, orders.net_price, address.name, address.phone, address.Address_information, address.tumbon, address.amphoe, address.province ,address.zipcode, orders.date_time
                                FROM orders 
                                INNER JOIN orders_status ON orders.orderstatus_ID = orders_status.orderstatus_ID 
                                INNER JOIN shipping_status ON orders.shipping_status_ID = shipping_status.status_ID
                                INNER JOIN address ON orders.address_ID = address.address_ID
                                WHERE orders_status.order_status != 'แก้ไข'";

                        // Add search condition if search term is provided
                        if (!empty($_GET['search'])) {
                            $searchTerm = mysqli_real_escape_string($conn, $_GET['search']);
                            // Check if there's an existing WHERE clause
                            $sql .= (strpos($sql, 'WHERE') === false ? ' WHERE ' : ' AND ') . "(
                                orders.order_ID LIKE '%$searchTerm%' OR 
                                orders.user_ID LIKE '%$searchTerm%' OR 
                                orders_status.order_status LIKE '%$searchTerm%' OR 
                                shipping_status.status_name LIKE '%$searchTerm%' OR 
                                orders.net_price LIKE '%$searchTerm%' OR 
                                address.name LIKE '%$searchTerm%' OR 
                                address.phone LIKE '%$searchTerm%' OR 
                                address.Address_information LIKE '%$searchTerm%' OR 
                                address.tumbon LIKE '%$searchTerm%' OR 
                                address.amphoe LIKE '%$searchTerm%' OR 
                                address.province LIKE '%$searchTerm%' OR 
                                address.zipcode LIKE '%$searchTerm%' OR 
                                DATE_FORMAT(orders.date_time, '%d/%m/%Y ') LIKE '%$searchTerm%'  -- Include formatted date and time in search
                            )";
                        }

                        $sql .= " ORDER BY order_ID DESC LIMIT $offset, $perPage"; // Ordering by order_ID in descending order

                    $result = $conn->query($sql);

                    // Check if there are any orders
                    if ($result->num_rows > 0) {
                        // Loop through each order
                        while ($row = $result->fetch_assoc()) {
                            echo  "<tr>";
                            echo "<td>". $row['order_ID'] . "</td>";
                            echo "<td>" . $row['user_ID'] . "</td>";
                            echo "<td>" . $row['order_status'] . "</td>";
                            echo "<td>" . $row['status_name'] . "</td>";
                            echo "<td>฿ " . $row['net_price'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['phone'] . "</td>";
                            echo "<td>" . $row['Address_information'] . "</td>";
                            echo "<td>" . $row['tumbon'] . "</td>";
                            echo "<td>" . $row['amphoe'] . "</td>";
                            echo "<td>" . $row['province'] . "</td>";
                            echo "<td>" . $row['zipcode'] . "</td>";
                            echo "<td>" . date('d/m/Y H:i:s', strtotime($row['date_time'])) . "</td>"; // Displaying date and time in dd/mm/yyyy HH:MM:SS format
                            echo "<td><a href='user_orderDetail.php?orderID=" . $row['order_ID'] . "' class='btn btn-primary'>View Details</a></td>";
                            echo "<td>
                                    <form method='post'>
                                        <input type='hidden' name='orderID' value='" . $row['order_ID'] . "' />
                                        <button type='submit' name='deleteOrder' class='btn btn-danger' disabled onclick='return confirm(\"Are you sure you want to delete this order?\")'>Delete</button>
                                    </form>
                                </td>"; // Delete button
                            echo "<td><a href='admin_orderEdit.php?orderID=" . $row['order_ID'] . "' class='btn btn-primary'>Change Status</a></td>"; // Change status button
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='13'>No orders found.</td></tr>";
                    }

                    // Handle order deletion
                    if (isset($_POST['deleteOrder'])) {
                        $orderID = $_POST['orderID'];

                        // Fetch products from the deleted order
                        $fetch_products_sql = "SELECT product_ID, quantity FROM orders_details WHERE order_ID = $orderID";
                        $products_result = $conn->query($fetch_products_sql);

                        if ($products_result->num_rows > 0) {
                            while ($product_row = $products_result->fetch_assoc()) {
                                $productID = $product_row['product_ID'];
                                $quantity = $product_row['quantity'];

                                // Restore the quantity of the product in products_phone
                                $update_stock_sql = "UPDATE products_phone SET product_stock = product_stock + $quantity WHERE product_ID = $productID";
                                if ($conn->query($update_stock_sql) !== TRUE) {
                                    echo "<script>alert('Error updating product stock: " . $conn->error . "');</script>";
                                }
                            }
                        }

                        // Delete associated details first
                        $delete_details_sql = "DELETE FROM orders_details WHERE order_ID = $orderID";
                        if ($conn->query($delete_details_sql) === TRUE) {
                            // Proceed with deleting the order
                            $delete_sql = "DELETE FROM orders WHERE order_ID = $orderID";
                            if ($conn->query($delete_sql) === TRUE) {
                                setSessionMessage("Order $orderID has been deleted and products have been restocked."); // Set success message
                                // Redirect to avoid resubmission
                                header("Location: {$_SERVER['PHP_SELF']}?page=$page");
                                exit();
                            } else {
                                echo "<script>alert('Error deleting order: " . $conn->error . "');</script>";
                            }
                        } else {
                            echo "<script>alert('Error deleting associated details: " . $conn->error . "');</script>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination links -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php
                // Fetch total number of orders for pagination
                $totalOrdersQuery = "SELECT COUNT(*) AS total FROM orders 
                    INNER JOIN orders_status ON orders.orderstatus_ID = orders_status.orderstatus_ID 
                    INNER JOIN shipping_status ON orders.shipping_status_ID = shipping_status.status_ID
                    INNER JOIN address ON orders.address_ID = address.address_ID
                    WHERE orders_status.order_status != 'แก้ไข'";
                $totalOrdersResult = $conn->query($totalOrdersQuery);
                $totalOrdersRow = $totalOrdersResult->fetch_assoc();
                $totalOrders = $totalOrdersRow['total'];

                $totalPages = ceil($totalOrders / $perPage);
                for ($i = 1; $i <= $totalPages; $i++) {
                    echo "<li class='page-item " . ($page == $i ? 'active' : '') . "'><a class='page-link' href='?page=$i'>" . $i . "</a></li>";
                }
                ?>
            </ul>
        </nav>
        <!-- Display session message -->
        <?php
        // Check if there is a session message
        if (isset($_SESSION['message'])) {
            echo "<div class='alert alert-success' role='alert'>" . $_SESSION['message'] . "</div>";
            unset($_SESSION['message']); // Clear the session message
        }
        ?>
    </div>
</body>

</html>