# 📋 PLAN DE TRABAJO - FASE 2
## Panel Administrativo - Aramed y Laboratorios

**Estado:** ✅ Aprobado por cliente  
**Fecha de inicio:** Enero 2025  
**Horas estimadas:** 210 horas  
**Duración estimada:** 12 semanas

---

## 🎯 OBJETIVO

Desarrollar el **Panel Administrativo completo** para gestionar todo el contenido del sitio sin requerir programación, convirtiendo la Fase 1 en un **CMS empresarial robusto**.

---

## 📊 RESUMEN DE MÓDULOS

| # | Módulo | Estado Actual | Prioridad | Semanas |
|---|--------|---------------|-----------|---------|
| 1 | Dashboard Avanzado | 🟡 60% | Media | 1-2 |
| 2 | Usuarios & Roles (RBAC) | 🟡 60% | Alta | 1-2 |
| 3 | Configuración General | 🟡 50% | Media | 1-2 |
| 4 | Catálogo CRUD Admin | 🔴 0% | **ALTA** | 3-4 |
| 5 | Gestor de Home | 🔴 0% | **ALTA** | 3-4 |
| 6 | Proyectos | 🔴 0% | Media | 5-6 |
| 7 | Blog (Completar) | 🟢 95% | Baja | 5-6 |
| 8 | Contacto Admin | 🔴 0% | **ALTA** | 7-8 |
| 9 | Cotizaciones Avanzado | 🟡 50% | **ALTA** | 7-8 |
| 10 | Newsletter Avanzado | 🟡 40% | Baja | 9-10 |
| 11 | SEO & Metadatos Admin | 🔴 0% | Media | 9-10 |
| 12 | Analytics Dashboard | 🟡 30% | Baja | 9-10 |
| 13 | Apariencia & Módulos | 🔴 0% | Baja | 11-12 |

---

## 🚀 FASE 1: FUNDAMENTOS (Semanas 1-2)

### ✅ TAREA 1.1: Dashboard Avanzado
**Estado:** 🟡 60% implementado  
**Archivo base:** `admin/index.php`  
**Tiempo estimado:** 8 horas

#### Checklist:
- [ ] **1.1.1** Agregar librería de gráficas (Chart.js o ApexCharts)
  - [ ] Incluir CDN en `admin/index.php`
  - [ ] Crear helper `admin/includes/dashboard_charts.php`
  
- [ ] **1.1.2** Implementar gráficas de tendencias
  - [ ] Gráfica de cotizaciones por mes (últimos 12 meses)
  - [ ] Gráfica de suscriptores por mes
  - [ ] Endpoint AJAX para datos: `admin/includes/dashboard_data.php`

- [ ] **1.1.3** Agregar KPIs adicionales
  - [ ] Productos publicados (contar desde `catalogo_productos`)
  - [ ] Mensajes de contacto por estado
  - [ ] Cotizaciones: hoy/semana/mes/acumulado

- [ ] **1.1.4** Implementar alertas automáticas
  - [ ] Crear helper `admin/includes/dashboard_alerts.php`
  - [ ] Mensajes de contacto "Abiertos" con más de 3 días
  - [ ] Cotizaciones "Nuevas" sin asignar
  - [ ] Productos sin categoría o sin ficha técnica

- [ ] **1.1.5** Agregar listas rápidas
  - [ ] Últimas 5 cotizaciones recibidas
  - [ ] Últimos 5 contactos

#### Archivos a Crear/Modificar:
- `admin/index.php` (modificar)
- `admin/includes/dashboard_charts.php` (nuevo)
- `admin/includes/dashboard_alerts.php` (nuevo)
- `admin/includes/dashboard_data.php` (nuevo)

---

### ✅ TAREA 1.2: Usuarios & Roles - RBAC Granular
**Estado:** 🟡 60% implementado  
**Archivos base:** `admin/usuarios.php`, `admin/auth_check.php`  
**Tiempo estimado:** 16 horas

#### Checklist:
- [ ] **1.2.1** Crear estructura de base de datos RBAC
  - [ ] Ejecutar script: `database/fase2/05_create_rbac_tables.sql`
  - [ ] Crear tabla `permisos`
  - [ ] Crear tabla `rol_permisos`
  - [ ] Agregar campos a `admin_usuarios` (forzar_cambio_password, intentos_fallidos, etc.)
  - [ ] Crear tabla `audit_logs`

