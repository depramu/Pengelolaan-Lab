<?php
require_once __DIR__ . '/../../../function/init.php';
require_once __DIR__ . '/../../../function/pagination.php';
authorize_role(['PIC Aset']);

// --- Tangkap parameter pencarian dan filter (menyesuaikan riwayatRuangan.php) ---
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';

$perPage = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$result = false;
$totalPages = 1;
$offset = ($page - 1) * $perPage;

// --- Query pencarian dan filter, menyesuaikan riwayatRuangan.php ---
$baseCountQuery = "FROM Peminjaman_Barang pb
                   JOIN Barang b ON pb.idBarang = b.idBarang
                   LEFT JOIN Status_Peminjaman sp ON pb.idPeminjamanBrg = sp.idPeminjamanBrg
                   LEFT JOIN Mahasiswa m ON pb.nim = m.nim
                   LEFT JOIN Karyawan k ON pb.npk = k.npk
                   WHERE 1=1";
$baseQuery = "FROM Peminjaman_Barang pb
              JOIN Barang b ON pb.idBarang = b.idBarang
              LEFT JOIN Status_Peminjaman sp ON pb.idPeminjamanBrg = sp.idPeminjamanBrg
              LEFT JOIN Mahasiswa m ON pb.nim = m.nim
              LEFT JOIN Karyawan k ON pb.npk = k.npk
              WHERE 1=1";

$countParams = [];
$params = [];

