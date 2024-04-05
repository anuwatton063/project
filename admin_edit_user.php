<?php

include('navbar-user.php');
include('condb.php');
include('checkuser.php');

$user_type_ID = getUserTypeID();
if ($user_type_ID != 1){
    header("Location: index.php"); 
    exit(); 
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin-user.php"); 
    exit(); 
}

$user_id = $_GET['id'];

$sql = "SELECT * FROM `user_information` WHERE `user_ID` = $user_id";
$query = mysqli_query($conn, $sql);
$user_data = mysqli_fetch_assoc($query);

if (!$user_data) {
    echo "User not found"; 
    exit(); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $user_type_ID = $_POST['user_type_ID'];

    $check_email_sql = "SELECT * FROM `user_information` WHERE `email` = '$email' AND `user_ID` != $user_id";
    $check_email_query = mysqli_query($conn, $check_email_sql);
    if (mysqli_num_rows($check_email_query) > 0) {
        $_SESSION['error_message'] = "Error: Email already exists.";
        header("Location: ".$_SERVER['PHP_SELF']."?id=".$user_id); 
        exit(); 
    }

    $update_sql = "UPDATE `user_information` SET `email` = '$email', `user_type_ID` = '$user_type_ID' WHERE `user_ID` = $user_id";
    $update_query = mysqli_query($conn, $update_sql);

    if ($update_query) {
        header("Location: admin-user.php?updated=1"); 
        exit(); 
    } else {
        $_SESSION['error_message'] = "Error updating user data"; 
        header("Location: ".$_SERVER['PHP_SELF']."?id=".$user_id); 
        exit(); 
    }
}


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
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="css/styles.css" rel="stylesheet">

    <script>
        function confirmChanges() {
            return confirm("Are you sure you want to make changes?");
        }
    </script>

    <script>
        function hideErrorMessage() {
            var errorMessage = document.getElementById('error-message');
            errorMessage.style.display = 'none';
        }

        setTimeout(hideErrorMessage, 5000);
    </script>
</head>
<body>
    <div class="container mt-4">
        <h2>ข้อมูลลูกค้า</h2><br>

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
                <label for="fname">ชื่อจริง</label>
                <input type="text" class="form-control" id="fname" name="fname" value="<?= $user_data['fname']; ?>" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
            </div>
            <div class="form-group">
                <label for="lname">นามสกุล</label>
                <input type="text" class="form-control" id="lname" name="lname" value="<?= $user_data['lname']; ?>" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= $user_data['email']; ?>"readonly style="background-color: #f8f9fa; cursor: not-allowed;">
            </div>
            <div class="form-group">
                <label for="user_type_ID">ปรเภทผู้ใช้</label>
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