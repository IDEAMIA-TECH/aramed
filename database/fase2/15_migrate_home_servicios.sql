-- ========================================
-- ARAMED Y LABORATORIOS - FASE 2
-- MIGRACIÓN DE SERVICIOS DEL HOME
-- ========================================
-- 
-- Este script migra los servicios hardcodeados en index.php
-- a la tabla home_servicios para gestión desde el admin
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios
-- @created    Enero 2025

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ========================================
-- MIGRACIÓN DE SERVICIOS
-- ========================================

-- Verificar si la tabla existe
-- Si no existe, ejecutar primero: database/fase2/01_create_home_tables.sql

-- Limpiar servicios existentes (opcional - comentar si quieres conservar los existentes)
-- DELETE FROM home_servicios WHERE orden BETWEEN 1 AND 6;

-- ========================================
-- INSERTAR SERVICIOS
-- ========================================
-- NOTA: Si ya existen servicios con estos títulos, se insertarán como nuevos registros.
-- Para evitar duplicados, descomenta la línea DELETE anterior.

-- Servicio 1: Diseño y Desarrollo (Orden 1)
INSERT INTO `home_servicios` (
    `icono`,
    `titulo`,
    `resumen`,
    `texto_largo`,
    `cta_texto`,
    `cta_url`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'iconos/iconos-01.png',
    'Diseño y Desarrollo',
    'Diseñamos y planificamos centros de simulación médica completos, desde la conceptualización hasta la implementación. Incluye planificación arquitectónica, distribución de espacios y selección de equipamiento.',
    '<ul class="service-features">
        <li><i class="bi bi-check-circle-fill text-primary"></i> Diseño Arquitectónico especializado</li>
        <li><i class="bi bi-check-circle-fill text-primary"></i> Distribución óptima de espacios</li>
        <li><i class="bi bi-check-circle-fill text-primary"></i> Selección de equipamiento</li>
        <li><i class="bi bi-check-circle-fill text-primary"></i> Instalación y puesta en marcha</li>
    </ul>',
    'Solicitar Cotización',
    '#newsletter',
    1,
    'activo',
    NOW(),
    NOW()
);

-- Servicio 2: Instalación y Configuración (Orden 2)
INSERT INTO `home_servicios` (
    `icono`,
    `titulo`,
    `resumen`,
    `texto_largo`,
    `cta_texto`,
    `cta_url`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'iconos/iconos-02.png',
    'Instalación y Configuración',
    'Realizamos la instalación profesional de todos los equipos de simulación médica, garantizando su correcto funcionamiento y optimización desde el primer día.',
    '<ul class="service-features">
        <li><i class="bi bi-check-circle-fill text-primary"></i> Instalación profesional certificada</li>
        <li><i class="bi bi-check-circle-fill text-primary"></i> Configuración de software y sistemas</li>
        <li><i class="bi bi-check-circle-fill text-primary"></i> Pruebas de funcionamiento</li>
        <li><i class="bi bi-check-circle-fill text-primary"></i> Documentación técnica completa</li>
    </ul>',
    'Solicitar Cotización',
    '#newsletter',
    2,
    'activo',
    NOW(),
    NOW()
);

-- Servicio 3: Capacitación y Entrenamiento (Orden 3)
INSERT INTO `home_servicios` (
    `icono`,
    `titulo`,
    `resumen`,
    `texto_largo`,
    `cta_texto`,
    `cta_url`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'iconos/iconos-04.png',
    'Capacitación y Entrenamiento',
    'Ofrecemos programas de capacitación integral para instructores y personal técnico, asegurando el máximo aprovechamiento de los equipos de simulación.',
    '<ul class="service-features">
        <li><i class="bi bi-check-circle-fill text-primary"></i> Capacitación para instructores</li>
        <li><i class="bi bi-check-circle-fill text-primary"></i> Entrenamiento en operación de equipos</li>
        <li><i class="bi bi-check-circle-fill text-primary"></i> Desarrollo de escenarios clínicos</li>
        <li><i class="bi bi-check-circle-fill text-primary"></i> Soporte continuo y actualizaciones</li>
    </ul>',
    'Solicitar Cotización',
    '#newsletter',
    3,
    'activo',
    NOW(),
    NOW()
);

