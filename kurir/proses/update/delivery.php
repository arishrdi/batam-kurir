<?php
include '../../../config/db.php'; // Load Koneksi DB SQL

$delivery_id        = $_POST['id'];
$status_delivery    = $_POST['status_delivery'];
$status_pickup      = $status_delivery;

$row_record         = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM trx_delivery WHERE id=$delivery_id"));
// $picture_finish     = $row_record['picture_finish'];
$pickup_id          = $row_record['pickup_id'];

$date_modified      = date('Y-m-d H:i:s');
$update_record      = mysqli_query($con, "UPDATE trx_delivery SET date_modified='$date_modified', status_delivery='$status_delivery' WHERE id='$delivery_id'");
if ($update_record) {
    mysqli_query($con, "UPDATE dlv_pickup SET status_pickup='$status_pickup', date_modified='$date_modified' WHERE id='$pickup_id'");
    
    // $uploadPath     = "../../../theme/dist/img/deliv/"; // Lokasi Upload
    // if ($picture_finish != '') { // Hapus Foto Yang Lalu
    //     $lokasi_file    = $filePath . $picture_finish;
    //     unlink("$lokasi_file");
    // } 
    echo 'Y';
} else {
    echo 'N';
}
?>