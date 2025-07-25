<?php
include '../../../config/db.php'; // Load Koneksi DB SQL

/* Tampung Form Data [POST] */ 
    $delivery_id        = $_POST['id'];
    $status_delivery    = $_POST['status_delivery'];
/* Tampung Form Data [POST] */ 
    $date_modified      = date('Y-m-d H:i:s');
    $date_now           = date('Y-m-d');
 

$cek_record = mysqli_query($con, "SELECT
trx_delivery.*,
dlv_pickup.seller_phone_no 
FROM
trx_delivery 
JOIN dlv_pickup ON dlv_pickup.id=trx_delivery.pickup_id
WHERE
trx_delivery.id=$delivery_id");
if (mysqli_num_rows($cek_record) > 0) {
    $data_record        = mysqli_fetch_assoc($cek_record);
    $pickup_id          = $data_record['pickup_id'];
    $seller_phone_no    = $data_record['seller_phone_no'];
    $cek_reward         = mysqli_query($con, "SELECT * FROM trx_reward WHERE seller_phone_no='$seller_phone_no'");
    $update_record = mysqli_query($con, "UPDATE trx_delivery SET date_modified='$date_modified', 
    delivery_finish_date='$date_now', status_delivery='$status_delivery' WHERE id='$delivery_id'");
    
    if ($update_record) {
        mysqli_query($con, "UPDATE dlv_pickup SET status_pickup='$status_delivery', date_modified='$date_modified' WHERE id='$pickup_id'");
        if (mysqli_num_rows($cek_reward) > 0) { // Update Reward
            mysqli_query($con, "UPDATE trx_reward SET counting=counting+1, milestone_date='$date_now' WHERE seller_phone_no='$seller_phone_no'");
        } else { // Insert Reward
            mysqli_query($con, "INSERT INTO trx_reward (id, seller_phone_no, status_claim, counting, milestone_date)
            VALUES (NULL, '$seller_phone_no', 'Pending', 1, '$date_now')");
        }
        echo 'Y';
    } else {
        echo 'N';
    }
}else{
    echo 'W3';
}
?>