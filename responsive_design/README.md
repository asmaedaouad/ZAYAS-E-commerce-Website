# ZAYAS Responsive Design Documentation

This folder contains all the files responsible for the responsive design of the ZAYAS e-commerce project. The responsive design ensures that the website looks and functions well across different device types and screen sizes.

## Device Types Supported

The responsive design in this project supports the following device types:

1. **Desktop** (1200px and above)
2. **Laptop/Small Desktop** (992px to 1199px)
3. **Tablet** (768px to 991px)
4. **Mobile Landscape** (576px to 767px)
5. **Mobile Portrait** (575px and below)

## Folder Structure

- **`/css`**: Contains all CSS files with responsive media queries
- **`/js`**: Contains JavaScript files that handle responsive behavior
- **`/docs`**: Contains detailed documentation for specific components

## Breakpoints

The project uses the following standard breakpoints:

- **Extra Small (xs)**: < 576px (Mobile phones in portrait mode)
- **Small (sm)**: ≥ 576px (Mobile phones in landscape mode)
- **Medium (md)**: ≥ 768px (Tablets)
- **Large (lg)**: ≥ 992px (Laptops/Desktops)
- **Extra Large (xl)**: ≥ 1200px (Large desktops)

## Responsive Approach

The project uses a mobile-first approach, where the base styles are designed for mobile devices and then enhanced for larger screens using media queries.

### Key Responsive Features

1. **Fluid Grid System**: Using Bootstrap's 12-column grid system
2. **Flexible Images**: Images scale with their containers
3. **Media Queries**: CSS adjustments based on viewport width
4. **Mobile Navigation**: Collapsible navigation menu for smaller screens
5. **Touch-Friendly Elements**: Larger touch targets on mobile devices

## Common Responsive Patterns

1. **Header**: Transforms from a horizontal menu on desktop to a hamburger menu on mobile
2. **Product Cards**: Adjust from 4-per-row on desktop to 2-per-row on tablet and 1-per-row on mobile
3. **Forms**: Stack form elements vertically on smaller screens
4. **Tables**: Become scrollable or transform into cards on mobile

## Testing

The responsive design has been tested on:
- Chrome, Firefox, Safari, and Edge browsers
- iOS and Android devices
- Various screen sizes from 320px to 1920px width

## How to Use

When developing new features, refer to the appropriate CSS and JS files in this folder to maintain consistent responsive behavior across the site.
