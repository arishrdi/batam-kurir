
<script>

    function changePeriode(date_from, date_to) {

        var currentURL  = window.location.href;
        var url         = new URL(currentURL);
        url.searchParams.set('from', date_from);
        url.searchParams.set('to', date_to);
        window.location.href = url.toString();
    }

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

    function changekurir(selectElement) {
        var kurir       = selectElement.value;
        var currentURL  = window.location.href;
        var url         = new URL(currentURL);
        url.searchParams.set('kurir', kurir);
        window.location.href = url.toString();
    }

    function changestatus(selectElement) {
        var status      = selectElement.value;
        var currentURL  = window.location.href;
        var url         = new URL(currentURL);
        url.searchParams.set('status', status);
        window.location.href = url.toString();
    }
</script>