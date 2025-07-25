<?php
include '../../../config/db.php'; // Load Koneksi DB SQL

$kurir_id   = $_POST['id'];
$cek_record = mysqli_query($con, "SELECT * FROM mst_kurir WHERE id='$kurir_id'");
if (mysqli_num_rows($cek_record) > 0) {
    $val_record     = mysqli_fetch_assoc($cek_record);
    $user_id        = $val_record['user_id'];
    $is_active      = ($val_record['is_active'] == 1) ? 0 : 1;
    $date_modified  = date('Y-m-d H:i:s');

    $del_record     = mysqli_query($con, "UPDATE mst_kurir SET is_active=$is_active, date_modified='$date_modified' WHERE id='$kurir_id'");
    if ($del_record) {
        mysqli_query($con, "UPDATE mst_user SET is_active=$is_active, date_modified='$date_modified' WHERE id='$user_id'");
        echo 'Y';
    } else {
        echo 'N';
    }
}else{
    echo 'W';
}