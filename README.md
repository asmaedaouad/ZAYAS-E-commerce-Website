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

# Order Status System

This document explains the order status system used in the ZAYAS Simple e-commerce application.

## Order Statuses

The system uses the following order statuses:

1. **Pending** - Order has been placed but not yet assigned to a delivery person. This is the initial status of all orders.
2. **Assigned** - Order has been assigned to a delivery person but not yet in transit.
3. **In-Transit** - Order is on the way to the customer.
4. **Delivered** - Order has been successfully delivered to the customer.
5. **Cancelled** - Order was cancelled before it arrived at the customer.
6. **Returned** - Order was returned after it arrived at the customer.

## Status Flow

The typical flow of an order is:

```
Pending → Assigned → In-Transit → Delivered
```

Alternative flows include:

```
Pending → Cancelled
Pending → Assigned → Cancelled
Pending → Assigned → In-Transit → Cancelled
Pending → Assigned → In-Transit → Delivered → Returned
```

## Implementation Details

- The status is stored in the `orders` table in the `status` column.
- The delivery status is stored in the `delivery` table in the `delivery_status` column.
- Both columns use ENUM data types with the values: 'pending', 'assigned', 'in_transit', 'delivered', 'cancelled', 'returned'.

## Updating the Database

To update an existing database to use the new status system, run the SQL script `update_order_status.sql`.

```sql
-- Update orders table to use the new status values
ALTER TABLE orders 
MODIFY COLUMN status ENUM('pending', 'assigned', 'in_transit', 'delivered', 'cancelled', 'returned') DEFAULT 'pending';

-- Update delivery table to use the new status values
ALTER TABLE delivery
MODIFY COLUMN delivery_status ENUM('pending', 'assigned', 'in_transit', 'delivered', 'cancelled', 'returned') DEFAULT 'pending';

-- Update any existing 'processing' status to 'assigned'
UPDATE orders SET status = 'assigned' WHERE status = 'processing';

-- Update any existing 'shipped' status to 'in_transit'
UPDATE orders SET status = 'in_transit' WHERE status = 'shipped';
```

# ZAYAS Delivery System

This is the delivery management system for ZAYAS e-commerce website. It allows delivery personnel to manage their assigned deliveries.

## Features

- Delivery personnel login
- Dashboard with delivery statistics
- View assigned deliveries
- Update delivery status
- View delivery details
- Profile management

## Setup

1. Make sure the main ZAYAS e-commerce system is set up and running
2. Update the database schema by running the SQL commands in `setup.sql`
3. Create delivery personnel accounts by setting `is_delivery = 1` in the users table
4. Assign deliveries to personnel using the admin interface

## Usage

1. Delivery personnel can log in at `/delivery/login.php`
2. Default credentials for testing:
   - Email: delivery@zayas.com
   - Password: 123456
3. After login, they will be redirected to the dashboard
4. They can view and manage their assigned deliveries
5. They can update delivery status as needed

## Delivery Status Flow

The typical flow of a delivery is:

```
Assigned → In-Transit → Delivered
```

Alternative flows include:

```
Assigned → In-Transit → Delivered → Returned
```

Note: Delivery personnel cannot cancel orders. Cancellations can only be done by administrators.

The system uses the following statuses:

1. **Pending** - Initial status, not yet assigned to delivery personnel (managed by admin)
2. **Assigned** - Admin has assigned the order to a delivery person, but the delivery person hasn't accepted it yet
3. **In-Transit** - Delivery person has accepted the order and is actively delivering it
4. **Delivered** - Successfully delivered to customer
5. **Returned** - Delivery was returned after delivery attempt

## Integration with Admin System

- Admin assigns deliveries to delivery personnel
- Delivery personnel update delivery status
- Admin can view all deliveries and their status
- Status changes are synchronized between delivery and admin systems
