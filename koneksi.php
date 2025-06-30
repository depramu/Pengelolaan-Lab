<?php
// ganti serverName sesuai dengan nama sqlserver yang ada di komputer kalian untuk testing
$serverName = "DEPENIGER\\SQLEXPRESS";
$connectionOptions = [
    "Database" => "PENGELOLAAN_LAB",
    // "Uid" => "pengabdiLab",
    // "PWD" => "abdilab123*",
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
