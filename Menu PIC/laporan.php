<?php

include '../templates/header.php';
include '../templates/sidebar.php';
?>

<!-- Konten Utama Halaman Laporan -->
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <!-- Pastikan path BASE_URL benar jika Anda memanggilnya dari header.php -->
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Laporan</li>
            </ol>
        </nav>
    </div>

    <!-- Filter Laporan Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Filter Laporan</h5>
            <div class="row g-3 align-items-end">
                <!-- Dropdown Jenis Laporan -->
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
                <!-- Dropdown Bulan -->
                <div class="col-md-3">
                    <label for="bulanLaporan" class="form-label">Bulan</label>
                    <select class="form-select" id="bulanLaporan">
                        <option selected disabled value="">Pilih Bulan...</option>
                        <!-- Opsi bulan dari Januari hingga Desember -->
                        <option value="01">Januari</option> <option value="02">Februari</option> <option value="03">Maret</option>
                        <option value="04">April</option> <option value="05">Mei</option> <option value="06">Juni</option>
                        <option value="07">Juli</option> <option value="08">Agustus</option> <option value="09">September</option>
                        <option value="10">Oktober</option> <option value="11">November</option> <option value="12">Desember</option>
                    </select>
                </div>
                <!-- Dropdown Tahun -->
                <div class="col-md-3">
                    <label for="tahunLaporan" class="form-label">Tahun</label>
                    <select class="form-select" id="tahunLaporan">
                        <option selected disabled value="">Pilih Tahun...</option>
                        <!-- Tahun akan diisi secara dinamis oleh JavaScript -->
                    </select>
                </div>
                <!-- Tombol Tampilkan Laporan -->
                <div class="col-md-2 d-flex">
                    <button class="btn btn-primary w-100" id="tampilkanLaporanBtn"><i class="bi bi-search me-1"></i> Tampilkan</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Filter Laporan Card -->

    <!-- Area untuk menampilkan konten laporan (judul, tombol export, tabel) -->
    <div id="areaKontenLaporan" style="display: none;"> <!-- Awalnya disembunyikan -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 id="judulKontenLaporan" class="mb-0"></h4> <!-- Judul laporan dinamis -->
            <button class="btn btn-success" id="exportExcelBtn"> <!-- Tombol Export ke Excel -->
                <i class="bi bi-file-earmark-excel me-1"></i> Export ke Excel
            </button>
        </div>
        <div id="wadahLaporan" class="table-responsive">
            <!-- Tabel data laporan akan dimuat di sini oleh JavaScript -->
        </div>
    </div>
    
    <!-- Kontrol Paginasi (navigasi halaman tabel) -->
    <div id="paginationControlsContainer" class="mt-3" style="display: none;"> <!-- Awalnya disembunyikan -->
        <nav aria-label="Page navigation">
            <ul class="pagination" id="paginationUl">
                <!-- Tombol paginasi (Previous, 1, 2, ..., Next) akan digenerate oleh JavaScript -->
            </ul>
        </nav>
    </div>
</main>
<!-- Akhir Konten Utama Halaman Laporan -->

<!-- Modal HTML untuk Validasi -->
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
                <!-- Pesan error validasi akan muncul di sini -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
            </div>
        </div>
    </div>
</div>

