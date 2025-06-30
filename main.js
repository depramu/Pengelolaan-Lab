/**
 * =================================================================
 * PENGELOLAAN LAB - SCRIPT UTAMA (Versi Gabungan & Rapi)
 * =================================================================
 */
document.addEventListener("DOMContentLoaded", function () {
  /**
   * Inisialisasi semua fungsi spesifik halaman.
   * Setiap fungsi akan memeriksa keberadaan elemennya sendiri.
   */
  setupLoginForm();
  setupLupaSandiForm();
  setupLaporanPage();
  setupCekBarangPage();
  setupCekRuanganPage();
  setupDetailRiwayatForm();
  setupPengajuanPage();
  setupPengembalianBarangPage();
  setupPengembalianRuanganPage();

  // Inisialisasi modal sukses global jika ada pesan dari PHP
  const successModalElement = document.getElementById("successModal");
  // Cek dari variabel global atau elemen tersembunyi jika diperlukan
  // Contoh sederhana:
  if (
    typeof showSuccessModalOnLoad !== "undefined" &&
    showSuccessModalOnLoad &&
    successModalElement
  ) {
    new bootstrap.Modal(successModalElement).show();
  }
});

// =================================================================
// #1: HALAMAN LOGIN & LUPA SANDI
// =================================================================

function setupLoginForm() {
  const loginForm = document.querySelector("form"); // Asumsi hanya ada 1 form di halaman login
  if (!loginForm || !document.getElementById("identifier")) return; // Cek unik untuk halaman login

  loginForm.addEventListener("submit", function (e) {
    const idInput = document.getElementById("identifier");
    const passInput = document.getElementById("kataSandi");
    const idError = document.getElementById("identifier-error");
    const passError = document.getElementById("password-error");

    let isValid = true;
    idError.textContent = "";
    passError.textContent = "";

    if (!idInput.value.trim()) {
      idError.textContent = "*NIM/NPK tidak boleh kosong.";
      isValid = false;
    } else if (!/^\d+$/.test(idInput.value.trim())) {
      idError.textContent = "*NIM/NPK harus berupa angka.";
      isValid = false;
    }

    if (!passInput.value.trim()) {
      passError.textContent = "*Kata Sandi tidak boleh kosong.";
      isValid = false;
    }

    if (!isValid) {
      e.preventDefault();
    }
  });

  // Menangani pesan error dari server setelah reload
  const serverError = document.getElementById("server-error");
  if (serverError && serverError.textContent.trim() !== "") {
    const errorMessage = serverError.textContent.trim().toLowerCase();
    serverError.classList.add("d-none"); // Sembunyikan pesan asli

    const idError = document.getElementById("identifier-error");
    const passError = document.getElementById("password-error");

    if (errorMessage.includes("akun_tidak_terdaftar")) {
      idError.textContent = "*Akun tidak terdaftar*";
    } else if (errorMessage.includes("kata_sandi_salah")) {
      passError.textContent = "*Kata sandi salah*";
    } else {
      idError.textContent = serverError.textContent.trim(); // Tampilkan error umum
    }
  }
}

function setupLupaSandiForm() {
  const lupaSandiForm = document.querySelector("form");
  if (!lupaSandiForm || !document.getElementById("email")) return; // Cek unik untuk halaman lupa sandi

  document.querySelector(".btn-back").onclick = () =>
    (window.location.href = "Login/login.php");

  lupaSandiForm.addEventListener("submit", function (e) {
    const emailInput = document.getElementById("email");
    const emailError = document.getElementById("emailError");
    let isValid = true;
    emailError.style.display = "none";

    if (!emailInput.value.trim()) {
      emailError.textContent = "*Harus diisi";
      isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
      emailError.textContent = "*Format email tidak valid";
      isValid = false;
    }

    if (!isValid) {
      e.preventDefault();
      emailError.style.display = "inline";
    }
  });
}

// =================================================================
// #2: HALAMAN LAPORAN
// =================================================================

