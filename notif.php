<?php

include 'templates/header.php';
include 'templates/sidebar.php';

?>

<!-- Content Area -->
<main class="col bg-white px-3 px-md-4 py-3 position-relative">
    <div class="mb-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
                <li class="breadcrumb-item active" aria-current="page">Notifikasi</li>
            </ol>
        </nav>
    </div>
    <div class="container">
                    <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu</th>
                                <th>Pesan</th>
                            </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>12-06-2025 12:00</td>
                            <td>Pengingat: Peminjaman alat harus dikembalikan sebelum pukul 17:00.</td>
                          </tr>
                        </tbody>
                    </table>

</main>
</div>
</div>

<?php
include 'templates/footer.php';
?>