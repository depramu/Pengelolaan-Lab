<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pengelolaan Laboratorium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f7f8fa;
        }

        .container-login {
            display: flex;
            height: 100vh;
        }

        .login-left {
            background: #fff;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .login-left img {
            width: 80px;
            margin-bottom: 24px;
        }

        .login-title {
            font-size: 2.3rem;
            font-weight: 600;
            color: #1766b5;
            margin-bottom: 24px;
            text-align: left;
        }

        .login-illustration {
            width: 270px;
            margin-bottom: 24px;
            height: 100%;
        }

        .login-right {
            background: #065ba6;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 40px 32px 32px 32px;
            min-width: 340px;
            width: 100%;
            max-width: 30rem;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }

        .login-card h3 {
            color: #fff;
            font-weight: 600;
            text-align: center;
            margin-bottom: 32px;
        }

        .form-label {
            color: #fff;
            font-weight: 500;
        }

        .input-group-text {
            background: #fff;
            border: none;
            border-radius: 8px 0 0 8px;
        }

        .form-control {
            border-radius: 0 8px 8px 0;
            border: none;
            height: 48px;
            font-size: 1.1rem;
        }

        .form-control:focus {
            box-shadow: none;
            border: 2px solid #1766b5;
        }

        .btn-login {
            background: #2ecc71;
            color: #fff;
            font-weight: 600;
            border-radius: 8px;
            height: 48px;
            font-size: 1.1rem;
            margin-top: 18px;
            margin-bottom: 8px;
            border: none;
            transition: background 0.2s;
        }

        .btn-login:hover {
            background: #27ae60;
        }

        .forgot-link {
            color: #fff;
            text-decoration: none;
            font-size: 0.98rem;
            text-align: right;
            display: block;
            margin-bottom: 10px;
        }

        @media (max-width: 900px) {
            .container-login {
                flex-direction: flex;
            }

            .login-left,
            .login-right {
                border-radius: 0;
                min-height: 40vh;
            }

            .login-right {
                border-top-left-radius: 0;
                border-bottom-left-radius: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container-login">
        <div class="login-left d-flex flex-column align-items-center justify-content-center">
            <div class="login-logo-container d-colum justify-content-center">
                <img src="icon/logo-astratech.png" alt="Logo Astra">
            </div>

            <div class="login-title">Sistem<br>Pengelolaan<br>Laboratorium</div>
            <div class="login-illustration-container">
                <img src="icon/atoyRole.png" alt="Ilustrasi" class="login-illustration">
            </div>
        </div>
        <div class="login-right">
            <div class="login-card">
                <h3>Login</h3>
                <form action="loginPIC.php" method="POST">
                    <div class="mb-3">
                        <label for="npk" class="form-label">NPK</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="npk" name="npk" placeholder="Masukkan NPK" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Kata Sandi" required>
                        </div>
                    </div>
                    <a href="lupaSandi.php" class="forgot-link">Lupa Kata Sandi?</a>
                    <button type="submit" class="btn btn-login w-100">Masuk</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>