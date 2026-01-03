# 📋 SCRIPTS DE BASE DE DATOS - FASE 2

Este directorio contiene los scripts SQL necesarios para la Fase 2 del proyecto.

## 🚀 INSTALACIÓN RÁPIDA (RECOMENDADO)

**Para crear todas las tablas de una vez, ejecuta el archivo consolidado:**

```sql
source database/fase2/00_create_all_tables.sql;
```

O desde línea de comandos:
```bash
mysql -u usuario -p nombre_base_datos < database/fase2/00_create_all_tables.sql
```

Este archivo incluye:
- ✅ Todas las tablas en el orden correcto
- ✅ Todas las dependencias resueltas
- ✅ Datos iniciales (configuración, permisos, plantillas)
- ✅ Triggers y procedimientos almacenados

## 📝 Instalación Individual (Opcional)

Si prefieres ejecutar los scripts individualmente, sigue este orden:

1. **05_create_rbac_tables.sql** - Crea las tablas para RBAC (permisos, roles, auditoría)
2. **05_populate_permissions.sql** - Pobla los permisos iniciales y los asigna a roles
3. **06_create_configuracion_table.sql** - Tabla de configuración del sitio
4. **01_create_home_tables.sql** - Tablas para gestor de Home
5. **02_create_proyectos_tables.sql** - Tablas para módulo de Proyectos
6. **03_create_cotizaciones_tables.sql** - Tablas para cotizaciones avanzado
7. **04_add_blog_programacion.sql** - Agrega campo de programación al blog
8. **08_create_seo_tables.sql** - Tablas para SEO y metadatos
9. **09_create_newsletter_templates.sql** - Tabla de plantillas de newsletter
10. **07_migrate_cotizaciones.sql** - (Opcional) Migra datos de newsletter a cotizaciones

## 🚀 Instrucciones de Ejecución

### Opción 1: Desde phpMyAdmin
1. Acceder a phpMyAdmin
2. Seleccionar la base de datos del proyecto
3. Ir a la pestaña "SQL"
4. Copiar y pegar el contenido del script
5. Ejecutar

### Opción 2: Desde línea de comandos
```bash
mysql -u usuario -p nombre_base_datos < 05_create_rbac_tables.sql
mysql -u usuario -p nombre_base_datos < 05_populate_permissions.sql
```

### Opción 3: Desde PHP (desarrollo)
```php
$pdo = getDB();
$sql = file_get_contents('database/fase2/05_create_rbac_tables.sql');
$pdo->exec($sql);
```

## ✅ Verificación

Después de ejecutar los scripts, verificar que se crearon las tablas:

```sql
SHOW TABLES LIKE '%permisos%';
SHOW TABLES LIKE '%audit%';
DESCRIBE admin_usuarios; -- Verificar nuevos campos
```

## 📊 Estructura Creada

### Tablas Nuevas (21 tablas):

**RBAC:**
- `permisos` - Permisos disponibles en el sistema
- `rol_permisos` - Relación entre roles y permisos
- `audit_logs` - Bitácora de actividad

**Configuración:**
- `configuracion` - Configuración dinámica del sitio

**Home:**
- `home_banners` - Banners/Hero del inicio
- `home_productos_destacados` - Productos destacados
- `home_servicios` - Servicios del home
- `home_mision_vision` - Misión y Visión
- `home_categorias_destacadas` - Categorías destacadas

**Proyectos:**
- `proyectos` - Información principal de proyectos
- `proyecto_imagenes` - Galería de imágenes
- `proyecto_videos` - Videos embebidos
- `proyecto_documentos` - Documentos adjuntos

**Cotizaciones:**
- `cotizaciones` - Cotizaciones principales
- `cotizacion_items` - Items de cada cotización
- `cotizacion_auditoria` - Historial de cambios

**SEO:**
- `seo_config` - Configuración SEO global y por página
- `seo_metadatos` - Metadatos por entidad
- `redirects` - Redirecciones 301/302

**Newsletter:**
- `newsletter_templates` - Plantillas HTML de newsletter

### Campos Agregados a `admin_usuarios`:
- `forzar_cambio_password` - Flag para forzar cambio de contraseña
- `intentos_fallidos` - Contador de intentos fallidos
- `bloqueado_hasta` - Fecha/hora de bloqueo temporal
- `ultimo_cambio_password` - Fecha del último cambio
- `token_recuperacion` - Token para recuperación de contraseña
- `token_expira` - Expiración del token

## 🔐 Roles Definidos

- **admin** - Todos los permisos
- **marketing** - Home, Blog, Newsletter, SEO, Catálogo (ver/editar)
- **ventas** - Catálogo, Cotizaciones, Contacto (ver)
- **soporte** - Cotizaciones, Contacto
- **analista** - Solo lectura (ver) en todos los módulos
- **editor** - Home, Blog, Catálogo, Proyectos (sin configuración)

## ⚠️ Notas Importantes

- Los scripts usan `IF NOT EXISTS` y `ON DUPLICATE KEY UPDATE` para evitar errores si se ejecutan múltiples veces
- El rol `admin` tiene todos los permisos automáticamente
- Los permisos se pueden modificar después desde el panel admin

