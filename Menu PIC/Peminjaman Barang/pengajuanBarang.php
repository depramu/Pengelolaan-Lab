<?php
include '../../templates/header.php';

$idPeminjamanBrg = $_GET['id'] ?? '';
$data = [];

$showRejectedModal = false;
$showModal = false;

if (!empty($idPeminjamanBrg)) {
    $_SESSION['idPeminjamanBrg'] = $idPeminjamanBrg;

    $query = "SELECT pb.*, b.namaBarang 
            FROM Peminjaman_Barang pb 
            JOIN Barang b ON pb.idBarang = b.idBarang 
            WHERE pb.idPeminjamanBrg = ?";
    $params = array($idPeminjamanBrg);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt && sqlsrv_has_rows($stmt)) {
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($idPeminjamanBrg)) {
    $query = "UPDATE Peminjaman_Barang 
                SET statusPeminjaman = 'Sedang Dipinjam'
                WHERE idPeminjamanBrg = ?";
    $params = array($idPeminjamanBrg);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt) {
        $showModal = true; // Tampilkan modal sukses
    } else {
        $error = "Gagal menyetujui peminjaman barang.";
        exit;
    }
}



$idBarang = $data['idBarang'] ?? '';
$nim = $data['nim'] ?? '';
$namaBarang = $data['namaBarang'] ?? '';
$npk = $data['npk'] ?? '';
$tglPeminjamanBrg = isset($data['tglPeminjamanBrg']) ? $data['tglPeminjamanBrg']->format('Y-m-d') : '';
$jumlahBrg = $data['jumlahBrg'] ?? '';
$alasanPeminjamanBrg = $data['alasanPeminjamanBrg'] ?? '';

include '../../templates/sidebar.php';
?>
<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Pengajuan Peminjaman Barang</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="peminjamanBarang.php">Peminjaman Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengajuan Peminjaman Barang</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-semibold">Pengajuan Peminjaman Barang</span>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">ID Barang</label>
                                        <input type="hidden" name="idBarang" value="<?= htmlspecialchars($idBarang) ?>">
                                        <div class="form-control-plaintext"><?= htmlspecialchars($idBarang) ?></div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">Tanggal Peminjaman</label>
                                        <input type="hidden" name="tglPeminjamanBrg" value="<?= htmlspecialchars($tglPeminjamanBrg) ?>">
                                        <div class="form-control-plaintext"><?= htmlspecialchars($tglPeminjamanBrg) ?></div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">Jumlah Barang</label>
                                        <input type="hidden" name="jumlahBrg" value="<?= htmlspecialchars($jumlahBrg) ?>">
                                        <div class="form-control-plaintext"><?= htmlspecialchars($jumlahBrg) ?></div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">Alasan Peminjaman</label>
                                        <div class="form-control-plaintext">
                                            <?= nl2br(htmlspecialchars($alasanPeminjamanBrg)) ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">ID Peminjaman Barang</label>
                                        <input type="hidden" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">
                                        <div class="form-control-plaintext"><?= htmlspecialchars($idPeminjamanBrg) ?></div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">Nama Barang</label>
                                        <input type="hidden" name="namaBarang" value="<?= htmlspecialchars($namaBarang) ?>">
                                        <div class="form-control-plaintext"><?= htmlspecialchars($namaBarang) ?></div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">NIM</label>
                                        <input type="hidden" name="nim" value="<?= htmlspecialchars($nim) ?>">
                                        <div class="form-control-plaintext"><?= htmlspecialchars($nim) ?></div>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label fw-bold">NPK</label>
                                        <input type="hidden" name="npk" value="<?= htmlspecialchars($npk) ?>">
                                        <div class="form-control-plaintext"><?= htmlspecialchars($npk) ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between gap-2 mt-4">
                                <a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php" class="btn btn-secondary">Kembali</a>
                                <div class="d-flex gap-2">
                                    <a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/penolakanBarang.php?id=<?= htmlspecialchars($idPeminjamanBrg) ?>" class="btn btn-danger">Tolak</a>
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


<?php
include '../../templates/footer.php';
?>