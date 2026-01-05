-- ========================================
-- ARAMED Y LABORATORIOS - FASE 2
-- TABLAS PARA MÓDULO APARIENCIA & MÓDULOS
-- ========================================
-- 
-- Este script crea las tablas necesarias para gestionar:
-- - Secciones del Home (activar/desactivar, ordenar)
-- - Páginas estáticas personalizadas
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios
-- @created    Enero 2025

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ========================================
-- 1. TABLA: home_secciones
-- Control de visibilidad y orden de secciones del Home
-- ========================================
CREATE TABLE IF NOT EXISTS `home_secciones` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `seccion` VARCHAR(100) NOT NULL COMMENT 'Identificador de la sección (hero, servicios, productos_destacados, mision_vision, categorias_destacadas, proyectos, newsletter)',
  `nombre` VARCHAR(255) NOT NULL COMMENT 'Nombre descriptivo de la sección',
  `activa` TINYINT(1) DEFAULT 1 COMMENT '1 = activa, 0 = inactiva',
  `orden` INT(11) DEFAULT 0 COMMENT 'Orden de visualización (menor = primero)',
  `configuracion` TEXT DEFAULT NULL COMMENT 'Configuración adicional en JSON',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_seccion` (`seccion`),
  INDEX `idx_activa` (`activa`),
  INDEX `idx_orden` (`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Control de secciones del Home';

-- Insertar secciones por defecto
INSERT INTO `home_secciones` (`seccion`, `nombre`, `activa`, `orden`) VALUES
('hero', 'Hero / Banner Principal', 1, 1),
('servicios', 'Soluciones Integrales para Educación Médica', 1, 2),
('productos_destacados', 'Productos Destacados', 1, 3),
('mision_vision', 'Misión y Visión', 1, 4),
('categorias_destacadas', 'Categorías Destacadas', 1, 5),
('proyectos', 'Proyectos Recientes', 0, 6),
('newsletter', 'Newsletter / Suscripción', 1, 7)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- ========================================
-- 2. TABLA: paginas_estaticas
-- Páginas estáticas personalizadas del sitio
-- ========================================
CREATE TABLE IF NOT EXISTS `paginas_estaticas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(255) NOT NULL COMMENT 'Título de la página',
  `slug` VARCHAR(255) NOT NULL COMMENT 'URL amigable (ej: sobre-nosotros)',
  `contenido` LONGTEXT DEFAULT NULL COMMENT 'Contenido HTML de la página',
  `contenido_markdown` LONGTEXT DEFAULT NULL COMMENT 'Contenido en Markdown (opcional)',
  `meta_titulo` VARCHAR(255) DEFAULT NULL COMMENT 'Meta título para SEO',
  `meta_descripcion` TEXT DEFAULT NULL COMMENT 'Meta descripción para SEO',
  `meta_keywords` VARCHAR(500) DEFAULT NULL COMMENT 'Meta keywords',
  `imagen_principal` VARCHAR(255) DEFAULT NULL COMMENT 'Imagen destacada de la página',
  `plantilla` VARCHAR(100) DEFAULT 'default' COMMENT 'Plantilla a usar (default, full-width, sidebar)',
  `estado` ENUM('borrador', 'publicado', 'archivado') DEFAULT 'borrador',
  `orden` INT(11) DEFAULT 0 COMMENT 'Orden para menús',
  `mostrar_en_menu` TINYINT(1) DEFAULT 0 COMMENT '1 = mostrar en menú principal',
  `menu_label` VARCHAR(100) DEFAULT NULL COMMENT 'Etiqueta para el menú',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `publicado_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Fecha de publicación',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`),
  INDEX `idx_estado` (`estado`),
  INDEX `idx_mostrar_en_menu` (`mostrar_en_menu`),
  INDEX `idx_orden` (`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Páginas estáticas personalizadas';

-- Insertar páginas de ejemplo (opcional)
-- INSERT INTO `paginas_estaticas` (`titulo`, `slug`, `contenido`, `estado`, `mostrar_en_menu`, `menu_label`) VALUES
-- ('Sobre Nosotros', 'sobre-nosotros', '<h1>Sobre Nosotros</h1><p>Contenido...</p>', 'publicado', 1, 'Sobre Nosotros'),
-- ('Política de Privacidad', 'privacidad', '<h1>Política de Privacidad</h1><p>Contenido...</p>', 'publicado', 0, NULL),
-- ('Términos y Condiciones', 'terminos', '<h1>Términos y Condiciones</h1><p>Contenido...</p>', 'publicado', 0, NULL);

