-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 03, 2026 at 03:28 PM
-- Server version: 8.0.30
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `akademik`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` varchar(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `teacher_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `user_id`, `teacher_id`, `name`, `code`, `created_at`) VALUES
('01KB6S6G7FHD6ZYZP8BKNNYBFF', '01K8WAF2VCSHCNQYZQNDQ0K806', '01KB4C6WZDNXRWB3SMFXSZ9NT1', '5a', '30D93B', '2025-11-29 10:06:11'),
('01KB7B61CNTD2D91YRZ6P1GVCF', '01K8WAF2VCSHCNQYZQNDQ0K806', '01KB7B41WP5AAA8MZ0TVADNRF8', 'Kelas 6', 'E3810E', '2025-11-29 15:20:30');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `exam_id` varchar(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exam_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('UTS','UAS') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`exam_id`, `class_id`, `exam_name`, `type`, `start_time`, `end_time`, `is_active`, `created_at`) VALUES
('01K481ZFGBCR7JM9CC7V4G5534', '01KB7B61CNTD2D91YRZ6P1GVCF', 'PPKN', 'UTS', '2025-09-03 22:08:00', '2025-09-03 23:08:00', 0, '2025-09-03 22:08:59'),
('01KDW4DSB2J790GGWCV772C5DC', '01KB7B61CNTD2D91YRZ6P1GVCF', 'Matematika', 'UTS', '2026-01-01 14:50:00', '2026-01-01 16:37:00', 0, '2026-01-01 13:37:50'),
('01KDWY132TBQWB6Q4EC0D17VQ3', '01KB7B61CNTD2D91YRZ6P1GVCF', 'IPA', 'UTS', '2026-01-01 21:44:00', '2026-01-01 23:04:00', 0, '2026-01-01 21:05:17'),
('01KE0XWYHGNRY0MJ06RFN64RFD', '01KB7B61CNTD2D91YRZ6P1GVCF', 'IPS', 'UTS', '2026-01-03 11:40:00', '2026-01-03 12:30:00', 0, '2026-01-03 10:19:59'),
('01KE24PSBQEJ2S56V8DT0BXX89', '01KB7B61CNTD2D91YRZ6P1GVCF', 'PPKN', 'UAS', '2026-01-03 21:38:00', '2026-01-03 22:38:00', 1, '2026-01-03 21:38:12'),
('01KE27DRCN7DX8ZCCX7YAGK04N', '01KB7B61CNTD2D91YRZ6P1GVCF', 'Bahasa Inggris', 'UTS', '2026-01-05 10:25:00', '2026-01-05 23:25:00', 1, '2026-01-03 22:25:42');

-- --------------------------------------------------------

--
-- Table structure for table `exam_answers`
--

