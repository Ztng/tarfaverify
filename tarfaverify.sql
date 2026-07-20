-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 24, 2026 at 10:56 AM
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
-- Database: `tarfaverify`
--

-- --------------------------------------------------------

--
-- Table structure for table `bvn_searches`
--

CREATE TABLE `bvn_searches` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `bvn` varchar(11) NOT NULL,
  `amount_charged` decimal(10,2) NOT NULL DEFAULT 300.00,
  `status` varchar(20) DEFAULT 'Pending',
  `response_json` longtext DEFAULT NULL,
  `searched_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bvn_searches`
--

INSERT INTO `bvn_searches` (`id`, `user_email`, `bvn`, `amount_charged`, `status`, `response_json`, `searched_at`) VALUES
(1, 'zayyad@gmail.com', '11111111111', 300.00, 'Success', NULL, '2026-02-22 02:14:03'),
(2, 'zayyad@gmail.com', '09088282828', 1000.00, 'Pending', NULL, '2026-02-22 02:14:22'),
(3, 'zayyad@gmail.com', '22233444322', 300.00, 'Success', NULL, '2026-02-22 02:49:01'),
(4, 'zayyad@gmail.com', '22234332112', 300.00, 'Success', NULL, '2026-02-22 02:51:24'),
(5, 'zayyad1@gmail.com', '2659595989', 1000.00, 'Pending', NULL, '2026-02-22 14:46:15'),
(6, 'zayyad@gmail.com', '00000000000', 300.00, 'Success', NULL, '2026-02-23 20:23:35'),
(7, 'zayyad@gmail.com', '99099999999', 1000.00, 'Pending', NULL, '2026-02-23 20:24:30');

-- --------------------------------------------------------

--
-- Table structure for table `nin_searches`
--

CREATE TABLE `nin_searches` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `nin` varchar(11) NOT NULL,
  `amount_charged` decimal(10,2) NOT NULL DEFAULT 200.00,
  `response_json` longtext DEFAULT NULL,
  `searched_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nin_searches`
--

