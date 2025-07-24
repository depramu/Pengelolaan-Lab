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
$query = "SELECT nim, nama, email, kataSandi FROM Mahasiswa WHERE nim = ?";
$stmt = sqlsrv_query($conn, $query, [$user_id]);
if ($stmt && sqlsrv_has_rows($stmt)) {
    $profil = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $profil['nim'] = $profil['nim'];
    $profil['nama'] = $profil['nama'];
    $profil['role'] = 'Peminjam (Mahasiswa)';
    $profil['email'] = $profil['email'];
    $profil['kataSandi'] = $profil['kataSandi'];
} else {
    // Jika tidak ditemukan, cek di Karyawan
    $query = "SELECT npk, nama, email, jenisRole, kataSandi FROM Karyawan WHERE npk = ?";
    $stmt = sqlsrv_query($conn, $query, [$user_id]);
    if ($stmt && sqlsrv_has_rows($stmt)) {
        $profil = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $profil['npk'] = $profil['npk'];
        $profil['nama'] = $profil['nama'];
        $profil['role'] = $profil['jenisRole'] ?? 'Peminjam (Karyawan)';
        $profil['email'] = $profil['email'];
        $profil['kataSandi'] = $profil['kataSandi'];
    } else {
        $error_message = "Data pengguna tidak ditemukan.";
    }
}
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Profil Akun</h3>

    <!-- Breadcrumb -->
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
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/<?= $dashboard_link ?>">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profil Akun</li>
            </ol>
        </nav>
    </div>

    <!-- Section Header -->
    <div class="card shadow-sm mb-4 p-4">
        <div class="d-flex align-items-center">
            <!-- Inisial -->
            <div class="me-4">
                <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center" style="width: 80px; height: 80px; font-size: 28px;">
                    <?= strtoupper(substr($profil['nama'], 0, 1)) ?>
                </div>
            </div>
            <div>
                <h5 class="mb-0 fw-semibold"><?= htmlspecialchars($profil['nama'] ?? '') ?></h5>
                <p class="text-muted mb-0"><?= htmlspecialchars($profil['role'] ?? '') ?></p>
                <p class="text-muted"><?= htmlspecialchars($profil['email'] ?? '') ?></p>
            </div>
        </div>
    </div>

    <!-- Informasi Detail -->
    <div class="card shadow-sm mb-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-semibold">Informasi Akun</h5>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <small class="text-muted"><?= isset($profil['nim']) ? 'NIM' : 'NPK' ?></small>
                <div class="fw-semibold"><?= htmlspecialchars($profil['nim'] ?? $profil['npk'] ?? '-') ?></div>
            </div>
            <div class="col-md-4 mb-3">
                <small class="text-muted">Nama Lengkap</small>
                <div class="fw-semibold"><?= htmlspecialchars($profil['nama'] ?? '-') ?></div>
            </div>
            <div class="col-md-4 mb-3">
                <small class="text-muted">Role</small>
                <div class="fw-semibold"><?= htmlspecialchars($profil['role'] ?? '-') ?></div>
            </div>
            <div class="col-md-6 mb-3">
                <small class="text-muted">Email</small>
                <div class="fw-semibold"><?= htmlspecialchars($profil['email'] ?? '-') ?></div>
            </div>
            <div class="d-flex justify-content-end">
                <a href="<?= BASE_URL ?>/templates/ubahKataSandi.php" class="btn btn-primary">
                Ubah Kata Sandi
            </a>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>