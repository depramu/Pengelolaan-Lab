<?php
$showSuccessModal = false;
if (isset($_GET['upload']) && $_GET['upload'] == 'sukses') {
    $showSuccessModal = true;
}
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
                COALESCE(m.nama, k.nama) AS namaPeminjam
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
<h3 class="fw-semibold mb-3">Riwayat Peminjaman Ruangan</h3>
    <div class="mb-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php">Riwayat Peminjaman Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Peminjaman Ruangan</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-semibold">Detail Peminjaman Ruangan</span>
                    </div>
                    <div class="card-body">
                        <?php if ($error_message) : ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $error_message ?>
                            </div>
                        <?php elseif ($data) : ?>
                            <form id="formDetail" action="proses_pengembalian.php" method="POST" enctype="multipart/form-data">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">ID Peminjaman</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['idPeminjamanRuangan']) ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['idPeminjamanRuangan']) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">NIM / NPK</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['nim'] ?? $data['npk'] ?? '-') ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['nim'] ?? $data['npk'] ?? '-') ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Ruangan</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['idRuangan']) ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['idRuangan']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Tanggal Peminjaman</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['tglPeminjamanRuangan'] instanceof DateTime)  ? $data['tglPeminjamanRuangan']->format('d F Y') : '' ?></div>
                                            <input type="hidden" class="form-control" value="<?= ($data['tglPeminjamanRuangan'] instanceof DateTime) ? $data['tglPeminjamanRuangan']->format('d F Y') : '' ?>">
                                        </div>
                                        <div class="mb-3">
                                            <div class="row">
                                                <div class="col-6"> <label class="form-label fw-bold">Waktu Mulai:</label> <p class="form-control-plaintext"><?= htmlspecialchars($data['waktuMulai'] instanceof DateTime ? $data['waktuMulai']->format('H:i') : '') ?></p></div>
                                                <div class="col-6"> <label class="form-label fw-bold">Waktu Selesai:</label> <p class="form-control-plaintext"><?= htmlspecialchars($data['waktuSelesai'] instanceof DateTime ? $data['waktuSelesai']->format('H:i') : '') ?></p></div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Status Peminjaman</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['statusPeminjaman']) ?></div>
                                            <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['statusPeminjaman']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Alasan Peminjaman</label>
                                            <div class="form-control-plaintext"><?= htmlspecialchars($data['alasanPeminjamanRuangan']) ?></div>
                                            <textarea class="form-control" rows="3" hidden><?= htmlspecialchars($data['alasanPeminjamanRuangan']) ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <?php if (in_array($data['statusPeminjaman'], ['Ditolak', 'Sedang dipinjam', 'Menunggu Pengecekan', 'Selesai'])) : ?>
                                    <hr>
                                    <?php if ($data['statusPeminjaman'] == 'Ditolak') : ?>
                                        <h6 class=" mb-3">DETAIL PENOLAKAN</h6>
                                        <div class="mt-3">
                                            <label class="form-label fw-bold">Alasan Penolakan dari PIC</label>
                                            <textarea class="form-control" rows="3"><?= htmlspecialchars($data['alasanPenolakan'] ?? 'Tidak ada alasan spesifik.') ?></textarea>
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

                                <div class="d-flex justify-content-between mt-3">
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
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="successModalLabel">Berhasil</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Dokumentasi berhasil terkirim
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
</main>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formDetail');
        if (form) {
            form.addEventListener('submit', function(event) {
                let isValid = true;
                const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.heif|\.heic)$/i;

                const validateFile = (inputId, errorId) => {
                    const fileInput = document.getElementById(inputId);
                    const errorSpan = document.getElementById(errorId);

                    if (fileInput) {
                        errorSpan.textContent = '';
                        if (fileInput.files.length === 0) {
                            errorSpan.textContent = 'File wajib diupload.';
                            isValid = false;
                        } else if (!allowedExtensions.exec(fileInput.value)) {
                            errorSpan.textContent = 'Format file harus JPG, JPEG, PNG, HEIF, atau HEIC.';
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
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($showSuccessModal) : ?>
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        <?php endif; ?>
    });
</script>


<?php
include '../../templates/footer.php';
?>