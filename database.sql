-- 1. Create database
CREATE DATABASE IF NOT EXISTS zayas_simple;
USE zayas_simple;

-- 2. Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    city VARCHAR(50),
    postal_code VARCHAR(20),
    phone VARCHAR(20),
    is_admin BOOLEAN DEFAULT 0,
    is_delivery BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    old_price DECIMAL(10, 2),
    image_path VARCHAR(255) NOT NULL,
    is_new BOOLEAN DEFAULT 0,
    quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Create orders table (depends on users)
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'assigned', 'in_transit', 'delivered', 'cancelled', 'returned') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. Create order_items table (depends on orders and products)
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 6. Create wishlist table (depends on users and products)
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, product_id)
);

-- 7. Create delivery table (depends on orders)
CREATE TABLE IF NOT EXISTS delivery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(50) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    delivery_notes TEXT,
    delivery_date DATE,
    personnel_id INT,
    delivery_status ENUM('pending', 'assigned', 'in_transit', 'delivered', 'cancelled', 'returned') DEFAULT 'pending',
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (personnel_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 8. Create cart table (depends on users and products)
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, product_id)
);

-- 9. Add index for cart
CREATE INDEX idx_cart_user_id ON cart(user_id);

-- 10. Insert sample products
INSERT INTO products (name, type, description, price, old_price, image_path, is_new, quantity) VALUES
('Modern Cut Abaya', 'abaya', 'Elegant modern abaya with clean lines', 129.99, 149.99, '1.png', 0, 15),
('Traditional Embroidered Abaya', 'abaya', 'Hand-embroidered traditional abaya', 179.99, NULL, '2.png', 1, 8),
('Open Front Kimono Abaya', 'abaya', 'Stylish open front kimono-style abaya', 159.99, 199.99, '3.png', 0, 10),
('Butterfly Abaya', 'abaya', 'Flowing butterfly style modern abaya', 139.99, NULL, '4.png', 1, 5),
('Evening Glory Dress', 'dress', 'Luxurious evening dress for special occasions', 199.99, NULL, '5.png', 1, 12),
('Casual Comfort Dress', 'dress', 'Comfortable everyday casual dress', 89.99, 109.99, '6.png', 0, 20),
('Party Dress', 'dress', 'Elegant party dress with stylish details', 149.99, NULL, '7.png', 0, 7),
('Premium Silk Hijab', 'hijab', 'Luxurious silk hijab with premium finish', 49.99, 59.99, '8.png', 0, 25),
('Breathable Cotton Hijab', 'hijab', 'Comfortable cotton hijab for everyday wear', 29.99, NULL, '9.png', 1, 30),
('Elegant Chiffon Hijab', 'hijab', 'Lightweight chiffon hijab with elegant drape', 39.99, NULL, '10.png', 0, 15);

-- Create password_reset_tokens table
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    used BOOLEAN DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Add index for faster token lookups
CREATE INDEX idx_password_reset_token ON password_reset_tokens(token);
