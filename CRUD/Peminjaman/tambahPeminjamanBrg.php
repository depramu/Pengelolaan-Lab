<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../koneksi.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/../../templates/header.php';

// Auto-generate id Peminjaman Ruangan dari database SQL Server
$idPeminjamanBrg = 'PJB001';
$sqlId = "SELECT TOP 1 idPeminjamanBrg FROM Peminjaman_Barang WHERE idPeminjamanBrg LIKE 'PJB%' ORDER BY idPeminjamanBrg DESC";
$stmtId = sqlsrv_query($conn, $sqlId);
if ($stmtId && $rowId = sqlsrv_fetch_array($stmtId, SQLSRV_FETCH_ASSOC)) {
    $lastId = $rowId['idPeminjamanBrg']; // contoh: PJR012
    $num = intval(substr($lastId, 3));
    $newNum = $num + 1;
    $idPeminjamanBrg = 'PJB' . str_pad($newNum, 3, '0', STR_PAD_LEFT);
}
// ID Barang
$idBarang = $_GET['idBarang'] ?? null;
if (empty($idBarang)) {
    die("Error: ID Barang tidak ditemukan. Silakan kembali dan pilih barang yang ingin dipinjam.");
}

$namaBarang = '';
$stokTersedia = 0;

$sqlDetail = "SELECT namaBarang, stokBarang FROM Barang WHERE idBarang = ?";
$paramsDetail = [$idBarang];
$stmtDetail = sqlsrv_query($conn, $sqlDetail, $paramsDetail);

// Cek apakah data barang ditemukan
if ($stmtDetail && $dataBarang = sqlsrv_fetch_array($stmtDetail, SQLSRV_FETCH_ASSOC)) {
    // Jika data ditemukan, simpan ke dalam variabel
    $namaBarang = $dataBarang['namaBarang'];
    $stokTersedia = $dataBarang['stokBarang'];
} else {
    // Jika ID barang dari URL tidak valid, hentikan proses.
    die("Error: Data untuk ID Barang '" . htmlspecialchars($idBarang) . "' tidak ditemukan di database.");
}

// Data sesi dan tanggal
$nim = $_SESSION['nim'] ?? null;
$npk = $_SESSION['npk'] ?? null;
$tglPeminjamanBrg = $_SESSION['tglPeminjamanBrg'] ?? null;

// Inisialisasi variabel
$error = null;
$showModal = false;

//  stok barang
$sqlStok = "SELECT stokBarang FROM Barang WHERE idBarang = ?";
$paramsStok = [$idBarang];
$stmtStok = sqlsrv_query($conn, $sqlStok, $paramsStok);
$stokBarang = sqlsrv_fetch_array($stmtStok, SQLSRV_FETCH_ASSOC)['stokBarang'];

// Proses Peminjaman hanya jika metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $alasanPeminjamanBrg = $_POST['alasanPeminjamanBrg'];
    $jumlahBrg = (int)$_POST['jumlahBrg']; // Pastikan integer

    // Validasi input
    if ($jumlahBrg <= 0) {
        $error = "Jumlah peminjaman harus lebih dari 0.";
    } elseif ($jumlahBrg > $stokTersedia) {
        $error = "*Jumlah peminjaman melebihi stok yang tersedia.";
    } else {
        // 1. Insert data peminjaman    
        $queryInsert = "INSERT INTO Peminjaman_Barang (idPeminjamanBrg, idBarang, tglPeminjamanBrg, nim, npk, jumlahBrg, alasanPeminjamanBrg, statusPeminjaman) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $paramsInsert = [$idPeminjamanBrg, $idBarang, $tglPeminjamanBrg, $nim, $npk, $jumlahBrg, $alasanPeminjamanBrg, 'Menunggu Persetujuan'];
        $stmtInsert = sqlsrv_query($conn, $queryInsert, $paramsInsert);

        if ($stmtInsert) {
            // 2. Jika insert berhasil, update stok barang
            $queryUpdate = "UPDATE Barang SET stokBarang = stokBarang - ? WHERE idBarang = ?";
            $paramsUpdate = [$jumlahBrg, $idBarang];
            $stmtUpdate = sqlsrv_query($conn, $queryUpdate, $paramsUpdate);

            if ($stmtUpdate) {
                $showModal = true;
            } else {
                $error = "Peminjaman tercatat, tetapi gagal mengupdate stok. Error: " . print_r(sqlsrv_errors(), true);
            }
        } else {
            $error = "Gagal menambahkan peminjaman barang. Error: " . print_r(sqlsrv_errors(), true);
        }
    }
}

include '../../templates/sidebar.php';
?>

<!-- BUAT DISABLE -->
<style>
    .protect-input {
        background-color: #e9ecef;
        color: #6c757d;
    }
</style>

