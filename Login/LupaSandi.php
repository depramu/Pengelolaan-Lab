<?php
session_start();
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        $error_message = 'Kolom tidak boleh kosong.';
    } else {
        require_once __DIR__ . '/../koneksi.php';
        require_once __DIR__ . '/../function/reset_password_helper.php';
        [$success, $msg] = resetUserPassword($conn, $email);
        if ($success) {
            $_SESSION['flash_success'] = $msg;
            header('Location: LupaSandi.php');
            exit;
        } else {
            $error_message = $msg;
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
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
        }

        .container-login {
            display: flex;
            height: 100vh;
            width: 100%;
            box-sizing: border-box;
        }

        .login-left {
            background: #fff;
            padding: 48px;
            flex: 1 1 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }

        .role-title {
            font-size: 2.5rem;
            font-weight: 600;
            color: #065ba6;
            margin-bottom: 24px;
        }

        .role-illustration {
            width: 260px;
            min-width: 160px;
            margin-bottom: 0;
        }

        .icon-role-img {
            width: 180px;
            margin-top: 18px;
        }

        img {
            width: 2rem;
        }

        .login-right {
            background: #065ba6;
            flex-basis: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            box-sizing: border-box;
        }

        .login-form-container {
            width: 100%;
            max-width: 380px;
        }

        .login-form-title {
            color: #fff;
            font-size: 1.2rem;
            font-weight: 500;
            margin-bottom: 10px; /* ↓ ganti dari 20px ke 10px */
            text-align: center;
        }


        .form-label {
            color: #e0e0e0;
            font-size: 0.9rem;
            margin-bottom: 8px;
            display: block;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group-text {
            background-color: #fff;
            border: none;
            border-radius: 8px 0 0 8px;
            padding: 0 15px;
            color: #6c757d;
        }

        .input-group-text img {
            width: 20px;
            height: 20px;
        }

        .form-control {
            background-color: #fff;
            border: none;
            border-radius: 0 8px 8px 0;
            height: 50px;
            padding-left: 15px;
            font-size: 1rem;
            color: #333;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            background-color: #fff;
            border: none;
        }

        .btn-login-submit, .btn-back {
            border: none;
            border-radius: 8px;
            padding: 6px 0;
            font-size: 0.9rem;
            font-weight: 600;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .btn-login-submit {
            background-color: #28a745;
            color: #fff;
        }

        .btn-login-submit:hover {
            background-color: #218838;
        }

        .btn-back {
            background-color: #6c757d;
            color: #fff;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }

        /* Custom style for the error message to match the screenshot */
        .alert-danger {
            font-size: 0.9rem;
            padding: 10px;
            background-color: #f8d7da; /* Light red background */
            color: #721c24; /* Dark red text */
            border-color: #f5c6cb; /* Red border */
            border-radius: 0; /* Remove border-radius */
            margin-bottom: 20px; /* Add some margin below */
            text-align: left; /* Align text to left */
        }

        @media (max-width: 768px) {
            .container-login {
                flex-direction: column;
            }

            .login-left, .login-right {
                width: 100%;
                padding: 20px;
            }

            .login-left {
                text-align: center;
                height: auto;
                min-height: 300px;
            }

            .login-right {
                min-height: 350px;
            }

            .login-form-container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
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
            <h2 class="text-center fw-75 mb-5" style="color: #fff;">Lupa Kata Sandi</h2>
            <h3 class="login-form-title" style="margin-bottom: 10px;">Silahkan Masukkan Data</h3>

            <form action="" method="POST" novalidate>

                <?php
                    // Success flash
                    $success_message = $_SESSION['flash_success'] ?? '';
                    unset($_SESSION['flash_success']);
                ?>
                <?php if ($success_message): ?>
                    <div class="alert alert-success" role="alert">
                        <?= htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <div class="input-group flex-column">
                    <span id="emailError" class="text-danger ms-2" style="display:none;font-size:0.9rem;"></span>
                    <div class="d-flex w-100">
                        <span class="input-group-text"><img src="../icon/mail.svg" alt="Email"></span>
                        <input type="text" id="email" name="email" class="form-control" placeholder="Masukkan Email">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4 gap-3">
                    <button type="button" class="btn btn-back">Kembali</button>
                    <button type="submit" class="btn btn-login-submit">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.querySelector('.btn-back').onclick = function () {
        window.location.href = '../index.php';
    };

    document.querySelector('form').addEventListener('submit', function(e){
        let email = document.getElementById('email').value.trim();
        let emailError = document.getElementById('emailError');
        let valid = true;
        // reset
        emailError.style.display = 'none';


        if(email === ''){
            emailError.textContent = '*Harus diisi';
            emailError.style.display = 'inline';
            valid = false;
        } else if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
            emailError.textContent = '*Format email tidak valid';
            emailError.style.display = 'inline';
            valid = false;
        }

        if(!valid){
            e.preventDefault();
        }
    });
</script>
</body>
</html>