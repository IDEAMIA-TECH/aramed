# 🚀 Plan de Desarrollo: Landing Page + Fase 1
**Proyecto:** Sitio Web Aramed y Laboratorios  
**Cliente:** Aramed y Laboratorio S.A. de C.V.  
**Desarrollador:** IDEAMIA – Tech  
**Responsable:** Ing. Jorge Alberto Plascencia Correa  
**Fecha de inicio:** 13 de octubre de 2025  
**Última actualización:** 13 de octubre de 2025

---

## 🎯 Objetivo Dual

### 🔥 URGENTE: Landing Page MVP
**Deadline:** 31 de octubre de 2025 (18 días)  
**Objetivo:** Lanzar un landing page funcional, elegante y optimizado que posicione a Aramed como líder en simulación médica.

### 📦 COMPLETO: Fase 1 – Sitio Web Integral
**Duración total:** 162 horas (aprox. 4 semanas)  
**Objetivo:** Sitio web corporativo completo con todas las funcionalidades descritas en el alcance original.

---

## ⚡ PRIORIDAD 1: Landing Page MVP (Entrega: 31/Oct/2025)

### 📋 Alcance del Landing

El landing page incluirá **una sola página scrollable** con todas las secciones clave:

#### 1. **Topbar** ⏱️ 2 horas
- Mensajes dinámicos de noticias/eventos
- Scroll lateral automático
- Editable desde backend (preparar estructura)
- **Referencia:** simulab.com

#### 2. **Navbar** ⏱️ 4 horas
- Logo Aramed
- Links: Inicio · Catálogos · Proyectos · Aliados · Blogs · Contáctanos
- CTA principal: **"Contáctanos"**
- Sticky navbar con efecto scroll
- Responsive con menú hamburguesa en móvil
- **Referencia:** medtronic.com/mx-es

#### 3. **Hero Section / Slideshow** ⏱️ 12 horas
- **Slide principal:**
  - H1: "Aramed y Laboratorio: Simuladores médicos para la enseñanza"
  - H2: "Distribuidores líderes de tecnología educativa en salud"
  - CTA: "Contáctanos"
  
- **Slides de productos (5 slides):**
  1. VICTORIA® S2200 — El simulador de parto más avanzado
  2. HAL® S5301 — Experiencia de simulación realista
  3. HAL® S3201 — Realismo clínico en cada entrenamiento
  4. Super TORY® S2220 — Realismo neonatal al máximo
  5. SUSIE® S2400 — Simulación integral para el cuidado
  
- **Implementación:**
  - Swiper.js o Slick Slider
  - Transiciones elegantes (fade/slide)
  - Autoplay con pausa al hover
  - Indicadores y navegación
  - Imágenes responsive con lazy loading
  
- **Recursos:** `/OLD/img/contenido/carousel/` (12 slides disponibles con versiones nat800, xs y full)
- **Referencia:** ekohealth.com

#### 4. **Social Proof (Aliados)** ⏱️ 8 horas
- **Header:** "Nuestros Aliados"
- Carrusel de logos con hover effects
- 16 aliados principales (logos disponibles en `/OLD/img/contenido/empresas/`)
- Sección de testimonios (2-3 testimonios destacados)
- **Aliados incluidos:**
  - Gaumard Scientific
  - Medical-X
  - Anatomage
  - Saratoga Dental
  - 3B Scientific
  - 3-Dmed
  - SafeGuard / SIMBODIES
  - Strategic Operations
  - Kyoto Kagaku
  - SimX
  - Nasco Healthcare
  - TruCorp
  - Erler-Zimmer
  - VATA Inc.
  - Adam Rouilly
  - Echo Healthcare
  
- **Recursos:** `/OLD/img/contenido/empresas/` (16 logos PNG disponibles)
- **Referencia:** anatomage.com

