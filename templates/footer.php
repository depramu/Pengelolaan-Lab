</div>
</div>


<!-- Modal Berhasil -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Berhasil</h5>
                <a href="<?php
                            $currentFile = basename($_SERVER['PHP_SELF']);
                            if ($currentFile == 'tambahBarang.php' || $currentFile == 'editBarang.php') {
                                echo BASE_URL . '/Menu PIC/manajemenBarang.php';
                            } else if ($currentFile == 'tambahRuangan.php' || $currentFile == 'editRuangan.php') {
                                echo BASE_URL . '/Menu PIC/manajemenRuangan.php';
                            } else if ($currentFile == 'tambahPeminjamanBrg.php') {
                                echo BASE_URL . '/Menu Peminjam/Riwayat Barang/riwayatBarang.php';
                            } else if ($currentFile == 'tambahPeminjamanRuangan.php') {
                                echo BASE_URL . '/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php';
                            } else if ($currentFile == 'tambahAkunKry.php' || $currentFile == 'editAkunKry.php') {
                                echo BASE_URL . '/Menu PIC/manajemenAkunKry.php';
                            } else if ($currentFile == 'tambahAkunMhs.php' || $currentFile == 'editAkunMhs.php') {
                                echo BASE_URL . '/Menu PIC/manajemenAkunMhs.php';
                            } else if ($currentFile == 'pengajuanBarang.php') {
                                echo BASE_URL . '/Menu PIC/Peminjaman Barang/peminjamanBarang.php';
                            } else if ($currentFile == 'pengajuanRuangan.php') {
                                echo BASE_URL . '/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php';
                            }
                            ?>"><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></a>
            </div>
            <div class="modal-body">
                <p><?php
                    $currentFile = basename($_SERVER['PHP_SELF']);
                    if ($currentFile == 'tambahBarang.php') {
                        echo 'Data barang berhasil ditambahkan.';
                    } else if ($currentFile == 'editBarang.php') {
                        echo 'Data barang berhasil diperbarui.';
                    } else if ($currentFile == 'tambahRuangan.php') {
                        echo 'Data ruangan berhasil ditambahkan.';
                    } else if ($currentFile == 'editRuangan.php') {
                        echo 'Data ruangan berhasil diperbarui.';
                    } else if ($currentFile == 'tambahPeminjamanBrg.php') {
                        echo 'Data peminjaman barang berhasil ditambahkan.';
                    } else if ($currentFile == 'tambahPeminjamanRuangan.php') {
                        echo 'Data peminjaman ruangan berhasil ditambahkan.';
                    } else if ($currentFile == 'editPeminjaman.php') {
                        echo 'Data peminjaman berhasil diperbarui.';
                    } else if ($currentFile == 'tambahPengembalian.php') {
                        echo 'Data pengembalian berhasil ditambahkan.';
                    } else if ($currentFile == 'editPengembalian.php') {
                        echo 'Data pengembalian berhasil diperbarui.';
                    } else if ($currentFile == 'tambahAkunKry.php') {
                        echo 'Data akun karyawan berhasil ditambahkan.';
                    } else if ($currentFile == 'editAkunKry.php') {
                        echo 'Data akun karyawan berhasil diperbarui.';
                    } else if ($currentFile == 'tambahAkunMhs.php') {
                        echo 'Data akun mahasiswa berhasil ditambahkan.';
                    } else if ($currentFile == 'editAkunMhs.php') {
                        echo 'Data akun mahasiswa berhasil diperbarui.';
                    } else if ($currentFile == 'pengajuanBarang.php') {
                        echo 'Peminjaman barang telah disetujui.';
                    } else if ($currentFile == 'pengajuanRuangan.php') {
                        echo 'Peminjaman ruangan telah disetujui.';
                    }
                    ?></p>
            </div>
            <div class="modal-footer">
                <a href="<?php
                            $currentFile = basename($_SERVER['PHP_SELF']);
                            if ($currentFile == 'tambahBarang.php' || $currentFile == 'editBarang.php') {
                                echo BASE_URL . '/Menu PIC/manajemenBarang.php';
                            } else if ($currentFile == 'tambahRuangan.php' || $currentFile == 'editRuangan.php') {
                                echo BASE_URL . '/Menu PIC/manajemenRuangan.php';
                            } else if ($currentFile == 'tambahPeminjamanBrg.php') {
                                echo BASE_URL . '/Menu Peminjam/Riwayat Barang/riwayatBarang.php';
                            } else if ($currentFile == 'tambahPeminjamanRuangan.php') {
                                echo BASE_URL . '/Menu Peminjam/Riwayat Ruangan/riwayatRuangan.php';
                            } else if ($currentFile == 'tambahPengembalian.php' || $currentFile == 'editPengembalian.php') {
                                echo BASE_URL . '/Menu PIC/manajemenPengembalian.php';
                            } else if ($currentFile == 'tambahAkunKry.php' || $currentFile == 'editAkunKry.php') {
                                echo BASE_URL . '/Menu PIC/manajemenAkunKry.php';
                            } else if ($currentFile == 'tambahAkunMhs.php' || $currentFile == 'editAkunMhs.php') {
                                echo BASE_URL . '/Menu PIC/manajemenAkunMhs.php';
                            } else if ($currentFile == 'pengajuanBarang.php') {
                                echo BASE_URL . '/Menu PIC/Peminjaman Barang/peminjamanBarang.php';
                            } else if ($currentFile == 'pengajuanRuangan.php') {
                                echo BASE_URL . '/Menu PIC/Peminjaman Ruangan/peminjamanRuangan.php';
                            }
                            ?>" class="btn btn-primary">OK</a>
            </div>
        </div>
    </div>
