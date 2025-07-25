document.addEventListener("DOMContentLoaded", function () {
    const passwordInput = document.querySelector("input[name='password']");
    const showPasswordButton = document.getElementById("show");

    showPasswordButton.addEventListener("click", function () {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            showPasswordButton.innerHTML = '<i class="fas fa-eye"></i>';
        } else {
            passwordInput.type = "password";
            showPasswordButton.innerHTML = '<i class="fas fa-eye-slash"></i>';
        }
    });
});

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    customClass: {
        popup: 'border-0 elevation-1 rounded-md pl-4 pr-0',
    }
});

$('#form_login').submit(function(e) {
    $('#submit_login').prop('disabled', true);
    e.preventDefault();
    $.ajax({
        url: 'proses.php',
        type: 'POST', 
        data: new FormData(this),
        contentType: false,
        processData: false,
        success: function(data) {
            if(data == 'Administrator') {
                Toast.fire({  
                    icon: 'success',
                    title: 'Login Success', 
                    text: 'Berhasil Login Sebagai Administrator',
                    timer: 1500,
                });
                setTimeout(function(){
                    document.location="../adm/";
                }, 1500);
            }else if(data == 'Kurir') {
                Toast.fire({  
                    icon: 'success',
                    title: 'Login Success', 
                    text: 'Berhasil Login Sebagai Kurir',
                    timer: 1500,
                });
                setTimeout(function(){
                    document.location="../kurir/";
                }, 1500);
            }else {
                Toast.fire({  
                    icon: 'error',
                    title: 'Login Failed',
                    text: 'Username / Passsword Tidak Sesuai',
                    timer: 1500,
                });
                setTimeout(function(){
                    $('#submit_login').prop('disabled', false);
                }, 1500);
            }
        }
    })
});