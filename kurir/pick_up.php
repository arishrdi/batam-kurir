<!-- Load Dependency CSS -->
<?php include 'theme/main_header.php'; ?>
<!-- Load Dependency CSS -->

<body class="hold-transition layout-top-nav bg-light-gray">
    <div class="wrapper">
        <!-- Load Nav Header -->
        <?php
        include 'theme/header.php';

        $query_kurir    = mysqli_query($con, "SELECT * FROM mst_kurir WHERE NOT kurir_name='Administrator' AND is_validate=1 ORDER BY kurir_name ASC");


        // Query Data
        $query_data     = "SELECT 
            dlv_pickup.id AS pickup_id,
            dlv_pickup.pickup_date,
            dlv_pickup.resi_code,
            dlv_pickup.kurir_id,
            mst_kurir.kurir_name,
            dlv_pickup.cs_name,
            CONCAT('+', dlv_pickup.seller_phone_no) AS seller_phone_no,
            dlv_pickup.price,
            dlv_pickup.shiping_cost,
            dlv_pickup.status_pickup
        FROM dlv_pickup 
            JOIN mst_kurir ON mst_kurir.id=dlv_pickup.kurir_id
        WHERE dlv_pickup.kurir_id={$data_kurir['id']} AND 1=1";
        
        /* Jika Pencarian Aktif */
        if ($_GET['cari'] ?? "" != "") {
            $pencarian  = $_GET['cari'];
            $query_data = $query_data . " AND (dlv_pickup.resi_code LIKE '%$pencarian%'
                OR dlv_pickup.cs_name LIKE '%$pencarian%'
                OR dlv_pickup.seller_phone_no LIKE '%$pencarian%')";
        } else {
            $pencarian  = '';
        }

        if ($_GET['date'] ?? "" != "") {
            $date       = $_GET['date'];
            $query_data = $query_data . " AND dlv_pickup.pickup_date='$date'";
        } else {
            $date       = date('Y-m-d');
            $query_data = $query_data . " AND dlv_pickup.pickup_date='$date'";
        }
        /* Jika Pencarian Aktif */
        
        // Menampilkan Data
        $sql_data       = mysqli_query($con, "$query_data ORDER BY dlv_pickup.id ASC");
        $all_data       = mysqli_num_rows($sql_data);
        $no_urut        = 1;

        if (!$sql_data) {
            die("Query Error: " . mysqli_error($con));
        }

        
        /* Calculate Totals */
        $total_price = 0;
        $total_shipping = 0;
        $temp_data = [];
        while ($row = mysqli_fetch_assoc($sql_data)) {
            $temp_data[] = $row;
            $total_price += $row['price'];
            $total_shipping += $row['shiping_cost'];
        }
        $sql_data = $temp_data;
        
        // Generate courier code for display
        $kode_kurir = ($data_kurir['kurir_name'] == 'Administrator') ? 'ADM' : getInitials($data_kurir['kurir_name']).$data_kurir['id'];
   
        ?>
        <!-- Load Nav Header  -->

        <div class="content-wrapper bg-transparent" style="margin-top: 100px;">
            <!-- Main content -->
            <div class="content">
                <div class="container-fluid px-4 mt-4">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="card card-primary rounded-md" style="border-top: 4px solid #263D57;">
                                <div class="card-body p-3">
                                    <button class="btn btn-warning btn-block ls-1 text-semibold text-uppercase rounded-pill">DATA PICK UP</button>
                                </div>
                                <div class="card-body pt-1 pb-1">
                                    <div class="float-right">
                                        <div class="form-inline mb-2">
                                            <div class="input-group input-group-sm">
                                                <input type="date" id="date_search" value="<?= $date ?>" max="<?= date('Y-m-d'); ?>" class="form-control form-control-sm px-3 my-auto rounded-0 fs-12">
                                                <input type="text" name="cari" maxlength="50" onkeyup="searchData(this)" onchange="search(this)" value="<?= $pencarian ?>" class="form-control px-3 my-auto rounded-0 fs-13 mb-2" placeholder="Search Data">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-1">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless table-striped table-border-dark table-hover w-100 mb-0 pb-0">
                                            <thead>
                                                <tr class="bg-transparent bg-gray text-white lh-3 text-nowrap text-uppercase fs-12">
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">No</th>
                                                    <th class="py-0 lh-2 text-center" style="vertical-align: middle !important;">Kurir Pick Up</th>
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
                                                        <td colspan="9" class="text-center fs-12">Record Not Found</td>
                                                    </tr>';
                                                } else {
                                                    foreach ($sql_data as $rows) {
                                                        $price                  = $rows['price'];
                                                        $shiping_cost           = $rows['shiping_cost'];
                                                ?>
                                                        <tr class="fs-13 text-dark hover-light">
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $no_urut++ . '.'; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $rows['kurir_name'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $rows['resi_code'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-uppercase"><?= $rows['cs_name'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-left"><?= $rows['seller_phone_no'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $price ; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $shiping_cost; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap text-uppercase"><?= $rows['status_pickup']; ?></td>
                                                        </tr>
                                                        <!-- Modal View Picture -->
                                                        <div class="modal fade" tabindex="-1" role="dialog" id="view<?= $rows['pickup_id'] ?>">
                                                            <div class="modal-dialog modal-sm" role="document">
                                                                <div class="modal-content rounded-sm">
                                                                    <img src="../theme/dist/img/pickup/<?= $rows['picture'] ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Modal View Picture -->
                                                <?php }
                                                } ?>
                                                <?php if ($all_data > 0) { ?>
                                                <tr class="bg-light text-bold">
                                                    <td colspan="5" class="text-right py-2 fs-13">TOTAL:</td>
                                                    <td class="text-center py-2 fs-13"><?= $total_price ?></td>
                                                    <td class="text-center py-2 fs-13"><?= $total_shipping ?></td>
                                                    <td colspan="2"></td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php if ($all_data > 0) { ?>
                                        <div class="d-inline-block float-left mt-3 fs-13">
                                            <?= 'Total ' . $all_data . ' entries' ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="card rounded-md bg-primary">
                                <div class="card-body py-2 text-semibold">
                                    Kode Kurir Anda: <span class="mx-2 bg-white px-4"><?= $kode_kurir . ' - ' . $all_data ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-primary rounded-md" style="border-top: 4px solid #263D57;">
                                <div class="card-body p-3">
                                    <h6 class="text-bold text-center mb-3">Form Pick Up</h6>
                                    <form enctype="multipart/form-data" id="insert-pickup">
                                        <input type="hidden" name="pickup_date" value="<?= date('Y-m-d'); ?>">
                                        <div class="form-group mb-3">
                                            <div class="input-group rounded-sm">
                                                <input type="text" disabled class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-left-sm text-semibold border-right-0" value="<?= daydate_id(date('Y-m-d')) ?>">
                                                <div class="input-group-text text-semibold rounded-right-sm fs-13 border-left-0">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <input type="hidden" name="kurir_id" value="<?= $data_kurir['id'] ?>"> -->
                                        <div class="form-group mb-3">
                                            <select name="kurir_id" class="form-control rounded-sm select2bs4" required>
                                                <option value="">PILIH KURIR</option>
                                                <?php foreach ($query_kurir as $val_kurir) { ?>
                                                    <option value="<?= $val_kurir['id'] ?>" <?=$val_kurir['id'] === $data_kurir['id'] ? "selected" : "" ?>><?= strtoupper($val_kurir['kurir_name']) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <input type="text" name="cs_name" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-sm text-semibold" maxlength="100" placeholder="Nama CS" autofocus required>
                                        </div>
                                        <div class="form-group mb-3" id="input-3">
                                            <div class="input-group rounded-sm">
                                                <div class="input-group-text border-1 bg-white border-left text-semibold rounded-left-sm fs-13 border-right-0">
                                                    +62
                                                </div>
                                                <input type="text" name="seller_phone_no" class="form-control ls1 fs-13 px-2 h-50 py-2 rounded-right-sm text-semibold border-right phone_number" placeholder="No HP Seller" autofocus required>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <div class="input-group rounded-sm">
                                                <input type="text" name="price" id="price" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-left-sm text-semibold" min="0" placeholder="Harga" autofocus required>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <div class="input-group rounded-sm">
                                                <input type="text" name="shiping_cost" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-left-sm text-semibold" min="0" placeholder="Ongkir" autofocus required>
                                            </div>
                                        </div>
                                        <div class="form-group mb-0">
                                            <button type="submit" id="submit" class="btn py-2 text-semibold btn-warning rounded-sm border-0 ls3 fs-12 hover px-4">Add</button>
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
        // function displayFileName() {
        //     const input = document.getElementById('file_input');
        //     const fileName = document.getElementById('file_name');

        //     if (input.files.length > 0) {
        //         fileName.value = input.files[0].name;
        //     } else {
        //         fileName.value = '';
        //     }
        // }
        $(document).ready(function() {
            $('#date_search').change(function() {
                var date = $(this).val();
                var currentURL = window.location.href;
                var url = new URL(currentURL);
                url.searchParams.set('date', date);
                window.location.href = url.toString();
            });

        });
    </script>
</body>

</html>