#### 5. **Oferta (Services)** ⏱️ 10 horas
- **Header:** "Por la dignidad del paciente, reduciendo el error humano"
- **Subheader:** Empresa mexicana con +20 años equipando instituciones de salud
- **5 Cards de servicios:**
  1. Desarrollo de área de Simulación
  2. Mantenimiento Preventivo
  3. Capacitación especializada
  4. Consultoría y Asesoría Académica
  5. Mantenimiento Correctivo
  
- Grid responsive (3 cols desktop, 2 cols tablet, 1 col móvil)
- Iconos modernos para cada servicio
- Hover effects con elevación/sombra
- **Recursos:** `/OLD/img/contenido/servicios/` (14 imágenes PNG)
- **Referencia:** pro-theme.com/html/health/about-1.html

#### 6. **Productos Destacados** ⏱️ 10 horas
- Layout alternado (imagen izq/der entre secciones)
- **4 Productos principales:**
  1. **Anatomage Table** — Revoluciona la enseñanza médica
  2. **Immersive Interactive (Echo Healthcare)** — Entornos inmersivos
  3. **Lifecast** — Simulación pediátrica de alto realismo
  4. **ADAM-X Xtreme** — Simulación clínica con realismo total
  
- Cada producto con:
  - Título + subtítulo
  - Descripción con bullets
  - Imagen de alta calidad
  - CTA: "Conocer más" / "Solicitar información"
  
- **Recursos:** `/OLD/img/contenido/productos/` (2860 imágenes disponibles)

#### 7. **Newsletter** ⏱️ 8 horas
- **Header:** "Mantente informado"
- **Formulario completo con:**
  - Institución
  - Tipo de institución (dropdown con lógica condicional)
  - Estado (dropdown - todos los estados de México)
  - Ciudad (dropdown dependiente del estado)
  - Nombre del interesado
  - Puesto
  - Correo electrónico (Oficial + Alterno)
  - Teléfono (Oficina con ext. + Celular)
  - Producto de interés
  - Fecha aproximada de compra (Año + Mes)
  - Observaciones
  
- **Validaciones:**
  - Campos obligatorios
  - Formato de email
  - Formato de teléfono
  - CAPTCHA / reCAPTCHA
  
- **Funcionalidad:**
  - Envío a: `marketing@aramedylaboratorio.com`
  - Confirmación al usuario
  - Almacenamiento en BD (opcional para MVP)
  
- **Diseño:** Modal o sección con fondo destacado

#### 8. **Footer** ⏱️ 4 horas
- **Logo Aramed**
- **Menú:** Inicio · Catálogos · Proyectos · Aliados · Blogs
- **Horarios:**
  - Lunes a Viernes: 9:00–14:00 y 16:00–19:00
  - Sábados: 10:00–14:00
  
- **Contacto:**
  - Email: `atencionacliente@aramedylaboratorio.com`
  - Teléfono: **(800) 999-0407**
  
- **Redes Sociales:**
  - LinkedIn · Facebook · Instagram · Twitter/X
  
- **Legal:**
  - © Derechos Reservados Aramed y Laboratorio
  - Aviso de Privacidad · Términos de Uso

#### 9. **Formulario de Contacto** ⏱️ 6 horas
- Modal activado desde CTA del Navbar y Hero
- **Campos:**
  - Nombre completo
  - Email
  - Teléfono
  - Institución
  - Motivo de contacto (dropdown)
  - Mensaje
  
- Envío a: `atencionacliente@aramedylaboratorio.com`
- Validaciones y confirmación

#### 10. **Optimizaciones Técnicas** ⏱️ 8 horas
- **Performance:**
  - Lazy loading de imágenes
  - Conversión a WebP (con fallback JPG)
  - Minificación CSS/JS
  - Compresión GZIP
  
- **SEO Básico:**
  - Meta tags (title, description, keywords)
  - Open Graph para redes sociales
  - Schema.org (Organization, LocalBusiness)
  - Sitemap.xml básico
  - Robots.txt
  
- **Accesibilidad:**
  - Alt text en imágenes
  - ARIA labels
  - Contraste de colores
  - Navegación por teclado

