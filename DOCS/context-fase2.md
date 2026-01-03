# 🚀 PLAN DE DESARROLLO – FASE 2  
## Aramed y Laboratorios – Sistema Web Corporativo (Backend / Panel Administrativo)

**Empresa desarrolladora:** IDEAMIA – Tech  
**Cliente:** Aramed y Laboratorios S.A. de C.V.  
**Responsable Técnico:** Ing. Jorge Alberto Plascencia Correa  
**Fecha de planificación:** Octubre 2025  
**Estado:** Fase planificada (Fase 1 completada y en producción)  

---

## 1. RESUMEN EJECUTIVO

La **Fase 2** del proyecto Aramed y Laboratorios está enfocada en el desarrollo completo del **Panel Administrativo (Backend)** para la gestión integral del sitio web corporativo:

- Catálogo de productos (categorías, fichas técnicas, documentos, imágenes).
- Proyectos y casos de éxito.
- Blog corporativo.
- Solicitudes de cotización.
- Mensajes de contacto.
- Newsletter y suscriptores.
- SEO y metadatos.
- Apariencia del sitio (secciones de Home, banners, servicios, marcas).
- Integraciones con analítica y correo.
- Gestión de usuarios, roles y permisos.

Esta fase convierte la solución actual (Fase 1) en un **CMS empresarial robusto**, alineado con los procesos de **Marketing, Ventas, Servicio al Cliente y Dirección** de Aramed.

**Horas estimadas (según cotización original):** 210 horas  
**Objetivo principal:** Entregar un backend seguro, modular, mantenible y fácil de usar que permita administrar todo el contenido sin requerir programación.

---

## 2. ALCANCE DE LA FASE 2

### 2.1. Alcance Funcional

La Fase 2 incluye el desarrollo de los siguientes módulos principales en el panel administrativo:

1. **Dashboard**
2. **Gestor de Inicio (Home)**
3. **Catálogo de Productos (categorías, productos, marcas, atributos)**
4. **Proyectos**
5. **Blog (artículos, categorías, comentarios)**
6. **Cotizaciones**
7. **Contacto**
8. **Newsletter**
9. **SEO & Metadatos Globales**
10. **Google Analytics / Métricas**
11. **Apariencia & Módulos (layout Home y páginas)**
12. **Usuarios & Roles (RBAC)**
13. **Configuración General del Sistema**

> Parte de la infraestructura de administración ya fue implementada en Fase 1 (login, topbar, newsletter, blog básico).  
> En Fase 2 se extiende, consolida y formaliza como **panel central de gestión**.

### 2.2. Fuera de Alcance (Fase 2)

- Desarrollo de API pública/REST para terceros (se podrá considerar en una Fase 3).
- Integraciones directas con ERP/CRM externos.
- Multi-idioma completo del sitio (puede considerarse como evolutivo).
- Módulo de inventarios y precios dinámicos en tiempo real.
- Carrito de compras transaccional (e-commerce completo).

---

## 3. PERFILES Y ROLES DE USUARIO

### 3.1. Visitante / Cliente (Front)

Se mantiene lo definido en Fase 1: navegación, catálogo, blog, contacto, newsletter, solicitud de cotización.

### 3.2. Roles del Panel Administrativo

La Fase 2 implementará gestión de roles basada en la matriz acordada:

- **Admin General / SysAdmin**
  - Acceso total a todos los módulos, usuarios, configuración e integraciones.
- **Marketing / Contenido**
  - Banners, Home, marcas, servicios, blog, SEO, newsletter (listas y plantillas).
- **Ventas**
  - Catálogo de productos (parcial), cotizaciones, clientes, documentos.
- **Servicio al Cliente / Soporte**
  - Bandejas de cotización y contacto, seguimiento y comentarios internos.
- **Analista**
  - Acceso de solo lectura a reportes, dashboards y analítica.

> La granularidad de permisos se implementará mediante un esquema RBAC simple (rol + acciones por módulo, con posibilidad de extender).

---

## 4. MÓDULOS DEL PANEL – DETALLE FUNCIONAL

### 4.1. Dashboard

