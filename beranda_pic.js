document.addEventListener('DOMContentLoaded', function () {
    const submenuToggles = document.querySelectorAll('.main-menu-toggle');

    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function () {
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