#### 11. **Testing y QA** ⏱️ 6 horas
- Pruebas en navegadores (Chrome, Firefox, Safari, Edge)
- Pruebas responsive (móvil, tablet, desktop)
- Validación de formularios
- Performance testing (Lighthouse)
- Corrección de bugs

---

### 📊 Cronograma Landing Page MVP

| Día | Fecha | Actividad | Horas | Status |
|:---:|:------|:----------|:-----:|:------:|
| **1** | 13-Oct | Setup entorno + Estructura base HTML/CSS | 8h | ⏳ Pendiente |
| **2** | 14-Oct | Topbar + Navbar + Footer | 8h | ⏳ Pendiente |
| **3** | 15-Oct | Hero Section + Slideshow (estructura) | 8h | ⏳ Pendiente |
| **4** | 16-Oct | Hero Section + Slideshow (contenido + animaciones) | 8h | ⏳ Pendiente |
| **5** | 17-Oct | Social Proof (Aliados + Testimonios) | 8h | ⏳ Pendiente |
| **6** | 18-Oct | Oferta (Services) | 8h | ⏳ Pendiente |
| **7** | 19-Oct | Productos Destacados | 8h | ⏳ Pendiente |
| **8** | 20-Oct | Newsletter (Formulario + Lógica) | 8h | ⏳ Pendiente |
| **9** | 21-Oct | Formulario Contacto + Backend emails | 8h | ⏳ Pendiente |
| **10** | 22-Oct | Optimizaciones (Performance + SEO) | 8h | ⏳ Pendiente |
| **11-12** | 23-24-Oct | Testing, ajustes y correcciones | 16h | ⏳ Pendiente |
| **13** | 25-Oct | Revisión con cliente + ajustes | 4h | ⏳ Pendiente |
| **14-17** | 26-29-Oct | Buffer para ajustes finales | 16h | ⏳ Pendiente |
| **18** | 30-Oct | Pre-lanzamiento y pruebas finales | 8h | ⏳ Pendiente |
| **19** | 31-Oct | 🚀 **LANZAMIENTO OFICIAL** | - | ⏳ Pendiente |

**Total:** 104 horas netas de desarrollo  
**Días de trabajo:** 13 días efectivos  
**Horas/día:** 8 horas  
**Buffer:** 4 días para ajustes

---

## 🎨 Stack Tecnológico - Landing Page MVP

### Frontend
- **HTML5** (Semántico y accesible)
- **CSS3** (Flexbox + Grid)
- **JavaScript ES6+** (Vanilla o jQuery mínimo)
- **Bootstrap 5** o **Tailwind CSS 3** (por definir con cliente)

### Librerías
- **Swiper.js** — Sliders/carruseles
- **AOS (Animate On Scroll)** — Animaciones de scroll
- **Vanilla Tilt** — Efectos hover en cards (opcional)
- **Typed.js** — Animaciones de texto (opcional)

### Formularios
- **PHPMailer** — Envío de emails
- **Google reCAPTCHA v3** — Anti-spam

### Optimización
- **WebP Converter** — Conversión de imágenes
- **TinyPNG API** — Compresión de imágenes
- **LazyLoad** — Carga diferida

### Testing
- **Google Lighthouse** — Performance
- **BrowserStack** — Testing cross-browser
- **GTmetrix** — Speed testing

---

## 📦 FASE 1 COMPLETA: Sitio Web Integral (Post-Landing)

### Páginas Adicionales a Desarrollar

#### 1. **Catálogo de Productos** ⏱️ 24 horas
- Sistema de filtros dinámicos (marca, tipo, precio)
- Buscador AJAX
- Paginación
- Fichas de producto detalladas:
  - Galería de imágenes
  - Video embebido
  - PDF descargable
  - Especificaciones técnicas
  - Botón "Agregar a cotización"
  
- **Recursos:** 
  - `/OLD/img/contenido/productos/` (2860 imágenes)
  - `/OLD/img/contenido/productoscat/` (56 categorías)
  - `/OLD/img/contenido/productospdf/` (282 PDFs)

