<?php
include '../../templates/header.php';
include '../../templates/sidebar.php';

$data = null;
$error_message = null;

if (isset($_GET['idPeminjamanRuangan'])) {
    $idPeminjamanRuangan = $_GET['idPeminjamanRuangan'];

    $sql = "SELECT 
                p.idPeminjamanRuangan, p.idRuangan, p.nim, p.npk,
                p.tglPeminjamanRuangan, p.waktuMulai, p.waktuSelesai,
                p.alasanPeminjamanRuangan, p.statusPeminjaman,
                peng.dokumentasiSebelum, peng.dokumentasiSesudah,
                tolak.alasanPenolakan,
                COALESCE(m.namaMhs, k.namaKry) AS namaPeminjam
            FROM 
                Peminjaman_Ruangan p
            LEFT JOIN 
                Pengembalian_Ruangan peng ON p.idPeminjamanRuangan = peng.idPeminjamanRuangan
            LEFT JOIN 
                Penolakan tolak ON p.idPeminjamanRuangan = tolak.idPeminjamanRuangan
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
} else {
    $error_message = "ID Peminjaman Ruangan tidak valid atau tidak disertakan.";
}
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php">Riwayat Peminjaman Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Peminjaman</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-semibold">Detail Peminjaman Ruangan</h5>
                </div>
                <div class="card-body p-4 scrollable-card-content">
                    <?php if ($error_message) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?= $error_message ?>
                        </div>
                    <?php elseif ($data) : ?>
                        <form id="formDetail" action="proses_pengembalian.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="idPeminjamanRuangan" value="<?= htmlspecialchars($data['idPeminjamanRuangan']) ?>">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ID Peminjaman</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($data['idPeminjamanRuangan']) ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">NIM / NPK</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($data['nim'] ?? $data['npk'] ?? '-') ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Ruangan</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($data['idRuangan']) ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Peminjaman</label>
                                        <input type="text" class="form-control" value="<?= ($data['tglPeminjamanRuangan'] instanceof DateTime) ? $data['tglPeminjamanRuangan']->format('d F Y') : '' ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Waktu Peminjaman</label>
                                        <input type="text" class="form-control" value="<?= ($data['waktuMulai'] instanceof DateTime ? $data['waktuMulai']->format('H:i') : '') . ' - ' . ($data['waktuSelesai'] instanceof DateTime ? $data['waktuSelesai']->format('H:i') : '') ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Status Peminjaman</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($data['statusPeminjaman']) ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Alasan Peminjaman</label>
                                        <textarea class="form-control" rows="3" disabled><?= htmlspecialchars($data['alasanPeminjamanRuangan']) ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <?php if (in_array($data['statusPeminjaman'], ['Ditolak', 'Sedang dipinjam', 'Menunggu Pengecekan', 'Selesai'])) : ?>
                                <hr>
                                <?php if ($data['statusPeminjaman'] == 'Ditolak') : ?>
                                    <h6 class=" mb-3">DETAIL PENOLAKAN</h6>
                                    <div class="mt-3">
                                        <label class="form-label fw-bold">Alasan Penolakan dari PIC</label>
                                        <textarea class="form-control" rows="3" disabled><?= htmlspecialchars($data['alasanPenolakan'] ?? 'Tidak ada alasan spesifik.') ?></textarea>
                                    </div>
                                <?php else: ?>
                                    <h6 class=" mb-3">DOKUMENTASI PEMAKAIAN</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">
                                                Dokumentasi Sebelum
                                                <span id="dokSebelumError" class="text-danger ms-2 fw-normal" style="font-size: 0.875em;"></span>
                                            </label>
                                            <?php if ($data['statusPeminjaman'] == 'Sedang dipinjam') : ?>
                                                <input type="file" class="form-control" id="dokSebelum" name="dokSebelum" accept="image/*">
                                            <?php else : ?>
                                                <div class="mt-1">
                                                    <?php if (!empty($data['dokumentasiSebelum'])) : ?>
                                                        <a href="<?= BASE_URL ?>/uploads/dokumentasi/<?= htmlspecialchars($data['dokumentasiSebelum']) ?>" target="_blank">Lihat Dokumentasi</a>
                                                    <?php else : ?>
                                                        <span class="text-danger"><em>(Tidak Diupload)</em></span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">
                                                Dokumentasi Selesai
                                                <span id="dokSesudahError" class="text-danger ms-2 fw-normal" style="font-size: 0.875em;"></span>
                                            </label>
                                            <?php if ($data['statusPeminjaman'] == 'Sedang dipinjam') : ?>
                                                <input type="file" class="form-control" id="dokSesudah" name="dokSesudah" accept="image/*">
                                            <?php else : ?>
                                                <div class="mt-1">
                                                    <?php if (!empty($data['dokumentasiSesudah'])) : ?>
                                                        <a href="<?= BASE_URL ?>/uploads/dokumentasi/<?= htmlspecialchars($data['dokumentasiSesudah']) ?>" target="_blank">Lihat Dokumentasi</a>
                                                    <?php else : ?>
                                                        <span class="text-danger"><em>(Tidak Diupload)</em></span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php" class="btn btn-secondary me-2">Kembali</a>
                                <?php if ($data['statusPeminjaman'] == 'Sedang dipinjam') : ?>
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
include '../../templates/footer.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formDetail');
        if (form) {
            form.addEventListener('submit', function(event) {
                let isValid = true;
                const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.heic)$/i;

                const validateFile = (inputId, errorId) => {
                    const fileInput = document.getElementById(inputId);
                    const errorSpan = document.getElementById(errorId);

                    if (fileInput) {
                        errorSpan.textContent = '';
                        if (fileInput.files.length === 0) {
                            errorSpan.textContent = 'File wajib diupload.';
                            isValid = false;
                        } else if (!allowedExtensions.exec(fileInput.value)) {
                            errorSpan.textContent = 'Format file harus JPG, PNG, GIF, atau HEIC.';
                            fileInput.value = '';
                            isValid = false;
                        }
                    }
                };

                validateFile('dokSebelum', 'dokSebelumError');
                validateFile('dokSesudah', 'dokSesudahError');

                if (!isValid) {
                    event.preventDefault();
                }
            });
        }
    });
</script>