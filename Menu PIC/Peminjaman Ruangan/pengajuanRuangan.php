<?php
include '../../templates/header.php';

$idPeminjamanRuangan = $_GET['id'] ?? '';
$data = null;

$showRejectedModal = false;
$showModal = false;
$error = '';
$alasanPenolakan = '';
$showAlasanPenolakan = false; // Untuk kontrol tampilan kolom alasan penolakan

// Ambil data peminjaman beserta nama peminjam (Mahasiswa/Karyawan) dan info nim/npk
if (!empty($idPeminjamanRuangan)) {
    $_SESSION['idPeminjamanRuangan'] = $idPeminjamanRuangan;

    $query = "SELECT 
                p.idPeminjamanRuangan, p.idRuangan, p.nim, p.npk,
                p.tglPeminjamanRuangan, p.waktuMulai, p.waktuSelesai,
                p.alasanPeminjamanRuangan, p.statusPeminjaman,
                COALESCE(m.nama, k.nama) AS namaPeminjam
            FROM 
                Peminjaman_Ruangan p
            LEFT JOIN 
                Mahasiswa m ON p.nim = m.nim
            LEFT JOIN 
                Karyawan k ON p.npk = k.npk
            WHERE 
                p.idPeminjamanRuangan = ?";
    $params = array($idPeminjamanRuangan);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt && sqlsrv_has_rows($stmt)) {
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }
}

