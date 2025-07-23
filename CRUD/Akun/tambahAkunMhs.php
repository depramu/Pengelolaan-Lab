<?php
require_once __DIR__ . '/../../function/init.php';
authorize_role(['PIC Aset']);


// Buffer any accidental output so header() redirect works
ob_start();

$showModal = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $jenisRole = $_POST['jenisRole'];

    // Auto-generate secure random password
    require_once __DIR__ . '/../../function/reset_password_helper.php';
    $kataSandi = generateSecurePassword();

    $cekNim = sqlsrv_query($conn, "SELECT nim FROM Mahasiswa WHERE nim = ?", [$nim]);
    if ($cekNim && sqlsrv_has_rows($cekNim)) {
        $nimError = "*NIM sudah terdaftar";
    } else {
        $nimError = '';     // Reset pesan error jika sebelumnya ada error
        $query = "INSERT INTO Mahasiswa (nim, nama, email, jenisRole, kataSandi) VALUES (?, ?, ?, ?, ?)";
        $params = [$nim, $nama, $email, $jenisRole, $kataSandi];
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt) {
            $showModal = true;
            require_once __DIR__ . '/../../function/email_mhs.php';
            sendAccountUser($email, $nama, $nim, $kataSandi);
        } else {
            $error = "Gagal menambahkan akun.";
        }
    }
}

include '../../templates/header.php';
include '../../templates/sidebar.php';
?>

<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Manajemen Akun Mahasiswa</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/manajemenAkunMhs.php">Manajemen Akun Mahasiswa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Akun Mahasiswa</li>
            </ol>
        </nav>
    </div>
    <div class="container mt-4">
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12 " style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header border-bottom border-dark text-white" style="background-color:rgb(9, 103, 185);">
                        <span class="fw-semibold">Tambah Akun Mahasiswa</span>
                    </div>
                    <div class="card-body">
                        <form id="formTambahAkunMhs" method="POST">
                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nim" class="form-label fw-semibold d-flex align-items-center">
                                            NIM
                                            <span id="nimError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                            <?php if (!empty($nimError)): ?>
                                                <span class="fw-normal text-danger ms-2" style="font-size:0.95em;"><?= $nimError ?></span>
                                            <?php endif; ?>
                                        </label>
                                        <input type="text" class="form-control" id="nim" name="nim" placeholder="Masukkan NIM.." value="<?= isset($nim) ? htmlspecialchars($nim) : '' ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="nama" class="form-label fw-semibold d-flex align-items-center">
                                            Nama Lengkap
                                            <span id="namaError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                        </label>
                                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama lengkap.." value="<?= isset($nama) ? htmlspecialchars($nama) : '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-semibold d-flex align-items-center">
                                            Email
                                            <span id="emailError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                        </label>
                                        <input type="text" class="form-control" id="email" name="email" placeholder="Masukkan email.." value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="jenisRole" class="form-label fw-semibold d-flex align-items-center">
                                            Role
                                            <span id="roleError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                        </label>
                                        <select class="form-select" id="jenisRole" name="jenisRole">
                                            <option hidden value="" <?= (!isset($jenisRole) || $jenisRole == '') ? 'selected' : '' ?>>Pilih Role</option>
                                            <option value="Peminjam" <?= (isset($jenisRole) && $jenisRole == 'Peminjam') ? 'selected' : '' ?>>Peminjam</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="<?= BASE_URL ?>/Menu/Menu PIC/manajemenAkunMhs.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../../templates/footer.php'; ?>