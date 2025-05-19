<footer class="footer py-5 text-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <h5 class="mb-4">ZAYAS</h5>
                <p>Discover the perfect blend of tradition and contemporary style with our curated collection of Islamic fashion.</p>
            </div>

            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="mb-4">Shop</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?php echo url('/views/home/shop.php?type=abaya'); ?>" class="text-white-50">Abayas</a></li>
                    <li class="mb-2"><a href="<?php echo url('/views/home/shop.php?type=dress'); ?>" class="text-white-50">Dresses</a></li>
                    <li class="mb-2"><a href="<?php echo url('/views/home/shop.php?type=hijab'); ?>" class="text-white-50">Hijabs</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="mb-4">Help</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?php echo url('/views/home/about.php'); ?>" class="text-white-50">About Us</a></li>
                    <li class="mb-2"><a href="<?php echo url('/views/home/contact.php'); ?>" class="text-white-50">Contact Us</a></li>
                </ul>
            </div>
        </div>

        <hr class="my-5 bg-secondary">

        <div class="row align-items-center">
            <div class="col-12 text-center">
                <div class="social-icons d-flex justify-content-center">
                    <?php
                    // Load contact settings for social media links
                    $contactSettingsFile = __DIR__ . '/../config/contact_settings.php';
                    $contactSettings = file_exists($contactSettingsFile) ? include $contactSettingsFile : [];
                    $socialMedia = isset($contactSettings['social_media']) ? $contactSettings['social_media'] : [
                        'facebook' => '#',
                        'twitter' => '#',
                        'instagram' => '#',
                        'pinterest' => '#'
                    ];
                    ?>
                    <a href="<?php echo htmlspecialchars($socialMedia['facebook']); ?>" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?php echo htmlspecialchars($socialMedia['twitter']); ?>" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="<?php echo htmlspecialchars($socialMedia['instagram']); ?>" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="<?php echo htmlspecialchars($socialMedia['pinterest']); ?>" class="social-icon"><i class="fab fa-pinterest"></i></a>
                </div>
                <p class="small mt-3">© <?php echo date('Y'); ?> ZAYAS. All rights reserved.</p>
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
<script src="<?php echo url('/public/js/logout-confirmation.js'); ?>"></script>
<script src="<?php echo url('/public/js/password-toggle.js'); ?>"></script>
<?php if (isset($pageTitle) && $pageTitle === 'Home'): ?>
<!-- Home page specific JS -->
<script src="<?php echo url('/public/js/home.js'); ?>"></script>
<?php endif; ?>

<!-- Scroll Position Handling -->
<script>
// Save scroll position before form submission
document.addEventListener('DOMContentLoaded', function() {
    // Check if we need to restore scroll position
    <?php if (isset($_SESSION['scroll_position'])): ?>
    window.scrollTo(0, <?php echo $_SESSION['scroll_position']; ?>);
    <?php
    // Clear the scroll position from session after using it
    unset($_SESSION['scroll_position']);
    ?>
    <?php endif; ?>

    // Add event listeners to all wishlist and cart forms
    const wishlistAddForms = document.querySelectorAll('form[action*="wishlist/add.php"]');
    const wishlistRemoveForms = document.querySelectorAll('form[action*="wishlist/remove.php"]');
    const cartForms = document.querySelectorAll('form[action*="cart/add.php"]');

    // Function to save scroll position
    function saveScrollPosition(e) {
        // Create a hidden input field to store scroll position
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'scroll_position';
        input.value = window.pageYOffset || document.documentElement.scrollTop;
        this.appendChild(input);
    }

    // Add event listener to wishlist add forms
    wishlistAddForms.forEach(form => {
        form.addEventListener('submit', saveScrollPosition);
    });

    // Add event listener to wishlist remove forms
    wishlistRemoveForms.forEach(form => {
        form.addEventListener('submit', saveScrollPosition);
    });

    // Add event listener to cart forms
    cartForms.forEach(form => {
        form.addEventListener('submit', saveScrollPosition);
    });
});
</script>
</body>
</html>