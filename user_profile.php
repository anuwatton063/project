<?php
include('navbar-user.php');
include('condb.php');
include('checkuser.php');

if (!isset($_SESSION['user_ID'])) {
    header("Location: login.php"); 
    exit();
}

$user_id = isset($_SESSION['user_ID']) ? mysqli_real_escape_string($conn, $_SESSION['user_ID']) : '';

$sql = "SELECT * FROM `user_information` WHERE `user_ID` = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$query = mysqli_stmt_get_result($stmt);
$user_data = mysqli_fetch_assoc($query);

if (!$user_data) {
    header("Location: error.php");
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
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .container2 {
            max-width: 800px;
            margin: 50px auto;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #404040;
            color: #fff;
            border-radius: 10px 10px 0 0;
        }

        .card-body {
            background-color: #fff;
            border-radius: 0 0 10px 10px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .list-group-item {
            background-color: transparent;
            border: none;
        }

        .list-group-item strong {
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container2 mt-4">
        <div class="card">
            <div class="card-header">
                <h2 class="text-center">ข้อมูลผู้ใช้</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Username:</strong> <?= htmlspecialchars($user_data['username']); ?></li>
                            <li class="list-group-item"><strong>ชื่อจริง:</strong> <?= htmlspecialchars($user_data['fname']); ?></li>
                            <li class="list-group-item"><strong>นามสกุล:</strong> <?= htmlspecialchars($user_data['lname']); ?></li>
                            <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($user_data['email']); ?></li>
                        </ul>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="user_edit.php" class="btn btn-primary">Edit Information</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>