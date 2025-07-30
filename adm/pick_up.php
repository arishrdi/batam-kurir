<!-- Load Dependency CSS -->
<?php include 'theme/main_header.php'; ?>
<!-- Load Dependency CSS -->

<body class="hold-transition layout-top-nav bg-light-gray">
    <div class="wrapper">
        <!-- Load Nav Header -->
        <?php
        include 'theme/header.php';

        /* Select Kurir */
        $query_kurir    = mysqli_query($con, "SELECT * FROM mst_kurir WHERE NOT kurir_name='Administrator' AND is_validate=1 ORDER BY kurir_name ASC");
        /* Select Kurir */

        /* Query Data */
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
            WHERE 1=1";
        /* Jika Pencarian Aktif */
        if ($_GET['kurir'] ?? "" != "") {
            $kurir_id   = $_GET['kurir'];
            $query_data = $query_data . " AND dlv_pickup.kurir_id='$kurir_id'";

            $cek_kurir  = mysqli_query($con, "SELECT * FROM mst_kurir WHERE id=$kurir_id");
            $row_kurir  = mysqli_fetch_assoc($cek_kurir);
            $kode_kurir = getInitials($row_kurir['kurir_name']) . $row_kurir['id'];
        } else {
            $kurir_id   = '';
            $kode_kurir = '';
        }

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

        /* Menampilkan Data */
        $sql_data       = mysqli_query($con, "$query_data ORDER BY mst_kurir.kurir_name ASC, dlv_pickup.id ASC");
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

        /* Cancel Table Data - Current Month */
        $current_month = date('Y-m');
        $query_cancel = "SELECT 
                dlv_pickup.id AS pickup_id,
                dlv_pickup.pickup_date,
                dlv_pickup.resi_code,
                dlv_pickup.kurir_id,
                mst_kurir.kurir_name AS pickup_kurir_name,
                CASE
                    WHEN trx_delivery.kurir_id != '' THEN (SELECT kurir_name FROM mst_kurir WHERE id=trx_delivery.kurir_id)
                    ELSE '-'
                END AS delivery_kurir_name,
                dlv_pickup.cs_name,
                CONCAT('+', dlv_pickup.seller_phone_no) AS seller_phone_no,
                dlv_pickup.price,
                dlv_pickup.shiping_cost,
                trx_delivery.status_delivery,
                dlv_pickup.date_created
            FROM dlv_pickup 
                JOIN mst_kurir ON mst_kurir.id=dlv_pickup.kurir_id
                JOIN trx_delivery ON trx_delivery.pickup_id = dlv_pickup.id 
                    AND trx_delivery.id = (SELECT MAX(id) FROM trx_delivery t2 WHERE t2.pickup_id = dlv_pickup.id)
            WHERE trx_delivery.status_delivery='CANCEL' 
                AND DATE_FORMAT(trx_delivery.delivery_date, '%Y-%m') = '$current_month'";

        /* Apply kurir filter to cancel table based on delivery kurir if active */
        if ($_GET['kurir'] ?? "" != "") {
            $query_cancel = $query_cancel . " AND trx_delivery.kurir_id='$kurir_id'";
        }

        $sql_cancel_data = mysqli_query($con, "$query_cancel ORDER BY delivery_kurir_name ASC, dlv_pickup.id ASC");
        $cancel_data_count = mysqli_num_rows($sql_cancel_data);
        $cancel_no_urut = 1;

        /* Calculate Cancel Totals */
        $cancel_total_price = 0;
        $cancel_total_shipping = 0;
        $cancel_temp_data = [];
        while ($cancel_row = mysqli_fetch_assoc($sql_cancel_data)) {
            $cancel_temp_data[] = $cancel_row;
            $cancel_total_price += $cancel_row['price'];
            $cancel_total_shipping += $cancel_row['shiping_cost'];
        }
        $sql_cancel_data = $cancel_temp_data;
        ?>
        <!-- Load Nav Header  -->

        <div class="content-wrapper bg-transparent" style="margin-top: 100px;">
            <!-- Main content -->
            <div class="content">
                <div class="container-fluid px-4 mt-4">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="card rounded-md">
                                <div class="card-body py-2 text-semibold">
                                    <div class="row">
                                        <div class="col-md-1 py-1">Kurir:</div>
                                        <div class="col-md-3 py-1">
                                            <select onchange="changekurir(this)" class="form-control rounded-sm select2bs4" required>
                                                <option <?= $kurir_id == '' ? 'selected' : ''; ?> value=""> PILIH KURIR </option>
                                                <option <?= $kurir_id == '1' ? 'selected' : ''; ?> value="1">ADMINISTRATOR</option>
                                                <?php foreach ($query_kurir as $val_kurir) { ?>
                                                    <option <?= $kurir_id == $val_kurir['id'] ? 'selected' : ''; ?> value="<?= $val_kurir['id'] ?>"><?= strtoupper($val_kurir['kurir_name']) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card card-primary rounded-md" style="border-top: 4px solid #263D57;">
                                <div class="card-body p-3">
                                    <button class="btn btn-warning btn-block ls-1 text-semibold text-uppercase rounded-pill">DATA PICK UP</button>
                                    <a href="export/pickup.php?cari=<?= $pencarian ?>&date=<?= $date ?>&kurir=<?= $kurir_id ?>" class="btn btn-sm btn-success border-0 rounded-sm px-4 mt-3 mb-0 hover float-right"><i class="fas fa-file-excel pr-2"></i>Export</a>
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
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">ID</th>
                                                    <th class="py-0 lh-2 text-center" style="vertical-align: middle !important;">Kurir Pick Up</th>
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kode Resi</th>
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Nama CS</th>
                                                    <th class="py-0 lh-5 text-left" style="vertical-align: middle !important;">No Hp Seller</th>
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Harga</th>
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Ongkir</th>
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Keterangan</th>
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Tools</th>
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
                                                        $kode_kurir             = ($rows['kurir_name'] == 'Administrator') ? 'ADM' : getInitials($rows['kurir_name']) . $rows['kurir_id'];
                                                        $price                  = $rows['price'];
                                                        $shiping_cost           = $rows['shiping_cost'];

                                                ?>
                                                        <tr class="fs-13 text-dark hover-light text-nowrap">
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $no_urut++ . '.'; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $rows['pickup_id'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $rows['kurir_name'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-uppercase"><?= $rows['resi_code'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-uppercase"><?= $rows['cs_name'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-left"><a href="https://wa.me/<?= $rows['seller_phone_no'] ?>" target="_blank" rel="noopener noreferrer"><?= $rows['seller_phone_no'] ?></a></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $price; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap"><?= $shiping_cost; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap text-uppercase"><?= $rows['status_pickup']; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center text-nowrap text-uppercase">
                                                                <a class="btn btn-sm rounded-0 border-0 hover_text" data-toggle="dropdown"><i class="fas fa-cogs"></i></a>
                                                                <div class="dropdown-menu border-0 rounded-0 elevation-2 py-0 p-0" role="menu">
                                                                    <a class="dropdown-item hover-light text-xs rounded-0 py-2" href="#" onclick="
                                                                    $('#form-insert').hide();
                                                                    $('#form-edit').show();
                                                                    $('#pickup_id').val(<?= $rows['pickup_id'] ?>);
                                                                    $('#cs_name').val('<?= $rows['cs_name'] ?>');
                                                                    $('#seller_phone_no').val('<?= substr($rows['seller_phone_no'], 3) ?>');
                                                                    $('#shiping_cost').val(<?= $rows['shiping_cost'] ?>);
                                                                    $('#price_2').val(<?= $price ?>);
                                                                    $('select[name=kurir_id]').val(<?= $rows['kurir_id'] ?>).trigger('change');
                                                                    "><i class="fas fa-edit pr-2"></i>Edit Record</a>
                                                                    <div class="dropdown-divider my-0 mx-2"></div>
                                                                    <a class="dropdown-item hover-light text-xs rounded-0 py-2" href="#" onclick="
                                                                        Toast_Confirm.fire({
                                                                        title: 'Apakah anda yakin...?', 
                                                                        text: 'Record akan dihapus secara permanen', 
                                                                        icon: 'warning',
                                                                        showCancelButton: true,
                                                                        confirmButtonText: 'Ya, Hapus',
                                                                        showCancelButton: true,
                                                                        cancelButtonText: 'Batal',
                                                                        customClass: {
                                                                            popup: 'border-0 elevation-2 rounded-md pl-4 pr-0',
                                                                            confirmButton: 'btn btn-sm btn-light bg-success py-1 fs-12 shadow-none border-0 rounded-sm mb-3 hover-light ml-3 mr-1 px-3', // Add your custom CSS classes here
                                                                            cancelButton: 'btn btn-sm btn-light bg-danger py-1 fs-12 shadow-none rounded-sm mb-3 hover-light px-3' // Add your custom CSS classes here
                                                                        }
                                                                        }).then((result) => {
                                                                            if (result.isConfirmed) {
                                                                                var id  = <?= $rows['pickup_id']; ?>;
                                                                                $.ajax({
                                                                                    type: 'POST',
                                                                                    url: 'proses/delete/pickup.php',
                                                                                    data: {id: id},
                                                                                    success: function(response) {
                                                                                        if(response == 'Y') {
                                                                                            Toast.fire({   
                                                                                                icon: 'success',
                                                                                                title: 'Delete Success',
                                                                                                text: 'Record berhasil dihapus',
                                                                                            });
                                                                                        }else if(response == 'W') {
                                                                                            Toast.fire({   
                                                                                                icon: 'warning',
                                                                                                title: 'Erorr 404',
                                                                                                text: 'Record Not Found',
                                                                                            });
                                                                                        }else{
                                                                                            Toast.fire({   
                                                                                                icon: 'error',
                                                                                                title: 'Delete Failed',
                                                                                                text: 'Record Gagal dihapus',
                                                                                            });
                                                                                        }
                                                                                        setTimeout(function(){
                                                                                            window.location.href = window.location.pathname;
                                                                                        }, 1500);
                                                                                    }
                                                                                })
                                                                            }
                                                                        })"><i class="fas fa-eraser pr-2"></i>Hapus Record</a>
                                                                </div>
                                                            </td>
                                                        </tr>

                                                <?php }
                                                } ?>
                                                <?php if ($all_data > 0) { ?>
                                                    <tr class="bg-light text-bold">
                                                        <td colspan="5"></td>
                                                        <td class="bg-gray text-right py-2 fs-13">TOTAL:</td>
                                                        <td class="bg-gray text-center py-2 fs-13"><?= $total_price ?></td>
                                                        <td class="bg-gray text-center py-2 fs-13"><?= $total_shipping ?></td>
                                                        <td class="bg-gray text-center py-2 fs-13"><?= $total_price - $total_shipping ?></td>
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

                            <!-- Cancel Table for Current Month -->
                            <div class="card card-danger rounded-md mt-3" style="border-top: 4px solid #DC3545;">
                                <div class="card-body p-3">
                                    <button class="btn btn-danger btn-block ls-1 text-semibold text-uppercase rounded-pill">TABEL CANCEL - <?= ['', 'JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'][date('n')] . ' ' . date('Y') ?></button>
                                </div>


                                <div class="card-body pt-1">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless table-striped table-border-dark table-hover w-100 mb-0 pb-0">
                                            <thead>
                                                <tr class="bg-transparent bg-danger text-white lh-3 text-nowrap text-uppercase fs-12">
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">No</th>
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">ID</th>
                                                    <th class="py-0 lh-2 text-center" style="vertical-align: middle !important;">Kurir Delivery</th>
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Kode Resi</th>
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Nama CS</th>
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">No HP Seller</th>
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Harga</th>
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Ongkir</th>
                                                    <th class="py-0 lh-5 text-center" style="vertical-align: middle !important;">Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if ($cancel_data_count <= 0) {
                                                    echo '<tr>
                                                        <td colspan="8" class="text-center fs-12">No Cancelled Records Found</td>
                                                    </tr>';
                                                } else {
                                                    foreach ($sql_cancel_data as $cancel_rows) {
                                                        $cancel_price = $cancel_rows['price'];
                                                        $cancel_shipping_cost = $cancel_rows['shiping_cost'];
                                                ?>
                                                        <tr class="fs-13 text-dark hover-light text-nowrap">
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $cancel_no_urut++ . '.'; ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $cancel_rows['pickup_id'] ?></td>
                                                            <td style="vertical-align: top;" class="py-2 lh-3 text-center"><?= $cancel_rows['delivery_kurir_name'] ?></td>
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
                                                        <td colspan="5"></td>
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
                                            <?= 'Total ' . $cancel_data_count . ' cancelled entries for ' . date('F Y') ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-primary rounded-md" id="form-edit" style="border-top: 4px solid #263D57;">
                                <div class="card-body p-3">
                                    <h6 class="text-bold text-center mb-3">Form Edit Pick Up</h6>
                                    <form id="update-pickup">
                                        <input type="hidden" id="payload" value="<?= '?cari=' . $pencarian . '&date=' . $date . '&kurir=' . $kurir_id;  ?>">
                                        <input type="hidden" name="pickup_id" id="pickup_id" value="" required>
                                        <div class="form-group mb-3">
                                            <select name="kurir_id" class="form-control rounded-sm select2bs4">
                                                <option value="">PILIH KURIR</option>
                                                <?php foreach ($query_kurir as $val_kurir) { ?>
                                                    <option value="<?= $val_kurir['id'] ?>"><?= strtoupper($val_kurir['kurir_name']) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <input type="text" name="cs_name" id="cs_name" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-sm text-semibold" maxlength="100" placeholder="Nama CS" autofocus required>
                                        </div>
                                        <div class="form-group mb-3" id="input-3">
                                            <div class="input-group rounded-sm">
                                                <div class="input-group-text border-1 bg-white border-left text-semibold rounded-left-sm fs-13 border-right-0">
                                                    +62
                                                </div>
                                                <input type="text" name="seller_phone_no" id="seller_phone_no" class="form-control ls1 fs-13 px-2 h-50 py-2 rounded-right-sm text-semibold border-right phone_number" placeholder="No HP Seller" autofocus required>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <div class="input-group rounded-sm">
                                                <input type="text" name="price" id="price_2" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-left-sm text-semibold" min="0" placeholder="Harga" autofocus required>
                                                <!--<div class="input-group-text border-1 bg-white border-left text-semibold rounded-right-sm fs-13 px-3 border-left-0">-->
                                                <!--    K-->
                                                <!--</div>-->
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <div class="input-group rounded-sm">
                                                <input type="text" name="shiping_cost" id="shiping_cost" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-left-sm text-semibold" min="0" placeholder="Ongkir" autofocus required>
                                                <!--<div class="input-group-text border-1 bg-white border-left text-semibold rounded-right-sm fs-13 px-3 border-left-0">-->
                                                <!--    K-->
                                                <!--</div>-->
                                            </div>
                                        </div>
                                        <div class="form-group mb-0">
                                            <button type="submit" id="submit_edit" class="btn py-2 text-semibold btn-warning rounded-sm border-0 ls3 fs-12 hover px-4">Save Change</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="card card-primary rounded-md" id="form-insert" style="border-top: 4px solid #263D57;">
                                <div class="card-body p-3">
                                    <h6 class="text-bold text-center mb-3">Form Pick Up</h6>
                                    <form enctype="multipart/form-data" id="insert-pickup">
                                        <input type="hidden" name="pickup_date" value="<?= date('Y-m-d'); ?>">
                                        <!-- <input type="hidden" name="kurir_id" value="1" required> -->
                                        <div class="form-group mb-3">
                                            <div class="input-group rounded-sm">
                                                <input type="text" disabled class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-left-sm text-semibold border-right-0" value="<?= daydate_id(date('Y-m-d')) ?>">
                                                <div class="input-group-text text-semibold rounded-right-sm fs-13 border-left-0">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <select name="kurir_id" class="form-control rounded-sm select2bs4" required>
                                                <option value="">PILIH KURIR</option>
                                                <?php foreach ($query_kurir as $val_kurir) { ?>
                                                    <option value="<?= $val_kurir['id'] ?>"><?= strtoupper($val_kurir['kurir_name']) ?></option>
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
                                                <input type="number" name="seller_phone_no" class="form-control ls1 fs-13 px-2 h-50 py-2 rounded-right-sm text-semibold border-right phone_number" placeholder="No HP Seller" autofocus required>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <div class="input-group rounded-sm">
                                                <input type="text" name="price" id="price" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-left-sm text-semibold" min="1" placeholder="Harga" autofocus required>
                                                <!--<div class="input-group-text border-1 bg-white border-left text-semibold rounded-right-sm fs-13 px-3 border-left-0">-->
                                                <!--    K-->
                                                <!--</div>-->
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <div class="input-group rounded-sm">
                                                <input type="text" name="shiping_cost" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-left-sm text-semibold" min="1" placeholder="Ongkir" autofocus required>
                                                <!--<div class="input-group-text border-1 bg-white border-left text-semibold rounded-right-sm fs-13 px-3 border-left-0">-->
                                                <!--    K-->
                                                <!--</div>-->
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
        $(document).ready(function() {
            $('#form-edit').hide();


            $('#date_search').change(function() {
                var date = $(this).val();
                var currentURL = window.location.href;
                var url = new URL(currentURL);
                url.searchParams.set('date', date);
                window.location.href = url.toString();
            })
        });
    </script>
</body>

</html>