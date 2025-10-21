-- ========================================
-- NUEVA ESTRUCTURA PARA CATÁLOGO DE PRODUCTOS
-- ========================================
-- Basada en el análisis de las tablas del sistema viejo
-- Optimizada para el nuevo sitio web

-- --------------------------------------------------------
-- Tabla de Marcas (simplificada y optimizada)
-- --------------------------------------------------------
CREATE TABLE `catalogo_marcas` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `orden` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `estado` (`estado`),
  KEY `orden` (`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabla de Categorías de Uso (simplificada)
-- --------------------------------------------------------
CREATE TABLE `catalogo_categorias` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `icono` varchar(50) DEFAULT NULL,
  `color` varchar(7) DEFAULT '#0066CC',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `orden` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `estado` (`estado`),
  KEY `orden` (`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabla Principal de Productos (optimizada)
-- --------------------------------------------------------
CREATE TABLE `catalogo_productos` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `descripcion_corta` text DEFAULT NULL,
  `descripcion_larga` longtext DEFAULT NULL,
  `caracteristicas` json DEFAULT NULL,
  `especificaciones` json DEFAULT NULL,
  `marca_id` int(10) UNSIGNED NOT NULL,
  `categoria_id` int(10) UNSIGNED NOT NULL,
  `precio_publico` decimal(10,2) DEFAULT NULL,
  `precio_especial` decimal(10,2) DEFAULT NULL,
  `moneda` varchar(3) DEFAULT 'MXN',
  `stock` int(11) DEFAULT 0,
  `disponibilidad` enum('disponible','agotado','por_pedido') DEFAULT 'disponible',
  `destacado` boolean DEFAULT false,
  `nuevo` boolean DEFAULT false,
  `promocion` boolean DEFAULT false,
  `imagen_principal` varchar(255) DEFAULT NULL,
  `galeria` json DEFAULT NULL,
  `documentos` json DEFAULT NULL,
  `videos` json DEFAULT NULL,
  `meta_titulo` varchar(255) DEFAULT NULL,
  `meta_descripcion` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `estado` enum('activo','inactivo','borrador') DEFAULT 'borrador',
  `visitas` int(11) DEFAULT 0,
  `orden` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  UNIQUE KEY `slug` (`slug`),
  KEY `marca_id` (`marca_id`),
  KEY `categoria_id` (`categoria_id`),
  KEY `estado` (`estado`),
  KEY `destacado` (`destacado`),
  KEY `nuevo` (`nuevo`),
  KEY `promocion` (`promocion`),
  KEY `disponibilidad` (`disponibilidad`),
  KEY `orden` (`orden`),
  FULLTEXT KEY `busqueda` (`nombre`,`descripcion_corta`,`descripcion_larga`),
  CONSTRAINT `fk_productos_marca` FOREIGN KEY (`marca_id`) REFERENCES `catalogo_marcas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `catalogo_categorias` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabla de Relaciones Producto-Imagen (optimizada)
-- --------------------------------------------------------
CREATE TABLE `catalogo_producto_imagenes` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `producto_id` int(10) UNSIGNED NOT NULL,
  `imagen_url` varchar(500) NOT NULL,
  `imagen_alt` varchar(255) DEFAULT NULL,
  `imagen_titulo` varchar(255) DEFAULT NULL,
  `es_principal` boolean DEFAULT false,
  `orden` int(11) DEFAULT 0,
  `tipo` enum('producto','detalle','uso','galeria') DEFAULT 'producto',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `producto_id` (`producto_id`),
  KEY `es_principal` (`es_principal`),
  KEY `tipo` (`tipo`),
  KEY `orden` (`orden`),
  CONSTRAINT `fk_imagenes_producto` FOREIGN KEY (`producto_id`) REFERENCES `catalogo_productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabla de Documentos de Productos
-- --------------------------------------------------------
CREATE TABLE `catalogo_producto_documentos` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `producto_id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `archivo_url` varchar(500) NOT NULL,
  `tipo` enum('manual','ficha_tecnica','certificado','brochure','video') DEFAULT 'ficha_tecnica',
  `tamaño` int(11) DEFAULT NULL,
  `formato` varchar(10) DEFAULT NULL,
  `idioma` varchar(5) DEFAULT 'es',
  `orden` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `producto_id` (`producto_id`),
  KEY `tipo` (`tipo`),
  KEY `idioma` (`idioma`),
  KEY `orden` (`orden`),
  CONSTRAINT `fk_documentos_producto` FOREIGN KEY (`producto_id`) REFERENCES `catalogo_productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabla de Filtros y Búsquedas (para funcionalidad avanzada)
-- --------------------------------------------------------
CREATE TABLE `catalogo_filtros` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('checkbox','radio','range','select') DEFAULT 'checkbox',
  `opciones` json DEFAULT NULL,
  `categoria_id` int(10) UNSIGNED DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `orden` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `categoria_id` (`categoria_id`),
  KEY `estado` (`estado`),
  KEY `orden` (`orden`),
  CONSTRAINT `fk_filtros_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `catalogo_categorias` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabla de Estadísticas de Productos
-- --------------------------------------------------------
CREATE TABLE `catalogo_producto_stats` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `producto_id` int(10) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `visitas` int(11) DEFAULT 0,
  `descargas` int(11) DEFAULT 0,
  `contactos` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `producto_fecha` (`producto_id`,`fecha`),
  KEY `fecha` (`fecha`),
  CONSTRAINT `fk_stats_producto` FOREIGN KEY (`producto_id`) REFERENCES `catalogo_productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- INSERTAR DATOS INICIALES
-- --------------------------------------------------------

-- Insertar marcas principales (basadas en las del sistema viejo)
INSERT INTO `catalogo_marcas` (`nombre`, `slug`, `estado`, `orden`) VALUES
('Adam Rouilly', 'adam-rouilly', 'activo', 1),
('Gaumard', 'gaumard', 'activo', 2),
('Kyoto Kagaku', 'kyoto-kagaku', 'activo', 3),
('Anatomage', 'anatomage', 'activo', 4),
('3B Scientific', '3b-scientific', 'activo', 5),
('3D-Med', '3d-med', 'activo', 6),
('Nasco', 'nasco', 'activo', 7),
('Simulaids', 'simulaids', 'activo', 8),
('Trucorp', 'trucorp', 'activo', 9),
('Saratoga', 'saratoga', 'activo', 10),
('Simulab', 'simulab', 'activo', 11),
('Vata', 'vata', 'activo', 12),
('Ruediger Anatomie', 'ruediger-anatomie', 'activo', 13),
('Surgical Science', 'surgical-science', 'activo', 14),
('Lifelike Biotissue', 'lifelike-biotissue', 'activo', 15),
('Epona', 'epona', 'activo', 16),
('mySmartHealthcare', 'mysmarthealthcare', 'activo', 17),
('Sakamoto', 'sakamoto', 'activo', 18),
('Chamberlain', 'chamberlain', 'activo', 19);

-- Insertar categorías principales (basadas en los usos del sistema viejo)
INSERT INTO `catalogo_categorias` (`nombre`, `slug`, `descripcion`, `icono`, `color`, `estado`, `orden`) VALUES
('Simulación Adulta', 'simulacion-adulta', 'Simuladores y maniquíes para pacientes adultos', 'bi-person', '#0066CC', 'activo', 1),
('Simulación Pediátrica', 'simulacion-pediatrica', 'Simuladores y maniquíes para pacientes pediátricos', 'bi-person-heart', '#FF6B6B', 'activo', 2),
('Simulación Neonatal', 'simulacion-neonatal', 'Simuladores y maniquíes para recién nacidos', 'bi-heart', '#4ECDC4', 'activo', 3),
('Simulación Obstétrica', 'simulacion-obstetrica', 'Simuladores para partos y obstetricia', 'bi-gender-female', '#FFE66D', 'activo', 4),
('Anatomía y Fisiología', 'anatomia-fisiologia', 'Modelos anatómicos y sistemas fisiológicos', 'bi-diagram-3', '#95E1D3', 'activo', 5),
('Emergencias y Trauma', 'emergencias-trauma', 'Simuladores para emergencias médicas', 'bi-ambulance', '#FF8A80', 'activo', 6),
('Cirugía y Procedimientos', 'cirugia-procedimientos', 'Simuladores quirúrgicos y procedimientos', 'bi-scissors', '#B39DDB', 'activo', 7),
('Diagnóstico por Imágenes', 'diagnostico-imagenes', 'Equipos de diagnóstico y visualización', 'bi-camera', '#81C784', 'activo', 8),
('Rehabilitación', 'rehabilitacion', 'Equipos de terapia y rehabilitación', 'bi-activity', '#FFB74D', 'activo', 9),
('Educación Médica', 'educacion-medica', 'Herramientas y recursos educativos', 'bi-book', '#64B5F6', 'activo', 10);

-- --------------------------------------------------------
-- COMENTARIOS Y DOCUMENTACIÓN
-- --------------------------------------------------------

/*
ESTRUCTURA OPTIMIZADA PARA EL NUEVO CATÁLOGO:

1. **catalogo_marcas**: Marcas simplificadas con slugs para URLs amigables
2. **catalogo_categorias**: Categorías organizadas con iconos y colores
3. **catalogo_productos**: Tabla principal con campos JSON para flexibilidad
4. **catalogo_producto_imagenes**: Relación optimizada de imágenes
5. **catalogo_producto_documentos**: Gestión de PDFs y documentos
6. **catalogo_filtros**: Sistema de filtros avanzados
7. **catalogo_producto_stats**: Estadísticas y métricas

VENTAJAS DE LA NUEVA ESTRUCTURA:
- URLs amigables con slugs
- Campos JSON para datos flexibles
- Optimización para búsquedas full-text
- Sistema de estadísticas integrado
- Mejor organización de recursos multimedia
- Preparado para SEO avanzado
- Escalable y mantenible

MIGRACIÓN DE DATOS:
- Las 882 productos del sistema viejo se pueden migrar
- Las 1012 relaciones de imágenes se preservan
- Los 56 archivos de productos-cat se integran
- Los 282 PDFs se organizan por producto
- Las 24 marcas se simplifican y optimizan
*/
