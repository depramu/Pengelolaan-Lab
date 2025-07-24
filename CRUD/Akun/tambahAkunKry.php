<?php
require_once __DIR__ . '/../../function/init.php';
authorize_role(['PIC Aset']);

// Buffer output to ensure header() redirects work even if some whitespace or HTML comes later
ob_start();

$showModal = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $npk = $_POST['npk'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $jenisRole = $_POST['jenisRole'];

    // Validasi NPK dan Email
    $hasError = false;
    $cekNpk = sqlsrv_query($conn, "SELECT npk FROM Karyawan WHERE npk = ?", [$npk]);
    $cekEmail = sqlsrv_query($conn, "SELECT email FROM Karyawan WHERE email = ?", [$email]);
    if ($cekNpk && sqlsrv_has_rows($cekNpk)) {
        $npkError = "*NPK sudah terdaftar";
        $hasError = true;
    } 
    if ($cekEmail && sqlsrv_has_rows($cekEmail)) {
        $emailError = "*Email sudah terdaftar";
        $hasError = true;
    }
    if (!$hasError) {
        // Auto-generate secure random password
        require_once __DIR__ . '/../../function/reset_password_helper.php';
        $kataSandi = generateSecurePassword();
        $query = "INSERT INTO Karyawan (npk, nama, email, jenisRole, kataSandi) VALUES (?, ?, ?, ?, ?)";
        $params = [$npk, $nama, $email, $jenisRole, $kataSandi];
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt) {
            $showModal = true;
            require_once __DIR__ . '/../../function/email_kry.php';
            sendAccountUser($email, $nama, $npk, $kataSandi);
        } else {
            $error = "Gagal menambahkan akun.";
        }
    }
}

include '../../templates/header.php';
include '../../templates/sidebar.php';

?>

<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Manajemen Akun Karyawan</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/manajemenAkunKry.php">Manajemen Akun Karyawan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Akun Karyawan</li>
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
                        <span class="fw-semibold">Tambah Akun Karyawan</span>
                    </div>
                    <div class="card-body">
                        <form id="formTambahAkunKry" method="POST">
                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="npk" class="form-label fw-semibold d-flex align-items-center">
                                            NPK
                                            <span id="npkError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                            <?php if (!empty($npkError)): ?>
                                                <span class="fw-normal text-danger ms-2" style="font-size:0.95em;"><?= $npkError ?></span>
                                            <?php endif; ?>
                                        </label>
                                        <input type="text" class="form-control" id="npk" name="npk" placeholder="Masukkan NPK.." value="<?= isset($npk) ? htmlspecialchars($npk) : '' ?>">
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
                                            <?php if (!empty($emailError)): ?>
                                                <span class="fw-normal text-danger ms-2" style="font-size:0.95em;"><?= $emailError ?></span>
                                            <?php endif; ?>
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
                                            <option value="KA UPT" <?= (isset($jenisRole) && $jenisRole == "KA UPT") ? "selected" : "" ?>>KA UPT</option>
                                            <option value="PIC Aset" <?= (isset($jenisRole) && $jenisRole == "PIC Aset") ? "selected" : "" ?>>PIC Aset</option>
                                            <option value="Peminjam" <?= (isset($jenisRole) && $jenisRole == "Peminjam") ? "selected" : "" ?>>Peminjam</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="<?= BASE_URL ?>/Menu/Menu PIC/manajemenAkunKry.php" class="btn btn-secondary">Kembali</a>
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