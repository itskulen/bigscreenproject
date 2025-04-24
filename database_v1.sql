-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 22 Apr 2025 pada 04.09
-- Versi server: 8.0.39
-- Versi PHP: 8.2.24

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
  `project_image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `material_image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `project_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `project_status` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `deadline` date DEFAULT NULL,
  `category` enum('mascot','costume') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'mascot'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `gallery`
--

INSERT INTO `gallery` (`id`, `project_image`, `material_image`, `description`, `project_name`, `project_status`, `deadline`, `category`) VALUES
(21, 'zahur.jpeg', 'Screenshot_2025-04-14_160840.png', '- 1 Set mascot tung tung tung tung tung tung sahur \r\n- 1 Pcs excalibur pentungan\r\n- 1 Set mascot capu capu capucinno assasinooo\r\n- 1 Pcs katana', 'Tung Tung Tung Tung Sahur and Friends', 'Completed', '2025-04-22', 'mascot'),
(22, 'duu.png', 'DALL__E_2025-04-14_15.54.23_-_A_surreal_humanoid_character_named__TI_TI_IT___composed_of_a_bizarre_fusion_of_IT_hardware__a_chaotic_amalgamation_of_a_keyboard_torso__monitor_chest_.webp', '- Testing 1\r\n- Testing 2\r\n- Testing 3', 'Hik Vision CCTV Mascot', 'In Progress', '2025-04-30', 'mascot'),
(23, 'ChatGPT_Image_Apr_9__2025__08_59_01_AM.png', 'DALL__E_2025-04-14_15.54.23_-_A_surreal_humanoid_character_named__TI_TI_IT___composed_of_a_bizarre_fusion_of_IT_hardware__a_chaotic_amalgamation_of_a_keyboard_torso__monitor_chest_.webp', 'wewgwrgwr', 'Hik Vision CCTV Mascot', 'Completed', '2025-04-24', 'mascot'),
(24, 'Cuplikan_layar_2024-08-06_153553.png', 'Cuplikan_layar_2024-08-06_153806.png', 'Test', 'Test1', 'Not Started', '2025-04-22', 'mascot'),
(25, 'Cuplikan_layar_2024-07-31_162013.png', 'Cuplikan_layar_2024-07-31_114424.png', 'test2', 'Test2', 'Not Started', '2025-04-22', 'mascot'),
(27, 'Cuplikan_layar_2024-07-31_162013.png', 'Cuplikan_layar_2024-07-31_114424.png', 'test2', 'Test2', 'Not Started', '2025-04-22', 'mascot'),
(28, 'Cuplikan layar 2024-07-31 162634.png', 'Cuplikan layar 2024-07-31 162944.png', 'test2', 'Test2', 'In Progress', '2025-04-22', 'costume'),
(29, '68060ca9efb40_Cage Vampire.png', '68060ca9eff07_Shoes.png', 'test344', 'test355', 'Completed', '2025-04-23', 'costume'),
(31, 'Opening_Dancers_Male_1.png', 'Cuplikan_layar_2024-07-31_162944.png', 'lagi bikin kepala', 'Upin inpin', 'Not Started', '2025-04-24', 'mascot'),
(32, '491465158_17852044245430394_2010514194785514257_n.jpg', 'Cuplikan_layar_2025-03-21_102654.png', 'wefwe', 'Test2wew', 'Not Started', '2025-04-11', 'mascot'),
(33, 'EgBP-17120255111.png', 'Cuplikan layar 2025-03-21 112823.png', 'ascswdc', 'test3', 'Revision', '2025-04-26', 'costume');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
