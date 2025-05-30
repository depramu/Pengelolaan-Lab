<?php
include 'koneksi.php';
$query = "SELECT idPeminjamanBrg, idBarang, jumlahBrg, tglPeminjamanBrg FROM Peminjaman_Barang";
$result = sqlsrv_query($conn, $query);
$currentPage = basename($_SERVER['PHP_SELF']); // Determine the current page

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
        <img src="icon/logo0.png" class="sidebar-logo img-fluid" alt="Logo" />
        <div class="d-none d-md-block ps-3 ps-md-4">
          <span class="fw-semibold fs-3">Hello,</span><br>
          <span class="fw-normal fs-6">Nadira Anindita (PIC)</span>
        </div>
      </div>
      <div class="d-flex align-items-center">
        <a href="notif.php" class="me-0"><img src="icon/bell.png" class="profile-img img-fluid" alt="Notif"></a>
        <a href="profil.php"><img src="icon/vector0.svg" class="profile-img img-fluid" alt="Profil"></a>
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
            <?php
            $peminjamanPages = ['peminjamanBarang.php', 'peminjamanRuangan.php'];
            $isPeminjamanActive = in_array($currentPage, $peminjamanPages);
            ?>
            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#pinjamSubmenu" role="button" aria-expanded="false" aria-controls="pinjamSubmenu">
              <span><img src="icon/ic-twotone-sync-alt0.svg">Peminjaman</span>
              <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4 <?php if ($isPeminjamanActive) echo 'show'; ?>" id="pinjamSubmenu">
              <a href="peminjamanBarang.php" class="nav-link <?php if ($currentPage === 'peminjamanBarang.php') echo 'active-submenu'; ?>">Barang</a>
              <a href="peminjamanRuangan.php" class="nav-link <?php if ($currentPage === 'peminjamanRuangan.php') echo 'active-submenu'; ?>">Ruangan</a>
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
                <a href="template.php" class="nav-link"><img src="icon/dashboard0.svg">Dashboard</a>
              </li>
              <li class="nav-item mb-2">
                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#asetSubmenuMobile" role="button" aria-expanded="false" aria-controls="asetSubmenuMobile">
                  <span><img src="icon/layers0.png">Manajemen Aset</span>
                  <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                </a>
                <div class="collapse ps-4" id="asetSubmenuMobile">
                  <a href="#" class="nav-link">Barang</a>
                  <a href="#" class="nav-link">Ruangan</a>
                </div>
              </li>
              <li class="nav-item mb-2">
                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#akunSubmenuMobile" role="button" aria-expanded="false" aria-controls="akunSubmenuMobile">
                  <span><img src="icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
                  <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                </a>
                <div class="collapse ps-4" id="akunSubmenuMobile">
                  <a href="#" class="nav-link">Mahasiswa</a>
                  <a href="#" class="nav-link">Karyawan</a>
                </div>
              </li>
              <li class="nav-item mb-2">
                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#pinjamSubmenuMobile" role="button" aria-expanded="false" aria-controls="pinjamSubmenuMobile">
                  <span><img src="icon/ic-twotone-sync-alt0.svg">Peminjaman</span>
                  <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                </a>
                <div class="collapse ps-4" id="pinjamSubmenuMobile">
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
        </div>
      </div>
      <!-- End Offcanvas Sidebar for small screens -->

      <!-- Content Area -->
      <main class="col bg-white px-4 py-3 position-relative">
        <div class="mb-4">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="template.php">Sistem Pengelolaan Lab</a></li>
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
                <th>Jumlah </th>
                <th>Tanggal Peminjaman</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Initialize IntlDateFormatter for Indonesian date format.
              // Ensure the 'intl' PHP extension is enabled in your environment.
              $dateFormatter = null;
              if (class_exists('IntlDateFormatter')) {
                $dateFormatter = new IntlDateFormatter(
                  'id_ID', // Locale for Indonesian
                  IntlDateFormatter::FULL, // Date type (not strictly necessary when pattern is used)
                  IntlDateFormatter::NONE, // Time type (not strictly necessary when pattern is used)
                  ini_get('date.timezone') ?: 'Asia/Jakarta', // Timezone
                  IntlDateFormatter::GREGORIAN, // Calendar
                  'EEEE, dd MMMM yyyy' // Desired pattern: e.g., Senin, 07 April 2025
                );
              }

              while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
              ?>
                <tr>
                  <td><?= $row['idPeminjamanBrg'] ?></td>
                  <td><?= $row['idBarang'] ?></td>
                  <td><?= $row['jumlahBrg'] ?></td>
                  <td>
                    <?php
                    if ($dateFormatter && isset($row['tglPeminjamanBrg']) && $row['tglPeminjamanBrg'] instanceof DateTimeInterface) {
                      echo htmlspecialchars($dateFormatter->format($row['tglPeminjamanBrg']));
                    } elseif (isset($row['tglPeminjamanBrg']) && $row['tglPeminjamanBrg'] instanceof DateTimeInterface) {
                      // Fallback if IntlDateFormatter is not available but it's a DateTime object
                      echo htmlspecialchars($row['tglPeminjamanBrg']->format('D, d M Y')); // e.g., Mon, 07 Apr 2025
                    } elseif (isset($row['tglPeminjamanBrg'])) {
                      // If it's already a string or other type, display as is (escaped).
                      echo htmlspecialchars((string)$row['tglPeminjamanBrg']);
                    } else {
                      echo 'N/A'; // No date provided
                    }
                    ?>
                  </td>
                  <td>
                    <?php
                    // --- ACTION COLUMN - STATUS ICON PLACEHOLDER LOGIC ---
                    $peminjamanId = $row['idPeminjamanBrg'] ?? ''; // Get ID for demo
                    $iconClass = 'bi-hourglass-split text-info'; // Default icon for unknown status
                    $iconTitle = 'Status Tidak Diketahui';

                    if (!empty($peminjamanId)) {
                      if (substr($peminjamanId, -1) === '1' && substr($peminjamanId, 0, 4) === 'PB00') { // For PB001
                        $iconClass = 'bi-clock-history text-warning';
                        $iconTitle = 'Menunggu Persetujuan';
                      } elseif (substr($peminjamanId, -1) === '2' && substr($peminjamanId, 0, 4) === 'PB00') { // For PB002
                        $iconClass = 'bi-clock-fill text-success';
                        $iconTitle = 'Proses Verifikasi'; // Green clock
                      } elseif (substr($peminjamanId, -1) === '3' && substr($peminjamanId, 0, 4) === 'PB00') { // For PB003
                        $iconClass = 'bi-check-circle-fill text-success';
                        $iconTitle = 'Disetujui';
                      } elseif (substr($peminjamanId, -1) === '4' && substr($peminjamanId, 0, 4) === 'PB00') { // For PB004
                        $iconClass = 'bi-x-circle-fill text-danger';
                        $iconTitle = 'Ditolak';
                      }
                    }
                    // --- END OF STATUS ICON PLACEHOLDER LOGIC ---
                    ?>
                    <span title="<?= htmlspecialchars($iconTitle); ?>" style="cursor: help; vertical-align: middle;">
                      <i class="bi <?= $iconClass; ?> me-2" style="font-size: 1.2rem;"></i>
                    </span>
                    <a href="detail_peminjam  an.php?id=<?= htmlspecialchars($row['idPeminjamanBrg']); ?>" class="text-secondary" title="Lihat Detail" style="vertical-align: middle;">
                      <i><img src="icon/detail.svg" alt="" style="width: 25px; height: 25px; margin-bottom: 7px;"></i>
                    </a>
                  </td>
                </tr>
              <?php } // End while loop 
              ?>
            </tbody>
          </table>
        </div>
      </main>
      <!-- End Content Area -->
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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

</body>

</html>