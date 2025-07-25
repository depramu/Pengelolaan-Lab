<?php
require_once __DIR__ . '/../../function/init.php'; // Penyesuaian: gunakan init.php untuk inisialisasi dan otorisasi
authorize_role('PIC Aset'); // Lindungi halaman ini untuk role 'PIC Aset'

// Ambil daftar lokasi unik untuk filter Data Barang
$lokasiList = [];
$lokasiQuery = "SELECT DISTINCT lokasiBarang FROM Barang WHERE isDeleted = 0 ORDER BY lokasiBarang ASC";
$lokasiResult = sqlsrv_query($conn, $lokasiQuery);
if ($lokasiResult !== false) {
    while ($rowLokasi = sqlsrv_fetch_array($lokasiResult, SQLSRV_FETCH_ASSOC)) {
        if (!empty($rowLokasi['lokasiBarang'])) {
            $lokasiList[] = $rowLokasi['lokasiBarang'];
        }
    }
}

// Ambil daftar kondisi unik untuk filter Data Ruangan
$kondisiList = [];
$kondisiQuery = "SELECT DISTINCT kondisiRuangan FROM Ruangan ORDER BY kondisiRuangan ASC";
$kondisiResult = sqlsrv_query($conn, $kondisiQuery);
if ($kondisiResult !== false) {
    while ($rowKondisi = sqlsrv_fetch_array($kondisiResult, SQLSRV_FETCH_ASSOC)) {
        if (!empty($rowKondisi['kondisiRuangan'])) {
            $kondisiList[] = $rowKondisi['kondisiRuangan'];
        }
    }
}

// Tambahkan file pagination.php agar fungsi pagination bisa digunakan
require_once __DIR__ . '/../../function/pagination.php';

include '../../templates/header.php';
include '../../templates/sidebar.php';
?>