-- Servicio 4: Mantenimiento y Soporte (Orden 4)
INSERT INTO `home_servicios` (
    `icono`,
    `titulo`,
    `resumen`,
    `texto_largo`,
    `cta_texto`,
    `cta_url`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'iconos/iconos-02.png',
    'Mantenimiento y Soporte',
    'Servicios de mantenimiento preventivo y correctivo para mantener sus equipos en óptimas condiciones, con planes de soporte técnico disponibles 24/7.',
    '<ul class="service-features">
        <li><i class="bi bi-check-circle-fill text-primary"></i> Mantenimiento preventivo programado</li>
        <li><i class="bi bi-check-circle-fill text-primary"></i> Reparación y servicio técnico</li>
        <li><i class="bi bi-check-circle-fill text-primary"></i> Soporte remoto y presencial</li>
        <li><i class="bi bi-check-circle-fill text-primary"></i> Disponibilidad de refacciones</li>
    </ul>',
    'Solicitar Cotización',
    '#newsletter',
    4,
    'activo',
    NOW(),
    NOW()
);

-- Servicio 5: Asesoría Especializada (Orden 5)
INSERT INTO `home_servicios` (
    `icono`,
    `titulo`,
    `resumen`,
    `texto_largo`,
    `cta_texto`,
    `cta_url`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'iconos/iconos-03.png',
    'Asesoría Especializada',
    'Consultoría personalizada para ayudarle a seleccionar los equipos más adecuados según sus necesidades educativas y presupuesto disponible.',
    '<ul class="service-features">
        <li><i class="bi bi-check-circle-fill text-primary"></i> Análisis de necesidades educativas</li>
        <li><i class="bi bi-check-circle-fill text-primary"></i> Recomendaciones de equipamiento</li>
        <li><i class="bi bi-check-circle-fill text-primary"></i> Planificación de presupuestos</li>
        <li><i class="bi bi-check-circle-fill text-primary"></i> Estrategias de implementación</li>
    </ul>',
    'Solicitar Cotización',
    '#newsletter',
    5,
    'activo',
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    icono = VALUES(icono),
    titulo = VALUES(titulo),
    resumen = VALUES(resumen),
    texto_largo = VALUES(texto_largo),
    cta_texto = VALUES(cta_texto),
    cta_url = VALUES(cta_url),
    orden = VALUES(orden),
    estado = VALUES(estado),
    updated_at = NOW();

-- Servicio 6: Consultoría en Simulación (Orden 6)
INSERT INTO `home_servicios` (
    `icono`,
    `titulo`,
    `resumen`,
    `texto_largo`,
    `cta_texto`,
    `cta_url`,
    `orden`,
    `estado`,
    `created_at`,
    `updated_at`
) VALUES (
    'iconos/iconos-05.png',
    'Consultoría en Simulación',
    'Asesoría estratégica para el desarrollo de programas de simulación clínica, diseño de currículos y evaluación de competencias médicas.',
    '<ul class="service-features">
        <li><i class="bi bi-check-circle-fill text-primary"></i> Diseño de programas educativos</li>
        <li><i class="bi bi-check-circle-fill text-primary"></i> Desarrollo de escenarios clínicos</li>
        <li><i class="bi bi-check-circle-fill text-primary"></i> Evaluación y acreditación</li>
        <li><i class="bi bi-check-circle-fill text-primary"></i> Mejora continua de procesos</li>
    </ul>',
    'Solicitar Cotización',
    '#newsletter',
    6,
    'activo',
    NOW(),
    NOW()
);

-- ========================================
-- VERIFICACIÓN FINAL
-- ========================================

-- Mostrar servicios migrados
SELECT
    id,
    orden,
    estado,
    titulo,
    icono,
    LEFT(resumen, 50) AS resumen_corto,
    cta_texto,
    created_at
FROM home_servicios
ORDER BY orden ASC;

