# 🚀 INSTRUCCIONES DE DEPLOYMENT - LANDING PAGE

**Fecha:** 13 de Octubre 2025  
**Proyecto:** Aramed y Laboratorios - Landing Page MVP  
**Servidor:** aramedylaboratorio.com  
**Path Producción:** `/NUEVO/aramed/public_html/`

---

## ⚡ DEPLOYMENT RÁPIDO

### PASO 1: Subir Imágenes al Servidor

```bash
# Desde tu máquina local
cd /Users/gorila/Desktop/CLONE/GIT/aramed/

# Opción A: Via FTP (FileZilla, Cyberduck, etc.)
# 1. Conectar al servidor FTP
# 2. Navegar a: /NUEVO/aramed/public_html/assets/
# 3. Subir la carpeta "images" completa

# Opción B: Via SFTP/SCP (línea de comandos)
scp -r public_html/assets/images/ \
    usuario@aramedylaboratorio.com:/home/usuario/public_html/NUEVO/aramed/public_html/assets/

# Opción C: Via rsync (recomendado - solo archivos nuevos)
rsync -avz --progress public_html/assets/images/ \
    usuario@aramedylaboratorio.com:/home/usuario/public_html/NUEVO/aramed/public_html/assets/images/
```

### PASO 2: Verificar Permisos

```bash
# Conectarse via SSH
ssh usuario@aramedylaboratorio.com

# Navegar al directorio
cd /home/usuario/public_html/NUEVO/aramed/public_html/assets/images/

# Establecer permisos correctos
find . -type d -exec chmod 755 {} \;  # Directorios
find . -type f -exec chmod 644 {} \;  # Archivos

# Verificar propiedad
chown -R usuario:usuario .
```

### PASO 3: Validar URLs

Abrir en el navegador y verificar que las imágenes se carguen:

```
https://aramedylaboratorio.com/NUEVO/aramed/public_html/assets/images/design/logo.png
https://aramedylaboratorio.com/NUEVO/aramed/public_html/assets/images/hero/hero-victoria-s2200.jpg
https://aramedylaboratorio.com/NUEVO/aramed/public_html/assets/images/aliados/ally-gaumard.webp
https://aramedylaboratorio.com/NUEVO/aramed/public_html/assets/images/productos/simulador-hal-s5301.webp
```

### PASO 4: Limpiar Caché

```bash
# Si usas Cloudflare
# Ir a: Caching > Purge Everything

# Si usas cPanel
# Ir a: Software > Select PHP Version > Reset Cache

# Si usas .htaccess (ya configurado en el proyecto)
# Las cabeceras de caché ya están optimizadas
```

---

## 📁 ESTRUCTURA DE ARCHIVOS A SUBIR

```
public_html/assets/images/
├── design/ (596 KB)
│   ├── logo.png
│   ├── logo-og.png
│   ├── logo-optimized.png
│   ├── logo-email.png
│   ├── favicon.ico
│   ├── favicon-16x16.png
│   ├── favicon-32x32.png
│   ├── apple-touch-icon.png
│   ├── android-chrome-192x192.png
│   └── android-chrome-512x512.png
│
├── aliados/ (276 KB)
│   └── [20 archivos .webp]
│
├── productos/ (2.8 MB)
│   └── [8 archivos: 4 .jpg + 4 .webp]
│
└── hero/ (3.0 MB)
    └── [8 archivos: 5 .jpg + 3 .webp]
```

**Total a subir:** ~6.7 MB (46 archivos)

---

## ✅ CHECKLIST DE VALIDACIÓN POST-DEPLOYMENT

### Imágenes
- [ ] Logo visible en header
- [ ] Logo visible en footer
- [ ] Favicon visible en pestaña del navegador
- [ ] Hero slideshow carga todas las imágenes
- [ ] Carrusel de aliados muestra todos los logos
- [ ] Productos destacados muestran imágenes
- [ ] No hay imágenes rotas (404)

### Performance
- [ ] Imágenes WebP se cargan en Chrome/Firefox/Safari
- [ ] Fallback JPG funciona en navegadores viejos
- [ ] Lazy loading funciona correctamente
- [ ] LCP < 2.5 segundos (Lighthouse)
- [ ] Performance Score > 85 (Lighthouse)

### SEO
- [ ] Meta og:image carga correctamente
- [ ] Twitter Card image visible
- [ ] Favicon aparece en resultados de búsqueda
- [ ] Alt text presente en todas las imágenes

### Responsive
- [ ] Imágenes se ven bien en Desktop (1920x1080)
- [ ] Imágenes se ven bien en Tablet (768x1024)
- [ ] Imágenes se ven bien en Mobile (375x667)
- [ ] Hero no se distorsiona en pantallas pequeñas

---

## 🔧 TROUBLESHOOTING

### Problema: Imágenes no se ven (404)

**Solución 1: Verificar paths**
```bash
# En el servidor
cd /home/usuario/public_html/NUEVO/aramed/public_html/assets/
ls -la images/

# Debe mostrar:
# drwxr-xr-x design
# drwxr-xr-x hero
# drwxr-xr-x aliados
# drwxr-xr-x productos
```

**Solución 2: Verificar config.php**
```php
// Debe estar en modo producción
define('ENVIRONMENT', 'production');

// URL debe incluir subdirectorio
define('SITE_URL', 'https://aramedylaboratorio.com/NUEVO/aramed/public_html');

// Images URL debe apuntar a /images/
define('IMAGES_URL', ASSETS_URL . '/images');
```

