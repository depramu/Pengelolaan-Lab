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

// Pengembalian Barang
// Fungsi untuk mengubah jumlah stok
function changeStok(val) {
  // Targetkan ID yang benar: 'jumlahPengembalian'
  let stokInput = document.getElementById("jumlahPengembalian");
  let maxStok = parseInt(document.getElementById("jumlahBrg").value) || 0;
  let current = parseInt(stokInput.value) || 0;
  let next = current + val;

  if (next < 0) next = 0;
  if (next > maxStok) next = maxStok; // Batasi agar tidak lebih dari jumlah pinjaman
  stokInput.value = next;
}
// Validasi form pengembalian
// Fungsi validasi form sebelum submit
document.querySelector("form").addEventListener("submit", function (e) {
  let valid = true;

  // Validasi Jumlah Pengembalian
  // Targetkan ID yang benar: 'jumlahPengembalian'
  const jumlahInput = document.getElementById("jumlahPengembalian");
  const jumlahError = document.getElementById("jumlahError");
  const jumlahPinjam =
    parseInt(document.getElementById("jumlahBrg").value) || 0;

  if (parseInt(jumlahInput.value) <= 0) {
    jumlahError.textContent = "*Jumlah harus lebih dari 0.";
    jumlahError.style.display = "block";
    valid = false;
  } else if (parseInt(jumlahInput.value) > jumlahPinjam) {
    jumlahError.textContent = "*Jumlah melebihi yang dipinjam.";
    jumlahError.style.display = "block";
    valid = false;
  } else {
    jumlahError.style.display = "none";
  }

  // Validasi Kondisi Barang
  const kondisiSelect = document.getElementById("txtKondisi");
  const kondisiError = document.getElementById("kondisiError");
  if (kondisiSelect.value === "Pilih Kondisi Barang") {
    kondisiError.style.display = "block";
    valid = false;
  } else {
    kondisiError.style.display = "none";
  }

  // Validasi Catatan Pengembalian
  // Targetkan ID yang benar: 'catatanPengembalianBarang'
  const catatanInput = document.getElementById("catatanPengembalianBarang");
  const catatanError = document.getElementById("catatanError");
  if (catatanInput.value.trim() === "") {
    catatanError.style.display = "block";
    valid = false;
  } else {
    catatanError.style.display = "none";
  }

  if (!valid) {
    e.preventDefault(); // Hentikan pengiriman form jika tidak valid
  }
});
// VValidasi hidden
let jumlah = parseInt(document.getElementById("jumlahBrg").value) || 0;

function updateJumlah() {
  // Update tampilan ke user
  document.getElementById("tampilJumlah").textContent = jumlah;

  // Update input hidden untuk dikirim ke server
  document.getElementById("jumlahBrg").value = jumlah;
}

let id = parseInt(document.getElementById("idPeminjamanBrg").value) || 0;

function updateId() {
  // Update tampilan ke user
  document.getElementById("idPeminjaman").textContent = id;

  // Update input hidden untuk dikirim ke server
  document.getElementById("idPeminjamanBrg").value = id;
}

let namaBarang = document.getElementById("namaBarang").value || "";

function updateNamaBarang() {
  // Update tampilan ke user
  document.getElementById("namaBarang").textContent = namaBarang;

  // Update input hidden untuk dikirim ke server
  document.getElementById("namaBarang").value = namaBarang;
}  
