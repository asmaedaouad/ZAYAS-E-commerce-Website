document.addEventListener('DOMContentLoaded', function() {
    // Initialize carousel
    const myCarousel = new bootstrap.Carousel(document.getElementById('mainCarousel'), {
        interval: 6000, // Increased interval to 6 seconds
        ride: 'carousel',
        pause: 'hover', // Pause on hover
        wrap: true, // Infinite looping
        touch: true // Enable touch swiping
    });
    
    // Play videos when slide becomes active
    document.getElementById('mainCarousel').addEventListener('slid.bs.carousel', function (e) {
        const activeSlide = e.relatedTarget;
        const video = activeSlide.querySelector('video');
        if (video) {
            video.currentTime = 0;
            video.play();
        }
    });
    
    // Pause videos when slide changes
    document.getElementById('mainCarousel').addEventListener('slide.bs.carousel', function (e) {
        const currentSlide = e.from;
        const video = currentSlide.querySelector('video');
        if (video) {
            video.pause();
        }
    });
    // Handle window resize for video repositioning
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            const activeVideo = document.querySelector('.carousel-item.active video');
            if (activeVideo) {
                activeVideo.style.display = 'none';
                void activeVideo.offsetWidth; // Trigger reflow
                activeVideo.style.display = 'block';
            }
        }, 250);
    });
    // Add smooth scrolling to all links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Wishlist button functionality
    document.querySelectorAll('.wishlist-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const icon = this.querySelector('i');
            icon.classList.toggle('far');
            icon.classList.toggle('fas');
            
            // Update header wishlist icon if item is added
            if (icon.classList.contains('fas')) {
                const headerWishlist = document.querySelector('#wishlist-icon i');
                headerWishlist.classList.add('fas');
                headerWishlist.classList.remove('far');
                
                // Show notification
                showNotification('Item added to your wishlist');
            }
        });
    });
    
    // Header wishlist icon click
    const headerWishlist = document.getElementById('wishlist-icon');
    if (headerWishlist) {
        headerWishlist.addEventListener('click', function(e) {
            e.preventDefault();
            showNotification('View your wishlist');
        });
    }
    
    // Account icon click
    const accountIcon = document.getElementById('account-icon');
    if (accountIcon) {
        accountIcon.addEventListener('click', function(e) {
            e.preventDefault();
            showNotification('Account menu');
        });
    }
    
    // Cart icon click
    const cartIcon = document.getElementById('cart-icon');
    if (cartIcon) {
        cartIcon.addEventListener('click', function(e) {
            e.preventDefault();
            showNotification('View your cart');
        });
    }
    
    // Newsletter form submission
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="email"]');
            if (emailInput.value) {
                showNotification('Thank you for subscribing to our newsletter!');
                emailInput.value = '';
            } else {
                showNotification('Please enter a valid email address');
            }
        });
    }
    
    // Helper function to show notifications
    function showNotification(message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'notification alert alert-success';
        notification.textContent = message;
        
        // Style notification
        notification.style.position = 'fixed';
        notification.style.bottom = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '2000';
        notification.style.padding = '15px 25px';
        notification.style.borderRadius = '5px';
        notification.style.boxShadow = '0 3px 10px rgba(0,0,0,0.2)';
        notification.style.transition = 'all 0.3s ease';
        
        // Add to body
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(20px)';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
    const productRow = document.querySelector('.featured-products .product-row');
    const leftArrow = document.querySelector('.scroll-arrow.left');
    const rightArrow = document.querySelector('.scroll-arrow.right');
    
    if (productRow && leftArrow && rightArrow) {
        // Calculate scroll amount based on card width
        const getScrollAmount = () => {
            const card = productRow.querySelector('.col-md-6.col-lg-3');
            if (!card) return 300; // fallback
            return card.offsetWidth + 30; // card width + margin
        };
    
        // Update arrow states based on scroll position
        const updateArrowStates = () => {
            const scrollLeft = productRow.scrollLeft;
            const maxScroll = productRow.scrollWidth - productRow.clientWidth;
            
            leftArrow.classList.toggle('disabled', scrollLeft <= 0);
            rightArrow.classList.toggle('disabled', scrollLeft >= maxScroll - 1);
        };
    
        // Initialize arrow states
        updateArrowStates();
    
        // Scroll left
        leftArrow.addEventListener('click', () => {
            if (leftArrow.classList.contains('disabled')) return;
            productRow.scrollBy({ left: -getScrollAmount(), behavior: 'smooth' });
        });
    
        // Scroll right
        rightArrow.addEventListener('click', () => {
            if (rightArrow.classList.contains('disabled')) return;
            productRow.scrollBy({ left: getScrollAmount(), behavior: 'smooth' });
        });
    
        // Update arrows on scroll
        productRow.addEventListener('scroll', updateArrowStates);
    
        // Update arrows on resize
        window.addEventListener('resize', () => {
            updateArrowStates();
        });
    }       
});