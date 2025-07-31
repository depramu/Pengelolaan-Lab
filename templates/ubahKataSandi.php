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
$error_kataSandi = '';
$showModal = false;

// Proses update sandi jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kataSandi']) && $user_id) {
    $kataSandiBaru = $_POST['kataSandi'];
    if (empty($kataSandiBaru)) {
        $error_kataSandi = "*Tidak boleh kosong";
    } elseif (strlen($kataSandiBaru) < 8) {
        $error_kataSandi = "*Minimal 8 karakter";
    } else {
        $query_cek = "SELECT nim FROM Mahasiswa WHERE nim = ?";
        $params_cek = [$user_id];
        $stmt_cek = sqlsrv_query($conn, $query_cek, $params_cek);
        if ($stmt_cek && sqlsrv_has_rows($stmt_cek)) {
            $query = "UPDATE Mahasiswa SET kataSandi = ? WHERE nim = ?";
            $params = [$kataSandiBaru, $user_id];
        } else {
            $query = "UPDATE Karyawan SET kataSandi = ? WHERE npk = ?";
            $params = [$kataSandiBaru, $user_id];
        }

        if (isset($query) && isset($params)) {
            $stmt_update = sqlsrv_query($conn, $query, $params);
            if ($stmt_update) {
                $showModal = true;
            } else {
                $error_message = "Gagal mengubah kata sandi.";
                if (($errors = sqlsrv_errors()) != null) {
                    foreach ($errors as $err) {
                        $error_message .= "<br>SQLSTATE: " . $err['SQLSTATE'] . " - " . $err['message'];
                    }
                }
            }
        }
    }
}

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
        <h2 class="fw-semibold display-5 ms-1 fs-5">Ubah Kata Sandi</h2>
        <div class="card-body ms-1 mt-3">
            <div class="card p-4 shadow-sm">
                <form method="POST">
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                    <?php elseif ($success_message): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
                    <?php endif; ?>

                    <?php if ($profil): ?>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-semibold">
                                <?= isset($profil['nim']) ? 'NIM' : (isset($profil['npk']) ? 'NPK' : 'ID') ?>
                                <span class="float-end">:</span>
                            </label>
                            <div class="col-sm-8">
                                <div class="form-control-plaintext"><?= htmlspecialchars($profil['nim'] ?? $profil['npk']) ?></div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-semibold">Nama Lengkap <span class="float-end">:</span></label>
                            <div class="col-sm-8">
                                <div class="form-control-plaintext"><?= htmlspecialchars($profil['nama']) ?></div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-semibold">Role <span class="float-end">:</span></label>
                            <div class="col-sm-8">
                                <div class="form-control-plaintext"><?= htmlspecialchars($profil['role']) ?></div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-semibold">Email <span class="float-end">:</span></label>
                            <div class="col-sm-8">
                                <div class="form-control-plaintext"><?= htmlspecialchars($profil['email']) ?></div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-semibold">Kata Sandi 
                                <span id="error_kataSandi" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                            <?php if (!empty($error_kataSandi)): ?>
                                                <span class="fw-normal text-danger ms-2" style="font-size:0.95em;"><?= $error_kataSandi ?></span>
                                            <?php endif; ?>
                                <span class="float-end">:</span></label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" name="kataSandi" placeholder="Masukkan kata sandi baru..">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4 gap-3">
                            <a href='<?= BASE_URL ?>/templates/profil.php' class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">Data profil tidak ditemukan.</div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
</main>

<?php include 'footer.php'; ?>