# 🚀 Instrucciones de Desarrollo - Aramed Landing Page

**Fecha:** 13 de octubre de 2025  
**Proyecto:** Aramed y Laboratorios - Landing Page MVP  
**Desarrollador:** IDEAMIA Tech

---

## 📋 Estado Actual

### ✅ DÍA 1 COMPLETADO
**Estructura Base del Proyecto**

Todos los archivos base están creados y funcionales. El proyecto tiene una base sólida para continuar con el desarrollo.

---

## 🔧 Cómo Probar Localmente

### Opción 1: XAMPP / WAMP / MAMP

1. **Copiar archivos**
   ```
   Copiar carpeta /aramed/ a:
   - XAMPP: C:\xampp\htdocs\aramed\
   - WAMP: C:\wamp64\www\aramed\
   - MAMP: /Applications/MAMP/htdocs/aramed/
   ```

2. **Configurar base de datos** (opcional para MVP)
   ```sql
   CREATE DATABASE aramed_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Editar configuración**
   ```
   Archivo: /includes/config.php
   
   Líneas 28-32:
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'aramed_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Dejar vacío en XAMPP
   ```

4. **Acceder al sitio**
   ```
   http://localhost/aramed/public_html/
   ```

### Opción 2: PHP Built-in Server

1. **Abrir terminal en la carpeta del proyecto**
   ```bash
   cd /ruta/a/aramed/public_html
   ```

2. **Iniciar servidor**
   ```bash
   php -S localhost:8000
   ```

3. **Acceder al sitio**
   ```
   http://localhost:8000
   ```

---

## 📂 Estructura de Archivos Creados

```
/aramed/
│
├── 📄 README.md                          # Documentación principal
│
├── 📁 DOCS/                              # Documentación del proyecto
│   ├── Plan_Desarrollo_Landing_Fase1.md # Plan completo
│   ├── Propuesta_sitio_web...md          # Propuesta cliente
│   ├── README_Aramed_Fase1.md            # README fase 1
│   ├── PROGRESO_DIA_1.md                 # ✅ Progreso día 1
│   └── INSTRUCCIONES_DESARROLLO.md       # Este archivo
│
├── 📁 includes/                          # Archivos PHP reutilizables
│   ├── config.php                        # ⚙️ Configuración general
│   ├── connection.php                    # 🔌 Conexión BD con PDO
│   ├── functions.php                     # 🛠️ Funciones auxiliares
│   ├── topbar.php                        # Topbar componente
│   ├── navbar.php                        # Navbar componente
│   └── footer.php                        # Footer componente
│
├── 📁 public_html/                       # 🌐 Raíz pública del sitio
│   │
│   ├── index.php                         # 🏠 Landing page principal
│   ├── .htaccess                         # Apache config
│   ├── robots.txt                        # 🔍 SEO
│   ├── sitemap.xml                       # 🗺️ Mapa del sitio
│   │
│   └── 📁 assets/                        # Recursos estáticos
│       │
│       ├── 📁 css/
│       │   ├── main.css                  # 🎨 Estilos principales (600+ líneas)
│       │   ├── landing.css               # 🎨 Estilos landing
│       │   └── responsive.css            # 📱 Media queries
│       │
│       ├── 📁 js/
│       │   ├── main.js                   # ⚡ JavaScript global
│       │   ├── landing.js                # ⚡ JS landing page
│       │   └── forms.js                  # ✅ Validación formularios
│       │
│       └── 📁 img/                       # (Por agregar imágenes)
│
├── 📁 library/                           # Librerías externas
│   └── (phpmailer, upload-file, etc.)
│
└── 📁 OLD/                               # Sistema anterior
    └── img/                              # 🖼️ Imágenes existentes
        ├── design/                       # Logos, favicon
        └── contenido/                    # Imágenes de contenido
```

---

## 🎯 Próximos Pasos de Desarrollo

### DÍA 2: Topbar + Navbar + Footer Completos (8 horas)

**Tareas:**

1. **Topbar Avanzado** (2 horas)
   - [ ] Scroll lateral automático de mensajes
   - [ ] Múltiples mensajes rotatorios
   - [ ] Iconos para cada tipo de mensaje
   - [ ] Animaciones suaves

2. **Navbar Mejorado** (3 horas)
   - [ ] Logo real de Aramed
   - [ ] Hover effects mejorados
   - [ ] Active states dinámicos
   - [ ] Submenu si es necesario
   - [ ] Search bar (opcional)

3. **Footer Enriquecido** (3 horas)
   - [ ] Newsletter subscribe simple
   - [ ] Mapa de Google Maps embebido
   - [ ] Información adicional
   - [ ] Enlaces legales funcionales

**Comandos para ejecutar:**
```bash
# 1. Copiar logos reales
cp /OLD/img/design/logo.png /public_html/assets/img/design/

