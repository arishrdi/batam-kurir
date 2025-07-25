<?php
include '../../../config/db.php'; // Load Koneksi DB SQL

/* Tampung Form Data [POST] */ 
    $kurir_id               = $_POST['id'];
    $phone_number           = '62'.$_POST['phone_number'];
    $status                 = $_POST['status'];
    $partner_name           = ($status == 'Menikah') ? $_POST['partner_name'] : '';
    $phone_number_partner   = ($status == 'Menikah') ? '62'.$_POST['phone_number_partner'] : '';
    $phone_number_family    = ($status == 'Menikah') ? '' : '62'.$_POST['phone_number_family'];
    $password               = $_POST['password'];
    $date_modified          = date('Y-m-d H:i:s');
/* Tampung Form Data [POST] */

$cek_record     = mysqli_query($con, "SELECT * FROM mst_kurir WHERE id='$kurir_id'");
if (mysqli_num_rows($cek_record) > 0) {
    $val_record = mysqli_fetch_assoc($cek_record);
    $user_id    = $val_record['user_id'];
    $phone_lama = $val_record['phone_number'];

    if ($phone_lama == $phone_number) { // Update Kurir
        $update = mysqli_query($con, "UPDATE mst_kurir SET `status`='$status', 
        partner_name='$partner_name', phone_number_partner='$phone_number_partner', phone_number_family='$phone_number_family', date_modified='$date_modified' WHERE id='$kurir_id'");
        if ($update) {
            mysqli_query($con, "UPDATE mst_user SET `password`='$password', date_modified='$date_modified' WHERE id='$user_id'");
            echo '<script>document.location="../../page_kurir.php?alert=Y";</script>';
        } else {
            echo '<script>document.location="../../page_kurir.php?alert=N";</script>';
        }
    }else{ // Update Kurir & User
        $cek_username   = mysqli_query($con, "SELECT * FROM mst_user WHERE username='$phone_number'");
        if (mysqli_num_rows($cek_username) > 1) {
            echo '<script>document.location="../../page_kurir.php?alert=W2";</script>';
        }else{
            $update     = mysqli_query($con, "UPDATE mst_kurir SET phone_number='$phone_number', `status`='$status', 
            partner_name='$partner_name', phone_number_partner='$phone_number_partner', phone_number_family='$phone_number_family', date_modified='$date_modified' WHERE id='$kurir_id'");
            if ($update) {
                mysqli_query($con, "UPDATE mst_user SET username='$phone_number', `password`='$password', date_modified='$date_modified' WHERE id='$user_id'");
                echo '<script>document.location="../../page_kurir.php?alert=Y";</script>';
            } else {
                echo '<script>document.location="../../page_kurir.php?alert=N";</script>';
            }
        }
    }
}else{
    echo '<script>document.location="../../page_kurir.php?alert=W1";</script>';
}