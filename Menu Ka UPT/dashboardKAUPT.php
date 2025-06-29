<?php
require_once __DIR__ . '/../auth.php'; // Muat fungsi otorisasi
authorize_role('KA UPT'); // Lindungi halaman ini untuk role 'Peminjam'
include '../templates/header.php';
include '../templates/sidebar.php';

?>
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
  <div class="mb-5">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Sistem Pengelolaan Lab</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
      </ol>
    </nav>
  </div>
  <div class="mb-5">
    <div class="display-5 display-md-3 fw-semibold text-primary">Selamat Datang</div>
    <div class="display-5 display-md-3 fw-semibold text-primary">di Sistem Pengelolaan <br>Laboratorium!</div>
  </div>
  <img src="../icon/atoy0.png" class="atoy-img d-none d-md-block img-fluid" alt="Atoy" />
</main>

<?php
include '../templates/footer.php';
?>
