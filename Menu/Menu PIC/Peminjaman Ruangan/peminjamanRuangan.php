<?php
require_once __DIR__ . '/../../../function/init.php';
require_once __DIR__ . '/../../../function/pagination.php';
authorize_role(['PIC Aset']);

// --- Tangkap parameter pencarian dan filter (menyesuaikan riwayatRuangan.php) ---
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';

// Hard cap: maksimum 7 data per halaman
$perPage = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$result = false;
$totalPages = 1;
$offset = ($page - 1) * $perPage;

// --- Query pencarian dan filter, menyesuaikan riwayatRuangan.php ---
$baseCountQuery = "FROM Peminjaman_Ruangan pr
                   JOIN Ruangan r ON pr.idRuangan = r.idRuangan
                   LEFT JOIN Status_Peminjaman sp ON pr.idPeminjamanRuangan = sp.idPeminjamanRuangan
                   LEFT JOIN Mahasiswa m ON pr.nim = m.nim
                   LEFT JOIN Karyawan k ON pr.npk = k.npk
                   WHERE 1=1";
$baseQuery = "FROM Peminjaman_Ruangan pr
              JOIN Ruangan r ON pr.idRuangan = r.idRuangan
              LEFT JOIN Status_Peminjaman sp ON pr.idPeminjamanRuangan = sp.idPeminjamanRuangan
              LEFT JOIN Mahasiswa m ON pr.nim = m.nim
              LEFT JOIN Karyawan k ON pr.npk = k.npk
              WHERE 1=1";

$countParams = [];
$params = [];

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
$query = "SELECT pr.*, r.namaRuangan, sp.statusPeminjaman, COALESCE(m.nama, k.nama) AS namaPeminjam "
       . $baseQuery .
       " ORDER BY pr.tglPeminjamanRuangan DESC, pr.waktuMulai DESC OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
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
        <h3 class="fw-semibold mb-0">Peminjaman Ruangan</h3>
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
                    <th>Nama Peminjam</th>
                    <th>Tanggal Peminjaman</th>
                    <th>Waktu Mulai</th>
                    <th>Waktu Selesai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $no = $offset + 1;
            if ($result === false) {
                echo "<tr><td colspan='7' class='text-center text-danger'>Gagal mengambil data dari database " . print_r(sqlsrv_errors(), true) . "</td></tr>";
            } elseif (sqlsrv_has_rows($result) === false) {
                $pesan = "Tidak ada data peminjaman ruangan.";
                if (!empty($searchTerm) || !empty($filterStatus)) {
                    $pesan = "Data yang Anda cari tidak ditemukan.";
                }
                echo "<tr><td colspan='7' class='text-center'>$pesan</td></tr>";
            } else {
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    $statusPeminjaman = $row['statusPeminjaman'] ?? '';
                    $idPeminjaman = htmlspecialchars($row['idPeminjamanRuangan'] ?? '');

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

                    // Penyesuaian link dan ikon aksi sesuai status
                    switch ($statusPeminjaman) {
                        case 'Menunggu Persetujuan':
                            $iconSrc = BASE_URL . '/icon/jamAbu.svg';
                            $altText = 'Menunggu Persetujuan oleh PIC';
                            $linkAksi = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/pengajuanRuangan.php?id=' . $idPeminjaman;
                            $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/pengajuanRuangan.php?id=' . $idPeminjaman;
                            break;
                        case 'Menunggu Pengecekan':
                            $iconSrc = BASE_URL . '/icon/jamhijau.svg';
                            $altText = 'Menunggu Pengecekan oleh PIC';
                            $linkAksi = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/pengembalianRuangan.php?id=' . $idPeminjaman;
                            $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/pengembalianRuangan.php?id=' . $idPeminjaman;
                            break;
                        case 'Sedang Dipinjam':
                            $iconSrc = BASE_URL . '/icon/jamkuning.svg';
                            $altText = 'Sedang Dipinjam';
                            $linkAksi = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/detailPeminjamanRuangan.php?id=' . $idPeminjaman;
                            $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/detailPeminjamanRuangan.php?id=' . $idPeminjaman;
                            break;
                        case 'Ditolak':
                            $iconSrc = BASE_URL . '/icon/silang.svg';
                            $altText = 'Ditolak';
                            $linkAksi = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/detailPeminjamanRuangan.php?id=' . $idPeminjaman;
                            $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/detailPeminjamanRuangan.php?id=' . $idPeminjaman;
                            break;
                        case 'Telah Dikembalikan':
                            $iconSrc = BASE_URL . '/icon/centang.svg';
                            $altText = 'Telah Dikembalikan';
                            $linkAksi = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/detailPeminjamanRuangan.php?id=' . $idPeminjaman;
                            $linkDetail = BASE_URL . '/Menu/Menu PIC/Peminjaman Ruangan/detailPeminjamanRuangan.php?id=' . $idPeminjaman;
                            break;
                        default:
                            $iconSrc = BASE_URL . '/icon/jamKuning.svg';
                            $altText = 'Status Tidak Diketahui';
                            $linkAksi = '#';
                            $linkDetail = '#';
                            break;
                    }
            ?>
                <tr class="<?= $terlambat ? 'table-danger' : '' ?> text-center">
                    <td><?= $no ?></td>
                    <td class="text-start"><?= htmlspecialchars($row['namaRuangan']) ?></td>
                    <td class="text-start"><?= htmlspecialchars($row['namaPeminjam']) ?></td>
                    <td>
                        <?= ($row['tglPeminjamanRuangan'] instanceof DateTime ? $row['tglPeminjamanRuangan']->format('d M Y') : htmlspecialchars($row['tglPeminjamanRuangan'] ?? '')) ?>
                    </td>
                    <td><?= ($row['waktuMulai'] instanceof DateTimeInterface) ? $row['waktuMulai']->format('H:i') : 'N/A'; ?></td>
                    <td><?= ($row['waktuSelesai'] instanceof DateTimeInterface) ? $row['waktuSelesai']->format('H:i') : 'N/A'; ?></td>
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
                <p><img src="<?= BASE_URL ?>/icon/jamhijau.svg" class="legend-icon"> : Menunggu Pengecekan</p>
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