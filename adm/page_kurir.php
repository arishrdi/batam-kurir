<!-- Load Dependency CSS -->
<?php include 'theme/main_header.php'; ?>
<!-- Load Dependency CSS -->

<body class="hold-transition layout-top-nav bg-light-gray">
    <div class="wrapper">
        <!-- Load Nav Header -->
        <?php
        include 'theme/header.php';


        // Base Query
        $base_query = "SELECT mst_kurir.*, mst_user.password FROM mst_kurir 
        JOIN mst_user ON mst_user.id = mst_kurir.user_id
        WHERE NOT mst_kurir.kurir_name='Administrator' AND mst_kurir.is_validate=1";

        // Search parameter
        $pencarian = $_GET['cari'] ?? '';
        $search_condition = "";
        if ($pencarian != "") {
            $search_condition = " AND (mst_kurir.kurir_name LIKE '%$pencarian%' OR mst_kurir.`status` LIKE '%$pencarian%')";
        }

        // Query for ALL kurirs (both active and inactive)
        $query_all = $base_query . $search_condition;
        $query_result = mysqli_query($con, $query_all);
        $all_data = mysqli_num_rows($query_result);
        $sql_data = mysqli_query($con, "$query_all ORDER BY mst_kurir.is_active DESC, mst_user.id DESC");
        $no_urut = 1;

        // Function to generate table rows
        function generateTableRows($sql_data, $no_urut_start, $all_data) {
            if ($all_data <= 0) {
                return '<tr><td colspan="10" class="text-center fs-13">Record Not Found</td></tr>';
            }
            
            $output = '';
            $no_urut = $no_urut_start;
            
            foreach ($sql_data as $rows) {
                $status = $rows['status'];
                $active = $rows['is_active']; 
                
                $output .= '<tr class="fs-13 text-dark hover-light">';
                $output .= '<td style="vertical-align: top;" class="py-2 lh-3 text-center">' . $no_urut . '.</td>';
                $output .= '<td style="vertical-align: top;" class="py-2 lh-3 text-left">' . $rows['kurir_name'] . '</td>';
                $output .= '<td style="vertical-align: top;" class="py-2 lh-3 text-left">' . $rows['birdth_place'] . ', ' . date_id($rows['birdth_date']) . '</td>';
                $output .= '<td style="vertical-align: top;" class="py-2 lh-3 text-left">' . $rows['batam_address'] . '</td>';
                $output .= '<td style="vertical-align: top;" class="py-2 lh-3 text-left">+' . $rows['phone_number'] . '</td>';
                $output .= '<td style="vertical-align: top;" class="py-2 lh-3 text-left">' . $status . '</td>';
                $output .= '<td style="vertical-align: top;" class="py-2 lh-3 text-left">' . (($status == 'Menikah') ? $rows['partner_name'] : '-') . '</td>';
                $output .= '<td style="vertical-align: top;" class="py-2 lh-3 text-left">' . (($status == 'Menikah') ? '+' . $rows['phone_number_partner'] : '-') . '</td>';
                $output .= '<td style="vertical-align: top;" class="py-2 lh-3 text-left">' . (($status == 'Single') ? '+' . $rows['phone_number_family'] : '-') . '</td>';
                $output .= '<td style="vertical-align: middle; width: 300px;" class="py-2 lh-3 text-left">';
                $output .= '<div class="d-flex align-items-center" style="gap: 5px;">';
                // Status toggle button
                $output .= '<button onclick="';
                $output .= 'Toast_Confirm.fire({';
                $output .= 'title: \'' . (($active == 1) ? 'Non Aktifkan' : 'Aktifkan') . ' Kurir\', ';
                $output .= 'text: \'Apakah anda yakin...?\', ';
                $output .= 'icon: \'warning\',';
                $output .= 'showCancelButton: true,';
                $output .= 'confirmButtonText: \'Ya\',';
                $output .= 'showCancelButton: true,';
                $output .= 'cancelButtonText: \'Batal\',';
                $output .= 'customClass: {';
                $output .= 'popup: \'border-0 elevation-2 rounded-md pl-4 pr-0\',';
                $output .= 'confirmButton: \'btn btn-sm btn-light bg-success py-1 fs-12 shadow-none border-0 rounded-sm mb-3 hover-light ml-3 mr-1 px-3\',';
                $output .= 'cancelButton: \'btn btn-sm btn-light bg-danger py-1 fs-12 shadow-none rounded-sm mb-3 hover-light px-3\'';
                $output .= '}';
                $output .= '}).then((result) => {';
                $output .= 'if (result.isConfirmed) {';
                $output .= 'var id = ' . $rows['id'] . ';';
                $output .= '$.ajax({';
                $output .= 'type: \'POST\',';
                $output .= 'url: \'proses/aprovement/kurir_status.php\',';
                $output .= 'data: {id: id},';
                $output .= 'success: function(response) {';
                $output .= 'if(response == \'Y\') {';
                $output .= 'Toast.fire({';
                $output .= 'icon: \'success\',';
                $output .= 'title: \'' . (($active == 1) ? 'Non Aktifkan' : 'Aktifkan') . ' Success\',';
                $output .= 'text: \'Kurir berhasil di ' . (($active == 1) ? 'Non Aktifkan' : 'Aktifkan') . '\',';
                $output .= '});';
                $output .= '}else if(response == \'N\') {';
                $output .= 'Toast.fire({';
                $output .= 'icon: \'error\',';
                $output .= 'title: \'' . (($active == 1) ? 'Non Aktifkan' : 'Aktifkan') . ' Failed\',';
                $output .= 'text: \'Kurir tidak dapat di ' . (($active == 1) ? 'Non Aktifkan' : 'Aktifkan') . '\',';
                $output .= '});';
                $output .= '}else {';
                $output .= 'Toast.fire({';
                $output .= 'icon: \'warning\',';
                $output .= 'title: \'Erorr 404, Not Found\',';
                $output .= 'text: \'Record tidak ditemukan\',';
                $output .= '});';
                $output .= '}';
                $output .= 'setTimeout(function(){';
                $output .= 'window.location.href = window.location.pathname;';
                $output .= '}, 1500);';
                $output .= '}';
                $output .= '})';
                $output .= '}';
                $output .= '})';
                $output .= '" class="btn btn-sm btn-block fs-12 py-1 rounded-pill hover mb-1 d-flex align-items-center justify-content-center ' . (($active == 0) ? 'btn-danger' : 'btn-success') . '">' . (($active == 0) ? 'OFF<i class="fas fa-circle pl-1"></i>' : '<i class="fas fa-circle pr-1"></i>ON') . '</button>';
                // Edit button
                $output .= '<button class="btn btn-sm btn-block btn-light bg-orange text-white text-semibold border-0 fs-12 py-1 rounded-pill hover mb-1" data-toggle="modal" data-target="#edit' . $rows['id'] . '">EDIT</button>';
                // Delete button
                $output .= '<button onclick="';
                $output .= 'Toast_Confirm.fire({';
                $output .= 'title: \'Hapus Kurir\', ';
                $output .= 'text: \'Apakah anda yakin ingin menghapus kurir ini?\', ';
                $output .= 'icon: \'warning\',';
                $output .= 'showCancelButton: true,';
                $output .= 'confirmButtonText: \'Ya\',';
                $output .= 'cancelButtonText: \'Batal\',';
                $output .= 'customClass: {';
                $output .= 'popup: \'border-0 elevation-2 rounded-md pl-4 pr-0\',';
                $output .= 'confirmButton: \'btn btn-sm btn-light bg-success py-1 fs-12 shadow-none border-0 rounded-sm mb-3 hover-light ml-3 mr-1 px-3\',';
                $output .= 'cancelButton: \'btn btn-sm btn-light bg-danger py-1 fs-12 shadow-none rounded-sm mb-3 hover-light px-3\'';
                $output .= '}';
                $output .= '}).then((result) => {';
                $output .= 'if (result.isConfirmed) {';
                $output .= 'var id = ' . $rows['id'] . ';';
                $output .= '$.ajax({';
                $output .= 'type: \'POST\',';
                $output .= 'url: \'proses/delete/kurir.php\',';
                $output .= 'data: {id: id},';
                $output .= 'success: function(response) {';
                $output .= 'if(response == \'Y\') {';
                $output .= 'Toast.fire({';
                $output .= 'icon: \'success\',';
                $output .= 'title: \'Hapus Berhasil\',';
                $output .= 'text: \'Kurir berhasil dihapus\',';
                $output .= '});';
                $output .= '}else if(response == \'N\') {';
                $output .= 'Toast.fire({';
                $output .= 'icon: \'error\',';
                $output .= 'title: \'Hapus Gagal\',';
                $output .= 'text: \'Kurir tidak dapat dihapus\',';
                $output .= '});';
                $output .= '}else {';
                $output .= 'Toast.fire({';
                $output .= 'icon: \'warning\',';
                $output .= 'title: \'Error 404\',';
                $output .= 'text: \'Record tidak ditemukan\',';
                $output .= '});';
                $output .= '}';
                $output .= 'setTimeout(function(){';
                $output .= 'window.location.href = window.location.pathname;';
                $output .= '}, 1500);';
                $output .= '}';
                $output .= '})';
                $output .= '}';
                $output .= '})';
                $output .= '" class="btn btn-sm btn-block btn-danger fs-12 py-1 rounded-pill hover">DELETE</button>';
                $output .= '</div>';
                $output .= '</td>';
                $output .= '</tr>';
                
                // Add edit modal for each row
                $output .= generateModalEdit($rows, $no_urut, $status);
                
                $no_urut++;
            }
            
            return $output;
        }

        // Function to generate edit modal
        function generateModalEdit($rows, $no_urut, $status) {
            $modal = '<div class="modal fade" tabindex="-1" role="dialog" id="edit' . $rows['id'] . '">';
            $modal .= '<div class="modal-dialog modal-sm" role="document">';
            $modal .= '<div class="modal-content rounded-md">';
            $modal .= '<div class="modal-header pb-2 pt-2 px-3">';
            $modal .= '<h6 class="modal-title text-bold"><i class="fas fa-edit pr-2"></i>Update Record</h6>';
            $modal .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
            $modal .= '<span aria-hidden="true">&times;</span>';
            $modal .= '</button>';
            $modal .= '</div>';
            $modal .= '<div class="modal-body py-0">';
            $modal .= '<form action="proses/update/kurir.php" method="post">';
            $modal .= '<input type="hidden" name="id" value="' . $rows['id'] . '">';
            $modal .= '<div class="form-group mb-3">';
            $modal .= '<div class="input-group rounded-sm">';
            $modal .= '<div class="input-group-text border-1 bg-white border-left text-semibold rounded-left-sm fs-13 border-right-0">+62</div>';
            $modal .= '<input type="number" name="phone_number" value="' . substr($rows['phone_number'], 2) . '" class="form-control ls1 fs-13 px-2 h-50 py-2 rounded-right-sm text-semibold border-right" placeholder="No Telephone" autofocus required>';
            $modal .= '</div>';
            $modal .= '</div>';
            $modal .= '<div class="form-group mb-3">';
            $modal .= '<select name="status" id="status' . $no_urut . '" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-sm text-semibold custom-select">';
            $modal .= '<option ' . (($status == 'Single') ? 'selected' : '') . ' value="Single">Single</option>';
            $modal .= '<option ' . (($status == 'Menikah') ? 'selected' : '') . ' value="Menikah">Menikah</option>';
            $modal .= '</select>';
            $modal .= '</div>';
            $modal .= '<div class="form-group mb-3" id="input-1' . $no_urut . '">';
            $modal .= '<input type="text" name="partner_name" value="' . (($status == 'Menikah') ? $rows['partner_name'] : '') . '" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-sm text-semibold" id="inputan1' . $no_urut . '" placeholder="Nama Istri / Suami" autofocus required>';
            $modal .= '</div>';
            $modal .= '<div class="form-group mb-3" id="input-2' . $no_urut . '">';
            $modal .= '<div class="input-group rounded-sm">';
            $modal .= '<div class="input-group-text border-1 bg-white border-left text-semibold rounded-left-sm fs-13 border-right-0">+62</div>';
            $modal .= '<input type="number" name="phone_number_partner" value="' . (($status == 'Menikah') ? substr($rows['phone_number_partner'], 2) : '') . '" class="form-control ls1 fs-13 px-2 h-50 py-2 rounded-right-sm text-semibold border-right" id="inputan2' . $no_urut . '" placeholder="No Telephone Istri / Suami" autofocus required>';
            $modal .= '</div>';
            $modal .= '</div>';
            $modal .= '<div class="form-group mb-3" id="input-3' . $no_urut . '">';
            $modal .= '<div class="input-group rounded-sm">';
            $modal .= '<div class="input-group-text border-1 bg-white border-left text-semibold rounded-left-sm fs-13 border-right-0">+62</div>';
            $modal .= '<input type="number" name="phone_number_family" value="' . (($status == 'Single') ? substr($rows['phone_number_family'], 2) : '') . '" class="form-control ls1 fs-13 px-2 h-50 py-2 rounded-right-sm text-semibold border-right" id="inputan3' . $no_urut . '" placeholder="No Telephone Keluarga Aktif" autofocus required>';
            $modal .= '</div>';
            $modal .= '</div>';
            $modal .= '<div class="form-group mb-4">';
            $modal .= '<div class="input-group">';
            $modal .= '<input type="password" name="password" id="password' . $no_urut . '" value="' . $rows['password'] . '" placeholder="Password" value="" class="form-control ls3 fs-13 px-3 h-50 py-2 rounded-left-sm" placeholder="Password" autofocus required>';
            $modal .= '<div class="input-group-append border-0 bg-white">';
            $modal .= '<div class="input-group-text hover rounded-right-sm bg-white" id="show' . $no_urut . '">';
            $modal .= '<span class="fas fa-eye-slash text-sm"></span>';
            $modal .= '</div>';
            $modal .= '</div>';
            $modal .= '</div>';
            $modal .= '</div>';
            $modal .= '<div class="form-group mb-0">';
            $modal .= '<button type="button" class="btn btn-danger btn-sm py-2 fs-12 hover-light border-0 shadow-none float-left px-3 rounded-sm mb-4" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Close</button>';
            $modal .= '<button type="submit" class="btn btn-success btn-sm py-2 fs-12 hover-light border-0 shadow-none float-right px-3 rounded-sm mb-4" name="edit"><i class="fa fa-save mr-2"></i>Simpan</button>';
            $modal .= '</div>';
            $modal .= '</form>';
            $modal .= '</div>';
            $modal .= '</div>';
            $modal .= '</div>';
            $modal .= '</div>';
            
            return $modal;
        }


        ?>
        <!-- Load Nav Header  -->

        <div class="content-wrapper bg-transparent" style="margin-top: 100px;">
            <div class="content-header pb-0">
                <div class="container-fluid px-4">
                    <h1 class="m-0 fs-25 text-gray text-bold my-2">Master Data <i class="fas fa-chevron-right text-md px-2"></i> <span class="fs-20">Data Kurir</span></h1>
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid px-4">
                    <div class="card card-primary rounded-md" style="border-top: 4px solid #263D57;">
                        <div class="card-body p-3">
                            <!-- Tambah Button -->
                            <button class="btn btn-success btn-sm ls-1 text-semibold rounded-pill mb-2 float-right" data-toggle="modal" data-target="#tambahKurir">
                                <i class="fas fa-plus mr-1"></i> TAMBAH KURIR
                            </button>
                            <div class="clearfix"></div>
                            <!-- Tambah Button -->
                            
                            <!-- <div class="alert alert-info mb-2">
                                <i class="fas fa-info-circle mr-2"></i>Total Kurir: <strong><?= $all_data ?></strong>
                            </div> -->
                        </div>
                        <div class="card-body pt-1 pb-1">
                            <div class="float-right">
                                <input type="text" name="cari" maxlength="50" onkeyup="searchData(this)" onchange="search(this)" value="<?= $pencarian ?>" class="form-control px-3 my-auto rounded-0 fs-13 h-75 mb-2" placeholder="Search Data">
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="card-body pt-1">
                            <!-- MERGED KURIR TABLE -->
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless table-striped table-border-dark table-hover w-100 mb-0 pb-0">
                                    <thead>
                                        <tr class="bg-transparent bg-gray text-white lh-2 text-nowrap fs-13 text-uppercase">
                                            <th style="vertical-align: top!important;" class="py-2 text-center" width="4%">No</th>
                                            <th style="vertical-align: top!important;" class="py-2 text-left">Nama Kurir</th>
                                            <th style="vertical-align: top!important;" class="py-2 text-left" width="12%">TTL</th>
                                            <th style="vertical-align: top!important;" class="py-2 text-left" width="15%">Alamat Dibatam</th>
                                            <th style="vertical-align: top!important;" class="py-2 text-left" width="9%">No Telepon</th>
                                            <th style="vertical-align: top!important;" class="py-2 text-left" width="6%">Status</th>
                                            <th style="vertical-align: top!important;" class="py-2 text-left" width="10%">Nama<br>Istri/Suami </th>
                                            <th style="vertical-align: top!important;" class="py-2 text-left" width="9%">No Telepon<br>Istri/Suami </th>
                                            <th style="vertical-align: top!important;" class="py-2 text-left" width="9%">No Telepon<br>Keluarga Aktif </th>
                                            <th style="vertical-align: top!important;" class="py-2 text-left" width="10%">Tools</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?= generateTableRows($sql_data, $no_urut, $all_data); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main content -->
        </div>
    </div>

    <!-- Modal Tambah Kurir -->
    <div class="modal fade" tabindex="-1" role="dialog" id="tambahKurir">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content rounded-md">
                <div class="modal-header pb-2 pt-3 px-4">
                    <h5 class="modal-title text-bold"><i class="fas fa-plus pr-2"></i>Tambah Kurir Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-3 px-4">
                    <form enctype="multipart/form-data" id="form_tambah_kurir" action="proses/insert/register_kurir.php" method="post">
                        <div class="form-group mb-3">
                            <input type="text" name="kurir_name" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-sm text-semibold" maxlength="100" placeholder="Nama Kurir" autofocus required>
                        </div>
                        <div class="form-group mb-3">
                            <div class="input-group rounded-sm">
                                <input type="text" name="birdth_place" class="form-control ls1 fs-13 px-3 rounded-left-sm text-semibold" placeholder="Tempat Lahir" required>
                                <div class="input-group-append">
                                    <input type="date" name="birdth_date" max="<?= date('Y-m-d') ?>" class="form-control ls1 fs-13 pl-0 pr-2 border-right rounded-right-sm text-semibold" placeholder="Tanggal Lahir" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <textarea name="batam_address" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-sm text-semibold" rows="2" placeholder="Alamat Dibatam" required></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <div class="input-group rounded-sm">
                                <div class="input-group-text border-1 border-left text-semibold rounded-left-sm fs-13 border-right-0">
                                    +62
                                </div>
                                <input type="text" name="phone_number" class="form-control ls1 fs-13 px-2 h-50 py-2 rounded-right-sm text-semibold border-right phone_number" placeholder="No Telephone" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <select name="status" id="status_tambah" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-sm text-semibold custom-select">
                                <option selected value="Single">Single</option>
                                <option value="Menikah">Menikah</option>
                            </select>
                        </div>
                        <div class="form-group mb-3" id="input_tambah_1">
                            <input type="text" name="partner_name" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-sm text-semibold" id="inputan_tambah_1" placeholder="Nama Istri / Suami">
                        </div>
                        <div class="form-group mb-3" id="input_tambah_2">
                            <div class="input-group rounded-sm">
                                <div class="input-group-text border-1 border-left text-semibold rounded-left-sm fs-13 border-right-0">
                                    +62
                                </div>
                                <input type="text" name="phone_number_partner" class="form-control ls1 fs-13 px-2 h-50 py-2 rounded-right-sm text-semibold border-right phone_number" id="inputan_tambah_2" placeholder="No Telephone Istri / Suami">
                            </div>
                        </div>
                        <div class="form-group mb-3" id="input_tambah_3">
                            <div class="input-group rounded-sm">
                                <div class="input-group-text border-1 border-left text-semibold rounded-left-sm fs-13 border-right-0">
                                    +62
                                </div>
                                <input type="text" name="phone_number_family" class="form-control ls1 fs-13 px-2 h-50 py-2 rounded-right-sm text-semibold border-right phone_number" id="inputan_tambah_3" placeholder="No Telephone Keluarga Aktif" required>
                            </div>
                        </div>
                        <!-- <div class="form-group mb-3">
                            <div class="input-group rounded-sm">
                                <input type="text" id="file_name_tambah" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-left-sm text-semibold bg-white" readonly placeholder="Foto Kurir" required>
                                <label class="input-group-text border-1 border-left-0 rounded-right-sm fs-13 border-right">
                                    <a for="file_input_tambah" class="btn btn-sm btn-outline-info fs-8 rounded-1" onclick="document.getElementById('file_input_tambah').click();"><i class="fas fa-upload"></i> Add File</a>
                                </label>
                                <input type="file" id="file_input_tambah" name="profile_pic" style="display:none;" onchange="displayFileNameTambah()" accept="image/*">
                            </div>
                        </div> -->
                        <div class="form-group mb-0">
                            <button type="button" class="btn btn-danger btn-sm py-2 fs-12 hover-light border-0 shadow-none float-left px-3 rounded-sm mb-3" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Close</button>
                            <button type="submit" class="btn btn-success btn-sm py-2 fs-12 hover-light border-0 shadow-none float-right px-3 rounded-sm mb-3"><i class="fa fa-save mr-2"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Tambah Kurir -->

    <!-- Load Dependency JS -->
    <?php
    include 'theme/main_footer.php';
    include 'theme/alert.php';
    include 'theme/helper_search.php';
    ?>
    <!-- Load Dependency JS -->
    <script>
        function displayFileNameTambah() {
            const input = document.getElementById('file_input_tambah');
            const fileName = document.getElementById('file_name_tambah');

            if (input.files.length > 0) {
                fileName.value = input.files[0].name;
            } else {
                fileName.value = '';
            }
        }

        $(document).ready(function () {
            
            // Initialize tambah form
            $('#input_tambah_1').hide();
            $('#input_tambah_2').hide();
            $('#input_tambah_3').show();

            $("#inputan_tambah_1").removeAttr('required');
            $("#inputan_tambah_2").removeAttr('required');
            $("#inputan_tambah_3").attr('required', '');

            $("#inputan_tambah_1").val('');
            $("#inputan_tambah_2").val('');
            $("#inputan_tambah_3").val('');

            // Handle status change for tambah form
            $('#status_tambah').change(function() {
                var status = $(this).val();
                
                $("#inputan_tambah_1").val('');
                $("#inputan_tambah_2").val('');
                $("#inputan_tambah_3").val('');

                if (status == 'Menikah') {
                    $('#input_tambah_1').show();
                    $('#input_tambah_2').show();
                    $('#input_tambah_3').hide();

                    $("#inputan_tambah_1").attr('required', '');
                    $("#inputan_tambah_2").attr('required', '');
                    $("#inputan_tambah_3").removeAttr('required');
                } else {
                    $('#input_tambah_1').hide();
                    $('#input_tambah_2').hide();
                    $('#input_tambah_3').show();

                    $("#inputan_tambah_1").removeAttr('required');
                    $("#inputan_tambah_2").removeAttr('required');
                    $("#inputan_tambah_3").attr('required', '');
                }
            });

            // Handle form submission
            $('#form_tambah_kurir').submit(function(e) {
                e.preventDefault();
                
                var formData = new FormData(this);
                
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if(response == 'Y') {
                            Toast.fire({  
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Kurir berhasil ditambahkan',
                            });
                            $('#tambahKurir').modal('hide');
                            setTimeout(function(){
                                window.location.reload();
                            }, 1500);
                        } else if(response == 'W1') {
                            Toast.fire({  
                                icon: 'error',
                                title: 'Format File Salah',
                                text: 'Format file yang diupload tidak didukung',
                            });
                        } else if(response == 'W2') {
                            Toast.fire({  
                                icon: 'error',
                                title: 'File Kosong',
                                text: 'Silahkan pilih foto kurir',
                            });
                        } else if(response == 'W3') {
                            Toast.fire({  
                                icon: 'error',
                                title: 'No HP Sudah Ada',
                                text: 'Nomor HP sudah terdaftar',
                            });
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat menambahkan kurir',
                            });
                        }
                    }
                });
            });

            // Reset form when modal is closed
            $('#tambahKurir').on('hidden.bs.modal', function () {
                $('#form_tambah_kurir')[0].reset();
                $('#file_name_tambah').val('');
                $('#status_tambah').val('Single').trigger('change');
            });

            <?php 
            // Generate JavaScript for all kurir records
            for ($i=1; $i <= $all_data; $i++) { 
            ?>
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

                var status<?= $i ?> = $('#status<?= $i ?>').val();
                if (status<?= $i ?> == 'Menikah') {
                    $('#input-1<?= $i ?>').show();
                    $('#input-2<?= $i ?>').show();
                    $('#input-3<?= $i ?>').hide();

                    /* Required */ 
                        $("#inputan1<?= $i ?>").attr('required', '');
                        $("#inputan2<?= $i ?>").attr('required', '');
                        $("#inputan3<?= $i ?>").removeAttr('required');
                    /* Required */ 
                }else{
                    $('#input-1<?= $i ?>').hide();
                    $('#input-2<?= $i ?>').hide();
                    $('#input-3<?= $i ?>').show();

                    /* Required */ 
                        $("#inputan1<?= $i ?>").removeAttr('required');
                        $("#inputan2<?= $i ?>").removeAttr('required');
                        $("#inputan3<?= $i ?>").attr('required', '');
                    /* Required */ 
                }
                $('#status<?= $i ?>').change(function() {
                    var status<?= $i ?> = $(this).val();
                    if (status<?= $i ?> == 'Menikah') {
                        $('#input-1<?= $i ?>').show();
                        $('#input-2<?= $i ?>').show();
                        $('#input-3<?= $i ?>').hide();

                        /* Required */ 
                            $("#inputan1<?= $i ?>").attr('required', '');
                            $("#inputan2<?= $i ?>").attr('required', '');
                            $("#inputan3<?= $i ?>").removeAttr('required');
                        /* Required */ 
                    }else{
                        $('#input-1<?= $i ?>').hide();
                        $('#input-2<?= $i ?>').hide();
                        $('#input-3<?= $i ?>').show();

                        /* Required */ 
                            $("#inputan1<?= $i ?>").removeAttr('required');
                            $("#inputan2<?= $i ?>").removeAttr('required');
                            $("#inputan3<?= $i ?>").attr('required', '');
                        /* Required */ 
                    }
                });
            <?php } ?>
        });
    </script>
</body>

</html>