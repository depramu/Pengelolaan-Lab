<<<<<<< HEAD
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
=======
<?php $serverName = "LAPTOP-U0TB8VN9\SQLEXPRESS";
    $connectionOptions = ["Database" => "PENGELOLAAN_LAB", "TrustServerCertificate" => true,];
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if ($conn === false) {
        echo "Koneksi Gagal:<br>";
        die(print_r(sqlsrv_errors(), true));
    } else {
        // echo "Koneksi Berhasil!";
    }
>>>>>>> 70b2cb8558c8bcbbaffc76d27d640a02d8f9fea3
