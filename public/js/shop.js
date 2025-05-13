/**
 * Shop page JavaScript
 */
document.addEventListener('DOMContentLoaded', function() {
    // Category bubbles animation
    const categoryBubbles = document.querySelectorAll('.category-bubble');

    categoryBubbles.forEach(bubble => {
        // Add hover animation
        bubble.addEventListener('mouseenter', function() {
            const img = this.querySelector('.bubble-img img');
            const icon = this.querySelector('.bubble-img i');

            if (img) {
                img.style.transform = 'scale(1.1)';
            }

            if (icon) {
                icon.style.transform = 'scale(1.1)';
            }
        });

        bubble.addEventListener('mouseleave', function() {
            const img = this.querySelector('.bubble-img img');
            const icon = this.querySelector('.bubble-img i');

            if (img) {
                img.style.transform = 'scale(1)';
            }

            if (icon) {
                icon.style.transform = 'scale(1)';
            }
        });

        // Add click effect
        bubble.addEventListener('click', function(e) {
            // Add ripple effect
            const ripple = document.createElement('div');
            ripple.classList.add('ripple');
            this.appendChild(ripple);

            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);

            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${e.clientX - rect.left - size/2}px`;
            ripple.style.top = `${e.clientY - rect.top - size/2}px`;

            ripple.classList.add('active');

            setTimeout(() => {
                ripple.remove();
            }, 500);
        });
    });

    // Highlight active category
    const highlightActiveCategory = () => {
        const urlParams = new URLSearchParams(window.location.search);
        const type = urlParams.get('type');

        categoryBubbles.forEach(bubble => {
            bubble.classList.remove('active');
        });

        if (type) {
            const activeCategory = document.querySelector(`.category-bubble[href*="type=${type}"]`);
            if (activeCategory) {
                activeCategory.classList.add('active');
            }
        } else {
            const allProductsCategory = document.querySelector('.category-bubble[href$="shop.php"]');
            if (allProductsCategory && !allProductsCategory.getAttribute('href').includes('type=')) {
                allProductsCategory.classList.add('active');
            }
        }
    };

    // Call on page load
    highlightActiveCategory();
});
