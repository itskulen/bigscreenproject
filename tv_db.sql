-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 14 Agu 2025 pada 10.02
-- Versi server: 11.4.8-MariaDB
-- Versi PHP: 8.4.10

-- ALTER TABLE gallery ADD COLUMN type VARCHAR(32) DEFAULT NULL;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mascoten_db_tv`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `project_image` varchar(255) DEFAULT NULL,
  `material_image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `project_name` varchar(255) NOT NULL,
  `project_status` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `deadline` date DEFAULT NULL,
  `category` enum('mascot','costume') NOT NULL DEFAULT 'mascot',
  `priority` varchar(20) NOT NULL DEFAULT 'Normal',
  `createAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updateAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `subform_embed` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `gallery`
--

INSERT INTO `gallery` (`id`, `project_image`, `material_image`, `description`, `project_name`, `project_status`, `quantity`, `deadline`, `category`, `priority`, `createAt`, `updateAt`, `subform_embed`) VALUES
(1, '[\"WhatsApp_Image_2025-04-22_at_14.56.34.jpeg\"]', '[\"Screenshot_2025-04-22_161651.png\"]', 'finish', 'Legoland jack&Emily', 'Archived', 1, '2025-04-22', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:33:20', NULL),
(2, '[\"mechamato.png\"]', '[\"680c34a6b81e8_mechamato notes 2.png\"]', '1 set done', 'Mechamato', 'Archived', 1, '2025-05-10', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:33:17', 'https://docs.google.com/presentation/d/15nzVtjZaWXT3axC9NSccO5Cy79hTV56OkqLDBSIc6uQ/edit'),
(3, '[\"6870afe1d6b9c_WhatsApp Image 2025-07-10 at 10.13.48.jpeg\"]', '[\"680c8190cdc7a_kyle notes.png\"]', 'need to continue adjust based on client comment', 'Australia dreamworld-Kyle', 'In Progress', 1, '2025-08-06', 'mascot', 'Urgent', '2025-04-30 03:50:17', '2025-08-09 03:02:45', 'https://docs.google.com/presentation/d/1V7kcm30ACaV4mt7c6_vYKYdoai_1dXZnM6BUTYiJk48/edit?slide=id.g370890a9124_4_27#slide=id.g370890a9124_4_27'),
(4, '[\"6870afd65fad7_WhatsApp Image 2025-07-10 at 10.13.48.jpeg\"]', '[\"680c8197c19fa_walter notes.png\"]', 'need to continue adjust based on client comment', 'Australia dreamworld-walter', 'In Progress', 1, '2025-08-06', 'mascot', 'Urgent', '2025-04-30 03:50:17', '2025-08-09 03:02:53', 'https://docs.google.com/presentation/d/1V7kcm30ACaV4mt7c6_vYKYdoai_1dXZnM6BUTYiJk48/edit?slide=id.g370890a9124_4_27#slide=id.g370890a9124_4_27'),
(5, '[\"6870afc67598f_WhatsApp Image 2025-07-10 at 10.10.33.jpeg\"]', '[\"680c8189ce3dc_bella notes.png\"]', 'need to continue adjust based on client comment', 'Australia dreamworld-bella', 'In Progress', 1, '2025-08-06', 'mascot', 'Urgent', '2025-04-30 03:50:17', '2025-08-09 03:02:59', 'https://docs.google.com/presentation/d/1V7kcm30ACaV4mt7c6_vYKYdoai_1dXZnM6BUTYiJk48/edit?slide=id.g370890a9124_4_27#slide=id.g370890a9124_4_27'),
(6, '[\"spf_.png\"]', '[\"680c814beee90_spf notes.png\"]', 'waiting the luggage to send out\r\nafter change the logo and take final photo, need to check the viewing', 'SPF lion inflatable ', 'Archived', 1, '2025-05-27', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:33:13', 'https://docs.google.com/presentation/d/1-YPsjjL2IuvnAero_WfKKqdRWBdfqQZPypnyHMR5JDI/edit'),
(7, '[\"bao_zai.png\"]', '[\"bao_zai_swatches.png\"]', 'finish\r\n', 'Din Tai Fung (Bao Zai) ', 'Archived', 1, '2025-04-28', 'mascot', 'High', '2025-04-30 03:50:17', '2025-08-09 02:33:12', 'https://docs.google.com/presentation/d/11lvWoMvvCcxYoZJaCPdbdWGHgczyrNdjmh7pQ_yNlr0/edit'),
(8, '[\"manny_ginseng.png\"]', '[\"manny.png\"]', 'finish', 'Manny Ginseng inflatable', 'Archived', 1, '2025-04-25', 'mascot', 'High', '2025-04-30 03:50:17', '2025-08-09 02:33:11', 'https://docs.google.com/presentation/d/1SuZxOq5N1ZiAYaVigmh8uBPLNUvsn4qRQDNGrTGJ_Pk/edit'),
(9, '[\"dawn_sentinel.png\"]', '[\"682a11cb2dbab_Screenshot 2025-05-18 235745.png\"]', 'adjust based on zai comment', 'Merlion Mascot-Dawn Sentinel', 'Completed', 1, '2025-07-09', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-11 01:06:49', 'https://docs.google.com/presentation/d/1J6Mr2-yuWsjQXrHHlwBTfpIi8mlK43xI4duU18VmZEA/edit'),
(10, '[\"WACS_-_Ollie.png\"]', '[\"680c3a31ae685_ollie notes.png\"]', '* Lengthen the legs \r\n* adjust based on client comment', 'WACS-Ollie repair', 'Archived', 1, '2025-07-04', 'mascot', 'High', '2025-04-30 03:50:17', '2025-08-04 09:46:36', 'https://docs.google.com/presentation/d/1mf9h8iUIji65tPUpctsGvy2eRe9vFxvHTaQD7jfbk68/edit'),
(35, '[\"dewey.png\"]', '[\"680c3a3c1b00a_dwey notes.png\"]', 'change the structure to be same as 2nd set ( meaning, change to open bottom and outfit make similar to set 2)\r\nwaiting the sublimation', 'WACS-Dewey repair', 'Archived', 1, '2025-07-04', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:33:01', 'https://docs.google.com/presentation/d/1mf9h8iUIji65tPUpctsGvy2eRe9vFxvHTaQD7jfbk68/edit'),
(36, '[\"kakee.png\"]', '[\"680c39c6cce1f_kakee notes.png\"]', 'Done', 'Kakee compresed foam ', 'Archived', 1, '2025-04-26', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:33:00', ''),
(37, '[\"bitbit_depavali.png\"]', '', 'finish, waiting to send out', 'Bitbit Overlay-Deepavali', 'Archived', 1, '2025-06-22', 'mascot', 'High', '2025-04-30 03:50:17', '2025-08-09 02:32:58', ''),
(39, '[\"xmas.png\"]', '', 'finish, waiting confirmation to add the hat detail', 'Bitbit overlay-XMas', 'Archived', 1, '2025-06-27', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:32:57', ''),
(40, '[\"new_year_hat.png\"]', '', 'finish, waiting to send out', 'Bitbit overlay-New year hat', 'Archived', 1, '2025-06-27', 'mascot', 'High', '2025-04-30 03:50:17', '2025-08-09 02:32:56', ''),
(41, '[\"jero_cat.png\"]', '[\"680c812617c0d_jero notes.png\"]', 'finish ', 'Ejen ali-Jero Cat', 'Archived', 1, '2025-05-10', 'mascot', 'High', '2025-04-30 03:50:17', '2025-08-09 02:32:54', ''),
(42, '[\"stitch_statue.png\"]', '[\"680c39f2e6651_stitch notes.png\"]', 'finish', 'Stitch Statue 1.5meter - Walt Disney Indonesia ', 'Archived', 1, '2025-05-10', 'mascot', 'High', '2025-04-30 03:50:17', '2025-08-09 02:32:52', ''),
(44, '[\"girl_boyhalfmask.png\"]', '[\"girl_boy_mask_swatches.png\"]', 'process wrapping all mask \r\nfinish ', 'Masks (Girl/Boy)', 'Archived', 25, '2025-05-23', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:32:51', 'https://docs.google.com/presentation/d/1HMFvbskHObH_2GcWWlProPdmoszumEvUWqdXFOJFwLM/edit'),
(45, '[\"WhatsApp_Image_2025-04-09_at_13.04.39.jpeg\"]', '', 'waiting confirmation to continue \r\n15 set is done ', 'Provaliant : (Costume dept make & mascot dept support) Oronamin C Inflatable Mascot : 20 Set  Ten Ten Penguin Inflatable Mascot : 10 Set', 'In Progress', 15, '2025-07-19', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:32:49', ''),
(46, '[\"680c2f2848bee_arti.png\"]', '[\"680c2f28491cf_notes arti.png\"]', 'adjust based on comparison', 'playmondo-Arti', 'Archived', 1, '2025-06-06', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:32:48', 'https://docs.google.com/presentation/d/1_6e8x3IFpjVf_bi2xA3omY3FklHG8et9pWNtI8261es/edit'),
(47, '[\"680c2f7d2a8bc_mira.png\"]', '[\"680c2f7d2ae34_notes mira.png\"]', 'finishing', 'Playmondo-Mira', 'Archived', 1, '2025-06-06', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:32:47', 'https://docs.google.com/presentation/d/1_6e8x3IFpjVf_bi2xA3omY3FklHG8et9pWNtI8261es/edit'),
(48, '[\"680c2faa342e1_penn.png\"]', '[\"680c2faa34929_penn notes.png\"]', 'finsih', 'Playmondo-Penn', 'Archived', 1, '2025-06-06', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:32:45', 'https://docs.google.com/presentation/d/1_6e8x3IFpjVf_bi2xA3omY3FklHG8et9pWNtI8261es/edit'),
(49, '[\"680c30394cf40_sharity.png\"]', NULL, 'process wrapping and make outfit', 'Sharity inflatable repair', 'Archived', 1, '2025-06-06', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:32:44', ''),
(50, '[\"680c30659696c_sharity.png\"]', NULL, 'Done', 'Sharity Compressed foam ', 'Archived', 1, '2025-05-03', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:32:42', ''),
(52, '[\"680c316f2c02d_philip capital.png\"]', '[\"680c316f2c7a8_philip capital notes.png\"]', 'adjust based on impose and take photo today', 'Philip capital otter', 'Archived', 1, '2025-05-23', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:32:40', 'https://docs.google.com/presentation/d/1gDnFt7trZ4NHgCK2Ty8asJmShgPTq-Z_MH9WJQULXZY/edit#slide=id.g34ec4a067f7_0_6'),
(53, '[\"680c31fe30a9e_pasir ris jacket.png\"]', '[\"680c31fe3102d_pasir ris jacket notes.png\"]', 'Making jacket', 'Pasir Ris Jacket', 'Archived', 1, '2025-04-30', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:32:39', NULL),
(54, '[\"680c32875a188_garfield.png\"]', '[\"680c32875a6dc_garfield notes.png\"]', 'adjust the structure, need to add magnet so the head cant come out and adjust manual book, need to take photo today and send out LCL ', 'Garfield ', 'Completed', 2, '2025-08-04', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:32:38', ''),
(55, '[\"680c3354b0da2_sadia kiki .png\"]', NULL, 'finish, waiting send out', 'Sadia Kiki repair', 'Archived', 1, '2025-05-06', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:32:37', ''),
(56, '[\"680c33a85c0c7_oth .png\"]', '[\"680c33a85c81a_oth notes.png\"]', 'completed', 'OTH Kids Club-Tommy, Timmy, Tammy', 'Archived', 3, '2025-05-23', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:32:36', 'https://docs.google.com/presentation/d/1j4GMZ9H89erNX6PZ709DZ270URRgXB1VHKwuGBc713g/edit'),
(57, '[\"680c365908eff_tiger .png\"]', '[\"680c3659097af_tiger notes.png\"]', 'Finish', 'Iamzenith-Tiger', 'Archived', 1, '2025-05-09', 'mascot', 'High', '2025-04-30 03:50:17', '2025-08-09 02:32:34', 'https://docs.google.com/presentation/d/1Xe3Luc0KdicgGkih6f5otW20oDRsZovKr_3TMg-SU7M/edit#slide=id.g32224fc8397_0_0'),
(58, '[\"680c367724c92_rabbit.png\"]', '[\"680c3677250a1_rabbit notes.png\"]', 'fully handstich and finishing\r\n', 'Iamzenith-Rabbit', 'Archived', 1, '2025-05-14', 'mascot', 'High', '2025-04-30 03:50:17', '2025-08-09 02:32:32', 'https://docs.google.com/presentation/d/1Xe3Luc0KdicgGkih6f5otW20oDRsZovKr_3TMg-SU7M/edit#slide=id.g32224fc8397_0_0'),
(59, '[\"680c3ae41c14b_great eastern .png\"]', '[\"685cbeee38c73_WhatsApp Image 2025-06-21 at 12.12.45.jpeg\"]', '- Adjust 1st set based on comments  need 19 june 2025\r\n- 7 sets start production(with latest adjustments)\r\n- paw light structure need to start print', 'Great Eastern lion + Paw lighted', 'Archived', 8, '2025-07-14', 'mascot', 'High', '2025-04-30 03:50:17', '2025-08-09 02:32:29', 'https://docs.google.com/presentation/d/1xjohT8kxKAxaTOQ3E3vECoE3swoz7Y3i93Z9pWnrjsE/edit#slide=id.g34aef374858_0_52'),
(60, '[\"680c3ca121541_WhatsApp Image 2025-03-22 at 12.46.02 (1).jpeg\"]', '[\"6833c259e3bb4_WhatsApp Image 2025-05-26 at 08.22.22.jpeg\"]', 'Waiting client confirmation\r\n* adjust the ventilation, please indicate the ventilation and do comparison\r\n* remove the velkro\r\n* adjust 1 helmet first (red) too matte need to take photo fullset with the costume', 'Power ranger-Red, blue, black, yellow, pink', 'In Progress', 5, '2025-08-04', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:32:27', ''),
(61, '[\"680c3d0366695_WhatsApp Image 2025-02-01 at 11.26.23.jpeg\"]', NULL, 'waiting silicon and find the right color, when can take photo after adjust?', 'Kleig', 'In Progress', 1, '2025-06-27', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:32:26', ''),
(62, '[\"682a15590c17e_Charlie and Snoopy.jpg\"]', '[\"682a15590c638_Charlie and Snoopy (1).jpg\"]', 'process adjust the structure based on impose\r\n* talent height is 150-160cm\r\n* 1 head + 2 body + 2 collars +2 shoes', 'Snoopy ', 'In Progress', 1, '2025-08-05', 'mascot', 'Urgent', '2025-04-30 03:50:17', '2025-08-09 03:01:41', 'https://docs.google.com/presentation/d/1koyWkQcHsqRS2lfXVkrNacXCWcZWPFYrD2KEICpk1JM/edit?slide=id.g313fd84a260_0_0#slide=id.g313fd84a260_0_0'),
(63, '[\"681e0914d1264_big brother beckybunny.png\"]', '[\"681e0914d191e_bigbro notes.png\"]', 'adjust based on impose and take photo on wednesday', 'Becky bunny-Big brother repair', 'In Progress', 1, '2025-08-07', 'mascot', 'Urgent', '2025-04-30 03:50:17', '2025-08-09 03:01:44', 'https://docs.google.com/presentation/d/11YLhlAKPVKmI3srO5Fb9W-0ol1c3zktXVs1oslNi7rI/edit#slide=id.g354cfb6f16a_0_40'),
(65, '[\"681e077e69ab6_kiwi .png\"]', '[\"680c7bc0483da_kiwi notes.png\"]', '* can start 4 set the other Compressed foam (3 gold, 1 green)\r\n* 8 set compressed foam done\r\n* 2 set inflatable is done (23 june 2025)\r\n* for standee', 'Zespri Kiwi', 'Archived', 14, '2025-07-14', 'mascot', 'Normal', '2025-04-30 03:50:17', '2025-08-09 02:32:12', 'https://docs.google.com/presentation/d/1Diafasg7vzjzLuIn1O5zya8utwCyUNcb/edit?slide=id.p8#slide=id.p8'),
(66, '[\"68229e23941ee_sana .png\"]', '[\"68521a8e772de_sana.png\"]', 'waiting client confirmation', 'Sana Preschool ', 'In Progress', 1, '2025-08-08', 'mascot', 'High', '2025-04-30 03:50:17', '2025-08-09 02:32:11', 'https://docs.google.com/presentation/d/1Oe4PDuVPavTJHKaDeo4ZJvZWAxE5eCTaVitCMSnHTE8/edit#slide=id.g34fc9cce4be_0_0'),
(67, '[\"680c7ef75f14e_nila repar.png\"]', NULL, 'adjust the pants and make the head can be erected without the metal stick (client planning the event)', 'Nila Inflatable repair', 'In Progress', 1, '2025-08-08', 'mascot', 'Urgent', '2025-04-30 03:50:17', '2025-08-09 03:01:37', ''),
(68, '[\"686413ea2b7ce_WhatsApp Image 2025-07-01 at 23.59.03.jpeg\"]', '[\"6886d2e78e586_WhatsApp Image 2025-07-24 at 10.22.05.jpeg\"]', 'sanding the small puffy and make the podium and clear after', 'Poofy statue', 'Archived', 1, '2025-07-05', 'mascot', 'High', '2025-04-30 03:50:17', '2025-08-09 02:32:09', ''),
(69, '[\"680c8025b4c3b_WhatsApp Image 2025-04-21 at 16.52.00.jpeg\"]', '[\"681e06fe4e24d_comic can notes.png\"]', 'waiting impose and confirmation after adjust', 'Comic Con Mascot inflatable', 'Archived', 1, '2025-06-16', 'mascot', 'Urgent', '2025-04-30 03:50:17', '2025-08-09 02:32:07', 'https://docs.google.com/presentation/d/1r2qmhGIgFnwaAzdsfqISsHpAy9u1UeSV-hSgelAT-mY/edit'),
(70, '[\"684833e22ea0d_hataw.png\"]', '[\"681e06b0b7661_hataw notes.png\"]', 'adjust based on impose', 'FIVB-Hataw', 'Archived', 1, '2025-06-23', 'mascot', 'High', '2025-04-30 03:50:17', '2025-08-09 02:32:05', 'https://docs.google.com/presentation/d/1iYr8UpColzBZfRs41yRRPH33am5MJXvXEPvX6MkjmqU/edit#slide=id.g3564d82bfc8_0_12'),
(71, '[\"680efe3d4b957_kid lat.png\"]', '[\"681e067279531_kidlat notes.png\"]', 'neeed to make neckpiece, need to  discuss with ms noor ', 'FIVB-Kid Lat', 'Archived', 1, '2025-07-01', 'mascot', 'High', '2025-04-30 03:50:17', '2025-08-09 02:32:02', 'https://docs.google.com/presentation/d/1iYr8UpColzBZfRs41yRRPH33am5MJXvXEPvX6MkjmqU/edit'),
(72, '[\"680efe53e2e92_koolog.png\"]', '[\"681e061ac8c8e_koolog notes.png\"]', 'neeed to make neckpiece, need to  discuss with ms noor ', 'FIVB-Koolog', 'Archived', 1, '2025-07-01', 'mascot', 'High', '2025-04-30 03:50:17', '2025-08-09 02:32:00', 'https://docs.google.com/presentation/d/1iYr8UpColzBZfRs41yRRPH33am5MJXvXEPvX6MkjmqU/edit'),
(83, '[\"6819df9402318_WhatsApp Image 2025-05-03 at 09.57.03.jpeg\"]', NULL, 'finish', 'CHS body repair', 'Archived', 1, '2025-05-06', 'mascot', 'Low', '2025-05-06 10:08:20', '2025-08-09 02:31:56', NULL),
(84, '[\"68229e50e416b_little.png\"]', '[\"681e094b6319f_lilbro notes.png\"]', '- Lil Bro to adjust based on comments in subform\r\n- process adjust and take photo on wednesday', 'becky bunny-Little brother repair ', 'In Progress', 1, '2025-08-16', 'mascot', 'Urgent', '2025-05-09 13:55:23', '2025-08-09 03:01:52', 'https://docs.google.com/presentation/d/11YLhlAKPVKmI3srO5Fb9W-0ol1c3zktXVs1oslNi7rI/edit#slide=id.g354cfb6f16a_0_48'),
(85, '[\"682a1456b8b61_Legoland Reskin - Surfer Boy and Mexican Man.pptx.jpg\"]', NULL, 'reskin, body and leg is done, waiting the collar', 'LEGOLAND SURFER BOY ', 'Archived', 1, '2025-06-25', 'mascot', 'Low', '2025-05-18 17:09:42', '2025-08-09 02:31:50', 'https://docs.google.com/presentation/d/1xGgEaV_nDqlZljouKgzHcT0etpDMXEua/edit?slide=id.p13#slide=id.p13'),
(86, '[\"682a1497d3ce7_Legoland Reskin - Surfer Boy and Mexican Man.pptx (1).jpg\"]', NULL, '* waiting sublimation for poncho\r\n* process reskin', 'LEGOLAND MEXICAN MAN RESKIN', 'Archived', 1, '2025-06-25', 'mascot', 'Normal', '2025-05-18 17:10:47', '2025-08-09 02:31:48', 'https://docs.google.com/presentation/d/1xGgEaV_nDqlZljouKgzHcT0etpDMXEua/edit?slide=id.g35720257532_0_0#slide=id.g35720257532_0_0'),
(87, '[\"682a15bd82bd9_Charlie and Snoopy (4).jpg\"]', '[\"682a15bd82ec3_Charlie and Snoopy (2).jpg\"]', 'process adjust based on impose\r\n* talent height is 148-155cm\r\n* 1 head + 1 cap + 2 body + 2 gloves + 2 shoes + 2 shorts + 2 shirt \r\n', 'Charlie brown', 'In Progress', 1, '2025-08-05', 'mascot', 'Urgent', '2025-05-18 17:15:41', '2025-08-09 03:01:55', 'https://docs.google.com/presentation/d/1koyWkQcHsqRS2lfXVkrNacXCWcZWPFYrD2KEICpk1JM/edit?slide=id.g3589ee81801_0_8#slide=id.g3589ee81801_0_8'),
(88, '[\"682a16abe8832_Mechamato mascot sub form (1).jpg\"]', '[\"6859f95eb19dd_mechamato notess.png\"]', 'process wraping and take photo by friday', 'Mechamato', 'In Progress', 2, '2025-08-20', 'mascot', 'Normal', '2025-05-18 17:19:39', '2025-08-09 02:31:44', 'https://docs.google.com/presentation/d/15nzVtjZaWXT3axC9NSccO5Cy79hTV56OkqLDBSIc6uQ/edit#slide=id.g358b686e8a4_0_0'),
(89, '[\"682a17596d977_Wahoo Waterworld - Odie Mascot.pptx.jpg\"]', '[\"682a17596df4b_Wahoo Waterworld - Odie Mascot.pptx (1).jpg\"]', 'finish ', 'Wahoo waterworld-Odhi', 'Archived', 1, '2025-07-27', 'mascot', 'Normal', '2025-05-18 17:22:33', '2025-08-09 02:31:43', 'https://docs.google.com/presentation/d/1_maEtwpcn1At_3i5oiJe4ecOxSg0-shC/edit?slide=id.p1#slide=id.p1'),
(91, '[\"6833c13910d97_WhatsApp Image 2025-05-24 at 11.14.46.jpeg\"]', NULL, '* process sanding and airbrush\r\n* today must finish sanding', 'Pakuwon City Mall Installation art : Panda statue 4 sets (1.5 meter 2 sets and 65 cm 2 Sets) Bench 1 ', 'Archived', 1, '2025-06-18', 'mascot', 'Normal', '2025-05-26 01:17:45', '2025-08-09 02:31:42', 'https://docs.google.com/presentation/d/1u28vtKxytii3skMAgALuHdFJBThCSGz_/edit?slide=id.g35fdfbbc194_2_17#slide=id.g35fdfbbc194_2_17'),
(92, '[\"683cfd0334b70_dad.png\"]', '[\"683cfd033544e_dad notes.png\"]', 'adjust struucture based on impose and take photo wednesday', 'Dad becky bunny', 'In Progress', 1, '2025-08-16', 'mascot', 'Urgent', '2025-06-02 01:23:15', '2025-08-09 03:01:59', 'https://docs.google.com/presentation/d/11YLhlAKPVKmI3srO5Fb9W-0ol1c3zktXVs1oslNi7rI/edit?slide=id.g356513681a3_0_6#slide=id.g356513681a3_0_6'),
(93, '[\"683cfd40dd384_mom .png\"]', '[\"683cfd40dd969_mom notes.png\"]', 'ajust the structure and take photo wednesday', 'mom becky bunny', 'In Progress', 1, '2025-08-16', 'mascot', 'Urgent', '2025-06-02 01:24:16', '2025-08-09 03:02:01', 'https://docs.google.com/presentation/d/11YLhlAKPVKmI3srO5Fb9W-0ol1c3zktXVs1oslNi7rI/edit?slide=id.g356513681a3_0_6#slide=id.g356513681a3_0_6'),
(94, '[\"683d0276b7cfe_grandpa.png\"]', '[\"683d0276b81a3_grandpa notes.png\"]', 'adjust the structure and take photo wednesday', 'grandpa becky bunny', 'In Progress', 1, '2025-08-16', 'mascot', 'Urgent', '2025-06-02 01:46:30', '2025-08-09 03:02:02', 'https://docs.google.com/presentation/d/11YLhlAKPVKmI3srO5Fb9W-0ol1c3zktXVs1oslNi7rI/edit?slide=id.g355f308394c_0_12#slide=id.g355f308394c_0_12'),
(95, '[\"683d02c4b8662_grandma .png\"]', '[\"683d02c4b8b82_grandma notes.png\"]', 'adjust the structure based on impose and take photo wednesday', 'Grandma becky bunny', 'In Progress', 1, '2025-08-16', 'mascot', 'Urgent', '2025-06-02 01:47:48', '2025-08-09 03:02:03', 'https://docs.google.com/presentation/d/11YLhlAKPVKmI3srO5Fb9W-0ol1c3zktXVs1oslNi7rI/edit?slide=id.g356513681a3_0_26#slide=id.g356513681a3_0_26'),
(96, '[\"683d03df3420f_pudgy penguin.png\"]', '[\"683d03df348e9_pudgy notes.png\"]', 'process making structure, today must photo both structure', 'pudgy penguin blue', 'Archived', 2, '2025-07-30', 'mascot', 'High', '2025-06-02 01:52:31', '2025-08-09 02:31:33', 'https://docs.google.com/presentation/d/1tsikrPrjXUaiTdWEZljdW3BdsOnq3Zk7Ny_I4uSGaj8/edit?slide=id.g330c2f17f08_0_172#slide=id.g330c2f17f08_0_172'),
(97, '[\"683d04bba3b76_WhatsApp Image 2025-05-27 at 08.59.01.jpeg\"]', '[\"683d04bba3d80_WhatsApp Image 2025-05-27 at 15.42.46 (1).jpeg\"]', 'process making bag with design option 3', 'welcia bag', 'Archived', 1, '2025-06-16', 'mascot', 'Low', '2025-06-02 01:56:11', '2025-08-09 02:31:31', NULL),
(98, '[\"683d094336291_mr merlion.png\"]', '[\"683d0943373f2_mr merlion notes.png\"]', 'process adjust and take photo today and send out LCL', 'Mr Merlion compresssed foam', 'Archived', 1, '2025-07-28', 'mascot', 'Urgent', '2025-06-02 02:15:31', '2025-08-09 02:31:29', 'https://docs.google.com/presentation/d/1vBUYslkxVW7Dn6ZM5NKRUASyQazdppAjKjygxjBuwNY/edit?slide=id.p5#slide=id.p5'),
(99, '[\"684f6e53adb79_xiao pang gege.png\"]', NULL, 'waiting to send out', 'Xiao Pang Ge Ge', 'Archived', 1, '2025-06-24', 'mascot', 'High', '2025-06-16 01:07:31', '2025-08-09 02:31:22', 'https://docs.google.com/presentation/d/1afjhcPfgHnbXz7fk0ZLG2AI4uhuKDE1dskBh8qPHHD4/edit?slide=id.g35ed64af9bf_0_1#slide=id.g35ed64af9bf_0_1'),
(100, '[\"68536945ec862_oedh-1742444540.png\"]', '[\"68536945ee6f3_Cuplikan layar 2025-06-19 083443.png\"]', 'Sample will be sent to Batam tomorrow via Soon Brother.', 'Jurong West Sec Choir Re-order - Female', 'Completed', 12, '2025-03-28', 'costume', 'Low', '2025-06-19 01:35:01', '2025-08-04 09:46:37', 'https://docs.google.com/presentation/d/1CbvnxQqIsYsv8APxqSnlvrvaVyddKTtNUjmf9WNtAyI/edit'),
(101, '[\"68536a6f543f6_5dNH-1742444560.png\"]', '[\"68536a6f54b5c_Cuplikan layar 2025-06-19 083943.png\"]', 'Sample will be sent to Batam tomorrow via Soon Brother.', 'Jurong West Sec Choir Re-order - Male', 'Completed', 1, '2025-03-28', 'costume', 'Low', '2025-06-19 01:39:59', '2025-08-04 09:46:37', 'https://docs.google.com/presentation/d/1CbvnxQqIsYsv8APxqSnlvrvaVyddKTtNUjmf9WNtAyI/edit'),
(102, '[\"68536aad30fbd_xjbp-1742445158.png\"]', '[\"68536aad31cfb_xjbp-1742445158.png\"]', 'All 5 alterations sending back to do.\r\nTake note zippers, next time we need to install further for the waist area, because it\'s mostly spoilt there. ', 'Bedok South Secondary School - Choir Re-order 2025', 'Completed', 1, '2025-03-28', 'costume', 'Low', '2025-06-19 01:41:01', '2025-08-04 09:46:37', 'https://docs.google.com/presentation/d/1En7FRwpb5zUutMaKB2fDHTuHgz4ADMz23KfptCF1Esc/edit'),
(103, '[\"68536b65a7e06_wEog-1742445526.png\"]', '[\"68536b65a80a6_Cuplikan layar 2025-06-19 084354.png\"]', 'Reorder, Measured,\r\nFull Lining.\r\nFabric and Materials: ETA Fri 14 March', 'Chung Cheng High Main Choir', 'In Progress', 11, '2025-03-27', 'costume', 'Low', '2025-06-19 01:44:05', '2025-08-04 09:46:37', 'https://docs.google.com/presentation/d/1SX17YfIMcDkwwQpgLK0TkNbwd8H6twxqv9sOJ1wVGdo/edit'),
(104, NULL, NULL, 'Hairband => bought 12, \r\nbelt => bought 11, \r\ntie => bought 4, \r\nFabric\r\n12F  + 2M \r\nBlack Satin\r\nNeed 12*2.5m = 30m - 8m = 22m ⇒ Bought 25m\r\n\r\nBlack Profit => Male Pants\r\nNeed 2*1.28 = 2.56 - 15m => stock enough\r\n\r\nShuangjia Black Shirt => Male Shirt\r\nNeed 2*1.33 = 2.66 - 7 => Stock Enough', 'Riverside Sec Choir - Female', 'Completed', 12, '2025-03-27', 'costume', 'Low', '2025-06-19 01:48:13', '2025-06-19 01:48:57', ''),
(105, '[\"68536df15c45f_Cuplikan layar 2025-06-19 085325.png\"]', '[\"68536df15cf39_Cuplikan layar 2025-06-19 085408.png\"]', '8/3 pending comment\r\n1/3 cutting process\r\n15/2 test 3d print harness and belt buckle', 'Captain America - Customised', 'Completed', 1, '2025-04-21', 'costume', 'Low', '2025-06-19 01:54:57', '2025-08-04 09:46:37', 'https://docs.google.com/presentation/d/1ogK3z2hee-sVsPSbGeIGmvtHloUvjxClqSlRy9SaDew/edit'),
(106, '[\"6853744bdceec_Cuplikan layar 2025-06-19 092117.png\"]', '[\"6853744be5713_Cuplikan layar 2025-06-19 092150.png\"]', 'Zaid to help to look through also so can help me follow through.\r\nWill pass a Whitley sample, need help to arrange for this to be sent to Batam as reference for draft, Embroidery, Sublimation.', 'Bilstein Race Queen', 'Completed', 2, NULL, 'costume', 'Low', '2025-06-19 02:22:03', '2025-08-04 09:46:37', 'https://docs.google.com/presentation/d/1_NkG4QdkkrdrDAg2rqn-VVwMhqHSgO9iyMBu1kmc3qw/edit'),
(108, '[\"6858a9014f1a8_bear inflatable.png\"]', '[\"6858a9014f929_bear notes.png\"]', 'do finishing and send out', 'Extraordinary People Bear Inflatable with 1 removable outfit', 'Archived', 1, '2025-07-28', 'mascot', 'High', '2025-06-23 01:08:17', '2025-08-09 02:31:19', 'https://docs.google.com/presentation/d/1fpPA50zvq3Cp7Xu_htuNZHsPwGfM5NNdJoKuF5BY3jU/edit?slide=id.g330c2f17f08_0_0#slide=id.g330c2f17f08_0_0'),
(109, '[\"6858a9ddbba05_rarrar lion inflatable.png\"]', '[\"6858a9ddbc188_rarrar lion notes.png\"]', 'Snout need to adjust, wrap up after adjustments. Need to send final photo & video next thursday, send to SG by next friday', 'CTC RarRar Lion Inflatbale with 2 outfits', 'Archived', 1, '2025-08-07', 'mascot', 'Urgent', '2025-06-23 01:11:57', '2025-08-13 04:24:14', 'https://docs.google.com/presentation/d/19HewUNc_fDd-7uBtZ7Jw3v0C1e0z28xo7CgStaG6N_E/edit?slide=id.g3682400642c_0_6#slide=id.g3682400642c_0_6'),
(110, '[\"6858b99510518_Mindchamp .png\"]', '[\"6858b99510af9_mindchamp notes.png\"]', 'process wrapping and take photo today', 'Mindchamp Mascot -Kashita', 'Archived', 1, '2025-07-30', 'mascot', 'High', '2025-06-23 02:19:01', '2025-08-09 02:31:15', 'https://docs.google.com/presentation/d/1YpKt_okhm0MjQ7B3kWU1Lo8Z98fjdMLJQu7Bd-MNiUY/edit?slide=id.g36a13b35c90_0_0#slide=id.g36a13b35c90_0_0'),
(111, '[\"6858cad1c9447_papa.png\"]', '[\"687ee213a0a54_papa notes.png\"]', 'adjust based on impose and take photo on tuesday', 'Papa Zola', 'In Progress', 1, '2025-08-20', 'mascot', 'High', '2025-06-23 03:32:33', '2025-08-09 02:31:13', 'https://docs.google.com/presentation/d/1R62vXVjLT1L8ZNn_Kj4Lr7y6DhZXQS_Q9jHOPnBlYwY/edit?slide=id.g36d6aa517b6_1_19#slide=id.g36d6aa517b6_1_19'),
(112, '[\"6858cb3235c92_pipi .png\"]', '[\"687ee2a6cf609_pipi.png\"]', 'adjust based on impose and take photo on tuesday', 'Pipi ', 'In Progress', 1, '2025-08-20', 'mascot', 'Normal', '2025-06-23 03:34:10', '2025-08-09 02:31:12', 'https://docs.google.com/presentation/d/1_kE82utEjYGdraxWSoasBDdIa37XsAgB/edit?slide=id.p1#slide=id.p1'),
(113, '[\"6859f9e91dd55_neddin bear statue.png\"]', '[\"6859f9e91e21f_neddy bear notes.png\"]', 'finish', 'Robin Wood Neddy Bear Statue 1.7m with round metal base and 5 sets of removable outfits', 'Archived', 1, '2025-07-23', 'mascot', 'Normal', '2025-06-24 01:05:45', '2025-08-09 02:31:11', 'https://docs.google.com/presentation/d/1o-zkhMeM7cz8-WSk12ZYH1exg1czoAwqnR1kQqLQXP8/edit?slide=id.g330c2f17f08_0_0#slide=id.g330c2f17f08_0_0'),
(114, '[\"687df17601953_loti.png\"]', '[\"687df17601f86_loti.png\"]', 'process making structure', 'Loti Bread inflatable', 'Archived', 1, '2025-07-16', 'mascot', 'Normal', '2025-06-24 01:10:19', '2025-08-09 02:31:09', 'https://docs.google.com/presentation/d/1hWv_Q3hfIrTRL0VDcSVKe8qWDUaTcE8I_bEtBn_T6uE/edit?slide=id.g33d048dc24b_0_11#slide=id.g33d048dc24b_0_11'),
(115, '[\"6859fb589bf78_durian.png\"]', '[\"6859fb589c5a3_durian notes.png\"]', 'waiting impose', 'Durian King of Fruits inflatable', 'Archived', 1, '2025-08-05', 'mascot', 'Normal', '2025-06-24 01:11:52', '2025-08-09 02:59:22', 'https://docs.google.com/presentation/d/16IFphkNZoXonHj4vgqIw1tY95lZh_9w1IVdZuf-6830/edit?slide=id.g3623f5f7b33_0_0#slide=id.g3623f5f7b33_0_0'),
(116, '[\"685a73ce5bc48_WhatsApp Image 2025-06-16 at 14.48.06 (1).jpeg\"]', '[\"687ee244767d8_tiggi notes..png\"]', 'adjust based on impose', 'TIGGI Mascot', 'In Progress', 1, '2025-08-11', 'mascot', 'Urgent', '2025-06-24 09:45:50', '2025-08-09 03:01:21', 'https://docs.google.com/presentation/d/1Gaej_OHxas-xCM4utFVJIQJro8ZZJFK8/edit?slide=id.p1#slide=id.p1'),
(117, '[\"686155617c307_WhatsApp Image 2025-06-19 at 13.58.33.jpeg\"]', '[\"68863ec444d5f_Screenshot 2025-07-27 215905.png\"]', 'all the tail must same size and can bendable and detachable\r\n* Along need to adjust based on impose \r\n* Angah and Achik still making structure \r\nall must take photo line up by tuesday', 'Along, Angah, Achik', 'In Progress', 3, '2025-08-20', 'mascot', 'Urgent', '2025-06-29 15:01:53', '2025-08-09 03:01:19', 'https://docs.google.com/presentation/d/1OiU7YN05IUWX8v0VljmifXjA5O5HDWaWGJ9Ofl1lBNQ/edit?slide=id.p5#slide=id.p5'),
(118, '[\"686156a0e39f4_WhatsApp Image 2025-06-25 at 11.17.11.jpeg\"]', NULL, 'need to repair the heatpress', 'NUS Yale body repair', 'Archived', 1, '2025-07-05', 'mascot', 'Normal', '2025-06-29 15:07:12', '2025-08-09 02:31:01', NULL),
(119, '[\"686412b0b5511_Legoland Reskin -Police, Toy Soldier, Pirate Sub Form.pptx.jpg\"]', NULL, 'waiting the mascot\r\nFabric ETA 8 July?\r\nCan do whatever we can first? Yellow gloves and collar', 'legoland policeman', 'Archived', 1, '2025-07-14', 'mascot', 'Urgent', '2025-07-01 16:54:08', '2025-08-09 02:30:59', 'https://docs.google.com/presentation/d/1gxb_5_wLpaWQ1-Bv7ocaWr5GG5Sqkg2k/edit?slide=id.p11#slide=id.p11'),
(120, '[\"6864137ca0ce3_WhatsApp Image 2025-07-01 at 23.57.06.jpeg\"]', '[\"6864137ca104d_WhatsApp Image 2025-07-01 at 23.57.05.jpeg\"]', 'repair based on client comment and customer will come to see', 'Nussa Rara repair', 'Archived', 2, '2025-07-05', 'mascot', 'Urgent', '2025-07-01 16:57:32', '2025-08-09 02:30:57', NULL),
(121, '[\"68687b16d2cbe_WhatsApp Image 2025-07-02 at 09.35.40.jpeg\"]', '[\"687e4d3fbbff5_butter notes.png\"]', 'process wrapping', 'Butter Baby  (compressed foam)', 'Archived', 1, '2025-07-29', 'mascot', 'Urgent', '2025-07-05 01:08:38', '2025-08-09 02:30:55', 'https://docs.google.com/presentation/d/1dd9rjG9h4xbhn9VPsHpoERK2n-oTbCM0/edit?slide=id.p1#slide=id.p1'),
(122, '[\"687ee1537a5c5_peppa pig.png\"]', NULL, 'proces making strutcure and today must take photo', 'Peppa Pig', 'In Progress', 1, '2025-08-04', 'mascot', 'Urgent', '2025-07-22 00:54:43', '2025-08-09 02:30:51', ''),
(123, '[\"688301337175c_iskl fiery.png\"]', '[\"6883013372c00_fiery notes.png\"]', 'process making structure', 'ISKL-Fiery Roary', 'In Progress', 1, '2025-08-29', 'mascot', 'High', '2025-07-25 03:59:47', '2025-08-09 02:30:48', 'https://docs.google.com/presentation/d/1PlFQKy4NBA8FDGe2IH7UcG-qRpkP41zOGdgCw4rxc-Q/edit?slide=id.g3711b2e7f24_0_14#slide=id.g3711b2e7f24_0_14'),
(124, '[\"68830387b53e2_mr merlion animatronic.png\"]', '[\"688f313927727_merlion.png\"]', 'waiting client confirmation before wrap', 'Mr Merlion Animatronic', 'In Progress', 1, '2025-08-13', 'mascot', 'Urgent', '2025-07-25 04:09:43', '2025-08-09 02:30:45', 'https://docs.google.com/presentation/d/15DJZPDOIl9vZuu3q0IKtbtw2Gi6Qx1Dd49BsIEfocoA/edit?slide=id.g36b7a2ebe46_0_3#slide=id.g36b7a2ebe46_0_3'),
(125, '[\"688306748b0e8_fluffy sky.png\"]', '[\"6883063455f49_fluffy notes.png\"]', 'adjust based on impose need to take photo today', 'sky wonderland -Fluffy', 'In Progress', 1, '2025-08-11', 'mascot', 'Urgent', '2025-07-25 04:21:08', '2025-08-09 02:30:43', 'https://docs.google.com/presentation/d/1re6PL_X74dIn390Cev1_8yasXSUp9cFNnS6Qr89WTbY/edit?slide=id.g36bfc9c6d79_0_2#slide=id.g36bfc9c6d79_0_2'),
(126, '[\"6883065c094e8_lily sky.png\"]', '[\"6883065c09f6d_lily notes.png\"]', 'adjust based on impose and take photo today', 'sky wonderland -Lily', 'In Progress', 1, '2025-08-07', 'mascot', 'Urgent', '2025-07-25 04:21:48', '2025-08-09 02:30:40', 'https://docs.google.com/presentation/d/1re6PL_X74dIn390Cev1_8yasXSUp9cFNnS6Qr89WTbY/edit?slide=id.g36d8c6442ab_0_78#slide=id.g36d8c6442ab_0_78'),
(127, '[\"688309efd4070_SCDF-dragon.png\"]', '[\"688309efd47dd_SCDF dragon notes.png\"]', 'Process making head sructure (hold first)', 'SCDF-Dragon', 'In Progress', 1, '2025-07-29', 'mascot', 'Urgent', '2025-07-25 04:37:03', '2025-08-09 02:30:37', 'https://docs.google.com/presentation/d/1vJJcDykNxgJuNEEoqxK8s9vR7Kbpz9eb/edit?slide=id.p1#slide=id.p1'),
(128, '[\"688321ab9c7ca_mindef bear.png\"]', '[\"688321ab9fbe3_mindef notes.png\"]', 'waiting impose', 'Recruit Bear Inflatable Mascot', 'Archived', 1, '2025-08-07', 'mascot', 'Urgent', '2025-07-25 06:18:19', '2025-08-09 03:00:17', 'https://docs.google.com/presentation/d/18ktN5bNiJ_V6Zr6eCk2SggtO5BydS6YG/edit?slide=id.p1#slide=id.p1'),
(129, '[\"68862df3183aa_Screenshot 2025-07-27 204446.png\"]', '[\"68862df319485_twinkle notes.png\"]', 'process wrapping, tomorrow need to take photo', 'PA Twinkle Mascot', 'Archived', 1, '2025-08-05', 'mascot', 'Urgent', '2025-07-27 13:47:31', '2025-08-09 02:59:14', 'https://docs.google.com/presentation/d/1ZITKFirdEBzIPrFiF7NVDoD0snlHvdpx/edit?slide=id.p1#slide=id.p1'),
(130, '[\"688f3305c1303_property.png\"]', '[\"688f3305c1d53_property notes.png\"]', 'waiting draft', 'Property Guru Tiger Mascot', 'In Progress', 1, '2025-09-10', 'mascot', 'Urgent', '2025-08-03 09:59:33', '2025-08-09 02:19:29', 'https://docs.google.com/presentation/d/1_dIDLCca35YyA9F-6CjAjG43w-8WY_M_oZvtJIgKf2I/edit?slide=id.g37243ae1211_0_0#slide=id.g37243ae1211_0_0'),
(131, '[\"688f35337923a_WhatsApp Image 2025-08-03 at 17.06.32.jpeg\"]', NULL, 'waiting 3d print', 'Fairy God Mother - Statue Animatronic (1.2 Meters)', 'In Progress', 1, '2025-08-11', 'mascot', 'Urgent', '2025-08-03 10:08:51', '2025-08-09 03:00:11', NULL),
(132, '[\"68904f295702f_mandai.png\"]', '[\"689054fa49122_Mandai .png\"]', '4 AUG\r\nCUT WITH ACTUAL FABRIC FOR FIRST SAMPLE\r\nNOW USING ACTUAL FABRIC TO MAKE SECOND SAMPLE.\r\nTO SEND PHOTO OF SAMPLE BY END OF DAY, BEFORE GOING HOME.', 'MANDAI RANGER BUDDIES LIVE SHOW', 'In Progress', 12, '2025-08-13', 'costume', 'High', '2025-08-04 06:11:53', '2025-08-07 04:36:50', ''),
(133, '[\"68904f8185e26_waterway.png\"]', NULL, 'jacket, inner shirt, pants and headband', 'WATERWAY PRIMARY SCHOOL', 'In Progress', 27, '2025-08-07', 'costume', 'High', '2025-08-04 06:12:26', '2025-08-07 04:36:48', ''),
(134, '[\"6890502982633_HHN13.png\"]', NULL, '', 'RWS HHN13', 'In Progress', 13, '2025-08-17', 'costume', 'High', '2025-08-04 06:16:09', '2025-08-07 04:36:46', NULL),
(135, '[\"6890506e3e4c4_Mascot Bag.png\"]', '[\"6890506e3ea66_Mascot Bag 2.png\"]', 'done but haven\'t attach the logo brand tag\r\nReceived in SG, will review and update comments\r\n\r\n\r\n4 Oct\r\nsewing process', 'Mascot Bag', 'Archived', 1, '2025-08-30', 'costume', 'Low', '2025-08-04 06:17:18', '2025-08-04 09:46:37', NULL),
(138, '[\"689050d08a004_Screenshot 2025-08-04 131826.png\"]', NULL, '', 'RWS Opening Dancer Male ', 'Archived', 16, '2025-08-07', 'costume', 'High', '2025-08-04 06:18:56', '2025-08-04 09:46:37', NULL),
(141, '[\"68905122b278f_Screenshot 2025-08-04 131934.png\"]', NULL, '', 'RWS Trolls Fizzay Dancer 1', 'Archived', 9, '2025-08-07', 'costume', 'High', '2025-08-04 06:20:18', '2025-08-04 09:46:37', NULL),
(142, '[\"68905207c4ea2_Screenshot 2025-08-04 132306.png\"]', NULL, 'pinafore make sample first', 'RWS Trolls Fizzay Dancer 2', 'In Progress', 12, '2025-08-07', 'costume', '', '2025-08-04 06:22:42', '2025-08-04 09:48:37', ''),
(143, '[\"6890527ebd756_Screenshot 2025-08-04 132434.png\"]', NULL, 'TBC', 'Red Swastika Chinese Orchestra', 'In Progress', 1, '2025-08-31', 'costume', '', '2025-08-04 06:26:06', '2025-08-07 04:36:30', NULL),
(144, '[\"689052b207461_Screenshot 2025-08-04 132346.png\"]', NULL, '\"KT TO UPDATE\r\nNeed Wai Toh to help suggest improvements\r\n\r\n14/7 - SHOULI TO FINISHED ALTERATION\"', 'DEYI SEC SCHOOL MARCHING BAND', 'Upcoming', 1, '2025-08-31', 'costume', '', '2025-08-04 06:26:58', '2025-08-07 04:36:29', NULL),
(145, '[\"689052b5b9173_FERNVALE GREEN MINDS.png\"]', '[\"689052e79f07b_FERNVALE GREEN MINDS 2.png\"]', 'TBC', 'FERNVALE GREEN MINDS', 'In Progress', 10, '2025-08-31', 'costume', 'Low', '2025-08-04 06:27:01', '2025-08-04 09:46:37', ''),
(146, NULL, NULL, '', 'RWS Trolls Eadie M', 'In Progress', 6, '2025-08-07', 'costume', '', '2025-08-04 06:27:15', '2025-08-04 09:48:37', NULL),
(147, '[\"689052dfaceff_Screenshot 2025-08-04 132711.png\"]', NULL, '\"9/7- MEASUREMENT DONE\r\n\r\nPending samples from China\r\n\r\n18/7 - WILLING TO ORDER\"', 'RIVERSIDE PRIMARY SCHOOL (PURCHASE ONLY', 'Upcoming', 161, '2025-08-21', 'costume', '', '2025-08-04 06:27:43', '2025-08-07 04:36:18', NULL),
(148, '[\"6890530a273a7_Screenshot 2025-08-04 132811.png\"]', NULL, '', 'GOING LIVE TEE', 'Upcoming', 300, '2025-09-29', 'costume', '', '2025-08-04 06:28:26', '2025-08-07 04:36:17', NULL),
(149, '[\"6890533c12962_Screenshot 2025-08-04 132839.png\"]', NULL, '\"4 Aug\r\nPlan to clear RWS Trolls first, then will run RGPS\r\n\r\n21 JUL\r\nRECEIVED FABRIC QUANTITY REQUEST FROM BT.\r\nWILL ORDER\r\nSAMPLE IN SG, WILL ARRANGE TO SEND BACK TO BATAM.\r\n\r\nMeasurement on Thu\r\nRequested to check fabric balance, so we can arrange to order by Thu\"', 'RGPS STRING ENSEMBLE', 'In Progress', 10, '2025-09-01', 'costume', '', '2025-08-04 06:29:16', '2025-08-07 04:36:15', NULL),
(150, '[\"6890535e1ab4b_Screenshot 2025-08-04 132944.png\"]', NULL, '\"4 Aug\r\nPlan to clear RWS Trolls first, then will run RGPS\r\n17/7- SAMPLE COLLECTED\"', 'RGPS BAND', 'In Progress', 15, '2025-09-01', 'costume', 'Normal', '2025-08-04 06:29:50', '2025-08-13 02:15:56', 'https://docs.google.com/presentation/d/1Rn4afYSNGaiRFYL_GCEtvyHVTP4hTJnLhJQwX6UTDVk/edit?slide=id.g36f58103d38_0_1#slide=id.g36f58103d38_0_1'),
(151, '[\"6890538494531_Screenshot 2025-08-04 133000.png\"]', NULL, '\"4 Aug\r\nWilling pending fabric quantity to order.\r\nTari to give fabric quantity by 4 Aug.\r\nFabric customization takes 3 to 5 days, excluding freight.\r\n\r\nCut off date for all materials to reach BT.\r\n2 Aug\r\n- Any pending materials?\r\n- Deadline shifted to 22 Aug\"', 'RWS ASET EGYPTIAN', 'In Progress', 9, '2025-08-22', 'costume', '', '2025-08-04 06:30:28', '2025-08-07 04:36:10', NULL),
(152, '[\"6890539008c49_Ai Tong Primary School.png\"]', '[\"68905390092bb_Ai Tong Primary School 2.png\"]', '2 Aug\r\nAlterations to be done.\r\nCheck how many more meters of satin do we have left?\r\nOne costume to be remade.\r\nPlease send the size chart used and size allocation for doing the grading and drafts', 'Ai Tong Primary School', 'In Progress', 30, '2025-09-29', 'costume', 'Low', '2025-08-04 06:30:40', '2025-08-04 09:46:37', ''),
(153, '[\"689053e038de9_Screenshot 2025-08-04 133121.png\"]', NULL, '\"4 Aug\r\nRequest to keep digital draft.\r\n31/7 SAMPLE SEND TO BATAM FOR DRAFT COPY\r\n', 'DENTAL WRAP', 'In Progress', 1, '2025-08-07', 'costume', '', '2025-08-04 06:32:00', '2025-08-07 04:36:07', NULL),
(154, '[\"6890568b8db65_Cuplikan layar 2025-08-04 134248.png\"]', '[\"6890568b8df45_Cuplikan layar 2025-08-04 134312.png\"]', '4 Aug\r\n- Fabric arrived for quite a while, but have not cut yet, due to pending for draft.\r\n- Target to finish draft by 4 Aug\r\n26/7\r\nqueing cutting', 'RWS TROLLS FINALE FEMALE 2', 'In Progress', 8, '2025-08-07', 'costume', '', '2025-08-04 06:34:03', '2025-08-07 04:36:05', ''),
(155, '[\"6890548d593e5_Screenshot 2025-08-04 133411.png\"]', '[\"6890558bd54c8_Cuplikan layar 2025-08-04 133855.png\"]', '4 Aug\r\n- Tassel ETA Wed / end of the week\r\n2/8\r\nqueing cutting\r\n- Send bolero to Singapore on Monday\r\n- SG to match fringe\r\n26/7\r\nqueing cutting\r\nTo prioritise :\r\nBella C\r\nFlintzel ', 'RWS TROLLS EADIE M BOLERO JACKET', 'In Progress', 6, '2025-08-07', 'costume', '', '2025-08-04 06:34:53', '2025-08-07 04:36:03', ''),
(156, '[\"689054a29c34c_RWS Troll stilt.png\"]', '[\"689054a29cc20_RWS Troll stilt 2.png\"]', 'All cut finish.\r\nSewing to be done by 4 Aug', 'RWS TROLLS FINALE DANCER (STILT)', 'In Progress', 4, '2025-08-07', 'costume', '', '2025-08-04 06:35:14', '2025-08-07 04:36:00', NULL),
(157, '[\"689aa3015764f_WhatsApp_Image_2025-08-12_at_09.11.29.jpeg\"]', NULL, '', 'PUNGGOL GREEN PRIMARY SCHOOL (PGPS)', 'In Progress', 260, '2025-08-30', 'costume', 'Normal', '2025-08-12 02:12:17', '2025-08-12 02:12:17', 'https://docs.google.com/presentation/d/1JAM6-3yMTGzxPxUPJB6m8wTswu0GdNn45QeLhQOAYEg/edit?slide=id.g27841569d2c_0_395#slide=id.g27841569d2c_0_395');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mascot','costume') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
