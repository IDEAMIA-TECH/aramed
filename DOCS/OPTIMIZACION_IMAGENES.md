# 🖼️ GUÍA DE OPTIMIZACIÓN DE IMÁGENES
## Aramed y Laboratorios - Landing Page

---

## 📋 RESUMEN EJECUTIVO

La optimización de imágenes es crucial para el rendimiento del sitio web. Las imágenes representan típicamente el 50-80% del peso total de una página web.

### Beneficios:
- ⚡ **Velocidad de carga:** 30-70% más rápido
- 📱 **Experiencia móvil:** Mejor rendimiento en 3G/4G
- 💰 **Ahorro de ancho de banda:** Reducción de costos de hosting
- 🎯 **SEO:** Mejor ranking en Google (Core Web Vitals)
- 📊 **Conversión:** Cada segundo de carga mejora conversión en ~7%

---

## 🎯 ESTÁNDARES RECOMENDADOS

### Tamaños Máximos por Tipo

| Tipo de Imagen | Resolución Recomendada | Peso Máximo | Formato |
|----------------|----------------------|-------------|---------|
| **Hero/Banner** | 1920x1080px | 150-200KB | WebP/JPG |
| **Productos** | 800x600px | 80-120KB | WebP/JPG |
| **Logos Aliados** | 400x300px | 30-50KB | WebP/PNG |
| **Testimonios** | 150x150px | 20-30KB | WebP/JPG |
| **Iconos** | 64x64px | 5-10KB | SVG/PNG |
| **Favicon** | 32x32px | <5KB | PNG/ICO |
| **OG Image** | 1200x630px | 100KB | JPG |

---

## 🔄 FORMATOS RECOMENDADOS

### 1. **WebP** (Primera Opción)
- ✅ Mejor compresión (25-35% menor que JPG)
- ✅ Soporta transparencia
- ✅ Compatible con 95%+ navegadores
- ❌ No soportado en IE11

```html
<picture>
    <source srcset="imagen.webp" type="image/webp">
    <img src="imagen.jpg" alt="Descripción">
</picture>
```

### 2. **AVIF** (Futuro)
- ✅ Mejor compresión que WebP (50% menor)
- ✅ Calidad superior
- ❌ Soporte limitado (Chrome 85+, Firefox 93+)

### 3. **JPG** (Fallback)
- ✅ Universal compatibility
- ✅ Bueno para fotografías
- ❌ No soporta transparencia
- Calidad recomendada: 75-85%

### 4. **PNG** (Solo para transparencias)
- ✅ Transparencias
- ✅ Gráficos simples
- ❌ Archivos pesados
- Usar PNG-8 cuando sea posible

### 5. **SVG** (Iconos y logos)
- ✅ Escalable sin pérdida
- ✅ Archivos muy ligeros
- ✅ Editable con código
- Recomendado para: iconos, logos simples, ilustraciones

---

## 🛠️ HERRAMIENTAS DE OPTIMIZACIÓN

### Online (Gratis)

1. **TinyPNG / TinyJPG**
   - URL: https://tinypng.com/
   - Formatos: PNG, JPG
   - Reducción: 50-80%
   - ✅ Batch processing
   - ✅ API disponible

2. **Squoosh** (Google)
   - URL: https://squoosh.app/
   - Formatos: Todos
   - ✅ Conversión a WebP/AVIF
   - ✅ Comparación lado a lado

3. **SVGOMG**
   - URL: https://jakearchibald.github.io/svgomg/
   - Formato: SVG
   - ✅ Limpieza de código
   - ✅ Preview en tiempo real

### CLI (Automatización)

```bash
# ImageMagick (conversión y redimensión)
convert input.jpg -quality 85 -resize 1920x output.jpg

# cwebp (convertir a WebP)
cwebp -q 85 input.jpg -o output.webp

# pngquant (optimizar PNG)
pngquant --quality=65-85 input.png --output output.png

# svgo (optimizar SVG)
svgo input.svg -o output.svg
```

### Node.js / npm

```bash
# sharp (procesamiento de imágenes)
npm install sharp

# imagemin (optimización automática)
npm install imagemin imagemin-webp imagemin-mozjpeg

# Example script (optimize.js)
const imagemin = require('imagemin');
const imageminWebp = require('imagemin-webp');

(async () => {
    await imagemin(['images/*.{jpg,png}'], {
        destination: 'build/images',
        plugins: [
            imageminWebp({quality: 85})
        ]
    });
})();
```

---

## 📦 IMPLEMENTACIÓN EN EL PROYECTO

### 1. Estructura de Directorios

```
/assets/images/
├── design/              # Logos, favicon, branding
│   ├── logo.svg
│   ├── logo.png
│   ├── logo.webp
│   └── favicon.ico
├── hero/                # Imágenes del hero section
│   ├── slide-1.webp
│   ├── slide-1.jpg      # Fallback
│   └── ...
├── products/            # Imágenes de productos
│   ├── producto-1.webp
│   ├── producto-1.jpg
│   └── ...
├── aliados/             # Logos de aliados
│   ├── aliado-1.webp
│   └── ...
└── icons/               # Iconos SVG
    └── ...
```

### 2. Implementación con `<picture>`

```html
<!-- Responsive + WebP -->
<picture>
    <!-- WebP para pantallas grandes -->
    <source media="(min-width: 1200px)" 
            srcset="hero-large.webp" 
            type="image/webp">
    
    <!-- JPG fallback para pantallas grandes -->
    <source media="(min-width: 1200px)" 
            srcset="hero-large.jpg">
    
    <!-- WebP para pantallas medianas -->
    <source media="(min-width: 768px)" 
            srcset="hero-medium.webp" 
            type="image/webp">
    
    <!-- JPG fallback para pantallas medianas -->
    <source media="(min-width: 768px)" 
            srcset="hero-medium.jpg">
    
    <!-- WebP para mobile -->
    <source srcset="hero-small.webp" 
            type="image/webp">
    
    <!-- JPG fallback (default) -->
    <img src="hero-small.jpg" 
         alt="Simulador Maternal Avanzado"
         loading="lazy"
         width="800"
         height="600">
</picture>
```

