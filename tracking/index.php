<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Batam Kurir Delivery &nbsp;&mdash;&nbsp; Tracking Paket</title>
    <link rel="shortcut icon" href="../theme/dist/img/favicon.png" type="image/x-icon">

    <!-- Dependency Stylesheet -->
    <link rel="stylesheet" href="../theme/node_modules/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../theme/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Dependency Stylesheet -->

    <!-- Theme style -->
    <link rel="stylesheet" href="../theme/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../theme/dist/css/wizard.css">
    <!-- Theme style -->
    <?php
    
    // error_reporting(E_ALL);
    // ini_set('display_errors', 1);

    include '../config/db.php';
    $date       = ($_GET['date'] ?? "") != "" ? date('Y-m-d', strtotime($_GET['date'])) : '';
    $resi       = ($_GET['resi'] ?? "") != "" ? $_GET['resi'] : '';

    $query_data     = "SELECT
        dlv_pickup.id AS pickup_id,
        trx_delivery.id AS delivery_id,
        dlv_pickup.pickup_date,
        trx_delivery.delivery_date,
        dlv_pickup.kurir_id AS kurir_pick_up_id,
        mst_kurir.kurir_name AS kurir_pick_up,
        CASE 
            WHEN trx_delivery.kurir_id != '' THEN trx_delivery.kurir_id
            ELSE ''
        END AS kurir_delivery_id,
        CASE
            WHEN trx_delivery.kurir_id != '' THEN (SELECT kurir_name FROM mst_kurir WHERE id=trx_delivery.kurir_id)
            ELSE '-'
        END AS kurir_delivery,
        dlv_pickup.resi_code,
        dlv_pickup.cs_name,
        CONCAT('+', dlv_pickup.seller_phone_no) AS seller_phone_no,
        dlv_pickup.price,
        dlv_pickup.shiping_cost,
        dlv_pickup.status_pickup,
        trx_delivery.status_delivery
    FROM dlv_pickup 
        JOIN mst_kurir ON mst_kurir.id=dlv_pickup.kurir_id
        LEFT JOIN trx_delivery ON trx_delivery.pickup_id = dlv_pickup.id 
            AND trx_delivery.id = (SELECT MAX(id) FROM trx_delivery t2 WHERE t2.pickup_id = dlv_pickup.id)
    WHERE 1=1";

    // $cek_data = mysqli_query($con, "$query_data WHERE dlv_pickup.resi_code='$resi'");
    $cek_data = mysqli_query($con, "$query_data AND dlv_pickup.resi_code='$resi'");

    // $row_cek_data = mysqli_num_rows($cek_data);

    if (!empty($resi)) {
        $cek_pickup = mysqli_query($con, "SELECT * FROM dlv_pickup WHERE resi_code='$resi' AND pickup_date='$date'");
        if (mysqli_num_rows($cek_pickup) > 0) {
            $show           = 'Y';
            $row_pickup     = mysqli_fetch_assoc($cek_pickup);
            $status_pickup  = $row_pickup['status_pickup'];
            $pickup_id      = $row_pickup['id'];
            
            // Get detailed tracking data with kurir information
            $tracking_data = mysqli_query($con, "$query_data AND dlv_pickup.resi_code='$resi' AND dlv_pickup.pickup_date='$date'");
            $row_tracking = mysqli_fetch_assoc($tracking_data);
            
            $array_status_pickup    = array('PROSES', 'PENDING', 'SUKSES', 'CANCEL');
            $array_status_delivery  = array('PENDING', 'SUKSES', 'CANCEL');
            $array_status_done      = array('SUKSES', 'CANCEL');
        } else {
            $show   = 'N';
        }
    } else {
        $show = '';
    }
    ?>
</head>

