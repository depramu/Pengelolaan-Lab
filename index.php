<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Role - Sistem Pengelolaan Laboratorium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f7f8fa;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        .role-container {
            height: 100vh;
            display: flex;
            align-items: stretch;
            justify-content: center;
        }

        .role-card {
            background: #fff;
            overflow: hidden;
            display: flex;
            max-width: 100vw;
            height: 100vh;
            width: 100%;
        }

        .role-left {
            background: #fff;
            padding: 48px;
            flex: 1 1 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .role-logo {
            width: 60px;
            margin-bottom: 16px;
        }

        .role-title {
            font-size: 2.5rem;
            font-weight: 600;
            color: #065ba6;
            margin-bottom: 24px;
            text-align: center;
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

        .role-right {
            background: #065ba6;
            color: #fff;
            flex-basis: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .role-right h3 {
            font-weight: 600;
            margin-bottom: 32px;
            font-size: 2rem;
        }

        .role-btn {
            width: 25rem;
            margin-bottom: 22px;
            font-weight: 500;
            font-size: 1.2rem;
            border-radius: 8px;
            padding: 10px;
        }

        @media (max-width: 768px) {
            .role-card {
                flex-direction: column;
                max-width: 99vw;
                height: 100vh;
                min-height: 0;
            }

            .role-left,
            .role-right {
                border-radius: 0;
                padding: 32px 10px;
                height: 50vh;
                min-height: 0;
            }

            .role-right {
                border-radius: 0 0 24px 24px;
            }

            .role-title {
                font-size: 1.2rem;
            }

            .role-btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="role-container ">
        <div class="role-card">
            <div class="role-left w-100">
                <div class="w-100 mb-4">
                    <img src="icon/logo-astratech.png" alt="Logo Astra" style="width:60px; margin-bottom:12px; display:block;">
                </div>
                <div class="d-flex align-items-center justify-content-center w-100 mb-2" style="gap: 32px;">
                    <img src="icon/atoyRole.png" alt="Ilustrasi" class="role-illustration">
                    <div class="d-flex flex-column align-items-start">
                        <div class="role-title text-start">Sistem<br>Pengelolaan<br>Laboratorium</div>
                        <img src="icon/iconRole.png" alt="Icon Role" class="icon-role-img">
                    </div>
                </div>
            </div>
            <div class="role-right">
                <h3>Login Sebagai</h3>
                <a href="Login/login.php?jenisRole=Peminjam" class="btn btn-light role-btn fw-bold" style="color: #065ba6;">Peminjam</a>
                <a href="Login/login.php?jenisRole=PIC Aset" class="btn btn-light role-btn fw-bold" style="color: #065ba6;">PIC Aset</a>
                <a href="Login/login.php?jenisRole=Ka UPT" class="btn btn-light role-btn fw-bold" style="color: #065ba6;">Ka UPT</a>
            </div>
        </div>
    </div>
</body>

</html>