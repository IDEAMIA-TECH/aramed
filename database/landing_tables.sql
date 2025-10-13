-- ========================================
-- ARAMED Y LABORATORIOS
-- Landing Page Database Tables
-- ========================================
-- 
-- Tablas necesarias para el funcionamiento del landing page
-- 
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios
-- @created    2025-10-13

-- ========================================
-- Tabla: newsletter_subscriptions
-- Almacena suscripciones al newsletter
-- ========================================
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
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Tabla: contact_messages
-- Almacena mensajes de contacto
-- ========================================
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
  `assigned_to` int(11) DEFAULT NULL COMMENT 'ID del usuario asignado (para futuro)',
  `notes` text DEFAULT NULL COMMENT 'Notas internas (para futuro)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_email` (`email`),
  KEY `idx_asunto` (`asunto`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Tabla: contact_quotes (opcional - para futuro)
-- Almacena solicitudes específicas de cotización
-- ========================================
CREATE TABLE IF NOT EXISTS `contact_quotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `institucion` varchar(255) NOT NULL,
  `tipo_institucion` varchar(100) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `ciudad` varchar(150) NOT NULL,
  `productos` text NOT NULL COMMENT 'JSON array de productos',
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

-- ========================================
-- Índices adicionales para optimización
-- ========================================

-- Para reportes y análisis
ALTER TABLE `newsletter_subscriptions` 
  ADD INDEX `idx_producto_interes` (`producto_interes`),
  ADD INDEX `idx_fecha_compra` (`fecha_compra_aprox`);

-- Para búsquedas de contacto
ALTER TABLE `contact_messages` 
  ADD FULLTEXT INDEX `ft_mensaje` (`mensaje`);

-- ========================================
-- Datos de ejemplo (opcional - solo para testing)
-- ========================================

-- Ejemplo de suscripción al newsletter
-- INSERT INTO `newsletter_subscriptions` 
-- (`institucion`, `tipo_institucion`, `estado`, `ciudad`, `nombre`, `puesto`, `email_oficial`, `telefono_oficina`, `status`) 
-- VALUES 
-- ('Hospital General de México', 'Hospital', 'Ciudad de México', 'CDMX', 'Dr. Juan Pérez', 'Director Médico', 'jperez@hgm.gob.mx', '5555-1234', 'active');

-- Ejemplo de mensaje de contacto
-- INSERT INTO `contact_messages` 
-- (`nombre`, `email`, `telefono`, `institucion`, `asunto`, `mensaje`, `status`) 
-- VALUES 
-- ('María García', 'mgarcia@universidad.edu', '5555-5678', 'Universidad Nacional', 'Información de Productos', 'Me gustaría recibir información sobre simuladores para nuestro laboratorio de enfermería.', 'nuevo');

-- ========================================
-- Comentarios finales
-- ========================================
-- 
-- Estas tablas están optimizadas para:
-- - Alto volumen de inserts (landing page con tráfico)
-- - Búsquedas rápidas por estado, fecha, email
-- - Soporte futuro para sistema CRM
-- - Exportación de datos para marketing
--
-- Mantenimiento recomendado:
-- - Backup diario de datos
-- - Limpieza de registros antiguos (> 2 años)
-- - Análisis periódico de índices
-- 
-- ========================================