# 2. Optimizar imágenes
# (Script de optimización - DÍA 10)
```

### DÍA 3-4: Hero Section + Slideshow (16 horas)

**Tareas:**

1. **Estructura Hero** (4 horas)
   - [ ] Swiper.js configurado
   - [ ] 6 slides con estructura HTML
   - [ ] Controles de navegación
   - [ ] Indicadores

2. **Contenido y Diseño** (8 horas)
   - [ ] Slide principal con H1/H2
   - [ ] 5 slides de productos con info completa
   - [ ] Imágenes optimizadas
   - [ ] Textos con animaciones
   - [ ] CTAs funcionales

3. **Animaciones** (4 horas)
   - [ ] Transiciones suaves
   - [ ] Autoplay configurado
   - [ ] Pausar al hover
   - [ ] Lazy loading de imágenes

**Archivos a editar:**
- `public_html/index.php` (sección hero)
- `public_html/assets/css/landing.css` (estilos hero)
- `public_html/assets/js/landing.js` (slider config)

---

## 🔍 Verificación y Testing

### Checklist de Verificación DÍA 1

**Backend:**
- [x] config.php configurado
- [x] connection.php funcional
- [x] functions.php completo

**Frontend:**
- [x] index.php carga sin errores
- [x] Topbar visible
- [x] Navbar funcional
- [x] Footer completo
- [x] Modal de contacto abre

**CSS:**
- [x] Estilos se aplican correctamente
- [x] Responsive funciona en móvil
- [x] No hay estilos rotos

**JavaScript:**
- [x] No hay errores en consola
- [x] Smooth scroll funciona
- [x] Back to top aparece al scroll

**SEO:**
- [x] Meta tags presentes
- [x] Open Graph configurado
- [x] robots.txt accesible
- [x] sitemap.xml accesible

### Herramientas de Testing

**1. Abrir en navegadores:**
- Chrome
- Firefox
- Safari
- Edge

**2. Responsive:**
```
F12 > Toggle Device Toolbar
Probar:
- iPhone SE (375px)
- iPad (768px)
- Desktop (1920px)
```

**3. Consola de desarrollador:**
```
F12 > Console
Verificar: Sin errores rojos
```

**4. Lighthouse (Performance):**
```
F12 > Lighthouse > Generate Report
Target: >90 en Performance, SEO, Accessibility
```

---

## 🐛 Troubleshooting

### Problema: Página en blanco

**Solución:**
```php
// 1. Activar errores en config.php
define('ENVIRONMENT', 'development');

// 2. Verificar permisos
chmod 755 -R public_html/

// 3. Ver log de errores
tail -f /logs/php-errors.log
```

### Problema: CSS no se aplica

**Solución:**
```
1. Verificar ruta en index.php
2. Limpiar caché del navegador (Ctrl + Shift + R)
3. Verificar que existan los archivos CSS
```

### Problema: JavaScript no funciona

**Solución:**
```
1. Abrir consola F12
2. Ver errores en rojo
3. Verificar carga de archivos JS (Network tab)
4. Verificar orden de scripts (main.js primero)
```

---

## 📖 Recursos Útiles

### Documentación Externa

**Bootstrap 5:**
- https://getbootstrap.com/docs/5.3/

**Swiper.js:**
- https://swiperjs.com/

**AOS (Animate On Scroll):**
- https://michalsnik.github.io/aos/

**PHP PDO:**
- https://www.php.net/manual/es/book.pdo.php

### Referencias de Diseño

**Inspiración Landing:**
- https://ihealthlabs.com/
- https://www.ekohealth.com/
- https://anatomage.com/

**Navbar:**
- https://www.medtronic.com/mx-es/

**Topbar:**
- https://simulab.com/

---

## 💡 Tips de Desarrollo

### 1. Usar Variables CSS
```css
/* Definir en main.css */
--color-primary: #0066CC;

/* Usar en cualquier lugar */
background-color: var(--color-primary);
```

### 2. Debugging PHP
```php
// Temporal para debug
dd($variable);  // Dump and die

// Log personalizado
logError('Mensaje de error', ['contexto' => $data]);
```

### 3. Debugging JavaScript
```javascript
console.log('Debug:', variable);
console.table(array);
console.error('Error:', error);
```

### 4. Git Commits
```bash
# Commit después de cada feature
git add .
git commit -m "DÍA 2: Topbar mejorado con scroll automático"
git push origin main
```

---

## 🎨 Guía de Estilo

### Colores
```css
Primary:    #0066CC  /* Azul corporativo */
Secondary:  #2C3E50  /* Gris oscuro */
Accent:     #FF6B35  /* Naranja */
Success:    #27AE60  /* Verde */
Error:      #E74C3C  /* Rojo */
```

### Tipografía
```css
Títulos:    font-family: 'Montserrat', sans-serif;
            font-weight: 700-800;
            
Cuerpo:     font-family: 'Open Sans', sans-serif;
            font-weight: 400-600;
```

### Espaciado
```css
Pequeño:    0.5rem (8px)
Medio:      1rem (16px)
Grande:     2rem (32px)
Extra:      4rem (64px)
```

### Sombras
```css
Pequeña:    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
Media:      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
Grande:     box-shadow: 0 10px 15px rgba(0,0,0,0.1);
```

---

## 📞 Soporte

### Contacto Técnico
- **Email:** soporte@ideamia.com.mx
- **Responsable:** Ing. Jorge Alberto Plascencia Correa

### Cliente
- **Email:** marketing@aramedylaboratorio.com
- **Teléfono:** (800) 999-0407

---

## ✅ Última Actualización

**Fecha:** 13 de octubre de 2025  
**Status:** DÍA 1 Completado ✅  
**Siguiente:** DÍA 2 - Topbar + Navbar + Footer

---

**© 2025 Aramed y Laboratorios | Desarrollado por IDEAMIA – Tech**

