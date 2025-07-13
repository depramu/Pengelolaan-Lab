/**
 * =================================================================
 * PENGELOLAAN LAB - SCRIPT UTAMA (Versi Final Gabungan)
 * Dibuat oleh: Partner Koding
 * Deskripsi: Menggabungkan semua fungsionalitas JavaScript untuk
 * halaman login, laporan, peminjaman, CRUD, dan lainnya
 * ke dalam satu file yang terstruktur.
 * =================================================================
 */

// =================================================================
// #0: HELPER & FUNGSI UMUM
// =================================================================

/**
 * @object dateTimeHelpers
 * @description Kumpulan fungsi untuk mengelola input tanggal dan waktu.
 */
const dateTimeHelpers = {
  isLeapYear: function (year) {
    return (year % 4 === 0 && year % 100 !== 0) || year % 400 === 0;
  },

  updateDays: function (dayId, monthId, yearId) {
    const bulan = parseInt(document.getElementById(monthId).value);
    const tahun = parseInt(document.getElementById(yearId).value);
    const hariSelect = document.getElementById(dayId);
    if (!hariSelect || isNaN(bulan) || isNaN(tahun)) return;

    const prevHari = hariSelect.value;
    let days = 31;
    if ([4, 6, 9, 11].includes(bulan)) days = 30;
    else if (bulan === 2) days = this.isLeapYear(tahun) ? 29 : 28;

    hariSelect.innerHTML = "";
    for (let i = 1; i <= days; i++) {
      hariSelect.innerHTML += `<option value="${String(i).padStart(
        2,
        "0"
      )}">${i}</option>`;
    }

    if (prevHari && parseInt(prevHari) <= days) {
      hariSelect.value = String(prevHari).padStart(2, "0");
    }
  },

  fillSelects: function (dayId, monthId, yearId) {
    const tahunSelect = document.getElementById(yearId);
    const bulanSelect = document.getElementById(monthId);
    const hariSelect = document.getElementById(dayId);
    if (!tahunSelect || !bulanSelect || !hariSelect) return;

    const now = new Date();
    tahunSelect.innerHTML = "";
    for (let y = now.getFullYear(); y <= now.getFullYear() + 1; y++) {
      tahunSelect.innerHTML += `<option value="${y}">${y}</option>`;
    }

    bulanSelect.innerHTML = "";
    for (let m = 1; m <= 12; m++) {
      bulanSelect.innerHTML += `<option value="${m}">${String(m).padStart(
        2,
        "0"
      )}</option>`;
    }

    // Set default value
    tahunSelect.value = now.getFullYear();
    bulanSelect.value = now.getMonth() + 1;
    this.updateDays(dayId, monthId, yearId);
    hariSelect.value = String(now.getDate()).padStart(2, "0");

    // Tambahkan event listener
    bulanSelect.addEventListener("change", () =>
      this.updateDays(dayId, monthId, yearId)
    );
    tahunSelect.addEventListener("change", () =>
      this.updateDays(dayId, monthId, yearId)
    );
  },

  fillTimeSelects: function (hourId, minuteId) {
    const fill = (elId, max) => {
      const el = document.getElementById(elId);
      if (!el) return;
      el.innerHTML = '<option value="">--</option>';
      for (let i = 0; i < max; i++) {
        const val = String(i).padStart(2, "0");
        el.innerHTML += `<option value="${val}">${val}</option>`;
      }
    };
    fill(hourId, 24);
    fill(minuteId, 60);
  },
};

/**
 * @function setupStockStepper
 * @description Menangani tombol +/- untuk input numerik.
 * @param {string} containerId - ID dari container yang membungkus input dan tombol.
 * @param {string} inputId - ID dari input field.
 * @param {string} maxLimitId - (Opsional) ID dari elemen yang menyimpan nilai batas maksimal.
 */
function setupStockStepper(containerId, inputId, maxLimitId = null) {
  // Versi sederhana: gunakan fungsi global changeStok(val) saja
  // Tidak perlu event delegation, cukup panggil dari onclick di HTML
  // Fungsi ini tetap ada agar tidak error jika dipanggil dari form lain
  window.changeStok = function (val) {
    const stokInput = document.getElementById(inputId);
    if (!stokInput) return;
    let current = parseInt(stokInput.value) || 0;
    let next = current + val;
    if (next < 0) next = 0;
    stokInput.value = next;
  };
}

/**
 * @function setupKondisiRuanganLogic
 * @description Jika kondisi ruangan 'Rusak', ketersediaan otomatis menjadi 'Tidak Tersedia'.
 */
function setupKondisiRuanganLogic() {
  document
    .getElementById("kondisiRuangan")
    .addEventListener("change", function () {
      const kondisi = this.value;
      const ketersediaan = document.getElementById("ketersediaan");

      if (kondisi === "Rusak") {
        ketersediaan.value = "Tidak Tersedia";
        ketersediaan.disabled = true;
      } else {
        ketersediaan.disabled = false;
      }
    });
}

// =================================================================
// #1: INISIALISASI UTAMA SAAT HALAMAN DIMUAT
// =================================================================

document.addEventListener("DOMContentLoaded", function () {
  /**
   * Panggil semua fungsi setup.
   * Setiap fungsi akan memeriksa keberadaan elemennya sendiri sebelum berjalan.
   */

  // Halaman Otentikasi
  setupLoginForm();
  setupLupaSandiForm();

  // Halaman Admin & Operator
  setupLaporanPage();
  setupPenolakanBarang();
  setupPengembalianBarangPage();
  setupPengembalianRuanganPage();
  setupDetailRiwayatForm();

  // Form CRUD
  setupFormTambahBarang();
  setupFormEditBarang();
  setupFormTambahRuangan();
  setupFormEditRuangan();
  setupFormTambahAkunMhs();
  setupFormEditAkunMhs();
  setupFormTambahAkunKry();
  setupFormEditAkunKry();

  // Form Peminjaman & Cek Ketersediaan
  setupCekKetersediaanBarangPage();
  setupCekKetersediaanRuanganPage();
  setupFormTambahPeminjamanBrg();
  setupFormTambahPeminjamanRuangan();

  // Fitur Tambahan
  setupSidebarPersistence();
  setupInputProtection();
  setupModalChaining();
  setupSuccessModalFromPHP();

  // Setup Profil
  setupProfil();
});

