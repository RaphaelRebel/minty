-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 15, 2025 at 02:19 PM
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
-- Database: `mintydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `domain` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `currency` char(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `parent_id`, `domain`, `status`, `price`, `currency`) VALUES
(20, 17, 'test.net', 'free', 16.00, 'EUR'),
(21, 17, 'test.org', 'free', 12.00, 'EUR'),
(22, 17, 'test.co', 'free', 19.00, 'EUR'),
(23, 17, 'test.shop', 'free', 11.00, 'EUR'),
(24, 17, 'test.dev', 'free', 12.00, 'EUR'),
(25, 17, 'test.amsterdam', 'free', 6.00, 'EUR'),
(26, 18, 'Hallo.net', 'free', 17.00, 'EUR'),
(27, 18, 'Hallo.io', 'free', 5.00, 'EUR'),
(28, 18, 'Hallo.co', 'free', 20.00, 'EUR'),
(29, 18, 'Hallo.nl', 'free', 20.00, 'EUR'),
(30, 19, 'doeidoei.org', 'free', 7.00, 'EUR'),
(31, 19, 'doeidoei.shop', 'free', 7.00, 'EUR'),
(32, 19, 'doeidoei.dev', 'free', 20.00, 'EUR'),
(33, 19, 'doeidoei.io', 'free', 13.00, 'EUR'),
(34, 20, 'haihai.amsterdam', 'free', 7.00, 'EUR'),
(35, 20, 'haihai.com', 'free', 9.00, 'EUR'),
(36, 20, 'haihai.net', 'free', 7.00, 'EUR'),
(37, 20, 'haihai.io', 'free', 11.00, 'EUR'),
(38, 20, 'test.net', 'free', 16.00, 'EUR'),
(39, 20, 'test.org', 'free', 12.00, 'EUR'),
(40, 21, 'test.net', 'free', 16.00, 'EUR'),
(41, 21, 'test.org', 'free', 12.00, 'EUR'),
(42, 21, 'test.co', 'free', 19.00, 'EUR');

-- --------------------------------------------------------

--
-- Table structure for table `orders_overview`
--

CREATE TABLE `orders_overview` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders_overview`
--

INSERT INTO `orders_overview` (`id`, `title`) VALUES
(11, '689ef989830b9'),
(12, '689ef99fda9dc'),
(13, '689ef9c9f3dfe'),
(14, '689f06ccbd595'),
(15, '689f06d62fb12'),
(16, '689f07093118e'),
(17, '689f11286fe45'),
(18, '689f12e711e20'),
(19, '689f17bfe6e74'),
(20, '689f17d763e0c'),
(21, '689f1f98b41c6');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders_overview`
--
ALTER TABLE `orders_overview`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `orders_overview`
--
ALTER TABLE `orders_overview`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
