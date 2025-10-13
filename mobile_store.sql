-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 11, 2025 at 10:17 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mobile_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `brand_logo_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brand_id`, `brand_name`, `brand_logo_url`) VALUES
(1, 'Samsung', 'Samsung.webp'),
(2, 'Apple', 'Apple.webp'),
(3, 'Google', 'Google.webp'),
(4, 'Oneplus', 'Oneplus.webp'),
(5, 'Nothing', 'Nothing.webp'),
(6, 'Xiaomi', 'Xiaomi.webp'),
(7, 'Motorola', 'Motorola.webp'),
(8, 'Iqoo', 'Iqoo.webp'),
(9, 'Vivo', 'Vivo.webp'),
(10, 'Oppo', 'Oppo.webp');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`) VALUES
(1, 'Smart Phones', 'All the premium smart phones. \r\n'),
(2, 'Accessories', 'All the accessories. \r\n');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `message_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`message_id`, `name`, `email`, `subject`, `message`, `submitted_at`) VALUES
(1, 'Yash Gajjar', 'yash@gmail.com', 'Request for Urgent Delivery of My Order', 'I recently ordered a Pixel 10 and have not received my delivery yet. I kindly request you to expedite the shipping process, as I would appreciate receiving my phone as soon as possible.\r\n\r\nPlease let me know the current status of my order and when I can expect the delivery.', '2025-10-03 18:37:08');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `shipping_address` text NOT NULL,
  `payment_method` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `total_amount`, `status`, `shipping_address`, `payment_method`) VALUES
(21, 3, '2025-10-03 18:42:16', 74999.00, 'Delivered', '                                    Gordhan chawk, London, Spain                                ', 'COD'),
(22, 3, '2025-10-04 05:28:39', 54999.00, 'Approved', '                                    Gordhan chawk, London, Spain                                ', 'COD'),
(23, 3, '2025-10-04 05:35:26', 119900.00, 'Declined', '                                    Gordhan chawk, London, Spain                                ', 'COD'),
(24, 3, '2025-10-04 05:41:58', 89999.00, 'Shipped', '                                    Gordhan chawk, London, Spain                                ', 'COD');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_item` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `variant_id`, `quantity`, `price_per_item`) VALUES
(18, 21, 1, 1, 74999.00),
(19, 22, 266, 1, 54999.00),
(20, 23, 93, 1, 119900.00),
(21, 24, 53, 1, 89999.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_status` varchar(255) NOT NULL DEFAULT 'Pending',
  `transaction_id` varchar(255) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `payment_method_id` int(11) NOT NULL,
  `method_name` varchar(255) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`payment_method_id`, `method_name`, `is_enabled`) VALUES
