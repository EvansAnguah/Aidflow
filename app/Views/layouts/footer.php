<?php
use App\Core\Session;
?>
<?php if (Session::isLoggedIn()): ?>
        </div> <!-- End content-body -->
        <footer class="text-center py-4 mt-auto border-top bg-white text-muted small">
            &copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.
        </footer>
    </div> <!-- End main-wrapper -->
</div> <!-- End d-flex -->
<?php endif; ?>

<!-- Core JavaScript Libraries -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Main App Javascript -->
<script src="<?= BASE_URL ?>/public/js/main.js"></script>
</body>
</html>
