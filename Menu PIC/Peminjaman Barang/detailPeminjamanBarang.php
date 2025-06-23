    <?php
    include '../../templates/header.php';
    include '../../templates/sidebar.php';

    $data = null;
    $error_message = null;

    $idPeminjamanBrg = $_GET['id'] ?? '';

    if (!empty($idPeminjamanBrg)) {
        // Query detail peminjaman barang beserta data terkait
        $sql = "SELECT 
                    pb.idPeminjamanBrg, pb.idBarang, pb.nim, pb.npk,
                    pb.tglPeminjamanBrg, pb.jumlahBrg, pb.alasanPeminjamanBrg, pb.statusPeminjaman
                FROM 
                    Peminjaman_Barang pb
                WHERE 
                    pb.idPeminjamanBrg = ?";
        $params = [$idPeminjamanBrg];
        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            $error_message = "Gagal mengambil data. Error: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
        } else {
            $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            if (!$data) {
                $error_message = "Data peminjaman dengan ID '" . htmlspecialchars($idPeminjamanBrg) . "' tidak ditemukan.";
            }
        }
    } else {
        $error_message = "ID Peminjaman Barang tidak valid atau tidak disertakan.";
    }
    ?>

    <main class="col bg-white px-3 px-md-4 py-3 position-relative">
        <h3 class="fw-semibold mb-3">Peminjaman Barang</h3>
        <div class="mb-1">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php">Peminjaman Barang</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail Peminjaman Barang</li>
                </ol>
            </nav>
        </div>

        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                    <div class="card border border-dark">
                        <div class="card-header bg-white border-bottom border-dark">
                            <span class="fw-semibold">Detail Peminjaman Barang</span>
                        </div>
                        <div class="card-body scrollable-card-content" style="max-height: 75vh; overflow-y: auto;">
                            <?php if ($error_message) : ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= $error_message ?>
                                </div>
                            <?php elseif ($data) : ?>
                                <form id="formDetail" method="POST">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">ID Peminjaman Barang</label>
                                                <div class="form-control-plaintext">
                                                    <?= htmlspecialchars($data['idPeminjamanBrg']) ?>
                                                </div>
                                                <input type="hidden" name="idPeminjamanBrg" class="form-control" value="<?= htmlspecialchars($data['idPeminjamanBrg']) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">NIM/NPK</label>
                                                <div class="form-control-plaintext">
                                                    <?php
                                                    // Tampilkan NIM jika ada, jika tidak tampilkan NPK, jika keduanya kosong tampilkan '-'
                                                    if (!empty($data['nim'])) {
                                                        echo htmlspecialchars($data['nim']);
                                                    } elseif (!empty($data['npk'])) {
                                                        echo htmlspecialchars($data['npk']);
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </div>
                                                <input type="hidden" class="form-control" value="<?php
                                                    if (!empty($data['nim'])) {
                                                        echo htmlspecialchars($data['nim']);
                                                    } elseif (!empty($data['npk'])) {
                                                        echo htmlspecialchars($data['npk']);
                                                    } else {
                                                        echo '-';
                                                    }
                                                ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">ID Barang</label>
                                                <div class="form-control-plaintext">
                                                    <?= htmlspecialchars($data['idBarang']) ?>
                                                </div>
                                                <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['idBarang']) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Jumlah Barang</label>
                                                <div class="form-control-plaintext">
                                                    <?= htmlspecialchars($data['jumlahBrg']) ?>
                                                </div>
                                                <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['jumlahBrg']) ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Tanggal Peminjaman</label>
                                                <div class="form-control-plaintext">
                                                    <?= htmlspecialchars(
                                                        $data['tglPeminjamanBrg'] instanceof DateTime
                                                            ? $data['tglPeminjamanBrg']->format('d F Y')
                                                            : ''
                                                    ) ?>
                                                </div>
                                                <input type="hidden" class="form-control" value="<?= ($data['tglPeminjamanBrg'] instanceof DateTime) ? $data['tglPeminjamanBrg']->format('d F Y') : '' ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6"></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Alasan Peminjaman</label>
                                                <div class="form-control-plaintext">
                                                    <?= htmlspecialchars($data['alasanPeminjamanBrg']) ?>
                                                </div>
                                                <textarea class="form-control" rows="3" hidden><?= htmlspecialchars($data['alasanPeminjamanBrg']) ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-3">
                                        <a href="<?= BASE_URL ?>/Menu PIC/Peminjaman Barang/peminjamanBarang.php" class="btn btn-secondary me-2">Kembali</a>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php
    include '../../templates/footer.php';
    ?>