- [ ] **1.2.2** Poblar permisos iniciales
  - [ ] Script SQL con permisos por módulo:
    - Dashboard: ver, editar
    - Home: ver, crear, editar, eliminar
    - Catálogo: ver, crear, editar, eliminar
    - Proyectos: ver, crear, editar, eliminar
    - Blog: ver, crear, editar, eliminar, moderar
    - Cotizaciones: ver, editar, asignar, exportar
    - Contacto: ver, editar, asignar
    - Newsletter: ver, importar, exportar
    - SEO: ver, editar
    - Usuarios: ver, crear, editar, eliminar
    - Configuración: ver, editar

- [ ] **1.2.3** Crear funciones RBAC
  - [ ] Crear `includes/rbac_functions.php`
  - [ ] Función `checkPermission($modulo, $accion)`
  - [ ] Función `hasPermission($usuario_id, $modulo, $accion)`
  - [ ] Función `getUserPermissions($usuario_id)`

- [ ] **1.2.4** Implementar verificación de permisos
  - [ ] Modificar `admin/auth_check.php` para verificar permisos
  - [ ] Agregar verificación en cada página admin
  - [ ] Mostrar errores amigables si no tiene permiso

- [ ] **1.2.5** Interfaz de gestión de permisos
  - [ ] Modificar `admin/usuarios.php` para asignar permisos
  - [ ] Checkboxes por módulo/acción
  - [ ] Asignación masiva por rol

- [ ] **1.2.6** Forzar cambio de contraseña inicial
  - [ ] Modificar `admin/auth_check.php` para verificar flag
  - [ ] Crear `admin/usuarios/cambiar-password.php`
  - [ ] Redirección automática si requiere cambio

- [ ] **1.2.7** Bloqueo tras intentos fallidos
  - [ ] Modificar `admin/login.php` para contar intentos
  - [ ] Bloqueo temporal (30 minutos) tras 5 intentos
  - [ ] Desbloqueo manual en `admin/usuarios.php`

- [ ] **1.2.8** Bitácora de actividad
  - [ ] Función `logActivity()` en `includes/functions.php`
  - [ ] Registrar: login, logout, cambios críticos, altas/bajas
  - [ ] Crear `admin/usuarios/logs.php` para ver historial

- [ ] **1.2.9** Recuperación de contraseña
  - [ ] Crear `admin/usuarios/recuperar-password.php`
  - [ ] Generación de token temporal
  - [ ] Email con link de recuperación
  - [ ] Expiración de token (24 horas)

#### Archivos a Crear/Modificar:
- `database/fase2/05_create_rbac_tables.sql` (nuevo)
- `includes/rbac_functions.php` (nuevo)
- `admin/auth_check.php` (modificar)
- `admin/login.php` (modificar)
- `admin/usuarios.php` (modificar)
- `admin/usuarios/cambiar-password.php` (nuevo)
- `admin/usuarios/recuperar-password.php` (nuevo)
- `admin/usuarios/logs.php` (nuevo)
- `includes/functions.php` (agregar logActivity)

---

### ✅ TAREA 1.3: Configuración General - Admin Panel
**Estado:** 🟡 50% implementado  
**Archivo base:** `includes/config.php`  
**Tiempo estimado:** 8 horas

#### Checklist:
- [ ] **1.3.1** Crear tabla de configuración
  - [ ] Ejecutar script: `database/fase2/06_create_configuracion_table.sql`
  - [ ] Crear tabla `configuracion`

- [ ] **1.3.2** Migrar configuración actual
  - [ ] Script para insertar valores desde `config.php`
  - [ ] Datos de empresa, SMTP, integraciones

- [ ] **1.3.3** Crear admin panel de configuración
  - [ ] Crear `admin/configuracion/index.php`
  - [ ] Tabs por categoría: Empresa, SMTP, Integraciones, Legal
  - [ ] Formularios editables con validación

- [ ] **1.3.4** Funcionalidades específicas
  - [ ] Datos de empresa (razón social, dirección, teléfonos, emails)
  - [ ] SMTP (host, puerto, usuario, contraseña)
  - [ ] Test de conexión SMTP: `admin/configuracion/test-smtp.php`
  - [ ] Google Analytics ID
  - [ ] Editor de textos legales (privacidad, términos, cookies)

- [ ] **1.3.5** Sistema de cache
  - [ ] Función para leer configuración desde BD
  - [ ] Cache en sesión o archivo
  - [ ] Invalidar cache al actualizar

#### Archivos a Crear/Modificar:
- `database/fase2/06_create_configuracion_table.sql` (nuevo)
- `admin/configuracion/index.php` (nuevo)
- `admin/configuracion/test-smtp.php` (nuevo)
- `includes/functions.php` (agregar getConfig, setConfig)

