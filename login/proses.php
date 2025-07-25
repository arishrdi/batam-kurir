<?php
include '../config/db.php'; // Load Koneksi DB SQL
// Tampung Variable [POST]
$username = $_POST['username'];
$password = $_POST['password'];

// Cek User Login
$login = mysqli_query($con, "SELECT mst_user.*, mst_role_access.role_access
FROM mst_user 
    JOIN mst_role_access ON mst_role_access.id=mst_user.role_access_id
WHERE is_active=1
    AND mst_user.username='$username'
    AND mst_user.password='$password'");

if (mysqli_num_rows($login) > 0) {
    $data           = mysqli_fetch_assoc($login);
    $user_id        = $data['id'];
    $role_access    = $data['role_access'];
    if ($role_access == 'Administrator') {
        $data = [
            'user_id'       => $user_id,
            'role_access'   => $role_access,
        ];
        $serialized_data = json_encode($data);
        setcookie("BK-DELIVERY", $serialized_data, 0, "/");
        echo $role_access;
    }elseif($role_access == 'Kurir'){
        $data = [
            'user_id'       => $user_id,
            'role_access'   => $role_access,
        ];
        $serialized_data = json_encode($data);
        setcookie("BK-DELIVERY", $serialized_data, 0, "/");

        echo $role_access;
    }else{
        echo 'failed';
    }
} else {
    echo 'failed';
}
