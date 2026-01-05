-- ========================================
-- ARAMED Y LABORATORIOS - FASE 2
-- MIGRACIÓN DE PRODUCTOS DESTACADOS DEL HOME
-- ========================================
-- 
-- Este script migra los productos destacados hardcodeados en index.php
-- a la tabla home_productos_destacados para gestión desde el admin
-- 
-- IMPORTANTE: Este script busca los productos por nombre en catalogo_productos.
-- Si los productos no existen, primero debes crearlos en el catálogo.
-- 
-- @package    Aramed
-- @author     IDEAMIA Tech
-- @copyright  2025 Aramed y Laboratorios
-- @created    Enero 2025

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ========================================
-- MIGRACIÓN DE PRODUCTOS DESTACADOS
-- ========================================

-- Verificar si la tabla existe
-- Si no existe, ejecutar primero: database/fase2/01_create_home_tables.sql

-- Limpiar productos destacados existentes en modo manual (opcional - comentar si quieres conservar los existentes)
-- DELETE FROM home_productos_destacados WHERE modo = 'manual';

-- ========================================
-- NOTA: Este script busca productos por nombre en catalogo_productos.
-- Si un producto no se encuentra, se mostrará un mensaje de advertencia.
-- ========================================

-- ========================================
-- VERIFICAR PRODUCTOS EXISTENTES
-- ========================================
-- 
-- Las siguientes consultas te ayudarán a verificar si los productos existen
-- antes de ejecutar la migración. Ejecútalas manualmente si lo deseas:
-- 
-- SELECT id, nombre, codigo FROM catalogo_productos 
-- WHERE nombre LIKE '%Anatomage Table%' OR nombre LIKE '%ANATOMAGE TABLE%';
-- 
-- SELECT id, nombre, codigo FROM catalogo_productos 
-- WHERE nombre LIKE '%Immersive%' OR nombre LIKE '%Echo Healthcare%';
-- 
-- SELECT id, nombre, codigo FROM catalogo_productos 
-- WHERE nombre LIKE '%Lifecast%' OR nombre LIKE '%LIFECAST%';
-- 
-- SELECT id, nombre, codigo FROM catalogo_productos 
-- WHERE nombre LIKE '%ADAM-X%' OR nombre LIKE '%Adam-X%' OR nombre LIKE '%ADAM X%';

-- ========================================
-- NOTA: Ejecutar primero 13_alter_home_productos_destacados_table.sql
-- para agregar los campos adicionales (badge_texto, subtitulo, descripcion, caracteristicas, imagen_url, cta_texto, cta_url)
-- ========================================

-- ========================================
-- INSERTAR PRODUCTOS DESTACADOS
-- ========================================
-- 
-- Este script busca los productos en catalogo_productos y luego
-- inserta los datos en home_productos_destacados.
-- 
-- Si un producto no se encuentra, se mostrará un mensaje de advertencia
-- pero el script continuará con los demás productos.

-- ========================================
-- PASO 1: Buscar IDs de productos
-- ========================================

SET @producto_anatomage_id = NULL;
SET @producto_immersive_id = NULL;
SET @producto_lifecast_id = NULL;
SET @producto_adamx_id = NULL;

-- Buscar ANATOMAGE TABLE
SELECT id INTO @producto_anatomage_id 
FROM catalogo_productos 
WHERE (nombre LIKE '%Anatomage Table%' OR nombre LIKE '%ANATOMAGE TABLE%' OR codigo LIKE '%ANATOMAGE%')
  AND estado = 'activo'
LIMIT 1;

-- Buscar IMMERSIVE INTERACTIVE
SELECT id INTO @producto_immersive_id 
FROM catalogo_productos 
WHERE (nombre LIKE '%Immersive Interactive%' OR nombre LIKE '%IMMERSIVE INTERACTIVE%' OR nombre LIKE '%Immersive%' OR nombre LIKE '%Echo Healthcare%')
  AND estado = 'activo'
LIMIT 1;

-- Buscar LIFECAST
SELECT id INTO @producto_lifecast_id 
FROM catalogo_productos 
WHERE (nombre LIKE '%Lifecast%' OR nombre LIKE '%LIFECAST%' OR nombre LIKE '%Life Cast%')
  AND estado = 'activo'
LIMIT 1;

