<?php
function getUserTypeID() {
    if (isset($_SESSION['user_type_ID'])) {
        return $_SESSION['user_type_ID'];
    } else {
        return 0;
    }
}
?>