// Jika ada kata kunci pencarian, tambahkan kondisi LIKE
if (!empty($searchTerm)) {
    $baseQuery .= " AND b.namaBarang LIKE ?";
    $baseCountQuery .= " AND b.namaBarang LIKE ?";
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
$query = "SELECT pb.idPeminjamanBrg, pb.idBarang, pb.jumlahBrg, 
                 pb.tglPeminjamanBrg, sp.statusPeminjaman, b.namaBarang,
                 COALESCE(m.nama, k.nama) AS namaPeminjam
          " . $baseQuery . "
          ORDER BY pb.tglPeminjamanBrg DESC OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
$result = sqlsrv_query($conn, $query, $params);

if ($result === false) {
    echo "Error executing query: <br>";
    die(print_r(sqlsrv_errors(), true));
}

include __DIR__ . '/../../../templates/header.php';
include __DIR__ . '/../../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h3 class="fw-semibold mb-0">Peminjaman Barang</h3>
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-funnel"></i> Filter Status
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item<?= empty($filterStatus) ? ' active' : '' ?>" href="?search=<?= htmlspecialchars($searchTerm) ?>">Semua Status</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item<?= $filterStatus === 'Menunggu Persetujuan' ? ' active' : '' ?>" href="?status=Menunggu Persetujuan&search=<?= htmlspecialchars($searchTerm) ?>">Menunggu Persetujuan</a></li>
                    <li><a class="dropdown-item<?= $filterStatus === 'Sedang Dipinjam' ? ' active' : '' ?>" href="?status=Sedang Dipinjam&search=<?= htmlspecialchars($searchTerm) ?>">Sedang Dipinjam</a></li>
                    <li><a class="dropdown-item<?= $filterStatus === 'Sebagian Dikembalikan' ? ' active' : '' ?>" href="?status=Sebagian Dikembalikan&search=<?= htmlspecialchars($searchTerm) ?>">Sebagian Dikembalikan</a></li>
                    <li><a class="dropdown-item<?= $filterStatus === 'Telah Dikembalikan' ? ' active' : '' ?>" href="?status=Telah Dikembalikan&search=<?= htmlspecialchars($searchTerm) ?>">Telah Dikembalikan</a></li>
                    <li><a class="dropdown-item<?= $filterStatus === 'Ditolak' ? ' active' : '' ?>" href="?status=Ditolak&search=<?= htmlspecialchars($searchTerm) ?>">Ditolak</a></li>
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
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Peminjaman Barang</li>
            </ol>
        </nav>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
                <tr class="text-center">
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Nama Peminjam</th>
                    <th>Tanggal Peminjaman</th>
                    <th>Jumlah Peminjaman</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = $offset + 1;
                if ($result === false) {
                    echo "<tr><td colspan='6' class='text-center text-danger'>Gagal mengambil data dari database " . print_r(sqlsrv_errors(), true) . "</td></tr>";
                } elseif (sqlsrv_has_rows($result) === false) {
                    $pesan = "Tidak ada data peminjaman barang.";
                    if (!empty($searchTerm) || !empty($filterStatus)) {
                        $pesan = "Data yang Anda cari tidak ditemukan.";
                    }
                    echo "<tr><td colspan='6' class='text-center'>$pesan</td></tr>";
                } else {
                    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        $statusPeminjaman = $row['statusPeminjaman'] ?? '';
                        $idPeminjaman = htmlspecialchars($row['idPeminjamanBrg'] ?? '');
                        $terlambat = false;
                        if (
                            $statusPeminjaman === 'Sedang Dipinjam' &&
                            isset($row['tglPeminjamanBrg']) &&
                            $row['tglPeminjamanBrg'] instanceof DateTime
                        ) {
                            $deadline = clone $row['tglPeminjamanBrg'];
                            $deadline->setTime(23, 59, 59);
                            $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                            if ($now > $deadline) {
                                $terlambat = true;
                            }
                        }

                        // Penyesuaian link dan ikon aksi sesuai status
                        switch ($statusPeminjaman) {
                            case 'Menunggu Persetujuan':
                                $iconSrc = BASE_URL . '/icon/jamAbu.svg';
                                $altText = 'Menunggu Persetujuan oleh PIC';
                                $linkAksi = BASE_URL . '/Menu/Menu PIC/Peminjaman Barang/pengajuanBarang.php?id=' . $idPeminjaman;
                                $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Barang/pengajuanBarang.php?id=' . $idPeminjaman;
                                break;
                            case 'Sedang Dipinjam':
                                $iconSrc = BASE_URL . '/icon/jamkuning.svg';
                                $altText = 'Sedang Dipinjam';
                                $linkAksi = BASE_URL . '/Menu/Menu PIC/Peminjaman Barang/pengembalianBarang.php?id=' . $idPeminjaman;
                                $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Barang/pengembalianBarang.php?id=' . $idPeminjaman;
                                break;
                            case 'Sebagian Dikembalikan':
                                $iconSrc = BASE_URL . '/icon/jamhijau.svg';
                                $altText = 'Sebagian Dikembalikan';
                                $linkAksi = BASE_URL . '/Menu/Menu PIC/Peminjaman Barang/pengembalianBarang.php?id=' . $idPeminjaman;
                                $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Barang/pengembalianBarang.php?id=' . $idPeminjaman;
                                break;
                            case 'Ditolak':
                                $iconSrc = BASE_URL . '/icon/silang.svg';
                                $altText = 'Ditolak';
                                $linkAksi = BASE_URL . '/Menu/Menu PIC/Peminjaman Barang/detailPeminjamanBarang.php?id=' . $idPeminjaman;
                                $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Barang/detailPeminjamanBarang.php?id=' . $idPeminjaman;
                                break;
                            case 'Telah Dikembalikan':
                                $iconSrc = BASE_URL . '/icon/centang.svg';
                                $altText = 'Telah Dikembalikan';
                                $linkAksi = BASE_URL . '/Menu/Menu PIC/Peminjaman Barang/detailPeminjamanBarang.php?id=' . $idPeminjaman;
                                $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Barang/detailPeminjamanBarang.php?id=' . $idPeminjaman;
                                break;
                            default:
                                $iconSrc = BASE_URL . '/icon/jamkuning.svg';
                                $altText = 'Status Tidak Diketahui';
                                $linkAksi = '#';
                                $linkDetail = '#';
                                break;
                        }
                ?>
                    <tr class="<?= $terlambat ? 'table-danger' : '' ?> text-center">
                        <td><?= $no ?></td>
                        <td class="text-start"><?= htmlspecialchars($row['namaBarang'] ?? '') ?></td>
                        <td class="text-start"><?= htmlspecialchars($row['namaPeminjam'] ?? '') ?></td>
                        <td><?= ($row['tglPeminjamanBrg'] instanceof DateTime ? $row['tglPeminjamanBrg']->format('d M Y') : htmlspecialchars($row['tglPeminjamanBrg'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($row['jumlahBrg'] ?? '') ?></td>
                        <td class="td-aksi">
                            <a href="<?= $linkAksi ?>">
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
        </tr>
    </table>
    <?php
    generatePagination($page, $totalPages);
    ?>
</main>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>