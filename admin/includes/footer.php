            </div> <!-- End of page-content -->
        </div> <!-- End of main-content -->
    </div> <!-- End of admin-container -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="<?php echo url('/public/js/logout-confirmation.js'); ?>"></script>
    <script src="<?php echo url('/admin/assets/js/admin.js'); ?>"></script>
    <script src="<?php echo url('/public/js/password-toggle.js'); ?>"></script>

    <?php if (isset($customJs)): ?>
    <script src="<?php echo url('/admin/assets/js/' . $customJs); ?>"></script>
    <?php endif; ?>
</body>
</html>