---

## 🚀 FASE 2: CONTENIDO CRÍTICO (Semanas 3-4)

### ✅ TAREA 2.1: Catálogo CRUD Admin (PRIORIDAD #1)
**Estado:** 🔴 0% del admin (100% frontend)  
**Archivos base:** `catalogo.php`, `producto.php`  
**Tiempo estimado:** 40 horas

#### Checklist:
- [ ] **2.1.1** Estructura de directorios
  - [ ] Crear `admin/catalogo/`
  - [ ] Crear `admin/catalogo/productos/`

- [ ] **2.1.2** CRUD de Categorías
  - [ ] Crear `admin/catalogo/categorias.php`
  - [ ] Listado con árbol jerárquico (recursivo)
  - [ ] Formulario crear/editar:
    - Nombre, slug (auto-generado), descripción
    - Imagen opcional (upload)
    - Categoría padre (dropdown)
    - SEO: meta title, meta description
    - Estado: activo/inactivo
  - [ ] Eliminar con validación (no si tiene productos)
  - [ ] Ordenar con drag & drop (SortableJS)

- [ ] **2.1.3** CRUD de Marcas
  - [ ] Crear `admin/catalogo/marcas.php`
  - [ ] Listado de marcas
  - [ ] Formulario crear/editar:
    - Nombre, slug, logo (upload), descripción, website
    - Orden
    - Estado: activo/inactivo
  - [ ] Eliminar con validación
  - [ ] Upload de logos (validar tipo, tamaño, redimensionar)

- [ ] **2.1.4** CRUD de Productos - Listado
  - [ ] Crear `admin/catalogo/productos/index.php`
  - [ ] Listado con tabla responsive
  - [ ] Filtros: marca, categoría, estado, búsqueda
  - [ ] Búsqueda full-text (nombre, SKU, descripción)
  - [ ] Paginación (12 por página)
  - [ ] Bulk actions: cambiar estado, asignar categoría
  - [ ] Exportar a CSV/Excel

- [ ] **2.1.5** CRUD de Productos - Crear
  - [ ] Crear `admin/catalogo/productos/create.php`
  - [ ] Sección 1: Datos básicos
    - Nombre, slug (auto), SKU, marca (dropdown), categoría (dropdown)
    - Tags (input múltiple)
  - [ ] Sección 2: Contenido
    - Descripción corta (textarea)
    - Descripción larga (WYSIWYG: TinyMCE)
  - [ ] Sección 3: Medios
    - Galería de imágenes (múltiple upload)
    - Videos embebidos (URLs YouTube/Vimeo)
  - [ ] Sección 4: Documentos
    - Upload de PDFs (fichas técnicas, manuales, brochures)
  - [ ] Sección 5: Atributos técnicos
    - Pares clave/valor dinámicos (agregar/quitar)
  - [ ] Sección 6: Estado y SEO
    - Estado: borrador/publicado/oculto
    - Flag "destacado"
    - Meta title, meta description, canonical, OG image

- [ ] **2.1.6** CRUD de Productos - Editar
  - [ ] Crear `admin/catalogo/productos/edit.php`
  - [ ] Mismo formulario que create
  - [ ] Precargar datos existentes
  - [ ] Gestión de imágenes existentes (eliminar, reordenar)
  - [ ] Gestión de documentos existentes (eliminar)
  - [ ] Gestión de videos existentes (eliminar, editar)

- [ ] **2.1.7** CRUD de Productos - Vista
  - [ ] Crear `admin/catalogo/productos/view.php`
  - [ ] Vista detallada del producto
  - [ ] Información completa
  - [ ] Historial de cambios (opcional)

- [ ] **2.1.8** Funcionalidades adicionales
  - [ ] Duplicar producto
  - [ ] Productos relacionados (manual o automático por categoría/tags)
  - [ ] Upload de imágenes: `admin/catalogo/productos/upload-image.php`
  - [ ] Upload de documentos: `admin/catalogo/productos/upload-document.php`
  - [ ] Validación de archivos (tipo, tamaño)
  - [ ] Optimización de imágenes (WebP, redimensionamiento)

- [ ] **2.1.9** Dashboard del catálogo
  - [ ] Crear `admin/catalogo/index.php`
  - [ ] Estadísticas: total productos, por estado, por marca
  - [ ] Accesos rápidos: crear producto, categoría, marca

- [ ] **2.1.10** Activar catálogo en navbar
  - [ ] Descomentar línea 30 en `includes/navbar.php`
  - [ ] Verificar que funcione correctamente