#### 2. **Sistema de Cotización** ⏱️ 16 horas
- Carrito de cotización (sin pago)
- Resumen de productos seleccionados
- Formulario con datos del solicitante
- Generación de folio único
- PDF de cotización
- Notificación automática por email

#### 3. **Proyectos** ⏱️ 12 horas
- Listado con filtros (año, categoría, marca)
- Grid/Listado responsive
- Vista detalle con:
  - Galería de imágenes (lightbox)
  - Video embebido
  - Descripción del proyecto
  - Productos utilizados
  - Cliente (opcional)
  
- **Recursos:** `/OLD/img/contenido/proyectos/` (178 imágenes)

#### 4. **Blog / Noticias** ⏱️ 12 horas
- Listado paginado
- Filtros por categoría/tema
- Buscador
- Vista detalle con:
  - Contenido rich text
  - Galería de imágenes
  - SEO optimizado
  - Open Graph
  - Botones de compartir en RRSS
  
- **Recursos:** `/OLD/img/contenido/noticias/` (730 imágenes)

#### 5. **Contacto (Página completa)** ⏱️ 8 horas
- Formulario extendido
- Mapa interactivo (Google Maps)
- Información de oficinas
- Horarios
- Múltiples motivos de contacto

#### 6. **Páginas Legales** ⏱️ 8 horas
- Aviso de Privacidad
- Términos y Condiciones de Uso
- Política de Cookies
- Diseño limpio y legible

---

### Cronograma Fase 1 Completa (Post-Landing)

| Semana | Actividad | Horas | Periodo |
|:------:|:----------|:-----:|:-------:|
| **1** | 🚀 Landing Page MVP | 104h | 13-Oct → 31-Oct |
| **2** | Backend: BD + Admin básico + Catálogos | 32h | 01-Nov → 05-Nov |
| **3** | Sistema de Cotización + Proyectos | 28h | 06-Nov → 11-Nov |
| **4** | Blog + Contacto + Páginas Legales | 28h | 12-Nov → 17-Nov |
| **5** | Integración completa + Testing final | 24h | 18-Nov → 22-Nov |

**Total Fase 1:** 216 horas  
**Fecha estimada de finalización completa:** 22 de noviembre de 2025

---

## 🎯 Estructura de Archivos del Proyecto

```
/aramed/
│
├── /public_html/                    # Raíz pública del sitio
│   ├── index.php                    # Landing page
│   ├── .htaccess                    # Rewrite rules
│   │
│   ├── /assets/
│   │   ├── /css/
│   │   │   ├── main.css             # Estilos principales
│   │   │   ├── landing.css          # Estilos landing page
│   │   │   ├── responsive.css       # Media queries
│   │   │   └── vendor.css           # Librerías (Bootstrap/Tailwind)
│   │   │
│   │   ├── /js/
│   │   │   ├── main.js              # JavaScript principal
│   │   │   ├── landing.js           # JS landing page
│   │   │   ├── forms.js             # Validación de formularios
│   │   │   └── /vendor/             # Librerías externas
│   │   │       ├── swiper.min.js
│   │   │       ├── aos.js
│   │   │       └── lazysizes.min.js
│   │   │
│   │   └── /img/
│   │       ├── /design/             # Logos, iconos, favicon
│   │       ├── /carousel/           # Imágenes del hero slider
│   │       ├── /productos/          # Imágenes de productos
│   │       ├── /empresas/           # Logos de aliados
│   │       ├── /servicios/          # Imágenes de servicios
│   │       └── /webp/               # Versiones optimizadas WebP
│   │
│   ├── /pages/                      # Páginas del sitio (Fase 1 completa)
│   │   ├── catalogo.php
│   │   ├── producto-detalle.php
│   │   ├── cotizar.php
│   │   ├── proyectos.php
│   │   ├── proyecto-detalle.php
│   │   ├── blog.php
│   │   ├── blog-detalle.php
│   │   ├── contacto.php
│   │   └── /legales/
│   │       ├── aviso-privacidad.php
│   │       ├── terminos-uso.php
│   │       └── politica-cookies.php
│   │
│   └── /api/                        # Endpoints para AJAX
│       ├── send-contact.php
│       ├── send-newsletter.php
│       ├── send-quotation.php
│       └── search-products.php
│
├── /includes/                       # Archivos PHP reutilizables
│   ├── config.php                   # Configuración general
│   ├── connection.php               # Conexión a BD
│   ├── header.php                   # Header global
│   ├── footer.php                   # Footer global
│   ├── navbar.php                   # Navbar
│   ├── topbar.php                   # Topbar
│   ├── functions.php                # Funciones auxiliares
│   └── mailer.php                   # PHPMailer setup
│
├── /library/                        # Librerías externas
│   ├── /phpmailer/
│   └── /upload-file/
│
├── /admin/                          # Panel administración (Fase 2)
│
├── /database/
│   ├── aramed.sql                   # Base de datos inicial
│   └── migrations/                  # Migraciones
│
└── /DOCS/                           # Documentación del proyecto
    ├── Plan_Desarrollo_Landing_Fase1.md
    ├── README_Aramed_Fase1.md
    └── Propuesta_sitio_web_Aramed_y_Laboratorio.md
```

