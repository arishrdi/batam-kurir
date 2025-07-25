
<script>
    function changeBatas(selectElement) {
        var batas       = selectElement.value;
        var currentURL  = window.location.href;
        var url         = new URL(currentURL);
        url.searchParams.set('hal', 1);
        url.searchParams.set('batas', batas);
        window.location.href = url.toString();
    }

    function searchData(inputElement) {
        var cari        = inputElement.value;
        var currentURL  = window.location.href;
        var url         = new URL(currentURL);
        url.searchParams.set('cari', cari);
        history.pushState(null, '', url.toString());
    }
    
    function search(inputElement) {
        var cari        = inputElement.value;
        var currentURL  = window.location.href;
        var url         = new URL(currentURL);
        url.searchParams.set('cari', cari);
        window.location.href = url.toString();
    }
</script>