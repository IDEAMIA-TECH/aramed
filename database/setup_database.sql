-- ========================================
-- ARAMED Y LABORATORIOS
-- Setup Completo de Base de Datos
-- ========================================
-- 
-- Script consolidado para crear todas las tablas necesarias
-- para el funcionamiento del landing page
-- 
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios
-- @created    2025-10-13
-- @updated    2025-10-13
-- @version    1.0
--
-- INSTRUCCIONES DE USO:
-- 1. Acceder a phpMyAdmin o línea de comandos MySQL
-- 2. Seleccionar la base de datos: aramed2025_produccion
-- 3. Ejecutar este script completo
-- 4. Verificar que las 3 tablas se crearon correctamente
--
-- IMPORTANTE:
-- - Este script es idempotente (se puede ejecutar múltiples veces)
-- - No elimina datos existentes
-- - Usa CREATE TABLE IF NOT EXISTS
-- ========================================

-- Configuración del charset
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ========================================
-- 1. TABLA: newsletter_subscriptions
-- Almacena suscripciones al newsletter
-- ========================================

DROP TABLE IF EXISTS `newsletter_subscriptions_old`;

CREATE TABLE IF NOT EXISTS `newsletter_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  
  -- Información de la Institución
  `institucion` varchar(255) NOT NULL COMMENT 'Nombre de la institución',
  `tipo_institucion` varchar(100) NOT NULL COMMENT 'Tipo: Hospital, Universidad, etc.',
  `campo_adicional` varchar(255) DEFAULT NULL COMMENT 'Campo extra para Escuela de salud o Institución gubernamental',
  `estado` varchar(100) NOT NULL COMMENT 'Estado de la República',
  `ciudad` varchar(150) NOT NULL COMMENT 'Ciudad',
  
  -- Información del Contacto
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre completo del contacto',
  `puesto` varchar(150) NOT NULL COMMENT 'Puesto o cargo',
  `email_oficial` varchar(255) NOT NULL COMMENT 'Email oficial de la institución',
  `email_alterno` varchar(255) DEFAULT NULL COMMENT 'Email alternativo (opcional)',
  `telefono_oficina` varchar(50) NOT NULL COMMENT 'Teléfono de oficina',
  `extension` varchar(20) DEFAULT NULL COMMENT 'Extensión telefónica',
  `telefono_celular` varchar(50) DEFAULT NULL COMMENT 'Teléfono celular (opcional)',
  
  -- Información de Interés
  `producto_interes` varchar(150) DEFAULT NULL COMMENT 'Producto de interés',
  `fecha_compra_aprox` date DEFAULT NULL COMMENT 'Fecha aproximada de compra',
  `observaciones` text DEFAULT NULL COMMENT 'Observaciones o comentarios adicionales',
  
  -- Metadata y Control
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP del usuario',
  `user_agent` varchar(500) DEFAULT NULL COMMENT 'User agent del navegador',
  `status` enum('active','inactive','unsubscribed') DEFAULT 'active' COMMENT 'Estado de la suscripción',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de registro',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'Fecha de última actualización',
  
  -- Índices
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_oficial` (`email_oficial`),
  KEY `idx_status` (`status`),
  KEY `idx_estado` (`estado`),
  KEY `idx_tipo_institucion` (`tipo_institucion`),
  KEY `idx_producto_interes` (`producto_interes`),
  KEY `idx_fecha_compra` (`fecha_compra_aprox`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_estado_ciudad` (`estado`, `ciudad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Suscripciones al newsletter';

-- ========================================
-- 2. TABLA: contact_messages
-- Almacena mensajes de contacto general
-- ========================================

DROP TABLE IF EXISTS `contact_messages_old`;

CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  
  -- Información del Contacto
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre completo',
  `email` varchar(255) NOT NULL COMMENT 'Email de contacto',
  `telefono` varchar(50) NOT NULL COMMENT 'Teléfono',
  `institucion` varchar(255) DEFAULT NULL COMMENT 'Institución (opcional)',
  
  -- Mensaje
  `asunto` varchar(150) NOT NULL COMMENT 'Asunto del mensaje',
  `mensaje` text NOT NULL COMMENT 'Contenido del mensaje',
  
  -- Metadata y Control
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP del usuario',
  `user_agent` varchar(500) DEFAULT NULL COMMENT 'User agent del navegador',
  `status` enum('nuevo','en_proceso','respondido','cerrado') DEFAULT 'nuevo' COMMENT 'Estado del mensaje',
  `assigned_to` int(11) DEFAULT NULL COMMENT 'ID del usuario asignado (para CRM futuro)',
  `notes` text DEFAULT NULL COMMENT 'Notas internas (para CRM futuro)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de recepción',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'Fecha de última actualización',
  
  -- Índices
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_email` (`email`),
  KEY `idx_asunto` (`asunto`),
  KEY `idx_created_at` (`created_at`),
  FULLTEXT KEY `ft_mensaje` (`mensaje`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Mensajes de contacto general';

-- ========================================
-- 3. TABLA: contact_quotes
-- Almacena solicitudes específicas de cotización
-- ========================================

DROP TABLE IF EXISTS `contact_quotes_old`;

CREATE TABLE IF NOT EXISTS `contact_quotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  
  -- Información del Contacto
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre completo',
  `email` varchar(255) NOT NULL COMMENT 'Email de contacto',
  `telefono` varchar(50) NOT NULL COMMENT 'Teléfono',
  `institucion` varchar(255) NOT NULL COMMENT 'Nombre de la institución',
  `tipo_institucion` varchar(100) NOT NULL COMMENT 'Tipo de institución',
  `estado` varchar(100) NOT NULL COMMENT 'Estado de la República',
  `ciudad` varchar(150) NOT NULL COMMENT 'Ciudad',
  
  -- Información de la Cotización
  `productos` text NOT NULL COMMENT 'JSON array de productos solicitados',
  `presupuesto_estimado` varchar(100) DEFAULT NULL COMMENT 'Presupuesto aproximado',
  `fecha_requerida` date DEFAULT NULL COMMENT 'Fecha en que se requieren los productos',
  `mensaje` text DEFAULT NULL COMMENT 'Comentarios o requisitos adicionales',
  
  -- Metadata y Control
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP del usuario',
  `user_agent` varchar(500) DEFAULT NULL COMMENT 'User agent del navegador',
  `status` enum('nueva','revisada','cotizada','enviada','cerrada') DEFAULT 'nueva' COMMENT 'Estado de la cotización',
  `cotizacion_enviada` tinyint(1) DEFAULT 0 COMMENT 'Si ya se envió la cotización',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de solicitud',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'Fecha de última actualización',
  
  -- Índices
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_email` (`email`),
  KEY `idx_tipo_institucion` (`tipo_institucion`),
  KEY `idx_estado` (`estado`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_cotizacion_enviada` (`cotizacion_enviada`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Solicitudes de cotización';

-- ========================================
-- DATOS DE EJEMPLO (Solo para Testing)
-- ========================================
-- 
-- IMPORTANTE: Descomentar solo si deseas insertar datos de prueba
-- NO USAR EN PRODUCCIÓN
-- 

/*
-- Ejemplo de suscripción al newsletter
INSERT INTO `newsletter_subscriptions` 
(`institucion`, `tipo_institucion`, `estado`, `ciudad`, `nombre`, `puesto`, `email_oficial`, `telefono_oficina`, `status`) 
VALUES 
('Hospital General de México', 'Hospital', 'Ciudad de México', 'CDMX', 'Dr. Juan Pérez', 'Director Médico', 'jperez@test.com', '5555-1234', 'active'),
('Universidad Nacional Autónoma de México', 'Universidad', 'Ciudad de México', 'CDMX', 'Dra. María García', 'Coordinadora de Laboratorio', 'mgarcia@test.com', '5555-5678', 'active'),
('Instituto Politécnico Nacional', 'Universidad', 'Ciudad de México', 'CDMX', 'Lic. Carlos Rodríguez', 'Jefe de Departamento', 'crodriguez@test.com', '5555-9012', 'active');

-- Ejemplo de mensaje de contacto
INSERT INTO `contact_messages` 
(`nombre`, `email`, `telefono`, `institucion`, `asunto`, `mensaje`, `status`) 
VALUES 
('Ana López', 'alopez@test.com', '5555-3456', 'Hospital ABC', 'Información de Productos', 'Me gustaría recibir información sobre simuladores para nuestro laboratorio de enfermería.', 'nuevo'),
('Roberto Sánchez', 'rsanchez@test.com', '5555-7890', 'Universidad del Valle', 'Cotización', 'Necesito una cotización para equipar nuestro nuevo laboratorio de simulación médica.', 'nuevo');

-- Ejemplo de cotización
INSERT INTO `contact_quotes` 
(`nombre`, `email`, `telefono`, `institucion`, `tipo_institucion`, `estado`, `ciudad`, `productos`, `presupuesto_estimado`, `mensaje`, `status`) 
VALUES 
('Ing. Pedro Martínez', 'pmartinez@test.com', '5555-2468', 'Tecnológico de Monterrey', 'Universidad', 'Nuevo León', 'Monterrey', '["Simulador HAL S3201", "Anatomage Table"]', '$500,000 - $1,000,000 MXN', 'Requerimos equipos para el nuevo campus de ciencias de la salud', 'nueva');
*/

-- ========================================
-- VERIFICACIÓN DE INSTALACIÓN
-- ========================================

-- Mostrar las tablas creadas
SHOW TABLES LIKE '%newsletter%';
SHOW TABLES LIKE '%contact%';

-- Mostrar la estructura de cada tabla
DESCRIBE `newsletter_subscriptions`;
DESCRIBE `contact_messages`;
DESCRIBE `contact_quotes`;

-- ========================================
-- INFORMACIÓN ADICIONAL
-- ========================================
-- 
-- TABLAS CREADAS:
-- ✓ newsletter_subscriptions - Suscripciones al newsletter
-- ✓ contact_messages - Mensajes de contacto general
-- ✓ contact_quotes - Solicitudes de cotización
--
-- CARACTERÍSTICAS:
-- ✓ Charset UTF-8 (utf8mb4) para soporte completo de caracteres
-- ✓ Índices optimizados para búsquedas rápidas
-- ✓ Campos de metadata (IP, user agent)
-- ✓ Status fields para seguimiento
-- ✓ Timestamps automáticos
-- ✓ Fulltext search en mensajes
-- ✓ Preparado para integración con CRM futuro
--
-- MANTENIMIENTO RECOMENDADO:
-- - Backup diario de datos
-- - Limpieza de registros antiguos (> 2 años) según política
-- - Análisis periódico de índices con ANALYZE TABLE
-- - Monitoreo de crecimiento de tablas
-- 
-- SEGURIDAD:
-- - Todos los inputs deben sanitizarse en PHP antes de insertar
-- - Usar prepared statements (PDO) en todas las queries
-- - Validar tipos de datos en el backend
-- - Implementar rate limiting para prevenir spam
-- 
-- PRÓXIMOS PASOS:
-- 1. Verificar que las tablas se crearon correctamente
-- 2. Probar inserción de datos desde los formularios
-- 3. Verificar recepción de emails
-- 4. Monitorear logs de errores
-- 
-- ========================================
-- FIN DEL SCRIPT
-- ========================================

SELECT 'Database setup completed successfully!' AS status;

