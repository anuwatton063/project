<?php
include 'navbar-user.php';
include('condb.php');
include 'checkuser.php';

$user_type_ID = getUserTypeID();
if ($user_type_ID == 1) {
    include 'navbar-admin.php';
}
if ($user_type_ID != 1){
    header("Location: index.php"); // Redirect to index.php
    exit(); // Ensure script execution stops after redirection
}


// Define the number of products to display per page
$products_per_page = 15;

// Determine the current page number
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the SQL query
$offset = ($current_page - 1) * $products_per_page;

// Query to fetch product information for the current page
$sql = "SELECT * FROM `products_phone` LIMIT $offset, $products_per_page";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Product Editor</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap CSS-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Product Editor</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Type ID</th>
                    <th>Product Brand ID</th>
                    <th>Product Color</th>
                    <th>Phone Capacity</th>
                    <th>Product Stock</th>
                    <th>Product Name</th>
                    <th>Product Detail</th>
                    <th>Product Cover Image</th>
                    <th>Product Price</th>
                    <th>Product Image 1</th>
                    <th>Product Image 2</th>
                    <th>Action</th> <!-- New column for editing actions -->
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row["product_ID"] . "</td>";
                        echo "<td>" . $row["product_type_ID"] . "</td>";
                        echo "<td>" . $row["product_brand_ID"] . "</td>";
                        echo "<td>" . $row["product_color"] . "</td>";
                        echo "<td>" . $row["Phone_capacity"] . "</td>";
                        echo "<td>" . $row["product_stock"] . "</td>";
                        echo "<td>" . $row["product_name"] . "</td>";
                        echo "<td>" . $row["product_detail"] . "</td>";
                        echo "<td>" . $row["product_cover_image"] . "</td>";
                        echo "<td>" . $row["product_price"] . "</td>";
                        echo "<td>" . $row["product_Image1"] . "</td>";
                        echo "<td>" . $row["product_Image2"] . "</td>";
                        echo "<td><a href='product-edit.php?id=" . $row["product_ID"] . "'>Edit</a></td>"; // Edit link
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='13'>No products found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>