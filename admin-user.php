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

// Define the number of users to display per page
$users_per_page = 15;

// Determine the current page number
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the SQL query
$offset = ($current_page - 1) * $users_per_page;

// Initialize search query variable
$search_query = '';

// If search query is provided, sanitize it
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
}

// Additional condition for SQL query based on search query
$search_condition = '';
if ($search_query !== '') {
    $search_condition = "WHERE username LIKE '%$search_query%' OR fname LIKE '%$search_query%' OR lname LIKE '%$search_query%' OR email LIKE '%$search_query%'"; // Adjust as needed
}

// Query to fetch user information for the current page with search condition
$sql = "SELECT ui.*, ut.user_type_name 
        FROM `user_information` ui 
        INNER JOIN `user_type` ut ON ui.user_type_ID = ut.user_type_ID 
        $search_condition
        LIMIT $offset, $users_per_page";
$query_sql = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>User Information</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@5.3.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="mt-4">ข้อมูลสมาชิก</h1>
                <div class="col-lg-12">
                    <form action="admin-user.php" method="GET">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="ค้นหาข้อมูลทั้งหมดจากฐานข้อมูล" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                            <button class="btn btn-outline-secondary" type="submit">ค้นหา</button>
                        </div>
                    </form>
                </div>
                <?php
                // Check if there's a message to display
                if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
                    echo '<div class="alert alert-success" role="alert">ลบผู้ใช้เรียบร้อยแล้ว</div>';
                } elseif (isset($_GET['error']) && $_GET['error'] == 1) {
                    echo '<div class="alert alert-danger" role="alert">เกิดข้อผิดพลาดในการลบผู้ใช้</div>';
                }
                ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>ชื่อจริง</th>
                            <th>นามสกุล</th>
                            <th>Email</th>
                            <th>User Type</th> <!-- New column for user type -->
                            <th>Actions</th> <!-- New column for actions -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Loop through each row of data
                        while ($row = mysqli_fetch_assoc($query_sql)) {
                            echo "<tr>";
                            echo "<td>" . $row['user_ID'] . "</td>";
                            echo "<td>" . $row['username'] . "</td>";
                            echo "<td>" . $row['fname'] . "</td>";
                            echo "<td>" . $row['lname'] . "</td>";
                            echo "<td>" . $row['email'] . "</td>";
                            echo "<td>" . $row['user_type_name'] . "</td>"; // Display user type name
                            echo "<td>";
                            echo "<a href='admin_edit_user.php?id=" . $row['user_ID'] . "' class='btn btn-primary'>แก้ไข</a>";
                            echo "<a href='delete_user.php?id=" . $row['user_ID'] . "' class='btn btn-danger' onclick='return confirm(\"คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้คนนี้?\")'>ลบ</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <?php
                // Display pagination if there are more than one page
                $total_users_query = mysqli_query($conn, "SELECT COUNT(*) AS total_users FROM `user_information` $search_condition");
                $total_users_data = mysqli_fetch_assoc($total_users_query);
                $total_users = $total_users_data['total_users'];
                $total_pages = ceil($total_users / $users_per_page);

                if ($total_pages > 1) {
                    echo "<nav aria-label='Page navigation'>";
                    echo "<ul class='pagination justify-content-center'>";
                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo "<li class='page-item" . ($current_page == $i ? " active" : "") . "'><a class='page-link' href='admin-user.php?page=" . $i . "&search=" . urlencode($search_query) . "'>" . $i . "</a></li>";
                    }
                    echo "</ul>";
                    echo "</nav>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