// Proses form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['setuju'])) {
        // Setujui peminjaman
        $query = "UPDATE Peminjaman_Ruangan 
                  SET statusPeminjaman = 'Sedang Dipinjam'
                  WHERE idPeminjamanRuangan = ?";
        $params = array($idPeminjamanRuangan);
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt) {
            $showModal = true;
        } else {
            $error = "Gagal melakukan pengajuan ruangan.";
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
            // Update status dan alasan penolakan di Peminjaman_Ruangan
            $query = "UPDATE Peminjaman_Ruangan 
                      SET statusPeminjaman = 'Ditolak', alasanPenolakan = ?
                      WHERE idPeminjamanRuangan = ?";
            $params = array($alasanPenolakan, $idPeminjamanRuangan);
            $stmt = sqlsrv_query($conn, $query, $params);

            // Simpan alasan penolakan ke tabel Penolakan
            $queryPenolakan = "INSERT INTO Penolakan (idPeminjamanRuangan, alasanPenolakan) VALUES (?, ?)";
            $paramsPenolakan = array($idPeminjamanRuangan, $alasanPenolakan);
            $stmtPenolakan = sqlsrv_query($conn, $queryPenolakan, $paramsPenolakan);

            if ($stmt && $stmtPenolakan) {
                $showModal = true;
            } else {
                $error = "Gagal menolak pengajuan ruangan.";
            }
        }
    } elseif (isset($_POST['tolak'])) {
        // Klik tombol tolak, tampilkan kolom alasan penolakan
        $showAlasanPenolakan = true;
    }
}
include '../../templates/sidebar.php';
?>
<!-- Content Area -->
<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Ruangan</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php">Peminjaman Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengajuan Ruangan</li>
            </ol>
        </nav>
    </div>

    <!-- Pengajuan Peminjaman Ruangan -->
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-semibold">Pengajuan Peminjaman Ruangan</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="formPengajuan">
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idRuangan" class="form-label fw-bold">ID Ruangan</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($data['idRuangan'] ?? '') ?></div>
                                        <input type="hidden" class="form-control" id="idRuangan" name="idRuangan" value="<?= htmlspecialchars($data['idRuangan'] ?? '') ?>" style="background: #f5f5f5;">
                                    </div>
                                    <div class="mb-2">
                                        <label for="tglPeminjamanRuangan" class="form-label fw-bold">Tanggal Peminjaman</label>
                                        <div class="form-control-plaintext">
                                            <?php
                                            if (isset($data['tglPeminjamanRuangan']) && $data['tglPeminjamanRuangan'] instanceof DateTime) {
                                                echo htmlspecialchars($data['tglPeminjamanRuangan']->format('Y-m-d'));
                                            } else {
                                                echo '';
                                            }
                                            ?>
                                        </div>
                                        <input type="hidden" class="form-control" id="tglPeminjamanRuangan" name="tglPeminjamanRuangan" value="<?php
                                            if (isset($data['tglPeminjamanRuangan']) && $data['tglPeminjamanRuangan'] instanceof DateTime) {
                                                echo htmlspecialchars($data['tglPeminjamanRuangan']->format('Y-m-d'));
                                            } else {
                                                echo '';
                                            }
                                        ?>" style="background: #f5f5f5;">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">NIM / NPK</label>
                                        <div class="form-control-plaintext">
                                            <?php
                                            if (!empty($data['nim'])) {
                                                echo htmlspecialchars($data['nim']);
                                            } elseif (!empty($data['npk'])) {
                                                echo htmlspecialchars($data['npk']);
                                            } else {
                                                echo "-";
                                            }
                                            ?>
                                        </div>
                                        <input type="hidden" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($data['nim'] ?? '') ?>">
                                        <input type="hidden" class="form-control" id="npk" name="npk" value="<?= htmlspecialchars($data['npk'] ?? '') ?>">
                                    </div>
                                </div>
                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idPeminjamanRuangan" class="form-label fw-bold">ID Peminjaman</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($idPeminjamanRuangan) ?></div>
                                        <input type="hidden" class="form-control" id="idPeminjamanRuangan" value="<?= htmlspecialchars($idPeminjamanRuangan) ?>" style="background: #f5f5f5;">
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label for="waktuMulai" class="form-label fw-bold">Waktu Mulai</label>
                                            <div class="form-control-plaintext">
                                                <?php
                                                if (isset($data['waktuMulai']) && $data['waktuMulai'] instanceof DateTime) {
                                                    echo htmlspecialchars($data['waktuMulai']->format('H:i'));
                                                } else {
                                                    echo '';
                                                }
                                                ?>
                                            </div>
                                            <input type="hidden" class="form-control" id="waktuMulai" name="waktuMulai" value="<?php
                                                if (isset($data['waktuMulai']) && $data['waktuMulai'] instanceof DateTime) {
                                                    echo htmlspecialchars($data['waktuMulai']->format('H:i'));
                                                } else {
                                                    echo '';
                                                }
                                            ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="waktuSelesai" class="form-label fw-bold">Waktu Selesai</label>
                                            <div class="form-control-plaintext">
                                                <?php
                                                if (isset($data['waktuSelesai']) && $data['waktuSelesai'] instanceof DateTime) {
                                                    echo htmlspecialchars($data['waktuSelesai']->format('H:i'));
                                                } else {
                                                    echo '';
                                                }
                                                ?>
                                            </div>
                                            <input type="hidden" class="form-control" id="waktuSelesai" name="waktuSelesai" value="<?php
                                                if (isset($data['waktuSelesai']) && $data['waktuSelesai'] instanceof DateTime) {
                                                    echo htmlspecialchars($data['waktuSelesai']->format('H:i'));
                                                } else {
                                                    echo '';
                                                }
                                            ?>">
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label for="alasanPeminjaman" class="form-label fw-bold">Alasan Peminjaman</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($data['alasanPeminjamanRuangan'] ?? '') ?></div>
                                        <textarea class="form-control w-100" id="alasanPeminjaman" name="alasanPeminjaman" hidden rows="3" style="background: #f5f5f5;"><?= htmlspecialchars($data['alasanPeminjamanRuangan'] ?? '') ?></textarea>
                                    </div>
                                </div>

                                <div class="mb-2" id="alasanPenolakanGroup" style="<?= $showAlasanPenolakan ? '' : 'display:none;' ?>">
                                    <label for="alasanPenolakan" class="form-label fw-bold">Alasan Penolakan</label>
                                    <textarea class="form-control" id="alasanPenolakan" name="alasanPenolakan" rows="3" placeholder="Masukkan alasan penolakan jika ingin menolak.."><?= htmlspecialchars($alasanPenolakan) ?></textarea>
                                    <div class="form-text text-danger" id="alasanPenolakanError" style="display: none;">Alasan penolakan harus diisi jika menolak.</div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between gap-2 mt-4">
                                <div class="align-self-start">
                                    <a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php" class="btn btn-secondary">Kembali</a>
                                </div>
                                <div class="d-flex justify-content-end gap-2">
                                    <?php if (!$showAlasanPenolakan): ?>
                                        <button type="submit" name="tolak" class="btn btn-danger" id="btnTolak">Tolak</button>
                                    <?php else: ?>
                                        <button type="submit" name="tolak_submit" class="btn btn-danger" onclick="return validateTolak();">Tolak</button>
                                    <?php endif; ?>
                                    <button type="submit" name="setuju" class="btn btn-primary">Setuju</button>
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
                                        // Ganti tombol Tolak menjadi Submit Penolakan
                                        btnTolak.style.display = 'none';
                                        // Tambahkan tombol submit penolakan secara dinamis
                                        if (!document.getElementById('btnSubmitPenolakan')) {
                                            var submitBtn = document.createElement('button');
                                            submitBtn.type = 'submit';
                                            submitBtn.name = 'tolak_submit';
                                            submitBtn.className = 'btn btn-danger';
                                            submitBtn.id = 'btnSubmitPenolakan';
                                            submitBtn.innerText = 'Tolak';
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

<?php include '../../templates/footer.php'; ?>