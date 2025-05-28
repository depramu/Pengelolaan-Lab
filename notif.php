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
            height: 80vh;
            margin-left: 20px;
            border-radius: 12px;

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
            width: 160px;
            position: absolute;
            right: 60px;
            bottom: 30px;
        }

        main {
            margin-left: 20px;
            margin-right: 20px;
            margin-bottom: 40px;
            border-radius: 12px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid min-vh-100 d-flex flex-column p-0">
        <!-- Header -->
        <header class="d-flex justify-content-between align-items-center px-5 py-3">
            <img src="icon/logo0.png" class="sidebar-logo" alt="Logo" />
            <div class="d-flex flex-column align-items mt-2" style="margin-left: -54%;">
                <span class="fw-semibold fs-3 font-poppins">Hello,</span>
                <span class="fw-normal fs-6 font-poppins">Nadira Anindita (PIC)</span>
            </div>
            <div>
                <a href="notif.php" class="me-0"><img src="icon/bell.png" class="profile-img" alt="Notif"></a>
                <a href="profil.php"><img src="icon/vector0.svg" class="profile-img" alt="Profil"></a>
            </div>
        </header>
        <div class="row flex-grow-1 g-0">

            <!-- Sidebar -->
            <nav class="col-auto sidebar d-flex flex-column p-4">
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item mb-2">
                        <a href="template.php" class="nav-link"><img src="icon/dashboard0.svg">Dashboard</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#asetSubmenu" role="button" aria-expanded="false" aria-controls="asetSubmenu">
                            <span><img src="icon/layers0.png">Manajemen Aset</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4" id="asetSubmenu">
                            <a href="#" class="nav-link">Barang</a>
                            <a href="#" class="nav-link">Ruangan</a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#akunSubmenu" role="button" aria-expanded="false" aria-controls="akunSubmenu">
                            <span><img src="icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4" id="akunSubmenu">
                            <a href="#" class="nav-link">Mahasiswa</a>
                            <a href="#" class="nav-link">Karyawan</a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#pinjamSubmenu" role="button" aria-expanded="false" aria-controls="pinjamSubmenu">
                            <span><img src="icon/ic-twotone-sync-alt0.svg">Peminjaman</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4" id="pinjamSubmenu">
                            <a href="peminjamanBarang.php" class="nav-link">Barang</a>
                            <a href="#" class="nav-link">Ruangan</a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link"><img src="icon/graph-report0.png" class="sidebar-icon-report">Laporan</a>
                    </li>
                    <li class="nav-item mt-0">
                        <a href="#" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="icon/exit.png">Log Out</a>
                    </li>
                </ul>
            </nav>
            <!-- End Sidebar -->

            <!-- Modal Show Exit -->
            <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="logoutModalLabel"><i><img src="icon/info.svg" alt="" style="width: 25px; height: 25px; margin-bottom: 5px; margin-right: 10px;"></i>PERINGATAN</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="margin-left: 35px;">
                            Yakin ingin log out?
                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                            <button type="button" class="btn btn-danger ps-4 pe-4" data-bs-dismiss="modal">Tidak</button>
                            <a href="logout.php" class="btn btn-primary ps-4 pe-4">Ya</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal Show Exit -->

            <!-- Content Area -->
            <main class="col bg-white px-4 py-3 position-relative">
                <div class="mb-5">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="template.php">Sistem Pengelolaan Lab</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Notifikasi</li>
                        </ol>
                    </nav>
                </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



</body>

</html>