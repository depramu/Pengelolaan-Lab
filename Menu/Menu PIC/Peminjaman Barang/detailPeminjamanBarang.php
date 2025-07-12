    <?php
    require_once __DIR__ . '/../../../function/init.php'; // Penyesuaian: gunakan init.php untuk inisialisasi dan otorisasi

    $data = null;
    $error_message = null;

    $idPeminjamanBrg = $_GET['id'] ?? '';

    if (!empty($idPeminjamanBrg)) {
        $_SESSION['idPeminjamanBrg'] = $idPeminjamanBrg;

        $query = "SELECT 
                    pb.idPeminjamanBrg, pb.idBarang, pb.nim, pb.npk,
                    pb.tglPeminjamanBrg, pb.jumlahBrg, pb.alasanPeminjamanBrg,
                    b.namaBarang,
                    sp.statusPeminjaman,
                    sp.alasanPenolakan,
                COALESCE(m.nama, k.nama) AS namaPeminjam
                FROM 
                    Peminjaman_Barang pb
                JOIN 
                    Barang b ON pb.idBarang = b.idBarang
                LEFT JOIN 
                    Status_Peminjaman sp ON pb.idPeminjamanBrg = sp.idPeminjamanBrg
                LEFT JOIN
                    Mahasiswa m ON pb.nim = m.nim
                LEFT JOIN
                    Karyawan k ON pb.npk = k.npk
                WHERE 
                    pb.idPeminjamanBrg = ?";
        $params = [$idPeminjamanBrg];
        $stmt = sqlsrv_query($conn, $query, $params);

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
    include '../../../templates/header.php';
    include '../../../templates/sidebar.php';

    ?>

    <main class="col bg-white px-3 px-md-4 py-3 position-relative">
        <h3 class="fw-semibold mb-3">Peminjaman Barang</h3>
        <div class="mb-1">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/Peminjaman Barang/peminjamanBarang.php">Peminjaman Barang</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail Peminjaman Barang</li>
                </ol>
            </nav>
        </div>

        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                    <div class="card border border-dark">
                        <div class="card-header border-bottom border-dark text-white" style="background-color:rgb(9, 103, 185);">
                            <span class="fw-semibold">Detail Peminjaman Barang</span>
                        </div>


                        <div class="card-body scrollable-card-content">
                            <?php if ($error_message) : ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= $error_message ?>
                                </div>
                            <?php elseif ($data) : ?>
                                <form id="formDetail" method="POST">
                                    <div class="row mb-3">
                                        <!-- Kolom Kiri -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nama Barang</label>
                                                <div class="form-control-plaintext">
                                                    <?= htmlspecialchars($data['namaBarang']) ?>
                                                </div>
                                                <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['namaBarang']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Tanggal Peminjaman</label>
                                                <div class="form-control-plaintext">
                                                    <?= htmlspecialchars(
                                                        $data['tglPeminjamanBrg'] instanceof DateTime
                                                            ? $data['tglPeminjamanBrg']->format('d M Y')
                                                            : ''
                                                    ) ?>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Jumlah Barang</label>
                                                <div class="form-control-plaintext">
                                                    <?= htmlspecialchars($data['jumlahBrg']) ?>
                                                </div>
                                                <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['jumlahBrg']) ?>">
                                            </div>
                                        </div>
                                        <!-- Kolom Kanan -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">NIM/NPK</label>
                                                <div class="form-control-plaintext">
                                                    <?php
                                                    if (!empty($data['nim'])) {
                                                        echo htmlspecialchars($data['nim']);
                                                    } elseif (!empty($data['npk'])) {
                                                        echo htmlspecialchars($data['npk']);
                                                    } else {
                                                        echo '-';
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
                                                <label class="form-label fw-semibold">Alasan Peminjaman</label>
                                                <div class="form-control-plaintext">
                                                    <?= nl2br(htmlspecialchars($data['alasanPeminjamanBrg'])) ?>
                                                </div>
                                                <textarea class="form-control" rows="3" hidden><?= htmlspecialchars($data['alasanPeminjamanBrg']) ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Status Peminjaman</label>
                                                <?php
                                                // Tentukan class status
                                                $statusClass = 'text-secondary';
                                                switch ($data['statusPeminjaman']) {
                                                    case 'Diajukan':
                                                        $statusClass = 'text-primary';
                                                        break;
                                                    case 'Menunggu Persetujuan':
                                                        $statusClass = 'text-warning';
                                                        break;
                                                    case 'Menunggu Pengecekan':
                                                        $statusClass = 'text-warning';
                                                        break;
                                                    case 'Sedang Dipinjam':
                                                        $statusClass = 'text-info';
                                                        break;
                                                    case 'Telah Dikembalikan':
                                                        $statusClass = 'text-success';
                                                        break;
                                                    case 'Ditolak':
                                                        $statusClass = 'text-danger';
                                                        break;
                                                }
                                                ?>
                                                <div class="form-control-plaintext <?= $statusClass ?>"><?= htmlspecialchars($data['statusPeminjaman']) ?></div>
                                                <input type="hidden" class="form-control" value="<?= htmlspecialchars($data['statusPeminjaman']) ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 d-flex justify-content-between mt-3">
                                            <a href="<?= BASE_URL ?>/Menu/Menu PIC/Peminjaman Barang/peminjamanBarang.php" class="btn btn-secondary me-2">Kembali</a>
                                        </div>
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
    include '../../../templates/footer.php';
    ?>