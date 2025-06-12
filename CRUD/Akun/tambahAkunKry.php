<?php
include '../../templates/header.php';

$showModal = false;

// Auto-generate npk dari database SQL Server
$npk = '51001';
$sqlId = "SELECT TOP 1 npk FROM Karyawan WHERE npk LIKE '51%' ORDER BY npk DESC";
$stmtId = sqlsrv_query($conn, $sqlId);
if ($stmtId && $rowId = sqlsrv_fetch_array($stmtId, SQLSRV_FETCH_ASSOC)) {
    $lastId = $rowId['npk']; // contoh: 51001
    $num = intval(substr($lastId, 3));
    $newNum = $num + 1;
    $npk = '51' . str_pad($newNum, 3, '0', STR_PAD_LEFT);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaKry = $_POST['namaKry'];
    $noHP = $_POST['noHP'];
    $jenisRole = $_POST['jenisRole'];
    $kataSandi = $_POST['kataSandi'];
    $konfirmasiSandi = $_POST['konfirmasiSandi'];

    $query = "INSERT INTO Karyawan (npk, namaKry, noHP, jenisRole, kataSandi) VALUES (?, ?, ?, ?, ?)";
    $params = [$npk, $namaKry, $noHP, $jenisRole, $kataSandi];
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt) {
        $showModal = true;
    } else {
        $error = "Gagal menambahkan akun.";
    }
}

$currentPage = basename($_SERVER['PHP_SELF']); // Determine the current page

// Variabel untuk Manajemen Aset (dibiarkan jika masih ada kemungkinan digunakan atau untuk konsistensi struktur)
$manajemenAsetPages = ['manajemenBarang.php', 'manajemenRuangan.php', 'tambahBarang.php', 'editBarang.php'];
$isManajemenAsetActive = in_array($currentPage, $manajemenAsetPages);

// Variabel untuk Manajemen Akun
$manajemenAkunPages = [
    'manajemenAkunKry.php',
    'tambahAkunKry.php',
    'editAkunKry.php',
    'manajemenAkunMhs.php',
    'tambahAkunMhs.php',
    'editAkunMhs.php'
];
$isManajemenAkunActive = in_array($currentPage, $manajemenAkunPages);
include '../../templates/sidebar.php';

?>

<!-- Content Area -->
<main class="col bg-white px-4 py-3 position-relative">
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="../../Menu PIC/manajemenAkunKry.php">Manajemen Akun Karyawan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Akun</li>
            </ol>
        </nav>
    </div>


    <!-- Tambah Barang -->
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
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-semibold">Tambah Akun</span>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="npk" class="form-label">NPK</label>
                                    <input type="text" class="form-control" id="npk" name="npk" value="<?= htmlspecialchars($npk) ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label for="namaKry" class="form-label d-flex align-items-center">Nama Lengkap
                                        <span id="namaError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                    </label>
                                    <input type="text" class="form-control" id="namaKry" name="namaKry">
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="noHP" class="form-label d-flex align-items-center">Nomor Telepon
                                        <span id="noHPError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                    </label>
                                    <input type="text" class="form-control" id="noHP" name="noHP">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="jenisRole" class="form-label">Role</label>
                                    <select class="form-select" id="jenisRole" name="jenisRole">
                                        <option value="" selected>Pilih Role</option>
                                        <option value="PIC Aset">PIC Aset</option>
                                        <option value="KA UPT">KA UPT</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="kataSandi" class="form-label d-flex align-items-center">Kata Sandi
                                    <span id="passLengthError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Minimal 8 karakter</span>
                                    <span id="passError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                </label>
                                <input type="password" class="form-control" id="kataSandi" name="kataSandi">
                            </div>
                            <div class="mb-2">
                                <label for="konfirmasiSandi" class="form-label d-flex align-items-center">Konfirmasi Kata Sandi
                                    <span id="passMatchError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Tidak sesuai</span>
                                    <span id="confPassError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                </label>
                                <input type="password" class="form-control" id="konfirmasiSandi" name="konfirmasiSandi">
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="../../Menu PIC/manajemenAkunKry.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Berhasil -->
        <div class="modal fade" id="successModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmModalLabel">Berhasil</h5>
                        <a href="../../Menu PIC/manajemenAkunKry.php"><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></a>
                    </div>
                    <div class="modal-body">
                        <p>Data akun berhasil ditambahkan.</p>
                    </div>
                    <div class="modal-footer">
                        <a href="../../Menu PIC/manajemenAkunKry.php" class="btn btn-primary">OK</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- End Tambah Akun -->
</main>

<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        let nama = document.getElementById('namaKry').value.trim();
        let nohp = document.getElementById('noHP').value.trim();
        let pass = document.getElementById('kataSandi').value;
        let conf = document.getElementById('konfirmasiSandi').value;

        let namaError = document.getElementById('namaError');
        let nohpError = document.getElementById('noHPError');
        let passError = document.getElementById('passError');
        let passMatchError = document.getElementById('passMatchError');
        let confPassError = document.getElementById('confPassError');
        let passLengthError = document.getElementById('passLengthError');

        let valid = true;

        // Reset error
        namaError.style.display = 'none';
        nohpError.style.display = 'none';
        passError.style.display = 'none';
        confPassError.style.display = 'none';
        passMatchError.style.display = 'none';
        passLengthError.style.display = 'none';

        if (nama === "") {
            namaError.style.display = 'block';
            valid = false;
        }
        if (nohp === "") {
            nohpError.style.display = 'block';
            valid = false;
        }
        if (pass === "") {
            passError.style.display = 'block';
            valid = false;
        }
        if (pass.length > 0 && pass.length < 8) {
            passLengthError.style.display = 'block';
            valid = false;
        }
        if (conf === "") {
            confPassError.style.display = 'block';
            valid = false;
        }
        if (pass !== "" && conf !== "" && pass !== conf) {
            passMatchError.style.display = 'block';
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
</script>
<?php include '../../templates/footer.php'; ?>