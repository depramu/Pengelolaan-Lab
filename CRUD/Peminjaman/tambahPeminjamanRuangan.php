<?php
include '../../koneksi.php';
session_start();

// Auto-generate id Peminjaman Ruangan dari database SQL Server
$idPeminjamanRuangan = 'PJR001';
$sqlId = "SELECT TOP 1 idPeminjamanRuangan FROM Peminjaman_Ruangan WHERE idPeminjamanRuangan LIKE 'PJR%' ORDER BY idPeminjamanRuangan DESC";
$stmtId = sqlsrv_query($conn, $sqlId);
if ($stmtId && $rowId = sqlsrv_fetch_array($stmtId, SQLSRV_FETCH_ASSOC)) {
    $lastId = $rowId['idPeminjamanRuangan']; // contoh: PJR012
    $num = intval(substr($lastId, 3));
    $newNum = $num + 1;
    $idPeminjamanRuangan = 'PJR' . str_pad($newNum, 3, '0', STR_PAD_LEFT);
}

// ID Ruangan
$idRuangan = $_GET['id'] ?? null;
if (empty($idRuangan)) {
    die("Error: ID Ruangan tidak ditemukan. Silakan kembali dan pilih ruangan yang ingin dipinjam.");
}

// NIM & NPK
$nim = $_SESSION['nim'] ?? null;
$npk = $_SESSION['npk'] ?? null;

// Tanggal Peminjaman & Waktu Peminjaman
$tglPeminjamanRuangan = $_SESSION['tglPeminjamanRuangan'] ?? date('Y-m-d');
$waktuMulai = $_SESSION['waktuMulai'] ?? '';
$waktuSelesai = $_SESSION['waktuSelesai'] ?? '';

// Validasi Input
$error = null;
$showModal = false;

