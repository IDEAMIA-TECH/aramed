-- ========================================
-- ARAMED Y LABORATORIOS - FASE 2
-- MIGRACIÓN DE BANNERS DEL HOME
-- ========================================
-- 
-- Este script migra los banners hardcodeados en index.php
-- a la tabla home_banners para gestión desde el admin
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios
-- @created    Enero 2025

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ========================================
-- MIGRACIÓN DE BANNERS
-- ========================================

-- Verificar si la tabla existe
-- Si no existe, ejecutar primero: database/fase2/01_create_home_tables.sql

-- Limpiar banners existentes (opcional - comentar si quieres conservar los existentes)
-- DELETE FROM home_banners;

-- ========================================
-- NOTA: Ejecutar primero 11_alter_home_banners_table.sql
-- para agregar los campos adicionales (badge_texto, descripcion, caracteristicas, cta2_texto, cta2_url)
-- ========================================

-- Banner 1: Principal
INSERT INTO `home_banners` (
    `titulo`, 
    `subtitulo`, 
    `badge_texto`,
    `descripcion`,
    `caracteristicas`,
    `imagen_url`, 
    `cta_texto`, 
    `cta_url`, 
    `cta2_texto`,
    `cta2_url`,
    `orden`, 
    `estado`,
    `fecha_inicio`,
    `fecha_fin`
) VALUES (
    'Simuladores médicos para la enseñanza',
    'Distribuidores líderes de tecnología educativa en salud. Equipamos universidades, hospitales e instituciones con simuladores de última generación.',
    NULL,
    NULL,
    NULL,
    'hero/aramedylaboratorio.jpg',
    'Contáctanos',
    '#newsletter',
    NULL,
    NULL,
    1,
    'publicado',
    NULL,
    NULL
) ON DUPLICATE KEY UPDATE 
    titulo = VALUES(titulo),
    subtitulo = VALUES(subtitulo),
    badge_texto = VALUES(badge_texto),
    descripcion = VALUES(descripcion),
    caracteristicas = VALUES(caracteristicas),
    imagen_url = VALUES(imagen_url),
    cta_texto = VALUES(cta_texto),
    cta_url = VALUES(cta_url),
    cta2_texto = VALUES(cta2_texto),
    cta2_url = VALUES(cta2_url),
    orden = VALUES(orden),
    estado = VALUES(estado);

-- Banner 2: VICTORIA® S2200
INSERT INTO `home_banners` (
    `titulo`, 
    `subtitulo`, 
    `badge_texto`,
    `descripcion`,
    `caracteristicas`,
    `imagen_url`, 
    `cta_texto`, 
    `cta_url`, 
    `cta2_texto`,
    `cta2_url`,
    `orden`, 
    `estado`,
    `fecha_inicio`,
    `fecha_fin`
) VALUES (
    'VICTORIA® S2200',
    'El simulador de parto más avanzado del mundo',
    'Simulador Obstétrico',
    'Entrenamiento realista para partos, emergencias y cuidados maternos',
    'Ojos interactivos\n4 abdómenes: vientre para procedimientos de cesárea, trabajo de parto, hemorragia post parto, maniobras de leopold\nCompatible con monitores y equipos clínicos reales\nNeonato con signos vitales y respuesta realista\nDos simuladores en un solo modelo: simulación obstétrica y ginecológica',
    'hero/hero-victoria-s2200.jpg',
    'Solicitar Información',
    '#newsletter',
    'Ver Detalles',
    '#productos',
    2,
    'publicado',
    NULL,
    NULL
) ON DUPLICATE KEY UPDATE 
    titulo = VALUES(titulo),
    subtitulo = VALUES(subtitulo),
    badge_texto = VALUES(badge_texto),
    descripcion = VALUES(descripcion),
    caracteristicas = VALUES(caracteristicas),
    imagen_url = VALUES(imagen_url),
    cta_texto = VALUES(cta_texto),
    cta_url = VALUES(cta_url),
    cta2_texto = VALUES(cta2_texto),
    cta2_url = VALUES(cta2_url),
    orden = VALUES(orden),
    estado = VALUES(estado);

-- Banner 3: HAL® S5301
INSERT INTO `home_banners` (
    `titulo`, 
    `subtitulo`, 
    `badge_texto`,
    `descripcion`,
    `caracteristicas`,
    `imagen_url`, 
    `cta_texto`, 
    `cta_url`, 
    `cta2_texto`,
    `cta2_url`,
    `orden`, 
    `estado`,
    `fecha_inicio`,
    `fecha_fin`
) VALUES (
    'HAL® S5301',
    'Donde la simulación se convierte en experiencia real',
    'Simulación Avanzada',
    'Audio, expresiones faciales y movimientos realistas',
    'Audio, expresiones faciales y movimientos realistas\nSimulación de problemas neurológicos\nRespuesta activa al dolor y presión\nReconocimiento automático de fármacos y fluidos\nEscenarios SLE™ interdisciplinarios preinstalados',
    'hero/hero-hal-s5301.jpg',
    'Solicitar Demo',
    '#newsletter',
    'Conocer Más',
    '#productos',
    3,
    'publicado',
    NULL,
    NULL
) ON DUPLICATE KEY UPDATE 
    titulo = VALUES(titulo),
    subtitulo = VALUES(subtitulo),
    badge_texto = VALUES(badge_texto),
    descripcion = VALUES(descripcion),
    caracteristicas = VALUES(caracteristicas),
    imagen_url = VALUES(imagen_url),
    cta_texto = VALUES(cta_texto),
    cta_url = VALUES(cta_url),
    cta2_texto = VALUES(cta2_texto),
    cta2_url = VALUES(cta2_url),
    orden = VALUES(orden),
    estado = VALUES(estado);

