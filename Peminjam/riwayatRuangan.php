<?php
session_start(); // Start the session, common for user-specific pages
// include '../koneksi.php'; // Include your database connection file, adjust path if needed
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Riwayat Peminjaman Ruangan - Sistem Pengelolaan Laboratorium</title>

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
    
    /* Specific active state for main menu item when a submenu item is active */
    .sidebar .nav-link.parent-active {
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
      height: 82vh; /* Consider making this min-height or auto if content overflows */
      overflow-y: auto; /* Add scroll for main content if it overflows */
    }

    .sidebar .collapse .nav-link {
      color: #ffffff !important;
      background-color: transparent !important;
    }

    .sidebar .collapse .nav-link:hover {
      background-color: rgba(255, 255, 255, 0.15) !important;
      color: #ffffff !important;
    }

    .sidebar .collapse .nav-link.active { /* Style for active submenu item */
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
        height: auto; /* Adjust height for mobile */
        min-height: 90vh;
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
          <span class="fw-normal fs-6">Dyah Ayu Puspitosari (Peminjam)</span> <!-- Replace with PHP session data if available -->
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
    <!-- End Header -->

    <!-- Content -->
    <div class="row flex-grow-1 g-0">
      <!-- Sidebar for large screens -->
      <nav class="col-auto sidebar d-none d-lg-flex flex-column p-3 ms-lg-4">
        <ul class="nav nav-pills flex-column mb-auto">
          <li class="nav-item mb-2">
            <a href="dashboardPeminjam.php" class="nav-link"><img src="../icon/dashboard0.svg">Dashboard</a>
          </li>
          <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#peminjamanSubmenu" role="button" aria-expanded="false" aria-controls="peminjamanSubmenu">
              <span><img src="../icon/peminjaman.svg">Peminjaman</span>
              <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4" id="peminjamanSubmenu">
              <a href="peminjamanBarang.php" class="nav-link">Barang</a>
              <a href="peminjamanRuangan.php" class="nav-link">Ruangan</a>
            </div>
          </li>
          <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center parent-active" data-bs-toggle="collapse" href="#riwayatSubmenu" role="button" aria-expanded="true" aria-controls="riwayatSubmenu">
              <span><img src="../icon/riwayat.svg" style="width: 28px; height: 28px; object-fit: contain;">Riwayat</span>
              <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4 show" id="riwayatSubmenu">
              <a href="#" class="nav-link">Barang</a> <!-- TODO: Link to actual riwayat barang page -->
              <a href="riwayatRuangan.php" class="nav-link active">Ruangan</a>
            </div>
          </li>
          <li class="nav-item mt-0">
            <a href="#" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="../icon/exit.png">Log Out</a>
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
                <a href="dashboardPeminjam.php" class="nav-link"><img src="../icon/dashboard0.svg">Dashboard</a>
              </li>
              <li class="nav-item mb-2">
                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#peminjamanSubmenuMobile" role="button" aria-expanded="false" aria-controls="peminjamanSubmenuMobile">
                  <span><img src="../icon/peminjaman.svg">Peminjaman</span>
                  <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                </a>
                <div class="collapse ps-4" id="peminjamanSubmenuMobile">
                  <a href="peminjamanBarang.php" class="nav-link">Barang</a>
                  <a href="peminjamanRuangan.php" class="nav-link">Ruangan</a>
                </div>
              </li>
              <li class="nav-item mb-2">
                <a class="nav-link d-flex justify-content-between align-items-center parent-active" data-bs-toggle="collapse" href="#riwayatSubmenuMobile" role="button" aria-expanded="true" aria-controls="riwayatSubmenuMobile">
                  <span><img src="../icon/riwayat.svg">Riwayat</span>
                  <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                </a>
                <div class="collapse ps-4 show" id="riwayatSubmenuMobile">
                  <a href="#" class="nav-link">Barang</a> <!-- TODO: Link to actual riwayat barang page -->
                  <a href="riwayatRuangan.php" class="nav-link active">Ruangan</a>
                </div>
              </li>
              <li class="nav-item mt-0">
                <a href="#" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="../icon/exit.png">Log Out</a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
      <!-- End Offcanvas Sidebar for small screens -->

      <!-- Content Area -->
      <main class="col bg-white px-3 px-md-4 py-3 position-relative">
        <div class="mb-3">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="dashboardPeminjam.php">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="#">Riwayat</a></li>
              <li class="breadcrumb-item active" aria-current="page">Riwayat Peminjaman Ruangan</li>
            </ol>
          </nav>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
              <tr>
                <th>ID Peminjaman</th>
                <th>ID Ruangan</th>
                <th>Tanggal Peminjaman</th>
                <th>Waktu Mulai</th>
                <th>Waktu Selesai</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Helper function for Indonesian Date Formatting
              if (!function_exists('formatIndonesianDate')) {
                  function formatIndonesianDate($dateInput) {
                      if (!$dateInput) return 'N/A';
              
                      try {
                          if ($dateInput instanceof DateTimeInterface) {
                              $date = $dateInput;
                          } else {
                              $date = new DateTime($dateInput);
                          }
                      } catch (Exception $e) {
                          return htmlspecialchars((string)$dateInput); 
                      }
              
                      $days = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
                      $months = array(
                          1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                          5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                          9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                      );
              
                      $dayName = $days[(int)$date->format('w')];
                      $dayOfMonth = $date->format('d');
                      $monthName = $months[(int)$date->format('n')];
                      $year = $date->format('Y');
              
                      return "$dayName, $dayOfMonth $monthName $year";
                  }
              }
              include '../koneksi.php'; // Connect to the database

              // User-specific filtering placeholder (same as before)
              $query = "SELECT idPeminjamanRuangan, idRuangan, tglPeminjamanRuangan, waktuMulaiPinjam, waktuSelesaiPinjam, statusPeminjaman FROM Peminjaman_Ruangan ORDER BY idPeminjamanRuangan ASC"; 
              $params = array(); 

              $stmt = sqlsrv_query($conn, $query, $params);

              if ($stmt === false) {
                echo "<tr><td colspan='5' class='text-center text-danger'>Error fetching data: " . htmlspecialchars(print_r(sqlsrv_errors(), true)) . "</td></tr>";
              } else {
                if (sqlsrv_has_rows($stmt)) {
                  while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['idPeminjamanRuangan']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['idRuangan']) . "</td>";
                    
                    // Tanggal Peminjaman (using formatIndonesianDate function)
                    $tglPeminjaman = $row['tglPeminjamanRuangan'];
                    echo "<td>" . formatIndonesianDate($tglPeminjaman) . "</td>";

                    // Waktu Mulai (using H:i format if DateTime, else as is)
                    $waktuMulai = $row['waktuMulaiPinjam'];
                    echo "<td>" . (isset($waktuMulai) ? ($waktuMulai instanceof DateTimeInterface ? $waktuMulai->format('H:i') : htmlspecialchars((string)$waktuMulai)) : 'N/A') . "</td>";
                    
                    // Waktu Selesai (using H:i format if DateTime, else as is)
                    $waktuSelesai = $row['waktuSelesaiPinjam'];
                    echo "<td>" . (isset($waktuSelesai) ? ($waktuSelesai instanceof DateTimeInterface ? $waktuSelesai->format('H:i') : htmlspecialchars((string)$waktuSelesai)) : 'N/A') . "</td>";
                    
                    // Aksi Column (Status Icon + Detail Icon)
                    echo "<td>";
                    
                    // Status Icon Logic
                    $statusText = $row['statusPeminjaman'] ?? 'Unknown';
                    $iconClass = 'bi-question-circle-fill text-secondary'; // Default for unknown
                    $iconTitle = 'Status: ' . htmlspecialchars($statusText); // Default title

                    // User-defined status mapping
                    if (stripos($statusText, 'Menunggu Approval') !== false) {
                        $iconClass = 'bi-clock-history text-warning';
                        $iconTitle = 'Menunggu Approval';
                    } elseif (stripos($statusText, 'Sedang dipinjam') !== false) {
                        $iconClass = 'bi-clock-fill text-success';
                        $iconTitle = 'Sedang dipinjam';
                    } elseif (stripos($statusText, 'Selesai') !== false || stripos($statusText, 'Completed') !== false) {
                        $iconClass = 'bi-check-circle-fill text-success';
                        $iconTitle = 'Selesai';
                    } elseif (stripos($statusText, 'Ditolak') !== false) {
                        $iconClass = 'bi-x-circle-fill text-danger';
                        $iconTitle = 'Ditolak';
                    } elseif (stripos($statusText, 'Disetujui') !== false) { // This will be caught if 'Menunggu Approval' or 'Sedang dipinjam' aren't matched first.
                        $iconClass = 'bi-check-lg text-primary';             // Consider if 'Disetujui' is a distinct, earlier phase.
                        $iconTitle = 'Disetujui';
                    } elseif (stripos($statusText, 'Dibatalkan') !== false || stripos($statusText, 'Cancelled') !== false) {
                        $iconClass = 'bi-slash-circle-fill text-dark';
                        $iconTitle = 'Dibatalkan';
                    }
                    // Note: The order of these elseif conditions matters if a status string could potentially match multiple conditions.
                    
                    echo "<span title=\"" . htmlspecialchars($iconTitle) . "\" style=\"cursor: help; vertical-align: middle;\"><i class=\"bi " . $iconClass . " me-3\" style=\"font-size: 1.3rem;\"></i></span>";
                    
                    // Detail Icon
                    echo "<a href='detailRiwayatRuangan.php?id=" . urlencode($row['idPeminjamanRuangan']) . "' class='text-info' title='Detail Peminjaman' style='text-decoration: none;'><i class='bi bi-list-ul' style='font-size: 1.3rem;'></i></a>"; 
                    echo "</td>"; 
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='5' class='text-center'>No room loan history found.</td></tr>";
                }
                sqlsrv_free_stmt($stmt);
              } 
              // sqlsrv_close($conn); // Not strictly necessary here as script ends
              ?>
            </tbody>
          </table>
        </div>
        <!-- Atoy image, if needed on this page -->
        <!-- <img src="../icon/atoy0.png" class="atoy-img d-none d-md-block img-fluid" alt="Atoy" /> -->
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
          <h5 class="modal-title" id="logoutModalLabel"><i><img src="../icon/info.svg" alt="" style="width: 25px; height: 25px; margin-bottom: 5px; margin-right: 10px;"></i>PERINGATAN</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Yakin ingin log out?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger ps-4 pe-4" data-bs-dismiss="modal">Tidak</button>
          <button type="button" class="btn btn-primary ps-4 pe-4" onclick="window.location.href='../logout.php';">Ya</button> <!-- Assuming logout.php is in root -->
        </div>
      </div>
    </div>
  </div>
  <!-- End Logout Modal -->

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>