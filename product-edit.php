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
    $product_price = mysqli_real_escape_string($conn, $_POST['product_price']);

    // Initialize variables to retain old image filenames
    $product_cover_image = '';
    $product_Image1 = '';
    $product_Image2 = '';

    // Check if new cover image is uploaded
    if (!empty($_FILES['product_cover_image']['name'])) {
        $product_cover_image = $_FILES['product_cover_image']['name'];
        $target_dir = "../project/png/";  
        move_uploaded_file($_FILES['product_cover_image']['tmp_name'], $target_dir . $product_cover_image);
    }

    // Check if new image 1 is uploaded
    if (!empty($_FILES['product_Image1']['name'])) {
        $product_Image1 = $_FILES['product_Image1']['name'];
        $target_dir = "../project/png/";  
        move_uploaded_file($_FILES['product_Image1']['tmp_name'], $target_dir . $product_Image1);
    }

    // Check if new image 2 is uploaded
    if (!empty($_FILES['product_Image2']['name'])) {
        $product_Image2 = $_FILES['product_Image2']['name'];
        $target_dir = "../project/png/";  
        move_uploaded_file($_FILES['product_Image2']['tmp_name'], $target_dir . $product_Image2);
    }

    // Update the product in the database
    $sql = "UPDATE products_phone AS pp
            INNER JOIN products_types AS pt ON pp.product_type_ID = pt.product_type_ID
            INNER JOIN product_brand AS pb ON pp.product_brand_ID = pb.product_brand_ID
            SET pp.product_type_ID = '$product_type_ID',
                pp.product_brand_ID = '$product_brand_ID',
                pp.product_color = '$product_color',
                pp.Phone_capacity = '$phone_capacity',
                pp.product_stock = '$product_stock',
                pp.product_name = '$product_name',
                pp.product_detail = '$product_detail',
                pp.product_price = '$product_price'";

    // Append cover image update to SQL query if provided
    if (!empty($product_cover_image)) {
        $sql .= ", pp.product_cover_image = '$product_cover_image'";
    }

    // Append image 1 update to SQL query if provided
    if (!empty($product_Image1)) {
        $sql .= ", pp.product_Image1 = '$product_Image1'";
    }

    // Append image 2 update to SQL query if provided
    if (!empty($product_Image2)) {
        $sql .= ", pp.product_Image2 = '$product_Image2'";
    }

    $sql .= " WHERE pp.product_ID = $product_id";

    if (mysqli_query($conn, $sql)) {
        // Product updated successfully, redirect to product.php
        header("Location: product.php?id=$product_id");
        exit();
    } else {
        echo "Error updating product: " . mysqli_error($conn);
    }
}

// Fetch the product details to populate the form
$sql = "SELECT pp.*, pt.type_name, pb.brand_name 
        FROM products_phone AS pp
        INNER JOIN products_types AS pt ON pp.product_type_ID = pt.product_type_ID
        INNER JOIN product_brand AS pb ON pp.product_brand_ID = pb.product_brand_ID
        WHERE pp.product_ID = $product_id";
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
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $product_id; ?>" method="POST" enctype="multipart/form-data" id="editForm">
            <label for="product_type_ID">Product Type:</label><br>
            <select id="product_type_ID" name="product_type_ID">
                <?php
                $sql_types = "SELECT * FROM products_types";
                $result_types = mysqli_query($conn, $sql_types);
                if ($result_types && mysqli_num_rows($result_types) > 0) {
                    while ($type = mysqli_fetch_assoc($result_types)) {
                        echo "<option value='{$type['product_type_ID']}'";
                        if ($type['product_type_ID'] == $row['product_type_ID']) {
                            echo " selected";
                        }
                        echo ">{$type['type_name']}</option>";
                    }
                }
                ?>
            </select><br>

            <label for="product_brand_ID">Product Brand:</label><br>
            <select id="product_brand_ID" name="product_brand_ID">
                <?php
                $sql_brands = "SELECT * FROM product_brand";
                $result_brands = mysqli_query($conn, $sql_brands);
                if ($result_brands && mysqli_num_rows($result_brands) > 0) {
                    while ($brand = mysqli_fetch_assoc($result_brands)) {
                        echo "<option value='{$brand['product_brand_ID']}'";
                        if ($brand['product_brand_ID'] == $row['product_brand_ID']) {
                            echo " selected";
                        }
                        echo ">{$brand['brand_name']}</option>";
                    }
                }
                ?>
            </select><br>

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
            <input type="file" id="product_cover_image" name="product_cover_image" onchange="previewImage(this, 'coverImagePreview')"><br>
            <?php if (!empty($row['product_cover_image'])): ?>
                <img id="coverImagePreview" src="../project/png/<?php echo $row['product_cover_image']; ?>" alt="Product Cover Image" style="max-width: 200px;"><br>
            <?php endif; ?>

            <label for="product_price">Product Price:</label><br>
            <input type="number" id="product_price" name="product_price" value="<?php echo $row['product_price']; ?>"><br>

            <label for="product_Image1">Product Image 1:</label><br>
            <input type="file" id="product_Image1" name="product_Image1" onchange="previewImage(this, 'image1Preview')"><br>
            <?php if (!empty($row['product_Image1'])): ?>
                <img id="image1Preview" src="../project/png/<?php echo $row['product_Image1']; ?>" alt="Product Image 1" style="max-width: 200px;"><br>
            <?php endif; ?>

            <label for="product_Image2">Product Image 2:</label><br>
            <input type="file" id="product_Image2" name="product_Image2" onchange="previewImage(this, 'image2Preview')"><br>
            <?php if (!empty($row['product_Image2'])): ?>
                <img id="image2Preview" src="../project/png/<?php echo $row['product_Image2']; ?>" alt="Product Image 2" style="max-width: 200px;"><br>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
    </div>

    <!-- JavaScript for image preview -->
    <script>
        function previewImage(input, previewId) {
            var preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = "block";
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = "#";
                preview.style.display = "none";
            }
        }
    </script>
</body>
</html>