<?php
include '../../templates/header.php';

$showModal = false;

// Generate ID Ruangan otomatis (CB001, CB002, dst)
$idRuangan = 'CB001';
$sqlId = "SELECT TOP 1 idRuangan FROM Ruangan WHERE idRuangan LIKE 'CB%' ORDER BY idRuangan DESC";
$stmtId = sqlsrv_query($conn, $sqlId);
if ($stmtId && $rowId = sqlsrv_fetch_array($stmtId, SQLSRV_FETCH_ASSOC)) {
    $lastId = $rowId['idRuangan']; // contoh: CB012
    $num = intval(substr($lastId, 2));
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
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="idRuangan" class="form-label fw-semibold d-flex align-items-center">ID Ruangan</label>
                                    <input type="text" class="form-control protect-input d-block bg-light" id="idRuangan" name="idRuangan" value="<?= htmlspecialchars($idRuangan) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="namaRuangan" class="form-label fw-semibold d-flex align-items-center">Nama Ruangan
                                        <span id="namaError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                        <?php if (!empty($namaError)): ?>
                                            <span class="fw-normal text-danger ms-2" style="font-size:0.95em;"><?= $namaError ?></span>
                                        <?php endif; ?>
<<<<<<< HEAD
                                    </label>
                                    <input type="text" class="form-control" id="namaRuangan" name="namaRuangan" value="<?= isset($namaRuangan) ? htmlspecialchars($namaRuangan) : '' ?>" placeholder="Masukkan nama ruangan..">
                                </div>
=======
                                </label>
                                <input type="text" class="form-control" id="namaRuangan" name="namaRuangan" placeholder="Masukkan Nama Ruangan" value="<?= isset($namaRuangan) ? htmlspecialchars($namaRuangan) : '' ?>">
>>>>>>> da99a8106382317812a99520fca98b4a7a1f956c
                            </div>
                            <div class="mb-2 row">
                                <div class="col-md-6">
                                    <label for="kondisiRuangan" class="form-label fw-semibold d-flex align-items-center">Kondisi Ruangan
                                        <span id="kondisiError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                    </label>
                                    <select class="form-select" id="kondisiRuangan" name="kondisiRuangan">
                                        <option value="" disabled <?= !isset($kondisiRuangan) || $kondisiRuangan == '' ? 'selected' : '' ?>>Pilih Kondisi</option>
                                        <?php foreach ($kondisiRuanganList as $kondisi): ?>
                                            <option value="<?= htmlspecialchars($kondisi) ?>" <?= (isset($kondisiRuangan) && $kondisiRuangan == $kondisi) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($kondisi) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="ketersediaan" class="form-label fw-semibold d-flex align-items-center">Ketersediaan Ruangan
                                        <span id="ketersediaanError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                    </label>
                                    <select class="form-select" id="ketersediaan" name="ketersediaan">
                                        <option value="" disabled <?= !isset($ketersediaan) || $ketersediaan == '' ? 'selected' : '' ?>>Pilih Ketersediaan</option>
                                        <?php foreach ($ketersediaanList as $tersedia): ?>
                                            <option value="<?= htmlspecialchars($tersedia) ?>" <?= (isset($ketersediaan) && $ketersediaan == $tersedia) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($tersedia) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
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
    </div>
</main>

<script>
<<<<<<< HEAD
    // Otomatis set ketersediaan jika kondisi "Rusak"
    document.getElementById('kondisiRuangan').addEventListener('change', function () {
        let kondisi = this.value;
        let ketersediaan = document.getElementById('ketersediaan');
        if (kondisi === 'Rusak') {
            ketersediaan.value = 'Tidak Tersedia';
            ketersediaan.disabled = true;
        } else {
            ketersediaan.disabled = false;
=======
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
            ketersediaanSelect.value = 'Pilih Ketersediaan';
            ketersediaanHidden.value = '';
>>>>>>> da99a8106382317812a99520fca98b4a7a1f956c
        }
    });

    // Set initial state on load
    window.addEventListener('DOMContentLoaded', function () {
        let kondisi = document.getElementById('kondisiRuangan').value;
        let ketersediaan = document.getElementById('ketersediaan');
        if (kondisi === 'Rusak') {
            ketersediaan.value = 'Tidak Tersedia';
            ketersediaan.disabled = true;
        }
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        let nama = document.getElementById('namaRuangan').value.trim();
        let kondisi = document.getElementById('kondisiRuangan').value;
        let ketersediaan = document.getElementById('ketersediaan').value;

        let namaError = document.getElementById('namaError');
        let kondisiError = document.getElementById('kondisiError');
        let ketersediaanError = document.getElementById('ketersediaanError');

        let valid = true;

        // Reset error messages
        namaError.style.display = 'none';
        kondisiError.style.display = 'none';
        ketersediaanError.style.display = 'none';

        if (nama === "") {
            namaError.textContent = '*Harus diisi';
            namaError.style.display = 'inline';
            valid = false;
        }

        if (kondisi === "" || kondisi === "Pilih Kondisi") {
            kondisiError.textContent = '*Harus diisi';
            kondisiError.style.display = 'inline';
            valid = false;
        }

        if (ketersediaan === "" || ketersediaan === "Pilih Ketersediaan") {
            ketersediaanError.textContent = '*Harus diisi';
            ketersediaanError.style.display = 'inline';
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
</script>

<?php include '../../templates/footer.php'; ?>