**Objetivo:** Dar una vista de alto nivel del estado del sitio y la operación comercial.

**Elementos:**
- KPIs:
  - Cotizaciones: hoy / semana / mes / acumulado.
  - Mensajes de contacto por estado.
  - Suscriptores de newsletter.
  - Número de productos publicados.
  - Posts de blog publicados.
- Gráficas:
  - Tendencia de cotizaciones por mes.
  - Tendencia de suscriptores.
- Listas rápidas:
  - Últimas cotizaciones recibidas.
  - Últimos contactos.
  - Últimos artículos publicados.
- Alertas:
  - Mensajes de contacto “Abiertos” con más de X días.
  - Cotizaciones “Nuevas” sin asignar.
  - Productos sin categoría o sin ficha técnica (reglas definibles).

---

### 4.2. Gestor de Inicio (Home)

**Submódulos:**

1. **Banners / Hero**
   - CRUD de slides: imagen/video, título, subtítulo, CTA, URL, orden, estado (publicado/borrador).
   - Posibilidad de programar inicio/fin de vigencia (opcional).

2. **Productos Destacados**
   - Selección manual de productos.
   - Opción de auto-selección por regla (más nuevos / con flag “destacado”).

3. **Marcas**
   - CRUD de marcas (logo, descripción corta, URL, orden).
   - Asociación con el catálogo.

4. **Servicios**
   - CRUD de tarjetas de servicio: ícono, título, resumen, texto largo, CTA, orden.

5. **Misión y Visión**
   - Editor WYSIWYG/Markdown.
   - Sección visible en Home.

6. **Categorías Destacadas**
   - Selección de categorías de producto para mostrar en Home.
   - Orden configurable.

---

### 4.3. Catálogo

#### 4.3.1. Categorías y Subcategorías

- CRUD completo:
  - Nombre, slug, descripción, imagen opcional.
  - Padres (estructura jerárquica).
  - Metadatos SEO (title, meta description).
  - Estado (activo/inactivo).
- Orden de despliegue en el sitio.

#### 4.3.2. Productos

- CRUD avanzado:
  - Datos básicos: nombre, slug, SKU, marca, categoría/subcategoría, tags.
  - Contenido: descripción corta, descripción larga.
  - Medios:
    - Galería de imágenes (WebP + JPG).
    - Video(s) embebidos (URL YouTube/Vimeo).
  - Documentos:
    - Fichas técnicas (PDF).
    - Manuales.
    - Brochures.
  - Atributos técnicos:
    - Pares clave/valor (dinámicos por producto).
  - Estado & visibilidad:
    - borrador / publicado / oculto.
    - Flag “destacado”.
  - SEO:
    - meta_title, meta_description, canonical, OG_image.
- Funcionalidades adicionales:
  - Búsqueda interna (por nombre, SKU, texto).
  - Productos relacionados:
    - manual o por categoría/tags.
  - Exportación de lista de productos (CSV/Excel).

---

### 4.4. Proyectos

- CRUD:
  - Título, slug, sector/categoría, año, país/ubicación.
  - Descripción larga.
  - Galería de imágenes.
  - Videos embebidos.
  - Documentos adjuntos (PDF).
  - Metadatos SEO.
  - Estado: borrador / publicado.
- Listado:
  - Filtros por año, categoría, marca (si aplica).
  - Orden por fecha.

---

### 4.5. Blog

> Parte ya se encuentra avanzado en Fase 1; en Fase 2 se consolidan y pulen las funcionalidades.

- Artículos:
  - CRUD: título, slug, resumen, contenido, portada, categoría, tags, autor.
  - Programación de publicación (fecha/hora).
  - Estados: borrador / programado / publicado / archivado.
- Categorías:
  - CRUD de categorías y tags.
