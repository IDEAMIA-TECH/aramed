-- ========================================
-- ARAMED Y LABORATORIOS - Newsletter Campaigns
-- ========================================
-- 
-- Tablas para almacenar campañas de newsletter y sus envíos
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios

-- Tabla de campañas
CREATE TABLE IF NOT EXISTS `newsletter_campanas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(255) NOT NULL COMMENT 'Nombre de la campaña',
  `plantilla_id` INT(11) NOT NULL COMMENT 'ID de la plantilla usada',
  `asunto` VARCHAR(255) NOT NULL COMMENT 'Asunto del email',
  `filtro_estado` VARCHAR(50) DEFAULT 'activo' COMMENT 'Estado de destinatarios filtrados',
  `total_destinatarios` INT(11) DEFAULT 0 COMMENT 'Total de destinatarios',
  `enviados` INT(11) DEFAULT 0 COMMENT 'Emails enviados exitosamente',
  `fallidos` INT(11) DEFAULT 0 COMMENT 'Emails que fallaron',
  `estado` ENUM('programada', 'en_proceso', 'completada', 'cancelada') DEFAULT 'programada',
  `creado_por` INT(11) DEFAULT NULL COMMENT 'ID del usuario que creó la campaña',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `programada_para` DATETIME DEFAULT NULL COMMENT 'Fecha programada para envío',
  `completada_at` DATETIME DEFAULT NULL COMMENT 'Fecha de finalización',
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_plantilla` (`plantilla_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_creado_por` (`creado_por`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Campañas de newsletter';

-- Nota: Las foreign keys se omiten para evitar problemas de compatibilidad
-- Si necesitas agregarlas manualmente:
-- ALTER TABLE `newsletter_campanas` ADD CONSTRAINT `fk_campana_plantilla` FOREIGN KEY (`plantilla_id`) REFERENCES `newsletter_templates` (`id`) ON DELETE RESTRICT;
-- ALTER TABLE `newsletter_campanas` ADD CONSTRAINT `fk_campana_usuario` FOREIGN KEY (`creado_por`) REFERENCES `admin_usuarios` (`id`) ON DELETE SET NULL;

-- Tabla de envíos individuales
CREATE TABLE IF NOT EXISTS `newsletter_envios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `campana_id` INT(11) NOT NULL COMMENT 'ID de la campaña',
  `destinatario_id` INT(11) DEFAULT NULL COMMENT 'ID del destinatario en newsletter_simple',
  `email` VARCHAR(255) NOT NULL COMMENT 'Email del destinatario',
  `estado` ENUM('enviado', 'fallido', 'rebotado', 'abierto', 'clic') DEFAULT 'enviado',
  `mensaje_error` TEXT DEFAULT NULL COMMENT 'Mensaje de error si falló',
  `enviado_at` DATETIME DEFAULT NULL COMMENT 'Fecha y hora del envío',
  `abierto_at` DATETIME DEFAULT NULL COMMENT 'Fecha y hora de apertura',
  `clic_at` DATETIME DEFAULT NULL COMMENT 'Fecha y hora del primer clic',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_campana` (`campana_id`),
  KEY `idx_destinatario` (`destinatario_id`),
  KEY `idx_email` (`email`),
  KEY `idx_estado` (`estado`),
  KEY `idx_enviado_at` (`enviado_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de envíos individuales de campañas';

-- Nota: La foreign key se omite para evitar problemas de compatibilidad
-- Si necesitas agregarla manualmente:
-- ALTER TABLE `newsletter_envios` ADD CONSTRAINT `fk_envio_campana` FOREIGN KEY (`campana_id`) REFERENCES `newsletter_campanas` (`id`) ON DELETE CASCADE;

