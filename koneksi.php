<?php
$serverName = "pengabdilab.database.windows.net";
$connectionOptions = [
    "Database" => "PENGELOLAAN_LAB",
    "Uid" => "pengabdiLab",
    "PWD" => "abdilab123*",
    "TrustServerCertificate" => true,
];

// Attempt to connect
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    echo "Koneksi Gagal:<br>";
    die(print_r(sqlsrv_errors(), true));
}
// If connection is successful
// echo "Koneksi Berhasil!<br>";
