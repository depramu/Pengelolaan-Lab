<?php
require_once __DIR__ . '/src/PHPMailer.php';
require_once __DIR__ . '/src/SMTP.php';
require_once __DIR__ . '/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendAccountUser($email, $nama, $nim, $kataSandi): bool {
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
        $mail->addAddress($email, $nama);
        $mail->isHTML(true);
        $mail->Subject = 'Akses Akun Sistem Pengelolaan Laboratorium';
        $mail->Body = "
            <p>Dear Saudara/i <strong>$nama</strong>,</p>
            <p>Kami informasikan bahwa Saudara/i telah mendapatkan akun untuk mengakses <strong>Sistem Pengelolaan Laboratorium</strong> dengan rincian sebagai berikut:</p>
            <p>
                Nomor Induk Mahasiswa (NIM): <strong>$nim</strong><br>
                Kata Sandi: <strong>$kataSandi</strong>
            </p>
            <p>Akun ini dapat digunakan untuk login ke sistem guna mengelola aktivitas peminjaman serta pengembalian aset laboratorium. Kami menyarankan Saudara/i untuk segera mengganti kata sandi setelah login demi menjaga keamanan akun.</p>
            <p>Jika Saudara/i mengalami kendala atau memiliki pertanyaan, silakan hubungi <strong>PIC Aset</strong>.</p>
            <p>Hormat kami,<br>
            <strong>Tim Pengelola Laboratorium</strong></p>
            <p><em>Catatan:<br>
            Email ini dikirim secara otomatis oleh sistem. Mohon untuk tidak membalas email ini.</em></p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Gagal kirim email: ' . $mail->ErrorInfo);
        return false;
    }
}
