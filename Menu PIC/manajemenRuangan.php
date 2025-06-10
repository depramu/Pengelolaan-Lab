<?php
session_start();
include '../koneksi.php';

// Pagination setup
$perPage = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Hitung total data
$countQuery = "SELECT COUNT(*) AS total FROM Ruangan";
$countResult = sqlsrv_query($conn, $countQuery);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'];
$totalPages = ceil($totalData / $perPage);


// Ambil data sesuai halaman
$offset = ($page - 1) * $perPage;
$query = "SELECT idRuangan, namaRuangan, kondisiRuangan, ketersediaan FROM Ruangan";
$result = sqlsrv_query($conn, $query);
$currentPage = basename($_SERVER['PHP_SELF']); // Determine the current page

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manajemen Ruangan - Sistem Pengelolaan Laboratorium</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .sidebar-logo {
            width: 180px;
            height: auto;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .sidebar {
            background: #065ba6;
            height: 82vh;
            border-radius: 12px;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                border-radius: 0;
                height: 100vh;
            }
        }

        .sidebar .nav-link {
            color: #fff;
            font-weight: 500;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .sidebar .nav-link img {
            width: 30px;
            margin-right: 10px;
            object-fit: contain;
        }

        .profile-img {
            width: 32px;
            height: 32px;
            object-fit: contain;
            margin-left: 10px;
        }

        .atoy-img {
            width: clamp(100px, 15vw, 160px);
            height: auto;
            position: absolute;
            right: clamp(30px, 5vw, 60px);
            bottom: clamp(15px, 3vh, 30px);
        }

        @media (max-width: 991.98px) {
            .atoy-img {
                display: none !important;
            }
        }

        main {
            margin-left: 3vh;
            margin-right: 3vh;
            border-radius: 12px;
            height: 82vh;
        }

        .sidebar .collapse .nav-link {
            color: #ffffff !important;
            background-color: transparent !important;
        }

        .sidebar .collapse .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
        }

        .sidebar .collapse .nav-link.active-submenu {
            background-color: rgba(255, 255, 255, 0.2) !important;
            font-weight: 500;
            color: #ffffff !important;
        }

        @media (max-width: 767.98px) {
            header.d-flex {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
                padding-top: 0.5rem !important;
                padding-bottom: 0.5rem !important;
            }

            header .fw-semibold.fs-3 {
                font-size: 1.1rem !important;
            }

            header .fw-normal.fs-6 {
                font-size: 0.9rem !important;
            }

            .sidebar-logo {
                width: 110px;
                margin-top: 0.5rem;
                margin-left: 2rem;
                margin-bottom: 0.5rem;
            }

            .profile-img {
                width: 24px;
                height: 24px;
                margin-left: 5px;
            }

            main {
                height: 90vh;
            }

            main nav {
                font-size: 0.8rem;
            }
        }

        .fixed-pagination {
            position: fixed;
            bottom: 30px;
            right: 40px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid min-vh-100 d-flex flex-column p-0">
        <!-- Header -->
        <header class="d-flex align-items-center justify-content-between px-3 px-md-5 py-3">
            <div class="d-flex align-items-center">
                <img src="../icon/logo0.png" class="sidebar-logo img-fluid" alt="Logo" />
                <div class="d-none d-md-block ps-3 ps-md-4" style="margin-left: 5vw;">
                    <span class="fw-semibold fs-3">Hello,</span><br>
                    <span class="fw-normal fs-6">
                        <?php
                        if (isset($_SESSION['user_nama'])) {
                            echo htmlspecialchars($_SESSION['user_nama']);
                        } else {
                            echo "PIC User";
                        }
                        ?>
                        (PIC Aset)
                    </span>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <a href="notifPIC.php" class="me-0"><img src="../icon/bell.png" class="profile-img img-fluid" alt="Notif"></a>
                <a href="profilPIC.php"><img src="../icon/vector0.svg" class="profile-img img-fluid" alt="Profil"></a>
                <button class="btn btn-primary d-lg-none ms-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </header>
        <div class="row flex-grow-1 g-0">

            <!-- Sidebar for large screens -->
            <nav class="col-auto sidebar d-none d-lg-flex flex-column p-3 ms-lg-4">
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item mb-2">
                        <a href="dashboardPIC.php" class="nav-link"><img src="../icon/dashboard0.svg">Dashboard</a>
                    </li>
                    <li class="nav-item mb-2">
                        <?php
                        $manajemenAsetPages = ['manajemenBarang.php', 'manajemenRuangan.php'];
                        $isManajemenAsetActive = in_array($currentPage, $manajemenAsetPages);
                        ?>
                        <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#manajemenAsetSubmenu" role="button" aria-expanded="false" aria-controls="manajemenAsetSubmenu">
                            <span><img src="../icon/layers0.png">Manajemen Aset</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4 <?php if ($isManajemenAsetActive) echo 'show'; ?>" id="manajemenAsetSubmenu">
                            <a href="manajemenBarang.php" class="nav-link <?php if ($currentPage === 'manajemenBarang.php') echo 'active-submenu'; ?>">Barang</a>
                            <a href="manajemenRuangan.php" class="nav-link <?php if ($currentPage === 'manajemenRuangan.php') echo 'active-submenu'; ?>">Ruangan</a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#akunSubmenu" role="button" aria-expanded="false" aria-controls="akunSubmenu">
                            <span><img src="../icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4" id="akunSubmenu">
                            <a href="manajemenAkunMhs.php" class="nav-link">Mahasiswa</a>
                            <a href="manajemenAkunKry.php" class="nav-link">Karyawan</a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#pinjamSubmenuMobile" role="button" aria-expanded="false" aria-controls="pinjamSubmenuMobile">
                            <span><img src="../icon/ic-twotone-sync-alt0.svg">Peminjaman</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4" id="pinjamSubmenuMobile">
                            <a href="peminjamanBarang.php" class="nav-link">Barang</a>
                            <a href="#" class="nav-link">Ruangan</a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link"><img src="../icon/graph-report0.png" class="sidebar-icon-report">Laporan</a>
                    </li>
                    <li class="nav-item mt-0">
                        <a href="../index.php" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="../icon/exit.png">Log Out</a>
                    </li>
                </ul>
            </nav>
            <!-- End Sidebar for large screens -->

            <!-- Offcanvas Sidebar for small screens -->
            <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasSidebarLabel">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0">
                    <nav class="sidebar flex-column p-4 h-100">
                        <ul class="nav nav-pills flex-column mb-auto">
                            <li class="nav-item mb-2">
                                <a href="dashboardPIC.php" class="nav-link"><img src="../icon/dashboard0.svg">Dashboard</a>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#asetSubmenuMobile" role="button" aria-expanded="false" aria-controls="asetSubmenuMobile">
                                    <span><img src="../icon/layers0.png">Manajemen Aset</span>
                                    <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                                </a>
                                <div class="collapse ps-4" id="asetSubmenuMobile">
                                    <a href="manajemenBarang.php" class="nav-link">Barang</a>
                                    <a href="manajemenRuangan.php" class="nav-link">Ruangan</a>
                                </div>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#akunSubmenuMobile" role="button" aria-expanded="false" aria-controls="akunSubmenuMobile">
                                    <span><img src="../icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
                                    <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                                </a>
                                <div class="collapse ps-4" id="akunSubmenuMobile">
                                    <a href="manajemenAkunMhs.php" class="nav-link">Mahasiswa</a>
                                    <a href="manajemenAkunKry.php" class="nav-link">Karyawan</a>
                                </div>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#pinjamSubmenuMobile" role="button" aria-expanded="false" aria-controls="pinjamSubmenuMobile">
                                    <span><img src="../icon/ic-twotone-sync-alt0.svg">Peminjaman</span>
                                    <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                                </a>
                                <div class="collapse ps-4" id="pinjamSubmenuMobile">
                                    <a href="peminjamanBarang.php" class="nav-link">Barang</a>
                                    <a href="peminjamanRuangan.php" class="nav-link">Ruangan</a>
                                </div>
                            </li>
                            <li class="nav-item mb-2">
                                <a href="#" class="nav-link"><img src="../icon/graph-report0.png" class="sidebar-icon-report">Laporan</a>
                            </li>
                            <li class="nav-item mt-0">
                                <a href="../index.php" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="../icon/exit.png">Log Out</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- End Offcanvas Sidebar for small screens -->

            <!-- Content Area -->
            <main class="col bg-white px-4 py-3 position-relative">
                <div class="mb-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manajemen Ruangan</li>
                        </ol>
                    </nav>
                </div>

                <!-- Table Manajemen Ruangan -->
                <div class="d-flex justify-content-start mb-2">
                    <a href="../CRUD/Ruangan/tambahRuangan.php" class="btn btn-primary">
                        <img src="../icon/tambah.svg" alt="tambah" class="me-2">Tambah Ruangan</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>ID Ruangan</th>
                                <th>Nama Ruangan</th>
                                <th>Kondisi</th>
                                <th>Ketersediaan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $hasData = false;
                            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                                $hasData = true;
                            ?>
                                <tr>
                                    <td><?= $row['idRuangan'] ?></td>
                                    <td><?= $row['namaRuangan'] ?></td>
                                    <td><?= $row['kondisiRuangan'] ?></td>
                                    <td><?= $row['ketersediaan'] ?></td>
                                    <td class="text-center">
                                        <a href="../CRUD/Ruangan/editRuangan.php?id=<?= $row['idRuangan'] ?>"><img src="../icon/edit.svg" alt="" style="width: 20px; height: 20px; margin-bottom: 5px; margin-right: 0px;"></a>
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['idRuangan'] ?>"><img src="../icon/hapus.svg" alt="" style="width: 20px; height: 20px; margin-bottom: 5px; margin-right: 0px;"></a>

                                        <!-- delete -->
                                        <div class="modal fade" id="deleteModal<?= $row['idRuangan'] ?>"
                                            tabindex="-1" aria-labelledby="modalLabel<?= $row['idRuangan'] ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <form action="../CRUD/Ruangan/hapusRuangan.php" method="POST">
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
                            }
                            if (!$hasData) {
                                echo '<tr>
                                <td colspan="5" class="text-center">Tidak ada data</td>
                                </tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>

        <!-- Pagination moved here, after .row and before Logout Modal -->
        <nav aria-label="Page navigation" class="fixed-pagination">
            <ul class="pagination justify-content-end">
                <!-- Previous button -->
                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>" tabindex="-1">&lt;</a>
                </li>
                <!-- Page numbers -->
                <?php
                $showPages = 3;
                $ellipsisShown = false;
                for ($i = 1; $i <= $totalPages; $i++) {
                    if (
                        $i <= $showPages ||
                        $i > $totalPages - $showPages ||
                        abs($i - $page) <= 1
                    ) {
                        $ellipsisShown = false;
                ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                <?php
                    } elseif (!$ellipsisShown) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        $ellipsisShown = true;
                    }
                }
                ?>
                <!-- Next button -->
                <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">&gt;</a>
                </li>
            </ul>
        </nav>

        <!-- Logout Modal -->
        <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="logoutModalLabel"><i><img src="../icon/info.svg" alt="" style="width: 25px; height: 25px; margin-bottom: 5px; margin-right: 10px;"></i>PERINGATAN</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Yakin ingin log out?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger ps-4 pe-4" data-bs-dismiss="modal">Tidak</button>
                        <a href="../index.php" class="btn btn-primary ps-4 pe-4">Ya</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Logout Modal -->

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </div>
</body>

</html>