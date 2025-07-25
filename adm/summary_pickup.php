<!-- Load Dependency CSS -->
<?php include 'theme/main_header.php'; ?>
<!-- Load Dependency CSS -->

<body class="hold-transition layout-top-nav bg-light-gray">
    <div class="wrapper">
        <!-- Load Nav Header -->
        <?php
        include 'theme/header.php';
        /* Select Kurir */
            $query_kurir    = mysqli_query($con, "SELECT * FROM mst_kurir WHERE is_validate=1 ORDER BY kurir_name ASC");
        /* Select Kurir */ 

        /* Query Data */ 
            $query_data = "SELECT
                dlv_pickup.id,
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
                dlv_pickup.status_pickup
            FROM dlv_pickup 
                JOIN mst_kurir ON mst_kurir.id=dlv_pickup.kurir_id
                LEFT JOIN trx_delivery ON trx_delivery.pickup_id = dlv_pickup.id 
            WHERE 1=1 ";
        /* Query Data */ 

        /* Jika Pencarian Aktif */
            if ($_GET['status'] ?? "" != "") {
                $status     = $_GET['status'];
                $query_data = $query_data . " AND dlv_pickup.status_pickup='$status'";
            } else {
                $status     = '';
            }

            if ($_GET['kurir'] ?? "" != "") {
                $kurir_id   = $_GET['kurir'];
                $query_data = $query_data . " AND dlv_pickup.kurir_id='$kurir_id'";
            } else {
                $kurir_id   = '';
            }
            
            $date_from      = ($_GET['from'] ?? "") != "" ? date('Y-m-d', strtotime($_GET['from'])) : date('Y-m-d');
            $date_to        = ($_GET['to'] ?? "") != "" ? date('Y-m-d', strtotime($_GET['to'])) : date('Y-m-d');
            $date_now       = date('Y-m-d');
            $query_data     .= ($_GET['from'] ?? "") != "" && ($_GET['to'] ?? "") != "" ? " AND dlv_pickup.pickup_date BETWEEN '$date_from' AND '$date_to'" : " AND dlv_pickup.pickup_date='$date_now'";
        /* Jika Pencarian Aktif */
        
        /* Menampilkan Data */ 
            $sql_data       = mysqli_query($con, "$query_data ORDER BY dlv_pickup.id ASC");
            $all_data       = mysqli_num_rows($sql_data);
            $no_urut        = 1;
        /* Menampilkan Data */ 
            foreach($sql_data as $row){
                $array_sumprice[]       = $row['price'];
                $array_sumcost[]        = $row['shiping_cost'];
                $totalprice             = $row['price']+$row['shiping_cost'];

                $array_sumtotal_price[] = $totalprice;
            }
            $sum_price      = (($all_data > 0) ? array_sum($array_sumprice) : 0);
            $sum_cost       = (($all_data > 0) ? array_sum($array_sumcost) : 0);
            $sum_price_cost = (($all_data > 0) ? array_sum($array_sumtotal_price) : 0);
        ?>
        <!-- Load Nav Header  -->

        <div class="content-wrapper bg-transparent" style="margin-top: 100px;">
            <div class="content-header pb-0">
                <div class="container-fluid px-4">
                    <h1 class="m-0 fs-25 text-gray text-bold mb-3">Master Data <i class="fas fa-chevron-right text-md px-2"></i> <span class="fs-20">Summary Pick Up</span></h1>
                </div>
                
                <div class="container-fluid px-1">
                    <div class="card-body py-0">
                        <form action="" class="table-responsive py-0">
                            <table class="table table-borderless py-0 my-0 px-0">                                
                                <tr class="lh-2">
                                    <td width="31%" class="px-0 py-0">
                                        <div class="input-group mb-2">
                                            <div class="input-group-text border-0 fs-13 px-0 pr-3">Nama Kurir : </div>
                                            <select id="kurir" class="form-control rounded-sm select2bs4" onchange="changekurir(this)" >
                                                <option <?= $kurir_id == '' ? 'selected' : ''; ?> value="">ALL KURIR</option>
                                                <?php foreach ($query_kurir as $val_kurir) { ?>
                                                    <option <?= $kurir_id == $val_kurir['id'] ? 'selected' : ''; ?> value="<?= $val_kurir['id'] ?>"><?= strtoupper($val_kurir['kurir_name']) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td colspan="3"></td>
                                    <td class="px-0 py-0">
                                        <a href="export/summary_pickup.php?from=<?= $date_from ?>&to=<?= $date_to ?>&kurir=<?= $kurir_id ?>&status=<?= $status ?>" class="btn btn-sm btn-block btn-success border-0 rounded-sm px-4 mb-0 hover float-right"><i class="fas fa-file-excel pr-2"></i>Export</a>
                                    </td>
                                </tr>
                                <tr class="lh-2">
                                    <td width="31%" class="px-0 py-0">
                                        <div class="input-group mb-2">
                                            <div class="input-group-text border-0 fs-13 px-0 pr-3">Status Paket : </div>
                                            <select id="status" class="form-control rounded-sm select2bs4" onchange="changestatus(this)">
                                                <option <?= $status == '' ? 'selected' : ''; ?> value="">ALL STATUS</option>
                                                <option <?= $status == 'PROSES' ? 'selected' : ''; ?> value="PROSES">PROSES</option>
                                                <option <?= $status == 'PENDING' ? 'selected' : ''; ?> value="PENDING">PENDING</option>
                                                <option <?= $status == 'SUKSES' ? 'selected' : ''; ?> value="SUKSES">SUKSES</option>
                                                <option <?= $status == 'CANCEL' ? 'selected' : ''; ?> value="CANCEL">CANCEL</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td></td>
                                    <td width="12%" class="px-0 py-0">
                                        <input type="date" name="from" value="<?= $date_from ?>" max="<?= date('Y-m-d'); ?>" required class="form-control h-75 bg-transparent px-3 my-auto rounded-sm fs-12">
                                    </td>
                                    <td width="4%" class="px-0 py-0">
                                        <div class="input-group-text border-0"><div class="btn btn-transparent btn-block border-0 rounded-0 py-0"><i class="fas fa-minus"></i></div></div>
                                    </td>
                                    <td width="12%" class="px-0 py-0">
                                        <input type="date" name="to" value="<?= $date_to ?>" max="<?= date('Y-m-d'); ?>" required class="form-control h-75 bg-transparent px-3 my-auto rounded-sm fs-12">
                                    </td>
                                    <td width="5%" class="px-2 py-0">
                                        <button onclick="changePeriode(document.querySelector('input[name=from]').value, document.querySelector('input[name=to]').value)" type="button" class="btn h-50 btn-warning btn-block border-0 rounded-sm hover"><i class="fas fa-search"></i></button>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    
                    <div class="row mt-3 mb-0">
                        <div class="col-lg-4 px-4">
                            <div class="small-box bg-success rounded-sm shadow-sm">
                                <div class="inner p-4 text-center">
                                    <h3 class="text-white mb-0 lh-4 py-3"><?= number_format($sum_price, 0, ',', '.'); ?></h3>
                                    <p class="my-0 text-white text-semibold fs-14">Harga Paket</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 px-4">
                            <div class="small-box bg-orange rounded-sm shadow-sm">
                                <div class="inner p-4 text-center">
                                    <h3 class="text-white mb-0 lh-4 py-3"><?= number_format($sum_cost, 0, ',', '.'); ?></h3>
                                    <p class="my-0 text-white text-semibold fs-14">Ongkir</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 px-4">
                            <div class="small-box bg-primary rounded-sm shadow-sm">
                                <div class="inner p-4 text-center">
                                    <h3 class="text-white mb-0 lh-4 py-3"><?= number_format($sum_price_cost, 0, ',', '.'); ?></h3>
                                    <p class="my-0 text-white text-semibold fs-14">Total</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main content -->

            <div class="content">
                <div class="container-fluid px-4">
                    <div class="card card-primary rounded-md" style="border-top: 4px solid #263D57;">
                        <div class="card-body pt-1 pb-1">
                            <h5 class="text-bold text-gray mt-2 text-uppercase float-left">Hasil Pick Up Hari Ini</h5>
                        </div>
                        <div class="card-body pt-1">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless table-striped table-border-dark table-hover w-100 mb-0 pb-0">
                                    <thead>
                                        <tr class="bg-transparent bg-gray text-white lh-3 text-nowrap text-uppercase fs-12">
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">No</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kurir Pick Up</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kurir Delivery</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kode Resi</th>   
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Nama CS</th>
                                            <th class="py-0 lh-5 text-left" style="vertical-align: middle !important;">No Hp Seller</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Harga</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Ongkir</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Total</th>
                                            <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($all_data <= 0) {
                                            echo '<tr>
                                                <td colspan="10" class="text-center fs-12">Record Not Found</td>
                                            </tr>';
                                        } else {
                                            foreach ($sql_data as $rows) {
                                                $price                  = $rows['price'];
                                                $shiping_cost           = $rows['shiping_cost'];
                                                $total_price            = $rows['price']+$rows['shiping_cost'];
                                                $array_sum_price[]      = $rows['price'];

                                                $array_sum_cost[]       = $rows['shiping_cost'];
                                                $array_sum_total_price[]= $total_price;
                                                ?>
                                                <tr class="fs-13 text-dark hover-light">
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $no_urut++. '.'; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $rows['kurir_pick_up'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $rows['kurir_delivery'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $rows['resi_code'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-uppercase"><?= $rows['cs_name'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-left"><a href="https://wa.me/<?= $rows['seller_phone_no'] ?>" target="_blank" rel="noopener noreferrer"><?= $rows['seller_phone_no'] ?></a></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $price ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $shiping_cost; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $total_price; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap text-uppercase"><?= $rows['status_pickup']; ?></td>
                                                </tr>
                                            <?php }
                                            $jumlah_price   = ($array_sum_price == '') ? 0 : array_sum($array_sum_price);
                                            $jumlah_cost    = ($array_sum_cost == '') ? 0 : array_sum($array_sum_cost);
                                            $jumlah_total   = ($array_sum_total_price == '') ? 0 : array_sum($array_sum_total_price);
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-transparent text-white lh-3 text-nowrap text-uppercase fs-12">
                                            <th colspan="4"></th>
                                            <th class="bg-gray text-right lh-3 py-2" style="vertical-align: middle !important;">JUMLAH : </th>
                                            <th class="bg-gray text-center lh-3 py-2" style="vertical-align: middle !important;"><?= (($all_data > 0) ? $jumlah_price  : 0); ?></th>
                                            <th class="bg-gray text-center lh-3 py-2" style="vertical-align: middle !important;"><?= (($all_data > 0) ? $jumlah_cost   : 0); ?></th>
                                            <th class="bg-gray text-center lh-3 py-2" style="vertical-align: middle !important;"><?= (($all_data > 0) ? $jumlah_total  : 0); ?></th>
                                            <th></th>
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