document.addEventListener('DOMContentLoaded', function() {
    // Initialize carousel
    const myCarousel = new bootstrap.Carousel(document.getElementById('mainCarousel'), {
        interval: 6000,
        ride: 'carousel',
        pause: 'hover',
        wrap: true,
        touch: true
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
    
    // Load products for New Arrivals section
    fetch('data/products.json')
        .then(response => response.json())
        .then(data => {
            const newArrivals = data.products.filter(product => product.isNew);
            renderNewArrivals(newArrivals);
        })
        .catch(error => {
            console.error('Error loading products:', error);
            document.querySelector('.featured-products .product-row').innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                    <h4>Failed to load products</h4>
                    <p class="text-muted">Please try refreshing the page</p>
                </div>
            `;
        });
    
    // Render New Arrivals products
    function renderNewArrivals(products) {
        const productRow = document.querySelector('.featured-products .product-row');
        
        if (!products.length) {
            productRow.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h4>No new arrivals found</h4>
                    <p class="text-muted">Check back later for new products</p>
                </div>
            `;
            return;
        }
        
        productRow.innerHTML = products.map(product => `
            <div class="col-md-6 col-lg-3">
                <div class="product-card" data-product-id="${product.id}">
                    <div class="product-image">
                        <img src="${product.image}" alt="${product.name}" loading="lazy">
                        
                        <div class="product-badges">
                            ${product.isNew ? '<span class="badge badge-new">New</span>' : ''}
                            ${product.oldPrice ? '<span class="badge badge-sale">Sale</span>' : ''}
                            ${product.quantity === 0 ? '<span class="badge badge-sold-out">Sold Out</span>' : ''}
                        </div>
                        
                        <button class="wishlist-btn">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                    
                    <div class="product-info mt-3">
                        <h5>${product.name}</h5>
                        <p class="product-description">${product.category}</p>
                        
                        <div class="product-price">
                            <span class="price">$${product.price.toFixed(2)}</span>
                            ${product.oldPrice ? `<span class="old-price">$${product.oldPrice.toFixed(2)}</span>` : ''}
                        </div>
                        
                        <a href="#" class="btn btn-outline-dark w-100 ${product.quantity === 0 ? 'disabled' : ''}">
                            ${product.quantity === 0 ? 'Out of Stock' : 'Add to Cart'}
                        </a>
                    </div>
                </div>
            </div>
        `).join('');
        
        // Initialize scroll functionality
        setupProductScrolling();
        
        // Reattach event listeners
        setupProductCardInteractions();
    }
    
    // Set up product scrolling functionality
    function setupProductScrolling() {
        const productRow = document.querySelector('.featured-products .product-row');
        const leftArrow = document.querySelector('.scroll-arrow.left');
        const rightArrow = document.querySelector('.scroll-arrow.right');
        
        if (!productRow || !leftArrow || !rightArrow) return;
        
        const getScrollAmount = () => {
            const card = productRow.querySelector('.col-md-6.col-lg-3');
            if (!card) return 300; // fallback
            return card.offsetWidth + 30; // card width + margin
        };
    
        const updateArrowStates = () => {
            const scrollLeft = productRow.scrollLeft;
            const maxScroll = productRow.scrollWidth - productRow.clientWidth;
            
            leftArrow.classList.toggle('disabled', scrollLeft <= 0);
            rightArrow.classList.toggle('disabled', scrollLeft >= maxScroll - 1);
        };
    
        updateArrowStates();
    
        leftArrow.addEventListener('click', () => {
            if (leftArrow.classList.contains('disabled')) return;
            productRow.scrollBy({ left: -getScrollAmount(), behavior: 'smooth' });
        });
    
        rightArrow.addEventListener('click', () => {
            if (rightArrow.classList.contains('disabled')) return;
            productRow.scrollBy({ left: getScrollAmount(), behavior: 'smooth' });
        });
    
        productRow.addEventListener('scroll', updateArrowStates);
        window.addEventListener('resize', updateArrowStates);
    }
    
    // Set up product card interactions (wishlist, etc.)
    function setupProductCardInteractions() {
        // Wishlist button functionality
        document.querySelectorAll('.wishlist-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const icon = this.querySelector('i');
                icon.classList.toggle('far');
                icon.classList.toggle('fas');
                
                if (icon.classList.contains('fas')) {
                    const headerWishlist = document.querySelector('#wishlist-icon i');
                    headerWishlist.classList.add('fas');
                    headerWishlist.classList.remove('far');
                    showNotification('Item added to your wishlist');
                }
            });
        });
    }
    
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
    
    // Header wishlist icon click
    const headerWishlist = document.getElementById('wishlist-icon');
    if (headerWishlist) {
        headerWishlist.addEventListener('click', function(e) {
            showNotification('View your wishlist');
        });
    }
    
    // Account icon click
    const accountIcon = document.getElementById('account-icon');
    if (accountIcon) {
        accountIcon.addEventListener('click', function(e) {
            showNotification('Account menu');
        });
    }
    
    // Cart icon click
    const cartIcon = document.getElementById('cart-icon');
    if (cartIcon) {
        cartIcon.addEventListener('click', function(e) {
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
        const notification = document.createElement('div');
        notification.className = 'notification alert alert-success';
        notification.textContent = message;
        
        notification.style.position = 'fixed';
        notification.style.bottom = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '2000';
        notification.style.padding = '15px 25px';
        notification.style.borderRadius = '5px';
        notification.style.boxShadow = '0 3px 10px rgba(0,0,0,0.2)';
        notification.style.transition = 'all 0.3s ease';
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(20px)';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
});