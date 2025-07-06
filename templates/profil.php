<?php
require_once __DIR__ . '/../function/init.php';
include 'header.php';
include 'sidebar.php';

// Ambil data user dari session
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['user_role'] ?? null;
$user_nama = $_SESSION['user_nama'] ?? null;

$profil = [];
$error_message = '';
$success_message = '';
$showModal = false;

// Proses update sandi jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kataSandi']) && $user_id) {
    $kataSandiBaru = $_POST['kataSandi'];
    if (!empty($kataSandiBaru)) {
        if ($user_role === 'Peminjam') {
            $query = "UPDATE Mahasiswa SET kataSandi = ? WHERE nim = ?";
            $params = [$kataSandiBaru, $user_id];
        } else {
            $query = "UPDATE Karyawan SET kataSandi = ? WHERE npk = ?";
            $params = [$kataSandiBaru, $user_id];
        }

        $stmt = sqlsrv_query($conn, $query, $params);
        if ($stmt) {
            $showModal = true;
        } else {
            $error_message = "Gagal mengubah kata sandi.";
            if (($errors = sqlsrv_errors()) != null) {
                foreach ($errors as $err) {
                    $error_message .= "<br>SQLSTATE: " . $err['SQLSTATE'] . " - " . $err['message'];
                }
            }
        }
    } else {
        $error_message = "Kata sandi tidak boleh kosong jika ingin mengubah.";
    }
}


// Ambil data profil
if ($user_id && $user_role) {
    if ($user_role === 'Peminjam') {
        $query = "SELECT nim, nama, email, kataSandi FROM Mahasiswa WHERE nim = ?";
        $stmt = sqlsrv_query($conn, $query, array($user_id));
        if ($stmt === false) {
            $error_message = "Gagal mengambil data Mahasiswa.";
        } else {
            $profil = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            if ($profil) {
                $profil['nim'] = $profil['nim'];
                $profil['nama'] = $profil['nama'];
                $profil['role'] = 'Peminjam (Mahasiswa)';
                $profil['email'] = $profil['email'];
                $profil['kataSandi'] = $profil['kataSandi'];
            }
        }
    } else {
        $query = "SELECT npk, nama, email, jenisRole, kataSandi FROM Karyawan WHERE npk = ?";
        $stmt = sqlsrv_query($conn, $query, array($user_id));
        if ($stmt === false) {
            $error_message = "Gagal mengambil data Karyawan.";
        } else {
            $profil = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            if ($profil) {
                $profil['npk'] = $profil['npk'];
                $profil['nama'] = $profil['nama'];
                $profil['role'] = $profil['jenisRole'] ?? 'Peminjam (Karyawan)';
                $profil['email'] = $profil['email'];
                $profil['kataSandi'] = $profil['kataSandi'];
            }
        }
    }
} else {
    $error_message = "Anda belum login.";
}
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Profil Akun</h3>
    <div class="mb-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <?php
                // Sesuaikan dashboard berdasarkan role
                $base_url = 'Menu Peminjam/';
                $dashboard_link = $base_url . 'dashboardPeminjam.php';
                if ($user_role === 'PIC Aset') {
                    $base_url = 'Menu PIC/';
                    $dashboard_link = $base_url . 'dashboardPIC.php';
                } elseif ($user_role === 'KA UPT') {
                    $base_url = 'Menu KA UPT/';
                    $dashboard_link = $base_url . 'dashboardKAUPT.php';
                }
                ?>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profil Akun</li>
            </ol>
        </nav>
    </div>
    <div class="col-md-9">
        <h2 class="fw-bold display-5" style="margin-left: 50px; margin-bottom: -30px; font-size:1.25rem;">Data Akun</h2>
        <div class="card-body p-4 p-md-5">
            <div class="d-flex align-items-center mb-3 pb-1">
                <div class="me-4">
                    <i class="bi bi-person-circle" style="font-size: 8rem; color: #343a40;"></i>
                </div>
                <div class="col-md-9 ps-5">
                    <form method="POST" id="profilForm">
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                        <?php elseif ($success_message): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
                        <?php endif; ?>
                        <?php if ($profil): ?>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label fw-semibold">
                                    <?php
                                    if (isset($profil['nim'])) {
                                        echo 'NIM';
                                    } elseif (isset($profil['npk'])) {
                                        echo 'NPK';
                                    } else {
                                        echo 'ID';
                                    }
                                    ?>
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control protect-input d-block bg-light" value="<?= htmlspecialchars($profil['nim'] ?? $profil['npk'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label fw-semibold">Nama Lengkap</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control protect-input d-block bg-light" value="<?= htmlspecialchars($profil['nama']) ?>">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label fw-semibold">Role</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control protect-input d-block bg-light" value="<?= htmlspecialchars($profil['role']) ?>">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label fw-semibold">Email</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control protect-input d-block bg-light" value="<?= htmlspecialchars($profil['email']) ?>">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label fw-semibold">Kata Sandi
                                    <span id="kataSandiError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                </label>
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="kataSandi" value="<?= htmlspecialchars($profil['kataSandi'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">Data profil tidak ditemukan.</div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
</main>

<?php include 'footer.php'; ?>