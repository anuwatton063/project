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
        <title>Shop Homepage - Start Bootstrap Template</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@5.3.0/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
    <style>
        /* Custom button style */
        .edit-btn {
            display: inline-block;
            padding: 5px 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .edit-btn:hover {
            background-color: #0056b3;
        }

        .add-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
            margin-bottom: 20px;
        }

        .add-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Product Editor</h1>
        <a class="add-btn" href="product-add.php">Add Product</a> <!-- Add Product button -->
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
                        // Define base directory for image uploads
                        $base_dir = "../project/png/";

                        // Fetch image paths
                        $cover_image_path = $base_dir . $row["product_cover_image"];
                        $image1_path = $base_dir . $row["product_Image1"];
                        $image2_path = $base_dir . $row["product_Image2"];

                        // Check if the paths are not empty and the files exist
                        if (!empty($row["product_cover_image"]) && file_exists($cover_image_path)) {
                            $cover_image_html = "<img src='$cover_image_path' alt='Cover Image' width='100' />";
                        } else {
                            $cover_image_html = "No Image Available";
                        }

                        if (!empty($row["product_Image1"]) && file_exists($image1_path)) {
                            $image1_html = "<img src='$image1_path' alt='Image 1' width='100' />";
                        } else {
                            $image1_html = "No Image Available";
                        }

                        if (!empty($row["product_Image2"]) && file_exists($image2_path)) {
                            $image2_html = "<img src='$image2_path' alt='Image 2' width='100' />";
                        } else {
                            $image2_html = "No Image Available";
                        }

                        // Output row with image HTML
                        echo "<tr>";
                        echo "<td>" . $row["product_ID"] . "</td>";
                        echo "<td>" . $row["product_type_ID"] . "</td>";
                        echo "<td>" . $row["product_brand_ID"] . "</td>";
                        echo "<td>" . $row["product_color"] . "</td>";
                        echo "<td>" . $row["Phone_capacity"] . "</td>";
                        echo "<td>" . $row["product_stock"] . "</td>";
                        echo "<td>" . $row["product_name"] . "</td>";
                        echo "<td>" . $row["product_detail"] . "</td>";
                        echo "<td>$cover_image_html</td>";
                        echo "<td>" . $row["product_price"] . "</td>";
                        echo "<td>$image1_html</td>";
                        echo "<td>$image2_html</td>";
                        echo "<td><a class='edit-btn' href='product-edit.php?id=" . $row["product_ID"] . "'>Edit</a></td>"; // Edit link with custom class
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