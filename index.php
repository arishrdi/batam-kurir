<?php
if (!empty($_COOKIE['BK-DELIVERY'])) {
    $sesion_admin   = json_decode($_COOKIE['BK-DELIVERY']);
    if ($sesion_admin->role_access == 'Administrator') {
        header("location:adm/");
    }else if ($sesion_admin->role_access == 'Kurir') {
        header("location:kurir/");
    }else{
        header("location:login/");
    }
}else{
    header("location:login/");
}
?>