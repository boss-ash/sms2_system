-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 01, 2026 at 01:48 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crad_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `chapter_evaluations`
--

CREATE TABLE `chapter_evaluations` (
  `id` int(10) UNSIGNED NOT NULL,
  `submission_id` int(10) UNSIGNED NOT NULL,
  `research_group_id` int(10) UNSIGNED NOT NULL,
  `evaluator_user_id` int(10) UNSIGNED NOT NULL,
  `evaluator_name` varchar(150) NOT NULL DEFAULT '',
  `content_score` decimal(5,2) NOT NULL,
  `methodology_score` decimal(5,2) NOT NULL,
  `references_score` decimal(5,2) NOT NULL,
  `format_score` decimal(5,2) NOT NULL,
  `content_remarks` text DEFAULT NULL,
  `methodology_remarks` text DEFAULT NULL,
  `references_remarks` text DEFAULT NULL,
  `format_remarks` text DEFAULT NULL,
  `overall_feedback` text DEFAULT NULL,
  `result` enum('APPROVED','APPROVED WITH REVISION') NOT NULL,
  `overall_score` decimal(5,2) DEFAULT NULL,
  `evaluated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chapter_evaluations`
--

INSERT INTO `chapter_evaluations` (`id`, `submission_id`, `research_group_id`, `evaluator_user_id`, `evaluator_name`, `content_score`, `methodology_score`, `references_score`, `format_score`, `content_remarks`, `methodology_remarks`, `references_remarks`, `format_remarks`, `overall_feedback`, `result`, `overall_score`, `evaluated_at`, `created_at`) VALUES
(25, 29, 61, 475, 'Grammarian', 100.00, 100.00, 100.00, 100.00, '', '', '', '', '', 'APPROVED', 100.00, '2026-08-23 04:07:50', '2026-08-23 04:07:50'),
(26, 30, 61, 475, 'Grammarian', 100.00, 100.00, 100.00, 99.97, '', '', '', '', '', 'APPROVED', 99.99, '2026-08-23 04:07:59', '2026-08-23 04:07:59'),
(27, 31, 61, 475, 'Grammarian', 100.00, 100.00, 100.00, 100.00, '', '', '', '', '', 'APPROVED', 100.00, '2026-08-23 04:08:08', '2026-08-23 04:08:08'),
(28, 32, 62, 475, 'Grammarian', 100.00, 100.00, 100.00, 100.00, '', '', '', '', '', 'APPROVED', 100.00, '2026-08-28 15:33:31', '2026-08-28 15:33:31'),
(29, 33, 62, 475, 'Grammarian', 100.00, 100.00, 100.00, 100.00, '', '', '', '', '', 'APPROVED', 100.00, '2026-08-28 15:33:46', '2026-08-28 15:33:46'),
(30, 34, 62, 475, 'Grammarian', 100.00, 100.00, 100.00, 100.00, '', '', '', '', '', 'APPROVED', 100.00, '2026-08-28 15:33:53', '2026-08-28 15:33:53'),
(31, 35, 63, 475, 'Grammarian', 99.00, 99.00, 99.00, 99.00, '', '', '', '', '', 'APPROVED', 99.00, '2026-08-28 16:30:28', '2026-08-28 16:30:28'),
(32, 36, 63, 475, 'Grammarian', 99.00, 99.00, 99.00, 98.96, '', '', '', '', '', 'APPROVED', 98.99, '2026-08-28 16:30:40', '2026-08-28 16:30:40'),
(33, 37, 63, 475, 'Grammarian', 99.00, 99.00, 99.00, 99.00, '', '', '', '', '', 'APPROVED', 99.00, '2026-08-28 16:30:48', '2026-08-28 16:30:48'),
(34, 38, 64, 475, 'Grammarian', 100.00, 100.00, 100.00, 100.00, '', '', '', '', '', 'APPROVED', 100.00, '2026-08-31 05:32:02', '2026-08-31 05:32:02'),
(35, 39, 64, 475, 'Grammarian', 100.00, 100.00, 100.00, 100.00, '', '', '', '', '', 'APPROVED', 100.00, '2026-08-31 05:32:16', '2026-08-31 05:32:16'),
(36, 40, 64, 475, 'Grammarian', 100.00, 100.00, 100.00, 100.00, '', '', '', '', '', 'APPROVED', 100.00, '2026-08-31 05:32:32', '2026-08-31 05:32:32'),
(37, 41, 65, 475, 'Grammarian', 100.00, 100.00, 100.00, 100.00, '', '', '', '', '', 'APPROVED', 100.00, '2026-08-31 09:42:10', '2026-08-31 09:42:10'),
(38, 42, 65, 475, 'Grammarian', 100.00, 100.00, 99.97, 100.00, '', '', '', '', '', 'APPROVED', 99.99, '2026-08-31 09:42:25', '2026-08-31 09:42:25'),
(39, 43, 65, 475, 'Grammarian', 100.00, 100.00, 100.00, 100.00, '', '', '', '', '', 'APPROVED', 100.00, '2026-08-31 09:42:36', '2026-08-31 09:42:36');

-- --------------------------------------------------------

--
-- Table structure for table `chapter_evaluation_notifications`
--

CREATE TABLE `chapter_evaluation_notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_key` varchar(120) NOT NULL,
  `recipient_user_id` int(10) UNSIGNED DEFAULT NULL,
  `recipient_role` varchar(60) NOT NULL DEFAULT '',
  `recipient_email` varchar(190) NOT NULL DEFAULT '',
  `submission_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(60) NOT NULL,
  `title` varchar(180) NOT NULL,
  `body` text NOT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chapter_evaluation_notifications`
--

INSERT INTO `chapter_evaluation_notifications` (`id`, `event_key`, `recipient_user_id`, `recipient_role`, `recipient_email`, `submission_id`, `type`, `title`, `body`, `url`, `is_read`, `created_at`) VALUES
(1, 'evaluator:new:1:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 1, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 1 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=1', 0, '2026-08-14 11:24:27'),
(2, 'evaluator:new:2:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 2, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 2 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=2', 0, '2026-08-14 11:24:31'),
(3, 'evaluator:new:3:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 3, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 3 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=3', 1, '2026-08-14 11:24:36'),
(4, 'student:under_review:1', 9, 'student', 'kenlangmalakas0308@gmail.com', 1, 'under_review', 'Chapter 1 is under review', 'Chapter 1 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 11:24:57'),
(5, 'student:under_review:3', 9, 'student', 'kenlangmalakas0308@gmail.com', 3, 'under_review', 'Chapter 3 is under review', 'Chapter 3 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 11:36:01'),
(6, 'student:needs_revision:3', 9, 'student', 'kenlangmalakas0308@gmail.com', 3, 'needs_revision', 'Chapter 3 needs revision', 'Chapter 3 Version 1 is now Needs Revision.', '/sms2_system/modules/student-portal/pages/submission-status.php', 1, '2026-08-14 11:36:16'),
(7, 'evaluator:new:4:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 4, 'new_submission', 'Revised Chapter Submitted', 'Group 01 submitted Chapter 3 Version 2 for re-evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=4', 1, '2026-08-14 11:36:41'),
(8, 'evaluator:new:5:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 5, 'new_submission', 'New Chapter Submission', 'Group 33 submitted Chapter 1 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=5', 1, '2026-08-14 12:07:57'),
(9, 'evaluator:new:6:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 6, 'new_submission', 'New Chapter Submission', 'Group 33 submitted Chapter 2 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=6', 0, '2026-08-14 12:08:00'),
(10, 'evaluator:new:7:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 7, 'new_submission', 'New Chapter Submission', 'Group 33 submitted Chapter 3 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=7', 1, '2026-08-14 12:08:03'),
(11, 'student:under_review:5', 9, 'student', 'kenlangmalakas0308@gmail.com', 5, 'under_review', 'Chapter 1 is under review', 'Chapter 1 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 12:16:20'),
(12, 'student:accepted:5', 9, 'student', 'kenlangmalakas0308@gmail.com', 5, 'accepted', 'Chapter 1 accepted', 'Chapter 1 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 12:16:27'),
(13, 'student:under_review:6', 9, 'student', 'kenlangmalakas0308@gmail.com', 6, 'under_review', 'Chapter 2 is under review', 'Chapter 2 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 12:16:32'),
(14, 'student:accepted:6', 9, 'student', 'kenlangmalakas0308@gmail.com', 6, 'accepted', 'Chapter 2 accepted', 'Chapter 2 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 12:16:37'),
(15, 'evaluator:new:8:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 8, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 1 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=8', 0, '2026-08-14 12:46:13'),
(16, 'evaluator:new:9:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 9, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 3 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=9', 0, '2026-08-14 12:46:17'),
(17, 'evaluator:new:10:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 10, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 2 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=10', 0, '2026-08-14 12:46:20'),
(18, 'student:under_review:8', 9, 'student', 'kenlangmalakas0308@gmail.com', 8, 'under_review', 'Chapter 1 is under review', 'Chapter 1 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 12:47:57'),
(19, 'student:accepted:8', 9, 'student', 'kenlangmalakas0308@gmail.com', 8, 'accepted', 'Chapter 1 accepted', 'Chapter 1 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 12:48:05'),
(20, 'student:under_review:9', 9, 'student', 'kenlangmalakas0308@gmail.com', 9, 'under_review', 'Chapter 3 is under review', 'Chapter 3 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 12:48:09'),
(21, 'student:accepted:9', 9, 'student', 'kenlangmalakas0308@gmail.com', 9, 'accepted', 'Chapter 3 accepted', 'Chapter 3 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 12:48:15'),
(22, 'student:under_review:10', 9, 'student', 'kenlangmalakas0308@gmail.com', 10, 'under_review', 'Chapter 2 is under review', 'Chapter 2 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 12:48:20'),
(23, 'student:accepted:10', 9, 'student', 'kenlangmalakas0308@gmail.com', 10, 'accepted', 'Chapter 2 accepted', 'Chapter 2 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 12:48:26'),
(24, 'evaluator:new:11:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 11, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 1 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=11', 0, '2026-08-14 13:23:21'),
(25, 'evaluator:new:12:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 12, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 2 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=12', 0, '2026-08-14 13:23:25'),
(26, 'evaluator:new:13:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 13, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 3 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=13', 0, '2026-08-14 13:23:28'),
(27, 'student:under_review:11', 9, 'student', 'kenlangmalakas0308@gmail.com', 11, 'under_review', 'Chapter 1 is under review', 'Chapter 1 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 13:45:53'),
(28, 'student:accepted:11', 9, 'student', 'kenlangmalakas0308@gmail.com', 11, 'accepted', 'Chapter 1 accepted', 'Chapter 1 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 13:46:02'),
(29, 'student:under_review:12', 9, 'student', 'kenlangmalakas0308@gmail.com', 12, 'under_review', 'Chapter 2 is under review', 'Chapter 2 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 13:46:08'),
(30, 'student:accepted:12', 9, 'student', 'kenlangmalakas0308@gmail.com', 12, 'accepted', 'Chapter 2 accepted', 'Chapter 2 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 13:46:13'),
(31, 'student:under_review:13', 9, 'student', 'kenlangmalakas0308@gmail.com', 13, 'under_review', 'Chapter 3 is under review', 'Chapter 3 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 13:46:16'),
(32, 'student:accepted:13', 9, 'student', 'kenlangmalakas0308@gmail.com', 13, 'accepted', 'Chapter 3 accepted', 'Chapter 3 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 13:46:22'),
(33, 'evaluator:new:14:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 14, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 1 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=14', 0, '2026-08-14 16:40:44'),
(34, 'student:under_review:14', 9, 'student', 'kenlangmalakas0308@gmail.com', 14, 'under_review', 'Chapter 1 is under review', 'Chapter 1 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 16:42:06'),
(35, 'student:accepted:14', 9, 'student', 'kenlangmalakas0308@gmail.com', 14, 'accepted', 'Chapter 1 accepted', 'Chapter 1 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 1, '2026-08-14 16:42:19'),
(36, 'evaluator:new:15:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 15, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 1 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=15', 0, '2026-08-14 17:36:01'),
(37, 'student:under_review:15', 9, 'student', 'kenlangmalakas0308@gmail.com', 15, 'under_review', 'Chapter 1 is under review', 'Chapter 1 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 17:36:17'),
(38, 'student:accepted:15', 9, 'student', 'kenlangmalakas0308@gmail.com', 15, 'accepted', 'Chapter 1 accepted', 'Chapter 1 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 1, '2026-08-14 17:36:32'),
(39, 'evaluator:new:16:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 16, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 1 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=16', 0, '2026-08-14 21:50:29'),
(40, 'student:under_review:16', 9, 'student', 'kenlangmalakas0308@gmail.com', 16, 'under_review', 'Chapter 1 is under review', 'Chapter 1 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 21:51:10'),
(41, 'student:accepted:16', 9, 'student', 'kenlangmalakas0308@gmail.com', 16, 'accepted', 'Chapter 1 accepted', 'Chapter 1 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-14 21:51:29'),
(42, 'evaluator:new:17:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 17, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 1 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=17', 1, '2026-08-15 16:43:36'),
(43, 'evaluator:new:18:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 18, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 2 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=18', 1, '2026-08-15 16:43:40'),
(44, 'evaluator:new:19:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 19, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 3 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=19', 1, '2026-08-15 16:43:43'),
(45, 'student:under_review:17', 9, 'student', 'kenlangmalakas0308@gmail.com', 17, 'under_review', 'Chapter 1 is under review', 'Chapter 1 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-15 16:48:40'),
(46, 'student:accepted:17', 9, 'student', 'kenlangmalakas0308@gmail.com', 17, 'accepted', 'Chapter 1 accepted', 'Chapter 1 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-15 16:48:46'),
(47, 'student:under_review:18', 9, 'student', 'kenlangmalakas0308@gmail.com', 18, 'under_review', 'Chapter 2 is under review', 'Chapter 2 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-15 16:48:51'),
(48, 'student:accepted:18', 9, 'student', 'kenlangmalakas0308@gmail.com', 18, 'accepted', 'Chapter 2 accepted', 'Chapter 2 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-15 16:48:55'),
(49, 'student:under_review:19', 9, 'student', 'kenlangmalakas0308@gmail.com', 19, 'under_review', 'Chapter 3 is under review', 'Chapter 3 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-15 16:49:00'),
(50, 'student:accepted:19', 9, 'student', 'kenlangmalakas0308@gmail.com', 19, 'accepted', 'Chapter 3 accepted', 'Chapter 3 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-15 16:49:04'),
(51, 'evaluator:new:20:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 20, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 1 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=20', 0, '2026-08-15 22:49:29'),
(52, 'evaluator:new:21:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 21, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 2 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=21', 0, '2026-08-15 22:49:33'),
(53, 'evaluator:new:22:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 22, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 3 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=22', 0, '2026-08-15 22:49:38'),
(54, 'student:under_review:20', 9, 'student', 'kenlangmalakas0308@gmail.com', 20, 'under_review', 'Chapter 1 is under review', 'Chapter 1 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-15 22:50:04'),
(55, 'student:accepted:20', 9, 'student', 'kenlangmalakas0308@gmail.com', 20, 'accepted', 'Chapter 1 accepted', 'Chapter 1 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-15 22:50:09'),
(56, 'student:under_review:21', 9, 'student', 'kenlangmalakas0308@gmail.com', 21, 'under_review', 'Chapter 2 is under review', 'Chapter 2 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-15 22:50:13'),
(57, 'student:accepted:21', 9, 'student', 'kenlangmalakas0308@gmail.com', 21, 'accepted', 'Chapter 2 accepted', 'Chapter 2 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-15 22:50:18'),
(58, 'student:under_review:22', 9, 'student', 'kenlangmalakas0308@gmail.com', 22, 'under_review', 'Chapter 3 is under review', 'Chapter 3 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-15 22:50:21'),
(59, 'student:accepted:22', 9, 'student', 'kenlangmalakas0308@gmail.com', 22, 'accepted', 'Chapter 3 accepted', 'Chapter 3 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-15 22:50:27'),
(60, 'evaluator:new:23:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 23, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 1 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=23', 0, '2026-08-16 15:00:39'),
(61, 'evaluator:new:24:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 24, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 2 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=24', 0, '2026-08-16 15:00:43'),
(62, 'evaluator:new:25:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 25, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 3 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=25', 0, '2026-08-16 15:00:47'),
(63, 'student:under_review:23', 9, 'student', 'kenlangmalakas0308@gmail.com', 23, 'under_review', 'Chapter 1 is under review', 'Chapter 1 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-16 15:01:13'),
(64, 'student:accepted:23', 9, 'student', 'kenlangmalakas0308@gmail.com', 23, 'accepted', 'Chapter 1 accepted', 'Chapter 1 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-16 15:01:21'),
(65, 'student:under_review:24', 9, 'student', 'kenlangmalakas0308@gmail.com', 24, 'under_review', 'Chapter 2 is under review', 'Chapter 2 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-16 15:01:26'),
(66, 'student:accepted:24', 9, 'student', 'kenlangmalakas0308@gmail.com', 24, 'accepted', 'Chapter 2 accepted', 'Chapter 2 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 1, '2026-08-16 15:01:31'),
(67, 'student:under_review:25', 9, 'student', 'kenlangmalakas0308@gmail.com', 25, 'under_review', 'Chapter 3 is under review', 'Chapter 3 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-16 15:01:35'),
(68, 'student:accepted:25', 9, 'student', 'kenlangmalakas0308@gmail.com', 25, 'accepted', 'Chapter 3 accepted', 'Chapter 3 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 1, '2026-08-16 15:01:40'),
(69, 'evaluator:new:26:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 26, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 1 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=26', 0, '2026-08-16 21:42:19'),
(70, 'evaluator:new:27:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 27, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 2 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=27', 0, '2026-08-16 21:42:22'),
(71, 'evaluator:new:28:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 28, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 3 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=28', 0, '2026-08-16 21:42:25'),
(72, 'student:under_review:26', 9, 'student', 'kenlangmalakas0308@gmail.com', 26, 'under_review', 'Chapter 1 is under review', 'Chapter 1 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-16 21:42:53'),
(73, 'student:accepted:26', 9, 'student', 'kenlangmalakas0308@gmail.com', 26, 'accepted', 'Chapter 1 accepted', 'Chapter 1 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-16 21:43:06'),
(74, 'student:under_review:27', 9, 'student', 'kenlangmalakas0308@gmail.com', 27, 'under_review', 'Chapter 2 is under review', 'Chapter 2 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-16 21:43:11'),
(75, 'student:accepted:27', 9, 'student', 'kenlangmalakas0308@gmail.com', 27, 'accepted', 'Chapter 2 accepted', 'Chapter 2 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-16 21:43:15'),
(76, 'student:under_review:28', 9, 'student', 'kenlangmalakas0308@gmail.com', 28, 'under_review', 'Chapter 3 is under review', 'Chapter 3 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-16 21:43:22'),
(77, 'student:accepted:28', 9, 'student', 'kenlangmalakas0308@gmail.com', 28, 'accepted', 'Chapter 3 accepted', 'Chapter 3 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-16 21:43:25'),
(78, 'evaluator:new:29:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 29, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 1 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=29', 0, '2026-08-23 03:50:56'),
(79, 'evaluator:new:30:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 30, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 2 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=30', 0, '2026-08-23 04:07:26'),
(80, 'evaluator:new:31:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 31, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 3 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=31', 0, '2026-08-23 04:07:31'),
(81, 'student:under_review:29', 9, 'student', 'kenlangmalakas0308@gmail.com', 29, 'under_review', 'Chapter 1 is under review', 'Chapter 1 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-23 04:07:45'),
(82, 'student:accepted:29', 9, 'student', 'kenlangmalakas0308@gmail.com', 29, 'accepted', 'Chapter 1 accepted', 'Chapter 1 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-23 04:07:50'),
(83, 'student:under_review:30', 9, 'student', 'kenlangmalakas0308@gmail.com', 30, 'under_review', 'Chapter 2 is under review', 'Chapter 2 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-23 04:07:54'),
(84, 'student:accepted:30', 9, 'student', 'kenlangmalakas0308@gmail.com', 30, 'accepted', 'Chapter 2 accepted', 'Chapter 2 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-23 04:07:59'),
(85, 'student:under_review:31', 9, 'student', 'kenlangmalakas0308@gmail.com', 31, 'under_review', 'Chapter 3 is under review', 'Chapter 3 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-23 04:08:03'),
(86, 'student:accepted:31', 9, 'student', 'kenlangmalakas0308@gmail.com', 31, 'accepted', 'Chapter 3 accepted', 'Chapter 3 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-23 04:08:08'),
(87, 'evaluator:new:32:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 32, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 1 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=32', 0, '2026-08-28 15:33:05'),
(88, 'evaluator:new:33:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 33, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 2 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=33', 0, '2026-08-28 15:33:08'),
(89, 'evaluator:new:34:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 34, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 3 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=34', 0, '2026-08-28 15:33:11'),
(90, 'student:under_review:32', 9, 'student', 'kenlangmalakas0308@gmail.com', 32, 'under_review', 'Chapter 1 is under review', 'Chapter 1 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-28 15:33:26'),
(91, 'student:accepted:32', 9, 'student', 'kenlangmalakas0308@gmail.com', 32, 'accepted', 'Chapter 1 accepted', 'Chapter 1 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-28 15:33:31'),
(92, 'student:under_review:33', 9, 'student', 'kenlangmalakas0308@gmail.com', 33, 'under_review', 'Chapter 2 is under review', 'Chapter 2 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-28 15:33:39'),
(93, 'student:accepted:33', 9, 'student', 'kenlangmalakas0308@gmail.com', 33, 'accepted', 'Chapter 2 accepted', 'Chapter 2 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-28 15:33:46'),
(94, 'student:under_review:34', 9, 'student', 'kenlangmalakas0308@gmail.com', 34, 'under_review', 'Chapter 3 is under review', 'Chapter 3 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-28 15:33:49'),
(95, 'student:accepted:34', 9, 'student', 'kenlangmalakas0308@gmail.com', 34, 'accepted', 'Chapter 3 accepted', 'Chapter 3 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 1, '2026-08-28 15:33:53'),
(96, 'student:final_manuscript_approved:2', 9, 'student', 'kenlangmalakas0308@gmail.com', 2, 'final_manuscript_approved', 'Final Manuscript Approved', 'Your latest final manuscript for your research group has been approved for the next CRAD stage.', '/sms2_system/modules/student-portal/pages/final-manuscript.php', 0, '2026-08-28 15:55:07'),
(97, 'evaluator:new:35:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 35, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 1 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=35', 0, '2026-08-28 16:30:12'),
(98, 'evaluator:new:36:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 36, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 2 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=36', 0, '2026-08-28 16:30:15'),
(99, 'evaluator:new:37:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 37, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 3 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=37', 0, '2026-08-28 16:30:18'),
(100, 'student:under_review:35', 9, 'student', 'kenlangmalakas0308@gmail.com', 35, 'under_review', 'Chapter 1 is under review', 'Chapter 1 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-28 16:30:22'),
(101, 'student:accepted:35', 9, 'student', 'kenlangmalakas0308@gmail.com', 35, 'accepted', 'Chapter 1 accepted', 'Chapter 1 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-28 16:30:28'),
(102, 'student:under_review:36', 9, 'student', 'kenlangmalakas0308@gmail.com', 36, 'under_review', 'Chapter 2 is under review', 'Chapter 2 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-28 16:30:35'),
(103, 'student:accepted:36', 9, 'student', 'kenlangmalakas0308@gmail.com', 36, 'accepted', 'Chapter 2 accepted', 'Chapter 2 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-28 16:30:40'),
(104, 'student:under_review:37', 9, 'student', 'kenlangmalakas0308@gmail.com', 37, 'under_review', 'Chapter 3 is under review', 'Chapter 3 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-28 16:30:44'),
(105, 'student:accepted:37', 9, 'student', 'kenlangmalakas0308@gmail.com', 37, 'accepted', 'Chapter 3 accepted', 'Chapter 3 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-28 16:30:48'),
(106, 'student:final_manuscript_approved:3', 9, 'student', 'kenlangmalakas0308@gmail.com', 3, 'final_manuscript_approved', 'Final Manuscript Approved', 'Your latest final manuscript for your research group has been approved for the next CRAD stage.', '/sms2_system/modules/student-portal/pages/final-manuscript.php', 0, '2026-08-28 16:39:02'),
(107, 'evaluator:new:38:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 38, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 1 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=38', 0, '2026-08-31 05:30:20'),
(108, 'evaluator:new:39:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 39, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 2 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=39', 0, '2026-08-31 05:30:27'),
(109, 'evaluator:new:40:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 40, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 3 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=40', 0, '2026-08-31 05:30:33'),
(110, 'student:under_review:38', 9, 'student', 'kenlangmalakas0308@gmail.com', 38, 'under_review', 'Chapter 1 is under review', 'Chapter 1 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-31 05:31:36'),
(111, 'student:accepted:38', 9, 'student', 'kenlangmalakas0308@gmail.com', 38, 'accepted', 'Chapter 1 accepted', 'Chapter 1 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-31 05:32:02'),
(112, 'student:under_review:39', 9, 'student', 'kenlangmalakas0308@gmail.com', 39, 'under_review', 'Chapter 2 is under review', 'Chapter 2 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-31 05:32:07'),
(113, 'student:accepted:39', 9, 'student', 'kenlangmalakas0308@gmail.com', 39, 'accepted', 'Chapter 2 accepted', 'Chapter 2 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-31 05:32:16'),
(114, 'student:under_review:40', 9, 'student', 'kenlangmalakas0308@gmail.com', 40, 'under_review', 'Chapter 3 is under review', 'Chapter 3 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-31 05:32:23'),
(115, 'student:accepted:40', 9, 'student', 'kenlangmalakas0308@gmail.com', 40, 'accepted', 'Chapter 3 accepted', 'Chapter 3 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-31 05:32:32'),
(116, 'student:final_manuscript_approved:4', 9, 'student', 'kenlangmalakas0308@gmail.com', 4, 'final_manuscript_approved', 'Final Manuscript Approved', 'Your latest final manuscript for your research group has been approved for the next CRAD stage.', '/sms2_system/modules/student-portal/pages/final-manuscript.php', 0, '2026-08-31 06:32:22'),
(117, 'evaluator:new:41:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 41, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 1 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=41', 0, '2026-08-31 09:41:02'),
(118, 'evaluator:new:42:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 42, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 2 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=42', 0, '2026-08-31 09:41:14'),
(119, 'evaluator:new:43:u475', 475, 'grammarian', 'grammarian@bestlink.edu.ph', 43, 'new_submission', 'New Chapter Submission', 'Group 01 submitted Chapter 3 Version 1 for evaluation.', '/sms2_system/modules/faculty/pages/evaluation-scoring.php?id=43', 0, '2026-08-31 09:41:20'),
(120, 'student:under_review:41', 9, 'student', 's230000001@bestlink.edu.ph', 41, 'under_review', 'Chapter 1 is under review', 'Chapter 1 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-31 09:41:37'),
(121, 'student:accepted:41', 9, 'student', 's230000001@bestlink.edu.ph', 41, 'accepted', 'Chapter 1 accepted', 'Chapter 1 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-31 09:42:10'),
(122, 'student:under_review:42', 9, 'student', 's230000001@bestlink.edu.ph', 42, 'under_review', 'Chapter 2 is under review', 'Chapter 2 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-31 09:42:16'),
(123, 'student:accepted:42', 9, 'student', 's230000001@bestlink.edu.ph', 42, 'accepted', 'Chapter 2 accepted', 'Chapter 2 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-31 09:42:25'),
(124, 'student:under_review:43', 9, 'student', 's230000001@bestlink.edu.ph', 43, 'under_review', 'Chapter 3 is under review', 'Chapter 3 Version 1 is now under review.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-31 09:42:30'),
(125, 'student:accepted:43', 9, 'student', 's230000001@bestlink.edu.ph', 43, 'accepted', 'Chapter 3 accepted', 'Chapter 3 Version 1 is now Accepted.', '/sms2_system/modules/student-portal/pages/submission-status.php', 0, '2026-08-31 09:42:37');

-- --------------------------------------------------------

--
-- Table structure for table `chapter_submissions`
--

CREATE TABLE `chapter_submissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `research_group_id` int(10) UNSIGNED NOT NULL,
  `research_plan_id` int(10) UNSIGNED DEFAULT NULL,
  `chapter_number` tinyint(3) UNSIGNED NOT NULL,
  `version_number` int(10) UNSIGNED NOT NULL,
  `status` enum('Submitted','Under Review','Needs Revision','Accepted') NOT NULL DEFAULT 'Submitted',
  `submitted_by_user` int(10) UNSIGNED DEFAULT NULL,
  `submitted_by_name` varchar(150) NOT NULL DEFAULT '',
  `submitted_by_email` varchar(190) NOT NULL DEFAULT '',
  `submission_notes` text DEFAULT NULL,
  `original_name` varchar(255) NOT NULL DEFAULT '',
  `stored_subdir` varchar(180) NOT NULL DEFAULT '',
  `stored_name` varchar(120) NOT NULL DEFAULT '',
  `file_size` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `file_mime` varchar(120) NOT NULL DEFAULT '',
  `submission_token` varchar(64) NOT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `review_started_at` datetime DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chapter_submissions`
--

INSERT INTO `chapter_submissions` (`id`, `research_group_id`, `research_plan_id`, `chapter_number`, `version_number`, `status`, `submitted_by_user`, `submitted_by_name`, `submitted_by_email`, `submission_notes`, `original_name`, `stored_subdir`, `stored_name`, `file_size`, `file_mime`, `submission_token`, `submitted_at`, `review_started_at`, `reviewed_at`, `updated_at`) VALUES
(29, 61, 22, 1, 1, 'Accepted', 9, 'Student User', 'kenlangmalakas0308@gmail.com', '', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'student_chapters/u9', '2c66fe8dbd047f238219412c3277ea73.docx', 236268, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '60076d809fe68ad03006c78e3eb0cb7e6b713f49381c9be6f5819e132f909dbe', '2026-08-23 03:50:56', '2026-08-23 04:07:45', '2026-08-23 04:07:50', '2026-08-23 04:07:50'),
(30, 61, 22, 2, 1, 'Accepted', 9, 'Student User', 'kenlangmalakas0308@gmail.com', '', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'student_chapters/u9', '74ec1209c457db33489fc036d354b869.docx', 236268, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'e18982e452e6050dd4e2eb8de6f2c350a519c3c31ce46abb5d68cb1ab693bada', '2026-08-23 04:07:26', '2026-08-23 04:07:54', '2026-08-23 04:07:59', '2026-08-23 04:07:59'),
(31, 61, 22, 3, 1, 'Accepted', 9, 'Student User', 'kenlangmalakas0308@gmail.com', '', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'student_chapters/u9', 'a23d4492676bde4479582b935f03be6e.docx', 236268, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '795962d634b938f5a1aa88751bbea1f4771fa7a908a6fc42152dd0b4266b55f5', '2026-08-23 04:07:31', '2026-08-23 04:08:03', '2026-08-23 04:08:08', '2026-08-23 04:08:08'),
(32, 62, 23, 1, 1, 'Accepted', 9, 'Student User', 'kenlangmalakas0308@gmail.com', '', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'student_chapters/u9', '07e9795835e3e49872ba6d848b7685c6.docx', 302605, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '703e3403822246c74c451ce2f12b73566fb90b0a97d19bbea49d5d0fcb9a88cf', '2026-08-28 15:33:05', '2026-08-28 15:33:26', '2026-08-28 15:33:31', '2026-08-28 15:33:31'),
(33, 62, 23, 2, 1, 'Accepted', 9, 'Student User', 'kenlangmalakas0308@gmail.com', '', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'student_chapters/u9', '550d95ace2cd8302a68470dbf0afb644.docx', 302605, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'f5f836310ed2735baba97ca0205d463ed350c915fd5bb5fec4bf27b05473196e', '2026-08-28 15:33:08', '2026-08-28 15:33:39', '2026-08-28 15:33:46', '2026-08-28 15:33:46'),
(34, 62, 23, 3, 1, 'Accepted', 9, 'Student User', 'kenlangmalakas0308@gmail.com', '', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'student_chapters/u9', 'd9bd00055fdcadd5610bc655bb2b053e.docx', 302605, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '14f013839c0abbd2062835337e38c78f7e6795a443c8d58a887ecf5bfc85a6de', '2026-08-28 15:33:11', '2026-08-28 15:33:49', '2026-08-28 15:33:53', '2026-08-28 15:33:53'),
(35, 63, 24, 1, 1, 'Accepted', 9, 'Student User', 'kenlangmalakas0308@gmail.com', '', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'student_chapters/u9', '5413ca29228d7adc3edaba3ee078efdf.docx', 302605, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '7fb495b6b2dabca9d3610f199900ab3827277df616748b6e78f849c6c0c522e3', '2026-08-28 16:30:12', '2026-08-28 16:30:22', '2026-08-28 16:30:28', '2026-08-28 16:30:28'),
(36, 63, 24, 2, 1, 'Accepted', 9, 'Student User', 'kenlangmalakas0308@gmail.com', '', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'student_chapters/u9', '3e22a7612aa86bb04175936080d13626.docx', 302605, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'bc81662cd28d38c2fe5219572c8d4dd74570be581f7b123396b56a56a2e54c31', '2026-08-28 16:30:15', '2026-08-28 16:30:35', '2026-08-28 16:30:40', '2026-08-28 16:30:40'),
(37, 63, 24, 3, 1, 'Accepted', 9, 'Student User', 'kenlangmalakas0308@gmail.com', '', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'student_chapters/u9', 'd63ca46384609e00826df060745c3a10.docx', 302605, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '1091e650c3ac7f3e6ed48b4f6dab3290ab309e75293e5842455bb08943720b3c', '2026-08-28 16:30:18', '2026-08-28 16:30:44', '2026-08-28 16:30:48', '2026-08-28 16:30:48'),
(38, 64, 25, 1, 1, 'Accepted', 9, 'Student User', 'kenlangmalakas0308@gmail.com', '', 'OLIVEROS CV.pdf', 'student_chapters/u9', 'db565eabd531f233133d7398bd3affcf.pdf', 294354, 'application/pdf', 'eb04651e6e4d8811e7596935b11cc7517de971da398a0aaf6d94f089148b33c3', '2026-08-31 05:30:20', '2026-08-31 05:31:36', '2026-08-31 05:32:02', '2026-08-31 05:32:02'),
(39, 64, 25, 2, 1, 'Accepted', 9, 'Student User', 'kenlangmalakas0308@gmail.com', '', 'OLIVEROS CV.pdf', 'student_chapters/u9', '9df614acae2f05933d0af2a09abed945.pdf', 294354, 'application/pdf', '88b6cc5da546d5b6c56dcb13bb28a25ac7cea4f939ff5f0a64edffe902785150', '2026-08-31 05:30:27', '2026-08-31 05:32:07', '2026-08-31 05:32:16', '2026-08-31 05:32:16'),
(40, 64, 25, 3, 1, 'Accepted', 9, 'Student User', 'kenlangmalakas0308@gmail.com', '', 'OLIVEROS CV.pdf', 'student_chapters/u9', '6b9019e3663ac5e3ba2b152abf5c8857.pdf', 294354, 'application/pdf', '53c9b60411c62a659c6dee58ba830ab87477c4945af47482fadee00c85a8d8a5', '2026-08-31 05:30:32', '2026-08-31 05:32:23', '2026-08-31 05:32:32', '2026-08-31 05:32:32'),
(41, 65, 26, 1, 1, 'Accepted', 9, 'Student User', 's230000001@bestlink.edu.ph', '', 'OLIVEROS CV.pdf', 'student_chapters/u9', '4f694cb5d0416281cbf798b4f2512b24.pdf', 294354, 'application/pdf', '68dd9f53e015b14e08a5eb96c1a5c383f5e02f34082846d940097fec8b97cc20', '2026-08-31 09:41:02', '2026-08-31 09:41:37', '2026-08-31 09:42:10', '2026-08-31 09:42:10'),
(42, 65, 26, 2, 1, 'Accepted', 9, 'Student User', 's230000001@bestlink.edu.ph', '', 'OLIVEROS CV.pdf', 'student_chapters/u9', 'b54dde9265a89caa8fe66d1c22cdbd62.pdf', 294354, 'application/pdf', '30cd83bc72425ee46feb4a5f1bfb0fd0d35db6061321fe3e590b3c296486416d', '2026-08-31 09:41:14', '2026-08-31 09:42:16', '2026-08-31 09:42:25', '2026-08-31 09:42:25'),
(43, 65, 26, 3, 1, 'Accepted', 9, 'Student User', 's230000001@bestlink.edu.ph', '', 'OLIVEROS CV.pdf', 'student_chapters/u9', 'c88aaf9cd7d1786adef383829d96f19d.pdf', 294354, 'application/pdf', '8caa0800c83f9cb0d4d5c6ff1739d304e1b31b7863feff6dcc779c651fded0ae', '2026-08-31 09:41:20', '2026-08-31 09:42:30', '2026-08-31 09:42:37', '2026-08-31 09:42:37');

-- --------------------------------------------------------

--
-- Table structure for table `chapter_submission_history`
--

CREATE TABLE `chapter_submission_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `submission_id` int(10) UNSIGNED NOT NULL,
  `research_group_id` int(10) UNSIGNED NOT NULL,
  `chapter_number` tinyint(3) UNSIGNED NOT NULL,
  `version_number` int(10) UNSIGNED NOT NULL,
  `status` varchar(40) NOT NULL,
  `event_type` varchar(60) NOT NULL,
  `actor_user_id` int(10) UNSIGNED DEFAULT NULL,
  `actor_name` varchar(150) NOT NULL DEFAULT '',
  `actor_role` varchar(60) NOT NULL DEFAULT '',
  `detail` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chapter_submission_history`
--

INSERT INTO `chapter_submission_history` (`id`, `submission_id`, `research_group_id`, `chapter_number`, `version_number`, `status`, `event_type`, `actor_user_id`, `actor_name`, `actor_role`, `detail`, `created_at`) VALUES
(78, 29, 61, 1, 1, 'Submitted', 'submitted', 9, 'Student User', 'student', '', '2026-08-23 03:50:56'),
(79, 30, 61, 2, 1, 'Submitted', 'submitted', 9, 'Student User', 'student', '', '2026-08-23 04:07:26'),
(80, 31, 61, 3, 1, 'Submitted', 'submitted', 9, 'Student User', 'student', '', '2026-08-23 04:07:31'),
(81, 29, 61, 1, 1, 'Under Review', 'review_started', 475, 'Grammarian', 'grammarian', 'Grammarian started review.', '2026-08-23 04:07:45'),
(82, 29, 61, 1, 1, 'Accepted', 'evaluated', 475, 'Grammarian', 'grammarian', 'APPROVED', '2026-08-23 04:07:50'),
(83, 30, 61, 2, 1, 'Under Review', 'review_started', 475, 'Grammarian', 'grammarian', 'Grammarian started review.', '2026-08-23 04:07:54'),
(84, 30, 61, 2, 1, 'Accepted', 'evaluated', 475, 'Grammarian', 'grammarian', 'APPROVED', '2026-08-23 04:07:59'),
(85, 31, 61, 3, 1, 'Under Review', 'review_started', 475, 'Grammarian', 'grammarian', 'Grammarian started review.', '2026-08-23 04:08:03'),
(86, 31, 61, 3, 1, 'Accepted', 'evaluated', 475, 'Grammarian', 'grammarian', 'APPROVED', '2026-08-23 04:08:08'),
(87, 32, 62, 1, 1, 'Submitted', 'submitted', 9, 'Student User', 'student', '', '2026-08-28 15:33:05'),
(88, 33, 62, 2, 1, 'Submitted', 'submitted', 9, 'Student User', 'student', '', '2026-08-28 15:33:08'),
(89, 34, 62, 3, 1, 'Submitted', 'submitted', 9, 'Student User', 'student', '', '2026-08-28 15:33:11'),
(90, 32, 62, 1, 1, 'Under Review', 'review_started', 475, 'Grammarian', 'grammarian', 'Grammarian started review.', '2026-08-28 15:33:26'),
(91, 32, 62, 1, 1, 'Accepted', 'evaluated', 475, 'Grammarian', 'grammarian', 'APPROVED', '2026-08-28 15:33:31'),
(92, 33, 62, 2, 1, 'Under Review', 'review_started', 475, 'Grammarian', 'grammarian', 'Grammarian started review.', '2026-08-28 15:33:39'),
(93, 33, 62, 2, 1, 'Accepted', 'evaluated', 475, 'Grammarian', 'grammarian', 'APPROVED', '2026-08-28 15:33:46'),
(94, 34, 62, 3, 1, 'Under Review', 'review_started', 475, 'Grammarian', 'grammarian', 'Grammarian started review.', '2026-08-28 15:33:49'),
(95, 34, 62, 3, 1, 'Accepted', 'evaluated', 475, 'Grammarian', 'grammarian', 'APPROVED', '2026-08-28 15:33:53'),
(96, 35, 63, 1, 1, 'Submitted', 'submitted', 9, 'Student User', 'student', '', '2026-08-28 16:30:12'),
(97, 36, 63, 2, 1, 'Submitted', 'submitted', 9, 'Student User', 'student', '', '2026-08-28 16:30:15'),
(98, 37, 63, 3, 1, 'Submitted', 'submitted', 9, 'Student User', 'student', '', '2026-08-28 16:30:18'),
(99, 35, 63, 1, 1, 'Under Review', 'review_started', 475, 'Grammarian', 'grammarian', 'Grammarian started review.', '2026-08-28 16:30:22'),
(100, 35, 63, 1, 1, 'Accepted', 'evaluated', 475, 'Grammarian', 'grammarian', 'APPROVED', '2026-08-28 16:30:28'),
(101, 36, 63, 2, 1, 'Under Review', 'review_started', 475, 'Grammarian', 'grammarian', 'Grammarian started review.', '2026-08-28 16:30:35'),
(102, 36, 63, 2, 1, 'Accepted', 'evaluated', 475, 'Grammarian', 'grammarian', 'APPROVED', '2026-08-28 16:30:40'),
(103, 37, 63, 3, 1, 'Under Review', 'review_started', 475, 'Grammarian', 'grammarian', 'Grammarian started review.', '2026-08-28 16:30:44'),
(104, 37, 63, 3, 1, 'Accepted', 'evaluated', 475, 'Grammarian', 'grammarian', 'APPROVED', '2026-08-28 16:30:48'),
(105, 38, 64, 1, 1, 'Submitted', 'submitted', 9, 'Student User', 'student', '', '2026-08-31 05:30:20'),
(106, 39, 64, 2, 1, 'Submitted', 'submitted', 9, 'Student User', 'student', '', '2026-08-31 05:30:27'),
(107, 40, 64, 3, 1, 'Submitted', 'submitted', 9, 'Student User', 'student', '', '2026-08-31 05:30:32'),
(108, 38, 64, 1, 1, 'Under Review', 'review_started', 475, 'Grammarian', 'grammarian', 'Grammarian started review.', '2026-08-31 05:31:36'),
(109, 38, 64, 1, 1, 'Accepted', 'evaluated', 475, 'Grammarian', 'grammarian', 'APPROVED', '2026-08-31 05:32:02'),
(110, 39, 64, 2, 1, 'Under Review', 'review_started', 475, 'Grammarian', 'grammarian', 'Grammarian started review.', '2026-08-31 05:32:07'),
(111, 39, 64, 2, 1, 'Accepted', 'evaluated', 475, 'Grammarian', 'grammarian', 'APPROVED', '2026-08-31 05:32:16'),
(112, 40, 64, 3, 1, 'Under Review', 'review_started', 475, 'Grammarian', 'grammarian', 'Grammarian started review.', '2026-08-31 05:32:23'),
(113, 40, 64, 3, 1, 'Accepted', 'evaluated', 475, 'Grammarian', 'grammarian', 'APPROVED', '2026-08-31 05:32:32'),
(114, 41, 65, 1, 1, 'Submitted', 'submitted', 9, 'Student User', 'student', '', '2026-08-31 09:41:02'),
(115, 42, 65, 2, 1, 'Submitted', 'submitted', 9, 'Student User', 'student', '', '2026-08-31 09:41:14'),
(116, 43, 65, 3, 1, 'Submitted', 'submitted', 9, 'Student User', 'student', '', '2026-08-31 09:41:20'),
(117, 41, 65, 1, 1, 'Under Review', 'review_started', 475, 'Grammarian', 'grammarian', 'Grammarian started review.', '2026-08-31 09:41:37'),
(118, 41, 65, 1, 1, 'Accepted', 'evaluated', 475, 'Grammarian', 'grammarian', 'APPROVED', '2026-08-31 09:42:10'),
(119, 42, 65, 2, 1, 'Under Review', 'review_started', 475, 'Grammarian', 'grammarian', 'Grammarian started review.', '2026-08-31 09:42:16'),
(120, 42, 65, 2, 1, 'Accepted', 'evaluated', 475, 'Grammarian', 'grammarian', 'APPROVED', '2026-08-31 09:42:25'),
(121, 43, 65, 3, 1, 'Under Review', 'review_started', 475, 'Grammarian', 'grammarian', 'Grammarian started review.', '2026-08-31 09:42:30'),
(122, 43, 65, 3, 1, 'Accepted', 'evaluated', 475, 'Grammarian', 'grammarian', 'APPROVED', '2026-08-31 09:42:37');

-- --------------------------------------------------------

--
-- Table structure for table `final_defense_evaluations`
--

CREATE TABLE `final_defense_evaluations` (
  `id` int(10) UNSIGNED NOT NULL,
  `defense_schedule_id` int(10) UNSIGNED NOT NULL,
  `research_group_id` int(10) UNSIGNED DEFAULT NULL,
  `panel_user_id` int(10) UNSIGNED NOT NULL,
  `panel_name` varchar(150) NOT NULL DEFAULT '',
  `content_score` decimal(5,2) NOT NULL,
  `methodology_score` decimal(5,2) NOT NULL,
  `references_score` decimal(5,2) NOT NULL,
  `format_score` decimal(5,2) NOT NULL,
  `remarks` text DEFAULT NULL,
  `result` enum('APPROVED','APPROVED WITH REVISION','FAILED') NOT NULL,
  `overall_score` decimal(5,2) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Submitted',
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `final_defense_evaluations`
--

INSERT INTO `final_defense_evaluations` (`id`, `defense_schedule_id`, `research_group_id`, `panel_user_id`, `panel_name`, `content_score`, `methodology_score`, `references_score`, `format_score`, `remarks`, `result`, `overall_score`, `status`, `submitted_at`, `created_at`) VALUES
(1, 32, 61, 491, 'Dr. Jobert Valentino', 90.00, 90.00, 90.00, 90.00, 'Final Defense evaluation completed.', 'APPROVED', 90.00, 'Submitted', '2026-08-28 08:31:36', '2026-08-28 08:31:36'),
(2, 32, 61, 492, 'Dr. Jonathan Estrada', 90.00, 88.00, 92.00, 90.00, 'Live demo evaluation test', 'APPROVED', 90.00, 'Submitted', '2026-08-28 13:31:08', '2026-08-28 13:31:08'),
(3, 32, 61, 493, 'Dr. Michelle Guevarra', 99.00, 99.00, 99.00, 99.00, '99', 'APPROVED', 99.00, 'Submitted', '2026-08-28 13:34:59', '2026-08-28 13:34:59'),
(4, 38, 62, 491, 'Dr. Jobert Valentino', 100.00, 100.00, 100.00, 100.00, '', 'APPROVED', 100.00, 'Submitted', '2026-08-28 15:53:12', '2026-08-28 15:53:12'),
(5, 38, 62, 492, 'Dr. Jonathan Estrada', 100.00, 100.00, 100.00, 100.00, '', 'APPROVED', 100.00, 'Submitted', '2026-08-28 15:53:31', '2026-08-28 15:53:31'),
(6, 38, 62, 493, 'Dr. Michelle Guevarra', 100.00, 100.00, 100.00, 100.00, '', 'APPROVED', 100.00, 'Submitted', '2026-08-28 15:53:47', '2026-08-28 15:53:47'),
(7, 44, 63, 491, 'Dr. Jobert Valentino', 99.00, 99.00, 99.00, 99.00, '', 'APPROVED', 99.00, 'Submitted', '2026-08-28 16:37:25', '2026-08-28 16:37:25'),
(8, 44, 63, 492, 'Dr. Jonathan Estrada', 99.00, 99.00, 99.00, 99.00, '', 'APPROVED', 99.00, 'Submitted', '2026-08-28 16:38:03', '2026-08-28 16:38:03'),
(9, 44, 63, 493, 'Dr. Michelle Guevarra', 99.00, 99.00, 99.00, 99.00, '', 'APPROVED', 99.00, 'Submitted', '2026-08-28 16:38:21', '2026-08-28 16:38:21'),
(10, 50, 64, 491, 'Dr. Jobert Valentino', 99.99, 100.00, 100.00, 100.00, '', 'APPROVED', 100.00, 'Submitted', '2026-08-31 06:28:01', '2026-08-31 06:28:01'),
(11, 50, 64, 492, 'Dr. Jonathan Estrada', 100.00, 100.00, 100.00, 100.00, '', 'APPROVED', 100.00, 'Submitted', '2026-08-31 06:28:40', '2026-08-31 06:28:40'),
(12, 50, 64, 493, 'Dr. Michelle Guevarra', 99.99, 100.00, 100.00, 100.00, '100', 'APPROVED', 100.00, 'Submitted', '2026-08-31 06:31:46', '2026-08-31 06:31:46');

-- --------------------------------------------------------

--
-- Table structure for table `final_defense_recommendations`
--

CREATE TABLE `final_defense_recommendations` (
  `id` int(10) UNSIGNED NOT NULL,
  `research_group_id` int(10) UNSIGNED NOT NULL,
  `group_number` varchar(40) NOT NULL DEFAULT '',
  `adviser_user_id` int(10) UNSIGNED DEFAULT NULL,
  `adviser_name` varchar(150) NOT NULL DEFAULT '',
  `status` enum('Not Ready','Recommended') NOT NULL DEFAULT 'Not Ready',
  `remarks` text DEFAULT NULL,
  `recommended_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `final_defense_recommendations`
--

INSERT INTO `final_defense_recommendations` (`id`, `research_group_id`, `group_number`, `adviser_user_id`, `adviser_name`, `status`, `remarks`, `recommended_at`, `created_at`, `updated_at`) VALUES
(1, 61, 'RG-2026-001', 54, 'Dr. Roberto M. Santos', 'Recommended', '', '2026-08-28 15:41:00', '2026-08-28 08:00:15', '2026-08-28 15:41:00'),
(2, 62, 'RG-2026-001', 54, 'Dr. Roberto M. Santos', 'Recommended', '', '2026-08-28 15:45:21', '2026-08-28 15:44:31', '2026-08-28 15:45:21'),
(4, 63, 'RG-2026-001', 54, 'Dr. Roberto M. Santos', 'Recommended', '', '2026-08-28 16:34:27', '2026-08-28 16:34:27', '2026-08-28 16:34:27'),
(5, 64, 'RG-2026-001', 54, 'Dr. Roberto M. Santos', 'Recommended', '', '2026-08-31 06:15:48', '2026-08-31 06:15:48', '2026-08-31 06:15:48');

-- --------------------------------------------------------

--
-- Table structure for table `final_manuscript_approvals`
--

CREATE TABLE `final_manuscript_approvals` (
  `id` int(10) UNSIGNED NOT NULL,
  `research_group_id` int(10) UNSIGNED NOT NULL,
  `defense_schedule_id` int(10) UNSIGNED DEFAULT NULL,
  `approved_by_user` int(10) UNSIGNED DEFAULT NULL,
  `approved_by_name` varchar(150) NOT NULL DEFAULT '',
  `status` enum('Pending','Approved','Returned') NOT NULL DEFAULT 'Pending',
  `remarks` text DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `final_manuscript_approvals`
--

INSERT INTO `final_manuscript_approvals` (`id`, `research_group_id`, `defense_schedule_id`, `approved_by_user`, `approved_by_name`, `status`, `remarks`, `approved_at`, `created_at`, `updated_at`) VALUES
(1, 61, 32, 3, '', 'Approved', 'Final Defense evaluations completed and approved.', '2026-08-28 13:41:26', '2026-08-28 13:41:26', '2026-08-28 13:41:26'),
(2, 62, 38, 3, '', 'Approved', 'done', '2026-08-28 15:55:07', '2026-08-28 15:55:07', '2026-08-28 15:55:07'),
(3, 63, 44, 3, '', 'Approved', 'done', '2026-08-28 16:39:02', '2026-08-28 16:39:02', '2026-08-28 16:39:02'),
(4, 64, 50, 3, '', 'Approved', '100', '2026-08-31 06:32:22', '2026-08-31 06:32:22', '2026-08-31 06:32:22');

-- --------------------------------------------------------

--
-- Table structure for table `grant_applications`
--

CREATE TABLE `grant_applications` (
  `id` int(10) UNSIGNED NOT NULL,
  `proposal_reference` varchar(30) DEFAULT NULL COMMENT 'Stable proposal ID e.g. GR-2026-001',
  `current_version` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Active proposal document version',
  `grant_opportunity_id` int(10) UNSIGNED NOT NULL COMMENT 'FK → grant_opportunities.id',
  `research_group_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'FK → research_groups.id (nullable for non-capstone applicants)',
  `group_number` varchar(30) DEFAULT NULL,
  `research_title` varchar(500) DEFAULT NULL,
  `applicant_name` varchar(200) NOT NULL DEFAULT '',
  `college_dept` varchar(200) DEFAULT NULL COMMENT 'Academic college / department of the lead proponent',
  `requested_budget` decimal(14,2) DEFAULT NULL COMMENT 'Budget requested by the proponent; must not exceed grant max_funding_cap',
  `approved_budget` decimal(14,2) DEFAULT NULL,
  `abstract` text DEFAULT NULL COMMENT 'Executive abstract of the research proposal',
  `objectives` text DEFAULT NULL COMMENT 'Research objectives',
  `proposal_pdf` varchar(255) DEFAULT NULL COMMENT 'Stored filename of the uploaded proposal PDF/DOC under storage/uploads/grant_proposals/',
  `proposal_pdf_original` varchar(300) DEFAULT NULL COMMENT 'Original filename of the uploaded proposal document',
  `supporting_docs` varchar(255) DEFAULT NULL COMMENT 'Stored filename of optional supporting documents',
  `supporting_docs_original` varchar(300) DEFAULT NULL COMMENT 'Original filename of optional supporting documents',
  `ethics_doc` varchar(255) DEFAULT NULL COMMENT 'Stored filename of optional ethics clearance document',
  `ethics_doc_original` varchar(300) DEFAULT NULL COMMENT 'Original filename of optional ethics clearance document',
  `applicant_user_id` int(10) UNSIGNED DEFAULT NULL,
  `application_notes` text DEFAULT NULL,
  `status` enum('Submitted','Under Review','Approved','Approved & Funded','Final Output Submitted','OUTPUT_VERIFIED','Archived','Denied','Withdrawn','Rejected','Revision Required','Resubmitted') NOT NULL DEFAULT 'Submitted',
  `submission_token` varchar(64) DEFAULT NULL COMMENT 'One-time token for duplicate-submission prevention',
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grant_applications`
--

INSERT INTO `grant_applications` (`id`, `proposal_reference`, `current_version`, `grant_opportunity_id`, `research_group_id`, `group_number`, `research_title`, `applicant_name`, `college_dept`, `requested_budget`, `approved_budget`, `abstract`, `objectives`, `proposal_pdf`, `proposal_pdf_original`, `supporting_docs`, `supporting_docs_original`, `ethics_doc`, `ethics_doc_original`, `applicant_user_id`, `application_notes`, `status`, `submission_token`, `submitted_at`, `updated_at`) VALUES
(5, 'GR-2026-001', 1, 1, NULL, NULL, 'ai analysis', 'Student User', 'bsit', 15000.00, 15000.00, 'dsda', 'sdas', '6d7a59295ee3d7cd325d51cdb880d11c.pdf', 'OLIVEROS CV.pdf', '2e8ab8d2fe5b210a8ebd034054e70bc1.pdf', 'OLIVEROS CV.pdf', '2eb694566c7d8741b6189a97af16dfd8.pdf', 'OLIVEROS CV.pdf', 9, NULL, 'OUTPUT_VERIFIED', 'bda47b1475d85cff0395bf67067bb2eb', '2026-08-31 14:11:48', '2026-08-31 16:40:51');

-- --------------------------------------------------------

--
-- Table structure for table `grant_document_repository`
--

CREATE TABLE `grant_document_repository` (
  `id` int(10) UNSIGNED NOT NULL,
  `grant_application_id` int(10) UNSIGNED NOT NULL,
  `archive_reference` varchar(40) NOT NULL DEFAULT '',
  `status` enum('ARCHIVED') NOT NULL DEFAULT 'ARCHIVED',
  `item_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `archived_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `archived_by_name` varchar(120) DEFAULT NULL,
  `archived_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grant_document_repository_items`
--

CREATE TABLE `grant_document_repository_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `repository_id` int(10) UNSIGNED NOT NULL,
  `grant_application_id` int(10) UNSIGNED NOT NULL,
  `category` varchar(40) NOT NULL,
  `item_label` varchar(255) NOT NULL DEFAULT '',
  `item_type` enum('file','record') NOT NULL DEFAULT 'record',
  `file_path` varchar(255) DEFAULT NULL,
  `file_original` varchar(255) DEFAULT NULL,
  `download_url` varchar(500) DEFAULT NULL,
  `summary_text` text DEFAULT NULL,
  `metadata_json` text DEFAULT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grant_final_output_submissions`
--

CREATE TABLE `grant_final_output_submissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `grant_application_id` int(10) UNSIGNED NOT NULL,
  `version_number` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `final_research_title` varchar(500) NOT NULL DEFAULT '',
  `authors` varchar(500) NOT NULL DEFAULT '',
  `abstract` text DEFAULT NULL,
  `publication_type` enum('Journal','Conference','Book Chapter','Repository','Other') NOT NULL DEFAULT 'Journal',
  `journal_conference` varchar(255) NOT NULL DEFAULT '',
  `doi` varchar(120) NOT NULL DEFAULT '',
  `publication_url` varchar(500) NOT NULL DEFAULT '',
  `ip_information` text DEFAULT NULL,
  `copyright_info` text DEFAULT NULL,
  `patent_info` text DEFAULT NULL,
  `other_ip_info` text DEFAULT NULL,
  `final_pdf_path` varchar(255) DEFAULT NULL,
  `final_pdf_original` varchar(255) DEFAULT NULL,
  `supporting_files_json` text DEFAULT NULL,
  `status` enum('FINAL_OUTPUT_SUBMITTED','RETURNED_FOR_CORRECTION','VERIFIED') NOT NULL DEFAULT 'FINAL_OUTPUT_SUBMITTED',
  `return_reason` text DEFAULT NULL,
  `verification_notes` text DEFAULT NULL,
  `submitted_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `submitted_by_name` varchar(120) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `reviewed_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `reviewed_by_name` varchar(120) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grant_final_output_submissions`
--

INSERT INTO `grant_final_output_submissions` (`id`, `grant_application_id`, `version_number`, `final_research_title`, `authors`, `abstract`, `publication_type`, `journal_conference`, `doi`, `publication_url`, `ip_information`, `copyright_info`, `patent_info`, `other_ip_info`, `final_pdf_path`, `final_pdf_original`, `supporting_files_json`, `status`, `return_reason`, `verification_notes`, `submitted_by_user_id`, `submitted_by_name`, `submitted_at`, `reviewed_by_user_id`, `reviewed_by_name`, `reviewed_at`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 'ai analysis', 'Student User', 'adasdsada', 'Journal', 'asdsa', '', 'http://localhost/sms2_system/modules/crad/pages/publications-ip.php', 'sadas', NULL, NULL, NULL, 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/grant_final_output/20fbebfce55289306d6bfbb63c4e3777.pdf', 'OLIVEROS CV.pdf', '[{\"path\":\"C:\\\\xampp\\\\htdocs\\\\sms2_system\\/storage\\/uploads\\/grant_final_output_supporting\\/39c7dccbc6b58439374010ce48077be1.pdf\",\"original_name\":\"Diaz CV.pdf_20260813_105004_0000.pdf\",\"stored_name\":\"39c7dccbc6b58439374010ce48077be1.pdf\"}]', '', NULL, NULL, 9, 'User', '2026-08-31 16:38:45', 3, 'User', '2026-08-31 16:40:51', '2026-08-31 16:38:45', '2026-08-31 16:40:51');

-- --------------------------------------------------------

--
-- Table structure for table `grant_funded_progress_evidence`
--

CREATE TABLE `grant_funded_progress_evidence` (
  `id` int(10) UNSIGNED NOT NULL,
  `grant_application_id` int(10) UNSIGNED NOT NULL,
  `milestone_id` int(10) UNSIGNED DEFAULT NULL,
  `evidence_title` varchar(200) NOT NULL DEFAULT '',
  `notes` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_original` varchar(255) DEFAULT NULL,
  `submitted_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `submitted_by_name` varchar(120) DEFAULT NULL,
  `status` enum('Submitted','Acknowledged') NOT NULL DEFAULT 'Submitted',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grant_funded_progress_evidence`
--

INSERT INTO `grant_funded_progress_evidence` (`id`, `grant_application_id`, `milestone_id`, `evidence_title`, `notes`, `file_path`, `file_original`, `submitted_by_user_id`, `submitted_by_name`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, 2, 'done', NULL, '08d2da99a1015997444d899fa46a0d1f.pdf', 'OLIVEROS CV.pdf', 9, 'User', 'Submitted', '2026-08-31 16:04:10', '2026-08-31 16:04:10'),
(2, 5, 3, 'done', NULL, '90d52a0e2d826deae5c328f9eac04e51.pdf', 'OLIVEROS CV.pdf', 9, 'User', 'Submitted', '2026-08-31 16:08:47', '2026-08-31 16:08:47'),
(3, 5, 4, 'week 2', NULL, '32d641db454454bc0a71d67ecae8d3d0.pdf', 'OLIVEROS CV.pdf', 9, 'User', 'Submitted', '2026-08-31 16:08:59', '2026-08-31 16:08:59'),
(4, 5, 5, 'done', NULL, 'ebc40321cf8207581e57069fbc7b5b25.pdf', 'OLIVEROS CV.pdf', 9, 'User', 'Submitted', '2026-08-31 16:09:09', '2026-08-31 16:09:09');

-- --------------------------------------------------------

--
-- Table structure for table `grant_funded_project_milestones`
--

CREATE TABLE `grant_funded_project_milestones` (
  `id` int(10) UNSIGNED NOT NULL,
  `grant_application_id` int(10) UNSIGNED NOT NULL,
  `milestone_order` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `milestone_name` varchar(120) NOT NULL,
  `due_date` date DEFAULT NULL,
  `completion_pct` decimal(5,2) NOT NULL DEFAULT 0.00,
  `status` enum('Pending','In Progress','Completed') NOT NULL DEFAULT 'Pending',
  `supporting_doc` varchar(255) DEFAULT NULL,
  `supporting_doc_original` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `updated_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `updated_by_name` varchar(120) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grant_funded_project_milestones`
--

INSERT INTO `grant_funded_project_milestones` (`id`, `grant_application_id`, `milestone_order`, `milestone_name`, `due_date`, `completion_pct`, `status`, `supporting_doc`, `supporting_doc_original`, `remarks`, `updated_by_user_id`, `updated_by_name`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 'Project Start', NULL, 100.00, 'Completed', NULL, NULL, NULL, NULL, NULL, '2026-08-31 14:41:26', '2026-08-31 14:41:26'),
(2, 5, 2, 'Data Gathering', NULL, 100.00, 'Completed', '2a7227d63dad821917cea9db255ebc76.pdf', 'OLIVEROS CV.pdf', 'sdasda', 3, 'User', '2026-08-31 14:41:26', '2026-08-31 16:08:16'),
(3, 5, 3, 'Analysis', '2026-08-31', 100.00, 'Completed', 'e33fc0e55fc37f7549946bd53fce56a3.pdf', 'OLIVEROS CV.pdf', NULL, 3, 'User', '2026-08-31 14:41:26', '2026-08-31 16:09:47'),
(4, 5, 4, 'Final Report', NULL, 100.00, 'Completed', '0e425eef515f88b4aca1c9ffdf95c89d.pdf', 'OLIVEROS CV.pdf', NULL, 3, 'User', '2026-08-31 14:41:26', '2026-08-31 16:10:01'),
(5, 5, 5, 'Publication', NULL, 100.00, 'Completed', 'd73f169b079774a38ea2f4b4a07f8047.pdf', 'OLIVEROS CV.pdf', NULL, 3, 'User', '2026-08-31 14:41:26', '2026-08-31 16:10:32');

-- --------------------------------------------------------

--
-- Table structure for table `grant_funding_disbursements`
--

CREATE TABLE `grant_funding_disbursements` (
  `id` int(10) UNSIGNED NOT NULL,
  `grant_application_id` int(10) UNSIGNED NOT NULL,
  `tranche_number` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `tranche_label` varchar(80) NOT NULL DEFAULT '',
  `approved_budget` decimal(14,2) NOT NULL DEFAULT 0.00,
  `amount_released` decimal(14,2) NOT NULL DEFAULT 0.00,
  `release_date` date DEFAULT NULL,
  `reference_number` varchar(80) DEFAULT NULL,
  `status` enum('Pending','Released','Cancelled') NOT NULL DEFAULT 'Pending',
  `released_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `released_by_name` varchar(120) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grant_funding_disbursements`
--

INSERT INTO `grant_funding_disbursements` (`id`, `grant_application_id`, `tranche_number`, `tranche_label`, `approved_budget`, `amount_released`, `release_date`, `reference_number`, `status`, `released_by_user_id`, `released_by_name`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 'Tranche 1', 15000.00, 7500.00, '2026-08-31', 'DISB-GR-2026-001-T1', 'Released', 3, 'User', NULL, '2026-08-31 14:35:24', '2026-08-31 14:57:57'),
(2, 5, 2, 'Tranche 2', 15000.00, 7500.00, '2026-08-31', 'DISB-GR-2026-001-T2', 'Released', 3, 'User', NULL, '2026-08-31 14:35:24', '2026-08-31 14:59:27');

-- --------------------------------------------------------

--
-- Table structure for table `grant_opportunities`
--

CREATE TABLE `grant_opportunities` (
  `id` int(10) UNSIGNED NOT NULL,
  `funding_title` varchar(300) NOT NULL,
  `max_funding_cap` decimal(14,2) NOT NULL DEFAULT 0.00,
  `application_deadline` date NOT NULL,
  `eligibility` varchar(100) NOT NULL DEFAULT 'Open',
  `college_program` varchar(200) DEFAULT NULL COMMENT 'Populated when eligibility = Specific College/Program',
  `status` enum('Open for Application','Closed','Expired') NOT NULL DEFAULT 'Open for Application',
  `created_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `created_by_name` varchar(150) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grant_opportunities`
--

INSERT INTO `grant_opportunities` (`id`, `funding_title`, `max_funding_cap`, `application_deadline`, `eligibility`, `college_program`, `status`, `created_by_user_id`, `created_by_name`, `created_at`, `updated_at`) VALUES
(1, 'BESTLINK Faculty Seed Grant Call 2026', 350000.00, '2026-09-01', 'Faculty & Student', NULL, 'Open for Application', 3, 'CRAD Officer', '2026-08-31 06:44:48', '2026-08-31 06:44:48');

-- --------------------------------------------------------

--
-- Table structure for table `grant_proposal_approval_steps`
--

CREATE TABLE `grant_proposal_approval_steps` (
  `id` int(10) UNSIGNED NOT NULL,
  `workflow_id` int(10) UNSIGNED NOT NULL,
  `grant_application_id` int(10) UNSIGNED NOT NULL,
  `step_key` varchar(40) NOT NULL,
  `step_order` tinyint(3) UNSIGNED NOT NULL,
  `step_label` varchar(80) NOT NULL,
  `approver_role_key` varchar(40) NOT NULL,
  `status` enum('Queued','Pending','Approved','Returned') NOT NULL DEFAULT 'Queued',
  `approver_user_id` int(10) UNSIGNED DEFAULT NULL,
  `approver_name` varchar(150) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `signature_data` mediumtext DEFAULT NULL,
  `acted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grant_proposal_approval_steps`
--

INSERT INTO `grant_proposal_approval_steps` (`id`, `workflow_id`, `grant_application_id`, `step_key`, `step_order`, `step_label`, `approver_role_key`, `status`, `approver_user_id`, `approver_name`, `remarks`, `signature_data`, `acted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'adviser', 1, 'Academic Adviser', 'adviser', 'Approved', 54, 'User', NULL, 'data:image/png;base64,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', '2026-08-31 11:03:47', '2026-08-31 08:47:21', '2026-08-31 11:03:47'),
(2, 1, 2, 'department_chair', 2, 'Dept. Chair', 'research_coordinator', 'Approved', 990, 'User', NULL, 'data:image/png;base64,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sWqfl+spRujK11PbIiACIiAC2yewhmFuv1J1LdENzE0tm/pr2bSu7VC9REAEREAEzk9Ahnl+ZjpDBERABESggwRkmA3vdFVfBERABERgOwRkmNvhrFJEQAREQAQaTkCG2fAOVPXrTEB1EwERaBMBGWabelNtEQEREAERqIyADLMytMpYBESgzgRUNxE4LwEZ5nmJ6XgREAEREIFOEpBhdrLb1WgREAERqDOBetZNhlnPflGtREAEREAEakZAhlmzDlF1REAEREAE6klAhnnSL3oWAREQAREQgVMJyDBPxaMXRUAEREAEROCEgAzzhIOe60xAdRMBERCBGhCQYdagE1QFERABERCB+hOQYda/j1RDEagzAdVNBDpDQIbZma5WQ0VABERABC5CQIZ5EXo6VwREQATqTEB1K5WADLNUnMpMBERABESgrQRkmG3tWbVLBERABESgVAIlG2apdVNmIiACIiACIlAbAjLM2nSFKiICIiACIlBnAjLMOvdOyXVTdiIgAiIgApsTkGFuzk5nioAIiIAIdIiADLNDna2m1pmA6iYCIlB3AjLMuveQ6icCIiACIlALAjLMWnSDKiECIlBnAqqbCEBAhgkFSQREQAREQATOICDDPAOQXhYBERABEagzge3VTYa5PdYqSQREQAREoMEEZJgN7jxVXQREQAREYHsEvgYAAP//hLmJggAAAAZJREFUAwDYHo5F4Ix0uAAAAABJRU5ErkJggg==', '2026-08-31 11:04:41', '2026-08-31 08:47:21', '2026-08-31 11:04:41'),
(3, 1, 2, 'dean', 3, 'College Dean', 'hr', 'Approved', 8, 'User', NULL, 'data:image/png;base64,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', '2026-08-31 11:05:45', '2026-08-31 08:47:21', '2026-08-31 11:05:45'),
(4, 1, 2, 'research_office', 4, 'Research Office', 'crad_officer', 'Approved', 991, 'User', NULL, 'data:image/png;base64,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', '2026-08-31 11:06:34', '2026-08-31 08:47:21', '2026-08-31 11:06:34');
INSERT INTO `grant_proposal_approval_steps` (`id`, `workflow_id`, `grant_application_id`, `step_key`, `step_order`, `step_label`, `approver_role_key`, `status`, `approver_user_id`, `approver_name`, `remarks`, `signature_data`, `acted_at`, `created_at`, `updated_at`) VALUES
(5, 1, 2, 'vpaa', 5, 'VPAA Sign-off', 'qa', 'Approved', 992, 'User', NULL, 'data:image/png;base64,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', '2026-08-31 11:07:08', '2026-08-31 08:47:21', '2026-08-31 16:57:24'),
(7, 1, 2, 'finance', 6, 'Finance Office', 'finance', 'Approved', NULL, NULL, NULL, NULL, NULL, '2026-08-31 11:35:22', '2026-08-31 16:57:24'),
(8, 2, 3, 'adviser', 1, 'Academic Adviser', 'adviser', 'Approved', 54, 'User', NULL, 'data:image/png;base64,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', '2026-08-31 12:03:28', '2026-08-31 11:44:23', '2026-08-31 12:03:28'),
(9, 2, 3, 'department_chair', 2, 'Dept. Chair', 'department_chair', 'Approved', 990, 'User', NULL, 'data:image/png;base64,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', '2026-08-31 12:06:50', '2026-08-31 11:44:23', '2026-08-31 12:06:50'),
(10, 2, 3, 'dean', 3, 'College Dean', 'hr', 'Approved', 8, 'User', NULL, 'data:image/png;base64,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', '2026-08-31 12:07:38', '2026-08-31 11:44:23', '2026-08-31 12:07:38'),
(11, 2, 3, 'research_office', 4, 'Research Office', 'research_office', 'Approved', 991, 'User', NULL, 'data:image/png;base64,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', '2026-08-31 12:08:11', '2026-08-31 11:44:23', '2026-08-31 12:08:11'),
(12, 2, 3, 'vpaa', 5, 'VPAA Sign-off', 'vpaa', 'Approved', 992, 'User', NULL, 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAcwAAACMCAYAAADx9JleAAAPWklEQVR4AeyczXIcSRVGu8XMsLLFAssRsCFgM3IEPAEE8Ga8GQt4AogYsQFWEGGbBR6vmGHUk6eta6fLpf6tqr6VdTr0qf6yMu89V7qfqyX5auVLAhKQgAQkIIG9BDTMvYgcIAEJSEACElitNMzMXwXGJgEJSEACaQhomGlKYSASkIAEJJCZgIaZuTrGlpmAsUlAAgsjoGEurOCmKwEJSEACpxHQME/j5l0SkEBmAsYmgREIaJgjQHVKCUhAAhJoj4CG2V5NzUgCEpBAZgKzjU3DnG3pDFwCEpCABKYkoGFOSdu1JCABCUhgtgQWYZizrY6BS0ACEpBAGgIaZppSGIgEJCABCWQmoGFmrs4iYjNJCUhAAvMgoGHOo05GKQEJSEACFyagYV64AC4vgcwEjE0CEvhAQMP8wMI9CUhAAhKQwKMENMxH0XhBAhKQQGYCxjY1AQ1zauKuJwEJSEACsySgYc6ybAYtAQlIQAJTEzjGMKeOzfUkIAEJSEACaQhomGlKYSASkIAEJJCZgIaZuTrHxOZYCUhAAhIYlYCGOSpeJ5eABCQggVYIaJitVNI8MhMwNglIoAECGmYDRTQFCUhAAhIYn4CGOT5jV5CABDITMDYJHEhAwzwQlMMkIAEJSGDZBDTMZdff7CUgAQlkJpAqNg0zVTkMRgISkIAEshLQMLNWxrgkIAEJSCAVAQ2zUw4PJSABCUhAAn0ENMw+Kp6TgAQkIAEJdAhomB0gHmYmYGwSkIAELkdAw7wce1eWgAQkIIEZEdAwZ1QsQ5VAZgLGJoHWCWiYrVfY/CQgAQlIYBACGuYgGJ1EAhKQQGYCxjYEAQ1zCIrOIQEJSEACzRPQMJsvsQlKQAISkMAQBMYyzCFicw4JSEACEpBAGgIaZppSGEh2Ak9vbu+vn7/YdPXk2ZffZY/d+CQggfMJaJjnM5zfDEZ8FAEMEZNcl1ffjeX0uu+85yQggbYIaJht1dNsBiQQRnlVXjHtfXm9efnVutbXr+78PgpAbiXQMAG/0RsurqmdRiDeei0++dH3Byb59vXffnDarAff5UAJSCApgY8aQtIYDUsCkxHALOu3WMsD5fYDs5wsCBeSgARSEtAwU5bFoKYmwNuvtVluyguT5IkSTR2P6yUlYFiLJqBhLrr8Jg8BjJK3X+PJsnjlxp9LQkZJQAI1AQ2zpuH+4gjwZBlGSfK8/6pZQkJJYHYERg9YwxwdsQtkJRBPlhEfT5a+/Ro03EpAAl0CGmaXiMfNE8Aou39X6ZNl82U3QQmcTUDDPAOht86fAE+V8cs988/GDCQggTEJaJhj0nXu1AQwS39embpEBieBVAQ0zFTlMJjhCDiTBCQggWEJaJjD8nQ2CUhAAhJolICG2WhhTUsCQYA/nUH8otMu8ctQIcbH/WNsnVMCcySgYc6xasYsgR0EMDuML8yR/5QB7bhle4m/Rw0xPu5nrhBzbwf7SQILJKBhLrDoS0+ZX/aBAebAtgVhZGFwmF1fbuTNn8/wW8FdcZ7rqMuDuULMHeuEibJl/e59Hs+VgHE/RkDDfIyM55slUP/nBC00egwLI+sWDBNEYY78RnCdez2e81xHMZ4t92OiqB7PfpgoW9bHSIkFcV1JoDUCGmZrFTWfowjQ7I+6IdFgzB6TqnPA5EKYIDonZO7HRFHMy7Y20tpMiQURF8aJzlnfeyWQiUAGw8zEw1gWQoCGT6o0d4yH/bmIeDEknuoiZvLByOJ47G1tpLWZYp6I9WGLiBVpnlBRcyagYc65esZ+MgEafjT22nhOnnDkG8MkMZ463jBK8hk5hIOmxzwR5g1fFDdqnkHC7VwJaJhzrdxUcTe8Do090sv89NM1SWLOZpTE1BV80S7zhDvq3uuxBDIS0DAzVsWYJiOA8bBYPP3wJMdxFnXNhHgxoCxPlIdywjgRsfPUibgX7og8EeeUBLIS0DCzVsa4JiGA8WBCsRhvd2Zp3DxZYibEhsFgNsTL8YNmucE4EfmQF0mQJ4I94pySQDYCGma2ihjP5AQwoW7zzta0MZjJwUywIHl12YdxTrC8S0jgKAIa5lG4HNwyAZp3/cTDE96ljJO1gzWGEvutbmFPnjX/odnzdjtiXvgOJeZDn9TGE80R0DCbK6kJnUNgisa9Lz4aeYzBRGJ/CVv4x1vkPGkem/MuQ+TtdnTKvLviYD5E3Vh/19ilXbt+fvvHpzcv/tBK3hpmK5U0j0EJ0Ljrpx2a4dRPEUszy3MKiFFRo0MMkbpiygjG54r5zom91XtLPf65Wq1/t15vfrvK9zopIg3zJGzetAQCmCbNNBpiPEVMbZxLYB05whbTi+Nd2zDJ0pg33Xt2mSF15efWaNf8h15jvkPHLmVcqcl9yfVnRas3L+9+z7YFaZgtVNEcRiVAQwzTZCGMk8bO/piaYo0x4z9m7jA/2O67L8buMsmhzHBfLF7/QKB8vf6pGOUGlbProuY+NMyJSuoy8yaAab55+dU6jJPGTmMoTYJ/SY+S3BRrjBL4kZNigLX51U+HTMV1WIfqsVyP8ZcySeIijiXq+ub2L+SPytfrrysGG75fUHVu9rsa5uxLaAJTEpjCOGkyYczkVhrRekxjZo2pRT40WRQGSM7kXhsfZhnX6xjDJLvj6zFT7JPHFOtkWeP65sXft3r+4t2T5Hr9y25spY5/LnVp0luaTKpbQI8lsJvA8VcxztIYNnFnmNrTm9tBnjiZvzSdSZ9oI5cxt/DBJOFVrwNLcuYcJskYFGbJdXiEalPlnqnVlwcmfum4huZQ8vwXohZotV79fKvuQpvNX6M2pY6/6V5u5VjDbKWS5jE5gdIYrmgSNHMWxwRQaTCDmCZzsgaNmH3E/DSuIddg3jFVGyDxx1rkBT9EnjEuTDLGwZfrcXzpLez78mjBLEtuHxlkyfMn6BPmm9U/VkXUbqtXd7/6ZEyDJzTMBotqStMSoJnTNGjsrFwazHpIU6MRx/x9a5QmN5hBE/9QeswAwyjJa99a5MsYcnzy7PZbxPGl9OTZl99RX9YnNupySB6Mz6inz29fw/b64S3WkluvQZZc/43Id6tXX/3iTVHGnMaMScMck65zL4rA2MbJ/IiGVZrX9u3g0uC2HzQ8Gt+lgWMoiHi6T4rHGGXksU3u4dPV1fozxNxdkXutJ89uv0Exz7nbJ8UoWbPOiVqcO++U9z/FHIuuH8yR7Xq1/jF4u3GUr6+PDLLk+lPUHbe0Yw1zaRU339EJlMZyhTmUpvPe1GhONPShFmeN2jiZl8YX69DgOTeFWIt1EYaC6nVhQaz7nsS4zrjQ/f3m/5vqVc/Z3Sf3WsVYP0fEtEvUpE9Pbm7/180r1iQkYozjDNuSw38r3fflvDXHYpDdeMlns9r8h5xC5evrQIPsztb2sYbZdn3N7kIEaP6l6VzRjCIEGno0stLc7hFNOa6fsmUNmlx3HUyLtVjjlHl33UPMiPkRa3XH35cXcSFYdK8fcvz29d3n5Bdirlr395tvQ+Rf65D5GUNN+nS1Xn/Rl1fcQ96ZVHK4rrQmzseEOaJgueX78u7ZY+M9/4GAhvmBhXsSGJwAzYjGRDOvJy/NbftBU47Gi7khzKgee8h+rHNfXvVaLML8zIsOmatvDDExDyJmVI8ry24/yBWdapL1nPv2i6F+ESL/WsTQp/vN5pta+9aY43Xq/6A3ZbtVzeLrYo5ojrldOua5Gealebm+BE4iQDOPpoWzlEa2/agnw9wQZoQx1cLsamFgqL6ffYwq1touUD5xnnlRzBlzxRxsQ1yLcbElJuapRR6RE+ui+nrG/bev7n642mw+u1qXJ8iivhgLsk2dW+Q4ly31f9CPynarvjw9dzwBDfN4Zt4hgbMIYCylkV2haMI0aBo16pscs6uFgaEwtL5tjN81X8zBNsR9ffcQI4qYyaNvXLZz8Q+BYESe3RjrvKjLXHLr5uHxuAQ0zHH5Lmt2sz2ZAA2aRo3CkNjSyBFGWuvkhc64EaPBTHkCDYUZsT1j6pNuZc1QxBOmWG+JG9WLwBTBGMG/vu6+BPoIaJh9VDwngSQEaOQII61Fkx9ahxgyhlkLIwrVJjXFfqzLNmLaVbauQcJ113ivSaBLQMPsEvFYAm0S2JvVY4aM0YRqU2V/76QTDSAWFHHGtv5HhQY5UTEaXkbDbLi4piaBIQhgNKHaVNmvDemS+8SCIs7YDpG/c0ggCGiYQcKtBCQggUsRcN1ZENAwZ1Emg5SABCQggUsT0DAvXQHXl4AEJCCBzATex6ZhvkfhjgQkIAEJSOBxAhrm42y8IgEJSEACEnhPQMN8jyLPjpFIQAISkEA+AhpmvpoYkQQkIAEJJCSgYSYsiiFlJmBsEpDAUglomEutvHlLQAISkMBRBDTMo3A5WAISyEzA2CQwJgENc0y6zi0BCUhAAs0Q0DCbKaWJSEACEshMYP6xaZjzr6EZSEACEpDABAQ0zAkgu4QEJCABCcyfQMuGOf/qmIEEJCABCaQhoGGmKYWBSEACEpBAZgIaZubqtBybuUlAAhKYGQENc2YFM1wJSEACErgMAQ3zMtxdVQKZCRibBCTQQ0DD7IHiKQlIQAISkECXgIbZJeKxBCQggcwEjO1iBDTMi6F3YQlIQAISmBMBDXNO1TJWCUhAAhK4GIEDDPNisbmwBCQgAQlIIA0BDTNNKQxEAhKQgAQyE9AwM1fngNgcIgEJSEAC0xDQMKfh7CoSkIAEJDBzAhrmzAto+JkJGJsEJNASAQ2zpWqaiwQkIAEJjEZAwxwNrRNLQAKZCRibBI4loGEeS8zxEpCABCSwSAIa5iLLbtISkIAEMhPIGZuGmbMuRiUBCUhAAskIaJjJCmI4EpCABCSQk4CG+a4ufpaABCQgAQnsJKBh7sTjRQlIQAISkMA7AhrmOw5+zkzA2CQgAQkkIKBhJiiCIUhAAhKQQH4CGmb+GhmhBDITMDYJLIaAhrmYUpuoBCQgAQmcQ0DDPIee90pAAhLITMDYBiWgYQ6K08kkIAEJSKBVAhpmq5U1LwlIQAISGJTAwIY5aGxOJgEJSEACEkhDQMNMUwoDkYAEJCCBzAQ0zMzVGTg2p5OABCQggdMJaJins/NOCUhAAhJYEAENc0HFNtXMBIxNAhLITkDDzF4h45OABCQggRQENMwUZTAICUggMwFjkwAENEwoKAlIQAISkMAeAhrmHkBeloAEJCCBzASmi03DnI61K0lAAhKQwIwJaJgzLp6hS0ACEpDAdAS+BwAA///et0PsAAAABklEQVQDAMwUKaADRg1/AAAAAElFTkSuQmCC', '2026-08-31 12:08:34', '2026-08-31 11:44:23', '2026-08-31 16:57:24'),
(13, 2, 3, 'finance', 6, 'Finance Office', 'finance', 'Approved', 4, 'User', NULL, 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAcwAAACMCAYAAADx9JleAAAP1klEQVR4Aeyd327cWhWHPQEdcS5Oey5oK8EdCIn2GeDdeDd4Ai4ahAR3IDVFQFskDkIng78kK9k4nsl44j9r21/UX+yxt/de61vp+o09qXrR+CUBCUhAAhKQwJMENMwnETlAAhKQgAQk0DQaZuafAmOTgAQkIIE0BDTMNKUwEAlIQAISyExAw8xcHWPLTMDYJCCBjRHQMDdWcNOVgAQkIIHzCGiY53HzKglIIDMBY5PABAQ0zAmgOqUEJCABCayPgIa5vpqakQQkIIHMBKqNTcOstnQGLgEJSEACcxLQMOek7VoSkIAEJFAtgU0YZrXVMXAJSEACEkhDQMNMUwoDkYAEJCCBzAQ0zMzV2URsJikBCUigDgIaZh11MkoJSEACEliYgIa5cAFcXgKZCRibBCTwQEDDfGDhngQkIAEJSOAgAQ3zIBpPSEACEshMwNjmJqBhzk3c9SQgAQlIoEoCGmaVZTNoCUhAAhKYm8AQw5w7NteTgAQkIAEJpCGgYaYphYFIQAISkEBmAhpm5uoMic2xEpCABCQwKQENc1K8Ti4BCUhAAmshoGGupZLmkZmAsUlAAisgoGGuoIimIAEJSEAC0xPQMKdn7AoSkEBmAsYmgRMJaJgngnKYBCQgAQlsm4CGue36m70EJCCBzARSxaZhpiqHwUhAAhKQQFYCGmbWyhiXBCQgAQmkIqBhdsrhSwlIQAISkEAfAQ2zj4rHJCABCUhAAh0CGmYHiC8zEzA2CUhAAssR0DCXY+/KEpCABCRQEQENs6JiGaoEMhMwNgmsnYCGufYKm58EJCABCYxCQMMcBaOTSEACEshMwNjGIKBhjkHROSQgAQlIYPUENMzVl9gEJSABCUhgDAJTGeYYsTmHBCQgAQlIIA0BDTNNKQxEAhKQgAQyE9AwM1dnqticVwISkIAEBhPQMAcj8wIJSEACEtgiAQ1zi1U358wEjE0CEkhKQMNMWhjDkoAEJCCBXAQ0zFz1MBoJ9BL45tUvv3/55t3+uXrx+u11V8wd6l3cgw8E3Ns0AQ1z0+U3+RoIYHAX7dcYse56vtqp7/90DZm1MdMx1nYOCdROQMOsvYKJ4+82X17TgBOHnCo0eCE8jsD27denD+93Q3Td+WqnePSHuQ+JtXFT4kDUTwM9RMvjCxOYfHkNc3LELlASoAFH4y2Pu3+cAL73+epy8N/XLx//8INSzNHVIQMOZy0jo34aaEnE/S0RGPwXcEtwzHU8AtF82TIrjTeMk7sWjqkHArCJVxgaphev59qGsbI+onaoXJ86hoFSR1Sed18CayKgYT6jml56nEA0WUbRWNnShGm6iNccRxiEzRYiTQOL271c36kdoq6IGqKIkjoi4reWQcXtmghomGuqZsJcaLDRVKOZ8prj0XQj7Dhvs70lAh90+yrfd2qIiBFR14iyrKWfeQYVt7UT0DBrr2AF8UdTjYbKI7wwxfJcnC+b7fnpeeXcBA7Vknp71zl3NVxvCgIa5hRUnbOXAA21NMUwTQZzDsV5jmGc5RiOqfwEqCM6dteZPwsjlMBjAhrmYyYemZAAjTRMEUPkzqNcjvNlo40xWzTOLpuSUy373XoSNzUlty3WlPxVvQQ0zHprV23kNFH+mUQk0Nc8GdM1zq00WPIu2cR+zduoJ3XvvmHaSl1rrp+x3xLQMG85+H1mAvwzCYyhbJ59jZNGW47pM9eZQ59lOdjEQn1c4lxtW+pOTclvi3Wto15GeYiAhnmIjMdnIUDzLBtnnzkwpttg+8bNEvCMi3A3xnI8wlzjb5pS16h95LmFupKrqpOAhlln3VYVddk4MQfuIvsMom/cmhssd2NhKPym6RpzpabdN0PUf425ruov7UaTyWCYG0Vv2iWBaJxx7JBBxLgwkjBYGiyK69eyJd/IhVzXmCP5kWfXOMm1740T45UEliCgYS5B3TUPEug2zUN3G30NFkM5NP7gghWcOJVJBak8GSJ1Ld8MHXrj9OREDpDABAQ0zAmgrmrKBZKJplk2zkNGyNjSUAh3jcZJnvGZZuTIHRj7axO5ljUds54vX7/7x+3P0rvfrI2b+UxPQMOcnrErnEGAponCNJniWONkbNlknxrP+drEZ5pljsd41JZbX7zUlDcJ8TNAvi9ev73uG/vUMUwSNbvm28YvCZxJQMM8E5yXzUOAplmaBKtG4+xrnuX4stHSLPvGM19tIkeMpGmam9CDx82LlX3jTQL5lrUcWsd2/G8Dy75pvuPn6fPVe+8wA4rbkwlomCejcuCSBD5fXV7Q6MrGecwoGI8wlvKatRgnRtLl0RrDWXdfS9b11LWjlow/VnfOl6Le7fhf3R3bf/7w/uu7fTcSGExAwxyMzAuWJEDj7BoFTfGQWWAsQ69ZMr+ha5PbGt8Q9HGglrwB4lxrgrtDNef8y9fv/sTPBfuoZfS79udm3n7HwmpVBPwBWlU5t5MMRtE2wF3bCNunbE1DA6VBvjjyGRfXxPim/eKaY+PbIVX8Ia+hLKpIrCfIp0yzredf+Dlods3P4nLYtIx+Ha/dSuBcAhrmueS8LgWBthE+elRLw2wbZ+/jyaHjUyR5YhDkxhsCxCW8ITjGgjE1qmuadzneGGWb80/uc9o3f8Ys71+7I4EHAmftaZhnYfOibAQwC5rjqWYxdHy2fA/FQ14IDohxrYns7kzl+tAbCcbVpN3F7u+RH3G3Od4bZXv8r/wsfLp6/3POKQmMRUDDHIuk86QgcMws+gJkPJ+LtU325Ee7ffNkO0Ze6Pp6/98yt9ZYbswzDPSb12//ky32Q/G8ePP248s37/Zo1+x+TC7dsRhlm/dPu8d9LYExCGiYY1A8YQ6HzEegbZgXKIyClWmuNNq+Oywe8fWNZyzi+lr15ePlV+SGkZQ8yAcmF7vdV3BB5JrBQNs4/nmna+IKYZLEXWrf7P9W5sXY8rz7EhiTgIY5Jk3nSkWgzygwibYZ9z6a7I5nLKIJc02q5M4IJvIL8yyNhunI9WL3YKDkHSL/UGuq/w5x3blq5+s3xt3uZRsL2vXNjUmSA/r84fIVeV23XzGWmGPfrQTGJKBhjknTuVISoKHSXMMg2mZ886dt2He/GPT/YXfHc5YLaMSHrmFMTSJHBBcEG3QoB/IPXex2PwrB5Fy182GKqN8YCWi//9RuPhFjCJPsxslTgtYz7+u5ljp18/T1sgQ0zGX5u/qMBMIg2gb86PPKvgbbHU+obZO/+QywbzznaxW5ojAlttf7/XcIXqXGzvFu7sfGeHXJo/Vv27i+PWXN0jSp09pqdAoDx0xLQMOclq+zJyTQNuBH/xSFBsudEk22+19KleNp7qRUjuf1GvXl6vJrRP6lMNMxdTf3QWMcwhbTLGvUreWQuRwrgS4BDbNLxNebIUCjpvFHgyVxjJD/UirMk2MhxiPGI44zPsZithxTyxKIGhEFtWSrJDAGAQ1zDIrOUTUBGizGyWdgYYQkdMgMGY+4JsYzFmGaL169/QXXq+UIUJ9YnTc0se92bALbmk/D3Fa9zfYIAR7n0WjDCLtmSOPFEMspyvEcxzR3F7s/dsdxTs1LgDrGij6aDRJun0NAw3wOPa9dLQGMENF0wzhJFkPEOFFpioz9vvnhmxjLuPI81yoJSKBuArUZZt20jb5KApghxvnUI9t/ffj9FWPDNKtMdmVBUzNS8rNMKKjnEtAwn0vQ6zdD4NRHtpgmBst2M3CSJkrNkoZmWBUS0DArLFrakDcUGGaIMMbyjpJHsfG41keyG/qBMNVNENAwN1Fmk5ySQGmcYZ4YJwrz9JdOpqyAc0tgHgIa5jycXWUDBDBO1HfXyWdoYZ4LodjksjDfZOImPQkBDXMSrE66dQJhnH3mSRPncS3aOqe58qcOc63lOusloGGut7ZmloRAmCePaxFh8bgWhXlyTG2YgKlXQUDDrKJMBrkGAhgn4m4njJO8wjg1T2iMJ3iON5szSaBpNEx/CiSwAIEwTv6dYJ958rgWLRDa6pbkDcrqkjKhOQncr6Vh3qNwRwLzE+DfCYZ5YpyIKLjrRNwlIcwTcU4dJ8BvJMPs+CjPSmA4AQ1zODOvkMAkBDBOxB1RGGcshHkijABhnghziDFumwYe/EZysIBl7LuVwHMJaJjPJTjB9U4pgTBOGj7miUoqmCfCHDBQhIEiTKMcu4V9coYBPCJf2MW+WwmMQUDDHIOic0hgQgKYJ8IAQhgoKpfFQBGmgXkgDBRhKOXYteyTF3mSc+TE58JwitduJTAWAQ1zLJLOsxECOdLEQBHGgDAJDBSVEWKgCEPBWBAGijCbcmwt+8RNHoi8yrhhwefC5TH3JTAWAQ1zLJLOI4EFCWASGCjCNNCaTPSYSYKdXMmZfSWBqQhomFORdV4JLExgDSYaRtm9k8QgESaJyBXcSgJTEtAwp6Tr3BJIRgBj4S4UYTQI4+FRLirD5VEuwqx4/DlUPPZFmB4q5+7bZwwq12HtciyxEjN5oPKc+xKYmoCGOTVh55dAcgIYDwaKMCOEMWGg6NzwMVuE6aHSCPv2GYO66xELMSFi7Z73dS0E6o9Tw6y/hmYggdEJYEwYKMKohgiDQ5gtGhoc13B9rEksQ+dwvASmIKBhTkHVOSWwYQIYHMJsURjfqVuu4foNIzT1pATWbJhJkRuWBCQgAQnUSEDDrLFqxiwBCUhAArMT0DBnR+6CNwT8JgEJSKAyAhpmZQUzXAlIQAISWIaAhrkMd1eVQGYCxiYBCfQQ0DB7oHhIAhKQgAQk0CWgYXaJ+FoCEpBAZgLGthgBDXMx9C4sAQlIQAI1EdAwa6qWsUpAAhKQwGIETjDMxWJzYQlIQAISkEAaAhpmmlIYiAQkIAEJZCagYWauzgmxOUQCEpCABOYhoGHOw9lVJCABCUigcgIaZuUFNPzMBIxNAhJYEwENc03VNBcJSEACEpiMgIY5GVonloAEMhMwNgkMJaBhDiXmeAlIQAIS2CQBDXOTZTdpCUhAApkJ5IxNw8xZF6OSgAQkIIFkBDTMZAUxHAlIQAISyElAw7yti98lIAEJSEACRwlomEfxeFICEpCABCRwS0DDvOXg98wEjE0CEpBAAgIaZoIiGIIEJCABCeQnoGHmr5ERSiAzAWOTwGYIaJibKbWJSkACEpDAcwhomM+h57USkIAEMhMwtlEJaJij4nQyCUhAAhJYKwENc62VNS8JSEACEhiVwMiGOWpsTiYBCUhAAhJIQ0DDTFMKA5GABCQggcwENMzM1Rk5NqeTgAQkIIHzCWiY57PzSglIQAIS2BABDXNDxTbVzASMTQISyE5Aw8xeIeOTgAQkIIEUBDTMFGUwCAlIIDMBY5MABDRMKCgJSEACEpDAEwQ0zCcAeVoCEpCABDITmC82DXM+1q4kAQlIQAIVE9AwKy6eoUtAAhKQwHwE/gcAAP//qhOOqAAAAAZJREFUAwAVEEOgwAHQnQAAAABJRU5ErkJggg==', '2026-08-31 12:13:46', '2026-08-31 11:44:23', '2026-08-31 16:57:24');
INSERT INTO `grant_proposal_approval_steps` (`id`, `workflow_id`, `grant_application_id`, `step_key`, `step_order`, `step_label`, `approver_role_key`, `status`, `approver_user_id`, `approver_name`, `remarks`, `signature_data`, `acted_at`, `created_at`, `updated_at`) VALUES
(14, 3, 4, 'adviser', 1, 'Academic Adviser', 'adviser', 'Approved', 54, 'User', NULL, 'data:image/png;base64,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', '2026-08-31 13:44:23', '2026-08-31 13:34:26', '2026-08-31 13:44:23'),
(15, 3, 4, 'department_chair', 2, 'Dept. Chair', 'department_chair', 'Approved', 990, 'User', NULL, 'data:image/png;base64,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', '2026-08-31 13:56:44', '2026-08-31 13:34:26', '2026-08-31 13:56:44'),
(16, 3, 4, 'dean', 3, 'College Dean', 'hr', 'Approved', 8, 'User', NULL, 'data:image/png;base64,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', '2026-08-31 13:57:33', '2026-08-31 13:34:26', '2026-08-31 13:57:33'),
(17, 3, 4, 'research_office', 4, 'Research Office', 'research_office', 'Approved', 991, 'User', NULL, 'data:image/png;base64,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', '2026-08-31 13:58:21', '2026-08-31 13:34:26', '2026-08-31 13:58:21'),
(18, 3, 4, 'vpaa', 5, 'VPAA Sign-off', 'vpaa', 'Approved', 992, 'User', NULL, 'data:image/png;base64,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', '2026-08-31 13:59:06', '2026-08-31 13:34:26', '2026-08-31 16:57:24'),
(19, 3, 4, 'finance', 6, 'Finance Office', 'finance', 'Pending', NULL, NULL, NULL, NULL, NULL, '2026-08-31 13:34:26', '2026-08-31 16:57:24'),
(20, 4, 5, 'adviser', 1, 'Academic Adviser', 'adviser', 'Approved', 54, 'User', NULL, 'data:image/png;base64,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', '2026-08-31 14:17:42', '2026-08-31 14:15:58', '2026-08-31 14:17:42'),
(21, 4, 5, 'department_chair', 2, 'Dept. Chair', 'department_chair', 'Approved', 990, 'User', NULL, 'data:image/png;base64,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', '2026-08-31 14:19:54', '2026-08-31 14:15:58', '2026-08-31 14:19:54'),
(22, 4, 5, 'dean', 3, 'College Dean', 'hr', 'Approved', 8, 'User', NULL, 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAcwAAACMCAYAAADx9JleAAAJsklEQVR4AezZwW4TVxQGYDuhUisE6QICy6qbAqs+QBftQ/R9kbquuipBlbroBkrpgoDULgpxfRMmWKNxbI9nxufe+0VM7LE9M//9DvIvO0czPwQIECBAgMBGAYW5kcgLCBAgQIDAbKYwI/8vkI0AAQIEwggozDCjEIQAAQIEIgsozMjTkS2ygGwECFQmoDArG7jlEiBAgEA/AYXZz81RBAhEFpCNwAgCCnMEVKckQIAAgfIEFGZ5M7UiAgQIRBbINpvCzHZ0ghMgQIDAlAIKc0pt1yJAgACBbAWqKMxspyM4AQIECIQRUJhhRiEIAQIECEQWUJiRp1NFNoskQIBAHgIKM485SUmAAAECBxZQmAcegMsTiCwgGwECnwQU5icL9wgQIECAwFoBhbmWxhMECBCILCDb1AIKc2px1yNAgACBLAUUZpZjE5oAAQIEphbYpTCnzuZ6BAgQIEAgjIDCDDMKQQgQIEAgsoDCjDydXbJ5LQECBAiMKqAwR+V1cgIECBAoRUBhljJJ64gsIBsBAgUIKMwChmgJBAgQIDC+gMIc39gVCBCILCAbgS0FFOaWUF5GgAABAnULKMy652/1BAgQiCwQKpvCDDUOYQgQIEAgqoDCjDoZuQgQIEAglIDCbI3DLgECBAgQ6BJQmF0qHiNAgAABAi0BhdkCsRtZQDYCBAgcTkBhHs7elQkQIEAgIwGFmdGwRCUQWUA2AqULKMzSJ2x9BAgQIDCIgMIchNFJCBAgEFlAtiEEFOYQis5BgAABAsULKMziR2yBBAgQIDCEwFiFOUQ25yBAgAABAmEEFGaYUQhCgAABApEFFGbk6YyVzXkJECBAYGcBhbkzmQMIECBAoEYBhVnj1K05soBsBAgEFVCYQQcjFgECBAjEElCYseYhDQECkQVkq1pAYVY9fosnQIAAgW0FFOa2Ul5HgAABApEFRs+mMEcndgECBAgQKEFAYZYwRWsgQIAAgdEFFOYexA4lQIAAgXoEFGY9s7ZSAgQIENhDQGHugefQyAKyESBAYFgBhTmsp7MRIECAQKECCrPQwVoWgcgCshHIUUBh5jg1mQkQIEBgcgGFOTm5CxIgQCCygGzrBBTmOhmPEyBAgACBFQGFuYLhLgECBAgQWCcQoTDXZfM4AQIECBAII6Aww4xCEAIECBCILKAwI08nQjYZCBAgQOBSQGFeMvhFgAABAgRuFlCYN/t4lkBkAdkIEJhQQGFOiO1SBAgQIJCvgMLMd3aSEyAQWUC24gQUZnEjtSACBAgQGENAYY6h6pwECBAgEFmgVzaF2YvNQQQIECBQm4DCrG3i1kuAAAECvQQUZi+23Q9yBAECBAjkLaAw856f9AQIECAwkYDCnAjaZSILyEaAAIHNAgpzs5FXECBAgACBmcL0n4AAgdACwhGIIqAwo0xCDgIECBAILaAwQ49HOAIECEQWqCubwqxr3lZLgAABAj0FFGZPOIcRIECAQF0CuRXmztO5c//Rh03b3dPHF5u2kwdPFrlv7TU2LjujOoAAAQIVCkxYmD8e37v3zZ3b9588bN6ou27bb+rNft+yOtriZ77FTwn/N9rLbGh2sW3mkWZXgok1ECBAYFuBSQozvcmePPj1/X/Hx29vHc1eNm/UXbftN/Vmf9sF9X3dYsPPxRY/56+ezSNuTfT2EvtYNfNIs1st2jTjux8/qacyTVuf8zuGAAECUQUmKcxdFt9+U2/2mzf99u38aPHVxfH7+7ePv7i9T1m9/evs6Kbt3evnx5u2XdY55Wub3O317eqV7Jt5tPM3RZpuU5mmratQU5GmrX28fQIECEQXmKQw0xv1tm/O6bVdW/Om37598/Lsj3cvfvv7xYuf/4mOnXu+ZN/MZnWeq0WaCrVrnalI05aKNG2rZbp6v/mk2nUOj+0l4GACBPYUmKQw98zo8OACq0WaCnW1TNP91ULdtJRUqmlTopukPE+AwNQCCnNq8Qqvt1qoqUDXbeuKNRVo2lZLtOt+hbSWXIqAdWQhoDCzGFMdIdcV67qvedsqXSV602Pp619/T20r2idAYJ2Awlwn4/EwAl1f87Y/pfYJmz61dv09dVmi7/uczzEECBQpcL0ohXlN4U7OAu0C3bTffP3bteZliR6nT59p63reYwQI1CmgMOuce/Wrbr7+bRdrA5M+faZt9StdBdrouCVQp4DCDDh3kQ4nkAr04mLxfrH8aadoCvTO6eN/28/ZJ0CgfAGFWf6MrXBHgXevzz5r/9102Z+X/9Kpjubzz33yTBI2AnUJKMy65m21PQVSgaZtsVgs2qdoPnkuv7J9037OPgEC5QgozHJmaSUTCKTSTF/bNtvlx87lr3TpZXGeLEvzIt23ESBQnoDCLG+mVjShQCrQtC078/KT57I056tf156cPvl9wjjVXwoAgTEFFOaYus5djUBTmk1xXi98Pvv67unjn6733SFAIFsBhZnt6ASPJpBKM23p69rVbMtPnd+dnD75ZfUx9wnUJ5D/ihVm/jO0goACV6W5+HAdbT77Nn1V++XDR99fP+YOAQJZCSjMrMYlbE4C56/Obp2/ejafzRZPr3Ivnr758/nH+1eP+E2AQD4CJRdmPlOQtGiB81dnP5wvi/N8eVv0Qi2OQOECCrPwAVseAQIECAwjoDCHcXSWXQW8ngABApkJKMzMBiYuAQIECBxGQGEext1VCUQWkI0AgQ4BhdmB4iECBAgQINAWUJhtEfsECBCILCDbwQQU5sHoXZgAAQIEchJQmDlNS1YCBAgQOJjAFoV5sGwuTIAAAQIEwggozDCjEIQAAQIEIgsozMjT2SKblxAgQIDANAIKcxpnVyFAgACBzAUUZuYDFD+ygGwECJQkoDBLmqa1ECBAgMBoAgpzNFonJkAgsoBsBHYVUJi7ink9AQIECFQpoDCrHLtFEyBAILJAzGwKM+ZcpCJAgACBYAIKM9hAxCFAgACBmAIK82oufhMgQIAAgRsFFOaNPJ4kQIAAAQJXAgrzysHvyAKyESBAIICAwgwwBBEIECBAIL6Awow/IwkJRBaQjUA1AgqzmlFbKAECBAjsI6Aw99FzLAECBCILyDaogMIclNPJCBAgQKBUAYVZ6mStiwABAgQGFRi4MAfN5mQECBAgQCCMgMIMMwpBCBAgQCCygMKMPJ2BszkdAQIECPQXUJj97RxJgAABAhUJKMyKhm2pkQVkI0AguoDCjD4h+QgQIEAghIDCDDEGIQgQiCwgG4EkoDCTgo0AAQIECGwQUJgbgDxNgAABApEFpsumMKezdiUCBAgQyFhAYWY8PNEJECBAYDqB/wEAAP//8WhvjwAAAAZJREFUAwD1ghNVn4Wm/AAAAABJRU5ErkJggg==', '2026-08-31 14:21:24', '2026-08-31 14:15:58', '2026-08-31 14:21:24'),
(23, 4, 5, 'research_office', 4, 'Research Office', 'research_office', 'Approved', 991, 'User', NULL, 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAcwAAACMCAYAAADx9JleAAAODklEQVR4Aezd324bxxXH8R3aTp3GFm2gooHkxk1vSgH1E7Tv0D5H+xB9iPY52kco0D5BAoi56Z+bBBAdIJbcwGmsajNnxSMON0tSXO6fMzNfIpOlqP0z8zkEfpjhUp4UPBBAAAEEEEBgrwCBuZeIHRBAAAEEECgKAtPyu4C+IYAAAgiYESAwzZSCjiCAAAIIWBYgMC1Xh75ZFqBvCCCQmQCBmVnBGS4CCCCAQDsBArOdG0chgIBlAfqGQA8CBGYPqJwSAQQQQCA9AQIzvZoyIgQQQMCyQLR9IzCjLR0dRwABBBAYUoDAHFKbayGAAAIIRCuQRWBGWx06jgACCCBgRoDANFMKOoIAAgggYFmAwLRcnSz6xiARQACBOAQIzDjqRC8RQAABBEYWIDBHLgCXR8CyAH1DAIG1AIG5tuAZAggggAACWwUIzK00/AIBBBCwLEDfhhYgMIcW53oIIIAAAlEKEJhRlo1OI4AAAggMLXBIYA7dN66HAAIIIICAGQEC00wp6AgCCFgUeDab/0na9MX839PZ2fuqvTgrpwO2k9nZHy3a5NYnAjOVijMOBBA4SkBCUVo9GEvnfi+tKNzLwhUPq1bwyFGAwMyx6owZgQwEprP5Xzbb2Td+dviNnxne1Fo1W5RQlFbUg7EsrgtpRfkfV5Z/lnZ5ce6GbFfLc2aYxfgPAnP8GtCD9AUYYc8CJ7P5330IVsGn28K532624lnhfCv8/zdbsXrcNAbj8vzRpbSLxc/fLBd/kLban01mAgRmZgVnuAikIFAPSOfcrxvGVfrX1q0s3vhA9K38a1GuWzBTfEAwFjx2CBCYO3D4FQII2BC4T0CWRfldWZb/CAJw4p+v2/L8uQ9E3xa/u1yuW2FjiPQiAgECM4Ii0UUEchKQcJSmS6uybZpB1gPy6mLx4dVy8ZucrBjrsAIE5rDeXA0BBGoC09n8s6qtvqYh4SittptfTd2cQRKQdaEkfzY1KALTVDnoDAJpC1TBKAG5CkeZPRbO/apqtaHL8qo0v6xa3ZFKQNaA+HFwAQJzcHIuiEA+AvWArIJRArKJoCw/L3y7C0i/vMoSaxMUr40lQGDW5PkRAQTaCdTDcffsUb7bWH6u4Vhtl4tXl761uzpHIdC/AIHZvzFXQCBJgens7J9VWy2vHjZ7lO82Ll4lCcOgkhUgMJMtbYoDY0xjClThKCF5F5DFp4XzrdapsmT2WCPhx0QECMxECskwEOha4L4BWZTFv6RVy6oX5+5K/ioOS6tdl4PzGRAgMA0UgS4gMLbAyWz+pTT53FFbNXtsmEFKOErTgLxcnv9C2thj4PoI9C1AYPYtzPkRMCYgwShNg1G2zrmPpTV2tTaDlHCU1rgvLyKQsACBmXBxGVreAhKKqyb/OsfdHyaXYJTWpFPePr66mz36JVYJR2lN+/NaLAL0swsBArMLRc6BwMgCq2D8UmaL2iQUV801dc9n41fSwnC8Wi4mvn3StD+vIZC7AIGZ+zuA8UcrcPJi/nq6umN1FYwfNw3Gh6L81xSOnxCOTWK8hkCzQF+B2Xw1XkUAgaMETsKQLNzP6ieTZPRtIxx9KFazRr9l5lgH42cEDhAgMA/AYlcExhCQkPRLrtXnkK4WkmVRfi1Nl1V9KBKOYxSJa2YhQGBmUebaIPkxCgEJyqlfcpWQ9EuuLuy0huTVxeJUWvg7niOAQD8CBGY/rpwVgVYCJzuWXDUkZTZJSLbi5SAEjhIgMI/i42AEjheoheTG55KE5PG+nAGBrgQIzK4kOQ8CBwgQkgdgsSsCRgQITCOFoBvpC0hISpuuPpcMR1zKoyi/Zrk1VDH4nC5lLUBgZl1+Bj+EwMls/kZD0jXc5VqFpPzBgIvF6RD94RoIINBOgMBs58ZRCOwU0JCsgtK5abgzn0uGGjxHoDOB3k9EYPZOzAVyEtCgdPWQLMtLmUlK4w7XnN4RjDUlAQIzpWoyllEENCQbZ5OroLxaLp6N0jkuigACnQkQmEdQcmi+AhKS0naFZDWbJCjzfZMw8uQECMzkSsqA+hQIQzJcdpWbXH2rll2ZTfZZAc6NwHgCBOZ49ly5V4HuTq4huWs26UNS/oYry67dsXMmBMwJEJjmSkKHrAhoUIYzSembziRZchUNGgL5CBCY+dSakd5DQENyz2ySmeQ9LHftwu8QiFGAwIyxavS5U4GT2dl/pe0KSWaTnZJzMgSiFCAwoywbne5CYB2SxUfOFR+F59RlV//ZJLPJEIbnGQgwxG0CBOY2GV5PUkBD8nY2WQ/J4luZSUojKJMsP4NC4CgBAvMoPg6OQeD+IXn+JIbx0EcEEBhHwEJgjjNyrpq0ACGZdHkZHAKjCBCYo7Bz0T4E9oVkWd4uuV4tmUn24c85EUhdgMBMvcLHjs/48RKS0po+k5SuhyFJUIoIDQEE2goQmG3lOG5UgTAkf3yH6+1M8vbmHWaToxaKiyOQkACBmVAxUx+KhmTTbFJnkpmFZOolZ3wImBIgME2Vg87UBQjJugg/I4DAWAIE5ljyXHerwNPZ/J3MIqWx3LqViV9YF6B/yQkQmMmVNN4BaVBOnHscjkKWW6Wx3Bqq8BwBBIYWIDCHFud6GwIakjKbrAflTVl+pyHJHa4bbPyAAALHCbQ6msBsxcZBxwpoUG4LSQnKt8vFh8deh+MRQACBrgQIzK4kOc9eAQ3J+myy9A+dTRKSexnZAQEERhIgMAeCz/UyEpIns/lNPSTFQ0PyarmYEJQiQkMAAcsCBKbl6kTcNwlKDUnnHzoUDUmWXFWELQIIxCJAYMZSqQj6qSGpQRl2WYPS5kwy7CnPEUAAgWYBArPZhVcPENCg5AaeA9DYFQEEohMgMKMrmY0Oa0gym7RRj5R7wdgQsCJAYFqpRAT9kJCUtisk+WwygkLSRQQQaCVAYLZiy+sgH5L/05AMl11L/+CzybzeC4wWgU2BvH4iMPOq90GjDYLyg/BADUm+DhKq8BwBBFIXIDBTr3CL8e0Iyu9Zcm0ByiEIIJCEQGyBmQS61UE0BaVfdS0lJKW9XS5+YrXv9AsBBBDoW4DA7Fs4gvM3BaVfdq1mk7LsGsEQ6CICCCDQuwCB2Tux3QsEf7Lu7jNKDcpWs0m7Q6VnCCCAwNECBObRhHGdQGeTcter8w/tPUGpEmwRQACBZgECs9kluVc1KCfO3c0mZZAEpShk0RgkAggcKUBgHglo/XCC0nqF6B8CCMQiQGDGUqkD+0lQHgjG7giMKcC1oxAgMKMo0/072RSUfDXk/n7siQACCGwTIDC3yUT2elNQ6ueTfDUksmLSXQQQsCRw1xcC844izid8NSTOutFrBBCIT4DAjK9mhc4m+WpIhMWjywggEK0AgWmwdNu6pEE5cXw1ZJsRryOAAAJ9CRCYfcl2eF6CskNMToUAAgi0FCAwW8INcRhBOYTyoddgfwQQyFWAwDRY+aen8+/l88mJWy+98tUQg4WiSwggkJUAgWmk3BqSVVBO3CPtFl8NUQm2COwXYA8E+hQgMPvUvce5NSgnQUjKYTc35Xv+DUqRoCGAAAI2BAjMEeogIXn3/cltQfl6sfFH0kfoJpdEAAEEOhSI/1QE5oA1lKDUJVfnH3ppnU1WM0qCUlnYIoAAAqYECMwByhEGZXg5Dcq3hGTIwnMEEEDApEDKgTkquIakzii1M3K3K0GpGmwRQACBeAQIzI5rpUG57SYe+UPozCg7Rud0CCCAwAACBGYHyBKS3MRzICS7I4AAApEJEJhHFEyCUpdcnX/oqXTJlZt4VIQtAgggEL8AgdmihmFQhodrULLkGqrwPEIBuowAAg0CBGYDStNLGpI6o9R9uIlHJdgigAACaQsQmHvqq0HJTTx7oPg1AggMI8BVRhMgMLfQcxPPFhheRgABBDIVIDCDwutsUpZdnX/or/SzSW7iURG2CCCAQH4C9wjM9FE0KLctu3ITT/rvAUaIAAII7BPINjA1JGU2GQYlN/Hse8vwewQQQCBPgewCU4MyDEkpvS67xvaXeKTvNAQQQACB/gWyCUxu4un/zcQVEEAAgZQFkg5MnU3KsqvzDy2kzia5iUdF2PYjwFkRQCAlgSQD88mLVzOZUW5bduUmnpTewowFAQQQGEYgqcB8/vzTqQTlg+L6wk8onRL6GeU1s0nVYIsAAiJAQ+BQgTQC8+XLxxKUNx88frMlKB8dCsP+CCCAAAIIhALRB6YE5fTdT98RlGFZeY4AAgjELGCz79EG5tPTX1433MyjS6/MKG2+3+gVAgggEK1AdIGpQTmZTB6oevAZJUGpKGwRQAABBDoViCYwn57O38uMcjLpJSg7ReVkCCCAAALpCZgPzHVQuofKz4xSJdgigAACCAwlYDYwCcqh3gIRXIcuIoAAAgYEzAVmc1De/H/1PUo+ozTwpqELCCCAQI4CZgKzKSjlXw65Dcov7pZjcywSY0bAsABdQyAbAROBKd+lnEzWn1HeBuXskfzLIdlUgoEigAACCJgWMBGYEpCiJNvHxcMnt0H5t2t5jYYAAggg0FKAwzoVMBGYb19/8UCWXiUoLy4++7bTEXIyBBBAAAEEOhAwEZgdjINTIIAAAggg0KtAx4HZa185OQIIIIAAAqMJEJij0XNhBBBAAIGYBAjMmKp1ZF85HAEEEECgvQCB2d6OIxFAAAEEMhIgMDMqNkO1LEDfEEDAugCBab1C9A8BBBBAwIQAgWmiDHQCAQQsC9A3BESAwBQFGgIIIIAAAnsECMw9QPwaAQQQQMCywHB9IzCHs+ZKCCCAAAIRCxCYERePriOAAAIIDCfwAwAAAP///ujIAQAAAAZJREFUAwCUZjWCaZ2KtQAAAABJRU5ErkJggg==', '2026-08-31 14:22:13', '2026-08-31 14:15:58', '2026-08-31 14:22:13'),
(24, 4, 5, 'vpaa', 5, 'VPAA Sign-off', 'vpaa', 'Approved', 992, 'User', NULL, 'data:image/png;base64,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', '2026-08-31 14:23:03', '2026-08-31 14:15:58', '2026-08-31 16:57:24'),
(25, 4, 5, 'finance', 6, 'Finance Office', 'finance', 'Approved', 4, 'User', NULL, 'data:image/png;base64,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', '2026-08-31 14:30:05', '2026-08-31 14:15:58', '2026-08-31 16:57:24');

-- --------------------------------------------------------

--
-- Table structure for table `grant_proposal_approval_workflows`
--

CREATE TABLE `grant_proposal_approval_workflows` (
  `id` int(10) UNSIGNED NOT NULL,
  `grant_application_id` int(10) UNSIGNED NOT NULL,
  `current_step_key` varchar(40) NOT NULL DEFAULT 'adviser',
  `workflow_status` enum('In Progress','Completed','Returned') NOT NULL DEFAULT 'In Progress',
  `started_at` datetime NOT NULL DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grant_proposal_approval_workflows`
--

INSERT INTO `grant_proposal_approval_workflows` (`id`, `grant_application_id`, `current_step_key`, `workflow_status`, `started_at`, `completed_at`, `updated_at`) VALUES
(1, 2, 'vpaa', 'Completed', '2026-08-31 08:47:21', '2026-08-31 11:07:08', '2026-08-31 11:07:08'),
(2, 3, 'finance', 'Completed', '2026-08-31 11:44:23', '2026-08-31 12:13:46', '2026-08-31 12:13:46'),
(3, 4, 'finance', 'In Progress', '2026-08-31 13:34:26', NULL, '2026-08-31 13:59:06'),
(4, 5, 'finance', 'Completed', '2026-08-31 14:15:58', '2026-08-31 14:30:05', '2026-08-31 14:30:05');

-- --------------------------------------------------------

--
-- Table structure for table `grant_proposal_evaluations`
--

CREATE TABLE `grant_proposal_evaluations` (
  `id` int(10) UNSIGNED NOT NULL,
  `grant_application_id` int(10) UNSIGNED NOT NULL,
  `proposal_version` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Proposal version evaluated',
  `evaluator_user_id` int(10) UNSIGNED NOT NULL,
  `evaluator_name` varchar(150) NOT NULL DEFAULT '',
  `evaluation_type` varchar(20) NOT NULL DEFAULT 'committee' COMMENT 'committee | adviser',
  `score_rationale` decimal(5,2) NOT NULL DEFAULT 0.00,
  `score_methodology` decimal(5,2) NOT NULL DEFAULT 0.00,
  `score_budget` decimal(5,2) NOT NULL DEFAULT 0.00,
  `score_team_capability` decimal(5,2) NOT NULL DEFAULT 0.00,
  `score_compliance` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `comments` text DEFAULT NULL,
  `recommendations` text DEFAULT NULL,
  `required_corrections` text DEFAULT NULL,
  `recommendation` varchar(40) DEFAULT NULL COMMENT 'Reviewer decision: disapprove | require_revisions',
  `revision_reason` text DEFAULT NULL COMMENT 'Reason for required revisions',
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grant_proposal_evaluations`
--

INSERT INTO `grant_proposal_evaluations` (`id`, `grant_application_id`, `proposal_version`, `evaluator_user_id`, `evaluator_name`, `evaluation_type`, `score_rationale`, `score_methodology`, `score_budget`, `score_team_capability`, `score_compliance`, `total_score`, `comments`, `recommendations`, `required_corrections`, `recommendation`, `revision_reason`, `submitted_at`, `updated_at`) VALUES
(1, 1, 1, 766, 'Review Committee Member', 'committee', 25.00, 30.00, 19.00, 10.00, 5.00, 89.00, 'asdsa', 'asdas', 'asdas', NULL, NULL, '2026-08-31 07:27:00', '2026-08-31 07:27:00'),
(2, 2, 1, 766, 'Review Committee Member', 'committee', 13.00, 13.00, 13.00, 13.00, 1.00, 53.00, 'sadas', 'asdsa', 'asdas', 'require_revisions', 'sadas', '2026-08-31 08:09:44', '2026-08-31 08:09:44'),
(3, 2, 2, 766, 'Review Committee Member', 'committee', 25.00, 25.00, 20.00, 15.00, 10.00, 95.00, 'sadas', 'asdsa', 'adsaa', 'recommend', NULL, '2026-08-31 08:47:21', '2026-08-31 08:47:21'),
(4, 2, 2, 54, 'Dr. Roberto M. Santos', 'adviser', 20.00, 30.00, 20.00, 10.00, 10.00, 90.00, 'sdasd', 'sdasd', 'dsadsa', NULL, NULL, '2026-08-31 11:03:24', '2026-08-31 11:03:24'),
(5, 3, 1, 766, 'Review Committee Member', 'committee', 10.00, 30.00, 20.00, 15.00, 10.00, 85.00, 'adsa', 'c', 'ads', 'recommend', NULL, '2026-08-31 11:44:23', '2026-08-31 11:44:23'),
(6, 3, 1, 54, 'Dr. Roberto M. Santos', 'adviser', 10.00, 10.00, 10.00, 10.00, 10.00, 50.00, 'sdasa', 'sadsa', 'sdasda', NULL, NULL, '2026-08-31 12:02:01', '2026-08-31 12:02:01'),
(7, 4, 1, 766, 'Review Committee Member', 'committee', 25.00, 25.00, 20.00, 15.00, 10.00, 95.00, 'sA', 'ASDAS', 'SDA', 'recommend', NULL, '2026-08-31 13:34:26', '2026-08-31 13:34:26'),
(8, 4, 1, 54, 'Dr. Roberto M. Santos', 'adviser', 25.00, 30.00, 20.00, 15.00, 10.00, 100.00, NULL, NULL, NULL, NULL, NULL, '2026-08-31 13:44:17', '2026-08-31 13:44:17'),
(9, 4, 1, 990, 'Department Chair', 'department_chair', 25.00, 30.00, 20.00, 15.00, 10.00, 100.00, NULL, NULL, NULL, NULL, NULL, '2026-08-31 13:56:36', '2026-08-31 13:56:36'),
(10, 4, 1, 8, 'Dean', 'dean', 25.00, 30.00, 20.00, 15.00, 10.00, 100.00, NULL, NULL, NULL, NULL, NULL, '2026-08-31 13:57:28', '2026-08-31 13:57:28'),
(11, 4, 1, 991, 'Research Office', 'research_office', 10.00, 30.00, 20.00, 15.00, 10.00, 85.00, NULL, NULL, NULL, NULL, NULL, '2026-08-31 13:58:16', '2026-08-31 13:58:16'),
(12, 4, 1, 992, 'VPAA', 'vpaa', 25.00, 30.00, 20.00, 15.00, 10.00, 100.00, NULL, NULL, NULL, NULL, NULL, '2026-08-31 13:59:01', '2026-08-31 13:59:01'),
(13, 5, 1, 766, 'Review Committee Member', 'committee', 25.00, 30.00, 20.00, 15.00, 10.00, 100.00, NULL, NULL, NULL, 'recommend', NULL, '2026-08-31 14:15:58', '2026-08-31 14:15:58'),
(14, 5, 1, 54, 'Dr. Roberto M. Santos', 'adviser', 25.00, 30.00, 20.00, 15.00, 10.00, 100.00, NULL, NULL, NULL, 'recommend', NULL, '2026-08-31 14:16:46', '2026-08-31 14:16:46'),
(15, 5, 1, 990, 'Department Chair', 'department_chair', 25.00, 30.00, 20.00, 15.00, 10.00, 100.00, NULL, NULL, NULL, 'recommend', NULL, '2026-08-31 14:19:03', '2026-08-31 14:19:03'),
(16, 5, 1, 8, 'Dean', 'dean', 25.00, 25.00, 20.00, 15.00, 4.00, 89.00, NULL, NULL, NULL, 'recommend', NULL, '2026-08-31 14:21:15', '2026-08-31 14:21:15'),
(17, 5, 1, 991, 'Research Office', 'research_office', 25.00, 30.00, 20.00, 15.00, 10.00, 100.00, NULL, NULL, NULL, 'recommend', NULL, '2026-08-31 14:22:05', '2026-08-31 14:22:05'),
(18, 5, 1, 992, 'VPAA', 'vpaa', 25.00, 30.00, 20.00, 15.00, 10.00, 100.00, NULL, NULL, NULL, 'recommend', NULL, '2026-08-31 14:22:52', '2026-08-31 14:22:52'),
(19, 5, 1, 4, 'Finance', 'finance', 25.00, 30.00, 20.00, 15.00, 10.00, 100.00, NULL, NULL, NULL, 'recommend', NULL, '2026-08-31 14:29:57', '2026-08-31 14:29:57');

-- --------------------------------------------------------

--
-- Table structure for table `grant_proposal_notifications`
--

CREATE TABLE `grant_proposal_notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_key` varchar(120) NOT NULL,
  `recipient_user_id` int(10) UNSIGNED DEFAULT NULL,
  `recipient_role` varchar(40) NOT NULL DEFAULT '',
  `recipient_email` varchar(190) NOT NULL DEFAULT '',
  `grant_application_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(40) NOT NULL DEFAULT '',
  `title` varchar(200) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `url` varchar(500) NOT NULL DEFAULT '',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grant_proposal_notifications`
--

INSERT INTO `grant_proposal_notifications` (`id`, `event_key`, `recipient_user_id`, `recipient_role`, `recipient_email`, `grant_application_id`, `type`, `title`, `body`, `url`, `is_read`, `created_at`) VALUES
(1, 'grant-proposal:grant_revision_required:2:v1:u9', 9, '', '', 2, 'grant_revision_required', 'Revise Grant Proposal', 'GR-2026-001 requires revisions. sadas Tap to revise and resubmit.', '/sms2_system/modules/crad/pages/revise-proposal.php?id=2', 1, '2026-08-31 08:46:38'),
(79, 'grant-proposal:grant_approved_funded:3:v1:u9', 9, 'student', '', 3, 'grant_approved_funded', 'Approved & Funded', 'GR-2026-001 (asdsadasdsadas) is APPROVED & FUNDED after all six institutional sign-offs. Finance Office recorded the final approval.', '/sms2_system/modules/crad/modules/crad/pages/approved-funded.php', 0, '2026-08-31 12:13:46'),
(80, 'grant-proposal:finance_pending:4:u4', 4, 'finance', '', 4, 'grant_finance_pending', 'Pending Finance Approval', 'GR-2026-001 is pending Finance Office final approval after VPAA sign-off. Review it under Payment Management → Approval Workflows. Title: CHATGPT 5.5', '/sms2_system/modules/payment/pages/approval-workflows.php?id=4', 0, '2026-08-31 13:59:06'),
(81, 'grant-proposal:finance_pending:5:u4', 4, 'finance', '', 5, 'grant_finance_pending', 'Pending Finance Approval', 'GR-2026-001 is pending Finance Office final approval after VPAA sign-off. Review it under Payment Management → Approval Workflows. Title: ai analysis', '/sms2_system/modules/payment/pages/approval-workflows.php?id=5', 0, '2026-08-31 14:23:03'),
(82, 'grant-proposal:grant_approved_funded:5:v1:u9', 9, 'student', '', 5, 'grant_approved_funded', 'Approved & Funded', 'GR-2026-001 (ai analysis) is APPROVED & FUNDED after all six institutional sign-offs. Finance Office recorded the final approval.', '/sms2_system/modules/crad/pages/approved-funded.php', 1, '2026-08-31 14:30:05'),
(83, 'grant-proposal:grant_fund_release:5:t1:d1', 9, 'student', '', 5, 'grant_fund_release', 'Fund Tranche Released', 'GR-2026-001 — Tranche 1 released ₱7,500 (Ref: DISB-GR-2026-001-T1). Recorded by User. View Budget & Disbursement for tranche status.', '/sms2_system/modules/crad/pages/budget-disbursement.php?id=5', 0, '2026-08-31 14:57:57'),
(84, 'grant-proposal:grant_fund_release:5:t2:d2', 9, 'student', '', 5, 'grant_fund_release', 'Fund Tranche Released', 'GR-2026-001 — Tranche 2 released ₱7,500 (Ref: DISB-GR-2026-001-T2). Recorded by User. View Budget & Disbursement for tranche status.', '/sms2_system/modules/crad/pages/budget-disbursement.php?id=5', 1, '2026-08-31 14:59:27'),
(85, 'grant-proposal:grant_milestone_update:5:m2:scompleted:p100', 9, 'student', '', 5, 'grant_milestone_update', 'Milestone Updated', 'GR-2026-001 — Data Gathering updated to Completed (100%). Updated by User. View Funded Research for timeline and requirements.', '/sms2_system/modules/crad/pages/funded-research.php?id=5', 0, '2026-08-31 16:08:16'),
(87, 'grant-proposal:grant_milestone_update:5:m3:scompleted:p100', 9, 'student', '', 5, 'grant_milestone_update', 'Milestone Updated', 'GR-2026-001 — Analysis updated to Completed (100%). Updated by User. View Funded Research for timeline and requirements.', '/sms2_system/modules/crad/pages/funded-research.php?id=5', 0, '2026-08-31 16:09:47'),
(89, 'grant-proposal:grant_milestone_update:5:m4:scompleted:p100', 9, 'student', '', 5, 'grant_milestone_update', 'Milestone Updated', 'GR-2026-001 — Final Report updated to Completed (100%). Updated by User. View Funded Research for timeline and requirements.', '/sms2_system/modules/crad/pages/funded-research.php?id=5', 0, '2026-08-31 16:10:01'),
(91, 'grant-proposal:grant_milestone_update:5:m5:scompleted:p100', 9, 'student', '', 5, 'grant_milestone_update', 'Milestone Updated', 'GR-2026-001 — Publication updated to Completed (100%). Updated by User. View Funded Research for timeline and requirements.', '/sms2_system/modules/crad/pages/funded-research.php?id=5', 0, '2026-08-31 16:10:32'),
(93, 'grant-proposal:final_output_submitted:5:u3', 3, 'crad_officer', '', 5, 'grant_final_output_submitted', 'Final Output Submitted', 'GR-2026-001 — final output submitted by User. Title: ai analysis. Verify under Outputs & Records → Publications & IP.', '/sms2_system/modules/crad/pages/publications-ip.php?id=5', 0, '2026-08-31 16:38:45'),
(94, 'grant-proposal:final_output_verified:5', 9, 'student', '', 5, 'grant_final_output_verified', 'Output Verified', 'GR-2026-001 — your final output has been verified (OUTPUT_VERIFIED) and recorded in the Publications & IP Repository (PIP-2026-001). Verified by User. Proceed to Document Repository for permanent archiving.', '/sms2_system/modules/crad/pages/publications-ip.php?id=5', 0, '2026-08-31 16:40:51');

-- --------------------------------------------------------

--
-- Table structure for table `grant_proposal_versions`
--

CREATE TABLE `grant_proposal_versions` (
  `id` int(10) UNSIGNED NOT NULL,
  `grant_application_id` int(10) UNSIGNED NOT NULL,
  `version_number` int(10) UNSIGNED NOT NULL,
  `version_label` varchar(60) NOT NULL DEFAULT '',
  `proposal_pdf` varchar(255) DEFAULT NULL,
  `proposal_pdf_original` varchar(300) DEFAULT NULL,
  `supporting_docs` varchar(255) DEFAULT NULL,
  `supporting_docs_original` varchar(300) DEFAULT NULL,
  `ethics_doc` varchar(255) DEFAULT NULL,
  `ethics_doc_original` varchar(300) DEFAULT NULL,
  `abstract` text DEFAULT NULL,
  `objectives` text DEFAULT NULL,
  `researcher_notes` text DEFAULT NULL,
  `submitted_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grant_proposal_versions`
--

INSERT INTO `grant_proposal_versions` (`id`, `grant_application_id`, `version_number`, `version_label`, `proposal_pdf`, `proposal_pdf_original`, `supporting_docs`, `supporting_docs_original`, `ethics_doc`, `ethics_doc_original`, `abstract`, `objectives`, `researcher_notes`, `submitted_by_user_id`, `submitted_at`) VALUES
(1, 1, 1, 'Original', '53ac796ed0ad7af6ca74703f6e4a5b41.pdf', 'OLIVEROS CV.pdf', '72706d8b5bfd82f1135557fa92faf84a.pdf', 'OLIVEROS CV.pdf', '3b1bf340d2db787753a7f77efb0fcdaf.pdf', 'OLIVEROS CV.pdf', 'adsadas', 'adsadas', NULL, 9, '2026-08-31 07:42:38'),
(2, 2, 1, 'Original', 'b594409c59e3ba6839050c9dd63f50a6.pdf', 'OLIVEROS CV.pdf', '6a9719b97b1c60266f543bba2f313c38.pdf', 'OLIVEROS CV.pdf', '757f2e0bf3a62bd565b415570d7df5e8.pdf', 'OLIVEROS CV.pdf', 'dsada', 'asdas', NULL, 9, '2026-08-31 08:46:40'),
(4, 2, 2, 'Revised', 'f3f3ec4b05c99f1fbbe58278c60d4afe.pdf', 'OLIVEROS CV.pdf', 'e411ba015c79a77a27013c4206313257.pdf', 'OLIVEROS CV.pdf', '95ae712a926e7a6e5ceef81ed3ba0f14.pdf', 'OLIVEROS CV.pdf', 'dsada', 'asdas', 'sada', 9, '2026-08-31 08:46:40'),
(5, 3, 1, 'Original', 'bf397c70c1ea835a393e6b25aceac34d.pdf', 'OLIVEROS CV.pdf', '57aae4180acef208c0d86635d413673a.pdf', 'OLIVEROS CV.pdf', '11fdbc98c4857488b1153c1b35f2ae6f.pdf', 'OLIVEROS CV.pdf', 'sadasd', 'asdas', NULL, 9, '2026-08-31 11:43:42'),
(6, 4, 1, 'Original', '554ef2a9aab43ba9626415abd863c06d.pdf', 'OLIVEROS CV.pdf', 'e756dec1b73523d3fadc516b13c8b8df.pdf', 'OLIVEROS CV.pdf', '94c401e14d58b14cf3ea870b076aa98d.pdf', 'OLIVEROS CV.pdf', 'asdas', 'asdsa', NULL, 9, '2026-08-31 13:32:27'),
(7, 5, 1, 'Original', '6d7a59295ee3d7cd325d51cdb880d11c.pdf', 'OLIVEROS CV.pdf', '2e8ab8d2fe5b210a8ebd034054e70bc1.pdf', 'OLIVEROS CV.pdf', '2eb694566c7d8741b6189a97af16dfd8.pdf', 'OLIVEROS CV.pdf', 'dsda', 'sdas', NULL, 9, '2026-08-31 14:11:48');

-- --------------------------------------------------------

--
-- Table structure for table `grant_publications_ip_repository`
--

CREATE TABLE `grant_publications_ip_repository` (
  `id` int(10) UNSIGNED NOT NULL,
  `grant_application_id` int(10) UNSIGNED NOT NULL,
  `submission_id` int(10) UNSIGNED NOT NULL,
  `repository_reference` varchar(40) NOT NULL DEFAULT '',
  `final_research_title` varchar(500) NOT NULL DEFAULT '',
  `authors` varchar(500) NOT NULL DEFAULT '',
  `abstract` text DEFAULT NULL,
  `publication_type` varchar(60) NOT NULL DEFAULT '',
  `journal_conference` varchar(255) NOT NULL DEFAULT '',
  `doi` varchar(120) NOT NULL DEFAULT '',
  `publication_url` varchar(500) NOT NULL DEFAULT '',
  `ip_information` text DEFAULT NULL,
  `copyright_info` text DEFAULT NULL,
  `patent_info` text DEFAULT NULL,
  `other_ip_info` text DEFAULT NULL,
  `final_pdf_path` varchar(255) DEFAULT NULL,
  `final_pdf_original` varchar(255) DEFAULT NULL,
  `supporting_files_json` text DEFAULT NULL,
  `verified_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `verified_by_name` varchar(120) DEFAULT NULL,
  `verified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grant_publications_ip_repository`
--

INSERT INTO `grant_publications_ip_repository` (`id`, `grant_application_id`, `submission_id`, `repository_reference`, `final_research_title`, `authors`, `abstract`, `publication_type`, `journal_conference`, `doi`, `publication_url`, `ip_information`, `copyright_info`, `patent_info`, `other_ip_info`, `final_pdf_path`, `final_pdf_original`, `supporting_files_json`, `verified_by_user_id`, `verified_by_name`, `verified_at`, `created_at`) VALUES
(1, 5, 1, 'PIP-2026-001', 'ai analysis', 'Student User', 'adasdsada', 'Journal', 'asdsa', '', 'http://localhost/sms2_system/modules/crad/pages/publications-ip.php', 'sadas', NULL, NULL, NULL, 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/grant_final_output/20fbebfce55289306d6bfbb63c4e3777.pdf', 'OLIVEROS CV.pdf', '[{\"path\":\"C:\\\\xampp\\\\htdocs\\\\sms2_system\\/storage\\/uploads\\/grant_final_output_supporting\\/39c7dccbc6b58439374010ce48077be1.pdf\",\"original_name\":\"Diaz CV.pdf_20260813_105004_0000.pdf\",\"stored_name\":\"39c7dccbc6b58439374010ce48077be1.pdf\"}]', 3, 'User', '2026-08-31 16:40:51', '2026-08-31 16:40:51');

-- --------------------------------------------------------

--
-- Table structure for table `manuscript_evaluations`
--

CREATE TABLE `manuscript_evaluations` (
  `id` int(10) UNSIGNED NOT NULL,
  `submission_id` int(10) UNSIGNED NOT NULL,
  `research_group_id` int(10) UNSIGNED NOT NULL,
  `evaluator_user_id` int(10) UNSIGNED NOT NULL,
  `evaluator_name` varchar(150) NOT NULL DEFAULT '',
  `content_score` decimal(5,2) NOT NULL,
  `methodology_score` decimal(5,2) NOT NULL,
  `results_score` decimal(5,2) NOT NULL,
  `conclusions_score` decimal(5,2) NOT NULL,
  `recommendations_score` decimal(5,2) NOT NULL,
  `references_score` decimal(5,2) NOT NULL,
  `formatting_score` decimal(5,2) NOT NULL,
  `compliance_score` decimal(5,2) NOT NULL,
  `remarks` text DEFAULT NULL,
  `result` enum('APPROVED','FOR REVISION') NOT NULL,
  `overall_score` decimal(5,2) NOT NULL,
  `evaluated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `manuscript_evaluations`
--

INSERT INTO `manuscript_evaluations` (`id`, `submission_id`, `research_group_id`, `evaluator_user_id`, `evaluator_name`, `content_score`, `methodology_score`, `results_score`, `conclusions_score`, `recommendations_score`, `references_score`, `formatting_score`, `compliance_score`, `remarks`, `result`, `overall_score`, `evaluated_at`, `created_at`) VALUES
(1, 0, 61, 3, '', 99.00, 99.00, 99.00, 99.00, 99.00, 99.00, 99.00, 99.00, '99', 'APPROVED', 99.00, '2026-08-28 08:03:48', '2026-08-28 08:03:48'),
(2, 2, 62, 3, '', 100.00, 100.00, 100.00, 100.00, 100.00, 100.00, 100.00, 100.00, '100', 'APPROVED', 100.00, '2026-08-28 15:45:48', '2026-08-28 15:45:48'),
(3, 3, 63, 3, '', 100.00, 100.00, 100.00, 100.00, 100.00, 100.00, 100.00, 100.00, '100', 'APPROVED', 100.00, '2026-08-28 16:35:30', '2026-08-28 16:35:30'),
(4, 4, 64, 3, '', 100.00, 100.00, 100.00, 100.00, 100.00, 100.00, 100.00, 100.00, '', 'APPROVED', 100.00, '2026-08-31 06:17:25', '2026-08-31 06:17:25');

-- --------------------------------------------------------

--
-- Table structure for table `manuscript_submissions`
--

CREATE TABLE `manuscript_submissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `research_group_id` int(10) UNSIGNED NOT NULL,
  `version_number` int(10) UNSIGNED NOT NULL,
  `status` enum('Submitted','Under Review','For Revision','Approved') NOT NULL DEFAULT 'Submitted',
  `submitted_by_user` int(10) UNSIGNED DEFAULT NULL,
  `submitted_by_name` varchar(150) NOT NULL DEFAULT '',
  `submitted_by_email` varchar(190) NOT NULL DEFAULT '',
  `submission_notes` text DEFAULT NULL,
  `original_name` varchar(255) NOT NULL DEFAULT '',
  `stored_subdir` varchar(180) NOT NULL DEFAULT '',
  `stored_name` varchar(120) NOT NULL DEFAULT '',
  `file_size` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `file_mime` varchar(120) NOT NULL DEFAULT '',
  `submission_token` varchar(64) NOT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `manuscript_submissions`
--

INSERT INTO `manuscript_submissions` (`id`, `research_group_id`, `version_number`, `status`, `submitted_by_user`, `submitted_by_name`, `submitted_by_email`, `submission_notes`, `original_name`, `stored_subdir`, `stored_name`, `file_size`, `file_mime`, `submission_token`, `submitted_at`, `reviewed_at`, `updated_at`) VALUES
(1, 61, 1, 'Approved', 9, '', 'kenlangmalakas0308@gmail.com', '', 'CRAD_Chapter_1_TO_4 (1).docx', 'manuscripts/g61', '059624a21b3ee3e8c9d5d6d6cae4d3d8.docx', 4824980, 'application/octet-stream', 'c0ba01bd7d587e02c4aac89baad005b9817863f67e132d46c53fd5a8e2f202b8', '2026-08-28 08:01:38', '2026-08-28 08:03:48', '2026-08-28 08:03:48'),
(2, 62, 1, 'Approved', 9, '', 'kenlangmalakas0308@gmail.com', '', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'manuscripts/g62', 'cd889c4f0fa3737edd9c7075cae72eb5.docx', 302605, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'ac45628a1a25f5f177bbcace7b945c684229993d6462e2d2dba7551d88a01198', '2026-08-28 15:45:09', '2026-08-28 15:45:48', '2026-08-28 15:45:48'),
(3, 63, 1, 'Approved', 9, '', 'kenlangmalakas0308@gmail.com', '', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'manuscripts/g63', 'c4d2449c98f39a5604fedf92ac2cc70e.docx', 302605, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '4be87c933edc484ef2cddf1a86daa6e4521cc149c28b66b278d67db825ffdd44', '2026-08-28 16:34:34', '2026-08-28 16:35:30', '2026-08-28 16:35:30'),
(4, 64, 1, 'Approved', 9, '', 'kenlangmalakas0308@gmail.com', '', 'OLIVEROS CV.pdf', 'manuscripts/g64', '5fa67590cfadb682bbf15dcef84b36d5.pdf', 294354, 'application/pdf', '750a785eb19895c7c3df2b3ff9ed349d8f57813089bb7bdbe2203b7228ca27db', '2026-08-31 06:16:23', '2026-08-31 06:17:25', '2026-08-31 06:17:25');

-- --------------------------------------------------------

--
-- Table structure for table `panel_assignment_notifications`
--

CREATE TABLE `panel_assignment_notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_key` varchar(140) NOT NULL,
  `recipient_user_id` int(10) UNSIGNED NOT NULL,
  `recipient_role` varchar(60) NOT NULL DEFAULT 'panel',
  `recipient_email` varchar(190) NOT NULL DEFAULT '',
  `panel_assignment_id` int(10) UNSIGNED DEFAULT NULL,
  `research_group_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(160) NOT NULL DEFAULT '',
  `body` text DEFAULT NULL,
  `url` varchar(500) NOT NULL DEFAULT '',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `panel_assignment_notifications`
--

INSERT INTO `panel_assignment_notifications` (`id`, `event_key`, `recipient_user_id`, `recipient_role`, `recipient_email`, `panel_assignment_id`, `research_group_id`, `title`, `body`, `url`, `is_read`, `created_at`) VALUES
(19, 'preoral-panel-assignment:61:u491', 491, 'panel', 'jobertvalentino@bestlink.edu.ph', 13, 61, 'Pre-Oral Panel Assignment', 'You have been assigned as a Panel Member for Group 01\nDEVELOPMENT OF AI ANALYSIS\nDefense Phase: Pre-Oral Defense', '/sms2_system/modules/faculty/pages/assigned-defenses.php?group=RG-2026-001', 0, '2026-08-23 04:09:43'),
(20, 'preoral-panel-assignment:61:u492', 492, 'panel', 'jonathanestrada@bestlink.edu.ph', 14, 61, 'Pre-Oral Panel Assignment', 'You have been assigned as a Panel Member for Group 01\nDEVELOPMENT OF AI ANALYSIS\nDefense Phase: Pre-Oral Defense', '/sms2_system/modules/faculty/pages/assigned-defenses.php?group=RG-2026-001', 0, '2026-08-23 04:09:43'),
(21, 'preoral-panel-assignment:61:u493', 493, 'panel', 'michelleguevarra@bestlink.edu.ph', 15, 61, 'Pre-Oral Panel Assignment', 'You have been assigned as a Panel Member for Group 01\nDEVELOPMENT OF AI ANALYSIS\nDefense Phase: Pre-Oral Defense', '/sms2_system/modules/faculty/pages/assigned-defenses.php?group=RG-2026-001', 0, '2026-08-23 04:09:43'),
(22, 'preoral-defense-finalized:s30:u491', 491, 'panel', 'jobertvalentino@bestlink.edu.ph', 13, 61, 'Pre-Oral Defense Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI ANALYSIS\nDate/Time: Aug 25, 2026 01:00 PM - 02:00 PM\nVenue: Computer Laboratory 1', '/sms2_system/modules/faculty/pages/defense-details.php?id=30', 0, '2026-08-23 04:10:47'),
(23, 'preoral-defense-finalized:s30:u492', 492, 'panel', 'jonathanestrada@bestlink.edu.ph', 14, 61, 'Pre-Oral Defense Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI ANALYSIS\nDate/Time: Aug 25, 2026 01:00 PM - 02:00 PM\nVenue: Computer Laboratory 1', '/sms2_system/modules/faculty/pages/defense-details.php?id=30', 0, '2026-08-23 04:10:47'),
(24, 'preoral-defense-finalized:s30:u493', 493, 'panel', 'michelleguevarra@bestlink.edu.ph', 15, 61, 'Pre-Oral Defense Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI ANALYSIS\nDate/Time: Aug 25, 2026 01:00 PM - 02:00 PM\nVenue: Computer Laboratory 1', '/sms2_system/modules/faculty/pages/defense-details.php?id=30', 0, '2026-08-23 04:10:47'),
(25, 'preoral-defense-finalized:s32:u491', 491, 'panel', 'jobertvalentino@bestlink.edu.ph', 13, 61, 'Pre-Oral Defense Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI ANALYSIS\nDate/Time: Aug 29, 2026 10:15 AM - 11:16 AM\nVenue: Computer Laboratory 1', '/sms2-capstone-main/sms2-capstone-main/modules/faculty/pages/defense-details.php?id=32', 0, '2026-08-28 08:21:42'),
(26, 'preoral-defense-finalized:s32:u492', 492, 'panel', 'jonathanestrada@bestlink.edu.ph', 14, 61, 'Pre-Oral Defense Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI ANALYSIS\nDate/Time: Aug 29, 2026 10:15 AM - 11:16 AM\nVenue: Computer Laboratory 1', '/sms2-capstone-main/sms2-capstone-main/modules/faculty/pages/defense-details.php?id=32', 0, '2026-08-28 08:21:42'),
(27, 'preoral-defense-finalized:s32:u493', 493, 'panel', 'michelleguevarra@bestlink.edu.ph', 15, 61, 'Pre-Oral Defense Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI ANALYSIS\nDate/Time: Aug 29, 2026 10:15 AM - 11:16 AM\nVenue: Computer Laboratory 1', '/sms2-capstone-main/sms2-capstone-main/modules/faculty/pages/defense-details.php?id=32', 0, '2026-08-28 08:21:42'),
(28, 'preoral-panel-assignment:62:u491', 491, 'panel', 'jobertvalentino@bestlink.edu.ph', 19, 62, 'Pre-Oral Panel Assignment', 'You have been assigned as a Panel Member for Group 01\nDEVELOPMENT OF AI ASSISTED DOCUMENT\nDefense Phase: Pre-Oral Defense', '/sms2_system/modules/faculty/pages/assigned-defenses.php?group=RG-2026-001', 0, '2026-08-28 15:34:33'),
(29, 'preoral-panel-assignment:62:u492', 492, 'panel', 'jonathanestrada@bestlink.edu.ph', 20, 62, 'Pre-Oral Panel Assignment', 'You have been assigned as a Panel Member for Group 01\nDEVELOPMENT OF AI ASSISTED DOCUMENT\nDefense Phase: Pre-Oral Defense', '/sms2_system/modules/faculty/pages/assigned-defenses.php?group=RG-2026-001', 0, '2026-08-28 15:34:33'),
(30, 'preoral-panel-assignment:62:u493', 493, 'panel', 'michelleguevarra@bestlink.edu.ph', 21, 62, 'Pre-Oral Panel Assignment', 'You have been assigned as a Panel Member for Group 01\nDEVELOPMENT OF AI ASSISTED DOCUMENT\nDefense Phase: Pre-Oral Defense', '/sms2_system/modules/faculty/pages/assigned-defenses.php?group=RG-2026-001', 0, '2026-08-28 15:34:33'),
(31, 'pre-oral-finalized:s36:u491', 491, 'panel', 'jobertvalentino@bestlink.edu.ph', 19, 62, 'Pre-Oral Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI ASSISTED DOCUMENT\nDate/Time: Sep 1, 2026 03:00 PM - 04:00 PM\nVenue: AVR Room', '/sms2_system/modules/faculty/pages/defense-details.php?id=36', 0, '2026-08-28 15:35:40'),
(32, 'pre-oral-finalized:s36:u492', 492, 'panel', 'jonathanestrada@bestlink.edu.ph', 20, 62, 'Pre-Oral Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI ASSISTED DOCUMENT\nDate/Time: Sep 1, 2026 03:00 PM - 04:00 PM\nVenue: AVR Room', '/sms2_system/modules/faculty/pages/defense-details.php?id=36', 0, '2026-08-28 15:35:40'),
(33, 'pre-oral-finalized:s36:u493', 493, 'panel', 'michelleguevarra@bestlink.edu.ph', 21, 62, 'Pre-Oral Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI ASSISTED DOCUMENT\nDate/Time: Sep 1, 2026 03:00 PM - 04:00 PM\nVenue: AVR Room', '/sms2_system/modules/faculty/pages/defense-details.php?id=36', 0, '2026-08-28 15:35:40'),
(34, 'final-defense-finalized:s38:u491', 491, 'panel', 'jobertvalentino@bestlink.edu.ph', 19, 62, 'Final Defense Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI ASSISTED DOCUMENT\nDate/Time: Sep 10, 2026 12:00 PM - 01:00 PM\nVenue: Computer Laboratory 1', '/sms2_system/modules/faculty/pages/defense-details.php?id=38', 0, '2026-08-28 15:52:23'),
(35, 'final-defense-finalized:s38:u492', 492, 'panel', 'jonathanestrada@bestlink.edu.ph', 20, 62, 'Final Defense Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI ASSISTED DOCUMENT\nDate/Time: Sep 10, 2026 12:00 PM - 01:00 PM\nVenue: Computer Laboratory 1', '/sms2_system/modules/faculty/pages/defense-details.php?id=38', 0, '2026-08-28 15:52:23'),
(36, 'final-defense-finalized:s38:u493', 493, 'panel', 'michelleguevarra@bestlink.edu.ph', 21, 62, 'Final Defense Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI ASSISTED DOCUMENT\nDate/Time: Sep 10, 2026 12:00 PM - 01:00 PM\nVenue: Computer Laboratory 1', '/sms2_system/modules/faculty/pages/defense-details.php?id=38', 0, '2026-08-28 15:52:23'),
(37, 'preoral-panel-assignment:63:u491', 491, 'panel', 'jobertvalentino@bestlink.edu.ph', 25, 63, 'Pre-Oral Panel Assignment', 'You have been assigned as a Panel Member for Group 01\nDEVELOPMENT OF AI\nDefense Phase: Pre-Oral Defense', '/sms2_system/modules/faculty/pages/assigned-defenses.php?group=RG-2026-001', 0, '2026-08-28 16:31:11'),
(38, 'preoral-panel-assignment:63:u492', 492, 'panel', 'jonathanestrada@bestlink.edu.ph', 26, 63, 'Pre-Oral Panel Assignment', 'You have been assigned as a Panel Member for Group 01\nDEVELOPMENT OF AI\nDefense Phase: Pre-Oral Defense', '/sms2_system/modules/faculty/pages/assigned-defenses.php?group=RG-2026-001', 0, '2026-08-28 16:31:11'),
(39, 'preoral-panel-assignment:63:u493', 493, 'panel', 'michelleguevarra@bestlink.edu.ph', 27, 63, 'Pre-Oral Panel Assignment', 'You have been assigned as a Panel Member for Group 01\nDEVELOPMENT OF AI\nDefense Phase: Pre-Oral Defense', '/sms2_system/modules/faculty/pages/assigned-defenses.php?group=RG-2026-001', 0, '2026-08-28 16:31:11'),
(40, 'pre-oral-finalized:s41:u491', 491, 'panel', 'jobertvalentino@bestlink.edu.ph', 25, 63, 'Pre-Oral Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI\nDate/Time: Aug 29, 2026 11:00 AM - 12:00 PM\nVenue: Computer Laboratory 1', '/sms2_system/modules/faculty/pages/defense-details.php?id=41', 0, '2026-08-28 16:32:15'),
(41, 'pre-oral-finalized:s41:u492', 492, 'panel', 'jonathanestrada@bestlink.edu.ph', 26, 63, 'Pre-Oral Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI\nDate/Time: Aug 29, 2026 11:00 AM - 12:00 PM\nVenue: Computer Laboratory 1', '/sms2_system/modules/faculty/pages/defense-details.php?id=41', 0, '2026-08-28 16:32:15'),
(42, 'pre-oral-finalized:s41:u493', 493, 'panel', 'michelleguevarra@bestlink.edu.ph', 27, 63, 'Pre-Oral Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI\nDate/Time: Aug 29, 2026 11:00 AM - 12:00 PM\nVenue: Computer Laboratory 1', '/sms2_system/modules/faculty/pages/defense-details.php?id=41', 0, '2026-08-28 16:32:15'),
(43, 'final-defense-finalized:s44:u491', 491, 'panel', 'jobertvalentino@bestlink.edu.ph', 25, 63, 'Final Defense Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI\nDate/Time: Aug 31, 2026 12:00 PM - 01:00 PM\nVenue: Computer Laboratory 1', '/sms2_system/modules/faculty/pages/defense-details.php?id=44', 0, '2026-08-28 16:37:05'),
(44, 'final-defense-finalized:s44:u492', 492, 'panel', 'jonathanestrada@bestlink.edu.ph', 26, 63, 'Final Defense Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI\nDate/Time: Aug 31, 2026 12:00 PM - 01:00 PM\nVenue: Computer Laboratory 1', '/sms2_system/modules/faculty/pages/defense-details.php?id=44', 0, '2026-08-28 16:37:05'),
(45, 'final-defense-finalized:s44:u493', 493, 'panel', 'michelleguevarra@bestlink.edu.ph', 27, 63, 'Final Defense Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI\nDate/Time: Aug 31, 2026 12:00 PM - 01:00 PM\nVenue: Computer Laboratory 1', '/sms2_system/modules/faculty/pages/defense-details.php?id=44', 0, '2026-08-28 16:37:05'),
(46, 'preoral-panel-assignment:64:u491', 491, 'panel', 'jobertvalentino@bestlink.edu.ph', 31, 64, 'Pre-Oral Panel Assignment', 'You have been assigned as a Panel Member for Group 01\nDEVELOPMENT OF AI ANALYSIS\nDefense Phase: Pre-Oral Defense', '/sms2_system/modules/faculty/pages/assigned-defenses.php?group=RG-2026-001', 0, '2026-08-31 05:34:23'),
(47, 'preoral-panel-assignment:64:u492', 492, 'panel', 'jonathanestrada@bestlink.edu.ph', 32, 64, 'Pre-Oral Panel Assignment', 'You have been assigned as a Panel Member for Group 01\nDEVELOPMENT OF AI ANALYSIS\nDefense Phase: Pre-Oral Defense', '/sms2_system/modules/faculty/pages/assigned-defenses.php?group=RG-2026-001', 0, '2026-08-31 05:34:23'),
(48, 'preoral-panel-assignment:64:u493', 493, 'panel', 'michelleguevarra@bestlink.edu.ph', 33, 64, 'Pre-Oral Panel Assignment', 'You have been assigned as a Panel Member for Group 01\nDEVELOPMENT OF AI ANALYSIS\nDefense Phase: Pre-Oral Defense', '/sms2_system/modules/faculty/pages/assigned-defenses.php?group=RG-2026-001', 0, '2026-08-31 05:34:23'),
(52, 'pre-oral-finalized:s48:u491', 491, 'panel', 'jobertvalentino@bestlink.edu.ph', 31, 64, 'Pre-Oral Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI ANALYSIS\nDate/Time: Aug 31, 2026 01:00 PM - 02:00 PM\nVenue: AVR Room', '/sms2_system/modules/faculty/pages/defense-details.php?id=48', 0, '2026-08-31 06:10:27'),
(53, 'pre-oral-finalized:s48:u492', 492, 'panel', 'jonathanestrada@bestlink.edu.ph', 32, 64, 'Pre-Oral Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI ANALYSIS\nDate/Time: Aug 31, 2026 01:00 PM - 02:00 PM\nVenue: AVR Room', '/sms2_system/modules/faculty/pages/defense-details.php?id=48', 0, '2026-08-31 06:10:27'),
(54, 'pre-oral-finalized:s48:u493', 493, 'panel', 'michelleguevarra@bestlink.edu.ph', 33, 64, 'Pre-Oral Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI ANALYSIS\nDate/Time: Aug 31, 2026 01:00 PM - 02:00 PM\nVenue: AVR Room', '/sms2_system/modules/faculty/pages/defense-details.php?id=48', 0, '2026-08-31 06:10:27'),
(55, 'final-defense-finalized:s50:u491', 491, 'panel', 'jobertvalentino@bestlink.edu.ph', 31, 64, 'Final Defense Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI ANALYSIS\nDate/Time: Sep 3, 2026 11:00 AM - 12:00 PM\nVenue: Computer Laboratory 1', '/sms2_system/modules/faculty/pages/defense-details.php?id=50', 0, '2026-08-31 06:22:35'),
(56, 'final-defense-finalized:s50:u492', 492, 'panel', 'jonathanestrada@bestlink.edu.ph', 32, 64, 'Final Defense Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI ANALYSIS\nDate/Time: Sep 3, 2026 11:00 AM - 12:00 PM\nVenue: Computer Laboratory 1', '/sms2_system/modules/faculty/pages/defense-details.php?id=50', 0, '2026-08-31 06:22:35'),
(57, 'final-defense-finalized:s50:u493', 493, 'panel', 'michelleguevarra@bestlink.edu.ph', 33, 64, 'Final Defense Scheduled', 'RG-2026-001\nDEVELOPMENT OF AI ANALYSIS\nDate/Time: Sep 3, 2026 11:00 AM - 12:00 PM\nVenue: Computer Laboratory 1', '/sms2_system/modules/faculty/pages/defense-details.php?id=50', 0, '2026-08-31 06:22:35'),
(58, 'preoral-panel-assignment:65:u491', 491, 'panel', 'jobert.valentino@bestlink.edu.ph', 37, 65, 'Pre-Oral Panel Assignment', 'You have been assigned as a Panel Member for Group 01\nDEVELOPMENT OF AI ANALYSIS\nDefense Phase: Pre-Oral Defense', '/sms2_system/modules/faculty/pages/assigned-defenses.php?group=RG-2026-001', 0, '2026-08-31 09:43:31'),
(59, 'preoral-panel-assignment:65:u492', 492, 'panel', 'jonathan.estrada@bestlink.edu.ph', 38, 65, 'Pre-Oral Panel Assignment', 'You have been assigned as a Panel Member for Group 01\nDEVELOPMENT OF AI ANALYSIS\nDefense Phase: Pre-Oral Defense', '/sms2_system/modules/faculty/pages/assigned-defenses.php?group=RG-2026-001', 0, '2026-08-31 09:43:31'),
(60, 'preoral-panel-assignment:65:u493', 493, 'panel', 'michelle.guevarra@bestlink.edu.ph', 39, 65, 'Pre-Oral Panel Assignment', 'You have been assigned as a Panel Member for Group 01\nDEVELOPMENT OF AI ANALYSIS\nDefense Phase: Pre-Oral Defense', '/sms2_system/modules/faculty/pages/assigned-defenses.php?group=RG-2026-001', 0, '2026-08-31 09:43:31');

-- --------------------------------------------------------

--
-- Table structure for table `panel_member_availability`
--

CREATE TABLE `panel_member_availability` (
  `id` int(10) UNSIGNED NOT NULL,
  `panel_user_id` int(10) UNSIGNED NOT NULL,
  `availability_status` varchar(40) NOT NULL DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `panel_member_availability`
--

INSERT INTO `panel_member_availability` (`id`, `panel_user_id`, `availability_status`, `notes`, `updated_at`, `created_at`) VALUES
(6, 491, 'Available', '', '2026-08-23 04:08:58', '2026-08-23 04:08:58'),
(7, 492, 'Available', '', '2026-08-23 04:09:10', '2026-08-23 04:09:10'),
(8, 493, 'Available', '', '2026-08-23 04:09:26', '2026-08-23 04:09:26');

-- --------------------------------------------------------

--
-- Table structure for table `preoral_defense_evaluations`
--

CREATE TABLE `preoral_defense_evaluations` (
  `id` int(10) UNSIGNED NOT NULL,
  `defense_schedule_id` int(10) UNSIGNED NOT NULL,
  `research_group_id` int(10) UNSIGNED DEFAULT NULL,
  `panel_user_id` int(10) UNSIGNED NOT NULL,
  `panel_name` varchar(150) NOT NULL DEFAULT '',
  `content_score` decimal(5,2) NOT NULL,
  `methodology_score` decimal(5,2) NOT NULL,
  `references_score` decimal(5,2) NOT NULL,
  `format_score` decimal(5,2) NOT NULL,
  `remarks` text DEFAULT NULL,
  `result` enum('APPROVED','APPROVED WITH REVISION','FAILED') NOT NULL,
  `overall_score` decimal(5,2) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Submitted',
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proposal_documents`
--

CREATE TABLE `proposal_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `proposal_id` int(10) UNSIGNED NOT NULL,
  `doc_key` varchar(60) NOT NULL COMMENT 'Slot key: manuscript, approval, abstract, etc.',
  `doc_title` varchar(200) NOT NULL,
  `original_name` varchar(300) NOT NULL,
  `stored_name` varchar(300) NOT NULL,
  `file_size` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Bytes',
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proposal_drafts`
--

CREATE TABLE `proposal_drafts` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'FK to sms2_db users (optional)',
  `form_type` varchar(30) NOT NULL DEFAULT 'document',
  `revision_ref` varchar(30) NOT NULL DEFAULT '' COMMENT 'Returned proposal ref when draft is for revision',
  `draft_data` longtext NOT NULL COMMENT 'JSON encoded draft form fields except upload files',
  `signature_data` mediumtext DEFAULT NULL COMMENT 'Base64 PNG of representative signature draft',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proposal_members`
--

CREATE TABLE `proposal_members` (
  `id` int(10) UNSIGNED NOT NULL,
  `proposal_id` int(10) UNSIGNED NOT NULL,
  `sort_order` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 = lead member',
  `student_id` varchar(50) NOT NULL,
  `student_name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `contact` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proposal_status_logs`
--

CREATE TABLE `proposal_status_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `proposal_id` int(10) UNSIGNED NOT NULL,
  `old_status` varchar(30) DEFAULT NULL,
  `new_status` varchar(30) NOT NULL,
  `changed_by` int(10) UNSIGNED DEFAULT NULL COMMENT 'FK to sms2_db users',
  `remarks` text DEFAULT NULL,
  `changed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publications`
--

CREATE TABLE `publications` (
  `id` int(10) UNSIGNED NOT NULL,
  `research_group_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(500) NOT NULL DEFAULT '',
  `authors` text DEFAULT NULL,
  `publication_outlet` varchar(255) NOT NULL DEFAULT '',
  `publication_date` date DEFAULT NULL,
  `doi_link` varchar(500) NOT NULL DEFAULT '',
  `status` enum('Draft','For Publication','Published','Archived') NOT NULL DEFAULT 'Draft',
  `notes` text DEFAULT NULL,
  `created_by_user` int(10) UNSIGNED DEFAULT NULL,
  `created_by_name` varchar(150) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `publications`
--

INSERT INTO `publications` (`id`, `research_group_id`, `title`, `authors`, `publication_outlet`, `publication_date`, `doi_link`, `status`, `notes`, `created_by_user`, `created_by_name`, `created_at`, `updated_at`) VALUES
(4, 62, 'DEVELOPMENT OF AI ASSISTED DOCUMENT', 'Group 01', '', '2026-08-29', 'i3o213uo1u3oi12', 'Published', '', 3, '', '2026-08-28 16:13:42', '2026-08-28 16:14:09'),
(5, 63, 'DEVELOPMENT OF AI', 'Group 01', '', NULL, '', 'Draft', NULL, 3, '', '2026-08-28 16:39:18', '2026-08-28 16:39:18'),
(6, 64, 'DEVELOPMENT OF AI ANALYSIS', 'Group 01', 'crad', '2026-08-31', 'http://localhost/sms2_system/modules/crad/pages/documentation-publication-management.php', 'Published', '', 3, '', '2026-08-31 06:32:29', '2026-08-31 06:33:01');

-- --------------------------------------------------------

--
-- Table structure for table `research_adviser_assignments`
--

CREATE TABLE `research_adviser_assignments` (
  `id` int(10) UNSIGNED NOT NULL,
  `research_group_id` int(10) UNSIGNED DEFAULT NULL,
  `proposal_id` int(10) UNSIGNED DEFAULT NULL,
  `proposal_number` varchar(30) DEFAULT NULL,
  `group_number` varchar(40) DEFAULT NULL,
  `adviser_name` varchar(150) NOT NULL DEFAULT '',
  `adviser_email` varchar(190) NOT NULL DEFAULT '',
  `adviser_user_id` int(10) UNSIGNED DEFAULT NULL,
  `expertise` varchar(255) NOT NULL DEFAULT '',
  `availability_status` varchar(40) NOT NULL DEFAULT 'Pending',
  `assignment_status` varchar(40) NOT NULL DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `assigned_by` int(10) UNSIGNED DEFAULT NULL,
  `assigned_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `notification_sent_at` datetime DEFAULT NULL,
  `notification_sent_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `research_adviser_assignments`
--

INSERT INTO `research_adviser_assignments` (`id`, `research_group_id`, `proposal_id`, `proposal_number`, `group_number`, `adviser_name`, `adviser_email`, `adviser_user_id`, `expertise`, `availability_status`, `assignment_status`, `notes`, `assigned_by`, `assigned_at`, `created_at`, `updated_at`, `notification_sent_at`, `notification_sent_by`) VALUES
(107, 65, NULL, 'TAP-2026-00054', 'RG-2026-001', 'Dr. Roberto M. Santos', 'rsantos@bestlink.edu.ph', 54, 'Artificial Intelligence / Machine Learning / Data Analytics / Data Analysis', 'Available', 'Assigned', 'Synced from fully approved research record.', 40, '2026-08-31 09:29:05', '2026-08-14 12:45:37', '2026-08-31 09:29:12', '2026-08-31 09:29:05', 40);

-- --------------------------------------------------------

--
-- Table structure for table `research_coordinator_assignments`
--

CREATE TABLE `research_coordinator_assignments` (
  `id` int(10) UNSIGNED NOT NULL,
  `research_group_id` int(10) UNSIGNED DEFAULT NULL,
  `proposal_id` int(10) UNSIGNED DEFAULT NULL,
  `title_approval_id` int(10) UNSIGNED DEFAULT NULL,
  `proposal_number` varchar(30) DEFAULT NULL,
  `group_number` varchar(40) DEFAULT NULL,
  `group_name` varchar(120) NOT NULL DEFAULT '',
  `research_title` varchar(255) NOT NULL DEFAULT '',
  `coordinator_user_id` int(10) UNSIGNED DEFAULT NULL,
  `coordinator_name` varchar(200) NOT NULL DEFAULT '',
  `coordinator_email` varchar(200) NOT NULL DEFAULT '',
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `assigned_by` int(10) UNSIGNED DEFAULT NULL,
  `assigned_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `research_coordinator_assignments`
--

INSERT INTO `research_coordinator_assignments` (`id`, `research_group_id`, `proposal_id`, `title_approval_id`, `proposal_number`, `group_number`, `group_name`, `research_title`, `coordinator_user_id`, `coordinator_name`, `coordinator_email`, `status`, `assigned_by`, `assigned_at`, `created_at`, `updated_at`) VALUES
(42, 65, NULL, 54, 'TAP-2026-00054', 'RG-2026-001', 'Group 01', 'DEVELOPMENT OF AI ANALYSIS', 40, 'Mrs. Kris Guevarra', 'researchcoordinator@bestlink.edu.ph', 'Active', 3, '2026-08-31 09:29:54', '2026-08-31 09:29:54', '2026-08-31 09:29:54');

-- --------------------------------------------------------

--
-- Table structure for table `research_defense_schedules`
--

CREATE TABLE `research_defense_schedules` (
  `id` int(10) UNSIGNED NOT NULL,
  `research_group_id` int(10) UNSIGNED DEFAULT NULL,
  `proposal_id` int(10) UNSIGNED DEFAULT NULL,
  `proposal_number` varchar(30) DEFAULT NULL,
  `group_number` varchar(40) NOT NULL,
  `research_group` varchar(120) NOT NULL,
  `research_title` varchar(255) NOT NULL,
  `adviser_name` varchar(160) DEFAULT NULL,
  `panel_members` text DEFAULT NULL,
  `panel_chair` varchar(160) DEFAULT NULL,
  `venue` varchar(120) DEFAULT NULL,
  `venue_id` int(10) UNSIGNED DEFAULT NULL,
  `defense_datetime` datetime DEFAULT NULL,
  `defense_end_datetime` datetime DEFAULT NULL,
  `defense_type` varchar(40) NOT NULL DEFAULT 'Pre-Oral',
  `status` varchar(40) NOT NULL DEFAULT 'Ready for Scheduling',
  `recorded_by` int(10) UNSIGNED DEFAULT NULL,
  `finalized_by` int(10) UNSIGNED DEFAULT NULL,
  `finalized_at` datetime DEFAULT NULL,
  `recorded_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `research_groups`
--

CREATE TABLE `research_groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `proposal_id` int(10) UNSIGNED DEFAULT NULL,
  `title_approval_id` int(10) UNSIGNED DEFAULT NULL,
  `proposal_number` varchar(30) DEFAULT NULL,
  `group_number` varchar(40) NOT NULL,
  `group_name` varchar(40) NOT NULL DEFAULT '',
  `research_title` varchar(255) NOT NULL DEFAULT '',
  `college_dept` varchar(120) NOT NULL DEFAULT '',
  `adviser` varchar(120) NOT NULL DEFAULT '',
  `academic_year` varchar(20) NOT NULL DEFAULT '',
  `leader_name` varchar(120) NOT NULL DEFAULT '',
  `leader_id` varchar(40) NOT NULL DEFAULT '',
  `leader_email` varchar(120) NOT NULL DEFAULT '',
  `leader_contact` varchar(40) NOT NULL DEFAULT '',
  `status` varchar(40) NOT NULL DEFAULT 'Approved',
  `date_assigned` date NOT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `research_groups`
--

INSERT INTO `research_groups` (`id`, `proposal_id`, `title_approval_id`, `proposal_number`, `group_number`, `group_name`, `research_title`, `college_dept`, `adviser`, `academic_year`, `leader_name`, `leader_id`, `leader_email`, `leader_contact`, `status`, `date_assigned`, `created_by`, `created_at`) VALUES
(65, NULL, 54, 'TAP-2026-00054', 'RG-2026-001', 'Group 01', 'DEVELOPMENT OF AI ANALYSIS', 'College of Computer Studies', 'Dr. Roberto M. Santos', '2026-2027', 'Student User', 'S230000001', '', '', 'Approved', '2026-09-01', 3, '2026-08-31 17:28:17');

--
-- Triggers `research_groups`
--
DELIMITER $$
CREATE TRIGGER `trg_research_groups_panel_notifications_after_delete` AFTER DELETE ON `research_groups` FOR EACH ROW BEGIN
                DELETE FROM panel_assignment_notifications
                WHERE research_group_id = OLD.id;
            END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_research_groups_preoral_evals_after_delete` AFTER DELETE ON `research_groups` FOR EACH ROW BEGIN
                DELETE FROM preoral_defense_evaluations
                WHERE research_group_id = OLD.id;
            END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_research_groups_preoral_evaluations_after_delete` AFTER DELETE ON `research_groups` FOR EACH ROW BEGIN
                DELETE FROM preoral_defense_evaluations
                WHERE research_group_id = OLD.id;
            END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `research_milestones`
--

CREATE TABLE `research_milestones` (
  `id` int(10) UNSIGNED NOT NULL,
  `research_plan_id` int(10) UNSIGNED NOT NULL,
  `milestone_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `milestone_order` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `progress_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `weight` decimal(5,2) NOT NULL DEFAULT 1.00 COMMENT 'For weighted progress calculation',
  `status` enum('Not Started','In Progress','Submitted for Review','Revision Requested','Approved','Completed') NOT NULL DEFAULT 'Not Started',
  `start_date` date DEFAULT NULL,
  `target_date` date DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `researcher_notes` text DEFAULT NULL,
  `adviser_remarks` text DEFAULT NULL,
  `panel_remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `research_milestones`
--

INSERT INTO `research_milestones` (`id`, `research_plan_id`, `milestone_name`, `description`, `milestone_order`, `progress_percentage`, `weight`, `status`, `start_date`, `target_date`, `completed_at`, `researcher_notes`, `adviser_remarks`, `panel_remarks`, `created_at`, `updated_at`) VALUES
(175, 21, 'Chapter 4', 'Results / System Design and Development', 4, 0.00, 1.00, 'Not Started', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-23 03:47:51', '2026-08-23 03:47:51'),
(176, 21, 'Chapter 5', 'Summary, Conclusions and Recommendations', 5, 0.00, 1.00, 'Not Started', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-23 03:47:51', '2026-08-23 03:47:51'),
(177, 22, 'Chapter 1', 'Introduction and Background', 1, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-23 04:11:53', NULL, 'Progress approved.', 'Approved by Panel.', '2026-08-23 03:49:51', '2026-08-23 04:11:53'),
(178, 22, 'Chapter 2', 'Review of Related Literature', 2, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-23 04:11:53', NULL, 'Progress approved.', 'Approved by Panel.', '2026-08-23 03:49:51', '2026-08-23 04:11:53'),
(179, 22, 'Chapter 3', 'Methodology', 3, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-23 04:11:53', NULL, 'Progress approved.', 'Approved by Panel.', '2026-08-23 03:49:51', '2026-08-23 04:11:53'),
(180, 22, 'Chapter 4', 'Results / System Design and Development', 4, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-23 04:13:25', NULL, 'asd', NULL, '2026-08-23 03:49:51', '2026-08-23 04:13:25'),
(181, 22, 'Chapter 5', 'Summary, Conclusions and Recommendations', 5, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-23 04:13:35', NULL, 'Progress approved.', NULL, '2026-08-23 03:49:51', '2026-08-23 04:13:35'),
(182, 22, 'System Development', 'System Implementation', 6, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-23 04:13:45', NULL, 'Progress approved.', NULL, '2026-08-23 03:49:51', '2026-08-23 04:13:45'),
(183, 22, 'Testing', 'Testing and Quality Assurance', 7, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-23 04:13:52', NULL, 'Progress approved.', NULL, '2026-08-23 03:49:51', '2026-08-23 04:13:52'),
(184, 22, 'Documentation', 'Final Documentation and Report', 8, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-23 04:13:59', NULL, 'Progress approved.', NULL, '2026-08-23 03:49:51', '2026-08-23 04:13:59'),
(185, 23, 'Chapter 1', 'Introduction and Background', 1, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-28 15:37:11', NULL, 'Progress approved.', 'Approved by Panel.', '2026-08-28 15:30:09', '2026-08-28 15:37:11'),
(186, 23, 'Chapter 2', 'Review of Related Literature', 2, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-28 15:37:11', NULL, 'Progress approved.', 'Approved by Panel.', '2026-08-28 15:30:09', '2026-08-28 15:37:11'),
(187, 23, 'Chapter 3', 'Methodology', 3, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-28 15:37:11', NULL, 'Progress approved.', 'Approved by Panel.', '2026-08-28 15:30:09', '2026-08-28 15:37:11'),
(188, 23, 'Chapter 4', 'Results / System Design and Development', 4, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-28 15:37:17', NULL, 'Progress approved.', NULL, '2026-08-28 15:30:09', '2026-08-28 15:37:17'),
(189, 23, 'Chapter 5', 'Summary, Conclusions and Recommendations', 5, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-28 15:37:22', NULL, 'Progress approved.', NULL, '2026-08-28 15:30:09', '2026-08-28 15:37:22'),
(190, 23, 'System Development', 'System Implementation', 6, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-28 15:37:29', NULL, 'Progress approved.', NULL, '2026-08-28 15:30:09', '2026-08-28 15:37:29'),
(191, 23, 'Testing', 'Testing and Quality Assurance', 7, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-28 15:37:35', NULL, 'Progress approved.', NULL, '2026-08-28 15:30:09', '2026-08-28 15:37:35'),
(192, 23, 'Documentation', 'Final Documentation and Report', 8, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-28 15:37:44', NULL, 'Progress approved.', NULL, '2026-08-28 15:30:09', '2026-08-28 15:37:44'),
(193, 24, 'Chapter 1', 'Introduction and Background', 1, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-28 16:33:40', NULL, 'Progress approved.', 'Approved by Panel.', '2026-08-28 16:28:29', '2026-08-28 16:33:40'),
(194, 24, 'Chapter 2', 'Review of Related Literature', 2, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-28 16:33:40', NULL, 'Progress approved.', 'Approved by Panel.', '2026-08-28 16:28:29', '2026-08-28 16:33:40'),
(195, 24, 'Chapter 3', 'Methodology', 3, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-28 16:33:40', NULL, 'Progress approved.', 'Approved by Panel.', '2026-08-28 16:28:29', '2026-08-28 16:33:40'),
(196, 24, 'Chapter 4', 'Results / System Design and Development', 4, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-28 16:33:58', NULL, 'Progress approved.', NULL, '2026-08-28 16:28:29', '2026-08-28 16:33:58'),
(197, 24, 'Chapter 5', 'Summary, Conclusions and Recommendations', 5, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-28 16:34:03', NULL, 'Progress approved.', NULL, '2026-08-28 16:28:29', '2026-08-28 16:34:03'),
(198, 24, 'System Development', 'System Implementation', 6, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-28 16:34:11', NULL, 'Progress approved.', NULL, '2026-08-28 16:28:29', '2026-08-28 16:34:11'),
(199, 24, 'Testing', 'Testing and Quality Assurance', 7, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-28 16:34:16', NULL, 'Progress approved.', NULL, '2026-08-28 16:28:29', '2026-08-28 16:34:16'),
(200, 24, 'Documentation', 'Final Documentation and Report', 8, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-28 16:34:23', NULL, 'Progress approved.', NULL, '2026-08-28 16:28:29', '2026-08-28 16:34:23'),
(201, 25, 'Chapter 1', 'Introduction and Background', 1, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-31 06:14:23', NULL, 'Progress approved.', 'Approved by Panel.', '2026-08-31 05:28:10', '2026-08-31 06:14:23'),
(202, 25, 'Chapter 2', 'Review of Related Literature', 2, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-31 06:14:23', NULL, 'Progress approved.', 'Approved by Panel.', '2026-08-31 05:28:10', '2026-08-31 06:14:23'),
(203, 25, 'Chapter 3', 'Methodology', 3, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-31 06:14:23', NULL, 'Progress approved.', 'Approved by Panel.', '2026-08-31 05:28:10', '2026-08-31 06:14:23'),
(204, 25, 'Chapter 4', 'Results / System Design and Development', 4, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-31 06:15:08', NULL, 'Progress approved.', NULL, '2026-08-31 05:28:10', '2026-08-31 06:15:08'),
(205, 25, 'Chapter 5', 'Summary, Conclusions and Recommendations', 5, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-31 06:15:17', NULL, 'Progress approved.', NULL, '2026-08-31 05:28:10', '2026-08-31 06:15:17'),
(206, 25, 'System Development', 'System Implementation', 6, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-31 06:15:28', NULL, 'Progress approved.', NULL, '2026-08-31 05:28:10', '2026-08-31 06:15:28'),
(207, 25, 'Testing', 'Testing and Quality Assurance', 7, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-31 06:15:35', NULL, 'Progress approved.', NULL, '2026-08-31 05:28:10', '2026-08-31 06:15:35'),
(208, 25, 'Documentation', 'Final Documentation and Report', 8, 100.00, 1.00, 'Approved', NULL, NULL, '2026-08-31 06:15:42', NULL, 'Progress approved.', NULL, '2026-08-31 05:28:10', '2026-08-31 06:15:42'),
(209, 26, 'Chapter 1', 'Introduction and Background', 1, 0.00, 1.00, 'Submitted for Review', NULL, NULL, NULL, NULL, 'Progress approved.', NULL, '2026-08-31 09:34:19', '2026-08-31 09:36:20'),
(210, 26, 'Chapter 2', 'Review of Related Literature', 2, 0.00, 1.00, 'Submitted for Review', NULL, NULL, NULL, NULL, 'Progress approved.', NULL, '2026-08-31 09:34:19', '2026-08-31 09:36:29'),
(211, 26, 'Chapter 3', 'Methodology', 3, 0.00, 1.00, 'Submitted for Review', NULL, NULL, NULL, NULL, 'Progress approved.', NULL, '2026-08-31 09:34:19', '2026-08-31 09:36:53'),
(212, 26, 'Chapter 4', 'Results / System Design and Development', 4, 0.00, 1.00, 'Submitted for Review', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-31 09:34:19', '2026-08-31 09:35:01'),
(213, 26, 'Chapter 5', 'Summary, Conclusions and Recommendations', 5, 0.00, 1.00, 'Submitted for Review', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-31 09:34:19', '2026-08-31 09:35:12'),
(214, 26, 'System Development', 'System Implementation', 6, 0.00, 1.00, 'Submitted for Review', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-31 09:34:19', '2026-08-31 09:35:29'),
(215, 26, 'Testing', 'Testing and Quality Assurance', 7, 0.00, 1.00, 'Submitted for Review', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-31 09:34:19', '2026-08-31 09:35:37'),
(216, 26, 'Documentation', 'Final Documentation and Report', 8, 0.00, 1.00, 'Submitted for Review', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-31 09:34:19', '2026-08-31 09:35:48');

-- --------------------------------------------------------

--
-- Table structure for table `research_panel_assignments`
--

CREATE TABLE `research_panel_assignments` (
  `id` int(10) UNSIGNED NOT NULL,
  `research_group_id` int(10) UNSIGNED NOT NULL,
  `defense_schedule_id` int(10) UNSIGNED DEFAULT NULL,
  `proposal_id` int(10) UNSIGNED DEFAULT NULL,
  `title_approval_id` int(10) UNSIGNED DEFAULT NULL,
  `proposal_number` varchar(30) DEFAULT NULL,
  `group_number` varchar(40) NOT NULL DEFAULT '',
  `research_title` varchar(255) NOT NULL DEFAULT '',
  `panel_user_id` int(10) UNSIGNED NOT NULL,
  `panel_name` varchar(150) NOT NULL DEFAULT '',
  `panel_email` varchar(190) NOT NULL DEFAULT '',
  `expertise` varchar(255) NOT NULL DEFAULT '',
  `availability_status` varchar(40) NOT NULL DEFAULT 'Pending',
  `assignment_status` varchar(40) NOT NULL DEFAULT 'Assigned',
  `defense_phase` varchar(60) NOT NULL DEFAULT 'Pre-Oral Defense',
  `assigned_by` int(10) UNSIGNED DEFAULT NULL,
  `assigned_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `research_panel_assignments`
--

INSERT INTO `research_panel_assignments` (`id`, `research_group_id`, `defense_schedule_id`, `proposal_id`, `title_approval_id`, `proposal_number`, `group_number`, `research_title`, `panel_user_id`, `panel_name`, `panel_email`, `expertise`, `availability_status`, `assignment_status`, `defense_phase`, `assigned_by`, `assigned_at`, `created_at`, `updated_at`) VALUES
(1, 52, NULL, NULL, 35, 'TAP-2026-00035', 'RG-2026-001', 'DEVELOPMENT OF AI ASSISTED OPEN AI GPT 5,5', 491, 'Dr. Jobert Valentino', 'jobertvalentino@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 116, '2026-08-15 18:28:32', '2026-08-15 18:28:32', '2026-08-15 18:28:32'),
(2, 52, NULL, NULL, 35, 'TAP-2026-00035', 'RG-2026-001', 'DEVELOPMENT OF AI ASSISTED OPEN AI GPT 5,5', 492, 'Dr. Jonathan Estrada', 'jonathanestrada@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 116, '2026-08-15 18:28:32', '2026-08-15 18:28:32', '2026-08-15 18:28:32'),
(3, 52, NULL, NULL, 35, 'TAP-2026-00035', 'RG-2026-001', 'DEVELOPMENT OF AI ASSISTED OPEN AI GPT 5,5', 493, 'Dr. Michelle Guevarra', 'michelleguevarra@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 116, '2026-08-15 18:28:32', '2026-08-15 18:28:32', '2026-08-15 18:28:32'),
(4, 53, NULL, NULL, 37, 'TAP-2026-00037', 'RG-2026-001', 'DEVELOPMENT OF AI ASSISTED DOCUMENT ANALYSIS', 491, 'Dr. Jobert Valentino', 'jobertvalentino@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-15 22:51:51', '2026-08-15 22:51:51', '2026-08-15 22:51:51'),
(5, 53, NULL, NULL, 37, 'TAP-2026-00037', 'RG-2026-001', 'DEVELOPMENT OF AI ASSISTED DOCUMENT ANALYSIS', 492, 'Dr. Jonathan Estrada', 'jonathanestrada@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-15 22:51:51', '2026-08-15 22:51:51', '2026-08-15 22:51:51'),
(6, 53, NULL, NULL, 37, 'TAP-2026-00037', 'RG-2026-001', 'DEVELOPMENT OF AI ASSISTED DOCUMENT ANALYSIS', 493, 'Dr. Michelle Guevarra', 'michelleguevarra@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-15 22:51:51', '2026-08-15 22:51:51', '2026-08-15 22:51:51'),
(7, 54, 23, NULL, 43, 'TAP-2026-00043', 'RG-2026-001', 'DEVELOPMENT OF AI ASSISTED DOCUMENT ANALYSIS', 491, 'Dr. Jobert Valentino', 'jobertvalentino@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-16 15:02:09', '2026-08-16 15:02:09', '2026-08-16 15:15:51'),
(8, 54, 23, NULL, 43, 'TAP-2026-00043', 'RG-2026-001', 'DEVELOPMENT OF AI ASSISTED DOCUMENT ANALYSIS', 492, 'Dr. Jonathan Estrada', 'jonathanestrada@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-16 15:02:09', '2026-08-16 15:02:09', '2026-08-16 15:15:51'),
(9, 54, 23, NULL, 43, 'TAP-2026-00043', 'RG-2026-001', 'DEVELOPMENT OF AI ASSISTED DOCUMENT ANALYSIS', 493, 'Dr. Michelle Guevarra', 'michelleguevarra@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-16 15:02:09', '2026-08-16 15:02:09', '2026-08-16 15:15:51'),
(10, 57, 26, NULL, 46, 'TAP-2026-00046', 'RG-2026-001', 'DEVELOPMENT OF AI ANALYSIS', 491, 'Dr. Jobert Valentino', 'jobertvalentino@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-16 21:44:21', '2026-08-16 21:44:21', '2026-08-16 21:46:14'),
(11, 57, 26, NULL, 46, 'TAP-2026-00046', 'RG-2026-001', 'DEVELOPMENT OF AI ANALYSIS', 492, 'Dr. Jonathan Estrada', 'jonathanestrada@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-16 21:44:21', '2026-08-16 21:44:21', '2026-08-16 21:46:14'),
(12, 57, 26, NULL, 46, 'TAP-2026-00046', 'RG-2026-001', 'DEVELOPMENT OF AI ANALYSIS', 493, 'Dr. Michelle Guevarra', 'michelleguevarra@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-16 21:44:21', '2026-08-16 21:44:21', '2026-08-16 21:46:14'),
(13, 61, 30, NULL, 50, 'TAP-2026-00050', 'RG-2026-001', 'DEVELOPMENT OF AI ANALYSIS', 491, 'Dr. Jobert Valentino', 'jobertvalentino@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-23 04:09:43', '2026-08-23 04:09:43', '2026-08-23 04:10:47'),
(14, 61, 30, NULL, 50, 'TAP-2026-00050', 'RG-2026-001', 'DEVELOPMENT OF AI ANALYSIS', 492, 'Dr. Jonathan Estrada', 'jonathanestrada@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-23 04:09:43', '2026-08-23 04:09:43', '2026-08-23 04:10:47'),
(15, 61, 30, NULL, 50, 'TAP-2026-00050', 'RG-2026-001', 'DEVELOPMENT OF AI ANALYSIS', 493, 'Dr. Michelle Guevarra', 'michelleguevarra@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-23 04:09:43', '2026-08-23 04:09:43', '2026-08-23 04:10:47'),
(16, 61, 32, NULL, 50, 'TAP-2026-00050', 'RG-2026-001', 'DEVELOPMENT OF AI ANALYSIS', 491, 'Dr. Jobert Valentino', 'jobertvalentino@bestlink.edu.ph', '', 'Available', 'Assigned', 'Final Defense', 40, '2026-08-28 08:21:42', '2026-08-28 08:21:42', '2026-08-28 08:21:42'),
(17, 61, 32, NULL, 50, 'TAP-2026-00050', 'RG-2026-001', 'DEVELOPMENT OF AI ANALYSIS', 492, 'Dr. Jonathan Estrada', 'jonathanestrada@bestlink.edu.ph', '', 'Available', 'Assigned', 'Final Defense', 40, '2026-08-28 08:21:42', '2026-08-28 08:21:42', '2026-08-28 08:21:42'),
(18, 61, 32, NULL, 50, 'TAP-2026-00050', 'RG-2026-001', 'DEVELOPMENT OF AI ANALYSIS', 493, 'Dr. Michelle Guevarra', 'michelleguevarra@bestlink.edu.ph', '', 'Available', 'Assigned', 'Final Defense', 40, '2026-08-28 08:21:42', '2026-08-28 08:21:42', '2026-08-28 08:21:42'),
(19, 62, 36, NULL, 51, 'TAP-2026-00051', 'RG-2026-001', 'DEVELOPMENT OF AI ASSISTED DOCUMENT', 491, 'Dr. Jobert Valentino', 'jobertvalentino@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-28 15:34:33', '2026-08-28 15:34:33', '2026-08-28 15:35:39'),
(20, 62, 36, NULL, 51, 'TAP-2026-00051', 'RG-2026-001', 'DEVELOPMENT OF AI ASSISTED DOCUMENT', 492, 'Dr. Jonathan Estrada', 'jonathanestrada@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-28 15:34:33', '2026-08-28 15:34:33', '2026-08-28 15:35:39'),
(21, 62, 36, NULL, 51, 'TAP-2026-00051', 'RG-2026-001', 'DEVELOPMENT OF AI ASSISTED DOCUMENT', 493, 'Dr. Michelle Guevarra', 'michelleguevarra@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-28 15:34:33', '2026-08-28 15:34:33', '2026-08-28 15:35:39'),
(22, 62, 38, NULL, 51, 'TAP-2026-00051', 'RG-2026-001', 'DEVELOPMENT OF AI ASSISTED DOCUMENT', 491, 'Dr. Jobert Valentino', 'jobertvalentino@bestlink.edu.ph', '', 'Available', 'Assigned', 'Final Defense', 40, '2026-08-28 15:52:23', '2026-08-28 15:52:23', '2026-08-28 15:52:23'),
(23, 62, 38, NULL, 51, 'TAP-2026-00051', 'RG-2026-001', 'DEVELOPMENT OF AI ASSISTED DOCUMENT', 492, 'Dr. Jonathan Estrada', 'jonathanestrada@bestlink.edu.ph', '', 'Available', 'Assigned', 'Final Defense', 40, '2026-08-28 15:52:23', '2026-08-28 15:52:23', '2026-08-28 15:52:23'),
(24, 62, 38, NULL, 51, 'TAP-2026-00051', 'RG-2026-001', 'DEVELOPMENT OF AI ASSISTED DOCUMENT', 493, 'Dr. Michelle Guevarra', 'michelleguevarra@bestlink.edu.ph', '', 'Available', 'Assigned', 'Final Defense', 40, '2026-08-28 15:52:23', '2026-08-28 15:52:23', '2026-08-28 15:52:23'),
(25, 63, 41, NULL, 52, 'TAP-2026-00052', 'RG-2026-001', 'DEVELOPMENT OF AI', 491, 'Dr. Jobert Valentino', 'jobertvalentino@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-28 16:31:11', '2026-08-28 16:31:11', '2026-08-28 16:32:15'),
(26, 63, 41, NULL, 52, 'TAP-2026-00052', 'RG-2026-001', 'DEVELOPMENT OF AI', 492, 'Dr. Jonathan Estrada', 'jonathanestrada@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-28 16:31:11', '2026-08-28 16:31:11', '2026-08-28 16:32:15'),
(27, 63, 41, NULL, 52, 'TAP-2026-00052', 'RG-2026-001', 'DEVELOPMENT OF AI', 493, 'Dr. Michelle Guevarra', 'michelleguevarra@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-28 16:31:11', '2026-08-28 16:31:11', '2026-08-28 16:32:15'),
(28, 63, 44, NULL, 52, 'TAP-2026-00052', 'RG-2026-001', 'DEVELOPMENT OF AI', 491, 'Dr. Jobert Valentino', 'jobertvalentino@bestlink.edu.ph', '', 'Available', 'Assigned', 'Final Defense', 40, '2026-08-28 16:37:05', '2026-08-28 16:37:05', '2026-08-28 16:37:05'),
(29, 63, 44, NULL, 52, 'TAP-2026-00052', 'RG-2026-001', 'DEVELOPMENT OF AI', 492, 'Dr. Jonathan Estrada', 'jonathanestrada@bestlink.edu.ph', '', 'Available', 'Assigned', 'Final Defense', 40, '2026-08-28 16:37:05', '2026-08-28 16:37:05', '2026-08-28 16:37:05'),
(30, 63, 44, NULL, 52, 'TAP-2026-00052', 'RG-2026-001', 'DEVELOPMENT OF AI', 493, 'Dr. Michelle Guevarra', 'michelleguevarra@bestlink.edu.ph', '', 'Available', 'Assigned', 'Final Defense', 40, '2026-08-28 16:37:05', '2026-08-28 16:37:05', '2026-08-28 16:37:05'),
(31, 64, 48, NULL, 53, 'TAP-2026-00053', 'RG-2026-001', 'DEVELOPMENT OF AI ANALYSIS', 491, 'Dr. Jobert Valentino', 'jobertvalentino@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-31 05:34:37', '2026-08-31 05:34:23', '2026-08-31 06:10:27'),
(32, 64, 48, NULL, 53, 'TAP-2026-00053', 'RG-2026-001', 'DEVELOPMENT OF AI ANALYSIS', 492, 'Dr. Jonathan Estrada', 'jonathanestrada@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-31 05:34:37', '2026-08-31 05:34:23', '2026-08-31 06:10:27'),
(33, 64, 48, NULL, 53, 'TAP-2026-00053', 'RG-2026-001', 'DEVELOPMENT OF AI ANALYSIS', 493, 'Dr. Michelle Guevarra', 'michelleguevarra@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-31 05:34:37', '2026-08-31 05:34:23', '2026-08-31 06:10:27'),
(34, 64, 50, NULL, 53, 'TAP-2026-00053', 'RG-2026-001', 'DEVELOPMENT OF AI ANALYSIS', 491, 'Dr. Jobert Valentino', 'jobertvalentino@bestlink.edu.ph', '', 'Available', 'Assigned', 'Final Defense', 40, '2026-08-31 06:22:35', '2026-08-31 06:22:35', '2026-08-31 06:22:35'),
(35, 64, 50, NULL, 53, 'TAP-2026-00053', 'RG-2026-001', 'DEVELOPMENT OF AI ANALYSIS', 492, 'Dr. Jonathan Estrada', 'jonathanestrada@bestlink.edu.ph', '', 'Available', 'Assigned', 'Final Defense', 40, '2026-08-31 06:22:35', '2026-08-31 06:22:35', '2026-08-31 06:22:35'),
(36, 64, 50, NULL, 53, 'TAP-2026-00053', 'RG-2026-001', 'DEVELOPMENT OF AI ANALYSIS', 493, 'Dr. Michelle Guevarra', 'michelleguevarra@bestlink.edu.ph', '', 'Available', 'Assigned', 'Final Defense', 40, '2026-08-31 06:22:35', '2026-08-31 06:22:35', '2026-08-31 06:22:35'),
(37, 65, NULL, NULL, 54, 'TAP-2026-00054', 'RG-2026-001', 'DEVELOPMENT OF AI ANALYSIS', 491, 'Dr. Jobert Valentino', 'jobertvalentino@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-31 09:43:31', '2026-08-31 09:43:31', '2026-08-31 10:56:14'),
(38, 65, NULL, NULL, 54, 'TAP-2026-00054', 'RG-2026-001', 'DEVELOPMENT OF AI ANALYSIS', 492, 'Dr. Jonathan Estrada', 'jonathanestrada@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-31 09:43:31', '2026-08-31 09:43:31', '2026-08-31 10:56:14'),
(39, 65, NULL, NULL, 54, 'TAP-2026-00054', 'RG-2026-001', 'DEVELOPMENT OF AI ANALYSIS', 493, 'Dr. Michelle Guevarra', 'michelleguevarra@bestlink.edu.ph', '', 'Available', 'Assigned', 'Pre-Oral Defense', 40, '2026-08-31 09:43:31', '2026-08-31 09:43:31', '2026-08-31 10:56:14');

-- --------------------------------------------------------

--
-- Table structure for table `research_plans`
--

CREATE TABLE `research_plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `research_group_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'FK to research_groups; nullable to preserve history if group is removed',
  `research_title` varchar(500) NOT NULL DEFAULT '',
  `group_number` varchar(40) NOT NULL DEFAULT '',
  `adviser_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'FK to sms2_db users (adviser)',
  `adviser_name` varchar(150) NOT NULL DEFAULT '',
  `adviser_email` varchar(190) NOT NULL DEFAULT '',
  `start_date` date DEFAULT NULL,
  `target_completion_date` date DEFAULT NULL,
  `current_stage` varchar(100) NOT NULL DEFAULT 'Planning',
  `overall_progress` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Auto-calculated from milestones',
  `status` enum('Active','Completed','On Hold','Cancelled') NOT NULL DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `final_defense_recommended` tinyint(1) NOT NULL DEFAULT 0,
  `final_defense_recommended_by` int(10) UNSIGNED DEFAULT NULL,
  `final_defense_recommended_by_name` varchar(150) DEFAULT NULL,
  `final_defense_recommended_at` datetime DEFAULT NULL,
  `final_defense_recommendation_remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `research_plans`
--

INSERT INTO `research_plans` (`id`, `research_group_id`, `research_title`, `group_number`, `adviser_id`, `adviser_name`, `adviser_email`, `start_date`, `target_completion_date`, `current_stage`, `overall_progress`, `status`, `created_at`, `updated_at`, `final_defense_recommended`, `final_defense_recommended_by`, `final_defense_recommended_by_name`, `final_defense_recommended_at`, `final_defense_recommendation_remarks`) VALUES
(21, NULL, 'DEVELOPMENT OF AI ASSISTED', 'RG-2026-001', 54, 'Dr. Roberto M. Santos', '', '2026-08-23', NULL, 'Planning', 0.00, 'Active', '2026-08-23 03:16:21', '2026-08-23 03:46:14', 0, NULL, NULL, NULL, NULL),
(22, NULL, 'DEVELOPMENT OF AI ANALYSIS', 'RG-2026-001', 54, 'Dr. Roberto M. Santos', '', '2026-08-23', NULL, 'Pre-Oral Defense', 100.00, 'Active', '2026-08-23 03:49:51', '2026-08-28 08:00:25', 0, NULL, NULL, NULL, NULL),
(23, NULL, 'DEVELOPMENT OF AI ASSISTED DOCUMENT', 'RG-2026-001', 54, 'Dr. Roberto M. Santos', '', '2026-08-28', NULL, 'Pre-Oral Defense', 100.00, 'Active', '2026-08-28 15:30:09', '2026-08-28 16:05:34', 1, 54, 'Dr. Roberto M. Santos', '2026-08-28 15:45:21', ''),
(24, NULL, 'DEVELOPMENT OF AI', 'RG-2026-001', 54, 'Dr. Roberto M. Santos', '', '2026-08-28', NULL, 'Pre-Oral Defense', 100.00, 'Active', '2026-08-28 16:28:29', '2026-08-28 16:34:47', 1, 54, 'Dr. Roberto M. Santos', '2026-08-28 16:34:27', ''),
(25, NULL, 'DEVELOPMENT OF AI ANALYSIS', 'RG-2026-001', 54, 'Dr. Roberto M. Santos', '', '2026-08-31', NULL, 'Pre-Oral Defense', 100.00, 'Active', '2026-08-31 05:28:10', '2026-08-31 06:16:38', 1, 54, 'Dr. Roberto M. Santos', '2026-08-31 06:15:48', ''),
(26, 65, 'DEVELOPMENT OF AI ANALYSIS', 'RG-2026-001', 54, 'Dr. Roberto M. Santos', '', '2026-08-31', NULL, 'Planning', 0.00, 'Active', '2026-08-31 09:34:19', '2026-08-31 09:36:53', 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `research_progress_activity_logs`
--

CREATE TABLE `research_progress_activity_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `research_plan_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'FK to sms2_db users',
  `user_name` varchar(150) NOT NULL DEFAULT '',
  `user_role` varchar(40) NOT NULL DEFAULT '',
  `action` varchar(100) NOT NULL COMMENT 'milestone_created, progress_updated, feedback_added, etc',
  `entity_type` varchar(50) NOT NULL DEFAULT '' COMMENT 'milestone, progress_update, feedback, etc',
  `entity_id` int(10) UNSIGNED DEFAULT NULL,
  `old_value` text DEFAULT NULL COMMENT 'JSON or text of previous state',
  `new_value` text DEFAULT NULL COMMENT 'JSON or text of new state',
  `description` varchar(500) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `research_progress_activity_logs`
--

INSERT INTO `research_progress_activity_logs` (`id`, `research_plan_id`, `user_id`, `user_name`, `user_role`, `action`, `entity_type`, `entity_id`, `old_value`, `new_value`, `description`, `created_at`) VALUES
(79, 21, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 43, NULL, NULL, 'Progress updated to 0%', '2026-08-23 03:16:35'),
(90, 22, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 53, NULL, NULL, 'Progress updated to 0%', '2026-08-23 03:49:58'),
(91, 22, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 54, NULL, NULL, 'Progress updated to 0%', '2026-08-23 03:50:08'),
(92, 22, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 55, NULL, NULL, 'Progress updated to 0%', '2026-08-23 03:50:18'),
(93, 22, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 36, NULL, NULL, 'Adviser approved progress', '2026-08-23 03:50:29'),
(94, 22, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 37, NULL, NULL, 'Adviser approved progress', '2026-08-23 03:50:36'),
(95, 22, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 38, NULL, NULL, 'Adviser approved progress', '2026-08-23 03:50:45'),
(96, 22, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 56, NULL, NULL, 'Progress updated to 0%', '2026-08-23 04:12:04'),
(97, 22, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 57, NULL, NULL, 'Progress updated to 0%', '2026-08-23 04:12:11'),
(98, 22, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 58, NULL, NULL, 'Progress updated to 0%', '2026-08-23 04:12:18'),
(99, 22, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 59, NULL, NULL, 'Progress updated to 0%', '2026-08-23 04:12:25'),
(100, 22, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 60, NULL, NULL, 'Progress updated to 0%', '2026-08-23 04:12:31'),
(101, 22, 54, 'Dr. Roberto M. Santos', 'adviser', 'revision_requested', 'feedback', 39, NULL, NULL, 'Adviser requested revision', '2026-08-23 04:13:01'),
(102, 22, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 61, NULL, NULL, 'Progress updated to 0%', '2026-08-23 04:13:16'),
(103, 22, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 40, NULL, NULL, 'Adviser approved progress', '2026-08-23 04:13:25'),
(104, 22, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 41, NULL, NULL, 'Adviser approved progress', '2026-08-23 04:13:35'),
(105, 22, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 42, NULL, NULL, 'Adviser approved progress', '2026-08-23 04:13:45'),
(106, 22, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 43, NULL, NULL, 'Adviser approved progress', '2026-08-23 04:13:52'),
(107, 22, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 44, NULL, NULL, 'Adviser approved progress', '2026-08-23 04:13:59'),
(108, 23, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 62, NULL, NULL, 'Progress updated to 0%', '2026-08-28 15:30:17'),
(109, 23, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 63, NULL, NULL, 'Progress updated to 0%', '2026-08-28 15:30:54'),
(110, 23, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 64, NULL, NULL, 'Progress updated to 0%', '2026-08-28 15:31:01'),
(111, 23, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 65, NULL, NULL, 'Progress updated to 0%', '2026-08-28 15:31:08'),
(112, 23, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 66, NULL, NULL, 'Progress updated to 0%', '2026-08-28 15:31:16'),
(113, 23, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 67, NULL, NULL, 'Progress updated to 0%', '2026-08-28 15:31:23'),
(114, 23, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 68, NULL, NULL, 'Progress updated to 0%', '2026-08-28 15:31:30'),
(115, 23, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 69, NULL, NULL, 'Progress updated to 0%', '2026-08-28 15:31:36'),
(116, 23, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 45, NULL, NULL, 'Adviser approved progress', '2026-08-28 15:32:47'),
(117, 23, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 46, NULL, NULL, 'Adviser approved progress', '2026-08-28 15:32:53'),
(118, 23, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 47, NULL, NULL, 'Adviser approved progress', '2026-08-28 15:32:57'),
(119, 23, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 48, NULL, NULL, 'Adviser approved progress', '2026-08-28 15:37:17'),
(120, 23, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 49, NULL, NULL, 'Adviser approved progress', '2026-08-28 15:37:22'),
(121, 23, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 50, NULL, NULL, 'Adviser approved progress', '2026-08-28 15:37:29'),
(122, 23, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 51, NULL, NULL, 'Adviser approved progress', '2026-08-28 15:37:35'),
(123, 23, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 52, NULL, NULL, 'Adviser approved progress', '2026-08-28 15:37:44'),
(124, 24, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 70, NULL, NULL, 'Progress updated to 0%', '2026-08-28 16:28:35'),
(125, 24, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 71, NULL, NULL, 'Progress updated to 0%', '2026-08-28 16:28:41'),
(126, 24, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 72, NULL, NULL, 'Progress updated to 0%', '2026-08-28 16:28:47'),
(127, 24, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 73, NULL, NULL, 'Progress updated to 0%', '2026-08-28 16:28:54'),
(128, 24, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 74, NULL, NULL, 'Progress updated to 0%', '2026-08-28 16:29:01'),
(129, 24, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 75, NULL, NULL, 'Progress updated to 0%', '2026-08-28 16:29:08'),
(130, 24, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 76, NULL, NULL, 'Progress updated to 0%', '2026-08-28 16:29:19'),
(131, 24, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 53, NULL, NULL, 'Adviser approved progress', '2026-08-28 16:29:27'),
(132, 24, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 54, NULL, NULL, 'Adviser approved progress', '2026-08-28 16:29:31'),
(133, 24, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 55, NULL, NULL, 'Adviser approved progress', '2026-08-28 16:29:36'),
(134, 24, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 77, NULL, NULL, 'Progress updated to 0%', '2026-08-28 16:33:53'),
(135, 24, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 56, NULL, NULL, 'Adviser approved progress', '2026-08-28 16:33:58'),
(136, 24, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 57, NULL, NULL, 'Adviser approved progress', '2026-08-28 16:34:03'),
(137, 24, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 58, NULL, NULL, 'Adviser approved progress', '2026-08-28 16:34:11'),
(138, 24, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 59, NULL, NULL, 'Adviser approved progress', '2026-08-28 16:34:16'),
(139, 24, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 60, NULL, NULL, 'Adviser approved progress', '2026-08-28 16:34:23'),
(140, 25, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 78, NULL, NULL, 'Progress updated to 0%', '2026-08-31 05:28:26'),
(141, 25, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 79, NULL, NULL, 'Progress updated to 0%', '2026-08-31 05:28:34'),
(142, 25, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 80, NULL, NULL, 'Progress updated to 0%', '2026-08-31 05:28:45'),
(143, 25, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 81, NULL, NULL, 'Progress updated to 0%', '2026-08-31 05:28:53'),
(144, 25, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 82, NULL, NULL, 'Progress updated to 0%', '2026-08-31 05:29:03'),
(145, 25, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 83, NULL, NULL, 'Progress updated to 0%', '2026-08-31 05:29:15'),
(146, 25, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 84, NULL, NULL, 'Progress updated to 0%', '2026-08-31 05:29:24'),
(147, 25, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 85, NULL, NULL, 'Progress updated to 0%', '2026-08-31 05:29:32'),
(148, 25, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 61, NULL, NULL, 'Adviser approved progress', '2026-08-31 05:29:47'),
(149, 25, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 62, NULL, NULL, 'Adviser approved progress', '2026-08-31 05:29:55'),
(150, 25, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 63, NULL, NULL, 'Adviser approved progress', '2026-08-31 05:30:03'),
(151, 25, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 64, NULL, NULL, 'Adviser approved progress', '2026-08-31 06:15:08'),
(152, 25, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 65, NULL, NULL, 'Adviser approved progress', '2026-08-31 06:15:17'),
(153, 25, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 66, NULL, NULL, 'Adviser approved progress', '2026-08-31 06:15:28'),
(154, 25, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 67, NULL, NULL, 'Adviser approved progress', '2026-08-31 06:15:35'),
(155, 25, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 68, NULL, NULL, 'Adviser approved progress', '2026-08-31 06:15:42'),
(156, 26, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 86, NULL, NULL, 'Progress updated to 0%', '2026-08-31 09:34:32'),
(157, 26, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 87, NULL, NULL, 'Progress updated to 0%', '2026-08-31 09:34:41'),
(158, 26, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 88, NULL, NULL, 'Progress updated to 0%', '2026-08-31 09:34:49'),
(159, 26, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 89, NULL, NULL, 'Progress updated to 0%', '2026-08-31 09:35:01'),
(160, 26, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 90, NULL, NULL, 'Progress updated to 0%', '2026-08-31 09:35:12'),
(161, 26, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 91, NULL, NULL, 'Progress updated to 0%', '2026-08-31 09:35:29'),
(162, 26, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 92, NULL, NULL, 'Progress updated to 0%', '2026-08-31 09:35:37'),
(163, 26, 9, 'Student User', 'student', 'progress_updated', 'progress_update', 93, NULL, NULL, 'Progress updated to 0%', '2026-08-31 09:35:48'),
(164, 26, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 69, NULL, NULL, 'Adviser approved progress', '2026-08-31 09:36:20'),
(165, 26, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 70, NULL, NULL, 'Adviser approved progress', '2026-08-31 09:36:29'),
(166, 26, 54, 'Dr. Roberto M. Santos', 'adviser', 'progress_approved', 'feedback', 71, NULL, NULL, 'Adviser approved progress', '2026-08-31 09:36:53');

-- --------------------------------------------------------

--
-- Table structure for table `research_progress_attachments`
--

CREATE TABLE `research_progress_attachments` (
  `id` int(10) UNSIGNED NOT NULL,
  `progress_update_id` int(10) UNSIGNED NOT NULL,
  `file_name` varchar(300) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(100) NOT NULL DEFAULT '',
  `file_size` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Bytes',
  `uploaded_by` int(10) UNSIGNED NOT NULL COMMENT 'FK to sms2_db users',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `research_progress_attachments`
--

INSERT INTO `research_progress_attachments` (`id`, `progress_update_id`, `file_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`) VALUES
(34, 43, 'CRAD_Chapter_1_TO_4_KULANG-PA.docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g60/u9/09a62b2daa1cce84684a82782b95b7df.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 7734354, 9, '2026-08-23 03:16:35'),
(41, 53, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g61/u9/df3f82b96645757c73e791012ab36dc4.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 236268, 9, '2026-08-23 03:49:58'),
(42, 54, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g61/u9/c17c49e54baa55cad91e60bd58bcad92.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 236268, 9, '2026-08-23 03:50:08'),
(43, 55, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g61/u9/7e5a55afefbeda325fee95b26668600b.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 236268, 9, '2026-08-23 03:50:18'),
(44, 56, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g61/u9/af25964fcccc0a65b09c503100ab6bd6.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 236268, 9, '2026-08-23 04:12:04'),
(45, 57, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g61/u9/b0b3bb6b5becdf66dd97395b68d85f8d.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 236268, 9, '2026-08-23 04:12:11'),
(46, 58, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g61/u9/ca491b2b5c87fa0313876eeca2cec331.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 236268, 9, '2026-08-23 04:12:18'),
(47, 59, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g61/u9/1eb10d997a17fe2c9f5ee73485b77ae8.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 236268, 9, '2026-08-23 04:12:25'),
(48, 61, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g61/u9/cff34883d0dec4263b79eddc43fcc992.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 236268, 9, '2026-08-23 04:13:16'),
(49, 62, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g62/u9/e232da6a01b4a6d1fef55ef744cfd286.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 302605, 9, '2026-08-28 15:30:17'),
(50, 63, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g62/u9/5eda2ee2c5b63097d514c7fb0febe2cb.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 302605, 9, '2026-08-28 15:30:54'),
(51, 64, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g62/u9/7c5f38451672efeefa6fc2f052755647.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 302605, 9, '2026-08-28 15:31:01'),
(52, 65, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g62/u9/d5738c98ea85c03003583de5a612bf42.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 302605, 9, '2026-08-28 15:31:08'),
(53, 66, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g62/u9/84f08f7484eb161bdcb8756a57e1b6c6.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 302605, 9, '2026-08-28 15:31:16'),
(54, 67, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g62/u9/c198a6471dc795e4a704cfb2ef3f808f.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 302605, 9, '2026-08-28 15:31:23'),
(55, 68, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g62/u9/b875bb0942866180b655526b5d79cb9c.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 302605, 9, '2026-08-28 15:31:30'),
(56, 69, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g62/u9/c4ad5851636b7b71d099039ebb69a252.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 302605, 9, '2026-08-28 15:31:36'),
(57, 70, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g63/u9/28b05d83f4c150c45929b60ec2eee255.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 302605, 9, '2026-08-28 16:28:35'),
(58, 71, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g63/u9/caf0f3c59320c81adae7ce19efc7c289.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 302605, 9, '2026-08-28 16:28:41'),
(59, 72, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g63/u9/77701e54e11148f3638382199cef968e.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 302605, 9, '2026-08-28 16:28:47'),
(60, 73, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g63/u9/3e2c3d7261ed6d038c8255773b17d194.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 302605, 9, '2026-08-28 16:28:54'),
(61, 74, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g63/u9/0f1662a4f091481e6d76664b0836e6e6.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 302605, 9, '2026-08-28 16:29:01'),
(62, 75, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g63/u9/93c3ee330a6b0a0dbcc31b66123df4b8.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 302605, 9, '2026-08-28 16:29:08'),
(63, 76, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g63/u9/e7b47a94f20de26b24e04e1dd7f246ac.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 302605, 9, '2026-08-28 16:29:19'),
(64, 77, 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g63/u9/c7ad58d98d311f6fd3ab1e6a4bf9a098.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 302605, 9, '2026-08-28 16:33:53'),
(65, 78, 'OLIVEROS CV.pdf', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g64/u9/1e8f6014e3459a3ebbe0e8362d99d252.pdf', 'application/pdf', 294354, 9, '2026-08-31 05:28:26'),
(66, 79, 'OLIVEROS CV.pdf', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g64/u9/0f04ea324e3230f9eb7dbff5fc1f0889.pdf', 'application/pdf', 294354, 9, '2026-08-31 05:28:34'),
(67, 80, 'OLIVEROS CV.pdf', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g64/u9/962a28233c08de26f2e9ef1209db1e82.pdf', 'application/pdf', 294354, 9, '2026-08-31 05:28:45'),
(68, 81, 'OLIVEROS CV.pdf', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g64/u9/cb8e4d50ad6222d2ac4243ba948e77b0.pdf', 'application/pdf', 294354, 9, '2026-08-31 05:28:53'),
(69, 82, 'OLIVEROS CV.pdf', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g64/u9/ddd91c717013890ffa4bd9e6503f9789.pdf', 'application/pdf', 294354, 9, '2026-08-31 05:29:03'),
(70, 83, 'Diaz CV.pdf_20260813_105004_0000.pdf', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g64/u9/3c49d34ce74b8c1fb4f6a3820bc4fd77.pdf', 'application/pdf', 62390, 9, '2026-08-31 05:29:15'),
(71, 84, 'OLIVEROS CV.pdf', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g64/u9/e9b4c863c0cdcb67975619e7f63d9e57.pdf', 'application/pdf', 294354, 9, '2026-08-31 05:29:24'),
(72, 85, 'OLIVEROS CV.pdf', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g64/u9/c1b6a2b034dbec7c9fe660ba7ab5cddb.pdf', 'application/pdf', 294354, 9, '2026-08-31 05:29:32'),
(73, 86, 'OLIVEROS CV.pdf', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g65/u9/0b9cb630bca2013fc5b5055241753819.pdf', 'application/pdf', 294354, 9, '2026-08-31 09:34:32'),
(74, 87, 'OLIVEROS CV.pdf', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g65/u9/ef6e446036b4ba2a88d92faf65ddc654.pdf', 'application/pdf', 294354, 9, '2026-08-31 09:34:41'),
(75, 88, 'OLIVEROS CV.pdf', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g65/u9/b0b0378a61ce66eaaca964143b30b63d.pdf', 'application/pdf', 294354, 9, '2026-08-31 09:34:49'),
(76, 89, 'OLIVEROS CV.pdf', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g65/u9/06cc39770a42bf06fff7da5285a54c52.pdf', 'application/pdf', 294354, 9, '2026-08-31 09:35:01'),
(77, 90, 'OLIVEROS CV.pdf', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g65/u9/4b2c7b8f8662c0acfecf51b09123ed51.pdf', 'application/pdf', 294354, 9, '2026-08-31 09:35:12'),
(78, 92, 'OLIVEROS CV.pdf', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g65/u9/3f2e2e77ca556957100166cd43b11e4b.pdf', 'application/pdf', 294354, 9, '2026-08-31 09:35:37'),
(79, 93, 'OLIVEROS CV.pdf', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g65/u9/afc9627ed4ff5d61ef4658268ee83f4c.pdf', 'application/pdf', 294354, 9, '2026-08-31 09:35:48');

-- --------------------------------------------------------

--
-- Table structure for table `research_progress_feedback`
--

CREATE TABLE `research_progress_feedback` (
  `id` int(10) UNSIGNED NOT NULL,
  `progress_update_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Can be NULL for general milestone feedback',
  `milestone_id` int(10) UNSIGNED DEFAULT NULL,
  `research_plan_id` int(10) UNSIGNED NOT NULL,
  `adviser_user_id` int(10) UNSIGNED NOT NULL,
  `adviser_name` varchar(200) NOT NULL DEFAULT '',
  `feedback_text` text NOT NULL,
  `new_milestone_status` varchar(60) DEFAULT NULL,
  `submission_token` varchar(64) DEFAULT NULL,
  `feedback_type` enum('Comment','Revision Request','Approval','Progress Approved') NOT NULL DEFAULT 'Comment',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `research_progress_feedback`
--

INSERT INTO `research_progress_feedback` (`id`, `progress_update_id`, `milestone_id`, `research_plan_id`, `adviser_user_id`, `adviser_name`, `feedback_text`, `new_milestone_status`, `submission_token`, `feedback_type`, `created_at`, `updated_at`) VALUES
(36, 53, 177, 22, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', 'cbab7e8bb4297eed5707494db5aca072', 'Progress Approved', '2026-08-23 03:50:29', '2026-08-23 03:50:29'),
(37, 54, 178, 22, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', '3db8eccb296ff187522dda8d89406e2e', 'Progress Approved', '2026-08-23 03:50:36', '2026-08-23 03:50:36'),
(38, 55, 179, 22, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', '2e9d9355eb6a72370d738e70531d8dbb', 'Progress Approved', '2026-08-23 03:50:45', '2026-08-23 03:50:45'),
(39, 60, 184, 22, 54, 'Dr. Roberto M. Santos', 'asda', 'Revision Requested', '808fb99c2fe3d46ec2c8c159f7c1371a', 'Revision Request', '2026-08-23 04:13:01', '2026-08-23 04:13:01'),
(40, 56, 180, 22, 54, 'Dr. Roberto M. Santos', 'asd', 'Approved', '437099e8103a1903c1f3a709f28ca760', 'Progress Approved', '2026-08-23 04:13:25', '2026-08-23 04:13:25'),
(41, 57, 181, 22, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', 'f684e5013bac8056cca3863e47bd7352', 'Progress Approved', '2026-08-23 04:13:35', '2026-08-23 04:13:35'),
(42, 58, 182, 22, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', '34a0fbac64e4286ca1562c9deabf4496', 'Progress Approved', '2026-08-23 04:13:45', '2026-08-23 04:13:45'),
(43, 59, 183, 22, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', '0199228cd673e9e3bd6a8a294f550e22', 'Progress Approved', '2026-08-23 04:13:52', '2026-08-23 04:13:52'),
(44, 61, 184, 22, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', 'fe26a976de6911f07e8c7128c3898650', 'Progress Approved', '2026-08-23 04:13:59', '2026-08-23 04:13:59'),
(45, 62, 185, 23, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', 'af47b77d85b3515a6285ccdacb8491f1', 'Progress Approved', '2026-08-28 15:32:47', '2026-08-28 15:32:47'),
(46, 63, 186, 23, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', '038b536a7039f500815f5d5caf26a6a6', 'Progress Approved', '2026-08-28 15:32:53', '2026-08-28 15:32:53'),
(47, 64, 187, 23, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', '6d859347e2ffd38a144721950f5acee6', 'Progress Approved', '2026-08-28 15:32:57', '2026-08-28 15:32:57'),
(48, 65, 188, 23, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', 'c5bdcdff4f2ee78dfdd9137957fae753', 'Progress Approved', '2026-08-28 15:37:17', '2026-08-28 15:37:17'),
(49, 66, 189, 23, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', 'de459f2c9b4baeb91890ba4ecf252afe', 'Progress Approved', '2026-08-28 15:37:22', '2026-08-28 15:37:22'),
(50, 67, 190, 23, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', 'ae366421ee920b567e05ff514901569d', 'Progress Approved', '2026-08-28 15:37:29', '2026-08-28 15:37:29'),
(51, 68, 191, 23, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', 'd0284261dab9d2c26df919065f0a7664', 'Progress Approved', '2026-08-28 15:37:35', '2026-08-28 15:37:35'),
(52, 69, 192, 23, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', 'e479b32ed11d98958b36ab3ff5e7b1b0', 'Progress Approved', '2026-08-28 15:37:44', '2026-08-28 15:37:44'),
(53, 70, 193, 24, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', '429bde8947dbc3683aed8a5b77e1c5b3', 'Progress Approved', '2026-08-28 16:29:27', '2026-08-28 16:29:27'),
(54, 71, 194, 24, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', '56357366b16542510b06ded75522a512', 'Progress Approved', '2026-08-28 16:29:31', '2026-08-28 16:29:31'),
(55, 72, 195, 24, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', '216a9067f2e597dfca83630cdabac766', 'Progress Approved', '2026-08-28 16:29:35', '2026-08-28 16:29:35'),
(56, 73, 196, 24, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', '356609fb6fee97c62ec5c3857ae98d4f', 'Progress Approved', '2026-08-28 16:33:58', '2026-08-28 16:33:58'),
(57, 74, 197, 24, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', 'e22ab3c8613001bf67270a318e9be606', 'Progress Approved', '2026-08-28 16:34:03', '2026-08-28 16:34:03'),
(58, 75, 198, 24, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', '1a62e9b035250f0549c888f4674f8954', 'Progress Approved', '2026-08-28 16:34:11', '2026-08-28 16:34:11'),
(59, 76, 199, 24, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', '4a659445e3ea9a05985ab23389ef47b8', 'Progress Approved', '2026-08-28 16:34:16', '2026-08-28 16:34:16'),
(60, 77, 200, 24, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', '9abb90ef2957198f47696085f25f79c1', 'Progress Approved', '2026-08-28 16:34:23', '2026-08-28 16:34:23'),
(61, 78, 201, 25, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', '150a2e1f8e5be45ab976f7a5a9965d39', 'Progress Approved', '2026-08-31 05:29:47', '2026-08-31 05:29:47'),
(62, 79, 202, 25, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', 'ea920a26c677e579211aafd7fd287ec1', 'Progress Approved', '2026-08-31 05:29:55', '2026-08-31 05:29:55'),
(63, 80, 203, 25, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', 'b356f432af193251dfd49c713cb15603', 'Progress Approved', '2026-08-31 05:30:03', '2026-08-31 05:30:03'),
(64, 81, 204, 25, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', 'bbffa704e7f2e707f2307f6cbf3e7469', 'Progress Approved', '2026-08-31 06:15:08', '2026-08-31 06:15:08'),
(65, 82, 205, 25, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', '1d847884e699e2c863f9e3cd6e9ea48c', 'Progress Approved', '2026-08-31 06:15:17', '2026-08-31 06:15:17'),
(66, 83, 206, 25, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', '989713e3784796ab62b6d27a46aeb2e4', 'Progress Approved', '2026-08-31 06:15:28', '2026-08-31 06:15:28'),
(67, 84, 207, 25, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', '0bf8cf6e90e94bbb8479232779451cba', 'Progress Approved', '2026-08-31 06:15:35', '2026-08-31 06:15:35'),
(68, 85, 208, 25, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', 'cb4538db89818a051a2ccb5dc5c034cb', 'Progress Approved', '2026-08-31 06:15:42', '2026-08-31 06:15:42'),
(69, 86, 209, 26, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', 'd44b47d44416bf872495d1a2148ecbbe', 'Progress Approved', '2026-08-31 09:36:20', '2026-08-31 09:36:20'),
(70, 87, 210, 26, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', '85cbb39397d0a40d618d796befc7b83d', 'Progress Approved', '2026-08-31 09:36:29', '2026-08-31 09:36:29'),
(71, 88, 211, 26, 54, 'Dr. Roberto M. Santos', 'Progress approved.', 'Approved', 'fa09c3d0d541bb1b2d0ba34ed41ad298', 'Progress Approved', '2026-08-31 09:36:53', '2026-08-31 09:36:53');

-- --------------------------------------------------------

--
-- Table structure for table `research_progress_notifications`
--

CREATE TABLE `research_progress_notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `recipient_user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'FK to sms2_db.users.id (NULL = role-based)',
  `recipient_email` varchar(200) NOT NULL DEFAULT '',
  `recipient_role` varchar(40) NOT NULL DEFAULT '',
  `batch_key` varchar(100) NOT NULL DEFAULT '' COMMENT 'Unique key per event for deduplication',
  `notification_type` varchar(60) NOT NULL DEFAULT 'progress_update',
  `title` varchar(255) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `related_entity_type` varchar(60) NOT NULL DEFAULT '',
  `related_entity_id` int(10) UNSIGNED DEFAULT NULL,
  `action_url` varchar(500) DEFAULT NULL,
  `status` enum('unread','read') NOT NULL DEFAULT 'unread',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `research_progress_notifications`
--

INSERT INTO `research_progress_notifications` (`id`, `recipient_user_id`, `recipient_email`, `recipient_role`, `batch_key`, `notification_type`, `title`, `body`, `related_entity_type`, `related_entity_id`, `action_url`, `status`, `created_at`, `read_at`) VALUES
(50, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:53', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 1', 'progress_update', 53, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-23 03:49:58', NULL),
(51, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:54', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 2', 'progress_update', 54, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-23 03:50:08', NULL),
(52, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:55', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 3', 'progress_update', 55, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-23 03:50:18', NULL),
(53, 9, '', 'student', 'approval:36', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 36, NULL, 'unread', '2026-08-23 03:50:29', NULL),
(54, 9, '', 'student', 'approval:37', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 37, NULL, 'unread', '2026-08-23 03:50:36', NULL),
(55, 9, '', 'student', 'approval:38', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 38, NULL, 'unread', '2026-08-23 03:50:45', NULL),
(56, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:56', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 4', 'progress_update', 56, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-23 04:12:04', NULL),
(57, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:57', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 5', 'progress_update', 57, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-23 04:12:11', NULL),
(58, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:58', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for System Development', 'progress_update', 58, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-23 04:12:18', NULL),
(59, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:59', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Testing', 'progress_update', 59, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-23 04:12:25', NULL),
(60, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:60', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Documentation', 'progress_update', 60, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-23 04:12:31', NULL),
(61, 9, '', 'student', 'revision:39', 'revision_requested', 'Revision Requested', 'Your adviser requested revisions on your progress update', 'feedback', 39, NULL, 'unread', '2026-08-23 04:13:01', NULL),
(62, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:61', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Documentation', 'progress_update', 61, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-23 04:13:16', NULL),
(63, 9, '', 'student', 'approval:40', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 40, NULL, 'unread', '2026-08-23 04:13:25', NULL),
(64, 9, '', 'student', 'approval:41', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 41, NULL, 'unread', '2026-08-23 04:13:35', NULL),
(65, 9, '', 'student', 'approval:42', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 42, NULL, 'unread', '2026-08-23 04:13:45', NULL),
(66, 9, '', 'student', 'approval:43', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 43, NULL, 'unread', '2026-08-23 04:13:52', NULL),
(67, 9, '', 'student', 'approval:44', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 44, NULL, 'unread', '2026-08-23 04:13:59', NULL),
(68, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:62', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 1', 'progress_update', 62, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-28 15:30:17', NULL),
(69, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:63', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 2', 'progress_update', 63, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-28 15:30:54', NULL),
(70, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:64', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 3', 'progress_update', 64, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-28 15:31:01', NULL),
(71, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:65', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 4', 'progress_update', 65, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-28 15:31:08', NULL),
(72, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:66', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 5', 'progress_update', 66, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-28 15:31:16', NULL),
(73, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:67', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for System Development', 'progress_update', 67, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-28 15:31:23', NULL),
(74, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:68', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Testing', 'progress_update', 68, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-28 15:31:30', NULL),
(75, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:69', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Documentation', 'progress_update', 69, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-28 15:31:36', NULL),
(76, 9, '', 'student', 'approval:45', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 45, NULL, 'unread', '2026-08-28 15:32:47', NULL),
(77, 9, '', 'student', 'approval:46', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 46, NULL, 'unread', '2026-08-28 15:32:53', NULL),
(78, 9, '', 'student', 'approval:47', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 47, NULL, 'unread', '2026-08-28 15:32:57', NULL),
(79, 9, '', 'student', 'approval:48', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 48, NULL, 'unread', '2026-08-28 15:37:17', NULL),
(80, 9, '', 'student', 'approval:49', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 49, NULL, 'unread', '2026-08-28 15:37:22', NULL),
(81, 9, '', 'student', 'approval:50', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 50, NULL, 'unread', '2026-08-28 15:37:29', NULL),
(82, 9, '', 'student', 'approval:51', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 51, NULL, 'unread', '2026-08-28 15:37:35', NULL),
(83, 9, '', 'student', 'approval:52', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 52, NULL, 'unread', '2026-08-28 15:37:44', NULL),
(84, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:70', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 1', 'progress_update', 70, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-28 16:28:35', NULL),
(85, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:71', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 2', 'progress_update', 71, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-28 16:28:41', NULL),
(86, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:72', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 3', 'progress_update', 72, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-28 16:28:47', NULL),
(87, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:73', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 4', 'progress_update', 73, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-28 16:28:54', NULL),
(88, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:74', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 5', 'progress_update', 74, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-28 16:29:01', NULL),
(89, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:75', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for System Development', 'progress_update', 75, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-28 16:29:08', NULL),
(90, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:76', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Testing', 'progress_update', 76, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-28 16:29:19', NULL),
(91, 9, '', 'student', 'approval:53', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 53, NULL, 'unread', '2026-08-28 16:29:27', NULL),
(92, 9, '', 'student', 'approval:54', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 54, NULL, 'unread', '2026-08-28 16:29:31', NULL),
(93, 9, '', 'student', 'approval:55', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 55, NULL, 'unread', '2026-08-28 16:29:36', NULL),
(94, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:77', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Documentation', 'progress_update', 77, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-28 16:33:53', NULL),
(95, 9, '', 'student', 'approval:56', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 56, NULL, 'unread', '2026-08-28 16:33:58', NULL),
(96, 9, '', 'student', 'approval:57', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 57, NULL, 'unread', '2026-08-28 16:34:03', NULL),
(97, 9, '', 'student', 'approval:58', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 58, NULL, 'unread', '2026-08-28 16:34:11', NULL),
(98, 9, '', 'student', 'approval:59', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 59, NULL, 'unread', '2026-08-28 16:34:16', NULL),
(99, 9, '', 'student', 'approval:60', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 60, NULL, 'unread', '2026-08-28 16:34:23', NULL),
(100, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:78', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 1', 'progress_update', 78, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-31 05:28:26', NULL),
(101, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:79', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 2', 'progress_update', 79, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-31 05:28:34', NULL),
(102, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:80', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 3', 'progress_update', 80, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-31 05:28:45', NULL),
(103, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:81', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 4', 'progress_update', 81, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-31 05:28:53', NULL),
(104, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:82', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 5', 'progress_update', 82, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-31 05:29:03', NULL),
(105, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:83', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for System Development', 'progress_update', 83, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-31 05:29:15', NULL),
(106, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:84', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Testing', 'progress_update', 84, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-31 05:29:24', NULL),
(107, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:85', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Documentation', 'progress_update', 85, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-31 05:29:32', NULL),
(108, 9, '', 'student', 'approval:61', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 61, NULL, 'unread', '2026-08-31 05:29:47', NULL),
(109, 9, '', 'student', 'approval:62', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 62, NULL, 'unread', '2026-08-31 05:29:55', NULL),
(110, 9, '', 'student', 'approval:63', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 63, NULL, 'unread', '2026-08-31 05:30:03', NULL),
(111, 9, '', 'student', 'approval:64', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 64, NULL, 'unread', '2026-08-31 06:15:08', NULL),
(112, 9, '', 'student', 'approval:65', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 65, NULL, 'unread', '2026-08-31 06:15:17', NULL),
(113, 9, '', 'student', 'approval:66', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 66, NULL, 'unread', '2026-08-31 06:15:28', NULL),
(114, 9, '', 'student', 'approval:67', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 67, NULL, 'unread', '2026-08-31 06:15:35', NULL),
(115, 9, '', 'student', 'approval:68', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 68, NULL, 'unread', '2026-08-31 06:15:42', NULL),
(116, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:86', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 1', 'progress_update', 86, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-31 09:34:32', NULL),
(117, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:87', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 2', 'progress_update', 87, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-31 09:34:41', NULL),
(118, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:88', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 3', 'progress_update', 88, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-31 09:34:49', NULL),
(119, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:89', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 4', 'progress_update', 89, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-31 09:35:01', NULL),
(120, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:90', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Chapter 5', 'progress_update', 90, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-31 09:35:12', NULL),
(121, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:91', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for System Development', 'progress_update', 91, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-31 09:35:29', NULL),
(122, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:92', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Testing', 'progress_update', 92, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-31 09:35:37', NULL),
(123, 54, 'rsantos@bestlink.edu.ph', 'adviser', 'progress_update:93', 'progress_update', 'New Progress Update', 'RG-2026-001 submitted a progress update for Documentation', 'progress_update', 93, '/sms2_system/modules/crad/modules/faculty/pages/research-progress.php?group=RG-2026-001', 'unread', '2026-08-31 09:35:48', NULL),
(124, 9, '', 'student', 'approval:69', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 69, NULL, 'unread', '2026-08-31 09:36:20', NULL),
(125, 9, '', 'student', 'approval:70', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 70, NULL, 'unread', '2026-08-31 09:36:29', NULL),
(126, 9, '', 'student', 'approval:71', 'progress_approved', 'Progress Approved', 'Your adviser approved your progress update', 'feedback', 71, NULL, 'unread', '2026-08-31 09:36:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `research_progress_updates`
--

CREATE TABLE `research_progress_updates` (
  `id` int(10) UNSIGNED NOT NULL,
  `research_plan_id` int(10) UNSIGNED NOT NULL,
  `research_group_id` int(10) UNSIGNED NOT NULL,
  `milestone_id` int(10) UNSIGNED DEFAULT NULL,
  `submitted_by_user_id` int(10) UNSIGNED NOT NULL,
  `submitted_by_name` varchar(200) NOT NULL DEFAULT '',
  `update_title` varchar(300) NOT NULL,
  `accomplishments` text DEFAULT NULL,
  `problems_blockers` text DEFAULT NULL,
  `next_planned_activity` text DEFAULT NULL,
  `attachment_path` varchar(500) DEFAULT NULL,
  `attachment_original_name` varchar(300) DEFAULT NULL,
  `submission_token` varchar(64) DEFAULT NULL,
  `previous_progress` decimal(5,2) DEFAULT NULL,
  `new_progress` decimal(5,2) NOT NULL,
  `milestone_status` varchar(60) NOT NULL DEFAULT 'In Progress',
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `research_progress_updates`
--

INSERT INTO `research_progress_updates` (`id`, `research_plan_id`, `research_group_id`, `milestone_id`, `submitted_by_user_id`, `submitted_by_name`, `update_title`, `accomplishments`, `problems_blockers`, `next_planned_activity`, `attachment_path`, `attachment_original_name`, `submission_token`, `previous_progress`, `new_progress`, `milestone_status`, `submitted_at`, `updated_at`) VALUES
(43, 21, 60, NULL, 9, 'Student User', 'DEVELOPMENT OF AI ASSISTED', 'sadas', '', 'asdas', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g60/u9/09a62b2daa1cce84684a82782b95b7df.docx', 'CRAD_Chapter_1_TO_4_KULANG-PA.docx', '8f3692e45ee0853f66293b2ec969314d', 0.00, 0.00, 'Submitted for Review', '2026-08-23 03:16:35', '2026-08-23 03:16:35'),
(53, 22, 61, 177, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'SADAS', '', 'ASDA', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g61/u9/df3f82b96645757c73e791012ab36dc4.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'e90bcc3cc197bb965809cb9c2e0e3080', 0.00, 0.00, 'Approved', '2026-08-23 03:49:58', '2026-08-23 03:50:29'),
(54, 22, 61, 178, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'ADSAD', '', 'ASDAS', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g61/u9/c17c49e54baa55cad91e60bd58bcad92.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', '7972c30afb20aa7a048bbb0f1d2c3da3', 0.00, 0.00, 'Approved', '2026-08-23 03:50:08', '2026-08-23 03:50:36'),
(55, 22, 61, 179, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'ASDAS', '', 'ADAS', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g61/u9/7e5a55afefbeda325fee95b26668600b.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', '9b1e3f2664b72052437162d568588703', 0.00, 0.00, 'Approved', '2026-08-23 03:50:18', '2026-08-23 03:50:45'),
(56, 22, 61, 180, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'asdas', '', 'asdas', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g61/u9/af25964fcccc0a65b09c503100ab6bd6.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', '3bf4afb87e3dbede7e93604e623034ba', 0.00, 0.00, 'Approved', '2026-08-23 04:12:04', '2026-08-23 04:13:25'),
(57, 22, 61, 181, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'asdas', '', 'asdas', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g61/u9/b0b3bb6b5becdf66dd97395b68d85f8d.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', '5ccb88ca9b4db2be2a773b0c0d9feea0', 0.00, 0.00, 'Approved', '2026-08-23 04:12:11', '2026-08-23 04:13:35'),
(58, 22, 61, 182, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'adasd', '', 'asdas', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g61/u9/ca491b2b5c87fa0313876eeca2cec331.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', '4c8b62f4160a5c17900fb95039e90ee1', 0.00, 0.00, 'Approved', '2026-08-23 04:12:18', '2026-08-23 04:13:45'),
(59, 22, 61, 183, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'asdas', '', 'das', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g61/u9/1eb10d997a17fe2c9f5ee73485b77ae8.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', '2ee796709264f5ab003420cd374a5e94', 0.00, 0.00, 'Approved', '2026-08-23 04:12:25', '2026-08-23 04:13:52'),
(60, 22, 61, 184, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'asdas', '', 'das', NULL, NULL, '95b713da492cb4c94648d63edaf98090', 0.00, 0.00, 'Revision Requested', '2026-08-23 04:12:31', '2026-08-23 04:13:01'),
(61, 22, 61, 184, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'asdasd', '', 'asdas', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g61/u9/cff34883d0dec4263b79eddc43fcc992.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'ed9f89cc33102105cda0fd24e9757687', 0.00, 0.00, 'Approved', '2026-08-23 04:13:16', '2026-08-23 04:13:59'),
(62, 23, 62, 185, 9, 'Student User', 'DEVELOPMENT OF AI ASSISTED DOCUMENT', 'asdas', '', 'das', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g62/u9/e232da6a01b4a6d1fef55ef744cfd286.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', '08abf973b072e98e204688196ccbeac3', 0.00, 0.00, 'Approved', '2026-08-28 15:30:17', '2026-08-28 15:32:47'),
(63, 23, 62, 186, 9, 'Student User', 'DEVELOPMENT OF AI ASSISTED DOCUMENT', 'dasdas', '', 'das', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g62/u9/5eda2ee2c5b63097d514c7fb0febe2cb.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', '64a904159da3f2af9d545f8c2417c10f', 0.00, 0.00, 'Approved', '2026-08-28 15:30:54', '2026-08-28 15:32:53'),
(64, 23, 62, 187, 9, 'Student User', 'DEVELOPMENT OF AI ASSISTED DOCUMENT', 'adasd', '', 'asdas', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g62/u9/7c5f38451672efeefa6fc2f052755647.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'd43491d8f89c17c5c7f703da9b3576da', 0.00, 0.00, 'Approved', '2026-08-28 15:31:01', '2026-08-28 15:32:57'),
(65, 23, 62, 188, 9, 'Student User', 'DEVELOPMENT OF AI ASSISTED DOCUMENT', 'asdasd', '', 'asda', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g62/u9/d5738c98ea85c03003583de5a612bf42.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', '14c3a48699c03c4d62efc66e2f3e1492', 0.00, 0.00, 'Approved', '2026-08-28 15:31:08', '2026-08-28 15:37:17'),
(66, 23, 62, 189, 9, 'Student User', 'DEVELOPMENT OF AI ASSISTED DOCUMENT', 'asdasd', '', 'asdas', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g62/u9/84f08f7484eb161bdcb8756a57e1b6c6.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', '7c5eb2f4157c0aba222689bc613ec4e4', 0.00, 0.00, 'Approved', '2026-08-28 15:31:16', '2026-08-28 15:37:22'),
(67, 23, 62, 190, 9, 'Student User', 'DEVELOPMENT OF AI ASSISTED DOCUMENT', 'asdas', '', 'asdas', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g62/u9/c198a6471dc795e4a704cfb2ef3f808f.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'f3dafeb930abcac8b726768777684d76', 0.00, 0.00, 'Approved', '2026-08-28 15:31:23', '2026-08-28 15:37:29'),
(68, 23, 62, 191, 9, 'Student User', 'DEVELOPMENT OF AI ASSISTED DOCUMENT', 'asdas', '', 'dasd', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g62/u9/b875bb0942866180b655526b5d79cb9c.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'afa92ac71725ee096ea19bdbeca42a00', 0.00, 0.00, 'Approved', '2026-08-28 15:31:30', '2026-08-28 15:37:35'),
(69, 23, 62, 192, 9, 'Student User', 'DEVELOPMENT OF AI ASSISTED DOCUMENT', 'dasd', '', 'asdas', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g62/u9/c4ad5851636b7b71d099039ebb69a252.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'bf8c8c7f2a614664e2dbdad792ad9251', 0.00, 0.00, 'Approved', '2026-08-28 15:31:36', '2026-08-28 15:37:44'),
(70, 24, 63, 193, 9, 'Student User', 'DEVELOPMENT OF AI', 'asdas', '', 'dasdas', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g63/u9/28b05d83f4c150c45929b60ec2eee255.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'c06285941c7be6c2ae3f81bb8c31a984', 0.00, 0.00, 'Approved', '2026-08-28 16:28:35', '2026-08-28 16:29:27'),
(71, 24, 63, 194, 9, 'Student User', 'DEVELOPMENT OF AI', 'asda', '', 'dasdas', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g63/u9/caf0f3c59320c81adae7ce19efc7c289.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'bcd4b023262a1a8a28a803fb59e15908', 0.00, 0.00, 'Approved', '2026-08-28 16:28:41', '2026-08-28 16:29:31'),
(72, 24, 63, 195, 9, 'Student User', 'DEVELOPMENT OF AI', 'asdasd', '', 'asdas', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g63/u9/77701e54e11148f3638382199cef968e.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', '7a4a07d369b5dcbf04280267ddbc7023', 0.00, 0.00, 'Approved', '2026-08-28 16:28:47', '2026-08-28 16:29:36'),
(73, 24, 63, 196, 9, 'Student User', 'DEVELOPMENT OF AI', 'asdas', '', 'asda', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g63/u9/3e2c3d7261ed6d038c8255773b17d194.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'de709355f4f64ed523aa6da44e6d4685', 0.00, 0.00, 'Approved', '2026-08-28 16:28:54', '2026-08-28 16:33:58'),
(74, 24, 63, 197, 9, 'Student User', 'DEVELOPMENT OF AI', 'asdas', '', 'asda', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g63/u9/0f1662a4f091481e6d76664b0836e6e6.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'e880b3899035ccefcdac1b654243e0d2', 0.00, 0.00, 'Approved', '2026-08-28 16:29:01', '2026-08-28 16:34:03'),
(75, 24, 63, 198, 9, 'Student User', 'DEVELOPMENT OF AI', 'asdasd', '', 'asd', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g63/u9/93c3ee330a6b0a0dbcc31b66123df4b8.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', 'd5dc84f912d4033ab7c994a5832b7d2b', 0.00, 0.00, 'Approved', '2026-08-28 16:29:08', '2026-08-28 16:34:11'),
(76, 24, 63, 199, 9, 'Student User', 'DEVELOPMENT OF AI', 'fsasf', '', 'fasfa', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g63/u9/e7b47a94f20de26b24e04e1dd7f246ac.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', '1b6099d2ef250a52d784c49d6def7db1', 0.00, 0.00, 'Approved', '2026-08-28 16:29:19', '2026-08-28 16:34:16'),
(77, 24, 63, 200, 9, 'Student User', 'DEVELOPMENT OF AI', 'asdas', '', 'das', 'F:\\xampp\\htdocs\\SMS2_system/storage/uploads/research_progress/g63/u9/c7ad58d98d311f6fd3ab1e6a4bf9a098.docx', 'OJT_PRACTICUM_1_NARRATIVE_REPORT (1) (1).docx', '0026fd1ce013574bf6a28b1a7e896c67', 0.00, 0.00, 'Approved', '2026-08-28 16:33:53', '2026-08-28 16:34:23'),
(78, 25, 64, 201, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'sadasd', '', 'asdas', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g64/u9/1e8f6014e3459a3ebbe0e8362d99d252.pdf', 'OLIVEROS CV.pdf', 'd1a4da63116003c07aa12ad5ffe41d1b', 0.00, 0.00, 'Approved', '2026-08-31 05:28:26', '2026-08-31 05:29:47'),
(79, 25, 64, 202, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'asdsa', '', 'sadas', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g64/u9/0f04ea324e3230f9eb7dbff5fc1f0889.pdf', 'OLIVEROS CV.pdf', '6beb825e0ee6318a8e6a2c54d3bbb9b3', 0.00, 0.00, 'Approved', '2026-08-31 05:28:34', '2026-08-31 05:29:55'),
(80, 25, 64, 203, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'asdasd', '', 'asda', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g64/u9/962a28233c08de26f2e9ef1209db1e82.pdf', 'OLIVEROS CV.pdf', '4a0fbed0e485071490abe7597e04bf16', 0.00, 0.00, 'Approved', '2026-08-31 05:28:45', '2026-08-31 05:30:03'),
(81, 25, 64, 204, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'asdasd', '', 'asdas', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g64/u9/cb8e4d50ad6222d2ac4243ba948e77b0.pdf', 'OLIVEROS CV.pdf', 'ef856f3399d1eb5c6220293ae928b8a5', 0.00, 0.00, 'Approved', '2026-08-31 05:28:53', '2026-08-31 06:15:08'),
(82, 25, 64, 205, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'adsad', '', 'asda', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g64/u9/ddd91c717013890ffa4bd9e6503f9789.pdf', 'OLIVEROS CV.pdf', '588a7637ead45e533964661ef45235cc', 0.00, 0.00, 'Approved', '2026-08-31 05:29:03', '2026-08-31 06:15:17'),
(83, 25, 64, 206, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'asdas', '', 'sadas', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g64/u9/3c49d34ce74b8c1fb4f6a3820bc4fd77.pdf', 'Diaz CV.pdf_20260813_105004_0000.pdf', 'f3b467991b22a417cc5ac786133470cb', 0.00, 0.00, 'Approved', '2026-08-31 05:29:15', '2026-08-31 06:15:28'),
(84, 25, 64, 207, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'asdas', '', 'sdada', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g64/u9/e9b4c863c0cdcb67975619e7f63d9e57.pdf', 'OLIVEROS CV.pdf', '4909840571a5dd9de8c7713abea1e589', 0.00, 0.00, 'Approved', '2026-08-31 05:29:24', '2026-08-31 06:15:35'),
(85, 25, 64, 208, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'asdad', '', 'sadas', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g64/u9/c1b6a2b034dbec7c9fe660ba7ab5cddb.pdf', 'OLIVEROS CV.pdf', '106cb9c755aefea1b9e2cd409390e32f', 0.00, 0.00, 'Approved', '2026-08-31 05:29:32', '2026-08-31 06:15:42'),
(86, 26, 65, 209, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'eadsa', '', 'asdas', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g65/u9/0b9cb630bca2013fc5b5055241753819.pdf', 'OLIVEROS CV.pdf', '83ab9604308555f7be25b0e22e83a444', 0.00, 0.00, 'Approved', '2026-08-31 09:34:32', '2026-08-31 09:36:20'),
(87, 26, 65, 210, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'sdfds', '', 'sdfsd', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g65/u9/ef6e446036b4ba2a88d92faf65ddc654.pdf', 'OLIVEROS CV.pdf', '09bb616bb90ee961baeda499028434b3', 0.00, 0.00, 'Approved', '2026-08-31 09:34:41', '2026-08-31 09:36:29'),
(88, 26, 65, 211, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'asdas', '', 'sadas', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g65/u9/b0b0378a61ce66eaaca964143b30b63d.pdf', 'OLIVEROS CV.pdf', '6880a5506088e0b98ba8ee2cd202c193', 0.00, 0.00, 'Approved', '2026-08-31 09:34:49', '2026-08-31 09:36:53'),
(89, 26, 65, 212, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'sadas', '', 'asda', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g65/u9/06cc39770a42bf06fff7da5285a54c52.pdf', 'OLIVEROS CV.pdf', 'c6d14c3b5a11e89648f42eb1a5922592', 0.00, 0.00, 'Submitted for Review', '2026-08-31 09:35:01', '2026-08-31 09:35:01'),
(90, 26, 65, 213, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'sadsad', '', 'asdas', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g65/u9/4b2c7b8f8662c0acfecf51b09123ed51.pdf', 'OLIVEROS CV.pdf', '37dcc8cb4c7ac5030b7e408f23e510db', 0.00, 0.00, 'Submitted for Review', '2026-08-31 09:35:12', '2026-08-31 09:35:12'),
(91, 26, 65, 214, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'eqweq', '', 'qweqw', NULL, NULL, 'f3c04411627f6e3711879b7b2670948c', 0.00, 0.00, 'Submitted for Review', '2026-08-31 09:35:29', '2026-08-31 09:35:29'),
(92, 26, 65, 215, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'qweqw', '', 'qweq', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g65/u9/3f2e2e77ca556957100166cd43b11e4b.pdf', 'OLIVEROS CV.pdf', '4b86cfc220d70f494f5a694899731d73', 0.00, 0.00, 'Submitted for Review', '2026-08-31 09:35:37', '2026-08-31 09:35:37'),
(93, 26, 65, 216, 9, 'Student User', 'DEVELOPMENT OF AI ANALYSIS', 'fdssdfsd', '', 'weqwe', 'C:\\xampp\\htdocs\\sms2_system/storage/uploads/research_progress/g65/u9/afc9627ed4ff5d61ef4658268ee83f4c.pdf', 'OLIVEROS CV.pdf', '90c4c6b20d3280f3e1c556985d525e17', 0.00, 0.00, 'Submitted for Review', '2026-08-31 09:35:48', '2026-08-31 09:35:48');

-- --------------------------------------------------------

--
-- Table structure for table `research_proposals`
--

CREATE TABLE `research_proposals` (
  `id` int(10) UNSIGNED NOT NULL,
  `ref_code` varchar(30) NOT NULL COMMENT 'Auto-generated reference e.g. CRD-2026-00001',
  `proposal_number` varchar(30) DEFAULT NULL COMMENT 'Official number generated after approved proposal registration',
  `research_title` varchar(500) NOT NULL,
  `program_course` varchar(200) NOT NULL,
  `year_section` varchar(100) NOT NULL,
  `college_department` varchar(200) NOT NULL,
  `research_adviser` varchar(200) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `rep_name` varchar(200) NOT NULL,
  `rep_id` varchar(50) NOT NULL,
  `rep_email` varchar(200) NOT NULL,
  `rep_contact` varchar(20) NOT NULL,
  `status` enum('Submitted','In Progress','Panel Assigned','Approved','Returned') NOT NULL DEFAULT 'Submitted',
  `progress` tinyint(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT 'Progress % shown in tracking',
  `date_submitted` date NOT NULL,
  `approved_at` datetime DEFAULT NULL COMMENT 'Date/time when tracking proposal was approved',
  `registered_at` datetime DEFAULT NULL COMMENT 'Date/time when approved proposal received official proposal number',
  `registration_status` enum('Pending','Registered') NOT NULL DEFAULT 'Pending',
  `signature_data` mediumtext DEFAULT NULL COMMENT 'Base64 PNG of representative signature',
  `submitted_by_user` int(10) UNSIGNED DEFAULT NULL COMMENT 'FK to sms2_db users (optional)',
  `notes` text DEFAULT NULL COMMENT 'CRAD officer notes',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `research_revision_cycles`
--

CREATE TABLE `research_revision_cycles` (
  `id` int(10) UNSIGNED NOT NULL,
  `research_group_id` int(10) UNSIGNED NOT NULL,
  `defense_schedule_id` int(10) UNSIGNED NOT NULL,
  `official_result` varchar(60) NOT NULL DEFAULT 'APPROVED WITH REVISION',
  `revision_status` varchar(60) NOT NULL DEFAULT 'Needs Revision',
  `opened_at` datetime NOT NULL DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `original_name` varchar(255) NOT NULL DEFAULT '',
  `stored_subdir` varchar(180) NOT NULL DEFAULT '',
  `stored_name` varchar(120) NOT NULL DEFAULT '',
  `file_size` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `file_mime` varchar(120) NOT NULL DEFAULT '',
  `submission_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `research_venues`
--

CREATE TABLE `research_venues` (
  `id` int(10) UNSIGNED NOT NULL,
  `venue_name` varchar(160) NOT NULL,
  `capacity` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `venue_type` varchar(80) NOT NULL DEFAULT '',
  `status` varchar(40) NOT NULL DEFAULT 'Available',
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `research_venues`
--

INSERT INTO `research_venues` (`id`, `venue_name`, `capacity`, `venue_type`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'CRAD Conference Room', 30, 'Conference Room', 'Available', NULL, '2026-08-10 13:50:31', '2026-08-10 13:50:31'),
(2, 'Research Room 1', 25, 'Research Room', 'Available', NULL, '2026-08-10 13:50:31', '2026-08-10 13:50:31'),
(3, 'Research Room 2', 25, 'Research Room', 'Available', NULL, '2026-08-10 13:50:31', '2026-08-10 13:50:31'),
(4, 'AVR Room', 100, 'Auditorium', 'Available', NULL, '2026-08-10 13:50:31', '2026-08-10 14:20:53'),
(5, 'Computer Laboratory 1', 40, 'Laboratory', 'Available', NULL, '2026-08-10 13:50:31', '2026-08-10 13:50:31');

-- --------------------------------------------------------

--
-- Table structure for table `title_approvals`
--

CREATE TABLE `title_approvals` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` varchar(50) NOT NULL DEFAULT '',
  `student_user_id` int(10) UNSIGNED DEFAULT NULL,
  `student_name` varchar(200) NOT NULL DEFAULT '',
  `submission_date` date NOT NULL,
  `department` varchar(200) NOT NULL DEFAULT '',
  `proposed_title` varchar(500) NOT NULL DEFAULT '',
  `discipline_cluster` varchar(200) NOT NULL DEFAULT '',
  `primary_sdg` varchar(120) NOT NULL DEFAULT '',
  `research_agenda` varchar(300) NOT NULL DEFAULT '',
  `sdg_justification` text NOT NULL,
  `members_json` longtext NOT NULL,
  `adviser_name` varchar(200) NOT NULL DEFAULT '',
  `adviser_email` varchar(200) NOT NULL DEFAULT '',
  `coordinator_name` varchar(200) NOT NULL DEFAULT '',
  `proposal_number` varchar(30) DEFAULT NULL,
  `status` enum('Pending','Reviewed','Approved','Returned') NOT NULL DEFAULT 'Pending',
  `adviser_remarks` text DEFAULT NULL,
  `adviser_signature_data` mediumtext DEFAULT NULL,
  `coordinator_status` varchar(30) NOT NULL DEFAULT 'Not Ready',
  `coordinator_remarks` text DEFAULT NULL,
  `coordinator_screening_json` text DEFAULT NULL,
  `coordinator_signature_data` mediumtext DEFAULT NULL,
  `coordinator_reviewed_at` datetime DEFAULT NULL,
  `crad_status` varchar(30) NOT NULL DEFAULT 'Not Ready',
  `crad_signature_data` mediumtext DEFAULT NULL,
  `crad_reviewed_at` datetime DEFAULT NULL,
  `sent_at` datetime NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `title_approvals`
--

INSERT INTO `title_approvals` (`id`, `student_id`, `student_user_id`, `student_name`, `submission_date`, `department`, `proposed_title`, `discipline_cluster`, `primary_sdg`, `research_agenda`, `sdg_justification`, `members_json`, `adviser_name`, `adviser_email`, `coordinator_name`, `proposal_number`, `status`, `adviser_remarks`, `adviser_signature_data`, `coordinator_status`, `coordinator_remarks`, `coordinator_screening_json`, `coordinator_signature_data`, `coordinator_reviewed_at`, `crad_status`, `crad_signature_data`, `crad_reviewed_at`, `sent_at`, `reviewed_at`, `created_at`, `updated_at`) VALUES
(54, 'S230000001', 9, 'Student User', '2026-09-01', 'College of Computer Studies', 'DEVELOPMENT OF AI ANALYSIS', 'Engineering, Information Technology, and Computing', 'SDG 9 — Industry, Innovation and Infrastructure', 'Science, Technology, Digital Transformation, and Innovation', 'adadsadasdasda', '[[\"User, Student A.\",\"BSIT 4101\",\"OR-2646376\"]]', 'Dr. Roberto M. Santos', 'rsantos@bestlink.edu.ph', 'Mrs. Kris Guevarra', 'TAP-2026-00054', 'Approved', NULL, 'data:image/png;base64,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', 'Approved', NULL, '{\"agenda_alignment\":\"yes\",\"feasible_original\":\"yes\",\"ethical_sdg\":\"yes\"}', 'data:image/png;base64,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', '2026-08-31 09:27:29', 'Approved', 'data:image/png;base64,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', '2026-08-31 09:28:12', '2026-08-31 09:26:24', '2026-08-31 09:26:37', '2026-08-31 09:26:24', '2026-08-31 09:28:17');

--
-- Triggers `title_approvals`
--
DELIMITER $$
CREATE TRIGGER `trg_title_approvals_after_delete` AFTER DELETE ON `title_approvals` FOR EACH ROW BEGIN
            UPDATE research_adviser_assignments a
               SET a.assignment_status = 'Pending'
             WHERE a.assignment_status = 'Assigned'
               AND (
                    (OLD.proposal_number IS NOT NULL
                     AND OLD.proposal_number <> ''
                     AND a.proposal_number = OLD.proposal_number)
                 OR (a.research_group_id IS NOT NULL
                     AND a.research_group_id IN (
                        SELECT g.id
                        FROM research_groups g
                        WHERE g.title_approval_id = OLD.id
                     ))
                 OR (a.group_number IS NOT NULL
                     AND a.group_number <> ''
                     AND a.group_number IN (
                        SELECT g2.group_number
                        FROM research_groups g2
                        WHERE g2.title_approval_id = OLD.id
                     ))
               );
        END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chapter_evaluations`
--
ALTER TABLE `chapter_evaluations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_chapter_evaluation_submission` (`submission_id`),
  ADD KEY `idx_chapter_eval_evaluator` (`evaluator_user_id`),
  ADD KEY `idx_chapter_eval_group` (`research_group_id`),
  ADD KEY `idx_chapter_eval_created` (`created_at`);

--
-- Indexes for table `chapter_evaluation_notifications`
--
ALTER TABLE `chapter_evaluation_notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_chapter_notification_event` (`event_key`),
  ADD KEY `idx_chapter_notification_recipient` (`recipient_user_id`,`recipient_role`,`recipient_email`),
  ADD KEY `idx_chapter_notification_submission` (`submission_id`),
  ADD KEY `idx_chapter_notification_created` (`created_at`);

--
-- Indexes for table `chapter_submissions`
--
ALTER TABLE `chapter_submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_chapter_version` (`research_group_id`,`chapter_number`,`version_number`),
  ADD UNIQUE KEY `uniq_chapter_token` (`submission_token`),
  ADD KEY `idx_chapter_status` (`status`),
  ADD KEY `idx_chapter_group` (`research_group_id`),
  ADD KEY `idx_chapter_student` (`submitted_by_user`),
  ADD KEY `idx_chapter_updated` (`updated_at`);

--
-- Indexes for table `chapter_submission_history`
--
ALTER TABLE `chapter_submission_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_chapter_history_submission` (`submission_id`),
  ADD KEY `idx_chapter_history_group` (`research_group_id`),
  ADD KEY `idx_chapter_history_created` (`created_at`);

--
-- Indexes for table `final_defense_evaluations`
--
ALTER TABLE `final_defense_evaluations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_final_panel_submission` (`defense_schedule_id`,`panel_user_id`),
  ADD KEY `idx_final_group` (`research_group_id`),
  ADD KEY `idx_final_panel` (`panel_user_id`),
  ADD KEY `idx_final_status` (`status`);

--
-- Indexes for table `final_defense_recommendations`
--
ALTER TABLE `final_defense_recommendations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_fdr_group` (`research_group_id`),
  ADD KEY `idx_fdr_status` (`status`);

--
-- Indexes for table `final_manuscript_approvals`
--
ALTER TABLE `final_manuscript_approvals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_fma_group` (`research_group_id`);

--
-- Indexes for table `grant_applications`
--
ALTER TABLE `grant_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_ga_token` (`submission_token`),
  ADD KEY `idx_ga_opportunity` (`grant_opportunity_id`),
  ADD KEY `idx_ga_group` (`research_group_id`),
  ADD KEY `idx_ga_status` (`status`),
  ADD KEY `idx_ga_submitted` (`submitted_at`);

--
-- Indexes for table `grant_document_repository`
--
ALTER TABLE `grant_document_repository`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_gdr_application` (`grant_application_id`),
  ADD KEY `idx_gdr_reference` (`archive_reference`),
  ADD KEY `idx_gdr_archived` (`archived_at`);

--
-- Indexes for table `grant_document_repository_items`
--
ALTER TABLE `grant_document_repository_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_gdri_repository` (`repository_id`),
  ADD KEY `idx_gdri_application` (`grant_application_id`),
  ADD KEY `idx_gdri_category` (`category`);

--
-- Indexes for table `grant_final_output_submissions`
--
ALTER TABLE `grant_final_output_submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_gfos_application` (`grant_application_id`),
  ADD KEY `idx_gfos_status` (`status`),
  ADD KEY `idx_gfos_submitted` (`submitted_at`);

--
-- Indexes for table `grant_funded_progress_evidence`
--
ALTER TABLE `grant_funded_progress_evidence`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_gfpe_application` (`grant_application_id`),
  ADD KEY `idx_gfpe_milestone` (`milestone_id`),
  ADD KEY `idx_gfpe_created` (`created_at`);

--
-- Indexes for table `grant_funded_project_milestones`
--
ALTER TABLE `grant_funded_project_milestones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_gfpm_app_order` (`grant_application_id`,`milestone_order`),
  ADD KEY `idx_gfpm_application` (`grant_application_id`),
  ADD KEY `idx_gfpm_status` (`status`);

--
-- Indexes for table `grant_funding_disbursements`
--
ALTER TABLE `grant_funding_disbursements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_gfd_app_tranche` (`grant_application_id`,`tranche_number`),
  ADD KEY `idx_gfd_application` (`grant_application_id`),
  ADD KEY `idx_gfd_status` (`status`);

--
-- Indexes for table `grant_opportunities`
--
ALTER TABLE `grant_opportunities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_go_status` (`status`),
  ADD KEY `idx_go_deadline` (`application_deadline`),
  ADD KEY `idx_go_created_by` (`created_by_user_id`);

--
-- Indexes for table `grant_proposal_approval_steps`
--
ALTER TABLE `grant_proposal_approval_steps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_gpas_workflow_step` (`workflow_id`,`step_key`),
  ADD KEY `idx_gpas_application` (`grant_application_id`),
  ADD KEY `idx_gpas_status` (`status`),
  ADD KEY `idx_gpas_role` (`approver_role_key`);

--
-- Indexes for table `grant_proposal_approval_workflows`
--
ALTER TABLE `grant_proposal_approval_workflows`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_gpaw_application` (`grant_application_id`),
  ADD KEY `idx_gpaw_status` (`workflow_status`),
  ADD KEY `idx_gpaw_current_step` (`current_step_key`);

--
-- Indexes for table `grant_proposal_evaluations`
--
ALTER TABLE `grant_proposal_evaluations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_gpe_app_eval_ver` (`grant_application_id`,`evaluator_user_id`,`proposal_version`),
  ADD KEY `idx_gpe_application` (`grant_application_id`),
  ADD KEY `idx_gpe_evaluator` (`evaluator_user_id`),
  ADD KEY `idx_gpe_submitted` (`submitted_at`);

--
-- Indexes for table `grant_proposal_notifications`
--
ALTER TABLE `grant_proposal_notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_gpn_event` (`event_key`),
  ADD KEY `idx_gpn_recipient_user` (`recipient_user_id`),
  ADD KEY `idx_gpn_application` (`grant_application_id`),
  ADD KEY `idx_gpn_created` (`created_at`);

--
-- Indexes for table `grant_proposal_versions`
--
ALTER TABLE `grant_proposal_versions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_gpv_app_ver` (`grant_application_id`,`version_number`),
  ADD KEY `idx_gpv_application` (`grant_application_id`),
  ADD KEY `idx_gpv_submitted` (`submitted_at`);

--
-- Indexes for table `grant_publications_ip_repository`
--
ALTER TABLE `grant_publications_ip_repository`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_gpip_application` (`grant_application_id`),
  ADD KEY `idx_gpip_reference` (`repository_reference`),
  ADD KEY `idx_gpip_verified` (`verified_at`);

--
-- Indexes for table `manuscript_evaluations`
--
ALTER TABLE `manuscript_evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_meval_submission` (`submission_id`),
  ADD KEY `idx_meval_group` (`research_group_id`);

--
-- Indexes for table `manuscript_submissions`
--
ALTER TABLE `manuscript_submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_manuscript_version` (`research_group_id`,`version_number`),
  ADD UNIQUE KEY `uniq_manuscript_token` (`submission_token`),
  ADD KEY `idx_manuscript_status` (`status`),
  ADD KEY `idx_manuscript_group` (`research_group_id`);

--
-- Indexes for table `panel_assignment_notifications`
--
ALTER TABLE `panel_assignment_notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_panel_assignment_notification` (`event_key`),
  ADD KEY `idx_panel_notification_recipient` (`recipient_user_id`,`recipient_role`,`recipient_email`),
  ADD KEY `idx_panel_notification_created` (`created_at`);

--
-- Indexes for table `panel_member_availability`
--
ALTER TABLE `panel_member_availability`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_panel_availability_user` (`panel_user_id`),
  ADD KEY `idx_panel_availability_status` (`availability_status`);

--
-- Indexes for table `preoral_defense_evaluations`
--
ALTER TABLE `preoral_defense_evaluations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_preoral_panel_submission` (`defense_schedule_id`,`panel_user_id`),
  ADD KEY `idx_preoral_group` (`research_group_id`),
  ADD KEY `idx_preoral_panel` (`panel_user_id`),
  ADD KEY `idx_preoral_status` (`status`);

--
-- Indexes for table `proposal_documents`
--
ALTER TABLE `proposal_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pd_proposal` (`proposal_id`);

--
-- Indexes for table `proposal_drafts`
--
ALTER TABLE `proposal_drafts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_proposal_draft_student_type` (`student_id`,`form_type`);

--
-- Indexes for table `proposal_members`
--
ALTER TABLE `proposal_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_proposal` (`proposal_id`);

--
-- Indexes for table `proposal_status_logs`
--
ALTER TABLE `proposal_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_psl_proposal` (`proposal_id`);

--
-- Indexes for table `publications`
--
ALTER TABLE `publications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pub_group` (`research_group_id`),
  ADD KEY `idx_pub_status` (`status`);

--
-- Indexes for table `research_adviser_assignments`
--
ALTER TABLE `research_adviser_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_raa_adviser_identity` (`adviser_email`,`adviser_name`),
  ADD KEY `idx_raa_group` (`research_group_id`),
  ADD KEY `idx_raa_proposal` (`proposal_id`),
  ADD KEY `idx_raa_group_number` (`group_number`),
  ADD KEY `idx_raa_status` (`assignment_status`),
  ADD KEY `idx_raa_user` (`adviser_user_id`);

--
-- Indexes for table `research_coordinator_assignments`
--
ALTER TABLE `research_coordinator_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_rca_group_number` (`group_number`),
  ADD UNIQUE KEY `uniq_rca_group_coordinator` (`research_group_id`,`coordinator_user_id`),
  ADD KEY `idx_rca_group` (`research_group_id`),
  ADD KEY `idx_rca_title_approval` (`title_approval_id`),
  ADD KEY `idx_rca_status` (`status`);

--
-- Indexes for table `research_defense_schedules`
--
ALTER TABLE `research_defense_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rds_proposal_number` (`proposal_number`),
  ADD KEY `idx_rds_status` (`status`),
  ADD KEY `idx_rds_proposal_id` (`proposal_id`),
  ADD KEY `idx_rds_venue_time` (`venue_id`,`defense_datetime`,`defense_end_datetime`),
  ADD KEY `idx_rds_group_time` (`research_group_id`,`defense_datetime`,`defense_end_datetime`),
  ADD KEY `idx_rds_group_number` (`group_number`);

--
-- Indexes for table `research_groups`
--
ALTER TABLE `research_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `group_number` (`group_number`),
  ADD UNIQUE KEY `proposal_id` (`proposal_id`),
  ADD UNIQUE KEY `title_approval_id` (`title_approval_id`),
  ADD KEY `idx_rg_proposal_number` (`proposal_number`);

--
-- Indexes for table `research_milestones`
--
ALTER TABLE `research_milestones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_rm_plan_name` (`research_plan_id`,`milestone_name`),
  ADD KEY `idx_rm_plan` (`research_plan_id`),
  ADD KEY `idx_rm_status` (`status`),
  ADD KEY `idx_rm_sequence` (`research_plan_id`,`milestone_order`);

--
-- Indexes for table `research_panel_assignments`
--
ALTER TABLE `research_panel_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_panel_assignment_phase` (`research_group_id`,`panel_user_id`,`defense_phase`),
  ADD KEY `idx_panel_assignment_group` (`research_group_id`),
  ADD KEY `idx_panel_assignment_user` (`panel_user_id`),
  ADD KEY `idx_panel_assignment_status` (`assignment_status`),
  ADD KEY `idx_panel_assignment_schedule` (`defense_schedule_id`);

--
-- Indexes for table `research_plans`
--
ALTER TABLE `research_plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_rp_group` (`research_group_id`),
  ADD KEY `idx_rp_group_number` (`group_number`),
  ADD KEY `idx_rp_adviser` (`adviser_id`),
  ADD KEY `idx_rp_status` (`status`);

--
-- Indexes for table `research_progress_activity_logs`
--
ALTER TABLE `research_progress_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rpal_plan` (`research_plan_id`),
  ADD KEY `idx_rpal_user` (`user_id`),
  ADD KEY `idx_rpal_action` (`action`),
  ADD KEY `idx_rpal_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_rpal_created` (`created_at`);

--
-- Indexes for table `research_progress_attachments`
--
ALTER TABLE `research_progress_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rpa_update` (`progress_update_id`),
  ADD KEY `idx_rpa_uploaded` (`uploaded_by`);

--
-- Indexes for table `research_progress_feedback`
--
ALTER TABLE `research_progress_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rpf_update` (`progress_update_id`),
  ADD KEY `idx_rpf_milestone` (`milestone_id`),
  ADD KEY `idx_rpf_plan` (`research_plan_id`),
  ADD KEY `idx_rpf_created` (`created_at`),
  ADD KEY `idx_rpf_adviser` (`adviser_user_id`),
  ADD KEY `idx_rpf_token` (`submission_token`),
  ADD KEY `idx_rpf_update_adviser` (`progress_update_id`,`adviser_user_id`),
  ADD KEY `idx_rpf_plan_type` (`research_plan_id`,`feedback_type`);

--
-- Indexes for table `research_progress_notifications`
--
ALTER TABLE `research_progress_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rpn_recipient_user` (`recipient_user_id`),
  ADD KEY `idx_rpn_recipient_email` (`recipient_email`),
  ADD KEY `idx_rpn_recipient_role` (`recipient_role`),
  ADD KEY `idx_rpn_batch_key` (`batch_key`),
  ADD KEY `idx_rpn_status` (`status`),
  ADD KEY `idx_rpn_created` (`created_at`);

--
-- Indexes for table `research_progress_updates`
--
ALTER TABLE `research_progress_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rpu_plan` (`research_plan_id`),
  ADD KEY `idx_rpu_milestone` (`milestone_id`),
  ADD KEY `idx_rpu_researcher` (`submitted_by_user_id`),
  ADD KEY `idx_rpu_submitted` (`submitted_at`),
  ADD KEY `idx_rpu_group` (`research_group_id`),
  ADD KEY `idx_rpu_token` (`submission_token`),
  ADD KEY `idx_rpu_group_milestone` (`research_group_id`,`milestone_id`),
  ADD KEY `idx_rpu_plan_submitted` (`research_plan_id`,`submitted_at`);

--
-- Indexes for table `research_proposals`
--
ALTER TABLE `research_proposals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ref_code` (`ref_code`),
  ADD UNIQUE KEY `proposal_number` (`proposal_number`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_dept` (`college_department`(50)),
  ADD KEY `idx_submitted` (`date_submitted`);

--
-- Indexes for table `research_revision_cycles`
--
ALTER TABLE `research_revision_cycles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_rrc_schedule` (`defense_schedule_id`),
  ADD KEY `idx_rrc_group` (`research_group_id`),
  ADD KEY `idx_rrc_status` (`revision_status`);

--
-- Indexes for table `research_venues`
--
ALTER TABLE `research_venues`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_research_venue_name` (`venue_name`),
  ADD KEY `idx_research_venues_status` (`status`);

--
-- Indexes for table `title_approvals`
--
ALTER TABLE `title_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ta_student_id` (`student_id`),
  ADD KEY `idx_ta_adviser_email` (`adviser_email`(100)),
  ADD KEY `idx_ta_status` (`status`),
  ADD KEY `idx_ta_sent_at` (`sent_at`),
  ADD KEY `idx_ta_proposal_number` (`proposal_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chapter_evaluations`
--
ALTER TABLE `chapter_evaluations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `chapter_evaluation_notifications`
--
ALTER TABLE `chapter_evaluation_notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `chapter_submissions`
--
ALTER TABLE `chapter_submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `chapter_submission_history`
--
ALTER TABLE `chapter_submission_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `final_defense_evaluations`
--
ALTER TABLE `final_defense_evaluations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `final_defense_recommendations`
--
ALTER TABLE `final_defense_recommendations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `final_manuscript_approvals`
--
ALTER TABLE `final_manuscript_approvals`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `grant_applications`
--
ALTER TABLE `grant_applications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `grant_document_repository`
--
ALTER TABLE `grant_document_repository`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grant_document_repository_items`
--
ALTER TABLE `grant_document_repository_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grant_final_output_submissions`
--
ALTER TABLE `grant_final_output_submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `grant_funded_progress_evidence`
--
ALTER TABLE `grant_funded_progress_evidence`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `grant_funded_project_milestones`
--
ALTER TABLE `grant_funded_project_milestones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `grant_funding_disbursements`
--
ALTER TABLE `grant_funding_disbursements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `grant_opportunities`
--
ALTER TABLE `grant_opportunities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `grant_proposal_approval_steps`
--
ALTER TABLE `grant_proposal_approval_steps`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `grant_proposal_approval_workflows`
--
ALTER TABLE `grant_proposal_approval_workflows`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `grant_proposal_evaluations`
--
ALTER TABLE `grant_proposal_evaluations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `grant_proposal_notifications`
--
ALTER TABLE `grant_proposal_notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `grant_proposal_versions`
--
ALTER TABLE `grant_proposal_versions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `grant_publications_ip_repository`
--
ALTER TABLE `grant_publications_ip_repository`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `manuscript_evaluations`
--
ALTER TABLE `manuscript_evaluations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `manuscript_submissions`
--
ALTER TABLE `manuscript_submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `panel_assignment_notifications`
--
ALTER TABLE `panel_assignment_notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `panel_member_availability`
--
ALTER TABLE `panel_member_availability`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `preoral_defense_evaluations`
--
ALTER TABLE `preoral_defense_evaluations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `proposal_documents`
--
ALTER TABLE `proposal_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT for table `proposal_drafts`
--
ALTER TABLE `proposal_drafts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `proposal_members`
--
ALTER TABLE `proposal_members`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `proposal_status_logs`
--
ALTER TABLE `proposal_status_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT for table `publications`
--
ALTER TABLE `publications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `research_adviser_assignments`
--
ALTER TABLE `research_adviser_assignments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `research_coordinator_assignments`
--
ALTER TABLE `research_coordinator_assignments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `research_defense_schedules`
--
ALTER TABLE `research_defense_schedules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `research_groups`
--
ALTER TABLE `research_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `research_milestones`
--
ALTER TABLE `research_milestones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=217;

--
-- AUTO_INCREMENT for table `research_panel_assignments`
--
ALTER TABLE `research_panel_assignments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `research_plans`
--
ALTER TABLE `research_plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `research_progress_activity_logs`
--
ALTER TABLE `research_progress_activity_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- AUTO_INCREMENT for table `research_progress_attachments`
--
ALTER TABLE `research_progress_attachments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `research_progress_feedback`
--
ALTER TABLE `research_progress_feedback`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `research_progress_notifications`
--
ALTER TABLE `research_progress_notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `research_progress_updates`
--
ALTER TABLE `research_progress_updates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `research_proposals`
--
ALTER TABLE `research_proposals`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `research_revision_cycles`
--
ALTER TABLE `research_revision_cycles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `research_venues`
--
ALTER TABLE `research_venues`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14032;

--
-- AUTO_INCREMENT for table `title_approvals`
--
ALTER TABLE `title_approvals`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `proposal_documents`
--
ALTER TABLE `proposal_documents`
  ADD CONSTRAINT `fk_pd_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `research_proposals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `proposal_members`
--
ALTER TABLE `proposal_members`
  ADD CONSTRAINT `fk_pm_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `research_proposals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `proposal_status_logs`
--
ALTER TABLE `proposal_status_logs`
  ADD CONSTRAINT `fk_psl_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `research_proposals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `research_adviser_assignments`
--
ALTER TABLE `research_adviser_assignments`
  ADD CONSTRAINT `fk_raa_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `research_proposals` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `research_coordinator_assignments`
--
ALTER TABLE `research_coordinator_assignments`
  ADD CONSTRAINT `fk_rca_title_approval` FOREIGN KEY (`title_approval_id`) REFERENCES `title_approvals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `research_defense_schedules`
--
ALTER TABLE `research_defense_schedules`
  ADD CONSTRAINT `fk_rds_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `research_proposals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `research_groups`
--
ALTER TABLE `research_groups`
  ADD CONSTRAINT `fk_rg_title_approval` FOREIGN KEY (`title_approval_id`) REFERENCES `title_approvals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `research_milestones`
--
ALTER TABLE `research_milestones`
  ADD CONSTRAINT `fk_rm_research_plan` FOREIGN KEY (`research_plan_id`) REFERENCES `research_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `research_plans`
--
ALTER TABLE `research_plans`
  ADD CONSTRAINT `fk_rp_research_group` FOREIGN KEY (`research_group_id`) REFERENCES `research_groups` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `research_progress_activity_logs`
--
ALTER TABLE `research_progress_activity_logs`
  ADD CONSTRAINT `fk_rpal_research_plan` FOREIGN KEY (`research_plan_id`) REFERENCES `research_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `research_progress_attachments`
--
ALTER TABLE `research_progress_attachments`
  ADD CONSTRAINT `fk_rpa_progress_update` FOREIGN KEY (`progress_update_id`) REFERENCES `research_progress_updates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `research_progress_feedback`
--
ALTER TABLE `research_progress_feedback`
  ADD CONSTRAINT `fk_rpf_milestone` FOREIGN KEY (`milestone_id`) REFERENCES `research_milestones` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rpf_progress_update` FOREIGN KEY (`progress_update_id`) REFERENCES `research_progress_updates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rpf_research_plan` FOREIGN KEY (`research_plan_id`) REFERENCES `research_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `research_progress_updates`
--
ALTER TABLE `research_progress_updates`
  ADD CONSTRAINT `fk_rpu_milestone` FOREIGN KEY (`milestone_id`) REFERENCES `research_milestones` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rpu_research_plan` FOREIGN KEY (`research_plan_id`) REFERENCES `research_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
