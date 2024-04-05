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
    // Retrieve type_name from form data
    $type_name = $_POST['type_name'];

    // Prepare SQL statement to insert data into products_types table
    $sql = "INSERT INTO products_types (type_name) VALUES ('$type_name')";

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

// Query to retrieve all product types
$sql = "SELECT * FROM products_types";

// Execute the query
$result = mysqli_query($conn, $sql);

// Check if there are any rows returned
// Check if there are any rows returned
if (mysqli_num_rows($result) > 0) {
    // Output data of each row
    echo "<div class='center existing-product-types'>";
    echo "<h2>ประเภทสินค้าต่างๆที่มีอยู่ในระบบ</h2>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<li>" . $row['type_name'] . "</li>";
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
    <title>เพิ่มประเภทสินค้า</title>
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        /* CSS Styles for centering */
        .center {
            margin: 0 auto;
            text-align: center;
        }

        /* CSS Styles for existing product types */
        .existing-product-types {
            text-align: center; /* Align text to the center */
        }
    </style>
</head>

<body>
<div class="container">
    <h1>เพิ่มข้อมูล</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="mb-3">
            <label for="type_name" class="form-label">Type Name:</label>
            <input type="text" name="type_name" id="type_name" class="form-control form-control-sm" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Type</button>
    </form>
</div>
</body>
</html>
