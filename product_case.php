<?php
include('condb.php');
include 'navbar-user.php';

// Number of products to display per page
$productsPerPage = 4;

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
    <title>Shop Homepage case</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@5.3.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        /* Custom styles can be added here */
        .card {
            max-width: 200px; /* Set maximum width for the card */
        }
        .card-img-top {
            width: 100%; /* Set width to 100% to make it fill the container */
            height: 200px; /* Automatically adjust height to maintain aspect ratio */
        }
        .product-block {
            margin-bottom: 40px; /* Adjust the margin between product type blocks */
        }
        /* Ensure the brand list is fully aligned to the left */
        .brand-list-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px; /* Move the pagination down by 20px */
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
        .centered-alert {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000; /* Ensure the message is on top of everything */
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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

                    // Display a message on the webpage
                    var alertMessage = '<div class="alert alert-success alert-dismissible fade show centered-alert" role="alert">';
                    alertMessage += '<strong>Success!</strong> ได้นำสินค้าเข้าตะกร้าแล้ว';
                    alertMessage += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                    alertMessage += '</div>';

                    // Append the message to the alert-message-container div
                    $('#alert-message-container').html(alertMessage);

                    // Hide the alert message after 3 seconds
                    setTimeout(function() {
                        $('#alert-message-container').html('');
                    }, 3000);
                },
                error: function(xhr, status, error) {
                    console.error('Error adding item to cart:', error);
                    // You can add further error handling here, like displaying an error message to the user
                }
            });
        }
    </script>
</head>
<body>
<div class="container px-4 px-lg-5 mt-5">
    <div class="row gx-4 gx-lg-5">
        <!-- Brand List (Aligned to the left) -->
        <div class="col-lg-3 col-md-4">
            <div class="list-group brand-list-container">
                <?php
                // Fetch all product brands from the database that have phones
                $sqlBrands = "SELECT DISTINCT pb.product_brand_ID, pb.brand_name
                              FROM product_brand pb
                              INNER JOIN products_phone pp ON pb.product_brand_ID = pp.product_brand_ID
                              INNER JOIN products_types pt ON pp.product_type_ID = pt.product_type_ID
                              WHERE pt.type_name = 'เคส'";
                $resultBrands = mysqli_query($conn, $sqlBrands);

                // Check if there are any brands available with phones
                if ($resultBrands && mysqli_num_rows($resultBrands) > 0) {
                    while ($rowBrand = mysqli_fetch_assoc($resultBrands)) {
                        echo '<a href="?brand_id=' . $rowBrand['product_brand_ID'] . '" class="list-group-item list-group-item-action">' . $rowBrand['brand_name'] . '</a>';
                    }
                } else {
                    // If there are no brands available with phones, display a disabled button
                    echo '<button class="list-group-item list-group-item-actiondisabled" disabled>No brands available</button>';
                }
                ?>
            </div>
        </div>
        <!-- Product Content -->
        <div class="col-lg-9 col-md-8">
            <?php
            // Check if a specific brand is selected
            if (isset($_GET['brand_id'])) {
                $selectedBrandID = $_GET['brand_id'];
                // Fetch all products from the database with the selected brand and type = 'เคส'
                $sql = "SELECT pp.*, pt.type_name 
                        FROM products_phone pp
                        INNER JOIN products_types pt ON pp.product_type_ID = pt.product_type_ID 
                        WHERE pp.product_brand_ID = $selectedBrandID
                        AND pt.type_name = 'เคส'
                        ORDER BY pp.product_type_ID
                        LIMIT $offset, $productsPerPage";
            } else {
                // Fetch all products with type = 'เคส' regardless of brand
                $sql = "SELECT pp.*, pt.type_name 
                        FROM products_phone pp
                        INNER JOIN products_types pt ON pp.product_type_ID = pt.product_type_ID 
                        WHERE pt.type_name = 'เคส'
                        ORDER BY pp.product_type_ID
                        LIMIT $offset, $productsPerPage";
            }

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
                            // Concatenate the product type and brand name in the heading only if a brand is selected
                            $heading = $current_type;
                            if (isset($_GET['brand_id'])) {
                                if (!empty($row['brand_name'])) {
                                    $heading .= ' - ' . $row['brand_name'];
                                }
                            }
                            echo '<h2>' . $heading . '</h2><div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">';
                        }
                        // Output the product
                        echo '<div class="col mb-5">
                                <div class="card">
                                    <img class="card-img-top" src="' . $cover_image_path . '" alt="Product Image" />
                                    <div class="card-body p-4">
                                        <div class="text-center">
                                            <h5 class="fw-bolder">' . $row['product_name'] . '</h5>
                                            ฿ ' . number_format($row['product_price'],2) . '
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

                // Display pagination only if there are more than $productsPerPage items
                if (!isset($_GET['brand_id'])) {
                    // Pagination links
                    $sqlCount = "SELECT COUNT(*) as count FROM products_phone WHERE product_type_ID = (SELECT product_type_ID FROM products_types WHERE type_name = 'เคส')";
                } else {
                    // Pagination links for selected brand
                    $selectedBrandID = $_GET['brand_id'];
                    $sqlCount = "SELECT COUNT(*) as count FROM products_phone WHERE product_brand_ID = $selectedBrandID AND product_type_ID = (SELECT product_type_ID FROM products_types WHERE type_name = 'เคส')";
                }

                $resultCount = mysqli_query($conn, $sqlCount);
                $rowCount = mysqli_fetch_assoc($resultCount)['count'];

                if ($rowCount > $productsPerPage) {
                    // Pagination links
                    $totalPages = ceil($rowCount / $productsPerPage);

                    echo '<div class="pagination">';
                    for ($i = 1; $i <= $totalPages; $i++) {
                        // Include selected brand ID in pagination links if present
                        $pageLink = isset($_GET['brand_id']) ? '?brand_id=' . $_GET['brand_id'] . '&page=' . $i : '?page=' . $i;
                        echo '<a href="' . $pageLink . '"' . ($page == $i ? ' class="active"' : '') . '>' . $i . '</a>';
                    }
                    echo '</div>';
                }
            } else {
                // No products
                echo "No products found.";
            }
            ?>
        </div>
    </div>
</div>
<div id="alert-message-container"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
</body>
</html>