function setupProfil() {
  const profilForm = document.getElementById("profilForm");
  if (!profilForm) return;

  // Hanya validasi passError
  const passInput = profilForm.querySelector("[name='kataSandi']");
  const passError = profilForm.document.getElementById("kataSandiError");

  profilForm.addEventListener("submit", function (e) {
    let isValid = true;

    // Reset pesan error
    if (passError) passError.textContent = "";

    // Validasi password
    if (passInput && !passInput.value.trim()) {
      if (passError) passError.textContent = "*Kata Sandi tidak boleh kosong.";
      isValid = false;
    }

    if (!isValid) {
      e.preventDefault();
      return;
    }

    // Tampilkan confirm modal
    const confirmModal = new bootstrap.Modal(
      document.getElementById("confirmModal")
    );
    const confirmMessage = document.getElementById("confirmMessage");
    if (confirmMessage) {
      confirmMessage.textContent =
        "Apakah Anda yakin ingin mengubah kata sandi?";
    }

    confirmModal.show();
  });
}

function setupSuccessModalFromPHP() {
  const successModalElement = document.getElementById("successModal");
  if (
    typeof showSuccessModalOnLoad !== "undefined" &&
    showSuccessModalOnLoad &&
    successModalElement
  ) {
    new bootstrap.Modal(successModalElement).show();
  }
}

// =================================================================
// #2: HALAMAN OTENTIKASI (LOGIN & LUPA SANDI)
// =================================================================

function setupLoginForm() {
  const loginForm = document.getElementById("loginForm");
  if (!loginForm) return;

  // Pastikan error span selalu terlihat (jika ada error)
  const idError = document.getElementById("identifier-error");
  const passError = document.getElementById("password-error");

  loginForm.addEventListener("submit", function (e) {
    const idInput = document.getElementById("identifier");
    const passInput = document.getElementById("kataSandi");
    let isValid = true;

    // Reset pesan error
    if (idError) idError.textContent = "";
    if (passError) passError.textContent = "";

    // Validasi identifier
    if (!idInput.value.trim()) {
      if (idError) idError.textContent = "*NIM/NPK tidak boleh kosong.";
      isValid = false;
    } else if (!/^\d+$/.test(idInput.value.trim())) {
      if (idError) idError.textContent = "*NIM/NPK harus berupa angka.";
      isValid = false;
    }

    // Validasi password
    if (!passInput.value.trim()) {
      if (passError) passError.textContent = "*Kata Sandi tidak boleh kosong.";
      isValid = false;
    }

    // Jika tidak valid, cegah submit dan pastikan error terlihat
    if (!isValid) {
      e.preventDefault();
      if (idError) idError.style.display = "inline";
      if (passError) passError.style.display = "inline";
    }
  });

  // Tampilkan error dari server jika ada
  const serverError = document.getElementById("server-error");
  if (serverError && serverError.textContent.trim() !== "") {
    const errorMessage = serverError.textContent.trim().toLowerCase();
    serverError.classList.add("d-none");

    if (idError) idError.textContent = "";
    if (passError) passError.textContent = "";

    if (
      errorMessage.includes("akun tidak terdafrar") ||
      errorMessage.includes("akun_tidak_terdaftar")
    ) {
      if (idError) idError.textContent = "*Akun tidak terdaftar*";
    } else if (errorMessage.includes("kata_sandi_salah")) {
      if (passError) passError.textContent = "*Kata sandi salah*";
    } else {
      if (idError) idError.textContent = serverError.textContent.trim();
    }
    if (idError) idError.style.display = "inline";
    if (passError) passError.style.display = "inline";
  }
}

