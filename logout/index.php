<?php
setcookie("BK-DELIVERY", "", time() - 3600, "/");
echo'<script>window.location.href="../login/";</script>'; // Alihkan Ke Halaman Login