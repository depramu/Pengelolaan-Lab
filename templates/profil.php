<?php
require_once __DIR__ . '/../function/init.php';
include 'header.php';
include 'sidebar.php';

// Ambil data user dari session
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['user_role'] ?? null;
$user_nama = $_SESSION['user_nama'] ?? null;

$profil = [];

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
    <div class="mb-4">
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
    <div>
        <h2 class="fw-semibold display-5 ms-1 fs-5">Detail Akun</h2>
<div class="card shadow-sm p-4">
    <div class="row g-4">
        <!-- Kolom Kiri: Foto Profil -->
        <div class="col-md-3 text-center">
            <i class="bi bi-person-circle" style="font-size: 8rem; color: #282727ff;"></i>
            <h5 class="mt-3 fw-semibold"><?= htmlspecialchars($profil['nama'] ?? '') ?></h5>
            <p class="text-muted mb-0"><?= htmlspecialchars($profil['role'] ?? '') ?></p>
        </div>

        <!-- Kolom Kanan: Detail Info -->
        <div class="col-md-8 mt-5">
            <?php if ($profil): ?>
                <div class="row mb-3">
                    <div class="col-sm-5 fw-semibold"> <?= isset($profil['nim']) ? 'NIM' : 'NPK' ?> </div>
                    <div class="col-sm-7">: <?= htmlspecialchars($profil['nim'] ?? $profil['npk'] ?? '') ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5 fw-semibold"> Nama Lengkap </div>
                    <div class="col-sm-7">: <?= htmlspecialchars($profil['nama'] ?? '') ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5 fw-semibold"> Role </div>
                    <div class="col-sm-7">: <?= htmlspecialchars($profil['role'] ?? '') ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5 fw-semibold"> Email </div>
                    <div class="col-sm-7">: <?= htmlspecialchars($profil['email'] ?? '') ?></div>
                </div>

                <div class="d-flex justify-content-end mt-5 gap-3">
                    <a href="<?= BASE_URL ?>/templates/ubahKataSandi.php" class="btn btn-primary">
                    Ubah Kata Sandi
                    </a>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">Data profil tidak ditemukan.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</main>

<?php include 'footer.php'; ?>