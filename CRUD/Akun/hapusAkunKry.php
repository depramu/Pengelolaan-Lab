<?php
include '../../function/koneksi.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $npk = $_POST['npk'] ?? null;
    
    if ($npk) {
        $query = "UPDATE Karyawan SET isDeleted = 1 WHERE npk = ?";
        $stmt = sqlsrv_query($conn, $query, [$npk]);

        if ($stmt) {
            header("Location: ../../Menu/Menu PIC/manajemenAkunKry.php");
            exit;
        } else {
            echo "<script>
            alert ('Gagal menghapus akun. Silahkan coba lagi.');
            window.location.href = '../../Menu/Menu PIC/manajemenAkunKry.php'
            </script>";
            exit;
        }
    }
}

include '../../templates/header.php';
exit;
?>