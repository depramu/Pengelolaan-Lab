
// Script untuk mengelola tampilan dan validasi form penolakan barang
document.addEventListener('DOMContentLoaded', function() {
    var btnTolak = document.getElementById('btnTolak');
    if (btnTolak) {
        btnTolak.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('alasanPenolakanGroup').style.display = '';
            btnTolak.style.display = 'none';
            if (!document.getElementById('btnSubmitPenolakan')) {
                var submitBtn = document.createElement('button');
                submitBtn.type = 'submit';
                submitBtn.name = 'tolak_submit';
                submitBtn.className = 'btn btn-danger';
                submitBtn.id = 'btnSubmitPenolakan';
                submitBtn.innerText = 'Submit Penolakan';
                submitBtn.onclick = function() {
                    return validateTolak();
                };
                btnTolak.parentNode.insertBefore(submitBtn, btnTolak);
            }
            document.getElementById('alasanPenolakan').focus();
        });
    }
});

function validateTolak() {
    var alasan = document.getElementById('alasanPenolakan').value.trim();
    var errorDiv = document.getElementById('alasanPenolakanError');
    if (alasan === '') {
        errorDiv.style.display = 'block';
        document.getElementById('alasanPenolakan').focus();
        return false;
    } else {
        errorDiv.style.display = 'none';
        return true;
    }
}