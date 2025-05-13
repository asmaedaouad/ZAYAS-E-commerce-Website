# ZAYAS Simple E-commerce Website

A simple e-commerce website for ZAYAS clothing store, built with PHP following the MVC architecture pattern.

## Project Structure

```
/ZAYAS_simple
  /config - Database connection and configuration
  /models - Database models
  /views - Frontend templates
    /home - Home page, shop, product details
    /user - User account, wishlist, cart
    /auth - Login, register
  /controllers - Business logic
    /cart - Cart actions
    /wishlist - Wishlist actions
    /user - User actions
  /public - Static assets
    /css - Stylesheets
    /js - JavaScript files
    /images - Product images
  /includes - Reusable components like header and footer
  index.php - Entry point
```

## Features

- User authentication (register, login, logout)
- Product browsing and filtering
- Product details view
- Shopping cart
- Wishlist
- User account management
- Order processing
- Responsive design

## Database Structure

- `users` - User information and authentication
- `products` - Product details
- `orders` - Order information
- `order_items` - Items in each order
- `wishlist` - User wishlist items
- `delivery` - Delivery information

## Setup Instructions

1. Place the project in your web server's document root (e.g., `xampp/htdocs/ZAYAS_simple`)
2. Make sure your MySQL server is running (e.g., through XAMPP control panel)
3. Open your web browser and navigate to `http://localhost/ZAYAS_simple/setup.php`
4. Click on "Set Up Database" to create the database, tables, and sample data
5. Once the setup is complete, click on "Go to Website" to access the website
6. Alternatively, you can access the website directly at `http://localhost/ZAYAS_simple`

If you need to manually configure the database:
- Configure the database connection in `config/Database.php` if needed
- The default configuration uses host: localhost, username: root, password: (empty)

## Technologies Used

- PHP
- MySQL
- HTML
- CSS
- Minimal JavaScript
- Bootstrap 5

## Default Admin Account

- Email: admin@zayas.com
- Password: admin123
