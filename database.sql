-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 10, 2025 at 10:45 AM
-- Server version: 8.0.37
-- PHP Version: 8.1.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sty1_fitness_meal_planner`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calories` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `protein` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carbs` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fats_per_100g` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`id`, `name`, `unit`, `calories`, `protein`, `carbs`, `fats_per_100g`, `price`, `created_at`, `updated_at`) VALUES
(1, 'RICE', NULL, '130', '2.7', '28', '0.3', '0.66', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(2, 'YELLOW RICE', NULL, '140.4', '2.916', '30.24', '0.32', '0.957', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(3, 'RED RICE', NULL, '144.61', '3.003', '31.14', '0.33', '0.957', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(4, 'GREEN RICE', NULL, '147.5', '3.06', '31.77', '0.34', '0.957', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(5, 'PASTA', NULL, '370', '13', '75', '1.5', '2.2', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(6, 'POTATO', NULL, '175.5', '3.96', '27.69', '5.89', '0.352', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(7, 'SPAGHETTI', NULL, '370', '13', '75', '1.5', '1.243', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(8, 'SWEET POTATO', NULL, '86', '1.8', '20', '0.1', '0.825', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(9, 'GRILLED POTATO', NULL, '110', '2.8', '29', '2.7', '0.99', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(10, 'FISH', NULL, '90', '17', '0', '2', '1.045', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(11, 'SALMON', NULL, '206', '20.4', '0', '13', '7.15', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(12, 'SHRIMP', NULL, '85', '20.1', '0.2', '0.5', '3.245', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(13, 'BEEF', NULL, '230', '26', '0', '15', '4.0689', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(14, 'CHICKEN FILLET', NULL, '165', '31', '0', '3.6', '1.672', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(15, 'EGG', NULL, '148', '13', '0.8', '10', '1.3288', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(16, 'Avocado', NULL, '174.06', '2', '8.53', '14.66', '6', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(17, 'Banana', NULL, '98.69', '1.09', '22.84', '0.33', '3', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(18, 'Blackberry', NULL, '48.41', '1.39', '9.61', '0.49', '15', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(19, 'Cantaloupe', NULL, '37.71', '0.84', '8.16', '0.19', '6', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(20, 'Dragon Fruit', NULL, '50.6', '1.1', '11.1', '0.6', '23', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(21, 'Grapes', NULL, '69.9', '0.72', '18.1', '0.16', '10', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(22, 'Kiwi', NULL, '67.88', '1.14', '14.66', '0.52', '15', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(23, 'Mango', NULL, '66.62', '0.82', '14.98', '0.38', '20', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(24, 'Orange', NULL, '69.9', '1.3', '15.5', '0.3', '6', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(25, 'Papaya', NULL, '47.5', '0.47', '10.82', '0.26', '6', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(26, 'Passion Fruit', NULL, '97.6', '2.2', '23.4', '0.4', '20', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(27, 'Pineapple', NULL, '55.72', '0.54', '13.12', '0.12', '8', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(28, 'Pomegranate', NULL, '92.01', '1.67', '18.7', '1.17', '10', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(29, 'Strawberry', NULL, '36.1', '0.67', '7.68', '0.3', '15', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(30, 'Watermelon', NULL, '33.99', '0.61', '7.55', '0.15', '4', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(31, 'QUINOA SALAD', NULL, '400', '18', '45', '20', '13.244', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(32, 'FATUOUS SALAD', NULL, '350', '10', '40', '18', '11.748', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(33, 'GREEK SALAD', NULL, '380', '15', '35', '22', '12.287', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(34, 'ITALIAN SALAD', NULL, '600', '12', '50', '40', '15.631', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(35, 'TUNA SALAD', NULL, '600', '50', '45', '30', '22.528', '2025-06-03 13:09:04', '2025-06-03 13:09:04'),
(36, 'CHICKEN SALAD', NULL, '550', '45', '40', '25', '15.433', '2025-06-03 13:09:04', '2025-06-03 13:09:04');

-- --------------------------------------------------------

--
-- Table structure for table `meals`
--

CREATE TABLE `meals` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calories` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `protein` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carbs` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fats` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kcal_with_avg_rice_sauce` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `protein_with_avg_rice_sauce` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carbs_with_avg_rice_sauce` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fat_with_avg_rice_sauce` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meals`
--

INSERT INTO `meals` (`id`, `name`, `unit`, `calories`, `protein`, `carbs`, `fats`, `kcal_with_avg_rice_sauce`, `protein_with_avg_rice_sauce`, `carbs_with_avg_rice_sauce`, `fat_with_avg_rice_sauce`, `price`, `created_at`, `updated_at`) VALUES
(1, 'BEEF STRIPS WITH KOREAN BBQ SAUCE', NULL, '601.09', '44.12', '60.84', '25.91', '874.94', '48.69', '98.99', '37.35', '19.745', '2025-06-03 13:09:20', '2025-06-03 13:09:20'),
(2, 'CHICKEN NUGGETS & POTATOES', NULL, '1046', '41.6', '131.3', '41.6', '1165.6', '42.63', '135.26', '52.74', '15.158', '2025-06-03 13:09:20', '2025-06-03 13:09:20'),
(3, 'VEGAS', NULL, '727.6', '55.18', '60.47', '31.13', '1001.45', '59.75', '98.62', '42.57', '18.15', '2025-06-03 13:09:20', '2025-06-03 13:09:20'),
(4, 'SPINACH CHICKEN', NULL, '718.25', '44.45', '37.08', '46.38', '992.1', '49.02', '75.23', '57.82', '16.61', '2025-06-03 13:09:20', '2025-06-03 13:09:20'),
(5, 'CHICKEN WITH BROCCOLI & MUSHROOM', NULL, '781.5', '54.85', '50.65', '43.9', '1055.35', '59.42', '88.8', '55.34', '16.566', '2025-06-03 13:09:20', '2025-06-03 13:09:20'),
(6, 'CHICKEN FAJITA', NULL, '685.2', '51.35', '55.85', '31.1', '959.05', '55.92', '94', '42.34', '14.553', '2025-06-03 13:09:20', '2025-06-03 13:09:20'),
(7, 'BUTTER CHICKEN', NULL, '1001.9', '58.35', '94.25', '46.45', '1275.75', '62.92', '132.4', '57.99', '17.215', '2025-06-03 13:09:20', '2025-06-03 13:09:20'),
(8, 'DYNAMITE SHRIMP', NULL, '1011.93', '59.58', '97.93', '48.43', '1285.78', '64.15', '136.08', '59.97', '18.733', '2025-06-03 13:09:20', '2025-06-03 13:09:20'),
(9, 'DYNAMITE CHICKEN', NULL, '1049.23', '60.48', '97.23', '49.33', '1323.08', '65.05', '135.38', '60.87', '16.841', '2025-06-03 13:09:20', '2025-06-03 13:09:20'),
(10, 'MUSAKHAN CHICKEN', NULL, '595.3', '45.1', '41.35', '31.1', '869.15', '49.67', '79.5', '42.38', '13.123', '2025-06-03 13:09:20', '2025-06-03 13:09:20'),
(11, 'MONGOLIAN BEEF', NULL, '748.4', '42.8', '43.2', '46.4', '1022.25', '47.37', '81.35', '57.89', '18.051', '2025-06-03 13:09:20', '2025-06-03 13:09:20'),
(12, 'CHINESE NOODLES', NULL, '500', '15', '80', '10', '500', '15', '80', '10', 'CHICKEN – 17.358, BEEF – 20.988, SHRIMP – 19.756', '2025-06-03 13:09:20', '2025-06-03 13:09:20'),
(13, '24 BEEF BURGER', NULL, '600', '35', '50', '25', '718.6', '36.03', '53.96', '36.14', '13.695', '2025-06-03 15:22:33', '2025-06-03 15:22:33'),
(14, '24 CHICKEN BREAST BURGER', NULL, '550', '40', '45', '20', '668.6', '41.03', '48.96', '31.14', '13.046', '2025-06-03 15:22:33', '2025-06-03 15:22:33'),
(15, 'SPICY CHICKEN BURGER', NULL, '580', '38', '48', '22', '698.6', '39.03', '51.96', '33.14', '13.596', '2025-06-03 15:22:33', '2025-06-03 15:22:33'),
(16, 'PIRATES FISH BURGER', NULL, '500', '25', '45', '18', '618.6', '26.03', '48.96', '29.14', '10.945', '2025-06-03 15:22:33', '2025-06-03 15:22:33'),
(17, 'GRILLED CHICKEN WRAP', NULL, '450', '35', '40', '15', '568.6', '36.03', '43.96', '26.14', '12.925', '2025-06-03 15:22:33', '2025-06-03 15:22:33'),
(18, 'EGG TORTILLA WRAP', NULL, '420', '25', '35', '18', '538.6', '26.03', '38.96', '29.14', '12.716', '2025-06-03 15:22:33', '2025-06-03 15:22:33'),
(19, 'TUNA TORTILLA WRAP', NULL, '550', '50', '30', '25', '668.6', '51.03', '33.96', '36.14', '21.868', '2025-06-03 15:22:33', '2025-06-03 15:22:33'),
(20, 'BRAZILIAN BEEF WRAP', NULL, '600', '45', '35', '30', '718.6', '46.03', '38.96', '41.14', '14.85', '2025-06-03 15:22:33', '2025-06-03 15:22:33'),
(21, 'SPICY TWISTED CHICKEN WRAP', NULL, '580', '40', '38', '28', '698.6', '41.03', '41.96', '39.14', '14.971', '2025-06-03 15:22:33', '2025-06-03 15:22:33'),
(22, 'CLUB SANDWICH', NULL, '650', '45', '60', '25', '768.6', '46.03', '63.96', '36.14', '13.046', '2025-06-03 15:22:33', '2025-06-03 15:22:33'),
(23, 'CHICKEN SANDWICH', NULL, '500', '35', '45', '20', '618.6', '36.03', '48.96', '31.14', '11.935', '2025-06-03 15:22:33', '2025-06-03 15:22:33'),
(24, 'EGG SANDWICH', NULL, '480', '30', '40', '22', '598.6', '31.03', '43.96', '33.14', '11.968', '2025-06-03 15:22:33', '2025-06-03 15:22:33'),
(25, 'BAKED POTATO', NULL, '231', '5.11', '42.33', '5.63', '231', '5.11', '42.33', '5.63', '11.528', '2025-06-03 15:22:33', '2025-06-03 15:22:33');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_05_23_061909_create_permission_tables', 1),
(6, '2025_05_23_110920_create_ingredients_table', 1),
(7, '2025_05_23_110927_create_sauces_table', 1),
(8, '2025_06_02_144935_create_meals_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2025-06-03 13:05:17', '2025-06-03 13:05:17'),
(2, 'chef', 'web', '2025-06-03 13:05:17', '2025-06-03 13:05:17');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sauces`
--

CREATE TABLE `sauces` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calories` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `protein` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carbs` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fats_per_100g` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sauces`
--

INSERT INTO `sauces` (`id`, `name`, `unit`, `calories`, `protein`, `carbs`, `fats_per_100g`, `price`, `created_at`, `updated_at`) VALUES
(1, '24 SAUCE', NULL, '253.2', '2.3', '11.6', '22.3', '3.872', '2025-06-03 13:09:13', '2025-06-03 13:09:13'),
(2, 'MUSHROOM SAUCE', NULL, '313.4', '8.9', '37.6', '14.3', '3.872', '2025-06-03 13:09:13', '2025-06-03 13:09:13'),
(3, 'TOMATO SAUCE', NULL, '87.4', '3.6', '20.2', '0.5', '3.872', '2025-06-03 13:09:13', '2025-06-03 13:09:13'),
(4, 'GARLIC LEMON SAUCE', NULL, '308.1', '2.7', '4.3', '31.5', '3.872', '2025-06-03 13:09:13', '2025-06-03 13:09:13'),
(5, 'WHITE SAUCE', NULL, '269.9', '2.5', '3.3', '27.7', '3.872', '2025-06-03 13:09:13', '2025-06-03 13:09:13'),
(6, 'YELLOW SAUCE', NULL, '276', '2.5', '15.6', '23.3', '3.872', '2025-06-03 13:09:13', '2025-06-03 13:09:13'),
(7, 'BROWN SAUCE', NULL, '340', '3', '4', '35', '3.872', '2025-06-03 13:09:13', '2025-06-03 13:09:13'),
(8, 'BBQ SAUCE', NULL, '180', '1', '45', '0.5', '3.872', '2025-06-03 13:09:13', '2025-06-03 13:09:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', NULL, '$2y$10$HsLv.iVSK9NBNCJX7sXy4OHvl4q/hG7cHRSlkL./158Xv5cIXMUHa', 'l1OZvAF0ip34IisIK4vXExLPcd3KzEVgHwYQzK4BczdxM8nykCqNCsnYEg9p', '2025-06-03 13:05:17', '2025-06-03 13:05:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meals`
--
ALTER TABLE `meals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sauces`
--
ALTER TABLE `sauces`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `meals`
--
ALTER TABLE `meals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sauces`
--
ALTER TABLE `sauces`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
