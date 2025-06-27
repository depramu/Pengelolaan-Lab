// CRUD AKUN (PUNYA NAD JANGAN DIUBAH!!!)
// Validasi untuk tambah akun mahasiswa
document.querySelector("form").addEventListener("submit", function (e) {
  let nim = document.getElementById("nim").value.trim();
  let nama = document.getElementById("nama").value.trim();
  let email = document.getElementById("email").value.trim();
  let jenisRole = document.getElementById("jenisRole").value;
  let pass = document.getElementById("kataSandi").value;
  let conf = document.getElementById("konfirmasiSandi").value;

  let nimError = document.getElementById("nimError");
  let namaError = document.getElementById("namaError");
  let emailError = document.getElementById("emailError");
  let roleError = document.getElementById("roleError");
  let passError = document.getElementById("passError");
  let confPassError = document.getElementById("confPassError");
  let passPattern = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;

  let valid = true;

  // Reset error messages
  nimError.style.display = "none";
  namaError.style.display = "none";
  emailError.style.display = "none";
  roleError.style.display = "none";
  passError.style.display = "none";

  if (nim === "") {
    nimError.textContent = "*Harus diisi";
    nimError.style.display = "inline";
    valid = false;
  } else if (!/^\d+$/.test(nim)) {
    nimError.textContent = "*Harus berupa angka";
    nimError.style.display = "inline";
    valid = false;
  }

  if (nama === "") {
    namaError.textContent = "*Harus diisi";
    namaError.style.display = "inline";
    valid = false;
  } else if (/\d/.test(nama)) {
    namaError.textContent = "*Harus berupa huruf";
    namaError.style.display = "inline";
    valid = false;
  }

  if (email === "") {
    emailError.textContent = "*Harus diisi";
    emailError.style.display = "inline";
    valid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    emailError.textContent = "*Format email tidak valid";
    emailError.style.display = "inline";
    valid = false;
  }

  if (jenisRole === "") {
    roleError.textContent = "*Harus diisi";
    roleError.style.display = "inline";
    valid = false;
  }

  if (pass === "") {
    passError.textContent = "*Harus diisi";
    passError.style.display = "inline";
    valid = false;
  } else if (pass.length > 0 && pass.length < 8) {
    passError.textContent = "*Minimal 8 karakter";
    passError.style.display = "inline";
    valid = false;
  } else if (!passPattern.test(pass)) {
    passError.textContent = "*Harus mengandung huruf dan angka";
    passError.style.display = "inline";
    valid = false;
  }

  if (conf === "") {
    confPassError.textContent = "*Harus diisi";
    confPassError.style.display = "inline";
    valid = false;
  } else if (pass !== "" && conf !== "" && pass !== conf) {
    confPassError.textContent = "*Tidak sesuai";
    confPassError.style.display = "inline";
    valid = false;
  }

  if (!valid) e.preventDefault();
});

