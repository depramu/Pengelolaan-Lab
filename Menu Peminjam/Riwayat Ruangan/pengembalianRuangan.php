<?php
include '../../koneksi.php';

$data = null;
$error_message = null;

if (isset($_GET['id'])) {
    $idPeminjamanRuanganDariURL = $_GET['id'];

    $sql = "SELECT 
                p.idPeminjamanRuangan,
                p.idRuangan,
                p.nim,
                p.npk,
                p.tglPeminjamanRuangan,
                p.waktuMulai,
                p.waktuSelesai,
                p.alasanPeminjamanRuangan,
                p.statusPeminjaman,
                r.dokumentasiSebelum,
                r.dokumentasiSesudah,
                r.catatanPengembalianRuangan,
                r.kondisiRuangan
            FROM 
                Peminjaman_Ruangan p
            LEFT JOIN 
                Pengembalian_Ruangan r ON p.idPeminjamanRuangan = r.idPeminjamanRuangan
            WHERE 
                p.idPeminjamanRuangan = ?";

    $params = array($idPeminjamanRuanganDariURL);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        $error_message = "Gagal mengambil data. Error: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
    } else {
        $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if (!$data) {
            $error_message = "Data peminjaman dengan ID '" . htmlspecialchars($idPeminjamanRuanganDariURL) . "' tidak ditemukan.";
        }
    }
} else {
    $error_message = "ID Peminjaman Ruangan tidak disertakan di URL.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistem Pengelolaan Laboratorium - Form Pengembalian</title>
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
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
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
        .error-text {
            color: red;
            font-size: 0.8em;
            margin-left: 8px;
            font-style: italic;
        }
        .scrollable-card-content {
            max-height: 67vh; 
            overflow-y: auto;
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
            .scrollable-card-content {
                max-height: 70vh;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid min-vh-100 d-flex flex-column p-0">
        <header class="d-flex align-items-center justify-content-between px-3 px-md-5 py-3">
            <div class="d-flex align-items-center">
                <img src="../../icon/logo0.png" class="sidebar-logo img-fluid" alt="Logo" />
                <div class="d-none d-md-block ps-3 ps-md-4" style="margin-left: 5vw;">
                    <span class="fw-semibold fs-3">Hello,</span><br>
                    <span class="fw-normal fs-6">Dyah Ayu Puspitosari (Peminjam)</span>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <a href="notif.php" class="me-0"><img src="../../icon/bell.png" class="profile-img img-fluid" alt="Notif"></a>
                <a href="profil.php"><img src="../../icon/vector0.svg" class="profile-img img-fluid" alt="Profil"></a>
                <button class="btn btn-primary d-lg-none ms-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </header>
        <div class="row flex-grow-1 g-0">
            <nav class="col-auto sidebar d-none d-lg-flex flex-column p-3 ms-lg-4">
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link"><img src="../../icon/dashboard0.svg">Dashboard</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#peminjamanSubmenu" role="button" aria-expanded="false" aria-controls="peminjamanSubmenu">
                            <span><img src="../../icon/peminjaman.svg">Peminjaman</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4" id="peminjamanSubmenu">
                            <a href="peminjamanBarang.php" class="nav-link">Barang</a>
                            <a href="#" class="nav-link">Ruangan</a>
                        </div>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#riwayatSubmenu" role="button" aria-expanded="false" aria-controls="riwayatSubmenu">
                            <span><img src="../../icon/riwayat.svg" style="width: 28px; height: 28px; object-fit: contain;">Riwayat</span>
                            <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                        </a>
                        <div class="collapse ps-4" id="riwayatSubmenu">
                            <a href="#" class="nav-link">Barang</a>
                            <a href="#" class="nav-link active-submenu">Ruangan</a>
                        </div>
                    </li>
                    <li class="nav-item mt-0">
                        <a href="#" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="../../icon/exit.png">Log Out</a>
                    </li>
                </ul>
            </nav>
            <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasSidebarLabel">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0">
                    <nav class="sidebar flex-column p-4 h-100">
                    </nav>
                </div>
            </div>
            <main class="col bg-white px-3 px-md-4 py-3 position-relative">
                <div class="mb-2">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Sistem Pengelolaan Lab</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Riwayat</li>
                            <li class="breadcrumb-item active" aria-current="page">Ruangan</li>
                            <li class="breadcrumb-item active" aria-current="page">Form Peminjaman</li>
                        </ol>
                    </nav>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-12 " style="margin-right: 5px;">
                        <div class="card border border-dark">
                            <div class="card-header bg-white border-bottom border-dark">
                                <span class="fw-semibold">Riwayat Pengajuan Peminjaman Ruangan</span>
                            </div>
                            <div class="card-body p-4 scrollable-card-content">
                                <?php if ($error_message): ?>
                                    <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                                <?php elseif ($data): ?>
                                    <form id="formPengembalian" action="proses_pengembalian.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="idPeminjamanRuangan" value="<?= htmlspecialchars($data['idPeminjamanRuangan'] ?? '') ?>">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="idRuangan" class="form-label">ID Ruangan</label>
                                                    <input type="text" class="form-control" id="idRuangan" name="idRuangan" value="<?= htmlspecialchars($data['idRuangan'] ?? '') ?>" disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="tanggalPeminjaman" class="form-label">Tanggal Peminjaman</label>
                                                    <input type="text" class="form-control" id="tanggalPeminjaman" name="tanggalPeminjaman" value="<?= ($data['tglPeminjamanRuangan'] instanceof DateTime ? $data['tglPeminjamanRuangan']->format('l, d F Y') : htmlspecialchars($data['tglPeminjamanRuangan'] ?? '')) ?>" disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="waktuPeminjaman" class="form-label">Waktu Peminjaman</label>
                                                    <input type="text" class="form-control" id="waktuPeminjaman" name="waktuPeminjaman" value="<?= ($data['waktuMulai'] instanceof DateTime ? $data['waktuMulai']->format('H:i') : '') . ' - ' . ($data['waktuSelesai'] instanceof DateTime ? $data['waktuSelesai']->format('H:i') : '') ?>" disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="alasanPeminjaman" class="form-label">Alasan Peminjaman</label>
                                                    <textarea class="form-control" id="alasanPeminjaman" name="alasanPeminjaman" rows="3" disabled><?= htmlspecialchars($data['alasanPeminjamanRuangan'] ?? '') ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="idPeminjaman" class="form-label">ID Peminjaman</label>
                                                    <input type="text" class="form-control" id="idPeminjaman" name="idPeminjaman" value="<?= htmlspecialchars($data['idPeminjamanRuangan'] ?? '') ?>" disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="nim" class="form-label">NIM</label>
                                                    <input type="text" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($data['nim'] ?? '') ?>" disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="npk" class="form-label">NPK</label>
                                                    <input type="text" class="form-control" id="npk" name="npk" value="<?= htmlspecialchars($data['npk'] ?? '') ?>" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <h5 class="mt-4 mb-3">Form Pengembalian</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="dokSebelum" class="form-label">Dokumentasi Sebelum Pemakaian <span id="errorDokSebelum" class="error-text"></span></label>
                                                    <input type="file" class="form-control" id="dokSebelum" name="dokSebelum" accept=".jpg, .jpeg, .png, .heif">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="dokSesudah" class="form-label">Dokumentasi Sesudah Pemakaian <span id="errorDokSesudah" class="error-text"></span></label>
                                                    <input type="file" class="form-control" id="dokSesudah" name="dokSesudah" accept=".jpg, .jpeg, .png, .heif">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-4 pt-3">
                                            <a href="riwayatRuangan.php" class="btn btn-secondary px-4">Kembali</a>
                                            <button type="submit" name="submit_pengembalian" class="btn btn-primary px-4">Kirim</button>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        Silakan pilih data peminjaman yang ingin dilihat detailnya, atau pastikan ID Peminjaman valid.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
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
                    <button type="button" class="btn btn-primary ps-4 pe-4">Ya</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('formPengembalian').addEventListener('submit', function(event) {
            let isValid = true;
            const dokSebelum = document.getElementById('dokSebelum');
            const errorDokSebelum = document.getElementById('errorDokSebelum');
            const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.heif)$/i;
            errorDokSebelum.textContent = '';
            if (dokSebelum.files.length === 0) {
                errorDokSebelum.textContent = '*Harus Diisi';
                isValid = false;
            } else if (!allowedExtensions.exec(dokSebelum.value)) {
                errorDokSebelum.textContent = '*Format file harus .jpg, .jpeg, .png, atau .heif';
                dokSebelum.value = '';
                isValid = false;
            }
            const dokSesudah = document.getElementById('dokSesudah');
            const errorDokSesudah = document.getElementById('errorDokSesudah');
            errorDokSesudah.textContent = '';
            if (dokSesudah.files.length === 0) {
                errorDokSesudah.textContent = '*Harus Diisi';
                isValid = false;
            } else if (!allowedExtensions.exec(dokSesudah.value)) {
                errorDokSesudah.textContent = '*Format file harus .jpg, .jpeg, .png, atau .heif';
                dokSesudah.value = '';
                isValid = false;
            }
            if (!isValid) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>