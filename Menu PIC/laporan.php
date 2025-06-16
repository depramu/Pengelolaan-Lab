<?php
include '../templates/header.php';
include '../templates/sidebar.php';
?>
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

    let currentPage = 1;
    const rowsPerPage = 5;
    let currentTableFullData = [];
    let currentReportTypeForPaging = '';
    const paginationControlsContainer = document.getElementById('paginationControlsContainer');
    const paginationUl = document.getElementById('paginationUl');

    // (Kode pengisian dropdown tahun sudah ada)
    const currentYear = new Date().getFullYear();
    for (let i = 0; i < 5; i++) {
      const year = currentYear - i;
      const option = document.createElement('option');
      option.value = year;
      option.textContent = year;
      tahunLaporanSelect.appendChild(option);
    }

    // Pastikan opsi "Ruangan yang Sering Dipinjam" ada di HTML select id="jenisLaporan"
    /* Ini seharusnya sudah ada di HTML Anda sebelumnya:
    <option value="ruanganSeringDipinjam">Ruangan yang Sering Dipinjam</option>
    */

    tampilkanLaporanBtn.addEventListener('click', function() {
      const jenisLaporan = jenisLaporanSelect.value;
      const bulan = bulanLaporanSelect.value;
      const tahun = tahunLaporanSelect.value;

      if (!jenisLaporan) {
        alert('Silakan pilih Jenis Laporan.');
        return;
      }
      // Pastikan bulan dan tahun dipilih untuk semua laporan berbasis waktu
      if ((jenisLaporan === 'peminjamSeringMeminjam' || jenisLaporan === 'barangSeringDipinjam' || jenisLaporan === 'ruanganSeringDipinjam') && (!bulan || !tahun)) {
        alert('Silakan pilih Bulan dan Tahun laporan.');
        return;
      }

      wadahLaporanDiv.innerHTML = '<p class="text-center py-5"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memuat data...</p>';
      paginationControlsContainer.style.display = 'none';
      areaKontenLaporanDiv.style.display = 'block';
      currentPage = 1;

      fetch(`../CRUD/Laporan/get_laporan_data.php?jenisLaporan=${jenisLaporan}&bulan=${bulan}&tahun=${tahun}`)
        .then(response => {
          if (!response.ok) {
            return response.text().then(text => {
              throw new Error(`HTTP error! status: ${response.status}, message: ${text}`);
            });
          }
          return response.json();
        })
        .then(result => {
          wadahLaporanDiv.innerHTML = '';
          if (result.status === 'success') {
            const namaBulan = bulanLaporanSelect.options[bulanLaporanSelect.selectedIndex].text;
            currentTableFullData = result.data || [];
            currentReportTypeForPaging = jenisLaporan;

            if (currentTableFullData.length > 0) {
              if (jenisLaporan === 'dataBarang') {
                judulKontenLaporanSpan.textContent = `Laporan Data Barang - ${namaBulan} ${tahun}`;
              } else if (jenisLaporan === 'dataRuangan') {
                judulKontenLaporanSpan.textContent = `Laporan Data Ruangan - ${namaBulan} ${tahun}`;
              } else if (jenisLaporan === 'peminjamSeringMeminjam') {
                judulKontenLaporanSpan.textContent = `Laporan Peminjam yang Sering Meminjam - ${namaBulan} ${tahun}`;
              } else if (jenisLaporan === 'barangSeringDipinjam') {
                judulKontenLaporanSpan.textContent = `Laporan Barang yang Sering Dipinjam - ${namaBulan} ${tahun}`;
              }
              // --- AWAL BLOK UNTUK JUDUL "Ruangan yang Sering Dipinjam" ---
              else if (jenisLaporan === 'ruanganSeringDipinjam') {
                judulKontenLaporanSpan.textContent = `Laporan Ruangan yang Sering Dipinjam - ${namaBulan} ${tahun}`;
              }
              // --- AKHIR BLOK ---
              else {
                judulKontenLaporanSpan.textContent = `Laporan (${jenisLaporanSelect.options[jenisLaporanSelect.selectedIndex].text})`;
              }
              displayPage(currentPage);
              setupPagination();
            } else {
              // Tetap set judul walaupun tidak ada data
              if (jenisLaporan === 'dataBarang') judulKontenLaporanSpan.textContent = `Laporan Data Barang - ${namaBulan} ${tahun}`;
              else if (jenisLaporan === 'dataRuangan') judulKontenLaporanSpan.textContent = `Laporan Data Ruangan - ${namaBulan} ${tahun}`;
              else if (jenisLaporan === 'peminjamSeringMeminjam') judulKontenLaporanSpan.textContent = `Laporan Peminjam yang Sering Meminjam - ${namaBulan} ${tahun}`;
              else if (jenisLaporan === 'barangSeringDipinjam') judulKontenLaporanSpan.textContent = `Laporan Barang yang Sering Dipinjam - ${namaBulan} ${tahun}`;
              else if (jenisLaporan === 'ruanganSeringDipinjam') judulKontenLaporanSpan.textContent = `Laporan Ruangan yang Sering Dipinjam - ${namaBulan} ${tahun}`; // Tambah ini
              else judulKontenLaporanSpan.textContent = `Laporan (${jenisLaporanSelect.options[jenisLaporanSelect.selectedIndex].text})`;

              wadahLaporanDiv.innerHTML = `<p class="text-center">Tidak ada data untuk laporan ini pada periode yang dipilih.</p>`;
              paginationControlsContainer.style.display = 'none';
            }
          } else {
            judulKontenLaporanSpan.textContent = 'Kesalahan';
            wadahLaporanDiv.innerHTML = `<p class="text-danger text-center">Gagal memuat data: ${result.message}</p>`;
            console.error('Server Error:', result);
            paginationControlsContainer.style.display = 'none';
          }
        })
        .catch(error => {
          console.error('Fetch Error:', error);
          judulKontenLaporanSpan.textContent = 'Kesalahan';
          wadahLaporanDiv.innerHTML = `<p class="text-danger text-center">Terjadi kesalahan saat mengambil data. Periksa konsol browser. Error: ${error.message}</p>`;
          paginationControlsContainer.style.display = 'none';
        });
    });

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
      if (paginatedItems.length === 0 && currentTableFullData.length === 0) {
        return;
      }

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
      // --- AWAL BLOK UNTUK "Ruangan yang Sering Dipinjam" ---
      else if (currentReportTypeForPaging === 'ruanganSeringDipinjam') {
        table.id = 'tabelLaporanRuanganSeringDipinjam';
        headers = ['ID Ruangan', 'Nama Ruangan', 'Jumlah Dipinjam'];
        dataKeys = ['idRuangan', 'namaRuangan', 'JumlahDipinjam'];
      }
      // --- AKHIR BLOK ---
      else {
        wadahLaporanDiv.innerHTML = '<p class="text-center">Tampilan tabel untuk jenis laporan ini belum didukung.</p>';
        paginationControlsContainer.style.display = 'none';
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

    // ( ... fungsi setupPagination() dan updatePaginationButtonsActiveState() tetap sama ... )
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
      prevLink.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage > 1) displayPage(currentPage - 1);
      });
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
        pageLink.addEventListener('click', (e) => {
          e.preventDefault();
          displayPage(parseInt(e.target.closest('li').dataset.page));
        });
        pageLi.appendChild(pageLink);
        paginationUl.appendChild(pageLi);
      }

      let nextLi = document.createElement('li');
      nextLi.className = 'page-item';
      let nextLink = document.createElement('a');
      nextLink.className = 'page-link';
      nextLink.href = '#';
      nextLink.innerHTML = '»';
      nextLink.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage < pageCount) displayPage(currentPage + 1);
      });
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
      } else if (jenisLaporan === 'peminjamSeringMeminjam') {
        headersDisplay = ['ID Peminjam', 'Nama Peminjam', 'Jenis Peminjam', 'Jumlah Peminjaman'];
        dataKeysForExport = ['IDPeminjam', 'NamaPeminjam', 'JenisPeminjam', 'JumlahPeminjaman'];
        fileName = `Laporan_Peminjam_Sering_Meminjam_${bulanText}_${tahunText}.xlsx`;
        sheetName = "Peminjam Sering Meminjam";
      } else if (jenisLaporan === 'barangSeringDipinjam') {
        headersDisplay = ['ID Barang', 'Nama Barang', 'Total Kuantitas Dipinjam'];
        dataKeysForExport = ['idBarang', 'namaBarang', 'TotalKuantitasDipinjam'];
        fileName = `Laporan_Barang_Sering_Dipinjam_${bulanText}_${tahunText}.xlsx`;
        sheetName = "Barang Sering Dipinjam";
      }
      // --- AWAL BLOK UNTUK EXPORT DATA "Ruangan yang Sering Dipinjam" ---
      else if (jenisLaporan === 'ruanganSeringDipinjam') {
        headersDisplay = ['ID Ruangan', 'Nama Ruangan', 'Jumlah Dipinjam'];
        dataKeysForExport = ['idRuangan', 'namaRuangan', 'JumlahDipinjam'];
        fileName = `Laporan_Ruangan_Sering_Dipinjam_${bulanText}_${tahunText}.xlsx`;
        sheetName = "Ruangan Sering Dipinjam";
      }
      // --- AKHIR BLOK ---

      if (dataToExport && dataToExport.length > 0 && headersDisplay.length > 0) {
        const ws_data = [headersDisplay];
        dataToExport.forEach(item => {
          const rowData = dataKeysForExport.map(key => item[key] !== null && item[key] !== undefined ? item[key] : '');
          ws_data.push(rowData);
        });

        const ws = XLSX.utils.aoa_to_sheet(ws_data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, sheetName);
        XLSX.writeFile(wb, fileName);
      } else {
        alert('Tidak ada data untuk diexport atau jenis laporan tidak valid.');
      }
    });

  });
</script>

<?php
include '../templates/footer.php';
?>