#### Archivos a Crear:
```
admin/catalogo/
  index.php
  categorias.php
  marcas.php
  productos/
    index.php
    create.php
    edit.php
    view.php
    upload-image.php
    upload-document.php
```

#### Archivos a Modificar:
- `includes/navbar.php` (descomentar catálogo)

---

### ✅ TAREA 2.2: Gestor de Inicio (Home)
**Estado:** 🔴 0% del admin (100% frontend)  
**Archivo base:** `index.php`  
**Tiempo estimado:** 24 horas

#### Checklist:
- [ ] **2.2.1** Crear estructura de base de datos
  - [ ] Ejecutar script: `database/fase2/01_create_home_tables.sql`
  - [ ] Crear tabla `home_banners`
  - [ ] Crear tabla `home_productos_destacados`
  - [ ] Crear tabla `home_servicios`
  - [ ] Crear tabla `home_mision_vision`
  - [ ] Crear tabla `home_categorias_destacadas`

- [ ] **2.2.2** CRUD de Banners/Hero
  - [ ] Crear `admin/home/banners.php`
  - [ ] Listado de banners con preview
  - [ ] Formulario crear/editar:
    - Título, subtítulo, imagen (upload), video URL (opcional)
    - CTA texto, CTA URL
    - Orden (drag & drop)
    - Estado: publicado/borrador
    - Fechas de vigencia (opcional)
  - [ ] Upload de imágenes/videos
  - [ ] Preview del banner

- [ ] **2.2.3** CRUD de Productos Destacados
  - [ ] Crear `admin/home/productos-destacados.php`
  - [ ] Listado de productos destacados
  - [ ] Selección manual: buscar y agregar productos
  - [ ] Modo automático: seleccionar por regla (más nuevos, destacados)
  - [ ] Ordenar productos (drag & drop)

- [ ] **2.2.4** CRUD de Servicios
  - [ ] Crear `admin/home/servicios.php`
  - [ ] Listado de servicios
  - [ ] Formulario crear/editar:
    - Ícono (selector o upload), título, resumen, texto largo (WYSIWYG)
    - CTA texto, CTA URL
    - Orden
    - Estado: activo/inactivo

- [ ] **2.2.5** Editor de Misión y Visión
  - [ ] Crear `admin/home/mision-vision.php`
  - [ ] Editor WYSIWYG para misión
  - [ ] Editor WYSIWYG para visión
  - [ ] Upload de imagen opcional
  - [ ] Preview

- [ ] **2.2.6** CRUD de Categorías Destacadas
  - [ ] Crear `admin/home/categorias-destacadas.php`
  - [ ] Seleccionar categorías del catálogo
  - [ ] Ordenar categorías (drag & drop)

- [ ] **2.2.7** Dashboard del gestor de Home
  - [ ] Crear `admin/home/index.php`
  - [ ] Vista general de todas las secciones
  - [ ] Accesos rápidos a cada sección
  - [ ] Estado de cada sección (activa/inactiva)

- [ ] **2.2.8** Modificar frontend para leer desde BD
  - [ ] Modificar `index.php` para leer banners desde BD
  - [ ] Modificar `index.php` para leer productos destacados desde BD
  - [ ] Modificar `index.php` para leer servicios desde BD
  - [ ] Modificar `index.php` para leer misión/visión desde BD
  - [ ] Modificar `index.php` para leer categorías destacadas desde BD
  - [ ] Mantener fallback a contenido hardcodeado si BD está vacía

#### Archivos a Crear:
```
admin/home/
  index.php
  banners.php
  productos-destacados.php
  servicios.php
  mision-vision.php
  categorias-destacadas.php
```

#### Archivos a Modificar:
- `index.php` (leer desde BD)
- `database/fase2/01_create_home_tables.sql` (nuevo)

---

## 🚀 FASE 3: NUEVOS MÓDULOS (Semanas 5-6)

### ✅ TAREA 3.1: Módulo Proyectos
**Estado:** 🔴 0% (módulo completo nuevo)  
**Tiempo estimado:** 20 horas

#### Checklist:
- [ ] **3.1.1** Crear estructura de base de datos
  - [ ] Ejecutar script: `database/fase2/02_create_proyectos_tables.sql`
  - [ ] Crear tabla `proyectos`
  - [ ] Crear tabla `proyecto_imagenes`
  - [ ] Crear tabla `proyecto_videos`
  - [ ] Crear tabla `proyecto_documentos`