function setupLaporanPage() {
  // Cek unik untuk halaman laporan
  const btnTampilkan = document.getElementById("tampilkanLaporanBtn");
  if (!btnTampilkan) return;
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
    let currentPage = 1; // Halaman tabel yang aktif saat ini.
    const rowsPerPage = 5; // Jumlah baris data yang ditampilkan per halaman.
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
              setupPagination(); // Panggil fungsi untuk membuat tombol-tombol paginasi.
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
      let dataKeys = []; // Array untuk menyimpan kunci (nama properti) data yang akan ditampilkan per kolom.

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
      } else if (currentReportTypeForPaging === 'ruanganSeringDipinjam') {
        table.id = 'tabelLaporanRuanganSeringDipinjam';
        headers = ['ID Ruangan', 'Nama Ruangan', 'Jumlah Dipinjam'];
        dataKeys = ['idRuangan', 'namaRuangan', 'JumlahDipinjam'];
      } else { // Jika jenis laporan tidak dikenali (seharusnya tidak terjadi jika validasi awal benar).
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
        dataKeys.forEach(key => { // Loop untuk setiap kunci/kolom.
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
      let prevLi = document.createElement('li');
      prevLi.className = 'page-item';
      let prevLink = document.createElement('a');
      prevLink.className = 'page-link';
      prevLink.href = '#';
      prevLink.innerHTML = '«'; // Karakter panah kiri.
      prevLink.addEventListener('click', (e) => {
        e.preventDefault(); // Cegah aksi default link.
        if (currentPage > 1) displayPage(currentPage - 1); // Pindah ke halaman sebelumnya jika bukan halaman pertama.
      });
      prevLi.appendChild(prevLink);
      paginationUl.appendChild(prevLi);

      // Membuat tombol nomor halaman.
      for (let i = 1; i <= pageCount; i++) {
        let pageLi = document.createElement('li');
        pageLi.className = 'page-item';
        pageLi.dataset.page = i; // Simpan nomor halaman di atribut data untuk referensi.
        let pageLink = document.createElement('a');
        pageLink.className = 'page-link';
        pageLink.href = '#';
        pageLink.textContent = i;
        pageLink.addEventListener('click', (e) => {
          e.preventDefault();
          displayPage(parseInt(e.target.closest('li').dataset.page)); // Pindah ke halaman yang diklik.
        });
        pageLi.appendChild(pageLink);
          paginationUl.appendChild(pageLi);
      }

      // Membuat tombol "Next".
      let nextLi = document.createElement('li');
      nextLi.className = 'page-item';
      let nextLink = document.createElement('a');
      nextLink.className = 'page-link';
      nextLink.href = '#';
      nextLink.innerHTML = '»'; // Karakter panah kanan.
      nextLink.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage < pageCount) displayPage(currentPage + 1); // Pindah ke halaman berikutnya jika bukan halaman terakhir.
      });
      nextLi.appendChild(nextLink);
      paginationUl.appendChild(nextLi);

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
        pageItems.forEach(item => item.classList.add('disabled')); // Disable semua tombolnya.
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
      let headersDisplay = []; // Header untuk kolom di file Excel.
      let dataKeysForExport = []; // Kunci data yang akan diekspor.
      let fileName = "Laporan.xlsx"; // Nama file default.
      let sheetName = "Laporan"; // Nama sheet default.

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

      // Membuat data untuk SheetJS (array of arrays). Baris pertama adalah header.
      const ws_data = [headersDisplay];
      dataToExport.forEach(item => { // Loop setiap item data.
        // Ambil nilai untuk setiap kolom sesuai dataKeysForExport.
        const rowData = dataKeysForExport.map(key => item[key] !== null && item[key] !== undefined ? item[key] : '');
        ws_data.push(rowData); // Tambahkan baris data ke ws_data.
      });

      // Membuat worksheet dan workbook menggunakan SheetJS.
      const ws = XLSX.utils.aoa_to_sheet(ws_data); // Convert array of arrays ke worksheet.
      const wb = XLSX.utils.book_new(); // Buat workbook baru.
      XLSX.utils.book_append_sheet(wb, ws, sheetName); // Tambahkan worksheet ke workbook.
      XLSX.writeFile(wb, fileName); // Memulai proses download file Excel.
    });
}

// =================================================================
// #3: HALAMAN PEMINJAMAN (CEK BARANG & RUANGAN)
// =================================================================

// Helper untuk date picker
function isLeapYear(year) {
  return (year % 4 === 0 && year % 100 !== 0) || year % 400 === 0;
}

function updateDays() {
  let bulan = parseInt(document.getElementById('tglBulan').value);
  let tahun = parseInt(document.getElementById('tglTahun').value);
  let hariSelect = document.getElementById('tglHari');
  let prevHari = hariSelect.value;
  let days = 31;
  if ([4, 6, 9, 11].includes(bulan)) days = 30;
  else if (bulan === 2) days = isLeapYear(tahun) ? 29 : 28;

  hariSelect.innerHTML = '';
  for (let i = 1; i <= days; i++) {
      hariSelect.innerHTML += `<option value="${i.toString().padStart(2, '0')}">${i}</option>`;
  }
  // Kembalikan ke hari sebelumnya jika masih valid, jika tidak pilih hari terakhir
  if (prevHari && parseInt(prevHari) <= days) {
      hariSelect.value = prevHari.padStart(2, '0');
  } else {
      hariSelect.value = days.toString().padStart(2, '0');
  }
}

function fillSelects() {
  let tahunSelect = document.getElementById('tglTahun');
  let bulanSelect = document.getElementById('tglBulan');
  let hariSelect = document.getElementById('tglHari');
  let now = new Date();
  for (let y = now.getFullYear(); y <= now.getFullYear() + 5; y++) {
      tahunSelect.innerHTML += `<option value="${y}">${y}</option>`;
  }
  for (let m = 1; m <= 12; m++) {
      bulanSelect.innerHTML += `<option value="${m}">${m.toString().padStart(2, '0')}</option>`;
  }
  bulanSelect.value = now.getMonth() + 1;
  tahunSelect.value = now.getFullYear();
  updateDays();
  // Set hari ke hari ini
  hariSelect.value = now.getDate().toString().padStart(2, '0');
}

document.addEventListener('DOMContentLoaded', function() {
  fillSelects();
  document.getElementById('tglBulan').addEventListener('change', updateDays);
  document.getElementById('tglTahun').addEventListener('change', updateDays);

  document.querySelector('form').addEventListener('submit', function(event) {
      // validasi tanggal
      let hari = document.getElementById('tglHari').value;
      let bulan = document.getElementById('tglBulan').value;
      let tahun = document.getElementById('tglTahun').value;
      let errorTanggal = document.getElementById('error-message');
      let isValid = hari && bulan && tahun;
      let pesan = '';
      // Validasi tanggal tidak boleh di masa lalu
      if (isValid) {
          let inputDate = new Date(`${tahun}-${bulan.padStart(2, '0')}-${hari.padStart(2, '0')}`);
          let today = new Date();
          today.setHours(0, 0, 0, 0);
          if (inputDate < today) {
              isValid = false;
              pesan = 'Input tanggal sudah lewat';
          }
      }
      if (!isValid) {
          errorTanggal.textContent = pesan ? `*${pesan}` : '*Harus Diisi';
          errorTanggal.style.display = 'inline';
          event.preventDefault();
      } else {
          errorTanggal.style.display = 'none';
          document.getElementById('tglPeminjamanBrg').value = `${hari.padStart(2, '0')}-${bulan.padStart(2, '0')}-${tahun}`;
      }
  });
});

function isiWaktu(id, max) {
  const el = document.getElementById(id);
  el.innerHTML = '<option value="">--</option>'; // Tambahkan pilihan kosong default
  for (let i = 0; i < max; i++) {
      const val = i.toString().padStart(2, '0');
      el.innerHTML += `<option value="${val}">${val}</option>`;
  }
}