function setupLupaSandiForm() {
  const lupaSandiForm = document.getElementById("lupaSandiForm"); // Gunakan ID spesifik
  if (!lupaSandiForm) return;

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
// #3: HALAMAN LAPORAN
// =================================================================

function setupLaporanPage() {
  const btnTampilkan = document.getElementById("tampilkanLaporanBtn");
  if (!btnTampilkan) return;

  btnTampilkan.addEventListener("click", () => {
    const jenisLaporanSelect = document.getElementById("jenisLaporan");
    const areaKontenDiv = document.getElementById("areaKontenLaporan");
    const wadahLaporanDiv = document.getElementById("wadahLaporan");
    const validationModalEl = document.getElementById("validationModal");
    const validationModal = validationModalEl
      ? new bootstrap.Modal(validationModalEl)
      : null;
    const validationMsg = document.getElementById("validationMessage");
    const bottomControlsContainer = document.getElementById(
      "bottomControlsContainer"
    );
    const laporanSummaryText = document.getElementById("laporanSummaryText");

    const type = jenisLaporanSelect.value;
    const bln = document.getElementById("bulanLaporan").value;
    const thn = document.getElementById("tahunLaporan").value;

    // Validasi filter
    if (
      !type ||
      (type !== "dataBarang" && type !== "dataRuangan" && (!bln || !thn))
    ) {
      validationMsg.textContent = "Silakan lengkapi filter yang diperlukan.";
      if (validationModal) validationModal.show();
      return;
    }

    // UI Loading state
    wadahLaporanDiv.innerHTML =
      '<p class="text-center py-5"><span class="spinner-border spinner-border-sm"></span> Memuat data...</p>';
    areaKontenDiv.style.display = "block";
    bottomControlsContainer.style.display = "none";
    laporanSummaryText.innerHTML = "";

    // Fetch data
    let url = `../../CRUD/Laporan/get_laporan_data.php?jenisLaporan=${type}`;
    if (type !== "dataBarang" && type !== "dataRuangan") {
      url += `&bulan=${bln}&tahun=${thn}`;
    }

    fetch(url)
      .then((res) => {
        if (!res.ok)
          throw new Error(res.statusText || "Gagal terhubung ke server");
        return res.json();
      })
      .then((res) => {
        if (res.status === "success") {
          const fullData = res.data || [];
          if (fullData.length > 0) {
            areaKontenDiv.style.display = "block";
            bottomControlsContainer.style.display = "flex";
            updateLaporanSummary(fullData, type);
            renderLaporanTable(fullData, type);
          } else {
            areaKontenDiv.style.display = "none";
            validationMsg.textContent =
              "Tidak Ada Data Laporan untuk periode yang dipilih.";
            if (validationModal) validationModal.show();
          }
        } else {
          throw new Error(res.message || "Gagal memuat data dari server.");
        }
      })
      .catch((err) => {
        wadahLaporanDiv.innerHTML = `<p class="text-danger text-center"><strong>Kesalahan:</strong> ${err.message}</p>`;
        areaKontenDiv.style.display = "block";
        bottomControlsContainer.style.display = "none";
      });
  });

  // Fungsi untuk menyesuaikan tampilan filter
  function adjustFilters() {
    const jenisLaporanSelect = document.getElementById("jenisLaporan");
    const bulanSelect = document.getElementById("bulanLaporan");
    const tahunSelect = document.getElementById("tahunLaporan");
    const colBulan = document.getElementById("colBulan");
    const colTahun = document.getElementById("colTahun");
    const colJenis = document.getElementById("colJenis");

    const val = jenisLaporanSelect.value;
    if (val === "dataBarang" || val === "dataRuangan") {
      colBulan.style.display = "none";
      colTahun.style.display = "none";
      colJenis.className = "col-md-10";
      if (bulanSelect) bulanSelect.value = "";
      if (tahunSelect) tahunSelect.value = "";
    } else {
      colBulan.style.display = "block";
      colTahun.style.display = "block";
      colJenis.className = "col-md-4";
    }
  }

  // Event listener untuk perubahan jenis laporan
  const jenisLaporanSelect = document.getElementById("jenisLaporan");
  if (jenisLaporanSelect) {
    jenisLaporanSelect.addEventListener("change", adjustFilters);
    adjustFilters(); // Panggil sekali untuk inisialisasi
  }

  // Fungsi untuk update summary laporan
  function updateLaporanSummary(fullData, reportType) {
    const laporanSummaryText = document.getElementById("laporanSummaryText");
    if (!laporanSummaryText || fullData.length === 0) return;

    let summaryText = "";
    switch (reportType) {
      case "dataBarang":
        const totalJenisBarang = fullData.length;
        const totalStokBarang = fullData.reduce(
          (sum, item) => sum + parseInt(item.stokBarang || 0),
          0
        );
        summaryText = `<strong>Total Jenis Barang:</strong> ${totalJenisBarang}, <strong>Total Stok Barang:</strong> ${totalStokBarang}`;
        break;
      case "dataRuangan":
        const totalJenisRuangan = fullData.length;
        const totalRuanganTersedia = fullData.filter(
          (item) =>
            item.ketersediaan && item.ketersediaan.toLowerCase() === "tersedia"
        ).length;
        summaryText = `<strong>Total Jenis Ruangan:</strong> ${totalJenisRuangan}, <strong>Total Ruangan yang Tersedia:</strong> ${totalRuanganTersedia}`;
        break;
      case "peminjamSeringMeminjam":
        const totalPeminjaman = fullData.reduce(
          (sum, item) => sum + parseInt(item.JumlahPeminjaman || 0),
          0
        );
        summaryText = `<strong>Total Peminjam yang Sering Pinjam:</strong> ${totalPeminjaman}`;
        break;
      case "barangSeringDipinjam":
        const totalKuantitasBarang = fullData.reduce(
          (sum, item) => sum + parseInt(item.TotalKuantitasDipinjam || 0),
          0
        );
        summaryText = `<strong>Total Barang yang Dipinjam:</strong> ${totalKuantitasBarang}`;
        break;
      case "ruanganSeringDipinjam":
        const totalRuanganDipinjam = fullData.reduce(
          (sum, item) => sum + parseInt(item.JumlahDipinjam || 0),
          0
        );
        summaryText = `<strong>Total Ruangan yang Dipinjam:</strong> ${totalRuanganDipinjam}`;
        break;
    }
    laporanSummaryText.innerHTML = summaryText;
  }

  /**
   * Fungsi untuk mengekspor data ke file Excel (.xlsx) dengan judul dan summary.
   * @param {string} filename - Nama file yang akan diunduh.
   * @param {string} title - Judul utama laporan.
   * @param {string} summary - Teks ringkasan laporan.
   * @param {Array<string>} headers - Array header tabel.
   * @param {Array<Object>} data - Array objek data.
   * @param {Array<string>} keys - Array kunci objek yang sesuai dengan header.
   */
  function exportToExcel(filename, title, summary, headers, data, keys) {
    // 1. Siapkan data untuk worksheet
    const sheetData = [];

    // Baris Judul
    sheetData.push([title]);
    // Baris Summary
    sheetData.push([summary]);
    // Baris Kosong
    sheetData.push([]);
    // Baris Header Tabel
    sheetData.push(headers);

    // Baris Data
    data.forEach((item) => {
      const row = keys.map((key) => item[key] ?? "");
      sheetData.push(row);
    });

    // 2. Buat worksheet dari array data
    const ws = XLSX.utils.aoa_to_sheet(sheetData);

    // 3. Atur format
    // Merge sel untuk judul dan summary
    const merge = [
      { s: { r: 0, c: 0 }, e: { r: 0, c: headers.length - 1 } }, // Merge Judul
      { s: { r: 1, c: 0 }, e: { r: 1, c: headers.length - 1 } }, // Merge Summary
    ];
    ws["!merges"] = merge;

    // Atur style (Bold untuk judul dan header)
    const boldStyle = { font: { bold: true } };
    ws["A1"].s = boldStyle; // Judul
    // Style untuk header
    for (let i = 0; i < headers.length; i++) {
      const cellRef = XLSX.utils.encode_cell({ c: i, r: 3 }); // Baris header ada di row 3 (0-indexed)
      if (ws[cellRef]) ws[cellRef].s = boldStyle;
    }

    // Atur lebar kolom secara otomatis
    const colWidths = headers.map((_, i) => {
      const key = keys[i];
      const maxLength = Math.max(
        headers[i]?.length || 0,
        ...data.map((item) => String(item[key] ?? "").length)
      );
      return { wch: maxLength + 2 }; // +2 untuk padding
    });
    ws["!cols"] = colWidths;

    // 4. Buat workbook dan tambahkan worksheet
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Laporan");

    // 5. Trigger download file
    XLSX.writeFile(wb, filename);
  }

  // Fungsi untuk render tabel laporan
  function renderLaporanTable(fullData, reportType) {
    const wadahLaporanDiv = document.getElementById("wadahLaporan");
    if (!wadahLaporanDiv) return;

    // Bagian rendering tabel di halaman utama tidak berubah
    const tbl = document.createElement("table");
    tbl.className = "table table-striped table-bordered table-hover";
    let headers = [], keys = [];
    switch (reportType) {
        case "dataBarang": headers = ["ID", "Nama", "Stok", "Lokasi"]; keys = ["idBarang", "namaBarang", "stokBarang", "lokasiBarang"]; break;
        case "dataRuangan": headers = ["ID", "Nama", "Kondisi", "Ketersediaan"]; keys = ["idRuangan", "namaRuangan", "kondisiRuangan", "ketersediaan"]; break;
        case "peminjamSeringMeminjam": headers = ["ID Peminjam", "Nama", "Jenis", "Jumlah"]; keys = ["IDPeminjam", "NamaPeminjam", "JenisPeminjam", "JumlahPeminjaman"]; break;
        case "barangSeringDipinjam": headers = ["ID Barang", "Nama", "Total Dipinjam"]; keys = ["idBarang", "namaBarang", "TotalKuantitasDipinjam"]; break;
        case "ruanganSeringDipinjam": headers = ["ID Ruangan", "Nama", "Jumlah Dipinjam"]; keys = ["idRuangan", "namaRuangan", "JumlahDipinjam"]; break;
    }
    const thead = tbl.createTHead().insertRow();
    headers.forEach((h) => (thead.insertCell().textContent = h));
    const tbody = tbl.createTBody();
    fullData.forEach((item) => {
        const r = tbody.insertRow();
        keys.forEach((k) => (r.insertCell().textContent = item[k] ?? ""));
    });
    wadahLaporanDiv.innerHTML = "";
    wadahLaporanDiv.append(tbl);

    // Setup tombol Export ke Excel
    const exportBtn = document.getElementById("exportExcelBtn");
    if (exportBtn) {
        exportBtn.style.display = "block";
        const newExportBtn = exportBtn.cloneNode(true);
        exportBtn.parentNode.replaceChild(newExportBtn, exportBtn);

        newExportBtn.addEventListener("click", () => {
            const bln = document.getElementById("bulanLaporan").value;
            const thn = document.getElementById("tahunLaporan").value;
            
            // Tentukan script mana yang akan digunakan berdasarkan path URL
            let scriptName = 'export_laporan_excel_pic.php';
            if (window.location.pathname.includes('/Menu Ka UPT/')) {
                scriptName = 'export_laporan_excel_kaupt.php';
            }
            
            // ================================================ //
            // ========= PERUBAHAN UTAMA UNTUK PREVIEW ======== //
            // ================================================ //

            // Buat URL ke script PHP, TANPA &mode=download
            // Ini akan secara default membuka mode preview
            let previewUrl = `../../CRUD/Laporan/${scriptName}?jenisLaporan=${reportType}`;

            if (bln && thn) {
                previewUrl += `&bulan=${bln}&tahun=${thn}`;
            }

            // Buka URL preview di tab baru
            window.open(previewUrl, '_blank');
        });
      }
  }
}


// =================================================================
// #4: HALAMAN CEK KETERSEDIAAN (BARANG & RUANGAN)
// =================================================================

function setupCekKetersediaanBarangPage() {
  const form = document.getElementById("formCekKetersediaanBarang");
  const flatpickrInput = document.getElementById("tglPeminjamanFlat");
  const hiddenInput = document.getElementById("tglPeminjamanBrg");
  const errorMessage = document.getElementById("error-message");

  if (!form || !flatpickrInput || !hiddenInput) return;

  // Ambil nilai dari hidden input (yang isinya format: dd-mm-yyyy)
  const hiddenVal = hiddenInput.value;
  let defaultFlatpickrDate = null;

  if (hiddenVal && hiddenVal.includes("-")) {
    const [day, month, year] = hiddenVal.split("-");
    defaultFlatpickrDate = new Date(
      parseInt(year),
      parseInt(month) - 1,
      parseInt(day)
    );
  }

  flatpickr(flatpickrInput, {
    dateFormat: "d F Y",
    defaultDate: defaultFlatpickrDate,
    allowInput: true,
    onChange: function (selectedDates, dateStr, instance) {
      const selectedDate = selectedDates[0];
      if (selectedDate) {
        const formatted = `${String(selectedDate.getDate()).padStart(
          2,
          "0"
        )}-${String(selectedDate.getMonth() + 1).padStart(
          2,
          "0"
        )}-${selectedDate.getFullYear()}`;
        hiddenInput.value = formatted;
      }
    },
    onReady: function (selectedDates, dateStr, instance) {
      const selectedDate = selectedDates[0];
      if (selectedDate) {
        const formatted = `${String(selectedDate.getDate()).padStart(
          2,
          "0"
        )}-${String(selectedDate.getMonth() + 1).padStart(
          2,
          "0"
        )}-${selectedDate.getFullYear()}`;
        hiddenInput.value = formatted;
      }
    },
  });

  form.addEventListener("submit", function (e) {
    let isValid = true;

    const inputVal = hiddenInput.value;
    if (!inputVal) {
      isValid = false;
      errorMessage.textContent = "*Harus diisi";
    } else {
      const [day, month, year] = inputVal.split("-");
      const inputDate = new Date(year, month - 1, day);
      const today = new Date();
      today.setHours(0, 0, 0, 0);

      if (inputDate < today) {
        isValid = false;
        errorMessage.textContent = "*Input tanggal sudah lewat";
      }
    }

    if (!isValid) {
      errorMessage.style.display = "inline";
      e.preventDefault();
    } else {
      errorMessage.style.display = "none";
    }
  });
}

document.addEventListener("DOMContentLoaded", setupCekKetersediaanBarangPage);

function setupCekKetersediaanRuanganPage() {
  const form = document.getElementById("formCekKetersediaanRuangan");
  if (!form) return;

  // Inisialisasi date & time picker
  dateTimeHelpers.fillSelects("tglHari", "tglBulan", "tglTahun");
  dateTimeHelpers.fillTimeSelects("jam_dari", "menit_dari");
  dateTimeHelpers.fillTimeSelects("jam_sampai", "menit_sampai");

  form.addEventListener("submit", function (e) {
    const hari = document.getElementById("tglHari").value;
    const bulan = document.getElementById("tglBulan").value;
    const tahun = document.getElementById("tglTahun").value;
    const jamDari = document.getElementById("jam_dari").value;
    const menitDari = document.getElementById("menit_dari").value;
    const jamSampai = document.getElementById("jam_sampai").value;
    const menitSampai = document.getElementById("menit_sampai").value;

    const errorMsg = document.getElementById("error-message");
    const errorWaktu = document.getElementById("error-waktu");
    const errorWaktuMulai = document.getElementById("error-waktu-mulai");
    const errorWaktuSelesai = document.getElementById("error-waktu-selesai");

    let isValid = true;

    // Validasi Tanggal
    if (!hari || !bulan || !tahun) {
      errorMsg.textContent = "*Harus Diisi";
      isValid = false;
    } else {
      const inputDate = new Date(
        `${tahun}-${String(bulan).padStart(2, "0")}-${String(hari).padStart(
          2,
          "0"
        )}`
      );
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      if (inputDate < today) {
        errorMsg.textContent = "*Input tanggal sudah lewat";
        isValid = false;
      }
    }
    errorMsg.style.display = isValid ? "none" : "inline";
    if (!isValid) e.preventDefault();

    // Validasi Waktu
    let isTimeValid = true;
    let isStartTimeFilled = jamDari !== "" && menitDari !== "";
    let isEndTimeFilled = jamSampai !== "" && menitSampai !== "";

    errorWaktuMulai.style.display = isStartTimeFilled ? "none" : "inline";
    errorWaktuSelesai.style.display = isEndTimeFilled ? "none" : "inline";

    if (!isStartTimeFilled || !isEndTimeFilled) {
      isTimeValid = false;
    } else {
      const startMinutes = parseInt(jamDari) * 60 + parseInt(menitDari);
      const endMinutes = parseInt(jamSampai) * 60 + parseInt(menitSampai);
      const selectedDate = new Date(
        `${tahun}-${String(bulan).padStart(2, "0")}-${String(hari).padStart(
          2,
          "0"
        )}`
      );
      const now = new Date();

      if (endMinutes <= startMinutes) {
        errorWaktu.textContent =
          "*Waktu selesai harus lebih besar dari waktu mulai";
        isTimeValid = false;
      } else if (
        selectedDate.toDateString() === now.toDateString() &&
        startMinutes < now.getHours() * 60 + now.getMinutes()
      ) {
        errorWaktu.textContent =
          "*Waktu mulai tidak boleh lebih kecil dari waktu sekarang";
        isTimeValid = false;
      }
    }

    errorWaktu.style.display = isTimeValid ? "none" : "block";
    if (!isTimeValid) {
      isValid = false;
      e.preventDefault();
    }

    // Jika semua valid, set hidden input
    if (isValid) {
      const tglPeminjamanInput = document.getElementById(
        "tglPeminjamanRuangan"
      );
      if (tglPeminjamanInput) {
        tglPeminjamanInput.value = `${String(hari).padStart(2, "0")}-${String(
          bulan
        ).padStart(2, "0")}-${tahun}`;
      }
    }
  });
}

// =================================================================
// #5: HALAMAN LAINNYA (DETAIL, PENGEMBALIAN, PENGAJUAN)
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

function setupPenolakanBarang() {
  // Cek kedua kemungkinan id form
  const form =
    document.getElementById("formPenolakanBarang") ||
    document.getElementById("formPenolakanRuangan");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent default submission untuk menampilkan confirm modal

    let isValid = true;
    const alasanInput = document.getElementById("alasanPenolakan");
    const alasanError = document.getElementById("alasanPenolakanError");

    // Selalu tampilkan error span, kosongkan dulu
    if (alasanError) {
      alasanError.textContent = "";
      alasanError.style.display = "inline";
    }

    if (!alasanInput || !alasanInput.value.trim()) {
      if (alasanError) {
        alasanError.textContent = "*Harus diisi";
        alasanError.style.display = "inline";
      }
      isValid = false;
    } else {
      if (alasanError) {
        alasanError.textContent = "";
        alasanError.style.display = "none";
      }
    }

    if (!isValid) {
      return; // Stop jika validasi gagal
    }

    // Show confirmation modal
    const confirmModal = new bootstrap.Modal(
      document.getElementById("confirmModal")
    );
    const confirmAction = document.getElementById("confirmAction");
    const confirmYes = document.getElementById("confirmYes");

    confirmAction.textContent = "menolak peminjaman ini";

    confirmYes.onclick = function () {
      confirmModal.hide();
      form.submit(); // Submit form after confirmation
    };

    confirmModal.show();
  });
}

