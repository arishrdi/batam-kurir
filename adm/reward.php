<!-- Load Dependency CSS -->
<?php include 'theme/main_header.php'; ?>
<!-- Load Dependency CSS -->

<body class="hold-transition layout-top-nav bg-light-gray">
    <div class="wrapper">
        <!-- Load Nav Header -->
        <?php
        include 'theme/header.php';
        // Select Option Pick Up
        $point_reward   = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM mst_config"))['poin_reward'];

        /* Set Pagination */ 
        $batas          = isset($_GET['batas']) ? (int) $_GET['batas'] : 10;
        $hal            = isset($_GET['hal']) ? (int) $_GET['hal'] : 1;
        $hal_awal       = ($hal > 1) ? ($hal * $batas) - $batas : 0;
        /* Set Butoon */
        $previous       = $hal - 1;
        $next           = $hal + 1;
        /* Query Data */
        $query_data     = "SELECT * FROM trx_reward WHERE counting >= $point_reward AND  1=1";
        /* Jika Pencarian Aktif */
            if ($_GET['cari'] ?? "" != "") {
                $pencarian  = $_GET['cari'];
                $query_data = $query_data . " AND seller_phone_no='$pencarian'";
            } else {
                $pencarian  = '';
            }

            if ($_GET['date'] ?? "" != "") {
                $date       = $_GET['date'];
                $query_data = $query_data . " AND milestone_date='$date'";
            } else {
                $date       = '';
            }
        /* Jika Pencarian Aktif */
        /* Menampilkan Data */ 
        $query_all_data = mysqli_query($con, $query_data);
        $all_data       = mysqli_num_rows($query_all_data);
        $total_page     = ceil($all_data / $batas);
        $sql_data       = mysqli_query($con, "$query_data ORDER BY id DESC LIMIT $hal_awal, $batas");
        $no_urut        = $hal_awal + 1;
        /* Setting Nav Pagination */
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
                <div class="container-fluid px-4 mt-3">
                    <h1 class="m-0 fs-25 text-gray text-bold my-2">Master Data <i class="fas fa-chevron-right text-md px-2"></i> <span class="fs-20">Reward</span></h1>
                    
                    <div class="row">
                        <div class="col-md-9">
                            <div class="card card-primary rounded-md" style="border-top: 4px solid #263D57;">
                                <div class="card-body p-3">
                                    <button class="badge badge-warning border-0 fs-14 py-2 lh-4 btn-block ls-1 text-semibold text-uppercase rounded-pill">DATA REWARD</button>
                                </div>
                                <div class="card-body pt-0 pb-1">
                                    <a href="export/reward.php?cari=<?= $pencarian ?>&date=<?= $date ?>" class="btn btn-sm btn-success border-0 rounded-sm px-4 mb-2 hover float-right"><i class="fas fa-file-excel pr-2"></i>Export</a>
                                    <table class="table table-borderless py-0 my-0 px-0">
                                        <tr>
                                            <td width="20%" class="px-0">
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
                                            <td width="15%" class="px-0"><input type="date" id="date_search" value="<?= $date ?>" max="<?= date('Y-m-d'); ?>" class="form-control form-control-sm px-3 my-auto rounded-0 fs-12"></td>
                                            <td></td>
                                            <td width="20%" class="px-0">
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
                                                    <th class="text-left" style="vertical-align: middle !important;">Tanggal Pencapaian</th>
                                                    <th class="text-left" width="25%" style="vertical-align: middle !important;">No Hp Seller</th>
                                                    <th class="text-center text-wrap" width="10%" style="vertical-align: middle !important;">Reward</th>
                                                    <th class="text-center text-wrap" width="15%" style="vertical-align: middle !important;">Detail Delivery</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if ($all_data <= 0) {
                                                    echo '<tr>
                                                        <td colspan="5" class="text-center fs-12">Record Not Found</td>
                                                    </tr>';
                                                } else {
                                                    foreach ($sql_data as $rows) {$status = $rows['status_claim']; ?>
                                                        <tr class="fs-13 text-dark hover-light">
                                                            <td style="vertical-align: middle;" class="py-1 lh-3 text-center"><?= $no_urut++. '.'; ?></td>
                                                            <td style="vertical-align: middle;" class="py-1 lh-3 text-left"><?= $rows['milestone_date'] ?></td>
                                                            <td style="vertical-align: middle;" class="py-1 lh-3 text-left text-uppercase"><?= '+'.$rows['seller_phone_no'] ?></td>
                                                            <td style="vertical-align: middle;" class="py-1 lh-3 text-left text-nowrap">
                                                                <?php if ($status == 'Claim') { ?>
                                                                    <button class="btn btn-sm btn-light bg-orange border-0 btn-block fs-12 rounded-sm">Reward</button>
                                                                <?php }else{ ?>
                                                                    <button onclick="
                                                                        Toast_Confirm.fire({
                                                                        title: 'Claim Reward', 
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
                                                                                    url: 'proses/aprovement/reward.php',
                                                                                    data: {id: id},
                                                                                    success: function(response) {
                                                                                        if(response == 'Y') {
                                                                                            Toast.fire({  
                                                                                                icon: 'success',
                                                                                                title: 'Claim Reward Success',
                                                                                                text: 'Reward berhasil di claim',
                                                                                            });
                                                                                        }else if(response == 'N') {
                                                                                            Toast.fire({  
                                                                                                icon: 'error',
                                                                                                title: 'Claim Reward Failed',
                                                                                                text: 'Reward gagal di claim',
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
                                                                    " class="btn btn-sm btn-light bg-gray border-0 btn-block fs-12 hover rounded-sm">Reward</button>
                                                                <?php } ?>
                                                            </td>
                                                            <td style="vertical-align: middle;" class="py-1 lh-3 text-center text-nowrap"><a href="view_delivery.php?seller=<?= $rows['seller_phone_no']; ?>" class="btn btn-link hover_text fs-12 hover rounded-sm"><i class="fas fa-list-alt"></i></a></td>
                                                        </tr>
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
                        <div class="col-md-3">
                            <div class="card card-primary rounded-md" style="border-top: 4px solid #263D57;">
                                <div class="card-body p-3">
                                    <button class="badge border-0 badge-primary btn-block ls-1 text-semibold text-uppercase rounded-pill mb-3 py-2 lh-4" disabled>Settingan Reward</button>
                                    <form id="point_reward">
                                        <div class="form-group mb-3">
                                            <label class="fs-14 text-semibold">Jumlah Poin Reward </label>
                                            <input type="number" name="poin_reward" class="form-control rounded-sm pr-1" placeholder="0" min="0" value="<?= $point_reward ?>">
                                        </div>
                                        <div class="form-group mb-0">
                                            <button type="submit" id="submit" class="btn btn-warning btn-block ls-1 text-semibold hover rounded-sm mb-2">Simpan</button>
                                        </div>
                                    </form>
                                </div>
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
    
    <script>
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