- Comentarios (si se habilita:
  - Moderación: pendiente / aprobado / rechazado / spam.
- SEO:
  - Meta title, description, OG tags por artículo.

---

### 4.6. Cotizaciones

**Flujo esperado:**

1. Cliente arma solicitud desde el front.
2. Se genera un registro en BD con:
   - Datos del cliente.
   - Lista de productos, cantidades y notas.
   - Folio único.
3. El panel de Cotizaciones permite:

**Funciones:**
- Listado de solicitudes:
  - Filtros por estado: Nueva, En seguimiento, Cotizada, Cerrada (Ganada/Perdida).
  - Filtros por fecha, cliente, empresa, marca, categoría.
- Detalle de cada cotización:
  - Datos del cliente.
  - Productos seleccionados.
  - Historial de acciones.
- Acciones internas:
  - Asignar a un ejecutivo (usuario del panel).
  - Cambiar estado.
  - Agregar notas internas.
  - Adjuntar PDF de propuesta.
- Exportación:
  - CSV/Excel por rango de fechas.
- Auditoría:
  - Log interno: quién cambió estado, cuándo, y qué notas se agregaron.

---

### 4.7. Contacto

- Bandeja de mensajes:
  - Listado con filtros (estado, motivo, fecha).
- Detalle de cada mensaje:
  - Datos de contacto, motivo, mensaje completo.
  - Estado: nuevo / en proceso / resuelto / cerrado.
- Asignación:
  - Permitir asignar a un responsable (usuario).
- Respuestas rápidas:
  - Plantillas cortas para facilitar respuesta manual.

---

### 4.8. Newsletter

- Listado de suscriptores:
  - Filtros por estado: confirmado / pendiente / baja.
- Importación:
  - Carga de CSV con campos estándar.
- Exportación:
  - Descarga CSV para usar en herramientas externas.
- Plantillas:
  - Administración de plantillas HTML base (para integración futura con Mailchimp/SendGrid/Acumbamail).
- Configuración:
  - Campos mínimos obligatorios y textos legales.

---

### 4.9. SEO & Metadatos

- Configuración global:
  - Título por defecto (prefijo/sufijo).
  - Favicon.
  - Imagen OG general.
- Configuración por página:
  - Home, Catálogo, Proyectos, Blog, Contacto, etc.
- Gestión de:
  - `robots.txt` desde el panel.
  - `sitemap.xml` (generación automática).
- Redirecciones:
  - Alta y edición de redirecciones 301 simples.
- Schema.org:
  - Activar/desactivar:
    - Organization
    - Product
    - BlogPosting
    - BreadcrumbList

---

### 4.10. Google Analytics / Métricas

- Configuración:
  - Campo para Measurement ID (GA4).
- Mostrar en panel (vía API o iframe):
  - Usuarios, sesiones, páginas vistas, tiempo en sitio.
  - Eventos clave: `add_to_quote`, `submit_quote`, `submit_contact`, `subscribe_newsletter`.
- Definición de objetivos/embudos (documentado).

---

### 4.11. Apariencia & Módulos

- Toggles:
  - Activar/desactivar secciones del Home (banners, blog, servicios, aliados, etc.).
- Orden:
  - Reordenar secciones de Home por drag & drop.
- Páginas informativas:
  - Editor de contenido (WYSIWYG/Markdown) para páginas estáticas.
- Vista previa:
  - Previsualizar cambios antes de publicarlos.

---

### 4.12. Usuarios & Roles

- CRUD de usuarios del panel:
  - Alta, edición, baja lógica.
  - Asignación de rol principal.
- Seguridad:
  - Forzar cambio de contraseña inicial.
  - Bloqueo de usuario tras N intentos fallidos (configurable).
- Bitácora de actividad:
  - Registro por usuario: login, cambios críticos, altas/bajas.
- Recuperación de contraseña:
  - Flujo por email con token temporal.

---

### 4.13. Configuración General

- Datos de empresa:
  - Razón social, dirección, teléfonos, emails de routing (ventas@, soporte@, admin@).
- SMTP:
  - Configuración central de correo (host, puerto, usuario, contraseña).
- Integraciones:
  - Analytics, newsletters, envío de archivos.
- Textos legales:
  - Ajustes de políticas de privacidad, términos, cookies.

---

## 5. ALCANCE TÉCNICO

### 5.1. Arquitectura

- Mantener arquitectura **LAMP / MVC personalizado** usada en Fase 1.
- Reutilizar:
  - `includes/config.php`
  - `includes/connection.php`
  - `includes/functions.php`
  - Sistema de `includes` y `partials`.
- Extender módulos admin ya existentes:
  - `admin/login.php`, `admin/logout.php`, `admin/auth_check.php`.
  - Admin de blog, newsletter, topbar.

### 5.2. Seguridad

- Uso de `password_hash()` / `password_verify()`.
- Uso de PDO con prepared statements (ya aplicado).
- CSRF tokens en formularios del panel.
- Control de sesiones:
  - Regeneración de session ID al login.
  - Expiración de sesión por inactividad.
- Filtros por rol y permisos por módulo.
- Logs de operaciones sensibles.

### 5.3. Base de Datos

- Reutilizar tablas definidas en Fase 1:
  - `newsletter_subscriptions`, `contact_messages`, `topbar_messages`,
  - `admin_usuarios`, `blog_articles`, `blog_categories`, `blog_comments`,
  - `marcas`, `productos`, `usos`, `imagenes_x_producto`, `catalogo_producto_documentos`.
- Agregar tablas de soporte si es necesario:
  - `cotizaciones` y sus detalles (`cotizacion_items`).
  - `audit_logs` (bitácora).
  - `redirects` (redirecciones 301).
  - `page_settings` o similar para configuraciones globales.

---

## 6. ENTREGABLES DE LA FASE 2

1. Panel administrativo completo con todos los módulos descritos.
2. Código fuente actualizado en repositorio (Git).
3. Scripts de base de datos para nuevas tablas y alteraciones.
4. Documentación:
   - `README_Aramed_Fase2.md`
   - `MANUAL_ADMIN_FASE2.md` (uso del panel).
   - `DB_CHANGELOG_FASE2.md`.
5. Configuración actualizada en entorno de producción.
6. Capacitación remota (sesión demostrativa del panel).
7. Reporte de cierre Fase 2 (similar al de Fase 1).

---

## 7. CRONOGRAMA PROPUESTO (Fase 2 – 210 h)

> El detalle fino de fechas se ajusta según agenda con el cliente.

- **Semana 1–2**
  - Ajustes de estructura admin base.
  - Módulos: Dashboard, Usuarios & Roles, Configuración.
- **Semana 3–4**
  - Gestor de Inicio (Home).
  - Catálogo: Categorías y Productos (CRUD).
- **Semana 5–6**
  - Módulo Proyectos.
  - Mejoras y consolidación de Blog.
- **Semana 7–8**
  - Módulos Cotizaciones y Contacto.
  - Newsletter avanzado.
- **Semana 9–10**
  - SEO & Metadatos, Apariencia, Analytics, Redirecciones.
  - Hardening de seguridad, QA interno, ajustes finales.
- **Semana 11–12**
  - Pruebas con cliente (UAT).
  - Correcciones.
  - Documentación y capacitación.
  - Cierre formal de Fase 2.

---

## 8. CRITERIOS DE ACEPTACIÓN

La Fase 2 se considerará **entregada y completada** cuando:

1. Todos los módulos descritos estén accesibles desde `/admin/` con control de roles.
2. Los contenidos (productos, blog, proyectos, banners, servicios) puedan gestionarse sin intervención de desarrollo.
3. Los flujos de cotización, contacto y newsletter funcionen extremo a extremo:
   - Almacenamiento en BD.
   - Visualización y gestión en panel.
   - Notificaciones por correo.
4. SEO básico (metadatos, sitemap, robots) sea gestionable desde el panel o documentado.
5. Se haya realizado:
   - Prueba UAT con Aramed.
   - Ajustes de la ronda de validación.
   - Entrega de documentación y capacitación básica.
6. Se firme el **Reporte de Cierre – Fase 2**.

---

## 9. RIESGOS Y SUPUESTOS

**Supuestos:**
- La infraestructura (hosting, dominio, SSL) se mantiene estable y disponible.
- Aramed facilita oportunamente contenidos (textos, imágenes, lineamientos de marca).
- No se solicitan cambios de alcance mayores a lo especificado.

**Riesgos:**
- Cambios de alcance significativos (nuevos módulos o lógicas) pueden impactar tiempo/costo.
- Retrasos en validación/UAT pueden diferir la fecha de cierre.
- Integraciones con terceros (Mailchimp/SendGrid, etc.) dependen de accesos y APIs provistos por el cliente.

---

## 10. CONTACTO

**Cliente – Aramed y Laboratorios**  
- Email: marketing@aramedylaboratorio.com  
- Atención: atencionacliente@aramedylaboratorio.com  
- Teléfono: (800) 999-0407  

**Desarrollador – IDEAMIA – Tech**  
- Responsable: Ing. Jorge Alberto Plascencia Correa  
- Email: soporte@ideamia.com.mx  

---



Fase 2 – Backend Administración (210 hrs) 

1. Arquitectura del Panel de Control
•	Configuración de MVC para el panel administrativo.
•	Creación de estructura modular (Dashboard, Catálogo, Blog, Cotizaciones, Configuración, etc.).
•	Sistema de autenticación (login seguro con hashing).
•	Gestión de roles y permisos (RBAC: Admin, Ventas, Marketing, Soporte, Analista).

2. Dashboard
•	KPIs dinámicos (cotizaciones hoy/mes, contactos, suscriptores, productos activos).
•	Gráficas de tendencias (cotizaciones, tráfico si se habilita Analytics).
•	Alertas de formularios pendientes y errores de envío.

3. Gestor de Contenidos del Inicio
•	CRUD de banners rotatorios (imagen/video, CTA, orden, estado).
•	CRUD de marcas (logo, descripción, orden, URL slug).
•	CRUD de servicios (título, imagen, resumen, cuerpo, orden).
•	Configuración de categorías destacadas y bloques de Misión/Visión con WYSIWYG.

4. Catálogo
•	CRUD de categorías y subcategorías con jerarquías y metadatos.
•	CRUD completo de productos con galería de imágenes/videos/PDFs.
•	SEO por producto: title, meta description, OG/Twitter Cards.
•	Configuración de productos relacionados y reglas automáticas.

5. Proyectos
•	CRUD de proyectos con galería, videos, SEO, adjuntos.

6. Blog
•	CRUD de posts con portada, cuerpo, categorías/tags, autor, SEO.
•	Programación de publicaciones.
•	(Opcional) Moderación de comentarios.

7. Cotizaciones
•	Bandeja con filtros por estado (Nueva, En seguimiento, Cotizada, Cerrada).
•	Vista detalle con productos, cantidades, notas y datos del cliente.
•	Bitácora de acciones: asignación a ejecutivo, adjuntar PDF, cambiar estado.
•	Plantillas de correo y PDF para propuestas.
•	Exportación a CSV/Excel y reportes por periodo, marca, categoría.

8. Contacto
•	Bandeja de mensajes con motivo y estado.
•	Reasignación de mensajes a Ventas/Soporte.
•	Respuestas rápidas/macros.

9. Newsletter
•	Gestión de listas de suscriptores (importación/exportación).
•	Plantillas HTML (editor drag & drop o simple).
•	Integración con proveedor externo (Mailchimp/SendGrid).
•	Configuración de doble opt-in.

10. SEO & Metadatos
•	Configuración de metadatos globales (favicon, OG image).
•	Sitemap.xml y robots.txt administrables.
•	Redirecciones 301 y manejo de slugs duplicados.
•	Integración de Schema.org (Organization, Product, BlogPosting).

11. Google Analytics
•	Configuración de Measurement ID (GA4).
•	Definición de eventos (add_to_quote, submit_quote, subscribe_newsletter).
•	Panel embebido con métricas clave.

12. Apariencia & Módulos
•	Activar/desactivar secciones y módulos con toggles.
•	Reordenar secciones por drag & drop.
•	Editor de bloques para páginas informativas.

13. Usuarios & Configuración
•	CRUD de usuarios y roles con permisos finos.
•	Bitácora de actividad (audit logs).
•	Configuración de empresa (SMTP, integraciones, plantillas de correo, textos legales).

14. Pruebas y Optimización
•	Validación funcional de todos los módulos.
•	Corrección de errores y pruebas de roles.
