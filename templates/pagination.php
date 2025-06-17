<!-- Pagination -->
<nav aria-label="Page navigation" class="fixed-pagination">
    <ul class="pagination justify-content-end">
        <!-- Previous button -->
        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page - 1 ?>" tabindex="-1">&lt;</a>
        </li>
        <!-- Page numbers -->
        <?php
        $showPages = 3; // Jumlah halaman yang selalu tampil di awal dan akhir
        $ellipsisShown = false;
        for ($i = 1; $i <= $totalPages; $i++) {
            if (
                $i <= $showPages || // always show first 3
                $i > $totalPages - $showPages || // always show last 3
                abs($i - $page) <= 1 // show current, previous, next
            ) {
                $ellipsisShown = false;
        ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
        <?php
            } elseif (!$ellipsisShown) {
                // Show ellipsis only once
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                $ellipsisShown = true;
            }
        }
        ?>
        <!-- Next button -->
        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?>">&gt;</a>
        </li>
    </ul>
</nav>