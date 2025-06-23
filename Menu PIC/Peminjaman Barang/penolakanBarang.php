<?php

include '../../koneksi.php';

$idPeminjamanBrg = $_GET['id'] ?? '';
$data = [];
$alasanPenolakan = trim($_POST['alasanPenolakan'] ?? '');

$showModal = false;
$error = '';

if (!empty($idPeminjamanBrg)) {
    // FIX: Remove unnecessary session storage
    // $_SESSION['idPeminjamanBrg'] = $idPeminjamanBrg;

    $query = "SELECT pb.idBarang, pb.jumlahBrg, pb.tglPeminjamanBrg, pb.alasanPeminjamanBrg, 
                     pb.nim, pb.npk, b.namaBarang
              FROM Peminjaman_Barang pb
              JOIN Barang b ON pb.idBarang = b.idBarang
              WHERE pb.idPeminjamanBrg = ?";
    $params = array($idPeminjamanBrg);
    $stmt = sqlsrv_query($conn, $query, $params);

    // FIX: Add error handling for query failure
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if ($stmt && sqlsrv_has_rows($stmt)) {
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }
}


$idBarang = $data['idBarang'] ?? '';
$nim = $data['nim'] ?? '';
$namaBarang = $data['namaBarang'] ?? '';
$npk = $data['npk'] ?? '';
$tglPeminjamanBrg = isset($data['tglPeminjamanBrg']) ? $data['tglPeminjamanBrg']->format('Y-m-d') : '';
$jumlahBrg = $data['jumlahBrg'] ?? '';
$alasanPeminjamanBrg = $data['alasanPeminjamanBrg'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($idPeminjamanBrg) && !empty($alasanPenolakan)) {
        sqlsrv_begin_transaction($conn);

        $updateQuery = "UPDATE Peminjaman_Barang 
                        SET statusPeminjaman = 'Ditolak'
                        WHERE idPeminjamanBrg = ?";
        $updateParams = array($idPeminjamanBrg);
        $updateStmt = sqlsrv_query($conn, $updateQuery, $updateParams);

        $insertQuery = "INSERT INTO Penolakan (idPeminjamanBrg, alasanPenolakan) VALUES (?, ?)";
        $insertParams = array($idPeminjamanBrg, $alasanPenolakan);
        $insertStmt = sqlsrv_query($conn, $insertQuery, $insertParams);

        if ($updateStmt && $insertStmt) {
            sqlsrv_commit($conn);
            // Langsung redirect, tidak perlu modal
            header("Location: peminjamanBarang.php?status=tolak&id=" . urlencode($idPeminjamanBrg));
            exit();
        } else {
            sqlsrv_rollback($conn);
            $errors = sqlsrv_errors();
            $error = "Gagal memproses penolakan: ";
            foreach ($errors as $err) {
                $error .= $err['message'] . "; ";
            }
        }
    } else {
        $error = "Form tidak boleh kosong.";
    }
}
include '../../templates/header.php';

include '../../templates/sidebar.php';
?>
<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Penolakan Peminjaman Barang</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="peminjamanBarang.php">Peminjaman Barang</a></li>
                <li class="breadcrumb-item"><a href="pengajuanBarang.php">Pengajuan Peminjaman Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Penolakan Peminjaman Barang</li>
            </ol>
        </nav>
    </div>


    <!-- Penolakan -->
    <div class="container mt-4 ">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12 " style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-semibold">Penolakan Peminjaman Barang</span>
                    </div>

                    <!-- Error Message Display -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ID Barang</label>
                                        <div class="form-control bg-light"><?= htmlspecialchars($idBarang) ?></div>
                                        <input type="hidden" name="idBarang" value="<?= htmlspecialchars($idBarang) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Peminjaman</label>
                                        <div class="form-control bg-light"><?= htmlspecialchars($tglPeminjamanBrg) ?></div>
                                        <input type="hidden" name="tglPeminjamanBrg" value="<?= htmlspecialchars($tglPeminjamanBrg) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Jumlah Barang</label>
                                        <div class="form-control bg-light"><?= htmlspecialchars($jumlahBrg) ?></div>
                                        <input type="hidden" name="jumlahBrg" value="<?= htmlspecialchars($jumlahBrg) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Alasan Peminjaman</label>
                                        <textarea class="form-control" rows="3" disabled><?= htmlspecialchars($alasanPeminjamanBrg) ?></textarea>
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ID Peminjaman Barang</label>
                                        <div class="form-control bg-light"><?= htmlspecialchars($idPeminjamanBrg) ?></div>
                                        <input type="hidden" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Nama Barang</label>
                                        <div class="form-control bg-light"><?= htmlspecialchars($namaBarang) ?></div>
                                        <input type="hidden" name="namaBarang" value="<?= htmlspecialchars($namaBarang) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">NIM</label>
                                        <div class="form-control bg-light"><?= htmlspecialchars($nim) ?></div>
                                        <input type="hidden" name="nim" value="<?= htmlspecialchars($nim) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">NPK</label>
                                        <div class="form-control bg-light"><?= htmlspecialchars($npk) ?></div>
                                        <input type="hidden" name="npk" value="<?= htmlspecialchars($npk) ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Alasan Penolakan -->
                            <div class="mb-3">
                                <label for="alasanPenolakan" class="form-label">Alasan Penolakan
                                    <span id="alasanError" class="text-danger ms-2" style="font-size:0.95em; display:none;">*Harus Diisi</span>
                                </label>
                                <textarea class="form-control" id="alasanPenolakan" name="alasanPenolakan" rows="3" style="resize: none;"><?= htmlspecialchars($_POST['alasanPenolakan'] ?? '') ?></textarea>
                            </div>

                            <!-- Tombol -->
                            <div class="d-flex justify-content-between">
                                <a href="pengajuanBarang.php?id=<?= urlencode($idPeminjamanBrg) ?>" class="btn btn-secondary">Kembali</a> <button type="submit" class="btn btn-danger">Tolak</button>
                            </div>
                        </form>

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
                                    e.preventDefault();
                                }
                            });
                        </script>



                        <?php include '../../templates/footer.php'; ?>