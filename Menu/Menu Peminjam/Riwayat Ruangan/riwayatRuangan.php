    <?php
    require_once __DIR__ . '/../../../function/init.php';
    require_once __DIR__ . '/../../../function/pagination.php';
    authorize_role(['Peminjam']);

    // --- Tangkap parameter pencarian dan filter ---
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    $filterStatus = isset($_GET['status']) ? $_GET['status'] : '';

    // Pagination setup
    $perPage = 7;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;

    $result = false;
    $totalPages = 1;
    $offset = ($page - 1) * $perPage;

    if (isset($_SESSION['user_role'])) {
        if ($_SESSION['user_role'] == 'Peminjam' && isset($_SESSION['nim'])) {
            $peminjam_field = 'nim';
            $peminjam_value = $_SESSION['nim'];
        } elseif ($_SESSION['user_role'] == 'Peminjam' && isset($_SESSION['npk'])) {
            $peminjam_field = 'npk';
            $peminjam_value = $_SESSION['npk'];
        } else {
            $peminjam_field = null;
            $peminjam_value = null;
        }

        if (!empty($peminjam_field) && !empty($peminjam_value)) {
            $updateKedaluwarsaSql = "
            UPDATE sp
            SET sp.statusPeminjaman = 'Kedaluwarsa'
            FROM Status_Peminjaman sp
            JOIN Peminjaman_Ruangan pr ON sp.idPeminjamanRuangan = pr.idPeminjamanRuangan
            WHERE pr.$peminjam_field = ?
            AND sp.statusPeminjaman = 'Menunggu Persetujuan'
            AND CONVERT(VARCHAR, pr.tglPeminjamanRuangan, 23) + ' ' + CONVERT(VARCHAR, pr.waktuMulai, 8) < GETDATE()";

            $updateParams = [$peminjam_value];
            sqlsrv_query($conn, $updateKedaluwarsaSql, $updateParams);
            // --- Modifikasi Kueri untuk Pencarian & Filter ---

            // Base query
            $baseCountQuery = "FROM Peminjaman_Ruangan pr 
                               JOIN Ruangan r ON pr.idRuangan = r.idRuangan 
                               LEFT JOIN Status_Peminjaman sp ON pr.idPeminjamanRuangan = sp.idPeminjamanRuangan 
                               WHERE pr.$peminjam_field = ?";
            $baseQuery = "FROM Peminjaman_Ruangan pr 
                          JOIN Ruangan r ON pr.idRuangan = r.idRuangan 
                          LEFT JOIN Status_Peminjaman sp ON pr.idPeminjamanRuangan = sp.idPeminjamanRuangan 
                          WHERE pr.$peminjam_field = ?";

            $countParams = [$peminjam_value];
            $params = [$peminjam_value];

            // Jika ada kata kunci pencarian, tambahkan kondisi LIKE
            if (!empty($searchTerm)) {
                $baseQuery .= " AND r.namaRuangan LIKE ?";
                $baseCountQuery .= " AND r.namaRuangan LIKE ?";
                $searchParam = "%" . $searchTerm . "%";
                $countParams[] = $searchParam;
                $params[] = $searchParam;
            }

            // Jika ada filter status, tambahkan kondisi
            if (!empty($filterStatus)) {
                $baseQuery .= " AND sp.statusPeminjaman = ?";
                $baseCountQuery .= " AND sp.statusPeminjaman = ?";
                $countParams[] = $filterStatus;
                $params[] = $filterStatus;
            }

            // Hitung total data (sudah termasuk filter)
            $countQuery = "SELECT COUNT(*) AS total " . $baseCountQuery;
            $countResult = sqlsrv_query($conn, $countQuery, $countParams);
            $countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
            $totalData = $countRow['total'];
            $totalPages = max(1, ceil($totalData / $perPage));

            // Ambil data sesuai halaman (sudah termasuk filter)
            $params[] = $offset;
            $params[] = $perPage;
            $query = "SELECT pr.*, r.namaRuangan, sp.statusPeminjaman " . $baseQuery .
                     " ORDER BY pr.tglPeminjamanRuangan DESC, pr.waktuMulai DESC OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
            $result = sqlsrv_query($conn, $query, $params);
        }
    }

    include __DIR__ . '/../../../templates/header.php';
    include __DIR__ . '/../../../templates/sidebar.php';
    ?>
    <main class="col bg-white px-3 px-md-4 py-3 position-relative">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h3 class="fw-semibold mb-0">Riwayat Peminjaman Ruangan</h3>
            <div class="d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-funnel"></i> Filter Status
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="?search=<?= htmlspecialchars($searchTerm) ?>">Semua Status</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="?status=Menunggu Persetujuan&search=<?= htmlspecialchars($searchTerm) ?>">Menunggu Persetujuan</a></li>
                        <li><a class="dropdown-item" href="?status=Menunggu Pengecekan&search=<?= htmlspecialchars($searchTerm) ?>">Menunggu Pengecekan</a></li>
                        <li><a class="dropdown-item" href="?status=Sedang Dipinjam&search=<?= htmlspecialchars($searchTerm) ?>">Sedang Dipinjam</a></li>
                        <li><a class="dropdown-item" href="?status=Telah Dikembalikan&search=<?= htmlspecialchars($searchTerm) ?>">Telah Dikembalikan</a></li>
                        <li><a class="dropdown-item" href="?status=Ditolak&search=<?= htmlspecialchars($searchTerm) ?>">Ditolak</a></li>
                        <li><a class="dropdown-item" href="?status=Kedaluwarsa&search=<?= htmlspecialchars($searchTerm) ?>">Kedaluwarsa</a></li>
                    </ul>
                </div>
                <form action="" method="GET" class="d-flex" role="search">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus) ?>">
                    <input type="text" name="search" class="form-control me-2" placeholder="Cari nama ruangan..." value="<?= htmlspecialchars($searchTerm) ?>" style="max-width: 250px;">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Riwayat Peminjaman Ruangan</li>
                </ol>
            </nav>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle table-bordered">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>No</th>
                        <th>Nama Ruangan</th>
                        <th>Tanggal Peminjaman</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Selesai</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = $offset + 1;
                if ($result === false) {
                    echo "<tr><td colspan='6' class='text-center text-danger'>Gagal mengambil data dari database " . print_r(sqlsrv_errors(), true) . "</td></tr>";
                } elseif (sqlsrv_has_rows($result) === false) {
                    $pesan = "Tidak ada data peminjaman ruangan.";
                    if (!empty($searchTerm) || !empty($filterStatus)) {
                        $pesan = "Data yang Anda cari tidak ditemukan.";
                    }
                    echo "<tr><td colspan='6' class='text-center'>$pesan</td></tr>";
                } else {
                    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        $statusPeminjaman = $row['statusPeminjaman'] ?? '';
                        $idPeminjaman = htmlspecialchars($row['idPeminjamanRuangan'] ?? '');
                        $linkDetail = "formDetailRiwayatRuangan.php?idPeminjamanRuangan=" . $idPeminjaman;

                        $now = new DateTime();
                        $terlambat = false;

                        if (
                            $statusPeminjaman === 'Sedang Dipinjam' &&
                            ($row['tglPeminjamanRuangan'] instanceof DateTime) &&
                            ($row['waktuSelesai'] instanceof DateTime) &&
                            $statusPeminjaman !== 'Telah Dikembalikan'
                        ) {
                            $tgl = $row['tglPeminjamanRuangan']->format('Y-m-d');
                            $jam = $row['waktuSelesai']->format('H:i:s');
                            $waktuSelesaiFull = new DateTime("$tgl $jam");
                            $terlambat = $now > $waktuSelesaiFull;
                        }

                        // Logika ikon status
                        if ($statusPeminjaman == 'Telah Dikembalikan') {
                            $iconSrc = BASE_URL . '/icon/centang.svg';
                            $altText = 'Peminjaman Selesai';
                        } elseif ($statusPeminjaman == 'Sedang Dipinjam') {
                            $iconSrc = BASE_URL . '/icon/jamKuning.svg';
                            $altText = 'Sedang Dipinjam';
                        } elseif ($statusPeminjaman == 'Menunggu Pengecekan') {
                            $iconSrc = BASE_URL . '/icon/jamHijau.svg';
                            $altText = 'Menunggu Pengecekan oleh PIC';
                        } elseif ($statusPeminjaman == 'Menunggu Persetujuan') {
                            $iconSrc = BASE_URL . '/icon/jamAbu.svg';
                            $altText = 'Menunggu Persetujuan oleh PIC';
                        } elseif ($statusPeminjaman == 'Ditolak') {
                            $iconSrc = BASE_URL . '/icon/silang.svg';
                            $altText = 'Ditolak';
                        } elseif ($statusPeminjaman == 'Kedaluwarsa') {
                            $iconSrc = BASE_URL . '/icon/jamMerah.svg';
                            $altText = 'Kedaluwarsa';
                        }
                ?>
                        <tr class="<?= $terlambat ? 'table-danger' : '' ?>">
                            <td class="text-center"><?= $no ?></td>
                            <td><?= htmlspecialchars($row['namaRuangan'] ?? '') ?></td>
                            <td class="text-center"><?= ($row['tglPeminjamanRuangan'] instanceof DateTime ? $row['tglPeminjamanRuangan']->format('d M Y') : htmlspecialchars($row['tglPeminjamanRuangan'] ?? '')) ?></td>
                            <td class="text-center"><?= ($row['waktuMulai'] instanceof DateTime ? $row['waktuMulai']->format('H:i') : htmlspecialchars($row['waktuMulai'] ?? '')) ?></td>
                            <td class="text-center"><?= ($row['waktuSelesai'] instanceof DateTime ? $row['waktuSelesai']->format('H:i') : htmlspecialchars($row['waktuSelesai'] ?? '')) ?></td>
                            <td class="td-aksi">
                                <a href="<?= $linkDetail ?>">
                                    <img src="<?= $iconSrc ?>" alt="<?= $altText ?>" class="aksi-icon" title="<?= $altText ?>">
                                </a>
                                <a href="<?= $linkDetail ?>">
                                    <img src="<?= BASE_URL ?>/icon/detail.svg" alt="Lihat Detail" class="aksi-icon">
                                </a>
                            </td>
                        </tr>
                <?php
                        $no++;
                    }
                    generatePagination($page, $totalPages);
                }
                ?>
                </tbody>
            </table>
        </div>
        <table class="legend-status">
            <tr>
                <td>
                    <p><img src="<?= BASE_URL ?>/icon/centang.svg" class="legend-icon"> : Telah Dikembalikan</p>
                </td>
                <td>
                    <p><img src="<?= BASE_URL ?>/icon/silang.svg" class="legend-icon"> : Ditolak</p>
                </td>
                <td>
                    <p><img src="<?= BASE_URL ?>/icon/jamhijau.svg" class="legend-icon"> : Menunggu Pengecekan</p>
                </td>
                <td>
                    <p><img src="<?= BASE_URL ?>/icon/jamkuning.svg" class="legend-icon"> : Sedang Dipinjam</p>
                </td>
                <td>
                    <p><img src="<?= BASE_URL ?>/icon/jamAbu.svg" class="legend-icon"> : Menunggu Persetujuan</p>
                </td>
                <td>
                    <p><img src="<?= BASE_URL ?>/icon/jamMerah.svg" class="legend-icon"> : Kedaluwarsa</p>
                </td>
            </tr>
        </table>
    </main>
    <?php
    include __DIR__ . '/../../../templates/footer.php';
    ?>