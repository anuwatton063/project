<?php
include('condb.php');
include 'navbar-user.php';


$productsPerPage = 24;

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
    <title>Shop Homepage </title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="css/styles.css" rel="stylesheet" />
    <style>
      
        .card {
            max-width: 250px; 
        }
        .card-img-top {
            width: 100%; 
            height: 200px; 
        }
        .product-block {
            margin-bottom: -140px;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 100px; 
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
</head>
<body>
<?php

$sql = "SELECT products_phone.*, products_types.type_name 
        FROM products_phone 
        INNER JOIN products_types 
        ON products_phone.product_type_ID = products_types.product_type_ID 
        WHERE products_phone.product_stock > 0
        ORDER BY products_phone.product_type_ID 
        LIMIT $offset, $productsPerPage";

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
                echo '<section class="py-5 product-block"><div class="container px-4 px-lg-5 mt-4">';
                echo '<h2>' . $current_type . '</h2><div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">';
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
    $totalPagesSql = "SELECT COUNT(*) as count FROM products_phone WHERE product_stock > 0";
    $totalPagesResult = mysqli_query($conn, $totalPagesSql);
    $totalRows = mysqli_fetch_assoc($totalPagesResult)['count'];
    $totalPages = ceil($totalRows / $productsPerPage);

    echo '<div class="pagination">';
    for ($i = 1; $i <= $totalPages; $i++) {
        echo '<a href="?page=' . $i . '"' . ($page == $i ? ' class="active"' : '') . '>' . $i . '</a>';
    }
    echo '</div>';
} else {

    echo "No products found.";
}

?>

<div id="alert-message-container"></div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="js/scripts.js"></script>

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
</body>
</html>
