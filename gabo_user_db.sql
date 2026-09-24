-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 24, 2026 at 11:59 AM
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
-- Database: `gabo_user_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(10) NOT NULL,
  `password` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `parking_logs`
--

CREATE TABLE `parking_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `slot_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `action` enum('occupied','released') NOT NULL,
  `logged_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking_logs`
--

INSERT INTO `parking_logs` (`id`, `slot_id`, `user_id`, `action`, `logged_at`) VALUES
(1, 2, 1, 'occupied', '2026-09-24 16:29:14'),
(2, 2, 1, 'released', '2026-09-24 16:29:21'),
(3, 6, 1, 'occupied', '2026-09-24 17:22:31'),
(4, 6, 1, 'released', '2026-09-24 17:22:50'),
(5, 6, 11, 'occupied', '2026-09-24 17:34:03'),
(6, 3, 1, 'occupied', '2026-09-24 17:34:42'),
(7, 6, 11, 'released', '2026-09-24 17:55:13'),
(8, 5, 11, 'occupied', '2026-09-24 17:55:23'),
(9, 5, 11, 'released', '2026-09-24 17:57:07'),
(10, 1, 11, 'occupied', '2026-09-24 17:57:18');

-- --------------------------------------------------------

--
-- Table structure for table `parking_slots`
--

CREATE TABLE `parking_slots` (
  `id` int(10) UNSIGNED NOT NULL,
  `slot_code` varchar(3) NOT NULL,
  `status` enum('available','occupied') NOT NULL DEFAULT 'available',
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `occupied_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking_slots`
--

INSERT INTO `parking_slots` (`id`, `slot_code`, `status`, `user_id`, `occupied_at`) VALUES
(1, 'A01', 'occupied', 11, '2026-09-24 17:57:18'),
(2, 'A02', 'available', NULL, NULL),
(3, 'A03', 'occupied', 1, '2026-09-24 17:34:42'),
(4, 'A04', 'available', NULL, NULL),
(5, 'B01', 'available', NULL, NULL),
(6, 'B02', 'available', NULL, NULL),
(7, 'B03', 'available', NULL, NULL),
(8, 'B04', 'available', NULL, NULL),
(9, 'C01', 'available', NULL, NULL),
(10, 'C02', 'available', NULL, NULL),
(11, 'C03', 'available', NULL, NULL),
(12, 'C04', 'available', NULL, NULL),
(13, 'D01', 'available', NULL, NULL),
(14, 'D02', 'available', NULL, NULL),
(15, 'D03', 'available', NULL, NULL),
(16, 'D04', 'available', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `student_number` varchar(40) NOT NULL,
  `email` varchar(160) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `student_number`, `email`, `password`, `created_at`) VALUES
(1, 'Lloyd Santiago', '01-2526-123456', 'jlloyd01@jggmmd.com', '$2y$10$GI68qjUSDkvCw9F6M9q/iuIPqatiY/DLTXFOZNoCCWVhX09272spa', '2026-09-24 08:28:48'),
(11, 'Emman Nerrie', '01-2526-123467', 'dadad@gghd.com', '$2y$10$t00c6WznA0ytx7LEPvvnbOV0IPwZUNOPXxGYMC9YcDcADb0fhVVO6', '2026-09-24 09:33:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parking_logs`
--
ALTER TABLE `parking_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_log_slot` (`slot_id`),
  ADD KEY `fk_log_user` (`user_id`);

--
-- Indexes for table `parking_slots`
--
ALTER TABLE `parking_slots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slot_code` (`slot_code`),
  ADD KEY `fk_slot_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_number` (`student_number`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `parking_logs`
--
ALTER TABLE `parking_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `parking_slots`
--
ALTER TABLE `parking_slots`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1201;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `parking_logs`
--
ALTER TABLE `parking_logs`
  ADD CONSTRAINT `fk_log_slot` FOREIGN KEY (`slot_id`) REFERENCES `parking_slots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `parking_slots`
--
ALTER TABLE `parking_slots`
  ADD CONSTRAINT `fk_slot_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
