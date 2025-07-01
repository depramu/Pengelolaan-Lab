<?php
// ganti serverName sesuai dengan nama sqlserver yang ada di komputer kalian untuk testing
$serverName = "LAPTOP-8NH7KJD5\SQLEXPRESS";
$connectionOptions = [
    "Database" => "PENGELOLAAN_LAB",
    "TrustServerCertificate" => true,
];

// Attempt to connect
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    echo "Koneksi Gagal:<br>";
    die(print_r(sqlsrv_errors(), true));
}
