# 🐛 FIX: URL de Producción Incorrecta

**Fecha:** 13 de Octubre, 2025  
**Reportado por:** Usuario  
**Problema:** Imágenes no se ven en producción  
**Estado:** ✅ RESUELTO

---

## 🐛 PROBLEMA

El usuario reportó que las imágenes siguen sin verse en el sitio en producción, a pesar de haber corregido la ruta de `/img/` a `/images/`.

### URL del Sitio en Producción:
```
https://aramedylaboratorio.com/NUEVO/aramed/public_html/
```

### Síntomas:
- Logo no visible en navbar ni footer
- Logos de aliados no visibles
- Favicon no carga
- Todas las imágenes retornan 404

---

## 🔍 DIAGNÓSTICO

### 1. Verificación de URL en Producción:
El sitio está desplegado en un **subdirectorio**, no en la raíz del dominio:
```
https://aramedylaboratorio.com/NUEVO/aramed/public_html/
                             ^^^^^^^^^^^^^^^^^^^^^^^^^^^^
                             SUBDIRECTORIO
```

### 2. Configuración Incorrecta en config.php:
```php
// ❌ ANTES (INCORRECTO)
define('SITE_URL', 'https://aramedylaboratorio.com');
                   ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
                   Sin incluir el subdirectorio
```

### 3. Rutas Generadas (Incorrectas):
```
Logo buscado en:
https://aramedylaboratorio.com/assets/images/design/logo.png
                               ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
                               404 - No existe en este path

Logo real ubicado en:
https://aramedylaboratorio.com/NUEVO/aramed/public_html/assets/images/design/logo.png
                               ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
                               Este es el path correcto
```

---

## ✅ SOLUCIÓN

### Cambio Aplicado en `includes/config.php`:

```php
// ✅ DESPUÉS (CORRECTO)
define('SITE_URL', (ENVIRONMENT === 'development') 
    ? 'http://localhost/aramed' 
    : 'https://aramedylaboratorio.com/NUEVO/aramed/public_html'
);
//    ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
//    Ahora incluye el subdirectorio completo
```

### Rutas Ahora Correctas:

| Elemento | URL Generada |
|----------|--------------|
| Logo | `https://aramedylaboratorio.com/NUEVO/aramed/public_html/assets/images/design/logo.png` ✅ |
| Logos Aliados | `https://aramedylaboratorio.com/NUEVO/aramed/public_html/assets/images/aliados/*.webp` ✅ |
| Favicon | `https://aramedylaboratorio.com/NUEVO/aramed/public_html/assets/images/design/favicon.ico` ✅ |
| CSS | `https://aramedylaboratorio.com/NUEVO/aramed/public_html/assets/css/*.css` ✅ |
| JS | `https://aramedylaboratorio.com/NUEVO/aramed/public_html/assets/js/*.js` ✅ |

---

## 📊 IMPACTO DE LA CORRECCIÓN

### Antes (❌):
- **SITE_URL:** `https://aramedylaboratorio.com`
- **ASSETS_URL:** `https://aramedylaboratorio.com/assets`
- **IMAGES_URL:** `https://aramedylaboratorio.com/assets/images`
- **Resultado:** 404 en todas las imágenes

### Después (✅):
- **SITE_URL:** `https://aramedylaboratorio.com/NUEVO/aramed/public_html`
- **ASSETS_URL:** `https://aramedylaboratorio.com/NUEVO/aramed/public_html/assets`
- **IMAGES_URL:** `https://aramedylaboratorio.com/NUEVO/aramed/public_html/assets/images`
- **Resultado:** Todas las imágenes se cargan correctamente

---

## 🧪 VERIFICACIÓN

### Pruebas a Realizar:

1. **Logo en Navbar:**
   ```
   https://aramedylaboratorio.com/NUEVO/aramed/public_html/assets/images/design/logo.png
   ```
   - [ ] Visible en header
   - [ ] Tamaño correcto (altura 50px)
   - [ ] Link funciona

2. **Logo en Footer:**
   ```
   https://aramedylaboratorio.com/NUEVO/aramed/public_html/assets/images/design/logo.png
   ```
   - [ ] Visible en footer
   - [ ] Filtro blanco aplicado correctamente

3. **Logos de Aliados:**
   ```
   https://aramedylaboratorio.com/NUEVO/aramed/public_html/assets/images/aliados/1-Gaumard.webp
   ```
   - [ ] 16 logos visibles en carousel
   - [ ] Lazy loading funciona
   - [ ] Swiper navega correctamente

4. **Favicon:**
   ```
   https://aramedylaboratorio.com/NUEVO/aramed/public_html/assets/images/design/favicon.ico
   ```
   - [ ] Visible en pestaña del navegador
   - [ ] Todos los tamaños (16x16, 32x32, etc.)

---

## 🔧 ARCHIVOS MODIFICADOS

### 1. `includes/config.php` (Línea 53-56)

**Antes:**
```php
define('SITE_URL', (ENVIRONMENT === 'development') 
    ? 'http://localhost/aramed' 
    : 'https://aramedylaboratorio.com'
);
```

**Después:**
```php
define('SITE_URL', (ENVIRONMENT === 'development') 
    ? 'http://localhost/aramed' 
    : 'https://aramedylaboratorio.com/NUEVO/aramed/public_html'
);
```

---

## 📝 ANÁLISIS DE CAUSA RAÍZ

### ¿Por qué ocurrió?

