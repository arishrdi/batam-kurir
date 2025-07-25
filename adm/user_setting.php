<!-- Load Dependency CSS -->
<?php include 'theme/main_header.php'; ?>
<!-- Load Dependency CSS -->

<body class="hold-transition layout-top-nav bg-light-gray">
    <div class="wrapper">
        <!-- Load Nav Header -->
        <?php
        include 'theme/header.php';

        // Set Pagination
        $batas      = isset($_GET['batas']) ? (int) $_GET['batas'] : 10;
        $hal        = isset($_GET['hal']) ? (int) $_GET['hal'] : 1;
        $hal_awal   = ($hal > 1) ? ($hal * $batas) - $batas : 0;
        // Set Butoon
        $previous   = $hal - 1;
        $next       = $hal + 1;
        // Query Data
        $query_data = "SELECT mst_user.*, 
        mst_role_access.role_access
        FROM mst_user 
        JOIN mst_role_access ON mst_role_access.id=mst_user.role_access_id
        WHERE mst_user.role_access_id='1' AND 1=1";
        /* Jika Pencarian Aktif */
        if ($_GET['cari'] ?? "" != "") {
            $pencarian  = $_GET['cari'];
            $query_data = $query_data . " AND mst_user.full_name LIKE '%$pencarian%'";
        } else {
            $pencarian  = '';
        }
        /* Jika Pencarian Aktif */
        // Menampilkan Data
        $query_all_data = mysqli_query($con, $query_data);
        $all_data       = mysqli_num_rows($query_all_data);
        $total_page     = ceil($all_data / $batas);
        $sql_data       = mysqli_query($con, "$query_data ORDER BY mst_user.id DESC LIMIT $hal_awal, $batas");
        $no_urut        = $hal_awal + 1;
        // Setting Nav Pagination
        if ($hal > 1) {
            $nav_prev       = 'onclick="window.location.href=\'?hal=' . $previous . '&batas=' . $batas . '&cari=' . $pencarian . '\'"';
            if ($hal == $total_page) {
                $nav_first  = 'onclick="window.location.href=\'?hal=1&batas=' . $batas . '&cari=' . $pencarian . '\'"';
                $nav_last   = 'disabled';
                $nav_next   = 'disabled';
            } else {
                $nav_first  = 'onclick="window.location.href=\'?hal=1&batas=' . $batas . '&cari=' . $pencarian . '\'"';
                $nav_last   = 'onclick="window.location.href=\'?hal=' . $total_page . '&batas=' . $batas . '&cari=' . $pencarian . '\'"';
                $nav_next   = 'onclick="window.location.href=\'?hal=' . $next . '&batas=' . $batas . '&cari=' . $pencarian . '\'"';
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
                    <h1 class="m-0 fs-25 text-gray text-bold my-2">Master Data <i class="fas fa-chevron-right text-md px-2"></i> <span class="fs-20">Management Role</span></h1>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid px-4">
                    <div class="card card-primary rounded-md" style="border-top: 4px solid #263D57;">
                        <div class="card-body p-3">
                            <button class="btn btn-warning btn-block ls-1 text-semibold text-uppercase rounded-pill">Management Role</button>
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
                                            <th style="vertical-align: top!important;" class="py-2 text-center" width="4%">No</th>
                                            <th style="vertical-align: top!important;" class="py-2 text-left">Nama Lengkap</th>
                                            <th style="vertical-align: top!important;" class="py-2 text-left" width="13%">Username</th>
                                            <th style="vertical-align: top!important;" class="py-2 text-left" width="22%">Password</th>
                                            <th style="vertical-align: top!important;" class="py-2 text-left" width="10%">Tools</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($all_data <= 0) {
                                            echo '<tr>
                                                <td colspan="11" class="text-center fs-13">Record Not Found</td>
                                            </tr>';
                                        } else {
                                            foreach ($sql_data as $rows) {?>
                                                <tr class="fs-13 text-dark hover-light">
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $no_urut. '.'; ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-left"><?= $rows['full_name'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-left"><?= $rows['username'] ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-left"><?= md5($rows['password']) ?></td>
                                                    <td style="vertical-align: top;" class="py-2 lh-3 text-left">
                                                        <button class="btn btn-sm btn-block btn-light bg-orange text-white text-semibold border-0 fs-12 py-1 rounded-pill hover mb-1" data-toggle="modal" data-target="#edit<?= $rows['id'] ?>">UPDATE</button>
                                                    </td>
                                                </tr>
                                                <!-- Modal Edit Kurir -->
                                                <div class="modal fade" tabindex="-1" role="dialog" id="edit<?= $rows['id'] ?>">
                                                    <div class="modal-dialog modal-sm" role="document">
                                                        <div class="modal-content rounded-md">
                                                            <div class="modal-header pb-2 pt-2 px-3">
                                                                <h6 class="modal-title text-bold"><i class="fas fa-edit pr-2"></i>Edit Record</h6>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body py-0">
                                                                <form action="proses/update/user.php" method="post">
                                                                    <input type="hidden" name="id" value="<?= $rows['id'] ?>">
                                                                    <div class="form-group mb-2">
                                                                        <label class="mb-1">Username</label>
                                                                        <input type="text" name="username" value="<?= $rows['username'] ?>" placeholder="Username" class="form-control ls3 fs-13 px-3 h-50 py-2 rounded-sm" autofocus required>
                                                                    </div>
                                                                    <div class="form-group mb-3">
                                                                        <label class="mb-1">Password</label>
                                                                        <div class="input-group">
                                                                            <input type="password" name="password" id="password<?= $no_urut ?>" placeholder="Password" class="form-control ls3 fs-13 px-3 h-50 py-2 rounded-left-sm" autofocus required>
                                                                            <div class="input-group-append border-0 bg-white">
                                                                                <div class="input-group-text hover rounded-right-sm bg-white" id="show<?= $no_urut ?>">
                                                                                    <span class="fas fa-eye-slash text-sm"></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group mb-0">
                                                                        <button type="button" class="btn btn-danger btn-sm py-2 fs-12 hover-light border-0 shadow-none float-left px-3 rounded-sm mb-4" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Close</button>
                                                                        <button type="submit" class="btn btn-success btn-sm py-2 fs-12 hover-light border-0 shadow-none float-right px-3 rounded-sm mb-4" name="edit"><i class="fa fa-save mr-2"></i>Simpan</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Modal Edit Kurir -->

                                                <!-- Modal View Profile -->
                                                <div class="modal fade" tabindex="-1" role="dialog" id="view<?= $rows['id'] ?>">
                                                    <div class="modal-dialog modal-sm" role="document">
                                                        <div class="modal-content rounded-sm">
                                                            <img src="../theme/dist/img/kurir/<?= $rows['profile_pic'] ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Modal View Profile -->
                                        <?php $no_urut++;}
                                        } ?>
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
                                                <button class="btn shadow-none border-0 rounded-sm px-3 btn-warning hover-light <?= $hal == $x ? 'active' : '' ?>" onclick="window.location.href='?hal=<?= $x ?>&batas=<?= $batas ?>&cari=<?= $pencarian ?>'"><?= $x ?></button>
                                            </li>
                                        <?php
                                        }
                                        if ($end_page < $total_page) {
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
    <script>
        $(document).ready(function () {
            <?php for ($i=1; $i <= $all_data; $i++) { ?>
                $('#show<?= $i ?>').click(function() {
                    var passwordField   = $('#password<?= $i ?>');
                    var icon            = $('#show<?= $i ?> span');
                    
                    if (passwordField.attr('type') === "password") {
                        passwordField.attr('type', 'text');
                        icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    } else {
                        passwordField.attr('type', 'password');
                        icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    }
                });
            <?php } ?>
        });
    </script>
</body>

</html>