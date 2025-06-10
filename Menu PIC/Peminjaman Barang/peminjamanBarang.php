<?php
session_start();
include '../../koneksi.php';


// Pagination setup
$currentPage = basename($_SERVER['PHP_SELF']); // Determine the current page
$perPage = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Define page groups for active sidebar states
$manajemenAsetPages = ['manajemenBarang.php', 'manajemenRuangan.php', 'tambahBarang.php', 'editBarang.php', 'tambahRuangan.php', 'editRuangan.php'];
$isManajemenAsetActive = in_array($currentPage, $manajemenAsetPages);

$manajemenAkunPages = ['manajemenAkunMhs.php', 'tambahAkunMhs.php', 'editAkunMhs.php', 'manajemenAkunKry.php', 'tambahAkunKry.php', 'editAkunKry.php'];
$isManajemenAkunActive = in_array($currentPage, $manajemenAkunPages);

$peminjamanPages = ['peminjamanBarang.php', 'peminjamanRuangan.php', 'detailPeminjaman.php']; // Added detailPeminjaman.php here
$isPeminjamanActive = in_array($currentPage, $peminjamanPages);

// Hitung total data
$countQuery = "SELECT COUNT(*) AS total FROM Peminjaman_Barang";
$countResult = sqlsrv_query($conn, $countQuery);
$countRow = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC);
$totalData = $countRow['total'];
$totalPages = ceil($totalData / $perPage);

