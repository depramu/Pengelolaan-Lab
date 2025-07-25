<?php
require_once __DIR__ . '/../../../function/init.php';
authorize_role(['Peminjam']);

$data = null;
$error_message = null;

if (isset($_GET['idPeminjamanBrg'])) {
    $idPeminjamanBrg = $_GET['idPeminjamanBrg'];
    $_SESSION['idPeminjamanBrg'] = $idPeminjamanBrg;

    $query = "SELECT 
                pb.idPeminjamanBrg, pb.idBarang, pb.nim, pb.npk,
                pb.tglPeminjamanBrg, pb.jumlahBrg, pb.alasanPeminjamanBrg,
                b.namaBarang,
                sp.statusPeminjaman,
                sp.alasanPenolakan
            FROM 
                Peminjaman_Barang pb
            JOIN 
                Barang b ON pb.idBarang = b.idBarang
            LEFT JOIN 
                Status_Peminjaman sp ON pb.idPeminjamanBrg = sp.idPeminjamanBrg
            WHERE 
                pb.idPeminjamanBrg = ?";
    $params = [$idPeminjamanBrg];
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        $error_message = "Gagal mengambil data. Error: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
    } else {
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if (!$data) {
            $error_message = "Data peminjaman dengan ID '" . htmlspecialchars($idPeminjamanBrg) . "' tidak ditemukan.";
        }
    }
} else {
    $error_message = "ID Peminjaman Barang tidak valid atau tidak disertakan.";
}

include __DIR__ . '/../../../templates/header.php';
include __DIR__ . '/../../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Riwayat Peminjaman Barang</h3>
    <div class="mb-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu Peminjam/Riwayat Barang/riwayatBarang.php">Riwayat Peminjaman Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Peminjaman Barang</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header border-bottom border-dark text-white" style="background-color:rgb(9, 103, 185);">
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
                                            <label class="form-label fw-semibold">Nama Barang</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['namaBarang']) ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['namaBarang']) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Tanggal Peminjaman</label>
                                            <div class="form-control-plaintext">
                                                <?= htmlspecialchars(
                                                    ($data['tglPeminjamanBrg'] instanceof DateTime)
                                                        ? $data['tglPeminjamanBrg']->format('d M Y')
                                                        : $data['tglPeminjamanBrg']
                                                ) ?>
                                            </div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars(
                                                                                                    ($data['tglPeminjamanBrg'] instanceof DateTime)
                                                                                                        ? $data['tglPeminjamanBrg']->format('d-m-Y')
                                                                                                        : $data['tglPeminjamanBrg']
                                                                                                ) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Jumlah Barang</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['jumlahBrg']) ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['jumlahBrg']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Alasan Peminjaman</label>
                                            <div class="form-control-plaintext"><?= nl2br(htmlspecialchars($data['alasanPeminjamanBrg'])) ?></div>
                                            <textarea class="form-control" rows="3" hidden><?= htmlspecialchars($data['alasanPeminjamanBrg']) ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Status Peminjaman</label>
                                            <?php
                                            $statusClass = 'text-secondary';
                                            switch ($data['statusPeminjaman']) {
                                                case 'Sebagian Dikembalikan':
                                                    $statusClass = 'text-success';
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
                                            <div class="form-control-plaintext <?= $statusClass ?>"><?= htmlspecialchars($data['statusPeminjaman']) ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['statusPeminjaman']) ?>">
                                        </div>
                                    </div>
                                </div>

                                <?php
                                if ($data['statusPeminjaman'] == 'Ditolak' && !empty($data['alasanPenolakan'])) : ?>
                                    <hr>
                                    <h6 class="mb-3">DETAIL PENOLAKAN</h6>
                                    <div class="mt-3">
                                        <label class="form-label fw-bold text-danger">Alasan Penolakan dari PIC</label>
                                        <div class="form-control-plaintext text-danger"><?= nl2br(htmlspecialchars($data['alasanPenolakan'])) ?></div>
                                    </div>
                                <?php endif; ?>

                                <div class="d-flex justify-content-between mt-3">
                                    <a href="<?= BASE_URL ?>/Menu/Menu Peminjam/Riwayat Barang/riwayatBarang.php" class="btn btn-secondary me-2">Kembali</a>
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
include __DIR__ . '/../../../templates/footer.php';
?>