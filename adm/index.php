<!-- Load Dependency CSS -->
<?php include 'theme/main_header.php'; ?>
<!-- Load Dependency CSS -->

<body class="hold-transition layout-top-nav bg-light-gray">
    <div class="wrapper">
        <!-- Load Nav Header -->
        <?php 
        include 'theme/header.php';
        $month_now          = date('m');
        $date_now           = date('Y-m-d');
        $first_date_month   = date('Y-m-01');
        $query_deliv        = "SELECT DISTINCT dlv_pickup.id, trx_delivery.status_delivery 
                               FROM dlv_pickup 
                               LEFT JOIN trx_delivery ON trx_delivery.pickup_id = dlv_pickup.id 
                                   AND trx_delivery.id = (SELECT MAX(id) FROM trx_delivery t2 WHERE t2.pickup_id = dlv_pickup.id)
                               WHERE MONTH(trx_delivery.delivery_date)='$month_now' AND trx_delivery.id IS NOT NULL AND 1=1";
        $sql_deliv_proses   = mysqli_query($con, $query_deliv . " AND trx_delivery.status_delivery='PROSES'");
        $sql_deliv_pending  = mysqli_query($con, $query_deliv . " AND trx_delivery.status_delivery='PENDING'");
        $sql_deliv_cancel   = mysqli_query($con, $query_deliv . " AND trx_delivery.status_delivery='CANCEL'");
        $sql_deliv_sukses   = mysqli_query($con, $query_deliv . " AND trx_delivery.status_delivery='SUKSES'");
        
        // Set Pagination
            $batas      = isset($_GET['batas']) ? (int) $_GET['batas'] : 10;
            $hal        = isset($_GET['hal']) ? (int) $_GET['hal'] : 1;
            $hal_awal   = ($hal > 1) ? ($hal * $batas) - $batas : 0;
        // Set Butoon
            $previous   = $hal - 1;
            $next       = $hal + 1;
        // Query Data
            $query_data = "SELECT * FROM mst_kurir WHERE is_validate=0 AND 1=1";
            /* Jika Pencarian Aktif */
                if ($_GET['cari'] ?? "" != "") {
                    $pencarian  = $_GET['cari'];
                    $query_data = $query_data . " AND kurir_name LIKE '%$pencarian%'
                                                OR `status` LIKE '%$pencarian%'";
                }else{
                    $pencarian  = '';   
                }
            /* Jika Pencarian Aktif */
        // Menampilkan Data
            $query_all_data = mysqli_query($con, $query_data);
            $all_data       = mysqli_num_rows($query_all_data);
            $total_page     = ceil($all_data / $batas);
            $sql_data       = mysqli_query($con, "$query_data ORDER BY id DESC LIMIT $hal_awal, $batas");
            $no_urut        = $hal_awal + 1;
        // Setting Nav Pagination
            if ($hal > 1) {
                $nav_prev       = 'onclick="window.location.href=\'?hal=' . $previous . '&batas=' . $batas . '&cari=' . $pencarian . '\'"';
                if ($hal == $total_page) {
                    $nav_first  = 'onclick="window.location.href=\'?hal=1&batas=' . $batas . '&cari=' . $pencarian . '\'"';
                    $nav_last   = 'disabled';
                    $nav_next   = 'disabled';
                }else{
                    $nav_first  = 'onclick="window.location.href=\'?hal=1&batas=' . $batas . '&cari=' . $pencarian . '\'"';
                    $nav_last   = 'onclick="window.location.href=\'?hal=' . $total_page . '&batas=' . $batas . '&cari=' . $pencarian . '\'"';
                    $nav_next   = 'onclick="window.location.href=\'?hal=' . $next . '&batas=' . $batas . '&cari=' . $pencarian . '\'"';
                }
            }else{
                if ($hal == $total_page) {
                    $nav_next   = 'disabled';
                    $nav_last   = 'disabled';
                    $nav_first  = 'disabled';
                }else{
                    if ($all_data == 0) {
                        $nav_last   = 'disabled';
                        $nav_first  = 'disabled';
                        $nav_next   = 'disabled';
                    }else{
                        $nav_last   = 'onclick="window.location.href=\'?hal=' . $total_page . '&batas=' . $batas . '&cari=' . $pencarian . '\'"';
                        $nav_first  = 'disabled';
                        $nav_next   = 'onclick="window.location.href=\'?hal=' . $next . '&batas=' . $batas . '&cari=' . $pencarian . '\'"';
                    }
                }
                $nav_prev = 'disabled';
            }
        ?>
        <!-- Load Nav Header  -->

        <div class="content-wrapper bg-transparent" style="margin-top: 100px;">
            <div class="content-header pb-0">
                <div class="container-fluid px-4">
                    <div class="row">
                        <div class="col-sm-6">
                            <h1 class="m-0 fs-25 text-gray text-bold">Dashboard</h1>
                        </div>
                        <div class="col-sm-6">
                        </div>
                    </div>
                </div>
                <div class="container-fluid px-2">
                    <div class="row mt-3 mb-0">
                        <div class="col-lg-3 col-6 px-4">
                            <div class="small-box bg-success rounded-sm shadow-sm">
                                <div class="inner px-4">
                                    <h3 class="text-white mb-0 lh-4 py-3"><?= mysqli_num_rows($sql_deliv_sukses) ?></h3>
                                    <p class="my-0 text-white text-semibold fs-14">Sukses</p>
                                </div>
                                <a href="summary_delivery.php?status=SUKSES&from=<?= $first_date_month ?>&to=<?= $date_now ?>" class="small-box-footer text-white fs-13 py-2 lh-3 rounded-bottom-sm hover-light">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6 px-4">
                            <div class="small-box bg-orange rounded-sm shadow-sm">
                                <div class="inner px-4">
                                    <h3 class="text-white mb-0 lh-4 py-3"><?= mysqli_num_rows($sql_deliv_pending) ?></h3>
                                    <p class="my-0 text-white text-semibold fs-14">Pending</p>
                                </div>
                                <a href="summary_delivery.php?status=PENDING&from=<?= $first_date_month ?>&to=<?= $date_now ?>" class="small-box-footer text-white fs-13 py-2 lh-3 rounded-bottom-sm hover-light">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6 px-4">
                            <div class="small-box bg-danger rounded-sm shadow-sm">
                                <div class="inner px-4">
                                    <h3 class="text-white mb-0 lh-4 py-3"><?= mysqli_num_rows($sql_deliv_cancel) ?></h3>
                                    <p class="my-0 text-white text-semibold fs-14">Cancel</p>
                                </div>
                                <a href="summary_delivery.php?status=CANCEL&from=<?= $first_date_month ?>&to=<?= $date_now ?>" class="small-box-footer text-white fs-13 py-2 lh-3 rounded-bottom-sm hover-light">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6 px-4">
                            <div class="small-box bg-warning rounded-sm shadow-sm">
                                <div class="inner px-4">
                                    <h3 class="text-white mb-0 lh-4 py-3"><?= mysqli_num_rows($sql_deliv_proses) ?></h3>
                                    <p class="my-0 text-white text-semibold fs-14">Proses</p>
                                </div>
                                <a href="summary_delivery.php?status=PROSES&from=<?= $first_date_month ?>&to=<?= $date_now ?>" class="small-box-footer text-white fs-13 py-2 lh-3 rounded-bottom-sm hover-light">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid px-4">
                    <div class="card card-primary rounded-md" style="border-top: 4px solid #263D57;">
                        <div class="card-body p-3">
                            <button class="btn btn-warning btn-block ls-1 text-semibold text-uppercase rounded-pill">KURIR REGISTRASI</button>
                        </div>
                        <div class="card-body pt-1 pb-1">
                            <div class="float-left">
                                <div class="input-group input-group-sm mb-2">
                                    <div class="input-group-text fs-12 border-0 bg-transparent">Show</div>
                                    <select id="batas" class="form-control custom-select rounded-0 text-center fs-13" onchange="changeBatas(this)">
                                        <option <?= $batas == 10 ? 'selected' : ''; ?> value="10">10</option>
                                        <option <?= $batas == 20 ? 'selected' : ''; ?> value="20">20</option>
                                        <option <?= $batas == 50 ? 'selected' : ''; ?> value="50">50</option>
                                        <option <?= $batas == 100 ? 'selected' : ''; ?> value="100">100</option>
                                    </select>
                                    <div class="input-group-text fs-12 border-0 bg-transparent">Entries</div>
                                </div>
                            </div>
                            <div class="float-right">
                                <input type="text" name="cari" maxlength="50" onkeyup="searchData(this)" onchange="search(this)" value="<?= $pencarian ?>" class="form-control px-3 my-auto rounded-0 fs-13 h-75 mb-2" placeholder="Search Data">
                            </div>
                        </div>
                        <div class="card-body pt-1">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless table-striped table-border-dark table-hover w-100 mb-0 pb-0">
                                    <thead>
                                        <tr class="bg-transparent bg-gray text-white lh-2 text-nowrap fs-13 text-uppercase">
                                            <th style="vertical-align: top !important;" class="py-2 text-center" width="5%">No</th>
                                            <th style="vertical-align: top !important;" class="py-2 text-center" width="5%">Foto Kurir</th>
                                            <th style="vertical-align: top !important;" class="py-2 text-left">Nama Kurir</th>
                                            <th style="vertical-align: top !important;" class="py-2 text-left" width="12%">TTL</th>
                                            <th style="vertical-align: top !important;" class="py-2 text-left" width="15%">Alamat Dibatam</th>
                                            <th style="vertical-align: top !important;" class="py-2 text-left" width="10%">No Telepon</th>
                                            <th style="vertical-align: top !important;" class="py-2 text-left" width="6%">Status</th>
                                            <th style="vertical-align: top !important;" class="py-2 text-left" width="10%">Nama<br>Istri/Suami </th>
                                            <th style="vertical-align: top !important;" class="py-2 text-left" width="10%">No Telepon<br>Istri/Suami </th>
                                            <th style="vertical-align: top !important;" class="py-2 text-left" width="10%">No Telepon<br>Keluarga Aktif </th>
                                            <th style="vertical-align: top !important;" class="py-2 text-left" width="5%">Tools</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if ($all_data <= 0) {
                                            echo'<tr>
                                                <td colspan="11" class="text-center fs-13">Record Not Found</td>
                                            </tr>';
                                        }else{
                                            foreach($sql_data as $rows){$status = $rows['status'];  ?>
                                                <tr class="fs-13 text-dark hover-light">
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $no_urut++.'.';?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><img src="../theme/dist/img/kurir/<?= $rows['profile_pic'] ?>"  data-toggle="modal" data-target="#view<?= $rows['id'] ?>" alt="<?= $rows['profile_pic'] ?>" class="rounded-circle" width="40px" height="40px"></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-left"><?= $rows['kurir_name'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-left"><?= $rows['birdth_place'].', '. date_id($rows['birdth_date']) ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-left"><?= $rows['batam_address'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-left"><?= '+'.$rows['phone_number'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-left"><?= $status ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-left"><?= ($status == 'Menikah') ? $rows['partner_name']: '-'; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-left"><?= ($status == 'Menikah') ? '+'.$rows['phone_number_partner']: '-' ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-left"><?= ($status == 'Single') ? '+'.$rows['phone_number_family']: '-' ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-left">
                                                        <button class="btn btn-sm py-1 rounded-sm btn-success hover-light mb-1 btn-block"  onclick="
                                                            Toast_Confirm.fire({
                                                            title: 'Aprove Kurir', 
                                                            text: 'Apakah anda yakin...?', 
                                                            icon: 'warning',
                                                            showCancelButton: true,
                                                            confirmButtonText: 'Ya',
                                                            showCancelButton: true,
                                                            cancelButtonText: 'Batal',
                                                            customClass: {
                                                                popup: 'border-0 elevation-2 rounded-md pl-4 pr-0',
                                                                confirmButton: 'btn btn-sm btn-light bg-success py-1 fs-12 shadow-none border-0 rounded-sm mb-3 hover-light ml-3 mr-1 px-3', // Add your custom CSS classes here
                                                                cancelButton: 'btn btn-sm btn-light bg-danger py-1 fs-12 shadow-none rounded-sm mb-3 hover-light px-3' // Add your custom CSS classes here
                                                            }
                                                            }).then((result) => {
                                                                if (result.isConfirmed) {
                                                                    var id  = <?= $rows['id']; ?>;
                                                                    $.ajax({
                                                                        type: 'POST',
                                                                        url: 'proses/aprovement/aprove_kurir.php',
                                                                        data: {id: id},
                                                                        success: function(response) {
                                                                            if(response == 'Y') {
                                                                                Toast.fire({  
                                                                                    icon: 'success',
                                                                                    title: 'Verification Success',
                                                                                    text: 'Kurir telah terverifikasi',
                                                                                });
                                                                            }else if(response == 'N') {
                                                                                Toast.fire({  
                                                                                    icon: 'error',
                                                                                    title: 'Verification Failed',
                                                                                    text: 'Kurir tidak dapat diverifikasi',
                                                                                });
                                                                            }else {
                                                                                Toast.fire({
                                                                                    icon: 'warning',
                                                                                    title: 'Erorr 404, Not Found',
                                                                                    text: 'Record tidak ditemukan',
                                                                                });
                                                                            }
                                                                            setTimeout(function(){
                                                                                window.location.href = window.location.pathname;
                                                                            }, 1500);
                                                                        }
                                                                    })
                                                                }
                                                            })
                                                        "><i class="fas fa-check"></i></button>
                                                        <button class="btn btn-sm py-1 rounded-sm btn-danger hover-light btn-block" onclick="
                                                            Toast_Confirm.fire({
                                                            title: 'Reject Kurir', 
                                                            text: 'Apakah anda yakin...?', 
                                                            icon: 'warning',
                                                            showCancelButton: true,
                                                            confirmButtonText: 'Ya',
                                                            showCancelButton: true,
                                                            cancelButtonText: 'Batal',
                                                            customClass: {
                                                                popup: 'border-0 elevation-2 rounded-md pl-4 pr-0',
                                                                confirmButton: 'btn btn-sm btn-light bg-success py-1 fs-12 shadow-none border-0 rounded-sm mb-3 hover-light ml-3 mr-1 px-3', // Add your custom CSS classes here
                                                                cancelButton: 'btn btn-sm btn-light bg-danger py-1 fs-12 shadow-none rounded-sm mb-3 hover-light px-3' // Add your custom CSS classes here
                                                            }
                                                            }).then((result) => {
                                                                if (result.isConfirmed) {
                                                                    var id = <?= $rows['id']; ?>;
                                                                    $.ajax({
                                                                        type: 'POST',
                                                                        url: 'proses/aprovement/reject_kurir.php',
                                                                        data: {id: id},
                                                                        success: function(response) {
                                                                            if(response == 'Y') {
                                                                                Toast.fire({   
                                                                                    icon: 'success',
                                                                                    title: 'Reject Success',
                                                                                    text: 'Kurir berhasil di reject',
                                                                                });
                                                                            }else if(response == 'N') {
                                                                                Toast.fire({  
                                                                                    icon: 'error',
                                                                                    title: 'Reject Failed',
                                                                                    text: 'Kurir tidak dapat di reject',
                                                                                });
                                                                            }else {
                                                                                Toast.fire({
                                                                                    icon: 'warning',
                                                                                    title: 'Erorr 404, Not Found',
                                                                                    text: 'Record tidak ditemukan',
                                                                                });
                                                                            }
                                                                            setTimeout(function(){
                                                                                window.location.href = window.location.pathname;
                                                                            }, 1500);
                                                                        }
                                                                    })
                                                                }
                                                            })
                                                        "><i class="fas fa-times"></i></button>
                                                    </td>
                                                </tr>
                                                <!-- Modal View Profile -->
                                                <div class="modal fade" tabindex="-1" role="dialog" id="view<?= $rows['id']?>">
                                                    <div class="modal-dialog modal-sm" role="document">
                                                        <div class="modal-content rounded-sm">
                                                            <img src="../theme/dist/img/kurir/<?= $rows['profile_pic'] ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Modal View Profile -->
                                            <?php }
                                        }?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                                <?php if ($all_data > 0) {?>
                                    <div class="d-inline-block float-left mt-3 fs-13">
                                        <?= 'Showing '.$hal.' to '.$total_page.' of '.$all_data.' entries' ?>
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
                                                    <button class="btn shadow-none border-0 rounded-sm px-3 btn-warning hover-light <?= $hal == $x ? 'active' : '' ?>" onclick="window.location.href='?hal=<?= $x ?>&batas=<?= $batas ?>&cari=<?= $pencarian ?>'"><?= $x ?></button>
                                                </li>
                                            <?php
                                            } if ($end_page < $total_page) {
                                            ?>
                                                <li class="page-item disabled"><span class="page-link border-0">...</span></li>
                                                <li class="page-item" style="padding-left: 0.8px">
                                                    <button class="btn shadow-none border-0 rounded-sm px-3 btn-warning hover-light <?= $hal == $total_page ? 'active' : '' ?>" onclick="window.location.href='?hal=<?= $total_page ?>&batas=<?= $batas ?>&cari=<?= $pencarian ?>"><?= $total_page ?></button>
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
</body>

</html>