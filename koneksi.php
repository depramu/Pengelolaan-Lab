<?php
// ganti serverName sesuai dengan nama sqlserver yang ada di komputer kalian untuk testing
<<<<<<< HEAD
$serverName = "DEPENIGER\SQLEXPRESS01";
=======
$serverName = "DESKTOP-3QFLTFG\SQLEXPRESS01";
>>>>>>> 214511133ce34a90da4a05e4292cdc131042ab7d
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
