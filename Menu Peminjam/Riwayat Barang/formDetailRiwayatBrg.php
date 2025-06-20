<?php
include '../../templates/header.php';
$idPeminjamanBrg = $_GET['idPeminjamanBrg'] ?? '';
$data = [];

if (!empty($idPeminjamanBrg)) {
    $_SESSION['idPeminjamanBrg'] = $idPeminjamanBrg;

    $query = "SELECT pb.*, b.namaBarang FROM Peminjaman_Barang pb 
              JOIN Barang b ON pb.idBarang = b.idBarang 
              WHERE pb.idPeminjamanBrg = ?";
    $params = array($idPeminjamanBrg);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt && sqlsrv_has_rows($stmt)) {
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }
}

// Ekstrak data
$idBarang = $data['idBarang'] ?? '';
$namaBarang = $data['namaBarang'] ?? '';
$nim = $data['nim'] ?? '';
$npk = $data['npk'] ?? '';
$tglPeminjamanBrg = isset($data['tglPeminjamanBrg']) ? $data['tglPeminjamanBrg']->format('d-m-Y') : '';
$jumlahBrg = $data['jumlahBrg'] ?? '';
$alasanPeminjamanBrg = $data['alasanPeminjamanBrg'] ?? '';
$currentStatus = $data['statusPeminjaman'] ?? 'Diajukan';

// Ambil alasan penolakan jika status ditolak
$alasanPenolakan = '';
if ($currentStatus == 'Ditolak') {
    $sqlPenolakan = "SELECT alasanPenolakan FROM Penolakan WHERE idPeminjamanBrg = ?";
    $stmtPenolakan = sqlsrv_query($conn, $sqlPenolakan, [$idPeminjamanBrg]);

    if ($stmtPenolakan && $rowPenolakan = sqlsrv_fetch_array($stmtPenolakan, SQLSRV_FETCH_ASSOC)) {
        $alasanPenolakan = $rowPenolakan['alasanPenolakan'];
    }
}

include '../../templates/sidebar.php';
?>
<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Riwayat Peminjaman Barang</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Barang/riwayatBarang.php">Riwayat Peminjaman Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Peminjaman Barang</li>
            </ol>
        </nav>
    </div>

    <!-- Detail Peminjaman Barang -->
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-semibold">Detail Peminjaman Barang</span>
                    </div>
                    <div class="card-body overflow-auto" style="max-height: 425px;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">ID Peminjaman Barang</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($idPeminjamanBrg) ?></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">ID Barang</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($idBarang) ?></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nama Barang</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($namaBarang) ?></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tanggal Peminjaman</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($tglPeminjamanBrg) ?></div>
                                </div>
                                <div class="d-flex justify-content-start">
                                    <a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Barang/riwayatBarang.php" class="btn btn-secondary">Kembali</a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Jumlah Barang</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($jumlahBrg) ?></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">NIM</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($nim) ?></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">NPK</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($npk) ?></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Status Peminjaman</label>
                                    <div class="form-control-plaintext">
                                        <?php
                                        $statusClass = '';
                                        switch ($currentStatus) {
                                            case 'Telah Dikembalikan':
                                                $statusClass = 'text-success';
                                                break;
                                            case 'Sedang Dipinjam':
                                                $statusClass = 'text-primary';
                                                break;
                                            case 'Menunggu Persetujuan':
                                                $statusClass = 'text-warning';
                                                break;
                                            case 'Ditolak':
                                                $statusClass = 'text-danger';
                                                break;
                                            default:
                                                $statusClass = 'text-secondary';
                                        }
                                        ?>
                                        <span class="<?= $statusClass ?> fw-semibold"><?= htmlspecialchars($currentStatus) ?></span>
                                    </div>
                                </div>
                                <!-- Alasan Peminjaman -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Alasan Peminjaman</label>
                                    <div class="form-control-plaintext"><?= nl2br(htmlspecialchars($alasanPeminjamanBrg)) ?></div>
                                </div>
                                <!-- Alasan Penolakan (hanya tampil jika status ditolak) -->
                                <?php if ($currentStatus == 'Ditolak' && !empty($alasanPenolakan)): ?>
                                    <div class="mb-0">
                                        <label class="form-label fw-semibold text-danger">Alasan Penolakan</label>
                                        <div class="form-control-plaintext text-danger"><?= nl2br(htmlspecialchars($alasanPenolakan)) ?></div>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include '../../templates/footer.php';
?>