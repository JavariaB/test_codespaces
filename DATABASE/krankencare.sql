-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2023 at 09:44 PM
-- Server version: 10.4.8-MariaDB
-- PHP Version: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `krankencare`
--

-- --------------------------------------------------------

--
-- Table structure for table `beds`
--

CREATE TABLE `beds` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bed_name` varchar(100) NOT NULL,
  `is_available` tinyint(1) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `beds`
--

INSERT INTO `beds` (`id`, `user_id`, `bed_name`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 7, 'A2', 0, '2023-06-26', NULL),
(2, 7, 'B12', 0, '2023-06-26', NULL),
(3, 7, 'A211', 1, '2023-06-26', NULL),
(4, 7, 'A123', 0, '2023-06-26', NULL),
(5, 7, 'A24', 1, '2023-06-26', NULL),
(7, 5, 'B129', 1, '2023-06-26', NULL),
(8, 5, 'B167', 0, '2023-06-26', NULL),
(9, 5, 'u8', 1, '2023-06-26', NULL),
(12, 4, 'y6', 1, '2023-06-26', NULL),
(14, 4, 'A20', 1, '2023-06-26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL,
  `postal_code` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `postal_code`) VALUES
(3, 'Harry Potter', 'test3@domain.com', '$2y$10$nca3Jyc5hIunn.SuMSI12uZwbPxg6X7cqsp/UKm7nSe2HDDnagNum', 'ambulance_staff', NULL),
(4, 'Rottal-Inn Kliniken Pfarrkirchen', 'test4@domain.com', '$2y$10$NlSA8/p0PGWIF/XZFQU2ueHqpNOw.luKdcxbOnMo3rNYl4zCrweme', 'hospital_staff', '12345'),
(5, 'Hogwarts Hospital', 'test2@domain.com', '$2y$10$NphNZyIBlndhQ5GVtRRLTOPws.gVqS6ShJ8EzhconQrBl88AOR0p.', 'hospital_staff', '12345'),
(6, 'Mystic Falls Hospital', 'test@domain.com', '$2y$10$SkDe99y67719.aLDtAz2b.QEixwF8HKgofgmrg7R7WTSU97bwtduK', 'hospital_staff', '56789'),
(7, 'University Hospital Pfarrkirchen', 'test5@domain.com', '$2y$10$qPi1xNsqdl7zi0qT8PsgAO/3KdXrHBPkuvAHOS1YhJEFpzpwDOTsa', 'hospital_staff', '98765');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `beds`
--
ALTER TABLE `beds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `beds`
--
ALTER TABLE `beds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `beds`
--
ALTER TABLE `beds`
  ADD CONSTRAINT `beds_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
