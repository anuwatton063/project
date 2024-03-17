<?php
// Include necessary files and check user type
include('navbar-user.php');
include('condb.php');
include('checkuser.php');

// Check if user is logged in and redirect if not an admin
$user_type_ID = getUserTypeID();
if ($user_type_ID != 1){
    header("Location: index.php"); // Redirect to index.php
    exit(); // Ensure script execution stops after redirection
}

// Check if user ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin-user.php"); // Redirect back to user list if ID is not provided
    exit(); // Ensure script execution stops after redirection
}

// Get the user ID from the URL
$user_id = $_GET['id'];

// Query to fetch user information
$sql = "SELECT * FROM `user_information` WHERE `user_ID` = $user_id";
$query = mysqli_query($conn, $sql);
$user_data = mysqli_fetch_assoc($query);

// Check if the user exists
if (!$user_data) {
    echo "User not found"; // Handle case where user ID does not exist
    exit(); // Ensure script execution stops after error handling
}

// Handle form submission for updating user data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $username = $_POST['username'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $user_type_ID = $_POST['user_type_ID'];

    // Update user data in the database
    $update_sql = "UPDATE `user_information` SET `username` = '$username', `fname` = '$fname', `lname` = '$lname', `email` = '$email', `user_type_ID` = '$user_type_ID' WHERE `user_ID` = $user_id";
    $update_query = mysqli_query($conn, $update_sql);

    if ($update_query) {
        header("Location: admin-user.php?updated=1"); // Redirect back to user list with success message
        exit(); // Ensure script execution stops after redirection
    } else {
        echo "Error updating user data"; // Handle database update error
        exit(); // Ensure script execution stops after error handling
    }
}
?>

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
</head>
<body>
    <div class="container mt-4">
        <h2>Edit User</h2>
        <form method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo $user_data['username']; ?>">
            </div>
            <div class="form-group">
                <label for="fname">First Name</label>
                <input type="text" class="form-control" id="fname" name="fname" value="<?php echo $user_data['fname']; ?>">
            </div>
            <div class="form-group">
                <label for="lname">Last Name</label>
                <input type="text" class="form-control" id="lname" name="lname" value="<?php echo $user_data['lname']; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $user_data['email']; ?>">
            </div>
            <div class="form-group">
                <label for="user_type_ID">User Type ID</label>
                <input type="number" class="form-control" id="user_type_ID" name="user_type_ID" value="<?php echo $user_data['user_type_ID']; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</body>
</html>
