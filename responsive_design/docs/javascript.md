# Responsive JavaScript Functionality

This document details the JavaScript functionality that handles responsive behavior in the ZAYAS e-commerce project.

## Header Navigation (`header.js`)

### Mobile Menu Toggle
- Toggles the mobile menu when the hamburger icon is clicked
- Adds a backdrop overlay to the page
- Prevents body scrolling when menu is open

```javascript
// Toggle menu when navbar toggler is clicked
navbarToggler.addEventListener('click', function() {
    navbarCollapse.classList.toggle('show');
    menuBackdrop.classList.toggle('show');
    document.body.classList.toggle('menu-open');
});
```

### Mobile Dropdown Handling
- Prevents default link behavior on mobile
- Toggles dropdown visibility on click
- Closes other open dropdowns

```javascript
// Handle dropdown menus on mobile
dropdownToggles.forEach(function(toggle) {
    toggle.addEventListener('click', function(e) {
        // Only prevent default for mobile view
        if (window.innerWidth < 992) {
            e.preventDefault();
            const dropdownMenu = this.nextElementSibling;
            // Toggle dropdown
            dropdownMenu.classList.toggle('show');
        }
    });
});
```

### Responsive Search
- Expands search input on click
- Adjusts behavior based on screen size
- Closes mobile menu when search is activated

```javascript
// Toggle search input when search icon is clicked
searchToggles.forEach(function(toggle) {
    toggle.addEventListener('click', function(e) {
        e.preventDefault();
        const inputContainer = toggle.closest('.search-box').querySelector('.search-input-container');
        inputContainer.classList.add('active');
        
        // Close mobile menu if open on small screens
        if (window.innerWidth < 992) {
            navbarCollapse.classList.remove('show');
            menuBackdrop.classList.remove('show');
        }
    });
});
```

### Window Resize Handling
- Resets mobile menu state when resizing to desktop
- Ensures proper state management across breakpoints

```javascript
// Handle window resize
window.addEventListener('resize', function() {
    if (window.innerWidth >= 992) {
        // Reset mobile menu state when resizing to desktop
        navbarCollapse.classList.remove('show');
        menuBackdrop.classList.remove('show');
        document.body.classList.remove('menu-open');
    }
});
```

## Home Page (`home.js`)

### Responsive Carousel
- Adjusts carousel behavior based on screen size
- Handles video playback differently on mobile

```javascript
// Initialize Bootstrap carousel with custom settings
const carousel = new bootstrap.Carousel(heroCarousel, {
    interval: 6000,
    pause: 'hover',
    wrap: true,
    keyboard: true
});

// Handle video playback in carousel
heroCarousel.addEventListener('slide.bs.carousel', function(event) {
    // Pause all videos when sliding
    const allVideos = document.querySelectorAll('.hero-video');
    allVideos.forEach(video => {
        if (video) video.pause();
    });
});
```

### Lazy Loading
- Improves performance on mobile devices
- Only loads images when they come into view

```javascript
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
```

## Admin Interface (`admin.js`)

### Sidebar Toggle
- Shows/hides sidebar on mobile
- Adjusts main content margin

```javascript
// Toggle sidebar on mobile
sidebarToggle.addEventListener('click', function() {
    sidebar.classList.toggle('active');
    mainContent.classList.toggle('expanded');
});
```

### Responsive Charts
- Resizes charts when window size changes
- Adjusts data display for smaller screens

```javascript
// Resize charts when window is resized
window.addEventListener('resize', function() {
    if (salesChart) salesChart.resize();
    if (ordersChart) ordersChart.resize();
});
```

## Shop Page (`shop.js`)

### Mobile Filters
- Toggles filter visibility on mobile
- Collapses filter sections into accordions

```javascript
// Toggle filters on mobile
filterToggle.addEventListener('click', function() {
    filterSidebar.classList.toggle('active');
    document.body.classList.toggle('filters-open');
});
```

### Product Quick View
- Adjusts modal content based on screen size
- Simplifies options on smaller screens

```javascript
// Initialize product quick view modal
quickViewButtons.forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        // Adjust modal content based on screen size
        if (window.innerWidth < 576) {
            // Simplified view for very small screens
        } else {
            // Full view for larger screens
        }
    });
});
```

## Best Practices for Responsive JavaScript

1. **Feature Detection**: Use feature detection instead of device detection
2. **Performance**: Be mindful of performance on mobile devices
3. **Touch Events**: Handle both click and touch events appropriately
4. **Throttling**: Throttle resize and scroll events to improve performance
5. **Progressive Enhancement**: Ensure basic functionality works without JavaScript
6. **Breakpoint Consistency**: Use the same breakpoints as in CSS
7. **Testing**: Test on actual devices, not just emulators
