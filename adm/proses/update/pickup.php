<?php
include '../../../config/db.php'; // Load Koneksi DB SQL

/* Tampung Form Data [POST] */ 
    $pickup_id          = $_POST['pickup_id'];
    $kurir_id          = $_POST['kurir_id'];
    $cs_name            = $_POST['cs_name'];
    $seller_phone_no    = '62'.$_POST['seller_phone_no'];
    // $price              = preg_replace("/[^0-9]/", "", $_POST['price']);
    // $shiping_cost       = preg_replace("/[^0-9]/", "", $_POST['shiping_cost']);
    $price              = $_POST['price'];
    $shiping_cost       = $_POST['shiping_cost'];
    $date_modified      = date('Y-m-d H:i:s');
/* Tampung Form Data [POST] */ 

$update = mysqli_query($con, "UPDATE dlv_pickup SET cs_name='$cs_name', kurir_id='$kurir_id', 
seller_phone_no='$seller_phone_no', price='$price', shiping_cost='$shiping_cost', date_modified='$date_modified' WHERE id='$pickup_id'");
if ($update) {
    echo'Y';
} else {
    echo'N';
}