function setupPengembalianBarangPage() {
  const form = document.getElementById("formPengembalianBarang");
  if (!form) return;

  // Gunakan helper stepper yang sudah dibuat
  setupStockStepper("stepperContainer", "jumlahPengembalian", "sisaPinjaman");

  const jumlahInput = document.getElementById("jumlahPengembalian");
  const jumlahError = document.getElementById("jumlahError");
  const sisaPinjaman = parseInt(
    document.getElementById("sisaPinjaman")?.value || "0",
    10
  );

  const kondisiSelect = document.getElementById("txtKondisi");
  const kondisiError = document.getElementById("kondisiError");

  const catatanInput = document.getElementById("catatanPengembalianBarang");
  const catatanError = document.getElementById("catatanError");

  // Validasi live: jumlahPengembalian
  jumlahInput.addEventListener("input", function () {
    const nilai = parseInt(jumlahInput.value, 10);
    if (!jumlahInput.value || nilai <= 0) {
      jumlahError.textContent = "*Jumlah harus lebih dari 0.";
    } else if (nilai > sisaPinjaman) {
      jumlahError.textContent = "*Melebihi sisa pinjaman.";
    } else {
      jumlahError.textContent = "";
    }
    jumlahError.style.display =
      jumlahError.textContent !== "" ? "inline" : "none";
  });

  // Validasi live: kondisi barang
  kondisiSelect.addEventListener("change", function () {
    if (
      !kondisiSelect.value ||
      kondisiSelect.value === "Pilih Kondisi Barang"
    ) {
      kondisiError.textContent = "*Harus Dipilih";
    } else {
      kondisiError.textContent = "";
    }
    kondisiError.style.display =
      kondisiError.textContent !== "" ? "inline" : "none";
  });

  // Validasi live: catatan
  catatanInput.addEventListener("input", function () {
    if (!catatanInput.value.trim()) {
      catatanError.textContent = "*Harus Diisi";
    } else {
      catatanError.textContent = "";
    }
    catatanError.style.display =
      catatanError.textContent !== "" ? "inline" : "none";
  });

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    let isValid = true;

    if (!jumlahInput.value || parseInt(jumlahInput.value, 10) <= 0) {
      jumlahError.textContent = "*Jumlah harus lebih dari 0.";
      isValid = false;
    } else if (parseInt(jumlahInput.value, 10) > sisaPinjaman) {
      jumlahError.textContent = "*Melebihi sisa pinjaman.";
      isValid = false;
    }
    jumlahError.style.display =
      jumlahError.textContent !== "" ? "inline" : "none";

    if (
      !kondisiSelect.value ||
      kondisiSelect.value === "Pilih Kondisi Barang"
    ) {
      kondisiError.textContent = "*Harus Dipilih";
      isValid = false;
    }
    kondisiError.style.display =
      !kondisiSelect.value || kondisiSelect.value === "Pilih Kondisi Barang"
        ? "inline"
        : "none";

    if (!catatanInput.value.trim()) {
      catatanError.textContent = "*Harus Diisi";
      isValid = false;
    }
    catatanError.style.display = !catatanInput.value.trim() ? "inline" : "none";

    if (!isValid) return;

    // Show confirmation modal
    const confirmModal = new bootstrap.Modal(
      document.getElementById("confirmModal")
    );
    const confirmAction = document.getElementById("confirmAction");
    const confirmYes = document.getElementById("confirmYes");

    confirmAction.textContent = "mengembalikan barang ini";

    confirmYes.onclick = function () {
      confirmModal.hide();
      form.submit(); // Submit form after confirmation
    };

    confirmModal.show();
  });
}

