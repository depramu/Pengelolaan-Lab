<?php
session_start();

include '../../koneksi.php';

$showModal = false;

// Auto-generate npk dari database SQL Server
$npk = '51001';
$sqlId = "SELECT TOP 1 npk FROM Karyawan WHERE npk LIKE '51%' ORDER BY npk DESC";
$stmtId = sqlsrv_query($conn, $sqlId);
if ($stmtId && $rowId = sqlsrv_fetch_array($stmtId, SQLSRV_FETCH_ASSOC)) {
    $lastId = $rowId['npk']; // contoh: 51001
    $num = intval(substr($lastId, 3));
    $newNum = $num + 1;
    $npk = '51' . str_pad($newNum, 3, '0', STR_PAD_LEFT);
}

// $roleList = [];
// $sqlRole = "SELECT jenisRole FROM Karyawan";
// $stmtRole = sqlsrv_query($conn, $sqlRole);
// if ($stmtRole) {
//     while ($row = sqlsrv_fetch_array($stmtRole, SQLSRV_FETCH_ASSOC)) {
//         $role[] = $row['jenisRole'];
//     }
// }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaKry = $_POST['namaKry'];
    $noHP = $_POST['noHP'];
    $jenisRole = $_POST['jenisRole'];
    $kataSandi = $_POST['kataSandi'];
    $konfirmasiSandi = $_POST['konfirmasiSandi'];

    $query = "INSERT INTO Karyawan   (namaKry, noHP, jenisRole, kataSandi) VALUES (?, ?, ?, ?)";
    $params = [$namaKry, $noHP, $jenisRole, $kataSandi];
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt) {
        $showModal = true;
    } else {
        $error = "Gagal menambahkan akun.";
    }
}

$currentPage = basename($_SERVER['PHP_SELF']); // Determine the current page

// Variabel untuk Manajemen Aset (dibiarkan jika masih ada kemungkinan digunakan atau untuk konsistensi struktur)
$manajemenAsetPages = ['manajemenBarang.php', 'manajemenRuangan.php', 'tambahBarang.php', 'editBarang.php'];
$isManajemenAsetActive = in_array($currentPage, $manajemenAsetPages);