// Proses Peminjaman
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $alasanPeminjamanRuangan = $_POST['alasanPeminjamanRuangan'];

    $query = "INSERT INTO Peminjaman_Ruangan (idPeminjamanRuangan, idRuangan, tglPeminjamanRuangan, waktuMulai, waktuSelesai, nim, npk, alasanPeminjamanRuangan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $params = [$idPeminjamanRuangan, $idRuangan, $tglPeminjamanRuangan, $waktuMulai, $waktuSelesai, $nim, $npk, $alasanPeminjamanRuangan];
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt) {
        $showModal = true;
    } else {
        $error = "Gagal menambahkan peminjaman ruangan. Error: " . print_r(sqlsrv_errors(), true);
    }

    if ($stmt) {
        $ketersediaan = "Tidak Tersedia";
        $query = "UPDATE Ruangan SET ketersediaan = ? WHERE idRuangan = ?";
        $params = [$ketersediaan, $idRuangan];
        $stmt = sqlsrv_query($conn, $query, $params);
    }
    if ($stmt) {
        $statusPeminjaman = "Sedang dipinjam";
        $query = "UPDATE Peminjaman_Ruangan SET statusPeminjaman = ? WHERE idRuangan = ?";
        $params = [$statusPeminjaman, $idRuangan];
        $stmt = sqlsrv_query($conn, $query, $params);
    }

    if ($stmt) {
        $showModal = true;
    } else {
        $error = "Gagal menambahkan peminjaman ruangan. Error: " . print_r(sqlsrv_errors(), true);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Peminjaman Ruangan - Sistem Pengelolaan Laboratorium</title>

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
            width: 278px;
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

        main {
            margin-left: 3vh;
            margin-right: 3vh;
            border-radius: 12px;
            height: 82vh;
        }

        /* === Styling for SUBMENU items (e.g., Barang, Ruangan) === */
        .sidebar .collapse .nav-link {
            color: #ffffff !important;
            /* White text for submenu items */
            background-color: transparent !important;
        }

        .sidebar .collapse .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15) !important;
            /* Subtle hover for submenu items */
            color: #ffffff !important;
        }

        /* Optional: If a submenu item itself can be marked 'active' (e.g. current page is 'Barang') */
        /* You would need to add class="active-submenu" to the link via PHP/JS */
        .sidebar .collapse .nav-link.active-submenu {
            background-color: rgba(255, 255, 255, 0.2) !important;
            /* Slightly more prominent for active submenu */
            font-weight: 500;
            /* Or bold, as you prefer */
            color: #ffffff !important;
        }

        /* Header kecil di layar kecil */
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

            .modal-header {
                padding: 0.5rem 1rem;
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
                        if (isset($_SESSION['peminjam_nama'])) {
                            echo htmlspecialchars($_SESSION['peminjam_nama']);
                        } else {
                            echo "Peminjam"; // Default if name not set
                        }
                        if (isset($_SESSION['peminjam_role'])) {
                            echo " (" . htmlspecialchars($_SESSION['peminjam_role']) . ")";
                        } else {
                            echo " (Peminjam)"; // Default if role not set
                        }
                        ?>
                    </span>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <a href="../../notif.php" class="me-0"><img src="../../icon/bell.png" class="profile-img img-fluid" alt="Notif"></a>
                <a href="../../profil.php"><img src="../../icon/vector0.svg" class="profile-img img-fluid" alt="Profil"></a>
                <!-- Sidebar toggle button for mobile -->
                <button class="btn btn-primary d-lg-none ms-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </header>
        <!-- End Header -->

        <!-- Content -->
        <div class="row flex-grow-1 g-0">
            <!-- Sidebar for large screens -->
            <nav class="col-auto sidebar d-none d-lg-flex flex-column p-3  ms-lg-4">
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item mb-2">
                        <a href="dashboardPeminjam.php" class="nav-link active"><img src="../../icon/dashboard0.svg">Dashboard</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#peminjamanSubmenu" role="button" aria-expanded="false" aria-controls="peminjamanSubmenu">
                            <span><img src="../../icon/peminjaman.svg">Peminjaman</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4" id="peminjamanSubmenu">
                            <a href="peminjamanBarang.php" class="nav-link">Barang</a>
                            <a href="cekRuangan.php" class="nav-link">Ruangan</a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#riwayatSubmenu" role="button" aria-expanded="false" aria-controls="riwayatSubmenu">
                            <span><img src="../../icon/riwayat.svg" style="width: 28px; height: 28px; object-fit: contain;">Riwayat</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4" id="riwayatSubmenu">
                            <a href="#" class="nav-link">Barang</a>
                            <a href="#" class="nav-link">Ruangan</a>
                        </div>
                    </li>

                    <li class="nav-item mt-0">
                        <a href="../../index.php" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="../../icon/exit.png">Log Out</a>
                    </li>
                </ul>
            </nav>
            <!-- End Sidebar for large screens -->

            <!-- Offcanvas Sidebar for small screens -->
            <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasSidebarLabel">Menu</h5>
                    <a href="lihatRuangan.php"><button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button></a>
                </div>
                <div class="offcanvas-body p-0">
                    <nav class="sidebar flex-column p-4 h-100">
                        <ul class="nav nav-pills flex-column mb-auto">
                            <li class="nav-item mb-2">
                                <a href="dashboardPeminjam.php" class="nav-link active"><img src="../../icon/dashboard0.svg">Dashboard</a>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#peminjamanSubmenuMobile" role="button" aria-expanded="false" aria-controls="peminjamanSubmenuMobile">
                                    <span><img src="../../icon/peminjaman.svg">Peminjaman</span>
                                    <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                                </a>
                                <div class="collapse ps-4" id="peminjamanSubmenuMobile">
                                    <a href="peminjamanBarang.php" class="nav-link">Barang</a>
                                    <a href="cekRuangan.php" class="nav-link">Ruangan</a>
                                </div>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#riwayatSubmenuMobile" role="button" aria-expanded="false" aria-controls="riwayatSubmenuMobile">
                                    <span><img src="../../icon/riwayat.svg">Riwayat</span>
                                    <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                                </a>
                                <div class="collapse ps-4" id="riwayatSubmenuMobile">
                                    <a href="#" class="nav-link">Barang</a>
                                    <a href="#" class="nav-link">Ruangan</a>
                                </div>
                            </li>
                            <li class="nav-item mt-0">
                                <a href="../../index.php" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="../../icon/exit.png">Log Out</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- End Offcanvas Sidebar for small screens -->


            <!-- Content Area -->
            <main class="col bg-white px-3 px-md-4 py-3 position-relative">
                <div class="mb-2">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="../../Menu Peminjam/dashboardPeminjam.php">Sistem Pengelolaan Lab</a></li>
                            <li class="breadcrumb-item"><a href="../../Menu Peminjam/cekRuangan.php">Cek Ruangan</a></li>
                            <li class="breadcrumb-item"><a href="../../Menu Peminjam/lihatRuangan.php">Lihat Ruangan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Pengajuan Peminjaman Ruangan</li>
                        </ol>
                    </nav>
                </div>

                <!-- Tambah Ruangan -->
                <div class="container mt-4">
                    <?php if (isset($error)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>


                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                            <div class="card border border-dark">
                                <div class="card-header bg-white border-bottom border-dark">
                                    <span class="fw-semibold">Pengajuan Peminjaman Ruangan</span>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="row">
                                            <!-- ID Peminjaman & ID Ruangan (Auto Increment, Disabled Input) -->
                                            <div class="col-md-6">
                                                <div class="mb-2" style="max-width: 400px;">
                                                    <label for="idPeminjamanRuangan" class="form-label">ID Peminjaman</label>
                                                    <input type="text" class="form-control" id="idPeminjamanRuangan" name="idPeminjamanRuangan" value="<?= $idPeminjamanRuangan ?>" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-2" style="max-width: 400px;">
                                                    <label for="idRuangan" class="form-label">ID Ruangan</label>
                                                    <input type="text" class="form-control" id="idRuangan" name="idRuangan" value="<?= $idRuangan ?>" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!-- Tanggal Peminjaman & NIM (Auto Increment, Disabled Input) -->
                                            <div class="col-md-6">
                                                <div class="mb-2" style="max-width: 400px;">
                                                    <label for="tglPeminjamanRuangan" class="form-label">Tanggal Peminjaman</label>
                                                    <input type="text" class="form-control" id="tglPeminjamanRuangan" name="tglPeminjamanRuangan" value="<?= $tglPeminjamanRuangan ?>" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-2" style="max-width: 400px;">
                                                    <label for="nim" class="form-label">NIM</label>
                                                    <input type="text" class="form-control" id="nim" name="nim" value="<?= $nim ?>" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!-- Waktu Peminjaman & NPK (Auto Increment, Disabled Input) -->
                                            <div class="col-md-6">
                                                <div class="mb-2" style="max-width: 400px;">
                                                    <label for="waktuMulai" class="form-label">Waktu Mulai</label>
                                                    <input type="text" class="form-control" id="waktuMulai" name="waktuMulai" value="<?= $waktuMulai ?>" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-2" style="max-width: 400px;">
                                                    <label for="npk" class="form-label">NPK</label>
                                                    <input type="text" class="form-control" id="npk" name="npk" value="<?= $npk ?>" disabled>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-2" style="max-width: 400px;">
                                                    <label for="waktuSelesai" class="form-label">Waktu Selesai</label>
                                                    <input type="text" class="form-control" id="waktuSelesai" name="waktuSelesai" value="<?= $waktuSelesai ?>" disabled>
                                                </div>
                                            </div>
                                            <!-- Alasan Peminjaman -->
                                            <div class="col-md-6">
                                                <label for="alasanPeminjamanRuangan" class="form-label">Alasan Peminjaman</label>
                                                <span id="error-message" style="color: red; display: none; margin-left: 10px;">*Harus Diisi</span>
                                                <textarea class="form-control" id="alasanPeminjamanRuangan" name="alasanPeminjamanRuangan" rows="2" style="max-width: 400px;"></textarea>

                                                <!--validasi kolom harus diisi -->
                                                <script>
                                                    document.getElementById('alasanPeminjamanRuangan').addEventListener('input', function() {
                                                        var alasanPeminjamanRuangan = document.getElementById('alasanPeminjamanRuangan').value;
                                                        var errorMessage = document.getElementById('error-message');

                                                        if (alasanPeminjamanRuangan.trim() === '') {
                                                            errorMessage.style.display = 'inline';
                                                        } else {
                                                            errorMessage.style.display = 'inline';
                                                        }
                                                    });

                                                    document.querySelector('form').addEventListener('submit', function(event) {
                                                        var alasanPeminjamanRuangan = document.getElementById('alasanPeminjamanRuangan').value;
                                                        var errorMessage = document.getElementById('error-message');

                                                        if (alasanPeminjamanRuangan.trim() === '') {
                                                            errorMessage.style.display = 'inline';
                                                            event.preventDefault(); // Mencegah form dikirim jika input kosong
                                                        }
                                                    });
                                                </script>

                                            </div>
                                        </div>


                                        <div class="d-flex justify-content-between mt-4">
                                            <a href="../../Menu Peminjam/lihatRuangan.php" class="btn btn-secondary">Kembali</a>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Modal Berhasil -->
                    <div class="modal fade" id="successModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="confirmModalLabel">Berhasil</h5>
                                    <a href="../../Menu Peminjam/lihatRuangan.php"><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></a>
                                </div>
                                <div class="modal-body">
                                    <p>Peminjaman ruangan berhasil dibuat dengan ID: <?php echo htmlspecialchars($idPeminjamanRuangan); ?></p>
                                </div>
                                <div class="modal-footer">
                                    <a href="../../Menu Peminjam/lihatRuangan.php" class="btn btn-primary">OK</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End Tambah Ruangan -->

                <!-- Success Modal -->
                <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Peminjaman Berhasil</h5>
                                <a href="../../Menu Peminjam/lihatRuangan.php"><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></a>
                            </div>
                            <div class="modal-body">
                                Peminjaman ruangan berhasil dibuat dengan ID: <?php echo htmlspecialchars($idPeminjamanRuangan); ?>
                            </div>
                            <div class="modal-footer">
                                <a href="../../Menu Peminjam/lihatRuangan.php" class="btn btn-primary">Lihat Peminjaman</a>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
            <!-- End Content Area -->
        </div>
    </div>
    <!-- End Container -->

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel"><i><img src="../../icon/info.svg" alt="" style="width: 25px; height: 25px; margin-bottom: 5px; margin-right: 10px;"></i>PERINGATAN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Yakin ingin log out?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger ps-4 pe-4" data-bs-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-primary ps-4 pe-4" onclick="window.location.href='../../index.php'">Ya</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Logout Modal -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (isset($showModal) && $showModal): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();
            });
        </script>
    <?php endif; ?>
</body>

</html>