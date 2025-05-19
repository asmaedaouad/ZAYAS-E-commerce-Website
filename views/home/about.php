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
$pageTitle = 'About Us';
$customCss = 'about.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create home controller
$homeController = new HomeController($db);

// Get about page data
$data = $homeController->about();

// Include header
include_once '../../includes/header.php';
?>

<!-- About Hero Section -->
<section class="about-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="about-content animate-on-scroll">
                    <h1 class="about-title">About ZAYAS</h1>
                    <p class="about-subtitle">Elegant Modest Fashion for Every Occasion</p>
                    <p class="about-text">
                        ZAYAS was founded with a simple mission: to provide elegant, high-quality modest fashion that empowers women to express their unique style while honoring their values.
                    </p>
                    <p class="about-text">
                        Our journey began in 2020 when our founder, Sarah Zayas, recognized the need for modest clothing that doesn't compromise on style or quality. What started as a small collection of abayas has grown into a comprehensive range of modest fashion pieces, including dresses, hijabs, and accessories.
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-image animate-on-scroll">
                    <img src="<?php echo url('/public/images/about-hero.jpg'); ?>" alt="About ZAYAS" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Values Section -->
<section class="our-values section-padding">
    <div class="container">
        <h2 class="section-title">Our Values</h2>
        <p class="section-subtitle">What drives us every day</p>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="value-card animate-on-scroll">
                    <div class="value-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="value-title">Quality</h3>
                    <p class="value-text">
                        We believe in creating garments that stand the test of time, both in style and durability. Each piece is crafted with attention to detail and made from premium fabrics.
                    </p>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="value-card animate-on-scroll">
                    <div class="value-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3 class="value-title">Sustainability</h3>
                    <p class="value-text">
                        We're committed to reducing our environmental impact by using sustainable materials and ethical manufacturing processes whenever possible.
                    </p>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="value-card animate-on-scroll">
                    <div class="value-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="value-title">Inclusivity</h3>
                    <p class="value-text">
                        We design for women of all backgrounds, celebrating diversity and creating pieces that make everyone feel confident and beautiful.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Story Section -->
<section class="our-story section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2">
                <div class="story-content animate-on-scroll">
                    <h2 class="section-title">Our Story</h2>
                    <p class="story-text">
                        The journey of ZAYAS began with a simple observation: modest fashion shouldn't mean compromising on style or quality. Our founder, Sarah Zayas, struggled to find clothing that aligned with her values while still feeling contemporary and fashionable.
                    </p>
                    <p class="story-text">
                        Drawing on her background in fashion design, Sarah created a small collection of abayas that quickly gained popularity among friends and family. Encouraged by the positive response, she expanded the collection to include a wider range of modest fashion pieces.
                    </p>
                    <p class="story-text">
                        Today, ZAYAS has grown into a beloved brand known for its elegant designs, quality craftsmanship, and commitment to empowering women through fashion. We continue to innovate and expand our offerings while staying true to our core values.
                    </p>
                </div>
            </div>
            <div class="col-lg-6 order-lg-1">
                <div class="story-image animate-on-scroll">
                    <img src="<?php echo url('/public/images/our-story.jpg'); ?>" alt="Our Story" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="our-team section-padding">
    <div class="container">
        <h2 class="section-title">Meet Our Team</h2>
        <p class="section-subtitle">The people behind ZAYAS</p>

        <div class="row">
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="team-member animate-on-scroll">
                    <div class="member-info">
                        <h3 class="member-name">Asmae Daouad</h3>
                        <p class="member-role">Founder & Creative Director</p>
                        <p class="member-bio">
                            With extensive experience in fashion design, Asmae brings her creative vision and passion for modest fashion to every aspect of ZAYAS.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 mb-4">
                <div class="team-member animate-on-scroll">
                    <div class="member-info">
                        <h3 class="member-name">Zaynab Ait Addi</h3>
                        <p class="member-role">Head of Design</p>
                        <p class="member-bio">
                            Zaynab's innovative designs and attention to detail have helped shape the distinctive ZAYAS aesthetic that our customers love.
                        </p>
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

<!-- About Page JavaScript -->
<script src="<?php echo url('/public/js/about.js'); ?>"></script>