CREATE TABLE `exam_answers` (
  `id` varchar(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempt_id` varchar(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_id` varchar(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_answers`
--

INSERT INTO `exam_answers` (`id`, `attempt_id`, `question_id`, `answer`, `is_correct`) VALUES
('01K4824DC6SZWNRQXP0NAYKFQT', '01K4824B06C7980PC8CT4JFG4D', '01K4823JH7E473WS544G4DCQ73', 'B', 0),
('01K4824FENKT4N01HKPV18FCG8', '01K4824B06C7980PC8CT4JFG4D', '01K4823JH7SSA6C1412Z01EVPX', 'C', 1),
('01KDW8PDSX1YB3FJEZDG521NQZ', '01KDW8P91QCGENNMY30F8WKNWZ', '01KDW71P9FFFSTDRRQT5EGW3H1', 'C', 1),
('01KDW8PKXE1CSRPXC7ST2AZ91E', '01KDW8P91QCGENNMY30F8WKNWZ', '01KDW72VRKEJMHNARYFW38EJAS', 'B', 1),
('01KDW8PPRBCYJN7KD0JAGN4GRP', '01KDW8P91QCGENNMY30F8WKNWZ', '01KDW72VRKHX32W12173SZ56X7', 'D', 1),
('01KDX0B9BQB2W8CJ68NYH40PM8', '01KDX0B1KGKXBBWDS4HZM88H97', '01KDWY3K6XWKAT2W9BW4TVM2H0', 'A', 1),
('01KE12WXNZEQNDCZA8FAHF6WKQ', '01KE12WV5M90XBWSW83X4N93M7', '01KE0XYZDP22TSAK2RH5FY3ZEF', 'C', 1),
('01KE2708N5QFFNHZ8A4KYRFR6A', '01KE2706JJR0N9YFN84CMFVG6S', '01KE24T8SW6E4PNAN28V7X2Y5D', 'C', 1),
('01KE2724X0EYK2WP0EPDJG4G8D', '01KE27204TAD5M7GESDJ3H7C79', '01KE24T8SW6E4PNAN28V7X2Y5D', 'C', 1);

-- --------------------------------------------------------

--
-- Table structure for table `exam_attempts`
--

CREATE TABLE `exam_attempts` (
  `id` varchar(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exam_id` varchar(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `finished_time` datetime DEFAULT NULL,
  `score` decimal(5,2) DEFAULT '0.00',
  `status` enum('ongoing','finished') COLLATE utf8mb4_unicode_ci DEFAULT 'ongoing'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_attempts`
--

INSERT INTO `exam_attempts` (`id`, `exam_id`, `user_id`, `start_time`, `finished_time`, `score`, `status`) VALUES
('01K4824B06C7980PC8CT4JFG4D', '01K481ZFGBCR7JM9CC7V4G5534', '01K976AHZGDA70DMQ7M9MF6SHS', '2025-09-03 22:11:39', '2025-09-03 22:11:49', '50.00', 'finished'),
('01KDW8P91QCGENNMY30F8WKNWZ', '01KDW4DSB2J790GGWCV772C5DC', '01K976AHZGDA70DMQ7M9MF6SHS', '2026-01-01 14:52:23', '2026-01-01 14:52:40', '100.00', 'finished'),
('01KDX0B1KGKXBBWDS4HZM88H97', '01KDWY132TBQWB6Q4EC0D17VQ3', '01K976AHZGDA70DMQ7M9MF6SHS', '2026-01-01 21:45:41', '2026-01-01 21:45:51', '100.00', 'finished'),
('01KE12WV5M90XBWSW83X4N93M7', '01KE0XWYHGNRY0MJ06RFN64RFD', '01K976AHZGDA70DMQ7M9MF6SHS', '2026-01-03 11:47:19', '2026-01-03 11:47:24', '100.00', 'finished'),
('01KE2706JJR0N9YFN84CMFVG6S', '01KE24PSBQEJ2S56V8DT0BXX89', '01K912FR1QZHEWJ6MCVK8WEK5V', '2026-01-03 22:18:17', '2026-01-03 22:18:22', '100.00', 'finished'),
('01KE27204TAD5M7GESDJ3H7C79', '01KE24PSBQEJ2S56V8DT0BXX89', '01K976AHZGDA70DMQ7M9MF6SHS', '2026-01-03 22:19:16', '2026-01-03 22:19:27', '100.00', 'finished');

-- --------------------------------------------------------

--
-- Table structure for table `exam_questions`
--

CREATE TABLE `exam_questions` (
  `id` varchar(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exam_id` varchar(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_a` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_b` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_c` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_d` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `correct_answer` enum('A','B','C','D') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_questions`
--

INSERT INTO `exam_questions` (`id`, `exam_id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `created_at`) VALUES
('01K4823JH7E473WS544G4DCQ73', '01K481ZFGBCR7JM9CC7V4G5534', 'kapan sumpah pemuda', '28 januari', '20 juli', '29 oktober', '28 oktober', 'D', '2025-09-03 22:11:13'),
('01K4823JH7SSA6C1412Z01EVPX', '01K481ZFGBCR7JM9CC7V4G5534', 'kapan ppki dibentuk', '18 september', '19 Agustus', '18 Agustus', '10 Agustus', 'C', '2025-09-03 22:11:13'),
('01KDW71P9FFFSTDRRQT5EGW3H1', '01KDW4DSB2J790GGWCV772C5DC', '1+1', '0', '1', '2', '3', 'C', '2026-01-01 14:23:40'),
('01KDW72VRKEJMHNARYFW38EJAS', '01KDW4DSB2J790GGWCV772C5DC', '2-1', '0', '1', '2', '3', 'B', '2026-01-01 14:24:18'),
('01KDW72VRKHX32W12173SZ56X7', '01KDW4DSB2J790GGWCV772C5DC', '2+2', '1', '2', '3', '4', 'D', '2026-01-01 14:24:18'),
('01KDWY3K6XWKAT2W9BW4TVM2H0', '01KDWY132TBQWB6Q4EC0D17VQ3', 'jika air berubah menjadi padat maka itu dinamakan', 'beku', 'cair', 'lebur', 'uap', 'A', '2026-01-01 21:06:39'),
('01KE0XYZDP22TSAK2RH5FY3ZEF', '01KE0XWYHGNRY0MJ06RFN64RFD', 'negara dibagian selatan di indonesia?', 'malaysia', 'singapura', 'timor leste', 'papua nugini', 'C', '2026-01-03 10:21:06'),
('01KE24T8SW6E4PNAN28V7X2Y5D', '01KE24PSBQEJ2S56V8DT0BXX89', 'kapan hari pahlawan', '10 oktober', '11 November', '10 november', '11 oktober', 'C', '2026-01-03 21:40:06');

-- --------------------------------------------------------

--
-- Table structure for table `pbl_essay_questions`
--

CREATE TABLE `pbl_essay_questions` (
  `id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `essay_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FK ke pbl_solution_essays.id',
  `question_number` int NOT NULL COMMENT 'Nomor urut pertanyaan',
  `question_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Teks pertanyaan',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pbl_essay_questions`
--

INSERT INTO `pbl_essay_questions` (`id`, `essay_id`, `question_number`, `question_text`, `created_at`) VALUES
('01KDZMSG9VWTG989H9GHASSYH9', '01KDZGW9Z918WZXMCMWEZXFEJN', 1, 'apakah yang dimaksud', '2026-01-02 22:21:35');

-- --------------------------------------------------------

--
-- Table structure for table `pbl_essay_submissions`
--

CREATE TABLE `pbl_essay_submissions` (
  `id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `essay_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FK ke pbl_solution_essays.id',
  `user_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FK ke users.id (siswa)',
  `submission_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade` int DEFAULT NULL,
  `feedback` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pbl_essay_submissions`
--

INSERT INTO `pbl_essay_submissions` (`id`, `essay_id`, `user_id`, `submission_content`, `grade`, `feedback`, `created_at`, `updated_at`) VALUES
('01KDZMVC7QJZ196R44ETB0255C', '01KDZGW9Z918WZXMCMWEZXFEJN', '01K976AHZGDA70DMQ7M9MF6SHS', '1. fghjkl', 88, 'ok', '2026-01-02 22:22:36', '2026-01-02 22:23:23'),
('01KE271BXT1983EMSQSR802ZZ2', '01KDZGW9Z918WZXMCMWEZXFEJN', '01K912FR1QZHEWJ6MCVK8WEK5V', '1. sesuatu', NULL, NULL, '2026-01-03 22:18:56', '2026-01-03 22:18:56');

-- --------------------------------------------------------

--
-- Table structure for table `pbl_final_results`
--

CREATE TABLE `pbl_final_results` (
  `id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FK ke users.id (siswa)',
  `final_score` int DEFAULT '0' COMMENT 'Nilai Akhir (0-100)',
  `feedback` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Refleksi/Penguatan dari Guru',
  `status` enum('draft','published') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pbl_observation_results`
--

CREATE TABLE `pbl_observation_results` (
  `id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `observation_slot_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `score` int NOT NULL,
  `feedback` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pbl_observation_results`
--

INSERT INTO `pbl_observation_results` (`id`, `observation_slot_id`, `user_id`, `score`, `feedback`, `created_at`) VALUES
('01KDZMWD2Y2N4NSJ0PCKQAJE2P', '01KDZGX579QG6GMGJZDKP75SBE', '01K976AHZGDA70DMQ7M9MF6SHS', 90, 'ok', '2026-01-02 22:23:10');

-- --------------------------------------------------------

--
-- Table structure for table `pbl_observation_slots`
--

CREATE TABLE `pbl_observation_slots` (
  `id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pbl_observation_slots`
--

INSERT INTO `pbl_observation_slots` (`id`, `class_id`, `title`, `description`, `created_at`) VALUES
('01KDZGX579QG6GMGJZDKP75SBE', '01KB7B61CNTD2D91YRZ6P1GVCF', 'tugas', 'PPKN', '2026-01-02 21:13:40');

-- --------------------------------------------------------

--
-- Table structure for table `pbl_observation_uploads`
--

CREATE TABLE `pbl_observation_uploads` (
  `id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `observation_slot_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ID Siswa',
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pbl_observation_uploads`
--

INSERT INTO `pbl_observation_uploads` (`id`, `observation_slot_id`, `user_id`, `file_name`, `original_name`, `description`, `created_at`) VALUES
('01KDZMTYQKSHFHA5S60S20HEX0', '01KDZGX579QG6GMGJZDKP75SBE', '01K976AHZGDA70DMQ7M9MF6SHS', 'be50dac1446e080450893b38dcb2f41e.png', '198e1a0b28ca666a9ddcd351365a2d04.png', 'tugas', '2026-01-02 22:22:22');

-- --------------------------------------------------------

--
-- Table structure for table `pbl_orientasi`
--

CREATE TABLE `pbl_orientasi` (
  `id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reflection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pbl_orientasi`
--

INSERT INTO `pbl_orientasi` (`id`, `class_id`, `title`, `reflection`, `file_path`, `created_at`) VALUES
('01KBH01DMKF6VGRXJJ6F1DE5TB', '01KB6S6G7FHD6ZYZP8BKNNYBFF', 'test', 'test', 'uploads/pbl/01KCKM5GXJ0KNXQ9AYVABAD50C.pdf', '2025-12-03 09:18:08'),
('01KCN7E22Y0P7WYBFQPM4XJVKZ', '01KB6S6G7FHD6ZYZP8BKNNYBFF', 'materi bahasa indonesia', 'materi pertemuan 1', 'uploads/pbl/01KCN9QQ6EZ1EJMB25ZMRN7573.png', '2025-12-17 11:00:02'),
('01KCNY7QZRZ8V4PFPNPHFWB5PX', '01KB7B61CNTD2D91YRZ6P1GVCF', 'Materi PKM ', 'Belajar kebaikan dari hal kecil untuk sekitar dan Indonesia', 'uploads/pbl/01KCQAHGPE0Z9CK1G08MDCBQJG.pdf', '2025-12-17 17:38:32');

-- --------------------------------------------------------

--
-- Table structure for table `pbl_quizzes`
--

CREATE TABLE `pbl_quizzes` (
  `id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pbl_quizzes`
--

INSERT INTO `pbl_quizzes` (`id`, `class_id`, `title`, `description`, `created_at`) VALUES
('01KBH0DPH9HD8DEV3N7T3XQQN1', '01KB6S6G7FHD6ZYZP8BKNNYBFF', 'test', 'test', '2025-12-03 09:24:51'),
('01KDZF5SN3HSF8170290Z49QRN', '01KB7B61CNTD2D91YRZ6P1GVCF', 'kuis', 'Matematika', '2026-01-02 20:43:26'),
('01KDZHZ8EGS9M17SCAXHCA2H3W', '01KB7B61CNTD2D91YRZ6P1GVCF', 'kuis 2', 'IPA', '2026-01-02 21:32:18');

-- --------------------------------------------------------

--
-- Table structure for table `pbl_quiz_answers`
--

CREATE TABLE `pbl_quiz_answers` (
  `id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `result_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `selected_option` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_correct` tinyint(1) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pbl_quiz_answers`
--

INSERT INTO `pbl_quiz_answers` (`id`, `result_id`, `question_id`, `selected_option`, `is_correct`, `created_at`) VALUES
('01KCKSE1CFHSZWD1YB6S6NRESD', '01KCKSE1CFYS36003XHZXJ181J', '01KBKK9A955KEQB14ZXVB7M0XP', 'C', 1, '2025-12-16 21:36:07'),
('01KCKSE1CFCC0QC2CE01B3N9ER', '01KCKSE1CFYS36003XHZXJ181J', '01KBKK9A956DA39N3BTY2JTAGW', 'C', 1, '2025-12-16 21:36:07'),
('01KCP9569ZYTMFKT2WW3NR0BFX', '01KCP9569ZCHVC34QE313N25R2', '01KCP8W65B3MXHP26TS671B8TH', 'C', 1, '2025-12-17 20:49:23'),
('01KCP9569ZB4H33KH6Y1E29GHC', '01KCP9569ZCHVC34QE313N25R2', '01KCP8W65BTHN88Y9QW8PXEAME', 'C', 1, '2025-12-17 20:49:23'),
('01KCPCR97DQ17VC3SE52W30V5N', '01KCPCR97DTQ4FCRYPG7W7FZSJ', '01KCP8W65B3MXHP26TS671B8TH', 'C', 1, '2025-12-17 21:52:15'),
('01KCPCR97DHW80KR9EXHGGZ1Y7', '01KCPCR97DTQ4FCRYPG7W7FZSJ', '01KCP8W65BTHN88Y9QW8PXEAME', 'C', 1, '2025-12-17 21:52:15'),
('01KCQX46503WZ93RJCNKRC2YMY', '01KCQX4650QV4KQMJ64454MN87', '01KCQW7MTPTBQEQ5QDGXFDBZWZ', 'A', 1, '2025-12-18 11:57:36'),
('01KCQX59NJ0C0GFJ158TNFYKFX', '01KCQX59NJ70YE0NHA20JHV02Z', '01KCP8W65B3MXHP26TS671B8TH', 'A', 0, '2025-12-18 11:58:13'),
('01KCQX59NJA3D30A9TGGCWNW5R', '01KCQX59NJ70YE0NHA20JHV02Z', '01KCP8W65BTHN88Y9QW8PXEAME', 'B', 1, '2025-12-18 11:58:13'),
('01KCQX7MS1NRBFHJBT1YV23FSK', '01KCQX7MS1KYPDQGV2DH4DXVMD', '01KCQW7MTPTBQEQ5QDGXFDBZWZ', 'A', 1, '2025-12-18 11:59:30'),
('01KDZJ0A13EKGXFQP3SMG5KD06', '01KDZJ0A130HPVEP67EY4A90X4', '01KDZHZKFGHZMWXN6KHDYBFNP3', 'A', 1, '2026-01-02 21:32:53'),
('01KDZJ0K1XCW206Z02R0XD44TQ', '01KDZJ0K1XG6TSF7F71E0XADWX', '01KDZHYY0HAHPF2ET1MVD6S4C8', 'A', 1, '2026-01-02 21:33:01'),
('01KE270XZQBPVMNW6BQK3VWGYB', '01KE270XZQ095XVM5Z7KRJQ3XG', '01KDZHZKFGHZMWXN6KHDYBFNP3', 'A', 1, '2026-01-03 22:18:42');

-- --------------------------------------------------------

--
-- Table structure for table `pbl_quiz_questions`
--

CREATE TABLE `pbl_quiz_questions` (
  `id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quiz_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_a` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_b` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_c` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_d` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `correct_answer` enum('A','B','C','D') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pbl_quiz_questions`
--

INSERT INTO `pbl_quiz_questions` (`id`, `quiz_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `created_at`) VALUES
('01KBKK9A955KEQB14ZXVB7M0XP', '01KBH0DPH9HD8DEV3N7T3XQQN1', '1+1=', '1', '0', '2', '3', 'C', '2025-12-04 09:32:59'),
('01KBKK9A956DA39N3BTY2JTAGW', '01KBH0DPH9HD8DEV3N7T3XQQN1', 'siapa', 'Saya', 'Aku', 'Dia', 'Kamu', 'C', '2025-12-04 09:32:59'),
('01KDZHYY0HAHPF2ET1MVD6S4C8', '01KDZF5SN3HSF8170290Z49QRN', 'siapa', 'a', 'b', 'c', 'd', 'A', '2026-01-02 21:32:07'),
('01KDZHZKFGHZMWXN6KHDYBFNP3', '01KDZHZ8EGS9M17SCAXHCA2H3W', 'apa', 'a', 'b', 'c', 'd', 'A', '2026-01-02 21:32:29');

-- --------------------------------------------------------

--
-- Table structure for table `pbl_quiz_results`
--

CREATE TABLE `pbl_quiz_results` (
  `id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quiz_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `score` int NOT NULL,
  `total_correct` int NOT NULL,
  `total_questions` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pbl_quiz_results`
--

INSERT INTO `pbl_quiz_results` (`id`, `quiz_id`, `user_id`, `score`, `total_correct`, `total_questions`, `created_at`) VALUES
('01KCKSE1CFYS36003XHZXJ181J', '01KBH0DPH9HD8DEV3N7T3XQQN1', '01K912FR1QZHEWJ6MCVK8WEK5V', 100, 2, 2, '2025-12-16 21:36:07'),
('01KCQX4650QV4KQMJ64454MN87', '01KCQVZ4JNK7097M1RHWYVMWK2', '01K976AHZGDA70DMQ7M9MF6SHS', 100, 1, 1, '2025-12-18 11:57:36'),
('01KCQX7MS1KYPDQGV2DH4DXVMD', '01KCQVZ4JNK7097M1RHWYVMWK2', '01K912FR1QZHEWJ6MCVK8WEK5V', 100, 1, 1, '2025-12-18 11:59:29'),
('01KDZJ0A130HPVEP67EY4A90X4', '01KDZHZ8EGS9M17SCAXHCA2H3W', '01K976AHZGDA70DMQ7M9MF6SHS', 100, 1, 1, '2026-01-02 21:32:52'),
('01KDZJ0K1XG6TSF7F71E0XADWX', '01KDZF5SN3HSF8170290Z49QRN', '01K976AHZGDA70DMQ7M9MF6SHS', 100, 1, 1, '2026-01-02 21:33:01'),
('01KE270XZQ095XVM5Z7KRJQ3XG', '01KDZHZ8EGS9M17SCAXHCA2H3W', '01K912FR1QZHEWJ6MCVK8WEK5V', 100, 1, 1, '2026-01-03 22:18:41');

-- --------------------------------------------------------

--
-- Table structure for table `pbl_reflections`
--

CREATE TABLE `pbl_reflections` (
  `id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Siswa ID',
  `teacher_reflection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `student_feedback` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pbl_reflections`
--

INSERT INTO `pbl_reflections` (`id`, `class_id`, `user_id`, `teacher_reflection`, `student_feedback`, `created_at`, `updated_at`) VALUES
('01KCN2MSA5ZM85XE1Q82TJSWVX', '01KB6S6G7FHD6ZYZP8BKNNYBFF', '01K912FR1QZHEWJ6MCVK8WEK5V', 'cukup baik', 'bagus', '2025-12-17 09:36:20', '2025-12-17 09:36:20'),
('01KCPDGM5084MNRTZVRMEE9CWK', '01KB7B61CNTD2D91YRZ6P1GVCF', '01K976AHZGDA70DMQ7M9MF6SHS', 'bagus', 'terus semangat', '2025-12-17 22:05:32', '2026-01-02 12:24:07'),
('01KCQXHA9ZG7XGWWWNJD2CXE6P', '01KB7B61CNTD2D91YRZ6P1GVCF', '01K912FR1QZHEWJ6MCVK8WEK5V', 'cukup bagus', 'belajar yang giat', '2025-12-18 12:04:46', '2026-01-03 10:42:59');

-- --------------------------------------------------------

--
-- Table structure for table `pbl_solution_essays`
--

CREATE TABLE `pbl_solution_essays` (
  `id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Instruksi/prompt untuk esai',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pbl_solution_essays`
--

INSERT INTO `pbl_solution_essays` (`id`, `class_id`, `title`, `description`, `created_at`) VALUES
('01KBMBYS6A9ZPHATNRX3JH175T', '01KB6S6G7FHD6ZYZP8BKNNYBFF', 'test', 'test', '2025-12-04 16:44:08'),
('01KDZGW9Z918WZXMCMWEZXFEJN', '01KB7B61CNTD2D91YRZ6P1GVCF', 'esai', 'PPKN', '2026-01-02 21:13:12');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` varchar(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role`) VALUES
('01K8WA6A9HTVM98RYM1P5ZWNYH', 'Admin'),
('01K8WA6WVXEKX7JK822G9PVZG9', 'Guru'),
('01K8WA74MMB7VBRM1Y05NS7GNQ', 'Siswa'),
('01K8WA7CX41SY2BEDRT2QBXQ7Q', 'Tamu');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` varchar(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_id` varchar(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `class_id`, `created_at`) VALUES
('01KCP92PV5YWRY19QC05GM1XXP', '01K976AHZGDA70DMQ7M9MF6SHS', '01KB7B61CNTD2D91YRZ6P1GVCF', '2025-12-17 20:48:02'),
('01KCQV300A2GVRF22949HGKZC3', '01K94KA9TRKC5ZEAPM3PRKVP9S', '01KB6S6G7FHD6ZYZP8BKNNYBFF', '2025-12-18 11:22:00'),
('01KCQX6V56QTZG2TCEKFMM5S23', '01K912FR1QZHEWJ6MCVK8WEK5V', '01KB7B61CNTD2D91YRZ6P1GVCF', '2025-12-18 11:59:03');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` varchar(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `user_id`, `created_at`) VALUES
('01KB4C6WZDNXRWB3SMFXSZ9NT1', '01KB4C6WR9SGY6RMQK5HAVA1AB', '2025-11-28 11:40:43'),
('01KB7B41WP5AAA8MZ0TVADNRF8', '01KB7B41P3TQC2JSDWFMEZTSE8', '2025-11-29 15:19:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` varchar(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '01K8WA74MMB7VBRM1Y05NS7GNQ',
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'foto.jpg',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role_id`, `name`, `email`, `image`, `is_active`, `created_at`) VALUES
('01K8WAF2VCSHCNQYZQNDQ0K806', 'Admin', '$2y$10$ZBicPw.RXfH2mZVnD.IHruqGGg9S8pVR/cQWGOnujiryKogfnqakq', '01K8WA6A9HTVM98RYM1P5ZWNYH', 'adm', 'admin@example.com', 'foto.jpg', 1, '2025-10-31 12:04:55'),
('01K8WTRAA9YN933F4BJ2NXXNKQ', 'guru', '$2y$10$H3S1k38s5/ItrsR.6fOKI.4z74dJtcmHe/AUts9cee6T6rXYkpWJy', '01K8WA6WVXEKX7JK822G9PVZG9', 'guru_ipas', 'guru_ipas@example.com', 'foto.jpg', 1, '2025-10-31 16:49:35'),
('01K912FR1QZHEWJ6MCVK8WEK5V', 'sulastri', '$2y$10$Sl7f2LZh5aRqpR1HwsGlwumimlhdRlWVrXBBCu6QlRbz8OA7APJbK', '01K8WA74MMB7VBRM1Y05NS7GNQ', 'Sulastri', 'sulastri6@email.id', 'foto.jpg', 1, '2025-11-02 08:21:41'),
('01K94KA9TRKC5ZEAPM3PRKVP9S', 'herman', '$2y$10$U/opFpM538ZKLQf2OSF.3evyqEERt/bA4bsxaDsWL4nFqQi1SlRIe', '01K8WA74MMB7VBRM1Y05NS7GNQ', 'Herman', 'herman6@email.id', 'foto.jpg', 1, '2025-11-03 17:13:31'),
('01K976AHZGDA70DMQ7M9MF6SHS', 'mujaki', '$2y$10$QpYl4IuqPUX1JXXgnhoN4OBg.nPy5Ra/9rPmEvtURp7UtkRcXqh/G', '01K8WA74MMB7VBRM1Y05NS7GNQ', 'Mujaki', 'Mujaki6@email.id', 'foto.jpg', 1, '2025-11-04 17:24:11'),
('01KB4C6WR9SGY6RMQK5HAVA1AB', 'siti_jainabun', '$2y$10$fHr8e70PrRoJ98su4XNBFebJ6xhM02gUCroXw5wc8gv9Fstng.nrG', '01K8WA6WVXEKX7JK822G9PVZG9', 'Ibu SITI JAINABUN', 'siti_jainabun@email.id', 'foto.jpg', 1, '2025-11-28 11:40:43'),
('01KB7B41P3TQC2JSDWFMEZTSE8', 'juhaeiriah', '$2y$10$HgUDzfFbppAUNhtwBEe7Zu0JQJ/KNSUueEzQpHafnPAJltjpj/Zy.', '01K8WA6WVXEKX7JK822G9PVZG9', 'Ibu Juhaeiriah', 'juhaeiriah@email.id', 'foto.jpg', 1, '2025-11-29 15:19:25');

-- --------------------------------------------------------

--
-- Table structure for table `user_access_menu`
--

CREATE TABLE `user_access_menu` (
  `id` int NOT NULL,
  `role_id` varchar(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_access_menu`
--

INSERT INTO `user_access_menu` (`id`, `role_id`, `menu_id`) VALUES
(1, '01K8WA6A9HTVM98RYM1P5ZWNYH', 1),
(2, '01K8WA6A9HTVM98RYM1P5ZWNYH', 2),
(3, '01K8WA6A9HTVM98RYM1P5ZWNYH', 3),
(4, '01K8WA6A9HTVM98RYM1P5ZWNYH', 4),
(5, '01K8WA6WVXEKX7JK822G9PVZG9', 2),
(6, '01K8WA74MMB7VBRM1Y05NS7GNQ', 3),
(8, '01K8WA6WVXEKX7JK822G9PVZG9', 6),
(9, '01K8WA74MMB7VBRM1Y05NS7GNQ', 6);

-- --------------------------------------------------------

--
-- Table structure for table `user_menu`
--

CREATE TABLE `user_menu` (
  `id` int NOT NULL,
  `menu` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_menu`
--

INSERT INTO `user_menu` (`id`, `menu`) VALUES
(1, 'Admin'),
(2, 'Guru'),
(3, 'Siswa'),
(4, 'Menu'),
(6, 'Ujian');

-- --------------------------------------------------------

--
-- Table structure for table `user_sub_menu`
--

CREATE TABLE `user_sub_menu` (
  `id` int NOT NULL,
  `menu_id` int NOT NULL,
  `title` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_sub_menu`
--

INSERT INTO `user_sub_menu` (`id`, `menu_id`, `title`, `url`, `icon`, `is_active`) VALUES
(1, 1, 'Dashboard Admin', 'admin/dashboard', 'bi-grid', 1),
(3, 1, 'Kelola Guru', 'admin/dashboard/teachers', 'bi-person', 1),
(4, 1, 'Kelola Murid', 'admin/dashboard/students', 'bi-people', 1),
(5, 4, 'Kelola Menu', 'menu', 'bi-folder', 1),
(6, 4, 'Kelola Submenu', 'menu/submenu', 'bi-folder2-open', 1),
(10, 2, 'Dashboard Guru', 'guru/dashboard', 'bi-grid', 1),
(11, 3, 'Dashboard Siswa', 'siswa/dashboard', 'bi-grid', 1),
(12, 1, 'Kelola Kelas', 'admin/dashboard/classes', 'bi-easel', 1),
(13, 6, 'Ujian', 'exam', 'bi-journal-bookmark', 1),
(14, 2, 'Laporan', 'guru/laporan', 'bi-bar-chart', 1),
(15, 3, 'Laporan', 'siswa/laporan', 'bi-bar-chart', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`exam_id`);

--
-- Indexes for table `exam_answers`
--
ALTER TABLE `exam_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pbl_essay_questions`
--
ALTER TABLE `pbl_essay_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_question_essay` (`essay_id`);

--
-- Indexes for table `pbl_essay_submissions`
--
ALTER TABLE `pbl_essay_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `essay_id` (`essay_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pbl_observation_slots`
--
ALTER TABLE `pbl_observation_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `pbl_observation_uploads`
--
ALTER TABLE `pbl_observation_uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `observation_slot_id` (`observation_slot_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pbl_quizzes`
--
ALTER TABLE `pbl_quizzes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pbl_quiz_questions`
--
ALTER TABLE `pbl_quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `pbl_quiz_results`
--
ALTER TABLE `pbl_quiz_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pbl_solution_essays`
--
ALTER TABLE `pbl_solution_essays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_access_menu`
--
ALTER TABLE `user_access_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `user_menu`
--
ALTER TABLE `user_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_sub_menu`
--
ALTER TABLE `user_sub_menu`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_access_menu`
--
ALTER TABLE `user_access_menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_menu`
--
ALTER TABLE `user_menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_sub_menu`
--
ALTER TABLE `user_sub_menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pbl_essay_questions`
--
ALTER TABLE `pbl_essay_questions`
  ADD CONSTRAINT `fk_question_essay` FOREIGN KEY (`essay_id`) REFERENCES `pbl_solution_essays` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pbl_essay_submissions`
--
ALTER TABLE `pbl_essay_submissions`
  ADD CONSTRAINT `pbl_essay_submissions_ibfk_1` FOREIGN KEY (`essay_id`) REFERENCES `pbl_solution_essays` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pbl_observation_uploads`
--
ALTER TABLE `pbl_observation_uploads`
  ADD CONSTRAINT `fk_obs_slot` FOREIGN KEY (`observation_slot_id`) REFERENCES `pbl_observation_slots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pbl_quiz_questions`
--
ALTER TABLE `pbl_quiz_questions`
  ADD CONSTRAINT `pbl_quiz_questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `pbl_quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `user_access_menu`
--
ALTER TABLE `user_access_menu`
  ADD CONSTRAINT `user_access_menu_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_access_menu_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `user_menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
