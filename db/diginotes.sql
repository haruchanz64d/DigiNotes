-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2024 at 01:34 PM
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
-- Database: `diginotes`
--

-- --------------------------------------------------------

--
-- Table structure for table `dn_notes`
--

CREATE TABLE `dn_notes` (
  `dn_noteId` int(11) NOT NULL,
  `dn_noteTitle` varchar(255) NOT NULL,
  `dn_noteContent` varchar(255) NOT NULL,
  `dn_uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dn_notes`
--

INSERT INTO `dn_notes` (`dn_noteId`, `dn_noteTitle`, `dn_noteContent`, `dn_uid`) VALUES
(3, 'asd', 'asd', 1),
(4, 'asd', 'asd', 1),
(5, '123', '123', 2);

-- --------------------------------------------------------

--
-- Table structure for table `dn_users`
--

CREATE TABLE `dn_users` (
  `dn_uid` int(11) NOT NULL,
  `dn_username` varchar(255) NOT NULL,
  `dn_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dn_users`
--

INSERT INTO `dn_users` (`dn_uid`, `dn_username`, `dn_password`) VALUES
(1, 'test', '$2y$10$JKBgLKOWBpxTKSDIemUNMeb499TZNPSgIee6ASadk68xEI5AvVyPG'),
(2, '123', '$2y$10$XMbFbrU3wrWEwA48FEfeC..f3O10REQUIp1DwgWTlETR8cSDOYER2');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dn_notes`
--
ALTER TABLE `dn_notes`
  ADD PRIMARY KEY (`dn_noteId`),
  ADD KEY `FK_NoteList` (`dn_uid`);

--
-- Indexes for table `dn_users`
--
ALTER TABLE `dn_users`
  ADD PRIMARY KEY (`dn_uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dn_notes`
--
ALTER TABLE `dn_notes`
  MODIFY `dn_noteId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `dn_users`
--
ALTER TABLE `dn_users`
  MODIFY `dn_uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dn_notes`
--
ALTER TABLE `dn_notes`
  ADD CONSTRAINT `FK_NoteList` FOREIGN KEY (`dn_uid`) REFERENCES `dn_users` (`dn_uid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
