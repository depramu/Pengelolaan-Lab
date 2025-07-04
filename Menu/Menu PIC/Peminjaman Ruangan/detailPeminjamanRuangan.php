<?php
require_once __DIR__ . '/../../../function/init.php';
authorize_role(['PIC Aset']);

$data = null;
$error_message = null;

if (isset($_GET['id'])) {
    $idPeminjamanRuangan = $_GET['id'];
    $_SESSION['idPeminjamanRuangan'] = $idPeminjamanRuangan;

    $query = "SELECT 
                pr.idPeminjamanRuangan, pr.idRuangan, pr.nim, pr.npk,
                pr.tglPeminjamanRuangan, pr.waktuMulai, pr.waktuSelesai,
                pr.alasanPeminjamanRuangan,
                r.namaRuangan,
                sp.statusPeminjaman,
                sp.alasanPenolakan,
                peng.dokumentasiSebelum, peng.dokumentasiSesudah
            FROM 
                Peminjaman_Ruangan pr
            JOIN 
                Ruangan r ON pr.idRuangan = r.idRuangan
            LEFT JOIN 
                Status_Peminjaman sp ON pr.idPeminjamanRuangan = sp.idPeminjamanRuangan
            LEFT JOIN 
                Pengembalian_Ruangan peng ON pr.idPeminjamanRuangan = peng.idPeminjamanRuangan
            WHERE 
                pr.idPeminjamanRuangan = ?";
    $params = [$idPeminjamanRuangan];
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        $error_message = "Gagal mengambil data. Error: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
    } else {
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if (!$data) {
            $error_message = "Data peminjaman dengan ID '" . htmlspecialchars($idPeminjamanRuangan) . "' tidak ditemukan.";
        }
    }
} else {
    $error_message = "ID Peminjaman Ruangan tidak valid atau tidak disertakan.";
}

include '../../../templates/header.php';
include '../../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Ruangan</h3>
    <div class="mb-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php">Peminjaman Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Peminjaman Ruangan</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-bold">Detail Peminjaman Ruangan</span>
                    </div>
                    <div class="card-body scrollable-card-content">
                        <?php if ($error_message) : ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $error_message ?>
                            </div>
                        <?php elseif ($data) : ?>
                            <form id="formDetail" method="POST">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">ID Peminjaman</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['idPeminjamanRuangan']) ?></div>
                                            <input type="hidden" name="idPeminjamanRuangan" class="form-control" value="<?= htmlspecialchars($data['idPeminjamanRuangan']) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">NIM / NPK</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['nim'] ?? $data['npk'] ?? '-') ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['nim'] ?? $data['npk'] ?? '-') ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Tanggal Peminjaman</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['tglPeminjamanRuangan'] instanceof DateTime ? $data['tglPeminjamanRuangan']->format('d-m-Y')
                                                                                    : $data['tglPeminjamanRuangan']) ?></div>
                                            <input type="hidden" class="form-control" value="<?= ($data['tglPeminjamanRuangan'] instanceof DateTime) ? $data['tglPeminjamanRuangan']->format('d F Y') : '' ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Alasan Peminjaman</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['alasanPeminjamanRuangan']) ?></div>
                                            <textarea class="form-control" rows="3" hidden><?= htmlspecialchars($data['alasanPeminjamanRuangan']) ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">ID Ruangan</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['idRuangan']) ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['idRuangan']) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nama Ruangan</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['namaRuangan'] ?? '-') ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['namaRuangan'] ?? '-') ?>">
                                        </div>
                                        <div class="mb-3">
                                            <div class="row">
                                                <div class="col-6"> <label class="form-label fw-semibold">Waktu Mulai:</label>
                                                    <p class="form-control-plaintext"><?= htmlspecialchars($data['waktuMulai'] instanceof DateTime ? $data['waktuMulai']->format('H:i') : '') ?></p>
                                                </div>
                                                <div class="col-6"> <label class="form-label fw-semibold">Waktu Selesai:</label>
                                                    <p class="form-control-plaintext"><?= htmlspecialchars($data['waktuSelesai'] instanceof DateTime ? $data['waktuSelesai']->format('H:i') : '') ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Status Peminjaman</label>
                                            <?php
                                            $statusClass = 'text-secondary';
                                            switch ($data['statusPeminjaman']) {
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

                                <?php if ($data['statusPeminjaman'] == 'Telah Dikembalikan') : ?>
                                    <h6 class="mb-3">DOKUMENTASI PEMAKAIAN</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">Dokumentasi Sebelum</label>
                                            <div class="mt-1">
                                                <?php if (!empty($data['dokumentasiSebelum'])) : ?>
                                                    <a href="<?= BASE_URL ?>/uploads/dokumentasi/<?= htmlspecialchars($data['dokumentasiSebelum']) ?>" target="_blank">Lihat Dokumentasi</a>
                                                <?php else : ?>
                                                    <span class="text-danger"><em>(Tidak Diupload)</em></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">Dokumentasi Selesai</label>
                                            <div class="mt-1">
                                                <?php if (!empty($data['dokumentasiSesudah'])) : ?>
                                                    <a href="<?= BASE_URL ?>/uploads/dokumentasi/<?= htmlspecialchars($data['dokumentasiSesudah']) ?>" target="_blank">Lihat Dokumentasi</a>
                                                <?php else : ?>
                                                    <span class="text-danger"><em>(Tidak Diupload)</em></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="d-flex justify-content-between mt-3">
                                    <a href="<?= BASE_URL ?>/Menu/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php" class="btn btn-secondary me-2">Kembali</a>
                                </div>

                            </form>
                        <?php endif; ?>
                </>
            </div>
        </div>
    </div>
    </div>
</main>


<?php
include '../../../templates/footer.php';
?>