</div>

<!-- modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel"><i><img src="<?= BASE_URL ?>/icon/info.svg" alt="" style="width: 25px; height: 25px; margin-bottom: 5px; margin-right: 10px;"></i>PERINGATAN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Yakin ingin log out?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ps-4 pe-4" data-bs-dismiss="modal">Tidak</button>
                <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary ps-4 pe-4">Ya</a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebar = document.querySelector(".sidebar, .offcanvas-body");
        if (!sidebar) return;

        const storageKey = 'sidebar_active_menus';

        // Fungsi untuk mendapatkan ID submenu yang aktif dari localStorage
        const getActiveMenus = () => {
            const activeMenus = localStorage.getItem(storageKey);
            return activeMenus ? JSON.parse(activeMenus) : [];
        };

        // Fungsi untuk menyimpan ID ke localStorage
        const setActiveMenus = (menus) => {
            localStorage.setItem(storageKey, JSON.stringify(menus));
        };

        // Saat halaman dimuat, buka submenu yang sebelumnya sudah dibuka
        const activeMenuIds = getActiveMenus();
        activeMenuIds.forEach(menuId => {
            const menuElement = document.getElementById(menuId);
            if (menuElement) {
                // Gunakan instance Bootstrap Collapse untuk membukanya
                const collapseInstance = new bootstrap.Collapse(menuElement, {
                    toggle: false // Jangan toggle, hanya buka
                });
                collapseInstance.show();

                // Juga update atribut aria-expanded pada tombolnya
                const trigger = document.querySelector(`[href="#${menuId}"]`);
                if (trigger) trigger.setAttribute('aria-expanded', 'true');
            }
        });

        // Tambahkan event listener untuk semua elemen collapse di sidebar
        const collapsible = sidebar.querySelectorAll('.collapse');
        collapsible.forEach(menu => {
            // Saat submenu akan ditampilkan (dibuka)
            menu.addEventListener('show.bs.collapse', function() {
                let activeMenus = getActiveMenus();
                if (!activeMenus.includes(this.id)) {
                    activeMenus.push(this.id);
                    setActiveMenus(activeMenus);
                }
            });

            // Saat submenu akan disembunyikan (ditutup)
            menu.addEventListener('hide.bs.collapse', function() {
                let activeMenus = getActiveMenus();
                const index = activeMenus.indexOf(this.id);
                if (index > -1) {
                    activeMenus.splice(index, 1);
                    setActiveMenus(activeMenus);
                }
            });
        });
    });
</script>

<?php
?>

<?php if (isset($showModal) && $showModal) : ?>
    <script>
        window.addEventListener('load', function() {
            let modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();
        });
    </script>
<?php endif; ?>

</body>

</html>

<?php if (isset($showModal) && $showModal) : ?>
    <script>
        window.addEventListener('load', function() {
            let modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();
        });
    </script>

<?php endif; ?>

</body>

</html>