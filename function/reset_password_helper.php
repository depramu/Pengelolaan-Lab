<?php
// Helper functions for password reset
require_once __DIR__ . '/src/PHPMailer.php';
require_once __DIR__ . '/src/SMTP.php';
require_once __DIR__ . '/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function generateSecurePassword(int $length = 8): string
{
    // Generates a random 8-character alphanumeric mixed-case password
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $maxIdx = strlen($chars) - 1;
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $idx = random_int(0, $maxIdx);
        $result .= $chars[$idx];
    }
    return $result;
}

/**
 * Attempt to reset user password for Mahasiswa or Karyawan.
 * Returns [bool success, string message]
 */
function resetUserPassword($conn, string $email, string $namaLengkap): array
{
    // First, attempt to locate the user in Mahasiswa
    $sqlMhs = "SELECT nim AS id, namaMhs AS nama FROM Mahasiswa WHERE namaMhs = ?";
    $stmtMhs = sqlsrv_query($conn, $sqlMhs, [$namaLengkap]);
    if ($stmtMhs === false) {
        return [false, 'Terjadi kesalahan database.'];
    }
    $row = sqlsrv_fetch_array($stmtMhs, SQLSRV_FETCH_ASSOC);

    $table = null;
    $idCol = null;
    $idVal = null;

    if ($row && strcasecmp($row['nama'], $namaLengkap) === 0) {
        $table = 'Mahasiswa';
        $idCol = 'nim';
        $idVal = $row['id'];
    } else {
        // Check Karyawan (namaKry or nama)
        $sqlKry = "SELECT npk AS id, namaKry AS nama FROM Karyawan WHERE namaKry = ?";
        $stmtKry = sqlsrv_query($conn, $sqlKry, [$namaLengkap]);
        if ($stmtKry === false) {
            return [false, 'Terjadi kesalahan database.'];
        }
        $rowK = sqlsrv_fetch_array($stmtKry, SQLSRV_FETCH_ASSOC);
        if ($rowK && strcasecmp($rowK['nama'], $namaLengkap) === 0) {
            $table = 'Karyawan';
            $idCol = 'npk';
            $idVal = $rowK['id'];
        }
    }

    if ($table === null) {
        return [false, 'Email atau nama tidak ditemukan.'];
    }

    $newPass = generateSecurePassword();

    $updateSql = "UPDATE $table SET kataSandi = ? WHERE $idCol = ?";
    $updateStmt = sqlsrv_query($conn, $updateSql, [$newPass, $idVal]);
    if ($updateStmt === false) {
        return [false, 'Gagal memperbarui kata sandi.'];
    }

    // Kirim email menggunakan PHPMailer SMTP
    $configMail = require __DIR__ . '/../config_mail.php';

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $configMail['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $configMail['username'];
        $mail->Password   = $configMail['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $configMail['port'];

        $mail->setFrom($configMail['from_email'], $configMail['from_name']);
        $mail->addAddress($email, $namaLengkap);

        $mail->Subject = 'Reset Kata Sandi - Sistem Pengelolaan Laboratorium';
        $mail->Body    = "Halo $namaLengkap,\n\nKata sandi sementara Anda: $newPass\n\nSegera ganti setelah login.";

        $mail->send();
        return [true, 'Kata sandi sementara telah dikirim ke email Anda.'];
    } catch (Exception $e) {
        return [true, 'Kata sandi sementara berhasil dibuat, namun email gagal dikirim: ' . $mail->ErrorInfo];
    }
}