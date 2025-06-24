<?php
session_start();
require_once 'koneksi.php'; // Ensure this path is correct

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['idPeminjamanRuangan']) && isset($_POST['alasanPenolakan'])) {
        $idPeminjamanRuangan = $_POST['idPeminjamanRuangan'];
        $alasanPenolakan = trim($_POST['alasanPenolakan']); // Trim whitespace
        $newStatus = 'Ditolak';

        if (empty($alasanPenolakan)) {
            $_SESSION['message'] = ['type' => 'warning', 'text' => 'Alasan penolakan tidak boleh kosong.'];
            // Redirect back to the form, passing the ID so the user can try again
            header('Location: PeminjamanRuanganDitolak.php?id=' . urlencode($idPeminjamanRuangan));
            exit();
        }

        if (isset($conn) && $conn) {
            // Start transaction
            if (sqlsrv_begin_transaction($conn) === false) {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Gagal memulai transaksi. Error: ' . print_r(sqlsrv_errors(), true)];
                header('Location: riwayatPeminjamanAdmin.php');
                exit();
            }

            // 1. Update status in peminjaman_ruangan table
            $sql_update_status = "UPDATE peminjaman_ruangan SET statusPeminjaman = ? WHERE idPeminjamanRuangan = ?";
            $params_update_status = array($newStatus, $idPeminjamanRuangan);
            $stmt_update_status = sqlsrv_query($conn, $sql_update_status, $params_update_status);

            if ($stmt_update_status) {
                // 2. Insert reason into Penolakan table
                // Assuming 'Penolakan' table has columns 'idPeminjamanRuangan' and 'alasanPenolakan'
                $sql_insert_reason = "INSERT INTO Penolakan (idPeminjamanRuangan, alasanPenolakan) VALUES (?, ?)";
                $params_insert_reason = array($idPeminjamanRuangan, $alasanPenolakan);
                $stmt_insert_reason = sqlsrv_query($conn, $sql_insert_reason, $params_insert_reason);

                if ($stmt_insert_reason) {
                    sqlsrv_commit($conn);
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Peminjaman ruangan berhasil ditolak dengan alasan yang dicatat.'];
                } else {
                    sqlsrv_rollback($conn);
                    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Gagal mencatat alasan penolakan. Error: ' . print_r(sqlsrv_errors(), true)];
                }
            } else {
                sqlsrv_rollback($conn);
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Gagal memperbarui status peminjaman. Error: ' . print_r(sqlsrv_errors(), true)];
            }
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Koneksi database gagal.'];
        }
    } else {
        $_SESSION['message'] = ['type' => 'warning', 'text' => 'Data tidak lengkap untuk memproses penolakan.'];
    }
} else {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Metode request tidak valid.'];
}

// Redirect back to the history page
header('Location: riwayatPeminjamanAdmin.php');
exit();
?>