---

## ✅ Checklist de Desarrollo - Landing Page MVP

### Pre-Desarrollo
- [ ] Reunión de kick-off con cliente
- [ ] Definir paleta de colores y tipografías
- [ ] Aprobación de referencias visuales
- [ ] Setup de entorno de desarrollo (LAMP)
- [ ] Setup de repositorio Git
- [ ] Creación de staging environment

### Desarrollo
#### Estructura Base
- [ ] HTML5 semántico base
- [ ] Sistema de grid/layout responsive
- [ ] Header con Topbar y Navbar
- [ ] Footer completo
- [ ] Integración de framework CSS (Bootstrap/Tailwind)

#### Hero / Slideshow
- [ ] Slide principal con H1/H2 y CTA
- [ ] 5 slides de productos con contenido completo
- [ ] Integración de Swiper.js
- [ ] Animaciones y transiciones
- [ ] Autoplay y controles de navegación
- [ ] Lazy loading de imágenes
- [ ] Responsive en todos los dispositivos

#### Social Proof
- [ ] Carrusel de logos de aliados (16 logos)
- [ ] Hover effects en logos
- [ ] Sección de testimonios (2-3 testimonios)
- [ ] Responsive layout

#### Oferta / Services
- [ ] 5 cards de servicios con iconos
- [ ] Grid responsive (3/2/1 columnas)
- [ ] Hover effects
- [ ] Contenido completo

#### Productos Destacados
- [ ] 4 secciones de productos alternadas
- [ ] Imágenes de alta calidad optimizadas
- [ ] Descripciones con bullets
- [ ] CTAs funcionales
- [ ] Layout responsive

#### Newsletter
- [ ] Formulario completo con todos los campos
- [ ] Lógica condicional (tipo de institución)
- [ ] Dropdowns dinámicos (Estado → Ciudad)
- [ ] Validaciones frontend
- [ ] Validaciones backend
- [ ] Integración con PHPMailer
- [ ] Confirmación al usuario
- [ ] reCAPTCHA implementado

#### Formulario de Contacto
- [ ] Modal funcional
- [ ] Formulario con validaciones
- [ ] Envío por email
- [ ] Confirmación al usuario
- [ ] Manejo de errores

#### Optimizaciones
- [ ] Conversión de imágenes a WebP
- [ ] Compresión de imágenes
- [ ] Lazy loading implementado
- [ ] Minificación CSS/JS
- [ ] GZIP habilitado
- [ ] Meta tags SEO
- [ ] Open Graph tags
- [ ] Schema.org markup
- [ ] Favicon implementado
- [ ] Sitemap.xml
- [ ] Robots.txt