fillSelects();
isiWaktu('jam_dari', 24);
isiWaktu('jam_sampai', 24);
isiWaktu('menit_dari', 60);
isiWaktu('menit_sampai', 60);

document.getElementById('tglBulan').addEventListener('change', updateDays);
document.getElementById('tglTahun').addEventListener('change', updateDays);

document.getElementById('form-peminjaman').addEventListener('submit', function(e) {
    const hari = document.getElementById('tglHari').value;
    const bulan = document.getElementById('tglBulan').value;
    const tahun = document.getElementById('tglTahun').value;
    const jamDari = document.getElementById('jam_dari').value;
    const menitDari = document.getElementById('menit_dari').value;
    const jamSampai = document.getElementById('jam_sampai').value;
    const menitSampai = document.getElementById('menit_sampai').value;

    const errorMsg = document.getElementById('error-message');
    const errorWaktu = document.getElementById('error-waktu');
    const errorWaktuMulai = document.getElementById('error-waktu-mulai');
    const errorWaktuSelesai = document.getElementById('error-waktu-selesai');

    let isValid = true;

    // Validasi Tanggal (kosong atau sudah lewat)
    if (!hari || !bulan || !tahun) {
        errorMsg.textContent = "*Harus Diisi";
        errorMsg.style.display = 'inline';
        isValid = false;
    } else {
        const inputDate = new Date(`${tahun}-${bulan.padStart(2, '0')}-${hari.padStart(2, '0')}`);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (inputDate < today) {
            errorMsg.textContent = "*Input tanggal sudah lewat";
            errorMsg.style.display = 'inline';
            isValid = false;
        } else {
            errorMsg.style.display = 'none';
        }
    }

    // Validasi Waktu kosong
    let waktuValid = true;
    if (jamDari === "" || menitDari === "" || isNaN(parseInt(jamDari)) || isNaN(parseInt(menitDari))) {
        errorWaktuMulai.style.display = 'inline';
        waktuValid = false;
    } else {
        errorWaktuMulai.style.display = 'none';
    }

    if (jamSampai === "" || menitSampai === "" || isNaN(parseInt(jamSampai)) || isNaN(parseInt(menitSampai))) {
        errorWaktuSelesai.style.display = 'inline';
        waktuValid = false;
    } else {
        errorWaktuSelesai.style.display = 'none';
    }

    // Validasi logika waktu (hanya jika waktu terisi)
    if (waktuValid) {
        const startMinutes = parseInt(jamDari) * 60 + parseInt(menitDari);
        const endMinutes = parseInt(jamSampai) * 60 + parseInt(menitSampai);
        const selectedDate = new Date(`${tahun}-${bulan.padStart(2, '0')}-${hari.padStart(2, '0')}`);
        const now = new Date();
        const nowMinutes = now.getHours() * 60 + now.getMinutes();

        if (endMinutes <= startMinutes) {
            errorWaktu.textContent = '*Waktu selesai harus lebih besar dari waktu mulai';
            errorWaktu.style.display = 'block';
            isValid = false;
        } else if (selectedDate.toDateString() === now.toDateString() && startMinutes < nowMinutes) {
            errorWaktu.textContent = '*Waktu mulai tidak boleh lebih kecil dari waktu sekarang';
            errorWaktu.style.display = 'block';
            isValid = false;
        } else {
            errorWaktu.style.display = 'none';
        }
    } else {
        errorWaktu.style.display = 'none';
        isValid = false;
    }

    // Cegah submit kalau ada yang salah
    if (!isValid) {
        e.preventDefault();
        return;
    }

    // Set input tersembunyi kalau semua valid
    document.getElementById('tglPeminjamanRuangan').value = `${hari}-${bulan}-${tahun}`;
});



// =================================================================
// #4: HALAMAN LAINNYA (DETAIL, PENGEMBALIAN, PENGAJUAN)
// =================================================================

function setupDetailRiwayatForm() {
  const form = document.getElementById("formDetail");
  if (!form) return;

  form.addEventListener("submit", function (event) {
    let isValid = true;
    const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.heif|\.heic)$/i;

    const validateFile = (inputId, errorId) => {
      const fileInput = document.getElementById(inputId);
      const errorSpan = document.getElementById(errorId);
      if (!fileInput) return;

      errorSpan.textContent = "";
      if (fileInput.files.length === 0) {
        errorSpan.textContent = "File wajib diupload.";
        isValid = false;
      } else if (!allowedExtensions.exec(fileInput.value)) {
        errorSpan.textContent = "Format file tidak valid.";
        isValid = false;
      }
    };

    validateFile("dokSebelum", "dokSebelumError");
    validateFile("dokSesudah", "dokSesudahError");

    if (!isValid) event.preventDefault();
  });
}

function setupPengajuanPage() {
  const btnTolakShowField = document.getElementById("btnTolakShowField");
  if (!btnTolakShowField) return;

  // ... (Logika untuk menampilkan field alasan penolakan dari blok skrip Anda) ...
}

