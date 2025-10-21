-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 21-10-2025 a las 13:58:44
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
-- Estructura de tabla para la tabla `usos`
--

CREATE TABLE `usos` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `posicion` int(11) DEFAULT NULL,
  `estado` enum('A','I','E') DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `usos`
--

INSERT INTO `usos` (`id`, `titulo`, `posicion`, `estado`, `created_by`, `created_at`, `updated_by`, `updated_at`) VALUES
(1, 'Simulación Médica', 1, 'A', 1, '2013-01-21 20:42:04', NULL, NULL),
(2, 'Modelos Anatómicos', 2, 'A', 1, '2013-01-21 20:42:52', NULL, NULL),
(3, 'Realidad Virtual y Material Didáctico', 3, 'A', 1, '2013-01-21 20:43:03', NULL, NULL),
(4, 'Mobiliario Médico', 4, 'A', 1, '2013-01-21 20:43:13', NULL, NULL),
(5, 'Simulación Dental', 5, 'A', 1, '2013-01-21 20:43:25', NULL, NULL),
(6, 'Laparoscopia', 6, 'A', 1, '2013-01-21 20:43:42', NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usos`
--
ALTER TABLE `usos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usos`
--
ALTER TABLE `usos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
