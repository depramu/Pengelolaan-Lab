<?php
require_once __DIR__ . '/../../../function/init.php'; // Penyesuaian: gunakan init.php untuk inisialisasi dan otorisasi
authorize_role(['PIC Aset']);

$idPeminjamanRuangan = $_GET['id'] ?? '';
$data = null;
$error = '';
$showModal = false;



// Ambil data peminjaman beserta nama peminjam (Mahasiswa/Karyawan) dan info nim/npk
if (!empty($idPeminjamanRuangan)) {
    $_SESSION['idPeminjamanRuangan'] = $idPeminjamanRuangan;

    $query = "SELECT 
                p.idPeminjamanRuangan, p.idRuangan, p.nim, p.npk,
                p.tglPeminjamanRuangan, p.waktuMulai, p.waktuSelesai,
                p.alasanPeminjamanRuangan, r.namaRuangan,
                COALESCE(m.nama, k.nama) AS namaPeminjam,
                sp.statusPeminjaman
            FROM 
                Peminjaman_Ruangan p
            LEFT JOIN 
                Mahasiswa m ON p.nim = m.nim
            LEFT JOIN 
                Karyawan k ON p.npk = k.npk
            LEFT JOIN 
                Ruangan r ON p.idRuangan = r.idRuangan
            LEFT JOIN
                Status_Peminjaman sp ON p.idPeminjamanRuangan = sp.idPeminjamanRuangan
            WHERE 
                p.idPeminjamanRuangan = ?";
    $params = array($idPeminjamanRuangan);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        $error_details = sqlsrv_errors();
        $error_message = "Error saat mengambil data peminjaman. ";
        if ($error_details) {
            foreach ($error_details as $err) {
                $error_message .= $err['message'] . " ";
            }
        }
        die($error_message);
    }

    if (sqlsrv_has_rows($stmt)) {
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    } else {
        $error = "Data peminjaman tidak ditemukan untuk ID: " . htmlspecialchars($idPeminjamanRuangan);
    }
} else {
    $error = "ID Peminjaman tidak valid.";
}

$nim = $data['nim'] ?? ''; // Pastikan $nim diinisialisasi, bisa dari session atau data yang diambil    

// Proses form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['setuju'])) {
        // Setujui peminjaman
        $query = "UPDATE Status_Peminjaman 
                  SET statusPeminjaman = 'Sedang Dipinjam'
                  WHERE idPeminjamanRuangan = ?";
        $params = array($idPeminjamanRuangan);
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt) {
            $untuk = $nim;
            $pesanNotif = "Pengajuan peminjaman ruangan disetujui oleh PIC.";
            $queryNotif = "INSERT INTO Notifikasi (pesan, status, untuk) VALUES (?, 'Belum Dibaca', ?)";
            sqlsrv_query($conn, $queryNotif, [$pesanNotif,$untuk]);

            $showModal = true;
        } else {
            $error = "Gagal melakukan pengajuan ruangan.";
            exit;
        }
    }
}
include  '../../../templates/header.php';
include '../../../templates/sidebar.php';
?>
<!-- Content Area -->
<main class="col bg-white px-4 py-3 position-relative">
    <h3 class="fw-semibold mb-3">Peminjaman Ruangan</h3>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php">Peminjaman Ruangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengajuan Peminjaman Ruangan</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                <div class="card border border-dark">
                    <div class="card-header border-bottom border-dark text-white" style="background-color:rgb(9, 103, 185);">

                        <span class="fw-semibold">Pengajuan Peminjaman Ruangan</span>
                    </div>
                    <div class="card-body scrollable-card-content">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <form method="POST" id="formPengajuan">
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="namaRuangan" class="form-label fw-semibold">Nama Ruangan</label>
                                        <div class="form-control-plaintext"><?= $data && isset($data['namaRuangan']) ? htmlspecialchars($data['namaRuangan']) : '' ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tglPeminjamanRuangan" class="form-label fw-semibold">Tanggal Peminjaman</label>
                                        <div class="form-control-plaintext">
                                            <?php
                                            if ($data && isset($data['tglPeminjamanRuangan']) && $data['tglPeminjamanRuangan'] instanceof DateTime) {
                                                echo htmlspecialchars($data['tglPeminjamanRuangan']->format('d M Y'));
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="waktuMulai" class="form-label fw-semibold">Waktu Mulai</label>
                                            <div class="form-control-plaintext">
                                                <?php
                                                if ($data && isset($data['waktuMulai']) && $data['waktuMulai'] instanceof DateTime) {
                                                    echo htmlspecialchars($data['waktuMulai']->format('H:i'));
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="waktuSelesai" class="form-label fw-semibold">Waktu Selesai</label>
                                            <div class="form-control-plaintext">
                                                <?php
                                                if ($data && isset($data['waktuSelesai']) && $data['waktuSelesai'] instanceof DateTime) {
                                                    echo htmlspecialchars($data['waktuSelesai']->format('H:i'));
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                  
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
                                    </div>
                                    <div class="mb-3">
                                        <label for="alasanPeminjaman" class="form-label fw-semibold">Alasan Peminjaman</label>
                                        <div class="form-control-plaintext">
                                            <?php
                                            if ($data && isset($data['alasanPeminjamanRuangan'])) {
                                                echo nl2br(htmlspecialchars($data['alasanPeminjamanRuangan']));
                                            }
                                            ?>
                                        </div>
                                        <textarea class="form-control w-100" id="alasanPeminjaman" name="alasanPeminjaman" hidden rows="3" style="background: #f5f5f5;"><?php
                                                                                                                                                                        if ($data && isset($data['alasanPeminjamanRuangan'])) {
                                                                                                                                                                            echo htmlspecialchars($data['alasanPeminjamanRuangan']);
                                                                                                                                                                        }
                                                                                                                                                                        ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between gap-2 mt-4">
                                <div class="align-self-start">
                                    <a href="<?= BASE_URL ?>/Menu/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php" class="btn btn-secondary">Kembali</a>
                                </div>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?= BASE_URL ?>Menu//Menu PIC/Peminjaman Ruangan/penolakanRuangan.php?id=<?= urlencode($idPeminjamanRuangan) ?>" class="btn btn-danger" id="btnTolak">Tolak</a>
                                    <button type="submit" name="setuju" class="btn btn-primary">Setuju</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../../../templates/footer.php'; ?>