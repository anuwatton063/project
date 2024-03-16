<?php include 'navbar-user.php'; ?>
<?php include('condb.php'); ?>
<?php include 'checkuser.php'; ?>
<?php
// Check user type ID and include navbar_admin.php if necessary
$user_type_ID = getUserTypeID();
if ($user_type_ID == 1) {
    include 'navbar-admin.php';
}
if ($user_type_ID != 1){
    header("Location: index.php"); // Redirect to index.php
    exit(); // Ensure script execution stops after redirection
}
?>


