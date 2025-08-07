<?php
// Helper functions for password reset
require_once __DIR__ . '/src/PHPMailer.php';
require_once __DIR__ . '/src/SMTP.php';
require_once __DIR__ . '/src/Exception.php';

// Database connection
require_once __DIR__ . '/../function/koneksi.php';

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
function resetUserPassword($conn, string $email): array
{
    // First, attempt to locate the user in Mahasiswa
    $sqlMhs = "SELECT nim AS id, nama AS nama FROM Mahasiswa WHERE email = ?";
    $stmtMhs = sqlsrv_query($conn, $sqlMhs, [$email]);
    if ($stmtMhs === false) {
        error_log('SQLSRV Mahasiswa query error: ' . print_r(sqlsrv_errors(), true));
        return [false, 'Terjadi kesalahan database.'];
    }
    $row = sqlsrv_fetch_array($stmtMhs, SQLSRV_FETCH_ASSOC);

    $table = null;
    $idCol = null;
    $idVal = null;
    $namaLengkap = null;

    if ($row) {
        $table = 'Mahasiswa';
        $idCol = 'nim';
        $idVal = $row['id'];
        $namaLengkap = $row['nama'];
    } else {
        // Check Karyawan by email
        $sqlKry = "SELECT npk AS id, nama AS nama FROM Karyawan WHERE email = ?";
        $stmtKry = sqlsrv_query($conn, $sqlKry, [$email]);
        if ($stmtKry === false) {
            error_log('SQLSRV Karyawan query error: ' . print_r(sqlsrv_errors(), true));
            return [false, 'Terjadi kesalahan database.'];
        }
        $rowK = sqlsrv_fetch_array($stmtKry, SQLSRV_FETCH_ASSOC);
        if ($rowK) {
            $table = 'Karyawan';
            $idCol = 'npk';
            $idVal = $rowK['id'];
            $namaLengkap = $rowK['nama'];
        }
    }

    if ($table === null) {
        return [false, '*Email tidak ditemukan'];
    }

    $newPass = generateSecurePassword();

    // Update password using positional parameters
    $updateSql = "UPDATE $table SET kataSandi = ?, nama = ? WHERE email = ?";
    $params = [$newPass, $namaLengkap, $email];
    try {
        $updateStmt = sqlsrv_query($conn, $updateSql, $params);
        if ($updateStmt === false) {
            $errors = sqlsrv_errors(SQLSRV_ERR_ALL);
            $errorDetails = [];
            foreach ($errors as $error) {
                $errorDetails[] = "Error Code: " . $error['code'] .
                    "\nSQL State: " . $error['SQLSTATE'] .
                    "\nMessage: " . $error['message'];
            }

            $error_message = "Database Error: " . implode("\n", $errorDetails);

            // Also try to get the last query
            $lastQuery = sqlsrv_query($conn, "SELECT @@ROWCOUNT AS 'RowCount'");
            if ($lastQuery) {
                $rowCount = sqlsrv_fetch_array($lastQuery, SQLSRV_FETCH_ASSOC);
                $errorDetails[] = "Rows affected: " . $rowCount['RowCount'];
            }

            // Check if the email exists in the table
            $checkSql = "SELECT email FROM $table WHERE email = ?";
            $checkStmt = sqlsrv_query($conn, $checkSql, [$email]);
            if ($checkStmt && sqlsrv_has_rows($checkStmt)) {
                error_log("Email exists in table but update failed");
            } else {
                error_log("Email does not exist in table");
            }

            return [false, $error_message];
        }

        // If update was successful
        // Kirim email menggunakan PHPMailer SMTP
        $configMail = require __DIR__ . '/config_email.php';

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

            // Kirim email dalam format HTML
            $mail->isHTML(true);

            // Determine role based on table
            $role = $table === 'Karyawan' ? 'PIC Aset' : 'Peminjam';
            $loginUrl = (defined('BASE_URL') ? BASE_URL : 'http://localhost:8080/projek-pengelolaan-lab/Pengelolaan-Lab') . '/Login/login.php?role=' . rawurlencode($role);

            $mail->Subject = 'Reset Kata Sandi - Sistem Pengelolaan Laboratorium';
            $mail->Body    = "
                <div style=\"font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#333333;\">
                    <p>Halo <strong>$namaLengkap</strong>,</p>
                    <p>Kata sandi sementara Anda telah direset. Berikut detailnya:</p>
                    <table style=\"margin:10px 0;\">
                        <tr><td style=\"padding:4px 10px 4px 0;\"><strong>Kata&nbsp;Sandi&nbsp;Baru</strong></td><td>$newPass</td></tr>
                    </table>
                    <p>Silakan klik tombol di bawah ini untuk login dan <em>segera</em> mengganti kata sandi demi keamanan akun.</p>
                    <p style=\"text-align:center;margin:20px 0;\">
                        <a href=\"$loginUrl\" style=\"background-color:#0967B9;color:#ffffff;padding:12px 24px;text-decoration:none;border-radius:4px;display:inline-block;\">Login ke Sistem</a>
                    </p>
                    <p>Jika mengalami kendala, hubungi <strong>PIC&nbsp;Aset</strong>.</p>
                    <p>Hormat kami,<br><strong>Tim Pengelola Laboratorium</strong></p>
                    <hr style=\"border:none;border-top:1px solid #cccccc;\">
                    <p style=\"font-size:12px;color:#777777;\"><em>Email ini dikirim otomatis; mohon untuk tidak membalas.</em></p>
                </div>
            ";

            $mail->send();
            error_log("Password reset successful for email: $email");
            error_log("Password reset completed successfully");
            return [true, 'Kata sandi baru berhasil dikirim ke email Anda'];
        } catch (Exception $e) {
            error_log('Gagal mengirim email: ' . $mail->ErrorInfo);
            return [false, 'Kata sandi berhasil dibuat, namun email gagal dikirim.'];
        }
    } catch (Exception $e) {
        return [false, 'Terjadi kesalahan: ' . $e->getMessage()];
    }
}