// Ambil data sesuai halaman
$offset = ($page - 1) * $perPage;
$query = "SELECT idPeminjamanBrg, idBarang, jumlahBrg, tglPeminjamanBrg, statusPeminjaman FROM Peminjaman_Barang ORDER BY idPeminjamanBrg OFFSET $offset ROWS FETCH NEXT $perPage ROWS ONLY";
$result = sqlsrv_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Peminjaman Barang - Sistem Pengelolaan Laboratorium</title>

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
            (PIC Aset)
          </span>
        </div>
      </div>
      <div class="d-flex align-items-center">
        <a href="notifPIC.php" class="me-0"><img src="../../icon/bell.png" class="profile-img img-fluid" alt="Notif"></a>
        <a href="profilPIC.php"><img src="../../icon/vector0.svg" class="profile-img img-fluid" alt="Profil"></a>
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
            <a href="../dashboardPIC.php" class="nav-link"><img src="../../icon/dashboard0.svg">Dashboard</a>
          </li>
          <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center <?php if ($isManajemenAsetActive) echo 'active'; ?>" data-bs-toggle="collapse" href="#asetSubmenu" role="button" aria-expanded="<?php echo $isManajemenAsetActive ? 'true' : 'false'; ?>" aria-controls="asetSubmenu">
              <span><img src="../../icon/layers0.png">Manajemen Aset</span>
              <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4 <?php if ($isManajemenAsetActive) echo 'show'; ?>" id="asetSubmenu">
              <a href="../manajemenBarang.php" class="nav-link <?php if ($currentPage === 'manajemenBarang.php' || $currentPage === 'tambahBarang.php' || $currentPage === 'editBarang.php') echo 'active-submenu'; ?>">Barang</a>
              <a href="../manajemenRuangan.php" class="nav-link <?php if ($currentPage === 'manajemenRuangan.php' || $currentPage === 'tambahRuangan.php' || $currentPage === 'editRuangan.php') echo 'active-submenu'; ?>">Ruangan</a>
            </div>
          </li>
          <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center <?php if ($isManajemenAkunActive) echo 'active'; ?>" data-bs-toggle="collapse" href="#akunSubmenu" role="button" aria-expanded="<?php echo $isManajemenAkunActive ? 'true' : 'false'; ?>" aria-controls="akunSubmenu">
              <span><img src="../../icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
              <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4 <?php if ($isManajemenAkunActive) echo 'show'; ?>" id="akunSubmenu">
              <a href="../manajemenAkunMhs.php" class="nav-link <?php if ($currentPage === 'manajemenAkunMhs.php' || $currentPage === 'tambahAkunMhs.php' || $currentPage === 'editAkunMhs.php') echo 'active-submenu'; ?>">Mahasiswa</a>
              <a href="../manajemenAkunKry.php" class="nav-link <?php if ($currentPage === 'manajemenAkunKry.php' || $currentPage === 'tambahAkunKry.php' || $currentPage === 'editAkunKry.php') echo 'active-submenu'; ?>">Karyawan</a>
            </div>
          </li>
          <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center <?php if ($isPeminjamanActive) echo 'active-submenu'; ?>" data-bs-toggle="collapse" href="#pinjamSubmenu" role="button" aria-expanded="<?php echo $isPeminjamanActive ? 'true' : 'false'; ?>" aria-controls="pinjamSubmenu">
              <span><img src="../../icon/ic-twotone-sync-alt0.svg">Peminjaman</span>
              <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4 <?php if ($isPeminjamanActive) echo 'show'; ?>" id="pinjamSubmenu">
              <a href="peminjamanBarang.php" class="nav-link <?php if ($currentPage === 'peminjamanBarang.php') echo 'active-submenu'; ?>">Barang</a>
              <a href="../Peminjaman Ruangan/peminjamanRuangan.php" class="nav-link <?php if ($currentPage === 'peminjamanRuangan.php') echo 'active-submenu'; ?>">Ruangan</a>
            </div>
          </li>
          <li class="nav-item mb-2">
            <a href="../laporan.php" class="nav-link"><img src="../../icon/graph-report0.png" class="sidebar-icon-report">Laporan</a>
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
                <a href="../dashboardPIC.php" class="nav-link"><img src="../../icon/dashboard0.svg">Dashboard</a>
              </li>
              <li class="nav-item mb-2">
                <a class="nav-link d-flex justify-content-between align-items-center <?php if ($isManajemenAsetActive) echo 'active'; ?>" data-bs-toggle="collapse" href="#asetSubmenuMobile" role="button" aria-expanded="<?php echo $isManajemenAsetActive ? 'true' : 'false'; ?>" aria-controls="asetSubmenuMobile">
                  <span><img src="../../icon/layers0.png">Manajemen Aset</span>
                  <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                </a>
                <div class="collapse ps-4 <?php if ($isManajemenAsetActive) echo 'show'; ?>" id="asetSubmenuMobile">
                  <a href="manajemenBarang.php" class="nav-link <?php if ($currentPage === 'manajemenBarang.php' || $currentPage === 'tambahBarang.php' || $currentPage === 'editBarang.php') echo 'active-submenu'; ?>">Barang</a>
                  <a href="manajemenRuangan.php" class="nav-link <?php if ($currentPage === 'manajemenRuangan.php' || $currentPage === 'tambahRuangan.php' || $currentPage === 'editRuangan.php') echo 'active-submenu'; ?>">Ruangan</a>
                </div>
              </li>
              <li class="nav-item mb-2">
                <a class="nav-link d-flex justify-content-between align-items-center <?php if ($isManajemenAkunActive) echo 'active'; ?>" data-bs-toggle="collapse" href="#akunSubmenuMobile" role="button" aria-expanded="<?php echo $isManajemenAkunActive ? 'true' : 'false'; ?>" aria-controls="akunSubmenuMobile">
                  <span><img src="../../icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
                  <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                </a>
                <div class="collapse ps-4 <?php if ($isManajemenAkunActive) echo 'show'; ?>" id="akunSubmenuMobile">
                  <a href="manajemenAkunMhs.php" class="nav-link <?php if ($currentPage === 'manajemenAkunMhs.php' || $currentPage === 'tambahAkunMhs.php' || $currentPage === 'editAkunMhs.php') echo 'active-submenu'; ?>">Mahasiswa</a>
                  <a href="manajemenAkunKry.php" class="nav-link <?php if ($currentPage === 'manajemenAkunKry.php' || $currentPage === 'tambahAkunKry.php' || $currentPage === 'editAkunKry.php') echo 'active-submenu'; ?>">Karyawan</a>
                </div>
              </li>
              <li class="nav-item mb-2">
                <a class="nav-link d-flex justify-content-between align-items-center <?php if ($isPeminjamanActive) echo 'active'; ?>" data-bs-toggle="collapse" href="#pinjamSubmenuMobile" role="button" aria-expanded="<?php echo $isPeminjamanActive ? 'true' : 'false'; ?>" aria-controls="pinjamSubmenuMobile">
                  <span><img src="../../icon/ic-twotone-sync-alt0.svg">Peminjaman</span>
                  <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                </a>
                <div class="collapse ps-4 <?php if ($isPeminjamanActive) echo 'show'; ?>" id="pinjamSubmenuMobile">
                  <a href="peminjamanBarang.php" class="nav-link <?php if ($currentPage === 'peminjamanBarang.php') echo 'active-submenu'; ?>">Barang</a>
                  <a href="peminjamanRuangan.php" class="nav-link <?php if ($currentPage === 'peminjamanRuangan.php') echo 'active-submenu'; ?>">Ruangan</a>
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
        <div class="mb-4">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
              <li class="breadcrumb-item active" aria-current="page">Peminjaman Barang</li>
            </ol>
          </nav>
        </div>

        <!-- Table Peminjaman Barang -->
        <div class="table-responsive">
          <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
              <tr>
                <th>ID Peminjaman</th>
                <th>ID Barang</th>
                <th>Tanggal Peminjaman</th>
                <th>Jumlah Peminjaman </th>
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
                  <td><?= htmlspecialchars($row['idPeminjamanBrg']) ?></td>
                  <td><?= htmlspecialchars($row['idBarang']) ?></td>
                  <td>
                    <?= ($row['tglPeminjamanBrg'] instanceof DateTimeInterface) ? $row['tglPeminjamanBrg']->format('D, d M Y') : 'N/A'; ?>
                  </td>
                  <td><?= htmlspecialchars($row['jumlahBrg']) ?></td>
                  <td class="text-center">
                    <?php
                    $statusFromDB = $row['statusPeminjaman'] ?? 'Menunggu Persetujuan';

                    $iconSource = 'bi-hourglass-split';
                    $statusText = 'Status Tidak Diketahui';

                    switch ($statusFromDB) {
                      case 'Menunggu Persetujuan':
                        $iconSource = '../../icon/jamkuning.svg';
                        $statusText = 'Menunggu Persetujuan';
                        break;
                      case 'Sedang Dipinjam':
                        $iconSource = '../../icon/jamhijau.svg';
                        $statusText = 'Sedang Dipinjam';
                        break;
                      case 'Ditolak':
                        $iconSource = '../../icon/silang.svg';
                        $statusText = 'Ditolak';
                        break;
                      case 'Telah Dikembalikan':
                        $iconSource = '../../icon/centang.svg';
                        $statusText = 'Telah Dikembalikan';
                        break;
                    }
                    ?>

                    <span title="<?= htmlspecialchars($statusText); ?>" style="cursor: help; vertical-align: middle;">
                      <?php
                      if (str_contains($iconSource, '.svg') || str_contains($iconSource, '.png')) {
                        echo '<img src="' . htmlspecialchars($iconSource) . '" 
                       alt="' . htmlspecialchars($statusText) . '" 
                       style="width: 30px; height: 30px;" 
                       class="me-2 mb-2">';
                      } else {
                        // JIKA TIDAK: Tampilkan sebagai font icon <i> (cara lama)
                        echo '<i class="bi ' . htmlspecialchars($iconSource) . ' me-3" 
                     style="font-size: 1.2rem;"></i>';
                      }
                      ?>
                    </span>
                    <?php if ($statusFromDB == 'Menunggu Persetujuan') { ?>
                      <a href="pengajuanBarang.php?id=<?= htmlspecialchars($row['idPeminjamanBrg']); ?>" class="text-secondary" title="Lihat Detail" style="vertical-align: middle;">
                        <i><img src="../../icon/detail.svg" alt="Detail" style="width: 25px; height: 25px; margin-bottom: 7px;"></i>
                      </a>
                    <?php } else if ($statusFromDB == 'Sedang Dipinjam') { ?>
                      <a href="pengembalianBarang.php?id=<?= htmlspecialchars($row['idPeminjamanBrg']); ?>" class="text-secondary" title="Lihat Detail" style="vertical-align: middle;">
                        <i><img src="../../icon/detail.svg" alt="Detail" style="width: 25px; height: 25px; margin-bottom: 7px;"></i>
                      </a>
                    <?php } else if ($statusFromDB == 'Ditolak') { ?>
                      <a href="detailPenolakanBarang.php?id=<?= htmlspecialchars($row['idPeminjamanBrg']); ?>" class="text-secondary" title="Lihat Detail" style="vertical-align: middle;">
                        <i><img src="../../icon/detail.svg" alt="Detail" style="width: 25px; height: 25px; margin-bottom: 7px;"></i>
                      </a>
                    <?php } else if ($statusFromDB == 'Telah Dikembalikan') { ?>
                      <a href="detailPeminjamanBarang.php?id=<?= htmlspecialchars($row['idPeminjamanBrg']); ?>" class="text-secondary" title="Lihat Detail" style="vertical-align: middle;">
                        <i><img src="../../icon/detail.svg" alt="Detail" style="width: 25px; height: 25px; margin-bottom: 7px;"></i>
                      </a>
                    <?php } ?>
                  </td>
                </tr>
              <?php }

              if (!$hasData) {
                echo '<tr><td colspan="5" class="text-center">Tidak ada data peminjaman</td></tr>';
              }
              ?>
            </tbody>
          </table>
        </div>
      </main>
      <!-- End Content Area -->
    </div>
  </div>
  <!-- End Container -->


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
  <!-- End Pagination -->

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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

</body>

</html>