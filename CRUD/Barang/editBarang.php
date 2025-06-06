<?php
session_start(); // Ensure session is started at the very top
include '../../koneksi.php';

$idBarang = $_GET['id'] ?? null;
$error = ''; // Initialize error string
$showModal = false; // For success modal

if (!$idBarang) {
    header('Location: ../../Menu PIC/manajemenBarang.php');
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);

// Initial data fetch
$query = "SELECT * FROM Barang WHERE idBarang = ?";
$stmt = sqlsrv_query($conn, $query, [$idBarang]);
$data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$data) {
    // Handle case where ID doesn't exist in DB
    header('Location: ../../Menu PIC/manajemenBarang.php?error=notfound');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaBarang = $_POST['namaBarang'];
    $stokBarang = $_POST['stokBarang'];
    $lokasiBarang = $_POST['lokasiBarang'];

    // Basic validation
    if (empty($namaBarang) || $stokBarang === '' || $stokBarang < 0 || empty($lokasiBarang)) {
        $error = "Semua field harus diisi dengan benar. Stok tidak boleh kurang dari 0.";
    } else {
        $updateQuery = "UPDATE Barang SET namaBarang = ?, stokBarang = ?, lokasiBarang = ? WHERE idBarang = ?";
        $params = [$namaBarang, $stokBarang, $lokasiBarang, $idBarang];
        $updateStmt = sqlsrv_query($conn, $updateQuery, $params);

        if ($updateStmt) {
            $showModal = true; // Show success modal
            // Re-fetch data to display updated values as we are staying on the page to show the modal
            $stmt = sqlsrv_query($conn, $query, [$idBarang]);
            $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        } else {
            $error = "Gagal mengubah data barang. Error: " . print_r(sqlsrv_errors(), true);
        }
    }
}

// Fetch list of Ruangan for Lokasi Barang dropdown
$lokasiList = [];
$sqlLokasi = "SELECT idRuangan FROM Ruangan";
$stmtLokasi = sqlsrv_query($conn, $sqlLokasi);
if ($stmtLokasi) {
    while ($row = sqlsrv_fetch_array($stmtLokasi, SQLSRV_FETCH_ASSOC)) {
        $lokasiList[] = $row['idRuangan'];
    }
}

// Define page groups for active sidebar states
$manajemenAsetPages = ['manajemenBarang.php', 'manajemenRuangan.php', 'tambahBarang.php', 'editBarang.php', 'tambahRuangan.php', 'editRuangan.php'];
$isManajemenAsetActive = in_array($currentPage, $manajemenAsetPages);

$manajemenAkunPages = ['manajemenAkunMhs.php', 'tambahAkunMhs.php', 'editAkunMhs.php', 'manajemenAkunKry.php', 'tambahAkunKry.php', 'editAkunKry.php'];
$isManajemenAkunActive = in_array($currentPage, $manajemenAkunPages);

