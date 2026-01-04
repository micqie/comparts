-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 04, 2026 at 08:16 AM
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
-- Database: `ordering_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `description`) VALUES
(1, 'Processor', 'Computer CPUs'),
(2, 'Motherboard', 'Main circuit board'),
(3, 'RAM', 'Memory modules'),
(4, 'Storage', 'HDD and SSD storage devices'),
(5, 'Graphics Card', 'GPU for display and gaming'),
(6, 'Power Supply', 'PSU units'),
(7, 'Computer Case', 'PC chassis'),
(8, 'Cooling', 'Fans and cooling systems');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `user_id`, `full_name`, `email`, `contact_number`, `address`, `created_at`) VALUES
(1, 1, 'micahlago', 'miach@gmail.com', '01293091230', 'zone 3 upper, Brgy. Bulua, Cagayan de Oro City, Misamis Oriental', '2025-12-28 12:43:24'),
(2, 3, 'ASD', 'asd@gmail.com', '01293012', 'asdasdasd', '2025-12-28 13:00:50'),
(3, 4, 'sd', 'ds@gmail.com', '123123', 'sadasd', '2025-12-28 13:05:22'),
(4, 5, 'NORELYN', 'nor@gmail.com', '0909090123', 'asd', '2025-12-28 15:09:34'),
(5, 1, 'Juan Dela Cruz', 'juan@email.com', '09190001111', 'Manila, Philippines', '2025-12-31 17:57:57');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transactions`
--

CREATE TABLE `inventory_transactions` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `transaction_type` enum('in','out') NOT NULL,
  `quantity` int(11) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_transactions`
--

INSERT INTO `inventory_transactions` (`id`, `product_id`, `supplier_id`, `transaction_type`, `quantity`, `transaction_date`) VALUES
(1, 1, 1, 'in', 20, '2025-12-31 17:57:45'),
(2, 3, 2, 'in', 10, '2025-12-31 17:57:45'),
(3, 5, 1, 'in', 30, '2025-12-31 17:57:45'),
(4, 9, 3, 'in', 8, '2025-12-31 17:57:45'),
(5, 16, 2, 'in', 50, '2025-12-31 17:57:45'),
(6, 9, NULL, 'out', 1, '2025-12-31 17:58:19'),
(7, 10, NULL, 'out', 1, '2025-12-31 18:06:15'),
(8, 10, NULL, 'out', 1, '2025-12-31 18:09:17');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `order_date`, `total_amount`, `status`) VALUES
(1, 1, '2025-12-31 17:58:05', 14000.00, 'completed'),
(2, 4, '2025-12-31 17:58:18', 18500.00, 'pending'),
(3, 4, '2025-12-31 18:06:15', 16500.00, 'pending'),
(4, 4, '2025-12-31 18:09:17', 16500.00, 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 10500.00),
(2, 1, 5, 2, 1800.00),
(3, 2, 9, 1, 18500.00),
(4, 3, 10, 1, 16500.00),
(5, 4, 10, 1, 16500.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `product_name`, `price`, `stock_quantity`, `created_at`) VALUES
(1, 1, 'Intel Core i5-12400', 10500.00, 20, '2025-12-31 17:57:34'),
(2, 1, 'AMD Ryzen 5 5600X', 9800.00, 15, '2025-12-31 17:57:34'),
(3, 2, 'ASUS PRIME B660M-A', 6200.00, 10, '2025-12-31 17:57:34'),
(4, 2, 'MSI B550M PRO-VDH WIFI', 6500.00, 12, '2025-12-31 17:57:34'),
(5, 3, 'Kingston HyperX 8GB DDR4 3200MHz', 1800.00, 30, '2025-12-31 17:57:34'),
(6, 3, 'Corsair Vengeance 16GB DDR4 3200MHz', 3500.00, 25, '2025-12-31 17:57:34'),
(7, 4, 'Samsung 970 EVO Plus 500GB SSD', 4200.00, 18, '2025-12-31 17:57:34'),
(8, 4, 'Seagate Barracuda 1TB HDD', 2300.00, 40, '2025-12-31 17:57:34'),
(9, 5, 'NVIDIA RTX 3060 12GB', 18500.00, 7, '2025-12-31 17:57:34'),
(10, 5, 'AMD Radeon RX 6600', 16500.00, 8, '2025-12-31 17:57:34'),
(11, 6, 'Corsair CV650 650W PSU', 3200.00, 15, '2025-12-31 17:57:34'),
(12, 6, 'Cooler Master MWE 550W PSU', 2900.00, 18, '2025-12-31 17:57:34'),
(13, 7, 'NZXT H510 Mid Tower Case', 4200.00, 10, '2025-12-31 17:57:34'),
(14, 7, 'Cooler Master MasterBox Q300L', 3100.00, 14, '2025-12-31 17:57:34'),
(15, 8, 'Cooler Master Hyper 212 CPU Cooler', 1900.00, 20, '2025-12-31 17:57:34'),
(16, 8, 'DeepCool RF120 RGB Fan', 650.00, 50, '2025-12-31 17:57:34');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_name`, `contact_number`, `email`, `address`, `created_at`) VALUES
(1, 'TechZone Supplies', '09171234567', 'techzone@email.com', 'Quezon City', '2025-12-31 17:57:19'),
(2, 'PC Hub Distributor', '09181234567', 'pchub@email.com', 'Manila', '2025-12-31 17:57:19'),
(3, 'Digital Depot', '09201234567', 'digitaldepot@email.com', 'Cebu City', '2025-12-31 17:57:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'Micash', '$2y$10$DEOd1AQrAjxQP7Rj6Bhu1.gUzJ9H1F8uEwxMW6iVHnFrD7UZVJYd2', 'customer', '2025-12-28 12:43:24'),
(3, 'asd', '$2y$10$cBQ7IBnsjYQFj6uIwIcHzOyAu1bqMpZrRK7LwU6B7Xs.VKGsrYI/S', 'customer', '2025-12-28 13:00:50'),
(4, 'sdasd', '$2y$10$5aK7/9l1nQZtil.6Sg67muRirrt9Ye2O9.68A4XiugN6LLNyPnq/2', 'customer', '2025-12-28 13:05:22'),
(5, 'nor', '$2y$10$ocHdKn7vCTNiopde5ZuMB.vWDPgftmL13mLK9.oiX5dsPOIlJX9BS', 'customer', '2025-12-28 15:09:34'),
(8, 'admin@gmail.com', '$2y$10$VsKoEiFUxS9RUftQVtg.QuNrSfh5/4IYI/5q32nNeMsNrTD0Sjov.', 'admin', '2025-12-31 17:52:11'),
(9, 'customer1', 'hashed_password_here', 'customer', '2025-12-31 17:57:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD CONSTRAINT `inventory_transactions_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `inventory_transactions_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
