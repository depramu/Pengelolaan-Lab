<?php
include '../templates/header.php';

// Pagination setup
$perPage = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Hitung total data
$countQuery = "SELECT COUNT(*) AS total FROM Karyawan";
$countResult = sqlsrv_query($conn, $countQuery);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'];
$totalPages = ceil($totalData / $perPage);

// Ambil data sesuai halaman
$offset = ($page - 1) * $perPage;
$query = "SELECT npk, nama, email, jenisRole FROM Karyawan ORDER BY npk OFFSET $offset ROWS FETCH NEXT $perPage ROWS ONLY";
$result = sqlsrv_query($conn, $query);
$currentPage = basename($_SERVER['PHP_SELF']); // Determine the current page

include '../templates/sidebar.php';
?>
<!-- Content Area -->
<main class="col bg-white px-4 py-3 position-relative">
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manajemen Akun Karyawan</li>
            </ol>
        </nav>
    </div>

    <!-- Table Manajemen Akun Karyawan -->
    <div class="d-flex justify-content-start mb-2">
        <a href="../CRUD/Akun/tambahAkunKry.php" class="btn btn-primary">
            <img src="../icon/tambah.svg" alt="tambahAkun" class="me-1">
            Tambah Akun</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
                <tr>
                    <th>NPK</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hasData = false; // Flag to check if there is data
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    $hasData = true; // Set flag to true if data is found
                ?>
                    <tr>
                        <td><?= $row['npk'] ?></td>
                        <td><?= $row['nama'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['jenisRole'] ?></td>
                        <td class="text-center">
                            <a href="../CRUD/Akun/editAkunKry.php?id=<?= $row['npk'] ?>"><img src="../icon/edit.svg" alt="" style=" width: 20px; height: 20px; margin-bottom: 5px; margin-right: 0px;"></a>
                            <a href="../CRUD/Akun/hapusAkunKry.php?id=<?= $row['npk'] ?>" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['npk'] ?>"><img src="../icon/hapus.svg" alt="" style="width: 20px; height: 20px; margin-bottom: 5px; margin-right: 0px;"></a>

                            <!-- delete -->
                            <div class="modal fade" id="deleteModal<?= $row['npk'] ?>"
                                tabindex="-1" aria-labelledby="modalLabel<?= $row['npk'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="../CRUD/Akun/hapusAkunKry.php" method="POST">
                                        <input type="hidden" name="npk" value="<?= $row['npk'] ?>">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalLabel<?= $row['npk'] ?>">Konfirmasi Hapus</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus akun? "<strong><?= htmlspecialchars($row['nama']) ?></strong>"?
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
                }
                if (!$hasData) {
                    echo '<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>';
                }
                ?>
            </tbody>
        </table>
        <!-- Pagination -->
        <nav aria-label="Page navigation" class="fixed-pagination">
            <ul class="pagination justify-content-end">
                <!-- Previous button -->
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>" tabindex="-1">&lt;</a>
                </li>
                <!-- Page numbers -->
                <?php
                $showPages = 3; // Jumlah halaman yang selalu tampil di awal dan akhir
                $ellipsisShown = false;
                for ($i = 1; $i <= $totalPages; $i++) {
                    if (
                        $i <= $showPages || // always show first 3
                        $i > $totalPages - $showPages || // always show last 3
                        abs($i - $page) <= 1 // show current, previous, next
                    ) {
                        $ellipsisShown = false;
                ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                <?php
                    } elseif (!$ellipsisShown) {
                        // Show ellipsis only once
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        $ellipsisShown = true;
                    }
                }
                ?>
                <!-- Next button -->
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">&gt;</a>
                </li>
            </ul>
        </nav>

    </div>
</main>

<?php
include '../templates/footer.php'
?>