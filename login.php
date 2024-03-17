<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script>
        function showAlert(message) {
            var modal = document.createElement('div');
            modal.classList.add('modal', 'fade', 'show');
            modal.setAttribute('id', 'exampleModal');
            modal.setAttribute('tabindex', '-1');
            modal.setAttribute('role', 'dialog');
            modal.setAttribute('aria-labelledby', 'exampleModalLabel');
            modal.setAttribute('aria-hidden', 'true');
            modal.innerHTML = `
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Alert</h5>
                            <button type="button" class="btn-close" onclick="closeModal()" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            ${message}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="closeModal()">Close</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            var modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
        }

        function closeModal() {
            var modal = document.getElementById('exampleModal');
            modal.parentNode.removeChild(modal);
            // Redirect to the current page to clear POST data
            window.location.href = window.location.href;
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Login</h3>
                        <form action="login.php" method="post">
                            <div class="mb-3">
                                <label for="username_email" class="form-label">Username or Email</label>
                                <input type="text" class="form-control" id="username_email" name="username_email" placeholder="Enter your username or email">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Login</button> 
                                <a href="register.php" class="btn btn-primary">Register</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- สคริปต์ Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php 
session_start(); // เริ่มหรือดำเนิน session ที่มีอยู่
include('condb.php'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST["username_email"])) {
        $username_email = $_POST["username_email"];
        $condition = "username=? OR email=?";
    } else {
        echo '<script>showAlert("กรุณากรอกชื่อผู้ใช้งานหรืออีเมล์")</script>';
        exit(); // ออกถ้าไม่ได้รับค่าชื่อผู้ใช้งานหรืออีเมล์
    }

    // เตรียมคำสั่ง SQL ตามเงื่อนไข
    $stmt = $conn->prepare("SELECT * FROM user_information WHERE $condition");
    $stmt->bind_param("ss", $username_email, $username_email);
    $stmt->execute();
    $check_user_result = $stmt->get_result();
    
    if ($check_user_result->num_rows == 1) {
        $user_row = $check_user_result->fetch_assoc();
        $stored_password = $user_row["password"];
    
        if (password_verify($_POST["password"], $stored_password)) {
            $_SESSION  = $user_row; // เก็บข้อมูลผู้ใช้งานใน session
            
            switch ($_SESSION['user_type_ID']) {
                case 1:
                    header("Location: admin.php");
                    exit();
                    break;
                case 2:
                    header("Location: index.php");
                    exit();
                    break;
                default:
                    // จัดการประเภทผู้ใช้งานอื่นๆ ตามความเหมาะสม
                    break;
                    
            }

        } else {
            echo '<script>showAlert("รหัสผ่านไม่ถูกต้อง")</script>';
        }
    } else {
        echo '<script>showAlert("ไม่พบชื่อผู้ใช้งานหรืออีเมล์")</script>';
    }
    
}

$conn->close();
?>
