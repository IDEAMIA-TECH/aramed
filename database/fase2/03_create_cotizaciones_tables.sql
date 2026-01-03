-- ========================================
-- ARAMED Y LABORATORIOS - Cotizaciones Avanzado
-- ========================================
-- 
-- Estructura de base de datos para sistema avanzado de cotizaciones
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios

-- ========================================
-- Tabla: cotizaciones
-- Almacena las cotizaciones principales
-- ========================================
CREATE TABLE IF NOT EXISTS `cotizaciones` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  
  -- Folio único
  `folio` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Folio único: COT-2025-001',
  
  -- Información de la Institución
  `institucion` VARCHAR(255) NOT NULL,
  `tipo_institucion` VARCHAR(100) NOT NULL,
  `campo_adicional` VARCHAR(255) DEFAULT NULL,
  `estado` VARCHAR(100) NOT NULL,
  `ciudad` VARCHAR(150) NOT NULL,
  
  -- Información del Contacto
  `nombre` VARCHAR(255) NOT NULL,
  `puesto` VARCHAR(150) NOT NULL,
  `email_oficial` VARCHAR(255) NOT NULL,
  `email_alterno` VARCHAR(255) DEFAULT NULL,
  `telefono_oficina` VARCHAR(50) NOT NULL,
  `extension` VARCHAR(20) DEFAULT NULL,
  `telefono_celular` VARCHAR(50) DEFAULT NULL,
  
  -- Información de la Cotización
  `producto_interes` VARCHAR(255) DEFAULT NULL COMMENT 'Producto principal de interés',
  `fecha_compra_aprox` DATE DEFAULT NULL,
  `presupuesto_estimado` DECIMAL(15,2) DEFAULT NULL,
  `observaciones` TEXT DEFAULT NULL,
  
  -- Gestión
  `estado_cotizacion` ENUM('nueva', 'en_seguimiento', 'cotizada', 'enviada', 'cerrada_ganada', 'cerrada_perdida') DEFAULT 'nueva',
  `assigned_to` INT(11) DEFAULT NULL COMMENT 'ID del ejecutivo asignado',
  `notas_internas` TEXT DEFAULT NULL COMMENT 'Notas internas del equipo',
  `pdf_propuesta` VARCHAR(255) DEFAULT NULL COMMENT 'Ruta al PDF de propuesta',
  
  -- Metadata
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(500) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `folio` (`folio`),
  KEY `idx_estado_cotizacion` (`estado_cotizacion`),
  KEY `idx_assigned_to` (`assigned_to`),
  KEY `idx_email_oficial` (`email_oficial`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_institucion` (`institucion`),
  FOREIGN KEY (`assigned_to`) REFERENCES `admin_usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Tabla: cotizacion_items
-- Almacena los productos/items de cada cotización
-- ========================================
CREATE TABLE IF NOT EXISTS `cotizacion_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `cotizacion_id` INT(11) NOT NULL,
  `producto_id` INT(11) DEFAULT NULL COMMENT 'ID del producto del catálogo (opcional)',
  `producto_nombre` VARCHAR(255) NOT NULL COMMENT 'Nombre del producto',
  `producto_codigo` VARCHAR(100) DEFAULT NULL COMMENT 'Código/SKU del producto',
  `cantidad` INT(11) DEFAULT 1,
  `precio_unitario` DECIMAL(15,2) DEFAULT NULL,
  `descuento` DECIMAL(5,2) DEFAULT 0 COMMENT 'Porcentaje de descuento',
  `subtotal` DECIMAL(15,2) DEFAULT NULL,
  `notas` TEXT DEFAULT NULL COMMENT 'Notas específicas del item',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_cotizacion_id` (`cotizacion_id`),
  KEY `idx_producto_id` (`producto_id`),
  FOREIGN KEY (`cotizacion_id`) REFERENCES `cotizaciones`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`producto_id`) REFERENCES `catalogo_productos`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Tabla: cotizacion_auditoria
-- Almacena el historial de cambios y acciones
-- ========================================
CREATE TABLE IF NOT EXISTS `cotizacion_auditoria` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `cotizacion_id` INT(11) NOT NULL,
  `usuario_id` INT(11) NOT NULL,
  `accion` VARCHAR(100) NOT NULL COMMENT 'Ej: estado_cambiado, asignado, nota_agregada, pdf_subido',
  `campo_anterior` VARCHAR(100) DEFAULT NULL,
  `valor_anterior` TEXT DEFAULT NULL,
  `campo_nuevo` VARCHAR(100) DEFAULT NULL,
  `valor_nuevo` TEXT DEFAULT NULL,
  `detalles` TEXT DEFAULT NULL COMMENT 'Información adicional',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_cotizacion_id` (`cotizacion_id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`cotizacion_id`) REFERENCES `cotizaciones`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`usuario_id`) REFERENCES `admin_usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Trigger para generar folio automáticamente
-- ========================================
DELIMITER $$

CREATE TRIGGER `generar_folio_cotizacion` 
BEFORE INSERT ON `cotizaciones`
FOR EACH ROW
BEGIN
  IF NEW.folio IS NULL OR NEW.folio = '' THEN
    SET @year = YEAR(NOW());
    SET @numero = (SELECT COALESCE(MAX(CAST(SUBSTRING(folio, 10) AS UNSIGNED)), 0) + 1 
                   FROM cotizaciones 
                   WHERE folio LIKE CONCAT('COT-', @year, '-%'));
    SET NEW.folio = CONCAT('COT-', @year, '-', LPAD(@numero, 4, '0'));
  END IF;
END$$

DELIMITER ;

