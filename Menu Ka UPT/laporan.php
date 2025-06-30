<?php
// Memanggil header.php. Ini akan menangani session_start(), koneksi ke DB, validasi login,
include '../templates/header.php';
// Memanggil sidebar.php. Ini akan merender menu navigasi samping sesuai peran pengguna (KaUPT).
include '../templates/sidebar.php';
?>

<!-- Area Konten Utama Halaman Laporan -->
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
  <div class="mb-3"> <!-- Container untuk breadcrumb, dengan margin bawah -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <!-- Breadcrumb navigasi, link ke dashboard KaUPT dan halaman saat ini -->
        <li class="breadcrumb-item"><a href="dashboardKAUPT.php">Sistem Pengelolaan Lab</a></li>
        <li class="breadcrumb-item active" aria-current="page">Laporan</li>
      </ol>
    </nav>
  </div>

  <!-- Card untuk Filter Laporan -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="card-title mb-3">Filter Laporan</h5>
      <!-- Baris yang berisi semua elemen filter -->
      <div class="row g-3 align-items-end">
        <!-- Dropdown untuk memilih Jenis Laporan -->
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
        <!-- Dropdown untuk memilih Bulan -->
        <div class="col-md-3">
          <label for="bulanLaporan" class="form-label">Bulan</label>
          <select class="form-select" id="bulanLaporan">
            <option selected disabled value="">Pilih Bulan...</option>
            <option value="01">Januari</option> <option value="02">Februari</option> <option value="03">Maret</option>
            <option value="04">April</option> <option value="05">Mei</option> <option value="06">Juni</option>
            <option value="07">Juli</option> <option value="08">Agustus</option> <option value="09">September</option>
            <option value="10">Oktober</option> <option value="11">November</option> <option value="12">Desember</option>
          </select>
        </div>
        <!-- Dropdown untuk memilih Tahun -->
        <div class="col-md-3">
          <label for="tahunLaporan" class="form-label">Tahun</label>
          <select class="form-select" id="tahunLaporan">
            <option selected disabled value="">Pilih Tahun...</option>
            <!-- Opsi tahun akan diisi secara dinamis oleh JavaScript -->
          </select>
        </div>
        <!-- Tombol untuk memicu pengambilan dan penampilan laporan -->
        <div class="col-md-2 d-flex">
          <button class="btn btn-primary w-100" id="tampilkanLaporanBtn"><i class="bi bi-search me-1"></i> Tampilkan</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Akhir Card Filter Laporan -->

  <!-- Area untuk menampilkan konten laporan (judul, tombol export, dan tabel data) -->
  <!-- Awalnya disembunyikan (display: none;) dan akan ditampilkan oleh JavaScript jika ada data -->
  <div id="areaKontenLaporan" style="display: none;"> 
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 id="judulKontenLaporan" class="mb-0"></h4> <!-- Judul laporan dinamis -->
      <button class="btn btn-success" id="exportExcelBtn"> <!-- Tombol untuk export data ke Excel -->
        <i class="bi bi-file-earmark-excel me-1"></i> Export ke Excel
      </button>
    </div>
    <div id="wadahLaporan" class="table-responsive">
      <!-- Tabel data laporan akan dirender di sini oleh JavaScript -->
    </div>
  </div>
  <!-- Elemen duplikat ini sepertinya tidak perlu dan bisa dihapus jika sudah ada di atas -->

  <!-- Kontrol Paginasi untuk tabel laporan -->
  <!-- Awalnya disembunyikan dan akan ditampilkan oleh JavaScript jika data melebihi satu halaman -->
  <div id="paginationControlsContainer" class="mt-3" style="display: none;"> 
    <nav aria-label="Page navigation">
      <ul class="pagination" id="paginationUl">
        <!-- Tombol-tombol paginasi (Previous, 1, 2, ..., Next) akan digenerate oleh JavaScript -->
      </ul>
    </nav>
  </div>
  <!-- Akhir Kontrol Paginasi -->

