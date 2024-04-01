<?php
include('condb.php');
include 'navbar-user.php';

// Check if the user is logged in
if (!isset($_SESSION['user_ID'])) {
    // Redirect to login page or perform any other action if the user is not logged in
    header("Location: login.php");
    exit(); // Stop script execution
}

// Get user ID from session
$user_ID = $_SESSION['user_ID'];

// Check if delete confirmation is submitted
if (isset($_POST['confirm_delete'])) {
    $address_id = $_POST['address_id'];
    // Query to delete address
    $delete_sql = "DELETE FROM address WHERE address_ID = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $address_id);
    if ($delete_stmt->execute()) {
        // Redirect back to this page after deletion
        header("Location: ".$_SERVER['PHP_SELF']);
        exit(); // Stop script execution
    } else {
        // Handle deletion error
        echo "Error deleting address.";
    }
}

// Query to fetch user's addresses
$sql = "SELECT * FROM address WHERE user_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_ID);
$stmt->execute();
$result = $stmt->get_result();

// Close statement
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Addresses</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        /* Add your custom styles here */
    </style>
</head>

<body>

    <!-- Navigation -->
    <!-- This part has already been included at the top -->

    <div class="container">
        <br><h1>ข้อมูลที่อยู่</h1><br>

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
                    <th>Actions</th>
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
                        <td>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="address_id" value="<?php echo $row['address_ID']; ?>">
                                <!-- Button to trigger confirmation modal -->
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDelete<?php echo $row['address_ID']; ?>">
                                    Delete
                                </button>

                                <!-- Modal for delete confirmation -->
                                <div class="modal fade" id="confirmDelete<?php echo $row['address_ID']; ?>" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmDeleteLabel">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                คุณต้องการลบข้อมูลที่อยู่ของคุณหรือไม่
                                            </div>
                                            <div class="modal-footer">
                                                <!-- Button to cancel deletion -->
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <!-- Form to submit delete confirmation -->
                                                <form method="POST">
                                                    <input type="hidden" name="address_id" value="<?php echo $row['address_ID']; ?>">
                                                    <button type="submit" name="confirm_delete" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <a href="user_addressEdit.php?address_id=<?php echo $row['address_ID']; ?>" class="btn btn-primary">Edit</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <br>

        <a href="user_addressAdd.php"><button class="btn btn-primary">Add Address</button></a>
    </div>

    <!-- Bootstrap core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
