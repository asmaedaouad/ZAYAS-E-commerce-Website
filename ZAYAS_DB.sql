-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2025 at 11:28 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zayas_simple`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery`
--

CREATE TABLE `delivery` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(50) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `delivery_notes` text DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `personnel_id` int(11) DEFAULT NULL,
  `delivery_status` enum('pending','assigned','in_transit','delivered','cancelled','returned') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery`
--

INSERT INTO `delivery` (`id`, `order_id`, `address`, `city`, `postal_code`, `phone`, `delivery_notes`, `delivery_date`, `personnel_id`, `delivery_status`) VALUES
(3, 3, 'N°0 RUE CHILIOUAT QUARTIER CHLIOUAT,83350', 'OULADTEIMA', '8769', '1234567890', 'NICE', NULL, 7, 'returned'),
(4, 4, 'N°0 RUE CHILIOUAT QUARTIER CHLIOUAT,83350', 'OULADTEIMA', '12334', '1234567890', 'Great', NULL, 3, 'returned'),
(5, 5, 'Al Hoceima rue 4', 'AlHoceima', '12347', '1234567890', 'Good', NULL, 3, 'delivered'),
(6, 6, 'Al Hoceima rue 4', 'AlHoceima', '23456', '1234567890', 'Quick', NULL, 3, 'delivered'),
(9, 9, 'Al Hoceima rue 4', 'AlHoceima', '23456', '123-456-7890', 'Quick', NULL, NULL, 'cancelled'),
(10, 10, 'Al Hoceima rue 4', 'AlHoceima', '23454', '1234567890', 'Great', NULL, 7, 'assigned'),
(12, 12, 'Al Hoceima rue 4', 'AlHoceima', '23454', '1234567890', 'Nice product', NULL, 3, 'in_transit'),
(13, 13, 'N°0 RUE CHILIOUAT QUARTIER CHLIOUAT,83350', 'OULADTEIMA', '23454', '1234567890', 'QUICK', NULL, NULL, 'pending'),
(15, 15, 'N°0 RUE CHILIOUAT QUARTIER CHLIOUAT,83350', 'OULADTEIMA', '23454', '1234567890', 'nice', NULL, 3, 'assigned');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','assigned','in_transit','delivered','cancelled','returned') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `created_at`) VALUES
(3, 6, 389.97, 'returned', '2025-05-17 22:36:25'),
(4, 6, 179.98, 'returned', '2025-05-19 09:10:45'),
(5, 8, 679.94, 'delivered', '2025-05-19 11:07:30'),
(6, 8, 78.00, 'delivered', '2025-05-19 11:31:46'),
(9, 8, 289.98, 'cancelled', '2025-05-19 12:05:11'),
(10, 8, 179.99, 'assigned', '2025-05-19 12:06:01'),
(12, 8, 199.99, 'in_transit', '2025-05-19 22:43:07'),
(13, 9, 279.98, 'pending', '2025-05-20 12:23:00'),
(15, 6, 439.97, 'assigned', '2025-05-21 09:20:15');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(5, 3, 6, 1, 89.99),
(6, 3, 7, 2, 149.99),
(7, 4, 9, 1, 29.99),
(8, 4, 7, 1, 149.99),
(9, 5, 6, 2, 89.99),
(10, 5, 10, 1, 39.99),
(11, 5, 7, 1, 149.99),
(12, 5, 1, 1, 129.99),
(13, 5, 2, 1, 179.99),
(14, 6, 11, 1, 78.00),
(17, 9, 5, 1, 199.99),
(18, 9, 6, 1, 89.99),
(19, 10, 2, 1, 179.99),
(21, 12, 5, 1, 199.99),
(22, 13, 4, 2, 139.99),
(24, 15, 1, 2, 129.99),
(25, 15, 2, 1, 179.99);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`id`, `user_id`, `token`, `created_at`, `expires_at`, `used`) VALUES
(11, 8, '67NU10', '2025-05-19 12:02:22', '2025-05-19 14:17:22', 0),
(13, 6, '33N2CZ', '2025-05-20 23:11:47', '2025-05-21 01:26:47', 0);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_new` tinyint(1) DEFAULT 0,
  `quantity` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `type`, `description`, `price`, `old_price`, `image_path`, `is_new`, `quantity`, `created_at`) VALUES
