<?php
session_start();
include 'koneksi.php'; // Your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idPeminjamanRuangan']) && isset($_POST['action'])) {
    $idPeminjamanRuangan = $_POST['idPeminjamanRuangan'];
    $action = $_POST['action'];

    if ($action === 'setuju') {
        // Begin Transaction
        if (sqlsrv_begin_transaction($conn) === false) {
            $_SESSION['notification'] = [
                'type' => 'danger',
                'message' => 'Gagal memulai transaksi: ' . print_r(sqlsrv_errors(), true)
            ];
            header('Location: riwayatPeminjamanAdmin.php');
            exit();
        }

        // Update status to 'Sedang Dipinjam'
        $newStatus = 'Sedang Dipinjam';
        $sql_update_status = "UPDATE peminjaman_ruangan SET statusPeminjaman = ? WHERE idPeminjamanRuangan = ?";
        $params_update_status = array($newStatus, $idPeminjamanRuangan);
        $stmt_update_status = sqlsrv_query($conn, $sql_update_status, $params_update_status);

        if ($stmt_update_status) {
            sqlsrv_commit($conn);
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'Peminjaman ruangan ID ' . htmlspecialchars($idPeminjamanRuangan) . ' telah disetujui.'
            ];
        } else {
            sqlsrv_rollback($conn);
            $_SESSION['notification'] = [
                'type' => 'danger',
                'message' => 'Gagal menyetujui peminjaman ruangan ID ' . htmlspecialchars($idPeminjamanRuangan) . '. Error: ' . print_r(sqlsrv_errors(), true)
            ];
        }
        sqlsrv_free_stmt($stmt_update_status);
    } else {
        // Handle other actions if any, or invalid action
        $_SESSION['notification'] = [
            'type' => 'warning',
            'message' => 'Aksi tidak valid.'
        ];
    }
} else {
    // Handle invalid request method or missing parameters
    $_SESSION['notification'] = [
        'type' => 'danger',
        'message' => 'Permintaan tidak valid.'
    ];
}

sqlsrv_close($conn);
header('Location: riwayatPeminjamanAdmin.php');
exit();
?>
