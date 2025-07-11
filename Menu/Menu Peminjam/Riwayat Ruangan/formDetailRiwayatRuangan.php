<?php
require_once __DIR__ . '/../../../function/init.php';
authorize_role(['Peminjam']);

$showModal = false;
$data = null;
$error_message = null;

// Cek baik POST maupun GET untuk idPeminjamanRuangan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idPeminjamanRuangan = $_POST['idPeminjamanRuangan'];
    $showModal = true; // hanya set true jika POST
} else if (isset($_GET['idPeminjamanRuangan'])) {
    $idPeminjamanRuangan = $_GET['idPeminjamanRuangan'];
} else {
    $idPeminjamanRuangan = null;
}

if ($idPeminjamanRuangan !== null) {
    // Query untuk mendapatkan data peminjaman ruangan
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
        // $showModal tidak di-set di sini, hanya di POST
    }

    // Query untuk mendapatkan status peminjaman dari tabel Status_Peminjaman
    if ($data && !$error_message) {
        $statusSql = "SELECT statusPeminjaman, alasanPenolakan
                      FROM Status_Peminjaman 
                      WHERE idPeminjamanRuangan = ?";
        $statusParams = [$idPeminjamanRuangan];
        $statusStmt = sqlsrv_query($conn, $statusSql, $statusParams);

        if ($statusStmt === false) {
            $error_message = "Gagal mengambil status peminjaman. Error: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
        } else {
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
                        <?php if ($error_message) : ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $error_message ?>
                            </div>
                        <?php elseif ($data) : ?>
                            <form id="formDetail" action="proses_pengembalian.php" method="POST" enctype="multipart/form-data">
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
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['tglPeminjamanRuangan'] instanceof DateTime)  ? $data['tglPeminjamanRuangan']->format('d-m-y') : '' ?></div>
                                            <input type="hidden" class="form-control" value="<?= ($data['tglPeminjamanRuangan'] instanceof DateTime) ? $data['tglPeminjamanRuangan']->format('d-m-y') : '' ?>">
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
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['namaRuangan']) ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['namaRuangan']) ?>">
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
                                                case 'Menunggu Pengecekan':
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

                                <?php if (in_array($data['statusPeminjaman'], ['Ditolak', 'Sedang Dipinjam', 'Menunggu Pengecekan', 'Telah Dikembalikan'])) : ?>
                                    <hr>
                                    <?php if ($data['statusPeminjaman'] == 'Ditolak') : ?>
                                        <h6 class=" mb-3">DETAIL PENOLAKAN</h6>
                                        <div class="mt-3">
                                            <label class="form-label fw-semibold">Alasan Penolakan dari PIC</label>
                                            <textarea class="form-control" rows="3"><?= htmlspecialchars($data['alasanPenolakan'] ?? 'Tidak ada alasan spesifik.') ?></textarea>
                                        </div>
                                    <?php else: ?>
                                        <h6 class=" mb-3">DOKUMENTASI PEMAKAIAN</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">
                                                    Dokumentasi Sebelum
                                                    <span id="dokSebelumError" class="text-danger ms-2 fw-normal" style="font-size: 0.875em;"></span>
                                                </label>
                                                <?php if ($data['statusPeminjaman'] == 'Sedang Dipinjam') : ?>
                                                    <input type="file" class="form-control" id="dokSebelum" name="dokSebelum" accept="image/*">
                                                <?php else : ?>
                                                    <div class="mt-1">
                                                        <?php if (!empty($data['dokumentasiSebelum'])) : ?>
                                                            <a href="<?= BASE_URL ?>/uploads/dokumentasi/<?= htmlspecialchars($data['dokumentasiSebelum']) ?>" target="_blank">Lihat Dokumentasi</a>
                                                        <?php else : ?>
                                                            <span class="text-danger"><em>(Tidak Diunggah)</em></span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">
                                                    Dokumentasi Selesai
                                                    <span id="dokSesudahError" class="text-danger ms-2 fw-normal" style="font-size: 0.875em;"></span>
                                                </label>
                                                <?php if ($data['statusPeminjaman'] == 'Sedang Dipinjam') : ?>
                                                    <input type="file" class="form-control" id="dokSesudah" name="dokSesudah" accept="image/*">
                                                <?php else : ?>
                                                    <div class="mt-1">
                                                        <?php if (!empty($data['dokumentasiSesudah'])) : ?>
                                                            <a href="<?= BASE_URL ?>/uploads/dokumentasi/<?= htmlspecialchars($data['dokumentasiSesudah']) ?>" target="_blank">Lihat Dokumentasi</a>
                                                        <?php else : ?>
                                                            <span class="text-danger"><em>(Tidak Diunggah)</em></span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>

                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <a href="<?= BASE_URL ?>/Menu/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php" class="btn btn-secondary me-2">Kembali</a>
                                <?php if ($data['statusPeminjaman'] == 'Sedang Dipinjam') : ?>
                                    <button type="submit" name="submit_pengembalian" class="btn btn-primary">Kirim</button>
                                <?php endif; ?>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>


<?php
include __DIR__ . '/../../../templates/footer.php';

?>