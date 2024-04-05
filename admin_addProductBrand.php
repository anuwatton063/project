<?php
// Include the connection file
include 'navbar-user.php';
include('condb.php');
include 'checkuser.php';

$user_type_ID = getUserTypeID();
if ($user_type_ID == 1) {
    include 'navbar-admin.php';
}
if ($user_type_ID != 1) {
    header("Location: index.php"); // Redirect to index.php
    exit(); // Ensure script execution stops after redirection
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve brand_name from form data
    $brand_name = $_POST['brand_name'];

    // Prepare SQL statement to insert data into product_brand table
    $sql = "INSERT INTO product_brand (brand_name) VALUES ('$brand_name')";

    // Execute SQL statement
    if (mysqli_query($conn, $sql)) {
        // If insertion successful, redirect back to product editor page
        header("Location: product.php");
        exit(); // Ensure script execution stops after redirection
    } else {
        // If there was an error with the SQL statement
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Query to retrieve all product brands
$sql = "SELECT * FROM product_brand";

// Execute the query
$result = mysqli_query($conn, $sql);

// Check if there are any rows returned
if (mysqli_num_rows($result) > 0) {
    // Output data of each row
    echo "<div class='center existing-product-brands'>";
    echo "<h2>แบลนด์ต่างๆที่มีอยู่ในระบบ</h2>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<li>" . $row['brand_name'] . "</li>";
    }
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div class='center'>0 results</div>"; // If no rows returned
}

// Free result set
mysqli_free_result($result);

// Close connection
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
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        /* CSS Styles for centering */
        .center {
            margin: 0 auto;
            text-align: center;
        }

        /* CSS Styles for existing product brands */
        .existing-product-brands {
            text-align: center; /* Align text to the center */
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
