<<<<<<< HEAD
<?php $serverName = "LAPTOP-K11EHC8I\SQLEXPRESS";
    $connectionOptions = ["Database" => "PENGELOLAAN_LAB", "TrustServerCertificate" => true,];
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if ($conn === false) {
        echo "Koneksi Gagal:<br>";
        die(print_r(sqlsrv_errors(), true));
    } else {
        // echo "Koneksi Berhasil!";
    }
=======
<?php
$serverName = "pengabdilab.database.windows.net";
$connectionOptions = [
    "Database" => "PENGELOLAAN_LAB",
    "Uid" => "pengabdiLab",
    "PWD" => "abdilab123*",
    "TrustServerCertificate" => true,
];
>>>>>>> 94c314b12f43056f13c5f1d93882bbcb36244e0c

// Attempt to connect
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    echo "Koneksi Gagal:<br>";
    die(print_r(sqlsrv_errors(), true));
}
// If connection is successful
// echo "Koneksi Berhasil!<br>";
