<?php
include '../../templates/header.php';

$idBarang = $_GET['id'] ?? null;
$error = ''; // Initialize error string
$showModal = false; // For success modal

if (!$idBarang) {
    header('Location: ../../Menu PIC/manajemenBarang.php');
    exit;
}

$query = "SELECT * FROM Barang WHERE idBarang = ?";
$stmt = sqlsrv_query($conn, $query, [$idBarang]);
$data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$data) {
    header('Location: ../../Menu PIC/manajemenBarang.php?error=notfound');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaBarang = $_POST['namaBarang'];
    $stokBarang = $_POST['stokBarang'];
    $lokasiBarang = $_POST['lokasiBarang'];

    // Basic validation
    if (empty($namaBarang) || $stokBarang === '' || $stokBarang < 0 || empty($lokasiBarang)) {
        $error = "Semua field harus diisi dengan benar. Stok tidak boleh kurang dari 0.";
    } else {
        $updateQuery = "UPDATE Barang SET namaBarang = ?, stokBarang = ?, lokasiBarang = ? WHERE idBarang = ?";
        $params = [$namaBarang, $stokBarang, $lokasiBarang, $idBarang];
        $updateStmt = sqlsrv_query($conn, $updateQuery, $params);

        if ($updateStmt) {
            $showModal = true;
            $stmt = sqlsrv_query($conn, $query, [$idBarang]);
            $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        } else {
            $error = "Gagal mengubah data barang. Error: " . print_r(sqlsrv_errors(), true);
        }
    }
}

$lokasiList = [];
$sqlLokasi = "SELECT idRuangan FROM Ruangan";
$stmtLokasi = sqlsrv_query($conn, $sqlLokasi);
if ($stmtLokasi) {
    while ($row = sqlsrv_fetch_array($stmtLokasi, SQLSRV_FETCH_ASSOC)) {
        $lokasiList[] = $row['idRuangan'];
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
                <li class="breadcrumb-item"><a href="../../Menu PIC/manajemenBarang.php">Manajemen Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Barang</li>
            </ol>
        </nav>
    </div>


    <!-- Edit Barang -->
    <div class="container mt-4">
        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
                <a href="../../Menu PIC/manajemenBarang.php" class="btn btn-secondary">Kembali</a>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-semibold">Edit Barang</span>
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
                                <input type="text" class="form-control" id="namaBarang" name="namaBarang" value="<?= htmlspecialchars($data['namaBarang']) ?>">
                            </div>
                            <div class="mb-2">
                                <label for="stokBarang" class="form-label">
                                    Stok Barang
                                    <span class="text-danger ms-2" id="errorStokBarang" style="font-size:0.95em;display:none;">*Harus Diisi</span>
                                </label>
                                <div class="input-group" style="max-width: 180px;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="changeStok(-1)">-</button>
                                    <input type="number hidden" class="form-control text-center" id="stokBarang" name="stokBarang" value="<?= htmlspecialchars($data['stokBarang']) ?>" min="0" style="max-width: 70px;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="changeStok(1)">+</button>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="lokasiBarang" class="form-label">
                                    Lokasi Barang
                                    <span class="text-danger ms-2" id="errorLokasiBarang" style="font-size:0.95em;display:none;">*Harus Diisi</span>
                                </label>
                                <select class="form-select" id="lokasiBarang" name="lokasiBarang">
                                    <option value="" disabled selected>Pilih Lokasi</option>
                                    <?php foreach ($lokasiList as $lokasi) : ?>
                                        <option value="<?= htmlspecialchars($lokasi) ?>" <?php if ($data['lokasiBarang'] == $lokasi) echo 'selected'; ?>><?= htmlspecialchars($lokasi) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="../../Menu PIC/manajemenBarang.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
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

    document.querySelector('form').addEventListener('submit', function(e) {
        let valid = true;

        // Nama Barang
        const namaBarang = document.getElementById('namaBarang');
        const errorNamaBarang = document.getElementById('errorNamaBarang');
        if (namaBarang.value.trim() === '') {
            errorNamaBarang.style.display = 'inline';
            valid = false;
        } else {
            errorNamaBarang.style.display = 'none';
        }

        // Stok Barang
        const stokBarang = document.getElementById('stokBarang');
        const errorStokBarang = document.getElementById('errorStokBarang');
        if (stokBarang.value.trim() === '' || parseInt(stokBarang.value) < 0) {
            errorStokBarang.style.display = 'inline';
            valid = false;
        } else {
            errorStokBarang.style.display = 'none';
        }

        // Lokasi Barang
        const lokasiBarang = document.getElementById('lokasiBarang');
        const errorLokasiBarang = document.getElementById('errorLokasiBarang');
        if (!lokasiBarang.value) {
            errorLokasiBarang.style.display = 'inline';
            valid = false;
        } else {
            errorLokasiBarang.style.display = 'none';
        }

        if (!valid) e.preventDefault();
    });
</script>

<?php include '../../templates/footer.php'; ?>