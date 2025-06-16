<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan - Sistem Pengelolaan Laboratorium</title>

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
      /* Responsive width: min 100px, preferred 15% of viewport width, max 160px */
      height: auto;
      /* Maintain aspect ratio */
      position: absolute;
      right: clamp(30px, 5vw, 60px);
      /* Responsive right offset */
      bottom: clamp(15px, 3vh, 30px);
      /* Responsive bottom offset */
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

    /* Header kecil di layar kecil */
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
        <img src="../icon/logo0.png" class="sidebar-logo img-fluid" alt="Logo" />
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
            (PIC)
          </span>
        </div>
      </div>
      <div class="d-flex align-items-center">
        <a href="notifPIC.php" class="me-0"><img src="../icon/bell.png" class="profile-img img-fluid" alt="Notif"></a>
        <a href="profilPIC.php"><img src="../icon/vector0.svg" class="profile-img img-fluid" alt="Profil"></a>
        <!-- Sidebar toggle button for mobile -->
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
            <a href="dashboardPIC.php" class="nav-link"><img src="../icon/dashboard0.svg">Dashboard</a>
          </li>
          <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#asetSubmenu" role="button" aria-expanded="false" aria-controls="asetSubmenu">
              <span><img src="../icon/layers0.png">Manajemen Aset</span>
              <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4" id="asetSubmenu">
              <a href="manajemenBarang.php" class="nav-link">Barang</a>
              <a href="manajemenRuangan.php" class="nav-link">Ruangan</a>
            </div>
          </li>
          <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#akunSubmenu" role="button" aria-expanded="false" aria-controls="akunSubmenu">
              <span><img src="../icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
              <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4" id="akunSubmenu">
              <a href="manajemenAkunMhs.php" class="nav-link">Mahasiswa</a>
              <a href="manajemenAkunKry.php" class="nav-link">Karyawan</a>
            </div>
          </li>
          <li class="nav-item mb-2">
            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#pinjamSubmenu" role="button" aria-expanded="false" aria-controls="pinjamSubmenu">
              <span><img src="../icon/ic-twotone-sync-alt0.svg">Peminjaman</span>
              <i class="bi bi-chevron-down transition-chevron ps-3"></i>
            </a>
            <div class="collapse ps-4" id="pinjamSubmenu">
              <a href="peminjamanBarang.php" class="nav-link">Barang</a>
              <a href="peminjamanRuangan.php" class="nav-link">Ruangan</a>
            </div>
          </li>
          <li class="nav-item mb-2">
            <a href="laporan.php" class="nav-link active"><img src="../icon/graph-report0.png" class="sidebar-icon-report">Laporan</a>
          </li>
          <li class="nav-item mt-0">
            <a href="../index.php" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="../icon/exit.png">Log Out</a>
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
                <a href="dashboardPIC.php" class="nav-link active"><img src="../icon/dashboard0.svg">Dashboard</a>
              </li>
              <li class="nav-item mb-2">
                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#asetSubmenuMobile" role="button" aria-expanded="false" aria-controls="asetSubmenuMobile">
                  <span><img src="../icon/layers0.png">Manajemen Aset</span>
                  <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                </a>
                <div class="collapse ps-4" id="asetSubmenuMobile">
                  <a href="manajemenBarang.php" class="nav-link">Barang</a>
                  <a href="manajemenRuangan.php" class="nav-link">Ruangan</a>
                </div>
              </li>
              <li class="nav-item mb-2">
                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#akunSubmenuMobile" role="button" aria-expanded="false" aria-controls="akunSubmenuMobile">
                  <span><img src="../icon/iconamoon-profile-fill0.svg">Manajemen Akun</span>
                  <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                </a>
                <div class="collapse ps-4" id="akunSubmenuMobile">
                  <a href="manajemenAkunMhs.php" class="nav-link">Mahasiswa</a>
                  <a href="manajemenAkunKry.php" class="nav-link">Karyawan</a>
                </div>
              </li>
              <li class="nav-item mb-2">
                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#pinjamSubmenuMobile" role="button" aria-expanded="false" aria-controls="pinjamSubmenuMobile">
                  <span><img src="../icon/ic-twotone-sync-alt0.svg">Peminjaman</span>
                  <i class="bi bi-chevron-down transition-chevron ps-3"></i>
                </a>
                <div class="collapse ps-4" id="pinjamSubmenuMobile">
                  <a href="peminjamanBarang.php" class="nav-link">Barang</a>
                  <a href="peminjamanRuangan.php" class="nav-link">Ruangan</a>
                </div>
              </li>
              <li class="nav-item mb-2">
                <a href="laporan.php" class="nav-link active"><img src="../icon/graph-report0.png" class="sidebar-icon-report">Laporan</a>
              </li>
              <li class="nav-item mt-0">
                <a href="pilihRole.php" class="nav-link logout" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="../icon/exit.png">Log Out</a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
      <!-- End Offcanvas Sidebar for small screens -->


      <!-- Content Area -->
      <main class="col bg-white px-3 px-md-4 py-3 position-relative">
        <div class="mb-3"> <!-- Mengurangi margin-bottom sedikit dari mb-5 agar ada ruang -->
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li> <!-- Sesuaikan href jika perlu -->
              <li class="breadcrumb-item active" aria-current="page">Laporan</li>
            </ol>
          </nav>
        </div>

        <!-- Filter Laporan -->
        <div class="card shadow-sm mb-4">
          <div class="card-body">
            <h5 class="card-title mb-3">Filter Laporan</h5>
            <div class="row g-3 align-items-end">
              <div class="col-md-4">
                <label for="jenisLaporan" class="form-label">Jenis Laporan</label>
                <select class="form-select" id="jenisLaporan">
                  <option selected disabled value="">Pilih Jenis Laporan...</option>
                  <option value="dataBarang">Data Barang</option>
                  <option value="dataRuangan">Data Ruangan</option>
                  <option value="peminjamSeringMeminjam">Peminjam yang Sering Meminjam</option>
                  <option value="barangSeringDipinjam">Barang yang Sering Dipinjam</option>
                  <option value="ruanganSeringDipinjam">Ruangan yang Sering Dipinjam</option>
                </select>
              </div>
              <div class="col-md-3">
                <label for="bulanLaporan" class="form-label">Bulan</label>
                <select class="form-select" id="bulanLaporan">
                  <option selected disabled value="">Pilih Bulan...</option>
                  <option value="01">Januari</option>
                  <option value="02">Februari</option>
                  <option value="03">Maret</option>
                  <option value="04">April</option>
                  <option value="05">Mei</option>
                  <option value="06">Juni</option>
                  <option value="07">Juli</option>
                  <option value="08">Agustus</option>
                  <option value="09">September</option>
                  <option value="10">Oktober</option>
                  <option value="11">November</option>
                  <option value="12">Desember</option>
                </select>
              </div>
              <div class="col-md-3">
                <label for="tahunLaporan" class="form-label">Tahun</label>
                <select class="form-select" id="tahunLaporan">
                  <option selected disabled value="">Pilih Tahun...</option>
                  <!-- Tahun akan diisi oleh JavaScript -->
                </select>
              </div>
              <div class="col-md-2 d-flex">
                <button class="btn btn-primary w-100" id="tampilkanLaporanBtn"><i class="bi bi-search me-1"></i> Tampilkan</button>
              </div>
            </div>
          </div>
        </div>
        <!-- End Filter Laporan -->

        <!-- Area Konten Laporan -->
        <div id="areaKontenLaporan" style="display: none;"> <!-- Awalnya disembunyikan -->
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 id="judulKontenLaporan" class="mb-0"></h4>
            <button class="btn btn-success" id="exportExcelBtn">
              <i class="bi bi-file-earmark-excel me-1"></i> Export ke Excel
            </button>
          </div>
          <div id="wadahLaporan" class="table-responsive">
            <!-- Tabel atau konten laporan lainnya akan dimuat di sini -->
          </div>
        </div>
        <div id="wadahLaporan" class="table-responsive">
          <!-- Tabel atau konten laporan lainnya akan dimuat di sini -->
        </div>

        <!-- AWAL KONTROL PAGINASI -->
        <div id="paginationControlsContainer" class="mt-3" style="display: none;"> <!-- Awalnya disembunyikan -->
          <nav aria-label="Page navigation">
            <ul class="pagination" id="paginationUl">
              <!-- Item paginasi akan digenerate oleh JavaScript -->
            </ul>
          </nav>
        </div>
        <!-- AKHIR KONTROL PAGINASI -->

    </div>
    <!-- End Area Konten Laporan -->

    </main>
    <!-- End Content Area -->
  </div>
  </div>
  <!-- End Container -->

  <!-- Modal untuk Validasi -->
  <!-- Modal Validasi -->
  <!-- Modal untuk Validasi (PASTIKAN HTML INI ADA) -->
 <div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="validationModalLabel">
            <i><img src="../icon/info.svg" alt="" style="width: 25px; height: 25px; margin-bottom: 5px; margin-right: 10px;"></i>
            PERINGATAN
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="validationMessage">
          <!-- Pesan error akan muncul di sini -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
        </div>
      </div>
    </div>
  </div>


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
          <button type="button" class="btn btn-primary ps-4 pe-4" onclick="window.location.href='../index.php'">Ya</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Logout Modal -->

  <!-- (Kode JavaScript yang sudah ada sebelumnya) -->
  <!-- ... (bagian HTML di atas, sudah tidak ada perubahan di sini) ... -->

  <!-- ... (bagian HTML di atas) ... -->

 <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- SheetJS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const jenisLaporanSelect = document.getElementById('jenisLaporan');
      const bulanLaporanSelect = document.getElementById('bulanLaporan');
      const tahunLaporanSelect = document.getElementById('tahunLaporan');
      const tampilkanLaporanBtn = document.getElementById('tampilkanLaporanBtn');

      const areaKontenLaporanDiv = document.getElementById('areaKontenLaporan');
      const judulKontenLaporanSpan = document.getElementById('judulKontenLaporan');
      const wadahLaporanDiv = document.getElementById('wadahLaporan');
      const exportExcelBtn = document.getElementById('exportExcelBtn');
      
      const validationModalElement = document.getElementById('validationModal');
      const validationModal = validationModalElement ? new bootstrap.Modal(validationModalElement) : null;
      const validationMessageEl = document.getElementById('validationMessage');

      let currentPage = 1;
      const rowsPerPage = 5;
      let currentTableFullData = [];
      let currentReportTypeForPaging = '';
      const paginationControlsContainer = document.getElementById('paginationControlsContainer');
      const paginationUl = document.getElementById('paginationUl');

      const currentYear = new Date().getFullYear();
      for (let i = 0; i < 5; i++) {
        const year = currentYear - i;
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        tahunLaporanSelect.appendChild(option);
      }

      tampilkanLaporanBtn.addEventListener('click', function() {
        const jenisLaporan = jenisLaporanSelect.value;
        const bulan = bulanLaporanSelect.value;
        const tahun = tahunLaporanSelect.value;

        // 1. Validasi input dropdown jenis, bulan, dan tahun (popup terpisah)
        if (!jenisLaporan || !bulan || !tahun) {
          if (validationModal && validationMessageEl) {
            validationMessageEl.textContent = 'Silakan lengkapi semua filter (Jenis Laporan, Bulan, dan Tahun).';
            validationModal.show();
          } else {
            alert('Silakan lengkapi semua filter (Jenis Laporan, Bulan, dan Tahun).');
          }
          return;          
        }

        // Tampilkan loader & sembunyikan konten sebelumnya saat proses fetch
        wadahLaporanDiv.innerHTML = '<p class="text-center py-5">... Memuat data...</p>';
        paginationControlsContainer.style.display = 'none';
        areaKontenLaporanDiv.style.display = 'none'; // PENTING: Sembunyikan dulu area utama
        currentPage = 1;

        const fetchUrl = `../CRUD/Laporan/get_laporan_data.php?jenisLaporan=${jenisLaporan}&bulan=${bulan}&tahun=${tahun}`;

        fetch(fetchUrl)
          .then(response => { /* ... (penanganan error HTTP) ... */ return response.json(); })
          .then(result => {
            wadahLaporanDiv.innerHTML = ''; // Bersihkan loader

            if (result.status === 'success') {
              currentTableFullData = result.data || []; // Ambil data, default array kosong jika null/undefined
              currentReportTypeForPaging = jenisLaporan;
              
              // **INI BAGIAN VALIDASI KUNCI**
              if (currentTableFullData.length > 0) { 
                // HANYA JIKA ADA DATA (array tidak kosong)
                areaKontenLaporanDiv.style.display = 'block'; // Baru tampilkan area judul dan tombol export
                
                const namaBulanDipilih = bulanLaporanSelect.options[bulanLaporanSelect.selectedIndex].text;
                const tahunDipilih = tahunLaporanSelect.value;
                
                // Set judul sesuai jenis laporan dan periode yang dipilih
                let judulText = `Laporan (${jenisLaporanSelect.options[jenisLaporanSelect.selectedIndex].text}) - ${namaBulanDipilih} ${tahunDipilih}`;
                if (jenisLaporan === 'dataBarang') judulText = `Laporan Data Barang - ${namaBulanDipilih} ${tahunDipilih}`;
                else if (jenisLaporan === 'dataRuangan') judulText = `Laporan Data Ruangan - ${namaBulanDipilih} ${tahunDipilih}`;
                else if (jenisLaporan === 'peminjamSeringMeminjam') judulText = `Laporan Peminjam yang Sering Meminjam - ${namaBulanDipilih} ${tahunDipilih}`;
                else if (jenisLaporan === 'barangSeringDipinjam') judulText = `Laporan Barang yang Sering Dipinjam - ${namaBulanDipilih} ${tahunDipilih}`;
                else if (jenisLaporan === 'ruanganSeringDipinjam') judulText = `Laporan Ruangan yang Sering Dipinjam - ${namaBulanDipilih} ${tahunDipilih}`;
                judulKontenLaporanSpan.textContent = judulText;
                
                displayPage(currentPage); // Render tabel
                setupPagination();      // Siapkan paginasi
              } else {
                // JIKA TIDAK ADA DATA (array kosong) DARI BACKEND untuk periode ini
                areaKontenLaporanDiv.style.display = 'none'; // Pastikan area judul dan export tetap tersembunyi
                paginationControlsContainer.style.display = 'none'; 
                wadahLaporanDiv.innerHTML = ''; // Area tabel juga kosong

                // Tampilkan modal validasi "Tidak ada data laporan"
                if (validationModal && validationMessageEl) {
                    validationMessageEl.textContent = 'Tidak Ada Data Laporan untuk periode yang dipilih.';
                    validationModal.show();
                } else {
                    alert('Tidak Ada Data Laporan untuk periode yang dipilih.'); // Fallback jika modal tidak terinisialisasi
                }
              }
            } else { 
              // Error dari server (result.status bukan 'success')
              areaKontenLaporanDiv.style.display = 'block'; // Tampilkan area untuk pesan error
              judulKontenLaporanSpan.textContent = 'Kesalahan Sistem';
              wadahLaporanDiv.innerHTML = `<p class="text-danger text-center">Gagal memuat data: ${result.message}</p>`;
              console.error('Server Error:', result.message);
              paginationControlsContainer.style.display = 'none';
            }
          })
          .catch(error => {
            // Error jaringan/fetch
            areaKontenLaporanDiv.style.display = 'block'; // Tampilkan area untuk pesan error
            console.error('Fetch Error:', error);
            judulKontenLaporanSpan.textContent = 'Kesalahan Jaringan';
            wadahLaporanDiv.innerHTML = `<p class="text-danger text-center">Terjadi kesalahan saat mengambil data. Detail: ${error.message}</p>`;
            paginationControlsContainer.style.display = 'none';
          });
      });

      // Fungsi displayPage(page) (TETAP SAMA, tidak perlu diubah dari versi paginasi sebelumnya)
      function displayPage(page) {
        currentPage = page;
        wadahLaporanDiv.innerHTML = '';

        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;
        const paginatedItems = currentTableFullData.slice(startIndex, endIndex);

        if (paginatedItems.length === 0 && currentTableFullData.length > 0 && currentPage > 1) {
          displayPage(currentPage - 1);
          return;
        }
        // Tidak perlu handle (paginatedItems.length === 0 && currentTableFullData.length === 0) di sini,
        // karena itu sudah ditangani sebelum pemanggilan displayPage.

        const table = document.createElement('table');
        table.className = 'table table-striped table-bordered table-hover';

        let headers = [];
        let dataKeys = [];

        if (currentReportTypeForPaging === 'dataBarang') {
          table.id = 'tabelLaporanDataBarang';
          headers = ['ID Barang', 'Nama Barang', 'Stok Barang', 'Lokasi Barang'];
          dataKeys = ['idBarang', 'namaBarang', 'stokBarang', 'lokasiBarang'];
        } else if (currentReportTypeForPaging === 'dataRuangan') {
          table.id = 'tabelLaporanDataRuangan';
          headers = ['ID Ruangan', 'Nama Ruangan', 'Kondisi Ruangan', 'Ketersediaan'];
          dataKeys = ['idRuangan', 'namaRuangan', 'kondisiRuangan', 'ketersediaan'];
        } else if (currentReportTypeForPaging === 'peminjamSeringMeminjam') {
          table.id = 'tabelLaporanPeminjamSeringMeminjam';
          headers = ['ID Peminjam', 'Nama Peminjam', 'Jenis Peminjam', 'Jumlah Peminjaman'];
          dataKeys = ['IDPeminjam', 'NamaPeminjam', 'JenisPeminjam', 'JumlahPeminjaman'];
        } else if (currentReportTypeForPaging === 'barangSeringDipinjam') {
          table.id = 'tabelLaporanBarangSeringDipinjam';
          headers = ['ID Barang', 'Nama Barang', 'Total Kuantitas Dipinjam'];
          dataKeys = ['idBarang', 'namaBarang', 'TotalKuantitasDipinjam'];
        }
        else if (currentReportTypeForPaging === 'ruanganSeringDipinjam') {
          table.id = 'tabelLaporanRuanganSeringDipinjam';
          headers = ['ID Ruangan', 'Nama Ruangan', 'Jumlah Dipinjam'];
          dataKeys = ['idRuangan', 'namaRuangan', 'JumlahDipinjam'];
        }
        else {
          // Jika sampai sini, seharusnya currentReportTypeForPaging valid dan ada data,
          // karena sudah dicek di blok `fetch().then()`
          return;
        }

        const thead = table.createTHead();
        const headerRow = thead.insertRow();
        headers.forEach(text => {
          let th = document.createElement('th');
          th.textContent = text;
          headerRow.appendChild(th);
        });

        const tbody = table.createTBody();
        paginatedItems.forEach(item => {
          const row = tbody.insertRow();
          dataKeys.forEach(key => {
            row.insertCell().textContent = item[key] !== null && item[key] !== undefined ? item[key] : '';
          });
        });
        wadahLaporanDiv.appendChild(table);
        updatePaginationButtonsActiveState();
      }

      // Fungsi setupPagination(), updatePaginationButtonsActiveState(), exportExcelBtn()
      // (TETAP SAMA seperti versi paginasi sebelumnya)
       function setupPagination() {
        paginationUl.innerHTML = '';
        const pageCount = Math.ceil(currentTableFullData.length / rowsPerPage);

        if (pageCount <= 1 && currentTableFullData.length <= rowsPerPage) {
          paginationControlsContainer.style.display = 'none';
          return;
        }
        paginationControlsContainer.style.display = 'block';

        let prevLi = document.createElement('li');
        prevLi.className = 'page-item';
        let prevLink = document.createElement('a');
        prevLink.className = 'page-link';
        prevLink.href = '#';
        prevLink.innerHTML = '«';
        prevLink.addEventListener('click', (e) => { e.preventDefault(); if (currentPage > 1) displayPage(currentPage - 1); });
        prevLi.appendChild(prevLink);
        paginationUl.appendChild(prevLi);

        for (let i = 1; i <= pageCount; i++) {
          let pageLi = document.createElement('li');
          pageLi.className = 'page-item';
          pageLi.dataset.page = i;
          let pageLink = document.createElement('a');
          pageLink.className = 'page-link';
          pageLink.href = '#';
          pageLink.textContent = i;
          pageLink.addEventListener('click', (e) => { e.preventDefault(); displayPage(parseInt(e.target.closest('li').dataset.page)); });
          pageLi.appendChild(pageLink);
          paginationUl.appendChild(pageLi);
        }

        let nextLi = document.createElement('li');
        nextLi.className = 'page-item';
        let nextLink = document.createElement('a');
        nextLink.className = 'page-link';
        nextLink.href = '#';
        nextLink.innerHTML = '»';
        nextLink.addEventListener('click', (e) => { e.preventDefault(); if (currentPage < pageCount) displayPage(currentPage + 1); });
        nextLi.appendChild(nextLink);
        paginationUl.appendChild(nextLi);

        updatePaginationButtonsActiveState();
      }

      function updatePaginationButtonsActiveState() {
        const pageCount = Math.ceil(currentTableFullData.length / rowsPerPage);
        const pageItems = paginationUl.querySelectorAll('.page-item');
        
        pageItems.forEach(item => {
          item.classList.remove('active', 'disabled');
          const link = item.querySelector('.page-link');
          const pageNumData = item.dataset.page;

          if (link) {
            if (link.innerHTML.includes('«')) { 
              if (currentPage === 1) item.classList.add('disabled');
            } else if (link.innerHTML.includes('»')) { 
              if (currentPage === pageCount || pageCount === 0) item.classList.add('disabled');
            } else if (pageNumData && parseInt(pageNumData) === currentPage) {
              item.classList.add('active');
            }
          }
        });
        if (pageCount <= 1 && currentTableFullData.length > 0) {
            paginationControlsContainer.style.display = 'block'; 
            pageItems.forEach(item => item.classList.add('disabled'));
        } else if (pageCount <= 1) { 
           paginationControlsContainer.style.display = 'none';
        } else {
            paginationControlsContainer.style.display = 'block';
        }
      }
      
      exportExcelBtn.addEventListener('click', function() {
        const jenisLaporan = jenisLaporanSelect.value;
        const bulanText = bulanLaporanSelect.options[bulanLaporanSelect.selectedIndex].text;
        const tahunText = tahunLaporanSelect.value;
        
        const dataToExport = currentTableFullData; 
        let headersDisplay = [];
        let dataKeysForExport = [];
        let fileName = "Laporan.xlsx";
        let sheetName = "Laporan";

        if (jenisLaporan === 'dataBarang') {
          headersDisplay = ['ID Barang', 'Nama Barang', 'Stok Barang', 'Lokasi Barang'];
          dataKeysForExport = ['idBarang', 'namaBarang', 'stokBarang', 'lokasiBarang'];
          fileName = `Laporan_Data_Barang_${bulanText}_${tahunText}.xlsx`;
          sheetName = "Data Barang";
        } else if (jenisLaporan === 'dataRuangan') {
          headersDisplay = ['ID Ruangan', 'Nama Ruangan', 'Kondisi Ruangan', 'Ketersediaan'];
          dataKeysForExport = ['idRuangan', 'namaRuangan', 'kondisiRuangan', 'ketersediaan'];
          fileName = `Laporan_Data_Ruangan_${bulanText}_${tahunText}.xlsx`;
          sheetName = "Data Ruangan";
        }
        else if (jenisLaporan === 'peminjamSeringMeminjam') {
          headersDisplay = ['ID Peminjam', 'Nama Peminjam', 'Jenis Peminjam', 'Jumlah Peminjaman'];
          dataKeysForExport = ['IDPeminjam', 'NamaPeminjam', 'JenisPeminjam', 'JumlahPeminjaman']; 
          fileName = `Laporan_Peminjam_Sering_Meminjam_${bulanText}_${tahunText}.xlsx`;
          sheetName = "Peminjam Sering Meminjam";
        }
        else if (jenisLaporan === 'barangSeringDipinjam') {
          headersDisplay = ['ID Barang', 'Nama Barang', 'Total Kuantitas Dipinjam'];
          dataKeysForExport = ['idBarang', 'namaBarang', 'TotalKuantitasDipinjam']; 
          fileName = `Laporan_Barang_Sering_Dipinjam_${bulanText}_${tahunText}.xlsx`;
          sheetName = "Barang Sering Dipinjam";
        }
        else if (jenisLaporan === 'ruanganSeringDipinjam') {
          headersDisplay = ['ID Ruangan', 'Nama Ruangan', 'Jumlah Dipinjam'];
          dataKeysForExport = ['idRuangan', 'namaRuangan', 'JumlahDipinjam']; 
          fileName = `Laporan_Ruangan_Sering_Dipinjam_${bulanText}_${tahunText}.xlsx`;
          sheetName = "Ruangan Sering Dipinjam";
        }

        if (dataToExport && dataToExport.length > 0 && headersDisplay.length > 0) {
            const ws_data = [ headersDisplay ]; 
            dataToExport.forEach(item => {
                const rowData = dataKeysForExport.map(key => item[key] !== null && item[key] !== undefined ? item[key] : '');
                ws_data.push(rowData);
            });
            
            const ws = XLSX.utils.aoa_to_sheet(ws_data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, sheetName);
            XLSX.writeFile(wb, fileName);
        } else {
          // Pesan jika tidak ada data yang bisa diekspor (karena sudah ditangani modal validasi "tidak ada data")
          // Ini bisa diganti dengan pesan yang lebih halus atau tidak perlu alert lagi jika modal sudah cukup.
          // alert('Tidak ada data yang tersedia untuk diexport pada periode ini.');
          // Untuk sekarang, kita biarkan alert yang sudah ada di kondisi `fetch()` di atas menangani notifikasi "tidak ada data".
        }
      });

    });
  </script>
</body>
</html>