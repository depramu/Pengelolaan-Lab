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
        <img src="icon/logo0.png" class="sidebar-logo img-fluid" alt="Logo" />
        <div class="d-none d-md-block ps-3 ps-md-4" style="margin-left: 5vw;">
          <span class="fw-semibold fs-3">Hello,</span><br>
          <span class="fw-normal fs-6">Admin (PIC)</span> <!-- Replace with PHP session data -->
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
    <!-- End Header -->

    <!-- Content -->
    <div class="row flex-grow-1 g-0">
      <!-- Sidebar for large screens -->
      <nav class="col-auto sidebar d-none d-lg-flex flex-column p-3 ms-lg-4">
        <ul class="nav nav-pills flex-column mb-auto">
          <li class="nav-item mb-2">
            <a href="index.php" class="nav-link"><img src="icon/dashboard0.svg">Dashboard</a>
          </li>
          <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#asetSubmenu" role="button" aria-expanded="false" aria-controls="asetSubmenu">
              <span><img src="icon/layers0.png">Manajemen Aset</span>
              <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4" id="asetSubmenu">
              <a href="manajemenBarang.php" class="nav-link">Barang</a>
              <a href="#" class="nav-link">Ruangan</a>
            </div>
          </li>
          <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#akunSubmenu" role="button" aria-expanded="false" aria-controls="akunSubmenu">
              <span class="d-flex align-items-center"><img src="icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
              <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4" id="akunSubmenu">
              <a href="#" class="nav-link">Mahasiswa</a>
              <a href="#" class="nav-link">Karyawan</a>
            </div>
          </li>
          <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#pinjamSubmenu" role="button" aria-expanded="true" aria-controls="pinjamSubmenu">
              <span><img src="icon/ic-twotone-sync-alt0.svg">Peminjaman</span>
              <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4 show" id="pinjamSubmenu">
              <a href="peminjamanBarang.php" class="nav-link">Barang</a>
              <a href="riwayatPeminjamanAdmin.php" class="nav-link active">Ruangan</a>
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
                <a href="index.php" class="nav-link"><img src="icon/dashboard0.svg">Dashboard</a>
              </li>
              <li class="nav-item mb-2">
                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#asetSubmenuMobile" role="button" aria-expanded="false" aria-controls="asetSubmenuMobile">
                  <span><img src="icon/layers0.png">Manajemen Aset</span>
                  <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                </a>
                <div class="collapse ps-4" id="asetSubmenuMobile">
                  <a href="manajemenBarang.php" class="nav-link">Barang</a>
                  <a href="#" class="nav-link">Ruangan</a>
                </div>
              </li>
              <li class="nav-item mb-2">
                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#akunSubmenuMobile" role="button" aria-expanded="false" aria-controls="akunSubmenuMobile">
                  <span class="d-flex align-items-center"><img src="icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
                  <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                </a>
                <div class="collapse ps-4" id="akunSubmenuMobile">
                  <a href="#" class="nav-link">Mahasiswa</a>
                  <a href="#" class="nav-link">Karyawan</a>
                </div>
              </li>
              <li class="nav-item mb-2">
                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#pinjamSubmenuMobile" role="button" aria-expanded="true" aria-controls="pinjamSubmenuMobile">
                  <span><img src="icon/ic-twotone-sync-alt0.svg">Peminjaman</span>
                  <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                </a>
                <div class="collapse ps-4 show" id="pinjamSubmenuMobile">
                  <a href="peminjamanBarang.php" class="nav-link">Barang</a>
                  <a href="riwayatPeminjamanAdmin.php" class="nav-link active">Ruangan</a>
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
      <main class="col bg-white px-3 px-md-4 py-3 position-relative">
        <div class="mb-3">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="riwayatPeminjamanAdmin.php">Riwayat Peminjaman Ruangan</a></li>
              <li class="breadcrumb-item active" aria-current="page">Detail Peminjaman Ruangan</li>
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
          include 'koneksi.php'; // Connect to the database

          if (isset($_GET['id'])) {
              $idPeminjaman = $_GET['id'];

              // Prepare SQL query to fetch all details for the specific loan ID
              // It's good practice to select specific columns instead of '*' in production
              $query = "SELECT pr.*, r.namaRuangan, pen.alasanPenolakan AS alasanPenolakan_explicit
                        FROM peminjaman_ruangan pr
                        LEFT JOIN ruangan r ON pr.idRuangan = r.idRuangan
                        LEFT JOIN Penolakan pen ON pr.idPeminjamanRuangan = pen.idPeminjamanRuangan
                        WHERE pr.idPeminjamanRuangan = ?";
              $params = array($idPeminjaman);
              $stmt = sqlsrv_query($conn, $query, $params);

              if ($stmt === false) {
                  echo "<div class='alert alert-danger'>Error fetching loan details: " . htmlspecialchars(print_r(sqlsrv_errors(), true)) . "</div>";
              } else {
                  if (sqlsrv_has_rows($stmt)) {
                      $loanDetails = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                      // TODO: Display details in a structured way based on loan status and UI example
                      // Generic details table removed as per request.

                      // Status-specific UI sections
                      if (stripos($loanDetails['statusPeminjaman'], 'Menunggu Approval') !== false) {
                          // Define formatted dates and times first
                          $tglPeminjamanFormatted = formatIndonesianDate($loanDetails['tglPeminjamanRuangan']);
                          $waktuMulaiFormatted = (isset($loanDetails['waktuMulaiPinjam']) && $loanDetails['waktuMulaiPinjam'] instanceof DateTimeInterface) ? $loanDetails['waktuMulaiPinjam']->format('H:i') : (isset($loanDetails['waktuMulaiPinjam']) ? htmlspecialchars((string)$loanDetails['waktuMulaiPinjam']) : 'N/A');
                          $waktuSelesaiFormatted = (isset($loanDetails['waktuSelesaiPinjam']) && $loanDetails['waktuSelesaiPinjam'] instanceof DateTimeInterface) ? $loanDetails['waktuSelesaiPinjam']->format('H:i') : (isset($loanDetails['waktuSelesaiPinjam']) ? htmlspecialchars((string)$loanDetails['waktuSelesaiPinjam']) : 'N/A');

                          echo "<div class='row mt-4'>";
                          echo "  <div class='col-12'>";
                          echo "    <div class='card border border-dark'>";
                          echo "      <div class='card-header bg-white border-bottom border-dark'>";
                          echo "        <span class='fw-semibold'>Detail Pengajuan Peminjaman Ruangan</span>";
                          echo "      </div>";
                          echo "      <div class='card-body'>";
                          echo "        <form method='POST' action='process_persetujuan.php' id='approvalForm'>";
                          echo "          <input type='hidden' name='idPeminjamanRuangan' value='" . htmlspecialchars($loanDetails['idPeminjamanRuangan']) . "'>";
                          echo "          <input type='hidden' name='action' id='approvalAction'>";

                          // Row 1: ID Peminjaman & ID Ruangan
                          echo "          <div class='mb-3 row'>";
                          echo "            <div class='col-md-6'>";
                          echo "              <label for='idPeminjamanDetail' class='form-label'>ID Peminjaman Ruangan</label>";
                          echo "              <input type='text' readonly class='form-control' id='idPeminjamanDetail' value='" . htmlspecialchars($loanDetails['idPeminjamanRuangan']) . "'>";
                          echo "            </div>";
                          echo "            <div class='col-md-6'>";
                          echo "              <label for='idRuanganDetail' class='form-label'>ID Ruangan</label>";
                          echo "              <input type='text' readonly class='form-control' id='idRuanganDetail' value='" . htmlspecialchars($loanDetails['idRuangan']) . "'>";
                          echo "            </div>";
                          echo "          </div>";

                          // Row 2: Tanggal Peminjaman (Left) & Waktu Mulai/Selesai (Right)
                          echo "          <div class='mb-3 row'>";
                          // Tanggal Peminjaman (Left Side)
                          echo "            <div class='col-md-6'>";
                          echo "              <label for='tglPeminjamanDetail' class='form-label'>Tanggal Peminjaman</label>";
                          echo "              <input type='text' readonly class='form-control' id='tglPeminjamanDetail' value='" . $tglPeminjamanFormatted . "'>";
                          echo "            </div>";
                          // Waktu Mulai & Selesai (Right Side, nested)
                          echo "            <div class='col-md-6'>";
                          echo "              <div class='row'>";
                          echo "                <div class='col-md-6'>";
                          echo "                  <label for='waktuMulaiDetail' class='form-label'>Waktu Mulai</label>";
                          echo "                  <input readonly type='text' class='form-control' id='waktuMulaiDetail' value='" . $waktuMulaiFormatted . "'>";
                          echo "                </div>";
                          echo "                <div class='col-md-6'>";
                          echo "                  <label for='waktuSelesaiDetail' class='form-label'>Waktu Selesai</label>";
                          echo "                  <input readonly type='text' class='form-control' id='waktuSelesaiDetail' value='" . $waktuSelesaiFormatted . "'>";
                          echo "                </div>";
                          echo "              </div>"; // End nested row for time
                          echo "            </div>"; // End col-md-6 for time container
                          echo "          </div>"; // End row for Tanggal & Waktu

                          // Row 3: NIM & NPK
                          echo "          <div class='mb-3 row'>";
                          echo "            <div class='col-md-6'>";
                          echo "              <label for='nimDetail' class='form-label'>NIM</label>";
                          echo "              <input type='text' readonly class='form-control' id='nimDetail' value='" . htmlspecialchars($loanDetails['nim'] ?? 'N/A') . "'>";
                          echo "            </div>";
                          echo "            <div class='col-md-6'>";
                          echo "              <label for='npkDetail' class='form-label'>NPK</label>";
                          echo "              <input type='text' readonly class='form-control' id='npkDetail' value='" . htmlspecialchars($loanDetails['npk'] ?? '-') . "'>";
                          echo "            </div>";
                          echo "          </div>";

                          // Row 4: Alasan Peminjaman (Adjusted row number in comment)
                          echo "          <div class='mb-3 row'>";
                          echo "            <div class='col-12'>";
                          echo "              <label for='alasanPeminjamanDetail' class='form-label'>Alasan Peminjaman</label>";
                          echo "              <textarea readonly class='form-control' id='alasanPeminjamanDetail' rows='3'>" . htmlspecialchars($loanDetails['alasanPeminjamanRuangan'] ?? 'N/A') . "</textarea>";
                          echo "            </div>";
                          echo "          </div>";

                          // Action Buttons (Tolak & Setuju - Swapped order, Setuju is blue)
                          echo "          <div class='mt-4 d-flex justify-content-end'>";
                          // Tolak button now links to PeminjamanRuanganDitolak.php
                          echo "            <a href='PeminjamanRuanganDitolak.php?id=" . htmlspecialchars($loanDetails['idPeminjamanRuangan']) . "' style='margin-right: 10px; text-decoration: none;'>";
                          echo "              <div class='rectangle-213 btn btn-danger'>Tolak</div>"; // Tolak button (Red)
                          echo "            </a>";
                          // Setuju button
                          echo "            <button type='button' class='rectangle-212 btn btn-primary' onclick=\"submitApprovalForm('setuju')\">Setuju</button>";
                          echo "          </div>";

                          echo "        </form>"; // End form
                          echo "      </div>"; // end card-body
                          echo "    </div>"; // end card
                          echo "  </div>"; // end col
                          echo "</div>"; // end row

                          // JavaScript for form submission (currently not used by buttons in this section)
                          echo "        <script>";
                          echo "          function submitApprovalForm(actionType) {";
                          echo "            document.getElementById('approvalAction').value = actionType;";
                          echo "            document.getElementById('approvalForm').submit();";
                          echo "          }";
                          echo "        </script>";
                          // Kembali button removed from this section as per user request.

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
                          echo "            <label for='idPeminjamanDetail_ditolak' class='form-label'>ID Peminjaman Ruangan</label>";
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

                          // Row 4: Alasan Peminjaman (Left) & Alasan Penolakan (Right)
                          echo "        <div class='mb-3 row'>";
                          echo "          <div class='col-md-6'>"; // Alasan Peminjaman
                          echo "            <label for='alasanPeminjamanDetail_ditolak' class='form-label'>Alasan Peminjaman</label>";
                          echo "            <textarea readonly class='form-control' id='alasanPeminjamanDetail_ditolak' rows='3'>" . htmlspecialchars($loanDetails['alasanPeminjamanRuangan'] ?? 'N/A') . "</textarea>";
                          echo "          </div>";
                          echo "          <div class='col-md-6'>"; // Alasan Penolakan
                          echo "            <label for='alasanPenolakanDetail' class='form-label fw-bold text-danger'>Alasan Penolakan</label>";
                          echo "            <textarea readonly class='form-control' id='alasanPenolakanDetail' rows='3'>" . htmlspecialchars($loanDetails['alasanPenolakan_explicit'] ?? 'Tidak ada catatan penolakan (debug key: alasanPenolakan_explicit).') . "</textarea>";
                          echo "          </div>";
                          echo "        </div>";

                          // Single 'Kembali' button for Ditolak status, positioned to the left
                          echo "        <div class='mt-4 d-flex justify-content-start'>"; // Changed to justify-content-start
                          echo "          <a href='riwayatPeminjamanAdmin.php' class='btn btn-secondary'>Kembali</a>";
                          echo "        </div>"; 

                          echo "      </div>"; // end card-body
                          echo "    </div>"; // end card
                          echo "  </div>"; // end col
                          echo "</div>"; // end row

                          // Hide the generic details display script
                          echo "<script>var genericCard = document.querySelector('.card-title'); if(genericCard) { genericCard.closest('.card').style.display = 'none'; }</script>";
                      } elseif (stripos($loanDetails['statusPeminjaman'], 'Sedang Dipinjam') !== false) {
                          // UI for Sedang Dipinjam status - Pengembalian Peminjaman Ruangan
                          echo "<form method='POST' action='process_pengembalian.php' enctype='multipart/form-data'>";
                          echo "<input type='hidden' name='idPeminjamanRuangan' value='" . htmlspecialchars($loanDetails['idPeminjamanRuangan']) . "'>";

                          echo "<div class='row mt-4'>";
                          echo "  <div class='col-12'>";
                          echo "    <div class='card'>"; // Default card border
                          echo "      <div class='card-header'>"; // Default card header
                          echo "        <h5 class='card-title mb-0 fw-semibold'>Pengembalian Peminjaman Ruangan</h5>";
                          echo "      </div>";
                          echo "      <div class='card-body'>";

                          // Row 1: ID Peminjaman & Kondisi Ruangan
                          echo "        <div class='mb-3 row'>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label class='form-label fw-semibold'>ID Peminjaman:</label>";
                          echo "            <p class='form-control-plaintext ps-0'>" . htmlspecialchars($loanDetails['idPeminjamanRuangan']) . "</p>";
                          echo "          </div>";
                          echo "          <div class='col-md-6'>";
                          echo "            <label for='kondisiRuangan' class='form-label fw-semibold'>Kondisi Ruangan Saat Dikembalikan: <span class='text-danger'>*</span></label>";
                          echo "            <select class='form-select' id='kondisiRuangan' name='kondisiRuangan' required>";
                          echo "              <option value=''>Pilih Kondisi</option>";
                          echo "              <option value='Baik'>Baik</option>";
                          echo "              <option value='Tidak Baik'>Tidak Baik</option>";
                          echo "            </select>";
                          echo "          </div>";
                          echo "        </div>";

                          // Row 2: Alasan Peminjaman (Full Width)
                          echo "        <div class='mb-3 row'>";
                          echo "          <div class='col-md-12'>";
                          echo "            <label class='form-label fw-semibold'>Alasan Peminjaman:</label>";
                          echo "            <p class='form-control-plaintext ps-0'>" . nl2br(htmlspecialchars($loanDetails['alasanPeminjamanRuangan'] ?? 'N/A')) . "</p>";
                          echo "          </div>";
                          echo "        </div>";

                          // Catatan Pengembalian Ruangan
                          echo "        <div class='mb-3'>";
                          echo "          <label for='catatanPengembalian' class='form-label fw-semibold'>Catatan Pengembalian Ruangan: <span class='text-muted'>(Opsional)</span></label>";
                          echo "          <textarea class='form-control' id='catatanPengembalian' name='catatanPengembalian' rows='3'></textarea>";
                          echo "        </div>";

                          // Dokumentasi Sebelum Pemakaian (Oleh Peminjam)
                          echo "        <div class='mb-3'>";
                          echo "          <label class='form-label fw-semibold'>Dokumentasi Sebelum Pemakaian (Oleh Peminjam):</label>";
                          if (!empty($loanDetails['buktiPeminjamanAwal'])) {
                              $fileNameSebelum = basename($loanDetails['buktiPeminjamanAwal']);
                              // Assumes 'buktiPeminjamanAwal' stores only the filename, and files are in 'uploads/bukti_awal/' relative to project root.
                              $filePathSebelum = 'uploads/bukti_awal/' . $loanDetails['buktiPeminjamanAwal'];
                              echo "          <p class='form-control-plaintext ps-0'>" . htmlspecialchars($fileNameSebelum) . " ";
                              echo "            <a href='" . htmlspecialchars($filePathSebelum) . "' class='btn btn-sm btn-outline-primary ms-2' download='" . htmlspecialchars($fileNameSebelum) . "'>Unduh</a>";
                              echo "          </p>";
                          } else {
                              echo "          <p class='form-control-plaintext ps-0 text-muted'>Tidak ada dokumentasi awal yang diunggah.</p>";
                          }
                          echo "        </div>";

                          // Dokumentasi Sesudah Pemakaian (Diunggah Admin)
                          echo "        <div class='mb-3'>";
                          echo "          <label for='dokumentasiPengembalianFile' class='form-label fw-semibold'>Dokumentasi Sesudah Pemakaian (Diunggah Admin): <span class='text-muted'>(Opsional, Foto/PDF)</span></label>";
                          echo "          <input class='form-control' type='file' id='dokumentasiPengembalianFile' name='dokumentasiPengembalianFile' accept='image/*,.pdf'>";
                          echo "          <small id='fileNameDisplayPengembalian' class='form-text text-muted'></small>";
                          echo "        </div>";

                          // Action Buttons
                          echo "        <div class='mt-4 d-flex justify-content-between'>";
                          echo "          <a href='riwayatPeminjamanAdmin.php' class='btn btn-secondary'>Kembali</a>";
                          echo "          <button type='submit' class='btn btn-primary'>Kirim</button>"; // Changed to Kirim and btn-primary
                          echo "        </div>";

                          echo "      </div>"; // end card-body
                          echo "    </div>"; // end card
                          echo "  </div>"; // end col
                          echo "</div>"; // end row
                          echo "</form>";

                          // JavaScript for file name display
                          echo "<script>
                                  const fileInputPengembalian = document.getElementById('dokumentasiPengembalianFile');
                                  const fileNameDisplayPengembalian = document.getElementById('fileNameDisplayPengembalian');
                                  if (fileInputPengembalian) {
                                      fileInputPengembalian.addEventListener('change', function() {
                                          if (this.files.length > 0) {
                                              fileNameDisplayPengembalian.textContent = 'File dipilih: ' + this.files[0].name;
                                          } else {
                                              fileNameDisplayPengembalian.textContent = '';
                                          }
                                      });
                                  }
                                </script>";
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
