-- Create the database if it doesn't already exist
CREATE DATABASE IF NOT EXISTS `mobile_store`;

-- Select the created database to use for the rest of the script
USE `mobile_store`;

-- Drop existing tables if they exist to start fresh
DROP TABLE IF EXISTS `contact_messages`;
DROP TABLE IF EXISTS `wishlist`;
DROP TABLE IF EXISTS `cart`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `payment_methods`;
DROP TABLE IF EXISTS `product_images`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `brands`;
DROP TABLE IF EXISTS `users`;

-- Table `users`
CREATE TABLE `users` (
    `user_id` INT PRIMARY KEY AUTO_INCREMENT,
    `full_name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL COMMENT 'Should be a hashed password',
    `phone_number` VARCHAR(20) NULL,
    `address` TEXT NULL,
    `is_admin` BOOLEAN NOT NULL DEFAULT 0 COMMENT '0 = User, 1 = Admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table `brands`   
CREATE TABLE `brands` (
    `brand_id` INT PRIMARY KEY AUTO_INCREMENT,
    `brand_name` VARCHAR(100) NOT NULL UNIQUE,
    `brand_logo_url` VARCHAR(255) NULL
);

-- Table `products`
CREATE TABLE `products` (
    `product_id` INT PRIMARY KEY AUTO_INCREMENT,
    `product_name` VARCHAR(255) NOT NULL,
    `brand_id` INT,
    `price` DECIMAL(10, 2) NOT NULL,
    `discount_percentage` DECIMAL(5, 2) NOT NULL DEFAULT 0.00,
    `description` TEXT NULL,
    `specifications` TEXT NULL,
    `availability_status` VARCHAR(50) NOT NULL DEFAULT 'out of stock' COMMENT '"in stock" or "out of stock"',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`)
);

-- Table `product_images`
CREATE TABLE `product_images` (
    `image_id` INT PRIMARY KEY AUTO_INCREMENT,
    `product_id` INT,
    `image_url` VARCHAR(255) NOT NULL,
    `is_primary` BOOLEAN NOT NULL DEFAULT 0 COMMENT '1 = Main image, 0 = Secondary image',
    FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
);

-- Table `payment_methods`
CREATE TABLE `payment_methods` (
    `method_id` INT PRIMARY KEY AUTO_INCREMENT,
    `method_name` VARCHAR(100) NOT NULL UNIQUE,
    `is_active` BOOLEAN NOT NULL DEFAULT 1 COMMENT '1 = Active, 0 = Inactive'
);

-- Table `payments`
CREATE TABLE `payments` (
    `payment_id` INT PRIMARY KEY AUTO_INCREMENT,
    `order_id` INT NOT NULL,
    `method_id` INT,
    `amount_paid` DECIMAL(10, 2) NOT NULL,
    `transaction_status` VARCHAR(50) NOT NULL DEFAULT 'Completed' COMMENT '"Completed", "Failed", "Pending"',
    `payment_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`method_id`) REFERENCES `payment_methods` (`method_id`)
);

-- Table `orders`
CREATE TABLE `orders` (
    `order_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT,
    `total_amount` DECIMAL(10, 2) NOT NULL,
    `final_amount` DECIMAL(10, 2) NOT NULL COMMENT 'Amount after discounts',
    `payment_id` INT,
    `order_status` VARCHAR(50) NOT NULL DEFAULT 'Pending' COMMENT '"Pending", "Approved", "Rejected", "Delivered"',
    `delivery_date` DATE NULL,
    `order_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
    FOREIGN KEY (`payment_id`) REFERENCES `payments` (`payment_id`)
);

-- Table `order_items`
CREATE TABLE `order_items` (
    `order_item_id` INT PRIMARY KEY AUTO_INCREMENT,
    `order_id` INT,
    `product_id` INT,
    `quantity` INT NOT NULL,
    `price_per_item` DECIMAL(10, 2) NOT NULL COMMENT 'Price at the time of order',
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
    FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
);

-- Table `cart`
CREATE TABLE `cart` (
    `cart_item_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT,
    `product_id` INT,
    `quantity` INT NOT NULL DEFAULT 1,
    `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
    FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
);

-- Table `wishlist`
CREATE TABLE `wishlist` (
    `wishlist_item_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT,
    `product_id` INT,
    `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
    FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
);

CREATE TABLE `contact_messages` (
    `message_id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- INSERTING ESSENTIAL DATA
INSERT INTO
    `users` (
        `full_name`,
        `email`,
        `password`,
        `is_admin`
    )
VALUES (
        'Admin',
        'admin@themobilestore.com',
        'admin123',
        1
    );

-- Insert default Payment Methods
INSERT INTO
    `payment_methods` (`method_name`, `is_active`)
VALUES ('Cash on Delivery', 1),
    ('Credit Card', 1),
    ('Debit Card', 1),
    ('UPI', 1);