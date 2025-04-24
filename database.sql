-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 24 Apr 2025 pada 04.53
-- Versi server: 8.0.39
-- Versi PHP: 8.3.12

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
-- Struktur dari tabel `gallery`
--

CREATE TABLE `gallery` (
  `id` int NOT NULL,
  `project_image` text COLLATE utf8mb4_general_ci,
  `material_image` text COLLATE utf8mb4_general_ci,
  `description` text COLLATE utf8mb4_general_ci,
  `project_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `project_status` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `deadline` date DEFAULT NULL,
  `category` enum('mascot','costume') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'mascot'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `gallery`
--

INSERT INTO `gallery` (`id`, `project_image`, `material_image`, `description`, `project_name`, `project_status`, `quantity`, `deadline`, `category`) VALUES
(21, 'zahur.jpeg', 'Screenshot_2025-04-14_160840.png', '- 1 Set mascot tung tung tung tung tung tung sahur \r\n- 1 Pcs excalibur pentungan\r\n- 1 Set mascot capu capu capucinno assasinooo\r\n- 1 Pcs katana', 'Tung Tung Tung Tung Sahur and Friends', 'Completed', 1, '2025-04-22', 'mascot'),
(22, 'duu.png', 'DALL__E_2025-04-14_15.54.23_-_A_surreal_humanoid_character_named__TI_TI_IT___composed_of_a_bizarre_fusion_of_IT_hardware__a_chaotic_amalgamation_of_a_keyboard_torso__monitor_chest_.webp', '- Testing 1\r\n- Testing 2\r\n- Testing 3', 'Hik Vision CCTV Mascot', 'Upcoming', 1, '2025-04-30', 'mascot'),
(23, 'ChatGPT_Image_Apr_9__2025__08_59_01_AM.png', 'DALL__E_2025-04-14_15.54.23_-_A_surreal_humanoid_character_named__TI_TI_IT___composed_of_a_bizarre_fusion_of_IT_hardware__a_chaotic_amalgamation_of_a_keyboard_torso__monitor_chest_.webp', 'wewgwrgwrttt', 'Hik Vision CCTV Mascot', 'Completed', 1, '2025-04-24', 'mascot'),
(24, 'Cuplikan_layar_2024-08-06_153553.png', 'Cuplikan_layar_2024-08-06_153806.png', 'Test', 'Test1', 'Archived', 1, '2025-04-22', 'mascot'),
(28, '6808b0ee0d936_Cuplikan layar 2025-03-21 102126.png', '6808b0ee0e182_6929f8f379bb7d63ce0283f6647f9a6e.png', 'test2', 'Test222', 'Revision', 12, '2025-04-22', 'costume'),
(29, '68060ca9efb40_Cage Vampire.png', '68060ca9eff07_Shoes.png', 'test344', 'test355', 'Completed', 1, '2025-04-23', 'costume'),
(33, 'EgBP-17120255111.png', 'Cuplikan layar 2025-03-21 112823.png', 'ascswdc', 'test3', 'Revision', 1, '2025-04-26', 'costume'),
(34, '680862f90058a_491456248_17852044233430394_6083765508520289848_n.jpg', '680862f900b5e_Gambar WhatsApp 2025-04-15 pukul 08.56.24_329fc156.jpg', 'swdcsd', 'terstttt', 'In Progress', 1, '2025-04-24', 'mascot'),
(36, '680884264a0af_491455826_17852044257430394_8797943226980328465_n.jpg', '680884264a64b_491389980_17852044272430394_5208796392557484244_n.jpg', 'sdvsd', 'sds', 'In Progress', 1, '2025-04-26', 'mascot'),
(44, '68089b7a0bc4c_Cuplikan layar 2025-03-21 111538.png', '68089b7a0bf11_Cuplikan layar 2025-03-21 111305.png', 'wew', 'assuiiii', 'Urgent', 1, '2025-04-16', 'mascot'),
(45, '68089bb009766_Cuplikan layar 2025-03-21 102126.png', '68089bb009f10_Check Mark Button.png', 'sacxwe', 'wahyu', 'In Progress', 1, '2025-04-23', 'mascot'),
(47, '68089f0160e02_Gambar WhatsApp 2024-11-02 pukul 09.28.56_f6fdc7af.jpg', '68089f0161303_Gambar WhatsApp 2025-04-14 pukul 08.56.01_6aac1a4f.jpg', 'asdad', 'meca', 'Archived', 1, '2025-04-03', 'mascot'),
(48, '68089f2b8e85e_Cuplikan layar 2025-04-17 154505.png', '68089f2b8f1a4_490467515_17852044287430394_2391781559457612175_n.jpg', 'asasdddd', 'test upload', 'In Progress', 1, '2025-04-29', 'mascot'),
(49, '68089fe5efd38_Cuplikan layar 2025-04-17 130913.png', '68089fe5f01ba_pRZw-1726028086.jpeg', 'rrrrr', 'ppppp333', 'Revision', 7, '2025-04-03', 'mascot'),
(50, '6808a006426be_Red_Arrow_PNG_Clip_Art_Transparent_Image.png', '6808a00642a4a_Cuplikan layar 2025-04-17 154407.png', 'erfverfv', 'yyyyyy', 'Revision', 34, '2025-04-05', 'mascot'),
(51, '6808b1140aa18_admin f.png', '6808b1140aef4_arrow-png-12457.png', 'tss', 'ppppp333', 'Completed', 5, '2025-04-16', 'costume');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mascot','costume') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'mascot', '$2y$10$J0tu6d6YIK9cv3PyGZ/6Fu92qjBWITbdBI4XnwqzH8tgd3WSxaxny', 'mascot'),
(2, 'costume', '$2y$10$g8CgSdz7IspmS4PzCflape7PqmwJALX268FeOh9tdGryNkPdkpNmq', 'costume');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
