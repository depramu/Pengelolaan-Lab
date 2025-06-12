<?php
include '../../templates/header.php';

$showModal = false;

// Auto-generate nim dari database SQL Server
$nim = '0920240001';
$sqlId = "SELECT TOP 1 nim FROM Mahasiswa WHERE nim LIKE '092024%' ORDER BY nim DESC";
$stmtId = sqlsrv_query($conn, $sqlId);
if ($stmtId && $rowId = sqlsrv_fetch_array($stmtId, SQLSRV_FETCH_ASSOC)) {
    $lastId = $rowId['nim'];
    $num = intval(substr($lastId, -4));
    $newNum = $num + 1;
    $nim = '092024' . str_pad($newNum, 4, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaMhs = $_POST['namaMhs'];
    $kataSandi = $_POST['kataSandi'];
    $konfirmasiSandi = $_POST['konfirmasiSandi'];

    $query = "INSERT INTO Mahasiswa (nim, namaMhs, kataSandi) VALUES (?, ?, ?)";
    $params = [$nim, $namaMhs, $kataSandi];
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt) {
        $showModal = true;
    } else {
        $error = "Gagal menambahkan akun.";
    }
}

$currentPage = basename($_SERVER['PHP_SELF']);

// Define page groups for active sidebar states (using only basenames)
$manajemenAsetPages = ['manajemenBarang.php', 'manajemenRuangan.php', 'tambahBarang.php', 'editBarang.php', 'tambahRuangan.php', 'editRuangan.php'];
$isManajemenAsetActive = in_array($currentPage, $manajemenAsetPages);

$manajemenAkunPages = ['manajemenAkunMhs.php', 'tambahAkunMhs.php', 'editAkunMhs.php', 'manajemenAkunKry.php', 'tambahAkunKry.php', 'editAkunKry.php'];
$isManajemenAkunActive = in_array($currentPage, $manajemenAkunPages);

$peminjamanPages = ['peminjamanBarang.php', 'peminjamanRuangan.php', 'detailPeminjaman.php']; // Add other relevant peminjaman pages
$isPeminjamanActive = in_array($currentPage, $peminjamanPages);
include '../../template/sidebar.php';
?>
            <!-- Content Area -->
            <main class="col bg-white px-4 py-3 position-relative">
                <div class="mb-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="../../Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                            <li class="breadcrumb-item"><a href="../../Menu PIC/manajemenAkunMhs.php">Manajemen Akun Mahasiswa</a></li>
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
                                        <div class="mb-2">
                                            <label for="nim" class="form-label">NIM</label>
                                            <input type="text" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($nim) ?>" disabled>
                                        </div>
                                        <div class="mb-2">
                                            <label for="namaMhs" class="form-label d-flex align-items-center">Nama Lengkap
                                                <span id="namaError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                            </label>
                                            <input type="text" class="form-control" id="namaMhs" name="namaMhs">
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
                                            <a href="../../Menu PIC/manajemenAkunMhs.php" class="btn btn-secondary">Kembali</a>
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
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Data akun berhasil ditambahkan.</p>
                                </div>
                                <div class="modal-footer">
                                    <a href="../../Menu PIC/manajemenAkunMhs.php" class="btn btn-primary">OK</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End Tambah Akun Mahasiswa -->
            </main>

        <script>
            document.querySelector('form').addEventListener('submit', function(e) {
                let nama = document.getElementById('namaMhs').value.trim();
                let pass = document.getElementById('kataSandi').value;
                let conf = document.getElementById('konfirmasiSandi').value;

                let namaError = document.getElementById('namaError');
                let passError = document.getElementById('passError');
                let passMatchError = document.getElementById('passMatchError');
                let confPassError = document.getElementById('confPassError');
                let passLengthError = document.getElementById('passLengthError');

                let valid = true;

                // Reset error
                namaError.style.display = 'none';
                passError.style.display = 'none';
                confPassError.style.display = 'none';
                passMatchError.style.display = 'none';
                passLengthError.style.display = 'none';

                if (nama === "") {
                    namaError.style.display = 'block';
                    valid = false;
                }
                if (pass === "") {
                    passError.style.display = 'block';
                    valid = false;
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

<?php include '../../template/footer.php'; ?>