<?php
include '../config/db.php';    // Load Koneksi DB SQL
include '../config/local_date.php';    // Load Koneksi DB SQL
if (!empty($_COOKIE['BK-DELIVERY'])) {
    $sesion_admin   = json_decode($_COOKIE['BK-DELIVERY']);
    if ($sesion_admin->role_access == 'Kurir') {
        $user_id    = $sesion_admin->user_id;
        $data_user  = mysqli_fetch_assoc(mysqli_query($con, "SELECT mst_user.*, mst_role_access.role_access
        FROM mst_user 
        JOIN mst_role_access ON mst_role_access.id=mst_user.role_access_id
        WHERE mst_user.id=$user_id")); 

        $data_kurir = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM mst_kurir WHERE `user_id`='$user_id'"));
        $kurir_id   = $data_kurir['id'];
        $kode_kurir = getInitials($data_kurir['kurir_name']).$data_kurir['id'];
    }else{
        header("location:../login/");
    }
}else{
    header("location:../login/");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Batam Kurir Delivery &nbsp;&mdash;&nbsp; Kurir</title>
    <link rel="shortcut icon" href="../theme/dist/img/favicon.png" type="image/x-icon">
    
    <!-- Dependency Stylesheet -->
    <link rel="stylesheet" href="../theme/node_modules/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../theme/node_modules/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="../theme/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="../theme/node_modules/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../theme/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Dependency Stylesheet -->

    <!-- Theme style -->
    <link rel="stylesheet" href="../theme/dist/css/adminlte.min.css">
    <!-- Theme style -->
</head>