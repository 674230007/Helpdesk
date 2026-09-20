-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2026 at 06:12 PM
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
-- Database: `helpdesk`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`, `created_at`) VALUES
(1, 'อุปกรณ์ไอที', 'คอมพิวเตอร์, ปริ้นเตอร์, เมาส์, คีย์บอร์ด', '2026-09-17 03:00:12'),
(2, 'อาคารสถานที่', 'ไฟฟ้า, ประปา, หลอดไฟ, แอร์', '2026-09-17 03:00:12');

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `feedback_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `technician_id` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(150) NOT NULL,
  `status` enum('Pending','Assigned','In Progress','Completed','Cancelled') DEFAULT 'Pending',
  `priority` enum('Low','Medium','High','Urgent') DEFAULT 'Medium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`ticket_id`, `user_id`, `contact_email`, `category_id`, `technician_id`, `title`, `description`, `location`, `status`, `priority`, `created_at`, `updated_at`) VALUES
(6, 3, NULL, 1, NULL, 'คอมพัง', '1', 'อาคาร C', 'Pending', 'Medium', '2026-09-17 04:00:25', '2026-09-17 04:00:25'),
(7, 3, NULL, 1, NULL, 'คอมพัง', '1', 'อาคาร C', 'Pending', 'Medium', '2026-09-17 04:02:41', '2026-09-17 04:02:41'),
(8, 3, NULL, 1, NULL, 'พัดลมคอมพัง', '2', 'อาคาร C', 'Pending', 'Medium', '2026-09-17 04:04:20', '2026-09-17 04:04:20'),
(9, 3, NULL, 1, NULL, 'คอมพัง', '1', 'อาคาร C', 'Pending', 'Medium', '2026-09-17 04:08:39', '2026-09-17 04:08:39'),
(10, 3, NULL, 1, NULL, 'คอมพัง', '1', 'อาคาร C', 'Pending', 'Medium', '2026-09-17 04:11:00', '2026-09-17 04:11:00'),
(11, 3, NULL, 1, NULL, 'cpu มีปัญหา', '2 เครื่อง', 'อาคาร C', 'Pending', 'Medium', '2026-09-17 04:13:45', '2026-09-17 04:13:45'),
(12, 3, NULL, 1, 2, 'คอมพัง', '1', 'อาคาร C', 'Completed', 'Medium', '2026-09-17 04:17:05', '2026-09-17 04:18:28'),
(13, 3, NULL, 1, NULL, 'พัดลมพัง', '2', 'อาคาร C', 'Pending', 'Medium', '2026-09-17 04:21:27', '2026-09-17 04:21:27'),
(14, 3, NULL, 1, NULL, 'พัดลมพัง', '2', 'อาคาร C', 'Pending', 'Medium', '2026-09-17 04:24:16', '2026-09-17 04:24:16'),
(15, 3, NULL, 1, NULL, 'แอร์ไม่เย็น', '111', 'อาคาร C', 'Pending', 'Medium', '2026-09-17 04:26:56', '2026-09-17 04:26:56'),
(16, 3, NULL, 1, NULL, 'เมาส์พัง', 'พัง2ชิ้น', 'อาคาร C', 'Pending', 'Medium', '2026-09-17 14:37:57', '2026-09-17 14:37:57'),
(17, 3, NULL, 1, NULL, 'เมาส์พัง', 'พัง2ชิ้น', 'อาคาร C', 'Pending', 'Medium', '2026-09-17 14:46:01', '2026-09-17 14:46:01'),
(18, 3, NULL, 1, 5, 'เมาส์พัง', 'พัง2ชิ้น', 'อาคาร C', 'Assigned', 'Medium', '2026-09-17 14:53:35', '2026-09-17 15:53:25'),
(19, 3, NULL, 1, 2, 'แอร์พัง', '1', 'อาคาร C', 'Completed', 'Medium', '2026-09-17 14:54:41', '2026-09-17 14:55:16'),
(20, 3, NULL, 1, 2, 'โน๊ตบุ๊คเปิดไม่ติด', 'ต้องรีบใช้', 'อาคาร C', 'Completed', 'High', '2026-09-17 15:01:32', '2026-09-17 15:19:15'),
(21, 3, 'janasis110248@gmail.com', 1, 2, 'รถพัง', 'รีบมา', 'อาคาร B', 'Completed', 'High', '2026-09-17 15:17:13', '2026-09-17 15:18:17'),
(22, 3, 'janasis110248@gmail.com', 1, 2, 'แอร์ไม่เย็น', '1ตัว', 'อาคาร A', 'Completed', 'High', '2026-09-17 15:31:31', '2026-09-17 15:32:09'),
(23, 3, '674230034@webmail.npru.ac.th', 1, 2, 'คอมพัง', 'พัง 1 เครื่อง', 'อาคาร A', 'Completed', 'High', '2026-09-17 15:38:50', '2026-09-17 15:39:45');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_comments`
--

CREATE TABLE `ticket_comments` (
  `comment_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_images`
--

CREATE TABLE `ticket_images` (
  `image_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `uploader_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `image_type` enum('Before','After') DEFAULT 'Before',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` enum('User','Technician','Admin') DEFAULT 'User',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `full_name`, `email`, `phone`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ปภัสสร แย้มชื่น', '674230034@webmail.npru.ac.th', '0812345678', 'Admin', 1, '2026-09-17 03:00:12', '2026-09-17 15:57:50'),
(2, 'tech01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ช่างสมชาย ใจดี', 'tech1@repair.com', '0823456789', 'Technician', 1, '2026-09-17 03:00:12', '2026-09-17 03:00:12'),
(3, 'user01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'สมหญิง รักเรียน', 'user1@repair.com', '0834567890', 'User', 1, '2026-09-17 03:00:12', '2026-09-17 03:00:12'),
(4, 'tech02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ช่างวิชัย ใจดี', 'Technician@repair.com', '0950000000', 'Technician', 1, '2026-09-17 15:49:52', '2026-09-17 15:51:24'),
(5, 'tech03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ช่างประเสริฐ มั่นคง', 'Technician03@repair.com', '0958840000', 'Technician', 1, '2026-09-17 15:52:43', '2026-09-17 15:52:43'),
(6, 'admin02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ยศวดี หาญสีภูมิ', 'yotwadee49824982@gmail.com', '0958840121', 'Admin', 1, '2026-09-17 15:55:55', '2026-09-17 15:55:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`feedback_id`),
  ADD UNIQUE KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_tech` (`technician_id`);

--
-- Indexes for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ticket_images`
--
ALTER TABLE `ticket_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `uploader_id` (`uploader_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_images`
--
ALTER TABLE `ticket_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `feedbacks_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedbacks_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `system_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  ADD CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`technician_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  ADD CONSTRAINT `ticket_comments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `ticket_images`
--
ALTER TABLE `ticket_images`
  ADD CONSTRAINT `ticket_images_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_images_ibfk_2` FOREIGN KEY (`uploader_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
