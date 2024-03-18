<?php
// Start the session


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
    $email = $_POST['email'];
    $user_type_ID = $_POST['user_type_ID'];

    // Check for duplicate email
    $check_email_sql = "SELECT * FROM `user_information` WHERE `email` = '$email' AND `user_ID` != $user_id";
    $check_email_query = mysqli_query($conn, $check_email_sql);
    if (mysqli_num_rows($check_email_query) > 0) {
        $_SESSION['error_message'] = "Error: Email already exists.";
        header("Location: ".$_SERVER['PHP_SELF']."?id=".$user_id); // Redirect back to form with error message
        exit(); // Ensure script execution stops after redirection
    }

    // Update user data in the database
    $update_sql = "UPDATE `user_information` SET `email` = '$email', `user_type_ID` = '$user_type_ID' WHERE `user_ID` = $user_id";
    $update_query = mysqli_query($conn, $update_sql);

    if ($update_query) {
        header("Location: admin-user.php?updated=1"); // Redirect back to user list with success message
        exit(); // Ensure script execution stops after redirection
    } else {
        $_SESSION['error_message'] = "Error updating user data"; // Handle database update error
        header("Location: ".$_SERVER['PHP_SELF']."?id=".$user_id); // Redirect back to form with error message
        exit(); // Ensure script execution stops after redirection
    }
}

// Query to fetch user types
$user_type_query = mysqli_query($conn, "SELECT * FROM `user_type`");
$user_types = mysqli_fetch_all($user_type_query, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Shop Homepage - Start Bootstrap Template</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@5.3.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet">

    <!-- JavaScript for confirmation dialog -->
    <script>
        // Function to confirm before submitting form
        function confirmChanges() {
            return confirm("Are you sure you want to make changes?");
        }
    </script>

    <!-- JavaScript to hide error message after a certain duration -->
    <script>
        // Function to hide error message after a certain duration
        function hideErrorMessage() {
            var errorMessage = document.getElementById('error-message');
            errorMessage.style.display = 'none';
        }

        // Automatically hide error message after 5 seconds
        setTimeout(hideErrorMessage, 5000);
    </script>
</head>
<body>
    <div class="container mt-4">
        <h2>Edit User</h2>

        <!-- Display error message if exists -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-message">
                <?= $_SESSION['error_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <form method="post" onsubmit="return confirmChanges()">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= $user_data['username']; ?>" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
            </div>
            <div class="form-group">
                <label for="fname">First Name</label>
                <input type="text" class="form-control" id="fname" name="fname" value="<?= $user_data['fname']; ?>" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
            </div>
            <div class="form-group">
                <label for="lname">Last Name</label>
                <input type="text" class="form-control" id="lname" name="lname" value="<?= $user_data['lname']; ?>" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= $user_data['email']; ?>"readonly style="background-color: #f8f9fa; cursor: not-allowed;">
            </div>
            <div class="form-group">
                <label for="user_type_ID">User Type</label>
                <select class="form-control" id="user_type_ID" name="user_type_ID">
                    <?php foreach ($user_types as $type) : ?>
                        <option value="<?= $type['user_type_ID']; ?>" <?= $type['user_type_ID'] == $user_data['user_type_ID'] ? 'selected' : ''; ?>><?= $type['user_type_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary save-button" style="margin-top: 25px;">Save Changes</button>
        </form>
    </div>
</body>
</html>