### 3. Lazy Loading

```html
<!-- Lazy loading nativo -->
<img src="producto.jpg" 
     alt="Descripción"
     loading="lazy"
     width="800"
     height="600">

<!-- Con placeholder blur (técnica avanzada) -->
<img src="producto-tiny.jpg"
     data-src="producto.jpg"
     class="lazy-load blur-up"
     alt="Descripción">
```

### 4. CSS para Lazy Loading

```css
/* Placeholder blur effect */
.lazy-load {
    filter: blur(20px);
    transition: filter 0.3s;
}

.lazy-load.loaded {
    filter: blur(0);
}

/* Aspect ratio para evitar layout shift */
.img-container {
    position: relative;
    padding-bottom: 75%; /* 4:3 ratio */
    height: 0;
    overflow: hidden;
}

.img-container img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}
```

---

## ⚡ SCRIPT DE OPTIMIZACIÓN AUTOMÁTICA

Crear archivo `scripts/optimize-images.js`:

```javascript
const sharp = require('sharp');
const fs = require('fs').promises;
const path = require('path');

// Configuración
const CONFIG = {
    inputDir: './assets/images-original',
    outputDir: './public_html/assets/images',
    quality: {
        jpg: 85,
        webp: 85,
        png: 90
    },
    sizes: {
        hero: { width: 1920, height: 1080 },
        product: { width: 800, height: 600 },
        thumbnail: { width: 400, height: 300 }
    }
};

async function optimizeImage(inputPath, outputPath, size) {
    const ext = path.extname(inputPath).toLowerCase();
    
    // Redimensionar y convertir a WebP
    await sharp(inputPath)
        .resize(size.width, size.height, {
            fit: 'cover',
            position: 'center'
        })
        .webp({ quality: CONFIG.quality.webp })
        .toFile(outputPath.replace(ext, '.webp'));
    
    // Mantener JPG como fallback
    if (ext === '.jpg' || ext === '.jpeg') {
        await sharp(inputPath)
            .resize(size.width, size.height, {
                fit: 'cover',
                position: 'center'
            })
            .jpeg({ quality: CONFIG.quality.jpg, progressive: true })
            .toFile(outputPath);
    }
    
    console.log(`✓ Optimized: ${path.basename(inputPath)}`);
}

// Ejecutar
(async () => {
    console.log('🖼️  Iniciando optimización de imágenes...\n');
    // Lógica de procesamiento aquí
    console.log('\n✅ Optimización completada!');
})();
```

---

## 📊 CHECKLIST DE OPTIMIZACIÓN

### Antes de subir imágenes:

- [ ] ¿La imagen es realmente necesaria?
- [ ] ¿Se puede reemplazar con CSS/SVG?
- [ ] ¿Está en el formato correcto?
- [ ] ¿Está optimizada/comprimida?
- [ ] ¿Tiene el tamaño correcto?
- [ ] ¿Incluye versión WebP?
- [ ] ¿Tiene atributos `width` y `height`?
- [ ] ¿Tiene `loading="lazy"` si no es above-the-fold?
- [ ] ¿Tiene `alt` text descriptivo?

---

## 🎯 METAS DE RENDIMIENTO

### Lighthouse Scores (Objetivos)

| Métrica | Target | Actual |
|---------|--------|--------|
| **Performance** | 90+ | - |
| **Largest Contentful Paint (LCP)** | <2.5s | - |
| **First Input Delay (FID)** | <100ms | - |
| **Cumulative Layout Shift (CLS)** | <0.1 | - |
| **Total Image Weight** | <500KB | - |

### Comandos para Testing

```bash
# Google PageSpeed Insights (online)
# URL: https://pagespeed.web.dev/

# Lighthouse CLI
npm install -g lighthouse
lighthouse https://www.aramedylaboratorio.com --view

# WebPageTest
# URL: https://www.webpagetest.org/
```

---

## 📚 RECURSOS ADICIONALES

### Documentación
- [WebP Guide - Google](https://developers.google.com/speed/webp)
- [Image Optimization - web.dev](https://web.dev/fast/#optimize-your-images)
- [Responsive Images - MDN](https://developer.mozilla.org/en-US/docs/Learn/HTML/Multimedia_and_embedding/Responsive_images)

### Herramientas
- [Can I Use - WebP](https://caniuse.com/webp)
- [Squoosh](https://squoosh.app/)
- [TinyPNG](https://tinypng.com/)

---

## 🚀 PLAN DE ACCIÓN PARA ARAMED

### Fase 1: Auditoría (1-2 horas)
1. Listar todas las imágenes actuales
2. Medir pesos y formatos
3. Identificar imágenes críticas (above-the-fold)

### Fase 2: Optimización (2-4 horas)
1. Convertir todas las JPG a WebP
2. Redimensionar según estándares
3. Implementar lazy loading
4. Agregar `width` y `height`

### Fase 3: Testing (1 hora)
1. Ejecutar Lighthouse
2. Probar en diferentes dispositivos
3. Verificar tiempos de carga

### Fase 4: Monitoreo
1. Configurar alertas de rendimiento
2. Revisar métricas semanalmente
3. Ajustar según sea necesario

---

**Última actualización:** 13 de Octubre, 2025  
**Responsable:** IDEAMIA Tech  
**Versión:** 1.0


