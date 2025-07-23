<?php
require_once __DIR__ . '/../../../function/init.php';
authorize_role(['Peminjam']);

$showModal = false;
$data = null;
$error_message = null;

if (isset($_GET['success']) && $_GET['success'] == '1') {
    $showModal = true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idPeminjamanRuangan = $_POST['idPeminjamanRuangan'];
    $showModal = true;
} else if (isset($_GET['idPeminjamanRuangan'])) {
    $idPeminjamanRuangan = $_GET['idPeminjamanRuangan'];
} else {
    $idPeminjamanRuangan = null;
}

if ($idPeminjamanRuangan !== null) {
    $sql = "SELECT
                p.idPeminjamanRuangan, p.idRuangan, p.nim, p.npk,
                p.tglPeminjamanRuangan, p.waktuMulai, p.waktuSelesai,
                p.alasanPeminjamanRuangan,
                sp.statusPeminjaman, sp.alasanPenolakan,
                peng.dokumentasiSebelum, peng.dokumentasiSesudah,
                r.namaRuangan,
                COALESCE(m.nama, k.nama) AS namaPeminjam
            FROM 
                Peminjaman_Ruangan p
            JOIN 
                Ruangan r ON p.idRuangan = r.idRuangan
            LEFT JOIN 
                Status_Peminjaman sp ON p.idPeminjamanRuangan = sp.idPeminjamanRuangan
            LEFT JOIN 
                Pengembalian_Ruangan peng ON p.idPeminjamanRuangan = peng.idPeminjamanRuangan
            LEFT JOIN 
                Mahasiswa m ON p.nim = m.nim
            LEFT JOIN 
                Karyawan k ON p.npk = k.npk
            WHERE 
                p.idPeminjamanRuangan = ?";

    $params = [$idPeminjamanRuangan];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        $error_message = "Gagal mengambil data. Error: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
    } else {
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if (!$data) {
            $error_message = "Data peminjaman dengan ID '" . htmlspecialchars($idPeminjamanRuangan) . "' tidak ditemukan.";
        }
    }

    if ($data && !$error_message) {
        $statusSql = "SELECT statusPeminjaman, alasanPenolakan
                      FROM Status_Peminjaman 
                      WHERE idPeminjamanRuangan = ?";
        $statusParams = [$idPeminjamanRuangan];
        $statusStmt = sqlsrv_query($conn, $statusSql, $statusParams);

        if ($statusStmt !== false) {
            $statusData = sqlsrv_fetch_array($statusStmt, SQLSRV_FETCH_ASSOC);
            if ($statusData) {
                $data['statusPeminjaman'] = $statusData['statusPeminjaman'];
                $data['alasanPenolakan'] = $statusData['alasanPenolakan'];
            }
        }
    }
} else {
    $error_message = "ID Peminjaman Ruangan tidak valid atau tidak disertakan.";
}

