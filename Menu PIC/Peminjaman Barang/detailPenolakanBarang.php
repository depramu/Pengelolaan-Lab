<?php
include '../../templates/header.php';

// Perbaikan: Gunakan parameter GET untuk menerima ID
$idPeminjamanBrg = $_GET['id'] ?? '';
$query = "SELECT * FROM Penolakan WHERE idPeminjamanBrg = ?";
$params = array($idPeminjamanBrg);
$stmt = sqlsrv_query($conn, $query, $params);
$alasanPenolakan = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)['alasanPenolakan'] ?? '';

include '../../templates/sidebar.php';
?>

<main class="col bg-white px-4 py-3 position-relative">
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php">Peminjaman Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Penolaka Peminjaman Barang</li>
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
                                <div class="form-control-plaintext"><?= htmlspecialchars($idPeminjamanBrg) ?></div>

                                <input type="hidden" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">
                            </div>

                            <div class="mb-2">
                                <label for="alasanPenolakan" class="form-label">Alasan Penolakan</label>
                                <div class="form-control-plaintext"><?= htmlspecialchars($alasanPenolakan) ?></div>
                                <textarea class="form-control" id="alasanPenolakan" name="alasanPenolakan" rows="3" style="resize: none;" hidden ><?= htmlspecialchars($alasanPenolakan) ?></textarea>
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



<?php include '../../templates/footer.php'; ?>