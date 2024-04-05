<?php
include 'navbar-user.php';
include('condb.php');

include 'checkuser.php';

        $user_type_ID = getUserTypeID();
        if ($user_type_ID == 1) {
            include 'navbar-admin.php';
            
        }
        if ($user_type_ID != 1){
            header("Location: index.php"); // Redirect to index.php
            exit(); // Ensure script execution stops after redirection
        }

// Get the user ID from the URL
$user_id = $_GET['user_id'];

// Query to fetch user's addresses and user information
$sql = "SELECT a.*, ui.*
        FROM address a
        INNER JOIN user_information ui ON a.user_ID = ui.user_ID
        WHERE a.user_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Close statement
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>admin user</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="mt-4">ข้อมูลที่อยู่</h1><br>
                <table class="table">
                    <thead>
                        <tr>
                        <th>ชื่อผู้รับ</th>
                        <th>เบอร์โทรศัพท์</th>
                        <th>ข้อมูลที่อยู่</th>
                        <th>ตำบล</th>
                        <th>อำเภอ</th>
                        <th>จังหวัด</th>
                        <th>รหัสไปรษณีย์</th>
                            <!-- Additional user information columns -->
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['phone']; ?></td>
                                <td><?php echo $row['Address_information']; ?></td>
                                <td><?php echo $row['tumbon']; ?></td>
                                <td><?php echo $row['amphoe']; ?></td>
                                <td><?php echo $row['province']; ?></td>
                                <td><?php echo $row['Zipcode']; ?></td>
                                <!-- Display additional user information -->
                                
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>