function setupPengembalianRuanganPage() {
  const form = document.getElementById("formPengembalianRuangan");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    // Validasi kondisi ruangan
    const kondisiSelect = document.getElementById("kondisiRuangan");
    const kondisiError = document.getElementById("kondisiError");
    if (!kondisiSelect.value || kondisiSelect.value === "") {
      kondisiError.textContent = "*Kondisi ruangan harus dipilih";
      kondisiError.style.display = "inline";
      isValid = false;
    } else {
      kondisiError.style.display = "none";
    }

    // Validasi catatan pengembalian
    const catatanInput = document.getElementById("catatanPengembalianRuangan");
    const catatanError = document.getElementById("catatanError");
    if (!catatanInput.value.trim()) {
      catatanError.textContent = "*Catatan pengembalian wajib diisi";
      catatanError.style.display = "inline";
      isValid = false;
    } else {
      catatanError.style.display = "none";
    }

    if (!isValid) return;

    // Show confirmation modal
    const confirmModal = new bootstrap.Modal(
      document.getElementById("confirmModal")
    );
    const confirmAction = document.getElementById("confirmAction");
    const confirmYes = document.getElementById("confirmYes");

    confirmAction.textContent = "mengembalikan ruangan ini";

    confirmYes.onclick = function () {
      confirmModal.hide();
      form.submit(); // Submit form after confirmation
    };

    confirmModal.show();
  });
}

