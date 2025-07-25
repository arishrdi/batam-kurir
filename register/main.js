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



$(document).ready(function () {
    $('#form_register').submit(function(e) {
        $('#submit_register').prop('disabled', true);
        e.preventDefault();
        $.ajax({
            url: 'proses.php',
            type: 'POST', 
            data: new FormData(this),
            contentType: false,
            processData: false,
            success: function(data) {
                if(data == 'Y') {
                    Toast.fire({  
                        icon: 'success',
                        title: 'Registration Success', 
                        text: 'Pendaftaran Kurir Berhasil',
                        timer: 1500,
                    });
                    setTimeout(function(){
                        document.location="../register/";
                    }, 1500);
                }else if(data == 'W1') {
                    Toast.fire({  
                        icon: 'warning',
                        title: 'Erorr Extention Not Found', 
                        text: 'Ektensi Foto Tidak Sesuai',
                        timer: 1500,
                    });
                    $('#submit_register').prop('disabled', false);
                }else if(data == 'W2') {
                    Toast.fire({  
                        icon: 'warning',
                        title: 'Erorr 404', 
                        text: 'Foto tidak ditemukan',
                        timer: 1500,
                    });
                    setTimeout(function(){
                        document.location="../register/";
                    }, 1500);
                }else if(data == 'W3') {
                    Toast.fire({  
                        icon: 'warning',
                        title: 'Erorr Already Used', 
                        text: 'No Telepone Sudah Digunakan Sebelumnya',
                        timer: 1500,
                    });
                    setTimeout(function(){
                        document.location="../register/";
                    }, 1500);
                }else {
                    Toast.fire({  
                        icon: 'error',
                        title: 'Registration Failed', 
                        text: 'Pendaftaran Kurir Gagal',
                        timer: 1500,
                    });
                    setTimeout(function(){
                        document.location="../register/";
                    }, 1500);
                }
            }
        })
    });

    $('.phone_number').keyup(function() {
        var phoneNumber = $(this).val().replace(/\D/g, '');
        var isValid     = phoneNumber.length >= 10 && phoneNumber.length <= 12; 
        if (isValid) {
            $(this).css('border-color', 'green'); // Add green border if valid
            $('#submit_register').prop('disabled', false);
        } else {
            $(this).css('border-color', 'red'); // Add red border if invalid
            $('#submit_register').prop('disabled', true);
        }
        $(this).val(phoneNumber.slice(0, 12));
    });
});