#### Testing
- [ ] Testing en Chrome
- [ ] Testing en Firefox
- [ ] Testing en Safari
- [ ] Testing en Edge
- [ ] Testing responsive móvil
- [ ] Testing responsive tablet
- [ ] Validación formularios
- [ ] Testing emails (contacto + newsletter)
- [ ] Google Lighthouse (score >90)
- [ ] GTmetrix (Grade A)
- [ ] Validación HTML (W3C)
- [ ] Validación CSS (W3C)
- [ ] Accesibilidad (WAVE)

### Post-Desarrollo
- [ ] Revisión con cliente
- [ ] Ajustes solicitados
- [ ] Documentación técnica
- [ ] Manual de usuario
- [ ] Deploy a producción
- [ ] Configuración de dominio
- [ ] Configuración de SSL
- [ ] Google Analytics instalado
- [ ] Google Search Console configurado
- [ ] Backup inicial

---

## 🔒 Configuración de Seguridad

### SSL/HTTPS
- Certificado SSL instalado (Let's Encrypt o comercial)
- Redirección HTTP → HTTPS forzada

### Protección de Formularios
- reCAPTCHA v3 en todos los formularios
- Validación de tokens CSRF
- Rate limiting en envíos

### Headers de Seguridad
```php
// En .htaccess o header.php
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
```

### Base de Datos
- Prepared statements (PDO)
- Escape de inputs
- Sanitización de datos

---

## 📈 Métricas de Éxito

### Performance
- **Google Lighthouse:** Score >90 en Performance, Accesibilidad, SEO
- **GTmetrix:** Grade A
- **Tiempo de carga:** <3 segundos (primera carga)
- **FCP (First Contentful Paint):** <1.8s
- **LCP (Largest Contentful Paint):** <2.5s

### SEO
- Meta tags completos
- Open Graph implementado
- Schema.org markup
- Sitemap.xml indexado
- Core Web Vitals en verde

### Funcionalidad
- 100% responsive en todos los dispositivos
- 0 errores de consola JavaScript
- Formularios funcionales con confirmación
- Emails entregados correctamente
- Compatible con navegadores modernos (últimas 2 versiones)

---

## 💰 Presupuesto y Recursos

### Fase Landing Page MVP
- **Horas de desarrollo:** 104 horas
- **Costo por hora:** (Por definir)
- **Subtotal Landing:** (Por calcular)

### Fase 1 Completa
- **Horas totales:** 216 horas
- **Costo por hora:** (Por definir)
- **Total Fase 1:** (Por calcular)

### Recursos Externos
- Dominio: `aramedylaboratorio.com` (Cliente)
- Hosting LAMP (Cliente o IDEAMIA)
- Certificado SSL (Let's Encrypt gratis o comercial)
- Google reCAPTCHA (Gratis)
- Email transaccional (PHPMailer + SMTP del cliente)

---

## 📞 Contactos del Proyecto

### Cliente
- **Email:** marketing@aramedylaboratorio.com
- **Email atención:** atencionacliente@aramedylaboratorio.com
- **Teléfono:** (800) 999-0407

### IDEAMIA – Tech
- **Responsable:** Ing. Jorge Alberto Plascencia Correa
- **Email:** soporte@ideamia.com.mx
- **Dirección:** Club de Golf Atlas 535 Int 20, Tlaquepaque, Jalisco, 45623

---

## 📋 Entregables

### Landing Page MVP (31/Oct/2025)
1. ✅ Sitio landing page funcional y publicado
2. ✅ Código fuente documentado
3. ✅ Imágenes optimizadas (WebP + fallbacks)
4. ✅ Formularios conectados a emails
5. ✅ Reporte de testing (Lighthouse + GTmetrix)
6. ✅ Manual de uso básico
7. ✅ Credenciales de acceso (hosting, emails)

### Fase 1 Completa (22/Nov/2025)
1. ✅ Sitio web completo con todas las páginas
2. ✅ Panel de administración básico
3. ✅ Base de datos estructurada
4. ✅ Sistema de cotización funcional
5. ✅ Documentación técnica completa
6. ✅ Manual de administrador
7. ✅ Backup inicial y procedimientos

---

## 🎨 Referencias Visuales y de Diseño

### Inspiración General
- **Landing Page:** [iHealth Labs](https://ihealthlabs.com/)
- **Navbar:** [Medtronic México](https://www.medtronic.com/mx-es/index.html)
- **Hero/Slider:** [Eko Health](https://www.ekohealth.com/)
- **Social Proof:** [Anatomage](https://anatomage.com/)
- **Services:** [Pro Theme Health](https://pro-theme.com/html/health/about-1.html)
- **Catálogo:** [Gaumard Scientific](https://www.gaumard.com/)
- **Topbar:** [Simulab](https://simulab.com/)
- **Animaciones:** [SimX](https://simxar.com/)

### Paleta de Colores (Por definir con cliente)
- **Primario:** Azul corporativo / Verde médico
- **Secundario:** Gris oscuro
- **Acentos:** Naranja / Turquesa
- **Neutros:** Blanco, Gris claro, Negro

### Tipografía (Por definir)
- **Títulos:** Montserrat / Poppins / Raleway
- **Cuerpo:** Open Sans / Roboto / Lato

---

## 🚨 Riesgos y Mitigaciones

| Riesgo | Probabilidad | Impacto | Mitigación |
|:-------|:------------:|:-------:|:-----------|
| Retrasos en contenido del cliente | Media | Alto | Solicitar todo el contenido en la reunión inicial |
| Cambios de alcance | Alta | Alto | Documento de alcance firmado, control de cambios |
| Problemas de hosting/servidor | Baja | Alto | Setup temprano del entorno, backup del staging |
| Imágenes no optimizadas | Media | Medio | Script de optimización automática, conversión WebP |
| Bugs en cross-browser | Media | Medio | Testing continuo desde día 1, BrowserStack |
| Sobrecarga de formularios (spam) | Media | Medio | reCAPTCHA + rate limiting implementados |

---

## 📝 Notas del Desarrollador

### Decisiones Técnicas
- **Framework CSS:** Por definir con cliente (Bootstrap 5 vs Tailwind CSS)
- **Slider:** Swiper.js (ligero, moderno, bien documentado)
- **Animaciones:** AOS.js (fácil implementación, buen performance)
- **Formularios:** Validación nativa HTML5 + JavaScript + PHP backend

### Optimizaciones Implementadas
- Lazy loading de imágenes (Intersection Observer)
- WebP con fallback automático a JPG/PNG
- Minificación automática de assets
- CDN para librerías externas (Swiper, Bootstrap)
- Caché del navegador configurado (.htaccess)

### Próximos Pasos Post-Landing
1. Implementar panel de administración (Fase 2)
2. Sistema de gestión de productos con BD
3. Blog con editor WYSIWYG
4. Sistema de usuarios y roles
5. Analytics y reportes
6. Integración con CRM (opcional)

---

## ✍️ Historial de Cambios

| Fecha | Versión | Cambios | Autor |
|:------|:-------:|:--------|:------|
| 13-Oct-2025 | 1.0 | Documento inicial creado | IDEAMIA Tech |
| | | Priorización Landing Page MVP | |
| | | Cronograma ajustado a 31/Oct | |
| | | Checklist completo agregado | |

---

## 📄 Aprobaciones

### IDEAMIA – Tech
**Nombre:** Ing. Jorge Alberto Plascencia Correa  
**Cargo:** Director General  
**Firma:** ___________________________  
**Fecha:** ___________________________

### Aramed y Laboratorios S.A. de C.V.
**Nombre:** ___________________________  
**Cargo:** ___________________________  
**Firma:** ___________________________  
**Fecha:** ___________________________

---

**© 2025 Aramed y Laboratorios | Desarrollado por IDEAMIA – Tech**

> *Este documento es confidencial y propiedad de IDEAMIA Tech y Aramed y Laboratorios. Su distribución sin autorización está prohibida.*

