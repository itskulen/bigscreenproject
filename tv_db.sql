-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 26 Bulan Mei 2025 pada 02.16
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
  `priority` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Normal',
  `quantity` int NOT NULL DEFAULT '1',
  `deadline` date DEFAULT NULL,
  `category` enum('mascot','costume') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'mascot',
  `createAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updateAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `subform_embed` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `gallery`
--

INSERT INTO `gallery` (`id`, `project_image`, `material_image`, `description`, `project_name`, `project_status`, `priority`, `quantity`, `deadline`, `category`, `createAt`, `updateAt`, `subform_embed`) VALUES
(21, 'zahur.jpeg', 'Screenshot_2025-04-14_160840.png', '- 1 Set mascot tung tung tung tung tung tung sahur \r\n- 1 Pcs excalibur pentungan\r\n- 1 Set mascot capu capu capucinno assasinooo\r\n- 1 Pcs katana', 'Tung Tung Tung Tung Sahur and Friends', 'Completed', 'Normal', 1, '2025-04-22', 'mascot', '2025-04-30 03:17:19', '2025-04-30 03:17:19', NULL),
(24, 'Cuplikan_layar_2024-08-06_153553.png', 'Cuplikan_layar_2024-08-06_153806.png', 'Test', 'Test1', 'Archived', 'Normal', 1, '2025-04-22', 'mascot', '2025-04-30 03:17:19', '2025-04-30 03:17:19', NULL),
(28, '6808b0ee0d936_Cuplikan layar 2025-03-21 102126.png', '6808b0ee0e182_6929f8f379bb7d63ce0283f6647f9a6e.png', 'test2', 'Test222', 'Revision', 'Normal', 12, '2025-04-22', 'costume', '2025-04-30 03:17:19', '2025-04-30 03:17:19', NULL),
(29, '68060ca9efb40_Cage Vampire.png', '68060ca9eff07_Shoes.png', 'test344', 'test355', 'Completed', 'Normal', 1, '2025-04-23', 'costume', '2025-04-30 03:17:19', '2025-04-30 03:17:19', NULL),
(33, 'EgBP-17120255111.png', 'Cuplikan layar 2025-03-21 112823.png', 'ascswdc', 'test3', 'Revision', 'Normal', 1, '2025-04-26', 'costume', '2025-04-30 03:17:19', '2025-04-30 03:17:19', NULL),
(34, '680862f90058a_491456248_17852044233430394_6083765508520289848_n.jpg', '680862f900b5e_Gambar WhatsApp 2025-04-15 pukul 08.56.24_329fc156.jpg', 'swdcsdsdcvsd', 'tersttttdfdf', 'In Progress', 'High', 1, '2025-04-24', 'mascot', '2025-04-30 03:17:19', '2025-04-30 03:17:19', NULL),
(36, '680884264a0af_491455826_17852044257430394_8797943226980328465_n.jpg', '680884264a64b_491389980_17852044272430394_5208796392557484244_n.jpg', 'sdvsdas', 'sdssdcvsd', 'In Progress', 'Low', 1, '2025-04-26', 'mascot', '2025-04-30 03:17:19', '2025-04-30 03:17:19', NULL),
(45, '68089bb009766_Cuplikan layar 2025-03-21 102126.png', '68089bb009f10_Check Mark Button.png', 'sacxwe', 'wahyu', 'In Progress', 'Normal', 1, '2025-04-23', 'mascot', '2025-04-30 03:17:19', '2025-04-30 03:17:19', NULL),
(48, '68089f2b8e85e_Cuplikan layar 2025-04-17 154505.png', '68089f2b8f1a4_490467515_17852044287430394_2391781559457612175_n.jpg', 'asasdddd', 'test upload', 'In Progress', 'Medium', 1, '2025-04-29', 'mascot', '2025-04-30 03:17:19', '2025-04-30 03:17:19', NULL),
(50, '6808a006426be_Red_Arrow_PNG_Clip_Art_Transparent_Image.png', '6808a00642a4a_Cuplikan layar 2025-04-17 154407.png', 'erfverfv', 'yyyyyy', 'Revision', 'Normal', 34, '2025-04-05', 'mascot', '2025-04-30 03:17:19', '2025-04-30 03:17:19', NULL),
(51, '6808b1140aa18_admin f.png', '6808b1140aef4_arrow-png-12457.png', 'tss', 'ppppp333', 'Completed', 'Normal', 5, '2025-04-16', 'costume', '2025-04-30 03:17:19', '2025-04-30 03:17:19', NULL),
(54, '68108e41bb1bd_491465158_17852044245430394_2010514194785514257_n.jpg', '68108e41bb393_Pushpin.png', 'erferf', 'xcv bfb f', 'In Progress', 'Low', 56, '2025-05-10', 'mascot', '2025-04-30 03:17:19', '2025-04-30 03:17:19', NULL),
(58, '6811933b69981_Barnes Statue Male.jpg', '6811933b69d3c_Batman Head Gear 2.png', 'gthnyt', 'gytasxdas', 'Urgent', 'Medium', 342, '2025-04-15', 'mascot', '2025-04-30 03:17:19', '2025-04-30 03:17:19', NULL),
(59, '681193efadd9c_RWS Cloud Shoes.png', '681193efae112_NDP Accessory Owl.png', '56', 'ascasc', 'Revision', 'Low', 544, '2025-04-17', 'mascot', '2025-04-30 03:17:19', '2025-04-30 03:17:19', NULL),
(60, '68119511ed27f_Cute Bunny.png', '68119511ed88d_Barnes Statue Male.jpg', 'tybhty', 'asxcasc', 'Urgent', 'Medium', 4, '2025-04-26', 'mascot', '2025-04-30 03:17:19', '2025-04-30 03:17:19', NULL),
(66, '6811dfda3eec1_Snow White Pose 1.png', NULL, 'wefwe', 'tttttttttttttt', 'Urgent', 'Medium', 3, '2025-04-17', 'mascot', '2025-04-30 08:31:22', '2025-04-30 08:31:22', 'https://docs.google.com/presentation/d/1J6Mr2-yuWsjQXrHHlwBTfpIi8mlK43xI4duU18VmZEA/edit'),
(67, '6811e00f5da3a_Barnes Statue Male.jpg', NULL, 'dfvwe', 'yyyyyyyyyyyy', 'Urgent', 'Medium', 6, '2025-05-02', 'mascot', '2025-04-30 08:32:15', '2025-04-30 08:32:15', NULL),
(68, '68142914567ae_Gardenia 2.png', '6814291456a06_RWS Cloud Shoes.png', ' sdvsdvsd', 'test3456', 'Urgent', 'High', 3, '2025-05-15', 'mascot', '2025-05-02 02:08:20', '2025-05-02 02:14:41', 'https://docs.google.com/presentation/d/1_6e8x3IFpjVf_bi2xA3omY3FklHG8et9pWNtI8261et/edit'),
(71, '6814638473082_ccm.png', '681463847325c_me.png', 'asc as', 'kkkkl', 'In Progress', 'Low', 2, '2025-05-10', 'mascot', '2025-05-02 06:17:13', '2025-05-02 06:17:40', ''),
(77, '6814972657150_Tiger Broker.jpg', '68149726573e9_Batman Head Gear.png', 'ascasc', 'testrrrr', 'In Progress', 'Medium', 1, '2025-05-10', 'mascot', '2025-05-02 09:57:58', '2025-05-02 10:01:11', ''),
(78, '68149a685e184_Barnes Statue Male.jpg', '68149a685e35c_Batman Armband.png', 'vfwesd d', 'ytsdytscdhusdcv sd', 'In Progress', 'Low', 4, '2025-05-10', 'mascot', '2025-05-02 10:11:52', '2025-05-02 10:12:14', ''),
(81, '681ea8617ae42_Cuplikan layar 2025-03-21 111121.png', '681ea8617b0fd_Picture1.jpg', '', 'testtttt2233', 'Urgent', 'Medium', 23, '2025-05-16', 'mascot', '2025-05-10 01:14:09', '2025-05-10 01:14:09', 'https://docs.google.com/presentation/d/1j4GMZ9H89erNX6PZ709DZ270URRgXB1VHKwuGBc713g/edit'),
(82, '681eaa10da6bf_EgBP-17120255111.png', '681eaa10da98f_Cuplikan layar 2025-03-21 101103.png', 'edef', 'sdcsdc', 'Urgent', 'Medium', 2, '2025-05-17', 'mascot', '2025-05-10 01:21:20', '2025-05-10 01:21:20', 'https://docs.google.com/presentation/d/1j4GMZ9H89erNX6PZ709DZ270URRgXB1VHKwuGBc713g/edit');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
