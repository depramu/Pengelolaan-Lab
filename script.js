// Menunggu hingga seluruh konten halaman dimuat sebelum menjalankan skrip
document.addEventListener("DOMContentLoaded", function () {
  // =================================================================
  // FUNGSI & HELPER UMUM
  // =================================================================

  /**
   * Mencegah input, paste, dan klik pada elemen dengan kelas .protect-input
   */
  function protectInputs() {
    document.querySelectorAll(".protect-input").forEach((input) => {
      // Mengembalikan nilai ke nilai awal jika ada perubahan
      const defaultValue = input.value;
      input.addEventListener("input", () => {
        input.value = defaultValue;
      });
      // Mencegah paste
      input.addEventListener("paste", (e) => e.preventDefault());
      // Mencegah fokus dengan mouse (opsional, tapi membantu)
      input.addEventListener("mousedown", (e) => e.preventDefault());
    });
  }
  protectInputs(); // Jalankan fungsi ini secara global

  /**
   * Fungsi untuk mengubah nilai stok pada input number dengan tombol +/-.
   * @param {number} val - Nilai penambahan (1) atau pengurangan (-1).
   * @param {string} inputId - ID dari elemen input stok.
   */
  function changeStok(val, inputId) {
    let stokInput = document.getElementById(inputId);
    if (stokInput) {
      let current = parseInt(stokInput.value) || 0;
      let next = current + val;
      if (next < 0) next = 0;

      // Cek jika ada batas maksimal dari atribut 'max'
      const max = stokInput.getAttribute("max");
      if (max !== null && next > parseInt(max)) {
        next = parseInt(max);
      }

      stokInput.value = next;
    }
  }

  // Listener global untuk tombol stepper stok (jika ada)
  // Ini menggantikan onclick di HTML
  document.body.addEventListener("click", function (e) {
    if (e.target.matches(".stok-increment")) {
      const inputId = e.target.dataset.target;
      changeStok(1, inputId);
    }
    if (e.target.matches(".stok-decrement")) {
      const inputId = e.target.dataset.target;
      changeStok(-1, inputId);
    }
  });

  // =================================================================
  // LOGIKA SPESIFIK UNTUK HALAMAN TERTENTU
  // =================================================================

  // --- Logika untuk Halaman Tambah & Edit Ruangan ---
  const kondisiRuanganSelect = document.getElementById("kondisiRuangan");
  if (kondisiRuanganSelect) {
    const ketersediaanSelect = document.getElementById("ketersediaan");
    const ketersediaanHidden = document.getElementById("ketersediaanHidden");

    const updateKetersediaan = () => {
      if (kondisiRuanganSelect.value === "Rusak") {
        ketersediaanSelect.value = "Tidak Tersedia";
        ketersediaanSelect.disabled = true;
        if (ketersediaanHidden) ketersediaanHidden.value = "Tidak Tersedia";
      } else {
        ketersediaanSelect.disabled = false;
        if (ketersediaanHidden)
          ketersediaanHidden.value = ketersediaanSelect.value;
      }
    };

    kondisiRuanganSelect.addEventListener("change", updateKetersediaan);
    ketersediaanSelect.addEventListener("change", () => {
      if (ketersediaanHidden)
        ketersediaanHidden.value = ketersediaanSelect.value;
    });

    // Jalankan saat halaman dimuat
    updateKetersediaan();
  }

  // --- Logika untuk Halaman Tambah & Edit Barang (Stepper) ---
  // Pastikan tombol di HTML memiliki kelas `stok-increment`/`stok-decrement` dan `data-target="stokBarang"`
  // Contoh: <button class="btn btn-outline-secondary stok-decrement" type="button" data-target="stokBarang">-</button>

  // --- Logika untuk Halaman Cek Ketersediaan (Barang & Ruangan) ---
  const datePickerContainer = document.querySelector("#tglHari");
  if (datePickerContainer) {
    const tahunSelect = document.getElementById("tglTahun");
    const bulanSelect = document.getElementById("tglBulan");
    const hariSelect = document.getElementById("tglHari");

    const isLeapYear = (year) =>
      (year % 4 === 0 && year % 100 !== 0) || year % 400 === 0;

    const updateDays = () => {
      const bulan = parseInt(bulanSelect.value);
      const tahun = parseInt(tahunSelect.value);
      const prevHari = hariSelect.value;
      let days = 31;
      if ([4, 6, 9, 11].includes(bulan)) days = 30;
      else if (bulan === 2) days = isLeapYear(tahun) ? 29 : 28;

      hariSelect.innerHTML = "";
      for (let i = 1; i <= days; i++) {
        hariSelect.add(new Option(i, i.toString().padStart(2, "0")));
      }
      if (prevHari && parseInt(prevHari) <= days) {
        hariSelect.value = prevHari;
      }
    };

    const fillSelects = () => {
      const now = new Date();
      for (let y = now.getFullYear(); y <= now.getFullYear() + 5; y++) {
        tahunSelect.add(new Option(y, y));
      }
      const months = [
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember",
      ];
      months.forEach((month, index) => {
        bulanSelect.add(
          new Option(month, (index + 1).toString().padStart(2, "0"))
        );
      });

      bulanSelect.value = (now.getMonth() + 1).toString().padStart(2, "0");
      tahunSelect.value = now.getFullYear();
      updateDays();
      hariSelect.value = now.getDate().toString().padStart(2, "0");
    };

    fillSelects();
    bulanSelect.addEventListener("change", updateDays);
    tahunSelect.addEventListener("change", updateDays);
  }

  // --- Logika untuk Halaman Cek Ruangan (Time Picker) ---
  const jamDariSelect = document.getElementById("jam_dari");
  if (jamDariSelect) {
    const isiWaktu = (id, max) => {
      const el = document.getElementById(id);
      el.innerHTML = '<option value="" disabled selected>--</option>';
      for (let i = 0; i < max; i++) {
        const val = i.toString().padStart(2, "0");
        el.add(new Option(val, val));
      }
    };
    isiWaktu("jam_dari", 24);
    isiWaktu("jam_sampai", 24);
    isiWaktu("menit_dari", 60);
    isiWaktu("menit_sampai", 60);
  }

  // =================================================================
  // VALIDASI FORM
  // =================================================================

  // --- Validasi untuk Form Tambah Akun Mahasiswa ---
  const formTambahAkunMhs = document.getElementById("formTambahAkunMhs");
  if (formTambahAkunMhs) {
    formTambahAkunMhs.addEventListener("submit", function (e) {
      // (Letakkan logika validasi dari file tambahAkunMhs.php di sini)
      // ...
      // if (!valid) e.preventDefault();
    });
  }

  // --- Validasi untuk Form Pengajuan Peminjaman Ruangan ---
  const formPengajuanRuangan = document.getElementById("formPengajuanRuangan");
  if (formPengajuanRuangan) {
    formPengajuanRuangan.addEventListener("submit", function (event) {
      let alasanPeminjamanRuangan = document.getElementById(
        "alasanPeminjamanRuangan"
      ).value;
      let errorMessage = document.getElementById("error-message");
      if (alasanPeminjamanRuangan.trim() === "") {
        errorMessage.style.display = "inline";
        event.preventDefault();
      }
    });
  }

  // --- Validasi untuk Halaman Cek Ruangan ---
  const formCekRuangan = document.getElementById("form-peminjaman");
  if (formCekRuangan && document.getElementById("jam_dari")) {
    formCekRuangan.addEventListener("submit", function (e) {
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
        errorMsg.style.display = "inline";
        isValid = false;
      } else {
        const inputDate = new Date(
          `${tahun}-${bulan.padStart(2, "0")}-${hari.padStart(2, "0")}`
        );
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        if (inputDate < today) {
          errorMsg.textContent = "*Input tanggal sudah lewat";
          errorMsg.style.display = "inline";
          isValid = false;
        } else {
          errorMsg.style.display = "none";
        }
      }

      // Validasi waktu diisi atau tidak
      errorWaktuMulai.style.display =
        !jamDari || !menitDari ? "inline" : "none";
      errorWaktuSelesai.style.display =
        !jamSampai || !menitSampai ? "inline" : "none";
      if (!jamDari || !menitDari || !jamSampai || !menitSampai) isValid = false;

      // Validasi logika waktu
      if (jamDari && menitDari && jamSampai && menitSampai) {
        const startMinutes = parseInt(jamDari) * 60 + parseInt(menitDari);
        const endMinutes = parseInt(jamSampai) * 60 + parseInt(menitSampai);
        if (endMinutes <= startMinutes) {
          errorWaktu.textContent =
            "*Waktu selesai harus lebih besar dari waktu mulai";
          errorWaktu.style.display = "block";
          isValid = false;
        } else {
          errorWaktu.style.display = "none";
        }
      }

      if (!isValid) {
        e.preventDefault();
      } else {
        document.getElementById(
          "tglPeminjamanRuangan"
        ).value = `${hari}-${bulan}-${tahun}`;
      }
    });
  }

  // --- Logika untuk Halaman Laporan (PIC & Ka UPT) ---
  const tampilkanLaporanBtn = document.getElementById("tampilkanLaporanBtn");
  if (tampilkanLaporanBtn) {
    // ... (seluruh kode JavaScript dari file laporan.php dipindahkan ke sini) ...
    // Ini adalah blok kode yang sangat besar dari file Menu PIC/laporan.php dan Menu Ka UPT/laporan.php
    // Pastikan untuk memindahkannya secara utuh ke dalam blok if ini.
    // Contoh awal:
    const jenisLaporanSelect = document.getElementById("jenisLaporan");
    // ... dan seterusnya
  }
});
