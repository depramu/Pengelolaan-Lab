<?php
include '../../templates/header.php';
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
$waktuMulai = isset($data['waktuMulai']) ? $data['waktuMulai']->format('H:i') : '';
$waktuSelesai = isset($data['waktuSelesai']) ? $data['waktuSelesai']->format('H:i') : '';
$alasanPeminjamanRuangan = $data['alasanPeminjamanRuangan'] ?? '';
$currentStatus = $data['statusPeminjaman'] ?? 'Diajukan';

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
include '../../templates/sidebar.php';
?>
<!-- Content Area -->
<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Pengajuan Peminjaman Ruangan</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php">Peminjaman Ruangan</a></li>
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
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idRuangan" class="form-label fw-bold">ID Ruangan</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($idRuangan) ?></div>
                                        <input type="hidden" class="form-control" id="idRuangan" name="idRuangan" value="<?= htmlspecialchars($idRuangan) ?>" style="background: #f5f5f5;">
                                    </div>
                                    <div class="mb-2">
                                        <label for="tglPeminjamanRuangan" class="form-label fw-bold">Tanggal Peminjaman</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($tglPeminjamanRuangan) ?></div>
                                        <input type="hidden" class="form-control" id="tglPeminjamanRuangan" name="tglPeminjamanRuangan" value="<?= htmlspecialchars($tglPeminjamanRuangan) ?>" style="background: #f5f5f5;">
                                    </div>
                                    <div class="mb-2">
                                        <label for="nim" class="form-label fw-bold">NIM</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($nim) ?></div>
                                        <input type="hidden" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($nim) ?>" style="background: #f5f5f5;">
                                    </div>

                                </div>
                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idPeminjamanRuangan" class="form-label fw-bold">ID Peminjaman Ruangan</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($idPeminjamanRuangan) ?></div>
                                        <input type="hidden" class="form-control" id="idPeminjamanRuangan" value="<?= htmlspecialchars($idPeminjamanRuangan) ?>" style="background: #f5f5f5;">
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label for="waktuMulai" class="form-label fw-bold">Waktu Mulai</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($waktuMulai) ?></div>
                                            <input type="hidden" class="form-control" id="waktuMulai" name="waktuMulai" value="<?= htmlspecialchars($waktuMulai) ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="waktuSelesai" class="form-label fw-bold">Waktu Selesai</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($waktuSelesai) ?></div>
                                            <input type="hidden" class="form-control" id="waktuSelesai" name="waktuSelesai" value="<?= htmlspecialchars($waktuSelesai) ?>">
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label for="npk" class="form-label fw-bold">NPK</label>
                                        <input type="hidden" class="form-control" id="npk" name="npk" value="<?= htmlspecialchars($npk) ?>" style="background: #f5f5f5;">
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label for="alasanPeminjaman" class="form-label fw-bold">Alasan Peminjaman</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($alasanPeminjamanRuangan) ?></div>
                                    <textarea class="form-control w-100" id="alasanPeminjaman" name="alasanPeminjaman" hidden rows="3" style="background: #f5f5f5;"><?= htmlspecialchars($alasanPeminjamanRuangan) ?></textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <!-- TOMBOL AKSI -->
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="penolakanRuangan.php?id=<?= htmlspecialchars($idPeminjamanRuangan) ?>" class="btn btn-danger">Tolak</a>
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

<?php include '../../templates/footer.php'; ?>