<?php

include('navbar-user.php');
include('condb.php');
include('checkuser.php');

if (!isset($_SESSION['user_ID'])) {
    header("Location: login.php");
}

$user_id = $_SESSION['user_ID'];

$sql = "SELECT * FROM `user_information` WHERE `user_ID` = $user_id";
$query = mysqli_query($conn, $sql);
$user_data = mysqli_fetch_assoc($query);

if (!$user_data) {
    echo "User not found"; 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];

    $check_username_sql = "SELECT * FROM `user_information` WHERE `username` = '$username' AND `user_ID` != $user_id";
    $check_username_query = mysqli_query($conn, $check_username_sql);
    if (mysqli_num_rows($check_username_query) > 0) {
        $_SESSION['error_message'] = "Error: Username already exists.";
        header("Location: ".$_SERVER['PHP_SELF']); 
        exit();
    }

    $check_email_sql = "SELECT * FROM `user_information` WHERE `email` = '$email' AND `user_ID` != $user_id";
    $check_email_query = mysqli_query($conn, $check_email_sql);
    if (mysqli_num_rows($check_email_query) > 0) {
        $_SESSION['error_message'] = "Error: Email already exists.";
        header("Location: ".$_SERVER['PHP_SELF']); 
        exit();
    }

    $update_sql = "UPDATE `user_information` SET `username` = '$username', `fname` = '$fname', `lname` = '$lname', `email` = '$email' WHERE `user_ID` = $user_id";
    $update_query = mysqli_query($conn, $update_sql);

    if ($update_query) {
        
        header("Location: user_profile.php"); 
        exit();
    } else {
        $_SESSION['error_message'] = "Error updating user data"; 
        header("Location: ".$_SERVER['PHP_SELF']); 
        exit();
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
    <title>User Information</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-4">
        <h2>Edit User</h2>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= $user_data['username']; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="fname">ชื่อจริง</label>
                <input type="text" class="form-control" id="fname" name="fname" value="<?= $user_data['fname']; ?>">
            </div>
            <div class="form-group">
                <label for="lname">นามสกุล</label>
                <input type="text" class="form-control" id="lname" name="lname" value="<?= $user_data['lname']; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= $user_data['email']; ?>">
            </div>
            
            <button type="submit" class="btn btn-primary save-button" style="margin-top: 25px;">Save Changes</button>
        </form>
    </div>
</body>
</html>