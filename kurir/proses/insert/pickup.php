<?php
include '../../../config/db.php'; // Load Koneksi DB SQL

/* Tampung Form Data [POST] */ 
    $pickup_date        = $_POST['pickup_date'];
    $kurir_id           = $_POST['kurir_id'];
    $resi_code          = generateAlphaNumericCode();
    $cs_name            = $_POST['cs_name'];
    $seller_phone_no    = '62'.$_POST['seller_phone_no'];
    // $price              = preg_replace("/[^0-9]/", "", $_POST['price']);
    // $shiping_cost       = preg_replace("/[^0-9]/", "", $_POST['shiping_cost']);
    $price              = $_POST['price'];
    $shiping_cost       = $_POST['shiping_cost'];
    $date_created       = date('Y-m-d H:i:s');
/* Tampung Form Data [POST] */

 

/* Infinite Generate Resi Code */ 
do {
    $resi_code  = generateAlphaNumericCode();
    $cek_duplikate_code = mysqli_query($con, "SELECT * FROM dlv_pickup WHERE resi_code='$resi_code' AND pickup_date >= DATE_SUB(CURDATE(), INTERVAL 3 DAY)");
} while (mysqli_num_rows($cek_duplikate_code) > 0);
/* Infinite Generate Resi Code */ 

$insert = mysqli_query($con, "INSERT INTO dlv_pickup (id, pickup_date, kurir_id, resi_code, cs_name, 
seller_phone_no, price, shiping_cost, status_pickup, date_created, date_modified) 
VALUES (NULL, '$pickup_date', '$kurir_id', '$resi_code', '$cs_name', 
'$seller_phone_no', '$price', '$shiping_cost', 'PROSES', '$date_created', NULL)");

if ($insert) {
    echo 'Y';
} else {
    echo 'N';
}