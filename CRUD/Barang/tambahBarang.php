<?php
include '../../templates/header.php';

$showModal = false;

$idBarang = 'BRG001';
$sqlId = "SELECT TOP 1 idBarang FROM Barang WHERE idBarang LIKE 'BRG%' ORDER BY idBarang DESC";
$stmtId = sqlsrv_query($conn, $sqlId);
if ($stmtId && $rowId = sqlsrv_fetch_array($stmtId, SQLSRV_FETCH_ASSOC)) {
    $lastId = $rowId['idBarang']; // contoh: BRG012
    $num = intval(substr($lastId, 3));
    $newNum = $num + 1;
    $idBarang = 'BRG' . str_pad($newNum, 3, '0', STR_PAD_LEFT);
}

$lokasiList = [];
$sqlLokasi = "SELECT idRuangan FROM Ruangan";
$stmtLokasi = sqlsrv_query($conn, $sqlLokasi);
if ($stmtLokasi) {
    while ($row = sqlsrv_fetch_array($stmtLokasi, SQLSRV_FETCH_ASSOC)) {
        $lokasiList[] = $row['idRuangan'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaBarang = $_POST['namaBarang'];
    $stokBarang = $_POST['stokBarang'];
    $lokasiBarang = $_POST['lokasiBarang'] ?? '';

    // Cek apakah nama barang sudah ada
    $cekNamaQuery = "SELECT COUNT(*) AS jumlah FROM Barang WHERE namaBarang = ?";
    $cekNamaParams = [$namaBarang];
    $cekNamaStmt = sqlsrv_query($conn, $cekNamaQuery, $cekNamaParams);
    $cekNamaRow = sqlsrv_fetch_array($cekNamaStmt, SQLSRV_FETCH_ASSOC);

    if ($cekNamaRow['jumlah'] > 0) {
        $error = "Nama barang sudah terdaftar, silakan gunakan nama lain.";
    } else {
        $query = "INSERT INTO Barang (idBarang, namaBarang, stokBarang, lokasiBarang) VALUES (?, ?, ?, ?)";
        $params = [$idBarang, $namaBarang, $stokBarang, $lokasiBarang];
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt) {
            $showModal = true;
        } else {
            $error = "Gagal menambahkan barang.";
        }
    }
}

include '../../templates/sidebar.php';
?>


<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Manajemen Barang</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="../../Menu PIC/manajemenBarang.php">Manajemen Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Barang</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-right: 1.5rem;">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12 " style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-semibold">Tambah Barang</span>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-2">
                                <label for="idBarang" class="form-label">ID Barang</label>
                                <input type="text" class="form-control" id="idBarang" name="idBarang" value="<?= htmlspecialchars($idBarang) ?>" disabled>
                            </div>
                            <div class="mb-2">
                                <label for="namaBarang" class="form-label">
                                    Nama Barang
                                    <span class="text-danger ms-2" id="errorNamaBarang" style="font-size:0.95em;display:none;">*Harus Diisi</span>
                                </label>
                                <input type="text" class="form-control" id="namaBarang" name="namaBarang" value="<?= isset($namaBarang) ? htmlspecialchars($namaBarang) : '' ?>">
                            </div>
                            <div class="mb-2">
                                <label for="stokBarang" class="form-label">
                                    Stok Barang
                                    <span class="text-danger ms-2" id="errorStokBarang" style="font-size:0.95em;display:none;">*Harus Diisi</span>
                                </label>
                                <div class="input-group" style="max-width: 180px;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="changeStok(-1)">-</button>
                                    <input type="text" class="form-control text-center" id="stokBarang" name="stokBarang"
                                        min="0" style="max-width: 70px;"
                                        value="<?= isset($stokBarang) ? htmlspecialchars($stokBarang) : '0' ?>">
                                    <button class="btn btn-outline-secondary" type="button" onclick="changeStok(1)">+</button>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="lokasiBarang" class="form-label">
                                    Lokasi Barang
                                    <span class="text-danger ms-2" id="errorLokasiBarang" style="font-size:0.95em;display:none;">*Harus Diisi</span>
                                </label>
                                <select class="form-select" id="lokasiBarang" name="lokasiBarang">
                                    <option disabled selected>Pilih Lokasi</option>
                                    <?php foreach ($lokasiList as $lokasi) : ?>
                                        <option value="<?= htmlspecialchars($lokasi) ?>"
                                            <?= (isset($lokasiBarang) && $lokasiBarang == $lokasi) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($lokasi) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="<?= BASE_URL ?>/Menu PIC/manajemenBarang.php" class="btn btn-secondary">Kembali</a>
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
    function changeStok(val) {
        let stokInput = document.getElementById('stokBarang');
        let current = parseInt(stokInput.value) || 0;
        let next = current + val;
        if (next < 0) next = 0;
        stokInput.value = next;
    }

    // Validasi form dan tampilkan modal konfirmasi
    document.querySelector('form').addEventListener('submit', function(e) {
        let valid = true;

        // Nama Barang
        let nama = document.getElementById('namaBarang');
        let namaError = document.getElementById('errorNamaBarang');
        if (nama.value.trim() === '') {
            namaError.style.display = 'inline';
            valid = false;
        } else {
            namaError.style.display = 'none';
        }

        // Stok Barang
        let stok = document.getElementById('stokBarang');
        let stokError = document.getElementById('errorStokBarang');
        if (stok.value.trim() === '' || parseInt(stok.value) <= 0) {
            stokError.style.display = 'inline';
            valid = false;
        } else {
            stokError.style.display = 'none';
        }

        // Lokasi Barang
        let lokasi = document.getElementById('lokasiBarang');
        let lokasiError = document.getElementById('errorLokasiBarang');
        if (!lokasi.value || lokasi.value === 'Pilih Lokasi') {
            lokasiError.style.display = 'inline';
            valid = false;
        } else {
            lokasiError.style.display = 'none';
        }

        if (!valid) e.preventDefault();
    });

    
</script>

<?php include '../../templates/footer.php'; ?>