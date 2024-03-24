<?php
// Include database connection
include('condb.php');

// Check if user ID is set and valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Escape user ID to prevent SQL injection
    $user_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Begin a MySQL transaction
    mysqli_begin_transaction($conn);

    // SQL query to delete user's address
    $sql_delete_address = "DELETE FROM `address` WHERE `user_ID` = $user_id";

    // Execute SQL query to delete user's address
    if (mysqli_query($conn, $sql_delete_address)) {
        // SQL query to delete user
        $sql_delete_user = "DELETE FROM `user_information` WHERE `user_ID` = $user_id";

        // Execute SQL query to delete user
        if (mysqli_query($conn, $sql_delete_user)) {
            // If both queries are successful, commit the transaction and redirect to admin-user.php with success message
            mysqli_commit($conn);
            header("Location: admin-user.php?deleted=1");
            exit();
        } else {
            // If an error occurs while deleting user, rollback the transaction and redirect to admin-user.php with error message
            mysqli_rollback($conn);
            header("Location: admin-user.php?error=1");
            exit();
        }
    } else {
        // If an error occurs while deleting user's address, rollback the transaction and redirect to admin-user.php with error message
        mysqli_rollback($conn);
        header("Location: admin-user.php?error=1");
        exit();
    }
} else {
    // If user ID is not set or not valid, redirect to admin-user.php with error message
    header("Location: admin-user.php?error=1");
    exit();
}
?>
