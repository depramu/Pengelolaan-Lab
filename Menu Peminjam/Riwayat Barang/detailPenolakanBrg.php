<?php
include '../../templates/header.php';
$idPeminjamanBrg = $_GET['id'] ?? '';
if (empty($idPeminjamanBrg)) {
    die("Akses tidak valid. ID Peminjaman tidak ditemukan.");
}

$data = [];
$alasanPenolakan = '';

$sqlPenolakan = "SELECT alasanPenolakan FROM Penolakan WHERE idPeminjamanBrg = ?";
$stmtPenolakan = sqlsrv_query($conn, $sqlPenolakan, [$idPeminjamanBrg]);

if ($stmtPenolakan && $rowPenolakan = sqlsrv_fetch_array($stmtPenolakan, SQLSRV_FETCH_ASSOC)) {
    $alasanPenolakan = $rowPenolakan['alasanPenolakan'];
}

$query = "SELECT * FROM Peminjaman_Barang WHERE idPeminjamanBrg = ? AND (nim = ? OR npk = ?)";
$params = [
    $idPeminjamanBrg,
    $_SESSION['nim'] ?? null,
    $_SESSION['npk'] ?? null
];
$stmt = sqlsrv_query($conn, $query, $params);

$idBarang = '';
$nim = '';
$npk = '';
$tglPeminjamanBrg = '';
$jumlahBrg = '';
$alasanPeminjamanBrg = '';

if ($stmt && sqlsrv_has_rows($stmt)) {
    $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    $idBarang = $data['idBarang'] ?? 'Data tidak ada';
    $nim = $data['nim'] ?? 'Data tidak ada';
    $npk = $data['npk'] ?? 'Data tidak ada';

    if (isset($data['tglPeminjamanBrg']) && $data['tglPeminjamanBrg'] instanceof DateTimeInterface) {
        $tglPeminjamanBrg = $data['tglPeminjamanBrg']->format('d F Y');
    } else {
        $tglPeminjamanBrg = 'Tanggal tidak valid';
    }

    $jumlahBrg = $data['jumlahBrg'] ?? '0';
    $alasanPeminjamanBrg = $data['alasanPeminjamanBrg'] ?? 'Tidak ada alasan.';
} else {
    die("Data peminjaman tidak ditemukan atau Anda tidak memiliki hak akses.");
}
include '../../templates/sidebar.php';
?>
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="riwayatBarang.php">Riwayat Peminjaman Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Peminjaman Barang</li>
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
                        <form method="POST">
                            <input type="hidden" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idBarang" class="form-label">ID Barang</label>
                                        <input type="text" class="form-control" id="idBarang" name="idBarang" value="<?= htmlspecialchars($idBarang) ?>" disabled>
                                    </div>
                                    <div class="mb-2">
                                        <label for="tglPeminjamanBrg" class="form-label">Tanggal Peminjaman</label>
                                        <input type="text" class="form-control" id="tglPeminjamanBrg" name="tglPeminjamanBrg" value="<?= htmlspecialchars($tglPeminjamanBrg) ?>" disabled>
                                    </div>
                                    <div class="mb-2">
                                        <label for="jumlahBrg" class="form-label">Jumlah Barang</label>
                                        <input type="text" class="form-control" id="jumlahBrg" name="jumlahBrg" value="<?= htmlspecialchars($jumlahBrg) ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="idPeminjamanBrgDisplay" class="form-label">ID Peminjaman Barang</label>
                                        <input type="text" class="form-control" id="idPeminjamanBrgDisplay" value="<?= htmlspecialchars($idPeminjamanBrg) ?>" disabled>
                                    </div>
                                    <div class="mb-2">
                                        <label for="nim" class="form-label">NIM</label>
                                        <input type="text" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($nim) ?>" disabled>
                                    </div>
                                    <div class="mb-2">
                                        <label for="npk" class="form-label">NPK</label>
                                        <input type="text" class="form-control" id="npk" name="npk" value="<?= htmlspecialchars($npk) ?>" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="alasanPeminjamanBrg" class="form-label">Alasan Peminjaman</label>
                                        <textarea class="form-control" id="alasanPeminjamanBrg" rows="3" style="width: 100%;" disabled><?= htmlspecialchars($alasanPeminjamanBrg) ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="alasanPenolakan" class="form-label">Alasan Penolakan</label>
                                        <textarea class="form-control" id="alasanPenolakan" rows="3" style="width: 100%;" disabled><?= htmlspecialchars($alasanPenolakan) ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-start mt-4">
                                <a href="<?= BASE_URL ?>/Menu Peminjam/Riwayat Barang/riwayatBarang.php" class="btn btn-secondary">Kembali</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php

include '../../templates/footer.php';
?>