// Validasi untuk edit akun mahasiswa
document.querySelector("form").addEventListener("submit", function (e) {
  let email = document.getElementById("email").value.trim();
  let emailError = document.getElementById("emailError");

  let valid = true;

  if (email === "") {
    emailError.textContent = "*Harus diisi";
    emailError.style.display = "inline";
    valid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    emailError.textContent = "*Format email tidak valid";
    emailError.style.display = "inline";
    valid = false;
  }
  if (!valid) e.preventDefault();
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

// Validasi untuk tambah akun karyawan
document.querySelector("form").addEventListener("submit", function (e) {
  let npk = document.getElementById("npk").value.trim();
  let nama = document.getElementById("nama").value.trim();
  let email = document.getElementById("email").value.trim();
  let jenisRole = document.getElementById("jenisRole").value;
  let pass = document.getElementById("kataSandi").value;
  let conf = document.getElementById("konfirmasiSandi").value;

  let npkError = document.getElementById("npkError");
  let namaError = document.getElementById("namaError");
  let emailError = document.getElementById("emailError");
  let roleError = document.getElementById("roleError");
  let passError = document.getElementById("passError");
  let confPassError = document.getElementById("confPassError");
  let passPattern = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;

  let valid = true;

  // Reset error messages
  npkError.style.display = "none";
  namaError.style.display = "none";
  emailError.style.display = "none";
  roleError.style.display = "none";
  passError.style.display = "none";

  if (npk === "") {
    npkError.textContent = "*Harus diisi";
    npkError.style.display = "inline";
    valid = false;
  } else if (!/^\d+$/.test(npk)) {
    npkError.textContent = "*Harus berupa angka";
    npkError.style.display = "inline";
    valid = false;
  }

  if (nama === "") {
    namaError.textContent = "*Harus diisi";
    namaError.style.display = "inline";
    valid = false;
  } else if (/\d/.test(nama)) {
    namaError.textContent = "*Harus berupa huruf";
    namaError.style.display = "inline";
    valid = false;
  }

  if (email === "") {
    emailError.textContent = "*Harus diisi";
    emailError.style.display = "inline";
    valid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    emailError.textContent = "*Format email tidak valid";
    emailError.style.display = "inline";
    valid = false;
  }

  if (jenisRole === "") {
    roleError.textContent = "*Harus diisi";
    roleError.style.display = "inline";
    valid = false;
  }

  if (pass === "") {
    passError.textContent = "*Harus diisi";
    passError.style.display = "inline";
    valid = false;
  } else if (pass.length > 0 && pass.length < 8) {
    passError.textContent = "*Minimal 8 karakter";
    passError.style.display = "inline";
    valid = false;
  } else if (!passPattern.test(pass)) {
    passError.textContent = "*Harus mengandung huruf dan angka";
    passError.style.display = "inline";
    valid = false;
  }

  if (conf === "") {
    confPassError.textContent = "*Harus diisi";
    confPassError.style.display = "inline";
    valid = false;
  } else if (pass !== "" && conf !== "" && pass !== conf) {
    confPassError.textContent = "*Tidak sesuai";
    confPassError.style.display = "inline";
    valid = false;
  }

  if (!valid) e.preventDefault();
});

// Validasi untuk edit akun karyawan
    document.querySelector('form').addEventListener('submit', function(e) {
        let email = document.getElementById('email').value.trim();
        let emailError = document.getElementById('emailError');

        let valid = true;

        if (email === "") {
            emailError.textContent = '*Harus diisi';
            emailError.style.display = 'inline';
            valid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            emailError.textContent = '*Format email tidak valid';
            emailError.style.display = 'inline';
            valid = false;
        }
        if (!valid) e.preventDefault();
    });

    document.querySelectorAll('.protect-input').forEach(input => {
        input.addEventListener('paste', e => e.preventDefault());
        input.addEventListener('input', e => input.value = input.defaultValue);
        input.addEventListener('mousedown', e => e.preventDefault());
    });

    const passInput = document.getElementById('kataSandi');
    passInput.addEventListener('mouseenter', function() {
        passInput.type = 'text';
    });
    passInput.addEventListener('mouseleave', function() {
        passInput.type = 'password';
    });

// AYUUUUUUU
    function changeStok(val) {
        let stokInput = document.getElementById('jumlahBrg');
        let current = parseInt(stokInput.value) || 0;
        let next = current + val;
        if (next < 0) next = 0;
        stokInput.value = next;
    }

    document.querySelectorAll('.protect-input').forEach(input => {
        input.addEventListener('paste', e => e.preventDefault());
        input.addEventListener('input', e => input.value = input.defaultValue);
        input.addEventListener('mousedown', e => e.preventDefault());
    });

    // Fungsi validasi form sebelum submit
    document.querySelector('form').addEventListener('submit', function(e) {
        let valid = true;

        // Validasi Jumlah Peminjaman
        let jumlahInput = document.getElementById('jumlahBrg');
        let jumlahError = document.getElementById('jumlahError');
        let stokTersedia = <?= $stokBarang ?>;
        let jumlahValue = parseInt(jumlahInput.value) || 0;

        if (jumlahValue <= 0) {
            jumlahError.textContent = '*Jumlah harus lebih dari 0.';
            jumlahError.style.display = 'inline';
            valid = false;
        } else if (jumlahValue > stokTersedia) {
            jumlahError.textContent = '*Jumlah melebihi stok tersedia.';
            jumlahError.style.display = 'inline';
            valid = false;
        }

        // Validate Alasan Peminjaman
        let alasanInput = document.getElementById('alasanPeminjamanBrg');
        let alasanError = document.getElementById('alasanError');
        if (alasanInput.value.trim() === '') {
            alasanError.textContent = '*Harus diisi';
            alasanError.style.display = 'inline';
            valid = false;
        } else {
            alasanError.style.display = 'none';
        }

        if (!valid) {
            e.preventDefault(); // Hentikan pengiriman form jika tidak valid
        }
    });

    // CRUD RUANGAN (KESYAAAAAAAAAA)
    // Validasi untuk tambah ruangan
    let kondisiSelect = document.getElementById('kondisiRuangan');
    let ketersediaanSelect = document.getElementById('ketersediaan');
    let ketersediaanHidden = document.getElementById('ketersediaanHidden');

    // Saat kondisi berubah
    kondisiSelect.addEventListener('change', function () {
        if (this.value === 'Rusak') {
            ketersediaanSelect.value = 'Tidak Tersedia';
            ketersediaanSelect.disabled = true;
            ketersediaanHidden.value = 'Tidak Tersedia';
        } else {
            ketersediaanSelect.disabled = false;
            ketersediaanSelect.value = '';
            ketersediaanHidden.value = '';
        }
    });

    // Saat ketersediaan dipilih manual
    ketersediaanSelect.addEventListener('change', function () {
        ketersediaanHidden.value = this.value;
    });

    // Pastikan hidden tetap update saat halaman dimuat
    window.addEventListener('DOMContentLoaded', function () {
        if (kondisiSelect.value === 'Rusak') {
            ketersediaanSelect.value = 'Tidak Tersedia';
            ketersediaanSelect.disabled = true;
            ketersediaanHidden.value = 'Tidak Tersedia';
        } else {
            ketersediaanHidden.value = ketersediaanSelect.value;
        }
    });

    // Validasi
    document.querySelector('form').addEventListener('submit', function(e) {
        let valid = true;

        // Nama
        let nama = document.getElementById('namaRuangan');
        let namaError = document.getElementById('namaError');
        if (nama.value.trim() === '') {
            namaError.style.display = 'inline';
            valid = false;
        } else {
            namaError.style.display = 'none';
        }

        // Kondisi
        let kondisiError = document.getElementById('kondisiError');
        if (!kondisiSelect.value || kondisiSelect.value === 'Pilih Kondisi') {
            kondisiError.style.display = 'inline';
            valid = false;
        } else {
            kondisiError.style.display = 'none';
        }

        // Ketersediaan (cek hidden)
        let ketersediaanError = document.getElementById('ketersediaanError');
        if (!ketersediaanHidden.value || ketersediaanHidden.value === 'Pilih Ketersediaan') {
            ketersediaanError.style.display = 'inline';
            valid = false;
        } else {
            ketersediaanError.style.display = 'none';
        }

        if (!valid) e.preventDefault();
    });

        // Validasi untuk edit ruangan
    // Saat kondisi berubah
    kondisiSelect.addEventListener('change', function () {
        if (this.value === 'Rusak') {
            ketersediaanSelect.value = 'Tidak Tersedia';
            ketersediaanSelect.disabled = true;
            ketersediaanHidden.value = 'Tidak Tersedia';
        } else {
            ketersediaanSelect.disabled = false;
            ketersediaanSelect.value = '';
            ketersediaanHidden.value = '';
        }
    });

    // Saat ketersediaan dipilih manual
    ketersediaanSelect.addEventListener('change', function () {
        ketersediaanHidden.value = this.value;
    });

    // Pastikan hidden tetap update saat halaman dimuat
    window.addEventListener('DOMContentLoaded', function () {
        if (kondisiSelect.value === 'Rusak') {
            ketersediaanSelect.value = 'Tidak Tersedia';
            ketersediaanSelect.disabled = true;
            ketersediaanHidden.value = 'Tidak Tersedia';
        } else {
            ketersediaanHidden.value = ketersediaanSelect.value;
        }
    });

    // Validasi
    document.querySelector('form').addEventListener('submit', function(e) {
        let valid = true;

        // Nama
        let nama = document.getElementById('namaRuangan');
        let namaError = document.getElementById('namaError');
        if (nama.value.trim() === '') {
            namaError.style.display = 'inline';
            valid = false;
        } else {
            namaError.style.display = 'none';
        }

        // Kondisi
        let kondisiError = document.getElementById('kondisiError');
        if (!kondisiSelect.value || kondisiSelect.value === 'Pilih Kondisi') {
            kondisiError.style.display = 'inline';
            valid = false;
        } else {
            kondisiError.style.display = 'none';
        }

        // Ketersediaan (cek hidden)
        let ketersediaanError = document.getElementById('ketersediaanError');
        if (!ketersediaanHidden.value || ketersediaanHidden.value === 'Pilih Ketersediaan') {
            ketersediaanError.style.display = 'inline';
            valid = false;
        } else {
            ketersediaanError.style.display = 'none';
        }

        if (!valid) e.preventDefault();
    });