function setupPengembalianBarangPage() {
  const form = document.getElementById("formPengembalianBarang"); // Ganti dengan ID yang benar
  if (!form) return;

  // Validasi form pengembalian barang
  form.addEventListener("submit", function (e) {
    let isValid = true;

    // Jumlah Pengembalian
    const jumlahInput = document.getElementById("jumlahPengembalian");
    const jumlahError = document.getElementById("jumlahError");
    const sisaPinjaman = parseInt(document.getElementById("sisaPinjaman")?.value || "0", 10);

    if (!jumlahInput.value || isNaN(jumlahInput.value) || parseInt(jumlahInput.value, 10) <= 0) {
      jumlahError.textContent = "*Harus Diisi";
      jumlahError.style.display = "inline";
      isValid = false;
    } else if (parseInt(jumlahInput.value, 10) > sisaPinjaman) {
      jumlahError.textContent = "*Melebihi sisa pinjaman";
      jumlahError.style.display = "inline";
      isValid = false;
    } else {
      jumlahError.style.display = "none";
    }

    // Kondisi Barang
    const kondisiSelect = document.getElementById("txtKondisi");
    const kondisiError = document.getElementById("kondisiError");
    if (!kondisiSelect.value || kondisiSelect.value === "Pilih Kondisi Barang") {
      kondisiError.textContent = "*Harus Dipilih";
      kondisiError.style.display = "inline";
      isValid = false;
    } else {
      kondisiError.style.display = "none";
    }

    // Catatan Pengembalian
    const catatanInput = document.getElementById("catatanPengembalianBarang");
    const catatanError = document.getElementById("catatanError");
    if (!catatanInput.value.trim()) {
      catatanError.textContent = "*Harus Diisi";
      catatanError.style.display = "inline";
      isValid = false;
    } else {
      catatanError.style.display = "none";
    }

    if (!isValid) {
      e.preventDefault();
    }
  });

  // Stepper tombol + dan - untuk jumlah pengembalian
  window.changeStok = function (delta) {
    const jumlahInput = document.getElementById("jumlahPengembalian");
    const sisaPinjaman = parseInt(document.getElementById("sisaPinjaman")?.value || "0", 10);
    let val = parseInt(jumlahInput.value, 10) || 0;
    val += delta;
    if (val < 0) val = 0;
    if (val > sisaPinjaman) val = sisaPinjaman;
    jumlahInput.value = val;
    // Trigger validasi ulang
    jumlahInput.dispatchEvent(new Event("input"));
  };
}

function setupPengembalianRuanganPage() {
  const form = document.getElementById("formPengembalianRuangan");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    // ... (Logika validasi untuk pengembalian ruangan) ...
  });
}

/**
 * =================================================================
 * PENGELOLAAN LAB - SCRIPT UTAMA (Tambahan Baru)
 * =================================================================
 * Mengintegrasikan logika persistensi sidebar, proteksi input, dan modal.
 */

// Letakkan ini di dalam event listener utama Anda di file main.js
document.addEventListener("DOMContentLoaded", function () {
  // Panggil fungsi-fungsi setup yang baru
  setupSidebarPersistence();
  setupInputProtection();
  setupModalChaining();

  // ... (panggil fungsi-fungsi lain yang sudah ada sebelumnya seperti setupLoginForm, dll.)
});

/**
 * @function setupSidebarPersistence
 * @description Menyimpan dan memulihkan status menu akordion pada sidebar menggunakan localStorage.
 * Sehingga menu yang terbuka akan tetap terbuka setelah refresh halaman.
 */
function setupSidebarPersistence() {
  const sidebar = document.querySelector(".sidebar, .offcanvas-body");
  if (!sidebar) return; // Keluar jika sidebar tidak ditemukan

  const storageKey = "sidebar_active_menus";

  // Helper untuk mengambil data dari localStorage
  const getActiveMenus = () => {
    const activeMenus = localStorage.getItem(storageKey);
    return activeMenus ? JSON.parse(activeMenus) : [];
  };

  // Helper untuk menyimpan data ke localStorage
  const setActiveMenus = (menus) => {
    localStorage.setItem(storageKey, JSON.stringify(menus));
  };

  // Saat halaman dimuat, pulihkan status menu
  const activeMenuIds = getActiveMenus();
  activeMenuIds.forEach((menuId) => {
    const menuElement = document.getElementById(menuId);
    if (menuElement) {
      // Gunakan instance Bootstrap untuk membukanya tanpa animasi toggle
      const collapseInstance = new bootstrap.Collapse(menuElement, {
        toggle: false,
      });
      collapseInstance.show();
    }
  });

  // Tambahkan event listener ke semua menu collapse di sidebar
  sidebar.querySelectorAll(".collapse").forEach((menu) => {
    // Saat submenu akan ditampilkan
    menu.addEventListener("show.bs.collapse", function () {
      let activeMenus = getActiveMenus();
      if (!activeMenus.includes(this.id)) {
        activeMenus.push(this.id);
        setActiveMenus(activeMenus);
      }
    });

    // Saat submenu akan disembunyikan
    menu.addEventListener("hide.bs.collapse", function () {
      let activeMenus = getActiveMenus();
      const index = activeMenus.indexOf(this.id);
      if (index > -1) {
        activeMenus.splice(index, 1);
        setActiveMenus(activeMenus);
      }
    });
  });
}

/**
 * @function setupInputProtection
 * @description Mencegah pengguna melakukan copy-paste atau mengubah nilai
 * pada setiap elemen input dengan kelas .protect-input.
 */
function setupInputProtection() {
  document.querySelectorAll(".protect-input").forEach((input) => {
    // Mencegah menempelkan konten
    input.addEventListener("paste", (e) => e.preventDefault());

    // Mengembalikan ke nilai awal jika ada perubahan
    input.addEventListener("input", () => (input.value = input.defaultValue));

    // Mencegah fokus dengan mouse (opsional tapi efektif)
    input.addEventListener("mousedown", (e) => e.preventDefault());
  });
}

/**
 * @function setupModalChaining
 * @description Menangani interaksi antar modal.
 * Contoh: Menutup modal konfirmasi dan membuka modal sukses.
 */
function setupModalChaining() {
  const confirmYesButton = document.getElementById("confirmYes");
  if (!confirmYesButton) return;

  confirmYesButton.addEventListener("click", function () {
    const confirmModalElement = document.getElementById("confirmModal");
    const successModalElement = document.getElementById("successModal");

    if (confirmModalElement && successModalElement) {
      // Sembunyikan modal konfirmasi
      const confirmModalInstance =
        bootstrap.Modal.getInstance(confirmModalElement);
      if (confirmModalInstance) {
        confirmModalInstance.hide();
      }

      // Tampilkan modal sukses
      const successModalInstance = new bootstrap.Modal(successModalElement);
      successModalInstance.show();
    }
  });
}

/**
 * =================================================================
 * PENGELOLAAN LAB - SCRIPT UTAMA (Tambahan Validasi Form)
 * =================================================================
 * Mengintegrasikan semua logika validasi form dari halaman CRUD.
 */

