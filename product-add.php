<?php
include 'navbar-user.php';
include 'condb.php';
include 'checkuser.php';

$user_type_ID = getUserTypeID();
if ($user_type_ID != 1) {
    header("Location: index.php"); 
    exit(); 
}

$productTypesQuery = "SELECT product_type_ID, type_name FROM products_types";
$productTypesResult = mysqli_query($conn, $productTypesQuery);

$productBrandsQuery = "SELECT product_brand_ID, brand_name FROM product_brand";
$productBrandsResult = mysqli_query($conn, $productBrandsQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_type_ID = mysqli_real_escape_string($conn, $_POST['product_type_ID']);
    $product_brand_ID = mysqli_real_escape_string($conn, $_POST['product_brand_ID']);
    $product_color = mysqli_real_escape_string($conn, $_POST['product_color']);
    $phone_capacity = mysqli_real_escape_string($conn, $_POST['phone_capacity']);
    $product_stock = mysqli_real_escape_string($conn, $_POST['product_stock']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_detail = mysqli_real_escape_string($conn, $_POST['product_detail']);
    $product_cover_image = $_FILES['product_cover_image']['name']; 
    $product_price = mysqli_real_escape_string($conn, $_POST['product_price']);
    $product_Image1 = ($_FILES['product_Image1']['name'] != '') ? $_FILES['product_Image1']['name'] : ''; 
    $product_Image2 = ($_FILES['product_Image2']['name'] != '') ? $_FILES['product_Image2']['name'] : ''; 

    $target_dir = "../project/png/";

    move_uploaded_file($_FILES['product_cover_image']['tmp_name'], $target_dir . $product_cover_image);
    if ($product_Image1 != '') {
        move_uploaded_file($_FILES['product_Image1']['tmp_name'], $target_dir . $product_Image1);
    }
    if ($product_Image2 != '') {
        move_uploaded_file($_FILES['product_Image2']['tmp_name'], $target_dir . $product_Image2);
    }

    $sql = "INSERT INTO `products_phone` 
            (`product_type_ID`, `product_brand_ID`, `product_color`, `Phone_capacity`, `product_stock`, 
             `product_name`, `product_detail`, `product_cover_image`, `product_price`, `product_Image1`, `product_Image2`) 
            VALUES 
            ('$product_type_ID', '$product_brand_ID', '$product_color', '$phone_capacity', '$product_stock', 
             '$product_name', '$product_detail', '$product_cover_image', '$product_price', '$product_Image1', '$product_Image2')";

    if (mysqli_query($conn, $sql)) {
        header("Location: product.php"); 
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        #product_detail {
            width: 100%;
            height: 150px; 
            resize: vertical; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Product</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data" id="addForm">
            <label for="product_type_ID">ประเภทสินค้า:</label><br>
            <select id="product_type_ID" name="product_type_ID" required>
                <?php
                while ($type = mysqli_fetch_assoc($productTypesResult)) {
                    echo "<option value='{$type['product_type_ID']}'>{$type['type_name']}</option>";
                }
                ?>
            </select><br>

            <label for="product_brand_ID">ยี่ห้อสินค้า:</label><br>
            <select id="product_brand_ID" name="product_brand_ID" required>
                <?php
                while ($brand = mysqli_fetch_assoc($productBrandsResult)) {
                    echo "<option value='{$brand['product_brand_ID']}'>{$brand['brand_name']}</option>";
                }
                ?>
            </select><br>

            <label for="product_color">สี:</label><br>
            <input type="text" id="product_color" name="product_color" required><br>

            <label for="phone_capacity">ความจุ:</label><br>
            <input type="text" id="phone_capacity" name="phone_capacity" required><br>

            <label for="product_stock">จำนวนสินค้า:</label><br>
            <input type="number" id="product_stock" name="product_stock" required><br>

            <label for="product_name">ชื่อ:</label><br>
            <input type="text" id="product_name" name="product_name" required><br>

            <label for="product_detail">รายละเอียด:</label><br>
            <textarea id="product_detail" name="product_detail" rows="4" required></textarea><br>

            <label for="product_price">ราคา:</label><br>
            <input type="number" id="product_price" name="product_price" required><br>

            <label for="product_cover_image">ภาพสินค้า:</label><br>
            <input type="file" id="product_cover_image" name="product_cover_image" required onchange="previewImage('product_cover_image', 'coverImagePreview')"><br>
            <img id="coverImagePreview" src="#" alt="Cover Image Preview" style="max-width: 200px; max-height: 200px; display: none;"><br>

            <label for="product_Image1">ภาพสินค้า 1:</label><br>
            <input type="file" id="product_Image1" name="product_Image1" onchange="previewImage('product_Image1', 'image1Preview')"><br>
            <img id="image1Preview" src="#" alt="Image 1 Preview" style="max-width: 200px; max-height: 200px; display: none;"><br>

            <label for="product_Image2">ภาพสินค้า 2:</label><br>
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
