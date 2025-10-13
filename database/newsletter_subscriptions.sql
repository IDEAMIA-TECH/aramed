-- ========================================
-- ARAMED Y LABORATORIOS - Newsletter Subscriptions Table
-- ========================================
-- 
-- Tabla para almacenar suscripciones al newsletter
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios

CREATE TABLE IF NOT EXISTS `newsletter_subscriptions` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    
    -- Información de la Institución
    `institucion` VARCHAR(255) NOT NULL,
    `tipo_institucion` VARCHAR(100) NOT NULL,
    `campo_adicional` VARCHAR(255) DEFAULT NULL COMMENT 'Campo extra para Escuela de salud o Institución gubernamental',
    `estado` VARCHAR(100) NOT NULL,
    `ciudad` VARCHAR(100) NOT NULL,
    
    -- Información del Contacto
    `nombre` VARCHAR(200) NOT NULL,
    `puesto` VARCHAR(150) NOT NULL,
    `email_oficial` VARCHAR(255) NOT NULL,
    `email_alterno` VARCHAR(255) DEFAULT NULL,
    `telefono_oficina` VARCHAR(50) NOT NULL,
    `extension` VARCHAR(20) DEFAULT NULL,
    `telefono_celular` VARCHAR(50) DEFAULT NULL,
    
    -- Información de Interés
    `producto_interes` VARCHAR(255) DEFAULT NULL,
    `fecha_compra_aprox` DATE DEFAULT NULL COMMENT 'Fecha aproximada de compra',
    `observaciones` TEXT DEFAULT NULL,
    
    -- Metadata
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `status` ENUM('active', 'unsubscribed', 'bounced') DEFAULT 'active',
    `unsubscribed_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `idx_email_oficial` (`email_oficial`),
    KEY `idx_status` (`status`),
    KEY `idx_institucion` (`institucion`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices adicionales para búsquedas frecuentes
CREATE INDEX `idx_estado_ciudad` ON `newsletter_subscriptions` (`estado`, `ciudad`);
CREATE INDEX `idx_tipo_institucion` ON `newsletter_subscriptions` (`tipo_institucion`);
CREATE INDEX `idx_producto_interes` ON `newsletter_subscriptions` (`producto_interes`);

