<?php
include '../../../config/db.php'; // Load Koneksi DB SQL

/* Tampung Form Data [POST] */ 
    $user_id        = $_POST['id'];
    $username       = $_POST['username'];
    $password       = $_POST['password'];
    $date_modified  = date('Y-m-d H:i:s');
/* Tampung Form Data [POST] */

$update     = mysqli_query($con, "UPDATE mst_user SET `password`='$password', `username`='$username', date_modified='$date_modified' WHERE id='$user_id'");
if ($update) {
    echo '<script>document.location="../../user_setting.php?alert=Y";</script>';
} else {
    echo '<script>document.location="../../user_setting.php?alert=N";</script>';
}