(1, 'Modern Abaya', 'abaya', 'Elegant modern abaya with clean lines', 129.99, 149.99, '1.png', 0, 14, '2025-05-15 16:20:41'),
(2, 'UAE Abaya', 'abaya', 'Hand-embroidered traditional abaya', 179.99, NULL, '2.png', 1, 7, '2025-05-15 16:20:41'),
(3, 'Kimono Abaya', 'abaya', 'Stylish open front kimono-style abaya', 159.99, 199.99, '3.png', 0, 10, '2025-05-15 16:20:41'),
(4, 'Butterfly Abaya', 'abaya', 'Flowing butterfly style modern abaya', 139.99, NULL, '4.png', 1, 5, '2025-05-15 16:20:41'),
(5, 'Glory Dress', 'dress', 'Luxurious evening dress for special occasions', 199.99, NULL, '5.png', 1, 12, '2025-05-15 16:20:41'),
(6, 'Comfort Dress', 'dress', 'Comfortable everyday casual dress', 89.99, 109.99, '6.png', 0, 18, '2025-05-15 16:20:41'),
(7, 'Party Dress', 'dress', 'Elegant party dress with stylish details', 149.99, NULL, '7.png', 0, 7, '2025-05-15 16:20:41'),
(8, 'Silk Hijab', 'hijab', 'Luxurious silk hijab with premium finish', 49.99, 59.99, '8.png', 0, 25, '2025-05-15 16:20:41'),
(9, 'Cotton Hijab', 'hijab', 'Comfortable cotton hijab for everyday wear', 29.99, NULL, '9.png', 1, 31, '2025-05-15 16:20:41'),
(10, 'Chiffon Hijab', 'hijab', 'Lightweight chiffon hijab with elegant drape', 39.99, NULL, '10.png', 0, 14, '2025-05-15 16:20:41'),
(11, 'Stoles Hijab', 'hijab', 'YELLOW CHIFFON HIJAB', 78.00, 90.90, '1747521538.png', 1, 2, '2025-05-17 22:38:58');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `is_delivery` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `address`, `city`, `postal_code`, `phone`, `is_admin`, `is_delivery`, `created_at`) VALUES
(1, 'Admin', 'User', 'admin@zayas.com', '$2y$10$ffssbHNNULjxfeOVM.1GK.q3LiDTI6b3MFHGgQZ7ZUTTSsWqKAofa', NULL, NULL, NULL, NULL, 1, 0, '2025-05-15 16:23:28'),
(3, 'Amin', 'SU', 'amin@fake.com', '$2y$10$CnTt69lk6y1BV05EY4sA7.DWtqIRIu5ILqKi2kn4IdWM7yWL47gfq', 'Agadir rue 1', 'Agadir', '23456', '1234567890', 0, 1, '2025-05-15 16:33:39'),
(6, 'Zaynab', 'Ait Addi', 'zaynabaitaddi2004@gmail.com', '$2y$10$szQh8lO1kNX60MOYN2M6A.m77mTVdf9bXTc6Kojbw0Dl8CQ3it/Ou', NULL, NULL, NULL, NULL, 0, 0, '2025-05-17 22:34:39'),
(7, 'Samir', 'SA', 'samir@fake.com', '$2y$10$96OYqjVWVmDbKHwNATCgWO7KlLsMD8vQQ8oeZ9JBvi9WoxHZtVnRG', 'Casablanca rue 20', 'Casablanca', '34566', '2345679765', 0, 1, '2025-05-17 22:41:15'),
(8, 'Asmae', 'Daouad', 'asmae.daouad@gmail.com', '$2y$10$pitawkp7zHZVYDpzRho2VO08./JYDYuXBDpXKFG3d1bUXvQFNdivG', 'Al Hoceima rue 4', 'AlHoceima', '54368', '123-456-7890', 0, 0, '2025-05-19 11:04:39'),
(9, 'ZINAH', 'CA', 'Zaynab@fake.com', '$2y$10$mK6NfAQgDcwRbk9VJEmmtupNjEv5bUVP1gxRnXRjnIzYFatjDrLv2', NULL, NULL, NULL, NULL, 0, 0, '2025-05-20 12:22:03');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(14, 6, 9, '2025-05-17 22:35:14'),
(15, 6, 4, '2025-05-17 22:35:46'),
(16, 8, 1, '2025-05-19 22:44:35'),
(17, 8, 2, '2025-05-19 22:44:36'),
(18, 9, 2, '2025-05-20 12:22:20'),
(19, 9, 5, '2025-05-20 12:22:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_cart_user_id` (`user_id`);

--
-- Indexes for table `delivery`
--
ALTER TABLE `delivery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `personnel_id` (`personnel_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_password_reset_token` (`token`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `delivery`
--
ALTER TABLE `delivery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery`
--
ALTER TABLE `delivery`
  ADD CONSTRAINT `delivery_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `delivery_ibfk_2` FOREIGN KEY (`personnel_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `password_reset_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