// Letakkan ini di dalam event listener utama Anda di file main.js
document.addEventListener("DOMContentLoaded", function () {
  // Panggil fungsi-fungsi setup form validasi yang baru
  setupFormTambahBarang();
  setupFormEditBarang();
  setupFormTambahRuangan();
  setupFormEditRuangan();
  setupFormTambahAkunMhs();
  setupFormTambahAkunKry();
  setupFormEditAkunMhs();
  setupFormEditAkunKry();
  setupFormTambahPeminjamanBrg();
  setupFormTambahPeminjamanRuangan();

  // Tambahkan pemanggilan untuk form edit akun jika diperlukan

  // ... (panggil fungsi-fungsi lain yang sudah ada sebelumnya)
});

// =================================================================
// #1: HELPER UMUM UNTUK FORM
// =================================================================

/**
 * @function setupStockStepper
 * @description Menangani tombol +/- untuk input stok.
 * @param {string} inputId - ID dari input field untuk stok.
 */
function setupStockStepper(inputId) {
  const stokInput = document.getElementById(inputId);
  if (!stokInput) return;

  // Menggunakan event delegation untuk menangani klik pada tombol
  stokInput.parentElement.addEventListener("click", function (e) {
    const changeStok = (val) => {
      let current = parseInt(stokInput.value) || 0;
      let next = current + val;
      if (next < 0) next = 0;
      stokInput.value = next;
    };

    if (e.target.matches(".btn-increment")) {
      changeStok(1);
    }
    if (e.target.matches(".btn-decrement")) {
      changeStok(-1);
    }
  });
}

/**
 * @function setupKondisiRuanganLogic
 * @description Menangani logika dimana jika kondisi ruangan 'Rusak',
 * maka status ketersediaan otomatis menjadi 'Tidak Tersedia'.
 */
function setupKondisiRuanganLogic() {
  const kondisiSelect = document.getElementById("kondisiRuangan");
  const ketersediaanSelect = document.getElementById("ketersediaan");
  if (!kondisiSelect || !ketersediaanSelect) return;

  const updateKetersediaan = () => {
    if (kondisiSelect.value === "Rusak") {
      ketersediaanSelect.value = "Tidak Tersedia";
      ketersediaanSelect.disabled = true;
    } else {
      ketersediaanSelect.disabled = false;
    }
  };

  kondisiSelect.addEventListener("change", updateKetersediaan);
  updateKetersediaan(); // Panggil saat halaman dimuat untuk set state awal
}

// =================================================================
// #2: FUNGSI VALIDASI SPESIFIK UNTUK SETIAP FORM
// =================================================================
// PENTING: Pastikan setiap form di file PHP memiliki ID yang sesuai.

function setupFormTambahBarang() {
  const form = document.getElementById("formTambahBarang");
  if (!form) return;

  setupStockStepper("stokBarang"); // Inisialisasi stepper untuk stok

  form.addEventListener("submit", function (e) {
    e.preventDefault(); // Selalu cegah submit default dulu

    let isValid = true;

    // Validasi Nama Barang
    const namaInput = document.getElementById("namaBarang");
    const namaError = document.getElementById("namaError");
    if (namaInput.value.trim() === "") {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    } else {
      namaError.style.display = "none";
    }

    // Validasi Stok Barang
    const stokInput = document.getElementById("stokBarang");
    const stokError = document.getElementById("stokError");
    if (stokInput.value.trim() === "" || parseInt(stokInput.value) <= 0) {
      stokError.textContent = "*Stok tidak boleh kosong atau negatif";
      stokError.style.display = "inline";
      isValid = false;
    } else {
      stokError.style.display = "none";
    }

    // Validasi Lokasi
    const lokasiSelect = document.getElementById("lokasiBarang");
    const lokasiError = document.getElementById("lokasiError");
    if (!lokasiSelect.value || lokasiSelect.value === "Pilih Lokasi") {
      lokasiError.textContent = "*Harus diisi";
      lokasiError.style.display = "inline";
      isValid = false;
    } else {
      lokasiError.style.display = "none";
    }

    if (isValid) {
      // Jika semua valid, tampilkan modal konfirmasi
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent = "menambah barang"; // Pesan dinamis

      // Atur agar form di-submit HANYA jika "Ya" pada modal diklik
      document.getElementById("confirmYes").onclick = function () {
        form.submit();
      };

      confirmModal.show();
    }
  });
}

// Anda bisa membuat fungsi serupa untuk `setupFormEditBarang`
function setupFormEditBarang() {
  // Validasi untuk form edit barang (mirip tambahBarang, ID form berbeda)
  const form = document.getElementById("formEditBarang");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault(); // Cegah submit default dulu

    let isValid = true;

    // Validasi Nama Barang
    const namaInput = document.getElementById("namaBarang");
    const namaError = document.getElementById("namaError");
    if (namaInput.value.trim() === "") {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    } else {
      namaError.style.display = "none";
    }

    // Validasi Stok Barang
    const stokInput = document.getElementById("stokBarang");
    const stokError = document.getElementById("stokError");
    // Untuk edit, stok boleh 0 (tidak boleh kosong atau negatif)
    if (stokInput.value.trim() === "" || isNaN(stokInput.value) || parseInt(stokInput.value) < 0) {
      stokError.textContent = "*Stok tidak boleh kosong atau negatif";
      stokError.style.display = "inline";
      isValid = false;
    } else {
      stokError.style.display = "none";
    }

    // Validasi Lokasi
    const lokasiSelect = document.getElementById("lokasiBarang");
    const lokasiError = document.getElementById("lokasiError");
    if (!lokasiSelect.value || lokasiSelect.value === "Pilih Lokasi") {
      lokasiError.textContent = "*Harus diisi";
      lokasiError.style.display = "inline";
      isValid = false;
    } else {
      lokasiError.style.display = "none";
    }

    if (isValid) {
      // Tampilkan modal konfirmasi jika valid
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent = "mengubah barang"; // Pesan dinamis

      // Submit form hanya jika "Ya" pada modal diklik
      document.getElementById("confirmYes").onclick = function () {
        form.submit();
      };

      confirmModal.show();
    }
  });
}

