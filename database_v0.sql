-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2025 at 09:21 AM
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
-- Database: `database`
--

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `project_image` varchar(255) DEFAULT NULL,
  `material_image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `project_name` varchar(255) NOT NULL,
  `project_status` varchar(255) NOT NULL,
  `deadline` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `project_image`, `material_image`, `description`, `project_name`, `project_status`, `deadline`) VALUES
(21, 'zahur.jpeg', 'Screenshot_2025-04-14_160840.png', '- 1 Set mascot tung tung tung tung tung tung sahur \r\n- 1 Pcs excalibur pentungan\r\n- 1 Set mascot capu capu capucinno assasinooo\r\n- 1 Pcs katana', 'Tung Tung Tung Tung Sahur and Friends', 'Completed', '2025-04-22'),
(22, 'duu.png', 'DALL__E_2025-04-14_15.54.23_-_A_surreal_humanoid_character_named__TI_TI_IT___composed_of_a_bizarre_fusion_of_IT_hardware__a_chaotic_amalgamation_of_a_keyboard_torso__monitor_chest_.webp', '- Testing 1\r\n- Testing 2\r\n- Testing 3', 'Hik Vision CCTV Mascot', 'In Progress', '2025-04-30'),
(23, 'ChatGPT_Image_Apr_9__2025__08_59_01_AM.png', 'DALL__E_2025-04-14_15.54.23_-_A_surreal_humanoid_character_named__TI_TI_IT___composed_of_a_bizarre_fusion_of_IT_hardware__a_chaotic_amalgamation_of_a_keyboard_torso__monitor_chest_.webp', 'wewgwrgwr', 'Hik Vision CCTV Mascot', 'Completed', '2025-04-24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
