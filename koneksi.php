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
<<<<<<< HEAD
// ganti serverName sesuai dengan nama sqlserver yang ada di komputer kalian untuk testing
$serverName = "DEPENIGER\\SQLEXPRESS";
=======
$serverName = "DEPENIGER\\SQLEXPRESS01";
>>>>>>> dc08be48b87b8727fb75ed5aa27a1c5c53ccd31a
$connectionOptions = [
    "Database" => "PENGELOLAAN_LAB",
    "TrustServerCertificate" => true,
];
>>>>>>> 6ecbb6e604038161c6865cb5373ac648feb4030b

// Attempt to connect
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    echo "Koneksi Gagal:<br>";
    die(print_r(sqlsrv_errors(), true));
}
// If connection is successful
// echo "Koneksi Berhasil!<br>";