- [ ] **3.1.2** Admin CRUD - Listado
  - [ ] Crear `admin/proyectos/index.php`
  - [ ] Listado con grid de proyectos
  - [ ] Filtros: año, categoría, sector, estado
  - [ ] Búsqueda
  - [ ] Paginación

- [ ] **3.1.3** Admin CRUD - Crear
  - [ ] Crear `admin/proyectos/create.php`
  - [ ] Formulario completo:
    - Título, slug (auto), sector, categoría, año, país, ubicación
    - Descripción corta, descripción larga (WYSIWYG)
    - Imagen principal (upload)
    - Galería de imágenes (múltiple upload)
    - Videos embebidos (URLs)
    - Documentos adjuntos (PDFs)
    - SEO: meta title, meta description
    - Estado: borrador/publicado

- [ ] **3.1.4** Admin CRUD - Editar
  - [ ] Crear `admin/proyectos/edit.php`
  - [ ] Mismo formulario que create
  - [ ] Gestión de medios existentes

- [ ] **3.1.5** Admin CRUD - Vista
  - [ ] Crear `admin/proyectos/view.php`
  - [ ] Vista detallada del proyecto

- [ ] **3.1.6** Frontend - Listado
  - [ ] Crear `proyectos.php`
  - [ ] Grid de proyectos con filtros
  - [ ] Paginación

- [ ] **3.1.7** Frontend - Detalle
  - [ ] Crear `proyecto.php`
  - [ ] Información completa
  - [ ] Galería de imágenes (lightbox)
  - [ ] Videos embebidos
  - [ ] Descarga de documentos
  - [ ] Proyectos relacionados

