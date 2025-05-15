# Responsive JavaScript Files

This directory contains copies of all JavaScript files in the ZAYAS project that handle responsive behavior. Below is a list of these files and their responsive features.

## User Interface JavaScript Files

### `header.js`
- **Location**: `/public/js/header.js`
- **Responsive Features**:
  - Mobile menu toggle functionality
  - Dropdown menu handling on mobile
  - Responsive search behavior
  - Window resize event handling
  - Mobile-specific event listeners

```javascript
// Example: Mobile menu toggle
navbarToggler.addEventListener('click', function() {
    navbarCollapse.classList.toggle('show');
    menuBackdrop.classList.toggle('show');
    document.body.classList.toggle('menu-open');
});
```

### `home.js`
- **Location**: `/public/js/home.js`
- **Responsive Features**:
  - Responsive carousel initialization
  - Video handling for different screen sizes
  - Lazy loading for performance on mobile
  - Touch-friendly hover alternatives

```javascript
// Example: Lazy loading for better mobile performance
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

### `shop.js`
- **Location**: `/public/js/shop.js`
- **Responsive Features**:
  - Mobile filter toggle
  - Touch-friendly category bubbles
  - Responsive product quick view
  - Adjusted product grid for different screens

```javascript
// Example: Category bubbles with touch support
categoryBubbles.forEach(bubble => {
    bubble.addEventListener('mouseenter', function() {
        const img = this.querySelector('.bubble-img img');
        const icon = this.querySelector('.bubble-img i');
        
        if (img) img.style.transform = 'scale(1.1)';
        if (icon) icon.style.transform = 'scale(1.1)';
    });
    
    bubble.addEventListener('mouseleave', function() {
        const img = this.querySelector('.bubble-img img');
        const icon = this.querySelector('.bubble-img i');
        
        if (img) img.style.transform = 'scale(1)';
        if (icon) icon.style.transform = 'scale(1)';
    });
});
```

### `cart.js`
- **Location**: `/public/js/cart.js`
- **Responsive Features**:
  - Mobile-friendly quantity controls
  - Responsive cart updates
  - Touch-optimized buttons

### `about.js`
- **Location**: `/public/js/about.js`
- **Responsive Features**:
  - Responsive animations
  - Touch-friendly team member cards
  - Scroll-based animations adjusted for mobile

```javascript
// Example: Animate elements when they come into view
const animateOnScroll = function() {
    const elements = document.querySelectorAll('.animate-on-scroll');
    
    elements.forEach(element => {
        const elementPosition = element.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        
        if (elementPosition < windowHeight - 100) {
            element.classList.add('animated');
        }
    });
};

// Run on page load and scroll
animateOnScroll();
window.addEventListener('scroll', animateOnScroll);
```

### `contact.js`
- **Location**: `/public/js/contact.js`
- **Responsive Features**:
  - Mobile-friendly form validation
  - Responsive animations
  - Touch-friendly social icons

```javascript
// Example: Touch-friendly social icons
const socialIcons = document.querySelectorAll('.social-icon');
socialIcons.forEach(icon => {
    icon.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
    });
    
    icon.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});
```

## Admin Interface JavaScript Files

### `admin.js`
- **Location**: `/admin/assets/js/admin.js`
- **Responsive Features**:
  - Sidebar toggle for mobile
  - Responsive chart initialization
  - Window resize handlers
  - Mobile-friendly dropdowns

```javascript
// Example: Sidebar toggle for mobile
sidebarToggle.addEventListener('click', function() {
    sidebar.classList.toggle('active');
    mainContent.classList.toggle('expanded');
});
```

### `dashboard.js`
- **Location**: `/admin/assets/js/dashboard.js`
- **Responsive Features**:
  - Responsive chart configurations
  - Mobile-optimized statistics display
  - Adaptive data visualization

```javascript
// Example: Responsive chart configuration
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    legend: {
        display: window.innerWidth > 768, // Only show legend on larger screens
        position: 'bottom'
    }
};
```

### `products.js`
- **Location**: `/admin/assets/js/products.js`
- **Responsive Features**:
  - Mobile-friendly product management
  - Responsive modal dialogs
  - Touch-optimized controls

### `delivery-personnel.js`
- **Location**: `/admin/assets/js/delivery-personnel.js`
- **Responsive Features**:
  - Mobile-friendly personnel management
  - Responsive modal dialogs
  - Touch-optimized action buttons

## Delivery Interface JavaScript Files

### `delivery.js`
- **Location**: `/delivery/public/js/delivery.js`
- **Responsive Features**:
  - Mobile-friendly order management
  - Responsive status updates
  - Touch-optimized action buttons

## Common Responsive JavaScript Patterns

1. **Feature Detection**: Using capability detection instead of device detection
2. **Event Delegation**: Efficient event handling for dynamic content
3. **Throttling/Debouncing**: Performance optimization for resize/scroll events
4. **Conditional Loading**: Loading different resources based on screen size
5. **Touch Events**: Supporting both mouse and touch interactions
6. **Responsive Initialization**: Configuring plugins differently based on screen size
7. **Media Queries in JS**: Using `window.matchMedia()` for JavaScript breakpoints

## Best Practices

1. **Performance First**: Mobile devices have less processing power
2. **Progressive Enhancement**: Ensure basic functionality works without JavaScript
3. **Avoid Browser Sniffing**: Use feature detection instead
4. **Test on Real Devices**: Emulators don't always reflect real-world performance
5. **Minimize DOM Manipulation**: Especially important on mobile devices
6. **Use Passive Event Listeners**: Improve scrolling performance
