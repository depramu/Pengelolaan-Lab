<?php
require_once __DIR__ . '/../../function/init.php';
authorize_role(['PIC Aset']);

// --- Tangkap parameter pencarian dan filter lokasi ---
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$filterLokasi = isset($_GET['lokasi']) ? $_GET['lokasi'] : '';

// Ambil daftar lokasi unik untuk dropdown filter
$lokasiList = [];
$lokasiQuery = "SELECT DISTINCT lokasiBarang FROM Barang WHERE isDeleted = 0 ORDER BY lokasiBarang ASC";
$lokasiResult = sqlsrv_query($conn, $lokasiQuery);
if ($lokasiResult !== false) {
    while ($rowLokasi = sqlsrv_fetch_array($lokasiResult, SQLSRV_FETCH_ASSOC)) {
        if (!empty($rowLokasi['lokasiBarang'])) {
            $lokasiList[] = $rowLokasi['lokasiBarang'];
        }
    }
}

// Pagination setup
$perPage = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Hitung total data (sudah termasuk filter pencarian dan lokasi)
$baseCountQuery = "FROM Barang WHERE isDeleted = 0";
$countParams = [];
if (!empty($searchTerm)) {
    $baseCountQuery .= " AND namaBarang LIKE ?";
    $countParams[] = "%" . $searchTerm . "%";
}
if (!empty($filterLokasi)) {
    $baseCountQuery .= " AND lokasiBarang = ?";
    $countParams[] = $filterLokasi;
}
$countQuery = "SELECT COUNT(*) AS total " . $baseCountQuery;
$countResult = sqlsrv_query($conn, $countQuery, $countParams);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'];
$totalPages = max(1, ceil($totalData / $perPage));

// Ambil data sesuai halaman (sudah termasuk filter pencarian dan lokasi)
$offset = ($page - 1) * $perPage;
$baseQuery = "FROM Barang WHERE isDeleted = 0";
$params = [];
if (!empty($searchTerm)) {
    $baseQuery .= " AND namaBarang LIKE ?";
    $params[] = "%" . $searchTerm . "%";
}
if (!empty($filterLokasi)) {
    $baseQuery .= " AND lokasiBarang = ?";
    $params[] = $filterLokasi;
}
$query = "SELECT idBarang, namaBarang, stokBarang, lokasiBarang " . $baseQuery .
         " ORDER BY idBarang OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
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
        <h3 class="fw-semibold mb-0">Manajemen Barang</h3>
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuLokasi" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-funnel"></i> Filter Lokasi
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLokasi">
                    <li>
                        <a class="dropdown-item" href="?search=<?= htmlspecialchars($searchTerm) ?>">Semua Lokasi</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <?php foreach ($lokasiList as $lokasi): ?>
                        <li>
                            <a class="dropdown-item" href="?lokasi=<?= urlencode($lokasi) ?>&search=<?= htmlspecialchars($searchTerm) ?>">
                                <?= htmlspecialchars($lokasi) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <form action="" method="GET" class="d-flex" role="search">
                <input type="hidden" name="lokasi" value="<?= htmlspecialchars($filterLokasi) ?>">
                <input type="text" name="search" class="form-control me-2" placeholder="Cari nama barang..." value="<?= htmlspecialchars($searchTerm) ?>" style="max-width: 200px;">
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
                <li class="breadcrumb-item active" aria-current="page">Manajemen Barang</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex justify-content-start mb-2">
        <a href="<?= BASE_URL ?>/CRUD/Barang/tambahBarang.php" class="btn btn-primary">
            <img src="<?= BASE_URL ?>/icon/tambah.svg" alt="tambah" class="me-2">Tambah Barang</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
                <tr class="text-center">
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Stok Barang</th>
                    <th>Lokasi Barang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hasData = false;
                $no = $offset + 1;
                if ($result === false) {
                    echo '<tr><td colspan="5" class="text-center">Terjadi kesalahan saat mengambil data.</td></tr>';
                } elseif (sqlsrv_has_rows($result) === false) {
                    $pesan = "Tidak ada data barang.";
                    if (!empty($searchTerm) || !empty($filterLokasi)) {
                        $pesan = "Data yang Anda cari tidak ditemukan.";
                    }
                    echo "<tr><td colspan='5' class='text-center'>$pesan</td></tr>";
                } else {
                    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        $hasData = true;
                ?>
                    <tr class="text-center">
                        <td><?= $no ?></td>
                        <td class="text-start"><?= htmlspecialchars($row['namaBarang']) ?></td>
                        <td><?= htmlspecialchars($row['stokBarang']) ?></td>
                        <td><?= htmlspecialchars($row['lokasiBarang']) ?></td>
                        <td class="text-center">
                            <a href="<?= BASE_URL ?>/CRUD/Barang/editBarang.php?id=<?= $row['idBarang'] ?>"><img src="<?= BASE_URL ?>/icon/edit.svg" alt="" style="width: 20px; height: 20px; margin-bottom: 5px; margin-right: 10px;"></a>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['idBarang'] ?>"><img src="<?= BASE_URL ?>/icon/hapus.svg" alt="" style="width: 20px; height: 20px; margin-bottom: 5px; margin-right: 10px;"></a>

                            <div class="modal fade" id="deleteModal<?= $row['idBarang'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $row['idBarang'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">

                                    <form action="<?= BASE_URL ?>/CRUD/Barang/hapusBarang.php" method="POST">
                                        <input type="hidden" name="idBarang" value="<?= $row['idBarang'] ?>">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalLabel<?= $row['idBarang'] ?>">Konfirmasi Hapus</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus Barang "<strong><?= htmlspecialchars($row['namaBarang']) ?></strong>"
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