</main>
<!-- Akhir Area Konten Utama -->

<!-- Modal HTML untuk Validasi Input Pengguna -->
<!-- Digunakan untuk menampilkan pesan error jika filter belum lengkap atau jika tidak ada data laporan -->
<div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="validationModalLabel">
          <i><img src="<?= BASE_URL ?>/icon/info.svg" alt="" style="width: 25px; height: 25px; margin-bottom: 5px; margin-right: 10px;"></i>
          PERINGATAN
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="validationMessage">
        <!-- Pesan validasi dinamis akan ditampilkan di sini oleh JavaScript -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
      </div>
    </div>
  </div>
</div>

<!-- Menyertakan library JavaScript SheetJS dari CDN untuk fungsionalitas export ke Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
// Semua kode JavaScript dieksekusi setelah seluruh halaman HTML selesai dimuat.
document.addEventListener('DOMContentLoaded', function() {
    // Mendapatkan referensi ke elemen-elemen HTML (filter, tombol, area konten, modal)
    const jenisLaporanSelect = document.getElementById('jenisLaporan');
    const bulanLaporanSelect = document.getElementById('bulanLaporan');
    const tahunLaporanSelect = document.getElementById('tahunLaporan');
    const tampilkanLaporanBtn = document.getElementById('tampilkanLaporanBtn');

    const areaKontenLaporanDiv = document.getElementById('areaKontenLaporan');
    const judulKontenLaporanSpan = document.getElementById('judulKontenLaporan');
    const wadahLaporanDiv = document.getElementById('wadahLaporan'); // Div tempat tabel akan muncul
    const exportExcelBtn = document.getElementById('exportExcelBtn');
    
    const validationModalElement = document.getElementById('validationModal');
    // Inisialisasi instance modal Bootstrap jika elemennya ada
    const validationModal = validationModalElement ? new bootstrap.Modal(validationModalElement) : null;
    const validationMessageEl = document.getElementById('validationMessage'); // Elemen untuk pesan di modal

    // Variabel untuk mengelola state paginasi dan data
    let currentPage = 1;                    // Halaman aktif saat ini
    const rowsPerPage = 5;                  // Jumlah baris data per halaman tabel
    let currentTableFullData = [];          // Menyimpan semua data laporan dari server
    let currentReportTypeForPaging = '';    // Menyimpan jenis laporan yang sedang aktif
    const paginationControlsContainer = document.getElementById('paginationControlsContainer');
    const paginationUl = document.getElementById('paginationUl'); // Elemen <ul> untuk tombol paginasi

    // Mengisi dropdown tahun (misalnya, dari tahun ini hingga 4 tahun ke belakang)
    const currentYear = new Date().getFullYear();
    for (let i = 0; i < 5; i++) {
        const year = currentYear - i;
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        tahunLaporanSelect.appendChild(option);
    }

    // Event listener ketika tombol "Tampilkan" diklik
    tampilkanLaporanBtn.addEventListener('click', function() {
        const jenisLaporan = jenisLaporanSelect.value;
        const bulan = bulanLaporanSelect.value;
        const tahun = tahunLaporanSelect.value;

        // Validasi: Pastikan semua filter (Jenis, Bulan, Tahun) sudah dipilih.
        if (!jenisLaporan || !bulan || !tahun) {
            if (validationModal && validationMessageEl) { // Cek apakah modal siap digunakan
                validationMessageEl.textContent = 'Silakan lengkapi semua filter (Jenis Laporan, Bulan, dan Tahun).';
                validationModal.show(); // Tampilkan modal peringatan.
            } else {
                alert('Silakan lengkapi semua filter (Jenis Laporan, Bulan, dan Tahun).'); // Fallback alert.
            }
            return; // Hentikan eksekusi lebih lanjut jika filter tidak lengkap.
        }

        // Persiapan UI sebelum memuat data: tampilkan loader, sembunyikan konten lama.
        wadahLaporanDiv.innerHTML = '<p class="text-center py-5"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memuat data...</p>';
        paginationControlsContainer.style.display = 'none'; // Sembunyikan kontrol paginasi
        areaKontenLaporanDiv.style.display = 'none';      // Sembunyikan area judul dan tombol export dulu
        currentPage = 1;                                 // Reset ke halaman pertama untuk data baru

        // Membuat URL untuk request AJAX ke server.
        // Pastikan path ke 'get_laporan_data.php' ini benar sesuai struktur folder proyek Anda.
        // Untuk KA UPT, path ini mungkin sama dengan PIC jika file PHP untuk ambil data laporannya sama.
        const fetchUrl = `../CRUD/Laporan/get_laporan_data.php?jenisLaporan=${jenisLaporan}&bulan=${bulan}&tahun=${tahun}`;

        // Melakukan request data ke server menggunakan Fetch API.
        fetch(fetchUrl)
            .then(response => { // Callback setelah server merespons.
                if (!response.ok) { // Jika status HTTP bukan 2xx (sukses).
                    return response.text().then(text => { // Coba ambil pesan error dari server.
                        throw new Error(`HTTP error! status: ${response.status}, message: ${text}`);
                    });
                }
                return response.json(); // Jika sukses, parse respons sebagai JSON.
            })
            .then(result => { // Callback setelah data JSON berhasil diparse.
                wadahLaporanDiv.innerHTML = ''; // Bersihkan pesan loading.

                if (result.status === 'success') { // Jika server merespons dengan status 'success'.
                    currentTableFullData = result.data || []; // Simpan data yang diterima, atau array kosong.
                    currentReportTypeForPaging = jenisLaporan; // Catat jenis laporan yang sedang aktif.

                    // Ambil nama bulan dan tahun yang dipilih untuk ditampilkan di judul laporan.
                    const namaBulanDipilih = bulanLaporanSelect.options[bulanLaporanSelect.selectedIndex].text;
                    const tahunDipilih = tahunLaporanSelect.value;

                    if (currentTableFullData.length > 0) { // Jika ada data yang diterima.
                        areaKontenLaporanDiv.style.display = 'block'; // Tampilkan area judul dan tombol export.
                        
                        // Membuat teks judul laporan secara dinamis.
                        let judulText = `Laporan (${jenisLaporanSelect.options[jenisLaporanSelect.selectedIndex].text}) - ${namaBulanDipilih} ${tahunDipilih}`;
                        if (jenisLaporan === 'dataBarang') judulText = `Laporan Data Barang - ${namaBulanDipilih} ${tahunDipilih}`;
                        else if (jenisLaporan === 'dataRuangan') judulText = `Laporan Data Ruangan - ${namaBulanDipilih} ${tahunDipilih}`;
                        else if (jenisLaporan === 'peminjamSeringMeminjam') judulText = `Laporan Peminjam yang Sering Meminjam - ${namaBulanDipilih} ${tahunDipilih}`;
                        else if (jenisLaporan === 'barangSeringDipinjam') judulText = `Laporan Barang yang Sering Dipinjam - ${namaBulanDipilih} ${tahunDipilih}`;
                        else if (jenisLaporan === 'ruanganSeringDipinjam') judulText = `Laporan Ruangan yang Sering Dipinjam - ${namaBulanDipilih} ${tahunDipilih}`;
                        judulKontenLaporanSpan.textContent = judulText; // Set teks judul.
                        
                        displayPage(currentPage); // Panggil fungsi untuk merender tabel dengan halaman pertama.
                        setupPagination();      // Panggil fungsi untuk membuat kontrol paginasi.
                    } else {
                        // Jika tidak ada data yang diterima (array data kosong) untuk periode tersebut.
                        areaKontenLaporanDiv.style.display = 'none';      // Sembunyikan area judul.
                        paginationControlsContainer.style.display = 'none'; // Sembunyikan paginasi.
                        wadahLaporanDiv.innerHTML = '';                 // Pastikan area tabel kosong.

                        // Tampilkan modal validasi dengan pesan "Tidak Ada Data Laporan".
                        if (validationModal && validationMessageEl) {
                            validationMessageEl.textContent = 'Tidak Ada Data Laporan untuk periode yang dipilih.';
                            validationModal.show();
                        } else {
                            alert('Tidak Ada Data Laporan untuk periode yang dipilih.');
                        }
                    }
                } else { 
                    // Jika server merespons dengan status 'error' (masalah di backend).
                    areaKontenLaporanDiv.style.display = 'block'; // Tampilkan area untuk pesan error.
                    judulKontenLaporanSpan.textContent = 'Kesalahan Sistem';
                    wadahLaporanDiv.innerHTML = `<p class="text-danger text-center">Gagal memuat data: ${result.message}</p>`;
                    console.error('Server Error:', result.message); // Log error di konsol.
                    paginationControlsContainer.style.display = 'none';
                }
            })
            .catch(error => { // Jika terjadi error saat proses fetch (misalnya, masalah jaringan).
                areaKontenLaporanDiv.style.display = 'block'; // Tampilkan area untuk pesan error.
                console.error('Fetch Error:', error); // Log error di konsol.
                judulKontenLaporanSpan.textContent = 'Kesalahan Jaringan';
                wadahLaporanDiv.innerHTML = `<p class="text-danger text-center">Terjadi kesalahan saat mengambil data. Detail: ${error.message}</p>`;
                paginationControlsContainer.style.display = 'none';
            });
    });

    // Fungsi untuk menampilkan data pada halaman tabel tertentu (paginasi).
    function displayPage(page) {
      currentPage = page; // Set halaman aktif.
      wadahLaporanDiv.innerHTML = ''; // Kosongkan konten tabel sebelumnya.

      // Hitung indeks data yang akan ditampilkan berdasarkan halaman dan jumlah baris per halaman.
      const startIndex = (currentPage - 1) * rowsPerPage;
      const endIndex = startIndex + rowsPerPage;
      const paginatedItems = currentTableFullData.slice(startIndex, endIndex); // Ambil potongan data.

      // Jika halaman kosong tapi ada data di halaman sebelumnya (misal setelah menghapus data di halaman terakhir).
      if (paginatedItems.length === 0 && currentTableFullData.length > 0 && currentPage > 1) {
        displayPage(currentPage - 1); // Mundur satu halaman.
        return;
      }
      // Tidak perlu lanjut jika tidak ada item sama sekali (kondisi data awal kosong sudah ditangani).
      if (paginatedItems.length === 0 && currentTableFullData.length === 0) return;

      const table = document.createElement('table'); // Buat elemen tabel.
      table.className = 'table table-striped table-bordered table-hover'; // Styling Bootstrap.

      let headers = []; // Array untuk header kolom.
      let dataKeys = [];// Array untuk kunci properti data yang akan ditampilkan.

      // Tentukan header dan kunci data berdasarkan jenis laporan yang dipilih.
      // Penting: dataKeys harus sama dengan nama kolom/alias yang dikembalikan oleh PHP dari database.
      if (currentReportTypeForPaging === 'dataBarang') {
        table.id = 'tabelLaporanDataBarang'; // ID untuk referensi (misal, untuk export Excel yang spesifik tabel).
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
      } else if (currentReportTypeForPaging === 'ruanganSeringDipinjam') {
        table.id = 'tabelLaporanRuanganSeringDipinjam';
        headers = ['ID Ruangan', 'Nama Ruangan', 'Jumlah Dipinjam'];
        dataKeys = ['idRuangan', 'namaRuangan', 'JumlahDipinjam'];
      } else { 
        // Fallback jika jenis laporan tidak dikenal (seharusnya tidak terjadi).
        wadahLaporanDiv.innerHTML = '<p class="text-center">Tampilan tabel untuk jenis laporan ini belum didukung.</p>';
        return;
      }

      // Membuat elemen <thead> dan baris header <tr><th>...</th></tr>.
      const thead = table.createTHead();
      const headerRow = thead.insertRow();
      headers.forEach(text => {
        let th = document.createElement('th');
        th.textContent = text;
        headerRow.appendChild(th);
      });

      // Membuat elemen <tbody> dan mengisi baris data <tr><td>...</td></tr>.
      const tbody = table.createTBody();
      paginatedItems.forEach(item => { // Untuk setiap objek data di halaman ini.
        const row = tbody.insertRow();   // Buat baris baru.
        dataKeys.forEach(key => {       // Untuk setiap kolom/kunci yang ditentukan.
          // Buat sel baru dan isi dengan data, pastikan aman dari null/undefined.
          row.insertCell().textContent = item[key] !== null && item[key] !== undefined ? item[key] : '';
        });
      });
      wadahLaporanDiv.appendChild(table); // Tambahkan tabel ke DOM.
      updatePaginationButtonsActiveState(); // Update status tombol paginasi.
    }

    // Fungsi untuk membuat tombol-tombol navigasi halaman (paginasi).
    function setupPagination() {
      paginationUl.innerHTML = ''; // Hapus tombol paginasi lama.
      // Hitung jumlah total halaman.
      const pageCount = Math.ceil(currentTableFullData.length / rowsPerPage);

      // Jika hanya 1 halaman atau kurang dan data sedikit, tidak perlu paginasi.
      if (pageCount <= 1 && currentTableFullData.length <= rowsPerPage) {
        paginationControlsContainer.style.display = 'none';
        return;
      }
      paginationControlsContainer.style.display = 'block'; // Tampilkan container paginasi.

      // Membuat tombol "Previous".
      let prevLi = document.createElement('li'); prevLi.className = 'page-item';
      let prevLink = document.createElement('a'); prevLink.className = 'page-link';
      prevLink.href = '#'; prevLink.innerHTML = '«'; // Panah kiri.
      prevLink.addEventListener('click', (e) => { e.preventDefault(); if (currentPage > 1) displayPage(currentPage - 1); });
      prevLi.appendChild(prevLink); paginationUl.appendChild(prevLi);

      // Membuat tombol nomor halaman.
      for (let i = 1; i <= pageCount; i++) {
        let pageLi = document.createElement('li'); pageLi.className = 'page-item';
        pageLi.dataset.page = i; // Simpan nomor halaman di data attribute.
        let pageLink = document.createElement('a'); pageLink.className = 'page-link';
        pageLink.href = '#'; pageLink.textContent = i;
        pageLink.addEventListener('click', (e) => { e.preventDefault(); displayPage(parseInt(e.target.closest('li').dataset.page)); });
        pageLi.appendChild(pageLink); paginationUl.appendChild(pageLi);
      }

      // Membuat tombol "Next".
      let nextLi = document.createElement('li'); nextLi.className = 'page-item';
      let nextLink = document.createElement('a'); nextLink.className = 'page-link';
      nextLink.href = '#'; nextLink.innerHTML = '»'; // Panah kanan.
      nextLink.addEventListener('click', (e) => { e.preventDefault(); if (currentPage < pageCount) displayPage(currentPage + 1); });
      nextLi.appendChild(nextLink); paginationUl.appendChild(nextLi);

      updatePaginationButtonsActiveState(); // Perbarui status aktif/disabled tombol.
    }

    // Fungsi untuk mengatur kelas 'active' dan 'disabled' pada tombol paginasi.
    function updatePaginationButtonsActiveState() {
      const pageCount = Math.ceil(currentTableFullData.length / rowsPerPage);
      const pageItems = paginationUl.querySelectorAll('.page-item'); // Ambil semua elemen <li> paginasi.
      
      pageItems.forEach(item => {
        item.classList.remove('active', 'disabled'); // Reset kelas.
        const link = item.querySelector('.page-link');
        const pageNumData = item.dataset.page; // Nomor halaman dari data-page attribute.

        if (link) {
          if (link.innerHTML.includes('«')) { // Tombol "Previous".
            if (currentPage === 1) item.classList.add('disabled');
          } else if (link.innerHTML.includes('»')) { // Tombol "Next".
            if (currentPage === pageCount || pageCount === 0) item.classList.add('disabled');
          } else if (pageNumData && parseInt(pageNumData) === currentPage) { // Tombol nomor halaman.
            item.classList.add('active'); // Jadikan aktif jika nomornya adalah halaman saat ini.
          }
        }
      });
      // Atur visibilitas container paginasi berdasarkan jumlah halaman.
      if (pageCount <= 1 && currentTableFullData.length > 0) {
          paginationControlsContainer.style.display = 'block'; // Tampilkan walau 1 halaman, tombol akan disabled.
          pageItems.forEach(item => item.classList.add('disabled'));
      } else if (pageCount <= 1) { 
         paginationControlsContainer.style.display = 'none'; // Sembunyikan jika 0 atau 1 halaman (tanpa data atau sedikit data).
      } else {
          paginationControlsContainer.style.display = 'block'; // Tampilkan jika lebih dari 1 halaman.
      }
    }
    
    // Event listener untuk tombol "Export ke Excel".
    exportExcelBtn.addEventListener('click', function() {
      // Validasi jika tidak ada data yang bisa di-export.
      if (!currentTableFullData || currentTableFullData.length === 0) {
        alert('Tidak ada data yang tersedia untuk diexport pada periode ini.');
        return;
      }

      const jenisLaporan = jenisLaporanSelect.value;
      const bulanText = bulanLaporanSelect.options[bulanLaporanSelect.selectedIndex].text;
      const tahunText = tahunLaporanSelect.value;
      
      const dataToExport = currentTableFullData; // Gunakan semua data yang sudah difilter server.
      let headersDisplay = []; // Array untuk header kolom Excel.
      let dataKeysForExport = [];// Array kunci untuk mengambil data.
      let fileName = "Laporan.xlsx"; // Nama file Excel default.
      let sheetName = "Laporan";     // Nama sheet default.

      // Konfigurasi header, kunci data, nama file, dan sheet berdasarkan jenis laporan.
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
      } else if (jenisLaporan === 'ruanganSeringDipinjam') {
        headersDisplay = ['ID Ruangan', 'Nama Ruangan', 'Jumlah Dipinjam'];
        dataKeysForExport = ['idRuangan', 'namaRuangan', 'JumlahDipinjam'];
        fileName = `Laporan_Ruangan_Sering_Dipinjam_${bulanText}_${tahunText}.xlsx`;
        sheetName = "Ruangan Sering Dipinjam";
      } else {
        alert('Jenis laporan tidak dikenal untuk export.');
        return;
      }

      // Mempersiapkan data untuk SheetJS: array of arrays, baris pertama adalah header.
      const ws_data = [ headersDisplay ]; 
      dataToExport.forEach(item => { // Loop untuk setiap baris data.
          // Ambil nilai untuk setiap kolom berdasarkan dataKeysForExport.
          const rowData = dataKeysForExport.map(key => item[key] !== null && item[key] !== undefined ? item[key] : '');
          ws_data.push(rowData); // Tambahkan baris data ke array utama.
      });
      
      // Menggunakan SheetJS untuk membuat dan mengunduh file Excel.
      const ws = XLSX.utils.aoa_to_sheet(ws_data); // Konversi array data ke worksheet.
      const wb = XLSX.utils.book_new();             // Buat workbook baru.
      XLSX.utils.book_append_sheet(wb, ws, sheetName); // Tambahkan worksheet ke workbook.
      XLSX.writeFile(wb, fileName);                 // Mulai download file.
    });
  });
</script>

<?php
// Memanggil footer.php yang akan menutup tag HTML dan memuat skrip global lainnya.
include '../templates/footer.php';
?>