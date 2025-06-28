<?php
include '../../templates/header.php';
require_once __DIR__ . '/../../function/notification_helper.php';

$idPeminjamanBrg = $_GET['id'] ?? '';
$data = [];

$showRejectedModal = false;
$showModal = false;
$error = '';
$alasanPenolakan = '';
$showAlasanPenolakan = false;

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
            // Tambahkan notifikasi (tanpa duplikat)
            add_notif_once("Peminjaman Barang $idPeminjamanBrg berhasil disetujui.");
            header("Location: peminjamanBarang.php?notif=approve_success");
            exit;
        } else {
            $error = "Gagal menyetujui peminjaman barang.";
            exit;
        }
    } elseif (isset($_POST['tolak_submit'])) {
        // Tolak peminjaman (submit alasan penolakan)
        $alasanPenolakan = trim($_POST['alasanPenolakan'] ?? '');
        $showAlasanPenolakan = true;
        if ($alasanPenolakan === '') {
            $error = "Alasan penolakan harus diisi.";
            $showRejectedModal = true;
        } else {
            // Update status dan alasan penolakan di Peminjaman_Barang
            $query = "UPDATE Peminjaman_Barang 
                       SET statusPeminjaman = 'Ditolak'
                       WHERE idPeminjamanBrg = ?";
            $params = array($idPeminjamanBrg);
            $stmt = sqlsrv_query($conn, $query, $params);

            // Simpan alasan penolakan ke tabel Penolakan
            $queryPenolakan = "INSERT INTO Penolakan (idPeminjamanBrg, alasanPenolakan) VALUES (?, ?)";
            $paramsPenolakan = array($idPeminjamanBrg, $alasanPenolakan);
            $stmtPenolakan = sqlsrv_query($conn, $queryPenolakan, $paramsPenolakan);

            if ($stmt && $stmtPenolakan) {
                // Tambahkan notifikasi (tanpa duplikat)
                add_notif_once("Peminjaman Barang $idPeminjamanBrg ditolak.");
                header("Location: peminjamanBarang.php?notif=reject_success");
                exit;
            } else {
                $error = "Gagal menolak pengajuan barang.";
            }
        }
    } elseif (isset($_POST['tolak'])) {
        // Klik tombol tolak, tampilkan kolom alasan penolakan
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
                        <form method="POST" id="formPengajuanBarang">
                            <div class="row">
                                <!-- Kolom Kiri -->
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
                                        <textarea class="form-control w-100" id="alasanPeminjamanBrg" name="alasanPeminjamanBrg" hidden rows="3" style="background: #f5f5f5;"><?= htmlspecialchars($alasanPeminjamanBrg) ?></textarea>
                                    </div>
                                </div>
                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idPeminjamanBrg" class="form-label fw-bold">ID Peminjaman Barang</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($idPeminjamanBrg) ?></div>
                                        <input type="hidden" class="form-control" id="idPeminjamanBrg" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label for="namaBarang" class="form-label fw-bold">Nama Barang</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($namaBarang) ?></div>
                                        <input type="hidden" class="form-control" id="namaBarang" name="namaBarang" value="<?= htmlspecialchars($namaBarang) ?>">
                                    </div>
                                    <div class="mb-2">
                                    </div>
                                    <div class="mb-2">
                                        <label for="jumlahBrg" class="form-label fw-bold">Jumlah Barang</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($jumlahBrg) ?></div>
                                        <input type="hidden" class="form-control" id="jumlahBrg" name="jumlahBrg" value="<?= htmlspecialchars($jumlahBrg) ?>">
                                    </div>
                                </div>
                                <div class="mb-2" id="alasanPenolakanGroup" style="<?= $showAlasanPenolakan ? '' : 'display:none;' ?>">
                                    <label for="alasanPenolakan" class="form-label fw-bold">Alasan Penolakan</label>
                                    <textarea class="form-control" id="alasanPenolakan" name="alasanPenolakan" rows="3" placeholder="Isi alasan penolakan jika ingin menolak" style="background: #f5f5f5;"><?= htmlspecialchars($alasanPenolakan) ?></textarea>
                                    <div class="form-text text-danger" id="alasanPenolakanError" style="display: none;">Alasan penolakan harus diisi jika menolak.</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <div class="d-flex justify-content-between w-100 gap-2">
                                    <div>
                                        <a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php" class="btn btn-secondary">Kembali</a>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <?php if (!$showAlasanPenolakan): ?>
                                            <button type="submit" name="tolak" class="btn btn-danger" id="btnTolak">Tolak</button>
                                        <?php else: ?>
                                            <button type="submit" name="tolak_submit" class="btn btn-danger" onclick="return validateTolak();" id="btnSubmitPenolakan">Submit Penolakan</button>
                                        <?php endif; ?>
                                        <?php if (!$showAlasanPenolakan): ?>
                                            <button type="submit" name="setuju" class="btn btn-primary" id="btnSetuju">Setuju</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <script>
                            // Tampilkan kolom alasan penolakan jika tombol tolak diklik (tanpa reload)
                            document.addEventListener('DOMContentLoaded', function() {
                                var btnTolak = document.getElementById('btnTolak');
                                if (btnTolak) {
                                    btnTolak.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        document.getElementById('alasanPenolakanGroup').style.display = '';
                                        btnTolak.style.display = 'none';
                                            var btnSetuju = document.getElementById('btnSetuju');
                                            if (btnSetuju) btnSetuju.style.display = 'none';
                                        if (!document.getElementById('btnSubmitPenolakan')) {
                                            var submitBtn = document.createElement('button');
                                            submitBtn.type = 'submit';
                                            submitBtn.name = 'tolak_submit';
                                            submitBtn.className = 'btn btn-danger';
                                            submitBtn.id = 'btnSubmitPenolakan';
                                            submitBtn.innerText = 'Submit Penolakan';
                                            submitBtn.onclick = function() {
                                                return validateTolak();
                                            };
                                            btnTolak.parentNode.insertBefore(submitBtn, btnTolak);
                                        }
                                        document.getElementById('alasanPenolakan').focus();
                                    });
                                }
                            });

                            function validateTolak() {
                                var alasan = document.getElementById('alasanPenolakan').value.trim();
                                var errorDiv = document.getElementById('alasanPenolakanError');
                                if (alasan === '') {
                                    errorDiv.style.display = 'block';
                                    document.getElementById('alasanPenolakan').focus();
                                    return false;
                                } else {
                                    errorDiv.style.display = 'none';
                                    return true;
                                }
                            }
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