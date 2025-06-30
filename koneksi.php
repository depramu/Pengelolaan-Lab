<?php $serverName = "LAPTOP-U0TB8VN9\SQLEXPRESS";
    $connectionOptions = ["Database" => "PENGELOLAAN_LAB", "TrustServerCertificate" => true,];
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if ($conn === false) {
        echo "Koneksi Gagal:<br>";
        die(print_r(sqlsrv_errors(), true));
    } else {
        // echo "Koneksi Berhasil!";
    }