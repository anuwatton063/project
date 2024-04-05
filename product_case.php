<?php
include('condb.php');
include 'navbar-user.php';

$productsPerPage = 4;

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

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
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@5.3.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        .card {
            max-width: 200px; 
        }
        .card-img-top {
            width: 100%; 
            height: 200px; 
        }
        .product-block {
            margin-bottom: 40px; 
        }
        .brand-list-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px; 
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
            z-index: 1000; 
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        function addToCartBackend(productId, productName, productPrice, productImage) {
            var quantityInput = 1; 
            jQuery.ajax({
                url: 'cartadd.php',
                method: 'POST',
                data: {
                    productId: productId,
                    productName: productName,
                    quantity: quantityInput,
                    productPrice: productPrice,
                    productImage: productImage 
                },
                success: function(response) {
                    console.log('Item added to cart successfully');
                    console.log('Response:', response); 

                    var alertMessage = '<div class="alert alert-success alert-dismissible fade show centered-alert" role="alert">';
                    alertMessage += '<strong>Success!</strong> ได้นำสินค้าเข้าตะกร้าแล้ว';
                    alertMessage += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                    alertMessage += '</div>';

                    $('#alert-message-container').html(alertMessage);

                    setTimeout(function() {
                        $('#alert-message-container').html('');
                    }, 3000);
                },
                error: function(xhr, status, error) {
                    console.error('Error adding item to cart:', error);
                }
            });
        }
    </script>
</head>
<body>
<div class="container px-4 px-lg-5 mt-5">
    <div class="row gx-4 gx-lg-5">
        <div class="col-lg-3 col-md-4">
            <div class="list-group brand-list-container">
                <?php
                $sqlBrands = "SELECT DISTINCT pb.product_brand_ID, pb.brand_name
                              FROM product_brand pb
                              INNER JOIN products_phone pp ON pb.product_brand_ID = pp.product_brand_ID
                              INNER JOIN products_types pt ON pp.product_type_ID = pt.product_type_ID
                              WHERE pt.type_name = 'เคส'";
                $resultBrands = mysqli_query($conn, $sqlBrands);

                if ($resultBrands && mysqli_num_rows($resultBrands) > 0) {
                    while ($rowBrand = mysqli_fetch_assoc($resultBrands)) {
                        echo '<a href="?brand_id=' . $rowBrand['product_brand_ID'] . '" class="list-group-item list-group-item-action">' . $rowBrand['brand_name'] . '</a>';
                    }
                } else {
                    echo '<button class="list-group-item list-group-item-actiondisabled" disabled>No brands available</button>';
                }
                ?>
            </div>
        </div>
        <div class="col-lg-9 col-md-8">
            <?php
            if (isset($_GET['brand_id'])) {
                $selectedBrandID = $_GET['brand_id'];
                $sql = "SELECT pp.*, pt.type_name 
                        FROM products_phone pp
                        INNER JOIN products_types pt ON pp.product_type_ID = pt.product_type_ID 
                        WHERE pp.product_brand_ID = $selectedBrandID
                        AND pt.type_name = 'เคส'
                        ORDER BY pp.product_type_ID
                        LIMIT $offset, $productsPerPage";
            } else {
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
                    $base_dir = "../project/png/";
                    $cover_image_path = $base_dir . $row["product_cover_image"];
                    if (!empty($row["product_cover_image"]) && file_exists($cover_image_path)) {
                        if ($current_type !== $row['type_name']) {
                            if ($current_type !== '') {
                                echo '</div></div></section>';
                            }
                            $current_type = $row['type_name'];
                            echo '<section class="py-5 product-block"><div class="container px-4 px-lg-5 mt-5">';
                            $heading = $current_type;
                            if (isset($_GET['brand_id'])) {
                                if (!empty($row['brand_name'])) {
                                    $heading .= ' - ' . $row['brand_name'];
                                }
                            }
                            echo '<h2>' . $heading . '</h2><div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">';
                        }
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
                echo '</div></div></section>';

                if (!isset($_GET['brand_id'])) {
                    $sqlCount = "SELECT COUNT(*) as count FROM products_phone WHERE product_type_ID = (SELECT product_type_ID FROM products_types WHERE type_name = 'เคส')";
                } else {
                    $selectedBrandID = $_GET['brand_id'];
                    $sqlCount = "SELECT COUNT(*) as count FROM products_phone WHERE product_brand_ID = $selectedBrandID AND product_type_ID = (SELECT product_type_ID FROM products_types WHERE type_name = 'เคส')";
                }

                $resultCount = mysqli_query($conn, $sqlCount);
                $rowCount = mysqli_fetch_assoc($resultCount)['count'];

                if ($rowCount > $productsPerPage) {
                    $totalPages = ceil($rowCount / $productsPerPage);

                    echo '<div class="pagination">';
                    for ($i = 1; $i <= $totalPages; $i++) {
                        $pageLink = isset($_GET['brand_id']) ? '?brand_id=' . $_GET['brand_id'] . '&page=' . $i : '?page=' . $i;
                        echo '<a href="' . $pageLink . '"' . ($page == $i ? ' class="active"' : '') . '>' . $i . '</a>';
                    }
                    echo '</div>';
                }
            } else {
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