$peminjamanPages = ['peminjamanBarang.php', 'peminjamanRuangan.php', 'detailPeminjaman.php'];
$isPeminjamanActive = in_array($currentPage, $peminjamanPages);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Barang - Sistem Pengelolaan Laboratorium</title>

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
                        if (isset($_SESSION['user_nama'])) {
                            echo htmlspecialchars($_SESSION['user_nama']);
                        } else {
                            echo "PIC User"; // Default if name not set
                        }
                        ?>
                        (PIC)
                    </span>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <a href="../../Menu PIC/notifPIC.php" class="me-0"><img src="../../icon/bell.png" class="profile-img img-fluid" alt="Notif"></a>
                <a href="../../Menu PIC/profilPIC.php"><img src="../../icon/vector0.svg" class="profile-img img-fluid" alt="Profil"></a>
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
                        <a href="../../Menu PIC/dashboardPIC.php" class="nav-link"><img src="../../icon/dashboard0.svg">Dashboard</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center <?php if ($isManajemenAsetActive) echo 'active'; ?>" data-bs-toggle="collapse" href="#manajemenAsetSubmenu" role="button" aria-expanded="<?php echo $isManajemenAsetActive ? 'true' : 'false'; ?>" aria-controls="manajemenAsetSubmenu">
                            <span><img src="../../icon/layers0.png">Manajemen Aset</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4 <?php if ($isManajemenAsetActive) echo 'show'; ?>" id="manajemenAsetSubmenu">
                            <a href="../../Menu PIC/manajemenBarang.php" class="nav-link <?php if ($currentPage === 'manajemenBarang.php' || $currentPage === 'tambahBarang.php' || $currentPage === 'editBarang.php') echo 'active-submenu'; ?>">Barang</a>
                            <a href="../../Menu PIC/manajemenRuangan.php" class="nav-link <?php if ($currentPage === 'manajemenRuangan.php' || $currentPage === 'tambahRuangan.php' || $currentPage === 'editRuangan.php') echo 'active-submenu'; ?>">Ruangan</a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center <?php if ($isManajemenAkunActive) echo 'active'; ?>" data-bs-toggle="collapse" href="#akunSubmenu" role="button" aria-expanded="<?php echo $isManajemenAkunActive ? 'true' : 'false'; ?>" aria-controls="akunSubmenu">
                            <span><img src="../../icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4 <?php if ($isManajemenAkunActive) echo 'show'; ?>" id="akunSubmenu">
                            <a href="../../Menu PIC/manajemenAkunMhs.php" class="nav-link <?php if ($currentPage === 'manajemenAkunMhs.php' || $currentPage === 'tambahAkunMhs.php' || $currentPage === 'editAkunMhs.php') echo 'active-submenu'; ?>">Mahasiswa</a>
                            <a href="../../Menu PIC/manajemenAkunKry.php" class="nav-link <?php if ($currentPage === 'manajemenAkunKry.php' || $currentPage === 'tambahAkunKry.php' || $currentPage === 'editAkunKry.php') echo 'active-submenu'; ?>">Karyawan</a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center <?php if ($isPeminjamanActive) echo 'active'; ?>" data-bs-toggle="collapse" href="#pinjamSubmenu" role="button" aria-expanded="<?php echo $isPeminjamanActive ? 'true' : 'false'; ?>" aria-controls="pinjamSubmenu">
                            <span><img src="../../icon/ic-twotone-sync-alt0.svg">Peminjaman</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4 <?php if ($isPeminjamanActive) echo 'show'; ?>" id="pinjamSubmenu">
                            <a href="../../Menu PIC/peminjamanBarang.php" class="nav-link <?php if ($currentPage === 'peminjamanBarang.php') echo 'active-submenu'; ?>">Barang</a>
                            <a href="../../Menu PIC/peminjamanRuangan.php" class="nav-link <?php if ($currentPage === 'peminjamanRuangan.php') echo 'active-submenu'; ?>">Ruangan</a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link"><img src="../../icon/graph-report0.png" class="sidebar-icon-report">Laporan</a>
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
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0">
                    <nav class="sidebar flex-column p-4 h-100">
                        <ul class="nav nav-pills flex-column mb-auto">
                            <li class="nav-item mb-2">
                                <a href="../../Menu PIC/dashboardPIC.php" class="nav-link"><img src="../../icon/dashboard0.svg">Dashboard</a>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link d-flex justify-content-between align-items-center <?php if ($isManajemenAsetActive) echo 'active'; ?>" data-bs-toggle="collapse" href="#asetSubmenuMobile" role="button" aria-expanded="<?php echo $isManajemenAsetActive ? 'true' : 'false'; ?>" aria-controls="asetSubmenuMobile">
                                    <span><img src="../../icon/layers0.png">Manajemen Aset</span>
                                    <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                                </a>
                                <div class="collapse ps-4 <?php if ($isManajemenAsetActive) echo 'show'; ?>" id="asetSubmenuMobile">
                                    <a href="../../Menu PIC/manajemenBarang.php" class="nav-link <?php if ($currentPage === 'manajemenBarang.php' || $currentPage === 'tambahBarang.php' || $currentPage === 'editBarang.php') echo 'active-submenu'; ?>">Barang</a>
                                    <a href="../../Menu PIC/manajemenRuangan.php" class="nav-link <?php if ($currentPage === 'manajemenRuangan.php' || $currentPage === 'tambahRuangan.php' || $currentPage === 'editRuangan.php') echo 'active-submenu'; ?>">Ruangan</a>
                                </div>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link d-flex justify-content-between align-items-center <?php if ($isManajemenAkunActive) echo 'active'; ?>" data-bs-toggle="collapse" href="#akunSubmenuMobile" role="button" aria-expanded="<?php echo $isManajemenAkunActive ? 'true' : 'false'; ?>" aria-controls="akunSubmenuMobile">
                                    <span><img src="../../icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
                                    <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                                </a>
                                <div class="collapse ps-4 <?php if ($isManajemenAkunActive) echo 'show'; ?>" id="akunSubmenuMobile">
                                    <a href="../../Menu PIC/manajemenAkunMhs.php" class="nav-link <?php if ($currentPage === 'manajemenAkunMhs.php' || $currentPage === 'tambahAkunMhs.php' || $currentPage === 'editAkunMhs.php') echo 'active-submenu'; ?>">Mahasiswa</a>
                                    <a href="../../Menu PIC/manajemenAkunKry.php" class="nav-link <?php if ($currentPage === 'manajemenAkunKry.php' || $currentPage === 'tambahAkunKry.php' || $currentPage === 'editAkunKry.php') echo 'active-submenu'; ?>">Karyawan</a>
                                </div>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link d-flex justify-content-between align-items-center <?php if ($isPeminjamanActive) echo 'active'; ?>" data-bs-toggle="collapse" href="#pinjamSubmenuMobile" role="button" aria-expanded="<?php echo $isPeminjamanActive ? 'true' : 'false'; ?>" aria-controls="pinjamSubmenuMobile">
                                    <span><img src="../../icon/ic-twotone-sync-alt0.svg">Peminjaman</span>
                                    <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                                </a>
                                <div class="collapse ps-4 <?php if ($isPeminjamanActive) echo 'show'; ?>" id="pinjamSubmenuMobile">
                                    <a href="../../Menu PIC/peminjamanBarang.php" class="nav-link <?php if ($currentPage === 'peminjamanBarang.php') echo 'active-submenu'; ?>">Barang</a>
                                    <a href="../../Menu PIC/peminjamanRuangan.php" class="nav-link <?php if ($currentPage === 'peminjamanRuangan.php') echo 'active-submenu'; ?>">Ruangan</a>
                                </div>
                            </li>
                            <li class="nav-item mb-2">
                                <a href="#" class="nav-link"><img src="../../icon/graph-report0.png" class="sidebar-icon-report">Laporan</a>
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
            <main class="col bg-white px-4 py-3 position-relative">
                <div class="mb-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="../../Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                            <li class="breadcrumb-item"><a href="../../Menu PIC/manajemenBarang.php">Manajemen Barang</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Barang</li>
                        </ol>
                    </nav>
                </div>


                <!-- Edit Barang -->
                <div class="container mt-4">
                    <?php if (!empty($error)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                            <a href="../../Menu PIC/manajemenBarang.php" class="btn btn-secondary">Kembali</a>
                        </div>
                    <?php endif; ?>

                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-12" style="margin-right: 20px;">
                            <div class="card border border-dark">
                                <div class="card-header bg-white border-bottom border-dark">
                                    <span class="fw-semibold">Edit Barang</span>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-2">
                                            <label for="idBarang" class="form-label">ID Barang</label>
                                            <input type="text" class="form-control" id="idBarang" name="idBarang" value="<?= htmlspecialchars($idBarang) ?>" disabled>
                                        </div>
                                        <div class="mb-2">
                                            <label for="namaBarang" class="form-label">
                                                Nama Barang
                                                <span class="text-danger ms-2" id="errorNamaBarang" style="font-size:0.95em;display:none;">*Harus Diisi</span>
                                            </label>
                                            <input type="text" class="form-control" id="namaBarang" name="namaBarang" value="<?= htmlspecialchars($data['namaBarang']) ?>">
                                        </div>
                                        <div class="mb-2">
                                            <label for="stokBarang" class="form-label">
                                                Stok Barang
                                                <span class="text-danger ms-2" id="errorStokBarang" style="font-size:0.95em;display:none;">*Harus Diisi</span>
                                            </label>
                                            <div class="input-group" style="max-width: 180px;">
                                                <button class="btn btn-outline-secondary" type="button" onclick="changeStok(-1)">-</button>
                                                <input type="number hidden" class="form-control text-center" id="stokBarang" name="stokBarang" value="<?= htmlspecialchars($data['stokBarang']) ?>" min="0" style="max-width: 70px;">
                                                <button class="btn btn-outline-secondary" type="button" onclick="changeStok(1)">+</button>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label for="lokasiBarang" class="form-label">
                                                Lokasi Barang
                                                <span class="text-danger ms-2" id="errorLokasiBarang" style="font-size:0.95em;display:none;">*Harus Diisi</span>
                                            </label>
                                            <select class="form-select" id="lokasiBarang" name="lokasiBarang">
                                                <option value="" disabled selected>Pilih Lokasi</option>
                                                <?php foreach ($lokasiList as $lokasi) : ?>
                                                    <option value="<?= htmlspecialchars($lokasi) ?>" <?php if ($data['lokasiBarang'] == $lokasi) echo 'selected'; ?>><?= htmlspecialchars($lokasi) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="d-flex justify-content-between mt-4">
                                            <a href="../../Menu PIC/manajemenBarang.php" class="btn btn-secondary">Kembali</a>
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
                                    <a href="../../Menu PIC/manajemenBarang.php"><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></a>
                                </div>
                                <div class="modal-body">
                                    <p>Data barang berhasil diubah.</p>
                                </div>
                                <div class="modal-footer">
                                    <a href="../../Menu PIC/manajemenBarang.php" class="btn btn-primary">OK</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End Edit Barang -->


            </main>

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
                        <a href="../../index.php" class="btn btn-primary ps-4 pe-4">Ya</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Logout Modal -->

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            function changeStok(val) {
                var stokInput = document.getElementById('stokBarang');
                var current = parseInt(stokInput.value) || 0;
                var next = current + val;
                if (next < 0) next = 0;
                stokInput.value = next;
            }

            document.querySelector('form').addEventListener('submit', function(e) {
                let valid = true;

                // Nama Barang
                const namaBarang = document.getElementById('namaBarang');
                const errorNamaBarang = document.getElementById('errorNamaBarang');
                if (namaBarang.value.trim() === '') {
                    errorNamaBarang.style.display = 'inline';
                    valid = false;
                } else {
                    errorNamaBarang.style.display = 'none';
                }

                // Stok Barang
                const stokBarang = document.getElementById('stokBarang');
                const errorStokBarang = document.getElementById('errorStokBarang');
                if (stokBarang.value.trim() === '' || parseInt(stokBarang.value) < 0) {
                    errorStokBarang.style.display = 'inline';
                    valid = false;
                } else {
                    errorStokBarang.style.display = 'none';
                }

                // Lokasi Barang
                const lokasiBarang = document.getElementById('lokasiBarang');
                const errorLokasiBarang = document.getElementById('errorLokasiBarang');
                if (!lokasiBarang.value) {
                    errorLokasiBarang.style.display = 'inline';
                    valid = false;
                } else {
                    errorLokasiBarang.style.display = 'none';
                }

                if (!valid) e.preventDefault();
            });
        </script>

        <?php if ($showModal) : ?>
            <script>
                var modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();
            </script>
        <?php endif; ?>



</body>

</html>