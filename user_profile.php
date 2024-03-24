<?php
// Start the session


// Include necessary files and check user type
include('navbar-user.php');
include('condb.php');
include('checkuser.php');

// Check if user is logged in
if (!isset($_SESSION['user_ID'])) {
    header("Location: login.php"); // Redirect to login page if user is not logged in
    exit();
}

// Get logged-in user ID
$user_id = $_SESSION['user_ID'];

// Query to fetch user information
$sql = "SELECT * FROM `user_information` WHERE `user_ID` = $user_id";
$query = mysqli_query($conn, $sql);
$user_data = mysqli_fetch_assoc($query);

// Check if the user exists
if (!$user_data) {
    echo "User not found"; // Handle case where user ID does not exist
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>User Information</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@5.3.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-4">
        <h2>User Information</h2>

        <div class="row">
            <div class="col-md-6">
                <ul class="list-group">
                    <li class="list-group-item"><strong>Username:</strong> <?= $user_data['username']; ?></li>
                    <li class="list-group-item"><strong>First Name:</strong> <?= $user_data['fname']; ?></li>
                    <li class="list-group-item"><strong>Last Name:</strong> <?= $user_data['lname']; ?></li>
                    <li class="list-group-item"><strong>Email:</strong> <?= $user_data['email']; ?></li>
                </ul>
            </div>
        </div>

        <!-- Button to redirect to edit page -->
        <a href="user_edit.php" class="btn btn-primary mt-3">Edit Information</a>
    </div>
</body>
</html>