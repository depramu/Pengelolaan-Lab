<?php
include '../../templates/header.php';

$data = null;
$error_message = null;

if (isset($_GET['id'])) {
    $idPeminjamanRuanganDariURL = $_GET['id'];

    $sql = "SELECT 
                p.idPeminjamanRuangan,
                p.idRuangan,
                p.nim,
                p.npk,
                p.tglPeminjamanRuangan,
                p.waktuMulai,
                p.waktuSelesai,
                p.alasanPeminjamanRuangan,
                p.statusPeminjaman,
                r.dokumentasiSebelum,
                r.dokumentasiSesudah,
                r.catatanPengembalianRuangan,
                r.kondisiRuangan
            FROM 
                Peminjaman_Ruangan p
            LEFT JOIN 
                Pengembalian_Ruangan r ON p.idPeminjamanRuangan = r.idPeminjamanRuangan
            WHERE 
                p.idPeminjamanRuangan = ?";

    $params = array($idPeminjamanRuanganDariURL);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        $error_message = "Gagal mengambil data. Error: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
    } else {
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if (!$data) {
            $error_message = "Data peminjaman dengan ID '" . htmlspecialchars($idPeminjamanRuanganDariURL) . "' tidak ditemukan.";
        }
    }
} else {
    $error_message = "ID Peminjaman Ruangan tidak disertakan di URL.";
}

include '../../templates/sidebar.php'
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <div class="mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php">Riwayat Peminjaman Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengembalian Ruangan</li>
            </ol>
        </nav>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-12 " style="margin-right: 5px;">
            <div class="card border border-dark">
                <div class="card-header bg-white border-bottom border-dark">
                    <span class="fw-semibold">Riwayat Pengajuan Peminjaman Ruangan</span>
                </div>
                <!-- Scroll hanya pada bagian form -->
                <div class="card-body p-4 scrollable-card-content" style="max-height: 65vh; overflow-y: auto;">
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                    <?php elseif ($data): ?>
                        <form id="formPengembalian" action="proses_pengembalian.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="idPeminjamanRuangan" value="<?= htmlspecialchars($data['idPeminjamanRuangan'] ?? '') ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="idRuangan" class="form-label">ID Ruangan</label>
                                        <input type="text" class="form-control" id="idRuangan" name="idRuangan" value="<?= htmlspecialchars($data['idRuangan'] ?? '') ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tanggalPeminjaman" class="form-label">Tanggal Peminjaman</label>
                                        <input type="text" class="form-control" id="tanggalPeminjaman" name="tanggalPeminjaman" value="<?= ($data['tglPeminjamanRuangan'] instanceof DateTime ? $data['tglPeminjamanRuangan']->format('l, d F Y') : htmlspecialchars($data['tglPeminjamanRuangan'] ?? '')) ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="waktuPeminjaman" class="form-label">Waktu Peminjaman</label>
                                        <input type="text" class="form-control" id="waktuPeminjaman" name="waktuPeminjaman" value="<?= ($data['waktuMulai'] instanceof DateTime ? $data['waktuMulai']->format('H:i') : '') . ' - ' . ($data['waktuSelesai'] instanceof DateTime ? $data['waktuSelesai']->format('H:i') : '') ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="alasanPeminjaman" class="form-label">Alasan Peminjaman</label>
                                        <textarea class="form-control" id="alasanPeminjaman" name="alasanPeminjaman" rows="3" disabled><?= htmlspecialchars($data['alasanPeminjamanRuangan'] ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="idPeminjaman" class="form-label">ID Peminjaman</label>
                                        <input type="text" class="form-control" id="idPeminjaman" name="idPeminjaman" value="<?= htmlspecialchars($data['idPeminjamanRuangan'] ?? '') ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="nim" class="form-label">NIM</label>
                                        <input type="text" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($data['nim'] ?? '') ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="npk" class="form-label">NPK</label>
                                        <input type="text" class="form-control" id="npk" name="npk" value="<?= htmlspecialchars($data['npk'] ?? '') ?>" disabled>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h5 class="mt-4 mb-3">Form Pengembalian</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="dokSebelum" class="form-label">Dokumentasi Sebelum Pemakaian <span id="errorDokSebelum" class="error-text"></span></label>
                                        <input type="file" class="form-control" id="dokSebelum" name="dokSebelum" accept=".jpg, .jpeg, .png, .heif">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="dokSesudah" class="form-label">Dokumentasi Sesudah Pemakaian <span id="errorDokSesudah" class="error-text"></span></label>
                                        <input type="file" class="form-control" id="dokSesudah" name="dokSesudah" accept=".jpg, .jpeg, .png, .heif">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4 pt-3">
                                <a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php" class="btn btn-secondary px-4">Kembali</a>
                                <button type="submit" name="submit_pengembalian" class="btn btn-primary px-4">Kirim</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-info">
                            Silakan pilih data peminjaman yang ingin dilihat detailnya, atau pastikan ID Peminjaman valid.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    document.getElementById('formPengembalian').addEventListener('submit', function(event) {
        let isValid = true;
        const dokSebelum = document.getElementById('dokSebelum');
        const errorDokSebelum = document.getElementById('errorDokSebelum');
        const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.heif)$/i;
        errorDokSebelum.textContent = '';
        if (dokSebelum.files.length === 0) {
            errorDokSebelum.textContent = '*Harus Diisi';
            isValid = false;
        } else if (!allowedExtensions.exec(dokSebelum.value)) {
            errorDokSebelum.textContent = '*Format file harus .jpg, .jpeg, .png, atau .heif';
            dokSebelum.value = '';
            isValid = false;
        }
        const dokSesudah = document.getElementById('dokSesudah');
        const errorDokSesudah = document.getElementById('errorDokSesudah');
        errorDokSesudah.textContent = '';
        if (dokSesudah.files.length === 0) {
            errorDokSesudah.textContent = '*Harus Diisi';
            isValid = false;
        } else if (!allowedExtensions.exec(dokSesudah.value)) {
            errorDokSesudah.textContent = '*Format file harus .jpg, .jpeg, .png, atau .heif';
            dokSesudah.value = '';
            isValid = false;
        }
        if (!isValid) {
            event.preventDefault();
        }
    });
</script>

<?php

include '../../templates/footer.php';
?>