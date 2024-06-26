<?php
include('condb.php');
include 'navbar-user.php';

if(isset($_GET['id'])) {
    $product_id = $_GET['id'];

    $sql = "SELECT * FROM products_phone WHERE product_ID = $product_id";
    $result = mysqli_query($conn, $sql);

    if($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $row['product_name']; ?></title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            padding-top: 0px; 
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px 0;
        }
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: black; 
        }
        .carousel-control-prev,
        .carousel-control-next {
            width: 5%;
        }
        .carousel-item img {
            max-height: 300px;
            max-width: 300px;
            margin: auto;
        }
        .product-info {
            margin-top: 20px; 
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
    <section class="py-5">
        <div class="container px-4 px-lg-5 my-5">
            <div class="row gx-4 gx-lg-5 align-items-start">
                <div class="col-md-6">
                    <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php if (!empty($row['product_cover_image'])): ?>
                                <div class="carousel-item active">
                                    <img src="../project/png/<?php echo $row['product_cover_image']; ?>" class="d-block w-100" alt="Product Cover Image">
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($row['product_Image1'])): ?>
                                <div class="carousel-item">
                                    <img src="../project/png/<?php echo $row['product_Image1']; ?>" class="d-block w-100" alt="Product Image 1">
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($row['product_Image2'])): ?>
                                <div class="carousel-item">
                                    <img src="../project/png/<?php echo $row['product_Image2']; ?>" class="d-block w-100" alt="Product Image 2">
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($row['product_cover_image']) || !empty($row['product_Image1']) || !empty($row['product_Image2'])): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon <?php if (empty($row['product_cover_image']) && empty($row['product_Image1']) && empty($row['product_Image2'])) echo 'd-none'; ?>" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon <?php if (empty($row['product_cover_image']) && empty($row['product_Image1']) && empty($row['product_Image2'])) echo 'd-none'; ?>" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <h1 class="display-5 fw-bolder"><?php echo $row['product_name']; ?></h1>
                    <div class="fs-5 mb-3">
                        <br><span>฿ <?php echo number_format($row['product_price'], 2); ?></span>
                        <?php if (!empty($row['product_color'])): ?>
                            <span class="ms-3">สี: <?php echo$row['product_color']; ?></span>
                            <span class="ms-3">ความจุ: <?php echo$row['Phone_capacity']; ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="lead">จำนวนสินค้าที่มี: <?php echo $row['product_stock']; ?></p>
                    <div class="d-flex">
                        <input class="form-control text-center me-3" id="inputQuantity_<?php echo $row['product_ID']; ?>" type="number" value="1" style="max-width: 3rem" />
                        <button class="btn btn-outline-dark flex-shrink-0" type="button" onclick="addToCartBackend(<?php echo $row['product_ID']; ?>, '<?php echo $row['product_name']; ?>', <?php echo $row['product_price']; ?>, '<?php echo $row['product_cover_image']; ?>')">
                            <i class="bi-cart-fill me-1"></i>
                            Add to cart
                        </button>
                    </div>
                    <br>
                    <p class="lead"><?php echo nl2br($row['product_detail']); ?></p>
                </div>
            </div>
        </div>
    </section>
    <div id="alert-message-container"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    function addToCartBackend(productId, productName, productPrice, productImage) {
        var quantityInput = document.getElementById('inputQuantity_' + productId);
        var quantity = quantityInput.value;

        $.ajax({
            url: 'cartadd.php',
            method: 'POST',
            data: {
                productId: productId,
                productName: productName,
                quantity: quantity,
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
<?php
    } else {
        echo "Product not found.";
    }
} else {
    echo "Product ID not provided.";
}
?>

