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
    $namaRuangan = $_POST['namaRuangan'] ?? '';
    $kondisiRuangan = $_POST['kondisiRuangan'] ?? '';
    $ketersediaan = $_POST['ketersediaan'] ?? '';

    // Cek apakah nama ruangan sudah ada
    $cekNamaQuery = "SELECT COUNT(*) AS jumlah FROM Ruangan WHERE namaRuangan = ?";
    $cekNamaParams = [$namaRuangan];
    $cekNamaStmt = sqlsrv_query($conn, $cekNamaQuery, $cekNamaParams);
    $cekNamaRow = sqlsrv_fetch_array($cekNamaStmt, SQLSRV_FETCH_ASSOC);

    if ($cekNamaRow['jumlah'] > 0) {
        $namaError = "*Nama ruangan sudah terdaftar";
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
<h3 class="fw-semibold mb-3">Manajemen Ruangan</h3>
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
                        <span class="fw-bold">Tambah Ruangan</span>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-2">
                                <label for="idRuangan" class="form-label d-flex align-items-center fw-semibold ">ID Ruangan</label>
                                <input type="text" class="form-control protect-input " id="idRuangan" name="idRuangan" value="<?= htmlspecialchars($idRuangan) ?>" readonly tabindex="-1" onfocus="this.blur()">
                            </div>
                            <div class="mb-2">
                                <label for="namaRuangan" class="form-label d-flex align-items-center fw-semibold">Nama Ruangan
                                    <span id="namaError" class="text-danger ms-2" style="font-size:0.95em;display:none;">*Harus Diisi</span>
                                        <?php if (!empty($namaError)): ?>
                                            <span class="text-danger ms-2" style="font-size:0.95em;"><?= $namaError ?></span>
                                        <?php endif; ?>
                                </label>
                                <input type="text" class="form-control" id="namaRuangan" name="namaRuangan" value="<?= isset($namaRuangan) ? htmlspecialchars($namaRuangan) : '' ?>">
                            </div>
                            <div class="mb-2">
                                <label for="kondisiRuangan" class="form-label d-flex align-items-center fw-semibold">Kondisi Ruangan
                                    <span id="kondisiError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                </label>
                                <select class="form-select" id="kondisiRuangan" name="kondisiRuangan" value="<?= isset($kondisiRuangan) ? htmlspecialchars($kondisiRuangan) : '' ?>">
                                    <option disabled selected>Pilih Kondisi</option>
                                    <option value="Baik" <?= (isset($kondisiRuangan) && $kondisiRuangan === 'Baik') ? 'selected' : '' ?>>Baik</option>
                                    <option value="Rusak" <?= (isset($kondisiRuangan) && $kondisiRuangan === 'Rusak') ? 'selected' : '' ?>>Rusak</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="ketersediaan" class="form-label d-flex align-items-center fw-semibold" value="<?= isset($ketersediaan) ? htmlspecialchars($ketersediaan) : '' ?>">Ketersediaan Ruangan
                                    <span id="ketersediaanError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                </label>
                                <select class="form-select" id="ketersediaan" name="ketersediaan">
                                    <option disabled selected>Pilih Ketersediaan</option>
                                    <option value="Tersedia" <?= (isset($ketersediaan) && $ketersediaan === 'Tersedia') ? 'selected' : '' ?>>Tersedia</option>
                                    <option value="Tidak Tersedia" <?= (isset($ketersediaan) && $ketersediaan === 'Tidak Tersedia') ? 'selected' : '' ?>>Tidak Tersedia</option>
                                </select>
                                <input type="hidden" id="ketersediaanHidden" name="ketersediaan" value="Tidak Tersedia">
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
    let kondisiSelect = document.getElementById('kondisiRuangan');
    let ketersediaanSelect = document.getElementById('ketersediaan');
    let ketersediaanHidden = document.getElementById('ketersediaanHidden');

    // Saat kondisi berubah
    kondisiSelect.addEventListener('change', function () {
        if (this.value === 'Rusak') {
            ketersediaanSelect.value = 'Tidak Tersedia';
            ketersediaanSelect.disabled = true;
            ketersediaanHidden.value = 'Tidak Tersedia';
        } else {
            ketersediaanSelect.disabled = false;
            ketersediaanSelect.value = '';
            ketersediaanHidden.value = '';
        }
    });

    // Saat ketersediaan dipilih manual
    ketersediaanSelect.addEventListener('change', function () {
        ketersediaanHidden.value = this.value;
    });

    // Pastikan hidden tetap update saat halaman dimuat
    window.addEventListener('DOMContentLoaded', function () {
        if (kondisiSelect.value === 'Rusak') {
            ketersediaanSelect.value = 'Tidak Tersedia';
            ketersediaanSelect.disabled = true;
            ketersediaanHidden.value = 'Tidak Tersedia';
        } else {
            ketersediaanHidden.value = ketersediaanSelect.value;
        }
    });

    // Validasi
    document.querySelector('form').addEventListener('submit', function(e) {
        let valid = true;

        // Nama
        let nama = document.getElementById('namaRuangan');
        let namaError = document.getElementById('namaError');
        if (nama.value.trim() === '') {
            namaError.style.display = 'inline';
            valid = false;
        } else {
            namaError.style.display = 'none';
        }

        // Kondisi
        let kondisiError = document.getElementById('kondisiError');
        if (!kondisiSelect.value || kondisiSelect.value === 'Pilih Kondisi') {
            kondisiError.style.display = 'inline';
            valid = false;
        } else {
            kondisiError.style.display = 'none';
        }

        // Ketersediaan (cek hidden)
        let ketersediaanError = document.getElementById('ketersediaanError');
        if (!ketersediaanHidden.value || ketersediaanHidden.value === 'Pilih Ketersediaan') {
            ketersediaanError.style.display = 'inline';
            valid = false;
        } else {
            ketersediaanError.style.display = 'none';
        }

        if (!valid) e.preventDefault();
    });
</script>


<?php
include '../../templates/footer.php';
?>