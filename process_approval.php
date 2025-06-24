<?php
session_start();

// Ensure this path is correct for your database connection file
require_once 'koneksi.php'; // Changed to koneksi.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['idPeminjamanRuangan']) && isset($_POST['action'])) {
        $idPeminjamanRuangan = $_POST['idPeminjamanRuangan'];
        $action = $_POST['action'];
        $newStatus = '';

        if ($action == 'approve') {
            $newStatus = 'Disetujui';
        } elseif ($action == 'reject') {
            $newStatus = 'Ditolak';
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Tindakan tidak valid.'];
            header('Location: riwayatPeminjamanAdmin.php');
            exit();
        }

        if (!empty($newStatus)) {
            // Assuming koneksi.php makes $conn available or returns it.
            // If koneksi.php directly establishes $conn, this line might not be needed, 
            // or if it returns the connection, it would be $conn = require 'koneksi.php';
            // For sqlsrv, often koneksi.php just runs sqlsrv_connect and the variable is used.
            // Let's assume koneksi.php provides $conn or a function to get it.
            // For now, we'll assume $conn is made available by koneksi.php or it returns the connection.
            // The original code used $conn = db_connect(); if koneksi.php defines a function like get_connection(), it should be used here.
            // If koneksi.php just establishes $conn, then we use $conn directly.
            // Given the context of other files, koneksi.php likely establishes $conn directly.
            // So, we will attempt to use $conn directly after including koneksi.php.
            // The check 'if ($conn)' will verify if it was established.
            if (isset($conn) && $conn) { // Check if $conn was set by koneksi.php
                $sql = "UPDATE peminjaman_ruangan SET statusPeminjaman = ? WHERE idPeminjamanRuangan = ?";
                $params = array($newStatus, $idPeminjamanRuangan);
                $stmt = sqlsrv_query($conn, $sql, $params);

                if ($stmt) {
                    $rows_affected = sqlsrv_rows_affected($stmt);
                    if ($rows_affected > 0) {
                        $_SESSION['message'] = ['type' => 'success', 'text' => 'Status peminjaman berhasil diperbarui menjadi ' . $newStatus . '.'];
                    } else {
                        $_SESSION['message'] = ['type' => 'warning', 'text' => 'Status peminjaman tidak berubah. Mungkin sudah ' . $newStatus . ' atau ID tidak ditemukan.'];
                    }
                } else {
                    // Log detailed error for admin, show generic message to user
                    error_log('SQLSRV Error: ' . print_r(sqlsrv_errors(), true));
                    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Gagal memperbarui status peminjaman. Kesalahan database.'];
                }
                sqlsrv_close($conn);
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Koneksi database gagal.'];
            }
        } else {
             // This case should ideally be caught by the initial action check
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Status baru tidak dapat ditentukan.'];
        }
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Data yang diperlukan tidak lengkap.'];
    }
} else {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Metode permintaan tidak valid.'];
}

header('Location: riwayatPeminjamanAdmin.php');
exit();
?>
