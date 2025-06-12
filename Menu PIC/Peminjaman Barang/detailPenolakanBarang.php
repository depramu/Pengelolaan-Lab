<?php
include '../../template/header.php';

// Perbaikan: Gunakan parameter GET untuk menerima ID
$idPeminjamanBrg = $_GET['id'] ?? '';
$query = "SELECT * FROM Penolakan WHERE idPeminjamanBrg = ?";
$params = array($idPeminjamanBrg);
$stmt = sqlsrv_query($conn, $query, $params);
$alasanPenolakan = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)['alasanPenolakan'] ?? '';

$currentPage = basename($_SERVER['PHP_SELF']);
$peminjamanPages = ['peminjamanBarang.php', 'peminjamanRuangan.php', 'detailPenolakanRuangan.php'];
$isPeminjamanActive = in_array($currentPage, $peminjamanPages);

include '../../template/sidebar.php';
?>

            <main class="col bg-white px-4 py-3 position-relative">
                <div class="mb-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                            <li class="breadcrumb-item"><a href="peminjamanBarang.php">Peminjaman Barang</a></li>
                            <li class="breadcrumb-item"><a href="pengajuanBrg.php">Pengajuan Barang</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detail Penolakan Barang</li>
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
                                    <form method="GET">
                                        <div class="mb-2">
                                            <label class="form-label">ID Peminjaman Barang</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($idPeminjamanBrg) ?>" disabled>
                                            <input type="hidden" name="id$idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">
                                        </div>

                                        <div class="mb-2">
                                            <label for="alasanPenolakan" class="form-label">Alasan Penolakan</label>
                                            <textarea class="form-control" id="alasanPenolakan" name="alasanPenolakan" rows="3" style="resize: none;" disabled><?= htmlspecialchars($alasanPenolakan) ?></textarea>
                                        </div>
                                        <div class="d-flex justify-content-between mt-4">
                                            <a href="peminjamanBarang.php" class="btn btn-secondary">Kembali</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

    

<?php include '../../template/footer.php'; ?>