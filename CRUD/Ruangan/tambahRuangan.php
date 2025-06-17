<?php
include '../../templates/header.php';

$showModal = false;
$idRuangan = 'CB001';
$sqlId = "SELECT TOP 1 idRuangan FROM Ruangan WHERE idRuangan LIKE 'CB%' ORDER BY idRuangan DESC";
$stmtId = sqlsrv_query($conn, $sqlId);
if ($stmtId && $rowId = sqlsrv_fetch_array($stmtId, SQLSRV_FETCH_ASSOC)) {
    $lastId = $rowId['idRuangan']; // contoh: CB012
    $num = intval(substr($lastId, 3));
    $newNum = $num + 1;
    $idRuangan = 'CB' . str_pad($newNum, 3, '0', STR_PAD_LEFT);
}

$kondisiRuanganList = ['Baik', 'Rusak'];
$ketersediaanList = ['Tersedia', 'Tidak Tersedia'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaRuangan = $_POST['namaRuangan'];
    $kondisiRuangan = $_POST['kondisiRuangan']; // ganti jadi kondisiRuangan
    $ketersediaan = $_POST['ketersediaan'];

    // Cek apakah nama ruangan sudah ada
    $cekNamaQuery = "SELECT COUNT(*) AS jumlah FROM Ruangan WHERE namaRuangan = ?";
    $cekNamaParams = [$namaRuangan];
    $cekNamaStmt = sqlsrv_query($conn, $cekNamaQuery, $cekNamaParams);
    $cekNamaRow = sqlsrv_fetch_array($cekNamaStmt, SQLSRV_FETCH_ASSOC);

    if ($cekNamaRow['jumlah'] > 0) {
        $error = "Nama ruangan sudah terdaftar";
    } else {
        $query = "INSERT INTO Ruangan (idRuangan, namaRuangan, kondisiRuangan, ketersediaan) VALUES (?, ?, ?, ?)";
        $params = [$idRuangan, $namaRuangan, $kondisiRuangan, $ketersediaan];
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt) {
            $showModal = true;
        } else {
            $error = "Gagal menambahkan ruangan.";
        }
    }
}
include '../../templates/sidebar.php';
?>
<!-- Content Area -->
<main class="col bg-white px-4 py-3 position-relative">
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="../../Menu PIC/manajemenRuangan.php">Manajemen Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Ruangan</li>
            </ol>
        </nav>
    </div>


    <!-- Tambah Ruangan -->
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
                        <span class="fw-semibold">Tambah Ruangan</span>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-2">
                                <label for="idRuangan" class="form-label d-flex align-items-center">ID Ruangan</label>
                                <input type="text" class="form-control" id="idRuangan" name="idRuangan" value="<?= htmlspecialchars($idRuangan) ?>" disabled>
                            </div>
                            <div class="mb-2">
                                <label for="namaRuangan" class="form-label d-flex align-items-center">Nama Ruangan
                                    <span id="namaError" class="text-danger ms-2" style="font-size:0.95em;display:none;">*Harus Diisi</span>
                                </label>
                                <input type="text" class="form-control" id="namaRuangan" name="namaRuangan" value="<?= isset($namaRuangan) ? htmlspecialchars($namaRuangan) : '' ?>">
                            </div>
                            <div class="mb-2">
                                <label for="kondisiRuangan" class="form-label d-flex align-items-center">Kondisi Ruangan
                                    <span id="kondisiError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                </label>
                                <select class="form-select" id="kondisiRuangan" name="kondisiRuangan" value="<?= isset($kondisiRuangan) ? htmlspecialchars($kondisiRuangan) : '' ?>">
                                    <option disabled selected>Pilih Kondisi</option>
                                    <option value="Baik" <?= (isset($kondisiRuangan) && $kondisiRuangan === 'Baik') ? 'selected' : '' ?>>Baik</option>
                                    <option value="Rusak" <?= (isset($kondisiRuangan) && $kondisiRuangan === 'Rusak') ? 'selected' : '' ?>>Rusak</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="ketersediaan" class="form-label d-flex align-items-center">Ketersediaan Ruangan
                                    <span id="ketersediaanError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                </label>
                                <select class="form-select" id="ketersediaan" name="ketersediaan">
                                    <option disabled selected>Pilih Ketersediaan</option>
                                    <option value="Tersedia">Tersedia</option>
                                    <option value="Tidak Tersedia">Tidak Tersedia</option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="../../Menu PIC/manajemenRuangan.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</main>

</div>

<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        let valid = true;

        // Nama Ruangan
        const nama = document.getElementById('namaRuangan');
        const namaError = document.getElementById('namaError');
        if (nama && nama.value.trim() === '') {
            namaError.style.display = 'inline';
            valid = false;
        } else if (namaError) {
            namaError.style.display = 'none';
        }

        // Kondisi Ruangan
        const kondisi = document.getElementById('kondisiRuangan');
        const kondisiError = document.getElementById('kondisiError');
        if (kondisi && (!kondisi.value || kondisi.value === 'Pilih Kondisi')) {
            kondisiError.style.display = 'inline';
            valid = false;
        } else if (kondisiError) {
            kondisiError.style.display = 'none';
        }

        // Ketersediaan Ruangan
        const ketersediaan = document.getElementById('ketersediaan');
        const ketersediaanError = document.getElementById('ketersediaanError');
        if (ketersediaan && (!ketersediaan.value || ketersediaan.value === 'Pilih Ketersediaan')) {
            ketersediaanError.style.display = 'inline';
            valid = false;
        } else if (ketersediaanError) {
            ketersediaanError.style.display = 'none';
        }

        if (!valid) e.preventDefault();
    });
</script>

<?php
include '../../templates/footer.php';
?>