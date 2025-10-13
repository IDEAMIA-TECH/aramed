-- ========================================
-- ARAMED Y LABORATORIOS - Setup Database
-- Versión Simple (Sin comentarios extensos)
-- ========================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Newsletter Subscriptions
CREATE TABLE IF NOT EXISTS `newsletter_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institucion` varchar(255) NOT NULL,
  `tipo_institucion` varchar(100) NOT NULL,
  `campo_adicional` varchar(255) DEFAULT NULL,
  `estado` varchar(100) NOT NULL,
  `ciudad` varchar(150) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `puesto` varchar(150) NOT NULL,
  `email_oficial` varchar(255) NOT NULL,
  `email_alterno` varchar(255) DEFAULT NULL,
  `telefono_oficina` varchar(50) NOT NULL,
  `extension` varchar(20) DEFAULT NULL,
  `telefono_celular` varchar(50) DEFAULT NULL,
  `producto_interes` varchar(150) DEFAULT NULL,
  `fecha_compra_aprox` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `status` enum('active','inactive','unsubscribed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_oficial` (`email_oficial`),
  KEY `idx_status` (`status`),
  KEY `idx_estado` (`estado`),
  KEY `idx_tipo_institucion` (`tipo_institucion`),
  KEY `idx_producto_interes` (`producto_interes`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contact Messages
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `institucion` varchar(255) DEFAULT NULL,
  `asunto` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `status` enum('nuevo','en_proceso','respondido','cerrado') DEFAULT 'nuevo',
  `assigned_to` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_email` (`email`),
  KEY `idx_created_at` (`created_at`),
  FULLTEXT KEY `ft_mensaje` (`mensaje`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contact Quotes
CREATE TABLE IF NOT EXISTS `contact_quotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `institucion` varchar(255) NOT NULL,
  `tipo_institucion` varchar(100) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `ciudad` varchar(150) NOT NULL,
  `productos` text NOT NULL,
  `presupuesto_estimado` varchar(100) DEFAULT NULL,
  `fecha_requerida` date DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `status` enum('nueva','revisada','cotizada','enviada','cerrada') DEFAULT 'nueva',
  `cotizacion_enviada` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_email` (`email`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Setup completed!' AS status;