// =================================================================
// #6: FITUR TAMBAHAN (SIDEBAR, PROTEKSI INPUT, MODAL)
// =================================================================

function setupSidebarPersistence() {
  const sidebar = document.querySelector(".sidebar, .offcanvas-body");
  if (!sidebar) return;

  const storageKey = "sidebar_active_menus";
  const getActiveMenus = () =>
    JSON.parse(localStorage.getItem(storageKey)) || [];
  const setActiveMenus = (menus) =>
    localStorage.setItem(storageKey, JSON.stringify(menus));

  // Pulihkan state saat load
  getActiveMenus().forEach((menuId) => {
    const menuElement = document.getElementById(menuId);
    if (menuElement) {
      const collapseInstance = new bootstrap.Collapse(menuElement, {
        toggle: false,
      });
      collapseInstance.show();
    }
  });

  // Tambahkan event listener
  sidebar.querySelectorAll(".collapse").forEach((menu) => {
    menu.addEventListener("show.bs.collapse", function () {
      let activeMenus = getActiveMenus();
      if (!activeMenus.includes(this.id)) {
        activeMenus.push(this.id);
        setActiveMenus(activeMenus);
      }
    });
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

function setupInputProtection() {
  document.querySelectorAll(".protect-input").forEach((input) => {
    input.addEventListener("paste", (e) => e.preventDefault());
    input.addEventListener("input", () => (input.value = input.defaultValue));
    input.addEventListener("mousedown", (e) => e.preventDefault());
  });
}

function setupModalChaining() {
  const confirmYesButton = document.getElementById("confirmYes");
  if (!confirmYesButton) return;

  confirmYesButton.addEventListener("click", function () {
    const confirmModalElement = document.getElementById("confirmModal");
    const successModalElement = document.getElementById("successModal");

    if (confirmModalElement && successModalElement) {
      const confirmModalInstance =
        bootstrap.Modal.getInstance(confirmModalElement);
      if (confirmModalInstance) confirmModalInstance.hide();

      const successModalInstance = new bootstrap.Modal(successModalElement);
      successModalInstance.show();
    }
  });
}

// =================================================================
// #7: VALIDASI SEMUA FORM CRUD
// =================================================================

function setupFormTambahBarang() {
  const form = document.getElementById("formTambahBarang");
  if (!form) return;

  setupStockStepper("stepperContainer", "stokBarang");

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    // Validasi nama barang
    const namaBarang = document.getElementById("namaBarang");
    const namaError = document.getElementById("namaError");
    if (!namaBarang.value.trim()) {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    } else {
      namaError.style.display = "none";
    }

    // Validasi stok barang
    const stokBarang = document.getElementById("stokBarang");
    const stokError = document.getElementById("stokError");
    if (!stokBarang.value || stokBarang.value <= 0) {
      stokError.textContent = "*Harus diisi dan minimal 1";
      stokError.style.display = "inline";
      isValid = false;
    } else {
      stokError.style.display = "none";
    }

    // Validasi lokasi barang
    const lokasiBarang = document.getElementById("lokasiBarang");
    const lokasiError = document.getElementById("lokasiError");
    if (!lokasiBarang.value) {
      lokasiError.textContent = "*Harus dipilih";
      lokasiError.style.display = "inline";
      isValid = false;
    } else {
      lokasiError.style.display = "none";
    }

    if (isValid) {
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent = "menambah barang";
      document.getElementById("confirmYes").onclick = () => form.submit();
      confirmModal.show();
    }
  });
}

function setupFormEditBarang() {
  const form = document.getElementById("formEditBarang");
  if (!form) return;

  setupStockStepper("stepperContainer", "stokBarang");

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    // Validasi nama barang
    const namaBarang = document.getElementById("namaBarang");
    const namaError = document.getElementById("namaError");
    if (!namaBarang.value.trim()) {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    } else {
      namaError.style.display = "none";
    }

    // Validasi stok barang (boleh 0 untuk edit)
    const stokBarang = document.getElementById("stokBarang");
    const stokError = document.getElementById("stokError");
    if (stokBarang.value === "" || stokBarang.value < 0) {
      stokError.textContent = "*Harus diisi dan minimal 0";
      stokError.style.display = "inline";
      isValid = false;
    } else {
      stokError.style.display = "none";
    }

    // Validasi lokasi barang
    const lokasiBarang = document.getElementById("lokasiBarang");
    const lokasiError = document.getElementById("lokasiError");
    if (!lokasiBarang.value) {
      lokasiError.textContent = "*Harus dipilih";
      lokasiError.style.display = "inline";
      isValid = false;
    } else {
      lokasiError.style.display = "none";
    }

    if (isValid) {
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent =
        "mengubah data barang";
      document.getElementById("confirmYes").onclick = () => form.submit();
      confirmModal.show();
    }
  });
}

