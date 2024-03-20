<?php
include('condb.php');
include 'navbar-user.php';

// Fetch all products from the database sorted by type name
$sql = "SELECT p.*, t.type_name
        FROM products_phone p
        INNER JOIN products_types t ON p.product_type_ID = t.product_type_ID
        ORDER BY t.type_name";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
?>
<!-- Header -->
<header class="bg-dark py-5">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">Shop in style</h1>
            <p class="lead fw-normal text-white-50 mb-0">With this shop homepage template</p>
        </div>
    </div>
</header>
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
    </style>
</head>
<body>
<!-- Section -->
<section class="py-5">
    <div class="container px-4 px-lg-5 mt-5">
        <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
            <?php
            // Loop through each product
            while ($row = mysqli_fetch_assoc($result)) {
                // Define base directory for image uploads
                $base_dir = "../project/png/";

                // Fetch image paths
                $cover_image_path = $base_dir . $row["product_cover_image"];

                // Check if the cover image path is not empty and if the file exists
                if (!empty($row["product_cover_image"]) && file_exists($cover_image_path)) {
            ?>
            <div class="col mb-5">
                <div class="card">
                    <!-- Product image -->
                    <img class="card-img-top" src="<?php echo $cover_image_path; ?>" alt="Product Image" />
                    <!-- Product details -->
                    <div class="card-body p-4">
                        <div class="text-center">
                            <!-- Product name -->
                            <h5 class="fw-bolder"><?php echo $row['product_name']; ?></h5>
                            <!-- Product price -->
                            <?php echo '$' . $row['product_price']; ?>
                        </div>
                    </div>
                    <!-- Product actions -->
                    <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                        <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="detail.php">View</a></div>
                        <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="#">Add</a></div>
                    </div>
                </div>
            </div>
            <?php
                }
            }
            ?>
        </div>
    </div>
</section>
<?php
} else {
    // No products found
    echo "No products found.";
}
?>

<!-- Footer -->
<footer class="py-5 bg-dark">
    <div class="container"><p class="m-0 text-center text-white">Copyright &copy; Your Website 2023</p></div>
</footer>

<!-- Bootstrap core JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Core theme JS -->
<script src="js/scripts.js"></script>
</body>
</html>