function setupFormTambahRuangan() {
  const form = document.getElementById("formTambahRuangan");
  if (!form) return;

  setupKondisiRuanganLogic(); // Terapkan logika dropdown

  form.addEventListener("submit", function (e) {
    // Validasi form tambah ruangan
    let isValid = true;

    // Nama Ruangan
    const namaInput = document.getElementById("namaRuangan");
    const namaError = document.getElementById("namaError");
    if (!namaInput.value.trim()) {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    } else {
      namaError.style.display = "none";
    }

    // Kondisi Ruangan
    const kondisiSelect = document.getElementById("kondisiRuangan");
    const kondisiError = document.getElementById("kondisiError");
    if (!kondisiSelect.value || kondisiSelect.value === "Pilih Kondisi") {
      kondisiError.textContent = "*Harus diisi";
      kondisiError.style.display = "inline";
      isValid = false;
    } else {
      kondisiError.style.display = "none";
    }

    // Ketersediaan Ruangan
    const ketersediaanSelect = document.getElementById("ketersediaan");
    const ketersediaanError = document.getElementById("ketersediaanError");
    if (!ketersediaanSelect.value || ketersediaanSelect.value === "Pilih Ketersediaan") {
      ketersediaanError.textContent = "*Harus diisi";
      ketersediaanError.style.display = "inline";
      isValid = false;
    } else {
      ketersediaanError.style.display = "none";
    }

    if (!isValid) {
      e.preventDefault();
    }
  });
}

// Anda bisa membuat fungsi serupa untuk `setupFormEditRuangan`
function setupFormEditRuangan() {
  const form = document.getElementById("formEditRuangan");
  if (!form) return;
  // ...logika yang sama...
}

function setupFormTambahAkunMhs() {
  const form = document.getElementById("formTambahAkunMhs");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    let isValid = true;

    // NIM
    const nimInput = document.getElementById("nim");
    const nimError = document.getElementById("nimError");
    if (!nimInput.value.trim()) {
      nimError.textContent = "*Harus diisi";
      nimError.style.display = "inline";
      isValid = false;
    } else if (!/^\d{8,20}$/.test(nimInput.value.trim())) {
      nimError.textContent = "*NIM harus berupa angka (8-20 digit)";
      nimError.style.display = "inline";
      isValid = false;
    } else {
      nimError.style.display = "none";
    }

    // Nama
    const namaInput = document.getElementById("nama");
    const namaError = document.getElementById("namaError");
    if (!namaInput.value.trim()) {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    } else {
      namaError.style.display = "none";
    }

    // Email
    const emailInput = document.getElementById("email");
    const emailError = document.getElementById("emailError");
    if (!emailInput.value.trim()) {
      emailError.textContent = "*Harus diisi";
      emailError.style.display = "inline";
      isValid = false;
    } else if (
      !/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/.test(emailInput.value.trim())
    ) {
      emailError.textContent = "*Format email tidak valid";
      emailError.style.display = "inline";
      isValid = false;
    } else {
      emailError.style.display = "none";
    }

    // Role
    const roleSelect = document.getElementById("jenisRole");
    const roleError = document.getElementById("roleError");
    if (!roleSelect.value) {
      roleError.textContent = "*Harus diisi";
      roleError.style.display = "inline";
      isValid = false;
    } else {
      roleError.style.display = "none";
    }

    // Password
    const passInput = document.getElementById("kataSandi");
    const passError = document.getElementById("passError");
    if (!passInput.value) {
      passError.textContent = "*Harus diisi";
      passError.style.display = "inline";
      isValid = false;
    } else if (passInput.value.length < 6) {
      passError.textContent = "*Minimal 6 karakter";
      passError.style.display = "inline";
      isValid = false;
    } else {
      passError.style.display = "none";
    }

    // Konfirmasi Password
    const confPassInput = document.getElementById("konfirmasiSandi");
    const confPassError = document.getElementById("confPassError");
    if (!confPassInput.value) {
      confPassError.textContent = "*Harus diisi";
      confPassError.style.display = "inline";
      isValid = false;
    } else if (confPassInput.value !== passInput.value) {
      confPassError.textContent = "*Konfirmasi tidak cocok";
      confPassError.style.display = "inline";
      isValid = false;
    } else {
      confPassError.style.display = "none";
    }

    if (!isValid) {
      e.preventDefault();
    }
  });
}

function setupFormTambahAkunKry() {
  const form = document.getElementById("formTambahAkunKry");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    let isValid = true;

    // Validasi NPK
    const npkInput = document.getElementById("npk");
    const npkError = document.getElementById("npkError");
    if (!npkInput.value.trim()) {
      npkError.textContent = "*Harus diisi";
      npkError.style.display = "inline";
      isValid = false;
    } else {
      npkError.style.display = "none";
    }

    // Validasi Nama
    const namaInput = document.getElementById("nama");
    const namaError = document.getElementById("namaError");
    if (!namaInput.value.trim()) {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    } else {
      namaError.style.display = "none";
    }

    // Validasi Email
    const emailInput = document.getElementById("email");
    const emailError = document.getElementById("emailError");
    if (!emailInput.value.trim()) {
      emailError.textContent = "*Harus diisi";
      emailError.style.display = "inline";
      isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
      emailError.textContent = "*Format email tidak valid";
      emailError.style.display = "inline";
      isValid = false;
    } else {
      emailError.style.display = "none";
    }

    // Validasi Role
    const roleSelect = document.getElementById("jenisRole");
    const roleError = document.getElementById("roleError");
    if (!roleSelect.value) {
      roleError.textContent = "*Harus diisi";
      roleError.style.display = "inline";
      isValid = false;
    } else {
      roleError.style.display = "none";
    }

    // Validasi Password
    const passInput = document.getElementById("kataSandi");
    const passError = document.getElementById("passError");
    if (!passInput.value) {
      passError.textContent = "*Harus diisi";
      passError.style.display = "inline";
      isValid = false;
    } else if (passInput.value.length < 6) {
      passError.textContent = "*Minimal 6 karakter";
      passError.style.display = "inline";
      isValid = false;
    } else {
      passError.style.display = "none";
    }

    // Validasi Konfirmasi Password
    const confPassInput = document.getElementById("konfirmasiSandi");
    const confPassError = document.getElementById("confPassError");
    if (!confPassInput.value) {
      confPassError.textContent = "*Harus diisi";
      confPassError.style.display = "inline";
      isValid = false;
    } else if (confPassInput.value !== passInput.value) {
      confPassError.textContent = "*Konfirmasi tidak cocok";
      confPassError.style.display = "inline";
      isValid = false;
    } else {
      confPassError.style.display = "none";
    }

    if (!isValid) {
      e.preventDefault();
    }
  });
}

