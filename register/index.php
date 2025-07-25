<?php

header("Location: ../");
exit();

setcookie("BK-DELIVERY", "", time() - 3600, "/"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Batam Kurir Delivery &nbsp;&mdash;&nbsp; Login Page</title>
    <link rel="shortcut icon" href="../theme/dist/img/favicon.png" type="image/x-icon">

    <!-- Dependency Stylesheet -->
    <link rel="stylesheet" href="../theme/node_modules/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../theme/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Dependency Stylesheet -->

    <!-- Theme style -->
    <link rel="stylesheet" href="../theme/dist/css/adminlte.min.css">
    <!-- Theme style -->
</head>

<body class="hold-transition login-page pt-5 bg-warning">
    <div class="register-box rounded-md border-0">
        <div class="card bg-white rounded-md border-0 elevation-0">
            <div class="card-body login-card-body p-5 bg-white rounded-lg">
                <p class="fs-20 text-bold ls-0 pb-2 mb-2 ls1 text-left">Form Registrasi Kurir</p>
                <form enctype="multipart/form-data" id="form_register">
                    <div class="form-group mb-3">
                        <input type="text" name="kurir_name" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-sm text-semibold" maxlength="100" placeholder="Nama Kurir" autofocus required>
                    </div>
                    <div class="form-group mb-3">
                        <div class="input-group rounded-sm">
                            <input type="text" name="birdth_place" class="form-control ls1 fs-13 px-3 rounded-left-sm text-semibold" placeholder="Tempat Lahir" autofocus required>
                            <div class="input-group-append">
                                <input type="date" name="birdth_date" max="<?= date('Y-m-d') ?>" class="form-control ls1 fs-13 pl-0 pr-2 border-right rounded-right-sm text-semibold" placeholder="Tanggal Lahir" autofocus required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <textarea name="batam_address" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-sm text-semibold" rows="1" placeholder="Alamat Dibatam" autofocus required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <div class="input-group rounded-sm">
                            <div class="input-group-text border-1 border-left text-semibold rounded-left-sm fs-13 border-right-0">
                                +62
                            </div>
                            <input type="text" name="phone_number" min="10" class="form-control ls1 fs-13 px-2 h-50 py-2 rounded-right-sm text-semibold border-right phone_number" placeholder="No Telephone" autofocus required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <select name="status" id="status" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-sm text-semibold custom-select">
                            <option selected value="Single">Single</option>
                            <option value="Menikah">Menikah</option>
                        </select>
                    </div>
                    <div class="form-group mb-3" id="input-1">
                        <input type="text" name="partner_name" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-sm text-semibold" id="inputan1" placeholder="Nama Istri / Suami" autofocus required>
                    </div>
                    <div class="form-group mb-3" id="input-2">
                        <div class="input-group rounded-sm">
                            <div class="input-group-text border-1 border-left text-semibold rounded-left-sm fs-13 border-right-0">
                                +62
                            </div>
                            <input type="number" name="phone_number_partner" class="form-control ls1 fs-13 px-2 h-50 py-2 rounded-right-sm text-semibold border-right phone_number" id="inputan2" placeholder="No Telephone Istri / Suami" autofocus required>
                        </div>
                    </div>
                    <div class="form-group mb-3" id="input-3">
                        <div class="input-group rounded-sm">
                            <div class="input-group-text border-1 border-left text-semibold rounded-left-sm fs-13 border-right-0">
                                +62
                            </div>
                            <input type="number" name="phone_number_family" class="form-control ls1 fs-13 px-2 h-50 py-2 rounded-right-sm text-semibold border-right phone_number" id="inputan3" placeholder="No Telephone Keluarga Aktif" autofocus required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <div class="input-group rounded-sm">
                            <input type="text" id="file_name" class="form-control ls1 fs-13 px-3 h-50 py-2 rounded-left-sm text-semibold bg-white" readonly placeholder="Foto Kurir" autofocus required>
                            <label class="input-group-text border-1 border-left-0 rounded-right-sm fs-13 border-right">
                                <a  for="file_input" class="btn btn-sm btn-outline-info fs-8 rounded-1" onclick="document.getElementById('file_input').click();"><i class="fas fa-upload"></i> Add File</a>
                            </label>
                            <input type="file" id="file_input" name="profile_pic" style="display:none;" onchange="displayFileName()">
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <button type="submit" id="submit_register" disabled class="btn btn-sm py-2 text-bold btn-warning rounded-sm border-0 ls-1 fs-14 hover px-4">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Js Dependency -->
    <script src="../theme/node_modules/jquery/dist/jquery.min.js"></script>
    <script src="../theme/node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="../theme/dist/js/adminlte.js"></script>
    <script src="main.js"></script>
    <!-- Js Dependency -->
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
            $('#input-1').hide();
            $('#input-2').hide();
            $('#input-3').show();

            /* Required */ 
                $("#inputan1").removeAttr('required');
                $("#inputan2").removeAttr('required');
                $("#inputan3").attr('required', '');
            /* Required */ 
            /* Value */ 
                $("#inputan1").val(null);
                $("#inputan2").val(null);
                $("#inputan3").val(null);
            /* Value */ 
            $('#status').change(function() {
                var status = $(this).val();
                /* Value */ 
                    $("#inputan1").val(null);
                    $("#inputan2").val(null);
                    $("#inputan3").val(null);
                /* Value */ 
                if (status == 'Menikah') {
                    $('#input-1').show();
                    $('#input-2').show();
                    $('#input-3').hide();

                    /* Required */ 
                        $("#inputan1").attr('required', '');
                        $("#inputan2").attr('required', '');
                        $("#inputan3").removeAttr('required');
                    /* Required */ 
                }else{
                    $('#input-1').hide();
                    $('#input-2').hide();
                    $('#input-3').show();

                    /* Required */ 
                        $("#inputan1").removeAttr('required');
                        $("#inputan2").removeAttr('required');
                        $("#inputan3").attr('required', '');
                    /* Required */ 
                }
            });
        });
    </script>
</body>

</html>