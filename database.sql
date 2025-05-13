-- Create database
CREATE DATABASE IF NOT EXISTS zayas_simple;
USE zayas_simple;

-- Users table
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

-- Products table
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

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Wishlist table
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, product_id)
);

-- Delivery table
CREATE TABLE IF NOT EXISTS delivery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(50) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    delivery_notes TEXT,
    delivery_date DATE,
    delivery_status ENUM('pending', 'in_transit', 'delivered') DEFAULT 'pending',
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Insert sample products
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

-- Insert admin user (password: admin123)
INSERT INTO users (first_name, last_name, email, password, is_admin) VALUES
('Admin', 'User', 'admin@zayas.com', '$2y$10$8WxYR0AIj5usILK.Ug.9.uMkC5yrA.L5xYwaTZOqj4vR0olHlz4Hy', 1);

-- Create cart table in the database
USE zayas_simple;

-- Create cart table
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

-- Add an index for faster cart retrieval
CREATE INDEX idx_cart_user_id ON cart(user_id);
