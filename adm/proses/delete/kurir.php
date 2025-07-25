<?php
include '../../../config/db.php'; // Load Koneksi DB SQL

/* Tampung Form Data [POST] */ 
$id = $_POST['id'];

// Delete from mst_kurir and related mst_user record
$delete_kurir = mysqli_query($con, "DELETE FROM mst_kurir WHERE id='$id'");
$delete_user = mysqli_query($con, "DELETE FROM mst_user WHERE id=(SELECT user_id FROM mst_kurir WHERE id='$id')");

if ($delete_kurir) {
    echo 'Y';
} else {
    echo 'N';
}
?>