**Solución 3: Verificar .htaccess**
```apache
# Debe permitir acceso a imágenes
<FilesMatch "\.(jpg|jpeg|png|gif|webp|svg|ico)$">
    Allow from all
</FilesMatch>
```

### Problema: Imágenes muy lentas

**Solución 1: Verificar compresión GZIP**
```bash
# Test con curl
curl -H "Accept-Encoding: gzip" -I https://aramedylaboratorio.com/NUEVO/aramed/public_html/assets/images/hero/hero-hal-s5301.jpg

# Debe incluir:
# Content-Encoding: gzip
```

**Solución 2: Verificar caché**
```bash
# Test con curl
curl -I https://aramedylaboratorio.com/NUEVO/aramed/public_html/assets/images/design/logo.png

# Debe incluir:
# Cache-Control: public, max-age=31536000
```

**Solución 3: Considerar CDN**
```
Cloudflare (gratis): https://www.cloudflare.com/
BunnyCDN: https://bunny.net/
AWS CloudFront: https://aws.amazon.com/cloudfront/
```

### Problema: WebP no funciona en Safari antiguo

**No es un problema:** El código ya incluye fallback JPG automático mediante `<picture>` elements.

```html
<picture>
    <source srcset="imagen.webp" type="image/webp">
    <img src="imagen.jpg" alt="..."> <!-- Se usa automáticamente si WebP no es soportado -->
</picture>
```

---

## 📊 TESTING POST-DEPLOYMENT

### 1. Performance Testing

**Google Lighthouse (Chrome DevTools)**
```
1. Abrir Chrome
2. F12 > Lighthouse tab
3. Seleccionar "Performance"
4. Click "Analyze page load"

Target Scores:
✅ Performance: > 85
✅ Accessibility: > 90
✅ Best Practices: > 90
✅ SEO: > 90
```

**GTmetrix**
```
1. Ir a: https://gtmetrix.com/
2. Ingresar URL: https://aramedylaboratorio.com/NUEVO/aramed/public_html/
3. Click "Analyze"

Target Scores:
✅ Performance Grade: A
✅ Structure Grade: A
✅ LCP: < 2.5s
✅ TBT: < 300ms
```

### 2. SEO Validation

**Open Graph (Facebook)**
```
1. Ir a: https://developers.facebook.com/tools/debug/
2. Ingresar URL
3. Click "Debug"
4. Verificar que og:image se vea correctamente
```

**Twitter Card**
```
1. Ir a: https://cards-dev.twitter.com/validator
2. Ingresar URL
3. Click "Preview card"
4. Verificar que twitter:image se vea correctamente
```

**Google Rich Results**
```
1. Ir a: https://search.google.com/test/rich-results
2. Ingresar URL
3. Verificar que Schema.org Organization se detecte
```

### 3. Responsive Testing

**Chrome DevTools**
```
1. F12 > Toggle Device Toolbar (Ctrl+Shift+M)
2. Probar dispositivos:
   - iPhone SE (375x667)
   - iPhone 12 Pro (390x844)
   - iPad (768x1024)
   - Desktop (1920x1080)
```

**Real Device Testing (Recomendado)**
- iPhone (Safari)
- Android (Chrome)
- iPad (Safari)
- Desktop (Chrome, Firefox, Edge)

---

## 🎯 OPTIMIZACIONES FUTURAS

### CDN (Content Delivery Network)

**Cloudflare (Gratis)**
```
1. Crear cuenta en cloudflare.com
2. Agregar sitio aramedylaboratorio.com
3. Cambiar nameservers en el registrador de dominio
4. Activar:
   - Auto Minify (HTML, CSS, JS)
   - Brotli compression
   - Polish (WebP conversion automática)
```

### Image CDN Dedicado

**BunnyCDN**
```
1. Crear cuenta en bunny.net
2. Crear Pull Zone
3. Configurar origin: aramedylaboratorio.com
4. Actualizar URLs en config.php:
   define('CDN_URL', 'https://aramed.b-cdn.net');
```

### AVIF (Next-gen format)

```html
<!-- Agregar AVIF antes de WebP -->
<picture>
    <source srcset="imagen.avif" type="image/avif">
    <source srcset="imagen.webp" type="image/webp">
    <img src="imagen.jpg" alt="...">
</picture>
```

---

## 📞 SOPORTE

Si encuentras problemas:

1. **Verificar logs de error del servidor**
   ```bash
   tail -f /home/usuario/public_html/error_log
   ```

2. **Verificar Console del navegador**
   ```
   F12 > Console tab
   Buscar errores 404 o CORS
   ```

3. **Contactar soporte técnico del hosting**
   - Informar sobre problemas con permisos o .htaccess
   - Solicitar verificación de mod_rewrite y mod_deflate

---

## ✅ SIGN-OFF DEPLOYMENT

Una vez completado el deployment:

- [ ] Todas las imágenes cargan correctamente
- [ ] Performance Score > 85 (Lighthouse)
- [ ] No hay errores en Console del navegador
- [ ] Responsive funciona en todos los dispositivos
- [ ] SEO tags validados (Open Graph, Twitter Card)
- [ ] Caché configurado correctamente
- [ ] Backup realizado

---

**🎉 ¡DEPLOYMENT COMPLETADO!**

*Siguiente fase: Testing completo y ajustes finales*

