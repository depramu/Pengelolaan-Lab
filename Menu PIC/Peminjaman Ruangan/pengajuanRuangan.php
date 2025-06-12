<?php
include '../../template/header.php';

// Set current page for sidebar highlighting
$currentPage = basename($_SERVER['PHP_SELF']);

// Helper for sidebar submenu state
$isManajemenAsetActive = (
    $currentPage === 'manajemenBarang.php' ||
    $currentPage === 'tambahBarang.php' ||
    $currentPage === 'editBarang.php' ||
    $currentPage === 'manajemenRuangan.php'
);

// Perbaikan: Ambil data dari tabel Peminjaman_Barang
$idPeminjamanRuangan = $_GET['id'] ?? '';
$data = [];

$showRejectedModal = false;
$showModal = false;

if (!empty($idPeminjamanRuangan)) {
    $_SESSION['idPeminjamanRuangan'] = $idPeminjamanRuangan;

    $query = "SELECT * FROM Peminjaman_Ruangan WHERE idPeminjamanRuangan = ?";
    $params = array($idPeminjamanRuangan);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt && sqlsrv_has_rows($stmt)) {
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }
}

// Ekstrak data 
$idRuangan = $data['idRuangan'] ?? '';
$nim = $data['nim'] ?? '';
$npk = $data['npk'] ?? '';
$tglPeminjamanRuangan = isset($data['tglPeminjamanRuangan']) ? $data['tglPeminjamanRuangan']->format('Y-m-d') : '';
$alasanPeminjamanRuangan = $data['alasanPeminjamanRuangan'] ?? '';
$currentStatus = $data['statusPeminjaman'] ?? 'Diajukan';

// Perbaikan: Proses persetujuan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $query = "UPDATE Peminjaman_Ruangan 
                  SET statusPeminjaman = 'Sedang Dipinjam'
                  WHERE idPeminjamanRuangan = ?";
    $params = array($idPeminjamanRuangan);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt) {
        $showModal = true;
    } else {
        $error = "Gagal melakukan pengajuan ruangan.";
        exit;
    }
}
include '../../template/sidebar.php';
?>
            <!-- Content Area -->
            <main class="col bg-white px-4 py-3 position-relative">
                <div class="mb-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="../dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                            <li class="breadcrumb-item"><a href="peminjamanRuangan.php">Peminjaman Ruangan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Pengajuan Ruangan</li>
                        </ol>
                    </nav>
                </div>


                <!-- Pengajuan Peminjaman Barang -->
                <div class="container mt-4">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                            <div class="card border border-dark">
                                <div class="card-header bg-white border-bottom border-dark">
                                    <span class="fw-semibold">Pengajuan Peminjaman Ruangan</span>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">

                                        <div class="row">
                                            <!-- Kolom Kiri -->
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <label for="idRuangan" class="form-label">ID Ruangan</label>
                                                    <input type="text" class="form-control" id="idRuangan" name="idRuangan" value="<?= htmlspecialchars($idRuangan) ?>" disabled style="background: #f5f5f5;">
                                                </div>
                                                <div class="mb-2">
                                                    <label for="tglPeminjamanRuangan" class="form-label">Tanggal Peminjaman</label>
                                                    <input type="text" class="form-control" id="tglPeminjamanRuangan" name="tglPeminjamanRuangan" value="<?= htmlspecialchars($tglPeminjamanRuangan) ?>" disabled style="background: #f5f5f5;">
                                                </div>
                                                <div class="mb-2">
                                                    <label for="nim" class="form-label">NIM</label>
                                                    <input type="text" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($nim) ?>" disabled style="background: #f5f5f5;">
                                                </div>

                                            </div>
                                            <!-- Kolom Kanan -->
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <label for="idPeminjamanRuangan" class="form-label">ID Peminjaman Ruangan</label>
                                                    <input type="text" class="form-control" id="idPeminjamanRuangan" value="<?= htmlspecialchars($idPeminjamanRuangan) ?>" disabled style="background: #f5f5f5;">
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-6">
                                                        <label for="waktuMulai" class="form-label">Waktu Mulai</label>
                                                        <input type="text" class="form-control" id="waktuMulai" name="waktuMulai" value="<?= htmlspecialchars($waktuMulai ?? '08.00') ?>" disabled style="background: #f5f5f5;">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="waktuSelesai" class="form-label">Waktu Selesai</label>
                                                        <input type="text" class="form-control" id="waktuSelesai" name="waktuSelesai" value="<?= htmlspecialchars($waktuSelesai ?? '10.00') ?>" disabled style="background: #f5f5f5;">
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <label for="npk" class="form-label">NPK</label>
                                                    <input type="text" class="form-control" id="npk" name="npk" value="<?= htmlspecialchars($npk) ?>" disabled style="background: #f5f5f5;">
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <label for="alasanPeminjaman" class="form-label">Alasan Peminjaman</label>
                                                <textarea class="form-control w-100" id="alasanPeminjaman" name="alasanPeminjaman" rows="3" disabled style="background: #f5f5f5;"><?= htmlspecialchars($alasanPeminjamanRuangan) ?></textarea>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end gap-2 mt-4">
                                            <!-- TOMBOL AKSI -->
                                            <div class="d-flex justify-content-end gap-2">
                                                <!-- Link untuk Tolak -->
                                                <a href="penolakanRuangan.php?id=<?= htmlspecialchars($idPeminjamanRuangan) ?>" class="btn btn-danger">Tolak</a>
                                                <!-- Form untuk Setuju -->
                                                <button type="submit" name="submit" class="btn btn-primary">Setuju</button>
                                            </div>
                                        </div>




                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

<?php include '../../template/footer.php'; ?>