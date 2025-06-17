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


$idBarang = $data['idBarang'] ?? '';
$nim = $data['nim'] ?? '';
$namaBarang = $data['namaBarang'] ?? '';
$npk = $data['npk'] ?? '';
$tglPeminjamanBrg = isset($data['tglPeminjamanBrg']) ? $data['tglPeminjamanBrg']->format('Y-m-d') : '';
$jumlahBrg = $data['jumlahBrg'] ?? '';
$alasanPeminjamanBrg = $data['alasanPeminjamanBrg'] ?? '';
$currentStatus = $data['statusPeminjaman'] ?? 'Diajukan';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $query = "UPDATE Peminjaman_Barang 
                    SET statusPeminjaman = 'Sedang Dipinjam'
                    WHERE idPeminjamanBrg = ?";
    $params = array($idPeminjamanBrg);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt) {
        $showModal = true;
    } else {
        $error = "Gagal melakukan pengajuan barang.";
        exit;
    }
}
include '../../templates/sidebar.php';
?>
<main class="col bg-white px-4 py-3 position-relative">
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="peminjamanBarang.php">Peminjaman Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengajuan Barang</li>
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
                                        <label class="form-label">ID Barang</label>
                                        <div class="form-control bg-light"><?= htmlspecialchars($idBarang) ?></div>
                                        <input type="hidden" name="idBarang" value="<?= htmlspecialchars($idBarang) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Tanggal Peminjaman Barang</label>
                                        <div class="form-control bg-light"><?= htmlspecialchars($tglPeminjamanBrg) ?></div>
                                        <input type="hidden" name="tglPeminjamanBrg" value="<?= htmlspecialchars($tglPeminjamanBrg) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Jumlah Barang</label>
                                        <div class="form-control bg-light"><?= htmlspecialchars($jumlahBrg) ?></div>
                                        <input type="hidden" name="jumlahBrg" value="<?= htmlspecialchars($jumlahBrg) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Alasan Peminjaman</label>
                                        <textarea class="form-control" id="alasanPeminjamanBrg" rows="3" disabled><?= htmlspecialchars($alasanPeminjamanBrg) ?></textarea>
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label">ID Peminjaman Barang</label>
                                        <div class="form-control bg-light"><?= htmlspecialchars($idPeminjamanBrg) ?></div>
                                        <input type="hidden" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Nama Barang</label>
                                        <div class="form-control bg-light"><?= htmlspecialchars($namaBarang) ?></div>
                                        <input type="hidden" name="namaBarang" value="<?= htmlspecialchars($namaBarang) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">NIM</label>
                                        <div class="form-control bg-light"><?= htmlspecialchars($nim) ?></div>
                                        <input type="hidden" name="nim" value="<?= htmlspecialchars($nim) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">NPK</label>
                                        <div class="form-control bg-light"><?= htmlspecialchars($npk) ?></div>
                                        <input type="hidden" name="npk" value="<?= htmlspecialchars($npk) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="penolakanBarang.php?id=<?= htmlspecialchars($idPeminjamanBrg) ?>" class="btn btn-danger">Tolak</a>
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