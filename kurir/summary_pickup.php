<!-- Load Dependency CSS -->
<?php include 'theme/main_header.php'; ?>
<!-- Load Dependency CSS -->

<body class="hold-transition layout-top-nav bg-light-gray">
    <div class="wrapper">
        <!-- Load Nav Header -->
        <?php
        include 'theme/header.php';
        // Query Data
        $query_data = "SELECT 
            pickup_with_sequence.pickup_id,
            pickup_with_sequence.pickup_date,
            pickup_with_sequence.kurir_pick_up,
            pickup_with_sequence.kurir_delivery,
            pickup_with_sequence.resi_code,
            pickup_with_sequence.cs_name,
            pickup_with_sequence.seller_phone_no,
            pickup_with_sequence.price,
            pickup_with_sequence.shiping_cost,
            pickup_with_sequence.status_pickup,
            pickup_with_sequence.daily_sequence_id
        FROM (
            SELECT
                dlv_pickup.id AS pickup_id,
                dlv_pickup.pickup_date,
                mst_kurir.kurir_name AS kurir_pick_up,
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
                dlv_pickup.kurir_id,
                ROW_NUMBER() OVER (PARTITION BY dlv_pickup.pickup_date, dlv_pickup.kurir_id ORDER BY dlv_pickup.id ASC) AS daily_sequence_id
            FROM dlv_pickup 
                JOIN mst_kurir ON mst_kurir.id=dlv_pickup.kurir_id
                LEFT JOIN trx_delivery ON trx_delivery.pickup_id = dlv_pickup.id 
                    AND trx_delivery.id = (SELECT MAX(id) FROM trx_delivery t2 WHERE t2.pickup_id = dlv_pickup.id) 
        ) AS pickup_with_sequence
        WHERE pickup_with_sequence.kurir_id=$kurir_id AND 1=1";

        /* Jika Pencarian Aktif */
            $date_from      = ($_GET['from'] ?? "") != "" ? date('Y-m-d', strtotime($_GET['from'])) : date('Y-m-d');
            $date_to        = ($_GET['to'] ?? "") != "" ? date('Y-m-d', strtotime($_GET['to'])) : date('Y-m-d');
            $date_now       = date('Y-m-d');
            $query_data     .= ($_GET['from'] ?? "") != "" && ($_GET['to'] ?? "") != "" ? " AND pickup_with_sequence.pickup_date BETWEEN '$date_from' AND '$date_to'" : " AND pickup_with_sequence.pickup_date='$date_now'";
        /* Jika Pencarian Aktif */
        
        // Menampilkan Data
        $query_all_data = mysqli_query($con, $query_data);
        $all_data       = mysqli_num_rows($query_all_data);
        $sql_data       = mysqli_query($con, "$query_data ORDER BY pickup_with_sequence.kurir_pick_up ASC, pickup_with_sequence.pickup_id ASC");
        $no_urut        = 1;
        ?>
        <!-- Load Nav Header  -->

        <div class="content-wrapper bg-transparent" style="margin-top: 100px;">
            <div class="content-header pb-0">
                <div class="container-fluid px-1">
                    <div class="card-body py-1">
                        <div class="float-right">
                            <form action="" class="py-0">
                                <div class="form-inline">
                                    <div class="form-group">
                                        <div class="input-group">
                                        <input type="date" name="from" value="<?= $date_from ?>" max="<?= date('Y-m-d'); ?>" required class="form-control h-75 bg-transparent px-3 my-auto rounded-sm fs-12">
                                        <div class="input-group-text border-0"><div class="btn btn-transparent btn-block border-0 rounded-0 py-0"><i class="fas fa-minus"></i></div></div>
                                        <input type="date" name="to" value="<?= $date_to ?>" max="<?= date('Y-m-d'); ?>" required class="form-control h-75 bg-transparent px-3 my-auto rounded-sm fs-12">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn h-50 btn-warning btn-block border-0 rounded-sm hover ml-2"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid px-4 pt-2">
                    <div class="card card-primary rounded-md" style="border-top: 4px solid #263D57;">
                        <div class="card-body pt-1 pb-1">
                            <h5 class="text-bold text-gray mt-2 text-uppercase">Hasil Pick Up Anda Hari Ini</h5>
                        </div>
                        <div class="card-body pt-1">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless table-striped table-border-dark table-hover w-100 mb-0 pb-0">
                                    <thead>
                                        <tr class="bg-transparent bg-gray text-white lh-3 text-nowrap text-uppercase fs-12">
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">No</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">ID</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kurir Pick Up</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kurir Delivery</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kode Resi</th>   
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Nama CS</th>
                                            <th class="py-0 lh-5 text-left" style="vertical-align: middle !important;">No Hp Seller</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Harga</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Ongkir</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($all_data <= 0) {
                                            echo '<tr>
                                                <td colspan="11" class="text-center fs-12">Record Not Found</td>
                                            </tr>';
                                        } else {
                                            foreach ($sql_data as $rows) {
                                                $price                  = $rows['price'];
                                                $shiping_cost           = $rows['shiping_cost'];
                                                $total_price            = $rows['price']+$rows['shiping_cost'];

                                                $array_sum_price[]      = $rows['price'];

                                                // if (in_array($paket_status, array('FULL LUNAS'))) {
                                                //     $array_sum_price[]  = -$rows['shiping_cost'];
                                                // }

                                                $array_sum_cost[]       = $rows['shiping_cost'];
                                                $array_sum_total_price[]= $total_price;
                                                ?>
                                                <tr class="fs-13 text-dark hover-light">
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $no_urut++. '.'; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><strong><?= $rows['daily_sequence_id'] ?></strong></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $rows['kurir_pick_up'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $rows['kurir_delivery'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $rows['resi_code'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-uppercase"><?= $rows['cs_name'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-left"><a href="https://wa.me/<?= $rows['seller_phone_no'] ?>" target="_blank" rel="noopener noreferrer"><?= $rows['seller_phone_no'] ?></a></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $price; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $shiping_cost; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap text-uppercase"><?= $rows['status_pickup']; ?></td>
                                                </tr>
                                            <?php }
                                            $jumlah_price   = array_sum($array_sum_price);
                                            $jumlah_cost    = array_sum($array_sum_cost);
                                            $jumlah_total   = array_sum($array_sum_total_price);
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-transparent text-white lh-3 text-nowrap text-uppercase fs-12">
                                            <th colspan="6"></th>
                                            <th class="bg-gray text-right lh-3 py-2" style="vertical-align: middle !important;">TOTAl : </th>
                                            <th class="bg-gray text-center lh-3 py-2" style="vertical-align: middle !important;"><?= (($all_data > 0) ? $jumlah_price : 0); ?></th>
                                            <th class="bg-gray text-center lh-3 py-2" style="vertical-align: middle !important;"><?= (($all_data > 0) ? $jumlah_cost : 0); ?></th>
                                            <th class="bg-gray text-center lh-3 py-2" style="vertical-align: middle !important;"><?= (($all_data > 0) ? $jumlah_price - $jumlah_cost : 0); ?></th>
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