function setupFormTambahRuangan() {
  const form = document.getElementById("formTambahRuangan");
  if (!form) return;

  setupKondisiRuanganLogic();

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    const namaRuangan = document.getElementById("namaRuangan");
    const kondisiRuangan = document.getElementById("kondisiRuangan");
    const ketersediaan = document.getElementById("ketersediaan");
    const namaError = document.getElementById("namaError");
    const kondisiError = document.getElementById("kondisiError");
    const ketersediaanError = document.getElementById("ketersediaanError");

    // Validasi nama ruangan
    if (!namaRuangan.value.trim()) {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    } else {
      namaError.style.display = "none";
    }

    // Validasi kondisi ruangan
    if (!kondisiRuangan.value) {
      kondisiError.textContent = "*Harus dipilih";
      kondisiError.style.display = "inline";
      isValid = false;
    } else {
      kondisiError.style.display = "none";
    }

    // Validasi ketersediaan
    if (!ketersediaan.value) {
      ketersediaanError.textContent = "*Harus dipilih";
      ketersediaanError.style.display = "inline";
      isValid = false;
    } else {
      ketersediaanError.style.display = "none";
    }

    if (isValid) {
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent = "menambah ruangan";
      document.getElementById("confirmYes").onclick = () => form.submit();
      confirmModal.show();
    }
  });
}

function setupFormEditRuangan() {
  const form = document.getElementById("formEditRuangan");
  if (!form) return;

  setupKondisiRuanganLogic();

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    const namaRuangan = document.getElementById("namaRuangan");
    const kondisiRuangan = document.getElementById("kondisiRuangan");
    const ketersediaan = document.getElementById("ketersediaan");
    const namaError = document.getElementById("namaError");
    const kondisiError = document.getElementById("kondisiError");
    const ketersediaanError = document.getElementById("ketersediaanError");

    // Reset error messages
    namaError.style.display = "none";
    kondisiError.style.display = "none";
    ketersediaanError.style.display = "none";

    // Validasi nama ruangan
    if (!namaRuangan.value.trim()) {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    }

    // Validasi kondisi ruangan
    if (!kondisiRuangan.value) {
      kondisiError.textContent = "*Harus dipilih";
      kondisiError.style.display = "inline";
      isValid = false;
    }

    // Validasi ketersediaan
    if (!ketersediaan.value) {
      ketersediaanError.textContent = "*Harus dipilih";
      ketersediaanError.style.display = "inline";
      isValid = false;
    }

    if (isValid) {
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent =
        "mengubah data ruangan";
      document.getElementById("confirmYes").onclick = () => form.submit();
      confirmModal.show();
    }
  });
}

function setupFormTambahAkunMhs() {
  const form = document.getElementById("formTambahAkunMhs");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;
    let nim = document.getElementById("nim").value.trim();
    let nama = document.getElementById("nama").value.trim();
    let email = document.getElementById("email").value.trim();
    let jenisRole = document.getElementById("jenisRole").value;

    let nimError = document.getElementById("nimError");
    let namaError = document.getElementById("namaError");
    let emailError = document.getElementById("emailError");
    let roleError = document.getElementById("roleError");

    // Reset error messages
    nimError.style.display = "none";
    namaError.style.display = "none";
    emailError.style.display = "none";
    roleError.style.display = "none";

    if (!nim) {
      nimError.textContent = "*Harus diisi";
      nimError.style.display = "inline";
      isValid = false;
    } else if (!/^\d+$/.test(nim)) {
      nimError.textContent = "*Harus berupa angka";
      nimError.style.display = "inline";
      isValid = false;
    } else if (nim.length < 10) {
      nimError.textContent = "*Minimal 10 digit";
      nimError.style.display = "inline";
      isValid = false;
    }

    if (!nama) {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    } else if (/\d/.test(nama)) {
      namaError.textContent = "*Harus berupa huruf";
      namaError.style.display = "inline";
      isValid = false;
    }

    if (!email) {
      emailError.textContent = "*Harus diisi";
      emailError.style.display = "inline";
      isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      emailError.textContent = "*Format email tidak valid";
      emailError.style.display = "inline";
      isValid = false;
    }

    if (!jenisRole) {
      roleError.textContent = "*Harus dipilih";
      roleError.style.display = "inline";
      isValid = false;
    }

    if (isValid) {
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent =
        "menambah akun mahasiswa";
      document.getElementById("confirmYes").onclick = () => form.submit();
      confirmModal.show();
    }
  });
}

function setupFormEditAkunMhs() {
  const form = document.getElementById("formEditAkunMhs");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    // Validasi email
    const email = document.getElementById("email").value.trim();
    const emailError = document.getElementById("emailError");
    if (!email) {
      emailError.textContent = "*Harus diisi";
      emailError.style.display = "inline";
      isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      emailError.textContent = "*Format email tidak valid";
      emailError.style.display = "inline";
      isValid = false;
    } else {
      emailError.style.display = "none";
    }

    if (isValid) {
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent =
        "mengubah data akun mahasiswa";
      document.getElementById("confirmYes").onclick = () => form.submit();
      confirmModal.show();
    }
  });

  // Setup input protection untuk field yang tidak boleh diubah
  document.querySelectorAll(".protect-input").forEach((input) => {
    input.addEventListener("paste", (e) => e.preventDefault());
    input.addEventListener("input", () => (input.value = input.defaultValue));
    input.addEventListener("mousedown", (e) => e.preventDefault());
  });

  // Setup password visibility toggle
  const passInput = document.getElementById("kataSandi");
  if (passInput) {
    passInput.addEventListener("mouseenter", function () {
      passInput.type = "text";
    });
    passInput.addEventListener("mouseleave", function () {
      passInput.type = "password";
    });
  }
}

