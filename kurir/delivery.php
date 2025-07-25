<!-- Load Dependency CSS -->
<?php include 'theme/main_header.php'; ?>
<!-- Load Dependency CSS -->

<body class="hold-transition layout-top-nav bg-light-gray">
    <div class="wrapper">
        <!-- Load Nav Header -->
        <?php
        include 'theme/header.php';

        // Select Option Pick Up
        $query_pickup   = mysqli_query($con, "SELECT * FROM dlv_pickup WHERE id NOT IN (SELECT pickup_id FROM trx_delivery WHERE status_delivery IN ('SUKSES', 'CANCEL'))");

        // Query Data - Show latest delivery attempt per pickup
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
        WHERE trx_delivery.kurir_id={$data_kurir['id']} AND 1=1";

        /* Jika Pencarian Aktif */
        if ($_GET['cari'] ?? "" != "") {
            $pencarian  = $_GET['cari'];
            $query_data = $query_data . " AND (dlv_pickup.resi_code LIKE '%$pencarian%'
                OR dlv_pickup.cs_name LIKE '%$pencarian%'
                OR dlv_pickup.seller_phone_no LIKE '%$pencarian%'
                OR trx_delivery.status_delivery='$pencarian')";
        } else {
            $pencarian  = '';
        }

        if ($_GET['date'] ?? "" != "") {
            $date       = $_GET['date'];
            $query_data = $query_data . " AND trx_delivery.delivery_date='$date'";
        } else {
            $date       = date('Y-m-d');
            $query_data = $query_data . " AND trx_delivery.delivery_date='$date'";
        }
        /* Jika Pencarian Aktif */

        // Menampilkan Data
        $sql_data       = mysqli_query($con, "$query_data ORDER BY trx_delivery.id ASC");
        $all_data       = mysqli_num_rows($sql_data);
        $no_urut        = 1;

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
        ?>
        <!-- Load Nav Header  -->

        <div class="content-wrapper bg-transparent" style="margin-top: 100px;">
            <!-- Main content -->
            <div class="content">
                <div class="container-fluid px-4 mt-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card card-primary rounded-md" style="border-top: 4px solid #263D57;">
                                <div class="card-body p-3">
                                    <h6 class="text-bold text-center mb-3">Form Delivery</h6>
                                    <form id="insert-deliv">
                                        <input type="hidden" name="delivery_date" value="<?= date('Y-m-d'); ?>">
                                        <input type="hidden" name="kurir_id" value="<?= $data_kurir['id'] ?>">
                                        <div class="form-group mb-3">
                                            <select name="pickup_id" class="form-control select2bs4 rounded-sm fs-13" required>
                                                <option value="">KODE RESI</option>
                                                <?php foreach ($query_pickup as $row_pick) { ?>
                                                    <option value="<?= $row_pick['id'] ?>"><?= $row_pick['resi_code'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group mb-0">
                                            <button type="submit" id="submit" class="btn py-2 text-semibold btn-warning rounded-sm border-0 ls3 fs-12 hover px-4">Add</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-primary rounded-md" style="border-top: 4px solid #263D57;">
                                <div class="card-body p-3">
                                    <button class="btn btn-warning btn-block ls-1 text-semibold text-uppercase rounded-pill">DATA DELIVERY</button>
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
                                                <tr class="bg-transparent bg-gray text-white lh-3 text-nowrap text-uppercase fs-12 ">
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">No</th>
                                                    <th class="py-0 lh-2 text-center" style="vertical-align: middle !important;">Kurir Pick Up</th>
                                                    <th class="py-0 lh-2 text-center" style="vertical-align: middle !important;">Kurir Delivery</th>
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
                                                        <td colspan="10" class="text-center fs-12">Record Not Found</td>
                                                    </tr>';
                                                } else {
                                                    foreach ($sql_data as $rows) {
                                                        $price              = $rows['price'];
                                                        $shiping_cost       = $rows['shiping_cost'];
                                                        $status_delivery    = $rows['status_delivery'];
                                                ?>
                                                        <tr class="fs-13 text-dark hover-light">
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $no_urut++ . '.'; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $rows['kurir_pick_up'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $rows['kurir_delivery'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $rows['resi_code'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-uppercase"><?= $rows['cs_name'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-left"><a href="https://wa.me/<?= $rows['seller_phone_no'] ?>" target="_blank" rel="noopener noreferrer"><?= $rows['seller_phone_no'] ?></a></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $price; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $shiping_cost; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap text-uppercase"><?= $status_delivery?></td>
                                                        </tr>
                                                    <?php } ?>
                                                    <!-- Hidden form for AJAX -->
                                                    <form id="form-update-pickup" style="display:none;">
                                                        <input type="hidden" name="id" id="delivery_id">
                                                        <input type="hidden" name="status_delivery" id="status_delivery">
                                                    </form>
                                                    <!-- End Hidden form -->
                                                <?php } ?>
                                                <?php if ($all_data > 0) { ?>
                                                    <tr class="bg-light text-bold">
                                                        <td colspan="6" class="text-right py-2 fs-13">TOTAL:</td>
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