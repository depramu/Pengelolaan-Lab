<?php
session_start();
include '../../koneksi.php';

// Check if user is logged in
if (!isset($_SESSION['pic_id'])) {
    header('Location: ../../Login/loginPIC.php');
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);

// Get peminjaman detail
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT p.*, m.namaMhs, m.email, m.noHp, b.namaBarang, b.kondisi 
              FROM Peminjaman p 
              JOIN Mahasiswa m ON p.nim = m.nim 
              JOIN Barang b ON p.idBarang = b.idBarang 
              WHERE p.idPeminjaman = ?";
    $params = [$id];
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt && sqlsrv_fetch($stmt)) {
        $peminjaman = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    } else {
        header('Location: index.php');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detail Peminjaman Barang - Sistem Pengelolaan Laboratorium</title>

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
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid min-vh-100 d-flex flex-column p-0">
        <!-- Header -->
        <header class="d-flex align-items-center justify-content-between px-3 px-md-5 py-3">
            <div class="d-flex align-items-center">
                <img src="../../icon/logo0.png" class="sidebar-logo img-fluid" alt="Logo" />
                <div class="d-none d-md-block ps-3 ps-md-4" style="margin-left: 5vw;">
                    <span class="fw-semibold fs-3">Hello,</span><br>
                    <span class="fw-normal fs-6">
                        <?php
                        if (isset($_SESSION['pic_nama'])) {
                            echo htmlspecialchars($_SESSION['pic_nama']);
                        }
                        if (isset($_SESSION['pic_role'])) {
                            echo " (" . htmlspecialchars($_SESSION['pic_role']) . ")";
                        }
                        ?>
                    </span>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <a href="#" class="me-0"><img src="../../icon/bell.png" class="profile-img img-fluid" alt="Notif"></a>
                <a href="#" class="me-0"><img src="../../icon/vector0.svg" class="profile-img img-fluid" alt="Profil"></a>
                <button class="btn btn-primary d-lg-none ms-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </header>

        <!-- Content -->
        <div class="row flex-grow-1 g-0">
            <!-- Sidebar -->
            <nav class="col-auto sidebar d-none d-lg-flex flex-column p-3 ms-lg-4">
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item mb-2">
                        <a href="../dashboardPIC.php" class="nav-link">
                            <img src="../../icon/dashboard0.svg">Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#manajemenSubmenu">
                            <span><img src="../../icon/manajemen.svg">Manajemen</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4" id="manajemenSubmenu">
                            <a href="../manajemenBarang.php" class="nav-link">Barang</a>
                            <a href="../manajemenRuangan.php" class="nav-link">Ruangan</a>
                            <a href="../manajemenAkunMhs.php" class="nav-link">Akun Mahasiswa</a>
                            <a href="../manajemenAkunKry.php" class="nav-link">Akun Karyawan</a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center active" data-bs-toggle="collapse" href="#peminjamanSubmenu">
                            <span><img src="../../icon/peminjaman.svg">Peminjaman</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse show ps-4" id="peminjamanSubmenu">
                            <a href="index.php" class="nav-link">Barang</a>
                            <a href="../Peminjaman Ruangan/index.php" class="nav-link">Ruangan</a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="../laporan.php" class="nav-link">
                            <img src="../../icon/laporan.svg">Laporan
                        </a>
                    </li>
                    <li class="nav-item mt-0">
                        <a href="#" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal">
                            <img src="../../icon/exit.png">Log Out
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="col bg-white px-3 px-md-4 py-3">
                <div class="mb-5">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="../dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                            <li class="breadcrumb-item"><a href="#">Peminjaman</a></li>
                            <li class="breadcrumb-item"><a href="index.php">Barang</a></li>
                            <li class="breadcrumb-item active">Detail Peminjaman</li>
                        </ol>
                    </nav>
                </div>

                <!-- Content here -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Detail Peminjaman Barang</h4>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="card-title mb-4">Informasi Peminjaman</h5>
                                <table class="table">
                                    <tr>
                                        <th>ID Peminjaman</th>
                                        <td><?php echo htmlspecialchars($peminjaman['idPeminjaman']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Peminjaman</th>
                                        <td><?php echo $peminjaman['tanggalPeminjaman']->format('d/m/Y'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Kembali</th>
                                        <td><?php echo $peminjaman['tanggalKembali']->format('d/m/Y'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <?php
                                            switch ($peminjaman['status']) {
                                                case 'pending':
                                                    echo '<span class="badge bg-warning">Menunggu</span>';
                                                    break;
                                                case 'approved':
                                                    echo '<span class="badge bg-success">Disetujui</span>';
                                                    break;
                                                case 'rejected':
                                                    echo '<span class="badge bg-danger">Ditolak</span>';
                                                    break;
                                                case 'returned':
                                                    echo '<span class="badge bg-info">Dikembalikan</span>';
                                                    break;
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="card-title mb-4">Informasi Peminjam</h5>
                                <table class="table">
                                    <tr>
                                        <th>Nama</th>
                                        <td><?php echo htmlspecialchars($peminjaman['namaMhs']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><?php echo htmlspecialchars($peminjaman['email']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>No. HP</th>
                                        <td><?php echo htmlspecialchars($peminjaman['noHp']); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="card-title mb-4">Informasi Barang</h5>
                                <table class="table">
                                    <tr>
                                        <th>Nama Barang</th>
                                        <td><?php echo htmlspecialchars($peminjaman['namaBarang']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Kondisi</th>
                                        <td><?php echo htmlspecialchars($peminjaman['kondisi']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Keterangan</th>
                                        <td><?php echo htmlspecialchars($peminjaman['keterangan']); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i><img src="../../icon/info.svg" alt="" style="width: 25px; height: 25px; margin-bottom: 5px; margin-right: 10px;"></i>
                        PERINGATAN
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Yakin ingin log out?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger ps-4 pe-4" data-bs-dismiss="modal">Tidak</button>
                    <a href="../../index.php" class="btn btn-primary ps-4 pe-4">Ya</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>