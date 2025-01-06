-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2025 at 07:58 AM
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
-- Database: `gl_scratch`
--

-- --------------------------------------------------------

--
-- Table structure for table `billing_subscriptions`
--

CREATE TABLE `billing_subscriptions` (
  `id` int(10) UNSIGNED NOT NULL,
  `fk_int_user_id` bigint(20) NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `no_of_licenses` int(11) DEFAULT NULL,
  `plan_type` int(11) DEFAULT NULL,
  `services` text DEFAULT NULL,
  `billing_id` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `promo_code_id` int(11) DEFAULT NULL,
  `promo_code_value` varchar(191) DEFAULT NULL,
  `additional_discount` varchar(191) DEFAULT NULL,
  `currency` varchar(191) DEFAULT NULL,
  `start_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `billing_subscriptions`
--

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `country_code` varchar(191) DEFAULT NULL,
  `tax` int(11) DEFAULT NULL,
  `code` varchar(191) DEFAULT NULL,
  `currency` varchar(191) DEFAULT NULL,
  `currency_code` varchar(191) DEFAULT NULL,
  `flags` varchar(191) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `country_code`, `tax`, `code`, `currency`, `currency_code`, `flags`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'UNITED ARAB EMIRATES', 'AE', NULL, '971', 'United Arab Emirates Dirham', 'AED', '/backend/images/flag-icons/ae.png', NULL, '2019-04-01 05:34:05', '2019-04-01 05:34:05'),
(2, 'AFGHANISTAN', 'AF', NULL, '93', 'Afghanistan Afghani', 'AFN', '/backend/images/flag-icons/af.png', NULL, '2019-04-01 05:34:05', '2019-04-01 05:34:05'),
(3, 'NETHERLANDS ANTILLES', 'AN', NULL, '599', 'Netherlands Antillean guilder', 'ANG', '/backend/images/flag-icons/am.png', NULL, '2019-04-01 05:34:05', '2019-04-01 05:34:05'),
(4, 'ARGENTINA', 'AR', NULL, '54', 'Argentine peso', 'ARS', '/backend/images/flag-icons/ar.png', NULL, '2019-04-01 05:34:05', '2019-04-01 05:34:05'),
(5, 'AUSTRIA', 'AT', NULL, '43', 'Euro', 'EUR', '/backend/images/flag-icons/at.png', NULL, '2019-04-01 05:34:05', '2019-04-01 05:34:05'),
(6, 'AUSTRALIA', 'AU', NULL, '61', 'Australian dollar', 'AUD', '/backend/images/flag-icons/au.png', NULL, '2019-04-01 05:34:05', '2019-04-01 05:34:05'),
(7, 'BANGLADESH', 'BD', NULL, '880', 'Bangladeshi taka', 'BDT', '/backend/images/flag-icons/bd.png', NULL, '2019-04-01 05:34:05', '2019-04-01 05:34:05'),
(8, 'BELGIUM', 'BE', NULL, '32', 'Euro', 'EUR', '/backend/images/flag-icons/be.png', NULL, '2019-04-01 05:34:05', '2019-04-01 05:34:05'),
(9, 'BAHRAIN', 'BH', NULL, '973', 'Bahraini dinar', 'BHD', '/backend/images/flag-icons/bh.png', NULL, '2019-04-01 05:34:05', '2019-04-01 05:34:05'),
(10, 'BRAZIL', 'BR', NULL, '55', 'Brazilian real', 'BRL', '/backend/images/flag-icons/br.png', NULL, '2019-04-01 05:34:05', '2019-04-01 05:34:05'),
(11, 'BHUTAN', 'BT', NULL, '975', 'Bhutanese ngultrum', 'BTN', '/backend/images/flag-icons/bt.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(12, 'CANADA', 'CA', NULL, '1', 'Canadian dollar', 'CAD', '/backend/images/flag-icons/ca.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(13, 'CONGO', 'CG', NULL, '242', 'Central African CFA franc', 'XAF', '/backend/images/flag-icons/cg.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(14, 'SWITZERLAND', 'CH', NULL, '41', 'wiss franc', 'CHF', '/backend/images/flag-icons/ch.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(15, 'CHILE', 'CL', NULL, '56', 'Chilean peso', 'CLP', '/backend/images/flag-icons/cl.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(16, 'CHINA', 'CN', NULL, '86', 'Renminbi|Chinese yuan', 'CNY', '/backend/images/flag-icons/cn.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(17, 'COLOMBIA', 'CO', NULL, '57', 'Colombian peso', 'COP', '/backend/images/flag-icons/co.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(18, 'COSTA RICA', 'CR', NULL, '506', 'Costa Rican colon', 'CRC', '/backend/images/flag-icons/cr.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(19, 'CUBA', 'CU', NULL, '53', 'Cuban peso', 'CUP', '/backend/images/flag-icons/cu.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(20, 'CZECH REPUBLIC', 'CZ', NULL, '420', 'Czech koruna', 'CZK', '/backend/images/flag-icons/cz.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(21, 'GERMANY', 'DE', NULL, '49', 'Euro', 'EUR', '/backend/images/flag-icons/de.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(22, 'DENMARK', 'DK', NULL, '45', 'Danish krone', 'DKK', '/backend/images/flag-icons/dk.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(23, 'ECUADOR', 'EC', NULL, '593', 'Ecuadorian sucre', 'ECS', '/backend/images/flag-icons/ec.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(24, 'SPAIN', 'ES', NULL, '34', 'Euro', 'EUR', '/backend/images/flag-icons/es.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(25, 'FINLAND', 'FI', NULL, '358', 'Euro', 'EUR', '/backend/images/flag-icons/fi.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(26, 'FRANCE', 'FR', NULL, '33', 'Euro', 'EUR', '/backend/images/flag-icons/fr.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(27, 'UNITED KINGDOM', 'GB', NULL, '44', 'Pound sterling', 'GBP', '/backend/images/flag-icons/gb.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(28, 'HONG KONG', 'HK', NULL, '852', 'Hong Kong dollar', 'HKD', '/backend/images/flag-icons/hn.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(29, 'INDIA', 'IN', NULL, '91', 'Indian rupee', 'INR', '/backend/images/flag-icons/in.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(30, 'IRAQ', 'IQ', NULL, '964', 'Iraqi dinar', 'IQD', '/backend/images/flag-icons/iq.png', NULL, '2019-04-01 05:34:06', '2019-04-01 05:34:06'),
(31, 'JAPAN', 'JP', NULL, '81', 'Japan Yen', 'JPY', '/backend/images/flag-icons/jp.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(32, 'KUWAIT', 'KW', NULL, '965', 'Kuwaiti dinar', 'KWD', '/backend/images/flag-icons/kw.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(33, 'SRI LANKA', 'LK', NULL, '94', 'Sri Lanka Rupee', 'LKR', '/backend/images/flag-icons/lk.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(34, 'MALAYSIA', 'MY', NULL, '60', 'Malaysia Ringgit', 'MYR', '/backend/images/flag-icons/my.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(35, 'NETHERLANDS', 'NL', NULL, '31', 'Euro', 'EUR', '/backend/images/flag-icons/nl.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(36, 'NEPAL', 'NP', NULL, '977', 'Nepal Rupee', 'NPR', '/backend/images/flag-icons/np.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(37, 'NEW ZEALAND', 'NZ', NULL, '64', 'New Zealand dollar', 'NZD', '/backend/images/flag-icons/nz.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(38, 'OMAN', 'OM', NULL, '968', 'Omani rial', 'OMR', '/backend/images/flag-icons/om.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(39, 'PHILIPPINES', 'PH', NULL, '63', 'Philippines Peso', 'PHP', '/backend/images/flag-icons/ph.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(40, 'PAKISTAN', 'PK', NULL, '92', 'Pakistani rupee', 'PKR', '/backend/images/flag-icons/pk.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(41, 'POLAND', 'PL', NULL, '48', 'Polish złoty', 'PLN', '/backend/images/flag-icons/pl.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(42, 'PORTUGAL', 'PT', NULL, '351', 'Euro', 'EUR', '/backend/images/flag-icons/pt.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(43, 'QATAR', 'QA', NULL, '974', 'Qatar Riyal', 'QAR', '/backend/images/flag-icons/qa.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(44, 'RUSSIAN FEDERATION', 'RU', NULL, '7', 'Russia Ruble', 'RUB', '/backend/images/flag-icons/ru.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(45, 'SAUDI ARABIA', 'SA', NULL, '966', 'Saudi Arabia Riyal', 'SAR', '/backend/images/flag-icons/sa.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(46, 'SINGAPORE', 'SG', NULL, '65', 'Singapore Dollar', 'SGD', '/backend/images/flag-icons/sg.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(47, 'TURKEY', 'TR', NULL, '90', 'Turkey Lira', 'TRL', '/backend/images/flag-icons/tr.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(48, 'UNITED STATES', 'US', NULL, '1', 'United States dollar', 'USD', '/backend/images/flag-icons/us.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(49, 'YEMEN', 'YE', NULL, '967', 'Yemeni rial', 'YER', '/backend/images/flag-icons/ye.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(50, 'SOUTH AFRICA', 'ZA', NULL, '27', 'South African rand', 'ZAR', '/backend/images/flag-icons/za.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07'),
(51, 'Ghana', 'GH', NULL, '233', 'Ghanaian Cedi', 'GHS', '/backend/images/flag-icons/za.png', NULL, '2019-04-01 05:34:07', '2019-04-01 05:34:07');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_scratches`
--

CREATE TABLE `purchase_scratches` (
  `id` int(11) NOT NULL,
  `fk_int_user_id` int(11) NOT NULL,
  `narration` varchar(500) NOT NULL,
  `scratch_count` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_scratches`
--

--
-- Table structure for table `scratch_ads`
--

CREATE TABLE `scratch_ads` (
  `id` int(10) UNSIGNED NOT NULL,
  `image` varchar(191) DEFAULT NULL,
  `video` varchar(191) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `scratch_ads`
--
-- --------------------------------------------------------

--
-- Table structure for table `scratch_api_users`
--

CREATE TABLE `scratch_api_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) DEFAULT NULL,
  `api_key` varchar(191) DEFAULT NULL,
  `mobile` varchar(191) DEFAULT NULL,
  `amount` varchar(191) DEFAULT NULL,
  `status` varchar(191) DEFAULT NULL,
  `redeem` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `unique_id` varchar(191) DEFAULT NULL,
  `bill_no` varchar(191) DEFAULT NULL,
  `offer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `scratch_api_users`
--

-- --------------------------------------------------------

--
-- Table structure for table `scratch_bill_numbers`
--

CREATE TABLE `scratch_bill_numbers` (
  `id` int(10) UNSIGNED NOT NULL,
  `vendor_id` int(10) UNSIGNED NOT NULL,
  `bill_number` varchar(191) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `offer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `scratch_bill_numbers`
--

-- --------------------------------------------------------

--
-- Table structure for table `scratch_branches`
--

CREATE TABLE `scratch_branches` (
  `id` int(10) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(4) NOT NULL,
  `branch` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `scratch_branches`
--

-- --------------------------------------------------------

--
-- Table structure for table `scratch_count`
--

CREATE TABLE `scratch_count` (
  `id` int(11) NOT NULL,
  `fk_int_user_id` int(11) NOT NULL,
  `total_count` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT NULL,
  `balance_count` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `scratch_count`
--

-- --------------------------------------------------------

--
-- Table structure for table `scratch_footer`
--

CREATE TABLE `scratch_footer` (
  `id` int(10) UNSIGNED NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `content` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scratch_type`
--

CREATE TABLE `scratch_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` varchar(191) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `scratch_type`
--

INSERT INTO `scratch_type` (`id`, `type`, `vendor_id`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Lucky Draw', NULL, 1, NULL, '2022-08-09 07:28:03', '2022-08-09 07:28:03'),
(2, 'Scratch & Win', NULL, 1, NULL, '2022-08-09 07:28:08', '2022-08-09 07:28:08');

-- --------------------------------------------------------

--
-- Table structure for table `scratch_web_customers`
--

CREATE TABLE `scratch_web_customers` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `unique_id` varchar(191) DEFAULT NULL,
  `name` varchar(191) DEFAULT NULL,
  `mobile` bigint(20) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `redeemed_agent` int(11) DEFAULT NULL,
  `country_code` int(11) DEFAULT NULL,
  `offer_id` bigint(20) DEFAULT NULL,
  `offer_list_id` int(11) DEFAULT NULL,
  `offer_text` text DEFAULT NULL,
  `bill_no` varchar(191) DEFAULT NULL,
  `short_link` varchar(191) DEFAULT NULL,
  `api_key` varchar(500) DEFAULT NULL,
  `amount` varchar(191) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `redeem` tinyint(1) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `redeemed_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `scratch_web_customers`
--


--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('OmLHcEOhhfwQsdzPyyQc9NXwqLszlS5cE3YZq9jg', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjY6Il90b2tlbiI7czo0MDoiQkxLVjliWGU1OVpjVnUyc1V4bkRSNmpmZjI5YjB6enRVRlBjN3M1OSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1735995337);

-- --------------------------------------------------------

--
-- Table structure for table `short_links`
--

CREATE TABLE `short_links` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `offer_id` int(11) DEFAULT NULL,
  `code` varchar(191) DEFAULT NULL,
  `link` varchar(191) DEFAULT NULL,
  `url` text DEFAULT NULL,
  `bill_number_only_apply_from_list` tinyint(1) NOT NULL DEFAULT 0,
  `email_required` tinyint(1) DEFAULT NULL,
  `branch_required` tinyint(1) DEFAULT 0,
  `click_count` int(11) DEFAULT 0,
  `custom_field` tinyint(4) DEFAULT NULL,
  `type` tinyint(4) DEFAULT 1,
  `status` tinyint(1) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `short_links`
--

-- --------------------------------------------------------

--
-- Table structure for table `short_link_histories`
--

CREATE TABLE `short_link_histories` (
  `id` int(10) UNSIGNED NOT NULL,
  `short_link_id` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `mac_address` varchar(17) DEFAULT NULL,
  `device` varchar(191) DEFAULT NULL,
  `os` varchar(191) DEFAULT NULL,
  `browser` varchar(191) DEFAULT NULL,
  `device_type` varchar(191) DEFAULT NULL,
  `country` varchar(191) DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `region` varchar(191) DEFAULT NULL,
  `area_code` varchar(191) DEFAULT NULL,
  `country_code` varchar(191) DEFAULT NULL,
  `continent` varchar(191) DEFAULT NULL,
  `latitude` varchar(191) DEFAULT NULL,
  `logitude` varchar(191) DEFAULT NULL,
  `currency` varchar(191) DEFAULT NULL,
  `timezone` varchar(191) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `short_link_histories`
--

--
-- Table structure for table `tbl_gl_api_tokens`
--

CREATE TABLE `tbl_gl_api_tokens` (
  `pk_int_token_id` int(10) UNSIGNED NOT NULL,
  `fk_int_user_id` int(11) NOT NULL,
  `vchr_token` varchar(191) NOT NULL,
  `int_status` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_gl_api_tokens`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_scratch_customers`
--

CREATE TABLE `tbl_scratch_customers` (
  `pk_int_scratch_customers_id` int(10) UNSIGNED NOT NULL,
  `fk_int_user_id` int(11) NOT NULL,
  `vchr_name` varchar(191) DEFAULT NULL,
  `vchr_mobno` varchar(191) DEFAULT NULL,
  `vchr_dob` varchar(191) DEFAULT NULL,
  `vchr_billno` varchar(191) DEFAULT NULL,
  `int_status` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `offer_text` varchar(191) DEFAULT NULL,
  `email` text DEFAULT NULL,
  `extrafield_values` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `fk_int_offer_id` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  `unique_id` varchar(191) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_scratch_customers`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_scratch_offers`
--

CREATE TABLE `tbl_scratch_offers` (
  `pk_int_scratch_offers_id` int(10) UNSIGNED NOT NULL,
  `vchr_scratch_offers_name` varchar(191) NOT NULL,
  `fk_int_user_id` int(11) NOT NULL,
  `vchr_scratch_offers_image` varchar(191) DEFAULT NULL,
  `mobile_image` varchar(191) DEFAULT NULL,
  `int_status` int(11) NOT NULL,
  `type_id` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_scratch_offers`
--

I
-- --------------------------------------------------------

--
-- Table structure for table `tbl_scratch_offers_listing`
--

CREATE TABLE `tbl_scratch_offers_listing` (
  `pk_int_scratch_offers_listing_id` int(10) UNSIGNED NOT NULL,
  `fk_int_scratch_offers_id` int(11) NOT NULL,
  `int_scratch_offers_count` int(11) NOT NULL,
  `txt_description` text NOT NULL,
  `image` varchar(191) DEFAULT NULL,
  `int_scratch_offers_balance` int(11) DEFAULT NULL,
  `fk_int_user_id` int(11) NOT NULL,
  `int_winning_status` int(11) NOT NULL,
  `int_status` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_scratch_offers_listing`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_settings`
--

CREATE TABLE `tbl_settings` (
  `pk_int_settings_id` int(10) UNSIGNED NOT NULL,
  `vchr_settings_type` longtext NOT NULL,
  `vchr_settings_value` longtext NOT NULL,
  `fk_int_user_id` int(11) DEFAULT NULL,
  `int_status` int(11) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_settings`
--
 --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `pk_int_user_id` int(10) UNSIGNED NOT NULL,
  `customer_id` varchar(191) DEFAULT NULL,
  `vchr_user_name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `countrycode` int(11) NOT NULL DEFAULT 91,
  `mobile` varchar(191) DEFAULT NULL,
  `vchr_user_mobile` varchar(191) DEFAULT NULL,
  `vchr_user_imei` varchar(191) DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `otp` varchar(191) DEFAULT NULL,
  `password_validity` datetime DEFAULT NULL,
  `vchr_logo` varchar(191) DEFAULT NULL,
  `datetime_last_login` datetime DEFAULT NULL,
  `int_role_id` int(11) NOT NULL,
  `permission_role_id` bigint(20) DEFAULT NULL,
  `is_co_admin` tinyint(4) NOT NULL DEFAULT 0,
  `reward` int(11) DEFAULT NULL,
  `rank` int(11) DEFAULT NULL,
  `telegram_id` int(11) DEFAULT NULL,
  `parent_user_id` int(11) DEFAULT NULL,
  `sticky_agent` tinyint(4) NOT NULL DEFAULT 0,
  `designation_id` varchar(100) DEFAULT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `address` varchar(191) DEFAULT NULL,
  `location` varchar(500) NOT NULL DEFAULT '',
  `latitude` varchar(191) DEFAULT NULL,
  `longitude` varchar(191) DEFAULT NULL,
  `battery` varchar(191) DEFAULT NULL,
  `device_model` varchar(191) DEFAULT NULL,
  `os_version` varchar(191) DEFAULT NULL,
  `speed` varchar(191) DEFAULT NULL,
  `angle` varchar(191) DEFAULT NULL,
  `visa_id` varchar(191) NOT NULL DEFAULT '',
  `gst_number` varchar(191) DEFAULT NULL,
  `verify_email` int(11) NOT NULL DEFAULT 0,
  `int_module` int(11) DEFAULT NULL,
  `int_registration_from` int(11) NOT NULL DEFAULT 1,
  `int_is_emergency_account` int(11) NOT NULL DEFAULT 0,
  `int_status` int(11) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fcm_token` text DEFAULT NULL,
  `last_app_used` datetime DEFAULT NULL,
  `last_app_version` varchar(191) DEFAULT NULL,
  `calling_method` tinyint(4) NOT NULL DEFAULT 2 COMMENT '1-Phone,2-IVR',
  `enquiry_display_fields` text DEFAULT NULL,
  `call_forwarded` tinyint(4) NOT NULL DEFAULT 0,
  `extension` varchar(191) DEFAULT NULL,
  `email_password` varchar(191) DEFAULT NULL,
  `web_sound` tinyint(1) DEFAULT NULL,
  `time_zone` varchar(20) DEFAULT NULL,
  `web_notification` tinyint(1) DEFAULT NULL,
  `employee_code` varchar(191) DEFAULT NULL,
  `subscription_start_date` date DEFAULT NULL,
  `subscription_end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`pk_int_user_id`, `customer_id`, `vchr_user_name`, `email`, `countrycode`, `mobile`, `vchr_user_mobile`, `vchr_user_imei`, `password`, `otp`, `password_validity`, `vchr_logo`, `datetime_last_login`, `int_role_id`, `permission_role_id`, `is_co_admin`, `reward`, `rank`, `telegram_id`, `parent_user_id`, `sticky_agent`, `designation_id`, `company_name`, `branch_id`, `address`, `location`, `latitude`, `longitude`, `battery`, `device_model`, `os_version`, `speed`, `angle`, `visa_id`, `gst_number`, `verify_email`, `int_module`, `int_registration_from`, `int_is_emergency_account`, `int_status`, `remember_token`, `deleted_at`, `created_at`, `updated_at`, `fcm_token`, `last_app_used`, `last_app_version`, `calling_method`, `enquiry_display_fields`, `call_forwarded`, `extension`, `email_password`, `web_sound`, `time_zone`, `web_notification`, `employee_code`, `subscription_start_date`, `subscription_end_date`) VALUES
(1, NULL, 'admin', 'admin@getlead.co.uk', 91, '1234567899', '911234567899', '123456789', '$2y$10$YTNKfi/TJeLKYHHT.V/S3uGr1JNlzikBG3Xm31YjZFNjUfrcid30a', NULL, NULL, NULL, '2025-01-04 12:44:13', 1, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, NULL, 1, 0, 1, '2BhZUdcb3lDSuUjcIUjwSw4IlwAF9bsf5YaYHAFgUF6A2QKP8a0dWKmuDZMz', NULL, '2020-02-13 22:52:58', '2025-01-04 07:14:13', NULL, NULL, NULL, 2, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `billing_subscriptions`
--
ALTER TABLE `billing_subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `purchase_scratches`
--
ALTER TABLE `purchase_scratches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scratch_ads`
--
ALTER TABLE `scratch_ads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scratch_api_users`
--
ALTER TABLE `scratch_api_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scratch_bill_numbers`
--
ALTER TABLE `scratch_bill_numbers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scratch_branches`
--
ALTER TABLE `scratch_branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scratch_count`
--
ALTER TABLE `scratch_count`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `fk_int_user_id` (`fk_int_user_id`);

--
-- Indexes for table `scratch_footer`
--
ALTER TABLE `scratch_footer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scratch_type`
--
ALTER TABLE `scratch_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scratch_web_customers`
--
ALTER TABLE `scratch_web_customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `short_links`
--
ALTER TABLE `short_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `short_link_histories`
--
ALTER TABLE `short_link_histories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_gl_api_tokens`
--
ALTER TABLE `tbl_gl_api_tokens`
  ADD PRIMARY KEY (`pk_int_token_id`);

--
-- Indexes for table `tbl_scratch_customers`
--
ALTER TABLE `tbl_scratch_customers`
  ADD PRIMARY KEY (`pk_int_scratch_customers_id`);

--
-- Indexes for table `tbl_scratch_offers`
--
ALTER TABLE `tbl_scratch_offers`
  ADD PRIMARY KEY (`pk_int_scratch_offers_id`);

--
-- Indexes for table `tbl_scratch_offers_listing`
--
ALTER TABLE `tbl_scratch_offers_listing`
  ADD PRIMARY KEY (`pk_int_scratch_offers_listing_id`);

--
-- Indexes for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  ADD PRIMARY KEY (`pk_int_settings_id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`pk_int_user_id`),
  ADD UNIQUE KEY `tbl_users_vchr_user_imei_unique` (`vchr_user_imei`),
  ADD UNIQUE KEY `mobile` (`mobile`),
  ADD KEY `vchr_user_name` (`vchr_user_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `billing_subscriptions`
--
ALTER TABLE `billing_subscriptions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_scratches`
--
ALTER TABLE `purchase_scratches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `scratch_ads`
--
ALTER TABLE `scratch_ads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `scratch_api_users`
--
ALTER TABLE `scratch_api_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `scratch_bill_numbers`
--
ALTER TABLE `scratch_bill_numbers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `scratch_branches`
--
ALTER TABLE `scratch_branches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `scratch_count`
--
ALTER TABLE `scratch_count`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `scratch_footer`
--
ALTER TABLE `scratch_footer`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scratch_type`
--
ALTER TABLE `scratch_type`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `scratch_web_customers`
--
ALTER TABLE `scratch_web_customers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `short_links`
--
ALTER TABLE `short_links`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `short_link_histories`
--
ALTER TABLE `short_link_histories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `tbl_scratch_customers`
--
ALTER TABLE `tbl_scratch_customers`
  MODIFY `pk_int_scratch_customers_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `tbl_scratch_offers`
--
ALTER TABLE `tbl_scratch_offers`
  MODIFY `pk_int_scratch_offers_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `tbl_scratch_offers_listing`
--
ALTER TABLE `tbl_scratch_offers_listing`
  MODIFY `pk_int_scratch_offers_listing_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  MODIFY `pk_int_settings_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `pk_int_user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
