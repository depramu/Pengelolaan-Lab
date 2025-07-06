<?php
require_once __DIR__ . '/../../../function/init.php';
authorize_role('PIC Aset');

$idPeminjamanRuangan = $_GET['id'] ?? '';
$data = null;
$alasanPenolakan = trim($_POST['alasanPenolakan'] ?? '');

$showModal = false;
$error = '';

if (!empty($idPeminjamanRuangan)) {
    $query = "SELECT
            pr.*,
            r.namaRuangan,
            COALESCE(m.nama, k.nama) AS namaPeminjam,
            sp.statusPeminjaman
        FROM
            Peminjaman_Ruangan pr
        JOIN
            Ruangan r ON pr.idRuangan = r.idRuangan
        LEFT JOIN
            Mahasiswa m ON pr.nim = m.nim
        LEFT JOIN
            Karyawan k ON pr.npk = k.npk
        LEFT JOIN
            Status_Peminjaman sp ON pr.idPeminjamanRuangan = sp.idPeminjamanRuangan
        WHERE
            pr.idPeminjamanRuangan = ?";
    $params = array($idPeminjamanRuangan);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if ($stmt && sqlsrv_has_rows($stmt)) {
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($idPeminjamanRuangan) && !empty($alasanPenolakan)) {
        sqlsrv_begin_transaction($conn);

        // Cek apakah sudah ada status peminjaman untuk id ini
        $cekStatusSql = "SELECT COUNT(*) as jumlah FROM Status_Peminjaman WHERE idPeminjamanRuangan = ?";
        $cekStatusParams = [$idPeminjamanRuangan];
        $cekStatusStmt = sqlsrv_query($conn, $cekStatusSql, $cekStatusParams);
        $sudahAdaStatus = false;
        if ($cekStatusStmt && ($cekStatusRow = sqlsrv_fetch_array($cekStatusStmt, SQLSRV_FETCH_ASSOC))) {
            $sudahAdaStatus = $cekStatusRow['jumlah'] > 0;
        }

        if ($sudahAdaStatus) {
            // Update status peminjaman menjadi 'Ditolak'
            $updateStatusQuery = "UPDATE Status_Peminjaman 
                                SET statusPeminjaman = 'Ditolak'
                                WHERE idPeminjamanRuangan = ?";
            $updateStatusParams = array($idPeminjamanRuangan);
            $updateStatusStmt = sqlsrv_query($conn, $updateStatusQuery, $updateStatusParams);
        } else {
            // Insert status peminjaman baru
            $insertStatusQuery = "INSERT INTO Status_Peminjaman (idPeminjamanRuangan, statusPeminjaman) VALUES (?, 'Ditolak')";
            $insertStatusParams = array($idPeminjamanRuangan);
            $updateStatusStmt = sqlsrv_query($conn, $insertStatusQuery, $insertStatusParams);
        }

        $insertQuery = "INSERT INTO PenolakanRuangan (idPeminjamanRuangan, alasanPenolakan) VALUES (?, ?)";
        $insertParams = array($idPeminjamanRuangan, $alasanPenolakan);
        $insertStmt = sqlsrv_query($conn, $insertQuery, $insertParams);

        if ($updateStatusStmt && $insertStmt) {
            sqlsrv_commit($conn);
            $showModal = true;
        } else {
            sqlsrv_rollback($conn);
            $errors = sqlsrv_errors();
            $error = "Gagal memproses penolakan: ";
            foreach ($errors as $err) {
                $error .= $err['message'] . "; ";
            }
        }
    }
}

include '../../../templates/header.php';
include '../../../templates/sidebar.php';
?>
<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Ruangan</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php">Peminjaman Ruangan</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/Peminjaman Ruangan/pengajuanRuangan.php">Pengajuan Peminjaman Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Penolakan Peminjaman Ruangan</li>
            </ol>
        </nav>
    </div>

    <!-- Penolakan -->
    <div class="container mt-4 ">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12 " style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header bg-white border-bottom border-dark">
                        <span class="fw-bold">Penolakan Peminjaman Ruangan</span>
                    </div>

                    <div class="card-body">
                        <form id="formPenolakanRuangan" method="POST">
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="idPeminjamanRuangan" class="form-label fw-semibold">ID Peminjaman</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($idPeminjamanRuangan) ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="idRuangan" class="form-label fw-semibold">ID Ruangan</label>
                                        <div class="form-control-plaintext"><?= $data && isset($data['idRuangan']) ? htmlspecialchars($data['idRuangan']) : '' ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tglPeminjamanRuangan" class="form-label fw-semibold">Tanggal Peminjaman</label>
                                        <div class="form-control-plaintext"><?= isset($data['tglPeminjamanRuangan']) && $data['tglPeminjamanRuangan'] instanceof DateTime ? htmlspecialchars($data['tglPeminjamanRuangan']->format('d-m-y')) : '' ?></div>
                                    </div>
                                </div>
                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="waktuMulai" class="form-label fw-semibold">Waktu Mulai</label>
                                            <div class="form-control-plaintext"><?= $data && isset($data['waktuMulai']) && $data['waktuMulai'] instanceof DateTime ? htmlspecialchars($data['waktuMulai']->format('H:i')) : '' ?></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="waktuSelesai" class="form-label fw-semibold">Waktu Selesai</label>
                                            <div class="form-control-plaintext"><?= $data && isset($data['waktuSelesai']) && $data['waktuSelesai'] instanceof DateTime ? htmlspecialchars($data['waktuSelesai']->format('H:i')) : '' ?></div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">NIM / NPK</label>
                                        <div class="form-control-plaintext">
                                            <?php
                                            if ($data && !empty($data['nim'])) {
                                                echo htmlspecialchars($data['nim']);
                                            } elseif ($data && !empty($data['npk'])) {
                                                echo htmlspecialchars($data['npk']);
                                            } else {
                                                echo "-";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="namaPeminjam" class="form-label fw-semibold">Nama Peminjam</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($data['namaPeminjam'] ?? '') ?></div>
                                        <input type="hidden" class="form-control" id="namaPeminjam" name="namaPeminjam" value="<?= htmlspecialchars($data['namaPeminjam'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="alasanPeminjamanRuangan" class="form-label fw-semibold">Alasan Peminjaman</label>
                                        <div class="form-control-plaintext"><?= $data && isset($data['alasanPeminjamanRuangan']) ? nl2br(htmlspecialchars($data['alasanPeminjamanRuangan'])) : '' ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="alasanPenolakan" class="form-label fw-semibold">Alasan Penolakan
                                    <span id="alasanPenolakanError" class="fw-normal text-danger ms-2" style="font-size:0.95em; display:none;"></span>
                                </label>
                                <textarea class="form-control" id="alasanPenolakan" name="alasanPenolakan" rows="2" style="resize: none;" placeholder="Masukkan alasan penolakan..."><?= htmlspecialchars($_POST['alasanPenolakan'] ?? '') ?></textarea>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="<?= BASE_URL ?>/Menu/Menu PIC/Peminjaman Ruangan/pengajuanRuangan.php?id=<?= urlencode($idPeminjamanRuangan) ?>" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-danger">Tolak</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../../templates/footer.php'; ?>