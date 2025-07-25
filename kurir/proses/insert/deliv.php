<?php
include '../../../config/db.php'; // Load Koneksi DB SQL

/* Tampung Form Data [POST] */ 
    $delivery_date      = $_POST['delivery_date'];
    $kurir_id           = $_POST['kurir_id'];
    $pickup_id          = $_POST['pickup_id'];
    $status_delivery    = 'PROSES';
    $status_pickup      = 'PROSES';
    $date_created       = date('Y-m-d H:i:s');
    $date_now           = date('Y-m-d');
/* Tampung Form Data [POST] */

 


$insert = mysqli_query($con, "INSERT INTO trx_delivery (id, kurir_id, status_delivery, pickup_id, delivery_date, 
delivery_finish_date, date_created, date_modified) VALUES (NULL, '$kurir_id', '$status_delivery', '$pickup_id', '$delivery_date',
NULL, '$date_created', NULL)");

if ($insert) {
    mysqli_query($con, "UPDATE dlv_pickup SET status_pickup='$status_pickup', date_modified='$date_created' WHERE id='$pickup_id'");
    echo 'Y';
} else {
    echo 'N';
}