-- Buscar ADAM-X
SELECT id INTO @producto_adamx_id 
FROM catalogo_productos 
WHERE (nombre LIKE '%ADAM-X%' OR nombre LIKE '%Adam-X%' OR nombre LIKE '%ADAM X%' OR nombre LIKE '%Adam X%' OR nombre LIKE '%ADAMX%')
  AND estado = 'activo'
LIMIT 1;

-- ========================================
-- INSERTAR PRODUCTOS DESTACADOS
-- ========================================
-- 
-- Estos productos destacados son INDEPENDIENTES del catálogo.
-- Tienen su propio título, marca, logo, descripción, etc.

-- Producto 1: ANATOMAGE TABLE (Orden 1)
-- Categoría: Plataforma Educativa
-- Marca: Anatomage
-- Badge: "Más Vendido"
-- Título: "ANATOMAGE TABLE"
-- Subtítulo: "Revoluciona la enseñanza médica con Anatomage Table"
-- Descripción: La Anatomage Table es la plataforma de educación médica más avanzada basada en cuerpos humanos reales digitalizados. Su tecnología de visualización 3D permite a los estudiantes explorar la anatomía, la fisiología y las patologías en tamaño real, con una precisión sumamente realista.
-- Imagen: productos/anatomage-table.jpg
-- Logo proveedor: aliados/3-Anatomage.webp
INSERT INTO `home_productos_destacados` (
    `producto_id`, 
    `titulo`,
    `marca_nombre`,
    `marca_logo_url`,
    `categoria_nombre`,
    `badge_texto`,
    `subtitulo`,
    `descripcion`,
    `caracteristicas`,
    `imagen_url`,
    `cta_texto`,
    `cta_url`,
    `modo`, 
    `orden`, 
    `estado`, 
    `created_at`, 
    `updated_at`
) VALUES (
    NULL,  -- No relacionado con catalogo_productos
    'ANATOMAGE TABLE',
    'Anatomage',
    'aliados/3-Anatomage.webp',
    'Plataforma Educativa',
    'Más Vendido',
    'Revoluciona la enseñanza médica con Anatomage Table',
    'La Anatomage Table es la plataforma de educación médica más avanzada basada en cuerpos humanos reales digitalizados. Su tecnología de visualización 3D permite a los estudiantes explorar la anatomía, la fisiología y las patologías en tamaño real, con una precisión sumamente realista.',
    'Visualización 3D de cuerpos humanos reales digitalizados\nHerramientas interactivas de disección virtual\nSimulaciones clínicas avanzadas\nVisor DICOM integrado\nAprendizaje práctico sin laboratorios tradicionales',
    'productos/anatomage-table.jpg',
    'Solicitar Cotización',
    '#newsletter',
    'manual',
    1,
    'activo',
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE 
    titulo = VALUES(titulo),
    marca_nombre = VALUES(marca_nombre),
    marca_logo_url = VALUES(marca_logo_url),
    categoria_nombre = VALUES(categoria_nombre),
    badge_texto = VALUES(badge_texto),
    subtitulo = VALUES(subtitulo),
    descripcion = VALUES(descripcion),
    caracteristicas = VALUES(caracteristicas),
    imagen_url = VALUES(imagen_url),
    cta_texto = VALUES(cta_texto),
    cta_url = VALUES(cta_url),
    orden = VALUES(orden),
    estado = VALUES(estado),
    updated_at = NOW();

-- Producto 2: IMMERSIVE INTERACTIVE (Orden 2)
-- Categoría: Realidad Inmersiva
-- Marca: Immersive Healthcare
-- Badge: "Inmersivo"
-- Título: "IMMERSIVE INTERACTIVE"
-- Subtítulo: "Transforma la educación médica con entornos inmersivos y realistas"
-- Descripción: El sistema Immersive Interactive de Echo Healthcare convierte cualquier aula o espacio en un entorno virtual envolvente, multisensorial e interactivo, diseñado para fomentar el aprendizaje activo en estudiantes de medicina.
-- Imagen: productos/immersive-echo.jpg
-- Logo proveedor: aliados/14-immersive.webp
INSERT INTO `home_productos_destacados` (
    `producto_id`, 
    `titulo`,
    `marca_nombre`,
    `marca_logo_url`,
    `categoria_nombre`,
    `badge_texto`,
    `subtitulo`,
    `descripcion`,
    `caracteristicas`,
    `imagen_url`,
    `cta_texto`,
    `cta_url`,
    `modo`, 
    `orden`, 
    `estado`, 
    `created_at`, 
    `updated_at`
) VALUES (
    NULL,  -- No relacionado con catalogo_productos
    'IMMERSIVE INTERACTIVE',
    'Immersive Healthcare',
    'aliados/14-immersive.webp',
    'Realidad Inmersiva',
    'Inmersivo',
    'Transforma la educación médica con entornos inmersivos y realistas',
    'El sistema Immersive Interactive de Echo Healthcare convierte cualquier aula o espacio en un entorno virtual envolvente, multisensorial e interactivo, diseñado para fomentar el aprendizaje activo en estudiantes de medicina.',
    'Entorno virtual envolvente y multisensorial\nTecnología sin gafas ni auriculares\nEscenarios clínicos realistas\nEstimula la toma de decisiones y colaboración\nMejora la retención del conocimiento',
    'productos/immersive-echo.jpg',
    'Solicitar Cotización',
    '#newsletter',
    'manual',
    2,
    'activo',
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE 
    titulo = VALUES(titulo),
    marca_nombre = VALUES(marca_nombre),
    marca_logo_url = VALUES(marca_logo_url),
    categoria_nombre = VALUES(categoria_nombre),
    badge_texto = VALUES(badge_texto),
    subtitulo = VALUES(subtitulo),
    descripcion = VALUES(descripcion),
    caracteristicas = VALUES(caracteristicas),
    imagen_url = VALUES(imagen_url),
    cta_texto = VALUES(cta_texto),
    cta_url = VALUES(cta_url),
    orden = VALUES(orden),
    estado = VALUES(estado),
    updated_at = NOW();

-- Producto 3: LIFECAST (Orden 3)
-- Categoría: Simulación Pediátrica
-- Marca: Lifecast
-- Badge: "Pediátrico"
-- Título: "LIFECAST"
-- Subtítulo: "Realismo y precisión en simulación pediátrica"
-- Descripción: Diseñados para ofrecer una experiencia de capacitación médica inigualable, los maniquíes Lifecast para niños pequeños y niños brindan un nivel de realismo anatómico y funcional que transforma la enseñanza y la práctica clínica.
-- Imagen: productos/lifecast.jpg
-- Logo proveedor: aliados/18-Lifecast.webp
INSERT INTO `home_productos_destacados` (
    `producto_id`, 
    `titulo`,
    `marca_nombre`,
    `marca_logo_url`,
    `categoria_nombre`,
    `badge_texto`,
    `subtitulo`,
    `descripcion`,
    `caracteristicas`,
    `imagen_url`,
    `cta_texto`,
    `cta_url`,
    `modo`, 
    `orden`, 
    `estado`, 
    `created_at`, 
    `updated_at`
) VALUES (
    NULL,  -- No relacionado con catalogo_productos
    'LIFECAST',
    'Lifecast',
    'aliados/18-Lifecast.webp',
    'Simulación Pediátrica',
    'Pediátrico',
    'Realismo y precisión en simulación pediátrica',
    'Diseñados para ofrecer una experiencia de capacitación médica inigualable, los maniquíes Lifecast para niños pequeños y niños brindan un nivel de realismo anatómico y funcional que transforma la enseñanza y la práctica clínica.',
    'Realismo anatómico y funcional superior\nAhogamiento pulmonar húmedo y seco\nHemorragia torácica y sangría\nEfectos de vómito realistas\nEscenarios de rescate acuático\nEmergencias pediátricas complejas',
    'productos/lifecast.jpg',
    'Solicitar Cotización',
    '#newsletter',
    'manual',
    3,
    'activo',
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE 
    titulo = VALUES(titulo),
    marca_nombre = VALUES(marca_nombre),
    marca_logo_url = VALUES(marca_logo_url),
    categoria_nombre = VALUES(categoria_nombre),
    badge_texto = VALUES(badge_texto),
    subtitulo = VALUES(subtitulo),
    descripcion = VALUES(descripcion),
    caracteristicas = VALUES(caracteristicas),
    imagen_url = VALUES(imagen_url),
    cta_texto = VALUES(cta_texto),
    cta_url = VALUES(cta_url),
    orden = VALUES(orden),
    estado = VALUES(estado),
    updated_at = NOW();

-- Producto 4: ADAM-X (Orden 4)
-- Categoría: Simulación Clínica
-- Marca: Medical X
-- Badge: "Adulto"
-- Título: "ADAM-X"
-- Subtítulo: "Simulación clínica avanzada con el realismo total de ADAM-X"
-- Descripción: ADAM-X Xtreme es un simulador de paciente adulto de alta fidelidad que reproduce fielmente la anatomía y fisiología humana. Destaca por su realismo extremo, con parpadeo, sudoración, secreciones, respiración espontánea y pulsos sincronizados.
-- Imagen: productos/adam-x.jpg
-- Logo proveedor: aliados/13-Medical X.webp
INSERT INTO `home_productos_destacados` (
    `producto_id`, 
    `titulo`,
    `marca_nombre`,
    `marca_logo_url`,
    `categoria_nombre`,
    `badge_texto`,
    `subtitulo`,
    `descripcion`,
    `caracteristicas`,
    `imagen_url`,
    `cta_texto`,
    `cta_url`,
    `modo`, 
    `orden`, 
    `estado`, 
    `created_at`, 
    `updated_at`
) VALUES (
    NULL,  -- No relacionado con catalogo_productos
    'ADAM-X',
    'Medical X',
    'aliados/13-Medical X.webp',
    'Simulación Clínica',
    'Adulto',
    'Simulación clínica avanzada con el realismo total de ADAM-X',
    'ADAM-X Xtreme es un simulador de paciente adulto de alta fidelidad que reproduce fielmente la anatomía y fisiología humana. Destaca por su realismo extremo, con parpadeo, sudoración, secreciones, respiración espontánea y pulsos sincronizados.',
    'Realismo extremo con parpadeo y sudoración\nSecreciones y respiración espontánea\nPulsos sincronizados y realistas\nControl táctil Command-X\nEscenarios clínicos personalizados\nEntrenamiento integral en vía aérea, RCP y ventilación',
    'productos/adam-x.jpg',
    'Solicitar Cotización',
    '#newsletter',
    'manual',
    4,
    'activo',
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE 
    titulo = VALUES(titulo),
    marca_nombre = VALUES(marca_nombre),
    marca_logo_url = VALUES(marca_logo_url),
    categoria_nombre = VALUES(categoria_nombre),
    badge_texto = VALUES(badge_texto),
    subtitulo = VALUES(subtitulo),
    descripcion = VALUES(descripcion),
    caracteristicas = VALUES(caracteristicas),
    imagen_url = VALUES(imagen_url),
    cta_texto = VALUES(cta_texto),
    cta_url = VALUES(cta_url),
    orden = VALUES(orden),
    estado = VALUES(estado),
    updated_at = NOW();

-- ========================================
-- VERIFICACIÓN FINAL
-- ========================================

-- Mostrar productos destacados migrados
SELECT 
    hpd.id,
    hpd.orden,
    hpd.estado,
    hpd.titulo,
    hpd.marca_nombre,
    hpd.marca_logo_url,
    hpd.categoria_nombre,
    hpd.badge_texto,
    hpd.subtitulo,
    hpd.imagen_url,
    hpd.cta_texto,
    hpd.cta_url
FROM home_productos_destacados hpd
WHERE hpd.modo = 'manual'
ORDER BY hpd.orden ASC;

-- ========================================
-- NOTAS IMPORTANTES
-- ========================================
-- 
-- IMPORTANTE: Estos productos destacados son INDEPENDIENTES del catálogo.
-- No están relacionados con catalogo_productos. Tienen su propio contenido:
-- - Título
-- - Marca/Proveedor (nombre y logo)
-- - Categoría
-- - Badge
-- - Subtítulo
-- - Descripción
-- - Características (bullets)
-- - Imagen
-- - CTA (botón)
-- 
-- Los productos migrados son:
-- 1. ANATOMAGE TABLE (Plataforma Educativa - Anatomage)
-- 2. IMMERSIVE INTERACTIVE (Realidad Inmersiva - Immersive Healthcare)
-- 3. LIFECAST (Simulación Pediátrica - Lifecast)
-- 4. ADAM-X (Simulación Clínica - Medical X)
-- 
-- Una vez migrados, estos productos se mostrarán dinámicamente desde la BD
-- y podrás gestionarlos desde: admin/home/productos-destacados.php

