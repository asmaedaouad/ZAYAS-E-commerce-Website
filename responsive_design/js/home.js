// Home page JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap carousel with custom settings
    const heroCarousel = document.getElementById('heroCarousel');
    if (heroCarousel) {
        const carousel = new bootstrap.Carousel(heroCarousel, {
            interval: 6000,  // Slide every 6 seconds
            pause: 'hover',  // Pause on hover
            wrap: true,      // Continuous loop
            keyboard: true   // Allow keyboard navigation
        });

        // Handle video playback in carousel
        heroCarousel.addEventListener('slide.bs.carousel', function(event) {
            // Pause all videos when sliding
            const allVideos = document.querySelectorAll('.hero-video');
            allVideos.forEach(video => {
                if (video) video.pause();
            });
        });

        heroCarousel.addEventListener('slid.bs.carousel', function(event) {
            // Play video in active slide if it exists
            const activeSlide = heroCarousel.querySelector('.carousel-item.active');
            const video = activeSlide.querySelector('.hero-video');
            if (video) {
                video.play();
            }
        });

        // Play video in first slide on page load
        const firstSlideVideo = heroCarousel.querySelector('.carousel-item.active .hero-video');
        if (firstSlideVideo) {
            firstSlideVideo.play();
        }
    }

    // Smooth hover effects for category cards
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.classList.add('hover');
        });

        card.addEventListener('mouseleave', function() {
            this.classList.remove('hover');
        });
    });

    // Lazy loading for product images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.getAttribute('data-src');
                    if (src) {
                        img.src = src;
                        img.removeAttribute('data-src');
                    }
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('.product-img img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // We're using standard form submission for cart functionality
    // No JavaScript event handler needed

    // We're using standard form submission for wishlist functionality
    // No JavaScript event handler needed

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId !== '#' && document.querySelector(targetId)) {
                e.preventDefault();
                document.querySelector(targetId).scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});
