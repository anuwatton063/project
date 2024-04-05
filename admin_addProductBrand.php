<?php
include 'navbar-user.php';
include('condb.php');
include 'checkuser.php';

$user_type_ID = getUserTypeID();
if ($user_type_ID == 1) {
    include 'navbar-admin.php';
}
if ($user_type_ID != 1) {
    header("Location: index.php"); 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $brand_name = $_POST['brand_name'];

    $sql = "INSERT INTO product_brand (brand_name) VALUES ('$brand_name')";

    if (mysqli_query($conn, $sql)) {
        header("Location: product.php");
        exit(); 
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

$sql = "SELECT * FROM product_brand";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<div class='center existing-product-brands'>";
    echo "<h2>แบลนด์ต่างๆที่มีอยู่ในระบบ</h2>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<li>" . $row['brand_name'] . "</li>";
    }
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div class='center'>0 results</div>"; 
}

mysqli_free_result($result);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Add Product Brand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        .center {
            margin: 0 auto;
            text-align: center;
        }

        .existing-product-brands {
            text-align: center;
        }
    </style>
</head>

<body>
<div class="container">
    <h1>เพิ่มข้อมูล</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="mb-3">
            <label for="brand_name" class="form-label">Brand Name:</label>
            <input type="text" name="brand_name" id="brand_name" class="form-control form-control-sm" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Brand</button>
    </form>
</div>
</body>
</html>