<body class="hold-transition login-page pt-5 bg-white">
    <div class="tracking-box rounded-md border-0">
        <div class="card bg-white rounded-md border-0 elevation-0">
            <div class="row">
                <div class="col-md-6">
                    <div class="card-body login-card-body p-4 bg-white rounded-lg border border-secondary" style="margin-top: 100px">
                        <p class="login-box-msg fs-24 text-warning text-bold ls-0 pb-2 mb-0 ls4">Yuk! Cek Paket Anda</p>
                        <form method="GET" action="">
                            <div class="form-group mb-2">
                                <input type="date" name="date" max="<?= date('Y-m-d') ?>" value="<?= $date ?>" class="form-control form-control-sm ls3 fs-13 px-3 h-50 py-2 rounded-sm border border-secondary" autofocus required>
                            </div>
                            <div class="form-group mb-2">
                                <input type="text" name="resi" placeholder="Kode Resi Anda" value="<?= $resi ?>" class="form-control s3 fs-13 px-3 h-50 py-2 rounded-sm border border-secondary" autofocus required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-sm py-2 float-right text-semibold ls3 px-4 btn-warning rounded-sm border-0 fs-13 hover">Cek Sekarang</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-body login-card-body p-4 bg-white rounded-lg" style="vertical-align: middle;">
                        <img src="../theme/dist/img/bg_1.png" class="img-fluid" alt="">
                    </div>
                </div>
            </div>

            <!-- Progress Paket -->
            <?php if ($show == 'Y') { ?>
                <p class="fs-24 text-bold ls4 mb-0 mt-3 text-center">Status Paket Anda</p>
                <p class="fs-14 mb-3 ls3 text-center">Terimakasih telah menggunakan layanan Batam Kurir Delivery</p>
                <div class="f1-steps">
                    <div class="f1-progress">
                        <div class="f1-progress-line"></div>
                    </div>
                    <div class="f1-step text-left <?= (in_array($status_pickup, $array_status_pickup)) ? 'active' : '' ?>">
                        <div class="f1-step-icon"><i class="fa fa-circle"></i></div>
                        <p class="text-dark fs-13 pt-2 text-left">Proses<br>Penjemputan</p>
                    </div>
                    <div class="f1-step text-center <?= (in_array($status_pickup, $array_status_delivery)) ? 'active' : '' ?>">
                        <div class="f1-step-icon"><i class="fa fa-circle"></i></div>
                        <p class="text-dark fs-13 pt-2 text-center">Proses<br>Pengantaran</p>
                    </div>
                    <div class="f1-step text-right <?= (in_array($status_pickup, $array_status_done)) ? 'active' : '' ?>">
                        <div class="f1-step-icon"><i class="fa fa-circle"></i></div>
                        <p class="text-dark fs-13 pt-2 text-right">Selesai</p>
                    </div>


                    <table class="table table-sm table-borderless table-striped table-border-dark table-hover w-100 mb-0 pb-0">
                        <thead>
                            <tr class="bg-transparent bg-gray text-white lh-3 text-nowrap text-uppercase fs-12 ">
                                <th class="py-0 lh-2 text-center" style="vertical-align: middle !important;">Kurir Pick Up</th>
                                <th class="py-0 lh-2 text-center" style="vertical-align: middle !important;">Kurir Delivery</th>
                                <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kode Resi</th>
                                <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Nama CS</th>
                                <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Harga</th>
                                <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Ongkir</th>
                                <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="fs-13 text-dark hover-light text-nowrap">
                                <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_tracking['kurir_pick_up'] ?? '-' ?></td>
                                <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_tracking['kurir_delivery'] ?? '-' ?></td>
                                <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_pickup['resi_code'] ?></td>
                                <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_pickup['cs_name'] ?></td>
                                <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_pickup['price'] ?></td>
                                <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_pickup['shiping_cost'] ?></td>
                                <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_tracking['status_delivery'] ?? $row_pickup['status_pickup'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php } else if ($show == 'N') { ?>
                <p class="fs-24 text-bold ls4 mb-0 mt-3 text-center">Erorr 404 Not Found</p>
                <p class="fs-14 mb-3 ls3 text-center">Kode Resi yang anda masukkan tidak valid</p>
            <?php } ?>
            <!-- Progress Paket -->
        </div>
    </div>

    <!-- Js Dependency -->
    <script src="../theme/node_modules/jquery/dist/jquery.min.js"></script>
    <script src="../theme/node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="../theme/dist/js/adminlte.js"></script>
    <script src="main.js"></script>
    <!-- Js Dependency -->
</body>

</html>