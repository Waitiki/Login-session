-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2023 at 07:03 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dairy`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `phone_number`, `email`, `password`) VALUES
(1, 'joseph', '0790771314', 'joseph@gmail.com', '1234');

-- --------------------------------------------------------

--
-- Table structure for table `farmers`
--

CREATE TABLE `farmers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `location` varchar(255) NOT NULL,
  `password` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `farmers`
--

INSERT INTO `farmers` (`id`, `name`, `email`, `telephone`, `location`, `password`, `created_at`) VALUES
(1, 'Stephen Mugao', 'marthagithinji37@gmail.com', '078888888888', 'kiharu', '', '2023-11-09 13:26:47'),
(2, 'Stephen Mugao', 'marthagithinji37@gmail.com', '078888888888', 'kiharu', '', '2023-11-09 13:29:04'),
(3, 'Stephen Mugao', 'marthagithinji37@gmail.com', '078888888888', 'kiharu', '', '2023-11-09 13:31:07'),
(4, 'jeff kuria', 'marthagithinji37@gmail.com', '078888888888', 'mathioya', '', '2023-11-09 14:23:46'),
(5, 'jose', 'marthagithinji37@gmail.com', '7999999999', 'mrt', '$2y$10$jkEu7MzJWbmtD', '2023-11-14 17:02:18'),
(6, 'joan', 'marthagithinji37@gmail.com', '7999999999', 'kangema', '$2y$10$kvmx/pMmlljk8', '2023-11-14 17:03:03'),
(7, 'naina', 'naina@gmail.com', '7999999999', 'kangema', '$2y$10$TOC6C/Z/IjFlR', '2023-11-14 17:17:10'),
(8, 'naina', 'jm@gmail.com', '7999999999', 'kangema', '1234', '2023-11-14 17:27:29'),
(9, 'naina', 'naina@gmail.com', '7999999999', 'kangema', '1234', '2023-11-14 17:28:35');

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE `records` (
  `id` int(11) NOT NULL,
  `farmer_Id` int(11) NOT NULL,
  `farmer_name` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `records`
--

INSERT INTO `records` (`id`, `farmer_Id`, `farmer_name`, `quantity`, `date_time`) VALUES
(1, 1, 'Stephen Mugao', '2.00', '2023-11-14 20:12:00'),
(2, 1, 'Stephen Mugao', '2.00', '2023-11-14 20:12:00'),
(3, 1, 'Stephen Mugao', '2.00', '2023-11-14 20:12:00'),
(4, 4, 'jeff kuria', '2.00', '2023-11-14 20:12:00'),
(5, 4, 'jeff kuria', '2.00', '2023-11-14 20:12:00'),
(6, 4, 'jeff kuria', '2.00', '2023-11-14 20:12:00'),
(7, 5, 'jose', '2.00', '2023-11-14 20:46:00'),
(8, 5, 'jose', '2.00', '2023-11-14 20:46:00'),
(9, 5, 'jose', '2.00', '2023-11-14 20:46:00'),
(10, 5, 'jose', '2.00', '2023-11-08 20:46:00'),
(11, 5, 'jose', '2.00', '2023-11-08 20:46:00');

-- --------------------------------------------------------

--
-- Table structure for table `total_income`
--

CREATE TABLE `total_income` (
  `id` int(11) NOT NULL,
  `farmer_id` int(11) DEFAULT NULL,
  `total_income` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `total_income`
--

INSERT INTO `total_income` (`id`, `farmer_id`, `total_income`) VALUES
(1, 1, '3300.00'),
(2, 1, '3300.00'),
(3, 1, '3300.00'),
(4, 1, '3300.00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `farmers`
--
ALTER TABLE `farmers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `records`
--
ALTER TABLE `records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `total_income`
--
ALTER TABLE `total_income`
  ADD PRIMARY KEY (`id`),
  ADD KEY `farmer_id` (`farmer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `farmers`
--
ALTER TABLE `farmers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `records`
--
ALTER TABLE `records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `total_income`
--
ALTER TABLE `total_income`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `total_income`
--
ALTER TABLE `total_income`
  ADD CONSTRAINT `total_income_ibfk_1` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
