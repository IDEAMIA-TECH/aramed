-- ========================================
-- TABLA: menu_config
-- Configuración de visibilidad del menú principal
-- ========================================
CREATE TABLE IF NOT EXISTS `menu_config` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `item_key` VARCHAR(50) NOT NULL COMMENT 'Clave única del elemento del menú (home, catalogo, blog, etc.)',
  `label` VARCHAR(100) NOT NULL COMMENT 'Etiqueta del menú',
  `href` VARCHAR(500) DEFAULT NULL COMMENT 'URL del enlace',
  `icon` VARCHAR(50) DEFAULT NULL COMMENT 'Icono Bootstrap Icons',
  `section` VARCHAR(50) DEFAULT NULL COMMENT 'Sección para identificar página activa',
  `orden` INT(11) DEFAULT 0 COMMENT 'Orden de visualización',
  `visible` TINYINT(1) DEFAULT 1 COMMENT '1 = visible, 0 = oculto',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_item_key` (`item_key`),
  INDEX `idx_visible` (`visible`),
  INDEX `idx_orden` (`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuración de elementos del menú principal';

-- Insertar elementos del menú por defecto
INSERT INTO `menu_config` (`item_key`, `label`, `href`, `icon`, `section`, `orden`, `visible`) VALUES
('home', 'Inicio', '/', 'house', 'home', 1, 1),
('catalogo', 'Catálogo', '/catalogo.php', 'grid-3x3-gap', 'catalogo', 2, 1),
('blog', 'Blog', '/blog.php', 'newspaper', 'blog', 3, 1),
('proyectos', 'Proyectos', '/proyectos.php', 'folder', 'proyectos', 4, 1),
('aliados', 'Aliados', '/#aliados', 'people', 'aliados', 5, 1)
ON DUPLICATE KEY UPDATE 
    `label` = VALUES(`label`),
    `href` = VALUES(`href`),
    `icon` = VALUES(`icon`),
    `section` = VALUES(`section`),
    `orden` = VALUES(`orden`);

