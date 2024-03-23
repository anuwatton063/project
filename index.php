<?php
include('condb.php');
include 'navbar-user.php';

// Number of products to display per page
$productsPerPage = 24;

// Current page number, default to 1 if not provided
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $productsPerPage;

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        /* Custom styles can be added here */
        .card {
            max-width: 400px; /* Set maximum width for the card */
        }
        .card-img-top {
            width: 100%; /* Set width to 100% to make it fill the container */
            height: 200px; /* Automatically adjust height to maintain aspect ratio */
        }
        .product-block {
            margin-bottom: -140px; /* Adjust the margin between product type blocks */
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 100px; /* Move the pagination down by 100px */
        }
        .pagination a {
            padding: 8px 16px;
            margin: 0 4px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        .pagination a.active {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }
    </style>
</head>
<body>
<?php

// Fetch products for the current page
$sql = "SELECT products_phone.*, products_types.type_name 
        FROM products_phone 
        INNER JOIN products_types 
        ON products_phone.product_type_ID = products_types.product_type_ID 
        ORDER BY products_phone.product_type_ID 
        LIMIT $offset, $productsPerPage";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $current_type = '';
    while ($row = mysqli_fetch_assoc($result)) {
        // Define base directory for image uploads
        $base_dir = "../project/png/";
        // Fetch image paths
        $cover_image_path = $base_dir . $row["product_cover_image"];
        // Check if the cover image path is not empty and if the file exists
        if (!empty($row["product_cover_image"]) && file_exists($cover_image_path)) {
            // Check if the type has changed
            if ($current_type !== $row['type_name']) {
                // If it's a new type, close previous block and start a new one
                if ($current_type !== '') {
                    echo '</div></div></section>';
                }
                $current_type = $row['type_name'];
                echo '<section class="py-5 product-block"><div class="container px-4 px-lg-5 mt-5">';
                echo '<h2>' . $current_type . '</h2><div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">';
            }
            // Output the product
            echo '<div class="col mb-5">
                    <div class="card">
                        <img class="card-img-top" src="' . $cover_image_path . '" alt="Product Image" />
                        <div class="card-body p-4">
                            <div class="text-center">
                                <h5 class="fw-bolder">' . $row['product_name'] . '</h5>
                                
                                ' . '$' . $row['product_price'] . '
                            </div>
                        </div>
                        <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                            <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="detail.php?id=' . $row['product_ID'] . '">View</a></div>
                            <div class="text-center"><button class="btn btn-outline-dark mt-auto" onclick="addToCartBackend(' . $row['product_ID'] . ', \'' . $row['product_name'] . '\', ' . $row['product_price'] . ', \'' . $row['product_cover_image'] . '\')">Add</button></div>
                        </div>
                    </div>
                </div>';
        }
    }
    // Close the last section
    echo '</div></div></section>';

    // Pagination links
    $totalPagesSql = "SELECT COUNT(*) as count FROM products_phone";
    $totalPagesResult = mysqli_query($conn, $totalPagesSql);
    $totalRows = mysqli_fetch_assoc($totalPagesResult)['count'];
    $totalPages = ceil($totalRows / $productsPerPage);

    echo '<div class="pagination">';
    for ($i = 1; $i <= $totalPages; $i++) {
        echo '<a href="?page=' . $i . '"' . ($page == $i ? ' class="active"' : '') . '>' . $i . '</a>';
    }
    echo '</div>';
} else {
    // No products found
    echo "No products found.";
}

?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="js/scripts.js"></script>

<script>
function addToCartBackend(productId, productName, productPrice, productImage) {
    var quantityInput = 1; // Default quantity, can be changed as needed

    jQuery.ajax({
        url: 'cartadd.php',
        method: 'POST',
        data: {
            productId: productId,
            productName: productName,
            quantity: quantityInput,
            productPrice: productPrice,
            productImage: productImage // Pass the product image URL
        },
        success: function(response) {
            console.log('Item added to cart successfully');
            console.log('Response:', response); // Log the response received from the server
            // You can add further actions here, like displaying a success message
        },
        error: function(xhr, status, error) {
            console.error('Error adding item to cart:', error);
            // You can add further error handling here, like displaying an error message to the user
        }
    });
}
</script>
</body>
</html>