INSERT INTO `nin_searches` (`id`, `user_email`, `nin`, `amount_charged`, `response_json`, `searched_at`) VALUES
(1, 'zayyad@gmail.com', '33333333333', 200.00, 'Development Mode - Success', '2026-02-22 00:18:36'),
(2, 'zayyad@gmail.com', '', 200.00, 'Development Mode - Success', '2026-02-22 00:18:45'),
(3, 'zayyad@gmail.com', '', 200.00, 'Development Mode - Success', '2026-02-22 00:18:49'),
(4, 'zayyad@gmail.com', '07062649398', 200.00, 'Development Mode - Success', '2026-02-22 00:19:01'),
(5, 'zayyad@gmail.com', '', 200.00, 'Development Mode - Success', '2026-02-22 00:19:21'),
(6, 'zayyad@gmail.com', '12222222222', 200.00, 'Development Mode - Success', '2026-02-22 00:19:30'),
(7, 'zayyad@gmail.com', '11111111111', 200.00, 'Development Mode - Success', '2026-02-22 00:38:53'),
(8, 'zayyad@gmail.com', '11111122221', 200.00, NULL, '2026-02-22 01:31:52'),
(9, 'zayyad@gmail.com', '22222222222', 200.00, NULL, '2026-02-22 01:32:29'),
(10, 'zayyad@gmail.com', '11122333322', 150.00, 'Dev_Success', '2026-02-22 01:43:49'),
(11, 'zayyad@gmail.com', '23455555555', 150.00, 'Dev_Success', '2026-02-22 01:44:17'),
(12, 'zayyad@gmail.com', '11122222222', 150.00, 'Dev_Success', '2026-02-22 03:10:38'),
(13, 'zayyad@gmail.com', '22332223333', 200.00, 'Success', '2026-02-22 03:16:37'),
(14, 'zayyad@gmail.com', '34322334566', 150.00, 'Success', '2026-02-22 03:29:44'),
(15, 'zayyad@gmail.com', '25682888589', 150.00, 'Success', '2026-02-22 04:55:07'),
(16, 'zayyad@gmail.com', '67887766777', 180.00, 'Success', '2026-02-22 05:45:11'),
(17, 'zayyad1@gmail.com', '25682888589', 150.00, 'Success', '2026-02-22 14:45:40'),
(18, 'zayyad@gmail.com', '33443333333', 200.00, 'Success', '2026-02-23 20:27:12'),
(19, 'zayyad@gmail.com', '54355555555', 150.00, 'Success', '2026-02-23 20:27:27'),
(20, 'zayyad@gmail.com', '00009999999', 150.00, 'Success', '2026-02-23 22:31:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `wallet_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(20) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `email`, `password`, `wallet_balance`, `reset_token`, `reset_expires`, `created_at`, `role`) VALUES
(1, 'usman', 'zayyad', 'zee', 'zayyad@gmail.com', '$2y$10$GgKVyFnIJ2JQosHcCzJZ1.j10YZDb8RMg.1OIMuwUbHPPWViRylfq', 11025.00, NULL, NULL, '2026-02-21 01:36:52', 'user'),
(2, 'Sani', 'Usman', 'xz', 'saniq@gmail.com', '$2y$10$7gMLhayAI8xsn7Hjnz9Uv.OzeegMoQK3oI0mfpbfOxwED1X..dB7i', 0.00, NULL, NULL, '2026-02-21 02:05:21', 'user'),
(3, 'ZAYYAD', 'USMAN', 'Yy', 'zayyad1@gmail.com', '$2y$10$k6sRFFbQZPgmhr..SP8IbujNz.lxpPyG4fPXDxBczTf4V3ObNCVvu', 850.00, NULL, NULL, '2026-02-22 13:38:41', 'user'),
(4, 'jj', 'nn', 'zayyad@gmail.com', 'zayyadii@gmail.com', '$2y$10$lasekPfYH2PNeAIQhesNlO577O3UlhF9Il7kYmzCeIfkWE1sQRkOi', 0.00, NULL, NULL, '2026-02-22 13:51:21', 'user'),
(5, 'sani', 'niini', 'zayyad22@gmail.com', 'zayyad22@gmail.com', '$2y$10$92v4hcM8bDbem.8luXQXH.gQ.SOFpv8ELkOTt6XUWIPg6LKzYL.Dq', 0.00, NULL, NULL, '2026-02-22 13:51:55', 'user'),
(6, '', '', '', 'zeeboykd@gmail.com', '$2y$10$7R1.wK/pX9S6tC.qH/qEBe9H5lP7H8Gz8k5M6J6z5z5z5z5z5z5z5', 0.00, NULL, NULL, '2026-02-24 06:24:35', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `validation_requests`
--

CREATE TABLE `validation_requests` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `nin` varchar(11) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `purpose` varchar(100) DEFAULT NULL,
  `amount_charged` decimal(10,2) DEFAULT NULL,
  `status` enum('Pending','Completed','Failed') DEFAULT 'Pending',
  `response` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `validation_requests`
--

INSERT INTO `validation_requests` (`id`, `user_email`, `nin`, `category`, `purpose`, `amount_charged`, `status`, `response`, `created_at`, `completed_at`) VALUES
(1, 'zayyad@gmail.com', '11111111111', 'No Record', 'Sim', 500.00, 'Pending', 'Completed', '2026-02-24 03:01:35', NULL),
(2, 'zayyad@gmail.com', '11111111111', 'No Record', 'sss', 1000.00, 'Pending', 'Completed', '2026-02-24 03:31:22', NULL),
(3, 'zayyad@gmail.com', '23322222222', 'No Record', 'ww', 1000.00, 'Pending', 'Completed', '2026-02-24 03:32:10', NULL),
(4, 'zayyad@gmail.com', '22222222222', 'No Record', '22', 1000.00, 'Pending', NULL, '2026-02-27 03:33:40', NULL),
(5, 'zayyad@gmail.com', '23232323232', 'mod', 'sim card', 1500.00, 'Pending', NULL, '2026-02-24 03:37:38', NULL),
(6, 'zayyad@gmail.com', '90989889000', 'mod', 'hh', 1500.00, 'Pending', NULL, '2026-02-24 06:57:37', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bvn_searches`
--
ALTER TABLE `bvn_searches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_email` (`user_email`),
  ADD KEY `idx_bvn` (`bvn`),
  ADD KEY `idx_searched_at` (`searched_at`);

--
-- Indexes for table `nin_searches`
--
ALTER TABLE `nin_searches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_email` (`user_email`),
  ADD KEY `idx_nin` (`nin`),
  ADD KEY `idx_searched_at` (`searched_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `validation_requests`
--
ALTER TABLE `validation_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_email` (`user_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bvn_searches`
--
ALTER TABLE `bvn_searches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `nin_searches`
--
ALTER TABLE `nin_searches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `validation_requests`
--
ALTER TABLE `validation_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
