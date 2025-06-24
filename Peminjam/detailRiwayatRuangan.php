<?php
session_start(); // Start the session
// include '../koneksi.php'; // Will be included later in the main content area
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detail Peminjaman Ruangan - Sistem Pengelolaan Laboratorium</title>

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
    }

    /* CSS for pkd-content-area removed as per new requirement to use form-like structure */

    /* Style for readonly form controls in detail view */
    .card-body input[readonly].form-control,
    .card-body textarea[readonly].form-control {
      background-color: #e9ecef; /* Bootstrap's default disabled/readonly background color */
      opacity: 1; /* Ensure text is fully readable */
      cursor: default; /* Indicate it's not interactive text */
    }

    .sidebar .nav-link.active,
    .sidebar .nav-link:hover {
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
    }
    
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
      height: 82vh; 
      overflow-y: auto; 
    }

    .sidebar .collapse .nav-link {
      color: #ffffff !important;
      background-color: transparent !important;
    }

    .sidebar .collapse .nav-link:hover {
      background-color: rgba(255, 255, 255, 0.15) !important;
      color: #ffffff !important;
    }

    .sidebar .collapse .nav-link.active { 
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
        height: auto; 
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
          <span class="fw-normal fs-6">Dyah Ayu Puspitosari (Peminjam)</span> <!-- Replace with PHP session data -->
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
          <li class="nav-item mb-2">
            <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="../icon/logout0.svg">Keluar</a>
          </li>
        </ul>
        <img src="../icon/atoy0.png" class="atoy-img img-fluid" alt="Atoy" />
      </nav>
      <!-- End Sidebar for large screens -->

      <!-- Offcanvas Sidebar for small screens -->
      <div class="offcanvas offcanvas-start sidebar" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header">
          <img src="../icon/logo0.png" class="sidebar-logo img-fluid" alt="Logo" />
          <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-2">
              <a href="dashboardPeminjam.php" class="nav-link"><img src="../icon/dashboard0.svg">Dashboard</a>
            </li>
            <li class="nav-item mb-2">
              <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#peminjamanSubmenuOffcanvas" role="button" aria-expanded="false" aria-controls="peminjamanSubmenuOffcanvas">
                <span><img src="../icon/peminjaman.svg">Peminjaman</span>
                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
              </a>
              <div class="collapse ps-4" id="peminjamanSubmenuOffcanvas">
                <a href="peminjamanBarang.php" class="nav-link">Barang</a>
                <a href="peminjamanRuangan.php" class="nav-link">Ruangan</a>
              </div>
            </li>
            <li class="nav-item mb-2">
              <a class="nav-link d-flex justify-content-between align-items-center parent-active" data-bs-toggle="collapse" href="#riwayatSubmenuOffcanvas" role="button" aria-expanded="true" aria-controls="riwayatSubmenuOffcanvas">
                <span><img src="../icon/riwayat.svg" style="width: 28px; height: 28px; object-fit: contain;">Riwayat</span>
                <i class="bi bi-chevron-down transition-chevron ps-3"></i>
              </a>
              <div class="collapse ps-4 show" id="riwayatSubmenuOffcanvas">
                <a href="#" class="nav-link">Barang</a> <!-- TODO: Link to actual riwayat barang page -->
                <a href="riwayatRuangan.php" class="nav-link active">Ruangan</a>
              </div>
            </li>
            <li class="nav-item mb-2">
              <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="../icon/logout0.svg">Keluar</a>
            </li>
          </ul>
        </div>
      </div>
      <!-- End Offcanvas Sidebar for small screens -->

      <!-- Content Area -->
      <main class="col bg-white px-3 px-md-4 py-3 position-relative">
        <div class="mb-3">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="dashboardPeminjam.php">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="riwayatRuangan.php">Riwayat Peminjaman Ruangan</a></li>
              <li class="breadcrumb-item active" aria-current="page">Detail Peminjaman</li>
            </ol>
          </nav>
        </div>
        
        <div class="container-fluid">
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

          if (isset($_GET['id'])) {
              $idPeminjaman = $_GET['id'];

              // Prepare SQL query to fetch all details for the specific loan ID
              // It's good practice to select specific columns instead of '*' in production
              $query = "SELECT * FROM Peminjaman_Ruangan WHERE idPeminjamanRuangan = ?";
              $params = array($idPeminjaman);
              $stmt = sqlsrv_query($conn, $query, $params);

              if ($stmt === false) {
                  echo "<div class='alert alert-danger'>Error fetching loan details: " . htmlspecialchars(print_r(sqlsrv_errors(), true)) . "</div>";
              } else {
                  if (sqlsrv_has_rows($stmt)) {
                      $loanDetails = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                      // TODO: Display details in a structured way based on loan status and UI example
                      // For now, just print all fetched data:
                      echo "<div class='card'><div class='card-body'>";
                      echo "<h5 class='card-title'>Peminjaman ID: " . htmlspecialchars($loanDetails['idPeminjamanRuangan']) . "</h5>";
                      echo "<p class='card-text'>Status: <strong>" . htmlspecialchars($loanDetails['statusPeminjaman']) . "</strong></p>";
                      echo "<h6>All Details:</h6>";
                      echo "<ul class='list-group list-group-flush'>";
                      foreach ($loanDetails as $key => $value) {
                          echo "<li class='list-group-item'><strong>" . htmlspecialchars($key) . ":</strong> ";
                          if ($value instanceof DateTimeInterface) {
                              // Format DateTime objects as Y-m-d H:i:s or as needed
                              echo htmlspecialchars($value->format('Y-m-d H:i:s'));
                          } elseif (is_string($value) || is_numeric($value) || is_null($value)) {
                              echo htmlspecialchars((string)$value);
                          } else {
                              echo htmlspecialchars(gettype($value)); // Fallback for other types
                          }
                          echo "</li>";
                      }
                      echo "</ul>";
                      echo "</div></div>";

                      // Status-specific UI sections
                      if (stripos($loanDetails['statusPeminjaman'], 'Menunggu Approval') !== false) {
                          // UI for Menunggu Approval based on editAkunKry.php form structure
                          // Adjusted for wider card display
                          echo "<div class='row mt-4'>"; // Removed justify-content-center
                          echo "  <div class='col-12'>";    // Changed from col-lg-10 to make it wider
                          echo "    <div class='card border border-dark'>";
                          echo "      <div class='card-header bg-white border-bottom border-dark'>";
                          echo "        <span class='fw-semibold'>Detail Pengajuan Peminjaman Ruangan</span>";
                          echo "      </div>";
                          echo "      <div class='card-body'>";

                          // Row 1: ID Peminjaman & ID Ruangan
                          echo "        <div class='mb-3 row'>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='idPeminjamanDetail' class='form-label'>ID Peminjaman</label>";
                          echo "            <input type='text' readonly class='form-control' id='idPeminjamanDetail' value='" . htmlspecialchars($loanDetails['idPeminjamanRuangan']) . "'>";
                          echo "          </div>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='idRuanganDetail' class='form-label'>ID Ruangan</label>";
                          echo "            <input type='text' readonly class='form-control' id='idRuanganDetail' value='" . htmlspecialchars($loanDetails['idRuangan']) . "'>";
                          echo "          </div>";
                          echo "        </div>";

                          // Row 2: Tanggal Peminjaman & Waktu Peminjaman
                          $tglPeminjamanFormatted = formatIndonesianDate($loanDetails['tglPeminjamanRuangan']);
                          $waktuMulaiFormatted = (isset($loanDetails['waktuMulaiPinjam']) && $loanDetails['waktuMulaiPinjam'] instanceof DateTimeInterface) ? $loanDetails['waktuMulaiPinjam']->format('H:i') : (isset($loanDetails['waktuMulaiPinjam']) ? htmlspecialchars((string)$loanDetails['waktuMulaiPinjam']) : 'N/A');
                          $waktuSelesaiFormatted = (isset($loanDetails['waktuSelesaiPinjam']) && $loanDetails['waktuSelesaiPinjam'] instanceof DateTimeInterface) ? $loanDetails['waktuSelesaiPinjam']->format('H:i') : (isset($loanDetails['waktuSelesaiPinjam']) ? htmlspecialchars((string)$loanDetails['waktuSelesaiPinjam']) : 'N/A');
                          $waktuPeminjamanFull = $waktuMulaiFormatted . " - " . $waktuSelesaiFormatted;
                          echo "        <div class='mb-3 row'>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='tglPeminjamanDetail' class='form-label'>Tanggal Peminjaman</label>";
                          echo "            <input type='text' readonly class='form-control' id='tglPeminjamanDetail' value='" . $tglPeminjamanFormatted . "'>";
                          echo "          </div>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='waktuPeminjamanDetail' class='form-label'>Waktu Peminjaman</label>";
                          echo "            <input type='text' readonly class='form-control' id='waktuPeminjamanDetail' value='" . $waktuPeminjamanFull . "'>";
                          echo "          </div>";
                          echo "        </div>";

                          // Row 3: NIM & NPK
                          echo "        <div class='mb-3 row'>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='nimDetail' class='form-label'>NIM</label>";
                          echo "            <input type='text' readonly class='form-control' id='nimDetail' value='" . htmlspecialchars($loanDetails['nim'] ?? 'N/A') . "'>";
                          echo "          </div>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='npkDetail' class='form-label'>NPK</label>";
                          echo "            <input type='text' readonly class='form-control' id='npkDetail' value='" . htmlspecialchars($loanDetails['npk'] ?? '-') . "'>";
                          echo "          </div>";
                          echo "        </div>";

                          // Row 4: Alasan Peminjaman (full width)
                          echo "        <div class='mb-3 row'>";
                          echo "          <div class='col-md-12'>";
                          echo "            <label for='alasanPeminjamanDetail' class='form-label'>Alasan Peminjaman</label>";
                          echo "            <textarea readonly class='form-control' id='alasanPeminjamanDetail' rows='4'>" . htmlspecialchars($loanDetails['alasanPeminjamanRuangan'] ?? 'N/A') . "</textarea>";
                          echo "          </div>";
                          echo "        </div>";

                          // Kembali button
                          echo "        <div class='mt-4 text-start'>"; // Changed text-end to text-start
                          echo "          <a href='riwayatRuangan.php' class='btn btn-secondary'>Kembali</a>";
                          echo "        </div>";

                          echo "      </div>"; // end card-body
                          echo "    </div>"; // end card
                          echo "  </div>"; // end col
                          echo "</div>"; // end row

                          // Hide the generic details display script
                          echo "<script>var genericCard = document.querySelector('.card-title'); if(genericCard) { genericCard.closest('.card').style.display = 'none'; }</script>";
                      } elseif (stripos($loanDetails['statusPeminjaman'], 'Ditolak') !== false) {
                          // UI for Ditolak status, with requested modifications
                          echo "<div class='row mt-4'>"; // Consistent with Menunggu Approval for full width
                          echo "  <div class='col-12'>"; 
                          echo "    <div class='card border border-dark'>"; // Changed border to border-dark
                          echo "      <div class='card-header bg-white border-bottom border-dark'>"; // Changed border to border-dark
                          echo "        <span class='fw-semibold text-danger'>Detail Peminjaman Ditolak</span>"; // Title text color red
                          echo "      </div>";
                          echo "      <div class='card-body'>";

                          // Row 1: ID Peminjaman & ID Ruangan
                          echo "        <div class='mb-3 row'>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='idPeminjamanDetail_ditolak' class='form-label'>ID Peminjaman</label>";
                          echo "            <input type='text' readonly class='form-control' id='idPeminjamanDetail_ditolak' value='" . htmlspecialchars($loanDetails['idPeminjamanRuangan']) . "'>";
                          echo "          </div>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='idRuanganDetail_ditolak' class='form-label'>ID Ruangan</label>";
                          echo "            <input type='text' readonly class='form-control' id='idRuanganDetail_ditolak' value='" . htmlspecialchars($loanDetails['idRuangan']) . "'>";
                          echo "          </div>";
                          echo "        </div>";

                          // Row 2: Tanggal Peminjaman & Waktu Peminjaman
                          $tglPeminjamanFormatted = formatIndonesianDate($loanDetails['tglPeminjamanRuangan']);
                          $waktuMulaiFormatted = (isset($loanDetails['waktuMulaiPinjam']) && $loanDetails['waktuMulaiPinjam'] instanceof DateTimeInterface) ? $loanDetails['waktuMulaiPinjam']->format('H:i') : (isset($loanDetails['waktuMulaiPinjam']) ? htmlspecialchars((string)$loanDetails['waktuMulaiPinjam']) : 'N/A');
                          $waktuSelesaiFormatted = (isset($loanDetails['waktuSelesaiPinjam']) && $loanDetails['waktuSelesaiPinjam'] instanceof DateTimeInterface) ? $loanDetails['waktuSelesaiPinjam']->format('H:i') : (isset($loanDetails['waktuSelesaiPinjam']) ? htmlspecialchars((string)$loanDetails['waktuSelesaiPinjam']) : 'N/A');
                          $waktuPeminjamanFull = $waktuMulaiFormatted . " - " . $waktuSelesaiFormatted;
                          echo "        <div class='mb-3 row'>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='tglPeminjamanDetail_ditolak' class='form-label'>Tanggal Peminjaman</label>";
                          echo "            <input type='text' readonly class='form-control' id='tglPeminjamanDetail_ditolak' value='" . $tglPeminjamanFormatted . "'>";
                          echo "          </div>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='waktuPeminjamanDetail_ditolak' class='form-label'>Waktu Peminjaman</label>";
                          echo "            <input type='text' readonly class='form-control' id='waktuPeminjamanDetail_ditolak' value='" . $waktuPeminjamanFull . "'>";
                          echo "          </div>";
                          echo "        </div>";

                          // Row 3: NIM & NPK
                          echo "        <div class='mb-3 row'>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='nimDetail_ditolak' class='form-label'>NIM</label>";
                          echo "            <input type='text' readonly class='form-control' id='nimDetail_ditolak' value='" . htmlspecialchars($loanDetails['nim'] ?? 'N/A') . "'>";
                          echo "          </div>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='npkDetail_ditolak' class='form-label'>NPK</label>";
                          echo "            <input type='text' readonly class='form-control' id='npkDetail_ditolak' value='" . htmlspecialchars($loanDetails['npk'] ?? '-') . "'>";
                          echo "          </div>";
                          echo "        </div>";

                          // Row 4: Alasan Peminjaman & Alasan Penolakan (Side-by-side)
                          echo "        <div class='mb-3 row'>";
                          echo "          <div class='col-md-6'>"; // Alasan Peminjaman
                          echo "            <label for='alasanPeminjamanDetail_ditolak' class='form-label'>Alasan Peminjaman</label>";
                          echo "            <textarea readonly class='form-control' id='alasanPeminjamanDetail_ditolak' rows='3'>" . htmlspecialchars($loanDetails['alasanPeminjamanRuangan'] ?? 'N/A') . "</textarea>";
                          echo "          </div>";
                          echo "          <div class='col-md-6'>"; // Alasan Penolakan
                          echo "            <label for='alasanPenolakanDetail' class='form-label fw-bold text-danger'>Alasan Penolakan</label>";
                          echo "            <textarea readonly class='form-control' id='alasanPenolakanDetail' rows='3'>" . htmlspecialchars($loanDetails['alasanPenolakan'] ?? 'Tidak ada catatan penolakan.') . "</textarea>";
                          echo "          </div>";
                          echo "        </div>";

                          // Kembali button
                          echo "        <div class='mt-4 text-start'>";
                          echo "          <a href='riwayatRuangan.php' class='btn btn-secondary'>Kembali</a>";
                          echo "        </div>";

                          echo "      </div>"; // end card-body
                          echo "    </div>"; // end card
                          echo "  </div>"; // end col
                          echo "</div>"; // end row

                          // Hide the generic details display script
                          echo "<script>var genericCard = document.querySelector('.card-title'); if(genericCard) { genericCard.closest('.card').style.display = 'none'; }</script>";
                      } elseif (stripos($loanDetails['statusPeminjaman'], 'Sedang Dipinjam') !== false) {
                          // UI for Sedang Dipinjam status with file upload capabilities
                          echo "<form method='POST' action='process_documentation.php' enctype='multipart/form-data'>"; // Placeholder action
                          echo "<input type='hidden' name='idPeminjamanRuangan' value='" . htmlspecialchars($loanDetails['idPeminjamanRuangan']) . "'>";

                          echo "<div class='row mt-4'>";
                          echo "  <div class='col-12'>"; 
                          echo "    <div class='card border border-success'>"; // Success border for active loan
                          echo "      <div class='card-header bg-white border-bottom border-success'>";
                          echo "        <span class='fw-semibold'>Detail Peminjaman Ruangan (Sedang Dipinjam)</span>";
                          echo "      </div>";
                          echo "      <div class='card-body'>";

                          // Row 1: ID Peminjaman & ID Ruangan (Read-only)
                          echo "        <div class='mb-3 row'>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='idPeminjamanDetail_dipinjam' class='form-label'>ID Peminjaman</label>";
                          echo "            <input type='text' readonly class='form-control' id='idPeminjamanDetail_dipinjam' value='" . htmlspecialchars($loanDetails['idPeminjamanRuangan']) . "'>";
                          echo "          </div>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='idRuanganDetail_dipinjam' class='form-label'>ID Ruangan</label>";
                          echo "            <input type='text' readonly class='form-control' id='idRuanganDetail_dipinjam' value='" . htmlspecialchars($loanDetails['idRuangan']) . "'>";
                          echo "          </div>";
                          echo "        </div>";

                          // Row 2: Tanggal Peminjaman & Waktu Peminjaman (Read-only)
                          $tglPeminjamanFormatted = formatIndonesianDate($loanDetails['tglPeminjamanRuangan']);
                          $waktuMulaiFormatted = (isset($loanDetails['waktuMulaiPinjam']) && $loanDetails['waktuMulaiPinjam'] instanceof DateTimeInterface) ? $loanDetails['waktuMulaiPinjam']->format('H:i') : (isset($loanDetails['waktuMulaiPinjam']) ? htmlspecialchars((string)$loanDetails['waktuMulaiPinjam']) : 'N/A');
                          $waktuSelesaiFormatted = (isset($loanDetails['waktuSelesaiPinjam']) && $loanDetails['waktuSelesaiPinjam'] instanceof DateTimeInterface) ? $loanDetails['waktuSelesaiPinjam']->format('H:i') : (isset($loanDetails['waktuSelesaiPinjam']) ? htmlspecialchars((string)$loanDetails['waktuSelesaiPinjam']) : 'N/A');
                          $waktuPeminjamanFull = $waktuMulaiFormatted . " - " . $waktuSelesaiFormatted;
                          echo "        <div class='mb-3 row'>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='tglPeminjamanDetail_dipinjam' class='form-label'>Tanggal Peminjaman</label>";
                          echo "            <input type='text' readonly class='form-control' id='tglPeminjamanDetail_dipinjam' value='" . $tglPeminjamanFormatted . "'>";
                          echo "          </div>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='waktuPeminjamanDetail_dipinjam' class='form-label'>Waktu Peminjaman</label>";
                          echo "            <input type='text' readonly class='form-control' id='waktuPeminjamanDetail_dipinjam' value='" . $waktuPeminjamanFull . "'>";
                          echo "          </div>";
                          echo "        </div>";

                          // Row 3: NIM & NPK (Read-only)
                          echo "        <div class='mb-3 row'>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='nimDetail_dipinjam' class='form-label'>NIM</label>";
                          echo "            <input type='text' readonly class='form-control' id='nimDetail_dipinjam' value='" . htmlspecialchars($loanDetails['nim'] ?? 'N/A') . "'>";
                          echo "          </div>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='npkDetail_dipinjam' class='form-label'>NPK</label>";
                          echo "            <input type='text' readonly class='form-control' id='npkDetail_dipinjam' value='" . htmlspecialchars($loanDetails['npk'] ?? '-') . "'>";
                          echo "          </div>";
                          echo "        </div>";

                          // Row 4: Alasan Peminjaman (Read-only)
                          echo "        <div class='mb-3 row'>";
                          echo "          <div class='col-md-12'>";
                          echo "            <label for='alasanPeminjamanDetail_dipinjam' class='form-label'>Alasan Peminjaman</label>";
                          echo "            <textarea readonly class='form-control' id='alasanPeminjamanDetail_dipinjam' rows='3'>" . htmlspecialchars($loanDetails['alasanPeminjamanRuangan'] ?? 'N/A') . "</textarea>";
                          echo "          </div>";
                          echo "        </div>";

                          // Row 5: Dokumentasi Sebelum Pemakaian (File Upload)
                          echo "        <div class='mb-3 row'>";
                          echo "          <div class='col-md-12'>";
                          echo "            <label class='form-label'>Dokumentasi Sebelum Pemakaian</label>";
                          echo "            <div class='input-group'>";
                          echo "              <label for='dokumentasiSebelum_dipinjam' class='btn btn-outline-secondary' type='button'><i class='bi bi-upload me-2'></i>Pilih File</label>";
                          echo "              <input type='file' class='d-none' id='dokumentasiSebelum_dipinjam' name='dokumentasi_sebelum' accept='image/*'>";
                          echo "              <span id='fileNameSebelum_dipinjam' class='form-control bg-light' readonly>Belum ada file dipilih</span>"; // bg-light for readonly appearance
                          echo "            </div>";
                          echo "          </div>";
                          echo "        </div>";

                          // Row 6: Dokumentasi Setelah Pemakaian (File Upload)
                          echo "        <div class='mb-3 row'>";
                          echo "          <div class='col-md-12'>";
                          echo "            <label class='form-label'>Dokumentasi Setelah Pemakaian</label>";
                          echo "            <div class='input-group'>";
                          echo "              <label for='dokumentasiSetelah_dipinjam' class='btn btn-outline-secondary' type='button'><i class='bi bi-upload me-2'></i>Pilih File</label>";
                          echo "              <input type='file' class='d-none' id='dokumentasiSetelah_dipinjam' name='dokumentasi_setelah' accept='image/*'>";
                          echo "              <span id='fileNameSetelah_dipinjam' class='form-control bg-light' readonly>Belum ada file dipilih</span>"; // bg-light for readonly appearance
                          echo "            </div>";
                          echo "          </div>";
                          echo "        </div>";

                          // Buttons: Kembali and Kirim
                          echo "        <div class='mt-4 d-flex justify-content-between'>";
                          echo "          <a href='riwayatRuangan.php' class='btn btn-secondary'>Kembali</a>";
                          echo "          <button type='submit' class='btn btn-primary' id='kirimDokumentasiBtn_dipinjam' style='display:none;'>Kirim</button>";
                          echo "        </div>";

                          echo "      </div>"; // end card-body
                          echo "    </div>"; // end card
                          echo "  </div>"; // end col
                          echo "</div>"; // end row
                          echo "</form>"; // end form

                          // JavaScript for file upload interactivity
                          echo "<script>
                            const fileInputSebelum_dipinjam = document.getElementById('dokumentasiSebelum_dipinjam');
                            const fileInputSetelah_dipinjam = document.getElementById('dokumentasiSetelah_dipinjam');
                            const fileNameDisplaySebelum_dipinjam = document.getElementById('fileNameSebelum_dipinjam');
                            const fileNameDisplaySetelah_dipinjam = document.getElementById('fileNameSetelah_dipinjam');
                            const kirimBtn_dipinjam = document.getElementById('kirimDokumentasiBtn_dipinjam');

                            function checkFilesAndToggleButton_dipinjam() {
                                if (fileInputSebelum_dipinjam.files.length > 0 && fileInputSetelah_dipinjam.files.length > 0) {
                                    kirimBtn_dipinjam.style.display = 'block';
                                } else {
                                    kirimBtn_dipinjam.style.display = 'none';
                                }
                            }

                            if(fileInputSebelum_dipinjam) {
                                fileInputSebelum_dipinjam.addEventListener('change', function() {
                                    if (this.files.length > 0) {
                                        fileNameDisplaySebelum_dipinjam.textContent = this.files[0].name;
                                    } else {
                                        fileNameDisplaySebelum_dipinjam.textContent = 'Belum ada file dipilih';
                                    }
                                    checkFilesAndToggleButton_dipinjam();
                                });
                            }

                            if(fileInputSetelah_dipinjam) {
                                fileInputSetelah_dipinjam.addEventListener('change', function() {
                                    if (this.files.length > 0) {
                                        fileNameDisplaySetelah_dipinjam.textContent = this.files[0].name;
                                    } else {
                                        fileNameDisplaySetelah_dipinjam.textContent = 'Belum ada file dipilih';
                                    }
                                    checkFilesAndToggleButton_dipinjam();
                                });
                            }
                            checkFilesAndToggleButton_dipinjam(); // Initial check
                          </script>";

                          // Hide the generic details display script
                          echo "<script>var genericCard = document.querySelector('.card-title'); if(genericCard) { genericCard.closest('.card').style.display = 'none'; }</script>";
                      } 
                      // Add other elseif conditions for other statuses here
                      // else { echo "<p>No specific UI defined for status: " . htmlspecialchars($loanDetails['statusPeminjaman']) . "</p>"; }

                  } else {
                      echo "<div class='alert alert-warning'>No loan details found for ID: " . htmlspecialchars($idPeminjaman) . "</div>";
                  }
              }
              sqlsrv_free_stmt($stmt);
              sqlsrv_close($conn);
          } else {
              echo "<div class='alert alert-danger'>No Peminjaman ID provided.</div>";
          }
          ?>
        </div>

      </main>
      <!-- End Content Area -->
    </div>
  </div>
  <!-- End Container -->

  <!-- Logout Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Keluar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Apakah Anda yakin ingin keluar?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <a href="../logout.php" class="btn btn-danger">Keluar</a>
        </div>
      </div>
    </div>
  </div>
  <!-- End Logout Modal -->

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // JavaScript for sidebar submenu toggle
    document.querySelectorAll('.sidebar .nav-link[data-bs-toggle="collapse"]').forEach(function (element) {
      element.addEventListener('click', function (e) {
        let chevron = this.querySelector('.bi-chevron-down');
        if (chevron) {
          chevron.classList.toggle('rotated');
        }
        // Optional: Logic to ensure only one submenu is open at a time can be added here
      });
    });

    // Ensure parent menu item stays highlighted when a submenu item is active
    // This script assumes the active submenu item has class 'active' and its parent anchor has 'parent-active'
    // The HTML structure for sidebar needs to be consistent for this to work
    // Example: Riwayat (parent-active) -> Ruangan (active)
    // This logic might need adjustment based on exact HTML and class usage
    const activeSubMenuItem = document.querySelector('.sidebar .collapse .nav-link.active');
    if (activeSubMenuItem) {
        const parentCollapse = activeSubMenuItem.closest('.collapse');
        if (parentCollapse) {
            const parentAnchor = document.querySelector('a[href="#' + parentCollapse.id + '"]');
            if (parentAnchor) {
                // parentAnchor.classList.add('parent-active'); // Already handled by 'parent-active' class in HTML
                // parentAnchor.setAttribute('aria-expanded', 'true'); // Already handled by 'aria-expanded' in HTML
                // parentCollapse.classList.add('show'); // Already handled by 'show' class in HTML
            }
        }
    }
  </script>
</body>
</html>
