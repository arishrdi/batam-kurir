<?php
include '../../../config/db.php'; // Load Koneksi DB SQL

$delivery_id        = $_POST['id'];
$status_delivery    = $_POST['status_delivery'];
$status_pickup      = $status_delivery;

$cek_record = mysqli_query($con, "SELECT
trx_delivery.*,
dlv_pickup.seller_phone_no 
FROM
trx_delivery 
JOIN dlv_pickup ON dlv_pickup.id=trx_delivery.pickup_id
WHERE
trx_delivery.id=$delivery_id");
$row_record         = mysqli_fetch_assoc($cek_record);
$pickup_id          = $row_record['pickup_id'];
$status_lama        = $row_record['status_delivery'];
$seller_phone_no    = $row_record['seller_phone_no'];
$date_modified      = date('Y-m-d H:i:s');
$milestone_date     = date('Y-m-d');

$update_record      = mysqli_query($con, "UPDATE trx_delivery SET date_modified='$date_modified', status_delivery='$status_delivery' WHERE id='$delivery_id'");
if ($update_record) {
    if ($status_lama == 'SUKSES') {
        mysqli_query($con, "UPDATE dlv_pickup SET status_pickup='$status_pickup', date_modified='$date_modified' WHERE id='$pickup_id'");
        mysqli_query($con, "UPDATE trx_reward SET counting=counting-1, milestone_date='$milestone_date' WHERE seller_phone_no='$seller_phone_no'");
    } else {
        mysqli_query($con, "UPDATE dlv_pickup SET status_pickup='$status_pickup', date_modified='$date_modified' WHERE id='$pickup_id'");
    }
    echo 'Y';
} else {
    echo 'N';
}
?>