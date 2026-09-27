-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 26, 2026 at 11:39 PM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `waterpoint_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` bigint UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'INITIAL_ADMIN_CREATED', 'USER', 1, 'Initial system administrator account created.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 13:46:56'),
(2, 1, 'LOGIN_SUCCESS', 'USER', 1, 'Staff user logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 14:01:21'),
(3, 1, 'LOGOUT', 'USER', 1, 'Staff user logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 14:04:00'),
(4, NULL, 'LOGIN_FAILED', 'USER', NULL, 'Unsuccessful staff login attempt for username: admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 14:04:07'),
(5, 1, 'LOGIN_SUCCESS', 'USER', 1, 'Staff user logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 14:23:34'),
(6, 1, 'LOGOUT', 'USER', 1, 'Staff user logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 14:23:40'),
(7, 1, 'LOGIN_SUCCESS', 'USER', 1, 'Staff user logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 14:23:42'),
(8, 1, 'WATER_POINT_REGISTERED', 'WATER_POINT', 1, 'Registered water point WP-000001 - Ishaka Community Borehole.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 14:29:05'),
(9, 1, 'LOGIN_SUCCESS', 'USER', 1, 'Staff user logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-26 14:41:39'),
(10, NULL, 'PUBLIC_FAULT_REPORTED', 'FAULT_REPORT', 1, 'Public fault report WFT-26-RNLVWJ submitted.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 15:07:39'),
(11, 1, 'LOGOUT', 'USER', 1, 'Staff user logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 15:08:24'),
(12, 1, 'LOGIN_SUCCESS', 'USER', 1, 'Staff user logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 15:08:31'),
(13, 1, 'LOGOUT', 'USER', 1, 'Staff user logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 15:09:47'),
(14, NULL, 'PUBLIC_FAULT_REPORTED', 'FAULT_REPORT', 2, 'Public fault report WFT-26-WMH4NP submitted.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 15:13:08'),
(15, 1, 'LOGIN_SUCCESS', 'USER', 1, 'Staff user logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 15:19:31'),
(16, 1, 'FAULT_REVIEWED', 'FAULT_REPORT', 2, 'Reviewed fault WFT-26-WMH4NP and assigned HIGH priority.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 15:20:38'),
(17, 1, 'LOGOUT', 'USER', 1, 'Staff user logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 20:57:56'),
(18, 1, 'LOGIN_SUCCESS', 'USER', 1, 'Staff user logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 21:14:41'),
(19, 1, 'FAULT_REVIEWED', 'FAULT_REPORT', 1, 'Reviewed fault WFT-26-RNLVWJ and assigned CRITICAL priority.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 21:15:58'),
(20, 1, 'USER_CREATED', 'USER', 2, 'Created COORDINATOR account.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 21:18:47'),
(21, 1, 'USER_CREATED', 'USER', 3, 'Created TECHNICIAN account.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 21:19:43'),
(22, 1, 'REPAIR_ASSIGN', 'FAULT_REPORT', 1, 'A technician has been assigned to the reported fault.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 21:21:44'),
(23, 1, 'LOGOUT', 'USER', 1, 'Staff user logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 21:23:49'),
(24, 3, 'LOGIN_SUCCESS', 'USER', 3, 'Staff user logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 21:23:57'),
(25, 3, 'REPAIR_ACCEPT', 'FAULT_REPORT', 1, 'The technician accepted the repair assignment.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 21:24:16'),
(26, 3, 'REPAIR_START', 'FAULT_REPORT', 1, 'Repair work is in progress.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 21:24:20'),
(27, 3, 'REPAIR_COMPLETE', 'FAULT_REPORT', 1, 'Repair completed; awaiting coordinator verification.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 21:24:40'),
(28, 3, 'LOGOUT', 'USER', 3, 'Staff user logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 21:24:51'),
(29, 1, 'LOGIN_SUCCESS', 'USER', 1, 'Staff user logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 21:27:48'),
(30, 1, 'LOGOUT', 'USER', 1, 'Staff user logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 21:27:58'),
(31, 2, 'LOGIN_SUCCESS', 'USER', 2, 'Staff user logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 21:28:12'),
(32, 2, 'REPAIR_ASSIGN', 'FAULT_REPORT', 2, 'A technician has been assigned to the reported fault.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 21:29:13'),
(33, 2, 'LOGOUT', 'USER', 2, 'Staff user logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 21:29:34'),
(34, 1, 'LOGIN_SUCCESS', 'USER', 1, 'Staff user logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 21:36:38'),
(35, 1, 'LOGOUT', 'USER', 1, 'Staff user logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 21:49:30'),
(36, 1, 'LOGIN_SUCCESS', 'USER', 1, 'Staff user logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:08:46'),
(37, 1, 'REPAIR_VERIFY', 'FAULT_REPORT', 1, 'The coordinator verified the repair and recorded the current water point condition.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:09:42'),
(38, 1, 'REPAIR_CLOSE', 'FAULT_REPORT', 1, 'The verified fault report has been closed.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:09:50'),
(39, 1, 'USER_STATUS_CHANGED', 'USER', 2, 'Account status changed to INACTIVE.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:10:55'),
(40, 1, 'USER_STATUS_CHANGED', 'USER', 2, 'Account status changed to ACTIVE.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:10:57'),
(41, 1, 'USER_STATUS_CHANGED', 'USER', 2, 'Account status changed to INACTIVE.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:11:00'),
(42, 1, 'LOGOUT', 'USER', 1, 'Staff user logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:11:03'),
(43, 2, 'LOGIN_BLOCKED', 'USER', 2, 'Login blocked because account status is INACTIVE.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:11:11'),
(44, 1, 'LOGIN_SUCCESS', 'USER', 1, 'Staff user logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:11:21'),
(45, 1, 'LOGOUT', 'USER', 1, 'Staff user logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:11:44'),
(46, 3, 'LOGIN_SUCCESS', 'USER', 3, 'Staff user logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:52:11'),
(47, 3, 'REPAIR_START', 'FAULT_REPORT', 2, 'Repair work is in progress.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:52:24'),
(48, 3, 'REPAIR_COMPLETE', 'FAULT_REPORT', 2, 'Repair completed; awaiting coordinator verification.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:52:49'),
(49, 3, 'LOGOUT', 'USER', 3, 'Staff user logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:53:18'),
(50, 1, 'LOGIN_SUCCESS', 'USER', 1, 'Staff user logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:53:29'),
(51, 1, 'REPAIR_RETURN', 'FAULT_REPORT', 2, 'The repair was returned for further work.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:53:47'),
(52, 1, 'REPAIR_ASSIGN', 'FAULT_REPORT', 2, 'A technician has been assigned to the reported fault.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:54:01'),
(53, 1, 'LOGOUT', 'USER', 1, 'Staff user logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:54:13'),
(54, 3, 'LOGIN_SUCCESS', 'USER', 3, 'Staff user logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:54:21'),
(55, 3, 'REPAIR_ACCEPT', 'FAULT_REPORT', 2, 'The technician accepted the repair assignment.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:54:43'),
(56, 3, 'REPAIR_START', 'FAULT_REPORT', 2, 'Repair work is in progress.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:54:46'),
(57, 3, 'REPAIR_UPDATE', 'FAULT_REPORT', 2, 'The technician recorded repair progress.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:54:57'),
(58, 3, 'REPAIR_COMPLETE', 'FAULT_REPORT', 2, 'Repair completed; awaiting coordinator verification.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:55:13'),
(59, 3, 'LOGOUT', 'USER', 3, 'Staff user logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:55:18'),
(60, 1, 'LOGIN_SUCCESS', 'USER', 1, 'Staff user logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:55:23'),
(61, 1, 'REPAIR_VERIFY', 'FAULT_REPORT', 2, 'The coordinator verified the repair and recorded the current water point condition.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:55:35'),
(62, 1, 'REPAIR_CLOSE', 'FAULT_REPORT', 2, 'The verified fault report has been closed.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:55:41'),
(63, 1, 'LOGOUT', 'USER', 1, 'Staff user logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:56:17'),
(64, NULL, 'PUBLIC_FAULT_REPORTED', 'FAULT_REPORT', 3, 'Public fault report WFT-26-6SH3XS submitted.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 22:56:48'),
(65, 1, 'LOGIN_SUCCESS', 'USER', 1, 'Staff user logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 23:04:37'),
(66, 1, 'USER_STATUS_CHANGED', 'USER', 2, 'Account status changed to ACTIVE.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 23:06:10'),
(67, 1, 'LOGOUT', 'USER', 1, 'Staff user logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-26 23:10:49');

