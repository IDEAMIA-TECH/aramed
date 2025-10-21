-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 21-10-2025 a las 13:57:49
-- Versión del servidor: 10.6.23-MariaDB
-- Versión de PHP: 8.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `aramed2025_aramed_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE `marcas` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `estado` enum('A','I','E') DEFAULT NULL,
  `visible` varchar(1) NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`id`, `titulo`, `estado`, `visible`, `created_by`, `created_at`, `updated_by`, `updated_at`) VALUES
(1, 'Adam Rouilly', 'A', '', 1, '2013-01-21 20:46:16', NULL, NULL),
(2, 'Gaumard', 'A', '', 1, '2013-01-21 20:47:13', NULL, NULL),
(3, 'Koken', 'E', '', 1, '2013-01-21 20:49:57', 1, '2016-02-05 18:11:52'),
(4, 'Kyoto Kagaku', 'A', '', 1, '2013-01-21 20:50:07', 32, '2017-07-18 22:50:47'),
(5, 'Nasco', 'I', '', 1, '2013-01-21 20:50:18', 1, '2018-02-01 18:48:13'),
(6, 'Simulaids', 'A', '', 1, '2013-01-21 20:50:30', NULL, NULL),
(7, 'Trucorp', 'A', '', 1, '2013-01-21 20:50:41', NULL, NULL),
(8, '3B', 'A', '', 1, '2013-04-22 14:21:32', NULL, NULL),
(9, '3D-Med', 'A', '', 1, '2013-06-25 16:52:31', NULL, NULL),
(10, 'Lifelike Biotissue', 'A', '', 1, '2013-06-25 16:52:49', NULL, NULL),
(11, 'Image Navegation', 'E', '', 1, '2013-06-25 16:53:05', 1, '2016-09-30 23:54:00'),
(12, 'Sakamoto', 'A', '', 1, '2013-06-25 16:53:17', 32, '2013-06-25 16:53:35'),
(13, 'Anatomage', 'A', '', 1, '2013-06-25 16:53:43', NULL, NULL),
(14, 'Epona', 'A', '', 1, '2013-06-25 16:54:05', NULL, NULL),
(15, 'mySmartHealthcare', 'A', '', 1, '2013-07-25 11:36:16', NULL, NULL),
(16, 'BioTissue', 'E', '', 1, '2014-04-28 10:46:35', 1, '2014-04-28 10:47:30'),
(17, 'Ruediger Anatomie', 'A', '', 1, '2014-05-12 18:23:00', NULL, NULL),
(18, 'Ing Mar  Medical', 'A', '', 1, '2014-05-12 18:23:33', NULL, NULL),
(19, 'Saratoga', 'A', '', 1, '2014-05-12 18:24:13', NULL, NULL),
(20, 'Simulab', 'A', '', 1, '2014-05-12 18:27:07', NULL, NULL),
(21, 'Surgical Science', 'A', '', 1, '2014-05-12 18:27:36', NULL, NULL),
(22, 'Limbs & Things', 'I', '', 1, '2014-05-12 18:28:53', 1, '2017-12-12 19:49:38'),
(23, 'Vata', 'A', '', 1, '2014-05-12 18:29:26', NULL, NULL),
(24, 'Chamberlain', 'A', '', 1, '2016-02-05 18:24:58', 32, '2016-02-05 18:39:08');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
