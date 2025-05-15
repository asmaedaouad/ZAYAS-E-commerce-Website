# Device Types and Responsive Design

This document details how the ZAYAS e-commerce project handles responsive design across different device types.

## 1. Desktop (1200px and above)

### Characteristics
- Full navigation menu displayed horizontally
- 4 products per row in grid views
- Sidebar filters visible by default in shop page
- Full-width hero carousel with large text
- Detailed product information visible without clicking "more"

### CSS Media Queries
```css
/* Desktop Specific Styles */
@media (min-width: 1200px) {
    /* Styles here */
}
```

### Key Files
- `header.css` - Desktop navigation
- `home.css` - Desktop homepage layout
- `shop.css` - Desktop product grid
- `admin.css` - Desktop admin interface

## 2. Laptop/Small Desktop (992px to 1199px)

### Characteristics
- Full navigation menu displayed horizontally
- 3 products per row in grid views
- Sidebar filters visible by default but narrower
- Slightly reduced font sizes and spacing
- Some content areas become more compact

### CSS Media Queries
```css
/* Laptop/Small Desktop Specific Styles */
@media (min-width: 992px) and (max-width: 1199px) {
    /* Styles here */
}
```

### Key Files
- `header.css` - Laptop navigation
- `home.css` - Laptop homepage layout
- `shop.css` - Laptop product grid
- `admin.css` - Laptop admin interface

## 3. Tablet (768px to 991px)

### Characteristics
- Hamburger menu for navigation
- 2 products per row in grid views
- Sidebar filters collapsed by default
- Reduced font sizes and spacing
- Stacked layouts for previously side-by-side content
- Admin sidebar collapses to icons only

### CSS Media Queries
```css
/* Tablet Specific Styles */
@media (min-width: 768px) and (max-width: 991px) {
    /* Styles here */
}
```

### Key Files
- `header.css` - Tablet navigation
- `home.css` - Tablet homepage layout
- `shop.css` - Tablet product grid
- `admin.css` - Tablet admin interface
- `header.js` - Tablet menu behavior

## 4. Mobile Landscape (576px to 767px)

### Characteristics
- Hamburger menu for navigation
- 2 products per row in grid views (smaller)
- All filters accessed via modal/dropdown
- Significantly reduced font sizes and spacing
- Stacked layouts for all content
- Tables convert to card view

### CSS Media Queries
```css
/* Mobile Landscape Specific Styles */
@media (min-width: 576px) and (max-width: 767px) {
    /* Styles here */
}
```

### Key Files
- `header.css` - Mobile landscape navigation
- `home.css` - Mobile landscape homepage layout
- `shop.css` - Mobile landscape product grid
- `cart.css` - Mobile landscape cart view
- `header.js` - Mobile menu behavior

## 5. Mobile Portrait (575px and below)

### Characteristics
- Hamburger menu for navigation
- 1 product per row in grid views
- All filters accessed via modal/dropdown
- Minimal font sizes and spacing
- Highly simplified layouts
- Tables convert to stacked card view

### CSS Media Queries
```css
/* Mobile Portrait Specific Styles */
@media (max-width: 575px) {
    /* Styles here */
}
```

### Key Files
- `header.css` - Mobile portrait navigation
- `home.css` - Mobile portrait homepage layout
- `shop.css` - Mobile portrait product grid
- `cart.css` - Mobile portrait cart view
- `header.js` - Mobile menu behavior

## Testing Devices

For optimal testing, use the following devices or emulate their screen sizes:

- **Desktop**: 1920×1080 (standard desktop)
- **Laptop**: 1366×768 (common laptop)
- **Tablet**: iPad (768×1024)
- **Mobile Landscape**: iPhone X/11/12 in landscape (812×375)
- **Mobile Portrait**: iPhone X/11/12 in portrait (375×812)
