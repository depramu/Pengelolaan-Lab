<<<<<<< HEAD
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

// ini validasi untuk batas stok, alasan harus diisi
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

// Validasi untuk tanggal peminjaman
    function isLeapYear(year) {
        return (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
    }

    function updateDays() {
        let bulan = parseInt(document.getElementById('tglBulan').value);
        let tahun = parseInt(document.getElementById('tglTahun').value);
        let days = 31;
        if ([4, 6, 9, 11].includes(bulan)) days = 30;
        else if (bulan === 2) days = isLeapYear(tahun) ? 29 : 28;

        let hariSelect = document.getElementById('tglHari');
        hariSelect.innerHTML = '';
        for (let i = 1; i <= days; i++) {
            hariSelect.innerHTML += `<option value="${i.toString().padStart(2, '0')}">${i}</option>`;
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
>>>>>>> 0af498c1044f0a65f9b71b394b8a9d497ad90cf2
