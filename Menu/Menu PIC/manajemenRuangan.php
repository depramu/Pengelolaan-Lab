<?php
require_once __DIR__ . '/../../function/init.php';
authorize_role(['PIC Aset']);

// --- Tangkap parameter pencarian dan filter ketersediaan & kondisi ---
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$filterKetersediaan = isset($_GET['ketersediaan']) ? $_GET['ketersediaan'] : '';
$filterKondisi = isset($_GET['kondisi']) ? $_GET['kondisi'] : '';

// Ambil daftar ketersediaan unik untuk dropdown filter
$ketersediaanList = [];
$ketersediaanQuery = "SELECT DISTINCT ketersediaan FROM Ruangan WHERE isDeleted = 0 ORDER BY ketersediaan ASC";
$ketersediaanResult = sqlsrv_query($conn, $ketersediaanQuery);
if ($ketersediaanResult !== false) {
    while ($rowKet = sqlsrv_fetch_array($ketersediaanResult, SQLSRV_FETCH_ASSOC)) {
        if (!empty($rowKet['ketersediaan'])) {
            $ketersediaanList[] = $rowKet['ketersediaan'];
        }
    }
}

// Ambil daftar kondisi unik untuk dropdown filter
$kondisiList = [];
$kondisiQuery = "SELECT DISTINCT kondisiRuangan FROM Ruangan WHERE isDeleted = 0 ORDER BY kondisiRuangan ASC";
$kondisiResult = sqlsrv_query($conn, $kondisiQuery);
if ($kondisiResult !== false) {
    while ($rowKon = sqlsrv_fetch_array($kondisiResult, SQLSRV_FETCH_ASSOC)) {
        if (!empty($rowKon['kondisiRuangan'])) {
            $kondisiList[] = $rowKon['kondisiRuangan'];
        }
    }
}

// Pagination setup
$perPage = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Hitung total data (sudah termasuk filter pencarian dan filter ketersediaan & kondisi)
$baseCountQuery = "FROM Ruangan WHERE isDeleted = 0";
$countParams = [];
if (!empty($searchTerm)) {
    $baseCountQuery .= " AND namaRuangan LIKE ?";
    $countParams[] = "%" . $searchTerm . "%";
}
if (!empty($filterKetersediaan)) {
    $baseCountQuery .= " AND ketersediaan = ?";
    $countParams[] = $filterKetersediaan;
}
if (!empty($filterKondisi)) {
    $baseCountQuery .= " AND kondisiRuangan = ?";
    $countParams[] = $filterKondisi;
}
$countQuery = "SELECT COUNT(*) AS total " . $baseCountQuery;
$countResult = sqlsrv_query($conn, $countQuery, $countParams);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'];
$totalPages = max(1, ceil($totalData / $perPage));

// Ambil data sesuai halaman (sudah termasuk filter pencarian dan filter ketersediaan & kondisi)
$offset = ($page - 1) * $perPage;
$baseQuery = "FROM Ruangan WHERE isDeleted = 0";
$params = [];
if (!empty($searchTerm)) {
    $baseQuery .= " AND namaRuangan LIKE ?";
    $params[] = "%" . $searchTerm . "%";
}
if (!empty($filterKetersediaan)) {
    $baseQuery .= " AND ketersediaan = ?";
    $params[] = $filterKetersediaan;
}
if (!empty($filterKondisi)) {
    $baseQuery .= " AND kondisiRuangan = ?";
    $params[] = $filterKondisi;
}
$query = "SELECT idRuangan, namaRuangan, kondisiRuangan, ketersediaan " . $baseQuery .
         " ORDER BY idRuangan OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
$params[] = $offset;
$params[] = $perPage;
$result = sqlsrv_query($conn, $query, $params);
if ($result === false) {
    echo "Error executing query: <br>";
    die(print_r(sqlsrv_errors(), true));
}

