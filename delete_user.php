<?php
// Include database connection
include('condb.php');

// Check if user ID is set and valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Escape user ID to prevent SQL injection
    $user_id = mysqli_real_escape_string($conn, $_GET['id']);

    // SQL query to delete user
    $sql = "DELETE FROM `user_information` WHERE `user_ID` = $user_id";

    // Execute SQL query
    if (mysqli_query($conn, $sql)) {
        // If user is deleted successfully, redirect to admin-user.php with success message
        header("Location: admin-user.php?deleted=1");
        exit();
    } else {
        // If an error occurs, redirect to admin-user.php with error message
        header("Location: admin-user.php?error=1");
        exit();
    }
} else {
    // If user ID is not set or not valid, redirect to admin-user.php with error message
    header("Location: admin-user.php?error=1");
    exit();
}
?>
