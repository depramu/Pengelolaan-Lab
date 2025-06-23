<?php
$showSuccessModal = false;
if (isset($_GET['upload']) && $_GET['upload'] == 'sukses') {
    $showSuccessModal = true;
}
include '../../templates/header.php';
include '../../templates/sidebar.php';

$data = [];
$error_message = null;

$idPeminjamanBrg = $_GET['idPeminjamanBrg'] ?? '';
if (!empty($idPeminjamanBrg)) {
    $_SESSION['idPeminjamanBrg'] = $idPeminjamanBrg;

    $query = "SELECT 
                pb.idPeminjamanBrg, pb.idBarang, pb.nim, pb.npk,
                pb.tglPeminjamanBrg, pb.jumlahBrg, pb.alasanPeminjamanBrg, pb.statusPeminjaman,
                b.namaBarang
            FROM 
                Peminjaman_Barang pb
            JOIN 
                Barang b ON pb.idBarang = b.idBarang
            WHERE 
                pb.idPeminjamanBrg = ?";
    $params = array($idPeminjamanBrg);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt && sqlsrv_has_rows($stmt)) {
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    } else {
        $error_message = "Data peminjaman dengan ID '" . htmlspecialchars($idPeminjamanBrg) . "' tidak ditemukan.";
    }
} else {
    $error_message = "ID Peminjaman Barang tidak valid atau tidak disertakan.";
}

// Ekstrak data
$idBarang = $data['idBarang'] ?? '';
$namaBarang = $data['namaBarang'] ?? '';
$nim = $data['nim'] ?? '';
$npk = $data['npk'] ?? '';
$tglPeminjamanBrg = isset($data['tglPeminjamanBrg']) && $data['tglPeminjamanBrg'] instanceof DateTime ? $data['tglPeminjamanBrg']->format('d-m-Y') : '';
$jumlahBrg = $data['jumlahBrg'] ?? '';
$alasanPeminjamanBrg = $data['alasanPeminjamanBrg'] ?? '';
$currentStatus = $data['statusPeminjaman'] ?? 'Diajukan';

// Ambil alasan penolakan jika status ditolak
$alasanPenolakan = '';
if ($currentStatus == 'Ditolak') {
    $sqlPenolakan = "SELECT alasanPenolakan FROM Penolakan WHERE idPeminjamanBrg = ?";
    $stmtPenolakan = sqlsrv_query($conn, $sqlPenolakan, [$idPeminjamanBrg]);
    if ($stmtPenolakan && sqlsrv_has_rows($stmtPenolakan)) {
        $rowPenolakan = sqlsrv_fetch_array($stmtPenolakan, SQLSRV_FETCH_ASSOC);
        $alasanPenolakan = $rowPenolakan['alasanPenolakan'] ?? '';
    }
}
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Riwayat Peminjaman Barang</h3>
    <div class="mb-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Barang/riwayatBarang.php">Riwayat Peminjaman Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Peminjaman Barang</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-semibold">Detail Peminjaman Barang</span>
                    </div>
                    <div class="card-body scrollable-card-content">
                        <?php if ($error_message) : ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $error_message ?>
                            </div>
                        <?php elseif (!empty($data)) : ?>
                            <form id="formDetail" action="#" method="POST" enctype="multipart/form-data">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">ID Peminjaman Barang</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['idPeminjamanBrg']) ?></div>
                                            <input type="hidden" name="idPeminjamanBrg" class="form-control" value="<?= htmlspecialchars($data['idPeminjamanBrg']) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">NIM / NPK</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($nim ?: $npk ?: '-') ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($nim ?: $npk ?: '-') ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">ID Barang</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($idBarang) ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($idBarang) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Nama Barang</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($namaBarang) ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($namaBarang) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Tanggal Peminjaman</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($tglPeminjamanBrg) ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($tglPeminjamanBrg) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Jumlah Barang</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($jumlahBrg) ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($jumlahBrg) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Status Peminjaman</label>
                                            <?php
                                            $statusClass = 'text-secondary';
                                            switch ($currentStatus) {
                                                case 'Diajukan':
                                                    $statusClass = 'text-primary';
                                                    break;
                                                case 'Menunggu Persetujuan':
                                                    $statusClass = 'text-warning';
                                                    break;
                                                case 'Sedang Dipinjam':
                                                    $statusClass = 'text-info';
                                                    break;
                                                case 'Telah Dikembalikan':
                                                    $statusClass = 'text-success';
                                                    break;
                                                case 'Ditolak':
                                                    $statusClass = 'text-danger';
                                                    break;
                                            }
                                            ?>
                                            <div class="form-control-plaintext <?= $statusClass ?> fw-semibold"><?= htmlspecialchars($currentStatus) ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($currentStatus) ?>">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Alasan Peminjaman</label>
                                            <div class="form-control-plaintext"><?= nl2br(htmlspecialchars($alasanPeminjamanBrg)) ?></div>
                                            <textarea class="form-control" rows="3" hidden><?= htmlspecialchars($alasanPeminjamanBrg) ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                if ($currentStatus == 'Ditolak' && !empty($alasanPenolakan)) : ?>
                                    <hr>
                                    <h6 class="mb-3">DETAIL PENOLAKAN</h6>
                                    <div class="mt-3">
                                        <label class="form-label fw-bold text-danger">Alasan Penolakan dari PIC</label>
                                        <div class="form-control-plaintext text-danger"><?= nl2br(htmlspecialchars($alasanPenolakan)) ?></div>
                                    </div>
                                <?php endif; ?>

                                <div class="d-flex justify-content-between mt-3">
                                    <a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Barang/riwayatBarang.php" class="btn btn-secondary me-2">Kembali</a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include '../../templates/footer.php';
?>