<?php
include '../../templates/header.php';

// Perbaikan: Gunakan parameter GET untuk menerima ID
$idPeminjamanRuangan = $_GET['id'] ?? '';
$alasanPenolakan = $_POST['alasanPenolakan'] ?? '';
$showModal = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($idPeminjamanRuangan) && !empty($alasanPenolakan)) {
        // 1. Update status di Peminjaman_Ruangan
        $updateQuery = "UPDATE Peminjaman_Ruangan 
                       SET statusPeminjaman = 'Ditolak'
                       WHERE idPeminjamanRuangan = ?";
        $updateParams = array($idPeminjamanRuangan);
        $updateStmt = sqlsrv_query($conn, $updateQuery, $updateParams);

        // 2. Simpan alasan penolakan ke tabel Penolakan
        $insertQuery = "INSERT INTO Penolakan (idPeminjamanRuangan, alasanPenolakan)
                       VALUES (?, ?)";
        $insertParams = array($idPeminjamanRuangan, $alasanPenolakan);
        $insertStmt = sqlsrv_query($conn, $insertQuery, $insertParams);

        if ($updateStmt && $insertStmt) {
            $showModal = true;
        } else {
            $error = "Gagal menambahkan Ruangan.";
        }
    } else {
        $error = "Form tidak boleh kosong.";
    }
}
include '../../templates/sidebar.php';
?>
            <!-- Content Area -->

            <main class="col bg-white px-4 py-3 position-relative">
                <div class="mb-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php">Peminjaman Ruangan</a></li>
                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Ruangan/pengajuanRuangan.php">Pengajuan Ruangan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Penolakan Ruangan</li>
                        </ol>
                    </nav>
                </div>


                <!-- Penolakan -->
                <div class="container mt-4">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-12 " style="margin-right: 20px;">
                            <div class="card border border-dark">
                                <div class="card-header bg-white border-bottom border-dark">
                                    <span class="fw-semibold">Penolakan Peminjaman Ruangan</span>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-2">
                                            <label class="form-label">ID Peminjaman Ruangan</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($idPeminjamanRuangan) ?>" disabled>
                                            <input type="hidden" name="idPeminjamanRuangan" value="<?= htmlspecialchars($idPeminjamanRuangan) ?>">
                                        </div>

                                        <div class="mb-2">
                                            <label for="alasanPenolakan" class="form-label">Alasan Penolakan
                                                <span id="alasanError" class="text-danger ms-2" style="font-size:0.95em;display:none;">*Harus Diisi</span>
                                            </label>
                                            <textarea class="form-control" id="alasanPenolakan" name="alasanPenolakan" rows="3" style="resize: none;"></textarea>
                                        </div>
                                        <div class="d-flex justify-content-between mt-4">
                                            <a href="pengajuanRuangan.php?id=<?= htmlspecialchars($idPeminjamanRuangan) ?>" class="btn btn-secondary">Kembali</a>
                                            <button type="submit" class="btn btn-primary">Kirim</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>


        <script>
            document.querySelector('form').addEventListener('submit', function(e) {
                const alasanField = document.getElementById('alasanPenolakan');
                const errorField = document.getElementById('alasanError');
                let valid = true;

                if (alasanField.value.trim() === '') {
                    errorField.textContent = '*Harus Diisi';
                    errorField.style.display = 'inline';
                    valid = false;
                } else {
                    errorField.textContent = '';
                    errorField.style.display = 'none';
                }

                if (!valid) {
                    e.preventDefault(); // Cegah form terkirim kalau tidak valid
                }
            });
        </script>

<?php include '../../templates/footer.php'; ?>