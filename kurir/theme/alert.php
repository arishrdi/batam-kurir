<?php
if(isset($_GET['alert'])) {
    $alert  = $_GET['alert'];
    if ($alert == 'Y') {
        echo"<script>
            Toast.fire({  
                icon: 'success',
                title: 'Update Success',
                text: 'Record berhasil diperbarui',
            });
            setTimeout(function(){
                window.location.href = window.location.pathname;
            }, 1500);
        </script>";
    }else if ($alert == 'W') {
        echo"<script>
            Toast.fire({  
                icon: 'warning',
                title: 'Erorr 404',
                text: 'Record Not Found',
            });
            setTimeout(function(){
                window.location.href = window.location.pathname;
            }, 1500);
        </script>";
    }else if ($alert == 'W1') {
        echo"<script>
            Toast.fire({  
                icon: 'warning',
                title: 'Erorr 404',
                text: 'Record Not Found',
            });
            setTimeout(function(){
                window.location.href = window.location.pathname;
            }, 1500);
        </script>";
    }else if ($alert == 'W2') {
        echo"<script>
            Toast.fire({  
                icon: 'warning',
                title: 'Erorr Already Use',
                text: 'No Telepone tidak tersedia / sudah digunakan',
            }); 
            setTimeout(function(){
                window.location.href = window.location.pathname;
            }, 1500);
        </script>";
    }else{
        echo"<script>
            Toast.fire({  
                icon: 'error',
                title: 'Update Failed',
                text: 'Record tidak dapat diperbarui',
            });
            setTimeout(function(){
                window.location.href = window.location.pathname;
            }, 1500);
        </script>";
    }
}
?>