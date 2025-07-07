<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Email - Sistem Pengelolaan Laboratorium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
            <h2 class="login-form-title">Lupa Email</h2>

            <div class="text-center mb-3">
                <p style="color: #fff; margin-bottom: 20px;">Untuk informasi email, silahkan hubungi <br>PIC Aset melalui:</p>

                <div style="background: rgba(255,255,255,0.08); border-radius: 12px; padding: 20px 16px; margin-bottom: 24px;">
                    <div class="d-flex align-items-center justify-content-center mb-3" style="gap: 10px;">
                        <?php
                        // Koneksi ke database SQL Server
                        include '../function/koneksi.php';

                        // Query untuk mendapatkan email PIC Aset dari tabel karyawan (atau mahasiswa jika perlu)
                        $email_pic = 'Belum tersedia';
                        $query = "SELECT TOP 1 email FROM karyawan WHERE jenisRole = 'PIC Aset'";
                        $result = sqlsrv_query($conn, $query);
                        if ($result && ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC))) {
                            $email_pic = $row['email'];
                        }
                        ?>
                        <i class="bi bi-envelope" style="font-size: 20px; color: #fff;"></i>
                        <span style="color: #fff;"><?php echo htmlspecialchars($email_pic); ?></span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center mb-0" style="gap: 10px;">
                        <i class="bi bi-geo-alt" style="font-size: 20px; color: #fff; align-self: flex-start;"></i>
                        <span style="color: #fff; text-align: left;">Ruangan Tenaga Pendidik 2,<br>Lantai 1, AstraTech</span>
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <button type="button" class="btn-login-submit" style="background-color: #6c757d; text-align: center; line-height: normal;" onclick="window.location.href='login.php'">Kembali</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    include '../templates/footer.php';
    ?>