<!-- Library JavaScript SheetJS untuk export ke Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
// Menjalankan kode setelah seluruh halaman HTML selesai dimuat (DOMContentLoaded).
document.addEventListener('DOMContentLoaded', function() {
    // Mengambil elemen-elemen DOM yang akan dimanipulasi.
    const jenisLaporanSelect = document.getElementById('jenisLaporan');
    const bulanLaporanSelect = document.getElementById('bulanLaporan');
    const tahunLaporanSelect = document.getElementById('tahunLaporan');
    const tampilkanLaporanBtn = document.getElementById('tampilkanLaporanBtn');

    const areaKontenLaporanDiv = document.getElementById('areaKontenLaporan');
    const judulKontenLaporanSpan = document.getElementById('judulKontenLaporan');
    const wadahLaporanDiv = document.getElementById('wadahLaporan'); // Tempat tabel akan dirender
    const exportExcelBtn = document.getElementById('exportExcelBtn');
    
    // Elemen dan instance untuk modal validasi Bootstrap.
    const validationModalElement = document.getElementById('validationModal');
    const validationModal = validationModalElement ? new bootstrap.Modal(validationModalElement) : null;
    const validationMessageEl = document.getElementById('validationMessage');

    // Variabel global untuk state paginasi dan data laporan.
    let currentPage = 1;        // Halaman tabel yang aktif saat ini.
    const rowsPerPage = 5;      // Jumlah baris data yang ditampilkan per halaman.
    let currentTableFullData = []; // Menyimpan semua data yang diterima dari server.
    let currentReportTypeForPaging = ''; // Menyimpan jenis laporan yang sedang ditampilkan.
    const paginationControlsContainer = document.getElementById('paginationControlsContainer');
    const paginationUl = document.getElementById('paginationUl'); // Elemen <ul> untuk tombol paginasi.

    // Mengisi dropdown tahun (misalnya, 5 tahun ke belakang dari tahun saat ini).
    const currentYear = new Date().getFullYear();
    for (let i = 0; i < 5; i++) {
        const year = currentYear - i;
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        tahunLaporanSelect.appendChild(option);
    }
    // Opsional: Set default tahun ke tahun saat ini atau tahun terakhir dalam daftar.
    // tahunLaporanSelect.value = currentYear; 

    // Event listener untuk tombol "Tampilkan Laporan".
    tampilkanLaporanBtn.addEventListener('click', function() {
        const jenisLaporan = jenisLaporanSelect.value;
        const bulan = bulanLaporanSelect.value;
        const tahun = tahunLaporanSelect.value;

        // 1. Validasi input awal: Pastikan semua filter (jenis, bulan, tahun) telah dipilih.
        if (!jenisLaporan || !bulan || !tahun) {
            if (validationModal && validationMessageEl) {
                validationMessageEl.textContent = 'Silakan lengkapi semua filter (Jenis Laporan, Bulan, dan Tahun).';
                validationModal.show(); // Tampilkan modal peringatan.
            } else {
                alert('Silakan lengkapi semua filter (Jenis Laporan, Bulan, dan Tahun).'); // Fallback alert.
            }
            return; // Hentikan proses jika validasi gagal.
        }
        
        // Tampilkan UI loading: Pesan "Memuat data..." dan sembunyikan konten laporan/paginasi yang lama.
        wadahLaporanDiv.innerHTML = '<p class="text-center py-5"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memuat data...</p>';
        paginationControlsContainer.style.display = 'none';
        areaKontenLaporanDiv.style.display = 'none'; // Sembunyikan area utama laporan (judul & tombol export) dulu.
        currentPage = 1; // Reset ke halaman pertama setiap kali data baru dimuat.

        // URL endpoint untuk mengambil data laporan dari server.
        // Pastikan path ini benar relatif terhadap lokasi file laporan.php.
        // Variabel BASE_APP_URL (jika didefinisikan dari config.php) bisa digunakan untuk path absolut.
        const fetchUrl = `../CRUD/Laporan/get_laporan_data.php?jenisLaporan=${jenisLaporan}&bulan=${bulan}&tahun=${tahun}`;

        // Melakukan request AJAX (fetch) ke server.
        fetch(fetchUrl)
          .then(response => { // Setelah server merespons.
            if (!response.ok) { // Jika status HTTP bukan 2xx (OK).
              // Coba baca pesan error dari server jika ada.
              return response.text().then(text => { 
                throw new Error(`HTTP error! status: ${response.status}, message: ${text}`); 
              });
            }
            return response.json(); // Jika respons OK, parse body sebagai JSON.
          })
          .then(result => { // Setelah data JSON berhasil di-parse.
            wadahLaporanDiv.innerHTML = ''; // Bersihkan pesan loading.

            if (result.status === 'success') { // Jika server mengembalikan status 'success'.
              currentTableFullData = result.data || []; // Simpan data laporan, atau array kosong jika data null/undefined.
              currentReportTypeForPaging = jenisLaporan; // Simpan jenis laporan saat ini.
              
              // Ambil nama bulan dan tahun yang dipilih pengguna untuk ditampilkan di judul.
              const namaBulanDipilih = bulanLaporanSelect.options[bulanLaporanSelect.selectedIndex].text;
              const tahunDipilih = tahunLaporanSelect.value; 
              
              if (currentTableFullData.length > 0) { 
                // KASUS 1: ADA DATA yang dikembalikan dari server untuk periode yang dipilih.
                areaKontenLaporanDiv.style.display = 'block'; // Tampilkan area judul laporan & tombol export.
                
                // Membuat teks judul laporan secara dinamis.
                let judulText = `Laporan (${jenisLaporanSelect.options[jenisLaporanSelect.selectedIndex].text}) - ${namaBulanDipilih} ${tahunDipilih}`;
                if (jenisLaporan === 'dataBarang') judulText = `Laporan Data Barang - ${namaBulanDipilih} ${tahunDipilih}`;
                else if (jenisLaporan === 'dataRuangan') judulText = `Laporan Data Ruangan - ${namaBulanDipilih} ${tahunDipilih}`;
                else if (jenisLaporan === 'peminjamSeringMeminjam') judulText = `Laporan Peminjam yang Sering Meminjam - ${namaBulanDipilih} ${tahunDipilih}`;
                else if (jenisLaporan === 'barangSeringDipinjam') judulText = `Laporan Barang yang Sering Dipinjam - ${namaBulanDipilih} ${tahunDipilih}`;
                else if (jenisLaporan === 'ruanganSeringDipinjam') judulText = `Laporan Ruangan yang Sering Dipinjam - ${namaBulanDipilih} ${tahunDipilih}`;
                judulKontenLaporanSpan.textContent = judulText;
                
                displayPage(currentPage); // Panggil fungsi untuk merender tabel dengan halaman pertama.
                setupPagination();      // Panggil fungsi untuk membuat tombol-tombol paginasi.
              } else {
                // KASUS 2: TIDAK ADA DATA (array data kosong) yang dikembalikan server untuk periode ini.
                areaKontenLaporanDiv.style.display = 'none'; // Pastikan area judul dan export tetap tersembunyi.
                paginationControlsContainer.style.display = 'none'; // Sembunyikan paginasi juga.
                wadahLaporanDiv.innerHTML = ''; // Area tabel juga dipastikan kosong.

                // Tampilkan modal validasi "Tidak Ada Data Laporan".
                if (validationModal && validationMessageEl) {
                    validationMessageEl.textContent = 'Tidak Ada Data Laporan untuk periode yang dipilih.';
                    validationModal.show();
                } else {
                    alert('Tidak Ada Data Laporan untuk periode yang dipilih.');
                }
              }
            } else { 
              // KASUS 3: Server mengembalikan status 'error' (ada masalah di sisi server).
              areaKontenLaporanDiv.style.display = 'block'; // Tampilkan area untuk pesan error.
              judulKontenLaporanSpan.textContent = 'Kesalahan Sistem'; // Judul generik untuk error.
              wadahLaporanDiv.innerHTML = `<p class="text-danger text-center">Gagal memuat data: ${result.message}</p>`; // Tampilkan pesan error dari server.
              console.error('Server Error:', result.message); // Log detail error ke konsol browser.
              paginationControlsContainer.style.display = 'none';
            }
          })
          .catch(error => { // Menangkap error jaringan atau error saat fetch/parsing JSON.
            areaKontenLaporanDiv.style.display = 'block'; // Tampilkan area untuk pesan error.
            console.error('Fetch Error:', error); // Log error ke konsol.
            judulKontenLaporanSpan.textContent = 'Kesalahan Jaringan';
            wadahLaporanDiv.innerHTML = `<p class="text-danger text-center">Terjadi kesalahan saat mengambil data. Periksa konsol browser. Detail: ${error.message}</p>`;
            paginationControlsContainer.style.display = 'none';
          });
      });

      // Fungsi untuk merender tabel data pada halaman tertentu.
      function displayPage(page) {
        currentPage = page; // Update halaman aktif saat ini.
        wadahLaporanDiv.innerHTML = ''; // Kosongkan konten tabel sebelumnya.

        // Menghitung indeks awal dan akhir data yang akan ditampilkan untuk halaman ini.
        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;
        // Mengambil potongan data dari `currentTableFullData` sesuai halaman.
        const paginatedItems = currentTableFullData.slice(startIndex, endIndex);

        // Jika halaman yang diminta kosong tapi ada data di halaman sebelumnya (misalnya setelah delete)
        if (paginatedItems.length === 0 && currentTableFullData.length > 0 && currentPage > 1) {
          displayPage(currentPage - 1); // Mundur satu halaman.
          return;
        }
        // Tidak perlu render tabel jika tidak ada item sama sekali (data awal memang kosong).
        if (paginatedItems.length === 0 && currentTableFullData.length === 0) return;


        const table = document.createElement('table'); // Buat elemen <table>.
        table.className = 'table table-striped table-bordered table-hover'; // Tambahkan kelas Bootstrap.
        
        let headers = []; // Array untuk menyimpan nama header kolom.
        let dataKeys = [];// Array untuk menyimpan kunci (nama properti) data yang akan ditampilkan per kolom.
        
        // Menentukan header dan kunci data berdasarkan jenis laporan yang aktif.
        // Kunci (dataKeys) harus SAMA dengan nama field/alias yang dikembalikan oleh PHP di get_laporan_data.php.
        if (currentReportTypeForPaging === 'dataBarang') {
          table.id = 'tabelLaporanDataBarang'; // ID unik untuk tabel (berguna untuk export Excel spesifik).
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
        else { // Jika jenis laporan tidak dikenali (seharusnya tidak terjadi jika validasi awal benar).
          wadahLaporanDiv.innerHTML = '<p class="text-center">Tampilan tabel untuk jenis laporan ini belum didukung.</p>';
          return;
        }

        // Membuat header tabel (<thead>).
        const thead = table.createTHead();
        const headerRow = thead.insertRow();
        headers.forEach(text => {
          let th = document.createElement('th');
          th.textContent = text;
          headerRow.appendChild(th);
        });

        // Membuat body tabel (<tbody>) dan mengisi baris data.
        const tbody = table.createTBody();
        paginatedItems.forEach(item => { // Loop untuk setiap item data pada halaman ini.
          const row = tbody.insertRow(); // Buat baris baru <tr>.
          dataKeys.forEach(key => {      // Loop untuk setiap kunci/kolom.
            // Buat sel <td> dan isi dengan nilai dari `item[key]`.
            // Cek null/undefined untuk menghindari menampilkan "null" atau "undefined" string.
            row.insertCell().textContent = item[key] !== null && item[key] !== undefined ? item[key] : '';
          });
        });
        wadahLaporanDiv.appendChild(table); // Tambahkan tabel yang sudah jadi ke dalam div wadah.
        updatePaginationButtonsActiveState(); // Update status aktif/disabled tombol paginasi.
      }

      // Fungsi untuk membuat tombol-tombol paginasi (Previous, Nomor Halaman, Next).
      function setupPagination() {
        paginationUl.innerHTML = ''; // Kosongkan tombol paginasi sebelumnya.
        // Hitung jumlah total halaman berdasarkan total data dan baris per halaman.
        const pageCount = Math.ceil(currentTableFullData.length / rowsPerPage);

        // Jika hanya 1 halaman atau kurang (atau tidak ada data), tidak perlu paginasi.
        if (pageCount <= 1 && currentTableFullData.length <= rowsPerPage) {
          paginationControlsContainer.style.display = 'none';
          return;
        }
        paginationControlsContainer.style.display = 'block'; // Tampilkan container paginasi.

        // Membuat tombol "Previous".
        let prevLi = document.createElement('li'); prevLi.className = 'page-item';
        let prevLink = document.createElement('a'); prevLink.className = 'page-link';
        prevLink.href = '#'; prevLink.innerHTML = '«'; // Karakter panah kiri.
        prevLink.addEventListener('click', (e) => { 
            e.preventDefault(); // Cegah aksi default link.
            if (currentPage > 1) displayPage(currentPage - 1); // Pindah ke halaman sebelumnya jika bukan halaman pertama.
        });
        prevLi.appendChild(prevLink); paginationUl.appendChild(prevLi);

        // Membuat tombol nomor halaman.
        for (let i = 1; i <= pageCount; i++) {
          let pageLi = document.createElement('li'); pageLi.className = 'page-item';
          pageLi.dataset.page = i; // Simpan nomor halaman di atribut data untuk referensi.
          let pageLink = document.createElement('a'); pageLink.className = 'page-link';
          pageLink.href = '#'; pageLink.textContent = i;
          pageLink.addEventListener('click', (e) => {
            e.preventDefault();
            displayPage(parseInt(e.target.closest('li').dataset.page)); // Pindah ke halaman yang diklik.
          });
          pageLi.appendChild(pageLink); paginationUl.appendChild(pageLi);
        }

        // Membuat tombol "Next".
        let nextLi = document.createElement('li'); nextLi.className = 'page-item';
        let nextLink = document.createElement('a'); nextLink.className = 'page-link';
        nextLink.href = '#'; nextLink.innerHTML = '»'; // Karakter panah kanan.
        nextLink.addEventListener('click', (e) => { 
            e.preventDefault(); 
            if (currentPage < pageCount) displayPage(currentPage + 1); // Pindah ke halaman berikutnya jika bukan halaman terakhir.
        });
        nextLi.appendChild(nextLink); paginationUl.appendChild(nextLi);

        updatePaginationButtonsActiveState(); // Update status tombol.
      }

      // Fungsi untuk mengatur status aktif/disabled pada tombol paginasi.
      function updatePaginationButtonsActiveState() {
        const pageCount = Math.ceil(currentTableFullData.length / rowsPerPage);
        const pageItems = paginationUl.querySelectorAll('.page-item');
        
        pageItems.forEach(item => { // Loop setiap elemen <li> di paginasi.
          item.classList.remove('active', 'disabled'); // Reset status.
          const link = item.querySelector('.page-link');
          const pageNumData = item.dataset.page; // Ambil nomor halaman dari data attribute.

          if (link) {
            if (link.innerHTML.includes('«')) { // Tombol "Previous".
              if (currentPage === 1) item.classList.add('disabled'); // Disable jika di halaman pertama.
            } else if (link.innerHTML.includes('»')) { // Tombol "Next".
              if (currentPage === pageCount || pageCount === 0) item.classList.add('disabled'); // Disable jika di halaman terakhir atau tidak ada halaman.
            } else if (pageNumData && parseInt(pageNumData) === currentPage) { // Tombol nomor halaman.
              item.classList.add('active'); // Tandai aktif jika nomornya sama dengan halaman saat ini.
            }
          }
        });
        // Logika tambahan untuk menyembunyikan/menampilkan container paginasi.
        if (pageCount <= 1 && currentTableFullData.length > 0) { // Jika 1 halaman tapi ada data.
            paginationControlsContainer.style.display = 'block'; // Tampilkan paginasi.
            pageItems.forEach(item => item.classList.add('disabled'));// Disable semua tombolnya.
        } else if (pageCount <= 1) { // Jika 0 atau 1 halaman (tidak ada data atau data sangat sedikit).
           paginationControlsContainer.style.display = 'none'; // Sembunyikan paginasi.
        } else { // Jika lebih dari 1 halaman.
            paginationControlsContainer.style.display = 'block'; // Pastikan paginasi terlihat.
        }
      }
      
      // Event listener untuk tombol "Export ke Excel".
      exportExcelBtn.addEventListener('click', function() {
        // Validasi: pastikan data sudah ada sebelum mencoba export.
        // Walaupun tombol export seharusnya tersembunyi jika tidak ada data, ini sebagai pengaman tambahan.
        if (!currentTableFullData || currentTableFullData.length === 0) {
          alert('Tidak ada data untuk diexport. Silakan tampilkan laporan terlebih dahulu.');
          return;
        }

        const jenisLaporan = jenisLaporanSelect.value; // Ambil jenis laporan yang aktif.
        const bulanText = bulanLaporanSelect.options[bulanLaporanSelect.selectedIndex].text;
        const tahunText = tahunLaporanSelect.value;
        
        const dataToExport = currentTableFullData; // Gunakan SEMUA data yang sudah difilter dari server.
        let headersDisplay = [];    // Header untuk kolom di file Excel.
        let dataKeysForExport = []; // Kunci data yang akan diekspor.
        let fileName = "Laporan.xlsx"; // Nama file default.
        let sheetName = "Laporan";     // Nama sheet default.

        // Menentukan header, kunci data, nama file, dan nama sheet berdasarkan jenis laporan.
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
        } else {
          alert('Jenis laporan tidak dikenal untuk export.');
          return;
        }

        // Membuat data untuk SheetJS (array of arrays). Baris pertama adalah header.
        const ws_data = [ headersDisplay ]; 
        dataToExport.forEach(item => { // Loop setiap item data.
            // Ambil nilai untuk setiap kolom sesuai dataKeysForExport.
            const rowData = dataKeysForExport.map(key => item[key] !== null && item[key] !== undefined ? item[key] : '');
            ws_data.push(rowData); // Tambahkan baris data ke ws_data.
        });
        
        // Membuat worksheet dan workbook menggunakan SheetJS.
        const ws = XLSX.utils.aoa_to_sheet(ws_data); // Convert array of arrays ke worksheet.
        const wb = XLSX.utils.book_new();             // Buat workbook baru.
        XLSX.utils.book_append_sheet(wb, ws, sheetName); // Tambahkan worksheet ke workbook.
        XLSX.writeFile(wb, fileName);                 // Memulai proses download file Excel.
      });

    });
  </script>

<?php
// Include file footer template (yang akan menutup tag body, html, dan memuat script JS global jika ada).
include '../templates/footer.php';
?>