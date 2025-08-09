-- =================================================================
-- E-COMMERCE PLATFORM: FULL DATABASE SETUP SCRIPT
-- =================================================================
-- This script will:
-- 1. Create a new database named 'e_commerce_db'.
-- 2. Drop any existing tables to ensure a clean setup.
-- 3. Create all required tables with correct relationships.
-- 4. Insert a variety of sample data for testing.
-- 5. Provide example queries for database interaction.
-- =================================================================

-- Drop the database if it exists to start fresh
DROP DATABASE IF EXISTS `mobile_store`;

-- Create the database
CREATE DATABASE `mobile_store`;

-- Select the database to use for subsequent queries
USE `mobile_store`;

-- Set timezone
SET time_zone = "+00:00";

-- Temporarily disable foreign key checks to avoid errors when dropping/creating tables in order
SET FOREIGN_KEY_CHECKS = 0;

-- =================================================================
-- TABLE DEFINITIONS
-- =================================================================

-- 1. USERS TABLE
CREATE TABLE `USERS` (
    `user_id` INT PRIMARY KEY AUTO_INCREMENT,
    `full_name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL, -- Note: Passwords should be securely hashed!
    `phone_number` VARCHAR(20) NULL,
    `address` TEXT NULL,
    `is_admin` BOOLEAN NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. BRANDS TABLE
CREATE TABLE `BRANDS` (
    `brand_id` INT PRIMARY KEY AUTO_INCREMENT,
    `brand_name` VARCHAR(100) NOT NULL UNIQUE,
    `brand_logo_url` VARCHAR(255) NULL
);

-- 3. PRODUCTS TABLE
CREATE TABLE `PRODUCTS` (
    `product_id` INT PRIMARY KEY AUTO_INCREMENT,
    `product_name` VARCHAR(255) NOT NULL,
    `tagline` VARCHAR(255) NULL,
    `brand_id` INT,
    `price` DECIMAL(10, 2) NOT NULL,
    `discount_percentage` DECIMAL(5, 2) NOT NULL DEFAULT 0.00,
    `description` TEXT NULL,
    `specifications` TEXT NULL,
    `availability_status` VARCHAR(50) NOT NULL DEFAULT 'Out of Stock',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`brand_id`) REFERENCES `BRANDS` (`brand_id`)
);

-- 4. PRODUCT_IMAGES TABLE
CREATE TABLE `PRODUCT_IMAGES` (
    `image_id` INT PRIMARY KEY AUTO_INCREMENT,
    `product_id` INT,
    `image_url` VARCHAR(255) NOT NULL,
    `is_primary` BOOLEAN NOT NULL DEFAULT 0,
    FOREIGN KEY (`product_id`) REFERENCES `PRODUCTS` (`product_id`) ON DELETE CASCADE
);

-- 5. PAYMENT_METHODS TABLE
CREATE TABLE `PAYMENT_METHODS` (
    `method_id` INT PRIMARY KEY AUTO_INCREMENT,
    `method_name` VARCHAR(100) NOT NULL UNIQUE,
    `is_active` BOOLEAN NOT NULL DEFAULT 1
);

-- 6. ORDERS TABLE
CREATE TABLE `ORDERS` (
    `order_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT,
    `total_amount` DECIMAL(10, 2) NOT NULL,
    `final_amount` DECIMAL(10, 2) NOT NULL,
    `order_status` VARCHAR(50) NOT NULL DEFAULT 'Pending',
    `delivery_date` DATE NULL,
    `order_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `USERS` (`user_id`)
);

-- 7. PAYMENTS TABLE
CREATE TABLE `PAYMENTS` (
    `payment_id` INT PRIMARY KEY AUTO_INCREMENT,
    `order_id` INT UNIQUE,
    `method_id` INT,
    `amount_paid` DECIMAL(10, 2) NOT NULL,
    `transaction_status` VARCHAR(50) DEFAULT 'Completed',
    `payment_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `ORDERS` (`order_id`),
    FOREIGN KEY (`method_id`) REFERENCES `PAYMENT_METHODS` (`method_id`)
);

-- 8. ORDER_ITEMS TABLE
CREATE TABLE `ORDER_ITEMS` (
    `order_item_id` INT PRIMARY KEY AUTO_INCREMENT,
    `order_id` INT,
    `product_id` INT,
    `quantity` INT NOT NULL,
    `price_per_item` DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `ORDERS` (`order_id`),
    FOREIGN KEY (`product_id`) REFERENCES `PRODUCTS` (`product_id`)
);

-- 9. CART TABLE
CREATE TABLE `CART` (
    `cart_item_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT,
    `product_id` INT,
    `quantity` INT NOT NULL DEFAULT 1,
    `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `USERS` (`user_id`),
    FOREIGN KEY (`product_id`) REFERENCES `PRODUCTS` (`product_id`)
);

-- 10. WISHLIST TABLE
CREATE TABLE `WISHLIST` (
    `wishlist_item_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT,
    `product_id` INT,
    `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `USERS` (`user_id`),
    FOREIGN KEY (`product_id`) REFERENCES `PRODUCTS` (`product_id`)
);

-- 11. CONTACT_MESSAGES TABLE
CREATE TABLE `CONTACT_MESSAGES` (
    `message_id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;