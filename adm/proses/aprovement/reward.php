<?php
include '../../../config/db.php'; // Load Koneksi DB SQL

$reward_id  = $_POST['id'];
$cek_record = mysqli_query($con, "SELECT * FROM trx_reward WHERE id='$reward_id'");
if (mysqli_num_rows($cek_record) > 0) {
    $update = mysqli_query($con, "UPDATE trx_reward SET status_claim='Claim', counting=0 WHERE id='$reward_id'");
    if ($update) {
        echo 'Y';
    } else {
        echo 'N';
    }
}else{
    echo 'W';
}