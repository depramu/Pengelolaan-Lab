<?php
session_start();
$error_message = '';

require_once __DIR__ . '/../function/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        $error_message = '*Harus diisi';
    } else {
        require_once __DIR__ . '/../function/reset_password_helper.php';
        try {
            [$success, $msg] = resetUserPassword($conn, $email);
            if ($success) {
                $_SESSION['flash_success'] = $msg ?: 'Kata sandi baru berhasil dikirim ke email Anda';
                header('Location: LupaSandi.php');
                exit;
            } else {
                $error_message = $msg;
            }
        } catch (Exception $e) {
            $error_message = 'Terjadi kesalahan: ' . $e->getMessage();
            // Set session notification for error
            $_SESSION['notifikasi'] = [
                'type' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - Sistem Pengelolaan Laboratorium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    
</head>
<div class="container-login">
    <div class="login-left">
        <div class="w-100 mb-4">
            <img src="../icon/logo-astratech.png" alt="Logo Astra" style="width:60px; margin-bottom:12px; display:block;">
        </div>
        <div class="d-flex align-items-center justify-content-center w-100 mb-2" style="gap: 32px;">
            <img src="../icon/atoyRole.png" alt="Ilustrasi" class="role-illustration">
            <div class="d-flex flex-column align-items-start">
                <div class="role-title text-start">Sistem<br>Pengelolaan<br>Laboratorium</div>
                <img src="../icon/iconRole.png" alt="Icon Role" class="icon-role-img">
            </div>
        </div>
    </div>
    <div class="login-right">
        <div class="login-form-container">
            <h2 class="login-form-title">Lupa Kata Sandi</h2>

            <?php
            $success_message = $_SESSION['flash_success'] ?? '';
            unset($_SESSION['flash_success']);
            ?>

            <form method="POST">
                <div class="input-group flex-column mb-3">
                    <label for="email" class="form-label fw-semibold d-flex align-items-center">
                        Email
                        <?php if (!empty($error_message)): ?>
                            <span id="emailError" class="text-danger ms-2" style="font-size:0.9rem;">
                                <?= htmlspecialchars($error_message) ?>
                            </span>
                        <?php endif; ?>
                    </label>
                    <div class="d-flex w-100">
                        <span class="input-group-text"><img src="../icon/mail.svg" alt="Email"></span>
                        <input type="text" id="email" name="email" class="form-control" placeholder="Masukkan Email yang Terdaftar">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4 gap-3">
                    <button type="button" class="btn-login-submit w-50" style="background-color: #6c757d; text-align: center; line-height: normal;" onclick="window.location.href='login.php'">Kembali</button>
                    <button type="submit" class="btn btn-login-submit w-100"  style="max-width: 300px;">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (!empty($success_message)): ?>
<!-- Modal Bootstrap -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="successModalLabel">Berhasil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body text-center">
        <?= htmlspecialchars($success_message) ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
    successModal.show();
  });
</script>
<?php endif; ?>

<?php

include '../templates/footer.php';

?>