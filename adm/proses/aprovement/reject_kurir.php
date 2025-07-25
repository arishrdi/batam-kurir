<?php
include '../../../config/db.php'; // Load Koneksi DB SQL
$filePath = "../../../theme/dist/img/kurir/"; // Lokasi File

$delete_id          = $_POST['id'];
$cek_record         = mysqli_query($con, "SELECT * FROM mst_kurir WHERE id='$delete_id'");
if (mysqli_num_rows($cek_record) > 0) {
    $row_record     = mysqli_fetch_assoc($cek_record);
    $user_id        = $row_record['user_id'];
    $foto_profile   = $row_record['profile_pic'];

    if ($foto_profile != '') { // Hapus Foto Yang Lalu
        $lokasi_file    = $filePath . $foto_profile;
        unlink("$lokasi_file");
    } 
    $del_record     = mysqli_query($con, "DELETE FROM mst_kurir WHERE id='$delete_id'");
    if ($del_record) {
        mysqli_query($con, "DELETE FROM mst_user WHERE id='$user_id'");
        echo 'Y';
    } else {
        echo 'N';
    }
}else{
    echo 'W';
}
?>