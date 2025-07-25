<?php setcookie("BK-DELIVERY", "", time() - 3600, "/"); ?>
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
    <div class="login-box rounded-md border-0">
        <div class="card bg-white rounded-md border-0 elevation-0">
            <div class="row">
                <div class="col-md-6">
                    <div class="card-body login-card-body p-5 bg-white rounded-lg">
                        <p class="login-box-msg fs-28 text-semibold ls-0 pb-2 mb-3 ls4">LOGIN</p>
                        <form id="form_login">
                            <div class="form-group mb-2">
                                <label class="mb-1">Username</label>
                                <input type="text" name="username" class="form-control ls3 fs-13 px-3 h-50 py-2 rounded-sm" maxlength="30" placeholder="Phone Number (628XXXXX)" autofocus required>
                            </div>
                            <div class="form-group mb-4">
                                <label class="mb-1">Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" placeholder="Password" class="form-control ls3 fs-13 px-3 h-50 py-2 rounded-left-sm" placeholder="Password" autofocus required>
                                    <div class="input-group-append border-0 bg-white">
                                        <div class="input-group-text hover rounded-right-sm" id="show">
                                            <span class="fas fa-eye-slash text-sm"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <button type="submit" id="submit_login" class="btn btn-sm py-2 text-bold btn-warning btn-block rounded-sm border-0 ls-1 fs-14 hover">Login</button>
                            </div>
                        </form>
                        <!-- <p class="fs-14 text-center">Lupa Password? <a href="../forgot_password/" class="text-primary"><u>Klik Disini</u></a></p> -->
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-body login-card-body p-3 bg-white rounded-lg d-none d-md-block">
                        <img src="../theme/dist/img/bg_1.png" class="img-fluid mt-4" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Js Dependency -->
    <script src="../theme/node_modules/jquery/dist/jquery.min.js"></script>
    <script src="../theme/node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="../theme/dist/js/adminlte.js"></script>
    <script src="main.js"></script>
    <!-- Js Dependency -->
</body>

</html>