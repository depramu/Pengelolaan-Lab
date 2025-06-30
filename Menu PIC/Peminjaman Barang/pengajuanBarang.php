<?php
require_once __DIR__ . '/../../auth.php';
authorize_role('PIC Aset');
include '../../templates/header.php';

$idPeminjamanBrg = $_GET['id'] ?? '';
$data = [];

$showRejectedModal = false;
$showModal = false;
$error = '';
$alasanPenolakan = '';
$showAlasanPenolakan = false; // Default: sembunyikan

if (!empty($idPeminjamanBrg)) {
    $_SESSION['idPeminjamanBrg'] = $idPeminjamanBrg;

    // Ambil data peminjaman beserta nama peminjam (Mahasiswa/Karyawan) dan info nim/npk
    $query = "SELECT
                pb.*,
                b.namaBarang,
                COALESCE(m.nama, k.nama) AS namaPeminjam
            FROM
                Peminjaman_Barang pb
            JOIN
                Barang b ON pb.idBarang = b.idBarang
            LEFT JOIN
                Mahasiswa m ON pb.nim = m.nim
            LEFT JOIN
                Karyawan k ON pb.npk = k.npk
            WHERE
                pb.idPeminjamanBrg = ?";
    $params = array($idPeminjamanBrg);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt && sqlsrv_has_rows($stmt)) {
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        // Jika status sudah Ditolak, tampilkan alasan penolakan yang tersimpan
        if (($data['statusPeminjaman'] ?? '') === 'Ditolak') {
            $showAlasanPenolakan = true;
            $alasanPenolakan = $data['alasanPenolakan'] ?? '';
        }
    }
}

// Proses form
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($idPeminjamanBrg)) {
    if (isset($_POST['setuju'])) {
        // Setujui peminjaman
        $query = "UPDATE Peminjaman_Barang
                    SET statusPeminjaman = 'Sedang Dipinjam'
                    WHERE idPeminjamanBrg = ?";
        $params = array($idPeminjamanBrg);
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt) {
            $showModal = true;
        } else {
            $error = "Gagal menyetujui peminjaman barang.";
            // Tampilkan error
        }
    } elseif (isset($_POST['tolak_submit'])) {
        // Tolak peminjaman (submit alasan penolakan)
        $alasanPenolakan = trim($_POST['alasanPenolakan'] ?? '');
        $showAlasanPenolakan = true; // Agar field tetap terlihat jika ada error
        if ($alasanPenolakan === '') {
            $error = "Alasan penolakan harus diisi.";
            // $showRejectedModal = true; // Ini mungkin tidak perlu jika validasi di client-side sudah bekerja
        } else {
            // Update status dan alasan penolakan di Peminjaman_Barang
            $query = "UPDATE Peminjaman_Barang
                        SET statusPeminjaman = 'Ditolak'
                        WHERE idPeminjamanBrg = ?";
            $params = array($idPeminjamanBrg);
            $stmt = sqlsrv_query($conn, $query, $params);

            // Simpan alasan penolakan ke tabel Penolakan (pastikan tabel Penolakan ada dan strukturnya sesuai)
            $queryPenolakan = "INSERT INTO Penolakan (idPeminjamanBrg, alasanPenolakan) VALUES (?, ?)"; // Tambahkan tglPenolakan
            $paramsPenolakan = array($idPeminjamanBrg, $alasanPenolakan);
            $stmtPenolakan = sqlsrv_query($conn, $queryPenolakan, $paramsPenolakan);

            if ($stmt && $stmtPenolakan) {
                $showModal = true;
            } else {
                $error = "Gagal menolak pengajuan barang.";
                // Tampilkan error
            }
        }
    } elseif (isset($_POST['tolak'])) {
        // Klik tombol tolak, tampilkan kolom alasan penolakan (ini harusnya tidak akan terjadi karena kita akan pakai JS)
        // Ini hanya sebagai fallback jika JS tidak bekerja
        $showAlasanPenolakan = true;
    }
}

