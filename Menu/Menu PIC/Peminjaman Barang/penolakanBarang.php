<?php
require_once __DIR__ . '/../../../function/init.php'; // Penyesuaian: gunakan init.php untuk inisialisasi dan otorisasi
authorize_role('PIC Aset');

$idPeminjamanBrg = $_GET['id'] ?? '';
$data = [];
$alasanPenolakan = trim($_POST['alasanPenolakan'] ?? '');

$showModal = false;
$error = '';

if (!empty($idPeminjamanBrg)) {

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

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if ($stmt && sqlsrv_has_rows($stmt)) {
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }
}

$nim = $data['nim'] ?? '';
$tglPeminjamanBrg = isset($data['tglPeminjamanBrg']) ? $data['tglPeminjamanBrg']->format('d M Y') : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($idPeminjamanBrg) && !empty($alasanPenolakan)) {
        sqlsrv_begin_transaction($conn);

        // Cek apakah sudah ada status peminjaman untuk id ini
        $cekStatusSql = "SELECT COUNT(*) as jumlah FROM Status_Peminjaman WHERE idPeminjamanBrg = ?";
        $cekStatusParams = [$idPeminjamanBrg];
        $cekStatusStmt = sqlsrv_query($conn, $cekStatusSql, $cekStatusParams);
        $sudahAdaStatus = false;
        if ($cekStatusStmt && ($cekStatusRow = sqlsrv_fetch_array($cekStatusStmt, SQLSRV_FETCH_ASSOC))) {
            $sudahAdaStatus = $cekStatusRow['jumlah'] > 0;
        }

        if ($sudahAdaStatus) {
            $updateStatusQuery = "UPDATE Status_Peminjaman 
                                  SET statusPeminjaman = 'Ditolak', alasanPenolakan = ?
                                  WHERE idPeminjamanBrg = ?";
            $updateStatusParams = array($alasanPenolakan, $idPeminjamanBrg);
            $updateStatusStmt = sqlsrv_query($conn, $updateStatusQuery, $updateStatusParams);
        } else {
            // Insert status peminjaman baru
            $insertStatusQuery = "INSERT INTO Status_Peminjaman (idPeminjamanBrg, statusPeminjaman, alasanPenolakan) VALUES (?, 'Ditolak', ?)";
            $insertStatusParams = array($idPeminjamanBrg, $alasanPenolakan);
            $updateStatusStmt = sqlsrv_query($conn, $insertStatusQuery, $insertStatusParams);
        }

        if ($updateStatusStmt) {
            $untuk = $nim;
            $pesanNotif = "Pengajuan peminjaman barang ditolak oleh PIC.";
            $queryNotif = "INSERT INTO Notifikasi (pesan, status, untuk) VALUES (?, 'Belum Dibaca', ?)";
            sqlsrv_query($conn, $queryNotif, [$pesanNotif,$untuk]);
            sqlsrv_commit($conn);
            $showModal = true;
        } else {
            sqlsrv_rollback($conn);
            $errors = sqlsrv_errors();
        }
    } else {
        // Validasi gagal, bisa tambahkan pesan error jika perlu
    }
}


include '../../../templates/header.php';
include '../../../templates/sidebar.php';
?>
<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Barang</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/Peminjaman Barang/peminjamanBarang.php">Peminjaman Barang</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/Peminjaman Barang/pengajuanBarang.php">Pengajuan Peminjaman Barang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Penolakan Peminjaman Barang</li>
            </ol>
        </nav>
    </div>

    <!-- Penolakan -->
    <div class="container mt-4 ">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12 " style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header border-bottom border-dark text-white" style="background-color:rgb(9, 103, 185);">
                        <span class="fw-semibold">Penolakan Peminjaman Barang</span>
                    </div>

                    <div class="card-body scrollable-card-content">
                        <form id="formPenolakanBarang" method="POST">
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="namaBarang" class="form-label fw-semibold">Nama Barang</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($data['namaBarang'] ?? '') ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tglPeminjamanBrg" class="form-label fw-semibold">Tanggal Peminjaman</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($tglPeminjamanBrg) ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">NIM / NPK</label>
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
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="namaPeminjam" class="form-label fw-semibold">Nama Peminjam</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($data['namaPeminjam'] ?? '') ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="jumlahBrg" class="form-label fw-semibold">Jumlah Barang</label>
                                        <div class="form-control-plaintext"><?= htmlspecialchars($data['jumlahBrg'] ?? '') ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="alasanPeminjamanBrg" class="form-label fw-semibold">Alasan Peminjaman</label>
                                        <div class="form-control-plaintext"><?= nl2br(htmlspecialchars($data['alasanPeminjamanBrg'] ?? '')) ?></div>
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
                                <a href="pengajuanBarang.php?id=<?= urlencode($idPeminjamanBrg) ?>" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-danger">Tolak</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../../templates/footer.php'; ?>