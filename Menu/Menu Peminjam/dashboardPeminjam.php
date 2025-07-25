<?php
require_once '../../function/init.php';
authorize_role(['Peminjam']);

include '../../templates/header.php';
include '../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">
  <h3 class="fw-semibold mb-3">Beranda</h3>
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

<?php

include '../../templates/footer.php';
