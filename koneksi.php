<<<<<<< HEAD
<?php 
    $serverName = "LAPTOP-K11EHC8I\SQLEXPRESS   ";
    $connectionOptions = ["Database" => "PENGELOLAAN_LAB", "TrustServerCertificate" => true,];
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if ($conn === false) {
        echo "Koneksi Gagal:<br>";
        die(print_r(sqlsrv_errors(), true));
    } else {
        // echo "Koneksi Berhasil!";
    }
?>
=======
<?php
// ganti serverName sesuai dengan nama sqlserver yang ada di komputer kalian untuk testing
$serverName = "DEPENIGER\\SQLEXPRESS01";
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
// If connection is successful
// echo "Koneksi Berhasil!<br>";
>>>>>>> f620eeb4d07ac05ab437ed9e747b7d464c2b2f96
