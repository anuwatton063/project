<?php
include 'navbar-user.php';
include('condb.php');
include 'checkuser.php';

$user_type_ID = getUserTypeID();
if ($user_type_ID != 1) {
    header("Location: index.php"); 
    exit(); 
}


if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php"); 
    exit();
}

$product_id = mysqli_real_escape_string($conn, $_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_type_ID = mysqli_real_escape_string($conn, $_POST['product_type_ID']);
    $product_brand_ID = mysqli_real_escape_string($conn, $_POST['product_brand_ID']);
    $product_color = mysqli_real_escape_string($conn, $_POST['product_color']);
    $phone_capacity = mysqli_real_escape_string($conn, $_POST['phone_capacity']);
    $product_stock = mysqli_real_escape_string($conn, $_POST['product_stock']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_detail = mysqli_real_escape_string($conn, $_POST['product_detail']);
    $product_price = mysqli_real_escape_string($conn, $_POST['product_price']);

    $product_cover_image = '';
    $product_Image1 = '';
    $product_Image2 = '';

    if (!empty($_FILES['product_cover_image']['name'])) {
        $product_cover_image = $_FILES['product_cover_image']['name'];
        $target_dir = "../project/png/";  
        move_uploaded_file($_FILES['product_cover_image']['tmp_name'], $target_dir . $product_cover_image);
    }

    if (!empty($_FILES['product_Image1']['name'])) {
        $product_Image1 = $_FILES['product_Image1']['name'];
        $target_dir = "../project/png/";  
        move_uploaded_file($_FILES['product_Image1']['tmp_name'], $target_dir . $product_Image1);
    }

    if (!empty($_FILES['product_Image2']['name'])) {
        $product_Image2 = $_FILES['product_Image2']['name'];
        $target_dir = "../project/png/";  
        move_uploaded_file($_FILES['product_Image2']['tmp_name'], $target_dir . $product_Image2);
    }

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

    if (!empty($product_cover_image)) {
        $sql .= ", pp.product_cover_image = '$product_cover_image'";
    }

    if (!empty($product_Image1)) {
        $sql .= ", pp.product_Image1 = '$product_Image1'";
    }

    if (!empty($product_Image2)) {
        $sql .= ", pp.product_Image2 = '$product_Image2'";
    }

    $sql .= " WHERE pp.product_ID = $product_id";

    if (mysqli_query($conn, $sql)) {
        header("Location: product.php?id=$product_id");
        exit();
    } else {
        echo "Error updating product: " . mysqli_error($conn);
    }
}

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

$row = mysqli_fetch_assoc($result); 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>

        #product_detail {
            width: 100%; 
            height: 100px; 
            resize: vertical; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Product</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $product_id; ?>" method="POST" enctype="multipart/form-data" id="editForm">
            <label for="product_type_ID">ประเภทสินค้า:</label><br>
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

            <label for="product_brand_ID">ยี่ห้อสินค้า:</label><br>
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

            <label for="product_color">สี:</label><br>
            <input type="text" id="product_color" name="product_color" value="<?php echo $row['product_color']; ?>"><br>

            <label for="phone_capacity">ความจุ:</label><br>
            <input type="text" id="phone_capacity" name="phone_capacity" value="<?php echo $row['Phone_capacity']; ?>"><br>

            <label for="product_stock">จำนวนสินค้า:</label><br>
            <input type="number" id="product_stock" name="product_stock" value="<?php echo $row['product_stock']; ?>"><br>

            <label for="product_name">ชื่อ:</label><br>
            <input type="text" id="product_name" name="product_name" value="<?php echo $row['product_name']; ?>"><br>

            <label for="product_detail">รายละเอียด:</label><br>
            <textarea id="product_detail" name="product_detail"><?php echo $row['product_detail']; ?></textarea><br>

            <label for="product_price">ราคา:</label><br>
            <input type="number" id="product_price" name="product_price" value="<?php echo $row['product_price']; ?>"><br>

            <label for="product_cover_image">ภาพสินค้า:</label><br>
            <input type="file" id="product_cover_image" name="product_cover_image" onchange="previewImage(this, 'coverImagePreview')"><br>
            <?php if (!empty($row['product_cover_image'])): ?>
                <img id="coverImagePreview" src="../project/png/<?php echo $row['product_cover_image']; ?>" alt="Product Cover Image" style="max-width: 200px;"><br>
            <?php endif; ?>

            <label for="product_Image1">ภาพสินค้า 1:</label><br>
            <input type="file" id="product_Image1" name="product_Image1" onchange="previewImage(this, 'image1Preview')"><br>
            <?php if (!empty($row['product_Image1'])): ?>
                <img id="image1Preview" src="../project/png/<?php echo $row['product_Image1']; ?>" alt="Product Image 1" style="max-width: 200px;"><br>
            <?php endif; ?>

            <label for="product_Image2">ภาพสินค้า 2:</label><br>
            <input type="file" id="product_Image2" name="product_Image2" onchange="previewImage(this, 'image2Preview')"><br>
            <?php if (!empty($row['product_Image2'])): ?>
                <img id="image2Preview" src="../project/png/<?php echo $row['product_Image2']; ?>" alt="Product Image 2" style="max-width: 200px;"><br>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
    </div>

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
