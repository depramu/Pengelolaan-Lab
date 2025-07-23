<?php
require_once '../../function/init.php';
authorize_role(['Peminjam']);

include '../../templates/header.php';
include '../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
  <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
    <h3 class="fw-semibold mb-0">Beranda</h3>
    <form class="d-flex" role="search" onsubmit="return false;">
      <input type="text" class="form-control me-2" placeholder="Cari disini!" id="inputCari" style="max-width: 250px;">
      <button class="btn btn-outline-secondary" type="button" id="searchButton">
        <i class="bi bi-search"></i>
      </button>
    </form>
  </div>
  <div class="mb-5">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
      <nav aria-label="breadcrumb" class="mb-2 mb-md-0">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="#">Sistem Pengelolaan Lab</a></li>
          <li class="breadcrumb-item active" aria-current="page">Beranda</li>
        </ol>
      </nav>
    </div>
  </div>
  <div class="mb-5">
    <div class="display-5 display-md-3 fw-semibold text-primary">Selamat Datang</div>
    <div class="display-5 display-md-3 fw-semibold text-primary">di Sistem Pengelolaan <br>Laboratorium!</div>
  </div>
  <img src="../../icon/atoy0.png" class="atoy-img d-none d-md-block img-fluid" alt="Atoy" />
</main>

<script>
  document.getElementById('searchButton').addEventListener('click', function() {
    const keyword = document.getElementById('inputCari').value.toLowerCase();
    const rows = document.querySelectorAll('#tabelPengumuman tbody tr');

    rows.forEach(row => {
      const columns = row.querySelectorAll('td');
      const no = columns[0]?.textContent.toLowerCase() || '';
      const nama = columns[1]?.textContent.toLowerCase() || '';
      const tanggal = columns[2]?.textContent.toLowerCase() || '';

      const isMatch = no.includes(keyword) || nama.includes(keyword) || tanggal.includes(keyword);
      row.style.display = isMatch ? '' : 'none';
    });
  });

  // Trigger pencarian saat user tekan Enter
  document.getElementById('inputCari').addEventListener('keyup', function(e) {
    document.getElementById('searchButton').click();
  });
</script>

<?php

include '../../templates/footer.php';
