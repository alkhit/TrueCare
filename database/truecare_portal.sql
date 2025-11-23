-- Drop the database if it exists
DROP DATABASE IF EXISTS `truecare_portal`;

-- Create the database
CREATE DATABASE `truecare_portal` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE `truecare_portal`;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 23, 2025 at 11:56 PM
-- Server version: 11.6.2-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Table structure for table `campaigns`
--
CREATE TABLE `campaigns` (
  `campaign_id` int(11) NOT NULL,
  `orphanage_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('education','medical','food','shelter','clothing','other') NOT NULL,
  `target_amount` decimal(10,2) NOT NULL,
  `current_amount` decimal(10,2) DEFAULT 0.00,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('draft','active','completed','cancelled') DEFAULT 'draft',
  `deadline` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `donations`
--
CREATE TABLE `donations` (
  `donation_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('mpesa','card','paypal') NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `donation_date` timestamp NULL DEFAULT current_timestamp(),
  `message` text DEFAULT NULL,
  `is_anonymous` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `orphanages`
--
CREATE TABLE `orphanages` (
  `orphanage_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `location` varchar(200) DEFAULT NULL,
  `registration_number` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `contact_info` varchar(100) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('pending','verified','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orphanages`
--
INSERT INTO `orphanages` (`orphanage_id`, `user_id`, `name`, `location`, `registration_number`, `description`, `contact_info`, `image_url`, `status`, `created_at`) VALUES
(1, 4, 'Israel Mshindi', '', NULL, 'uiryfhgjou8y7t8ugh', NULL, NULL, 'verified', '2025-11-23 12:16:50'),
(4, 11, 'Healthy Kids', 'Kibera', NULL, 'Gives food to kids', NULL, NULL, 'verified', '2025-11-23 22:39:05');

--
-- Table structure for table `orphanage_registrations`
--
CREATE TABLE `orphanage_registrations` (
  `registration_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `pending_orphanage_changes`
--
CREATE TABLE `pending_orphanage_changes` (
  `change_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `orphanage_id` int(11) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pending_orphanage_changes`
--
INSERT INTO `pending_orphanage_changes` (`change_id`, `user_id`, `orphanage_id`, `name`, `location`, `description`, `status`, `created_at`) VALUES
(1, 4, NULL, 'Happy Homes', 'Langata', 'Home for boys', 'approved', '2025-11-23 12:24:08');

--
-- Table structure for table `users`
--
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('donor','orphanage','admin') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--
INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `is_active`, `phone`, `created_at`, `updated_at`, `last_login`) VALUES
(4, 'Israel Mshindi', 'israelmshindi78@gmail.com', '$2y$10$vCq.soEG9cYnHcv5GIEBjOZdPVXK0siujk3wu16FVmsWxCRacHmYW', 'admin', 1, '0703270251', '2025-11-23 10:57:05', '2025-11-23 22:54:52', '2025-11-23 22:54:52'),
(5, 'Kaye Keith', 'kaye4@gmail.com', '$2y$10$e7hMnJ/w.q/bWJEB.ixqwOT5J9XXdikPh2m2X1nh4AhWlVWjPI3pe', 'donor', 1, '0115324321', '2025-11-23 12:26:27', '2025-11-23 22:11:35', '2025-11-23 22:11:35'),
(11, 'Baba Pima', 'babapima@gmail.com', '$2y$10$jUFyMNM9yjh8ez9vNtbxN.WGablQe/tvNcFeYeZrWUP96RtZOG0li', 'orphanage', 1, '0703270251', '2025-11-23 22:38:39', '2025-11-23 22:39:37', '2025-11-23 22:39:37');

--
-- Indexes for dumped tables
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`campaign_id`),
  ADD KEY `orphanage_id` (`orphanage_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_category` (`category`);

ALTER TABLE `donations`
  ADD PRIMARY KEY (`donation_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_campaign_id` (`campaign_id`);

ALTER TABLE `orphanages`
  ADD PRIMARY KEY (`orphanage_id`),
  ADD UNIQUE KEY `registration_number` (`registration_number`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_user_id` (`user_id`);

ALTER TABLE `orphanage_registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `pending_orphanage_changes`
  ADD PRIMARY KEY (`change_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `orphanage_id` (`orphanage_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--
ALTER TABLE `campaigns`
  MODIFY `campaign_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `donations`
  MODIFY `donation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `orphanages`
  MODIFY `orphanage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `orphanage_registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `pending_orphanage_changes`
  MODIFY `change_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--
ALTER TABLE `campaigns`
  ADD CONSTRAINT `campaigns_ibfk_1` FOREIGN KEY (`orphanage_id`) REFERENCES `orphanages` (`orphanage_id`) ON DELETE CASCADE;

ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `donations_ibfk_2` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`) ON DELETE CASCADE;

ALTER TABLE `orphanages`
  ADD CONSTRAINT `orphanages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `orphanage_registrations`
  ADD CONSTRAINT `orphanage_registrations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `pending_orphanage_changes`
  ADD CONSTRAINT `pending_orphanage_changes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pending_orphanage_changes_ibfk_2` FOREIGN KEY (`orphanage_id`) REFERENCES `orphanages` (`orphanage_id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
