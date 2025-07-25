<?php

include '../../../config/db.php'; // Load Koneksi DB SQL

/* Tampung Variable [POST] */ 
    $kurir_name             = $_POST['kurir_name'];
    $birdth_place           = $_POST['birdth_place'];
    $birdth_date            = $_POST['birdth_date'];
    $batam_address          = $_POST['batam_address'];
    $phone_number           = '62'.$_POST['phone_number'];
    $status                 = $_POST['status'];
    $partner_name           = ($status == 'Menikah') ? $_POST['partner_name'] : '';
    $phone_number_partner   = ($status == 'Menikah') ? '62'.$_POST['phone_number_partner'] : '';
    $phone_number_family    = ($status == 'Menikah') ? '' : '62'.$_POST['phone_number_family'];
    $date_created           = date('Y-m-d H:i:s');
/* Tampung Variable [POST] */ 

$cek_no_hp  = mysqli_query($con, "SELECT * FROM mst_kurir WHERE phone_number='$phone_number'");
if (mysqli_num_rows($cek_no_hp) > 0) { // No Hp Duplicate
    echo 'W3';
}else{
    $insert_user    = mysqli_query($con, "INSERT INTO mst_user (id, username, `password`, full_name, role_access_id, date_created, date_modified, is_active)
    VALUES(NULL, '$phone_number', '$birdth_date', '$kurir_name', 3, '$date_created', NULL, 1)");
    $user_id        = mysqli_insert_id($con);            
    $insert         = mysqli_query($con, "INSERT INTO mst_kurir (id, kurir_name, birdth_place, birdth_date, batam_address, phone_number,
    `status`, partner_name, phone_number_partner, phone_number_family, is_active, is_validate, `user_id`, date_created, date_modified) VALUES
    (NULL, '$kurir_name', '$birdth_place', '$birdth_date', '$batam_address', '$phone_number', 
    '$status', '$partner_name', '$phone_number_partner', '$phone_number_family', 1, 1, '$user_id', '$date_created', NULL)");
    if ($insert) {
        echo 'Y';
    }else{
        echo 'N';
    }
}