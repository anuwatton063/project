<?php
include 'navbar-user.php';
include('condb.php');

include 'checkuser.php';

        $user_type_ID = getUserTypeID();
        if ($user_type_ID == 1) {
            include 'navbar-admin.php';
            
        }
        if ($user_type_ID != 1){
            header("Location: index.php"); 
            exit(); 
        }

$users_per_page = 15;

$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;

$offset = ($current_page - 1) * $users_per_page;

$search_query = '';

if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
}

$search_condition = '';
if ($search_query !== '') {
    $search_condition = "AND (ui.user_ID LIKE '%$search_query%' OR ui.username LIKE '%$search_query%' OR ui.fname LIKE '%$search_query%' OR ui.lname LIKE '%$search_query%' OR ui.email LIKE '%$search_query%' OR ut.user_type_name LIKE '%$search_query%')"; // Include user_ID in search
}

$user_type_filter = '';

if (isset($_GET['user_type'])) {
    $user_type_filter = mysqli_real_escape_string($conn, $_GET['user_type']);
}

$user_type_condition = '';
if ($user_type_filter !== '') {
    $user_type_condition = "AND ui.user_type_ID = '$user_type_filter'";
}

$sql = "SELECT ui.*, ut.user_type_name 
        FROM `user_information` ui 
        INNER JOIN `user_type` ut ON ui.user_type_ID = ut.user_type_ID 
        WHERE 1=1 $search_condition $user_type_condition
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
    <title>admin user</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
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
                            <select class="form-select" name="user_type">
                                <option value="" <?php echo ($user_type_filter === '') ? 'selected' : ''; ?>>เลือกประเภทผู้ใช้</option>
                                <?php
                                $user_type_query = mysqli_query($conn, "SELECT * FROM `user_type`");
                                while ($type_row = mysqli_fetch_assoc($user_type_query)) {
                                    echo "<option value='" . $type_row['user_type_ID'] . "' " . ($user_type_filter == $type_row['user_type_ID'] ? 'selected' : '') . ">" . $type_row['user_type_name'] . "</option>";
                                }
                                ?>
                            </select>
                            <button class="btn btn-outline-secondary" type="submit">ค้นหา</button>
                        </div>
                    </form>
                </div>
                <?php
                if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
                    echo '<div id="successAlert" class="alert alert-success" role="alert">ลบผู้ใช้เรียบร้อยแล้ว</div>';
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
                            <th>ประเภทผู้ใช้</th>
                            <th>Actions</th> 
                            <th>ที่อยู่</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_assoc($query_sql)) {
                            echo "<tr>";
                            echo "<td>" . $row['user_ID'] . "</td>";
                            echo "<td>" . $row['username'] . "</td>";
                            echo "<td>" . $row['fname'] . "</td>";
                            echo "<td>" . $row['lname'] . "</td>";
                            echo "<td>" . $row['email'] . "</td>";
                            echo "<td>" . $row['user_type_name'] . "</td>"; 
                            echo "<td>";
                            echo "<a href='admin_edit_user.php?id=" . $row['user_ID'] . "' class='btn btn-primary'>แก้ไข</a>";
                            echo "<a href='delete_user.php?id=" . $row['user_ID'] . "' class='btn btn-danger' onclick='return confirm(\"คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้คนนี้?\")'>ลบ</a>";
                            echo "</td>";
                            echo "<td>";
                            echo "<a href='admin_useraddress.php?user_id=" . $row['user_ID'] . "' class='btn btn-success'>Address</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <?php
                $total_users_query = mysqli_query($conn, "SELECT COUNT(*) AS total_users FROM `user_information` ui INNER JOIN `user_type` ut ON ui.user_type_ID = ut.user_type_ID WHERE 1=1 $search_condition $user_type_condition");
                $total_users_data = mysqli_fetch_assoc($total_users_query);
                $total_users = $total_users_data['total_users'];
                $total_pages = ceil($total_users / $users_per_page);

                if ($total_pages > 1) {
                    echo "<nav aria-label='Page navigation'>";
                    echo "<ul class='pagination justify-content-center'>";
                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo "<li class='page-item" . ($current_page == $i ? " active" : "") . "'><a class='page-link' href='admin-user.php?page=" . $i . "&search=" . urlencode($search_query) . "&user_type=" . urlencode($user_type_filter) . "'>" . $i . "</a></li>";
                    }
                    echo "</ul>";
                    echo "</nav>";
                }
                ?>

                <script>
                    
                    function showSuccessMessage() {
                        var alertBox = document.getElementById('successAlert');
                        alertBox.style.display = 'block'; 

                        setTimeout(function() {
                            alertBox.style.display = 'none';
                        }, 5000); 
                    }
                    window.onload = function() {
                        showSuccessMessage();
                    };
                </script>

            </div>
        </div>
    </div>
</body>
</html>