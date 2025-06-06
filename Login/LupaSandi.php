<?php
session_start();
include '../koneksi.php';

$error_message = '';

$_SESSION['noHP'] = "Nomor tidak tersedia";

if (isset($conn)) {
    $query_pic_details = "SELECT TOP 1 noHP FROM Karyawan WHERE jenisRole = 'PIC Aset' ORDER BY npk ASC";
    $stmt_pic_details = sqlsrv_query($conn, $query_pic_details);

    if ($stmt_pic_details) {
        $row_pic_details = sqlsrv_fetch_array($stmt_pic_details, SQLSRV_FETCH_ASSOC);
        if ($row_pic_details && !empty($row_pic_details['noHP'])) {
            $_SESSION['noHP'] = htmlspecialchars($row_pic_details['noHP']);
        }
        sqlsrv_free_stmt($stmt_pic_details);
    } else {
        $error_message = 'Terjadi kesalahan pada sistem. Coba lagi nanti.';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $npk = $_POST['npk'];
    $kataSandi = $_POST['kataSandi'];

    if (empty($npk) || empty($kataSandi)) {
        $error_message = 'NPK dan Kata Sandi tidak boleh kosong.';
    } else {
        $query = "SELECT npk, kataSandi, namaKry, jenisRole FROM Karyawan WHERE npk = ?";
        $params = [$npk];
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt === false) {
            $error_message = 'Terjadi kesalahan pada sistem. Coba lagi nanti.';
        } else {
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

            if ($row) {
                if ($kataSandi === $row['kataSandi']) {
                    if (isset($row['jenisRole']) && $row['jenisRole'] === 'PIC Aset') {
                        $_SESSION['user_npk'] = $row['npk'];
                        $_SESSION['user_nama'] = $row['namaKry'];
                        $_SESSION['user_role'] = $row['jenisRole'];

                        header('Location: ../Menu PIC/dashboardPIC.php');
                        exit;
                    } else {
                        $error_message = 'Anda tidak memiliki hak akses sebagai PIC Aset.';
                    }
                } else {
                    $error_message = 'NPK atau Kata Sandi salah.';
                }
            } else {
                $error_message = 'NPK atau Kata Sandi salah.';
            }
        }
        if (isset($stmt)) {
            sqlsrv_free_stmt($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - Sistem Pengelolaan Laboratorium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            /* Latar belakang abu-abu muda seperti umum */
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
            /* Biru yang lebih mendekati gambar */
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
            /* Lebar form */
        }

        .login-form-title {
            color: #fff;
            font-size: 2.2rem;
            /* Ukuran font "Login" */
            font-weight: 600;
            margin-bottom: 35px;
            text-align: center;
        }

        .form-label {
            color: #e0e0e0;
            /* Warna label NPK dan Kata Sandi */
            font-size: 0.9rem;
            margin-bottom: 8px;
            display: block;
            /* Agar margin-bottom bekerja */
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group-text {
            background-color: #fff;
            border: none;
            border-radius: 8px 0 0 8px;
            /* Sudut kiri ikon */
            padding: 0 15px;
            color: #6c757d;
        }

        .input-group-text i {
            font-size: 1.2rem;
        }

        .form-control {
            background-color: #fff;
            border: none;
            border-radius: 0 8px 8px 0;
            /* Sudut kanan input */
            height: 50px;
            padding-left: 15px;
            font-size: 1rem;
            color: #333;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            /* Shadow biru saat fokus */
            background-color: #fff;
            border: none;
        }

        .forgot-link {
            color: #bfe4ff;
            /* Warna link "Lupa Kata Sandi?" */
            font-size: 0.9rem;
            text-align: right;
            display: block;
            margin-top: -10px;
            /* Tarik sedikit ke atas */
            margin-bottom: 30px;
            text-decoration: none;
        }

        .forgot-link:hover {
            color: #fff;
            text-decoration: underline;
        }

        .btn-login-submit {
            background-color: #28a745;
            /* Hijau untuk tombol Masuk */
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 0;
            font-size: 1.1rem;
            font-weight: 600;
            width: 180px;
            /* Lebar tombol tidak penuh */
            display: block;
            margin: 0 auto;
            /* Tombol di tengah */
            transition: background-color 0.3s ease;
        }

        .btn-login-submit:hover {
            background-color: #218838;
            /* Hijau lebih gelap saat hover */
        }

        .alert-danger {
            font-size: 0.9rem;
            padding: 10px;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .login-left {
                flex-basis: 50%;
                padding: 30px 40px;
            }

            .login-right {
                flex-basis: 50%;
                padding: 30px;
            }

            .login-title {
                font-size: 2.2rem;
            }

            .login-illustration {
                width: 320px;
            }

            .login-form-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            .container-login {
                flex-direction: column;
            }

            .login-left,
            .login-right {
                flex-basis: auto;
                /* Reset basis */
                width: 100%;
            }

            .login-left {
                padding: 40px 20px;
                align-items: center;
                /* Tengahkan item di mobile */
                text-align: center;
                /* Judul juga tengah */
                height: auto;
                /* Tinggi otomatis */
                min-height: 300px;
                /* Minimal tinggi untuk konten */
            }

            .login-title {
                text-align: center;
            }

            .login-illustration {
                width: 280px;
                /* Ilustrasi lebih kecil di mobile */
                margin-top: 20px;
            }

            .login-right {
                padding: 30px 20px;
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
                <h3 class="login-form-title fs-3">Hubungi PIC Aset untuk mengubah kata sandi</h3>
                <form action="loginPIC.php" method="POST">
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    <div class="mb-2">
                        <div class="input-group">
                            <span class="input-group-text"><img src="../icon/iconID.svg" alt="Info Kontak"></span>
                            <span class="form-control" style="background-color: #e9ecef; opacity: 1; display: flex; align-items: center;"><?php echo $_SESSION['noHP']; ?></span>
                        </div>
                    </div>

                    <h3 class="text-center">atau</h3>

                    <div class="mb-2">
                        <div class="input-group">
                            <span class="input-group-text"><img src="../icon/iconPass.svg" alt="Info Kata Sandi"></span>
                            <span class="form-control text-center" style="background-color: #e9ecef; opacity: 1; display: flex; align-items: center;">Ruangan Tenaga Pendidik 2,<br>Lantai 1, AstraTech</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        <button type="button" class="btn btn-primary w-75" onclick="window.location.href='../index.php'">Kembali</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>