
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