function setupFormTambahAkunKry() {
  const form = document.getElementById("formTambahAkunKry");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    const npk = document.getElementById("npk").value.trim();
    const nama = document.getElementById("nama").value.trim();
    const email = document.getElementById("email").value.trim();
    const jenisRole = document.getElementById("jenisRole").value;

    const npkError = document.getElementById("npkError");
    const namaError = document.getElementById("namaError");
    const emailError = document.getElementById("emailError");
    const roleError = document.getElementById("roleError");

    // Reset error messages
    npkError.style.display = "none";
    namaError.style.display = "none";
    emailError.style.display = "none";
    roleError.style.display = "none";

    // Validasi NPK
    if (!npk) {
      npkError.textContent = "*Harus diisi";
      npkError.style.display = "inline";
      isValid = false;
    } else if (!/^\d+$/.test(npk)) {
      npkError.textContent = "*Harus berupa angka";
      npkError.style.display = "inline";
      isValid = false;
    } else if (npk.length < 5) {
      npkError.textContent = "*Minimal 5 digit";
      npkError.style.display = "inline";
      isValid = false;
    }

    // Validasi nama
    if (!nama) {
      namaError.textContent = "*Harus diisi";
      namaError.style.display = "inline";
      isValid = false;
    } else if (/\d/.test(nama)) {
      namaError.textContent = "*Harus berupa huruf";
      namaError.style.display = "inline";
      isValid = false;
    }

    // Validasi email
    if (!email) {
      emailError.textContent = "*Harus diisi";
      emailError.style.display = "inline";
      isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      emailError.textContent = "*Format email tidak valid";
      emailError.style.display = "inline";
      isValid = false;
    }

    // Validasi role
    if (!jenisRole) {
      roleError.textContent = "*Harus dipilih";
      roleError.style.display = "inline";
      isValid = false;
    }

    if (isValid) {
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent =
        "menambah akun karyawan";
      document.getElementById("confirmYes").onclick = () => form.submit();
      confirmModal.show();
    }
  });
}

function setupFormEditAkunKry() {
  const form = document.getElementById("formEditAkunKry");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;
    let email = document.getElementById("email").value.trim();
    let emailError = document.getElementById("emailError");

    // Reset error messages
    emailError.style.display = "none";

    if (email === "") {
      emailError.textContent = "*Harus diisi";
      emailError.style.display = "inline";
      isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      emailError.textContent = "*Format email tidak valid";
      emailError.style.display = "inline";
      isValid = false;
    }

    if (isValid) {
      const confirmModal = new bootstrap.Modal(
        document.getElementById("confirmModal")
      );
      document.getElementById("confirmAction").textContent =
        "mengubah data  akun karyawan";
      document.getElementById("confirmYes").onclick = () => form.submit();
      confirmModal.show();
    }
  });

  document.querySelectorAll(".protect-input").forEach((input) => {
    input.addEventListener("paste", (e) => e.preventDefault());
    input.addEventListener("input", (e) => (input.value = input.defaultValue));
    input.addEventListener("mousedown", (e) => e.preventDefault());
  });

  const passInput = document.getElementById("kataSandi");
  passInput.addEventListener("mouseenter", function () {
    passInput.type = "text";
  });
  passInput.addEventListener("mouseleave", function () {
    passInput.type = "password";
  });
}

function setupFormTambahPeminjamanBrg() {
  const form = document.getElementById("formTambahPeminjamanBrg");
  if (!form) return;

  setupStockStepper("stepperContainerPeminjaman", "jumlahBrg", "stokTersedia");

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    const jumlahInputEl = document.getElementById("jumlahBrg");
    const alasanInputEl = document.getElementById("alasanPeminjamanBrg");
    const jumlahError = document.getElementById("jumlahError");
    const alasanError = document.getElementById("alasanError");

    // Reset error messages
    jumlahError.style.display = "none";
    alasanError.style.display = "none";

    // Validasi jumlah barang
    let jumlahValue = parseInt(jumlahInputEl.value, 10) || 0;
    if (!jumlahInputEl.value.trim()) {
      jumlahError.textContent = "*Harus diisi";
      jumlahError.style.display = "inline";
      isValid = false;
    }

    // Validasi alasan peminjaman
    if (!alasanInputEl.value.trim()) {
      alasanError.textContent = "*Harus diisi";
      alasanError.style.display = "inline";
      isValid = false;
    }

    // Ambil stok tersedia dari elemen (misal hidden input atau data attribute)
    let stokTersedia = 0;
    const stokElem = document.getElementById("stokBarang");
    if (stokElem) {
      stokTersedia = parseInt(
        stokElem.value || stokElem.textContent || "0",
        10
      );
    } else if (window.stokTersedia !== undefined) {
      stokTersedia = parseInt(window.stokTersedia, 10);
    } else {
      stokTersedia = parseInt(
        jumlahInputEl.getAttribute("data-stok") || "0",
        10
      );
    }
    if (isNaN(stokTersedia)) stokTersedia = 0;

    // Validasi jumlah terhadap stok
    if (jumlahValue <= 0) {
      jumlahError.textContent = "*Jumlah harus lebih dari 0.";
      jumlahError.style.display = "inline";
      isValid = false;
    } else if (jumlahValue > stokTersedia) {
      jumlahError.textContent = "*Jumlah melebihi stok tersedia.";
      jumlahError.style.display = "inline";
      isValid = false;
    }

    if (!isValid) return;

    // Show confirmation modal
    const confirmModal = new bootstrap.Modal(
      document.getElementById("confirmModal")
    );
    const confirmAction = document.getElementById("confirmAction");
    const confirmYes = document.getElementById("confirmYes");

    confirmAction.textContent = "menambah peminjaman barang";

    confirmYes.onclick = function () {
      confirmModal.hide();
      form.submit(); // Submit form after confirmation
    };

    confirmModal.show();
  });
}

function setupFormTambahPeminjamanRuangan() {
  const form = document.getElementById("formTambahPeminjamanRuangan");
  if (!form) return;

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    let isValid = true;
    const alasanInput = document.getElementById("alasanPeminjamanRuangan");
    const alasanError = document.getElementById("error-message");

    if (!alasanInput.value.trim()) {
      alasanError.textContent = "*Harus Diisi";
      alasanError.style.display = "inline";
      isValid = false;
    } else {
      alasanError.style.display = "none";
    }

    if (!isValid) return;

    // Show confirmation modal
    const confirmModal = new bootstrap.Modal(
      document.getElementById("confirmModal")
    );
    const confirmAction = document.getElementById("confirmAction");
    const confirmYes = document.getElementById("confirmYes");

    confirmAction.textContent = "menambah peminjaman ruangan";

    confirmYes.onclick = function () {
      confirmModal.hide();
      form.submit(); // Submit form after confirmation
    };

    confirmModal.show();
  });
}