<!-- Content Area -->
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Barang/cekBarang.php">Cek Barang</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Barang/lihatBarang.php">Lihat Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengajuan Peminjaman Barang</li>
            </ol>
        </nav>
    </div>


    <!-- Peminjaman Barang -->
    <div class="container mt-4">

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-semibold">Peminjaman Barang</span>
                    </div>
                    <div class="card-body">

                        <form method="POST">
                            <!-- Add hidden field for date -->

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2" style="max-width: 400px;">
                                        <label for="idBarang" class="form-label">ID Barang</label>
                                        <input type="hidden" name="idBarang" value="<?= $idBarang ?>">
                                        <input type="text" class="form-control protect-input" id="idBarang" name="idBarang_display" value="<?= $idBarang ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2" style="max-width: 400px;">
                                        <label for="namaBarang" class="form-label">Nama Barang</label>
                                        <input type="hidden" name="namaBarang" value="<?= $namaBarang ?>">
                                        <input type="text" class="form-control protect-input" name="namaBarang_display" value="<?= $namaBarang ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2" style="max-width: 400px;">
                                        <label class="form-label">Tanggal Peminjaman</label>
                                        <input type="hidden" name="tglPeminjamanBrg" value="<?= htmlspecialchars($tglPeminjamanBrg) ?>">
                                        <input type="text" class="form-control protect-input" name="tglDisplay" value="<?php
                                                                                                                        if (!empty($tglPeminjamanBrg)) {
                                                                                                                            $dateObj = DateTime::createFromFormat('Y-m-d', $tglPeminjamanBrg);
                                                                                                                            echo $dateObj ? $dateObj->format('d-m-Y') : htmlspecialchars($tglPeminjamanBrg);
                                                                                                                        }
                                                                                                                        ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2" style="max-width: 400px;">
                                        <label for="nim" class="form-label">NIM</label>
                                        <input type="text" class="form-control protect-input" id="nim" name="nim_display"
                                            value="<?= isset($_SESSION['nim']) ? htmlspecialchars($_SESSION['nim']) : '' ?>">
                                        <input type="hidden" name="nim" value="<?= $_SESSION['nim'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2" style="max-width: 400px;">
                                        <label for="alasanPeminjamanBrg" class="form-label">
                                            Alasan Peminjaman <span id="alasanError" class="text-danger small mt-1" style="font-size: 0.95em; display:none;">*Harus Diisi</span>
                                        </label>
                                        <textarea class="form-control" id="alasanPeminjamanBrg" name="alasanPeminjamanBrg" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2" style="max-width: 400px;">
                                        <label for="npk" class="form-label">NPK</label>
                                        <input type="text" class="form-control protect-input" id="npk" name="npk_display"
                                            value="<?= isset($_SESSION['npk']) ? htmlspecialchars($_SESSION['npk']) : '' ?>">
                                        <input type="hidden" name="npk" value="<?= isset($_SESSION['npk']) ? htmlspecialchars($_SESSION['npk']) : '' ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="jumlahBrg" class="form-label w-100">
                                        Jumlah Peminjaman
                                        <span id="jumlahError" class="text-danger small mt-1" style="font-size: 0.95em; display:none;">*Jumlah harus lebih dari 0.</span>
                                    </label>
                                    <div class="input-group" style="max-width: 140px;">
                                        <button class="btn btn-outline-secondary" type="button" onclick="changeStok(-1)">-</button>
                                        <input class="form-control text-center" id="jumlahBrg" name="jumlahBrg" value="0" min="0" required style="max-width: 70px;">
                                        <button class="btn btn-outline-secondary" type="button" onclick="changeStok(1)">+</button>
                                    </div>
                                    <small class="text-muted">Stok tersedia: <?= $stokBarang ?></small>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="<?= BASE_URL ?>/Menu Peminjam/Peminjaman Barang/lihatBarang.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Ajukan Peminjaman</button>
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
                        <a href="../../Menu Peminjam/lihatBarang.php"><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></a>
                    </div>
                    <div class="modal-body">
                        <p>Peminjaman dengan ID <?= $idPeminjamanBrg ?> berhasil.</p>
                    </div>
                    <div class="modal-footer">
                        <a href="../../Menu Peminjam/Peminjaman Barang/lihatBarang.php" class="btn btn-primary">OK</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
<script>
    function changeStok(val) {
        let stokInput = document.getElementById('jumlahBrg');
        let current = parseInt(stokInput.value) || 0;
        let next = current + val;
        if (next < 0) next = 0;
        stokInput.value = next;
    }

    document.querySelectorAll('.protect-input').forEach(input => {
        input.addEventListener('paste', e => e.preventDefault());
        input.addEventListener('input', e => input.value = input.defaultValue);
        input.addEventListener('mousedown', e => e.preventDefault());
    });
</script>
<script>
    // Fungsi validasi form sebelum submit
    document.querySelector('form').addEventListener('submit', function(e) {
        let valid = true;

        // Validasi Jumlah Peminjaman
        const jumlahInput = document.getElementById('jumlahBrg');
        const jumlahError = document.getElementById('jumlahError');
        const stokTersedia = <?= $stokBarang ?>;
        const jumlahValue = parseInt(jumlahInput.value) || 0;

        if (jumlahValue <= 0) {
            jumlahError.textContent = '*Jumlah harus lebih dari 0.';
            jumlahError.style.display = 'inline';
            valid = false;
        } else if (jumlahValue > stokTersedia) {
            jumlahError.textContent = '*Jumlah melebihi stok tersedia.';
            jumlahError.style.display = 'inline';
            valid = false;
        }

        // Validate Alasan Peminjaman
        let alasanInput = document.getElementById('alasanPeminjamanBrg');
        let alasanError = document.getElementById('alasanError');
        if (alasanInput.value.trim() === '') {
            alasanError.textContent = '*Harus diisi';
            alasanError.style.display = 'inline';
            valid = false;
        } else {
            alasanError.style.display = 'none';
        }

        if (!valid) {
            e.preventDefault(); // Hentikan pengiriman form jika tidak valid
        }
    });
</script>