#### Archivos a Crear:
```
admin/proyectos/
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

---

### ✅ TAREA 3.2: Blog - Completar Programación
**Estado:** 🟢 95% implementado  
**Archivos base:** `admin/blog/*`  
**Tiempo estimado:** 4 horas

#### Checklist:
- [ ] **3.2.1** Agregar campo a base de datos
  - [ ] Ejecutar: `ALTER TABLE blog_articulos ADD COLUMN fecha_programada DATETIME NULL;`

- [ ] **3.2.2** Modificar formularios
  - [ ] Agregar campo fecha/hora en `admin/blog/create.php`
  - [ ] Agregar campo fecha/hora en `admin/blog/edit.php`
  - [ ] Validar que fecha sea futura

- [ ] **3.2.3** Implementar publicación automática
  - [ ] Crear función `publicarArticulosProgramados()` en `includes/functions.php`
  - [ ] Llamar función en `admin/index.php` o crear cron job
  - [ ] Cambiar estado a "publicado" cuando llegue la fecha

- [ ] **3.2.4** Agregar filtro en listado
  - [ ] Modificar `admin/blog/index.php`
  - [ ] Agregar filtro "programados"
  - [ ] Mostrar estado "programado" en listado

#### Archivos a Modificar:
- `admin/blog/create.php`
- `admin/blog/edit.php`
- `admin/blog/index.php`
- `includes/functions.php`

---

## 🚀 FASE 4: GESTIÓN DE CLIENTES (Semanas 7-8)

### ✅ TAREA 4.1: Contacto Admin
**Estado:** 🔴 0% del admin (100% frontend)  
**Archivo base:** `contact_messages` (tabla existe)  
**Tiempo estimado:** 12 horas

#### Checklist:
- [ ] **4.1.1** Verificar estructura de tabla
  - [ ] Verificar que `contact_messages` tenga todos los campos necesarios
  - [ ] Agregar campo `assigned_to` si no existe

- [ ] **4.1.2** Listado de mensajes
  - [ ] Crear `admin/contacto/index.php`
  - [ ] Listado con tabla
  - [ ] Filtros: estado, motivo (asunto), fecha
  - [ ] Búsqueda
  - [ ] Paginación
  - [ ] Indicador de "no leído"

- [ ] **4.1.3** Vista detallada
  - [ ] Crear `admin/contacto/view.php`
  - [ ] Mostrar todos los datos del mensaje
  - [ ] Cambiar estado (nuevo / en proceso / resuelto / cerrado)
  - [ ] Asignar a responsable (dropdown de usuarios)
  - [ ] Respuestas rápidas (plantillas predefinidas)
  - [ ] Historial de cambios

- [ ] **4.1.4** Funcionalidades adicionales
  - [ ] Marcar como leído/no leído
  - [ ] Exportación CSV
  - [ ] Estadísticas (mensajes por estado, por mes)

#### Archivos a Crear:
```
admin/contacto/
  index.php
  view.php
```

---

### ✅ TAREA 4.2: Cotizaciones Avanzado
**Estado:** 🟡 50% implementado  
**Archivo base:** `admin/newsletter-subscriptions.php`  
**Tiempo estimado:** 20 horas

#### Checklist:
- [ ] **4.2.1** Crear nueva estructura de base de datos
  - [ ] Ejecutar script: `database/fase2/03_create_cotizaciones_tables.sql`
  - [ ] Crear tabla `cotizaciones`
  - [ ] Crear tabla `cotizacion_items`
  - [ ] Crear tabla `cotizacion_auditoria`

- [ ] **4.2.2** Migrar datos existentes
  - [ ] Crear script: `database/fase2/07_migrate_cotizaciones.sql`
  - [ ] Migrar de `newsletter_subscriptions` a `cotizaciones`
  - [ ] Validar integridad de datos
  - [ ] Generar folios únicos (COT-2025-001, etc.)

- [ ] **4.2.3** Listado avanzado
  - [ ] Crear `admin/cotizaciones/index.php`
  - [ ] Listado con filtros avanzados:
    - Estado: Nueva, En seguimiento, Cotizada, Enviada, Cerrada (Ganada/Perdida)
    - Fecha (rango)
    - Cliente, empresa
    - Marca, categoría (desde items)
  - [ ] Búsqueda
  - [ ] Paginación
  - [ ] Indicadores visuales por estado

- [ ] **4.2.4** Vista detallada
  - [ ] Crear `admin/cotizaciones/view.php`
  - [ ] Datos del cliente completos
  - [ ] Lista de productos con cantidades
  - [ ] Asignar a ejecutivo (dropdown de usuarios)
  - [ ] Cambiar estado
  - [ ] Editor de notas internas
  - [ ] Upload de PDF de propuesta
  - [ ] Historial de acciones (auditoría)

- [ ] **4.2.5** Sistema de auditoría
  - [ ] Registrar automáticamente cambios de estado
  - [ ] Registrar asignaciones
  - [ ] Registrar notas agregadas
  - [ ] Mostrar historial en vista detallada

- [ ] **4.2.6** Exportación
  - [ ] Crear `admin/cotizaciones/export.php`
  - [ ] Exportar a CSV
  - [ ] Exportar a Excel
  - [ ] Aplicar filtros a exportación
  - [ ] Incluir todos los datos relevantes

- [ ] **4.2.7** Mejorar frontend (opcional)
  - [ ] Carrito de productos en formulario
  - [ ] Selección de productos desde catálogo
  - [ ] Cálculo de totales

#### Archivos a Crear:
```
admin/cotizaciones/
  index.php
  view.php
  export.php
```

#### Archivos a Modificar:
- `includes/newsletter_handler.php` (adaptar para nueva tabla si es necesario)

---

## 🚀 FASE 5: OPTIMIZACIÓN (Semanas 9-10)

### ✅ TAREA 5.1: SEO & Metadatos Admin
**Estado:** 🔴 0% del admin  
**Tiempo estimado:** 16 horas

#### Checklist:
- [ ] **5.1.1** Crear estructura de base de datos
  - [ ] Ejecutar script: `database/fase2/04_create_seo_tables.sql`
  - [ ] Crear tabla `seo_config`
  - [ ] Crear tabla `redirects`

- [ ] **5.1.2** Configuración global y por página
  - [ ] Crear `admin/seo/config.php`
  - [ ] Configuración global: título prefijo/sufijo, favicon, OG image
  - [ ] Configuración por página: Home, Catálogo, Blog, etc.

- [ ] **5.1.3** Gestión de robots.txt
  - [ ] Crear `admin/seo/robots.php`
  - [ ] Editor de `robots.txt`
  - [ ] Preview
  - [ ] Validación básica

- [ ] **5.1.4** Generación de sitemap.xml
  - [ ] Crear `admin/seo/sitemap.php`
  - [ ] Generación automática desde BD
  - [ ] Configuración de frecuencia
  - [ ] Excluir páginas
  - [ ] Endpoint: `sitemap.xml` (generar dinámicamente)

- [ ] **5.1.5** Redirecciones 301
  - [ ] Crear `admin/seo/redirects.php`
  - [ ] CRUD de redirecciones
  - [ ] Validación de URLs
  - [ ] Implementar en `.htaccess` o `redirects.php`

- [ ] **5.1.6** Schema.org
  - [ ] Crear `admin/seo/schema.php`
  - [ ] Activar/desactivar tipos: Organization, Product, BlogPosting, BreadcrumbList
  - [ ] Configuración de Organization
  - [ ] Preview de JSON-LD

- [ ] **5.1.7** Funciones helper
  - [ ] Crear `includes/seo_functions.php`
  - [ ] Función para generar meta tags
  - [ ] Función para generar sitemap
  - [ ] Función para manejar redirects

#### Archivos a Crear:
```
admin/seo/
  index.php
  config.php
  robots.php
  sitemap.php
  redirects.php
  schema.php

includes/
  seo_functions.php
```

---

### ✅ TAREA 5.2: Newsletter Avanzado
**Estado:** 🟡 40% implementado  
**Archivo base:** `admin/newsletter-simple.php`  
**Tiempo estimado:** 8 horas

#### Checklist:
- [ ] **5.2.1** Importación CSV
  - [ ] Crear `admin/newsletter/import.php`
  - [ ] Formulario de upload
  - [ ] Validación de formato CSV
  - [ ] Mapeo de columnas
  - [ ] Procesamiento en lote
  - [ ] Validación de emails

- [ ] **5.2.2** Exportación CSV
  - [ ] Crear `admin/newsletter/export.php`
  - [ ] Botón de exportar en listado
  - [ ] Aplicar filtros a exportación
  - [ ] Formato estándar

- [ ] **5.2.3** Plantillas HTML
  - [ ] Crear `admin/newsletter/plantillas.php`
  - [ ] CRUD de plantillas
  - [ ] Editor WYSIWYG
  - [ ] Preview de plantilla
  - [ ] Variables dinámicas

- [ ] **5.2.4** Configuración avanzada
  - [ ] Crear `admin/newsletter/config.php`
  - [ ] Campos obligatorios configurables
  - [ ] Textos legales editables

#### Archivos a Crear:
```
admin/newsletter/
  import.php
  export.php
  plantillas.php
  config.php
```

---

### ✅ TAREA 5.3: Analytics Dashboard
**Estado:** 🟡 30% implementado  
**Tiempo estimado:** 6 horas

#### Checklist:
- [ ] **5.3.1** Configuración desde admin
  - [ ] Crear `admin/analytics/config.php`
  - [ ] Campo para Measurement ID (GA4)
  - [ ] Activar/desactivar tracking

- [ ] **5.3.2** Dashboard de métricas
  - [ ] Crear `admin/analytics/dashboard.php`
  - [ ] Iframe de Google Analytics (más simple que API)
  - [ ] O integración con GA4 API (si se requiere)

- [ ] **5.3.3** Eventos personalizados
  - [ ] Crear `includes/analytics_events.php`
  - [ ] Implementar eventos en frontend:
    - `add_to_quote`
    - `submit_quote`
    - `submit_contact`
    - `subscribe_newsletter`

- [ ] **5.3.4** Documentación
  - [ ] Documentar objetivos/embudos
  - [ ] Guía de eventos

#### Archivos a Crear:
```
admin/analytics/
  config.php
  dashboard.php

includes/
  analytics_events.php
```

---

## 🚀 FASE 6: FINALIZACIÓN (Semanas 11-12)

### ✅ TAREA 6.1: Apariencia & Módulos
**Estado:** 🔴 0% implementado  
**Tiempo estimado:** 12 horas

#### Checklist:
- [ ] **6.1.1** Crear estructura de base de datos
  - [ ] Crear tabla `home_secciones`
  - [ ] Crear tabla `paginas_estaticas`

- [ ] **6.1.2** Gestión de secciones del Home
  - [ ] Crear `admin/apariencia/secciones.php`
  - [ ] Toggles para activar/desactivar secciones
  - [ ] Drag & drop para reordenar (SortableJS)
  - [ ] Configuración por sección

- [ ] **6.1.3** Editor de páginas estáticas
  - [ ] Crear `admin/apariencia/paginas.php`
  - [ ] CRUD de páginas estáticas
  - [ ] Editor WYSIWYG/Markdown
  - [ ] Vista previa

- [ ] **6.1.4** Vista previa del Home
  - [ ] Crear `admin/apariencia/vista-previa.php`
  - [ ] Preview con cambios aplicados
  - [ ] Comparación antes/después

- [ ] **6.1.5** Sistema de routing
  - [ ] Modificar `index.php` para leer configuración de secciones
  - [ ] Crear sistema de routing para páginas estáticas

#### Archivos a Crear:
```
admin/apariencia/
  index.php
  secciones.php
  paginas.php
  vista-previa.php
```

---

### ✅ TAREA 6.2: QA y Testing
**Tiempo estimado:** 16 horas

#### Checklist:
- [ ] **6.2.1** Testing funcional
  - [ ] Probar cada módulo completo
  - [ ] Verificar CRUDs
  - [ ] Verificar permisos RBAC
  - [ ] Verificar validaciones

- [ ] **6.2.2** Testing de seguridad
  - [ ] Verificar CSRF tokens
  - [ ] Verificar XSS prevention
  - [ ] Verificar SQL injection (ya aplicado)
  - [ ] Verificar file uploads
  - [ ] Verificar permisos

- [ ] **6.2.3** Testing de integración
  - [ ] Verificar flujos completos
  - [ ] Verificar emails
  - [ ] Verificar exportaciones
  - [ ] Verificar migraciones

- [ ] **6.2.4** Testing de performance
  - [ ] Optimizar consultas SQL
  - [ ] Verificar índices
  - [ ] Cache donde sea necesario

- [ ] **6.2.5** Testing de compatibilidad
  - [ ] Probar en diferentes navegadores
  - [ ] Probar en móviles
  - [ ] Verificar responsive

---

### ✅ TAREA 6.3: Documentación y Capacitación
**Tiempo estimado:** 12 horas

#### Checklist:
- [ ] **6.3.1** Documentación técnica
  - [ ] `README_Aramed_Fase2.md` - Resumen técnico
  - [ ] `DB_CHANGELOG_FASE2.md` - Cambios en BD
  - [ ] Comentarios en código complejo

- [ ] **6.3.2** Manual de usuario
  - [ ] `MANUAL_ADMIN_FASE2.md` - Guía de uso del panel
  - [ ] Screenshots de cada módulo
  - [ ] Ejemplos prácticos

- [ ] **6.3.3** Capacitación
  - [ ] Preparar presentación
  - [ ] Sesión demostrativa remota
  - [ ] Grabar sesión (opcional)
  - [ ] Q&A con cliente

---

## 📦 ENTREGABLES FINALES

### Código
- [ ] Panel administrativo completo
- [ ] Código en repositorio Git
- [ ] Todos los módulos funcionando

### Base de Datos
- [ ] Scripts de migración ejecutados
- [ ] Todas las tablas creadas
- [ ] Datos migrados correctamente

### Documentación
- [ ] `README_Aramed_Fase2.md`
- [ ] `MANUAL_ADMIN_FASE2.md`
- [ ] `DB_CHANGELOG_FASE2.md`

### Producción
- [ ] Deploy en producción
- [ ] Configuración actualizada
- [ ] Capacitación realizada
- [ ] Reporte de cierre Fase 2

---

## 📝 NOTAS IMPORTANTES

### Prioridades
1. **ALTA:** Catálogo CRUD, Gestor de Home, Contacto Admin, Cotizaciones Avanzado
2. **MEDIA:** Proyectos, SEO Admin, Dashboard Avanzado, Usuarios & Roles
3. **BAJA:** Apariencia & Módulos, Analytics Dashboard, Newsletter Avanzado

### Puntos Críticos
- ⚠️ **Catálogo:** Activar en navbar solo cuando admin CRUD esté listo
- ⚠️ **Cotizaciones:** Migrar datos de `newsletter_subscriptions` cuidadosamente
- ⚠️ **RBAC:** Implementar antes de agregar más módulos
- ⚠️ **Seguridad:** No comprometer por velocidad

### Recursos
- Reutilizar patrones del blog (ya funciona bien)
- Mantener consistencia en UI/UX
- Documentar decisiones técnicas importantes

---

## ✅ CHECKLIST GENERAL DE PROGRESO

### Semana 1-2: Fundamentos
- [ ] Dashboard Avanzado
- [ ] Usuarios & Roles (RBAC)
- [ ] Configuración General

### Semana 3-4: Contenido Crítico
- [ ] Catálogo CRUD Admin
- [ ] Gestor de Home

### Semana 5-6: Nuevos Módulos
- [ ] Proyectos
- [ ] Blog (Completar)

### Semana 7-8: Gestión de Clientes
- [ ] Contacto Admin
- [ ] Cotizaciones Avanzado

### Semana 9-10: Optimización
- [ ] SEO & Metadatos Admin
- [ ] Newsletter Avanzado
- [ ] Analytics Dashboard

### Semana 11-12: Finalización
- [ ] Apariencia & Módulos
- [ ] QA y Testing
- [ ] Documentación y Capacitación

---

**Documento creado:** Enero 2025  
**Versión:** 1.0  
**Estado:** Listo para iniciar desarrollo