function setupFormEditAkunMhs() {
  const form = document.getElementById("formEditAkunMhs");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    let isValid = true;

    // NIM
    const nimInput = document.getElementById("nim");
    const nimError = document.getElementById("nimError");
    if (!nimInput.value.trim()) {
      nimError.textContent = "*Harus diisi";
      nimError.style.display = "inline";
      isValid = false;
    } else if (!/^\d{8,20}$/.test(nimInput.value.trim())) {
      nimError.textContent = "*NIM harus berupa angka (8-20 digit)";
      nimError.style.display = "inline";
      isValid = false;
    } else {
      nimError.style.display = "none";
    }

    // Nama
    const namaInput = document.getElementById("nama");
    const namaError = document.getElementById("namaError");
    if (!namaInput.value.trim()) {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    } else {
      namaError.style.display = "none";
    }

    // Email
    const emailInput = document.getElementById("email");
    const emailError = document.getElementById("emailError");
    if (!emailInput.value.trim()) {
      emailError.textContent = "*Harus diisi";
      emailError.style.display = "inline";
      isValid = false;
    } else if (
      !/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/.test(emailInput.value.trim())
    ) {
      emailError.textContent = "*Format email tidak valid";
      emailError.style.display = "inline";
      isValid = false;
    } else {
      emailError.style.display = "none";
    }

    // Role
    const roleSelect = document.getElementById("jenisRole");
    const roleError = document.getElementById("roleError");
    if (!roleSelect.value) {
      roleError.textContent = "*Harus diisi";
      roleError.style.display = "inline";
      isValid = false;
    } else {
      roleError.style.display = "none";
    }

    // Password (opsional pada edit, hanya validasi jika diisi)
    const passInput = document.getElementById("kataSandi");
    const passError = document.getElementById("passError");
    if (passInput.value) {
      if (passInput.value.length < 6) {
        passError.textContent = "*Minimal 6 karakter";
        passError.style.display = "inline";
        isValid = false;
      } else {
        passError.style.display = "none";
      }
    } else {
      passError.style.display = "none";
    }

    // Konfirmasi Password (hanya jika password diisi)
    const confPassInput = document.getElementById("konfirmasiSandi");
    const confPassError = document.getElementById("confPassError");
    if (passInput.value) {
      if (!confPassInput.value) {
        confPassError.textContent = "*Harus diisi";
        confPassError.style.display = "inline";
        isValid = false;
      } else if (confPassInput.value !== passInput.value) {
        confPassError.textContent = "*Konfirmasi tidak cocok";
        confPassError.style.display = "inline";
        isValid = false;
      } else {
        confPassError.style.display = "none";
      }
    } else {
      confPassError.style.display = "none";
    }

    if (!isValid) {
      e.preventDefault();
    }
  });
}

function setupFormEditAkunKry() {
  const form = document.getElementById("formEditAkunKry");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    let isValid = true;

    // NPK
    const npkInput = document.getElementById("npk");
    const npkError = document.getElementById("npkError");
    if (!npkInput.value.trim()) {
      npkError.textContent = "*Harus diisi";
      npkError.style.display = "inline";
      isValid = false;
    } else if (!/^\d{6,20}$/.test(npkInput.value.trim())) {
      npkError.textContent = "*NPK harus berupa angka (6-20 digit)";
      npkError.style.display = "inline";
      isValid = false;
    } else {
      npkError.style.display = "none";
    }

    // Nama
    const namaInput = document.getElementById("nama");
    const namaError = document.getElementById("namaError");
    if (!namaInput.value.trim()) {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    } else {
      namaError.style.display = "none";
    }

    // Email
    const emailInput = document.getElementById("email");
    const emailError = document.getElementById("emailError");
    if (!emailInput.value.trim()) {
      emailError.textContent = "*Harus diisi";
      emailError.style.display = "inline";
      isValid = false;
    } else if (
      !/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/.test(emailInput.value.trim())
    ) {
      emailError.textContent = "*Format email tidak valid";
      emailError.style.display = "inline";
      isValid = false;
    } else {
      emailError.style.display = "none";
    }

    // Role
    const roleSelect = document.getElementById("jenisRole");
    const roleError = document.getElementById("roleError");
    if (!roleSelect.value) {
      roleError.textContent = "*Harus diisi";
      roleError.style.display = "inline";
      isValid = false;
    } else {
      roleError.style.display = "none";
    }

    // Password (opsional pada edit, hanya validasi jika diisi)
    const passInput = document.getElementById("kataSandi");
    const passError = document.getElementById("passError");
    if (passInput.value) {
      if (passInput.value.length < 6) {
        passError.textContent = "*Minimal 6 karakter";
        passError.style.display = "inline";
        isValid = false;
      } else {
        passError.style.display = "none";
      }
    } else {
      passError.style.display = "none";
    }

    // Konfirmasi Password (hanya jika password diisi)
    const confPassInput = document.getElementById("konfirmasiSandi");
    const confPassError = document.getElementById("confPassError");
    if (passInput.value) {
      if (!confPassInput.value) {
        confPassError.textContent = "*Harus diisi";
        confPassError.style.display = "inline";
        isValid = false;
      } else if (confPassInput.value !== passInput.value) {
        confPassError.textContent = "*Konfirmasi tidak cocok";
        confPassError.style.display = "inline";
        isValid = false;
      } else {
        confPassError.style.display = "none";
      }
    } else {
      confPassError.style.display = "none";
    }

    if (!isValid) {
      e.preventDefault();
    }
  });
}

