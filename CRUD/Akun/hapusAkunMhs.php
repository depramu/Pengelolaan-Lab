<?php
include '../../function/koneksi.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = $_POST['nim'] ?? null;

    if ($nim) {
        $query = "UPDATE Mahasiswa SET isDeleted = 1 WHERE nim = ?";
        $stmt = sqlsrv_query($conn, $query, [$nim]);

        if ($stmt) {
            header("Location: ../../Menu/Menu PIC/manajemenAkunMhs.php");
            exit;
        } else {
            echo "<script>
            alert ('Gagal menghapus akun. Silahkan coba lagi.');
            window.location.href = '../../Menu/Menu PIC/manajemenAkunMhs.php'
            </script>";
            exit;
        }
    }
}

include '../../templates/header.php';
exit;
