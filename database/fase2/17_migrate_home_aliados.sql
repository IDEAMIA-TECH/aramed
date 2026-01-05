-- ========================================
-- ARAMED Y LABORATORIOS - FASE 2
-- MIGRACIÓN DE ALIADOS / PARTNERS GLOBALES
-- ========================================
-- 
-- Este script migra los aliados hardcodeados en index.php
-- a la tabla home_aliados para gestión desde el admin
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios
-- @created    Enero 2025

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ========================================
-- MIGRACIÓN DE ALIADOS
-- ========================================

-- Verificar si la tabla existe
-- Si no existe, ejecutar primero: database/fase2/16_create_home_aliados_table.sql

-- Limpiar aliados existentes (opcional - comentar si quieres conservar los existentes)
-- DELETE FROM home_aliados;

-- ========================================
-- INSERTAR ALIADOS
-- ========================================
-- NOTA: Los aliados aparecen en dos secciones:
-- 1. Carrusel simple de logos (mostrar_en_carrusel = 1)
-- 2. Carrusel detallado con descripciones (mostrar_en_detalle = 1)

-- Aliado 1: GAUMARD
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'GAUMARD',
    'aliados/1-Gaumard.webp',
    'Gaumard Scientific desarrolla simuladores médicos de alta fidelidad que transforman la enseñanza clínica. Su innovación tecnológica complementa nuestra misión de ofrecer experiencias de aprendizaje realistas y seguras en salud.',
    NULL,
    1,
    1,
    1,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 2: KYOTO KAGAKU
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'KYOTO KAGAKU',
    'aliados/2-Kyoto-Kagaku.webp',
    'Kyoto Kagaku fabrica modelos anatómicos, simuladores y "phantoms" para imagen médica. Su precisión e innovación fortalecen nuestra excelencia educativa y liderazgo en simulación.',
    NULL,
    1,
    1,
    2,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 3: ANATOMAGE
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'ANATOMAGE',
    'aliados/3-Anatomage.webp',
    'Anatomage crea plataformas 3D interactivas que revolucionan la enseñanza anatómica mediante visualizaciones precisas del cuerpo humano. Su innovación eleva nuestros estándares en simulación médica educativa.',
    NULL,
    1,
    1,
    3,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 4: RUDIGER
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'RUDIGER',
    'aliados/4-Rudiger.webp',
    'Rüdiger Anatomie produce modelos anatómicos y pósters educativos "Made in Germany" con manufactura artesanal. Su precisión y autenticidad enriquecen nuestra enseñanza de ciencias de la salud.',
    NULL,
    1,
    1,
    4,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 5: SIMULAB
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'SIMULAB',
    'aliados/5-Simulab.webp',
    NULL,
    NULL,
    1,
    0,
    5,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 6: 3D MED
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    '3D MED',
    'aliados/6-3D-Med.webp',
    '3-Dmed diseña simuladores quirúrgicos y entrenadores médicos de alta precisión. Su enfoque en realismo y desempeño mejora nuestras soluciones para la práctica clínica y educativa.',
    NULL,
    1,
    1,
    6,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 7: 3B SCIENTIFIC
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    '3B SCIENTIFIC',
    'aliados/7-3B Scientific.webp',
    '3B Scientific fabrica modelos anatómicos y simuladores médicos para educación en salud. Su calidad global refuerza nuestra oferta educativa y credibilidad como aliado estratégico.',
    NULL,
    1,
    1,
    7,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 8: ADAM ROUILLY
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'ADAM ROUILLY',
    'aliados/8-Adam Rouilly.webp',
    'AdamRouilly diseña desde 1918 modelos anatómicos, simuladores clínicos y herramientas formativas. Su legado, innovación y versatilidad enriquecen nuestro portafolio educativo.',
    NULL,
    1,
    1,
    8,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 9: ERLER ZIMMER
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'ERLER ZIMMER',
    'aliados/9-Erler-Zimmer.webp',
    'Erler-Zimmer diseña modelos anatómicos y simuladores médicos con altísima calidad histórica. Su innovación y rigor elevan nuestra formación práctica con precisión educativa.',
    NULL,
    1,
    1,
    9,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 10: TRUCORP
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'TRUCORP',
    'aliados/10-TrueCorp.webp',
    'TruCorp fabrica maniquíes y simuladores médicos con retroalimentación en tiempo real para entrenamiento clínico. Su realismo y precisión elevan nuestra formación práctica y eficacia educativa.',
    NULL,
    1,
    1,
    10,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 11: SIMX
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'SIMX',
    'aliados/11-SimX.webp',
    'SimX desarrolla simulaciones médicas en realidad virtual inmersiva que entrenan juicio clínico realista. Su innovación potencia nuestra oferta formativa de alto impacto.',
    NULL,
    1,
    1,
    11,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 12: VATA
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'VATA',
    'aliados/12-VATA.webp',
    'VATA Inc. desarrolla herramientas de simulación médica realistas (acceso vascular, heridas, modelos de ultrasonido). Su precisión eleva nuestras prácticas clínicas y fortalece nuestra formación.',
    NULL,
    1,
    1,
    12,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 13: MEDICAL X
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'MEDICAL X',
    'aliados/13-Medical X.webp',
    'Medical-X desarrolla simuladores médicos de alta fidelidad para entrenamiento clínico. Su tecnología avanzada potencia a Aramed en formación realista y segura.',
    NULL,
    1,
    1,
    13,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 14: IMMERSIVE
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'IMMERSIVE',
    'aliados/14-immersive.webp',
    'Immersive Healthcare desarrolla soluciones de simulación médica inmersiva que transforman la educación clínica mediante tecnología de realidad virtual y aumentada. Su innovación potencia nuestra formación con experiencias de aprendizaje únicas.',
    NULL,
    1,
    1,
    14,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 15: SARATOGA
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'SARATOGA',
    'aliados/15-Saratoga.webp',
    'Saratoga Dental diseña y fabrica equipos dentales, laboratorios técnicos y simuladores formativos. Su enfoque "a medida" refuerza nuestra oferta educativa con soluciones profesionales y personalizadas.',
    NULL,
    1,
    1,
    15,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 16: NASCO HEALTHCARE
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'NASCO HEALTHCARE',
    'aliados/16-Nasco Healthcare.webp',
    'Nasco Healthcare provee simuladores clínicos, maniquíes y herramientas de entrenamiento para emergencias y cuidados avanzados. Su oferta robustece nuestra formación con tecnología confiable.',
    NULL,
    1,
    1,
    16,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 17: SAFEGUARD MEDICAL (SIMBODIES)
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'SAFEGUARD / SIMBODIES',
    'aliados/17-Safeguard Medical (Simbodies).webp',
    'Safeguard Medical provee tecnología, equipamiento y entrenamiento en medicina de emergencia. Su enfoque en salvamento y realismo fortalece nuestro respaldo en formación crítica.',
    NULL,
    1,
    1,
    17,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 18: LIFECAST
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'LIFECAST',
    'aliados/18-Lifecast.webp',
    'Lifecast desarrolla modelos anatómicos y simuladores médicos de alta fidelidad para educación en salud. Su compromiso con la calidad y realismo fortalece nuestra oferta educativa con soluciones innovadoras.',
    NULL,
    1,
    1,
    18,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 19: KEKLIKOĞLU
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'KEKLIKOĞLU',
    'aliados/19-KEKLIGOKLU.webp',
    'Keklikoğlu desarrolla modelos anatómicos de alta fidelidad que elevan la enseñanza clínica y veterinaria. Su compromiso con calidad e innovación fortalece nuestra misión de aprendizaje seguro y realista.',
    NULL,
    1,
    1,
    19,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 20: iSIMULATE
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'iSIMULATE',
    'aliados/18-iSimulate-1.webp',
    'iSimulate desarrolla soluciones de simulación médica móviles e inteligentes que elevan la formación clínica. Su tecnología complementa nuestra misión de brindar capacitación realista, eficiente y accesible en salud.',
    NULL,
    1,
    1,
    20,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 21: ECHO HEALTHCARE
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'ECHO HEALTHCARE',
    'aliados/7-Echo Healthcare.webp',
    'Echo Healthcare desarrolla soluciones inmersivas y realistas para simulación médica (maniquíes, máscaras, entornos interactivos). Su innovación eleva nuestra oferta formativa con un enfoque de alto impacto.',
    NULL,
    0,
    1,
    21,
    'activo',
    NOW(),
    NOW()
);

-- Aliado 22: STRATEGIC OPERATIONS
INSERT INTO `home_aliados` (
    `nombre`,
    `logo_url`,
    `descripcion`,
    `url_website`,
    `mostrar_en_carrusel`,
    `mostrar_en_detalle`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'STRATEGIC OPERATIONS',
    'aliados/21-strategic-operations.webp',
    'Strategic Operations desarrolla simuladores quirúrgicos de alta fidelidad que replican con exactitud la anatomía humana y las condiciones del quirófano. Gracias a esta alianza, potenciamos nuestra capacidad para brindar capacitación avanzada en entornos controlados.',
    NULL,
    0,
    1,
    22,
    'activo',
    NOW(),
    NOW()
);

-- ========================================
-- VERIFICACIÓN FINAL
-- ========================================

-- Mostrar aliados migrados
SELECT
    id,
    orden,
    estado,
    nombre,
    logo_url,
    mostrar_en_carrusel,
    mostrar_en_detalle,
    LEFT(descripcion, 50) AS descripcion_corta,
    created_at
FROM home_aliados
ORDER BY orden ASC;