include '../../templates/header.php';
include '../../templates/sidebar.php';
?>
<main class="col bg-white px-4 py-3 position-relative">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h3 class="fw-semibold mb-0">Manajemen Ruangan</h3>
        <div class="d-flex align-items-center gap-2">
            <!-- Filter Ketersediaan -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuKetersediaan" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-funnel"></i> Filter Ketersediaan
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuKetersediaan">
                    <li>
                        <a class="dropdown-item" href="?search=<?= htmlspecialchars($searchTerm) ?>&kondisi=<?= urlencode($filterKondisi) ?>">Semua Ketersediaan</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <?php foreach ($ketersediaanList as $ketersediaan): ?>
                        <li>
                            <a class="dropdown-item" href="?ketersediaan=<?= urlencode($ketersediaan) ?>&kondisi=<?= urlencode($filterKondisi) ?>&search=<?= htmlspecialchars($searchTerm) ?>">
                                <?= htmlspecialchars($ketersediaan) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <!-- Filter Kondisi -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuKondisi" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-funnel"></i> Filter Kondisi
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuKondisi">
                    <li>
                        <a class="dropdown-item" href="?search=<?= htmlspecialchars($searchTerm) ?>&ketersediaan=<?= urlencode($filterKetersediaan) ?>">Semua Kondisi</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <?php foreach ($kondisiList as $kondisi): ?>
                        <li>
                            <a class="dropdown-item" href="?kondisi=<?= urlencode($kondisi) ?>&ketersediaan=<?= urlencode($filterKetersediaan) ?>&search=<?= htmlspecialchars($searchTerm) ?>">
                                <?= htmlspecialchars($kondisi) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <!-- Form Pencarian -->
            <form action="" method="GET" class="d-flex" role="search">
                <input type="hidden" name="ketersediaan" value="<?= htmlspecialchars($filterKetersediaan) ?>">
                <input type="hidden" name="kondisi" value="<?= htmlspecialchars($filterKondisi) ?>">
                <input type="text" name="search" class="form-control me-2" placeholder="Cari nama ruangan..." value="<?= htmlspecialchars($searchTerm) ?>" style="max-width: 200px;">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </div>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manajemen Ruangan</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex justify-content-start mb-2">
        <a href="<?= BASE_URL ?>/CRUD/Ruangan/tambahRuangan.php" class="btn btn-primary">
            <img src="<?= BASE_URL ?>/icon/tambah.svg" alt="tambah" class="me-2">Tambah Ruangan</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
                <tr class="text-center">
                    <th>No</th>
                    <th>Nama Ruangan</th>
                    <th>Kondisi</th>
                    <th>Ketersediaan</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hasData = false;
                $no = $offset + 1;
                if ($result === false) {
                    echo '<tr><td colspan="5" class="text-center">Terjadi kesalahan saat mengambil data.</td></tr>';
                } elseif (sqlsrv_has_rows($result) === false) {
                    $pesan = "Tidak ada data ruangan.";
                    if (!empty($searchTerm) || !empty($filterKetersediaan) || !empty($filterKondisi)) {
                        $pesan = "Data yang Anda cari tidak ditemukan.";
                    }
                    echo "<tr><td colspan='5' class='text-center'>$pesan</td></tr>";
                } else {
                    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        $hasData = true;
                ?>
                    <tr class="text-center">
                        <td><?= $no ?></td>
                        <td class="text-start"><?= htmlspecialchars($row['namaRuangan']) ?></td>
                        <td><?= htmlspecialchars($row['kondisiRuangan']) ?></td>
                        <td><?= htmlspecialchars($row['ketersediaan']) ?></td>
                        <td class="text-center">
                            <a href="<?= BASE_URL ?>/CRUD/Ruangan/editRuangan.php?id=<?= $row['idRuangan'] ?>">
                                <img src="<?= BASE_URL ?>/icon/edit.svg" alt="Edit" style="width: 20px; height: 20px; margin-bottom: 5px; margin-right: 10px;">
                            </a>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['idRuangan'] ?>">
                                <img src="<?= BASE_URL ?>/icon/hapus.svg" alt="Hapus" style="width: 20px; height: 20px; margin-bottom: 5px; margin-right: 10px;">
                            </a>

                            <div class="modal fade" id="deleteModal<?= $row['idRuangan'] ?>"
                                tabindex="-1" aria-labelledby="modalLabel<?= $row['idRuangan'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="<?= BASE_URL ?>/CRUD/Ruangan/hapusRuangan.php" method="POST">
                                        <input type="hidden" name="idRuangan" value="<?= $row['idRuangan'] ?>">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalLabel<?= $row['idRuangan'] ?>">Konfirmasi Hapus</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus Ruangan "<strong><?= htmlspecialchars($row['namaRuangan']) ?></strong>"?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
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
    <?php
    generatePagination($page, $totalPages);
    ?>
</main>

<?php
include '../../templates/footer.php';
?>