function setupFormTambahPeminjamanRuangan() {
  const form = document.getElementById("form-peminjaman");
  if (!form) return;

  form.addEventListener("submit", function (event) {
    let isValid = true;

    // Validasi tanggal
    const hari = document.getElementById('tglHari').value;
    const bulan = document.getElementById('tglBulan').value;
    const tahun = document.getElementById('tglTahun').value;
    const errorTanggal = document.getElementById('error-message');
    let pesan = '';
    if (!hari || !bulan || !tahun) {
      errorTanggal.textContent = "*Harus Diisi";
      errorTanggal.style.display = 'inline';
      isValid = false;
    } else {
      let inputDate = new Date(`${tahun}-${bulan.padStart(2, '0')}-${hari.padStart(2, '0')}`);
      let today = new Date();
      today.setHours(0, 0, 0, 0);
      if (inputDate < today) {
        errorTanggal.textContent = "*Input tanggal sudah lewat";
        errorTanggal.style.display = 'inline';
        isValid = false;
      } else {
        errorTanggal.style.display = 'none';
      }
    }

    // Validasi waktu mulai dan selesai
    const jamDari = document.getElementById('jam_dari').value;
    const menitDari = document.getElementById('menit_dari').value;
    const jamSampai = document.getElementById('jam_sampai').value;
    const menitSampai = document.getElementById('menit_sampai').value;
    const errorWaktu = document.getElementById('error-waktu');
    const errorWaktuMulai = document.getElementById('error-waktu-mulai');
    const errorWaktuSelesai = document.getElementById('error-waktu-selesai');

    let waktuValid = true;
    if (jamDari === "" || menitDari === "" || isNaN(parseInt(jamDari)) || isNaN(parseInt(menitDari))) {
      errorWaktuMulai.style.display = 'inline';
      waktuValid = false;
    } else {
      errorWaktuMulai.style.display = 'none';
    }

    if (jamSampai === "" || menitSampai === "" || isNaN(parseInt(jamSampai)) || isNaN(parseInt(menitSampai))) {
      errorWaktuSelesai.style.display = 'inline';
      waktuValid = false;
    } else {
      errorWaktuSelesai.style.display = 'none';
    }

    if (waktuValid) {
      const startMinutes = parseInt(jamDari) * 60 + parseInt(menitDari);
      const endMinutes = parseInt(jamSampai) * 60 + parseInt(menitSampai);
      const selectedDate = new Date(`${tahun}-${bulan.padStart(2, '0')}-${hari.padStart(2, '0')}`);
      const now = new Date();
      const nowMinutes = now.getHours() * 60 + now.getMinutes();

      if (endMinutes <= startMinutes) {
        errorWaktu.textContent = '*Waktu selesai harus lebih besar dari waktu mulai';
        errorWaktu.style.display = 'block';
        isValid = false;
      } else if (selectedDate.toDateString() === now.toDateString() && startMinutes < nowMinutes) {
        errorWaktu.textContent = '*Waktu mulai tidak boleh lebih kecil dari waktu sekarang';
        errorWaktu.style.display = 'block';
        isValid = false;
      } else {
        errorWaktu.style.display = 'none';
      }
    } else {
      errorWaktu.style.display = 'none';
      isValid = false;
    }

    // Validasi alasan peminjaman ruangan
    const alasanInput = document.getElementById("alasanPeminjamanRuangan");
    const alasanError = document.getElementById("error-message");
    if (!alasanInput.value.trim()) {
      alasanError.textContent = "*Harus Diisi";
      alasanError.style.display = "inline";
      isValid = false;
    } else {
      alasanError.style.display = "none";
    }

    if (!isValid) {
      event.preventDefault();
      return;
    }

    // Set input tersembunyi kalau semua valid
    document.getElementById('tglPeminjamanRuangan').value = `${hari}-${bulan}-${tahun}`;
  });
}

function setupFormTambahPeminjamanBrg() {
  const form = document.getElementById("form-peminjaman-barang");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    let isValid = true;

    // Validasi jumlah barang > 0
    const jumlahInput = document.getElementById("jumlahBrg");
    const jumlahError = document.getElementById("jumlahError");
    if (!jumlahInput || parseInt(jumlahInput.value, 10) <= 0) {
      if (jumlahError) {
        jumlahError.style.display = "inline";
      }
      isValid = false;
    } else {
      if (jumlahError) {
        jumlahError.style.display = "none";
      }
    }

    // Validasi alasan peminjaman barang
    const alasanInput = document.getElementById("alasanPeminjamanBrg");
    const alasanError = document.getElementById("alasanError");
    if (!alasanInput || !alasanInput.value.trim()) {
      if (alasanError) {
        alasanError.style.display = "inline";
      }
      isValid = false;
    } else {
      if (alasanError) {
        alasanError.style.display = "none";
      }
    }

    if (!isValid) {
      e.preventDefault();
    }
  });

  // Stepper tombol + dan - untuk jumlah barang
  window.changeStok = function (delta) {
    const jumlahInput = document.getElementById("jumlahBrg");
    const stokTersedia = parseInt(document.getElementById("stokTersedia")?.value || "0", 10);
    let val = parseInt(jumlahInput.value, 10) || 0;
    val += delta;
    if (val < 0) val = 0;
    if (stokTersedia && val > stokTersedia) val = stokTersedia;
    jumlahInput.value = val;
    // Trigger validasi ulang
    jumlahInput.dispatchEvent(new Event("input"));
  };
}


