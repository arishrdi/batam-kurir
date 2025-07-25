<?php
include '../../../config/db.php'; // Load Koneksi DB SQL

/* Tampung Form Data [POST] */ 
    $poin_reward = $_POST['poin_reward'];
/* Tampung Form Data [POST] */

$update = mysqli_query($con, "UPDATE mst_config SET poin_reward='$poin_reward'");
if ($update) {
    mysqli_query($con, "UPDATE trx_reward SET status_claim='Pending'");
    echo 'Y';
} else {
    echo 'N';
}
?>