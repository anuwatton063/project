<?php
include 'navbar-user.php';
include 'condb.php';
include 'checkuser.php';

$user_type_ID = getUserTypeID();
if ($user_type_ID != 1) {
    header("Location: index.php"); // Redirect to index.php if not admin
    exit(); // Ensure script execution stops after redirection
}

// Fetch product types
$productTypesQuery = "SELECT product_type_ID, type_name FROM products_types";
$productTypesResult = mysqli_query($conn, $productTypesQuery);

// Fetch product brands
$productBrandsQuery = "SELECT product_brand_ID, brand_name FROM product_brand";
$productBrandsResult = mysqli_query($conn, $productBrandsQuery);

// If the form is submitted, add the new product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch product details from the form
    $product_type_ID = mysqli_real_escape_string($conn, $_POST['product_type_ID']);
    $product_brand_ID = mysqli_real_escape_string($conn, $_POST['product_brand_ID']);
    $product_color = mysqli_real_escape_string($conn, $_POST['product_color']);
    $phone_capacity = mysqli_real_escape_string($conn, $_POST['phone_capacity']);
    $product_stock = mysqli_real_escape_string($conn, $_POST['product_stock']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_detail = mysqli_real_escape_string($conn, $_POST['product_detail']);
    $product_cover_image = $_FILES['product_cover_image']['name']; // Fetching image file name
    $product_price = mysqli_real_escape_string($conn, $_POST['product_price']);
    $product_Image1 = ($_FILES['product_Image1']['name'] != '') ? $_FILES['product_Image1']['name'] : ''; // Fetching image file name
    $product_Image2 = ($_FILES['product_Image2']['name'] != '') ? $_FILES['product_Image2']['name'] : ''; // Fetching image file name

    // Directory to save images
    $target_dir = "../project/png/";

    // Move uploaded images to the upload directory
    move_uploaded_file($_FILES['product_cover_image']['tmp_name'], $target_dir . $product_cover_image);
    if ($product_Image1 != '') {
        move_uploaded_file($_FILES['product_Image1']['tmp_name'], $target_dir . $product_Image1);
    }
    if ($product_Image2 != '') {
        move_uploaded_file($_FILES['product_Image2']['tmp_name'], $target_dir . $product_Image2);
    }

    // Insert the new product into the database
    $sql = "INSERT INTO `products_phone` 
            (`product_type_ID`, `product_brand_ID`, `product_color`, `Phone_capacity`, `product_stock`, 
             `product_name`, `product_detail`, `product_cover_image`, `product_price`, `product_Image1`, `product_Image2`) 
            VALUES 
            ('$product_type_ID', '$product_brand_ID', '$product_color', '$phone_capacity', '$product_stock', 
             '$product_name', '$product_detail', '$product_cover_image', '$product_price', '$product_Image1', '$product_Image2')";

    if (mysqli_query($conn, $sql)) {
        // Product added successfully, redirect to product.php or any other desired page
        header("Location: product.php"); // Redirect to product listing page
        exit();
    } else {
        echo "Error adding product: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <style>
        #product_detail {
            width: 100%;
            height: 150px; /* ปรับขนาดตามที่ต้องการ */
            resize: vertical; /* ทำให้สามารถขยายความสูงได้เฉพาะ */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Product</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data" id="addForm">
            <!-- Product Type ID -->
            <label for="product_type_ID">Product Type:</label><br>
            <select id="product_type_ID" name="product_type_ID" required>
                <?php
                while ($type = mysqli_fetch_assoc($productTypesResult)) {
                    echo "<option value='{$type['product_type_ID']}'>{$type['type_name']}</option>";
                }
                ?>
            </select><br>

            <!-- Product Brand ID -->
            <label for="product_brand_ID">Product Brand:</label><br>
            <select id="product_brand_ID" name="product_brand_ID" required>
                <?php
                while ($brand = mysqli_fetch_assoc($productBrandsResult)) {
                    echo "<option value='{$brand['product_brand_ID']}'>{$brand['brand_name']}</option>";
                }
                ?>
            </select><br>

            <!-- Product Color -->
            <label for="product_color">Product Color:</label><br>
            <input type="text" id="product_color" name="product_color" required><br>

            <!-- Phone Capacity -->
            <label for="phone_capacity">Phone Capacity:</label><br>
            <input type="text" id="phone_capacity" name="phone_capacity" required><br>

            <!-- Product Stock -->
            <label for="product_stock">Product Stock:</label><br>
            <input type="number" id="product_stock" name="product_stock" required><br>

            <!-- Product Name -->
            <label for="product_name">Product Name:</label><br>
            <input type="text" id="product_name" name="product_name" required><br>

            <!-- Product Detail -->
            <label for="product_detail">Product Detail:</label><br>
            <textarea id="product_detail" name="product_detail" rows="4" required></textarea><br>

            <!-- Product Price -->
            <label for="product_price">Product Price:</label><br>
            <input type="number" id="product_price" name="product_price" required><br>

            <!-- Product Cover Image -->
            <label for="product_cover_image">Product Cover Image:</label><br>
            <input type="file" id="product_cover_image" name="product_cover_image" required onchange="previewImage('product_cover_image', 'coverImagePreview')"><br>
            <img id="coverImagePreview" src="#" alt="Cover Image Preview" style="max-width: 200px; max-height: 200px; display: none;"><br>

            <!-- Product Image 1 -->
            <label for="product_Image1">Product Image 1:</label><br>
            <input type="file" id="product_Image1" name="product_Image1" onchange="previewImage('product_Image1', 'image1Preview')"><br>
            <img id="image1Preview" src="#" alt="Image 1 Preview" style="max-width: 200px; max-height: 200px; display: none;"><br>

            <!-- Product Image 2 -->
            <label for="product_Image2">Product Image 2:</label><br>
            <input type="file" id="product_Image2" name="product_Image2" onchange="previewImage('product_Image2', 'image2Preview')"><br>
            <img id="image2Preview" src="#" alt="Image 2 Preview" style="max-width: 200px; max-height: 200px; display: none;"><br>

            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>

    <script>
        function previewImage(inputId, imgId) {
            const input = document.getElementById(inputId);
            const img = document.getElementById(imgId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    img.src = e.target.result;
                    img.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                img.src = '#';
                img.style.display = 'none';
            }
        }
    </script>
</body>
</html>