(1, 'Cash On Delivery', 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specifications`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `brand_id`, `category_id`, `status`, `specifications`, `created_at`) VALUES
(1, 'Galaxy S25', 'Released in early 2025, the Galaxy S25 delivers flagship performance in a compact body. It has a 6.2-inch Dynamic LTPO AMOLED 2X display at 120Hz, powered by the Snapdragon 8 Elite chip with 12GB RAM. The camera setup features a 50MP main sensor with 3× telephoto and a 12MP ultra-wide lens. With a 4,000 mAh battery, IP68 protection, Gorilla Glass Victus 2 build, and Android 15 with One UI 7, it’s a premium all-rounder', 1, 1, 'Hot', '{\"Processor\":\"Qualcomm Snapdragon 8 Elite (3 nm)\",\"Main Camera\":\"50 MP, f\\/1.8, 24mm (wide), 10 MP, f\\/2.4, 67mm (telephoto), 12 MP, f\\/2.2, 13mm, 120\\u02da (ultrawide)\",\"Selfie Camera\":\"12 MP, f\\/2.2, 26mm (wide)\",\"Video Camera\":\"8K@24\\/30fps, 4K@30\\/60fps, 1080p@30\\/60\\/120\\/240fps, 10-bit HDR, HDR10+\",\"Display\":\"Dynamic LTPO AMOLED 2X, 120Hz\",\"Battery\":\"4000mAh\",\"Operating System\":\"Android 15, up to 7 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 01:23:16'),
(2, 'Galaxy S25 Plus', 'Released in early 2025, the Galaxy S25+ brings a larger display and stronger battery without going full Ultra. It has a 6.7-inch QHD+ Dynamic AMOLED 2X screen with 120Hz refresh rate, powered by the Snapdragon 8 Elite chip with 12GB RAM. The camera setup includes a 50MP main lens, 12MP ultra-wide, and 10MP telephoto (3× zoom), along with a 12MP front cam. It’s backed by a 4,900mAh battery with 45W wired charging, supports wireless charging and reverse wireless charging, and comes with IP68 protection. Runs Android 15 with One UI 7, and to top it off, promises 7 years of OS & security updates.', 1, 1, '', '{\"Processor\":\"Qualcomm Snapdragon 8 Elite (3 nm)\",\"Main Camera\":\"50 MP, f\\/1.8, 24mm (wide), 10 MP, f\\/2.4, 67mm (telephoto), 12 MP, f\\/2.2, 13mm, 120\\u02da (ultrawide)\",\"Selfie Camera\":\"12 MP, f\\/2.2, 26mm (wide)\",\"Video Camera\":\"8K@24\\/30fps, 4K@30\\/60fps, 1080p@30\\/60\\/120\\/240fps, 10-bit HDR, HDR10+\",\"Display\":\"Dynamic LTPO AMOLED 2X, 120Hz, 6.7 inches\",\"Battery\":\"4900mAH\",\"Operating System\":\"Android 15, up to 7 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 01:34:09'),
(3, 'Galaxy S25 Ultra', 'Released early 2025, the Galaxy S25 Ultra is Samsung’s top-flagship, built for power, creativity, and cutting-edge tech. It features a 6.9-inch QHD+ Dynamic AMOLED 2X display with 1-120Hz refresh, powered by the Snapdragon 8 Elite chip with up to 16GB RAM and up to 1TB storage. It boasts a quad-camera setup led by a 200MP main lens, plus 5× and 3× telephoto zooms and an upgraded ultra-wide. With a 5,000mAh battery, 45W wired + wireless charging, a titanium frame, IP68 protection, integrated S Pen, and Android 15 with One UI 7, the S25 Ultra is made for users who want flagship specs across the board.', 1, 1, 'Trending', '{\"Processor\":\"Qualcomm Snapdragon 8 Elite (3 nm)\",\"Main Camera\":\"200 MP, f\\/1.7, 24mm (wide), 10 MP, f\\/2.4, 67mm (telephoto), 50 MP, f\\/3.4, 111mm (periscope telephoto), 50 MP, f\\/1.9, 120\\u02da (ultrawide)\",\"Selfie Camera\":\"12 MP, f\\/2.2, 26mm (wide)\",\"Video Camera\":\"4K@30\\/60fps, 1080p@30fps\",\"Display\":\"Dynamic LTPO AMOLED 2X, 120Hz, 6.9 inches\",\"Battery\":\"5000mAh\",\"Operating System\":\"Android 15, up to 7 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 01:46:19'),
(4, 'Galaxy S25 Edge', 'Released May 2025, the Galaxy S25 Edge is a premium ultra-slim flagship. It has a 6.7″ QHD+ AMOLED 120Hz display, powered by Snapdragon 8 Elite, with 12GB RAM and up to 512GB storage. It sports a 200MP main camera + 12MP ultrawide, 3,900mAh battery with 25W wired and wireless charging, IP68 rating, and runs Android 15 with One UI 7.', 1, 1, '', '{\"Processor\":\"Qualcomm Snapdragon 8 Elite (3 nm)\",\"Main Camera\":\"200 MP, f\\/1.7, 24mm (wide), 12 MP, f\\/2.2, 13mm (ultrawide)\",\"Selfie Camera\":\"12 MP, f\\/2.2, 26mm (wide)\",\"Video Camera\":\"8K@30fps, 4K@30\\/60\\/120fps, 1080p@30\\/60\\/120\\/240fps\",\"Display\":\"LTPO AMOLED 2X, 120Hz\",\"Battery\":\"3900mAh\",\"Operating System\":\"Android 15, up to 7 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 01:53:00'),
(7, 'Galaxy Z Fold7', 'Released mid-2025, the Z Fold 7 is Samsung’s slimmest & lightest book-foldable so far. It has an 8.0-inch Dynamic AMOLED 2X main display (folded out) and a wider 6.5-inch cover screen. Powered by the Snapdragon 8 Elite for Galaxy chip, with up to 16GB RAM and up to 1TB storage. Camera setup includes a 200MP main lens, 12MP ultra-wide, and a 10MP telephoto (3× optical zoom). Its 4,400 mAh battery supports 25W wired charging, wireless charging, and has IP48 water/dust resistance.', 1, 1, 'New', '{\"Processor\":\"Qualcomm Snapdragon 8 Elite (3 nm)\",\"Main Camera\":\"200 MP, f\\/1.7, 24mm (wide), 10 MP, f\\/2.4, 67mm (telephoto), 12 MP, f\\/2.2, 120\\u02da (ultrawide)\",\"Selfie Camera\":\"10 MP, f\\/2.2, 18mm (ultrawide), Cover camera: 10 MP, f\\/2.2, 24mm (wide)\",\"Video Camera\":\"8K@30fps, 4K@60fps, 1080p@60\\/120\\/240fps (gyro-EIS), 10-bit HDR, HDR10+\",\"Display\":\"Foldable Dynamic LTPO AMOLED 2X, 120Hz, HDR10+, 8.0 inches\",\"Battery\":\"4400mAh\",\"Operating System\":\"Android 16, up to 7 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 02:38:55'),
(8, 'Galaxy Z Flip7', 'Released in mid-2025, the Galaxy Z Flip 7 is a premium clamshell foldable with a 6.9″ 120Hz Dynamic AMOLED 2X main display and a new 4.1″ edge-to-edge cover screen. It runs on the 3nm Exynos 2500 chip with 12GB RAM and up to 512GB storage. It has a dual camera setup (50MP main + 12MP ultra-wide), a 4,300mAh battery, and IP48 water/dust resistance. Other highlights include a slimmer hinge design, Armour Aluminium frame, Gorilla Glass Victus 2 protection, and Android 16 with One UI 8.', 1, 1, 'New', '{\"Processor\":\"Exynos 2500 (3 nm)\",\"Main Camera\":\"50 MP, f\\/1.8, 23mm (wide), 12 MP, f\\/2.2, 13mm, 123\\u02da (ultrawide)\",\"Selfie Camera\":\"10 MP, f\\/2.2, 23mm (wide)\",\"Video Camera\":\"4K@30\\/60fps, 1080p@60\\/120\\/240fps, HDR10+\",\"Display\":\"Foldable Dynamic LTPO AMOLED 2X, 120Hz, HDR10+, 6.9 inches\",\"Battery\":\"4300mAh\",\"Operating System\":\"Android 16, up to 7 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 03:08:17'),
(9, 'Galaxy Z Flip7 FE', 'Released mid-2025, the Flip 7 FE is the more affordable version of the Flip 7, bringing foldable style to a wider audience. It has a 6.7-inch Dynamic AMOLED 2X inner display with 120Hz refresh, and a 3.4-inch cover screen. Powered by Exynos 2400 with 8GB RAM and up to 256GB storage. The rear cameras are a 50MP main and 12MP ultra-wide, with a 10MP selfie cam. A 4,000 mAh battery supports 25W wired and wireless charging. IP48 water/dust resistance, Android 16 with One UI 8, and a build similar to Flip 6 in many design aspects.', 1, 1, 'New', '{\"Processor\":\"Exynos 2400 (4 nm)\",\"Main Camera\":\"50 MP, f\\/1.8, 23mm (wide), 12 MP, f\\/2.2, 13mm, 123\\u02da (ultrawide)\",\"Selfie Camera\":\"10 MP, f\\/2.2, 23mm (wide)\",\"Video Camera\":\"4K@30\\/60fps, 1080p@60\\/120\\/240fps, HDR10+\",\"Display\":\"Foldable Dynamic LTPO AMOLED 2X, 120Hz, 6.7 inches\",\"Battery\":\"4000mAh\",\"Operating System\":\"Android 16, up to 7 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 03:14:15'),
(11, 'iPhone 16E', 'Released in early 2025, the iPhone 16e joins the iPhone 16 line as the most affordable model. It has a 6.1-inch Super Retina OLED display, powered by Apple’s A18 chip and a new C1 modem. It features a 48MP “Fusion” rear camera with integrated 2× telephoto, Face ID in a notch design, USB-C charging (no MagSafe fast wireless charging), and an aerospace-grade aluminum frame with IP68 water/dust protection. Available in 128/256/512 GB storage, and starts around $599 in the U.S.', 2, 1, '', '{\"Processor\":\"Apple A18 (3 nm)\",\"Main Camera\":\"48 MP, f\\/1.6, 26mm (wide)\",\"Selfie Camera\":\"12 MP, f\\/1.9, 23mm (wide), \",\"Video Camera\":\"4K@24\\/25\\/30\\/60fps, 1080p@25\\/30\\/60\\/120fps, HDR\",\"Display\":\"Super Retina XDR OLED, HDR10\",\"Battery\":\"4005mAh\",\"Operating System\":\"iOS 18.3.1\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 03:38:26'),
(13, 'iPhone 17 Air', 'The iPhone 17 Air is Apple’s thinnest large-screen model to date, featuring a ~6.5-inch 120Hz OLED display, powered by the A19 Pro chip with ~12GB RAM. It has a single 48MP rear camera, 24MP front cam, weighs ~165g, uses a titanium-aluminium frame, supports eSIM only, wireless charging, and runs iOS 26.', 2, 1, 'New', '{\"Processor\":\"Apple A19 Pro (3 nm)\",\"Main Camera\":\"48 MP, f\\/1.6, 26mm (wide)\",\"Selfie Camera\":\"18 MP multi-aspect, f\\/1.9, (wide)\",\"Video Camera\":\"4K@24\\/25\\/30\\/60fps, 1080p@25\\/30\\/60\\/120fps\",\"Display\":\"LTPO Super Retina XDR OLED, 120Hz, HDR10, Dolby Vision, 6.5 inches\",\"Battery\":\"3149mAH\",\"Operating System\":\"iOS 26\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 04:00:03'),
(15, 'iPhone 17 Pro', 'Released in September 2025, the iPhone 17 Pro brings major upgrades in performance, display, and camera. It features a 6.3-inch Super Retina XDR OLED display with ProMotion at up to 120Hz and peak outdoor brightness of around 3,000 nits. It’s powered by the A19 Pro chip with 12GB RAM. The rear camera setup includes three 48MP sensors (wide, ultra-wide, and a telephoto lens with ~8× optical-quality reach). The front camera is now 18MP with Center Stage. It also introduces vapor-chamber cooling, an aluminum unibody build, upgraded Ceramic Shield 2 glass, and runs iOS 26.', 2, 1, 'New', '{\"Processor\":\"Apple A19 Pro (3 nm)\",\"Main Camera\":\"48 MP, f\\/1.6, 24mm (wide), 48 MP, f\\/2.8, 100mm (periscope telephoto)\",\"Selfie Camera\":\"18 MP multi-aspect, f\\/1.9, (wide)\",\"Video Camera\":\"4K@24\\/25\\/30\\/60\\/100\\/120fps, 1080p@25\\/30\\/60\\/120\\/240fps, 10-bit HDR, Dolby Vision HDR (up to 120fps), ProRes, ProRes RAW (up to 120fps), Apple Log 2\",\"Display\":\"LTPO Super Retina XDR OLED, 120Hz, HDR10, Dolby Vision\",\"Battery\":\"3988mAh\",\"Operating System\":\"iOS 26\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 07:59:12'),
(16, 'iPhone 17 Pro Max', 'Released September 2025, the iPhone 17 Pro Max features a 6.9-inch 120Hz Super Retina XDR OLED, powered by the A19 Pro chip with 12GB RAM. It sports triple 48MP rear cameras with 8× telephoto, an 18MP front cam, and up to 2TB storage. With a ~5,000mAh battery, USB-C, MagSafe + Qi2 wireless charging, IP68 durability, and iOS 26, it’s Apple’s most advanced flagship yet.', 2, 1, 'New', '{\"Processor\":\"Apple A19 Pro (3 nm)\",\"Main Camera\":\"48 MP, f\\/1.6, 24mm (wide), 48 MP, f\\/2.8, 100mm (periscope telephoto),  OIS, 4x optical zoom 48 MP, f\\/2.2, 13mm, 120\\u02da (ultrawide)\",\"Selfie Camera\":\"18 MP multi-aspect, f\\/1.9, (wide), \",\"Video Camera\":\"4K@24\\/25\\/30\\/60\\/100\\/120fps, 1080p@25\\/30\\/60\\/120\\/240fps, 10-bit HDR, Dolby Vision HDR (up to 120fps), ProRes, ProRes RAW (up to 120fps), Apple Log 2\",\"Display\":\"LTPO Super Retina XDR OLED, 120Hz, HDR10, Dolby Vision\",\"Battery\":\"4832mAh\",\"Operating System\":\"iOS 26\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 08:17:08'),
(17, 'Pixel 9A', 'Released early 2025, the Pixel 9a is a solid upper-midrange phone with premium features. It has a 6.3-inch 120Hz OLED display with up to 2700 nits peak brightness, powered by the Tensor G4 chip with 8 GB RAM. It offers a 48MP main camera + 13MP ultra-wide, a 5100 mAh battery, 23W wired + wireless charging, IP68 water/dust resistance, and Android 15 with 7 years of updates.', 3, 1, 'Trending', '{\"Processor\":\"Google Tensor G4 (4 nm)\",\"Main Camera\":\"48 MP, f\\/1.7, 25mm (wide), 13 MP, f\\/2.2, 120\\u02da (ultrawide)\",\"Selfie Camera\":\"13 MP, f\\/2.2, 20mm (ultrawide)\",\"Video Camera\":\"4K@30\\/60fps, 1080p@30\\/60\\/120\\/240fps, gyro-EIS, OIS\",\"Display\":\"P-OLED, HDR, 120Hz, 6.3 inches\",\"Battery\":\"5100mAh\",\"Operating System\":\"Android 15, up to 7 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 08:52:19'),
(18, 'Pixel 10', 'Released August 2025, the Pixel 10 has a 6.3-inch 120Hz OLED display, runs on the Tensor G5 chip with 12GB RAM, and features a triple camera setup (48MP main + 13MP ultra-wide + 10.8MP telephoto). It packs a ~4970mAh battery with 30W wired and 15W wireless charging, offers IP68 durability, and ships with Android 16.', 3, 1, 'New', '{\"Processor\":\"Google Tensor G5 (3 nm)\",\"Main Camera\":\"48 MP, f\\/1.7, 25mm (wide), 10.8 MP, f\\/3.1, 112mm (telephoto), 13 MP, f\\/2.2, 120\\u02da (ultrawide)\",\"Selfie Camera\":\"10.5 MP, f\\/2.2, 95\\u02da, 20mm (ultrawide)\",\"Video Camera\":\"4K@24\\/30\\/60fps, 1080p@24\\/30\\/60\\/120\\/240fps; gyro-EIS, OIS, 10-bit HDR\",\"Display\":\"OLED, 120Hz, HDR10+, 6.3 inches\",\"Battery\":\"4970mAh\",\"Operating System\":\"Android 16, up to 7 major Android upgrades\",\"Connectivity\":\"LTE\"}', '2025-09-19 09:01:21'),
(19, 'Pixel 10 Pro', 'Released August 2025, the Pixel 10 Pro sports a 6.3-inch LTPO OLED (1-120Hz) display, powered by Google’s Tensor G5 chip with 16GB RAM. It features a triple-rear camera setup — 50MP main + 48MP ultra-wide + 48MP telephoto with 5× optical zoom — and a 42MP selfie cam. With a 4,870mAh battery, it supports 30W wired and 15W Qi2 wireless charging, has IP68 water- and dust-resistance, and runs Android 16 with around 7 years of software updates.', 3, 1, 'New', '{\"Processor\":\"Google Tensor G5 (3 nm)\",\"Main Camera\":\"50 MP, f\\/1.7, 25mm (wide), 48 MP, f\\/2.8, 113mm (periscope telephoto), optical zoom 48 MP, f\\/1.7, 123\\u02da (ultrawide)\",\"Selfie Camera\":\"42 MP, f\\/2.2, 17mm (ultrawide)\",\"Video Camera\":\"8K@30fps (via cloud-based upscaling), 4K@24\\/30\\/60fps, 1080p@24\\/30\\/60\\/120\\/240fps; gyro-EIS, OIS, 10-bit HDR\",\"Display\":\"LTPO OLED, 120Hz, HDR10+, 6.3 inches\",\"Battery\":\"4870mAh\",\"Operating System\":\"Android 16, up to 7 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 09:08:57'),
(20, 'Pixel 10 Pro XL', 'Released August 2025, the Pixel 10 Pro XL is Google’s big-screen, ultra-premium model. It has a 6.8-inch Super Actua LTPO OLED display (1-120 Hz) with Gorilla Glass Victus 2, powered by the Tensor G5 chip and 16 GB RAM. Its camera system includes a 50MP main sensor, a 48MP ultra-wide, and a 48MP telephoto with 5× optical zoom, plus a 42MP front camera. It comes with a large 5,200 mAh battery, 45W wired and 25W Qi2 wireless charging, IP68 water/dust protection, up to 256GB storage (in India base variant), and runs Android 16 with seven years of updates.', 3, 1, 'New', '{\"Processor\":\"Google Tensor G5 (3 nm)\",\"Main Camera\":\"50 MP, f\\/1.7, 25mm (wide), 48 MP, f\\/2.8, 113mm (periscope telephoto),  optical zoom 48 MP, f\\/1.7, 123\\u02da (ultrawide)\",\"Selfie Camera\":\"42 MP, f\\/2.2, 17mm (ultrawide)\",\"Video Camera\":\"8K@30fps (via cloud-based upscaling), 4K@24\\/30\\/60fps, 1080p@24\\/30\\/60\\/120\\/240fps; gyro-EIS, OIS, 10-bit HDR\",\"Display\":\"LTPO OLED, 120Hz, HDR10+, 6.8 inches\",\"Battery\":\"5200mAh\",\"Operating System\":\"Android 16, up to 7 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 09:16:45'),
(21, 'Oneplus 13', 'OnePlus 13 has a 6.82-inch QHD+ LTPO AMOLED display (1-120Hz, peak ≈ 4500 nits), powered by Snapdragon 8 Elite with up to 24 GB RAM & 1 TB storage. It features triple 50 MP rear cameras (wide + ultra-wide + 3× periscope telephoto), a 6000 mAh battery with 100W wired + 50W wireless charging, IP68/IP69 water/dust resistance, and runs Android 15.', 4, 1, '', '{\"Processor\":\"Qualcomm Snapdragon 8 Elite (3 nm)\",\"Main Camera\":\"50 MP, f\\/1.6, 23mm (wide), 50 MP, f\\/2.6, 73mm (periscope telephoto),  OIS 50 MP, f\\/2.0, 15mm, 120\\u02da (ultrawide)\",\"Selfie Camera\":\"32 MP, f\\/2.4, 21mm (wide)\",\"Video Camera\":\"8K@30fps, 4K@30\\/60fps, 1080p@30\\/60\\/240\\/480fps, Auto HDR, gyro-EIS, Dolby Vision\",\"Display\":\"LTPO 4.1 AMOLED, 1B colors, 120Hz, 6.82 inches\",\"Battery\":\"6000mAh\",\"Operating System\":\"Android 15, up to 4 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 13:02:51'),
(22, 'Oneplus 13R', 'Released January 2025, the OnePlus 13R is a performance-midrange phone with many flagship traits. It has a 6.78-inch LTPO AMOLED display (120Hz, very bright ~4500 nits), powered by Snapdragon 8 Gen 3 with up to 16GB RAM and up to 512GB storage. It carries a 50MP main cam + a 50MP 2× telephoto + 8MP ultra-wide, a 16MP front cam. Backed by a large 6000 mAh battery with 80W wired fast charging. It lacks wireless charging but has an IP65 rating and runs Android 15 with OxygenOS.', 4, 1, '', '{\"Processor\":\"Qualcomm Snapdragon 8 Gen 3 (4 nm)\",\"Main Camera\":\"50 MP, f\\/1.8, 24mm (wide), 50 MP, f\\/2.0, 47mm (telephoto), 8 MP, f\\/2.2, 16mm, 112\\u02da (ultrawide)\",\"Selfie Camera\":\"16 MP, f\\/2.4, 26mm (wide)\",\"Video Camera\":\"K@30\\/60fps, 1080p@30\\/60\\/120\\/240fps, gyro-EIS, OIS\",\"Display\":\"LTPO 4.1 AMOLED, 1B colors, 120Hz, 2160Hz PWM, HDR10+, Dolby Vision, 6.78 inches\",\"Battery\":\"6000mAh\",\"Operating System\":\"Android 15, up to 4 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 13:11:10'),
(23, 'Oneplus 13S', 'OnePlus 13s has a 6.32″ 120Hz LTPO display, Snapdragon 8 Elite chip, 12 GB RAM and up to 512 GB storage. It packs dual 50MP rear cameras (main + 2× telephoto), a 32MP selfie cam, a 5,850 mAh battery with 80W fast charging, and runs OxygenOS 15 based on Android 15. It also introduces a new “Plus Key” replacing the alert slider, for easy AI and utility shortcuts.', 4, 1, 'Hot', '{\"Processor\":\"Qualcomm Snapdragon 8 Elite (3 nm)\",\"Main Camera\":\"50 MP, f\\/1.8, 24mm (wide), 50 MP, f\\/2.0, (telephoto)\",\"Selfie Camera\":\"32 MP, f\\/2.0, 21mm (wide)\",\"Video Camera\":\"4K@30\\/60fps, 1080p@30\\/60\\/240fps, gyro-EIS, OIS, Dolby Vision HDR\",\"Display\":\"LTPO AMOLED, 1B colors, 120Hz, 2160Hz PWM, Dolby Vision, HDR10+\",\"Battery\":\"5850mAh\",\"Operating System\":\"Android 15, OxygenOS 15\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 13:22:49'),
(24, 'Oneplus Open', 'Released in October 2023, the OnePlus Open is the brand’s first foldable phone, featuring a 7.82-inch inner LTPO OLED display and a 6.31-inch cover screen, both with 120Hz refresh rates. It runs on the Snapdragon 8 Gen 2 with 16GB RAM and up to 1TB storage. The triple camera system includes a 48MP “Pixel-Stacked” main sensor, 64MP telephoto with 3× zoom, and 48MP ultra-wide lens, supported by dual front cameras. Powering it is a 4,805mAh battery with 67W fast charging. With IPX4 splash resistance and a slim 5.8mm unfolded profile, it blends portability with flagship performance.', 4, 1, '', '{\"Processor\":\"Qualcomm Snapdragon 8 Gen 2 (4 nm)\",\"Main Camera\":\"48 MP, f\\/1.7, 24mm (wide), 64 MP, f\\/2.6, 70mm (telephoto), 48 MP, f\\/2.2, 14mm, 114\\u02da (ultrawide)\",\"Selfie Camera\":\"20MP, f\\/2.2, 20mm (ultrawide),  Cover camera: 32MP, f\\/2.4, 22mm (ultrawide)\",\"Video Camera\":\"4K@30\\/60fps, 1080p@30\\/60\\/120\\/240\\/480fps gyro-EIS, HDR10+, Dolby Vision\",\"Display\":\"Foldable LTPO3 Flexi-fluid AMOLED, 1B colors, Dolby Vision, 120Hz, 7.82 inches\",\"Battery\":\"4805mAh\",\"Operating System\":\"Android 13, upgradable to Android 15\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 13:30:48'),
(25, 'Nothing 3', 'Released in mid-2025, the Nothing Phone (3) features a 6.67-inch 120Hz AMOLED display, powered by the Snapdragon 8s Gen 4 with up to 16GB RAM and 512GB storage. It comes with a triple 50MP rear camera system, a 50MP front camera, and a 5,500mAh battery supporting 65W wired and 15W wireless charging. With Gorilla Glass 7i, IP68 durability, and long-term software support, it blends performance, design, and reliability.', 5, 1, 'New', '{\"Processor\":\"Qualcomm Snapdragon 8s Gen 4 (4 nm)\",\"Main Camera\":\"50 MP, f\\/1.7, 24mm (wide), 50 MP, f\\/2.7, (periscope telephoto), 50 MP, f\\/2.2, 114\\u02da (ultrawide)\",\"Selfie Camera\":\"50 MP, f\\/2.2, (wide)\",\"Video Camera\":\"K@30\\/60fps, 1080p@30\\/60fps, gyro-EIS, OIS\",\"Display\":\"OLED, 1B colors, 120Hz, 960Hz PWM, HDR10+\",\"Battery\":\"5150mAh\",\"Operating System\":\"Android 15, up to 5 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 13:36:49'),
(26, 'Nothing 3A', 'Released in March 2025, the Nothing Phone (3a) features a 6.77-inch 120Hz AMOLED display with up to 3000 nits peak brightness. Powered by the Snapdragon 7s Gen 3 with up to 12GB RAM, it delivers smooth performance. The triple camera system includes a 50MP main, 50MP telephoto (2× zoom), and 8MP ultra-wide, along with a 32MP front camera. A 5,000mAh battery supports 50W fast charging. Running Android 15 with Nothing OS 3.1, it also comes with IP64 dust and splash resistance.', 5, 1, '', '{\"Processor\":\"Qualcomm Snapdragon 7s Gen 3 (4 nm)\",\"Main Camera\":\"50 MP, f\\/1.9, 24mm (wide), 50 MP, f\\/2.0, 50mm (telephoto), zoom 8 MP, f\\/2.2, 15mm, 120\\u02da (ultrawide)\",\"Selfie Camera\":\"32 MP, f\\/2.2, 22mm (wide)\",\"Video Camera\":\"4K@30fps, 1080p@30\\/60\\/120fps, gyro-EIS, OIS\",\"Display\":\"AMOLED, 1B colors, 120Hz, 2160Hz PWM, HDR10+, 6.77 inches\",\"Battery\":\"5000mAh\",\"Operating System\":\"Android 15, up to 3 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-19 13:43:37'),
(27, 'Nothing 3A Pro', 'Released March 2025, the Nothing Phone (3a) Pro has a 6.77-inch 120Hz AMOLED display, powered by the Snapdragon 7s Gen 3 chip. It features a triple rear camera setup: 50MP main + 50MP periscope telephoto with 3× optical & 6× in-sensor zoom + 8MP ultra-wide, plus a 50MP selfie cam. A 5,000mAh battery with 50W fast charging, IP64 dust/splash resistance, and runs on NothingOS 3.1 (Android 15).', 5, 1, '', '{\"Processor\":\"Qualcomm SM7635 Snapdragon 7s Gen 3 (4 nm)\",\"Main Camera\":\"50 MP, f\\/1.9, 24mm (wide), 50 MP, f\\/2.6, 70mm (periscope telephoto), 3x optical zoom 8 MP, f\\/2.2, 15mm, 120\\u02da (ultrawide)\",\"Selfie Camera\":\"50 MP, f\\/2.2, 24mm (wide)\",\"Video Camera\":\"4K@30fps, 1080p@30\\/60\\/120fps, gyro-EIS, OIS\",\"Display\":\"AMOLED, 1B colors, 120Hz, 2160Hz PWM, HDR10+\",\"Battery\":\"5000mAh\",\"Operating System\":\"Android 15, up to 3 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-20 01:29:21'),
(28, 'Xiaomi 15', 'Launched October 2024, the Xiaomi 15 is a premium flagship featuring a 6.36-inch LTPO OLED display (120Hz, ~1.5K) and powered by the Snapdragon 8 Elite chip. It has a Leica-branded triple 50MP camera setup (wide, ultra-wide, and 3× telephoto), a 5240mAh battery with 90W wired + 50W wireless charging, IP68 water/dust resistance, and runs HyperOS 2 (Android 15).', 6, 1, '', '{\"Processor\":\"Qualcomm Snapdragon 8 Elite (3 nm)\",\"Main Camera\":\"50 MP, f\\/1.6, 23mm (wide), 50 MP, f\\/2.0, 60mm (telephoto), optical zoom 50 MP, f\\/2.2, 14mm, 115\\u02da (ultrawide)\",\"Selfie Camera\":\"32 MP, f\\/2.0, 21mm (wide)\",\"Video Camera\":\"8K@24\\/30fps (HDR), 4K@24\\/30\\/60fps (HDR10+, 10-bit Dolby Vision HDR, 10-bit LOG)\",\"Display\":\"LTPO AMOLED, 68B colors, 1920Hz PWM, 120Hz, Dolby Vision, HDR10+\",\"Battery\":\"5240mAh\",\"Operating System\":\"Android 15, up to 4 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-20 01:38:15'),
(29, 'Xiaomi 15 Ultra', 'Released early 2025, the Xiaomi 15 Ultra is Xiaomi’s photography-focused flagship. It has a 6.73-inch LTPO AMOLED display (120Hz, 3,200 nits peak brightness), powered by the Snapdragon 8 Elite chip with up to 16 GB RAM and up to 1 TB storage. Its quad rear camera setup is tuned with Leica and features a 1-inch 50MP main sensor, a 200MP periscope telephoto lens, plus ultra-wide and 3× telephoto modules. It includes a 5,410-6,000 mAh battery with 90W wired and 80W wireless charging, IP68 dust/water resistance, and runs HyperOS 2 (Android 15).', 6, 1, '', '{\"Processor\":\"Qualcomm Snapdragon 8 Elite (3 nm)\",\"Main Camera\":\"50 MP, f\\/1.6, 23mm (wide), 50 MP, f\\/1.8, 70mm (telephoto), 3x optical zoom 200 MP, f\\/2.6, 100mm (periscope telephoto), PDAF, OIS, 4.3x optical zoom 50 MP, f\\/2.2, 14mm, 115\\u02da (ultrawide)\",\"Selfie Camera\":\"32 MP, f\\/2.0, 21mm (wide)\",\"Video Camera\":\"8K@30fps, 4K@30\\/60\\/120fps, 1080p@30\\/60\\/120\\/240\\/480\\/960\\/1920fps, gyro-EIS, Dolby Vision HDR 10-bit rec\",\"Display\":\"LTPO AMOLED, 68B colors, 120Hz, 1920Hz PWM, Dolby Vision, HDR10+\",\"Battery\":\"5410mAh\",\"Operating System\":\"Android 15, up to 4 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-20 01:47:21'),
(30, 'Motorola Edge 60 ', 'Launched mid-2025, the Edge 60 is a premium mid-range phone with a 6.67-inch 120Hz quad-curved pOLED display (≈1.5K) and up to 4,500 nits peak brightness. It’s powered by the MediaTek Dimensity 7400 chip, paired with 12 GB RAM and 256/512 GB storage. The rear camera setup includes a 50 MP main sensor (with OIS), 50 MP ultra-wide, and a 10 MP 3× telephoto lens, plus a 50 MP selfie cam up front. It has a 5,500 mAh battery with 68W wired fast charging, dual-stereo speakers, Gorilla Glass 7i protection, IP68/IP69 water-dust protection, and runs Android 15 with Hello UI.', 7, 1, '', '{\"Processor\":\"Mediatek Dimensity 7400 (4 nm)\",\"Main Camera\":\"50 MP, f\\/1.8, 24mm (wide), 10 MP, f2.0, 73mm (telephoto), 50 MP, f\\/2.0, 12mm, 122\\u02da (ultrawide)\",\"Selfie Camera\":\"50 MP, f\\/2.0, (wide)\",\"Video Camera\":\"K@30fps, 1080p@30\\/60\\/120\\/240fps, gyro-EIS\",\"Display\":\"P-OLED, 1B colors, 120Hz, 720Hz PWM, HDR10+\",\"Battery\":\"5200mAh\",\"Operating System\":\"Android 15, up to 3 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-20 02:00:21'),
(31, 'Motorola Edge 60 Pro', 'Released in 2025, the Edge 60 Pro features a 6.7\" 120Hz quad-curved pOLED display (≈1.5K) with Gorilla Glass 7i and ~4,500 nits peak brightness. It’s powered by the Dimensity 8350 Extreme chip with up to 12 GB RAM and up to 512 GB storage. Cameras include a triple rear setup: 50MP wide (with OIS), 50MP ultra-wide, and 10MP telephoto (3×), plus a 50MP front cam. It’s backed by a 6,000 mAh battery with 90W fast wired charging, 15W wireless charging, and 5W reverse charging. Built durable with IP68/IP69 and MIL-STD-810H ratings; runs Android 15 (Hello UI).', 7, 1, '', '{\"Processor\":\"Mediatek Dimensity 8350 Extreme (4 nm)\",\"Main Camera\":\"50 MP, f\\/1.8, 24mm (wide), 10 MP, f2.0, 73mm (telephoto), 50 MP, f\\/2.0, 120\\u02da (ultrawide)\",\"Selfie Camera\":\"50 MP, f\\/2.0, (wide)\",\"Video Camera\":\"4K@30fps, 1080p@30\\/60\\/120\\/240fps, gyro-EIS, HDR10+\",\"Display\":\"P-OLED, 1B colors, 120Hz, 720Hz PWM, HDR10+\",\"Battery\":\"6000mAh\",\"Operating System\":\"Android 15, up to 3 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-20 02:09:22'),
(32, 'Motorola Razr 60', 'Released in mid-2025, the Razr 60 is Motorola’s flip-style foldable that balances style and functionality. It has a 6.9-inch inner pOLED LTPO display (120Hz, ~3000 nits) and a 3.6-inch cover display (90Hz, ~1700 nits) with Gorilla Glass Victus protection. It’s powered by the MediaTek Dimensity 7400X chip, with 8GB RAM + 256GB storage. The rear cameras include a 50MP main (with OIS) + 13MP ultra-wide; a 32MP front cam sits behind the fold. With a 4,500mAh battery, 30W wired + 15W wireless charging, IP48 rating, and Android 15 with Moto’s Hello UI, it’s a capable, stylish foldable.', 7, 1, '', '{\"Processor\":\"Mediatek Dimensity 7400X (4 nm)\",\"Main Camera\":\"50 MP, f\\/1.7, 25mm (wide), 13 MP, f\\/2.2, 120\\u02da (ultrawide)\",\"Selfie Camera\":\"32 MP, f\\/2.4, 25mm (wide)\",\"Video Camera\":\"4K@30fps, 1080p@30\\/60fps, gyro-EIS\",\"Display\":\"Foldable LTPO AMOLED, 1B colors, 120Hz, HDR10+, 6.9 inches\",\"Battery\":\"4500mAh\",\"Operating System\":\"Android 15\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-20 02:41:16'),
(33, 'Motorola Razr 60 Ultra', 'Released in April 2025, the Razr 60 Ultra is a premium flip-foldable featuring a 7.0-inch 1.5K LTPO pOLED inner display (165Hz) and a 4.0-inch cover screen, both with high brightness. Powered by the Snapdragon 8 Elite chip with 16GB RAM and up to 512GB storage. Dual rear cameras: 50MP main with OIS + 50MP ultra-wide (which also doubles as macro). It also has a 50MP inner selfie cam. Battery is 4,700mAh with 68W wired + 30W wireless + 5W reverse charging. Protected with Gorilla Glass Ceramic, eco-leather/wood/alcantara finishes, IP48 dust/water resistance, and runs Android 15 with Hello UI and Moto AI features.', 7, 1, '', '{\"Processor\":\"Qualcomm Snapdragon 8 Elite (3 nm)\",\"Main Camera\":\"50 MP, f\\/1.8, 24mm (wide), 50 MP, f\\/2.0, 12mm, 122\\u02da (ultrawide)\",\"Selfie Camera\":\"50 MP, f\\/2.0, (wide)\",\"Video Camera\":\"8K@30fps, 4K@30\\/60\\/120fps, 1080p@30\\/60\\/120\\/240fps, Dolby Vision HDR\",\"Display\":\"Foldable LTPO AMOLED, 1B colors, 165Hz, Dolby Vision, HDR10+\",\"Battery\":\"4700mAh\",\"Operating System\":\"Android 15\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-20 02:50:34'),
(34, 'IQOO Neo 10R', 'Released March 2025, the Neo 10R is a performance-midrange phone with 6.78-inch 1.5K AMOLED display (144Hz), powered by Snapdragon 8s Gen 3, 8-12GB RAM and up to 256GB storage. It has a 50MP main rear camera + 8MP ultra-wide, 32MP front cam, large 6,400 mAh battery with 80W wired fast charging. It weighs ~196g, IP65 rated, and runs Android 15 with Funtouch OS.', 8, 1, '', '{\"Processor\":\"Qualcomm Snapdragon 8s Gen 3 (4 nm)\",\"Main Camera\":\"50 MP, f\\/1.8, (wide), 8 MP, f\\/2.2, (ultrawide)\",\"Selfie Camera\":\"32 MP, f\\/2.5, (wide)\",\"Video Camera\":\"4K@30\\/60fps, 1080p, gyro-EIS, OIS\",\"Display\":\"AMOLED, 1B colors, 144Hz, 3840Hz PWM, HDR10+\",\"Battery\":\"6400mAh\",\"Operating System\":\"Android 15, up to 3 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-20 03:06:54'),
(35, 'IQOO Neo 10', 'iQOO Neo 10 is a high-performance mid-range phone launched in India in mid-2025. It has a 6.78-inch 1.5K AMOLED display with 144Hz refresh, powered by the Snapdragon 8s Gen 4 chip with up to 16GB RAM. It packs a huge 7,000 mAh battery with 120W fast charging. Camera setup includes a 50MP main sensor with OIS + 8MP ultra-wide, and a 32MP front cam. It offers IP65 dust/splash protection, includes Wi-Fi 7, and runs Android 15 with Funtouch OS 15.', 8, 1, '', '{\"Processor\":\"Qualcomm Snapdragon 8s Gen 4 (4 nm)\",\"Main Camera\":\"50 MP, f\\/1.8, (wide), 8 MP, f\\/2.2, (ultrawide)\",\"Selfie Camera\":\"32 MP, f\\/2.5, (wide)\",\"Video Camera\":\"4K@30\\/60fps, 1080p, gyro-EIS, OIS\",\"Display\":\"AMOLED, 1B colors, 144Hz, 4320Hz PWM, HDR, 6.78 inches\",\"Battery\":\"7000mAh\",\"Operating System\":\"Android 15, up to 3 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-20 10:42:59'),
(36, 'IQOO Neo 13', 'iQOO 13 is a flagship launched in late 2024 with a 6.82″ 2K LTPO AMOLED display (144Hz), Snapdragon 8 Elite chip, and up to 16GB RAM + 1TB storage. It has a triple-50MP camera setup (wide + ultra-wide + 2× telephoto), a 6,000-6,150 mAh battery with 120W wired fast charging, IP68/IP69 protection, and runs Android 15 with Funtouch OS 15.', 8, 1, 'Hot', '{\"Processor\":\"Qualcomm Snapdragon 8 Elite (3 nm)\",\"Main Camera\":\"50 MP, f\\/1.9, 23mm (wide), 50 MP, f\\/1.9, 50mm (telephoto), 50 MP, f\\/2.0, 15mm (ultrawide)\",\"Selfie Camera\":\"32 MP, f\\/2.5, 28mm (wide)\",\"Video Camera\":\"8K@30fps, 4K@24\\/30\\/60fps, 1080p@30\\/60\\/120\\/240fps, gyro-EIS\",\"Display\":\"LTPO AMOLED, 1B colors, 144Hz, 2592Hz PWM, HDR10+\",\"Battery\":\"6150mAh\",\"Operating System\":\"Android 15, up to 4 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-20 10:56:43'),
(37, 'Vivo X200', 'Vivo X200 is a 2024 flagship featuring a 6.67-inch AMOLED display at 120Hz with ~4500-nits peak brightness. It’s powered by the 3 nm Dimensity 9400 chip, paired with up to 16 GB RAM and 1 TB storage. It has a triple 50 MP rear camera setup (wide + 3× periscope telephoto + ultra-wide) and a 32 MP front cam. The phone includes a 5,800 mAh battery with 90W fast charging, IP68/IP69 water & dust resistance, and runs Android 15 with Vivo’s Funtouch OS 15.', 9, 1, '', '{\"Processor\":\"Mediatek Dimensity 9400 (3 nm)\",\"Main Camera\":\"50 MP, f\\/1.6, 23mm (wide), 50 MP, f\\/2.6, 70mm (periscope telephoto),  50 MP, f\\/2.0, 15mm, 119\\u02da (ultrawide)\",\"Selfie Camera\":\"32 MP, f\\/2.0, 20mm (ultrawide)\",\"Video Camera\":\"4K@30\\/60fps, 1080p@30\\/60\\/120\\/240fps, gyro-EIS\",\"Display\":\"AMOLED, 1B colors, 120Hz, 2160Hz PWM, HDR10+, 6.67 inches\",\"Battery\":\"5800mAh\",\"Operating System\":\"Android 15, up to 4 major Android upgrades,\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-20 11:06:58'),
(38, 'Vivo X200 Pro', 'Vivo X200 Pro is a premium flagship launched in late 2024. It features a 6.78-inch LTPO AMOLED display (120Hz, ~1.5K) with up to ~4,500 nits peak brightness. Powered by the 3 nm Dimensity 9400 chipset with 16 GB RAM, it offers top-tier performance. The camera system includes a 200MP periscope telephoto lens, a 50MP main wide sensor, and a 50MP ultra-wide, plus a 32MP front cam. It has a 6,000 mAh battery (with 90W wired & 30W wireless charging), IP68/IP69 water and dust protection, and runs Android 15 with Funtouch OS 15.', 9, 1, 'Hot', '{\"Processor\":\"Mediatek Dimensity 9400 (3 nm)\",\"Main Camera\":\"50 MP, f\\/1.6, 23mm (wide), 200 MP, f\\/2.7, 85mm (periscope telephoto),  50 MP, f\\/2.0, 15mm, 119\\u02da (ultrawide)\",\"Selfie Camera\":\"32 MP, f\\/2.0, 20mm (ultrawide)\",\"Video Camera\":\"8K@30fps, 4K@30\\/60\\/120fps, 1080p@30\\/60\\/120\\/240fps, gyro-EIS, 10-bit Log, Dolby Vision HDR\",\"Display\":\"LTPO AMOLED, 1B colors, 120Hz, 2160Hz PWM, HDR10+, Dolby Vision\",\"Battery\":\"6000mAh\",\"Operating System\":\"Android 15, up to 4 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-20 11:14:24'),
(39, 'Vivo X200 FE', 'The Vivo X200 FE is a compact flagship with a flat 6.31-inch 120Hz LTPO display, powered by the Dimensity 9300+ chip with up to 16GB RAM. It has a Zeiss-tuned triple rear camera system (50MP main with OIS + 3× periscope telephoto + ultra-wide), a 6,500mAh battery with 90W fast charging, IP68/IP69 water & dust protection, and runs Funtouch OS 15 on Android 15.', 9, 1, 'Trending', '{\"Processor\":\"Mediatek Dimensity 9300+ (4 nm)\",\"Main Camera\":\"50 MP, f\\/1.9, 23mm (wide), 50 MP, f\\/2.7, 70mm (periscope telephoto), 8 MP, f\\/2.0, 15mm (ultrawide)\",\"Selfie Camera\":\"50 MP, f\\/2.0, 20mm (wide)\",\"Video Camera\":\"4K@30\\/60fps, 1080p@30\\/60\\/120fps, gyro-EIS, HDR\",\"Display\":\"LTPO AMOLED, 1B colors, 120Hz, 2160Hz PWM, HDR10+, 6.31 inches\",\"Battery\":\"6500mAh\",\"Operating System\":\"Android 15, up to 4 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-20 11:22:58'),
(40, 'Vivo X Fold 5', 'The Vivo X Fold 5 is a premium foldable released in mid-2025, featuring an 8.03-inch LTPO inner display and a 6.53-inch cover screen, both with 120Hz refresh rates and up to 4,500 nits peak brightness. It runs on the Snapdragon 8 Gen 3 with 16 GB RAM and 512 GB storage. The ZEISS-tuned triple camera system includes 50MP wide, 50MP ultra-wide, and 50MP 3× telephoto lenses. Backed by a 6,000 mAh battery with 80W wired and 40W wireless charging, it also offers IPX8/IPX9 water resistance and IP5X dust protection, making it durable and powerful.', 9, 1, '', '{\"Processor\":\"Qualcomm Snapdragon 8 Gen 3 (4 nm)\",\"Main Camera\":\"50 MP, f\\/1.6, 23mm (wide), 50 MP, f\\/2.6, 70mm (periscope telephoto), zoom 50 MP, f\\/2.1, 15mm, 119\\u02da (ultrawide)\",\"Selfie Camera\":\"20 MP, f\\/2.4, 21mm (wide), Cover camera: 20 MP, f\\/2.4, 21mm (wide)\",\"Video Camera\":\"8K@30fps, 4K@30\\/60fps, 1080p@30\\/60fps, gyro-EIS\",\"Display\":\"Foldable LTPO AMOLED, 1B colors, 120Hz, 5280Hz PWM, HDR10+, Dolby Vision, 8.03 inches\",\"Battery\":\"6000mAh\",\"Operating System\":\"Android 15, up to 4 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-20 11:28:41'),
(41, 'Oppo Find X8', 'Released late 2024, the Find X8 features a 6.59-inch 120Hz LTPO AMOLED display with peak brightness of ~4500 nits. It is powered by the Dimensity 9400 chip with up to 16 GB RAM and up to 1 TB storage. It sports a triple 50MP rear camera setup (wide + ultra-wide + 3× periscope telephoto), a 32MP front camera, a 5630mAh battery with 80W wired + 50W wireless charging. It runs on ColorOS 15 (Android 15) and has IP68/IP69 water/dust resistance.', 10, 1, '', '{\"Processor\":\"Mediatek Dimensity 9400 (3 nm)\",\"Main Camera\":\"50 MP, f\\/1.8, 24mm (wide), 50 MP, f\\/2.6, 73mm (periscope telephoto), directional PDAF, OIS 50 MP, f\\/2.0, 15mm, 120\\u02da (ultrawide)\",\"Selfie Camera\":\"32 MP, f\\/2.4, 21mm (wide)\",\"Video Camera\":\"4K@30\\/60fps, 1080p@30\\/60\\/240fps; gyro-EIS; HDR, 10\\u2011bit video, Dolby Vision\",\"Display\":\"AMOLED, 1B colors, 120Hz, 3840Hz PWM, Dolby Vision, HDR10+, 6.59 inches\",\"Battery\":\"5630mAh\",\"Operating System\":\"Android 15, up to 5 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-20 11:35:11'),
(42, 'Oppo Find X8 Pro', 'Released October 2024, the Find X8 Pro features a 6.78-inch LTPO AMOLED display with 120Hz refresh and ~4,500 nit peak brightness. It runs on the Dimensity 9400 (3nm) chip with up to 16GB RAM and up to 1TB storage. The rear camera setup includes four 50MP sensors: wide (main), ultra-wide, 3× telephoto, and a 6× periscope zoom. It has a 5,910mAh silicon-carbon battery with 80W wired + 50W wireless charging. The phone is rated IP68/IP69, has curved glass edges, and ships with ColorOS 15 on Android 15.', 10, 1, '', '{\"Processor\":\"Mediatek Dimensity 9400 (3 nm)\",\"Main Camera\":\"50 MP, f\\/1.6, 23mm (wide), 50 MP, f\\/2.6, 73mm (periscope telephoto), directional PDAF, OIS 50 MP, f\\/4.3, 135mm (periscope telephoto), pixel PDAF (35cm - \\u221e), OIS 50 MP, f\\/2.0, 15mm, 120\\u02da (ultrawide)\",\"Selfie Camera\":\"32 MP, f\\/2.4, 21mm (wide)\",\"Video Camera\":\"4K@30\\/60fps, 1080p@30\\/60\\/240fps; gyro-EIS; HDR, 10\\u2011bit video, Dolby Vision\",\"Display\":\"LTPO AMOLED, 1B colors, 120Hz, 2160Hz PWM, Dolby Vision, HDR10+, 6.78 inches\",\"Battery\":\"5910mAh\",\"Operating System\":\"Android 15, up to 5 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-20 11:41:54'),
(43, 'Oppo Find N3 Flip', 'Released in late 2023, the OPPO Find N3 Flip is a compact foldable phone featuring a 6.8-inch AMOLED main display with a 120Hz refresh rate and a 3.26-inch cover screen for quick interactions. Powered by the MediaTek Dimensity 9200 chipset, it offers 12GB RAM and 256GB storage. The rear camera setup includes a 50MP main sensor, a 32MP ultra-wide lens, and a 48MP periscope telephoto lens, while the front camera is 32MP. It houses a 4,300mAh battery with 44W SUPERVOOC fast charging and runs on ColorOS 15 based on Android 15.', 10, 1, '', '{\"Processor\":\"Mediatek Dimensity 9200 (4 nm)\",\"Main Camera\":\"50 MP, f\\/1.8, 24mm (wide), 32 MP, f\\/2.0, 47mm (telephoto), 48 MP, f\\/2.2, 14mm, 114\\u02da (ultrawide)\",\"Selfie Camera\":\"32 MP, f\\/2.4, 21mm (wide)\",\"Video Camera\":\"4K@30fps, 1080p@30\\/60\\/240fps; gyro-EIS\",\"Display\":\"Foldable LTPO AMOLED, 120Hz, HDR10+\",\"Battery\":\"4300mAh\",\"Operating System\":\"Android 13, up to 4 major Android upgrades\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-20 12:53:12'),
(44, 'iPhone 17', 'Released September 2025, the iPhone 17 features a 6.3-inch Super Retina XDR OLED display with ProMotion (120Hz), Always-On mode, and enhanced brightness up to ~3000 nits outdoors. It’s powered by the new A19 chip built on a 3nm process, with 256GB as base storage and up to 512GB. The rear cameras have jumped to a dual 48MP setup (wide + ultra-wide) and the front camera is upgraded with 18MP & Center Stage features. It has improved durability using Ceramic Shield 2, IP68 protection, better battery life, 40W fast charging, and runs iOS 26.', 2, 1, 'New', '{\"Processor\":\"Apple A19 (3 nm)\",\"Main Camera\":\"48 MP, f\\/1.6, 26mm (wide), 48 MP, f\\/2.2, 13mm, 120\\u02da (ultrawide), \",\"Selfie Camera\":\"18 MP multi-aspect, f\\/1.9, 20mm (ultrawide)\",\"Video Camera\":\"4K@24\\/25\\/30\\/60fps, 1080p@25\\/30\\/60\\/120\\/240fps, HDR, Dolby Vision HDR (up to 60fps)\",\"Display\":\"LTPO Super Retina XDR OLED, 120Hz, HDR10\",\"Battery\":\"3692mAh\",\"Operating System\":\"iOS 26\",\"Connectivity\":\"LTE\\/5G\"}', '2025-09-21 03:16:33');

-- --------------------------------------------------------

--
-- Table structure for table `product_color_images`
--

CREATE TABLE `product_color_images` (
  `image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color` varchar(50) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_thumbnail` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_color_images`
--

INSERT INTO `product_color_images` (`image_id`, `product_id`, `color`, `image_url`, `is_thumbnail`) VALUES
(1, 1, 'Icyblue', 'Samsung/S25(Icyblue)(1)(1).webp', 1),
(2, 1, 'Icyblue', 'Samsung/S25(Icyblue)(2)(1).webp', 0),
(3, 1, 'Icyblue', 'Samsung/S25(Icyblue)(3)(1).webp', 0),
(4, 1, 'Mint', 'Samsung/S25(Mint)(1).webp', 1),
(5, 1, 'Mint', 'Samsung/S25(Mint)(2).webp', 0),
(6, 1, 'Mint', 'Samsung/S25(Mint)(3).webp', 0),
(7, 1, 'Navy', 'Samsung/S25(Navy)(1).webp', 1),
(8, 1, 'Navy', 'Samsung/S25(Navy)(2).webp', 0),
(9, 1, 'Navy', 'Samsung/S25(Navy)(3).webp', 0),
(10, 1, 'Silver Shadow', 'Samsung/S25(SilverShadow)(1).webp', 1),
(11, 1, 'Silver Shadow', 'Samsung/S25(SilverShadow)(2).webp', 0),
(12, 1, 'Silver Shadow', 'Samsung/S25(SilverShadow)(3).webp', 0),
(13, 4, 'Titanium Jetblack', 'Samsung/S25Edge(TitaniumJetBlack)(1).webp', 1),
(14, 4, 'Titanium Jetblack', 'Samsung/S25Edge(TitaniumJetBlack)(2).webp', 0),
(15, 4, 'Titanium Jetblack', 'Samsung/S25Edge(TitaniumJetBlack)(3).webp', 0),
(16, 4, 'Titanium Silver', 'Samsung/S25Edge(TitaniumSilver)(1).webp', 1),
(17, 4, 'Titanium Silver', 'Samsung/S25Edge(TitaniumSilver)(2).webp', 0),
(18, 4, 'Titanium Silver', 'Samsung/S25Edge(TitaniumSilver)(3).webp', 0),
(19, 2, 'Navy', 'Samsung/S25(Navy)(1)(1).webp', 1),
(20, 2, 'Navy', 'Samsung/S25(Navy)(2)(1).webp', 0),
(21, 2, 'Navy', 'Samsung/S25(Navy)(3)(1).webp', 0),
(22, 2, 'Silver Shadow', 'Samsung/S25(SilverShadow)(1)(1).webp', 1),
(23, 2, 'Silver Shadow', 'Samsung/S25(SilverShadow)(2)(1).webp', 0),
(24, 2, 'Silver Shadow', 'Samsung/S25(SilverShadow)(3)(1).webp', 0),
(25, 3, 'Titanium Black', 'Samsung/S25Ultra(TitaniumBlack)(1).webp', 1),
(26, 3, 'Titanium Black', 'Samsung/S25Ultra(TitaniumBlack)(2).webp', 0),
(27, 3, 'Titanium Black', 'Samsung/S25Ultra(TitaniumBlack)(3).webp', 0),
(28, 3, 'Titanium Gray', 'Samsung/S25Ultra(TitaniumGray)(1).webp', 1),
(29, 3, 'Titanium Gray', 'Samsung/S25Ultra(TitaniumGray)(2).webp', 0),
(30, 3, 'Titanium Gray', 'Samsung/S25Ultra(TitaniumGray)(3).webp', 0),
(31, 3, 'Titanium Silver Blue', 'Samsung/S25Ultra(TitaniumSilverblue)(1).webp', 1),
(32, 3, 'Titanium Silver Blue', 'Samsung/S25Ultra(TitaniumSilverblue)(2).webp', 0),
(33, 3, 'Titanium Silver Blue', 'Samsung/S25Ultra(TitaniumSilverblue)(3).webp', 0),
(34, 3, 'Titanium Whitesilver', 'Samsung/S25Ultra(TitaniumWhitesilver)(1).webp', 1),
(35, 3, 'Titanium Whitesilver', 'Samsung/S25Ultra(TitaniumWhitesilver)(2).webp', 0),
(36, 3, 'Titanium Whitesilver', 'Samsung/S25Ultra(TitaniumWhitesilver)(3).webp', 0),
(37, 9, 'Black', 'Samsung/ZFlip7FE(Black)(1).webp', 1),
(38, 9, 'Black', 'Samsung/ZFlip7FE(Black)(2).webp', 0),
(39, 9, 'Black', 'Samsung/ZFlip7FE(Black)(3).webp', 0),
(40, 9, 'Black', 'Samsung/ZFlip7FE(Black)(4).webp', 0),
(41, 9, 'Black', 'Samsung/ZFlip7FE(Black)(5).webp', 0),
(42, 9, 'White', 'Samsung/ZFlip7FE(White)(1).webp', 1),
(43, 9, 'White', 'Samsung/ZFlip7FE(White)(2).webp', 0),
(44, 9, 'White', 'Samsung/ZFlip7FE(White)(3).webp', 0),
(45, 9, 'White', 'Samsung/ZFlip7FE(White)(4).webp', 0),
(46, 9, 'White', 'Samsung/ZFlip7FE(White)(5).webp', 0),
(47, 8, 'Blue Shadow', 'Samsung/ZFilp7(BlueShadow)(1).webp', 1),
(48, 8, 'Blue Shadow', 'Samsung/ZFilp7(BlueShadow)(2).webp', 0),
(49, 8, 'Blue Shadow', 'Samsung/ZFilp7(BlueShadow)(3).webp', 0),
(50, 8, 'Blue Shadow', 'Samsung/ZFilp7(BlueShadow)(4).webp', 0),
(51, 8, 'Blue Shadow', 'Samsung/ZFilp7(BlueShadow)(5).webp', 0),
(52, 8, 'Coralred', 'Samsung/ZFlip7(CoralRed)(1).webp', 1),
(53, 8, 'Coralred', 'Samsung/ZFlip7(CoralRed)(2).webp', 0),
(54, 8, 'Coralred', 'Samsung/ZFlip7(CoralRed)(3).webp', 0),
(55, 8, 'Coralred', 'Samsung/ZFlip7(CoralRed)(4).webp', 0),
(56, 8, 'Coralred', 'Samsung/ZFlip7(CoralRed)(5).webp', 0),
(57, 8, 'Jetblack', 'Samsung/ZFlip7(JetBlack)(1).webp', 1),
(58, 8, 'Jetblack', 'Samsung/ZFlip7(JetBlack)(2).webp', 0),
(59, 8, 'Jetblack', 'Samsung/ZFlip7(JetBlack)(3).webp', 0),
(60, 8, 'Jetblack', 'Samsung/ZFlip7(JetBlack)(4).webp', 0),
(61, 8, 'Jetblack', 'Samsung/ZFlip7(JetBlack)(5).webp', 0),
(62, 7, 'Blue Shadow', 'Samsung/ZFold7(BlueShadow)(1).webp', 1),
(63, 7, 'Blue Shadow', 'Samsung/ZFold7(BlueShadow)(2).webp', 0),
(64, 7, 'Blue Shadow', 'Samsung/ZFold7(BlueShadow)(3).webp', 0),
(65, 7, 'Blue Shadow', 'Samsung/ZFold7(BlueShadow)(4).webp', 0),
(66, 7, 'Blue Shadow', 'Samsung/ZFold7(BlueShadow)(5).webp', 0),
(67, 7, 'Jet Black', 'Samsung/ZFold7(JetBlack)(1).webp', 1),
(68, 7, 'Jet Black', 'Samsung/ZFold7(JetBlack)(2).webp', 0),
(69, 7, 'Jet Black', 'Samsung/ZFold7(JetBlack)(3).webp', 0),
(70, 7, 'Jet Black', 'Samsung/ZFold7(JetBlack)(4).webp', 0),
(71, 7, 'Jet Black', 'Samsung/ZFold7(JetBlack)(5).webp', 0),
(72, 7, 'Silver Shadow', 'Samsung/ZFold7(SilverShadow)(1).webp', 1),
(73, 7, 'Silver Shadow', 'Samsung/ZFold7(SilverShadow)(2).webp', 0),
(74, 7, 'Silver Shadow', 'Samsung/ZFold7(SilverShadow)(3).webp', 0),
(75, 7, 'Silver Shadow', 'Samsung/ZFold7(SilverShadow)(4).webp', 0),
(76, 7, 'Silver Shadow', 'Samsung/ZFold7(SilverShadow)(5).webp', 0),
(77, 11, 'Black', 'Apple/iPhone16E(Black)(1).webp', 1),
(78, 11, 'Black', 'Apple/iPhone16E(Black)(2).webp', 0),
(79, 11, 'White', 'Apple/iPhone16E(White)(1).webp', 1),
(80, 11, 'White', 'Apple/iPhone16E(White)(2).webp', 0),
(81, 44, 'Black', 'Apple/iPhone17(Black)(1).webp', 1),
(82, 44, 'Black', 'Apple/iPhone17(Black)(2).webp', 0),
(83, 44, 'Levender', 'Apple/iPhone17(Lavender)(1).webp', 1),
(84, 44, 'Levender', 'Apple/iPhone17(Lavender)(2).webp', 0),
(85, 44, 'Mist Blue', 'Apple/iPhone17(MistBlue)(1).webp', 1),
(86, 44, 'Mist Blue', 'Apple/iPhone17(MistBlue)(2).webp', 0),
(87, 44, 'Sage', 'Apple/iPhone17(Sage)(1).webp', 1),
(88, 44, 'Sage', 'Apple/iPhone17(Sage)(2).webp', 0),
(89, 44, 'White', 'Apple/iPhone17(White)(1).webp', 1),
(90, 44, 'White', 'Apple/iPhone17(White)(2).webp', 0),
(91, 13, 'Cloud White', 'Apple/iPhone17Air(CloudWhite)(1).webp', 1),
(92, 13, 'Cloud White', 'Apple/iPhone17Air(CloudWhite)(2).webp', 0),
(93, 13, 'Light Gold', 'Apple/iPhone17Air(LightGold)(1).webp', 1),
(94, 13, 'Light Gold', 'Apple/iPhone17Air(LightGold)(2).webp', 0),
(95, 13, 'Sky Blue', 'Apple/iPhone17Air(SkyBlue)(1).webp', 1),
(96, 13, 'Sky Blue', 'Apple/iPhone17Air(SkyBlue)(2).webp', 0),
(97, 13, 'Space Black', 'Apple/iPhone17Air(SpaceBlack)(1).webp', 1),
(98, 13, 'Space Black', 'Apple/iPhone17Air(SpaceBlack)(2).webp', 0),
(99, 15, 'Cosmic Orange', 'Apple/iPhone17Pro(CosmicOrange)(1).webp', 1),
(100, 15, 'Cosmic Orange', 'Apple/iPhone17Pro(CosmicOrange)(2).webp', 0),
(101, 15, 'Deep Blue', 'Apple/iPhone17Pro(DeepBlue)(1).webp', 1),
(102, 15, 'Deep Blue', 'Apple/iPhone17Pro(DeepBlue)(2).webp', 0),
(103, 15, 'Silver', 'Apple/iPhone17Pro(Silver)(1).webp', 1),
(104, 15, 'Silver', 'Apple/iPhone17Pro(Silver)(2).webp', 0),
(105, 16, 'Deep Blue', 'Apple/iPhone17Pro(DeepBlue)(1)(1).webp', 1),
(106, 16, 'Deep Blue', 'Apple/iPhone17Pro(DeepBlue)(2)(1).webp', 0),
(107, 16, 'Silver', 'Apple/iPhone17Pro(Silver)(1)(1).webp', 1),
(108, 16, 'Silver', 'Apple/iPhone17Pro(Silver)(2)(1).webp', 0),
(109, 16, 'Cosmic Orange', 'Apple/iPhone17Pro(CosmicOrange)(1)(1).webp', 1),
(110, 16, 'Cosmic Orange', 'Apple/iPhone17Pro(CosmicOrange)(2)(1).webp', 0),
(111, 18, 'Frost', 'Google/Pixel10(Frost)(1).webp', 1),
(112, 18, 'Frost', 'Google/Pixel10(Frost)(2).webp', 0),
(113, 18, 'Frost', 'Google/Pixel10(Frost)(3).webp', 0),
(114, 18, 'Indigo', 'Google/Pixel10(Indigo)(1).webp', 1),
(115, 18, 'Indigo', 'Google/Pixel10(Indigo)(2).webp', 0),
(116, 18, 'Indigo', 'Google/Pixel10(Indigo)(3).webp', 0),
(117, 18, 'Lemongrass', 'Google/Pixel10(LemonGrass)(1).webp', 1),
(118, 18, 'Lemongrass', 'Google/Pixel10(LemonGrass)(2).webp', 0),
(119, 18, 'Lemongrass', 'Google/Pixel10(LemonGrass)(3).webp', 0),
(120, 18, 'Obsidian', 'Google/Pixel10(Obsidian)(1).webp', 1),
(121, 18, 'Obsidian', 'Google/Pixel10(Obsidian)(2).webp', 0),
(122, 18, 'Obsidian', 'Google/Pixel10(Obsidian)(3).webp', 0),
(123, 19, 'Moonstone', 'Google/Pixel10Pro(Moonstone)(1).webp', 1),
(124, 19, 'Moonstone', 'Google/Pixel10Pro(Moonstone)(2).webp', 0),
(125, 19, 'Moonstone', 'Google/Pixel10Pro(Moonstone)(3).webp', 0),
(126, 19, 'Porcelain', 'Google/Pixel10Pro(Porcelain)(1).webp', 1),
(127, 19, 'Porcelain', 'Google/Pixel10Pro(Porcelain)(2).webp', 0),
(128, 19, 'Porcelain', 'Google/Pixel10Pro(Porcelain)(3).webp', 0),
(129, 19, 'Jade', 'Google/Pixel10Pro(Jade)(1).webp', 1),
(130, 19, 'Jade', 'Google/Pixel10Pro(Jade)(2).webp', 0),
(131, 19, 'Jade', 'Google/Pixel10Pro(Jade)(3).webp', 0),
(132, 19, 'Obsidian', 'Google/Pixel10Pro(Obsidian)(1).webp', 1),
(133, 19, 'Obsidian', 'Google/Pixel10Pro(Obsidian)(2).webp', 0),
(134, 19, 'Obsidian', 'Google/Pixel10Pro(Obsidian)(3).webp', 0),
(135, 20, 'Obsidian', 'Google/Pixel10Pro(Obsidian)(1)(1).webp', 1),
(136, 20, 'Obsidian', 'Google/Pixel10Pro(Obsidian)(2)(1).webp', 0),
(137, 20, 'Obsidian', 'Google/Pixel10Pro(Obsidian)(3)(1).webp', 0),
(138, 20, 'Moonstone', 'Google/Pixel10Pro(Moonstone)(1)(1).webp', 1),
(139, 20, 'Moonstone', 'Google/Pixel10Pro(Moonstone)(2)(1).webp', 0),
(140, 20, 'Moonstone', 'Google/Pixel10Pro(Moonstone)(3)(1).webp', 0),
(141, 20, 'Jade', 'Google/Pixel10Pro(Jade)(1)(1).webp', 1),
(142, 20, 'Jade', 'Google/Pixel10Pro(Jade)(2)(1).webp', 0),
(143, 20, 'Jade', 'Google/Pixel10Pro(Jade)(3)(1).webp', 0),
(144, 17, 'Lris', 'Google/Pixel9A(Iris)(1).webp', 1),
(145, 17, 'Lris', 'Google/Pixel9A(Iris)(2).webp', 0),
(146, 17, 'Obsidian', 'Google/Pixel9A(Obsidian)(1).webp', 1),
(147, 17, 'Obsidian', 'Google/Pixel9A(Obsidian)(2).webp', 0),
(148, 17, 'Porcelain', 'Google/Pixel9A(Porcelain)(1).webp', 1),
(149, 17, 'Porcelain', 'Google/Pixel9A(Porcelain)(2).webp', 0),
(150, 21, 'Arctic Dawn', 'Oneplus/Oneplus13(ArcticDawn).webp', 1),
(151, 21, 'Black Eclipse', 'Oneplus/Oneplus13(BlackEclipse).webp', 1),
(152, 21, 'Midnight Ocean', 'Oneplus/Oneplus13(MidnightOcean).webp', 1),
(153, 22, 'Astral Trail', 'Oneplus/Oneplus13R(AstralTrail).webp', 1),
(154, 22, 'Nebula Noir', 'Oneplus/Oneplus13R(NebulaNoir).webp', 1),
(155, 23, 'Black Velvet', 'Oneplus/Oneplus13S(BlackVelvet).webp', 1),
(156, 23, 'Green Silk', 'Oneplus/Oneplus13S(GreenSilk).webp', 1),
(157, 23, 'Pink Satin', 'Oneplus/Oneplus13S(PinkSatin).webp', 1),
(158, 24, 'Emerald Dusk', 'Oneplus/OneplusOpen(EmeraldDusk)(1).webp', 1),
(159, 24, 'Emerald Dusk', 'Oneplus/OneplusOpen(EmeraldDusk)(2).webp', 0),
(160, 24, 'Voyager Black', 'Oneplus/OneplusOpen(VoyagerBlack)(1).webp', 1),
(161, 24, 'Voyager Black', 'Oneplus/OneplusOpen(VoyagerBlack)(2).webp', 0),
(162, 25, 'Black', 'Nothing/Nothing3(Black)(1).webp', 1),
(163, 25, 'Black', 'Nothing/Nothing3(Black)(2).webp', 0),
(164, 25, 'White', 'Nothing/Nothing3(White)(1).webp', 1),
(165, 25, 'White', 'Nothing/Nothing3(White)(2).webp', 0),
(166, 26, 'Blue', 'Nothing/Nothing3A(Blue)(1).webp', 1),
(167, 26, 'Blue', 'Nothing/Nothing3A(Blue)(2).webp', 0),
(168, 26, 'Black', 'Nothing/Nothing3A(Black)(1).webp', 1),
(169, 26, 'Black', 'Nothing/Nothing3A(Black)(2).webp', 0),
(170, 26, 'White', 'Nothing/Nothing3A(White)(1).webp', 1),
(171, 26, 'White', 'Nothing/Nothing3A(White)(2).webp', 0),
(172, 27, 'Grey', 'Nothing/Nothing3APro(Grey)(1).webp', 1),
(173, 27, 'Grey', 'Nothing/Nothing3APro(Grey)(2).webp', 0),
(174, 27, 'Black', 'Nothing/Nothing3APro(Black)(1).webp', 1),
(175, 27, 'Black', 'Nothing/Nothing3APro(Black)(2).webp', 0),
(176, 28, 'Green', 'Xiaomi/Xiomi15(Green).webp', 1),
(177, 28, 'Black', 'Xiaomi/Xiomi15(Black).webp', 1),
(178, 28, 'White', 'Xiaomi/Xiomi15(White).webp', 1),
(179, 29, 'Silver', 'Xiaomi/Xiomi15Ultra(Silver)(1).webp', 1),
(180, 30, 'PANTONE Gibraltar Sea', 'Motorola/Edge60(PANTONEGibraltarSea)(1).webp', 1),
(181, 30, 'PANTONE Gibraltar Sea', 'Motorola/Edge60(PANTONEGibraltarSea)(2).webp', 0),
(182, 30, 'PANTONE Gibraltar Sea', 'Motorola/Edge60(PANTONEGibraltarSea)(3).webp', 0),
(183, 30, 'PANTONE Shamrock', 'Motorola/Edge60(PANTONEShamrock)(1).webp', 1),
(184, 30, 'PANTONE Shamrock', 'Motorola/Edge60(PANTONEShamrock)(2).webp', 0),
(185, 30, 'PANTONE Shamrock', 'Motorola/Edge60(PANTONEShamrock)(3).webp', 0),
(186, 31, 'PANTONE Shadow', 'Motorola/Edge60Pro(PANTONEShadow)(1).webp', 1),
(187, 31, 'PANTONE Shadow', 'Motorola/Edge60Pro(PANTONEShadow)(2).webp', 0),
(188, 31, 'PANTONE Shadow', 'Motorola/Edge60Pro(PANTONEShadow)(3).webp', 0),
(189, 31, 'PANTONE Dazzling Blue', 'Motorola/Edge60Pro(PANTONEDazzlingBlue)(1).webp', 1),
(190, 31, 'PANTONE Dazzling Blue', 'Motorola/Edge60Pro(PANTONEDazzlingBlue)(2).webp', 0),
(191, 31, 'PANTONE Dazzling Blue', 'Motorola/Edge60Pro(PANTONEDazzlingBlue)(3).webp', 0),
(192, 31, 'PANTONE Sparkling Grape', 'Motorola/Edge60Pro(PANTONESparklingGrape)(1).webp', 1),
(193, 31, 'PANTONE Sparkling Grape', 'Motorola/Edge60Pro(PANTONESparklingGrape)(2).webp', 0),
(194, 31, 'PANTONE Sparkling Grape', 'Motorola/Edge60Pro(PANTONESparklingGrape)(3).webp', 0),
(195, 31, 'PANTONE Walnut', 'Motorola/Edge60Pro(PANTONEWalnut)(1).webp', 1),
(196, 31, 'PANTONE Walnut', 'Motorola/Edge60Pro(PANTONEWalnut)(2).webp', 0),
(197, 32, 'PANTONE Gibraltar Sea', 'Motorola/Razr60(PANTONEGibraltarSea)(1).webp', 1),
(198, 32, 'PANTONE Gibraltar Sea', 'Motorola/Razr60(PANTONEGibraltarSea)(2).webp', 0),
(199, 32, 'PANTONE Lightest Sky', 'Motorola/Razr60(PANTONELightestSky)(1).webp', 1),
(200, 32, 'PANTONE Lightest Sky', 'Motorola/Razr60(PANTONELightestSky)(2).webp', 0),
(201, 32, 'PANTONE Lightest Sky', 'Motorola/Razr60(PANTONELightestSky)(3).webp', 0),
(202, 32, 'PANTONE Gibraltar Sea', 'Motorola/Razr60(PANTONEGibraltarSea)(2)(2).webp', 0),
(203, 33, 'PANTONE Mountain Trail', 'Motorola/Razr60Ultra(PANTONEMountainTrail)(1).webp', 1),
(204, 33, 'PANTONE Mountain Trail', 'Motorola/Razr60Ultra(PANTONEMountainTrail)(2).webp', 0),
(205, 33, 'PANTONE Mountain Trail', 'Motorola/Razr60Ultra(PANTONEMountainTrail)(3).webp', 0),
(206, 33, 'PANTONE Mountain Trail', 'Motorola/Razr60Ultra(PANTONEMountainTrail)(4).webp', 0),
(207, 33, 'PANTONE Rio Red', 'Motorola/Razr60Ultra(PANTONERioRed)(1).webp', 1),
(208, 33, 'PANTONE Rio Red', 'Motorola/Razr60Ultra(PANTONERioRed)(2).webp', 0),
(209, 33, 'PANTONE Rio Red', 'Motorola/Razr60Ultra(PANTONERioRed)(3).webp', 0),
(210, 33, 'PANTONE Rio Red', 'Motorola/Razr60Ultra(PANTONERioRed)(4).webp', 0),
(211, 33, 'PANTONE Scarab', 'Motorola/Razr60Ultra(PANTONEScarab)(1).webp', 1),
(212, 33, 'PANTONE Scarab', 'Motorola/Razr60Ultra(PANTONEScarab)(2).webp', 0),
(213, 33, 'PANTONE Scarab', 'Motorola/Razr60Ultra(PANTONEScarab)(3).webp', 0),
(214, 33, 'PANTONE Scarab', 'Motorola/Razr60Ultra(PANTONEScarab)(4).webp', 0),
(215, 35, 'Inferno Red', 'Iqoo/IqooNeo10(InfernoRed)(1).webp', 1),
(216, 35, 'Inferno Red', 'Iqoo/IqooNeo10(InfernoRed)(2).webp', 0),
(217, 35, 'Inferno Red', 'Iqoo/IqooNeo10(InfernoRed)(3).webp', 0),
(218, 35, 'Titanium Chrome', 'Iqoo/IqooNeo10(TitaniumChrome)(1).webp', 1),
(219, 35, 'Titanium Chrome', 'Iqoo/IqooNeo10(TitaniumChrome)(2).webp', 0),
(220, 35, 'Titanium Chrome', 'Iqoo/IqooNeo10(TitaniumChrome)(3).webp', 0),
(221, 34, 'Raging Blue', 'Iqoo/IqooNeo10R(RagingBlue).webp', 1),
(222, 34, 'Moonknight Titanium', 'Iqoo/IqooNeo10R(MoonKnightTitanium).webp', 1),
(223, 36, 'Legend', 'Iqoo/Iqoo13(Legend).webp', 1),
(224, 36, 'Nardo Grey', 'Iqoo/Iqoo13(NardoGrey).webp', 1),
(277, 40, 'Titanium Grey', 'Vivo/X5Fold(TitaniumGrey)(1).webp', 1),
(278, 40, 'Titanium Grey', 'Vivo/X5Fold(TitaniumGrey)(2).webp', 0),
(279, 37, 'Natural Green', 'Vivo/X200(NaturalGreen)(1).webp', 1),
(280, 37, 'Natural Green', 'Vivo/X200(NaturalGreen)(2).webp', 0),
(281, 37, 'Natural Green', 'Vivo/X200(NaturalGreen)(3).webp', 0),
(282, 37, 'Cosmos Black', 'Vivo/X200(CosmosBlack)(1).webp', 1),
(283, 37, 'Cosmos Black', 'Vivo/X200(CosmosBlack)(2).webp', 0),
(284, 37, 'Cosmos Black', 'Vivo/X200(CosmosBlack)(3).webp', 0),
(285, 39, 'Luxe Grey', 'Vivo/X200FE(LuxeGrey)(1).webp', 1),
(286, 39, 'Luxe Grey', 'Vivo/X200FE(LuxeGrey)(2).webp', 0),
(287, 39, 'Luxe Grey', 'Vivo/X200FE(LuxeGrey)(3).webp', 0),
(288, 39, 'Frost Blue', 'Vivo/X200FE(FrostBlue)(1).webp', 1),
(289, 39, 'Frost Blue', 'Vivo/X200FE(FrostBlue)(2).webp', 0),
(290, 39, 'Frost Blue', 'Vivo/X200FE(FrostBlue)(3).webp', 0),
(291, 39, 'Amber Yellow', 'Vivo/X200FE(AmberYellow)(1).webp', 1),
(292, 39, 'Amber Yellow', 'Vivo/X200FE(AmberYellow)(2).webp', 0),
(293, 39, 'Amber Yellow', 'Vivo/X200FE(AmberYellow)(3).webp', 0),
(306, 38, 'Titanium Grey', 'Vivo/X200Pro(TitaniumGrey)(1).webp', 1),
(307, 38, 'Titanium Grey', 'Vivo/X200Pro(TitaniumGrey)(2).webp', 0),
(308, 38, 'Titanium Grey', 'Vivo/X200Pro(TitaniumGrey)(3).webp', 0),
(309, 38, 'Cosmos Black', 'Vivo/X200Pro(TitaniumBlack)(1).webp', 1),
(310, 38, 'Cosmos Black', 'Vivo/X200Pro(TitaniumBlack)(2).webp', 0),
(311, 38, 'Cosmos Black', 'Vivo/X200Pro(TitaniumBlack)(3).webp', 0),
(312, 43, 'Cream Gold', 'Oppo/FindN3Filp(CreamGold)(1).webp', 1),
(313, 43, 'Cream Gold', 'Oppo/FindN3Filp(CreamGold)(2).webp', 0),
(314, 43, 'Sleek Black', 'Oppo/FindN3Filp(SleekBlack)(1).webp', 1),
(315, 43, 'Sleek Black', 'Oppo/FindN3Filp(SleekBlack)(2).webp', 0),
(316, 41, 'Star Grey', 'Oppo/FindX8(StarGrey)(1).webp', 1),
(317, 41, 'Star Grey', 'Oppo/FindX8(StarGrey)(2).webp', 0),
(318, 41, 'Star Grey', 'Oppo/FindX8(StarGrey)(3).webp', 0),
(322, 41, 'Space Black', 'Oppo/FindX8(SpaceBlack)(1).webp', 1),
(323, 41, 'Space Black', 'Oppo/FindX8(SpaceBlack)(2).webp', 0),
(324, 41, 'Space Black', 'Oppo/FindX8(SpaceBlack)(3).webp', 0),
(328, 42, 'Space Black', 'Oppo/FindX8Pro(SpaceBlack)(1).webp', 1),
(329, 42, 'Space Black', 'Oppo/FindX8Pro(SpaceBlack)(2).webp', 0),
(330, 42, 'Space Black', 'Oppo/FindX8Pro(SpaceBlack)(3).webp', 0),
(331, 42, 'Peral White', 'Oppo/FindX8Pro(PearlWhite)(1).webp', 1),
(332, 42, 'Peral White', 'Oppo/FindX8Pro(PearlWhite)(2).webp', 0),
(333, 42, 'Peral White', 'Oppo/FindX8Pro(PearlWhite)(3).webp', 0),
(334, 3, 'Titanium Black', 'Samsung/S25Ultra(TitaniumBlack)(1).webp', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `variant_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `storage_gb` int(11) DEFAULT NULL,
  `ram_gb` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`variant_id`, `product_id`, `color`, `storage_gb`, `ram_gb`, `price`, `stock_quantity`) VALUES
