<?php 
include('condb.php'); 

$insert_user_query = null; // ประกาศตัวแปรนอกเพื่อให้สามารถปิดได้ทุกกรณี
$errors = []; // เก็บข้อผิดพลาดที่เกิดขึ้น

// เรียกใช้งาน session_start() ก่อนการใช้งาน session
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    // Check if confirmPassword matches password
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    }

    // Check if username or email already exists in user_information table
    $check_user_query = $conn->prepare("SELECT * FROM user_information WHERE username=? OR email=?");
    $check_user_query->bind_param("ss", $username, $email);
    $check_user_query->execute();
    $check_user_result = $check_user_query->get_result();

    if ($check_user_result->num_rows > 0) {
        $row = $check_user_result->fetch_assoc();
        if ($row["username"] == $username) {
            $errors[] = "ชื่อผู้ใช้นี้มีอยู่แล้ว.";
        }
        if ($row["email"] == $email) {
            $errors[] = "อีเมลนี้มีอยู่แล้ว";
        }
    }

    // If there are no errors, proceed with user creation
    if (empty($errors)) {
        // SQL to insert data into the database for user_information table
        $insert_user_query = $conn->prepare("INSERT INTO user_information (username, email, password) VALUES (?, ?, ?)");
        $insert_user_query->bind_param("sss", $username, $email, $hashed_password);

        // Execute the SQL query
        if ($insert_user_query->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $errors[] = ".";
        }
    }

    // Close prepared statements
    $check_user_query->close();
    if ($insert_user_query != null) {
        $insert_user_query->close();
    }
}

$conn->close();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .form-container {
            max-width: 400px;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="form-container">
                <h2 class="mb-4">Registration Form</h2>

                <!-- แสดงข้อผิดพลาด -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger mt-3" role="alert">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form id="registerForm" method="post" action="register.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <div id="usernameError" class="text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div id="emailError" class="text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div id="passwordError" class="text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                        <div id="confirmPasswordError" class="text-danger"></div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="registerButton">Register</button>
                    <a class="btn btn-outline-dark me-2" href="login.php">Login</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript function to validate email format
    function validateEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // JavaScript function to handle errors
    function showError(fieldId, errorMessage) {
        document.getElementById(fieldId + "Error").innerText = errorMessage;
        document.getElementById(fieldId).classList.add("is-invalid");
    }

    // Clear error messages when inputs are changed
    document.getElementById("username").addEventListener("input", function() {
        document.getElementById("usernameError").innerText = "";
        document.getElementById("username").classList.remove("is-invalid");
    });

    document.getElementById("email").addEventListener("input", function() {
        var email = document.getElementById("email").value;
        if (!validateEmail(email)) {
            showError("email", "โปรดระบุอีเมล์ที่ถูกต้อง");
        } else {
            document.getElementById("emailError").innerText = "";
            document.getElementById("email").classList.remove("is-invalid");
        }
    });

    // Check if confirmPassword matches password
    document.getElementById("confirmPassword").addEventListener("input", function() {
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirmPassword").value;

        if (password !== confirmPassword) {
            showError("password", "กรุณากรอกรหัสให้ตรงกัน");
            showError("confirmPassword", "กรุณากรอกรหัสให้ตรงกัน");
        } else {
            document.getElementById("passwordError").innerText = "";
            document.getElementById("password").classList.remove("is-invalid");
            document.getElementById("confirmPasswordError").innerText = "";
            document.getElementById("confirmPassword").classList.remove("is-invalid");
        }
    });

    // Check password length
    document.getElementById("password").addEventListener("input", function() {
        var password = document.getElementById("password").value;

        if (password.length < 8) {
            showError("password", "รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร");
        } else {
            document.getElementById("passwordError").innerText = "";
            document.getElementById("password").classList.remove("is-invalid");
        }
    });

    // Prevent form submission if there are errors
    document.getElementById("registerButton").addEventListener("click", function(event) {
        var errorsExist = false;

        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirmPassword").value;

        if (password !== confirmPassword || password.length < 8) {
            showError("password", "กรุณากรอกรหัสให้ตรงกันและมีความยาวอย่างน้อย 8 ตัวอักษร");
            showError("confirmPassword", "กรุณากรอกรหัสให้ตรงกันและมีความยาวอย่างน้อย 8 ตัวอักษร");
            errorsExist = true;
        }

        var email = document.getElementById("email").value;
        if (!validateEmail(email)) {
            showError("email", "โปรดระบุอีเมล์ที่ถูกต้อง");
            errorsExist = true;
        }

        if (errorsExist) {
            event.preventDefault();
        }
    });
</script>


</body>
</html>
