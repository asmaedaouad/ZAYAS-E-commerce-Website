# Responsive Components

This document details how specific components in the ZAYAS e-commerce project adapt across different device sizes.

## Navigation Header

### Desktop (≥992px)
- Full horizontal menu
- Search icon expands to search bar
- Cart/wishlist icons with notification badges
- Dropdown menus on hover

### Mobile (<992px)
- Hamburger menu that slides in from left
- Collapsed search that expands on click
- Simplified cart/wishlist icons
- Dropdown menus require click to expand

**Key Files:**
- `header.css`
- `header.js`

## Product Cards

### Desktop (≥992px)
- 4 cards per row
- Hover effects show quick-view and add-to-cart buttons
- Product image fills container
- "New" and "Out of Stock" badges visible

### Tablet (768px-991px)
- 3 cards per row
- Simplified hover effects
- Slightly smaller images

### Mobile (<768px)
- 2 cards per row (landscape) or 1 card per row (portrait)
- No hover effects (touch-based interaction)
- Larger add-to-cart button for touch targets

**Key Files:**
- `product-card.css`
- `shop.css`
- `home.css`

## Cart Table

### Desktop (≥768px)
- Full table layout with columns
- Quantity controls inline
- Update button visible

### Mobile (<768px)
- Card-based layout instead of table
- Each product in its own card
- Labels above values
- Stacked controls
- Full-width update button

**Key Files:**
- `cart.css`

## Checkout Form

### Desktop (≥992px)
- Two-column layout
- Order summary fixed on right
- Form fields side by side

### Mobile (<992px)
- Single column layout
- Order summary below form
- Stacked form fields
- Full-width buttons

**Key Files:**
- `checkout.css`

## Admin Dashboard

### Desktop (≥992px)
- Sidebar always visible
- 4 stat cards per row
- Charts at full size
- Tables with all columns visible

### Tablet (768px-991px)
- Sidebar collapses to icons
- 2 stat cards per row
- Smaller charts
- Tables with horizontal scroll

### Mobile (<768px)
- Sidebar hidden by default (toggle button)
- 1 stat card per row
- Simplified charts
- Tables transform to cards

**Key Files:**
- `admin.css`
- `dashboard.css`

## Delivery Interface

### Desktop (≥992px)
- Status cards in single row
- Full table view of orders
- Action buttons with text

### Mobile (<992px)
- Status cards stack in grid
- Simplified table view
- Icon-only action buttons

**Key Files:**
- `delivery.css`
- `style.css` (in delivery folder)

## Product Filters

### Desktop (≥992px)
- Sidebar filters always visible
- Multiple filter sections expanded
- Price slider at full width

### Mobile (<992px)
- Filters collapse into dropdown/accordion
- Only one filter section expanded at a time
- Optimized controls for touch

**Key Files:**
- `shop.css`

## Home Page Carousel

### Desktop (≥992px)
- Large, full-width slides
- Large text and buttons
- Video background plays automatically

### Mobile (<768px)
- Smaller slides with simplified content
- Reduced text size
- Static image instead of video on very small devices

**Key Files:**
- `home.css`
- `home.js`

## Account Dashboard

### Desktop (≥992px)
- Sidebar navigation with account sections
- Two-column layout for information
- Tables with all columns

### Mobile (<992px)
- Top navigation for account sections
- Single column layout
- Simplified tables or card views

**Key Files:**
- `account.css`
