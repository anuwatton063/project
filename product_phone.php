<?php
include('condb.php');
include 'navbar-user.php';
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
        /* Custom styles can be added here */
        .card {
            max-width: 400px; /* Set maximum width for the card */
        }
        .card-img-top {
            width: 100%; /* Set width to 100% to make it fill the container */
            height: 200px; /* Automatically adjust height to maintain aspect ratio */
        }
        .product-block {
            margin-bottom: 40px; /* Adjust the margin between product type blocks */
        }
    </style>
</head>
<body>
<div class="container px-4 px-lg-5 mt-5">
    <div class="row gx-4 gx-lg-5">
        <!-- Brand List -->
        <div class="col-lg-3 col-md-4">
            <div class="list-group">
                <?php
                // Fetch all product brands from the database
                $sqlBrands = "SELECT * FROM product_brand";
                $resultBrands = mysqli_query($conn, $sqlBrands);

                if ($resultBrands && mysqli_num_rows($resultBrands) > 0) {
                    while ($rowBrand = mysqli_fetch_assoc($resultBrands)) {
                        // Check if the brand has phones
                        $sqlPhones = "SELECT * FROM products_phone WHERE product_brand_ID = " . $rowBrand['product_brand_ID'];
                        $resultPhones = mysqli_query($conn, $sqlPhones);
                        $hasPhones = mysqli_num_rows($resultPhones) > 0;

                        // If the brand has phones, display the link
                        if ($hasPhones) {
                            echo '<a href="?brand_id=' . $rowBrand['product_brand_ID'] . '" class="list-group-item list-group-item-action">' . $rowBrand['brand_name'] . '</a>';
                        }
                    }
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
                // Fetch all products from the database with the selected brand and type = 'โทรศัพท์'
                $sql = "SELECT products_phone.*, products_types.type_name 
                        FROM products_phone 
                        INNER JOIN products_types 
                        ON products_phone.product_type_ID = products_types.product_type_ID 
                        WHERE products_phone.product_brand_ID = $selectedBrandID
                        AND products_types.type_name = 'โทรศัพท์'
                        ORDER BY products_phone.product_type_ID"; // Assuming 'products_phone' and 'products_types' are the names of your tables
            } else {
                // Fetch all products with type = 'โทรศัพท์' regardless of brand
                $sql = "SELECT products_phone.*, products_types.type_name 
                        FROM products_phone 
                        INNER JOIN products_types 
                        ON products_phone.product_type_ID = products_types.product_type_ID 
                        WHERE products_types.type_name = 'โทรศัพท์'
                        ORDER BY products_phone.product_type_ID";
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
                                        <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="#">Add</a></div>
                                    </div>
                                </div>
                            </div>';
                    }
                }
                // Close the last section
                echo '</div></div></section>';
            } else {
                // No products
                echo "No products found.";
            }
            ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="js/scripts.js"></script>
</body>
</html>