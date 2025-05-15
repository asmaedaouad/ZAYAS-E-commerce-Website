<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/HomeController.php';

// Check if user is delivery personnel and redirect to logout
if (isLoggedIn() && isDelivery()) {
    // Logout delivery personnel who try to access the store
    redirect('/views/auth/logout.php');
}

// Set page title
$pageTitle = 'Contact Us';
$customCss = 'contact.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create home controller
$homeController = new HomeController($db);

// Get contact page data
$data = $homeController->contact();

// Include header
include_once '../../includes/header.php';
?>

<!-- Contact Hero Section -->
<section class="contact-page">
    <div class="container">
        <div class="page-header text-center mb-5">
            <h2 class="contact-title">Contact Us</h2>
            <p class="contact-subtitle">We're here to assist you with any inquiries</p>
        </div>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="contact-card">
                    <div class="row">
                        <!-- Left Column - Contact Info -->
                        <div class="col-md-6">
                            <div class="contact-info-wrapper">
                                <div class="contact-info-item">
                                    <div class="icon-wrapper">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h3>Visit Us</h3>
                                        <p><?php echo isset($data['contact_settings']['address']) ? htmlspecialchars($data['contact_settings']['address']) : 'ENSAH (École Nationale des Sciences Appliquées), Al Hoceima, Morocco'; ?></p>
                                    </div>
                                </div>

                                <div class="contact-info-item">
                                    <div class="icon-wrapper">
                                        <i class="fas fa-phone-alt"></i>
                                    </div>
                                    <div>
                                        <h3>Call Us</h3>
                                        <p><?php echo isset($data['contact_settings']['phone']) ? htmlspecialchars($data['contact_settings']['phone']) : '+1 (123) 456-7890'; ?></p>
                                    </div>
                                </div>

                                <div class="contact-info-item">
                                    <div class="icon-wrapper">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div>
                                        <h3>Email Us</h3>
                                        <p><?php echo isset($data['contact_settings']['email']) ? htmlspecialchars($data['contact_settings']['email']) : 'info@zayas.com'; ?></p>
                                    </div>
                                </div>

                                <div class="contact-info-item">
                                    <div class="icon-wrapper">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <h3>Opening Hours</h3>
                                        <p>
                                            <?php if (isset($data['contact_settings']['opening_hours']) && is_array($data['contact_settings']['opening_hours'])): ?>
                                                <?php foreach ($data['contact_settings']['opening_hours'] as $day => $hours): ?>
                                                    <?php echo htmlspecialchars($day) . ': ' . htmlspecialchars($hours); ?><br>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                Monday - Friday: 9:00 AM - 6:00 PM<br>
                                                Saturday: 10:00 AM - 4:00 PM<br>
                                                Sunday: Closed
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="social-links-wrapper">
                                    <h3>Follow Us</h3>
                                    <div class="social-icons">
                                        <a href="<?php echo isset($data['contact_settings']['social_media']['facebook']) ? htmlspecialchars($data['contact_settings']['social_media']['facebook']) : '#'; ?>" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                                        <a href="<?php echo isset($data['contact_settings']['social_media']['instagram']) ? htmlspecialchars($data['contact_settings']['social_media']['instagram']) : '#'; ?>" class="social-icon"><i class="fab fa-instagram"></i></a>
                                        <a href="<?php echo isset($data['contact_settings']['social_media']['pinterest']) ? htmlspecialchars($data['contact_settings']['social_media']['pinterest']) : '#'; ?>" class="social-icon"><i class="fab fa-pinterest-p"></i></a>
                                        <a href="<?php echo isset($data['contact_settings']['social_media']['twitter']) ? htmlspecialchars($data['contact_settings']['social_media']['twitter']) : '#'; ?>" class="social-icon"><i class="fab fa-twitter"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Map -->
                        <div class="col-md-6">
                            <div class="map-wrapper">
                                <h3>Find Us on the Map</h3>
                                <div class="map-container">
                                    <iframe src="<?php echo isset($data['contact_settings']['map_embed_url']) ? htmlspecialchars($data['contact_settings']['map_embed_url']) : 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d52872.95042542127!2d-3.9535259242187465!3d35.24594499999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd77b93bb6a8f7bf%3A0x6c398c97db1a5994!2sAl%20Hoceima!5e0!3m2!1sen!2sma!4v1716051600000!5m2!1sen!2sma'; ?>" width="100%" height="300" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include_once '../../includes/footer.php';
?>

<!-- Contact Page JavaScript -->
<script src="<?php echo url('/public/js/contact.js'); ?>"></script>
