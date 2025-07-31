<?php
    require_once __DIR__ . '/../../../function/init.php'; // Penyesuaian: gunakan init.php untuk inisialisasi dan otorisasi
    authorize_role('PIC Aset');

    $showModal = false;
    $idPeminjamanRuangan = $_GET['id'] ?? '';
    $error = null;

    // Ambil data pengembalian ruangan dan dokumentasi
    $data = null;
    $dokSebelum = null;
    $dokSesudah = null;
    if ($idPeminjamanRuangan) {
        $sql = "SELECT 
                p.idPeminjamanRuangan, p.idRuangan, p.nim, p.npk,
                p.tglPeminjamanRuangan, p.waktuMulai, p.waktuSelesai,
                p.alasanPeminjamanRuangan,
                sp.statusPeminjaman,
                peng.kondisiRuangan, peng.catatanPengembalianRuangan,
                COALESCE(m.nama, k.nama) AS namaPeminjam,
                r.namaRuangan
            FROM 
                Peminjaman_Ruangan p
            LEFT JOIN 
                Status_Peminjaman sp ON p.idPeminjamanRuangan = sp.idPeminjamanRuangan
            LEFT JOIN 
                Pengembalian_Ruangan peng ON p.idPeminjamanRuangan = peng.idPeminjamanRuangan
            LEFT JOIN 
                Mahasiswa m ON p.nim = m.nim
            LEFT JOIN 
                Karyawan k ON p.npk = k.npk
            LEFT JOIN 
                Ruangan r ON p.idRuangan = r.idRuangan
            WHERE 
                p.idPeminjamanRuangan = ?";
        $params = [$idPeminjamanRuangan];
        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt && ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
            $data = $row;
        }

        // Ambil dokumentasi sebelum dan sesudah dari database
        $sqlDok = "SELECT dokumentasiSebelum, dokumentasiSesudah FROM Pengembalian_Ruangan WHERE idPeminjamanRuangan = ?";
        $paramsDok = [$idPeminjamanRuangan];
        $stmtDok = sqlsrv_query($conn, $sqlDok, $paramsDok);
        if ($stmtDok && ($rowDok = sqlsrv_fetch_array($stmtDok, SQLSRV_FETCH_ASSOC))) {
            $dokSebelum = $rowDok['dokumentasiSebelum'] ?? null;
            $dokSesudah = $rowDok['dokumentasiSesudah'] ?? null;
        }
    }

    $nim = $data['nim'] ?? ''; // Pastikan $nim diinisialisasi, bisa dari session atau data yang diambil    


    // Proses POST untuk simpan ke database
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $idPeminjamanRuangan) {
        $kondisiRuangan = $_POST['kondisiRuangan'] ?? '';
        $catatanPengembalianRuangan = $_POST['catatanPengembalianRuangan'] ?? '';

        // Cek apakah sudah ada data pengembalian untuk id ini
        $cekSql = "SELECT COUNT(*) as jumlah FROM Pengembalian_Ruangan WHERE idPeminjamanRuangan = ?";
        $cekParams = [$idPeminjamanRuangan];
        $cekStmt = sqlsrv_query($conn, $cekSql, $cekParams);
        $sudahAda = false;
        if ($cekStmt && ($cekRow = sqlsrv_fetch_array($cekStmt, SQLSRV_FETCH_ASSOC))) {
            $sudahAda = $cekRow['jumlah'] > 0;
        }

        if ($sudahAda) {
            // Update
            $sqlSave = "UPDATE Pengembalian_Ruangan SET kondisiRuangan = ?, catatanPengembalianRuangan = ? WHERE idPeminjamanRuangan = ?";
            $paramsSave = [$kondisiRuangan, $catatanPengembalianRuangan, $idPeminjamanRuangan];
        } else {
            // Insert
            $sqlSave = "INSERT INTO Pengembalian_Ruangan (idPeminjamanRuangan, kondisiRuangan, catatanPengembalianRuangan) VALUES (?, ?, ?)";
            $paramsSave = [$idPeminjamanRuangan, $kondisiRuangan, $catatanPengembalianRuangan];
        }

        $stmtSave = sqlsrv_query($conn, $sqlSave, $paramsSave);

        if ($stmtSave) {
            // Cek apakah sudah ada status peminjaman untuk id ini
            $cekStatusSql = "SELECT COUNT(*) as jumlah FROM Status_Peminjaman WHERE idPeminjamanRuangan = ?";
            $cekStatusParams = [$idPeminjamanRuangan];
            $cekStatusStmt = sqlsrv_query($conn, $cekStatusSql, $cekStatusParams);
            $sudahAdaStatus = false;
            if ($cekStatusStmt && ($cekStatusRow = sqlsrv_fetch_array($cekStatusStmt, SQLSRV_FETCH_ASSOC))) {
                $sudahAdaStatus = $cekStatusRow['jumlah'] > 0;
            }

            if ($sudahAdaStatus) {
                // Update status peminjaman menjadi 'Telah Dikembalikan'
                $sqlUpdateStatus = "UPDATE Status_Peminjaman SET statusPeminjaman = ? WHERE idPeminjamanRuangan = ?";
                $paramsUpdateStatus = ['Telah Dikembalikan', $idPeminjamanRuangan];
            } else {
                // Insert status peminjaman baru
                $sqlUpdateStatus = "INSERT INTO Status_Peminjaman (idPeminjamanRuangan, statusPeminjaman) VALUES (?, ?)";
                $paramsUpdateStatus = [$idPeminjamanRuangan, 'Telah Dikembalikan'];
            }

            $stmtUpdateStatus = sqlsrv_query($conn, $sqlUpdateStatus, $paramsUpdateStatus);

            if ($stmtUpdateStatus) {
                // Ambil idRuangan dari peminjaman untuk update ketersediaan ruangan
                $sqlGetRuangan = "SELECT idRuangan FROM Peminjaman_Ruangan WHERE idPeminjamanRuangan = ?";
                $paramsGetRuangan = [$idPeminjamanRuangan];
                $stmtGetRuangan = sqlsrv_query($conn, $sqlGetRuangan, $paramsGetRuangan);
                $idRuangan = null;
                if ($stmtGetRuangan && ($rowRuangan = sqlsrv_fetch_array($stmtGetRuangan, SQLSRV_FETCH_ASSOC))) {
                    $idRuangan = $rowRuangan['idRuangan'];
                }

                if ($idRuangan) {
                    // Update kondisi dan ketersediaan ruangan
                    $updateKondisi = $kondisiRuangan;
                    $updateKetersediaan = ($kondisiRuangan === 'Rusak') ? 'Tidak Tersedia' : 'Tersedia';

                    $sqlUpdateRuangan = "UPDATE Ruangan SET kondisiRuangan = ?, ketersediaan = ? WHERE idRuangan = ?";
                    $paramsUpdateRuangan = [$updateKondisi, $updateKetersediaan, $idRuangan];
                    sqlsrv_query($conn, $sqlUpdateRuangan, $paramsUpdateRuangan);
                }

                $untuk = $nim; // atau $_SESSION['user_role'] untuk peminjam
                $pesanNotif = "Ruangan yang dipinjam telah dikembalikan.";
                $queryNotif = "INSERT INTO Notifikasi (pesan, status, untuk) VALUES (?, 'Belum Dibaca', ?)";
                sqlsrv_query($conn, $queryNotif, [$pesanNotif,$untuk]);
                $showModal = true;
            } else {
                $error = "Gagal mengubah status peminjaman.";
            }
        } else {
            $error = "Gagal menyimpan data pengembalian ruangan.";
        }
    }



    include '../../../templates/header.php';
    include '../../../templates/sidebar.php';
    ?>

    <main class="col bg-white px-4 py-3 position-relative">
        <h3 class="fw-semibold mb-3">Pengembalian Ruangan</h3>

        <div class="mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php">Peminjaman Ruangan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pengembalian Ruangan </li>
                </ol>
            </nav>
        </div>

        <div class="container mt-4">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                    <div class="card border border-dark">
                        <div class="card-header border-bottom border-dark text-white" style="background-color:rgb(9, 103, 185);">
                            <span class="fw-semibold">Pengembalian Ruangan</span>
                        </div>
                        <div class="card-body scrollable-card-content">
                            <form method="POST" id="formPengembalianRuangan" enctype="multipart/form-data">
                                <!-- Hidden input for ID Peminjaman - processed in background -->
                                <input type="hidden" id="idPeminjamanRuangan" name="idPeminjamanRuangan" value="<?= isset($idPeminjamanRuangan) ? htmlspecialchars($idPeminjamanRuangan) : '' ?>">
                                
                                <!-- Display borrower information for Menunggu Pengecekan status -->
                                <?php if (($data['statusPeminjaman'] ?? '') === 'Menunggu Pengecekan'): ?>
                                <div class="mb-3 row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">NIM / NPK</label>
                                            <div class="form-control-plaintext bg-light"><?= htmlspecialchars($data['nim'] ?? $data['npk'] ?? '-') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nama Ruangan</label>
                                            <div class="form-control-plaintext bg-light"><?= htmlspecialchars($data['namaRuangan'] ?? '-') ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nama Peminjam</label>
                                            <div class="form-control-plaintext bg-light"><?= htmlspecialchars($data['namaPeminjam'] ?? '-') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="kondisiRuangan" class="form-label fw-semibold d-flex align-items-center">
                                                Kondisi Ruangan
                                                <span id="kondisiError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                            </label>
                                            <select class="form-select" id="kondisiRuangan" name="kondisiRuangan">
                                                <option value="" hidden <?= empty($data['kondisiRuangan']) ? 'selected' : '' ?>>Pilih Kondisi Ruangan</option>
                                                <option value="Baik" <?= ($data['kondisiRuangan'] ?? '') === 'Baik' ? 'selected' : '' ?>>Baik</option>
                                                <option value="Rusak" <?= ($data['kondisiRuangan'] ?? '') === 'Rusak' ? 'selected' : '' ?>>Rusak</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="mb-3 row">
                                    <div class="col-md-6">
                                        <label for="kondisiRuangan" class="form-label fw-semibold d-flex align-items-center">
                                            Kondisi Ruangan
                                            <span id="kondisiError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                        </label>
                                        <select class="form-select" id="kondisiRuangan" name="kondisiRuangan">
                                            <option value="" hidden <?= empty($data['kondisiRuangan']) ? 'selected' : '' ?>>Pilih Kondisi Ruangan</option>
                                            <option value="Baik" <?= ($data['kondisiRuangan'] ?? '') === 'Baik' ? 'selected' : '' ?>>Baik</option>
                                            <option value="Rusak" <?= ($data['kondisiRuangan'] ?? '') === 'Rusak' ? 'selected' : '' ?>>Rusak</option>
                                        </select>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="mb-3">
                                    <label for="catatanPengembalianRuangan" class="form-label fw-semibold d-flex align-items-center">
                                        Catatan Pengembalian
                                        <span id="catatanError" class="fw-normal text-danger ms-2" style="display:none;font-size:0.95em;"></span>
                                    </label>
                                    <textarea type="text" class="form-control" id="catatanPengembalianRuangan" name="catatanPengembalianRuangan" rows="2" style="resize: none;" placeholder="Masukkan catatan pengembalian.."><?= htmlspecialchars($data['catatanPengembalianRuangan'] ?? '') ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="dokumentasiSebelum" class="fw-semibold">Dokumentasi Sebelum</label><br>
                                            <?php if (!empty($dokSebelum)): ?>
                                                <a href="<?= BASE_URL ?>/uploads/dokumentasi/<?= htmlspecialchars($dokSebelum) ?>" target="_blank">
                                                            <img src="<?= BASE_URL ?>uploads/dokumentasi/<?= htmlspecialchars($dokSebelum) ?>"
                                                            alt="Dokumentasi Sebelum"
                                                            class="img-fluid rounded border"
                                                            style="max-height: 200px; cursor: pointer;"
                                                            onclick="window.open('<?= BASE_URL ?>uploads/dokumentasi/<?= htmlspecialchars($dokSebelum) ?>', '_blank')">
                                                            </a>
                                            <?php else: ?>
                                                <span class="text-danger"><em>(Tidak Diunggah)</em></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="dokumentasiSesudah" class="fw-semibold">Dokumentasi Sesudah</label><br>
                                            <?php if (!empty($dokSesudah)): ?>
                                                <a href="<?= BASE_URL ?>/uploads/dokumentasi/<?= htmlspecialchars($dokSesudah) ?>" target="_blank">
                                                            <img src="<?= BASE_URL ?>uploads/dokumentasi/<?= htmlspecialchars($dokSesudah) ?>"
                                                            alt="Dokumentasi Sesudah"
                                                            class="img-fluid rounded border"
                                                            style="max-height: 200px; cursor: pointer;"
                                                            onclick="window.open('<?= BASE_URL ?>uploads/dokumentasi/<?= htmlspecialchars($dokSesudah) ?>', '_blank')">
                                                            </a>
                                            <?php else: ?>
                                                <span class="text-danger"><em>(Tidak Diunggah)</em></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="<?= BASE_URL ?>/Menu/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php" class="btn btn-secondary">Kembali</a>
                                    <button type="submit" class="btn btn-primary">Kirim</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </main>


    <?php include '../../../templates/footer.php'; ?>