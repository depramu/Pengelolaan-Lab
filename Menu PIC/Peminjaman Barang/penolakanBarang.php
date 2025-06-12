<?php
include '../../template/header.php';

// Perbaikan: Gunakan parameter GET untuk menerima ID
$idPeminjamanBrg = $_GET['id'] ?? '';
$alasanPenolakan = $_POST['alasanPenolakan'] ?? '';
$showModal = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($idPeminjamanBrg) && !empty($alasanPenolakan)) {
        // 1. Update status di Peminjaman_Barang
        $updateQuery = "UPDATE Peminjaman_Barang 
                       SET statusPeminjaman = 'Ditolak'
                       WHERE idPeminjamanBrg = ?";
        $updateParams = array($idPeminjamanBrg);
        $updateStmt = sqlsrv_query($conn, $updateQuery, $updateParams);

        // 2. Simpan alasan penolakan ke tabel Penolakan
        $insertQuery = "INSERT INTO Penolakan (idPeminjamanBrg, alasanPenolakan)
                       VALUES (?, ?)";
        $insertParams = array($idPeminjamanBrg, $alasanPenolakan);
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
include '../../template/sidebar.php';
?>

            <main class="col bg-white px-4 py-3 position-relative">
                <div class="mb-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                            <li class="breadcrumb-item"><a href="peminjamanBarang.php">Peminjaman Barang</a></li>
                            <li class="breadcrumb-item"><a href="pengajuanBarang.php">Pengajuan Barang</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Penolakan Barang</li>
                        </ol>
                    </nav>
                </div>


                <!-- Penolakan -->
                <div class="container mt-4">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-12 " style="margin-right: 20px;">
                            <div class="card border border-dark">
                                <div class="card-header bg-white border-bottom border-dark">
                                    <span class="fw-semibold">Penolakan Peminjaman Barang</span>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-2">
                                            <label class="form-label">ID Peminjaman Barang</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($idPeminjamanBrg) ?>" disabled>
                                            <input type="hidden" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">
                                        </div>

                                        <div class="mb-2">
                                            <label for="alasanPenolakan" class="form-label">Alasan Penolakan
                                                <span id="alasanError" class="text-danger ms-2" style="font-size:0.95em;display:none;">*Harus Diisi</span>
                                            </label>
                                            <textarea class="form-control" id="alasanPenolakan" name="alasanPenolakan" rows="3" style="resize: none;"></textarea>
                                        </div>
                                        <div class="d-flex justify-content-between mt-4">
                                            <a href="pengajuanBarang.php?id=<?= htmlspecialchars($idPeminjamanBrg) ?>" class="btn btn-secondary">Kembali</a>
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
     
<?php include '../../template/footer.php'; ?>