$idBarang = $data['idBarang'] ?? '';
$nim = $data['nim'] ?? '';
$npk = $data['npk'] ?? '';
$namaBarang = $data['namaBarang'] ?? '';
$namaPeminjam = $data['namaPeminjam'] ?? '';
$tglPeminjamanBrg = isset($data['tglPeminjamanBrg']) ? $data['tglPeminjamanBrg']->format('Y-m-d') : '';
$jumlahBrg = $data['jumlahBrg'] ?? '';
$alasanPeminjamanBrg = $data['alasanPeminjamanBrg'] ?? '';
$statusPeminjaman = $data['statusPeminjaman'] ?? ''; // Ambil status peminjaman untuk disable tombol

include '../../templates/sidebar.php';
?>
<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Barang</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php">Peminjaman Barang</a></li>
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
                        <?php if ($error) : ?>
                            <div class="alert alert-danger" role="alert">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" id="formPengajuanBarang">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idBarang" class="form-label fw-bold">ID Barang</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($idBarang) ?></div>
                                        <input type="hidden" class="form-control" id="idBarang" name="idBarang" value="<?= htmlspecialchars($idBarang) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="tglPeminjamanBrg" class="form-label fw-bold">Tanggal Peminjaman</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($tglPeminjamanBrg) ?></div>
                                        <input type="hidden" class="form-control" id="tglPeminjamanBrg" name="tglPeminjamanBrg" value="<?= htmlspecialchars($tglPeminjamanBrg) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">NIM / NPK</label>
                                        <div class="form-control-plaintext">
                                            <?php
                                            if (!empty($nim)) {
                                                echo htmlspecialchars($nim);
                                            } elseif (!empty($npk)) {
                                                echo htmlspecialchars($npk);
                                            } else {
                                                echo "-";
                                            }
                                            ?>
                                        </div>
                                        <input type="hidden" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($nim) ?>">
                                        <input type="hidden" class="form-control" id="npk" name="npk" value="<?= htmlspecialchars($npk) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="alasanPeminjamanBrg" class="form-label fw-bold">Alasan Peminjaman</label>
                                        <div class="form-control-plaintext"><?= nl2br(htmlspecialchars($alasanPeminjamanBrg)) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idPeminjamanBrg" class="form-label fw-bold">ID Peminjaman</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($idPeminjamanBrg) ?></div>
                                        <input type="hidden" class="form-control" id="idPeminjamanBrg" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="namaBarang" class="form-label fw-bold">Nama Barang</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($namaBarang) ?></div>
                                        <input type="hidden" class="form-control" id="namaBarang" name="namaBarang" value="<?= htmlspecialchars($namaBarang) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="namaPeminjam" class="form-label fw-bold">Nama Peminjam</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($namaPeminjam) ?></div>
                                    </div>
                                    <div class="mb-2">
                                        <label for="jumlahBrg" class="form-label fw-bold">Jumlah Barang</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($jumlahBrg) ?></div>
                                        <input type="hidden" class="form-control" id="jumlahBrg" name="jumlahBrg" value="<?= htmlspecialchars($jumlahBrg) ?>">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-2" id="alasanPenolakanGroup" style="<?= $showAlasanPenolakan ? '' : 'display:none;' ?>">
                                        <label for="alasanPenolakan" class="form-label fw-bold">Alasan Penolakan</label>
                                        <?php if ($statusPeminjaman === 'Ditolak') : ?>
                                            <div class="form-control-plaintext border p-2 bg-light rounded"><?= nl2br(htmlspecialchars($alasanPenolakan)) ?></div>
                                        <?php else : ?>
                                            <textarea class="form-control" id="alasanPenolakan" name="alasanPenolakan" rows="3" placeholder="Masukkan alasan penolakan jika ingin menolak.."><?= htmlspecialchars($alasanPenolakan) ?></textarea>
                                        <?php endif; ?>
                                        <div class="form-text text-danger" id="alasanPenolakanError" style="display: none;">Alasan penolakan harus diisi jika menolak.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <div class="d-flex justify-content-between w-100 gap-2">
                                    <div>
                                        <a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php" class="btn btn-secondary">Kembali</a>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <?php if ($statusPeminjaman === 'Menunggu Persetujuan') : ?>
                                            <button type="button" class="btn btn-danger" id="btnTolakShowField">Tolak</button>
                                            <button type="submit" name="tolak_submit" class="btn btn-danger" id="btnTolakSubmit" style="display:none;">Tolak</button>
                                            <button type="submit" name="setuju" class="btn btn-primary">Setuju</button>
                                        <?php else : ?>
                                            <button type="button" class="btn btn-danger">Tolak</button>
                                            <button type="button" class="btn btn-primary">Setuju</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var btnTolakShowField = document.getElementById('btnTolakShowField');
                                var btnTolakSubmit = document.getElementById('btnTolakSubmit');
                                var alasanPenolakanGroup = document.getElementById('alasanPenolakanGroup');
                                var alasanPenolakanField = document.getElementById('alasanPenolakan');
                                var alasanPenolakanError = document.getElementById('alasanPenolakanError');

                                // Logika untuk menampilkan/menyembunyikan field alasan penolakan
                                if (btnTolakShowField) {
                                    btnTolakShowField.addEventListener('click', function(e) {
                                        e.preventDefault(); // Mencegah form submit saat hanya ingin menampilkan field

                                        alasanPenolakanGroup.style.display = ''; // Tampilkan grup
                                        btnTolakShowField.style.display = 'none'; // Sembunyikan tombol "Tolak"
                                        btnTolakSubmit.style.display = ''; // Tampilkan tombol "Submit Penolakan"
                                        alasanPenolakanField.focus(); // Fokus ke field
                                    });
                                }

                                // Validasi saat tombol "Submit Penolakan" diklik
                                if (btnTolakSubmit) {
                                    btnTolakSubmit.addEventListener('click', function(e) {
                                        var alasan = alasanPenolakanField.value.trim();
                                        if (alasan === '') {
                                            e.preventDefault(); // Mencegah submit jika alasan kosong
                                            alasanPenolakanError.style.display = 'block'; // Tampilkan pesan error
                                            alasanPenolakanField.focus();
                                        } else {
                                            alasanPenolakanError.style.display = 'none'; // Sembunyikan pesan error
                                            // Form akan disubmit secara otomatis karena ini adalah tombol type="submit"
                                        }
                                    });
                                }

                                // Jika ada error dari server dan field alasan penolakan seharusnya tampil
                                <?php if ($showAlasanPenolakan && $error) : ?>
                                    alasanPenolakanGroup.style.display = '';
                                    if (btnTolakShowField) btnTolakShowField.style.display = 'none';
                                    if (btnTolakSubmit) btnTolakSubmit.style.display = '';
                                    <?php if ($error === "Alasan penolakan harus diisi.") : ?>
                                        alasanPenolakanError.style.display = 'block';
                                        alasanPenolakanField.focus();
                                    <?php endif; ?>
                                <?php endif; ?>

                                // Disable buttons if status is not 'Menunggu Persetujuan'
                                var statusPeminjaman = "<?= htmlspecialchars($statusPeminjaman) ?>";
                                if (statusPeminjaman !== 'Menunggu Persetujuan') {
                                    var setujuBtn = document.querySelector('button[name="setuju"]');
                                    var tolakShowBtn = document.getElementById('btnTolakShowField');
                                    var tolakSubmitBtn = document.getElementById('btnTolakSubmit');

                                    if (setujuBtn) setujuBtn.disabled = true;
                                    if (tolakShowBtn) tolakShowBtn.disabled = true;
                                    if (tolakSubmitBtn) tolakSubmitBtn.disabled = true;

                                    // If already rejected, show the rejection reason and disable its input
                                    if (statusPeminjaman === 'Ditolak') {
                                        alasanPenolakanGroup.style.display = ''; // Make sure it's visible
                                        // The PHP logic already makes it plain text, no need to hide input
                                    }
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include '../../templates/footer.php';
?>