// Variabel untuk Manajemen Akun
$manajemenAkunPages = [
    'manajemenAkunKry.php',
    'tambahAkunKry.php',
    'editAkunKry.php',
    'manajemenAkunMhs.php',
    'tambahAkunMhs.php',
    'editAkunMhs.php'
];
$isManajemenAkunActive = in_array($currentPage, $manajemenAkunPages);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistem Pengelolaan Laboratorium</title>

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
                    <span class="fw-normal fs-6">Nadira Anindita (PIC)</span>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <a href="../../notif.php" class="me-0"><img src="../../icon/bell.png" class="profile-img img-fluid" alt="Notif"></a>
                <a href="../../profil.php"><img src="../../icon/vector0.svg" class="profile-img img-fluid" alt="Profil"></a>
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
                        <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#manajemenAsetSubmenu" role="button" aria-expanded="<?php echo $isManajemenAsetActive ? 'true' : 'false'; ?>" aria-controls="manajemenAsetSubmenu">
                            <span><img src="../../icon/layers0.png">Manajemen Aset</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4 <?php if ($isManajemenAsetActive) echo 'show'; ?>" id="manajemenAsetSubmenu">
                            <a href="../../Menu PIC/manajemenBarang.php" class="nav-link <?php if ($currentPage === 'manajemenBarang.php' || $currentPage === 'tambahBarang.php' || $currentPage === 'editBarang.php') echo 'active-submenu'; ?>">Barang</a>
                            <a href="../../Menu PIC/manajemenRuangan.php" class="nav-link <?php if ($currentPage === 'manajemenRuangan.php') echo 'active-submenu'; ?>">Ruangan</a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#akunSubmenu" role="button" aria-expanded="<?php echo $isManajemenAkunActive ? 'true' : 'false'; ?>" aria-controls="akunSubmenu">
                            <span><img src="../../icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4 <?php if ($isManajemenAkunActive) echo 'show'; ?>" id="akunSubmenu">
                            <a href="../../Menu PIC/manajemenAkunMhs.php" class="nav-link <?php if ($currentPage === 'manajemenAkunMhs.php' || $currentPage === 'tambahAkunMhs.php' || $currentPage === 'editAkunMhs.php') echo 'active-submenu'; ?>">Mahasiswa</a>
                            <a href="../../Menu PIC/manajemenAkunKry.php" class="nav-link <?php if ($currentPage === 'manajemenAkunKry.php' || $currentPage === 'tambahAkunKry.php' || $currentPage === 'editAkunKry.php') echo 'active-submenu'; ?>">Karyawan</a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#pinjamSubmenuMobile" role="button" aria-expanded="false" aria-controls="pinjamSubmenuMobile">
                            <span><img src="../../icon/ic-twotone-sync-alt0.svg">Peminjaman</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4" id="pinjamSubmenuMobile">
                            <a href="../../peminjamanBarang.php" class="nav-link">Barang</a>
                            <a href="#" class="nav-link">Ruangan</a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link"><img src="../../icon/graph-report0.png" class="sidebar-icon-report">Laporan</a>
                    </li>
                    <li class="nav-item mt-0">
                        <a href="#" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="../../icon/exit.png">Log Out</a>
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
                                <a href="../../index.php" class="nav-link"><img src="../../icon/dashboard0.svg">Dashboard</a>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#asetSubmenuMobile" role="button" aria-expanded="<?php echo $isManajemenAsetActive ? 'true' : 'false'; ?>" aria-controls="asetSubmenuMobile">
                                    <span><img src="../../icon/layers0.png">Manajemen Aset</span>
                                    <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                                </a>
                                <div class="collapse ps-4 <?php if ($isManajemenAsetActive) echo 'show'; ?>" id="asetSubmenuMobile">
                                    <a href="../../Menu PIC/manajemenBarang.php" class="nav-link <?php if ($currentPage === 'manajemenBarang.php' || $currentPage === 'tambahBarang.php' || $currentPage === 'editBarang.php') echo 'active-submenu'; ?>">Barang</a>
                                    <a href="../../Menu PIC/manajemenRuangan.php" class="nav-link <?php if ($currentPage === 'manajemenRuangan.php') echo 'active-submenu'; ?>">Ruangan</a>
                                </div>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#akunSubmenuMobile" role="button" aria-expanded="<?php echo $isManajemenAkunActive ? 'true' : 'false'; ?>" aria-controls="akunSubmenuMobile">
                                    <span><img src="../../icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
                                    <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                                </a>
                                <div class="collapse ps-4 <?php if ($isManajemenAkunActive) echo 'show'; ?>" id="akunSubmenuMobile">
                                    <a href="../../Menu PIC/manajemenAkunMhs.php" class="nav-link <?php if ($currentPage === 'manajemenAkunMhs.php' || $currentPage === 'tambahAkunMhs.php' || $currentPage === 'editAkunMhs.php') echo 'active-submenu'; ?>">Mahasiswa</a>
                                    <a href="../../Menu PIC/manajemenAkunKry.php" class="nav-link <?php if ($currentPage === 'manajemenAkunKry.php' || $currentPage === 'tambahAkunKry.php' || $currentPage === 'editAkunKry.php') echo 'active-submenu'; ?>">Karyawan</a>
                                </div>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#pinjamSubmenuMobile" role="button" aria-expanded="false" aria-controls="pinjamSubmenuMobile">
                                    <span><img src="../../icon/ic-twotone-sync-alt0.svg">Peminjaman</span>
                                    <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                                </a>
                                <div class="collapse ps-4" id="pinjamSubmenuMobile">
                                    <a href="../../peminjamanBarang.php" class="nav-link">Barang</a>
                                    <a href="#" class="nav-link">Ruangan</a>
                                </div>
                            </li>
                            <li class="nav-item mb-2">
                                <a href="#" class="nav-link"><img src="../../icon/graph-report0.png" class="sidebar-icon-report">Laporan</a>
                            </li>
                            <li class="nav-item mt-0">
                                <a href="#" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="../../icon/exit.png">Log Out</a>
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
                            <li class="breadcrumb-item"><a href="../../Menu PIC/manajemenAkunKry.php">Manajemen Akun Karyawan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah Akun</li>
                        </ol>
                    </nav>
                </div>


                <!-- Tambah Barang -->
                <div class="container mt-4">
                    <?php if (isset($error)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-12 " style="margin-right: 20px;">
                            <div class="card border border-dark">
                                <div class="card-header bg-white border-bottom border-dark">
                                    <span class="fw-semibold">Tambah Akun</span>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-2 row">
                                            <div class="col-md-6">
                                                <label for="npk" class="form-label">NPK</label>
                                                <input type="text" class="form-control" id="npk" name="npk" value="<?= htmlspecialchars($npk) ?>" disabled>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="namaKry" class="form-label d-flex align-items-center">Nama Lengkap
                                                    <span id="namaError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                                </label>
                                                <input type="text" class="form-control" id="namaKry" name="namaKry">
                                            </div>
                                        </div>
                                        <div class="mb-2 row">
                                            <div class="col-md-6">
                                                <label for="noHP" class="form-label d-flex align-items-center">Nomor Telepon
                                                    <span id="noHPError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                                </label>
                                                <input type="text" class="form-control" id="noHP" name="noHP">
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label for="jenisRole" class="form-label">Role</label>
                                                <select class="form-select" id="jenisRole" name="jenisRole">
                                                    <option value="" selected>Pilih Role</option>
                                                    <option value="PIC Aset">PIC Aset</option>
                                                    <option value="KA UPT">KA UPT</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label for="kataSandi" class="form-label d-flex align-items-center">Kata Sandi
                                                <span id="passLengthError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Minimal 8 karakter</span>
                                                <span id="passError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                            </label>
                                            <input type="password" class="form-control" id="kataSandi" name="kataSandi">
                                        </div>
                                        <div class="mb-2">
                                            <label for="konfirmasiSandi" class="form-label d-flex align-items-center">Konfirmasi Kata Sandi
                                                <span id="passMatchError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Tidak sesuai</span>
                                                <span id="confPassError" class="text-danger ms-2" style="display:none;font-size:0.95em;">*Harus diisi</span>
                                            </label>
                                            <input type="password" class="form-control" id="konfirmasiSandi" name="konfirmasiSandi">
                                        </div>

                                        <div class="d-flex justify-content-between mt-4">
                                            <a href="../../Menu PIC/manajemenAkunKry.php" class="btn btn-secondary">Kembali</a>
                                            <button type="submit" class="btn btn-primary">Tambah</button>
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
                                    <a href="../../Menu PIC/manajemenAkunKry.php"><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></a>
                                </div>
                                <div class="modal-body">
                                    <p>Data akun berhasil ditambahkan.</p>
                                </div>
                                <div class="modal-footer">
                                    <a href="../../Menu PIC/manajemenAkunKry.php" class="btn btn-primary">OK</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End Tambah Akun -->
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
                        <a href="../../logout.php" class="btn btn-primary ps-4 pe-4">Ya</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Logout Modal -->

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- <script>
            function changeStok(val) {
                var stokInput = document.getElementById('noHP');
                var current = parseInt(stokInput.value) || 0;
                var next = current + val;
                if (next < 0) next = 0;
                stokInput.value = next;
            }
        </script> -->

        <script>
            document.querySelector('form').addEventListener('submit', function(e) {
                var nama = document.getElementById('namaKry').value.trim();
                var nohp = document.getElementById('noHP').value.trim();
                var pass = document.getElementById('kataSandi').value;
                var conf = document.getElementById('konfirmasiSandi').value;

                var namaError = document.getElementById('namaError');
                var nohpError = document.getElementById('noHPError');
                var passError = document.getElementById('passError');
                var passMatchError = document.getElementById('passMatchError');
                var confPassError = document.getElementById('confPassError');
                var passLengthError = document.getElementById('passLengthError');

                var valid = true;

                // Reset error
                namaError.style.display = 'none';
                nohpError.style.display = 'none';
                passError.style.display = 'none';
                confPassError.style.display = 'none';
                passMatchError.style.display = 'none';
                passLengthError.style.display = 'none';

                if (nama === "") {
                    namaError.style.display = 'block';
                    valid = false;
                }
                if (nohp === "") {
                    nohpError.style.display = 'block';
                    valid = false;
                }
                if (pass === "") {
                    passError.style.display = 'block';
                    valid = false;
                }
                if (pass.length > 0 && pass.length < 8) {
                    passLengthError.style.display = 'block';
                    valid = false;
                }
                if (conf === "") {
                    confPassError.style.display = 'block';
                    valid = false;
                }
                if (pass !== "" && conf !== "" && pass !== conf) {
                    passMatchError.style.display = 'block';
                    valid = false;
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