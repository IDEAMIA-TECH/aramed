# 🌐 Proyecto: Sitio Web Integral – Aramed y Laboratorios
**Desarrollado por:** IDEAMIA – Tech  
**Cliente:** Aramed y Laboratorios S.A. de C.V.  
**Fase actual:** Fase 1 – Frontend Cliente + Diseño  
**Fecha de inicio:** 08/10/2025  
**Responsable técnico:** Ing. Jorge Alberto Plascencia Correa  

---

## 📘 Objetivo del Proyecto
Desarrollar un **sitio web corporativo moderno, funcional y optimizado** para Aramed y Laboratorios, con diseño responsivo, gestión modular y enfoque escalable.  
La **Fase 1** comprende el desarrollo del **Frontend completo del sitio público**, con base en el diseño UX/UI aprobado y la estructura funcional definida.

---

## ⚙️ Alcance de la Fase 1

### 1. Análisis y Diseño UI/UX
- Reunión inicial para definir **look & feel**, branding y paleta de colores.  
- Creación de **wireframes y navegación completa** (Home, Catálogo, Blog, Contacto, Proyectos).  
- Diseño responsivo (desktop, tablet, móvil).  
- **Prototipo interactivo** (Figma o Adobe XD).  
- Revisión y ajustes con el cliente.  

**Duración estimada:** 38 horas  

---

### 2. Estructura Base del Frontend
- Configuración del entorno **LAMP** y estructura MVC.  
- Layout general (header, footer, menús dinámicos).  
- Integración de **Bootstrap o Tailwind** según el diseño final.  
- Rutas amigables (`mod_rewrite`) y SEO técnico básico.  

**Duración estimada:** 32 horas  

---

### 3. Desarrollo de Páginas Públicas

#### 🏠 Home
- Banner rotatorio (slides dinámicos con títulos, subtítulos y CTA).  
- Bloques de servicios y productos destacados.  
- Sección **Marcas / Alianzas** (logos dinámicos en carrusel).  
- Sección “Misión, Visión y Valores”.  
- Testimonios y aliados.  

#### 🧬 Catálogo de Productos
- Listado con **filtros dinámicos** (marca, tipo, precio, disponibilidad).  
- Buscador AJAX.  
- Fichas de producto con galería, video, PDF y botón **“Agregar a cotización”**.  

#### 💬 Solicitud de Cotización
- Formulario dinámico con resumen de productos seleccionados.  
- Validaciones y consentimiento.  
- Generación automática de **folio** y notificación por correo.  

#### 🧪 Proyectos
- Listado filtrable por **año, categoría y marca**.  
- Vista detalle con galería de imágenes y videos.  

#### 📰 Blog
- Listado paginado, buscador por tema o categoría.  
- Página de detalle con estructura **SEO y Open Graph**.  
- Botones para compartir en redes sociales.  

#### 📞 Contacto
- Formulario con motivo de contacto.  
- **Mapa interactivo** embebido.  
- Envío automático al correo de atención al cliente.  

#### ✉️ Newsletter
- Formulario con validaciones y doble opt-in (opcional).  
- Notificación a **marketing@aramedylaboratorio.com**.  

#### ⚖️ Páginas Legales
- Aviso de Privacidad, Términos de Uso, Política de Cookies.  

---

### 4. Componentes Frontend Avanzados
- Filtros dinámicos con **AJAX**.  
- Lazy Loading de imágenes.  
- Conversión automática a formatos **WebP / AVIF**.  
- Sliders con **Swiper** o **Slick**.  

---

### 5. Testing y QA
- Pruebas de usabilidad y navegación en desktop / móvil.  
- Validación de formularios (contacto, cotización, newsletter).  
- Corrección de errores visuales y responsive.  
- Revisión con cliente antes de despliegue final.  

---

## 📅 Cronograma – Fase 1 (162 horas)

| Semana | Actividad Principal | Responsable | Horas |
|:--|:--|:--|:--|
| **1** | Análisis y Diseño UI/UX | IDEAMIA / Aramed | 38 h |
| **2** | Estructura Base del Frontend | IDEAMIA Tech | 32 h |
| **3** | Páginas Públicas – Parte 1 (Home, Proyectos) | IDEAMIA Tech | 36 h |
| **4** | Páginas Públicas – Parte 2 + QA Final | IDEAMIA Tech / Aramed | 56 h |

---

## 🎨 Referencias Visuales

### Sitio Base
- [iHealth Labs](https://ihealthlabs.com/)
- [Gaumard Scientific](https://www.gaumard.com/)
- [Simulab](https://simulab.com/)
- [SimX](https://simxar.com/)
- [Medtronic México](https://www.medtronic.com/mx-es/index.html)
- [Anatomage](https://anatomage.com/)
- [Eko Health](https://www.ekohealth.com/)
- [Pro Theme Health](https://pro-theme.com/html/health/about-1.html)

---

## 🧩 Estructura del Sitio

### **Header**
- Topbar con mensajes automáticos (avisos / eventos).  
- Navbar con links: `Inicio`, `Catálogos`, `Proyectos`, `Aliados`, `Blogs`, `Contáctanos`.  

### **Footer**
- Logo Aramed y Laboratorios.  
- Menú rápido + horarios.  
- Información de contacto.  
- Redes sociales: LinkedIn, Facebook, Instagram, X.  

---

## 🔒 Tecnologías y Estándares
- **Lenguajes:** PHP, JavaScript, CSS, HTML5, AJAX  
- **Frameworks:** Bootstrap / Tailwind  
- **Base de datos:** MySQL  
- **Arquitectura:** MVC  
- **Optimización:** SEO, Open Graph, WebP, Lazy Load  
- **Correo saliente:** PHPMailer o API SendGrid  

---

## ✅ Entregables Fase 1
1. Prototipo visual aprobado (Figma o Adobe XD).  
2. Frontend completo funcional (páginas públicas).  
3. Formularios de contacto y cotización conectados a correo.  
4. Archivos optimizados y listos para integración backend (Fase 2).  
5. Reporte de pruebas y correcciones de QA.  

---

## 🧾 Declaraciones
IDEAMIA – Tech se compromete a desarrollar las funcionalidades descritas en la Fase 1 conforme al presente documento.  
Aramed y Laboratorios reconoce que las funciones adicionales se desarrollarán en fases posteriores.  

**Firmas de aceptación**  
**IDEAMIA – Tech:** Ing. Jorge Alberto Plascencia Correa – Dirección General  
**Aramed y Laboratorios:** ___________________________

---

## 📂 Estructura Inicial de Carpetas
/aramedylaboratorio
│
├── /public_html
│   ├── index.php
│   ├── /assets
│   │   ├── /css
│   │   ├── /js
│   │   ├── /img
│   └── /views
│       ├── home.php
│       ├── catalogo.php
│       ├── proyectos.php
│       ├── blog.php
│       ├── contacto.php
│       └── legales.php
│
├── /includes
│   ├── header.php
│   ├── footer.php
│   └── config.php
│
└── /admin (para fases futuras)

---

## 📧 Contactos
- **Cliente:** marketing@aramedylaboratorio.com  
- **Soporte Técnico:** soporte@ideamia.com.mx  
- **Dirección IDEAMIA:** Club de Golf Atlas 535 Int 20, Tlaquepaque, Jalisco, 45623  

---

© 2025 Aramed y Laboratorios | Desarrollado por IDEAMIA – Tech