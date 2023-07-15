-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 14, 2023 at 03:11 PM
-- Server version: 5.7.33
-- PHP Version: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `family_trees`
--

-- --------------------------------------------------------

--
-- Table structure for table `family_tree`
--

CREATE TABLE `family_tree` (
  `family_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `jenis_kelamin` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `family_tree`
--

INSERT INTO `family_tree` (`family_id`, `parent_id`, `nama`, `jenis_kelamin`) VALUES
(1, NULL, 'Budi', 'Laki-laki'),
(2, 1, 'Dedi', 'Laki-laki'),
(3, 1, 'Dodi', 'Laki-laki'),
(4, 1, 'Dede', 'Laki-laki'),
(5, 1, 'Dewi', 'Perempuan'),
(6, 2, 'Feri', 'Laki-laki'),
(7, 2, 'Farah', 'Perempuan'),
(8, 3, 'Gugus', 'Laki-laki'),
(15, 3, 'Gandi', 'Laki-laki'),
(16, 4, 'Hani', 'Perempuan'),
(17, 4, 'Hana', 'Perempuan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `family_tree`
--
ALTER TABLE `family_tree`
  ADD PRIMARY KEY (`family_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `family_tree`
--
ALTER TABLE `family_tree`
  MODIFY `family_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
