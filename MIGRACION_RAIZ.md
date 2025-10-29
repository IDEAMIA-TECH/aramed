# Guía de Migración: De Subcarpeta a Raíz del Dominio

## 📋 Resumen de Cambios

Este documento describe los cambios necesarios para mover el proyecto desde:
- **Ubicación Actual:** `https://aramedylaboratorio.com/NUEVO/aramed/public_html/`
- **Ubicación Nueva:** `https://aramedylaboratorio.com/`

---

## ✅ Cambios Realizados en el Código

### 1. **Configuración Principal (config.php)**
   - ✅ **Actualizado:** `SITE_URL` de `https://aramedylaboratorio.com/NUEVO/aramed/public_html` a `https://aramedylaboratorio.com`
   - **Archivo:** `public_html/includes/config.php` (línea 55)

### 2. **Archivos Verificados (No Requieren Cambios)**
   - ✅ `sitemap.xml` - Ya está usando URLs del dominio raíz
   - ✅ `robots.txt` - Ya está usando URLs del dominio raíz
   - ✅ `.htaccess` - Configurado correctamente con `RewriteBase /`

---

## 📝 Pasos para Completar la Migración

### Paso 1: Respaldar el Sitio Actual
```bash
# En el servidor, crear un backup completo
tar -czf backup-aramed-$(date +%Y%m%d).tar.gz /ruta/actual/NUEVO/aramed/
```

### Paso 2: Mover Archivos Físicos

**Opción A: Si tienes acceso SSH al servidor:**
```bash
# Mover todos los archivos de public_html a la raíz
mv /home/usuario/public_html/NUEVO/aramed/public_html/* /home/usuario/public_html/
mv /home/usuario/public_html/NUEVO/aramed/public_html/.[^.]* /home/usuario/public_html/
```

**Opción B: Usando FTP/cPanel:**
1. Acceder al directorio `/NUEVO/aramed/public_html/`
2. Seleccionar todos los archivos y carpetas
3. Copiar/mover a la raíz `/public_html/` (o la carpeta raíz de tu dominio)

### Paso 3: Verificar Permisos de Archivos
```bash
# Asegurar permisos correctos
chmod 755 /home/usuario/public_html
find /home/usuario/public_html -type d -exec chmod 755 {} \;
find /home/usuario/public_html -type f -exec chmod 644 {} \;
```

### Paso 4: Actualizar Configuración del Servidor

#### Para Apache:
- Verificar que el `DocumentRoot` apunte a la nueva ubicación
- Verificar que `.htaccess` esté activo
- Reiniciar Apache si es necesario

#### Para cPanel:
1. Ir a **Dominios** → **Dominios Adicionales** o **Subdominios**
2. Verificar que el `Document Root` esté configurado correctamente
3. Verificar que el dominio apunte a la raíz

### Paso 5: Actualizar Tareas Programadas (Cron Jobs)

Si tienes cron jobs configurados, actualiza las rutas:

**Antes:**
```
https://aramedylaboratorio.com/NUEVO/aramed/public_html/cron/expire_topbar_messages.php
```

**Después:**
```
https://aramedylaboratorio.com/cron/expire_topbar_messages.php
```

O si usas rutas del servidor:
```bash
# Antes
/usr/bin/php /home/usuario/public_html/NUEVO/aramed/public_html/cron/expire_topbar_messages.php

# Después
/usr/bin/php /home/usuario/public_html/cron/expire_topbar_messages.php
```

### Paso 6: Verificar Funcionalidad

1. **Página Principal:**
   - ✅ `https://aramedylaboratorio.com/`
   
2. **Assets (CSS, JS, Imágenes):**
   - ✅ `https://aramedylaboratorio.com/assets/css/main.css`
   - ✅ `https://aramedylaboratorio.com/assets/js/main.js`
   - ✅ `https://aramedylaboratorio.com/assets/images/`

3. **Formularios:**
   - ✅ Newsletter funciona correctamente
   - ✅ Formulario de contacto funciona

4. **Admin Panel:**
   - ✅ `https://aramedylaboratorio.com/admin/`
   - ✅ Login funciona correctamente

5. **SEO:**
   - ✅ `https://aramedylaboratorio.com/sitemap.xml`
   - ✅ `https://aramedylaboratorio.com/robots.txt`

---

## ⚠️ Consideraciones Importantes

### 1. **Redirecciones 301**
Si necesitas mantener acceso desde la ruta antigua temporalmente:
```apache
# Agregar en .htaccess (temporalmente)
Redirect 301 /NUEVO/aramed/public_html/ https://aramedylaboratorio.com/
```

### 2. **Base de Datos**
- ✅ Las conexiones a la base de datos **NO** requieren cambios
- Las rutas en la BD se generan dinámicamente usando `SITE_URL`

### 3. **Sesiones y Cookies**
- Limpiar cookies del sitio después de la migración
- Verificar que las sesiones funcionen correctamente

### 4. **Cache del Navegador**
- Limpiar cache del navegador después de la migración
- Considerar purgar cache del servidor/CDN si lo usas

### 5. **Google Search Console**
- Actualizar la propiedad en Google Search Console
- Reenviar el sitemap
- Monitorear errores de rastreo

---

## 🔧 Archivos que NO Requieren Cambios

Estos archivos usan rutas relativas o constantes dinámicas:

- ✅ Todos los archivos PHP (usan `imageUrl()`, `assetUrl()`, etc.)
- ✅ Archivos CSS (rutas relativas)
- ✅ Archivos JavaScript (rutas relativas o dinámicas)
- ✅ Estructura de carpetas
- ✅ Configuración de base de datos

---

## 📊 Checklist de Verificación Post-Migración

- [ ] Todos los archivos movidos correctamente
- [ ] `config.php` actualizado con nueva URL
- [ ] Página principal carga correctamente
- [ ] Todas las imágenes cargan
- [ ] CSS y JavaScript funcionan
- [ ] Formularios funcionan (Newsletter, Contacto)
- [ ] Admin panel accesible
- [ ] Login funciona
- [ ] Cron jobs actualizados (si aplica)
- [ ] Sitemap accesible
- [ ] Robots.txt accesible
- [ ] Sin errores 404
- [ ] Sin errores 500
- [ ] HTTPS funcionando correctamente
- [ ] Redirecciones configuradas (si necesario)

---

## 🆘 Problemas Comunes y Soluciones

### Error 404 en todas las páginas
- **Causa:** DocumentRoot no configurado correctamente
- **Solución:** Verificar configuración del servidor web

### Imágenes no cargan
- **Causa:** Rutas incorrectas o permisos
- **Solución:** Verificar permisos de carpeta `/assets/images/`

### CSS/JS no funciona
- **Causa:** Cache del navegador o rutas incorrectas
- **Solución:** Limpiar cache y verificar rutas en el código fuente

### Formularios no funcionan
- **Causa:** Rutas incorrectas en `action` del formulario
- **Solución:** Verificar que los handlers estén en la ubicación correcta

---

## 📞 Soporte

Si encuentras problemas durante la migración, verifica:
1. Permisos de archivos y carpetas
2. Configuración del servidor web
3. Logs de errores de PHP/Apache
4. Configuración de DNS (si aplica)

---

**Fecha de Creación:** 2025-01-XX  
**Versión:** 1.0  
**Estado:** ✅ Cambios en código completados - Pendiente migración física de archivos