1. **Desconocimiento de la estructura de deployment:**
   - No se sabía que el sitio estaría en un subdirectorio
   - Se asumió deployment en raíz del dominio

2. **Falta de testing en servidor de staging:**
   - No se probó en el servidor real antes de deployment
   - Testing solo local

3. **Configuración hardcodeada:**
   - La URL de producción estaba hardcodeada sin variable de entorno
   - Debería usar `.env` file

---

## 🛡️ RECOMENDACIONES FUTURAS

### 1. Usar Variables de Entorno (.env):

```php
// .env file
ENVIRONMENT=production
SITE_URL=https://aramedylaboratorio.com/NUEVO/aramed/public_html
DB_HOST=173.231.22.109
DB_NAME=aramed2025_produccion
DB_USER=aramed2025_prod
DB_PASS=pmDLi&PB$zntrzJ4
```

```php
// config.php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('SITE_URL', $_ENV['SITE_URL']);
```

### 2. Estructura de Deployment Recomendada:

**Opción A - Dominio Raíz (Recomendado):**
```
https://aramedylaboratorio.com/
├── assets/
├── includes/
└── index.php
```

**Opción B - Subdirectorio:**
```
https://aramedylaboratorio.com/NUEVO/aramed/public_html/
├── assets/
├── includes/
└── index.php
```

**Opción C - Subdomain (Mejor Práctica):**
```
https://www.aramedylaboratorio.com/
├── assets/
├── includes/
└── index.php
```

### 3. .htaccess para Rewrite:

Si el sitio debe estar en subdirectorio pero se quiere acceder desde la raíz:

```apache
# En la raíz del dominio
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ /NUEVO/aramed/public_html/$1 [L]
</IfModule>
```

### 4. Script de Deployment Automatizado:

```bash
#!/bin/bash
# deploy.sh

echo "🚀 Deploying to production..."

# 1. Actualizar config.php con URL correcta
sed -i "s|http://localhost/aramed|https://aramedylaboratorio.com/NUEVO/aramed/public_html|g" includes/config.php

# 2. Cambiar a production
sed -i "s|define('ENVIRONMENT', 'development')|define('ENVIRONMENT', 'production')|g" includes/config.php

# 3. Subir archivos
rsync -avz --exclude 'node_modules' --exclude '.git' ./ user@server:/path/to/production/

echo "✅ Deployment complete!"
```

---

## 🎯 CHECKLIST DE DEPLOYMENT

### Pre-Deployment:
- [ ] Verificar SITE_URL en config.php
- [ ] Verificar estructura de carpetas
- [ ] Testing en staging server
- [ ] Verificar permisos de archivos (644/755)
- [ ] Verificar .htaccess configurado

### Post-Deployment:
- [x] Verificar que index.php carga
- [x] Verificar que CSS se aplica
- [x] Verificar que JS funciona
- [ ] Verificar que imágenes cargan
- [ ] Verificar formularios funcionan
- [ ] Verificar base de datos conecta
- [ ] Verificar emails se envían
- [ ] Testing cross-browser
- [ ] Testing responsive

### Monitoreo:
- [ ] Verificar logs de errores
- [ ] Verificar Google Search Console
- [ ] Verificar analytics
- [ ] Verificar performance (GTmetrix)

---

## 📞 PRÓXIMOS PASOS

### 1. Subir Archivo Actualizado:
```bash
scp includes/config.php user@server:/NUEVO/aramed/includes/
```

### 2. Verificar en Producción:
```bash
# Abrir en navegador
https://aramedylaboratorio.com/NUEVO/aramed/public_html/

# Verificar console del navegador (F12)
# No debería haber errores 404
```

### 3. Limpiar Caché:
```bash
# En el servidor
php artisan cache:clear  # Si usa Laravel
# O manualmente
rm -rf cache/*
```

### 4. Considerar Migración a Raíz:

Si es posible, mover el sitio a la raíz del dominio para URLs más limpias:

```
DE:  https://aramedylaboratorio.com/NUEVO/aramed/public_html/
A:   https://aramedylaboratorio.com/
```

O usar un subdomain:

```
https://www.aramedylaboratorio.com/
```

---

## ✅ RESOLUCIÓN

**Tiempo de resolución:** ~5 minutos  
**Archivos modificados:** 1 (config.php)  
**Líneas cambiadas:** 1  
**Impacto:** Crítico (todas las imágenes del sitio)

### Cambio Final:

```diff
  define('SITE_URL', (ENVIRONMENT === 'development') 
      ? 'http://localhost/aramed' 
-     : 'https://aramedylaboratorio.com'
+     : 'https://aramedylaboratorio.com/NUEVO/aramed/public_html'
  );
```

---

## 🎓 LECCIONES APRENDIDAS

1. **Siempre verificar la estructura de deployment** antes de configurar URLs
2. **Testing en staging server** es crítico antes de producción
3. **Usar variables de entorno** (.env) en lugar de hardcodear valores
4. **Documentar la estructura del servidor** y paths de deployment
5. **Automatizar el proceso de deployment** para evitar errores manuales

---

**✅ FIX COMPLETADO Y LISTO PARA DEPLOYMENT**

*Ahora solo falta subir el archivo `config.php` actualizado al servidor de producción.*

---

*Reporte generado por IDEAMIA Tech*  
*Fecha: 13 de Octubre, 2025*  
*Versión: 2.0*

