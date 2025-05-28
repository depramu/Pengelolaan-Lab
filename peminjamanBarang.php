<?php
include 'koneksi.php';
$query = "SELECT idPeminjamanBrg, idBarang, jumlahBrg, tglPeminjamanBrg FROM Peminjaman_Barang";
$result = sqlsrv_query($conn, $query);


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

  <style>
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


    /* === Styling for SUBMENU items (e.g., Barang, Ruangan) === */
    .sidebar .collapse .nav-link {
      color: #ffffff !important;
      /* White text for submenu items */
      background-color: transparent !important;

    }

    .sidebar .collapse .nav-link:hover {
      background-color: rgba(255, 255, 255, 0.15) !important;
      /* Subtle hover for submenu items */
      color: #ffffff !important;
    }

    /* Optional: If a submenu item itself can be marked 'active' (e.g. current page is 'Barang') */
    /* You would need to add class="active-submenu" to the link via PHP/JS */
    .sidebar .collapse .nav-link.active-submenu {
      background-color: rgba(255, 255, 255, 0.2) !important;
      /* Slightly more prominent for active submenu */
      font-weight: 500;
      /* Or bold, as you prefer */
      color: #ffffff !important;
    }
  </style>
</head>

<body class="bg-light">
  <div class="container-fluid min-vh-100 d-flex flex-column p-0">
    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center px-5 py-3">
      <img src="icon/logo0.png" class="sidebar-logo" alt="Logo" />
      <div class="d-flex flex-column align-items mt-2" style="margin-left: -55%;">
        <span class="fw-semibold fs-3 font-monospace">Hello,</span>
        <span class="fw-normal fs-6 font-monospace">Nadira Anindita (PIC)</span>
      </div>
      <div>
        <a href="notif.php" class="me-3"><img src="icon/bell.png" class="profile-img" alt="Notif"></a>
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
              <a href="peminjamanBarang.php" class="nav-link active">Barang</a>
              <a href="#" class="nav-link">Ruangan</a>
            </div>
          </li>
          <li class="nav-item mb-2">
            <a href="#" class="nav-link"><img src="icon/graph-report0.png" class="sidebar-icon-report">Laporan</a>
          </li>
          <li class="nav-item mt-0">
            <a href="#" class="nav-link logout"><img src="icon/exit.png">Log Out</a>
          </li>
        </ul>
      </nav>

      <!-- Content Area -->
      <main class="col bg-white px-4 py-3 position-relative">
        <div class="mb-5">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="template.php">Sistem Pengelolaan Lab</a></li>
              <li class="breadcrumb-item active" aria-current="page">Peminjaman Barang</li>
            </ol>
          </nav>
        </div>
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
                    // IMPORTANT: This section uses placeholder logic to mimic the icons in your image.
                    // Your SQL query: SELECT idPeminjamanBrg, idBarang, jumlahBrg, tglPeminjamanBrg FROM Peminjaman_Barang
                    // This query does NOT include a status field. You must:
                    // 1. Add a status field to your query (e.g., 'statusPeminjaman').
                    // 2. Replace the placeholder logic below with your actual status determination logic.

                    $peminjamanId = $row['idPeminjamanBrg'] ?? ''; // Get ID for demo
                    $iconClass = 'bi-hourglass-split text-info'; // Default icon for unknown status
                    $iconTitle = 'Status Tidak Diketahui';

                    // Placeholder logic to match icons from the image based on idPeminjamanBrg ending.
                    // REPLACE THIS with your actual status logic based on $row['your_status_field'].
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
                    <a href="detail_peminjaman.php?id=<?= htmlspecialchars($row['idPeminjamanBrg']); ?>" class="text-secondary" title="Lihat Detail" style="vertical-align: middle;">
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
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



</body>

</html>