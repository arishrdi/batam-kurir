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
            delivery_with_sequence.pickup_id,
            delivery_with_sequence.delivery_id,
            delivery_with_sequence.pickup_date,
            delivery_with_sequence.delivery_date,
            delivery_with_sequence.kurir_pick_up_id,
            delivery_with_sequence.kurir_pick_up,
            delivery_with_sequence.kurir_delivery_id,
            delivery_with_sequence.kurir_delivery,
            delivery_with_sequence.resi_code,
            delivery_with_sequence.cs_name,
            delivery_with_sequence.seller_phone_no,
            delivery_with_sequence.price,
            delivery_with_sequence.shiping_cost,
            delivery_with_sequence.status_pickup,
            delivery_with_sequence.status_delivery,
            delivery_with_sequence.daily_sequence_id
        FROM (
            SELECT
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
                trx_delivery.status_delivery,
                ROW_NUMBER() OVER (PARTITION BY dlv_pickup.pickup_date, dlv_pickup.kurir_id ORDER BY dlv_pickup.id ASC) AS daily_sequence_id
            FROM dlv_pickup 
                JOIN mst_kurir ON mst_kurir.id=dlv_pickup.kurir_id
                LEFT JOIN trx_delivery ON trx_delivery.pickup_id = dlv_pickup.id 
                    AND trx_delivery.id = (SELECT MAX(id) FROM trx_delivery t2 WHERE t2.pickup_id = dlv_pickup.id)
        ) AS delivery_with_sequence
        WHERE delivery_with_sequence.kurir_delivery_id='{$data_kurir['id']}' AND 1=1";

        /* Jika Pencarian Aktif */
        if ($_GET['cari'] ?? "" != "") {
            $pencarian  = $_GET['cari'];
            $query_data = $query_data . " AND (delivery_with_sequence.resi_code LIKE '%$pencarian%'
                OR delivery_with_sequence.cs_name LIKE '%$pencarian%'
                OR delivery_with_sequence.seller_phone_no LIKE '%$pencarian%'
                OR delivery_with_sequence.status_delivery='$pencarian')";
        } else {
            $pencarian  = '';
        }

        if ($_GET['date'] ?? "" != "") {
            $date       = $_GET['date'];
            $query_data = $query_data . " AND delivery_with_sequence.delivery_date='$date'";
        } else {
            $date       = date('Y-m-d');
            $query_data = $query_data . " AND delivery_with_sequence.delivery_date='$date'";
        }
        /* Jika Pencarian Aktif */

        // Menampilkan Data
        $sql_data       = mysqli_query($con, "$query_data ORDER BY delivery_with_sequence.pickup_id ASC, delivery_with_sequence.kurir_pick_up ASC");
        $all_data       = mysqli_num_rows($sql_data);
        $no_urut        = 1;

        /* Calculate Main Table Totals */
        $main_total_price = 0;
        $main_total_shipping = 0;
        $pending_summary_total = 0;
        $cancel_summary_total = 0;
        $main_temp_data = [];
        while ($main_row = mysqli_fetch_assoc($sql_data)) {
            $main_temp_data[] = $main_row;
            $main_total_price += $main_row['price'];
            $main_total_shipping += $main_row['shiping_cost'];
            
            // Calculate totals by status for summary
            if ($main_row['status_delivery'] == 'PENDING') {
                $pending_summary_total += $main_row['price'];
            } elseif ($main_row['status_delivery'] == 'CANCEL') {
                $cancel_summary_total += $main_row['price'];
            }
        }
        $sql_data = $main_temp_data;
        $total_delivery_summary = $main_total_price - $pending_summary_total - $cancel_summary_total;

        /* Extract Pending Data from main result set for consistent ID numbering */
        $sql_pending_data = [];
        $pending_total_price = 0;
        $pending_total_shipping = 0;
        foreach ($sql_data as $row) {
            if ($row['status_delivery'] == 'PENDING') {
                $sql_pending_data[] = $row;
                $pending_total_price += $row['price'];
                $pending_total_shipping += $row['shiping_cost'];
            }
        }
        $pending_data_count = count($sql_pending_data);
        $pending_no_urut = 1;

        /* Extract Cancel Data from main result set for consistent ID numbering */
        $sql_cancel_data = [];
        $cancel_total_price = 0;
        $cancel_total_shipping = 0;
        foreach ($sql_data as $row) {
            if ($row['status_delivery'] == 'CANCEL') {
                $sql_cancel_data[] = $row;
                $cancel_total_price += $row['price'];
                $cancel_total_shipping += $row['shiping_cost'];
            }
        }
        $cancel_data_count = count($sql_cancel_data);
        $cancel_no_urut = 1;
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
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">ID</th>
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
                                                        <td colspan="9" class="text-center fs-12">Record Not Found</td>
                                                    </tr>';
                                                } else {
                                                    foreach ($sql_data as $rows) {
                                                        $price              = $rows['price'];
                                                        $shiping_cost       = $rows['shiping_cost'];
                                                        $status_delivery    = $rows['status_delivery'];
                                                ?>
                                                        <tr class="fs-13 text-dark hover-light">
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $no_urut++ . '.'; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><strong><?= $rows['daily_sequence_id'] ?></strong></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $rows['kurir_pick_up'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $rows['kurir_delivery'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $rows['resi_code'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-uppercase"><?= $rows['cs_name'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-left"><a href="https://wa.me/<?= $rows['seller_phone_no'] ?>" target="_blank" rel="noopener noreferrer"><?= $rows['seller_phone_no'] ?></a></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $price; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $shiping_cost; ?></td>
                                                            <!-- <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap text-uppercase"><?= $status_delivery?></td> -->
                                                             <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap text-uppercase">
                                                                <select name="status_delivery" id="status_delivery<?= $rows['delivery_id']; ?>" <?= ($status_delivery == 'SUKSES') ? 'disabled' : (($status_delivery == 'CANCEL') ? 'disabled' : '') ?> onchange="
                                                                    var status_delivery = $(this).val();
                                                                    var date_search = $('#date_search').val();
                                                                    $('#delivery_id').val(<?= $rows['delivery_id']; ?>);
                                                                    $('#status_delivery').val(status_delivery);
                                                                    if(status_delivery == '<?= $status_delivery ?>'){ // Kosongkan Form
                                                                        $('#delivery_id').val(null);
                                                                        $('#status_delivery').val(null);
                                                                    }else{ // Update Status Delivery
                                                                        var formData = $('#form-update-pickup').serialize(); // Mengambil data formulir
                                                                        $.ajax({
                                                                            url: 'proses/update/delivery.php',
                                                                            type: 'POST', 
                                                                            data: formData,
                                                                            success: function(data) {
                                                                                if(data == 'Y') {
                                                                                    Toast.fire({  
                                                                                        icon: 'success',
                                                                                        title: 'Change Status Success', 
                                                                                        text: 'Status Delivery berhasil diperbarui',
                                                                                    });
                                                                                }else {
                                                                                    Toast.fire({
                                                                                        icon: 'error',
                                                                                        title: 'Change Status Failed',
                                                                                        text: 'Tidak dapat memperbarui Status Delivery',
                                                                                    });
                                                                                }
                                                                                setTimeout(function(){
                                                                                    // window.location.href='delivery.php?date='+date_search;
                                                                                    window.location.reload();
                                                                                }, 1500);
                                                                            }
                                                                        })
                                                                    }" class="form-control custom-select rounded-sm fs-13 py-1 border-0 h-75" required>
                                                                    <option <?= ($status_delivery == 'PROSES') ? 'selected' : '' ?> value="PROSES">PROSES</option>
                                                                    <option <?= ($status_delivery == 'PENDING') ? 'selected' : '' ?> value="PENDING">PENDING</option>
                                                                    <option <?= ($status_delivery == 'SUKSES') ? 'selected' : '' ?> value="SUKSES">SUKSES</option>
                                                                    <option <?= ($status_delivery == 'CANCEL') ? 'selected' : '' ?> value="CANCEL">CANCEL</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    <!-- Hidden form for AJAX -->
                                                    <form id="form-update-pickup" style="display:none;">
                                                        <input type="hidden" name="id" id="delivery_id">
                                                        <input type="hidden" name="status_delivery" id="status_delivery">
                                                    </form>
                                                    <!-- End Hidden form -->
                                                <?php } ?>
                                                <!-- Total Row for Main Delivery Table -->
                                                <?php if ($all_data > 0) { ?>
                                                <tr class="bg-light text-bold">
                                                    <td colspan="5" class="bg-gray text-center py-2 fs-13">TOTAL</td>
                                                    <td colspan="2" class="bg-gray text-right py-2 fs-13"></td>
                                                    <td class="bg-gray text-center py-2 fs-13"><?= $main_total_price ?></td>
                                                    <td class="bg-gray text-center py-2 fs-13"><?= $main_total_shipping ?></td>
                                                    <td class="bg-gray text-center py-2 fs-13"><?= $main_total_price - $main_total_shipping ?></td>
                                                </tr>
                                                <tr class="bg-light text-bold">
                                                    <td colspan="4" class="bg-gray text-center py-2 fs-13">PENDING</td>
                                                    <td class="bg-gray text-center py-2 fs-13"><?= $pending_summary_total ?></td>
                                                </tr>
                                                <tr class="bg-light text-bold">
                                                    <td colspan="4" class="bg-gray text-center py-2 fs-13">CANCEL</td>
                                                    <td class="bg-gray text-center py-2 fs-13"><?= $cancel_summary_total ?></td>
                                                </tr>
                                                <tr class="bg-light text-bold">
                                                    <td colspan="4" class="bg-gray text-center py-2 fs-13">TOTAL DELIVERY</td>
                                                    <td class="bg-gray text-center py-2 fs-13"><?= $total_delivery_summary ?></td>
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

                        <!-- Pending Table for Today -->
                        <div class="col-md-12">
                            <div class="card card-warning rounded-md mt-3" style="border-top: 4px solid #FFC107;">
                                <div class="card-body p-3">
                                    <button class="btn btn-warning btn-block ls-1 text-semibold text-uppercase rounded-pill">TABEL PENDING HARI INI</button>
                                </div>

                                <div class="card-body pt-1">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless table-striped table-border-dark table-hover w-100 mb-0 pb-0">
                                            <thead>
                                                <tr class="bg-transparent bg-warning text-white lh-3 text-nowrap text-uppercase fs-12">
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">No</th>
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">ID</th>
                                                    <th class="py-0 lh-2 text-center" style="vertical-align: middle !important;">Kurir Pickup</th>
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
                                                if ($pending_data_count <= 0) {
                                                    echo '<tr>
                                                        <td colspan="9" class="text-center fs-12">No Pending Records Found</td>
                                                    </tr>';
                                                } else {
                                                    foreach ($sql_pending_data as $pending_rows) {
                                                        $pending_price = $pending_rows['price'];
                                                        $pending_shipping_cost = $pending_rows['shiping_cost'];
                                                ?>
                                                        <tr class="fs-13 text-dark hover-light text-nowrap">
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $pending_no_urut++ . '.'; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><strong><?= $pending_rows['daily_sequence_id'] ?></strong></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $pending_rows['kurir_pick_up'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $pending_rows['kurir_delivery'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-uppercase"><?= $pending_rows['resi_code'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-uppercase"><?= $pending_rows['cs_name'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-left"><a href="https://wa.me/<?= $pending_rows['seller_phone_no'] ?>" target="_blank" rel="noopener noreferrer"><?= $pending_rows['seller_phone_no'] ?></a></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $pending_price; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $pending_shipping_cost; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap text-uppercase"><?= $pending_rows['status_delivery']; ?></td>
                                                        </tr>
                                                <?php }
                                                } ?>
                                                <?php if ($pending_data_count > 0) { ?>
                                                    <tr class="bg-light text-bold">
                                                        <td colspan="6"></td>
                                                        <td class="bg-gray text-right py-2 fs-13">TOTAL PENDING:</td>
                                                        <td class="bg-gray text-center py-2 fs-13"><?= $pending_total_price ?></td>
                                                        <td class="bg-gray text-center py-2 fs-13"><?= $pending_total_shipping ?></td>
                                                        <td class="bg-gray text-center py-2 fs-13"><?= $pending_total_price - $pending_total_shipping ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php if ($pending_data_count > 0) { ?>
                                        <div class="d-inline-block float-left mt-3 fs-13">
                                            <?= 'Total ' . $pending_data_count . ' pending entries for today' ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <!-- Cancel Table for Today -->
                        <div class="col-md-12">
                            <div class="card card-danger rounded-md mt-3" style="border-top: 4px solid #DC3545;">
                                <div class="card-body p-3">
                                    <button class="btn btn-danger btn-block ls-1 text-semibold text-uppercase rounded-pill">TABEL CANCEL HARI INI</button>
                                </div>

                                <div class="card-body pt-1">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless table-striped table-border-dark table-hover w-100 mb-0 pb-0">
                                            <thead>
                                                <tr class="bg-transparent bg-danger text-white lh-3 text-nowrap text-uppercase fs-12">
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">No</th>
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">ID</th>
                                                    <th class="py-0 lh-2 text-center" style="vertical-align: middle !important;">Kurir Pickup</th>
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
                                                if ($cancel_data_count <= 0) {
                                                    echo '<tr>
                                                        <td colspan="9" class="text-center fs-12">No Cancel Records Found</td>
                                                    </tr>';
                                                } else {
                                                    foreach ($sql_cancel_data as $cancel_rows) {
                                                        $cancel_price = $cancel_rows['price'];
                                                        $cancel_shipping_cost = $cancel_rows['shiping_cost'];
                                                ?>
                                                        <tr class="fs-13 text-dark hover-light text-nowrap">
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $cancel_no_urut++ . '.'; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><strong><?= $cancel_rows['daily_sequence_id'] ?></strong></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $cancel_rows['kurir_pick_up'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $cancel_rows['kurir_delivery'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-uppercase"><?= $cancel_rows['resi_code'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-uppercase"><?= $cancel_rows['cs_name'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-left"><a href="https://wa.me/<?= $cancel_rows['seller_phone_no'] ?>" target="_blank" rel="noopener noreferrer"><?= $cancel_rows['seller_phone_no'] ?></a></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $cancel_price; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $cancel_shipping_cost; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap text-uppercase"><?= $cancel_rows['status_delivery']; ?></td>
                                                        </tr>
                                                <?php }
                                                } ?>
                                                <?php if ($cancel_data_count > 0) { ?>
                                                    <tr class="bg-light text-bold">
                                                        <td colspan="6"></td>
                                                        <td class="bg-gray text-right py-2 fs-13">TOTAL CANCEL:</td>
                                                        <td class="bg-gray text-center py-2 fs-13"><?= $cancel_total_price ?></td>
                                                        <td class="bg-gray text-center py-2 fs-13"><?= $cancel_total_shipping ?></td>
                                                        <td class="bg-gray text-center py-2 fs-13"><?= $cancel_total_price - $cancel_total_shipping ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php if ($cancel_data_count > 0) { ?>
                                        <div class="d-inline-block float-left mt-3 fs-13">
                                            <?= 'Total ' . $cancel_data_count . ' cancelled entries for today' ?>
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

            // Add validation for delivery form
            $('#insert-deliv').on('submit', function(e) {
                var pickupId = $('select[name="pickup_id"]').val();
                
                if (!pickupId || pickupId === '') {
                    e.preventDefault();
                    Toast.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Silakan pilih kode resi terlebih dahulu'
                    });
                    return false;
                }
            });
        });
    </script>
</body>

</html>