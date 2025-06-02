-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2025 at 09:53 AM
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
-- Database: `inventory_teacher2`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `account_id` int(11) NOT NULL,
  `un` varchar(255) NOT NULL,
  `pw` varchar(255) NOT NULL,
  `account_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`account_id`, `un`, `pw`, `account_type_id`) VALUES
(3, 'mimi', '$2y$10$xwATyHuSUWJ4Kc7B4lDsAO.MGqDFn7JgLFPvdfS5vK7qDP8108AoS', 1),
(6, 'admin', '$2y$10$c8ysb..pFjOk/ellzle.ReOEXxcjhoaaa47KgZ2407J1i8f1QNIc6', 2),
(14, 'jake', '$2y$10$T1OYeYVwAuGn8K5/oExJkOS/ffgCNkAN8zGsrqkja/zx5QjafwjJC', 1),
(17, 'rino', '$2y$10$2O90uGCV8fwB6arPigWla.jEnC6KguwGKO4SzURCDyBMPuRC.bUzW', 1),
(20, 'Athens', '$2y$10$P3QXWSevc1TtXE9KWG1QY.iT9NiyiyZKJyFD8LMlhh/t/TkcZOkBi', 1),
(23, 'mimiyu', '$2y$10$2PxzogOHpcvetIUBtJrX5.R3RcJikYfOSYvQuwGw1f.Tya0YeELSa', 1),
(24, 'jake1', '$2y$10$NpEkYt6H/BdXOMJHtWQi4.0aW99dVdgcOg.yLaOp0tdQwDPaGJZnu', 1);

-- --------------------------------------------------------

--
-- Table structure for table `account_type`
--

CREATE TABLE `account_type` (
  `account_type_id` int(11) NOT NULL,
  `account_type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account_type`
--

INSERT INTO `account_type` (`account_type_id`, `account_type_name`) VALUES
(2, 'Admin'),
(1, 'Teacher Account');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `department_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`department_id`, `name`) VALUES
(2, 'Junior High'),
(1, 'Senior High');

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `teacher_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `fn` varchar(100) NOT NULL,
  `ln` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `account_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`teacher_id`, `department_id`, `fn`, `ln`, `email`, `contact_number`, `account_id`) VALUES
(1, 1, 'mimi', 'sarana', 'fernandosaranajr@gmail.com', '090809', 3),
(4, 1, 'JAKE ', 'SABOLBORO', 'dddd@gmail.com', '0908099', 14),
(6, 2, 'JOHN ', 'RINO', 'sad@GMAIL.COM', '090809', 17),
(8, 1, 'Athena', 'Torres', 'athenaayish@gmail.com', '092938923891', 20),
(11, 1, 'Lorie Mae', 'Santillan', 'mimi@gmail.com', '235686666', 23),
(12, 2, 'JAKE ', 'SABOLBORO', 'ddd@gmail.com', '092938923891', 24);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_detail`
--

CREATE TABLE `teacher_detail` (
  `teacher_detail_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `item` varchar(255) NOT NULL,
  `specs` text DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `return` int(11) DEFAULT 0,
  `remark` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_detail`
--

INSERT INTO `teacher_detail` (`teacher_detail_id`, `teacher_id`, `item`, `specs`, `qty`, `unit`, `return`, `remark`) VALUES
(1, 1, 'BOOK', 'haha', 100, '0', 99, 'guba '),
(2, 4, 'BOOK', 'none', 100, '0', 10, 'basa'),
(3, 6, 'computer', 'none', 40, '0', 10, 'goods'),
(4, 4, 'chair', 'none', 50, '0', 6, ''),
(5, 4, 'IT HANDBOOK', 'none', 60, '0', 5, 'goods'),
(6, 8, 'book', '', 3, '0', 0, 'very good'),
(9, 11, 'laptop', 'i3', 6, '0', 3, 'guba'),
(10, 12, 'computer', 'core 5', 5, 'pcs', 10, 'guba ang tana');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `un` (`un`),
  ADD KEY `account_type_id` (`account_type_id`);

--
-- Indexes for table `account_type`
--
ALTER TABLE `account_type`
  ADD PRIMARY KEY (`account_type_id`),
  ADD UNIQUE KEY `account_type_name` (`account_type_name`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`department_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `account_id` (`account_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `teacher_detail`
--
ALTER TABLE `teacher_detail`
  ADD PRIMARY KEY (`teacher_detail_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `account_type`
--
ALTER TABLE `account_type`
  MODIFY `account_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `teacher_detail`
--
ALTER TABLE `teacher_detail`
  MODIFY `teacher_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account`
--
ALTER TABLE `account`
  ADD CONSTRAINT `account_ibfk_1` FOREIGN KEY (`account_type_id`) REFERENCES `account_type` (`account_type_id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher`
--
ALTER TABLE `teacher`
  ADD CONSTRAINT `teacher_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_detail`
--
ALTER TABLE `teacher_detail`
  ADD CONSTRAINT `teacher_detail_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
