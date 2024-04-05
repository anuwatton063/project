<?php
include('condb.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = mysqli_real_escape_string($conn, $_GET['id']);

    mysqli_begin_transaction($conn);

    $sql_delete_address = "DELETE FROM `address` WHERE `user_ID` = $user_id";

    if (mysqli_query($conn, $sql_delete_address)) {
        $sql_delete_user = "DELETE FROM `user_information` WHERE `user_ID` = $user_id";

        if (mysqli_query($conn, $sql_delete_user)) {
            mysqli_commit($conn);
            header("Location: admin-user.php?deleted=1");
            exit();
        } else {
            mysqli_rollback($conn);
            header("Location: admin-user.php?error=1");
            exit();
        }
    } else {
        mysqli_rollback($conn);
        header("Location: admin-user.php?error=1");
        exit();
    }
} else {
    header("Location: admin-user.php?error=1");
    exit();
}
?>
