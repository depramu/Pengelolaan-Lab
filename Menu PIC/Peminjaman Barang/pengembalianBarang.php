    <?php
    include '../../koneksi.php';
    session_start();

    $showModal = false;
    $idPeminjamanBrg = $_GET['id'] ?? '';

    // Hentikan jika tidak ada ID
    if (empty($idPeminjamanBrg)) {
        die("Akses tidak valid. ID Peminjaman tidak ditemukan.");
    }

    // Ambil data awal peminjaman
    $data = [];
    $jumlahBrg = 0;
    $idBarang = null;

    // Menggunakan prepared statement yang lebih aman untuk GET
    $query_get = "SELECT jumlahBrg, idBarang FROM Peminjaman_Barang WHERE idPeminjamanBrg = ?";
    $params_get = [$idPeminjamanBrg];
    $stmt_get = sqlsrv_query($conn, $query_get, $params_get);

    if ($stmt_get && ($data = sqlsrv_fetch_array($stmt_get, SQLSRV_FETCH_ASSOC))) {
        $jumlahBrg = (int)$data['jumlahBrg'];
        $idBarang = $data['idBarang'];
    } else {
        // Tampilkan error jika query gagal atau data tidak ditemukan
        die("Data peminjaman tidak ditemukan atau terjadi kesalahan query. " . print_r(sqlsrv_errors(), true));
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Ambil data dari form
        $jumlahPengembalian = (int)$_POST['jumlahPengembalian'];
        $catatan = $_POST['catatanPengembalianBarang'];
        $kondisiBrg = $_POST['kondisiBrg'];

        // Validasi Sederhana di Sisi Server (jumlahBrg dari data awal)
        if ($jumlahPengembalian <= 0 || $jumlahPengembalian > $jumlahBrg || empty($kondisiBrg) || $kondisiBrg == 'Pilih Kondisi Barang') {
            $error = "Data tidak valid. Pastikan jumlah pengembalian benar (tidak melebihi jumlah pinjam) dan kondisi barang telah dipilih.";
        } else {
            // --- MULAI TRANSAKSI DATABASE (PENTING!) ---
            sqlsrv_begin_transaction($conn);

            // LANGKAH 1: Masukkan data ke tabel pengembalian_barang
            $query_insert_pengembalian = "INSERT INTO pengembalian_barang 
                                            (idPeminjamanBrg, jumlahPengembalian, kondisiBrg, catatanPengembalianBarang) 
                                          VALUES (?, ?, ?, ?)";
            $params_insert_pengembalian = [$idPeminjamanBrg, $jumlahPengembalian, $kondisiBrg, $catatan];
            $stmt_insert_pengembalian = sqlsrv_query($conn, $query_insert_pengembalian, $params_insert_pengembalian);

            // LANGKAH 2: Update jumlahBrg (jumlah yang dipinjam) di tabel Peminjaman_Barang
            $sisaPinjaman = $jumlahBrg - $jumlahPengembalian;
            $statusPeminjaman = ($sisaPinjaman == 0) ? 'Telah Dikembalikan' : 'Sebagian Dikembalikan';

            $query_update_peminjaman = "UPDATE Peminjaman_Barang 
                                        SET jumlahBrg = ?, 
                                            statusPeminjaman = ?
                                        WHERE idPeminjamanBrg = ?";
            $params_update_peminjaman = [$sisaPinjaman, $statusPeminjaman, $idPeminjamanBrg];
            $stmt_update_peminjaman = sqlsrv_query($conn, $query_update_peminjaman, $params_update_peminjaman);

            // LANGKAH 3: Update stok di tabel master Barang (stok bertambah sesuai jumlah pengembalian)
            $query_update_stok = "UPDATE Barang SET stokBarang = stokBarang + ? WHERE idBarang = ?";
            $params_update_stok = [$jumlahPengembalian, $idBarang];
            $stmt_update_stok = sqlsrv_query($conn, $query_update_stok, $params_update_stok);

            // LANGKAH 4: Cek apakah SEMUA (3) query berhasil
            if ($stmt_insert_pengembalian && $stmt_update_peminjaman && $stmt_update_stok) {
                sqlsrv_commit($conn); // Jika semua berhasil, simpan perubahan
                $showModal = true;
            } else {
                sqlsrv_rollback($conn); // Jika ada yang gagal, batalkan semua perubahan
                $error = "Gagal memproses pengembalian barang. Silakan coba lagi.";
                // die(print_r(sqlsrv_errors(), true)); 
            }
            // --- AKHIR TRANSAKSI DATABASE ---
        }
    }
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
                    <a href="notif.php" class="me-0"><img src="../../icon/bell.png" class="profile-img img-fluid" alt="Notif"></a>
                    <a href="profil.php"><img src="../../icon/vector0.svg" class="profile-img img-fluid" alt="Profil"></a>
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
                            <a href="dashboardPeminjam.php" class="nav-link"><img src="../../icon/dashboard0.svg">Dashboard</a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#manajemenAsetSubmenu" role="button" aria-expanded="false" aria-controls="manajemenAsetSubmenu">
                                <span><img src="../../icon/layers0.png">Manajemen Aset</span>
                                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                            </a>
                            <div class="collapse ps-4 <?php if ($isManajemenAsetActive) echo 'show'; ?>" id="manajemenAsetSubmenu">
                                <a href="manajemenBarang.php" class="nav-link <?php if ($currentPage === 'manajemenBarang.php' || $currentPage === 'tambahBarang.php' || $currentPage === 'editBarang.php') echo 'active-submenu'; ?>">Barang</a>
                                <a href="manajemenRuangan.php" class="nav-link <?php if ($currentPage === 'manajemenRuangan.php') echo 'active-submenu'; ?>">Ruangan</a>
                            </div>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#akunSubmenu" role="button" aria-expanded="false" aria-controls="akunSubmenu">
                                <span><img src="../../icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
                                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                            </a>
                            <div class="collapse ps-4" id="akunSubmenu">
                                <a href="#" class="nav-link">Mahasiswa</a>
                                <a href="#" class="nav-link">Karyawan</a>
                            </div>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#pinjamSubmenuMobile" role="button" aria-expanded="false" aria-controls="pinjamSubmenuMobile">
                                <span><img src="../../icon/ic-twotone-sync-alt0.svg">Peminjaman</span>
                                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                            </a>
                            <div class="collapse ps-4" id="pinjamSubmenuMobile">
                                <a href="peminjamanBarang.php" class="nav-link">Barang</a>
                                <a href="#" class="nav-link">Ruangan</a>
                            </div>
                        </li>
                        <li class="nav-item mb-2">
                            <a href="#" class="nav-link"><img src="../../icon/graph-report0.png" class="sidebar-icon-report">Laporan</a>
                        </li>
                        <li class="nav-item mt-0">
                            <a href="logout.php" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="../../icon/exit.png">Log Out</a>
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
                                    <a href="index.php" class="nav-link"><img src="../../icon/dashboard0.svg">Dashboard</a>
                                </li>
                                <li class="nav-item mb-2">
                                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#asetSubmenuMobile" role="button" aria-expanded="false" aria-controls="asetSubmenuMobile">
                                        <span><img src="../../icon/layers0.png">Manajemen Aset</span>
                                        <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                                    </a>
                                    <div class="collapse ps-4" id="asetSubmenuMobile">
                                        <a href="manajemenBarang.php" class="nav-link <?php if ($currentPage === 'manajemenBarang.php' || $currentPage === 'tambahBarang.php' || $currentPage === 'editBarang.php') echo 'active-submenu'; ?>">Barang</a>
                                        <a href="manajemenRuangan.php" class="nav-link <?php if ($currentPage === 'manajemenRuangan.php') echo 'active-submenu'; ?>">Ruangan</a>
                                    </div>
                                </li>
                                <li class="nav-item mb-2">
                                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#akunSubmenuMobile" role="button" aria-expanded="false" aria-controls="akunSubmenuMobile">
                                        <span><img src="../../icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
                                        <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                                    </a>
                                    <div class="collapse ps-4" id="akunSubmenuMobile">
                                        <a href="#" class="nav-link">Mahasiswa</a>
                                        <a href="#" class="nav-link">Karyawan</a>
                                    </div>
                                </li>
                                <li class="nav-item mb-2">
                                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#pinjamSubmenuMobile" role="button" aria-expanded="false" aria-controls="pinjamSubmenuMobile">
                                        <span><img src="../../icon/ic-twotone-sync-alt0.svg">Peminjaman</span>
                                        <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                                    </a>
                                    <div class="collapse ps-4" id="pinjamSubmenuMobile">
                                        <a href="peminjamanBarang.php" class="nav-link">Barang</a>
                                        <a href="#" class="nav-link">Ruangan</a>
                                    </div>
                                </li>
                                <li class="nav-item mb-2">
                                    <a href="#" class="nav-link"><img src="../../icon/graph-report0.png" class="sidebar-icon-report">Laporan</a>
                                </li>
                                <li class="nav-item mt-0">
                                    <a href="logout.php" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="../../icon/exit.png">Log Out</a>
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
                                <li class="breadcrumb-item active" aria-current="page">Pengembalian Barang</li>
                            </ol>
                        </nav>
                    </div>


                    <!-- Pengembalian Barang -->
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
                                        <span class="fw-semibold">Pengembalian Peminjaman Barang</span>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST">
                                            <div class="mb-2">
                                                <label for="idPeminjamanBrg" class="form-label">ID Peminjaman Barang</label>
                                                <input type="text" class="form-control" id="idPeminjamanBrg" name="idPeminjamanBrg" value="<?= isset($idPeminjamanBrg) ? htmlspecialchars($idPeminjamanBrg) : '' ?>" disabled>
                                            </div>
                                            <div class="mb-2 row">
                                                <div class="col-md-3">
                                                    <label for="jumlahBrg" class="form-label">Jumlah Peminjaman</label>
                                                    <input type="text" class="form-control" id="jumlahBrg" name="jumlahBrg" value="<?= $jumlahBrg ?>" disabled>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="jumlahPengembalian" class="form-label w-100 text-center">Jumlah Pengembalian
                                                        <span id="jumlahError" class="text-danger small mt-1" style="font: size 0.95em;display:none;">*Harus Diisi</span>
                                                    </label>
                                                    <div class="input-group mx-auto" style="max-width: 140px;">
                                                        <button class="btn btn-outline-secondary" type="button" onclick="changeStok(-1)">-</button>
                                                        <input class="form-control text-center" id="jumlahPengembalian" name="jumlahPengembalian" value="0" min="0" required style="max-width: 70px;">
                                                        <button class="btn btn-outline-secondary" type="button" onclick="changeStok(1)">+</button>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="txtKondisi" class="form-label">Kondisi Barang
                                                        <span id="kondisiError" class="text-danger small mt-1" style="font: size 0.95em;display:none;">*Harus Dipilih</span>
                                                    </label>
                                                    <select class="form-select" id="txtKondisi" name="kondisiBrg">
                                                        <option selected>Pilih Kondisi Barang</option>
                                                        <option value="Baik" <?= (isset($data['kondisiBrg']) && $data['kondisiBrg'] == 'Baik') ? 'selected' : '' ?>>Baik</option>
                                                        <option value="Rusak" <?= (isset($data['kondisiBrg']) && $data['kondisiBrg'] == 'Rusak') ? 'selected' : '' ?>>Rusak</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <label for="catatanPengembalianBarang" class="form-label">Catatan Pengembalian
                                                    <span id="catatanError" class="text-danger small mt-1" style="font: size 0.95em;display:none;">*Harus Diisi</span>
                                                </label>
                                                <textarea type="text" class="form-control" id="catatanPengembalianBarang" name="catatanPengembalianBarang" rows="3" style="resize: none;"><?= isset($data['catatanPengembalianBarang']) ? htmlspecialchars($data['catatanPengembalianBarang']) : '' ?></textarea>
                                            </div>
                                            <div class="d-flex justify-content-between mt-4">
                                                <a href="peminjamanBarang.php" class="btn btn-secondary">Kembali</a>
                                                <button type="submit" class="btn btn-primary">Kirim</button>
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
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Peminjaman berhasil Dikembalikan.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="peminjamanBarang.php" class="btn btn-primary">OK</a>
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
                            <h5 class="modal-title" id="logoutModalLabel"><i><img src="icon/info.svg" alt="" style="width: 25px; height: 25px; margin-bottom: 5px; margin-right: 10px;"></i>PERINGATAN</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Yakin ingin log out?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger ps-4 pe-4" data-bs-dismiss="modal">Tidak</button>
                            <a href="logout.php" class="btn btn-primary ps-4 pe-4">Ya</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Logout Modal -->

            <!-- Bootstrap JS -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

            <script>
                // Fungsi stepper untuk tombol +/-
                function changeStok(val) {
                    // Targetkan ID yang benar: 'jumlahPengembalian'
                    let stokInput = document.getElementById('jumlahPengembalian');
                    let maxStok = parseInt(document.getElementById('jumlahBrg').value) || 0;
                    let current = parseInt(stokInput.value) || 0;
                    let next = current + val;

                    if (next < 0) next = 0;
                    if (next > maxStok) next = maxStok; // Batasi agar tidak lebih dari jumlah pinjaman
                    stokInput.value = next;
                }
            </script>


            <script>
                // Fungsi validasi form sebelum submit
                document.querySelector('form').addEventListener('submit', function(e) {
                    let valid = true;

                    // Validasi Jumlah Pengembalian
                    // Targetkan ID yang benar: 'jumlahPengembalian'
                    const jumlahInput = document.getElementById('jumlahPengembalian');
                    const jumlahError = document.getElementById('jumlahError');
                    const jumlahPinjam = parseInt(document.getElementById('jumlahBrg').value) || 0;

                    if (parseInt(jumlahInput.value) <= 0) {
                        jumlahError.textContent = '*Jumlah harus lebih dari 0.';
                        jumlahError.style.display = 'block';
                        valid = false;
                    } else if (parseInt(jumlahInput.value) > jumlahPinjam) {
                        jumlahError.textContent = '*Jumlah melebihi yang dipinjam.';
                        jumlahError.style.display = 'block';
                        valid = false;
                    } else {
                        jumlahError.style.display = 'none';
                    }

                    // Validasi Kondisi Barang
                    const kondisiSelect = document.getElementById('txtKondisi');
                    const kondisiError = document.getElementById('kondisiError');
                    if (kondisiSelect.value === 'Pilih Kondisi Barang') {
                        kondisiError.style.display = 'block';
                        valid = false;
                    } else {
                        kondisiError.style.display = 'none';
                    }

                    // Validasi Catatan Pengembalian
                    // Targetkan ID yang benar: 'catatanPengembalianBarang'
                    const catatanInput = document.getElementById('catatanPengembalianBarang');
                    const catatanError = document.getElementById('catatanError');
                    if (catatanInput.value.trim() === '') {
                        catatanError.style.display = 'block';
                        valid = false;
                    } else {
                        catatanError.style.display = 'none';
                    }

                    if (!valid) {
                        e.preventDefault(); // Hentikan pengiriman form jika tidak valid
                    }
                });
            </script>

            <?php if ($showModal) : ?>
                <script>
                    let modal = new bootstrap.Modal(document.getElementById('successModal'));
                    modal.show();
                </script>
            <?php endif; ?>

    </body>

    </html>