-- --------------------------------------------------------

--
-- Table structure for table `fault_reports`
--

CREATE TABLE `fault_reports` (
  `id` bigint UNSIGNED NOT NULL,
  `fault_reference` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `report_scope` enum('KNOWN_WATER_POINT','OTHER_INFRASTRUCTURE') COLLATE utf8mb4_unicode_ci NOT NULL,
  `water_point_id` bigint UNSIGNED DEFAULT NULL,
  `infrastructure_type` enum('WATER_POINT','DISTRIBUTION_PIPE','MAIN_WATER_PIPE','PUBLIC_TAP','VALVE_CHAMBER','STORAGE_TANK','PUMPING_INFRASTRUCTURE','UNKNOWN','OTHER') COLLATE utf8mb4_unicode_ci NOT NULL,
  `fault_location` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `village` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fault_type` enum('NO_WATER','LOW_WATER_FLOW','BROKEN_PUMP','LEAKING_PIPE','BURST_PIPE','DAMAGED_TAP','CONTAMINATED_WATER','STRUCTURAL_DAMAGE','OTHER') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reporter_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reporter_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` enum('UNASSESSED','LOW','MEDIUM','HIGH','CRITICAL') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UNASSESSED',
  `status` enum('REPORTED','REVIEWED','ASSIGNED','IN_PROGRESS','COMPLETED','VERIFIED','RETURNED','CLOSED','CANCELLED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'REPORTED',
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `reported_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` datetime DEFAULT NULL,
  `assigned_at` datetime DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fault_reports`
--

INSERT INTO `fault_reports` (`id`, `fault_reference`, `report_scope`, `water_point_id`, `infrastructure_type`, `fault_location`, `village`, `fault_type`, `description`, `latitude`, `longitude`, `photo_path`, `reporter_name`, `reporter_phone`, `priority`, `status`, `reviewed_by`, `reported_at`, `reviewed_at`, `assigned_at`, `started_at`, `completed_at`, `verified_at`, `closed_at`, `cancelled_at`, `updated_at`) VALUES
(1, 'WFT-26-RNLVWJ', 'KNOWN_WATER_POINT', 1, 'WATER_POINT', NULL, 'Ishaka', 'NO_WATER', 'no water', -0.5434270, 30.1415400, NULL, 'Joshua Muhwezi', '+256786226774', 'CRITICAL', 'CLOSED', 1, '2026-09-26 15:07:39', '2026-09-27 00:15:58', NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-26 22:09:50'),
(2, 'WFT-26-WMH4NP', 'OTHER_INFRASTRUCTURE', NULL, 'STORAGE_TANK', 'KIU', 'ISHAKA', 'NO_WATER', 'NO WATER', -0.5434270, 30.1415400, 'uploads/faults/wft_26_wmh4np_3fcde012bc7b08db.png', 'Joshua Muhwezi', '+256786226774', 'HIGH', 'CLOSED', 1, '2026-09-26 15:13:08', '2026-09-26 18:20:38', NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-26 22:55:41'),
(3, 'WFT-26-6SH3XS', 'KNOWN_WATER_POINT', 1, 'WATER_POINT', NULL, 'Ishaka', 'BURST_PIPE', 'pipe mye', -0.5405165, 30.1380960, NULL, 'Joshua Muhwezi', '+256786226774', 'UNASSESSED', 'REPORTED', NULL, '2026-09-26 22:56:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-26 22:56:48');

-- --------------------------------------------------------

--
-- Table structure for table `fault_status_history`
--

CREATE TABLE `fault_status_history` (
  `id` bigint UNSIGNED NOT NULL,
  `fault_report_id` bigint UNSIGNED NOT NULL,
  `status` enum('REPORTED','REVIEWED','ASSIGNED','IN_PROGRESS','COMPLETED','VERIFIED','RETURNED','CLOSED','CANCELLED') COLLATE utf8mb4_unicode_ci NOT NULL,
  `changed_by` bigint UNSIGNED DEFAULT NULL,
  `public_note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `internal_note` text COLLATE utf8mb4_unicode_ci,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fault_status_history`
--

INSERT INTO `fault_status_history` (`id`, `fault_report_id`, `status`, `changed_by`, `public_note`, `internal_note`, `changed_at`) VALUES
(1, 1, 'REPORTED', NULL, 'Fault report received.', NULL, '2026-09-26 15:07:39'),
(2, 2, 'REPORTED', NULL, 'Fault report received.', NULL, '2026-09-26 15:13:08'),
(3, 2, 'REVIEWED', 1, 'Fault report reviewed and awaiting technician assignment.', 'SASASA', '2026-09-26 15:20:38'),
(4, 1, 'REVIEWED', 1, 'Fault report reviewed and awaiting technician assignment.', 'review', '2026-09-26 21:15:58'),
(5, 1, 'ASSIGNED', 1, 'A technician has been assigned to the reported fault.', 'repair', '2026-09-26 21:21:44'),
(6, 1, 'ASSIGNED', 3, 'The technician accepted the repair assignment.', NULL, '2026-09-26 21:24:16'),
(7, 1, 'IN_PROGRESS', 3, 'Repair work is in progress.', NULL, '2026-09-26 21:24:20'),
(8, 1, 'COMPLETED', 3, 'Repair completed; awaiting coordinator verification.', 'done', '2026-09-26 21:24:40'),
(9, 2, 'ASSIGNED', 2, 'A technician has been assigned to the reported fault.', 'clear this', '2026-09-26 21:29:13'),
(10, 1, 'VERIFIED', 1, 'The coordinator verified the repair and recorded the current water point condition.', 'RESTORED', '2026-09-26 22:09:42'),
(11, 1, 'CLOSED', 1, 'The verified fault report has been closed.', NULL, '2026-09-26 22:09:50'),
(12, 2, 'IN_PROGRESS', 3, 'Repair work is in progress.', NULL, '2026-09-26 22:52:24'),
(13, 2, 'COMPLETED', 3, 'Repair completed; awaiting coordinator verification.', 'ok', '2026-09-26 22:52:49'),
(14, 2, 'RETURNED', 1, 'The repair was returned for further work.', 'dsdss', '2026-09-26 22:53:47'),
(15, 2, 'ASSIGNED', 1, 'A technician has been assigned to the reported fault.', 'dsdsdsds', '2026-09-26 22:54:01'),
(16, 2, 'ASSIGNED', 3, 'The technician accepted the repair assignment.', NULL, '2026-09-26 22:54:43'),
(17, 2, 'IN_PROGRESS', 3, 'Repair work is in progress.', NULL, '2026-09-26 22:54:46'),
(18, 2, 'IN_PROGRESS', 3, 'The technician recorded repair progress.', 'done', '2026-09-26 22:54:57'),
(19, 2, 'COMPLETED', 3, 'Repair completed; awaiting coordinator verification.', 'done', '2026-09-26 22:55:13'),
(20, 2, 'VERIFIED', 1, 'The coordinator verified the repair and recorded the current water point condition.', 'ok', '2026-09-26 22:55:35'),
(21, 2, 'CLOSED', 1, 'The verified fault report has been closed.', NULL, '2026-09-26 22:55:41'),
(22, 3, 'REPORTED', NULL, 'Fault report received.', NULL, '2026-09-26 22:56:48');

-- --------------------------------------------------------

--
-- Table structure for table `repair_assignments`
--

CREATE TABLE `repair_assignments` (
  `id` bigint UNSIGNED NOT NULL,
  `fault_report_id` bigint UNSIGNED NOT NULL,
  `technician_id` bigint UNSIGNED NOT NULL,
  `assigned_by` bigint UNSIGNED NOT NULL,
  `assignment_notes` text COLLATE utf8mb4_unicode_ci,
  `expected_completion_at` datetime DEFAULT NULL,
  `status` enum('ASSIGNED','ACCEPTED','IN_PROGRESS','COMPLETED','CANCELLED','REASSIGNED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ASSIGNED',
  `assigned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `accepted_at` datetime DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `repair_assignments`
--

INSERT INTO `repair_assignments` (`id`, `fault_report_id`, `technician_id`, `assigned_by`, `assignment_notes`, `expected_completion_at`, `status`, `assigned_at`, `accepted_at`, `started_at`, `completed_at`, `updated_at`) VALUES
(1, 1, 3, 1, 'repair', '2026-09-28 00:21:00', 'COMPLETED', '2026-09-26 21:21:44', '2026-09-27 00:24:16', '2026-09-27 00:24:20', '2026-09-27 00:24:40', '2026-09-26 21:24:40'),
(2, 2, 3, 2, 'clear this', '2026-09-29 00:29:00', 'COMPLETED', '2026-09-26 21:29:13', NULL, '2026-09-27 01:52:24', '2026-09-27 01:52:49', '2026-09-26 22:52:49'),
(3, 2, 3, 1, 'dsdsdsds', '2026-09-30 01:53:00', 'COMPLETED', '2026-09-26 22:54:01', '2026-09-27 01:54:43', '2026-09-27 01:54:46', '2026-09-27 01:55:13', '2026-09-26 22:55:13');

-- --------------------------------------------------------

--
-- Table structure for table `repair_updates`
--

CREATE TABLE `repair_updates` (
  `id` bigint UNSIGNED NOT NULL,
  `assignment_id` bigint UNSIGNED NOT NULL,
  `update_type` enum('STARTED','PROGRESS','COMPLETED','NOTE') COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `repair_cost` decimal(14,2) DEFAULT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `repair_updates`
--

INSERT INTO `repair_updates` (`id`, `assignment_id`, `update_type`, `notes`, `repair_cost`, `photo_path`, `updated_by`, `created_at`) VALUES
(1, 1, 'COMPLETED', 'done', 1000000.00, 'uploads/repairs/repair_f1ef00788d19768bb809b5ef1a2d6a6a.png', 3, '2026-09-26 21:24:40'),
(2, 2, 'COMPLETED', 'ok', 8000000.00, 'uploads/repairs/repair_ec028f825629aeb05477e01f9e371ed5.png', 3, '2026-09-26 22:52:49'),
(3, 3, 'PROGRESS', 'done', 1000000.00, NULL, 3, '2026-09-26 22:54:57'),
(4, 3, 'COMPLETED', 'done', 10000000.00, NULL, 3, '2026-09-26 22:55:13');

-- --------------------------------------------------------

--
-- Table structure for table `repair_verifications`
--

CREATE TABLE `repair_verifications` (
  `id` bigint UNSIGNED NOT NULL,
  `fault_report_id` bigint UNSIGNED NOT NULL,
  `assignment_id` bigint UNSIGNED NOT NULL,
  `verified_by` bigint UNSIGNED NOT NULL,
  `decision` enum('APPROVED','RETURNED') COLLATE utf8mb4_unicode_ci NOT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `verified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `repair_verifications`
--

INSERT INTO `repair_verifications` (`id`, `fault_report_id`, `assignment_id`, `verified_by`, `decision`, `comments`, `verified_at`) VALUES
(1, 1, 1, 1, 'APPROVED', 'RESTORED', '2026-09-26 22:09:42'),
(2, 2, 2, 1, 'RETURNED', 'dsdss', '2026-09-26 22:53:47'),
(3, 2, 3, 1, 'APPROVED', 'ok', '2026-09-26 22:55:35');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_by`, `updated_at`) VALUES
(1, 'water_point_code_prefix', 'WP', 'Prefix used when generating water point codes.', NULL, '2026-09-26 13:35:48'),
(2, 'fault_reference_prefix', 'WFT', 'Prefix used when generating public fault references.', NULL, '2026-09-26 13:35:48'),
(3, 'fault_turnaround_target_hours', '48', 'Target number of hours within which a reported fault should be resolved.', NULL, '2026-09-26 13:35:48'),
(4, 'photo_max_size_mb', '5', 'Maximum image upload size in megabytes.', NULL, '2026-09-26 13:35:48'),
(5, 'water_point_next_number', '2', 'Next sequential number used when generating water point codes.', 1, '2026-09-26 14:29:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `full_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('ADMIN','COORDINATOR','TECHNICIAN') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE','LOCKED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `last_login_at` datetime DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `email`, `phone`, `password_hash`, `role`, `status`, `last_login_at`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Joshua Muhwezi', 'admin', 'muhwezijosh30@gmail.com', '+256786226774', '$2y$10$EtdqFCDGVtG4QD0GfVd/h.FtvoNN68l7xEkMxtW1I/nTcMZMZy28C', 'ADMIN', 'ACTIVE', '2026-09-27 02:04:37', NULL, '2026-09-26 13:46:56', '2026-09-26 23:04:37'),
(2, 'Clare Odo', 'clare', 'clare@kob.com', '+256786226775', '$2y$10$7LLjaJBptect4zcHp/vmUe00NDhOnvraWmrUd1bRkzDoYeiDeRpte', 'COORDINATOR', 'ACTIVE', '2026-09-27 00:28:12', 1, '2026-09-26 21:18:47', '2026-09-26 23:06:10'),
(3, 'Sarah Natamba', 'sarah', 'sarah@kob.com', '+256786226776', '$2y$10$tlbt6nvwWkH.O7t9hm6UVuNLdXOk2Fsw1yCxV.Ui1XG0s/Lvk31Cm', 'TECHNICIAN', 'ACTIVE', '2026-09-27 01:54:21', 1, '2026-09-26 21:19:43', '2026-09-26 22:54:21');

-- --------------------------------------------------------

--
-- Table structure for table `water_points`
--

CREATE TABLE `water_points` (
  `id` bigint UNSIGNED NOT NULL,
  `water_point_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('BOREHOLE','PROTECTED_SPRING','SHALLOW_WELL','PUBLIC_TAP','RAINWATER_TANK','STORAGE_TANK','OTHER') COLLATE utf8mb4_unicode_ci NOT NULL,
  `village` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('FUNCTIONAL','PARTIALLY_FUNCTIONAL','NON_FUNCTIONAL','UNDER_REPAIR') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FUNCTIONAL',
  `registered_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `water_points`
--

INSERT INTO `water_points` (`id`, `water_point_code`, `name`, `type`, `village`, `latitude`, `longitude`, `photo_path`, `status`, `registered_by`, `created_at`, `updated_at`) VALUES
(1, 'WP-000001', 'Ishaka Community Borehole', 'PUBLIC_TAP', 'Ishaka', -0.5434270, 30.1415400, 'uploads/water-points/wp_000001_a463b8720a1aca1e.png', 'FUNCTIONAL', 1, '2026-09-26 14:29:05', '2026-09-26 14:29:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_logs_user` (`user_id`),
  ADD KEY `idx_audit_logs_created_at` (`created_at`),
  ADD KEY `idx_audit_logs_entity` (`entity_type`,`entity_id`);

--
-- Indexes for table `fault_reports`
--
ALTER TABLE `fault_reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_fault_reports_reference` (`fault_reference`),
  ADD KEY `fk_fault_reports_reviewed_by` (`reviewed_by`),
  ADD KEY `idx_fault_reports_status` (`status`),
  ADD KEY `idx_fault_reports_priority` (`priority`),
  ADD KEY `idx_fault_reports_water_point` (`water_point_id`),
  ADD KEY `idx_fault_reports_reported_at` (`reported_at`),
  ADD KEY `idx_fault_reports_village` (`village`),
  ADD KEY `idx_fault_reports_scope_status` (`report_scope`,`status`);

--
-- Indexes for table `fault_status_history`
--
ALTER TABLE `fault_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_fault_history_user` (`changed_by`),
  ADD KEY `idx_fault_history_fault_time` (`fault_report_id`,`changed_at`);

--
-- Indexes for table `repair_assignments`
--
ALTER TABLE `repair_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_assignments_assigned_by` (`assigned_by`),
  ADD KEY `idx_assignments_fault` (`fault_report_id`),
  ADD KEY `idx_assignments_technician_status` (`technician_id`,`status`),
  ADD KEY `idx_assignments_due` (`expected_completion_at`);

--
-- Indexes for table `repair_updates`
--
ALTER TABLE `repair_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_repair_updates_user` (`updated_by`),
  ADD KEY `idx_repair_updates_assignment` (`assignment_id`),
  ADD KEY `idx_repair_updates_created_at` (`created_at`);

--
-- Indexes for table `repair_verifications`
--
ALTER TABLE `repair_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_verifications_user` (`verified_by`),
  ADD KEY `idx_verifications_fault` (`fault_report_id`),
  ADD KEY `idx_verifications_assignment` (`assignment_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_system_settings_key` (`setting_key`),
  ADD KEY `fk_system_settings_user` (`updated_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_username` (`username`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD KEY `fk_users_created_by` (`created_by`),
  ADD KEY `idx_users_role_status` (`role`,`status`);

--
-- Indexes for table `water_points`
--
ALTER TABLE `water_points`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_water_points_code` (`water_point_code`),
  ADD KEY `fk_water_points_registered_by` (`registered_by`),
  ADD KEY `idx_water_points_status` (`status`),
  ADD KEY `idx_water_points_village` (`village`),
  ADD KEY `idx_water_points_type` (`type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `fault_reports`
--
ALTER TABLE `fault_reports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `fault_status_history`
--
ALTER TABLE `fault_status_history`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `repair_assignments`
--
ALTER TABLE `repair_assignments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `repair_updates`
--
ALTER TABLE `repair_updates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `repair_verifications`
--
ALTER TABLE `repair_verifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `water_points`
--
ALTER TABLE `water_points`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `fault_reports`
--
ALTER TABLE `fault_reports`
  ADD CONSTRAINT `fk_fault_reports_reviewed_by` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fault_reports_water_point` FOREIGN KEY (`water_point_id`) REFERENCES `water_points` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `fault_status_history`
--
ALTER TABLE `fault_status_history`
  ADD CONSTRAINT `fk_fault_history_fault` FOREIGN KEY (`fault_report_id`) REFERENCES `fault_reports` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fault_history_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `repair_assignments`
--
ALTER TABLE `repair_assignments`
  ADD CONSTRAINT `fk_assignments_assigned_by` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_assignments_fault` FOREIGN KEY (`fault_report_id`) REFERENCES `fault_reports` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_assignments_technician` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `repair_updates`
--
ALTER TABLE `repair_updates`
  ADD CONSTRAINT `fk_repair_updates_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `repair_assignments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_repair_updates_user` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `repair_verifications`
--
ALTER TABLE `repair_verifications`
  ADD CONSTRAINT `fk_verifications_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `repair_assignments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_verifications_fault` FOREIGN KEY (`fault_report_id`) REFERENCES `fault_reports` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_verifications_user` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `fk_system_settings_user` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `water_points`
--
ALTER TABLE `water_points`
  ADD CONSTRAINT `fk_water_points_registered_by` FOREIGN KEY (`registered_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
