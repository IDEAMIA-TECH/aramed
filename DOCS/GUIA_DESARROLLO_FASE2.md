# 🚀 GUÍA DE DESARROLLO - FASE 2
## Panel Administrativo - Aramed y Laboratorios

**Documento híbrido:** Plan + Análisis + Checklist de implementación  
**Versión:** 1.0  
**Fecha:** Enero 2025  
**Estado:** Listo para desarrollo

---

## 📋 ÍNDICE

1. [Resumen Ejecutivo](#1-resumen-ejecutivo)
2. [Estado Actual vs Plan](#2-estado-actual-vs-plan)
3. [Orden de Implementación Recomendado](#3-orden-de-implementación-recomendado)
4. [Módulos Detallados con Checklist](#4-módulos-detallados-con-checklist)
5. [Arquitectura y Estructura](#5-arquitectura-y-estructura)
6. [Base de Datos](#6-base-de-datos)
7. [Seguridad y Mejores Prácticas](#7-seguridad-y-mejores-prácticas)
8. [Checklist Final de Entrega](#8-checklist-final-de-entrega)

---

## 1. RESUMEN EJECUTIVO

### Objetivo
Desarrollar el **Panel Administrativo completo** para gestionar todo el contenido del sitio sin requerir programación.

### Estado Actual
- ✅ **Fase 1:** Completada y en producción
- 🔄 **Fase 2:** ~40% de infraestructura base existe
- 📊 **13 módulos planificados:** 1 completo, 5 parciales, 7 sin implementar

### Horas Estimadas
**210 horas** según cotización original

### Prioridades
1. **ALTA:** Catálogo CRUD, Gestor de Home, Contacto Admin, Cotizaciones Avanzado
2. **MEDIA:** Proyectos, SEO Admin, Dashboard Avanzado, Usuarios & Roles
3. **BAJA:** Apariencia & Módulos, Analytics Dashboard, Newsletter Avanzado

---

## 2. ESTADO ACTUAL VS PLAN

### Módulos por Estado

| Módulo | Estado | % | Archivos Existentes | Archivos a Crear |
|--------|--------|---|---------------------|------------------|
| **Dashboard** | 🟡 Parcial | 60% | `admin/index.php` | Gráficas, alertas |
| **Gestor de Home** | 🔴 No existe | 0% | `index.php` (frontend) | Todo el admin |
| **Catálogo Admin** | 🔴 No existe | 0% | `catalogo.php`, `producto.php` | Todo el admin CRUD |
| **Proyectos** | 🔴 No existe | 0% | Ninguno | Módulo completo |
| **Blog** | 🟢 Completo | 95% | `admin/blog/*` | Programación publicación |
| **Cotizaciones** | 🟡 Parcial | 50% | `admin/newsletter-subscriptions.php` | Avanzado |
| **Contacto Admin** | 🔴 No existe | 0% | `contact_messages` (BD) | Todo el admin |
| **Newsletter** | 🟡 Parcial | 40% | `admin/newsletter-simple.php` | Import/Export |
| **SEO Admin** | 🔴 No existe | 0% | Meta tags básicos | Todo el admin |
| **Analytics** | 🟡 Parcial | 30% | `includes/analytics.php` | Dashboard admin |
| **Apariencia** | 🔴 No existe | 0% | Home estático | Todo el admin |
| **Usuarios & Roles** | 🟡 Parcial | 60% | `admin/usuarios.php` | RBAC granular |
| **Configuración** | 🟡 Parcial | 50% | `includes/config.php` | Admin panel |

---

## 3. ORDEN DE IMPLEMENTACIÓN RECOMENDADO

### Semana 1-2: Fundamentos
1. ✅ **Dashboard Avanzado** (completar gráficas y alertas)
2. ✅ **Usuarios & Roles** (completar RBAC)
3. ✅ **Configuración General** (admin panel)

### Semana 3-4: Contenido Crítico
4. ✅ **Catálogo CRUD Admin** (PRIORIDAD #1)
5. ✅ **Gestor de Home** (banners, servicios, marcas)

### Semana 5-6: Nuevos Módulos
6. ✅ **Proyectos** (módulo completo)
7. ✅ **Blog** (completar programación)

### Semana 7-8: Gestión de Clientes
8. ✅ **Contacto Admin** (bandeja de mensajes)
9. ✅ **Cotizaciones Avanzado** (completar funcionalidades)

### Semana 9-10: Optimización
10. ✅ **SEO & Metadatos Admin**
11. ✅ **Newsletter Avanzado** (import/export)
12. ✅ **Analytics Dashboard**

### Semana 11-12: Finalización
13. ✅ **Apariencia & Módulos** (toggles, drag & drop)
14. ✅ **QA y Testing**
15. ✅ **Documentación y Capacitación**

---

## 4. MÓDULOS DETALLADOS CON CHECKLIST

### 4.1. DASHBOARD AVANZADO

**Estado:** 🟡 60% implementado  
**Archivo base:** `admin/index.php`

#### ✅ Ya Implementado:
- [x] Dashboard básico con estadísticas
- [x] KPIs de blog (artículos, comentarios, vistas)
- [x] KPIs de newsletter/cotizaciones
- [x] Lista de artículos recientes
- [x] Lista de comentarios recientes

#### ⚠️ Falta Implementar:
- [ ] Gráficas de tendencias (Chart.js o similar)
  - [ ] Tendencia de cotizaciones por mes
  - [ ] Tendencia de suscriptores
- [ ] KPIs adicionales:
  - [ ] Productos publicados
  - [ ] Mensajes de contacto por estado
  - [ ] Cotizaciones: hoy/semana/mes/acumulado
- [ ] Alertas automáticas:
  - [ ] Mensajes de contacto "Abiertos" con más de X días
  - [ ] Cotizaciones "Nuevas" sin asignar
  - [ ] Productos sin categoría o sin ficha técnica
- [ ] Listas rápidas adicionales:
  - [ ] Últimas cotizaciones recibidas
  - [ ] Últimos contactos

#### 📁 Archivos a Modificar:
- `admin/index.php` (agregar gráficas y alertas)

#### 📁 Archivos a Crear:
- `admin/includes/dashboard_charts.php` (helper para gráficas)
- `admin/includes/dashboard_alerts.php` (helper para alertas)

#### 🔧 Tecnologías:
- Chart.js o ApexCharts para gráficas
- AJAX para actualización en tiempo real (opcional)

---

### 4.2. GESTOR DE INICIO (HOME)

**Estado:** 🔴 0% del admin (100% frontend)  
**Archivo base:** `index.php` (frontend)

#### ⚠️ Todo por Implementar:

#### 2.1. Banners / Hero
- [ ] Crear tabla `home_banners`:
  ```sql
  CREATE TABLE `home_banners` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `titulo` VARCHAR(255),
    `subtitulo` TEXT,
    `imagen` VARCHAR(255),
    `video_url` VARCHAR(500) NULL,
    `cta_texto` VARCHAR(100),
    `cta_url` VARCHAR(500),
    `orden` INT DEFAULT 0,
    `estado` ENUM('publicado', 'borrador') DEFAULT 'borrador',
    `fecha_inicio` DATETIME NULL,
    `fecha_fin` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  );
  ```
- [ ] Crear `admin/home/banners.php` (CRUD)
- [ ] Modificar `index.php` para leer desde BD
- [ ] Upload de imágenes/videos

#### 2.2. Productos Destacados
- [ ] Crear tabla `home_productos_destacados`:
  ```sql
  CREATE TABLE `home_productos_destacados` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `producto_id` INT UNSIGNED,
    `orden` INT DEFAULT 0,
    `modo_seleccion` ENUM('manual', 'auto') DEFAULT 'manual',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`producto_id`) REFERENCES `catalogo_productos`(`id`)
  );
  ```
- [ ] Crear `admin/home/productos-destacados.php`
- [ ] Modificar `index.php` para leer desde BD

#### 2.3. Marcas
- [ ] **YA EXISTE:** Tabla `catalogo_marcas`
- [ ] Crear `admin/catalogo/marcas.php` (CRUD)
- [ ] Modificar `index.php` para leer desde BD

#### 2.4. Servicios
- [ ] Crear tabla `home_servicios`:
  ```sql
  CREATE TABLE `home_servicios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `icono` VARCHAR(255),
    `titulo` VARCHAR(255),
    `resumen` TEXT,
    `texto_largo` TEXT,
    `cta_texto` VARCHAR(100),
    `cta_url` VARCHAR(500),
    `orden` INT DEFAULT 0,
    `estado` ENUM('activo', 'inactivo') DEFAULT 'activo',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  );
  ```
- [ ] Crear `admin/home/servicios.php` (CRUD)
- [ ] Modificar `index.php` para leer desde BD

#### 2.5. Misión y Visión
- [ ] Crear tabla `home_mision_vision`:
  ```sql
  CREATE TABLE `home_mision_vision` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tipo` ENUM('mision', 'vision'),
    `titulo` VARCHAR(255),
    `contenido` TEXT,
    `imagen` VARCHAR(255) NULL,
    `estado` ENUM('activo', 'inactivo') DEFAULT 'activo',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  );
  ```
- [ ] Crear `admin/home/mision-vision.php` (editor WYSIWYG)
- [ ] Modificar `index.php` para leer desde BD

#### 2.6. Categorías Destacadas
- [ ] Crear tabla `home_categorias_destacadas`:
  ```sql
  CREATE TABLE `home_categorias_destacadas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `categoria_id` INT UNSIGNED,
    `orden` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`categoria_id`) REFERENCES `catalogo_categorias`(`id`)
  );
  ```
- [ ] Crear `admin/home/categorias-destacadas.php`
- [ ] Modificar `index.php` para leer desde BD

#### 📁 Estructura de Archivos:
```
admin/
  home/
    index.php (dashboard del gestor de home)
    banners.php
    productos-destacados.php
    servicios.php
    mision-vision.php
    categorias-destacadas.php
```

#### ⚠️ Puntos de Atención:
- Mantener compatibilidad con contenido hardcodeado actual
- Migrar datos existentes si es necesario
- Validar que todas las secciones del Home sean editables

---

### 4.3. CATÁLOGO DE PRODUCTOS (ADMIN CRUD)

**Estado:** 🔴 0% del admin (100% frontend)  
**Archivos base:** `catalogo.php`, `producto.php` (frontend)  
**PRIORIDAD:** #1 - CRÍTICO

#### ✅ Ya Existe:
- [x] Frontend completo (`catalogo.php`, `producto.php`)
- [x] Base de datos (`catalogo_marcas`, `catalogo_categorias`, `catalogo_productos`, etc.)
- [x] 882 productos migrados
- [x] Estructura de imágenes y documentos

#### ⚠️ Todo por Implementar:

#### 3.1. Categorías y Subcategorías
- [ ] Crear `admin/catalogo/categorias.php`:
  - [ ] Listado con árbol jerárquico
  - [ ] Crear categoría (nombre, slug, descripción, imagen, padre)
  - [ ] Editar categoría
  - [ ] Eliminar categoría (con validación de productos asociados)
  - [ ] Ordenar categorías (drag & drop)
  - [ ] SEO: meta title, meta description
  - [ ] Estado: activo/inactivo

#### 3.2. Marcas
- [ ] Crear `admin/catalogo/marcas.php`:
  - [ ] Listado de marcas
  - [ ] Crear marca (nombre, slug, logo, descripción, website)
  - [ ] Editar marca
  - [ ] Eliminar marca (con validación)
  - [ ] Ordenar marcas
  - [ ] Upload de logos

#### 3.3. Productos (CRUD Avanzado)
- [ ] Crear `admin/catalogo/productos.php`:
  - [ ] Listado con filtros (marca, categoría, estado, búsqueda)
  - [ ] Paginación
  - [ ] Búsqueda por nombre, SKU, texto
- [ ] Crear `admin/catalogo/productos/create.php`:
  - [ ] Datos básicos: nombre, slug, SKU, marca, categoría, tags
  - [ ] Contenido: descripción corta, descripción larga (WYSIWYG)
  - [ ] Galería de imágenes (múltiple upload, WebP + JPG)
  - [ ] Videos embebidos (URL YouTube/Vimeo)
  - [ ] Documentos (PDF): fichas técnicas, manuales, brochures
  - [ ] Atributos técnicos (pares clave/valor dinámicos)
  - [ ] Estado: borrador/publicado/oculto
  - [ ] Flag "destacado"
  - [ ] SEO: meta_title, meta_description, canonical, OG_image
- [ ] Crear `admin/catalogo/productos/edit.php`:
  - [ ] Mismo formulario que create, pero con datos precargados
  - [ ] Gestión de imágenes existentes (eliminar, reordenar)
  - [ ] Gestión de documentos existentes
- [ ] Crear `admin/catalogo/productos/view.php`:
  - [ ] Vista detallada del producto
  - [ ] Historial de cambios (opcional)
- [ ] Funcionalidades adicionales:
  - [ ] Productos relacionados (manual o por categoría/tags)
  - [ ] Exportación CSV/Excel
  - [ ] Duplicar producto
  - [ ] Bulk actions (cambiar estado, asignar categoría, etc.)

#### 📁 Estructura de Archivos:
```
admin/
  catalogo/
    index.php (dashboard del catálogo)
    categorias.php
    marcas.php
    productos/
      index.php (listado)
      create.php
      edit.php
      view.php
      upload-image.php
      upload-document.php
```

#### 🔧 Funcionalidades Técnicas:
- **Upload de imágenes:** 
  - Soporte WebP + JPG
  - Redimensionamiento automático
  - Optimización de peso
  - Thumbnails
- **Upload de documentos:**
  - Validar tipo (solo PDF)
  - Validar tamaño máximo
  - Renombrar archivos (slug del producto + tipo)
- **Editor WYSIWYG:**
  - TinyMCE o CKEditor
  - Upload de imágenes desde editor
- **Búsqueda:**
  - Full-text search en MySQL
  - Filtros combinados

#### ⚠️ Puntos de Atención:
- **Catálogo oculto:** Actualmente comentado en `navbar.php` línea 30
- **Activar catálogo:** Descomentar cuando admin CRUD esté listo
- **Migración de datos:** Ya existen 882 productos, solo falta admin para gestionarlos
- **Rutas de archivos:** Verificar que coincidan con estructura actual

---

### 4.4. PROYECTOS

**Estado:** 🔴 0% (módulo completo nuevo)

#### ⚠️ Todo por Implementar:

#### 4.1. Base de Datos
- [ ] Crear tabla `proyectos`:
  ```sql
  CREATE TABLE `proyectos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `titulo` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) UNIQUE NOT NULL,
    `sector` VARCHAR(100),
    `categoria` VARCHAR(100),
    `ano` YEAR,
    `pais` VARCHAR(100),
    `ubicacion` VARCHAR(255),
    `descripcion_corta` TEXT,
    `descripcion_larga` TEXT,
    `imagen_principal` VARCHAR(255),
    `meta_titulo` VARCHAR(255),
    `meta_descripcion` TEXT,
    `estado` ENUM('borrador', 'publicado') DEFAULT 'borrador',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_estado` (`estado`),
    INDEX `idx_ano` (`ano`),
    INDEX `idx_sector` (`sector`)
  );
  ```
- [ ] Crear tabla `proyecto_imagenes`:
  ```sql
  CREATE TABLE `proyecto_imagenes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `proyecto_id` INT NOT NULL,
    `imagen_url` VARCHAR(255) NOT NULL,
    `titulo` VARCHAR(255),
    `descripcion` TEXT,
    `orden` INT DEFAULT 0,
    `es_principal` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`) ON DELETE CASCADE
  );
  ```
- [ ] Crear tabla `proyecto_videos`:
  ```sql
  CREATE TABLE `proyecto_videos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `proyecto_id` INT NOT NULL,
    `video_url` VARCHAR(500) NOT NULL,
    `tipo` ENUM('youtube', 'vimeo', 'otro') DEFAULT 'youtube',
    `titulo` VARCHAR(255),
    `orden` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`) ON DELETE CASCADE
  );
  ```
- [ ] Crear tabla `proyecto_documentos`:
  ```sql
  CREATE TABLE `proyecto_documentos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `proyecto_id` INT NOT NULL,
    `documento_url` VARCHAR(255) NOT NULL,
    `nombre` VARCHAR(255) NOT NULL,
    `tipo` ENUM('pdf', 'doc', 'otro') DEFAULT 'pdf',
    `tamanio` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`) ON DELETE CASCADE
  );
  ```

#### 4.2. Admin CRUD
- [ ] Crear `admin/proyectos/index.php`:
  - [ ] Listado con filtros (año, categoría, sector, estado)
  - [ ] Búsqueda
  - [ ] Paginación
- [ ] Crear `admin/proyectos/create.php`:
  - [ ] Formulario completo
  - [ ] Upload de imagen principal
  - [ ] Gestión de galería de imágenes
  - [ ] Gestión de videos embebidos
  - [ ] Upload de documentos
  - [ ] SEO: meta title, description
- [ ] Crear `admin/proyectos/edit.php`:
  - [ ] Mismo formulario que create
  - [ ] Gestión de medios existentes
- [ ] Crear `admin/proyectos/view.php`:
  - [ ] Vista detallada

#### 4.3. Frontend
- [ ] Crear `proyectos.php` (listado público):
  - [ ] Grid de proyectos
  - [ ] Filtros por año, categoría, sector
  - [ ] Paginación
- [ ] Crear `proyecto.php` (detalle público):
  - [ ] Información completa
  - [ ] Galería de imágenes
  - [ ] Videos embebidos
  - [ ] Descarga de documentos
  - [ ] Proyectos relacionados

#### 📁 Estructura de Archivos:
```
admin/
  proyectos/
    index.php
    create.php
    edit.php
    view.php
    upload-image.php
    upload-document.php

