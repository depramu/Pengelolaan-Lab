<?php
include '../../function/koneksi.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idBarang = $_POST['idBarang'] ?? null;

    if ($idBarang) {
        $query = "UPDATE Barang SET isDeleted = 1 WHERE idBarang = ?";
        $stmt = sqlsrv_query($conn, $query, [$idBarang]);

        if ($stmt) {
            header("Location: ../../Menu/Menu PIC/manajemenBarang.php");
            exit;
        } else {
            echo "<script>
            alert('Gagal menghapus Barang. Silahkan coba lagi.');
            window.location.href = '../../Menu/Menu PIC/manajemenBarang.php';
            </script>";
            exit;
        }
    }
}

include '../../templates/header.php';
exit;
