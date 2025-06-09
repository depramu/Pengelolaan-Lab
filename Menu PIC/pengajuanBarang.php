    <?php
    include '../koneksi.php';
    session_start();

    // Perbaikan: Ambil data dari tabel Peminjaman_Barang
    $idPeminjamanBrg = $_GET['id'] ?? '';
    $data = [];

    $showRejectedModal = false;
    $showModal = false;

    if (!empty($idPeminjamanBrg)) {
        $_SESSION['idPeminjamanBrg'] = $idPeminjamanBrg;

        $query = "SELECT * FROM Peminjaman_Barang WHERE idPeminjamanBrg = ?";
        $params = array($idPeminjamanBrg);
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt && sqlsrv_has_rows($stmt)) {
            $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        }
    }

    // Ekstrak data
    $idBarang = $data['idBarang'] ?? '';
    $nim = $data['nim'] ?? '';
    $npk = $data['npk'] ?? '';
    $tglPeminjamanBrg = isset($data['tglPeminjamanBrg']) ? $data['tglPeminjamanBrg']->format('Y-m-d') : '';
    $jumlahBrg = $data['jumlahBrg'] ?? '';
    $alasanPeminjamanBrg = $data['alasanPeminjamanBrg'] ?? '';
    $currentStatus = $data['statusPeminjaman'] ?? 'Diajukan';

    // Perbaikan: Proses persetujuan
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $query = "UPDATE Peminjaman_Barang 
                    SET statusPeminjaman = 'Sedang Dipinjam'
                    WHERE idPeminjamanBrg = ?";
        $params = array($idPeminjamanBrg);
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt) {
            $showModal = true;
        } else {
            $error = "Gagal melakukan pengajuan barang.";
            exit;
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Pengajuan Barang - Sistem Pengelolaan Laboratorium</title>

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
                    <a href="notif.php" class="me-0"><img src="../icon/bell.png" class="profile-img img-fluid" alt="Notif"></a>
                    <a href="profil.php"><img src="../icon/vector0.svg" class="profile-img img-fluid" alt="Profil"></a>
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
                            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#manajemenAsetSubmenu" role="button" aria-expanded="false" aria-controls="manajemenAsetSubmenu">
                                <span><img src="../icon/layers0.png">Manajemen Aset</span>
                                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                            </a>
                            <div class="collapse ps-4 <?php if ($isManajemenAsetActive) echo 'show'; ?>" id="manajemenAsetSubmenu">
                                <a href="manajemenBarang.php" class="nav-link <?php if ($currentPage === 'manajemenBarang.php' || $currentPage === 'tambahBarang.php' || $currentPage === 'editBarang.php') echo 'active-submenu'; ?>">Barang</a>
                                <a href="manajemenRuangan.php" class="nav-link <?php if ($currentPage === 'manajemenRuangan.php') echo 'active-submenu'; ?>">Ruangan</a>
                            </div>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#akunSubmenu" role="button" aria-expanded="false" aria-controls="akunSubmenu">
                                <span><img src="../icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
                                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                            </a>
                            <div class="collapse ps-4" id="akunSubmenu">
                                <a href="#" class="nav-link">Mahasiswa</a>
                                <a href="#" class="nav-link">Karyawan</a>
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
                                        <a href="manajemenBarang.php" class="nav-link <?php if ($currentPage === 'manajemenBarang.php' || $currentPage === 'tambahBarang.php' || $currentPage === 'editBarang.php') echo 'active-submenu'; ?>">Barang</a>
                                        <a href="manajemenRuangan.php" class="nav-link <?php if ($currentPage === 'manajemenRuangan.php') echo 'active-submenu'; ?>">Ruangan</a>
                                    </div>
                                </li>
                                <li class="nav-item mb-2">
                                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#akunSubmenuMobile" role="button" aria-expanded="false" aria-controls="akunSubmenuMobile">
                                        <span><img src="../icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
                                        <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                                    </a>
                                    <div class="collapse ps-4" id="akunSubmenuMobile">
                                        <a href="#" class="nav-link">Mahasiswa</a>
                                        <a href="#" class="nav-link">Karyawan</a>
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
                    </div>
                </div>
                <!-- End Offcanvas Sidebar for small screens -->

                <!-- Content Area -->
                <main class="col bg-white px-4 py-3 position-relative">
                    <div class="mb-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                                <li class="breadcrumb-item"><a href="peminjamanBarang.php">Peminjaman Barang</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Pengajuan Barang</li>
                            </ol>
                        </nav>
                    </div>


                    <!-- Pengajuan Peminjaman Barang -->
                    <div class="container mt-4">
                        <div class="row justify-content-center">
                            <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                                <div class="card border border-dark">
                                    <div class="card-header bg-white border-bottom border-dark">
                                        <span class="fw-semibold">Pengajuan Peminjaman Barang</span>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST">
                                            <input type="hidden" name="idPeminjamanBrg" value="<?= htmlspecialchars($idPeminjamanBrg) ?>">

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <label for="idBarang" class="form-label">ID Barang</label>
                                                        <input type="text" class="form-control" id="idBarang" name="idBarang" value="<?= htmlspecialchars($idBarang) ?>" disabled>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label for="tglPeminjamanBrg" class="form-label">Tanggal Peminjaman</label>
                                                        <input type="text" class="form-control" id="tglPeminjamanBrg" name="tglPeminjamanBrg" value="<?= htmlspecialchars($tglPeminjamanBrg) ?>" disabled>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label for="jumlahBrg" class="form-label">Jumlah Barang</label>
                                                        <input type="text" class="form-control" id="jumlahBrg" name="jumlahBrg" value="<?= htmlspecialchars($jumlahBrg) ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <label for="idPeminjamanBrgDisplay" class="form-label">ID Peminjaman Barang</label>
                                                        <input type="text" class="form-control" id="idPeminjamanBrgDisplay" value="<?= htmlspecialchars($idPeminjamanBrg) ?>" disabled>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label for="nim" class="form-label">NIM</label>
                                                        <input type="text" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($nim) ?>" disabled>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label for="npk" class="form-label">NPK</label>
                                                        <input type="text" class="form-control" id="npk" name="npk" value="<?= htmlspecialchars($npk) ?>" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Alasan Peminjaman -->
                                            <div class="mb-2">
                                                <label for="alasanPeminjamanBrg" class="form-label">Alasan Peminjaman</label>
                                                <textarea class="form-control" id="alasanPeminjamanBrg" rows="3" style="width: 49%;" disabled><?= htmlspecialchars($alasanPeminjamanBrg) ?></textarea>
                                            </div>

                                            <div class="d-flex justify-content-end gap-2 mt-4">
                                                <!-- TOMBOL AKSI -->
                                                <div class="d-flex justify-content-end gap-2">
                                                    <!-- Link untuk Tolak -->
                                                    <a href="penolakanBarang.php?id=<?= htmlspecialchars($idPeminjamanBrg) ?>" class="btn btn-danger">Tolak</a>
                                                    <!-- Form untuk Setuju -->
                                                    <button type="submit" name="submit" class="btn btn-primary">Setuju</button>
                                                </div>
                                            </div>




                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>


                <!-- Modal Berhasil -->
                <div class="modal fade" id="successModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmModalLabel">Berhasil</h5>
                                <a href="peminjamanBarang.php"><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Setuju"></button></a>
                            </div>
                            <div class="modal-body">
                                <p>PIC Menyetujui Peminjaman Barang <?= htmlspecialchars($idPeminjamanBrg) ?>.</p>
                            </div>
                            <div class="modal-footer">
                                <a href="peminjamanBarang.php" class="btn btn-primary">OK</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

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
                            <a href="../../logout.php" class="btn btn-primary ps-4 pe-4">Ya</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Logout Modal -->

            <!-- Bootstrap JS -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

            <!-- Modal Setuju -->
            <?php if ($showModal): ?>
                <script>
                    let modal = new bootstrap.Modal(document.getElementById('successModal'));
                    modal.show();
                </script>
            <?php endif; ?>


    </body>