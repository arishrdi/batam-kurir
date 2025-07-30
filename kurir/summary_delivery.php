<!-- Load Dependency CSS -->
<?php include 'theme/main_header.php'; ?>
<!-- Load Dependency CSS -->

<body class="hold-transition layout-top-nav bg-light-gray">
    <div class="wrapper">
        <!-- Load Nav Header -->
        <?php
        include 'theme/header.php';
        
        /* Query Data */ 
            $query_data     = "SELECT
                dlv_pickup.id AS pickup_id,
                trx_delivery.id AS delivery_id,
                dlv_pickup.pickup_date,
                trx_delivery.delivery_date,
                dlv_pickup.kurir_id AS kurir_pick_up_id,
                mst_kurir.kurir_name AS kurir_pick_up,
                CASE
                    WHEN trx_delivery.kurir_id != '' THEN (SELECT kurir_name FROM mst_kurir WHERE id=trx_delivery.kurir_id)
                    ELSE '-'
                END AS kurir_delivery,
                trx_delivery.kurir_id AS kurir_delivery_id,
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
            WHERE trx_delivery.kurir_id={$data_kurir['id']} AND 1=1";
        /* Query Data */ 

        /* Jika Pencarian Aktif */
            $date_from          = ($_GET['from'] ?? "") != "" ? date('Y-m-d', strtotime($_GET['from'])) : date('Y-m-d');
            $date_to            = ($_GET['to'] ?? "") != "" ? date('Y-m-d', strtotime($_GET['to'])) : date('Y-m-d');
            $date_now           = date('Y-m-d');
            $query_data         .= ($_GET['from'] ?? "") != "" && ($_GET['to'] ?? "") != "" ? " AND trx_delivery.delivery_date BETWEEN '$date_from' AND '$date_to'" : " AND trx_delivery.delivery_date='$date_now'";
        /* Jika Pencarian Aktif */
        
        /* Menampilkan Data */
            $sql_top            = mysqli_query($con, "$query_data AND trx_delivery.status_delivery='SUKSES' ORDER BY trx_delivery.id ASC");
            $all_data_top       = mysqli_num_rows($sql_top);
            $no_urut_top        = 1;
            $array_sumprice[]       = 0;
            $array_sumcost[]        = 0;
            $array_sumtotal_price[] = 0;
            foreach($sql_top as $row){
                $pricetop              = $row['price'];
                $shipingcost_top       = $row['shiping_cost'];
                $totalprice_top        = $row['price']+$row['shiping_cost'];

                $array_sumprice[]       = $pricetop;
                $array_sumcost[]        = $shipingcost_top;
                $array_sumtotal_price[] = $totalprice_top;
            }
            $sum_price          = (($all_data_top > 0) ? array_sum($array_sumprice) : 0);
            $sum_cost           = (($all_data_top > 0) ? array_sum($array_sumcost) : 0);
            $sum_price_cost     = (($all_data_top > 0) ? array_sum($array_sumtotal_price) : 0);

            $sql_pending        = mysqli_query($con, "$query_data AND trx_delivery.status_delivery='PENDING' ORDER BY trx_delivery.id ASC");
            $all_data_pending   = mysqli_num_rows($sql_pending);
            $no_urut_pending    = 1;
            
            $sql_cancel         = mysqli_query($con, "$query_data AND trx_delivery.status_delivery='CANCEL' ORDER BY trx_delivery.id ASC");
            $all_data_cancel    = mysqli_num_rows($sql_cancel);
            $no_urut_cancel     = 1;

            // Calculate pending and cancel price sums
            $pending_price_sum = 0;
            $cancel_price_sum = 0;
            if ($sql_pending && $all_data_pending > 0) {
                foreach($sql_pending as $row_pending) {
                    $pending_price_sum += $row_pending['price'];
                }
                mysqli_data_seek($sql_pending, 0);
            }
            if ($sql_cancel && $all_data_cancel > 0) {
                foreach($sql_cancel as $row_cancel) {
                    $cancel_price_sum += $row_cancel['price'];
                }
                mysqli_data_seek($sql_cancel, 0);
            }

            $query_no_delivery  = "SELECT
                dlv_pickup.id AS pickup_id,
                dlv_pickup.pickup_date,
                dlv_pickup.kurir_id AS kurir_pick_up_id,
                mst_kurir.kurir_name AS kurir_pick_up,
                dlv_pickup.resi_code,
                dlv_pickup.cs_name,
                dlv_pickup.price,
                dlv_pickup.shiping_cost
            FROM dlv_pickup 
                JOIN mst_kurir ON mst_kurir.id=dlv_pickup.kurir_id
                LEFT JOIN trx_delivery ON trx_delivery.pickup_id = dlv_pickup.id 
            WHERE dlv_pickup.kurir_id={$data_kurir['id']} 
                AND trx_delivery.id IS NULL
                AND dlv_pickup.pickup_date='$date_now'
            ORDER BY dlv_pickup.id ASC";
            
            $sql_no_delivery    = mysqli_query($con, $query_no_delivery);
            $all_data_no_delivery = mysqli_num_rows($sql_no_delivery);
            $no_urut_no_delivery = 1;

            // Initialize all summary variables to avoid undefined variable errors
            $jumlah_price_top = 0;
            $jumlah_cost_top = 0;
            $jumlah_price_pending = 0;
            $jumlah_cost_pending = 0;
            $jumlah_price_cancel = 0;
            $jumlah_cost_cancel = 0;
            $jumlah_price_no_delivery = 0;
            $jumlah_cost_no_delivery = 0;
        /* Menampilkan Data */
        ?>
        <!-- Load Nav Header  -->

        <div class="content-wrapper bg-transparent" style="margin-top: 100px;">
            <div class="content-header pb-0">
                <div class="container-fluid px-4">
                    <h1 class="m-0 fs-25 text-gray text-bold mb-3">
                        Summary Delivery <i class="fas fa-chevron-right text-md px-2"></i> <span class="fs-20">Data Anda</span>
                    </h1>
                </div>
                <div class="container-fluid px-2">
                    <div class="card-body py-0">
                        <form action="" class="table-responsive py-0">
                            <table class="table table-borderless py-0 my-0 px-0">
                                <tr class="lh-2">
                                    <td class="px-0 py-0">
                                        <input type="date" name="from" value="<?= $date_from ?>" max="<?= date('Y-m-d'); ?>" required class="form-control h-75 bg-transparent px-3 my-auto rounded-sm fs-12">
                                    </td>
                                    <td class="px-0 py-0">
                                        <div class="input-group-text border-0"><div class="btn btn-transparent btn-block border-0 rounded-0 py-0"><i class="fas fa-minus"></i></div></div>
                                    </td>
                                    <td class="px-0 py-0">
                                        <input type="date" name="to" value="<?= $date_to ?>" max="<?= date('Y-m-d'); ?>" required class="form-control h-75 bg-transparent px-3 my-auto rounded-sm fs-12">
                                    </td>
                                    <td class="px-2 py-0">
                                        <button type="submit" class="btn h-50 btn-warning btn-block border-0 rounded-sm hover"><i class="fas fa-search"></i></button>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    
                </div>
            </div>
            <!-- Main content -->
            <div class="content">
                <div class="container-fluid px-4">
                    <div class="card card-primary rounded-md" style="border-top: 4px solid #263D57;">
                        <div class="card-body pt-1 pb-1">
                            <h5 class="text-bold text-gray mt-2 text-uppercase">Hasil Delivery Hari Ini</h5>
                        </div>
                        <div class="card-body pt-1">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless table-striped table-border-dark table-hover w-100 mb-0 pb-0">
                                    <thead>
                                        <tr class="bg-transparent bg-gray text-white lh-3 text-nowrap text-uppercase fs-12 ">
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">No</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">ID</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kurir Pickup</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kurir Delivery</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kode Resi</th>   
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Nama CS</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Harga</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Ongkir</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $array_sum_price_top[]          = 0;
                                        $array_sum_cost_top[]           = 0;
                                        if ($all_data_top <= 0) {
                                            echo '<tr>
                                                <td colspan="9" class="text-center fs-12">Record Not Found</td>
                                            </tr>';
                                        } else {
                                            foreach ($sql_top as $row_top) {
                                                // $paket_status               = $row_top['paket_status'];
                                                $price_top                  = $row_top['price'];
                                                $shiping_cost_top           = $row_top['shiping_cost'];

                                                // if ($paket_status == 'FULL LUNAS') {
                                                //     $array_sum_price_top[]  = -$row_top['shiping_cost'];
                                                // }
                                                
                                                $array_sum_price_top[]      = $row_top['price'];
                                                $array_sum_cost_top[]       = $row_top['shiping_cost'];
                                                ?>
                                                <tr class="fs-13 text-dark hover-light">
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $no_urut_top++. '.'; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_top['pickup_id'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_top['kurir_pick_up'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_top['kurir_delivery'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_top['resi_code'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-uppercase"><?= $row_top['cs_name'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $price_top; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $shiping_cost_top; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap text-uppercase"><?= $row_top['status_delivery']; ?></td>
                                                </tr>
                                            <?php }
                                            $jumlah_price_top   = array_sum($array_sum_price_top);
                                            $jumlah_cost_top    = array_sum($array_sum_cost_top);
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-transparent text-white lh-3 text-nowrap text-uppercase fs-12">
                                            <th colspan="4" class="bg-gray text-right lh-3 py-2" style="vertical-align: middle !important;">TOTAL : </th>
                                            <th colspan="2" class="bg-gray text-right lh-3 py-2" style="vertical-align: middle !important;"></th>
                                            <th class="bg-gray text-center lh-3 py-2" style="vertical-align: middle !important;"><?= (($all_data_top > 0) ? $jumlah_price_top  : 0); ?></th>
                                            <th class="bg-gray text-center lh-3 py-2" style="vertical-align: middle !important;"><?= (($all_data_top > 0) ? $jumlah_cost_top   : 0); ?></th>
                                            <th class="bg-gray text-center lh-3 py-2" style="vertical-align: middle !important;"><?= (($all_data_top > 0) ? $jumlah_price_top - $jumlah_cost_top   : 0); ?></th>
                                        </tr>
                                         <tr class="bg-light text-bold">
                                            <td colspan="4" class="bg-gray text-center py-2 fs-13">PENDING</td>
                                            <td class="bg-gray text-center py-2 fs-13"><?= (($all_data_pending > 0) ? $jumlah_price_pending : 0) ?></td>
                                        </tr>
                                        <tr class="bg-light text-bold">
                                            <td colspan="4" class="bg-gray text-center py-2 fs-13">CANCEL</td>
                                            <td class="bg-gray text-center py-2 fs-13"><?= (($all_data_cancel > 0) ? $jumlah_price_cancel : 0) ?></td>
                                        </tr>
                                        <tr class="bg-light text-bold">
                                            <td colspan="4" class="bg-gray text-center py-2 fs-13">TOTAL DELIVERY</td>
                                            <td class="bg-gray text-center py-2 fs-13"><?= (($all_data_top > 0) ? $jumlah_price_top - (($all_data_pending > 0) ? $jumlah_price_pending : 0) - (($all_data_cancel > 0) ? $jumlah_price_cancel : 0) : 0) ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Pending Hari Ini -->
                    <div class="card card-primary rounded-md" style="border-top: 4px solid #263D57;">
                        <div class="card-body pt-1 pb-1">
                            <h5 class="text-bold text-gray mt-2 text-uppercase">Tabel Pending Hari Ini</h5>
                        </div>
                        <div class="card-body pt-1">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless table-striped table-border-dark table-hover w-100 mb-0 pb-0">
                                    <thead>
                                        <tr class="bg-transparent bg-gray text-white lh-3 text-nowrap text-uppercase fs-12 ">
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">No</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">ID</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kurir Pickup</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kurir Delivery</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kode Resi</th>   
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Nama CS</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Harga</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Ongkir</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $array_sum_price_pending[]   = 0;
                                        $array_sum_cost_pending[]    = 0;
                                        if ($all_data_pending <= 0) {
                                            echo '<tr>
                                                <td colspan="9" class="text-center fs-12">Record Not Found</td>
                                            </tr>';
                                        } else {
                                            foreach ($sql_pending as $row_pending) {
                                                $price_pending          = $row_pending['price'];
                                                $shiping_cost_pending   = $row_pending['shiping_cost'];
                                                
                                                $array_sum_price_pending[]  = $row_pending['price'];
                                                $array_sum_cost_pending[]   = $row_pending['shiping_cost'];
                                                ?>
                                                <tr class="fs-13 text-dark hover-light">
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $no_urut_pending++. '.'; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_pending['pickup_id'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_pending['kurir_pick_up'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_pending['kurir_delivery'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_pending['resi_code'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-uppercase"><?= $row_pending['cs_name'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $price_pending; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $shiping_cost_pending; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap text-uppercase"><?= $row_pending['status_delivery']; ?></td>
                                                </tr>
                                            <?php }
                                            $jumlah_price_pending   = array_sum($array_sum_price_pending);
                                            $jumlah_cost_pending    = array_sum($array_sum_cost_pending);
                                        }?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-transparent text-white lh-3 text-nowrap text-uppercase fs-12">
                                            <th colspan="5"></th>
                                            <th class="bg-gray text-right lh-3 py-2" style="vertical-align: middle !important;">TOTAL : </th>
                                            <th class="bg-gray text-center lh-3 py-2" style="vertical-align: middle !important;"><?= (($all_data_pending > 0) ? $jumlah_price_pending : 0); ?></th>
                                            <th class="bg-gray text-center lh-3 py-2" style="vertical-align: middle !important;"><?= (($all_data_pending > 0) ? $jumlah_cost_pending : 0); ?></th>
                                            <th class="bg-gray text-center lh-3 py-2" style="vertical-align: middle !important;"><?= (($all_data_pending > 0) ? $jumlah_price_pending - $jumlah_cost_pending : 0); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Cancel Hari Ini -->
                    <div class="card card-primary rounded-md" style="border-top: 4px solid #263D57;">
                        <div class="card-body pt-1 pb-1">
                            <h5 class="text-bold text-gray mt-2 text-uppercase">Tabel Cancel Hari Ini</h5>
                        </div>
                        <div class="card-body pt-1">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless table-striped table-border-dark table-hover w-100 mb-0 pb-0">
                                    <thead>
                                        <tr class="bg-transparent bg-gray text-white lh-3 text-nowrap text-uppercase fs-12 ">
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">No</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">ID</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kurir Pickup</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kurir Delivery</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kode Resi</th>   
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Nama CS</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Harga</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Ongkir</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $array_sum_price_cancel[]   = 0;
                                        $array_sum_cost_cancel[]    = 0;
                                        if ($all_data_cancel <= 0) {
                                            echo '<tr>
                                                <td colspan="9" class="text-center fs-12">Record Not Found</td>
                                            </tr>';
                                        } else {
                                            foreach ($sql_cancel as $row_cancel) {
                                                $price_cancel           = $row_cancel['price'];
                                                $shiping_cost_cancel    = $row_cancel['shiping_cost'];
                                                
                                                $array_sum_price_cancel[]   = $row_cancel['price'];
                                                $array_sum_cost_cancel[]    = $row_cancel['shiping_cost'];
                                                ?>
                                                <tr class="fs-13 text-dark hover-light">
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $no_urut_cancel++. '.'; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_cancel['pickup_id'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_cancel['kurir_pick_up'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_cancel['kurir_delivery'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_cancel['resi_code'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-uppercase"><?= $row_cancel['cs_name'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $price_cancel; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $shiping_cost_cancel; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap text-uppercase"><?= $row_cancel['status_delivery']; ?></td>
                                                </tr>
                                            <?php }
                                            $jumlah_price_cancel    = array_sum($array_sum_price_cancel);
                                            $jumlah_cost_cancel     = array_sum($array_sum_cost_cancel);
                                        }?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-transparent text-white lh-3 text-nowrap text-uppercase fs-12">
                                            <th colspan="5"></th>
                                            <th class="bg-gray text-right lh-3 py-2" style="vertical-align: middle !important;">TOTAL : </th>
                                            <th class="bg-gray text-center lh-3 py-2" style="vertical-align: middle !important;"><?= (($all_data_cancel > 0) ? $jumlah_price_cancel : 0); ?></th>
                                            <th class="bg-gray text-center lh-3 py-2" style="vertical-align: middle !important;"><?= (($all_data_cancel > 0) ? $jumlah_cost_cancel : 0); ?></th>
                                            <th class="bg-gray text-center lh-3 py-2" style="vertical-align: middle !important;"><?= (($all_data_cancel > 0) ? $jumlah_price_cancel - $jumlah_cost_cancel : 0); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Belum Input Kurir Delivery -->
                    <div class="card card-primary rounded-md" style="border-top: 4px solid #263D57;">
                        <div class="card-body pt-1 pb-1">
                            <h5 class="text-bold text-gray mt-2 text-uppercase">Tabel Belum Input Kurir Delivery</h5>
                        </div>
                        <div class="card-body pt-1">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless table-striped table-border-dark table-hover w-100 mb-0 pb-0">
                                    <thead>
                                        <tr class="bg-transparent bg-gray text-white lh-3 text-nowrap text-uppercase fs-12 ">
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">No</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">ID</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kurir Pickup</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kurir Delivery</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kode Resi</th>   
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Nama CS</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Harga</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Ongkir</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $array_sum_price_no_delivery[]  = 0;
                                        $array_sum_cost_no_delivery[]   = 0;
                                        if ($all_data_no_delivery <= 0) {
                                            echo '<tr>
                                                <td colspan="9" class="text-center fs-12">Record Not Found</td>
                                            </tr>';
                                        } else {
                                            foreach ($sql_no_delivery as $row_no_delivery) {
                                                $price_no_delivery          = $row_no_delivery['price'];
                                                $shiping_cost_no_delivery   = $row_no_delivery['shiping_cost'];
                                                
                                                $array_sum_price_no_delivery[]  = $row_no_delivery['price'];
                                                $array_sum_cost_no_delivery[]   = $row_no_delivery['shiping_cost'];
                                                ?>
                                                <tr class="fs-13 text-dark hover-light">
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $no_urut_no_delivery++. '.'; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_no_delivery['pickup_id'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_no_delivery['kurir_pick_up'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center">-</td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $row_no_delivery['resi_code'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-uppercase"><?= $row_no_delivery['cs_name'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $price_no_delivery; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $shiping_cost_no_delivery; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap">PROSES</td>
                                                </tr>
                                            <?php }
                                            $jumlah_price_no_delivery   = array_sum($array_sum_price_no_delivery);
                                            $jumlah_cost_no_delivery    = array_sum($array_sum_cost_no_delivery);
                                        }?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-transparent text-white lh-3 text-nowrap text-uppercase fs-12">
                                            <th colspan="5"></th>
                                            <th class="bg-gray text-right lh-3 py-2" style="vertical-align: middle !important;">TOTAL : </th>
                                            <th class="bg-gray text-center lh-3 py-2" style="vertical-align: middle !important;"><?= (($all_data_no_delivery > 0) ? $jumlah_price_no_delivery : 0); ?></th>
                                            <th class="bg-gray text-center lh-3 py-2" style="vertical-align: middle !important;"><?= (($all_data_no_delivery > 0) ? $jumlah_cost_no_delivery : 0); ?></th>
                                            <th class="bg-gray text-center lh-3 py-2" style="vertical-align: middle !important;"><?= (($all_data_no_delivery > 0) ? $jumlah_price_no_delivery - $jumlah_cost_no_delivery : 0); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main content -->
        </div>
    </div>
    <!-- Load Dependency JS -->
    <?php
    include 'theme/main_footer.php';
    include 'theme/alert.php';
    include 'theme/helper_search.php';
    ?>
    <!-- Load Dependency JS -->
</body>

</html>