<main class="col bg-white px-3 px-md-4 py-3 position-relative">

  <!-- Judul Statis Halaman -->
  <h3 class="fw-semibold mb-3">Laporan</h3>

  <!-- Breadcrumb -->
  <div class="mb-3">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/Menu PIC/dashboardPIC.php">Sistem Pengelolaan Lab</a></li>
        <li class="breadcrumb-item active" aria-current="page">Laporan</li>
      </ol>
    </nav>
  </div>

  <!-- Card untuk Filter Laporan -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="card-title mb-3">Filter Laporan</h5>
      <div class="row g-3 align-items-end" id="filterRow">
        <div class="col-md-4" id="colJenis">
          <label for="jenisLaporan" class="form-label">Jenis Laporan</label>
          <select class="form-select" id="jenisLaporan">
            <option selected hidden value="">Pilih Jenis Laporan...</option>
            <option value="dataBarang">Data Barang</option>
            <option value="dataRuangan">Data Ruangan</option>
            <option value="peminjamSeringMeminjam">Peminjam yang Sering Meminjam</option>
            <option value="barangSeringDipinjam">Barang yang Sering Dipinjam</option>
            <option value="ruanganSeringDipinjam">Ruangan yang Sering Dipinjam</option>
          </select>
        </div>
        <!-- Filter Lokasi Barang (khusus Data Barang) -->
        <div class="col-md-3" id="colLokasiBarang" style="display:none;">
          <label for="lokasiBarangFilter" class="form-label">Lokasi Barang</label>
          <select class="form-select" id="lokasiBarangFilter">
            <option selected value="">Semua Lokasi</option>
            <?php foreach ($lokasiList as $lokasi): ?>
              <option value="<?= htmlspecialchars($lokasi) ?>"><?= htmlspecialchars($lokasi) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <!-- Filter Kondisi Ruangan (khusus Data Ruangan) -->
        <div class="col-md-3" id="colKondisiRuangan" style="display:none;">
          <label for="kondisiRuanganFilter" class="form-label">Kondisi Ruangan</label>
          <select class="form-select" id="kondisiRuanganFilter">
            <option selected value="">Semua Kondisi</option>
            <?php foreach ($kondisiList as $kondisi): ?>
              <option value="<?= htmlspecialchars($kondisi) ?>"><?= htmlspecialchars($kondisi) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3" id="colBulan">
          <label for="bulanLaporan" class="form-label">Bulan</label>
          <select class="form-select" id="bulanLaporan">
            <option selected hidden value="">Pilih Bulan...</option>
            <?php
            $bulan = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
            foreach ($bulan as $num => $nama) echo "<option value=\"{$num}\">{$nama}</option>";
            ?>
          </select>
        </div>
        <div class="col-md-3" id="colTahun">
          <label for="tahunLaporan" class="form-label">Tahun</label>
          <select class="form-select" id="tahunLaporan">
            <option selected hidden value="">Pilih Tahun...</option>
            <?php
            $currentYear = date('Y');
            for ($i = 0; $i < 5; $i++) echo "<option value=\"" . ($currentYear - $i) . "\">" . ($currentYear - $i) . "</option>";
            ?>
          </select>
        </div>
        <div class="col-md-2 d-flex" id="colBtn">
          <button class="btn btn-primary w-100" id="tampilkanLaporanBtn">
            <i class="bi bi-search me-1"></i> Tampilkan
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- Akhir Card Filter Laporan -->

  <!-- Area Konten Laporan (Wadah untuk Tabel) -->
  <div id="areaKontenLaporan" style="display:none;">
    <div id="wadahLaporan" class="table-responsive">
      <!-- Tabel akan dirender oleh JavaScript di sini -->
    </div>
    <!-- Pagination PHP (server-side) jika ingin digunakan secara statis, bisa dipanggil di sini -->
    <?php
    // Contoh penggunaan generatePagination jika ingin server-side (AJAX/JS tetap bisa handle client-side)
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $totalPages = 1; // Ganti dengan total halaman hasil query
    generatePagination($page, $totalPages);
    ?>
  </div>

  <div id="bottomControlsContainer" class="d-flex justify-content-between align-items-end mt-0">
    <div id="leftControls">
      <div id="laporanSummaryText" class="mb-0" style="font-weight: 500;">
        <!-- Keterangan summary akan muncul di sini -->
      </div>
      <nav aria-label="Page navigation" id="paginationControlsContainer">
        <ul class="pagination mb-0" id="paginationUl">
          <!-- Tombol paginasi akan muncul di sini (client-side JS) -->
        </ul>
      </nav>
    </div>
    <div id="rightControls">
      <button class="btn btn-success" id="exportExcelBtn" style="display:none;">
        <i class="bi bi-file-earmark-excel me-0"></i> Export ke Excel
      </button>
    </div>
  </div>
</main>
<!-- Akhir Area Konten Utama -->

<!-- Modal untuk Peringatan Validasi -->
<div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="validationModalLabel">
          <i><img src="<?= BASE_URL ?>/icon/info.svg" alt="Ikon Info" style="width:25px;height:25px;margin-right:10px;"></i> PERINGATAN
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="validationMessage"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const jenisLaporan = document.getElementById('jenisLaporan');
  const colLokasiBarang = document.getElementById('colLokasiBarang');
  const colKondisiRuangan = document.getElementById('colKondisiRuangan');
  const colBulan = document.getElementById('colBulan');
  const colTahun = document.getElementById('colTahun');

  function updateFilterVisibility() {
    const value = jenisLaporan.value;
    // Sembunyikan semua filter khusus dulu
    colLokasiBarang.style.display = 'none';
    colKondisiRuangan.style.display = 'none';

    // Tampilkan filter sesuai jenis laporan
    if (value === 'dataBarang') {
      colLokasiBarang.style.display = '';
    } else if (value === 'dataRuangan') {
      colKondisiRuangan.style.display = '';
    }
    // Filter bulan/tahun tetap tampil untuk semua jenis laporan
  }

  jenisLaporan.addEventListener('change', updateFilterVisibility);

  // Inisialisasi tampilan filter saat load
  updateFilterVisibility();
});
</script>
    
<?php include '../../templates/footer.php';
