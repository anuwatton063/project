<?php
include 'navbar-user.php';
include('condb.php');
include 'checkuser.php';

$user_type_ID = getUserTypeID();
if ($user_type_ID != 1) {
    header("Location: index.php"); // Redirect to index.php if not admin
    exit(); // Ensure script execution stops after redirection
}

// Check if the product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php"); // Redirect if product ID is not provided or empty
    exit();
}

// Fetch the product ID from the URL
$product_id = mysqli_real_escape_string($conn, $_GET['id']);

// If the form is submitted, update the product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch updated product details
    $product_type_ID = mysqli_real_escape_string($conn, $_POST['product_type_ID']);
    $product_brand_ID = mysqli_real_escape_string($conn, $_POST['product_brand_ID']);
    $product_color = mysqli_real_escape_string($conn, $_POST['product_color']);
    $phone_capacity = mysqli_real_escape_string($conn, $_POST['phone_capacity']);
    $product_stock = mysqli_real_escape_string($conn, $_POST['product_stock']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_detail = mysqli_real_escape_string($conn, $_POST['product_detail']);
    $product_cover_image = $_FILES['product_cover_image']['name']; // Fetching image file name
    $product_price = mysqli_real_escape_string($conn, $_POST['product_price']);
    $product_Image1 = $_FILES['product_Image1']['name']; // Fetching image file name
    $product_Image2 = $_FILES['product_Image2']['name']; // Fetching image file name

    // File upload directory
    $target_dir = "uploads/";

    // Move uploaded images to the upload directory
    move_uploaded_file($_FILES['product_cover_image']['tmp_name'], $target_dir . $product_cover_image);
    move_uploaded_file($_FILES['product_Image1']['tmp_name'], $target_dir . $product_Image1);
    move_uploaded_file($_FILES['product_Image2']['tmp_name'], $target_dir . $product_Image2);

    // Update the product in the database
    $sql = "UPDATE `products_phone` SET 
            `product_type_ID` = '$product_type_ID',
            `product_brand_ID` = '$product_brand_ID',
            `product_color` = '$product_color',
            `Phone_capacity` = '$phone_capacity',
            `product_stock` = '$product_stock',
            `product_name` = '$product_name',
            `product_detail` = '$product_detail',
            `product_cover_image` = '$product_cover_image',
            `product_price` = '$product_price',
            `product_Image1` = '$product_Image1',
            `product_Image2` = '$product_Image2' 
            WHERE `product_ID` = $product_id";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php"); // Redirect to product editor after successful update
        exit();
    } else {
        echo "Error updating product: " . mysqli_error($conn);
    }
}

// Fetch the product details to populate the form
$sql = "SELECT * FROM `products_phone` WHERE `product_ID` = $product_id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Product not found.";
    exit();
}

$row = mysqli_fetch_assoc($result); // Fetch product details

// Display the form to edit the product
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h1>Edit Product</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $product_id; ?>" method="POST" enctype="multipart/form-data">
            <label for="product_type_ID">Product Type ID:</label><br>

            <input type="text" id="product_type_ID" name="product_type_ID" value="<?php echo $row['product_type_ID']; ?>"><br>
            <label for="product_brand_ID">Product Brand ID:</label><br>

            <input type="text" id="product_brand_ID" name="product_brand_ID" value="<?php echo $row['product_brand_ID']; ?>"><br>
            <label for="product_color">Product Color:</label><br>

            <input type="text" id="product_color" name="product_color" value="<?php echo $row['product_color']; ?>"><br>
            <label for="phone_capacity">Phone Capacity:</label><br>

            <input type="text" id="phone_capacity" name="phone_capacity" value="<?php echo $row['Phone_capacity']; ?>"><br>
            <label for="product_stock">Product Stock:</label><br>

            <input type="number" id="product_stock" name="product_stock" value="<?php echo $row['product_stock']; ?>"><br>
            <label for="product_name">Product Name:</label><br>

            <input type="text" id="product_name" name="product_name" value="<?php echo $row['product_name']; ?>"><br>
            <label for="product_detail">Product Detail:</label><br>

            <input type="text" id="product_detail" name="product_detail" value="<?php echo $row['product_detail']; ?>"><br>
            <label for="product_cover_image">Product Cover Image:</label><br>

            <input type="file" id="product_cover_image" name="product_cover_image"><br>
            <label for="product_price">Product Price:</label><br>

            <input type="number" id="product_price" name="product_price" value="<?php echo $row['product_price']; ?>"><br>
            <label for="product_Image1">Product Image 1:</label><br>

            <input type="file" id="product_Image1" name="product_Image1"><br>
            <label for="product_Image2">Product Image 2:</label><br>
            
            <input type="file" id="product_Image2" name="product_Image2"><br>
            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
    </div>
</body>
</html>