public_html/
  proyectos.php
  proyecto.php
```

#### ⚠️ Puntos de Atención:
- Crear estructura completa desde cero
- Reutilizar patrones del blog para consistencia
- Considerar integración con catálogo (marcas asociadas)

---

### 4.5. BLOG (Completar)

**Estado:** 🟢 95% implementado  
**Archivos base:** `admin/blog/*`

#### ✅ Ya Implementado:
- [x] CRUD completo de artículos
- [x] CRUD de categorías
- [x] Gestión de comentarios
- [x] Editor de imágenes
- [x] Upload de imágenes
- [x] Estados: borrador, publicado, archivado
- [x] SEO básico

#### ⚠️ Falta Implementar:
- [ ] Programación de publicación (fecha/hora futura):
  - [ ] Agregar campo `fecha_programada` a tabla `blog_articulos`
  - [ ] Modificar `admin/blog/create.php` y `edit.php`
  - [ ] Crear cron job o verificación en `index.php` para publicar automáticamente
  - [ ] Estado "programado" en listado

#### 📁 Archivos a Modificar:
- `admin/blog/create.php`
- `admin/blog/edit.php`
- `admin/blog/index.php` (agregar filtro "programados")
- `database/` (script de alteración de tabla)

#### 🔧 Implementación Técnica:
```sql
ALTER TABLE `blog_articulos` 
ADD COLUMN `fecha_programada` DATETIME NULL AFTER `fecha_publicacion`;
```

Crear cron job o verificación en cada carga de página:
```php
// En includes/functions.php o similar
function publicarArticulosProgramados() {
    $pdo = getDB();
    $sql = "UPDATE blog_articulos 
            SET estado = 'publicado', fecha_publicacion = NOW() 
            WHERE estado = 'programado' 
            AND fecha_programada <= NOW()";
    $pdo->exec($sql);
}
```

---

### 4.6. COTIZACIONES AVANZADO

**Estado:** 🟡 50% implementado  
**Archivo base:** `admin/newsletter-subscriptions.php`  
**⚠️ IMPORTANTE:** Actualmente usa `newsletter_subscriptions` como cotizador

#### ✅ Ya Implementado:
- [x] Frontend funcional (`includes/newsletter_handler.php`)
- [x] Base de datos (`newsletter_subscriptions`)
- [x] Admin básico (`admin/newsletter-subscriptions.php`):
  - [x] Listado con filtros
  - [x] Ver detalles
  - [x] Cambiar estado
  - [x] Eliminar

#### ⚠️ Falta Implementar:

#### 6.1. Nueva Estructura (Recomendado)
- [ ] Crear tabla `cotizaciones`:
  ```sql
  CREATE TABLE `cotizaciones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `folio` VARCHAR(50) UNIQUE NOT NULL,
    `nombre` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `telefono` VARCHAR(50) NOT NULL,
    `institucion` VARCHAR(255) NOT NULL,
    `tipo_institucion` VARCHAR(100) NOT NULL,
    `estado` VARCHAR(100) NOT NULL,
    `ciudad` VARCHAR(150) NOT NULL,
    `estado_cotizacion` ENUM('nueva', 'en_seguimiento', 'cotizada', 'enviada', 'cerrada_ganada', 'cerrada_perdida') DEFAULT 'nueva',
    `asignado_a` INT NULL,
    `notas_internas` TEXT,
    `pdf_propuesta` VARCHAR(255) NULL,
    `ip_address` VARCHAR(45),
    `user_agent` VARCHAR(500),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`asignado_a`) REFERENCES `admin_usuarios`(`id`),
    INDEX `idx_estado` (`estado_cotizacion`),
    INDEX `idx_folio` (`folio`)
  );
  ```
- [ ] Crear tabla `cotizacion_items`:
  ```sql
  CREATE TABLE `cotizacion_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `cotizacion_id` INT NOT NULL,
    `producto_id` INT UNSIGNED,
    `producto_nombre` VARCHAR(255),
    `cantidad` INT DEFAULT 1,
    `precio_unitario` DECIMAL(10,2),
    `notas` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`cotizacion_id`) REFERENCES `cotizaciones`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`producto_id`) REFERENCES `catalogo_productos`(`id`)
  );
  ```
- [ ] Crear tabla `cotizacion_auditoria`:
  ```sql
  CREATE TABLE `cotizacion_auditoria` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `cotizacion_id` INT NOT NULL,
    `usuario_id` INT NOT NULL,
    `accion` VARCHAR(100),
    `campo` VARCHAR(100),
    `valor_anterior` TEXT,
    `valor_nuevo` TEXT,
    `notas` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`cotizacion_id`) REFERENCES `cotizaciones`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`usuario_id`) REFERENCES `admin_usuarios`(`id`)
  );
  ```

#### 6.2. Migración de Datos
- [ ] Script de migración de `newsletter_subscriptions` a `cotizaciones`
- [ ] Validar integridad de datos

#### 6.3. Funcionalidades Avanzadas
- [ ] Asignación a ejecutivo:
  - [ ] Dropdown de usuarios en detalle
  - [ ] Filtro por ejecutivo asignado
- [ ] Notas internas:
  - [ ] Editor de notas en detalle
  - [ ] Historial de notas
- [ ] Adjuntar PDF:
  - [ ] Upload de PDF de propuesta
  - [ ] Descarga de PDF
- [ ] Exportación:
  - [ ] Exportar a CSV
  - [ ] Exportar a Excel
  - [ ] Filtros aplicables a exportación
- [ ] Auditoría:
  - [ ] Log automático de cambios
  - [ ] Vista de historial en detalle

#### 6.4. Frontend (Mejora)
- [ ] Carrito de productos en formulario de cotización
- [ ] Selección de productos desde catálogo
- [ ] Cálculo automático de totales (opcional)

#### 📁 Archivos a Crear:
```
admin/
  cotizaciones/
    index.php (listado avanzado)
    view.php (detalle con todas las funcionalidades)
    export.php (exportación CSV/Excel)
    migrar.php (script de migración)
```

#### ⚠️ Puntos de Atención:
- **Migración:** No perder datos existentes
- **Compatibilidad:** Mantener `newsletter_subscriptions` para newsletter real
- **Folio único:** Generar automáticamente (ej: COT-2025-001)

---

### 4.7. CONTACTO ADMIN

**Estado:** 🔴 0% del admin (100% frontend)  
**Archivo base:** `contact_messages` (tabla existe)

#### ✅ Ya Existe:
- [x] Frontend funcional (`includes/contact_handler.php`)
- [x] Base de datos (`contact_messages`)

#### ⚠️ Todo por Implementar:
- [ ] Crear `admin/contacto/index.php`:
  - [ ] Listado de mensajes
  - [ ] Filtros: estado, motivo (asunto), fecha
  - [ ] Búsqueda
  - [ ] Paginación
- [ ] Crear `admin/contacto/view.php`:
  - [ ] Detalle completo del mensaje
  - [ ] Datos de contacto
  - [ ] Cambiar estado (nuevo / en proceso / resuelto / cerrado)
  - [ ] Asignar a responsable (dropdown de usuarios)
  - [ ] Respuestas rápidas (plantillas)
  - [ ] Historial de cambios
- [ ] Funcionalidades adicionales:
  - [ ] Marcar como leído/no leído
  - [ ] Exportación CSV
  - [ ] Estadísticas (mensajes por estado, por mes)

#### 📁 Archivos a Crear:
```
admin/
  contacto/
    index.php
    view.php
```

#### ⚠️ Puntos de Atención:
- Tabla `contact_messages` ya existe con estructura correcta
- Agregar campo `assigned_to` si no existe
- Considerar notificaciones por email al asignar

---

### 4.8. NEWSLETTER AVANZADO

**Estado:** 🟡 40% implementado  
**Archivo base:** `admin/newsletter-simple.php`

#### ✅ Ya Implementado:
- [x] Listado de suscriptores
- [x] Filtros básicos

#### ⚠️ Falta Implementar:
- [ ] Importación CSV:
  - [ ] Formulario de upload
  - [ ] Validación de formato
  - [ ] Mapeo de columnas
  - [ ] Procesamiento en lote
- [ ] Exportación CSV:
  - [ ] Botón de exportar
  - [ ] Aplicar filtros a exportación
  - [ ] Formato estándar
- [ ] Plantillas HTML:
  - [ ] CRUD de plantillas
  - [ ] Editor WYSIWYG
  - [ ] Preview de plantilla
- [ ] Configuración avanzada:
  - [ ] Campos obligatorios configurables
  - [ ] Textos legales editables
  - [ ] Integración con Mailchimp/SendGrid (futuro)

#### 📁 Archivos a Modificar:
- `admin/newsletter-simple.php` (agregar funcionalidades)

#### 📁 Archivos a Crear:
```
admin/
  newsletter/
    import.php
    export.php
    plantillas.php
    config.php
```

---

### 4.9. SEO & METADATOS ADMIN

**Estado:** 🔴 0% del admin (100% frontend básico)

#### ✅ Ya Existe:
- [x] Meta tags básicos en todas las páginas
- [x] Schema.org en `index.php`
- [x] `sitemap.xml` estático
- [x] `robots.txt` estático

#### ⚠️ Todo por Implementar:

#### 9.1. Base de Datos
- [ ] Crear tabla `seo_config`:
  ```sql
  CREATE TABLE `seo_config` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tipo` ENUM('global', 'pagina') NOT NULL,
    `pagina` VARCHAR(100) NULL,
    `titulo_prefijo` VARCHAR(100),
    `titulo_sufijo` VARCHAR(100),
    `meta_descripcion_default` TEXT,
    `favicon` VARCHAR(255),
    `og_image` VARCHAR(255),
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  );
  ```
- [ ] Crear tabla `redirects`:
  ```sql
  CREATE TABLE `redirects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `url_antigua` VARCHAR(500) NOT NULL,
    `url_nueva` VARCHAR(500) NOT NULL,
    `tipo` ENUM('301', '302') DEFAULT '301',
    `estado` ENUM('activo', 'inactivo') DEFAULT 'activo',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `url_antigua` (`url_antigua`)
  );
  ```

#### 9.2. Admin Panel
- [ ] Crear `admin/seo/config.php`:
  - [ ] Configuración global (título, favicon, OG image)
  - [ ] Configuración por página (Home, Catálogo, Blog, etc.)
- [ ] Crear `admin/seo/robots.php`:
  - [ ] Editor de `robots.txt`
  - [ ] Preview
  - [ ] Validación
- [ ] Crear `admin/seo/sitemap.php`:
  - [ ] Generación automática de sitemap
  - [ ] Configuración de frecuencia
  - [ ] Excluir páginas
- [ ] Crear `admin/seo/redirects.php`:
  - [ ] CRUD de redirecciones 301
  - [ ] Validación de URLs
  - [ ] Importación masiva (opcional)
- [ ] Crear `admin/seo/schema.php`:
  - [ ] Activar/desactivar tipos de Schema
  - [ ] Configuración de Organization
  - [ ] Preview de JSON-LD

#### 9.3. Implementación Técnica
- [ ] Modificar `includes/functions.php` para leer SEO desde BD
- [ ] Crear `includes/seo_functions.php`:
  - [ ] Función para generar meta tags
  - [ ] Función para generar sitemap
  - [ ] Función para manejar redirects
- [ ] Modificar `.htaccess` o crear `redirects.php` para manejar 301s

#### 📁 Archivos a Crear:
```
admin/
  seo/
    index.php (dashboard SEO)
    config.php
    robots.php
    sitemap.php
    redirects.php
    schema.php

includes/
  seo_functions.php
```

#### ⚠️ Puntos de Atención:
- Mantener compatibilidad con meta tags hardcodeados actuales
- Generar sitemap dinámicamente desde BD
- Manejar redirects eficientemente (cache si es posible)

---

### 4.10. GOOGLE ANALYTICS / MÉTRICAS

**Estado:** 🟡 30% implementado  
**Archivo base:** `includes/analytics.php`

#### ✅ Ya Implementado:
- [x] Google Analytics tag (`G-3BPRR93ZCY`)
- [x] Implementado en todas las páginas públicas

#### ⚠️ Falta Implementar:
- [ ] Configuración desde admin:
  - [ ] Campo para Measurement ID
  - [ ] Activar/desactivar tracking
- [ ] Dashboard de métricas en admin:
  - [ ] Integración con GA4 API (requiere OAuth)
  - [ ] O usar iframe de Google Analytics
  - [ ] Métricas: usuarios, sesiones, páginas vistas, tiempo en sitio
- [ ] Eventos personalizados:
  - [ ] `add_to_quote` (agregar producto a cotización)
  - [ ] `submit_quote` (enviar cotización)
  - [ ] `submit_contact` (enviar contacto)
  - [ ] `subscribe_newsletter` (suscribirse)
- [ ] Documentación de objetivos/embudos

#### 📁 Archivos a Crear:
```
admin/
  analytics/
    config.php
    dashboard.php

includes/
  analytics_events.php (helper para eventos)
```

#### ⚠️ Puntos de Atención:
- GA4 API requiere autenticación OAuth (complejo)
- Alternativa: iframe de Google Analytics (más simple)
- Eventos se implementan en JavaScript del frontend

---

### 4.11. APARIENCIA & MÓDULOS

**Estado:** 🔴 0% implementado

#### ⚠️ Todo por Implementar:

#### 11.1. Base de Datos
- [ ] Crear tabla `home_secciones`:
  ```sql
  CREATE TABLE `home_secciones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `seccion` VARCHAR(100) NOT NULL,
    `activa` TINYINT(1) DEFAULT 1,
    `orden` INT DEFAULT 0,
    `configuracion` JSON NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  );
  ```
- [ ] Crear tabla `paginas_estaticas`:
  ```sql
  CREATE TABLE `paginas_estaticas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(255) UNIQUE NOT NULL,
    `titulo` VARCHAR(255) NOT NULL,
    `contenido` TEXT,
    `meta_titulo` VARCHAR(255),
    `meta_descripcion` TEXT,
    `estado` ENUM('publicado', 'borrador') DEFAULT 'borrador',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  );
  ```

#### 11.2. Admin Panel
- [ ] Crear `admin/apariencia/secciones.php`:
  - [ ] Toggles para activar/desactivar secciones
  - [ ] Drag & drop para reordenar
  - [ ] Configuración por sección
- [ ] Crear `admin/apariencia/paginas.php`:
  - [ ] CRUD de páginas estáticas
  - [ ] Editor WYSIWYG/Markdown
  - [ ] Vista previa
- [ ] Crear `admin/apariencia/vista-previa.php`:
  - [ ] Preview del Home con cambios
  - [ ] Comparación antes/después

#### 11.3. Frontend
- [ ] Modificar `index.php` para leer configuración desde BD
- [ ] Crear sistema de routing para páginas estáticas

#### 📁 Archivos a Crear:
```
admin/
  apariencia/
    index.php
    secciones.php
    paginas.php
    vista-previa.php
```

#### 🔧 Tecnologías:
- SortableJS o similar para drag & drop
- WYSIWYG editor para contenido

---

### 4.12. USUARIOS & ROLES (Completar RBAC)

**Estado:** 🟡 60% implementado  
**Archivos base:** `admin/usuarios.php`, `admin/auth_check.php`

#### ✅ Ya Implementado:
- [x] CRUD básico de usuarios
- [x] Roles básicos (admin, editor)
- [x] Autenticación
- [x] Perfil de usuario

#### ⚠️ Falta Implementar:

#### 12.1. Base de Datos
- [ ] Crear tabla `permisos`:
  ```sql
  CREATE TABLE `permisos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `modulo` VARCHAR(100) NOT NULL,
    `accion` VARCHAR(100) NOT NULL,
    `descripcion` TEXT,
    UNIQUE KEY `modulo_accion` (`modulo`, `accion`)
  );
  ```
- [ ] Crear tabla `rol_permisos`:
  ```sql
  CREATE TABLE `rol_permisos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `rol` VARCHAR(50) NOT NULL,
    `permiso_id` INT NOT NULL,
    FOREIGN KEY (`permiso_id`) REFERENCES `permisos`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `rol_permiso` (`rol`, `permiso_id`)
  );
  ```
- [ ] Agregar campos a `admin_usuarios`:
  ```sql
  ALTER TABLE `admin_usuarios`
  ADD COLUMN `forzar_cambio_password` TINYINT(1) DEFAULT 0,
  ADD COLUMN `intentos_fallidos` INT DEFAULT 0,
  ADD COLUMN `bloqueado_hasta` DATETIME NULL,
  ADD COLUMN `ultimo_cambio_password` TIMESTAMP NULL;
  ```
- [ ] Crear tabla `audit_logs`:
  ```sql
  CREATE TABLE `audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NOT NULL,
    `accion` VARCHAR(100) NOT NULL,
    `modulo` VARCHAR(100),
    `entidad_id` INT,
    `detalles` TEXT,
    `ip_address` VARCHAR(45),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`) REFERENCES `admin_usuarios`(`id`),
    INDEX `idx_usuario` (`usuario_id`),
    INDEX `idx_accion` (`accion`),
    INDEX `idx_created_at` (`created_at`)
  );
  ```

#### 12.2. Funcionalidades
- [ ] Forzar cambio de contraseña inicial:
  - [ ] Verificación en `auth_check.php`
  - [ ] Redirección a cambio de contraseña
  - [ ] Formulario de cambio obligatorio
- [ ] Bloqueo tras intentos fallidos:
  - [ ] Contador de intentos en `login.php`
  - [ ] Bloqueo temporal (configurable)
  - [ ] Desbloqueo manual por admin
- [ ] Bitácora de actividad:
  - [ ] Función `logActivity()` en `includes/functions.php`
  - [ ] Registrar: login, cambios críticos, altas/bajas
  - [ ] Vista de logs en admin
- [ ] Recuperación de contraseña:
  - [ ] Formulario "Olvidé mi contraseña"
  - [ ] Generación de token temporal
  - [ ] Email con link de recuperación
  - [ ] Expiración de token (24 horas)
- [ ] RBAC granular:
  - [ ] Sistema de permisos por módulo
  - [ ] Verificación en cada página admin
  - [ ] Interfaz para asignar permisos a roles

#### 📁 Archivos a Modificar:
- `admin/login.php`
- `admin/auth_check.php`
- `admin/usuarios.php`
- `includes/functions.php`

#### 📁 Archivos a Crear:
```
admin/
  usuarios/
    recuperar-password.php
    cambiar-password.php
    logs.php

includes/
  rbac_functions.php
```

---

### 4.13. CONFIGURACIÓN GENERAL

**Estado:** 🟡 50% implementado  
**Archivo base:** `includes/config.php`

#### ✅ Ya Implementado:
- [x] Configuración centralizada (`includes/config.php`)
- [x] SMTP configurado
- [x] Datos de empresa

#### ⚠️ Falta Implementar:

#### 13.1. Base de Datos
- [ ] Crear tabla `configuracion`:
  ```sql
  CREATE TABLE `configuracion` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `clave` VARCHAR(100) UNIQUE NOT NULL,
    `valor` TEXT,
    `tipo` ENUM('texto', 'numero', 'boolean', 'json') DEFAULT 'texto',
    `categoria` VARCHAR(100),
    `descripcion` TEXT,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  );
  ```

#### 13.2. Admin Panel
- [ ] Crear `admin/configuracion/index.php`:
  - [ ] Tabs por categoría (Empresa, SMTP, Integraciones, Legal)
  - [ ] Formularios editables
  - [ ] Validación de campos
  - [ ] Preview de cambios

#### 13.3. Funcionalidades
- [ ] Datos de empresa:
  - [ ] Razón social, dirección, teléfonos
  - [ ] Emails de routing (ventas@, soporte@, admin@)
- [ ] SMTP:
  - [ ] Host, puerto, usuario, contraseña
  - [ ] Test de conexión
- [ ] Integraciones:
  - [ ] Google Analytics ID
  - [ ] APIs de terceros
- [ ] Textos legales:
  - [ ] Editor de políticas de privacidad
  - [ ] Editor de términos y condiciones
  - [ ] Editor de política de cookies

#### 📁 Archivos a Crear:
```
admin/
  configuracion/
    index.php
    test-smtp.php
```

#### ⚠️ Puntos de Atención:
- Mantener `config.php` como fallback
- Cachear configuración para performance
- Validar cambios antes de guardar

---

## 5. ARQUITECTURA Y ESTRUCTURA

### 5.1. Estructura de Directorios

```
public_html/
  admin/
    index.php (dashboard)
    login.php
    logout.php
    auth_check.php
    perfil.php
    
    # Módulos nuevos
    home/
      index.php
      banners.php
      productos-destacados.php
      servicios.php
      mision-vision.php
      categorias-destacadas.php
    
    catalogo/
      index.php
      categorias.php
      marcas.php
      productos/
        index.php
        create.php
        edit.php
        view.php
    
    proyectos/
      index.php
      create.php
      edit.php
      view.php
    
    cotizaciones/
      index.php
      view.php
      export.php
    
    contacto/
      index.php
      view.php
    
    seo/
      index.php
      config.php
      robots.php
      sitemap.php
      redirects.php
      schema.php
    
    apariencia/
      index.php
      secciones.php
      paginas.php
      vista-previa.php
    
    analytics/
      config.php
      dashboard.php
    
    configuracion/
      index.php
      test-smtp.php
    
    usuarios/
      recuperar-password.php
      cambiar-password.php
      logs.php
    
    # Módulos existentes (mejorar)
    blog/ (ya existe)
    newsletter-simple.php (mejorar)
    newsletter-subscriptions.php (mejorar)
    topbar-messages.php (ya existe)
    usuarios.php (mejorar)
    
    includes/
      admin_menu.php (actualizar con nuevos módulos)
      dashboard_charts.php
      dashboard_alerts.php
      rbac_functions.php
```

### 5.2. Archivos Compartidos

```
includes/
  config.php (ya existe)
  connection.php (ya existe)
  functions.php (extender)
  email_functions.php (ya existe)
  seo_functions.php (nuevo)
  analytics_events.php (nuevo)
  rbac_functions.php (nuevo)
```

### 5.3. Patrones de Código

#### Estructura de Página Admin Típica:
```php
<?php
define('ARAMED_SITE', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/connection.php';
require_once __DIR__ . '/auth_check.php';

// Verificar permisos
checkPermission('modulo', 'accion');

$pdo = getDB();
// ... lógica ...
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Head común -->
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/includes/admin_menu.php'; ?>
            <div class="col-md-9">
                <!-- Contenido -->
            </div>
        </div>
    </div>
</body>
</html>
```

---

## 6. BASE DE DATOS

### 6.1. Tablas Nuevas a Crear

1. `home_banners`
2. `home_productos_destacados`
3. `home_servicios`
4. `home_mision_vision`
5. `home_categorias_destacadas`
6. `proyectos`
7. `proyecto_imagenes`
8. `proyecto_videos`
9. `proyecto_documentos`
10. `cotizaciones`
11. `cotizacion_items`
12. `cotizacion_auditoria`
13. `seo_config`
14. `redirects`
15. `home_secciones`
16. `paginas_estaticas`
17. `permisos`
18. `rol_permisos`
19. `audit_logs`
20. `configuracion`

### 6.2. Tablas a Modificar

1. `blog_articulos` - Agregar `fecha_programada`
2. `admin_usuarios` - Agregar campos de seguridad

### 6.3. Scripts de Migración

Crear en `database/fase2/`:
- `01_create_home_tables.sql`
- `02_create_proyectos_tables.sql`
- `03_create_cotizaciones_tables.sql`
- `04_create_seo_tables.sql`
- `05_create_rbac_tables.sql`
- `06_alter_existing_tables.sql`
- `07_migrate_cotizaciones.sql` (migrar de newsletter_subscriptions)

---

## 7. SEGURIDAD Y MEJORES PRÁCTICAS

### 7.1. Seguridad

- [ ] **CSRF Tokens:** En todos los formularios admin
- [ ] **XSS Prevention:** `esc()` en todos los outputs
- [ ] **SQL Injection:** PDO prepared statements (ya aplicado)
- [ ] **File Upload:** Validar tipo, tamaño, renombrar archivos
- [ ] **Password Security:** `password_hash()` / `password_verify()` (ya aplicado)
- [ ] **Session Security:** Regenerar ID al login, timeout
- [ ] **RBAC:** Verificar permisos en cada página
- [ ] **Audit Logs:** Registrar acciones críticas

### 7.2. Mejores Prácticas

- [ ] **Código Reutilizable:** Funciones en `includes/functions.php`
- [ ] **Consistencia:** Mismo patrón en todos los módulos
- [ ] **Validación:** Frontend y backend
- [ ] **Manejo de Errores:** Try-catch, mensajes claros
- [ ] **Performance:** Índices en BD, cache cuando sea posible
- [ ] **Documentación:** Comentarios en código complejo

---

## 8. CHECKLIST FINAL DE ENTREGA

### Funcionalidad
- [ ] Todos los módulos accesibles desde `/admin/`
- [ ] Control de roles funcionando
- [ ] Contenido gestionable sin programación
- [ ] Flujos completos (cotización, contacto, newsletter)

### Seguridad
- [ ] CSRF tokens implementados
- [ ] RBAC granular funcionando
- [ ] Audit logs activos
- [ ] Validación de uploads

### Base de Datos
- [ ] Todas las tablas creadas
- [ ] Migraciones ejecutadas
- [ ] Índices optimizados
- [ ] Datos de prueba (opcional)

### Documentación
- [ ] `README_Aramed_Fase2.md`
- [ ] `MANUAL_ADMIN_FASE2.md`
- [ ] `DB_CHANGELOG_FASE2.md`
- [ ] Comentarios en código

### Testing
- [ ] Pruebas funcionales de cada módulo
- [ ] Pruebas de seguridad
- [ ] Pruebas de permisos
- [ ] UAT con cliente

### Producción
- [ ] Código en repositorio Git
- [ ] Deploy en producción
- [ ] Configuración actualizada
- [ ] Capacitación realizada

---

## 📝 NOTAS FINALES

### Orden de Desarrollo Recomendado:
1. **Semana 1-2:** Dashboard, Usuarios, Configuración (fundamentos)
2. **Semana 3-4:** Catálogo CRUD, Gestor de Home (crítico)
3. **Semana 5-6:** Proyectos, Blog (completar)
4. **Semana 7-8:** Contacto, Cotizaciones (gestión)
5. **Semana 9-10:** SEO, Newsletter, Analytics (optimización)
6. **Semana 11-12:** Apariencia, QA, Documentación (finalización)

### Puntos Críticos:
- ⚠️ **Catálogo:** Prioridad #1, activar en navbar cuando esté listo
- ⚠️ **Cotizaciones:** Migrar de `newsletter_subscriptions` a `cotizaciones`
- ⚠️ **RBAC:** Implementar antes de agregar más módulos
- ⚠️ **Seguridad:** No comprometer por velocidad

### Recursos:
- Reutilizar patrones del blog (ya funciona bien)
- Mantener consistencia en UI/UX
- Documentar decisiones técnicas importantes

---

**Documento creado:** Enero 2025  
**Versión:** 1.0  
**Estado:** Listo para desarrollo


