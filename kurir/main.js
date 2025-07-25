const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    customClass: {
        popup: 'border-0 elevation-1 rounded-md pl-4 pr-0',
    }
});

const Toast_Confirm = Swal.mixin({
    toast: true,
    position: 'top-end',
});
const Toast_Confirm_Center = Swal.mixin({
    toast: true,
    position: 'center-center',
});

// Format Rupiah
$(document).ready(function () {
    
    /* Log Out */ 
    $('#logout').click(function(e) {
        e.preventDefault();
        Toast_Confirm.fire({  
            icon: 'warning',
            title: 'Anda yakin...?', 
            text: 'Ingin meninggalkan laman ini',
            showCancelButton: true,
            confirmButtonText: 'Ya, Keluar',
            showCancelButton: true,
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'border-0 elevation-1 rounded-md pl-4 pr-0',
                confirmButton: 'btn btn-sm btn-light bg-success py-1 fs-12 shadow-none border-0 rounded-sm mb-3 hover-light ml-3 mr-1 px-3', // Add your custom CSS classes here
                cancelButton: 'btn btn-sm btn-light bg-danger py-1 fs-12 shadow-none rounded-sm mb-3 hover-light px-3' // Add your custom CSS classes here
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Toast.fire({
                    icon: 'success',
                    title: 'Berhasil Log Out !',
                    text: 'Terimakasih',
                });
                setTimeout(function(){
                    document.location = "../logout/";
                }, 1500);
            }
        })
    });
    /* Log Out */ 

    /* Pick UP */ 
        $('#insert-pickup').submit(function(e) {
            $('#submit').prop('disabled', true);
            e.preventDefault();
            $.ajax({
                url: 'proses/insert/pickup.php',
                type: 'POST', 
                data: new FormData(this),
                contentType: false,
                processData: false,
                success: function(data) {
                    if(data == 'Y') {
                        Toast.fire({  
                            icon: 'success',
                            title: 'Insert Success', 
                            text: 'Record berhasil ditambahkan',
                        });
                        setTimeout(function(){
                            document.location="pick_up.php";
                        }, 1500);
                    }else if(data == 'W1') {
                        Toast.fire({  
                            icon: 'warning',
                            title: 'Erorr Extention Not Found', 
                            text: 'Ektensi Foto Tidak Sesuai',
                            timer: 1500,
                        });
                        $('#submit').prop('disabled', false);
                    }else if(data == 'W2') {
                        Toast.fire({  
                            icon: 'warning',
                            title: 'Erorr 404', 
                            text: 'Foto tidak ditemukan',
                            timer: 1500,
                        });
                        $('#submit').prop('disabled', false);
                    }else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Insert Failed',
                            text: 'Record gagal ditambahkan',
                        });
                        setTimeout(function(){
                            window.location.href='pick_up.php';
                        }, 1500);
                    }
                }
            })
        });
    /* Pick UP */ 

    /* Delivery */ 
        $('#insert-deliv').submit(function(e) {
            $('#submit').prop('disabled', true);
            var date_search = $('#date_search').val();
            e.preventDefault();
            $.ajax({
                url: 'proses/insert/deliv.php',
                type: 'POST', 
                data: new FormData(this),
                contentType: false,
                processData: false,
                success: function(data) {
                    if(data == 'Y') {
                        Toast.fire({  
                            icon: 'success',
                            title: 'Delivery Success', 
                            text: 'Paket telah dikonfirmasi', 
                        });
                        setTimeout(function(){
                            document.location="delivery.php?date="+date_search;
                        }, 1500);
                    }else if(data == 'W1') {
                        Toast.fire({  
                            icon: 'warning',
                            title: 'Erorr Extention Not Found', 
                            text: 'Ektensi Foto Tidak Sesuai',
                            timer: 1500,
                        });
                        $('#submit').prop('disabled', false);
                    }else if(data == 'W2') {
                        Toast.fire({  
                            icon: 'warning',
                            title: 'Erorr 404', 
                            text: 'Foto tidak ditemukan',
                            timer: 1500,
                        });
                        $('#submit').prop('disabled', false);
                    }else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Delivery Failed',
                            text: 'Paket tidak dapat dikonfirmasi', 
                        });
                        setTimeout(function(){
                            window.location.href='delivery.php?date='+date_search;
                        }, 1500);
                    }
                }
            })
        });

        $('#form-update-pickup').submit(function(e) {
            var date_search = $('#date_search').val();
            $('#submit_delivery').prop('disabled', true);
            e.preventDefault();
            $.ajax({
                url: 'proses/update/confirm_delivery.php',
                type: 'POST', 
                data: new FormData(this),
                contentType: false,
                processData: false,
                success: function(data) {
                    if(data == 'Y') {
                        Toast.fire({  
                            icon: 'success',
                            title: 'Change Status Success', 
                            text: 'Status Delivery berhasil diperbarui', 
                        });
                        setTimeout(function(){
                            document.location="delivery.php?date="+date_search;
                        }, 1500);
                    }else if(data == 'W1') {
                        Toast.fire({  
                            icon: 'warning',
                            title: 'Erorr Extention Not Found', 
                            text: 'Ektensi Foto Tidak Sesuai',
                            timer: 1500,
                        });
                        $('#submit').prop('disabled', false);
                    }else if(data == 'W2') {
                        Toast.fire({  
                            icon: 'warning',
                            title: 'Erorr 404', 
                            text: 'Foto tidak ditemukan',
                            timer: 1500,
                        });
                        $('#submit').prop('disabled', false);
                    }else if(data == 'W3') {
                        Toast.fire({  
                            icon: 'warning',
                            title: 'Erorr 404', 
                            text: 'Record tidak ditemukan',
                            timer: 1500,
                        });
                        $('#submit').prop('disabled', false);
                    }else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Change Status Failed',
                            text: 'Tidak dapat memperbarui Status Delivery', 
                        });
                        setTimeout(function(){
                            window.location.href='delivery.php?date='+date_search;
                        }, 1500);
                    }
                }
            })
        });
    /* Delivery */ 
    $('.phone_number').keyup(function() {
        var phoneNumber = $(this).val().replace(/\D/g, '');
        var isValid     = phoneNumber.length >= 10 && phoneNumber.length <= 12; 
        if (isValid) {
            $(this).css('border-color', 'green'); // Add green border if valid
            $('#submit').prop('disabled', false);
        } else {
            $(this).css('border-color', 'red'); // Add red border if invalid
            $('#submit').prop('disabled', true);
        }
        $(this).val(phoneNumber.slice(0, 12));
    });
    
    $('.select2bs4').select2({
        theme: 'bootstrap4',
        width: 'auto'
    });

    $('.price').keyup(function() {
        var price    = $(this).val();
        $(this).val(formatRupiah(price));
    });

});

function formatRupiah(angka, prefix) {
    var number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    // tambahkan titik jika yang di input sudah menjadi angka ribuan
    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix == undefined ? rupiah : (rupiah ? rupiah : '');
}

$(document).getElementById('price').addEventListener('input', function (e) {
    let value = e.target.value;

    // Hanya izinkan angka dan satu titik desimal
    value = value.replace(/[^0-9.]/g, '');

    // Pastikan hanya ada satu titik desimal
    let decimalCount = (value.match(/\./g) || []).length;
    if (decimalCount > 1) {
        value = value.slice(0, value.lastIndexOf('.'));
    }

    e.target.value = value;
});