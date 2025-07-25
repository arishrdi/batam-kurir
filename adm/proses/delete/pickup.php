<?php
include '../../../config/db.php'; // Load Koneksi DB SQL
$filePath = "../../../theme/dist/img/pickup/"; // Lokasi File

$pickup_id  = $_POST['id'];
$cek_record = mysqli_query($con, "SELECT * FROM dlv_pickup WHERE id='$pickup_id'");
if (mysqli_num_rows($cek_record) > 0) {
    $row_record = mysqli_fetch_assoc($cek_record);
    $picture    = $row_record['picture'];
    $del_record = mysqli_query($con, "DELETE FROM dlv_pickup WHERE id=$pickup_id");
    if ($del_record) {
        if ($picture != '') { // Hapus Foto Yang Lalu
            $lokasi_file    = $filePath . $picture;
            unlink("$lokasi_file");
        } 
        echo 'Y';
    } else {
        echo 'N';
    }
}else{
    echo 'W';
}
?>