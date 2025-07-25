<nav class="main-header navbar navbar-expand-md navbar-light bg-light-gray py-2 border-0 fixed-top">
    <div class="container-fluid px-3">
        <a href="pick_up.php" class="navbar-brand mx-3">
            <img src="../theme/dist/img/apps_logo.png" alt="BK Delivery" class="brand-image elevation-0">
        </a>

        <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto collapsed">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="far fa-user-circle text-lg"></i>
                    <span class="px-1 fs-14 text-semibold">Hi, <?= $data_user['full_name'] ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-sm bg-light dropdown-menu-left hover_text border-0 elevation-3 mt-1 py-0 rounded-0">
                    <a id="logout" class="dropdown-item nav-link bg-light text-bold text-custom hover_text fs-14 py-1 h-50">Log Out</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<nav class="navbar navbar-expand-md navbar-light navbar-warning py-1 border-0 fixed-top" style="margin-top: 55px">
    <div class="container-fluid px-3">
        <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse order-3" id="navbarCollapse">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="pick_up.php" class="nav-link text-bold text-custom hover_text fs-15 ls1">Form Pick Up</a>
                </li>
                <li class="nav-item">
                    <a href="delivery.php" class="nav-link text-bold text-custom hover_text fs-15 ls1">Form Delivery</a>
                </li>
                <li class="nav-item">
                    <a href="summary_pickup.php" class="nav-link text-bold text-custom hover_text fs-15 ls1">Summary Pick Up</a>
                </li>
                <li class="nav-item">
                    <a href="summary_delivery.php" class="nav-link text-bold text-custom hover_text fs-15 ls1">Summary Delivery</a>
                </li>
            </ul>
        </div>
    </div>
</nav>