-- Banner 4: HAL® S3201
INSERT INTO `home_banners` (
    `titulo`, 
    `subtitulo`, 
    `badge_texto`,
    `descripcion`,
    `caracteristicas`,
    `imagen_url`, 
    `cta_texto`, 
    `cta_url`, 
    `cta2_texto`,
    `cta2_url`,
    `orden`, 
    `estado`,
    `fecha_inicio`,
    `fecha_fin`
) VALUES (
    'HAL® S3201',
    'Realismo clínico en cada entrenamiento',
    'UCI y Emergencias',
    'Simulación para emergencias, UCI y medicina general',
    'Simulación para emergencias, UCI y medicina general\nFisiología dinámica y respuesta automática\nCompatible con ventiladores y equipo clínico real\nControl mediante UNI® 3\nMonitoreo ECG, SpO₂, presión y CO₂ en tiempo real',
    'hero/hero-hal-s3201.jpg',
    'Solicitar Cotización',
    '#newsletter',
    'Ver Características',
    '#productos',
    4,
    'publicado',
    NULL,
    NULL
) ON DUPLICATE KEY UPDATE 
    titulo = VALUES(titulo),
    subtitulo = VALUES(subtitulo),
    badge_texto = VALUES(badge_texto),
    descripcion = VALUES(descripcion),
    caracteristicas = VALUES(caracteristicas),
    imagen_url = VALUES(imagen_url),
    cta_texto = VALUES(cta_texto),
    cta_url = VALUES(cta_url),
    cta2_texto = VALUES(cta2_texto),
    cta2_url = VALUES(cta2_url),
    orden = VALUES(orden),
    estado = VALUES(estado);

-- Banner 5: Super TORY® S2220
INSERT INTO `home_banners` (
    `titulo`, 
    `subtitulo`, 
    `badge_texto`,
    `descripcion`,
    `caracteristicas`,
    `imagen_url`, 
    `cta_texto`, 
    `cta_url`, 
    `cta2_texto`,
    `cta2_url`,
    `orden`, 
    `estado`,
    `fecha_inicio`,
    `fecha_fin`
) VALUES (
    'Super TORY® S2220',
    'Realismo neonatal al máximo',
    'Neonatología',
    'Simulación avanzada del recién nacido con movimientos faciales y expresiones realistas',
    'Simulación avanzada del recién nacido\nMovimientos faciales y expresiones realistas\nSignos vitales y reacciones en tiempo real\nRespuesta real al soporte ventilatorio\nOperación totalmente inalámbrica y portátil',
    'hero/hero-super-tory-s2220.jpg',
    'Contactar Ahora',
    '#newsletter',
    'Más Información',
    '#productos',
    5,
    'publicado',
    NULL,
    NULL
) ON DUPLICATE KEY UPDATE 
    titulo = VALUES(titulo),
    subtitulo = VALUES(subtitulo),
    badge_texto = VALUES(badge_texto),
    descripcion = VALUES(descripcion),
    caracteristicas = VALUES(caracteristicas),
    imagen_url = VALUES(imagen_url),
    cta_texto = VALUES(cta_texto),
    cta_url = VALUES(cta_url),
    cta2_texto = VALUES(cta2_texto),
    cta2_url = VALUES(cta2_url),
    orden = VALUES(orden),
    estado = VALUES(estado);

-- Banner 6: SUSIE® S2400
INSERT INTO `home_banners` (
    `titulo`, 
    `subtitulo`, 
    `badge_texto`,
    `descripcion`,
    `caracteristicas`,
    `imagen_url`, 
    `cta_texto`, 
    `cta_url`, 
    `cta2_texto`,
    `cta2_url`,
    `orden`, 
    `estado`,
    `fecha_inicio`,
    `fecha_fin`
) VALUES (
    'SUSIE® S2400',
    'Simulación integral para el cuidado del paciente',
    'Enfermería',
    'Entrenamiento en enfermería y salud aliada',
    'Entrenamiento en enfermería y salud aliada\nExploración ginecológica y 7 senos con patologías\nEscenarios SLE™ multidisciplinarios\nFisiología y signos vitales dinámicos\nReconocimiento automático de medicamentos',
    'hero/hero-susie-s2400.jpg',
    'Agendar Demo',
    '#newsletter',
    'Especificaciones',
    '#productos',
    6,
    'publicado',
    NULL,
    NULL
) ON DUPLICATE KEY UPDATE 
    titulo = VALUES(titulo),
    subtitulo = VALUES(subtitulo),
    badge_texto = VALUES(badge_texto),
    descripcion = VALUES(descripcion),
    caracteristicas = VALUES(caracteristicas),
    imagen_url = VALUES(imagen_url),
    cta_texto = VALUES(cta_texto),
    cta_url = VALUES(cta_url),
    cta2_texto = VALUES(cta2_texto),
    cta2_url = VALUES(cta2_url),
    orden = VALUES(orden),
    estado = VALUES(estado);

-- Verificar inserción
SELECT 
    id,
    titulo,
    subtitulo,
    badge_texto,
    orden,
    estado,
    imagen_url,
    cta_texto,
    cta2_texto,
    created_at
FROM home_banners
ORDER BY orden ASC;

