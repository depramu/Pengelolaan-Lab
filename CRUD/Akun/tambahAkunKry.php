<?php
require_once __DIR__ . '/../../function/init.php';
authorize_role('PIC Aset');

// Buffer output to ensure header() redirects work even if some whitespace or HTML comes later
ob_start();

// Load PHPMailer (stand-alone, same as reset_password_helper)
require_once __DIR__ . '/../../function/src/PHPMailer.php';
require_once __DIR__ . '/../../function/src/SMTP.php';
require_once __DIR__ . '/../../function/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$showModal = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $npk = $_POST['npk'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $jenisRole = $_POST['jenisRole'];

    // Auto-generate secure random password
    require_once __DIR__ . '/../../function/reset_password_helper.php';
    $kataSandi = generateSecurePassword();
    $konfirmasiSandi = $kataSandi; // For consistency, not used further

    $cekNpk = sqlsrv_query($conn, "SELECT npk FROM Karyawan WHERE npk = ?", [$npk]);
    if ($cekNpk && sqlsrv_has_rows($cekNpk)) {
        $npkError = "*NPK sudah terdaftar";
    } else {
        $query = "INSERT INTO Karyawan (npk, nama, email, jenisRole, kataSandi) VALUES (?, ?, ?, ?, ?)";
        $params = [$npk, $nama, $email, $jenisRole, $kataSandi];
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt) {
            // Kirim kredensial ke email pengguna
            $configMail = require __DIR__ . '/../../function/config_email.php';
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

                $mail->Subject = 'Pembuatan Akun Baru - Sistem Pengelolaan Laboratorium';
                $mail->Body    = "Halo $nama,\n\nAkun Anda telah dibuat oleh PIC Aset. Berikut detail login:\nNPK : $npk\nPassword : $kataSandi\n\nSilakan login ke Sistem Pengelolaan Laboratorium dan segera ubah password Anda.";

                $mail->send();
            } catch (Exception $e) {
                error_log('Email gagal dikirim: ' . $mail->ErrorInfo);
            }

            // Berhasil, alihkan agar tidak mengulang validasi & menampilkan pesan duplikat
            $_SESSION['notif_sukses'] = 'Akun Karyawan berhasil ditambahkan.';
            session_write_close(); // release session lock before redirect
            if (ob_get_length()) {
                ob_end_clean(); // discard any buffered output
            }
            header('Location: ' . BASE_URL . '/Menu/Menu%20PIC/manajemenAkunKry.php');
            exit;
        } else {
            $error = "Gagal menambahkan akun.";
        }
    }
}

include '../../templates/header.php';
include '../../templates/sidebar.php';

?>

<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Manajemen Akun Karyawan</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/manajemenAkunKry.php">Manajemen Akun Karyawan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Akun Karyawan</li>
            </ol>
        </nav>
    </div>
    <div class="container mt-4">
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12 " style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-bold">Tambah Akun Karyawan</span>
                    </div>
                    <div class="card-body">
                        <form id="formTambahAkunKry" method="POST">
                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="npk" class="form-label fw-semibold d-flex align-items-center">
                                            NPK
                                            <span id="npkError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                            <?php if (!empty($npkError)): ?>
                                                <span class="fw-normal text-danger ms-2" style="font-size:0.95em;"><?= $npkError ?></span>
                                            <?php endif; ?>
                                        </label>
                                        <input type="text" class="form-control" id="npk" name="npk" placeholder="Masukkan NPK.." value="<?= isset($npk) ? htmlspecialchars($npk) : '' ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="nama" class="form-label fw-semibold d-flex align-items-center">
                                            Nama Lengkap
                                            <span id="namaError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                        </label>
                                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama lengkap.." value="<?= isset($nama) ? htmlspecialchars($nama) : '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-semibold d-flex align-items-center">
                                            Email
                                            <span id="emailError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                        </label>
                                        <input type="text" class="form-control" id="email" name="email" placeholder="Masukkan email.." value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="jenisRole" class="form-label fw-semibold d-flex align-items-center">
                                            Role
                                            <span id="roleError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                        </label>
                                        <select class="form-select" id="jenisRole" name="jenisRole">
                                            <option hidden value="" <?= (!isset($jenisRole) || $jenisRole == '') ? 'selected' : '' ?>>Pilih Role</option>
                                            <option value="KA UPT" <?= (isset($jenisRole) && $jenisRole == "KA UPT") ? "selected" : "" ?>>KA UPT</option>
                                            <option value="PIC Aset" <?= (isset($jenisRole) && $jenisRole == "PIC Aset") ? "selected" : "" ?>>PIC Aset</option>
                                            <option value="Peminjam" <?= (isset($jenisRole) && $jenisRole == "Peminjam") ? "selected" : "" ?>>Peminjam</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="<?= BASE_URL ?>/Menu/Menu PIC/manajemenAkunKry.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


<?php

include '../../templates/footer.php';

?>