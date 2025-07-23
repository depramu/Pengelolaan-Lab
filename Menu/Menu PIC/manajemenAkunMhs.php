<?php
require_once __DIR__ . '/../../function/init.php';
authorize_role('PIC Aset');

// --- Tangkap parameter pencarian ---
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

// Pagination setup
$perPage = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Hitung total data (dengan filter pencarian jika ada)
$baseCountQuery = "FROM Mahasiswa WHERE isDeleted = 0";
$countParams = [];
if (!empty($searchTerm)) {
    $baseCountQuery .= " AND (nim LIKE ? OR nama LIKE ? OR email LIKE ?)";
    $likeTerm = "%" . $searchTerm . "%";
    $countParams = [$likeTerm, $likeTerm, $likeTerm];
}
$countQuery = "SELECT COUNT(*) AS total " . $baseCountQuery;
$countResult = sqlsrv_query($conn, $countQuery, $countParams);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'];
$totalPages = max(1, ceil($totalData / $perPage));

// Ambil data sesuai halaman (dengan filter pencarian jika ada)
$offset = ($page - 1) * $perPage;
$baseQuery = "FROM Mahasiswa WHERE isDeleted = 0";
$params = [];
if (!empty($searchTerm)) {
    $baseQuery .= " AND (nim LIKE ? OR nama LIKE ? OR email LIKE ?)";
    $likeTerm = "%" . $searchTerm . "%";
    $params = [$likeTerm, $likeTerm, $likeTerm];
}
$query = "SELECT nim, nama, email, jenisRole " . $baseQuery .
    " ORDER BY nim OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
$params[] = $offset;
$params[] = $perPage;
$result = sqlsrv_query($conn, $query, $params);
$currentPage = basename($_SERVER['PHP_SELF']);

include '../../templates/header.php';
include '../../templates/sidebar.php';
?>
<!-- Content Area -->
<main class="col bg-white px-4 py-3 position-relative">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h3 class="fw-semibold mb-0">Manajemen Akun Mahasiswa</h3>
        <form action="" method="GET" class="d-flex" role="search">
            <input type="text" name="search" class="form-control me-2" placeholder="Cari NIM, Nama, atau Email..." value="<?= htmlspecialchars($searchTerm) ?>" style="max-width: 250px;">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manajemen Akun Mahasiswa</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
        <a href="<?= BASE_URL ?>/CRUD/Akun/tambahAkunMhs.php" class="btn btn-primary">
            <img src="<?= BASE_URL ?>/icon/tambah.svg" alt="Tambah Akun" class="me-2">
            Tambah Akun
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
                <tr class="text-center">
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hasData = false;
                $no = $offset + 1;
                if ($result === false) {
                    echo '<tr><td colspan="6" class="text-center">Terjadi kesalahan saat mengambil data.</td></tr>';
                } elseif (sqlsrv_has_rows($result) === false) {
                    $pesan = "Tidak ada data mahasiswa.";
                    if (!empty($searchTerm)) {
                        $pesan = "Data yang Anda cari tidak ditemukan.";
                    }
                    echo "<tr><td colspan='6' class='text-center'>$pesan</td></tr>";
                } else {
                    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        $hasData = true;
                ?>
                        <tr class="text-center">
                            <td><?= $no ?></td>
                            <td><?= htmlspecialchars($row['nim']) ?></td>
                            <td class="text-start"><?= htmlspecialchars($row['nama']) ?></td>
                            <td class="text-start"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="text-start"><?= htmlspecialchars($row['jenisRole']) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>/CRUD/Akun/editAkunMhs.php?id=<?= $row['nim'] ?>"><img src="<?= BASE_URL ?>/icon/edit.svg" alt="editAkun" style="width: 20px; height: 20px; margin-bottom: 5px; margin-right: 0px;"></a>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['nim'] ?>"><img src="<?= BASE_URL ?>/icon/hapus.svg" alt="hapusAkun" style="width: 20px; height: 20px; margin-bottom: 5px; margin-right: 0px;"></a>

                                <!-- delete -->
                                <div class="modal fade" id="deleteModal<?= $row['nim'] ?>"
                                    tabindex="-1" aria-labelledby="modalLabel<?= $row['nim'] ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="../../CRUD/Akun/hapusAkunMhs.php" method="POST">
                                            <input type="hidden" name="nim" value="<?= $row['nim'] ?>">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalLabel<?= $row['nim'] ?>">Konfirmasi Hapus</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus akun <br>"<strong><?= htmlspecialchars($row['nama']) ?></strong>"?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Ya, hapus</button>
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
