<!-- Load Dependency CSS -->
<?php include 'theme/main_header.php'; ?>
<!-- Load Dependency CSS -->

<body class="hold-transition layout-top-nav bg-light-gray">
    <div class="wrapper">
        <!-- Load Nav Header -->
        <?php
        include 'theme/header.php';
        // Select Option Pick Up
        $query_pickup   = mysqli_query($con, "SELECT * FROM dlv_pickup WHERE status_pickup='Proses'");

        $seller_phone   = ($_GET['seller'] ?? "") != "" ? $_GET['seller'] : '';
        // Set Pagination
        $batas          = isset($_GET['batas']) ? (int) $_GET['batas'] : 10;
        $hal            = isset($_GET['hal']) ? (int) $_GET['hal'] : 1;
        $hal_awal       = ($hal > 1) ? ($hal * $batas) - $batas : 0;
        // Set Butoon
        $previous       = $hal - 1;
        $next           = $hal + 1;
        // Query Data - Show latest delivery attempt per pickup
        $query_data     = "SELECT
            trx_delivery.*,
            dlv_pickup.resi_code,
            dlv_pickup.cs_name,
            dlv_pickup.seller_phone_no,
            dlv_pickup.price,
            dlv_pickup.shiping_cost,
            mst_kurir.kurir_name
        FROM
            trx_delivery 
            JOIN dlv_pickup ON dlv_pickup.id=trx_delivery.pickup_id 
            JOIN mst_kurir ON mst_kurir.id=trx_delivery.kurir_id
        WHERE dlv_pickup.seller_phone_no='$seller_phone' 
            AND trx_delivery.status_delivery='SUKSES' 
            AND trx_delivery.id = (SELECT MAX(id) FROM trx_delivery t2 WHERE t2.pickup_id = dlv_pickup.id)
            AND 1=1";
        /* Jika Pencarian Aktif */
            if ($_GET['cari'] ?? "" != "") {
                $pencarian  = $_GET['cari'];
                $query_data = $query_data . " AND dlv_pickup.resi_code LIKE '%$pencarian%'
                OR dlv_pickup.cs_name LIKE '%$pencarian%'
                OR trx_delivery.status_delivery='$pencarian'";
            } else {
                $pencarian  = '';
            }

            if ($_GET['date'] ?? "" != "") {
                $date       = $_GET['date'];
                $query_data = $query_data . " AND trx_delivery.delivery_date='$date'";
            } else {
                $date       = '';
            }
        /* Jika Pencarian Aktif */
        // Menampilkan Data
        $query_all_data = mysqli_query($con, $query_data);
        $all_data       = mysqli_num_rows($query_all_data);
        $total_page     = ceil($all_data / $batas);
        $sql_data       = mysqli_query($con, "$query_data ORDER BY trx_delivery.id DESC LIMIT $hal_awal, $batas");
        $no_urut        = $hal_awal + 1;
        // Setting Nav Pagination
        if ($hal > 1) {
            $nav_prev       = 'onclick="window.location.href=\'?hal=' . $previous . '&batas=' . $batas . '&cari=' . $pencarian . '&date='. $date .'\'"';
            if ($hal == $total_page) {
                $nav_first  = 'onclick="window.location.href=\'?hal=1&batas=' . $batas . '&cari=' . $pencarian . '&date='. $date .'\'"';
                $nav_last   = 'disabled';
                $nav_next   = 'disabled';
            } else {
                $nav_first  = 'onclick="window.location.href=\'?hal=1&batas=' . $batas . '&cari=' . $pencarian . '&date='. $date .'\'"';
                $nav_last   = 'onclick="window.location.href=\'?hal=' . $total_page . '&batas=' . $batas . '&cari=' . $pencarian . '&date='. $date .'\'"';
                $nav_next   = 'onclick="window.location.href=\'?hal=' . $next . '&batas=' . $batas . '&cari=' . $pencarian . '&date='. $date .'\'"';
            }
        } else {
            if ($hal == $total_page) {
                $nav_next   = 'disabled';
                $nav_last   = 'disabled';
                $nav_first  = 'disabled';
            } else {
                if ($all_data == 0) {
                    $nav_last   = 'disabled';
                    $nav_first  = 'disabled';
                    $nav_next   = 'disabled';
                } else {
                    $nav_last   = 'onclick="window.location.href=\'?hal=' . $total_page . '&batas=' . $batas . '&cari=' . $pencarian . '&date='. $date .'\'"';
                    $nav_first  = 'disabled';
                    $nav_next   = 'onclick="window.location.href=\'?hal=' . $next . '&batas=' . $batas . '&cari=' . $pencarian . '&date='. $date .'\'"';
                }
            }
            $nav_prev = 'disabled';
        }
        ?>
        <!-- Load Nav Header  -->

        <div class="content-wrapper bg-transparent" style="margin-top: 100px;">
            <!-- Main content -->
            <div class="content">
                <div class="container-fluid px-4 mt-4">
                    <div class="card card-primary rounded-md" style="border-top: 4px solid #263D57;">
                        <div class="card-body p-3">
                            <button class="btn btn-warning btn-block ls-1 text-semibold text-uppercase rounded-pill">DATA DELIVERY</button>
                        </div>
                        <div class="card-body pt-1 pb-1">
                            <table class="table table-borderless py-0 my-0 px-0">
                                <tr>
                                    <td width="15%" class="px-0">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-text fs-12 border-0 bg-transparent">Show</div>
                                            <select id="batas" class="form-control custom-select rounded-0 text-center fs-13" onchange="changeBatas(this)">
                                                <option <?= $batas == 10 ? 'selected' : ''; ?> value="10">10</option>
                                                <option <?= $batas == 20 ? 'selected' : ''; ?> value="20">20</option>
                                                <option <?= $batas == 50 ? 'selected' : ''; ?> value="50">50</option>
                                                <option <?= $batas == 100 ? 'selected' : ''; ?> value="100">100</option>
                                            </select>
                                            <div class="input-group-text fs-12 border-0 bg-transparent">Entries</div>
                                        </div>
                                    </td>
                                    <td></td>
                                    <td width="10%" class="px-0"><input type="date" id="date_search" value="<?= $date ?>" max="<?= date('Y-m-d'); ?>" class="form-control form-control-sm px-3 my-auto rounded-0 fs-12"></td>
                                    <td></td>
                                    <td width="15%" class="px-0">
                                        <input type="text" name="cari" maxlength="50" onkeyup="searchData(this)" onchange="search(this)" value="<?= $pencarian ?>" class="form-control px-3 my-auto rounded-0 fs-13 h-75 mb-2" placeholder="Search Data">
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-body pt-1">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless table-striped table-border-dark table-hover w-100 mb-0 pb-0">
                                    <thead>
                                        <tr class="bg-transparent bg-gray text-white lh-4 text-nowrap fs-12 text-uppercase">
                                            <th class="text-center" width="5%" style="vertical-align: middle !important;">No</th>
                                            <th class="text-left" width="7%" style="vertical-align: middle !important;">Tanggal</th>
                                            <th width="7%" class="py-0 lh-5 text-left" style="vertical-align: middle !important;">Kode Kurir</th>
                                            <th width="11%" class="py-0 lh-5 text-left" style="vertical-align: middle !important;">Nama Kurir</th>
                                            <th class="text-left" style="vertical-align: middle !important;">Kode Resi</th>
                                            <th width="11%" class="text-left text-wrap" style="vertical-align: middle !important;">Nama CS</th>
                                            <th class="text-left" width="9%" style="vertical-align: middle !important;">No Hp Seller</th>
                                            <th class="text-center" width="6%" style="vertical-align: middle !important;">Harga</th>
                                            <th class="text-center" width="6%" style="vertical-align: middle !important;">Ongkir</th>
                                            <th class="text-center" width="8%" style="vertical-align: middle !important;">Foto Proses</th>
                                            <th class="text-center" width="10%" style="vertical-align: middle !important;">Keterangan</th>
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
                                                $kode_kurir   = getInitials($rows['kurir_name']).$rows['kurir_id'];
                                                $price        = $rows['price'];
                                                $shiping_cost = $rows['shiping_cost'];
                                                ?>
                                                <tr class="fs-13 text-dark hover-light">
                                                    <td style="vertical-align: middle;" class="py-2 lh-3 text-center"><?= $no_urut++. '.'; ?></td>
                                                    <td style="vertical-align: middle;" class="py-2 lh-3 text-left"><?= $rows['delivery_date'] ?></td>
                                                    <td style="vertical-align: middle;" class="py-2 lh-3 text-left"><?= $kode_kurir ?></td>
                                                    <td style="vertical-align: middle;" class="py-2 lh-3 text-left text-uppercase"><?= $rows['kurir_name'] ?></td>
                                                    <td style="vertical-align: middle;" class="py-2 lh-3 text-left"><?= $rows['resi_code'] ?></td>
                                                    <td style="vertical-align: middle;" class="py-2 lh-3 text-left text-uppercase"><?= $rows['cs_name'] ?></td>
                                                    <td style="vertical-align: middle;" class="py-2 lh-3 text-left"><?= '+'.$rows['seller_phone_no'] ?></td>
                                                    <td style="vertical-align: middle;" class="py-2 lh-3 text-center text-nowrap"><?= $price.' K'; ?></td>
                                                    <td style="vertical-align: middle;" class="py-2 lh-3 text-center text-nowrap" ><?= $shiping_cost.' K'; ?></td>
                                                    <td style="vertical-align: middle;" class="py-2 lh-3 text-left text-nowrap"><button class="btn btn-sm btn-light bg-orange border-0 btn-block fs-12 hover rounded-sm" data-toggle="modal" data-target="#view<?= $rows['id'] ?>" >View</button></td>
                                                    <td style="vertical-align: middle;" class="py-2 lh-3 text-center text-nowrap text-uppercase"><?= $rows['status_delivery']; ?></td>
                                                </tr>
                                                <!-- Modal View Picture -->
                                                    <div class="modal fade" tabindex="-1" role="dialog" id="view<?= $rows['id'] ?>">
                                                        <div class="modal-dialog modal-sm" role="document">
                                                            <div class="modal-content rounded-sm">
                                                                <img src="../theme/dist/img/deliv/<?= $rows['picture_deliv'] ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                <!-- Modal View Picture -->
                                            <?php }
                                        }?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <?php if ($all_data > 0) { ?>
                                <div class="d-inline-block float-left mt-3 fs-13">
                                    <?= 'Showing ' . $hal . ' to ' . $total_page . ' of ' . $all_data . ' entries' ?>
                                </div>
                                <nav class="d-inline-block float-right mt-3">
                                    <ul class="pagination mb-0 fs-13">
                                        <?php if ($hal != 1) { ?>
                                            <li class="page-item">
                                                <button class="btn btn-warning border-0 shadow-none rounded-sm px-3 hover-light" <?= $nav_first ?>>First Page</button>
                                            </li>
                                        <?php } ?>
                                        <li class="page-item" style="padding-right:0.8px">
                                            <button class="btn btn-warning border-0 shadow-none rounded-sm px-3 hover-light" <?= $nav_prev ?>><i class="fa fa-caret-left"></i></button>
                                        </li>
                                        <?php
                                        $start_page = max(1, $hal - 2);
                                        $end_page   = min($total_page, $start_page + 3);
                                        for ($x = $start_page; $x <= $end_page; $x++) {
                                        ?>
                                            <li class="page-item" style="padding-left: 0.8px; padding-right: 0.8px">
                                                <button class="btn shadow-none border-0 rounded-sm px-3 btn-warning hover-light <?= $hal == $x ? 'active' : '' ?>" onclick="window.location.href='?hal=<?= $x ?>&batas=<?= $batas ?>&cari=<?= $pencarian ?>&date=<?= $date ?>'"><?= $x ?></button>
                                            </li>
                                        <?php
                                        }
                                        if ($end_page < $total_page) {
                                        ?>
                                            <li class="page-item disabled"><span class="page-link border-0">...</span></li>
                                            <li class="page-item" style="padding-left: 0.8px">
                                                <button class="btn shadow-none border-0 rounded-sm px-3 btn-warning hover-light <?= $hal == $total_page ? 'active' : '' ?>" onclick="window.location.href='?hal=<?= $total_page ?>&batas=<?= $batas ?>&cari=<?= $pencarian ?>&date=<?= $date ?>"><?= $total_page ?></button>
                                            </li>
                                        <?php } ?>
                                        <li class="page-item" style="padding-left:0.8px">
                                            <button class="btn btn-warning border-0 shadow-none rounded-sm px-3 hover-light" <?= $nav_next ?>><i class="fa fa-caret-right"></i></button>
                                        </li>
                                        <?php if ($hal >= 1 && $hal < $total_page) { ?>
                                            <li class="page-item">
                                                <button class="btn btn-warning border-0 shadow-none rounded-sm px-3 hover-light" <?= $nav_last ?>>Last Page</button>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </nav>
                            <?php } ?>
                            <!-- Pagination -->
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
    
    <script>
        function displayFileName() {
            const input = document.getElementById('file_input');
            const fileName = document.getElementById('file_name');

            if (input.files.length > 0) {
                fileName.value = input.files[0].name;
            } else {
                fileName.value = '';
            }
        }

        $(document).ready(function () {
            $('#date_search').change(function(){
                var date        = $(this).val();
                var currentURL  = window.location.href;
                var url         = new URL(currentURL);
                url.searchParams.set('date', date);
                window.location.href = url.toString();
            })
        });
    </script>
</body>

</html>