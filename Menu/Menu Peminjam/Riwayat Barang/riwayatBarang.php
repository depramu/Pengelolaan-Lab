<?php
require_once __DIR__ . '/../../../function/init.php';
authorize_role(['Peminjam']);

// --- Tangkap parameter pencarian dan filter ---
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
// BARU: Tangkap parameter filter status
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';

// Pagination setup
$perPage = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Otomatis perbarui status Kedaluwarsa untuk peminjaman yang belum disetujui dan sudah melewati waktu mulai
$updateKedaluwarsaSql = "UPDATE sp
SET sp.statusPeminjaman = 'Kedaluwarsa'
FROM Status_Peminjaman sp
JOIN Peminjaman_Barang pb ON sp.idPeminjamanBrg = pb.idPeminjamanBrg
WHERE sp.statusPeminjaman = 'Menunggu Persetujuan'
AND (CAST(pb.tglPeminjamanBrg AS DATETIME) < GETDATE())";
sqlsrv_query($conn, $updateKedaluwarsaSql);

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
        // --- Modifikasi Kueri untuk Pencarian & Filter ---

        // Base query
        $baseCountQuery = "FROM Peminjaman_Barang pb JOIN Barang b ON pb.idBarang = b.idBarang LEFT JOIN Status_Peminjaman sp ON pb.idPeminjamanBrg = sp.idPeminjamanBrg WHERE pb.$peminjam_field = ?";
        $baseQuery = "FROM Peminjaman_Barang pb 
                      JOIN Barang b ON pb.idBarang = b.idBarang 
                      LEFT JOIN Status_Peminjaman sp ON pb.idPeminjamanBrg = sp.idPeminjamanBrg
                      WHERE pb.$peminjam_field = ?";

        $countParams = [$peminjam_value];
        $params = [$peminjam_value];

        // Jika ada kata kunci pencarian, tambahkan kondisi LIKE
        if (!empty($searchTerm)) {
            $baseQuery .= " AND b.namaBarang LIKE ?";
            $baseCountQuery .= " AND b.namaBarang LIKE ?";
            $searchParam = "%" . $searchTerm . "%";
            $countParams[] = $searchParam;
            $params[] = $searchParam;
        }

        // BARU: Jika ada filter status, tambahkan kondisi
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
        $totalPages = ceil($totalData / $perPage);

        // Ambil data sesuai halaman (sudah termasuk filter)
        $offset = ($page - 1) * $perPage;
        $query = "SELECT pb.idPeminjamanBrg, pb.idBarang, pb.tglPeminjamanBrg, pb.jumlahBrg, sp.statusPeminjaman, b.namaBarang "
               . $baseQuery
               . " ORDER BY pb.tglPeminjamanBrg DESC OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
        
        $params[] = $offset;
        $params[] = $perPage;

        $result = sqlsrv_query($conn, $query, $params);
    }
}

include __DIR__ . '/../../../templates/header.php';
include __DIR__ . '/../../../templates/sidebar.php';
?>
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h3 class="fw-semibold mb-0">Riwayat Peminjaman Barang</h3>

        <div class="d-flex align-items-center gap-2">
            
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-funnel"></i> Filter Status
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item" href="?search=<?= htmlspecialchars($searchTerm) ?>">Semua Status</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="?status=Menunggu Persetujuan&search=<?= htmlspecialchars($searchTerm) ?>">Menunggu Persetujuan</a></li>
                    <li><a class="dropdown-item" href="?status=Sedang Dipinjam&search=<?= htmlspecialchars($searchTerm) ?>">Sedang Dipinjam</a></li>
                    <li><a class="dropdown-item" href="?status=Sebagian Dikembalikan&search=<?= htmlspecialchars($searchTerm) ?>">Sebagian Dikembalikan</a></li>
                    <li><a class="dropdown-item" href="?status=Telah Dikembalikan&search=<?= htmlspecialchars($searchTerm) ?>">Telah Dikembalikan</a></li>
                    <li><a class="dropdown-item" href="?status=Ditolak&search=<?= htmlspecialchars($searchTerm) ?>">Ditolak</a></li>
                    <li><a class="dropdown-item" href="?status=Kedaluwarsa&search=<?= htmlspecialchars($searchTerm) ?>">Kedaluwarsa</a></li>
                </ul>
            </div>

            <form action="" method="GET" class="d-flex" role="search">
                <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus) ?>">
                <input type="text" name="search" class="form-control me-2" placeholder="Cari nama barang..." value="<?= htmlspecialchars($searchTerm) ?>" style="max-width: 250px;">
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
                <li class="breadcrumb-item active" aria-current="page">Riwayat Peminjaman Barang</li>
            </ol>
        </nav>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle table-bordered">
        <thead class="table-light">
                <tr class="text-center">
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Tanggal Peminjaman</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = $offset + 1;
                if ($result === false) {
                    // ... (error handling)
                } elseif (sqlsrv_has_rows($result) === false) {
                    $pesan = "Tidak ada data peminjaman barang.";
                    if (!empty($searchTerm) || !empty($filterStatus)) {
                        $pesan = "Data yang Anda cari tidak ditemukan.";
                    }
                    echo "<tr><td colspan='5' class='text-center'>$pesan</td></tr>";
                } else {
                    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        $statusPeminjaman = $row['statusPeminjaman'] ?? '';
                        $idPeminjaman = htmlspecialchars($row['idPeminjamanBrg'] ?? '');
                        $linkDetail = "formDetailRiwayatBrg.php?idPeminjamanBrg=" . $idPeminjaman;

                        // Logika ikon status (tidak ada perubahan)
                        if ($statusPeminjaman == 'Telah Dikembalikan') {
                            $iconSrc = BASE_URL . '/icon/centang.svg';
                            $altText = 'Telah Dikembalikan';
                        } elseif ($statusPeminjaman == 'Sedang Dipinjam') {
                            $iconSrc = BASE_URL . '/icon/jamKuning.svg';
                            $altText = 'Sedang Dipinjam';
                        } elseif ($statusPeminjaman == 'Sebagian Dikembalikan') {
                            $iconSrc = BASE_URL . '/icon/jamHijau.svg';
                            $altText = 'Sebagian Dikembalikan';
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
                        <tr class="text-center">
                            <td><?= $no ?></td>
                            <td class="text-start"><?= htmlspecialchars($row['namaBarang'] ?? '') ?></td>
                            <td><?= ($row['tglPeminjamanBrg'] instanceof DateTime ? $row['tglPeminjamanBrg']->format('d M Y') : htmlspecialchars($row['tglPeminjamanBrg'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($row['jumlahBrg'] ?? '') ?></td>
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
                <p><img src="<?= BASE_URL ?>/icon/jamhijau.svg" class="legend-icon"> : Sebagian Dikembalikan</p>
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
<?php include __DIR__ . '/../../../templates/footer.php'; ?>