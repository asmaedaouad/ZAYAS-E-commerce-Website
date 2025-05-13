<footer class="footer py-5 text-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <h5 class="mb-4">ZAYAS</h5>
                <p>Discover the perfect blend of tradition and contemporary style with our curated collection of Islamic fashion.</p>
                <div class="social-icons mt-4">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                <h5 class="mb-4">Shop</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?php echo url('/views/home/shop.php?type=abaya'); ?>" class="text-white-50">Abayas</a></li>
                    <li class="mb-2"><a href="<?php echo url('/views/home/shop.php?type=dress'); ?>" class="text-white-50">Dresses</a></li>
                    <li class="mb-2"><a href="<?php echo url('/views/home/shop.php?type=hijab'); ?>" class="text-white-50">Hijabs</a></li>
                    <li class="mb-2"><a href="<?php echo url('/views/home/shop.php?is_new=1'); ?>" class="text-white-50">New Arrivals</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                <h5 class="mb-4">Help</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?php echo url('/views/home/about.php'); ?>" class="text-white-50">About Us</a></li>
                    <li class="mb-2"><a href="<?php echo url('/views/home/contact.php'); ?>" class="text-white-50">Contact Us</a></li>
                    <li><a href="#" class="text-white-50">FAQ</a></li>
                </ul>
            </div>

            <div class="col-lg-4">
                <h5 class="mb-4">Newsletter</h5>
                <p>Subscribe to get updates on new arrivals and special offers.</p>
                <form class="newsletter-form mt-4">
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Your email" required>
                        <button class="btn btn-primary" type="submit">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>

        <hr class="my-5 bg-secondary">

        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="small mb-0">© 2025 ZAYAS. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <ul class="list-inline small mb-0">
                    <li class="list-inline-item"><a href="#" class="text-white-50">Privacy Policy</a></li>
                    <li class="list-inline-item"><a href="#" class="text-white-50">Terms of Service</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Font Awesome JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<!-- Custom JS -->
<script src="<?php echo url('/public/js/header.js'); ?>"></script>
<?php if (isset($pageTitle) && $pageTitle === 'Home'): ?>
<!-- Home page specific JS -->
<script src="<?php echo url('/public/js/home.js'); ?>"></script>
<?php endif; ?>
</body>
</html>