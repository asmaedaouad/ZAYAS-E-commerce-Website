    </div>
    <!-- End Main Content -->

    <!-- Footer -->
    <footer class="bg-brown text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p>&copy; <?php echo date('Y'); ?> ZAYAS Delivery. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <?php if (isset($customJs)): ?>
    <script src="<?php echo url('/delivery/public/js/' . $customJs); ?>"></script>
    <?php endif; ?>

    <!-- Logout confirmation script -->
    <script src="<?php echo url('/public/js/logout-confirmation.js'); ?>"></script>
</body>
</html>
