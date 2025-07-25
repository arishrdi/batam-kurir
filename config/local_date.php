<?php
date_default_timezone_set("ASIA/JAKARTA"); // Set Time Zone 7+ Indonesia

function datetime_id($date){ // Function Local Day Date Time
    $Hari   = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu",);
    $Bulan  = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

    $tahun  = substr($date, 0, 4);
    $bulan  = substr($date, 5, 2);
    $tgl    = substr($date, 8, 2);
    $waktu  = substr($date, 11, 8);
    $hari   = date("w", strtotime($date));

    $result = $Hari[(int)$hari] . ", " . $tgl . " " . $Bulan[(int)$bulan - 1] . " " . $tahun . " " . $waktu . "";
    return $result;
}
function date_id($date){ // Function Local Date
    $Hari   = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu",);
    $Bulan  = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

    $tahun  = substr($date, 0, 4);
    $bulan  = substr($date, 5, 2);
    $tgl    = substr($date, 8, 2);
    $waktu  = substr($date, 11, 5);
    $hari   = date("w", strtotime($date));

    $result = $tgl . " " . $Bulan[(int)$bulan - 1] . " " . $tahun;
    return $result;
}
function daydate_id($date){ // Function Local Day Date
    $Hari   = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu",);
    $Bulan  = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

    $tahun  = substr($date, 0, 4);
    $bulan  = substr($date, 5, 2);
    $tgl    = substr($date, 8, 2);
    $waktu  = substr($date, 11, 5);
    $hari   = date("w", strtotime($date));

    $result = $Hari[(int)$hari] . ", " . $tgl . " " . $Bulan[(int)$bulan - 1] . " " . $tahun;
    return $result;
}
function day_id($date){ // Function Local Day
    $Hari   = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu",);
    $Bulan  = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

    $tahun  = substr($date, 0, 4);
    $bulan  = substr($date, 5, 2);
    $tgl    = substr($date, 8, 2);
    $waktu  = substr($date, 11, 5);
    $hari   = date("w", strtotime($date));

    $result = $Hari[(int)$hari];
    return $result;
}
function month_id($date){ // Function Local Month
    $Hari   = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu",);
    $Bulan  = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

    $tahun  = substr($date, 0, 4);
    $bulan  = substr($date, 5, 2);
    $tgl    = substr($date, 8, 2);
    $waktu  = substr($date, 11, 5);
    $hari   = date("w", strtotime($date));

    $result = $Bulan[(int)$bulan - 1];
    return $result;
}

function getInitials($string) {
    $words      = explode(" ", $string);
    $initials   = "";

    foreach ($words as $word) {
        $initials .= substr($word, 0, 1);
    }

    return strtoupper($initials);
}