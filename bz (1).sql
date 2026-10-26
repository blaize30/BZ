-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 17, 2026 at 10:19 AM
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
-- Database: `bz`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Oil-Based Perfume'),
(2, 'Premium Oil-Based Perfume'),
(3, 'Car Diffuser'),
(4, 'Room/Linen Spray');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(150) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `customer_type` enum('Retailer','Reseller','Special') NOT NULL DEFAULT 'Retailer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_name`, `contact_number`, `email`, `customer_type`) VALUES
(1, 'Cynthia Agulto', '09123456789', 'cynthia@gmail.com', 'Special');

-- --------------------------------------------------------

--
-- Table structure for table `customer_special_prices`
--

CREATE TABLE `customer_special_prices` (
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `special_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_ledger`
--

CREATE TABLE `inventory_ledger` (
  `ledger_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `movement_id` int(11) NOT NULL,
  `quantity_change` int(11) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `order_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_ledger`
--

INSERT INTO `inventory_ledger` (`ledger_id`, `product_id`, `movement_id`, `quantity_change`, `timestamp`, `order_id`, `notes`) VALUES
(1, 16, 1, 100, '2026-07-16 19:16:32', NULL, 'Initial restock of Dior Sauvage 50ml'),
(2, 42, 1, 10, '2026-07-16 19:31:21', NULL, 'Initial stocks'),
(3, 42, 2, -2, '2026-07-16 19:40:31', 1, 'Sale - Order #1'),
(4, 16, 2, -1, '2026-07-16 19:40:31', 1, 'Sale - Order #1'),
(5, 145, 1, 10, '2026-07-16 19:57:14', NULL, 'Manual Inventory Adjustment'),
(6, 145, 2, -5, '2026-07-16 19:59:05', 2, 'Sale - Order #2'),
(7, 42, 2, -1, '2026-07-16 19:59:05', 2, 'Sale - Order #2'),
(8, 136, 1, 5, '2026-07-16 20:04:47', NULL, 'Manual Inventory Adjustment'),
(9, 136, 2, -2, '2026-07-16 20:07:08', 3, 'Sale - Order #3'),
(10, 42, 2, -1, '2026-07-16 20:07:08', 3, 'Sale - Order #3'),
(11, 42, 2, -1, '2026-07-17 12:17:24', 4, 'Sale - Order #4'),
(12, 189, 1, 25, '2026-07-17 12:42:08', NULL, 'Initial stock seed during product creation');

-- --------------------------------------------------------

--
-- Table structure for table `movement_types`
--

CREATE TABLE `movement_types` (
  `movement_id` int(11) NOT NULL,
  `movement_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movement_types`
--

INSERT INTO `movement_types` (`movement_id`, `movement_name`) VALUES
(1, 'Restock'),
(2, 'Sale'),
(3, 'Adjustment'),
(4, 'Return');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `order_timestamp` datetime DEFAULT current_timestamp(),
  `rep_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `delivery_status` enum('Preparing','Delivered') DEFAULT 'Preparing',
  `payment_mode` enum('Cash','Bank Transfer','GCash','Other') NOT NULL,
  `payment_status` enum('Unpaid','Paid') NOT NULL DEFAULT 'Paid',
  `payment_timestamp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_timestamp`, `rep_id`, `customer_id`, `delivery_status`, `payment_mode`, `payment_status`, `payment_timestamp`) VALUES
(1, '2026-07-16 19:40:31', 2, 1, 'Delivered', 'Bank Transfer', 'Paid', '2026-07-16 19:40:31'),
(2, '2026-07-16 19:59:05', 1, 1, 'Preparing', 'Cash', 'Paid', '2026-07-16 19:59:05'),
(3, '2026-07-16 20:07:08', 2, 1, 'Preparing', 'Cash', 'Paid', '2026-07-16 20:07:08'),
(4, '2026-07-17 12:17:24', 1, 1, 'Preparing', 'GCash', 'Unpaid', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_applied` decimal(10,2) NOT NULL,
  `commission_applied` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price_applied`, `commission_applied`) VALUES
(1, 1, 42, 2, 125.00, 0.00),
(2, 1, 16, 1, 125.00, 0.00),
(3, 2, 145, 5, 78.50, 0.00),
(4, 2, 42, 1, 132.48, 0.00),
(5, 3, 136, 2, 295.92, 0.00),
(6, 3, 42, 1, 133.16, 0.00),
(7, 4, 42, 1, 170.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `scent_id` int(11) NOT NULL,
  `raw_price` decimal(10,2) NOT NULL,
  `reseller_price` decimal(10,2) NOT NULL,
  `retail_price` decimal(10,2) NOT NULL,
  `commission_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `size_id`, `scent_id`, `raw_price`, `reseller_price`, `retail_price`, `commission_amount`) VALUES
(1, 1, 1, 1, 0.00, 80.00, 60.00, 0.00),
(2, 1, 2, 1, 0.00, 135.00, 170.00, 0.00),
(3, 1, 1, 2, 0.00, 80.00, 60.00, 0.00),
(4, 1, 2, 2, 0.00, 135.00, 170.00, 0.00),
(5, 1, 1, 3, 0.00, 80.00, 60.00, 0.00),
(6, 1, 2, 3, 0.00, 135.00, 170.00, 0.00),
(7, 1, 1, 4, 0.00, 80.00, 60.00, 0.00),
(8, 1, 2, 4, 0.00, 135.00, 170.00, 0.00),
(9, 1, 1, 5, 0.00, 80.00, 60.00, 0.00),
(10, 1, 2, 5, 0.00, 135.00, 170.00, 0.00),
(11, 1, 1, 6, 0.00, 80.00, 60.00, 0.00),
(12, 1, 2, 6, 0.00, 135.00, 170.00, 0.00),
(13, 1, 1, 7, 0.00, 80.00, 60.00, 0.00),
(14, 1, 2, 7, 0.00, 135.00, 170.00, 0.00),
(15, 1, 1, 8, 0.00, 80.00, 60.00, 0.00),
(16, 1, 2, 8, 0.00, 135.00, 170.00, 0.00),
(17, 1, 1, 9, 0.00, 80.00, 60.00, 0.00),
(18, 1, 2, 9, 0.00, 135.00, 170.00, 0.00),
(19, 1, 1, 10, 0.00, 80.00, 60.00, 0.00),
(20, 1, 2, 10, 0.00, 135.00, 170.00, 0.00),
(21, 1, 1, 11, 0.00, 80.00, 60.00, 0.00),
(22, 1, 2, 11, 0.00, 135.00, 170.00, 0.00),
(23, 1, 1, 12, 0.00, 80.00, 60.00, 0.00),
(24, 1, 2, 12, 0.00, 135.00, 170.00, 0.00),
(25, 1, 1, 13, 0.00, 80.00, 60.00, 0.00),
(26, 1, 2, 13, 0.00, 135.00, 170.00, 0.00),
(27, 1, 1, 14, 0.00, 80.00, 60.00, 0.00),
(28, 1, 2, 14, 0.00, 135.00, 170.00, 0.00),
(29, 1, 1, 15, 0.00, 80.00, 60.00, 0.00),
(30, 1, 2, 15, 0.00, 135.00, 170.00, 0.00),
(31, 1, 1, 16, 0.00, 80.00, 60.00, 0.00),
(32, 1, 2, 16, 0.00, 135.00, 170.00, 0.00),
(33, 1, 1, 17, 0.00, 80.00, 60.00, 0.00),
(34, 1, 2, 17, 0.00, 135.00, 170.00, 0.00),
(35, 1, 1, 18, 0.00, 80.00, 60.00, 0.00),
(36, 1, 2, 18, 0.00, 135.00, 170.00, 0.00),
(37, 1, 1, 19, 0.00, 80.00, 60.00, 0.00),
(38, 1, 2, 19, 0.00, 135.00, 170.00, 0.00),
(39, 1, 1, 25, 0.00, 80.00, 60.00, 0.00),
(40, 1, 2, 25, 0.00, 135.00, 170.00, 0.00),
(41, 1, 1, 26, 0.00, 80.00, 60.00, 0.00),
(42, 1, 2, 26, 0.00, 135.00, 170.00, 0.00),
(43, 1, 1, 27, 0.00, 80.00, 60.00, 0.00),
(44, 1, 2, 27, 0.00, 135.00, 170.00, 0.00),
(45, 1, 1, 28, 0.00, 80.00, 60.00, 0.00),
(46, 1, 2, 28, 0.00, 135.00, 170.00, 0.00),
(47, 1, 1, 29, 0.00, 80.00, 60.00, 0.00),
(48, 1, 2, 29, 0.00, 135.00, 170.00, 0.00),
(49, 1, 1, 30, 0.00, 80.00, 60.00, 0.00),
(50, 1, 2, 30, 0.00, 135.00, 170.00, 0.00),
(51, 1, 1, 31, 0.00, 80.00, 60.00, 0.00),
(52, 1, 2, 31, 0.00, 135.00, 170.00, 0.00),
(53, 1, 1, 32, 0.00, 80.00, 60.00, 0.00),
(54, 1, 2, 32, 0.00, 135.00, 170.00, 0.00),
(55, 1, 1, 33, 0.00, 80.00, 60.00, 0.00),
(56, 1, 2, 33, 0.00, 135.00, 170.00, 0.00),
(57, 1, 1, 34, 0.00, 80.00, 60.00, 0.00),
(58, 1, 2, 34, 0.00, 135.00, 170.00, 0.00),
(59, 1, 1, 35, 0.00, 80.00, 60.00, 0.00),
(60, 1, 2, 35, 0.00, 135.00, 170.00, 0.00),
(61, 1, 1, 36, 0.00, 80.00, 60.00, 0.00),
(62, 1, 2, 36, 0.00, 135.00, 170.00, 0.00),
(63, 1, 1, 37, 0.00, 80.00, 60.00, 0.00),
(64, 1, 2, 37, 0.00, 135.00, 170.00, 0.00),
(65, 1, 1, 38, 0.00, 80.00, 60.00, 0.00),
(66, 1, 2, 38, 0.00, 135.00, 170.00, 0.00),
(67, 1, 1, 39, 0.00, 80.00, 60.00, 0.00),
(68, 1, 2, 39, 0.00, 135.00, 170.00, 0.00),
(69, 1, 1, 40, 0.00, 80.00, 60.00, 0.00),
(70, 1, 2, 40, 0.00, 135.00, 170.00, 0.00),
(71, 1, 1, 41, 0.00, 80.00, 60.00, 0.00),
(72, 1, 2, 41, 0.00, 135.00, 170.00, 0.00),
(73, 1, 1, 42, 0.00, 80.00, 60.00, 0.00),
(74, 1, 2, 42, 0.00, 135.00, 170.00, 0.00),
(75, 1, 1, 43, 0.00, 80.00, 60.00, 0.00),
(76, 1, 2, 43, 0.00, 135.00, 170.00, 0.00),
(77, 1, 1, 44, 0.00, 80.00, 60.00, 0.00),
(78, 1, 2, 44, 0.00, 135.00, 170.00, 0.00),
(79, 1, 1, 45, 0.00, 80.00, 60.00, 0.00),
(80, 1, 2, 45, 0.00, 135.00, 170.00, 0.00),
(81, 1, 1, 46, 0.00, 80.00, 60.00, 0.00),
(82, 1, 2, 46, 0.00, 135.00, 170.00, 0.00),
(83, 1, 1, 47, 0.00, 80.00, 60.00, 0.00),
(84, 1, 2, 47, 0.00, 135.00, 170.00, 0.00),
(85, 1, 1, 48, 0.00, 80.00, 60.00, 0.00),
(86, 1, 2, 48, 0.00, 135.00, 170.00, 0.00),
(87, 1, 1, 49, 0.00, 80.00, 60.00, 0.00),
(88, 1, 2, 49, 0.00, 135.00, 170.00, 0.00),
(89, 1, 1, 50, 0.00, 80.00, 60.00, 0.00),
(90, 1, 2, 50, 0.00, 135.00, 170.00, 0.00),
(91, 1, 1, 51, 0.00, 80.00, 60.00, 0.00),
(92, 1, 2, 51, 0.00, 135.00, 170.00, 0.00),
(128, 2, 2, 20, 0.00, 300.00, 350.00, 0.00),
(129, 2, 2, 21, 0.00, 300.00, 350.00, 0.00),
(130, 2, 2, 22, 0.00, 300.00, 350.00, 0.00),
(131, 2, 2, 23, 0.00, 300.00, 350.00, 0.00),
(132, 2, 2, 24, 0.00, 300.00, 350.00, 0.00),
(133, 2, 2, 52, 0.00, 300.00, 350.00, 0.00),
(134, 2, 2, 53, 0.00, 300.00, 350.00, 0.00),
(135, 2, 2, 54, 0.00, 300.00, 350.00, 0.00),
(136, 2, 2, 55, 0.00, 300.00, 350.00, 0.00),
(137, 2, 2, 56, 0.00, 300.00, 350.00, 0.00),
(143, 3, 1, 57, 0.00, 90.00, 110.00, 0.00),
(144, 3, 1, 58, 0.00, 90.00, 110.00, 0.00),
(145, 3, 1, 59, 0.00, 90.00, 110.00, 0.00),
(146, 3, 1, 60, 0.00, 90.00, 110.00, 0.00),
(147, 3, 1, 61, 0.00, 90.00, 110.00, 0.00),
(148, 3, 1, 62, 0.00, 90.00, 110.00, 0.00),
(149, 3, 1, 63, 0.00, 90.00, 110.00, 0.00),
(150, 3, 1, 64, 0.00, 90.00, 110.00, 0.00),
(158, 4, 3, 57, 0.00, 100.00, 120.00, 0.00),
(159, 4, 4, 57, 0.00, 230.00, 0.00, 0.00),
(160, 4, 3, 58, 0.00, 100.00, 120.00, 0.00),
(161, 4, 4, 58, 0.00, 230.00, 0.00, 0.00),
(162, 4, 3, 59, 0.00, 100.00, 120.00, 0.00),
(163, 4, 4, 59, 0.00, 230.00, 0.00, 0.00),
(164, 4, 3, 60, 0.00, 100.00, 120.00, 0.00),
(165, 4, 4, 60, 0.00, 230.00, 0.00, 0.00),
(166, 4, 3, 61, 0.00, 100.00, 120.00, 0.00),
(167, 4, 4, 61, 0.00, 230.00, 0.00, 0.00),
(168, 4, 3, 62, 0.00, 100.00, 120.00, 0.00),
(169, 4, 4, 62, 0.00, 230.00, 0.00, 0.00),
(170, 4, 3, 63, 0.00, 100.00, 120.00, 0.00),
(171, 4, 4, 63, 0.00, 230.00, 0.00, 0.00),
(172, 4, 3, 64, 0.00, 100.00, 120.00, 0.00),
(173, 4, 4, 64, 0.00, 230.00, 0.00, 0.00),
(189, 1, 2, 67, 0.00, 135.00, 170.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `sales_representatives`
--

CREATE TABLE `sales_representatives` (
  `rep_id` int(11) NOT NULL,
  `rep_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_representatives`
--

INSERT INTO `sales_representatives` (`rep_id`, `rep_name`) VALUES
(1, 'Blaize'),
(2, 'Zaida'),
(5, 'Zhayne');

-- --------------------------------------------------------

--
-- Table structure for table `scents`
--

CREATE TABLE `scents` (
  `scent_id` int(11) NOT NULL,
  `scent_name` varchar(100) NOT NULL,
  `target_audience` enum('Him','Her','Unisex') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scents`
--

INSERT INTO `scents` (`scent_id`, `scent_name`, `target_audience`) VALUES
(1, 'Armani Acqua Di Gio', 'Him'),
(2, 'Bleu De Chanel', 'Him'),
(3, 'Bvlgari Aqua Amara', 'Him'),
(4, 'Bvlgari Extreme', 'Him'),
(5, 'Clinique Happy', 'Him'),
(6, 'CK One', 'Him'),
(7, 'Davidoff Cool Water', 'Him'),
(8, 'Dior Sauvage', 'Him'),
(9, 'Dior Fahrenheit', 'Him'),
(10, 'D&G Light Blue Intense', 'Him'),
(11, 'Hugo Boss', 'Him'),
(12, 'Issey Miyake', 'Him'),
(13, 'Lacoste Red', 'Him'),
(14, 'Lacoste Blue', 'Him'),
(15, 'Lacoste White', 'Him'),
(16, 'Paco Rabanne Invictus', 'Him'),
(17, 'Ralph Lauren Polo Blue', 'Him'),
(18, 'Ralph Lauren Polo Sport', 'Him'),
(19, 'Versace Eros', 'Him'),
(20, 'Explorer', 'Him'),
(21, 'Le Male', 'Him'),
(22, 'Eros', 'Him'),
(23, 'Sauvage', 'Him'),
(24, 'Coolwater Men', 'Him'),
(25, 'Angel\'s Breath', 'Her'),
(26, 'Ariana Grande Cloud', 'Her'),
(27, 'Ariana Grande Sweet Like Candy', 'Her'),
(28, 'Baccarat Rouge 540', 'Her'),
(29, 'Bvlgari Amethyste', 'Her'),
(30, 'Bvlgari Crystaline', 'Her'),
(31, 'Burberry London', 'Her'),
(32, 'Carolina Herrera Very Good Girl', 'Her'),
(33, 'Chanel Chance', 'Her'),
(34, 'Clinique Happy', 'Her'),
(35, 'D&G Light Blue', 'Her'),
(36, 'Davidoff Cool Water', 'Her'),
(37, 'Eclat D\'Arpege', 'Her'),
(38, 'Jo Malone English Pear & Freesia', 'Her'),
(39, 'Jo Malone Nectarine Blossom & Honey', 'Her'),
(40, 'Jo Malone Peony Blush Suede', 'Her'),
(41, 'Katy Perry Meow', 'Her'),
(42, 'Lacoste Touch Of Pink', 'Her'),
(43, 'Ms. Dior Blooming Bouquet', 'Her'),
(44, 'Paris Hilton', 'Her'),
(45, 'Versace Bright Crystal', 'Her'),
(46, 'Victoria Secret Bombshell', 'Her'),
(47, 'Victoria Secret Love Spell', 'Her'),
(48, 'Victoria Secret Pure Seduction', 'Her'),
(49, 'Victoria Secret Vanilla Lace', 'Her'),
(50, 'YSL Black Opium', 'Her'),
(51, 'YSL Libre', 'Her'),
(52, 'Idole', 'Her'),
(53, 'Delina', 'Her'),
(54, 'Valaya', 'Her'),
(55, 'Crystalline', 'Her'),
(56, 'Libre', 'Her'),
(57, 'Bamboo Fresh', 'Unisex'),
(58, 'Blackout', 'Unisex'),
(59, 'City Of Dreams', 'Unisex'),
(60, 'Coffee', 'Unisex'),
(61, 'Conrad Hotel', 'Unisex'),
(62, 'Lavender', 'Unisex'),
(63, 'Marine', 'Unisex'),
(64, 'Warm Cotton', 'Unisex'),
(67, 'Baccarat Rouge', 'Unisex');

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `size_id` int(11) NOT NULL,
  `volume_ml` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`size_id`, `volume_ml`) VALUES
(1, 10),
(2, 50),
(3, 100),
(4, 250);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `customer_special_prices`
--
ALTER TABLE `customer_special_prices`
  ADD PRIMARY KEY (`customer_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `inventory_ledger`
--
ALTER TABLE `inventory_ledger`
  ADD PRIMARY KEY (`ledger_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `movement_id` (`movement_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `movement_types`
--
ALTER TABLE `movement_types`
  ADD PRIMARY KEY (`movement_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `rep_id` (`rep_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `size_id` (`size_id`),
  ADD KEY `scent_id` (`scent_id`);

--
-- Indexes for table `sales_representatives`
--
ALTER TABLE `sales_representatives`
  ADD PRIMARY KEY (`rep_id`);

--
-- Indexes for table `scents`
--
ALTER TABLE `scents`
  ADD PRIMARY KEY (`scent_id`);

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`size_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory_ledger`
--
ALTER TABLE `inventory_ledger`
  MODIFY `ledger_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `movement_types`
--
ALTER TABLE `movement_types`
  MODIFY `movement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=192;

--
-- AUTO_INCREMENT for table `sales_representatives`
--
ALTER TABLE `sales_representatives`
  MODIFY `rep_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `scents`
--
ALTER TABLE `scents`
  MODIFY `scent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer_special_prices`
--
ALTER TABLE `customer_special_prices`
  ADD CONSTRAINT `customer_special_prices_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_special_prices_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_ledger`
--
ALTER TABLE `inventory_ledger`
  ADD CONSTRAINT `inventory_ledger_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `inventory_ledger_ibfk_2` FOREIGN KEY (`movement_id`) REFERENCES `movement_types` (`movement_id`),
  ADD CONSTRAINT `inventory_ledger_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`rep_id`) REFERENCES `sales_representatives` (`rep_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`size_id`),
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`scent_id`) REFERENCES `scents` (`scent_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
