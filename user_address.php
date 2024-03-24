<?php
include('condb.php');
include 'navbar-user.php';

// Check if the user is logged in
if (!isset($_SESSION['user_ID'])) {
    // Redirect to login page or perform any other action if the user is not logged in
    header("Location: login.php");
    exit();
}

// Get user ID from session
$user_ID = $_SESSION['user_ID'];

// Delete address if delete button is clicked
if (isset($_POST['delete_address'])) {
    $address_id = $_POST['address_id'];
    // Display a confirmation dialog before deletion
    echo "<script>
            if(confirm('Are you sure you want to delete this address?')) {
                window.location.href = 'delete_address.php?address_id=$address_id';
            }
          </script>";
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
        <h1>User Addresses</h1>

        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Address Information</th>
                    <th>Tumbon</th>
                    <th>Amphoe</th>
                    <th>Province</th>
                    <th>Zipcode</th>
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
                                <button type="submit" name="delete_address" class="btn btn-danger">Delete</button>
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