(1, 1, 'Navy', 256, 12, 74999.00, 23),
(2, 1, 'Navy', 512, 12, 86999.00, 9),
(3, 1, 'Icyblue', 256, 12, 74999.00, 10),
(4, 1, 'Icyblue', 512, 12, 86999.00, 8),
(5, 1, 'Silver Shadow', 256, 12, 74999.00, 12),
(6, 1, 'Silver Shadow', 512, 12, 86999.00, 6),
(7, 1, 'Mint', 256, 12, 74999.00, 12),
(8, 1, 'Mint', 512, 12, 86999.00, 9),
(9, 2, 'Navy', 256, 12, 99999.00, 4),
(10, 2, 'Navy', 512, 12, 111999.00, 5),
(11, 2, 'Silver Shadow', 256, 12, 99999.00, 4),
(12, 2, 'Silver Shadow', 512, 12, 111999.00, 2),
(13, 3, 'Titanium Black', 256, 12, 123499.00, 3),
(14, 3, 'Titanium Black', 512, 12, 135499.00, 2),
(15, 3, 'Titanium Gray', 256, 12, 123499.00, 4),
(16, 3, 'Titanium Gray', 512, 12, 135499.00, 2),
(17, 3, 'Titanium Silver Blue', 256, 12, 123499.00, 3),
(18, 3, 'Titanium Silver Blue', 512, 12, 135499.00, 3),
(19, 3, 'Titanium Silver Blue', 1024, 12, 159499.00, 2),
(20, 3, 'Titanium Whitesilver', 256, 12, 123499.00, 4),
(21, 3, 'Titanium Whitesilver', 512, 12, 135499.00, 2),
(38, 7, 'Blue Shadow', 256, 12, 174999.00, 3),
(39, 7, 'Blue Shadow', 512, 12, 186999.00, 2),
(40, 7, 'Blue Shadow', 1024, 12, 216999.00, 3),
(41, 7, 'Jet Black', 256, 12, 174999.00, 2),
(42, 7, 'Jet Black', 512, 12, 186999.00, 3),
(43, 7, 'Jet Black', 1024, 12, 216999.00, 1),
(44, 7, 'Silver Shadow', 256, 12, 174999.00, 3),
(45, 7, 'Silver Shadow', 512, 12, 186999.00, 2),
(46, 7, 'Silver Shadow', 1024, 12, 216999.00, 1),
(47, 8, 'Blue Shadow', 256, 12, 109999.00, 3),
(48, 8, 'Blue Shadow', 512, 12, 121999.00, 5),
(49, 8, 'Coralred', 256, 12, 109999.00, 3),
(50, 8, 'Coralred', 512, 12, 121999.00, 2),
(51, 8, 'Jetblack', 256, 12, 109999.00, 2),
(52, 8, 'Jetblack', 512, 12, 121999.00, 1),
(53, 9, 'Black', 128, 8, 89999.00, 2),
(54, 9, 'Black', 256, 8, 95999.00, 1),
(55, 9, 'White', 128, 8, 89999.00, 2),
(56, 9, 'White', 256, 8, 95999.00, 3),
(72, 11, 'White', 128, 8, 54900.00, 5),
(73, 11, 'White', 256, 8, 64900.00, 3),
(74, 11, 'White', 512, 8, 84900.00, 4),
(75, 11, 'Black', 128, 8, 54900.00, 3),
(76, 11, 'Black', 256, 8, 64900.00, 3),
(77, 11, 'Black', 512, 8, 84900.00, 4),
(90, 13, 'Sky Blue', 256, 12, 119900.00, 17),
(91, 13, 'Sky Blue', 512, 12, 139900.00, 20),
(92, 13, 'Sky Blue', 1024, 12, 159900.00, 21),
(93, 13, 'Cloud White', 256, 12, 119900.00, 21),
(94, 13, 'Cloud White', 512, 12, 139900.00, 32),
(95, 13, 'Cloud White', 1024, 12, 159900.00, 29),
(96, 13, 'Light Gold', 256, 12, 119900.00, 4),
(97, 13, 'Light Gold', 512, 12, 139900.00, 3),
(98, 13, 'Light Gold', 1024, 12, 159900.00, 5),
(99, 13, 'Space Black', 256, 12, 119900.00, 6),
(100, 13, 'Space Black', 512, 12, 139900.00, 12),
(101, 13, 'Space Black', 1024, 12, 159900.00, 9),
(111, 15, 'Silver', 256, 12, 134900.00, 34),
(112, 15, 'Silver', 512, 12, 154900.00, 32),
(113, 15, 'Silver', 1024, 12, 174900.00, 24),
(114, 15, 'Deep Blue', 256, 12, 134900.00, 23),
(115, 15, 'Deep Blue', 512, 12, 154900.00, 32),
(116, 15, 'Deep Blue', 1024, 12, 174900.00, 12),
(117, 15, 'Cosmic Orange', 256, 12, 134900.00, 23),
(118, 15, 'Cosmic Orange', 512, 12, 154900.00, 25),
(119, 15, 'Cosmic Orange', 1024, 12, 174900.00, 30),
(120, 16, 'Cosmic Orange', 256, 12, 149900.00, 232),
(121, 16, 'Cosmic Orange', 512, 12, 169900.00, 20),
(122, 16, 'Cosmic Orange', 1024, 12, 189900.00, 12),
(123, 16, 'Cosmic Orange', 2048, 12, 229900.00, 40),
(124, 16, 'Deep Blue', 256, 12, 149900.00, 20),
(125, 16, 'Deep Blue', 512, 12, 169900.00, 19),
(126, 16, 'Deep Blue', 1024, 12, 189900.00, 172),
(127, 16, 'Deep Blue', 2048, 12, 229900.00, 21),
(128, 16, 'Silver', 256, 12, 149900.00, 20),
(129, 16, 'Silver', 512, 12, 169900.00, 232),
(130, 16, 'Silver', 1024, 12, 189900.00, 11),
(131, 16, 'Silver', 2048, 12, 229900.00, 9),
(132, 17, 'Lris', 256, 8, 49999.00, 21),
(133, 17, 'Obsidian', 256, 8, 49999.00, 19),
(134, 17, 'Porcelain', 256, 8, 49999.00, 21),
(135, 18, 'Indigo', 256, 12, 79999.00, 15),
(136, 18, 'Frost', 256, 12, 79999.00, 12),
(137, 18, 'Lemongrass', 256, 12, 79999.00, 14),
(138, 18, 'Obsidian', 256, 12, 79999.00, 2),
(139, 19, 'Obsidian', 256, 16, 109999.00, 15),
(140, 19, 'Jade', 256, 16, 109999.00, 20),
(141, 19, 'Moonstone', 256, 16, 109999.00, 8),
(142, 19, 'Porcelain', 256, 16, 109999.00, 4),
(143, 20, 'Moonstone', 256, 16, 124999.00, 23),
(144, 20, 'Jade', 256, 16, 124999.00, 32),
(145, 20, 'Obsidian', 256, 16, 124999.00, 19),
(146, 21, 'Midnight Ocean', 256, 12, 69999.00, 5),
(147, 21, 'Midnight Ocean', 512, 12, 78999.00, 9),
(148, 21, 'Arctic Dawn', 512, 16, 74999.00, 12),
(149, 21, 'Black Eclipse', 256, 12, 69999.00, 10),
(150, 22, 'Nebula Noir', 256, 12, 39999.00, 21),
(151, 22, 'Nebula Noir', 512, 12, 46999.00, 13),
(152, 22, 'Astral Trail', 256, 12, 39999.00, 12),
(153, 22, 'Astral Trail', 512, 12, 46999.00, 15),
(160, 24, 'Emerald Dusk', 512, 16, 124999.00, 4),
(161, 24, 'Voyager Black', 512, 16, 124999.00, 2),
(166, 26, 'White', 128, 8, 24999.00, 4),
(167, 26, 'White', 256, 8, 26999.00, 5),
(168, 26, 'Blue', 128, 8, 24999.00, 5),
(169, 26, 'Blue', 256, 8, 26999.00, 3),
(170, 26, 'Black', 128, 8, 24999.00, 2),
(171, 26, 'Black', 256, 8, 26999.00, 3),
(172, 27, 'Grey', 128, 8, 29999.00, 14),
(173, 27, 'Grey', 256, 8, 31999.00, 17),
(174, 27, 'Black', 128, 8, 29999.00, 12),
(175, 27, 'Black', 256, 8, 31999.00, 27),
(176, 28, 'Green', 512, 12, 64999.00, 12),
(177, 28, 'Black', 512, 12, 64999.00, 12),
(178, 28, 'White', 512, 12, 64999.00, 12),
(179, 29, 'Silver', 512, 12, 109999.00, 9),
(180, 30, 'PANTONE Gibraltar Sea', 256, 12, 25999.00, 18),
(181, 30, 'PANTONE Shamrock', 256, 12, 25999.00, 12),
(182, 31, 'PANTONE Shadow', 256, 12, 30999.00, 12),
(183, 31, 'PANTONE Shadow', 512, 12, 34999.00, 11),
(184, 31, 'PANTONE Dazzling Blue', 256, 12, 30999.00, 8),
(185, 31, 'PANTONE Dazzling Blue', 512, 12, 34999.00, 6),
(186, 31, ' PANTONE Sparkling Grape', 256, 12, 30999.00, 3),
(187, 31, ' PANTONE Sparkling Grape', 512, 12, 34999.00, 6),
(188, 31, 'PANTONE Walnut', 256, 12, 30999.00, 2),
(189, 31, 'PANTONE Walnut', 512, 12, 34999.00, 1),
(190, 32, 'PANTONE Gibraltar Sea', 256, 8, 39999.00, 4),
(191, 32, 'PANTONE Lightest Sky', 256, 8, 39999.00, 5),
(192, 32, 'PANTONE Spring Bud', 256, 8, 39999.00, 2),
(193, 33, 'PANTONE Rio Red', 512, 16, 97999.00, 5),
(194, 33, 'PANTONE Mountain Trail', 512, 16, 98999.00, 4),
(195, 33, 'PANTONE Scarab', 512, 16, 98999.00, 2),
(196, 34, 'Raging Blue', 128, 8, 25499.00, 6),
(197, 34, 'Raging Blue', 256, 8, 27999.00, 7),
(198, 34, 'Moonknight Titanium', 256, 12, 28999.00, 6),
(199, 35, 'Inferno Red', 128, 8, 32999.00, 12),
(200, 35, 'Inferno Red', 256, 8, 34499.00, 19),
(201, 35, 'Titanium Chrome', 128, 8, 32999.00, 14),
(202, 35, 'Titanium Chrome', 256, 8, 34499.00, 8),
(205, 37, 'Cosmos Black', 256, 12, 65999.00, 12),
(206, 37, 'Cosmos Black', 512, 12, 71999.00, 11),
(207, 37, 'Natural Green', 256, 12, 65999.00, 12),
(208, 37, 'Natural Green', 512, 12, 71999.00, 8),
(217, 40, 'Titanium Grey', 512, 16, 149999.00, 5),
(218, 41, 'Space Black', 256, 12, 69999.00, 3),
(219, 41, 'Space Black', 512, 12, 79999.00, 1),
(220, 41, 'Star Grey', 256, 12, 69999.00, 5),
(221, 41, 'Star Grey', 512, 12, 79999.00, 3),
(222, 42, 'Peral White', 512, 16, 99999.00, 2),
(223, 42, 'Space Black', 512, 16, 99999.00, 5),
(224, 43, 'Cream Gold', 256, 12, 48999.00, 4),
(225, 43, 'Sleek Black', 256, 12, 48999.00, 5),
(240, 44, 'Black', 256, 8, 82900.00, 12),
(241, 44, 'Black', 512, 8, 102900.00, 32),
(242, 44, 'Levender', 256, 8, 82900.00, 20),
(243, 44, 'Levender', 512, 8, 102900.00, 12),
(244, 44, 'Mist Blue', 256, 8, 82900.00, 12),
(245, 44, 'Mist Blue', 512, 8, 102900.00, 19),
(246, 44, 'Sage', 256, 8, 82900.00, 12),
(247, 44, 'Sage', 512, 8, 102900.00, 14),
(248, 44, 'White', 256, 8, 82900.00, 5),
(249, 44, 'White', 512, 8, 102900.00, 12),
(250, 23, 'Black Velvet', 256, 12, 52999.00, 12),
(251, 23, 'Black Velvet', 512, 12, 59999.00, 11),
(252, 23, 'Green Silk', 256, 12, 53499.00, 12),
(253, 23, 'Green Silk', 512, 12, 59999.00, 18),
(254, 23, 'Pink Satin', 256, 12, 54999.00, 11),
(255, 23, 'Pink Satin', 512, 12, 59999.00, 9),
(256, 25, 'Black', 256, 12, 79999.00, 10),
(257, 25, 'Black', 512, 12, 89999.00, 8),
(258, 25, 'White', 256, 12, 79999.00, 12),
(259, 25, 'White', 512, 12, 89999.00, 10),
(260, 36, 'Legend', 512, 16, 59999.00, 6),
(261, 36, 'Nardo Grey', 512, 16, 59999.00, 3),
(262, 39, 'Amber Yellow', 256, 12, 54999.00, 6),
(263, 39, 'Amber Yellow', 512, 12, 59999.00, 4),
(264, 39, 'Frost Blue', 256, 12, 54999.00, 4),
(265, 39, 'Frost Blue', 512, 12, 59999.00, 2),
(266, 39, 'Luxe Grey', 256, 12, 54999.00, 5),
(267, 39, 'Luxe Grey', 512, 12, 59999.00, 8),
(268, 38, 'Cosmos Black', 512, 16, 94999.00, 4),
(269, 38, 'Titanium Grey', 512, 16, 94999.00, 3),
(270, 4, 'Titanium Jetblack', 256, 12, 104999.00, 3),
(271, 4, 'Titanium Jetblack', 512, 12, 116999.00, 2),
(272, 4, 'Titanium Silver', 256, 12, 104999.00, 4),
(273, 4, 'Titanium Silver', 512, 12, 116999.00, 4);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `product_id`, `user_id`, `rating`, `comment`, `status`, `created_at`) VALUES
(3, 20, 3, 5, 'Best camera ever in Android device.', 'pending', '2025-10-03 18:13:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone_number`, `address`, `is_admin`, `created_at`) VALUES
(2, 'Admin', 'admin@gmail.com', '$2y$10$f8gH9a1Lj8.f3uP31oTF3OvC4nZ/Bi.zUPOpR6BJNC.yK8tjHVqcy', '9876543210', 'Ahmedabad, Gujarat, India', 1, '2025-09-21 07:20:25'),
(3, 'Yash Gajjar', 'yash@gmail.com', '$2y$10$50o43XFtBXkUFO7bdA7ES.86c/o1.UkOrRMlpeDYLE1.tb1Ua6QfW', '9876543210', 'Royal Tower, Indira Gandhi Marg, Jamnagar - 361005', 0, '2025-10-03 05:25:39');

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `selector` varchar(255) NOT NULL,
  `hashed_validator` varchar(255) NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`wishlist_item_id`, `user_id`, `product_id`, `added_at`) VALUES
(16, 3, 18, '2025-10-03 18:38:40'),
(17, 3, 39, '2025-10-03 18:43:11'),
(19, 3, 7, '2025-10-03 18:43:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`),
  ADD UNIQUE KEY `brand_name` (`brand_name`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `payment_method_id` (`payment_method_id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`payment_method_id`),
  ADD UNIQUE KEY `method_name` (`method_name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_color_images`
--
ALTER TABLE `product_color_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`variant_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_item_id`),
  ADD UNIQUE KEY `user_product_unique` (`user_id`,`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `payment_method_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `product_color_images`
--
ALTER TABLE `product_color_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=335;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `variant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=274;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`payment_method_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `product_color_images`
--
ALTER TABLE `product_color_images`
  ADD CONSTRAINT `product_color_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