include __DIR__ . '/../../../templates/header.php';
include __DIR__ . '/../../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Riwayat Peminjaman Ruangan</h3>
    <div class="mb-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php">Riwayat Peminjaman Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Peminjaman Ruangan</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header border-bottom border-dark text-white" style="background-color:rgb(9, 103, 185);">
                        <span class="fw-semibold">Detail Peminjaman Ruangan</span>
                    </div>
                    <div class="card-body scrollable-card-content">
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger"><?= $error_message ?></div>
                        <?php elseif ($data): ?>
                            <form id="formDetail" action="proses_Pengembalian.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="idPeminjamanRuangan" value="<?= htmlspecialchars($data['idPeminjamanRuangan']) ?>">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">NIM / NPK</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['nim'] ?? $data['npk'] ?? '-') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Tanggal Peminjaman</label>
                                            <div class="form-control-plaintext"><?= $data['tglPeminjamanRuangan'] instanceof DateTime ? $data['tglPeminjamanRuangan']->format('d M Y') : '' ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Alasan Peminjaman</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['alasanPeminjamanRuangan']) ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nama Ruangan</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['namaRuangan']) ?></div>
                                        </div>
                                        <div class="mb-3 row">
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Waktu Mulai</label>
                                                <div class="form-control-plaintext"><?= $data['waktuMulai'] instanceof DateTime ? $data['waktuMulai']->format('H:i') : '' ?></div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Waktu Selesai</label>
                                                <div class="form-control-plaintext"><?= $data['waktuSelesai'] instanceof DateTime ? $data['waktuSelesai']->format('H:i') : '' ?></div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Status Peminjaman</label>
                                            <?php
                                            $statusClass = match($data['statusPeminjaman']) {
                                                'Menunggu Persetujuan' => 'text-warning',
                                                'Sedang Dipinjam', 'Menunggu Pengecekan' => 'text-info',
                                                'Telah Dikembalikan' => 'text-success',
                                                'Ditolak' => 'text-danger',
                                                default => 'text-secondary',
                                            };
                                            ?>
                                            <div class="form-control-plaintext <?= $statusClass ?>"><?= htmlspecialchars($data['statusPeminjaman']) ?></div>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($data['statusPeminjaman'] === 'Ditolak'): ?>
                                    <hr>
                                    <h6 class="mb-3">DETAIL PENOLAKAN</h6>
                                    <label class="form-label fw-semibold">Alasan Penolakan dari PIC</label>
                                    <textarea class="form-control bg-light" rows="3" readonly><?= htmlspecialchars($data['alasanPenolakan'] ?? 'Tidak ada alasan spesifik.') ?></textarea>
                                    <div class="mt-3">
                                        <a href="<?= BASE_URL ?>/Menu/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php" class="btn btn-secondary">Kembali</a>
                                    </div>

                                <?php elseif (in_array($data['statusPeminjaman'], ['Sedang Dipinjam', 'Menunggu Pengecekan', 'Telah Dikembalikan'])): ?>
                                    <hr>
                                    <h6 class="mb-3">DOKUMENTASI PEMAKAIAN</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">Dokumentasi Sebelum</label>
                                            <?php if ($data['statusPeminjaman'] === 'Sedang Dipinjam'): ?>
                                                <span id="dokSebelumError" class="text-danger"></span>
                                                <input type="file" class="form-control" id="dokSebelum" name="dokSebelum" accept="image/*">
                                            <?php elseif (!empty($data['dokumentasiSebelum'])): ?>
                                                <a href="<?= BASE_URL ?>/uploads/dokumentasi/<?= htmlspecialchars($data['dokumentasiSebelum']) ?>" target="_blank">
                                                    <img src="<?= BASE_URL ?>/uploads/dokumentasi/<?= htmlspecialchars($data['dokumentasiSebelum']) ?>"
                                                    alt="Dokumentasi Sebelum"
                                                            class="img-fluid rounded border"
                                                            style="max-height: 200px; cursor: pointer;"
                                                            onclick="window.open('<?= BASE_URL ?>uploads/dokumentasi/<?= htmlspecialchars($data['dokumentasiSebelum']) ?>', '_blank')">
                                                </a>
                                            <?php else: ?>
                                                <span class="text-danger"><em>(Tidak Diunggah)</em></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">Dokumentasi Sesudah</label>
                                            <?php if ($data['statusPeminjaman'] === 'Sedang Dipinjam'): ?>
                                                <span id="dokSesudahError" class="text-danger"></span>
                                                <input type="file" class="form-control" id="dokSesudah"name="dokSesudah" accept="image/*">
                                            <?php elseif (!empty($data['dokumentasiSesudah'])): ?>
                                                <a href="<?= BASE_URL ?>/uploads/dokumentasi/<?= htmlspecialchars($data['dokumentasiSesudah']) ?>" target="_blank">
                                                    <img src="<?= BASE_URL ?>/uploads/dokumentasi/<?= htmlspecialchars($data['dokumentasiSesudah']) ?>"
                                                    alt="Dokumentasi Sesudah"
                                                            class="img-fluid rounded border"
                                                            style="max-height: 200px; cursor: pointer;"
                                                            onclick="window.open('<?= BASE_URL ?>uploads/dokumentasi/<?= htmlspecialchars($data['dokumentasiSesudah']) ?>', '_blank')">
                                                </a>
                                            <?php else: ?>
                                                <span class="text-danger"><em>(Tidak Diunggah)</em></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-3">
                                        <a href="<?= BASE_URL ?>/Menu/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php" class="btn btn-secondary">Kembali</a>
                                        <?php if ($data['statusPeminjaman'] === 'Sedang Dipinjam'): ?>
                                            <button type="submit" name="submit_pengembalian" class="btn btn-primary">Kirim</button>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="mt-3">
                                        <a href="<?= BASE_URL ?>/Menu/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php" class="btn btn-secondary">Kembali</a>
                                    </div>
                                <?php endif; ?>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
