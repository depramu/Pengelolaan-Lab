<?php
include '../function/init.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_destroy();
    session_start();
}

$error_message = '';
$kataSandiError = '';
$role = $_GET['role'] ?? 'Peminjam';

if ($role === 'PIC Aset') {
    $pageTitle = "Login PIC Aset";
    $identifierLabel = "NPK";
    $identifierPlaceholder = "Masukkan NPK Anda";
} elseif ($role === 'KA UPT') {
    $pageTitle = "Login KA UPT";
    $identifierLabel = "NPK";
    $identifierPlaceholder = "Masukkan NPK Anda";
} else {
    $role = 'Peminjam';
    $pageTitle = "Login Peminjam";
    $identifierLabel = "NIM / NPK";
    $identifierPlaceholder = "Masukkan NIM / NPK Anda";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifier = $_POST['identifier'] ?? '';
    $kataSandi = $_POST['kataSandi'] ?? '';

    if (empty($kataSandi)) {
        $kataSandiError = 'Kata Sandi tidak boleh kosong.';
    }

    if (empty($kataSandiError)) {
        if ($role === 'Peminjam') {
            $query_mhs = "SELECT nim, kataSandi, nama FROM Mahasiswa WHERE nim = ?";
            $stmt_mhs = sqlsrv_query($conn, $query_mhs, [$identifier]);
            $row_mhs = $stmt_mhs ? sqlsrv_fetch_array($stmt_mhs, SQLSRV_FETCH_ASSOC) : false;

            if ($row_mhs) {
                if ($kataSandi === $row_mhs['kataSandi']) {
                    $_SESSION['user_id'] = $row_mhs['nim'];
                    $_SESSION['user_nama'] = $row_mhs['nama'];
                    $_SESSION['user_role'] = 'Peminjam';
                    $_SESSION['nim'] = $row_mhs['nim'];
                    header('Location: ../Menu/Menu Peminjam/dashboardPeminjam.php');
                    exit;
                } else {
                    $kataSandiError = 'Kata Sandi salah.';
                }
            } else {
                $query_kry = "SELECT npk, kataSandi, nama, jenisRole FROM Karyawan WHERE npk = ?";
                $stmt_kry = sqlsrv_query($conn, $query_kry, [$identifier]);
                $row_kry = $stmt_kry ? sqlsrv_fetch_array($stmt_kry, SQLSRV_FETCH_ASSOC) : false;

                if ($row_kry) {
                    if (
                        ($row_kry['jenisRole'] === null || $row_kry['jenisRole'] === '' || $row_kry['jenisRole'] === 'Peminjam') &&
                        $kataSandi === $row_kry['kataSandi']
                    ) {
                        $_SESSION['user_id'] = $row_kry['npk'];
                        $_SESSION['user_nama'] = $row_kry['nama'];
                        $_SESSION['user_role'] = 'Peminjam';
                        $_SESSION['npk'] = $row_kry['npk'];
                        header('Location: ../Menu/Menu Peminjam/dashboardPeminjam.php');
                        exit;
                    } elseif ($kataSandi === $row_kry['kataSandi']) {
                        $kataSandiError = 'Akun Anda bukan peminjam.';
                    } else {
                        $kataSandiError = 'Kata Sandi salah.';
                    }
                } else {
                    $kataSandiError = 'NIM/NPK tidak ditemukan.';
                }
            }
        } elseif ($role === 'PIC Aset' || $role === 'KA UPT') {
            $expectedRole = ($role === 'PIC Aset') ? 'PIC Aset' : 'KA UPT';
            $redirectPath = ($role === 'PIC Aset') ? '../Menu/Menu PIC/dashboardPIC.php' : '../Menu/Menu Ka UPT/dashboardKaUPT.php';

            $query = "SELECT npk, kataSandi, nama, jenisRole FROM Karyawan WHERE npk = ?";
            $stmt = sqlsrv_query($conn,  $query, [$identifier]);
            $row = $stmt ? sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) : false;

            if ($row) {
                if ($kataSandi === $row['kataSandi'] && isset($row['jenisRole']) && $row['jenisRole'] === $expectedRole) {
                    $_SESSION['user_id'] = $row['npk'];
                    $_SESSION['user_nama'] = $row['nama'];
                    $_SESSION['user_role'] = $row['jenisRole'];
                    $_SESSION['npk'] = $row['npk'];
                    header('Location: ' . $redirectPath);
                    exit;
                } elseif ($kataSandi === $row['kataSandi']) {
                    $kataSandiError = "Anda tidak memiliki hak akses sebagai $expectedRole.";
                } else {
                    $kataSandiError = 'Kata Sandi salah.';
                }
            } else {
                $kataSandiError = 'Akun tidak terdaftar.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?> - Sistem Pengelolaan Laboratorium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
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
            <h3 class="login-form-title"><?= htmlspecialchars($pageTitle) ?></h3>
            <form action="login.php?role=<?= htmlspecialchars($role) ?>" method="POST" id="loginForm">
                <div class="mb-3">
                    <label for="identifier" class="form-label d-flex align-items-start">
                        <span><?= htmlspecialchars($identifierLabel) ?></span>
                        <span id="identifier-error" class="text-danger" style="font-size: 0.9rem; padding-left: 10px;"></span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><img src="../icon/iconID.svg" alt=""></span>
                        <input type="text" class="form-control" id="identifier" name="identifier" placeholder="<?= htmlspecialchars($identifierPlaceholder) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="kataSandi" class="form-label d-flex align-items-start">
                        <span>Kata Sandi</span>
                        <span id="kataSandi-error" class="text-danger" style="font-size: 0.9rem; padding-left: 10px;">
                            <?= htmlspecialchars($kataSandiError) ?>
                        </span>
                    </label>
                    <div class="input-group password-wrapper">
                        <span class="input-group-text"><img src="../icon/iconPass.svg" alt=""></span>
                        <input type="password" class="form-control" id="kataSandi" name="kataSandi" placeholder="Masukkan Kata Sandi Anda">
                        <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                            <i class="fa fa-eye-slash" id="eyeIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="LupaSandi.php" class="forgot-link text-white">Lupa Kata Sandi?</a>
                    <a href="LupaEmail.php" class="forgot-link text-white">Lupa Email?</a>
                </div>

                <div class="d-flex justify-content-center" style="gap: 12px;">
                    <button type="button" class="btn-login-submit w-50" style="background-color: #6c757d;" onclick="window.location.href='../index.php'">Kembali</button>
                    <button type="submit" class="btn-login-submit w-100" style="max-width: 300px;">Masuk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script toggle password -->
<script>
document.getElementById("togglePassword").addEventListener("click", function () {
    const input = document.getElementById("kataSandi");
    const icon = document.getElementById("eyeIcon");
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    }
});
</script>

<?php include '../templates/footer.php'; ?>
</body>
</html>
