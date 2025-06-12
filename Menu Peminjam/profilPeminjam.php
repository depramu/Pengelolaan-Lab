<?php
include '../templates/header.php';
include '../templates/sidebar.php';

?>
<!-- Content Area -->
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
  <div class="mb-5">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
        <li class="breadcrumb-item active" aria-current="page">Profil Akun</li>
      </ol>
    </nav>
  </div>
  <!-- ====== PROFILE SECTION START ====== -->
  <div class="col-lg-7 col-md-9">
    <h2 class="fw-bold display-5" style=" margin-left: 50px; margin-bottom: -30px;">Data Akun</h2>
    <div class="card-body p-4 p-md-5">
      <div class="d-flex align-items-center mb-3 pb-1">
        <div class="me-4">
          <!-- Bootstrap Icon for profile -->
          <i class="bi bi-person-circle" style="font-size: 8rem; color: #343a40;"></i>
        </div>
        <h3 class="fw-bold mb-0" style="font-size: 1.75rem; margin-left: 20px;">Nadira Anindita</h3>
      </div>
      <div class="bg-primary text-white p-4 rounded-3">
        <div class="row gy-3">
          <div class="col-md-4">
            <div class="fw-semibold" style="font-size: 0.9rem;">NPK :</div>
            <div style="font-size: 1.1rem;">7203974538</div>
          </div>
          <div class="col-md-4">
            <div class="fw-semibold" style="font-size: 0.9rem;">No Telp :</div>
            <div style="font-size: 1.1rem;">089876543210</div>
          </div>
          <div class="col-md-4">
            <div class="fw-semibold" style="font-size: 0.9rem;">Role :</div>
            <div style="font-size: 1.1rem;">PIC Aset</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- ====== PROFILE SECTION END ====== -->
</main>


<?php

include '../templates/footer.php';
?>