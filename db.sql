-- Create the database if it doesn't already exist
CREATE DATABASE IF NOT EXISTS `mobile_store`;

USE `mobile_store`;

-- --------------------------------------------------------
-- Users & Security
-- --------------------------------------------------------

CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone_number` VARCHAR(20) DEFAULT NULL,
    `address` TEXT DEFAULT NULL,
    `is_admin` BOOLEAN NOT NULL DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `user_tokens` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `selector` VARCHAR(255) NOT NULL,
    `hashed_validator` VARCHAR(255) NOT NULL,
    `expires` DATETIME NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- Core Product & Category Structure
-- --------------------------------------------------------

CREATE TABLE `brands` (
    `brand_id` INT AUTO_INCREMENT PRIMARY KEY,
    `brand_name` VARCHAR(255) NOT NULL UNIQUE,
    `brand_logo_url` VARCHAR(255)
);

CREATE TABLE `categories` (
    `category_id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_name` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT
);

CREATE TABLE `products` (
    `product_id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `brand_id` INT,
    `category_id` INT,
    `status` VARCHAR(255),
    `specifications` JSON,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`) ON DELETE SET NULL,
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL
);

CREATE TABLE `product_variants` (
    `variant_id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `color` VARCHAR(50),
    `storage_gb` INT,
    `ram_gb` INT,
    `price` DECIMAL(10, 2) NOT NULL,
    `stock_quantity` INT NOT NULL DEFAULT 0,
    FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE
);

CREATE TABLE `product_images` (
    `image_id` INT AUTO_INCREMENT PRIMARY KEY,
    `variant_id` INT NOT NULL,
    `image_url` VARCHAR(255) NOT NULL,
    `is_thumbnail` BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- User Interaction Tables
-- --------------------------------------------------------

CREATE TABLE `reviews` (
    `review_id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `rating` INT NOT NULL CHECK (
        rating >= 1
        AND rating <= 5
    ),
    `comment` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

CREATE TABLE `cart` (
    `cart_item_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `variant_id` INT NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE CASCADE
);

CREATE TABLE `wishlist` (
    `wishlist_item_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- Order & Payment Structure
-- --------------------------------------------------------

CREATE TABLE `orders` (
    `order_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `order_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `total_amount` DECIMAL(10, 2) NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Pending',
    `shipping_address` TEXT NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

CREATE TABLE `order_items` (
    `order_item_id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `variant_id` INT NOT NULL,
    `quantity` INT NOT NULL,
    `price_per_item` DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
    FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE CASCADE
);

CREATE TABLE `payment_methods` (
    `payment_method_id` INT AUTO_INCREMENT PRIMARY KEY,
    `method_name` VARCHAR(255) NOT NULL UNIQUE,
    `is_enabled` BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE `payments` (
    `payment_id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `payment_method_id` INT NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `payment_status` VARCHAR(255) NOT NULL DEFAULT 'Pending',
    `transaction_id` VARCHAR(255) DEFAULT NULL,
    `payment_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
    FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`payment_method_id`)
);

-- --------------------------------------------------------
-- Miscellaneous
-- --------------------------------------------------------

CREATE TABLE `contact_messages` (
    `message_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);