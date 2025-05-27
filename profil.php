<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sistem Pengelolaan Laboratorium</title>

  <style>
    /* Reset and base styles */
    html,
    body {
      height: 100%;
      overflow: hidden;
    }

    body {
      font-family: "Poppins-Regular", sans-serif;
      background: #f9fafc;
      margin: 0;
      padding: 0;
    }

    a,
    button,
    input,
    select,
    h1,
    h2,
    h3,
    h4,
    h5,
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      border: none;
      text-decoration: none;
      background: none;
      -webkit-font-smoothing: antialiased;
    }

    menu,
    ol,
    ul {
      list-style-type: none;
      margin: 0;
      padding: 0;
    }

    /* Layout */
    .dashboard-pic {
      padding: 20px;
      height: 100vh;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    /* Header */
    .menu-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 20px;
      margin-bottom: 20px;
    }

    .logo {
      width: 220px;
      height: auto;
      object-fit: contain;
    }

    .user-profile-icons {
      display: flex;
      align-items: center;
    }

    .icon-notification,
    .icon-profile {
      width: 32px;
      height: 32px;
      margin-left: 20px;
      cursor: pointer;
    }

    /* Main content wrapper */
    .main-content-wrapper {
      flex: 1;
      display: flex;
      gap: 20px;
      overflow: hidden;
    }

    /* Sidebar */
    .sidebar {
      background: #065ba6;
      border-radius: 15px;
      width: 280px;
      padding: 20px;
      display: flex;
      flex-direction: column;
      color: #ffffff;
    }

    .menu-item-wrapper {
      margin-bottom: 15px;
    }

    .menu-link {
      display: flex;
      align-items: center;
      padding: 12px 15px;
      color: #ffffff;
      font-size: 18px;
      text-decoration: none;
      border-radius: 8px;
      transition: background-color 0.2s ease, padding-left 0.2s ease;
    }

    .menu-link:hover,
    .menu-item-wrapper.active .main-menu-toggle {
      background-color: rgba(255, 255, 255, 0.1);
      padding-left: 20px;
    }

    .menu-icon {
      width: 24px;
      height: 24px;
      margin-right: 15px;
      object-fit: contain;
    }

    /* Specific styling for vector3.svg (logout icon) */
    .menu-icon[src*="vector3.svg"] {
      width: 20px;
      height: 20px;
    }

    /* Ensure report icon matches other icons */
    .menu-icon[src*="graph-report0.png"] {
      width: 40px;
      height: 40px;
      object-fit: contain;
      margin-right: 9px;
      margin-left: -8px;
    }

    .menu-text {
      flex-grow: 1;
      font-family: "Poppins-Regular", sans-serif;
    }

    .menu-item-wrapper a.menu-link .menu-text,
    .menu-item-wrapper.active .main-menu-toggle .menu-text {
      font-family: "Poppins-SemiBold", sans-serif;
      font-weight: 600;
    }

    .arrow-icon {
      width: 16px;
      height: 16px;
      margin-left: auto;
      transition: transform 0.3s ease;
    }

    .submenu {
      display: none;
      padding-left: 39px;
      margin-top: 8px;
    }

    .submenu li a {
      display: block;
      padding: 8px 10px;
      color: #e0e0e0;
      font-size: 16px;
      text-decoration: none;
      border-radius: 6px;
      transition: background-color 0.2s ease, color 0.2s ease;
    }

    .submenu li a:hover {
      background-color: rgba(255, 255, 255, 0.15);
      color: #ffffff;
    }

    /* Content area */
    .content-area {
      background: #ffffff;
      border-radius: 15px;
      flex-grow: 1;
      padding: 30px 40px;
      position: relative;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .welcome-message {
      color: #065ba6;
      font-family: "Poppins-Regular", sans-serif;
      font-size: 48px;
      line-height: 1.2;
      font-weight: 600;
      flex-grow: 1;
    }

    .atoy {
      width: 160px;
      height: auto;
      object-fit: contain;
      position: absolute;
      right: 70px;
      bottom: 30px;
    }

    /* Greeting header */
    .greeting-header {
      margin-left: -50%;
    }

    .hello {
      color: #3a3a3a;
      font-family: "Poppins-Regular", sans-serif;
      font-size: 28px;
      line-height: 1;
    }

    .pengguna {
      color: #000000;
      font-family: "Poppins-Regular", sans-serif;
      font-size: 18px;
      line-height: 1;
      margin-top: 5px;
    }

    .profile-content-area {
      background: #f9fafc;
      min-height: 500px;
      border-radius: 15px;
      padding: 40px 40px 40px 40px;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    .profile-breadcrumb {
      font-size: 16px;
      color: #1976d2;
      margin-bottom: 18px;
    }

    .profile-breadcrumb .profile-breadcrumb-link {
      cursor: pointer;
      text-decoration: underline;
      color: #1976d2;
    }

    .profile-breadcrumb .profile-breadcrumb-current {
      color: #000;
      text-decoration: none;
    }

    .profile-title {
      font-size: 40px;
      font-weight: bold;
      color: #111;
      margin-bottom: 32px;
      font-family: 'Poppins', sans-serif;
    }

    .profile-header {
      display: flex;
      align-items: center;
      margin-bottom: 40px;
    }

    .profile-avatar {
      margin-top: 30;
      width: 140px;
      height: 140px;
      min-width: 140px;
      min-height: 140px;
      border-radius: 50%;
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 8px solid #111;
    }

    .profile-name {
      margin-left: 40px;
      font-size: 32px;
      font-weight: 600;
      color: #111;
      font-family: 'Poppins', sans-serif;
    }

    .profile-info-box {
      background: #065ba6;
      border-radius: 18px;
      padding: 32px 0px 30px 60px;
      display: flex;
      gap: 50px;
      color: #fff;
      font-family: 'Poppins', sans-serif;
      max-width: 700px;
      margin-top: 75px;
    }

    .profile-info-col {
      flex: 1;
      min-width: 120px;
    }

    .profile-info-label {
      font-weight: 600;
      font-size: 18px;
      margin-bottom: 8px;
    }

    .profile-info-value {
      font-size: 20px;
      font-weight: 400;
    }
  </style>
</head>

<body>
  <div class="dashboard-pic">
    <div class="menu-container">
      <img class="logo" src="icon/logo0.png" />
      <div class="greeting-header">
        <div class="hello">Hello,</div>
        <div class="pengguna">Nadira Anindita (PIC)</div>
      </div>
      <div class="user-profile-icons">
        <a href="notif.php">
          <img class="icon-notification" src="icon/bell.png" />
        </a>
        <a href="profil.php">
          <img class="icon-profile" src="icon/vector0.svg" />
        </a>
      </div>
    </div>

    <div class="main-content-wrapper">
      <!-- SIDEBAR START -->
      <div class="sidebar">
        <div class="component-40">
          <div class="menu-item-wrapper">
            <a href="template.php" class="menu-link">
              <img class="menu-icon" src="icon/dashboard0.svg" />
              <span class="menu-text">Dashboard</span>
            </a>
          </div>

          <div class="menu-item-wrapper has-submenu">
            <div class="menu-link main-menu-toggle">
              <img class="menu-icon" src="icon/layers0.png" />
              <span class="menu-text">Manajemen Aset</span>
              <img class="arrow-icon" src="icon/drop-up0.svg" />
            </div>
            <ul class="submenu">
              <li><a href="#">Barang</a></li>
              <li><a href="#">Ruangan</a></li>
            </ul>
          </div>

          <div class="menu-item-wrapper has-submenu">
            <div class="menu-link main-menu-toggle">
              <img class="menu-icon" src="icon/iconamoon-profile-fill0.svg" />
              <span class="menu-text">Manajemen Akun</span>
              <img class="arrow-icon" src="icon/drop-up2.svg" />
            </div>
            <ul class="submenu">
              <li><a href="#">Mahasiswa</a></li>
              <li><a href="#">Karyawan</a></li>
            </ul>
          </div>

          <div class="menu-item-wrapper has-submenu">
            <div class="menu-link main-menu-toggle">
              <img class="menu-icon" src="icon/ic-twotone-sync-alt0.svg" />
              <span class="menu-text">Peminjaman</span>
              <img class="arrow-icon" src="icon/drop-up1.svg" />
            </div>
            <ul class="submenu">
              <li><a href="#">Barang</a></li>
              <li><a href="#">Ruangan</a></li>
            </ul>
          </div>

          <div class="menu-item-wrapper">
            <a href="#" class="menu-link">
              <img class="menu-icon" src="icon/graph-report0.png" />
              <span class="menu-text">Laporan</span>
            </a>
          </div>

          <div class="menu-item-wrapper">
            <a href="#" class="menu-link">
              <img class="menu-icon" src="icon/exit.png" />
              <span class="menu-text">Log Out</span>
            </a>
          </div>
        </div>
      </div>
      <!-- SIDEBAR END -->

      <!-- CONTENT AREA TEMPLATE -->
      <!-- ===================== -->
      <!-- This section is a template that can be replaced with different content -->
      <!-- You can create separate PHP files for each page content and include them here -->
      <!-- Example: include 'pages/dashboard.php' or include 'pages/manajemen_aset.php' -->
      <div class="content-area">
          <div class="profile-breadcrumb">
            <span class="profile-breadcrumb-link"><a href="template.php">Sistem Pengelolaan Lab</a></span> <span class="profile-breadcrumb-current">/ Profile</span>
          </div>
          <div class="profile-title">Data Akun</div>
          <div class="profile-header">
            <div class="profile-avatar">
              <svg width="90" height="90" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="8" r="5" />
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
              </svg>
            </div>
            <div class="profile-name">Nadira Anindita</div>
          </div>
          <div class="profile-info-box">
            <div class="profile-info-col">
              <div class="profile-info-label">NPK :</div>
              <div class="profile-info-value">7203974538</div>
            </div>
            <div class="profile-info-col">
              <div class="profile-info-label">No Telp :</div>
              <div class="profile-info-value">089876543210</div>
            </div>
            <div class="profile-info-col">
              <div class="profile-info-label">Role :</div>
              <div class="profile-info-value">PIC Aset</div>
            </div>
          </div>
        </div>
      </div>
      <!-- END CONTENT AREA TEMPLATE -->

    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const submenuToggles = document.querySelectorAll('.main-menu-toggle');

      submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
          // Toggle kelas 'active' pada parent (.menu-item-wrapper)
          this.parentElement.classList.toggle('active');

          // Toggle submenu
          const submenu = this.nextElementSibling;
          if (submenu && submenu.classList.contains('submenu')) {
            if (submenu.style.display === 'block') {
              submenu.style.display = 'none';
            } else {
              submenu.style.display = 'block';
            }
          }

          // Toggle panah (putar 180 derajat jika 'active')
          const arrow = this.querySelector('.arrow-icon');
          if (arrow) {
            if (this.parentElement.classList.contains('active')) {
              arrow.style.transform = 'rotate(0deg)'; // Panah ke atas (drop-up0.svg)
            } else {
              arrow.style.transform = 'rotate(180deg)'; // Panah ke bawah
            }
          }
        });

        // Set initial state for arrows (all submenus closed, so arrows point down)
        const arrow = toggle.querySelector('.arrow-icon');
        if (arrow && !toggle.parentElement.classList.contains('active')) {
          arrow.style.transform = 'rotate(180deg)';
        }
      });
    });
  </script>
</body>

</html>