# 🏥 Aramed y Laboratorios - Sitio Web

**Cliente:** Aramed y Laboratorio S.A. de C.V.  
**Desarrollador:** IDEAMIA – Tech  
**Versión:** 1.0.0 (Landing Page MVP)  
**Fecha:** Octubre 2025

---

## 📋 Descripción del Proyecto

Sitio web corporativo moderno para **Aramed y Laboratorios**, distribuidores líderes de simuladores médicos y tecnología educativa en salud en México.

---

## 🚀 Estado Actual: Fase 1 - Landing Page MVP

### ✅ Completado (DÍA 1)
- [x] Estructura de carpetas y archivos
- [x] Configuración PHP (config.php, connection.php, functions.php)
- [x] HTML5 base con estructura semántica
- [x] Sistema de estilos CSS (main.css, landing.css, responsive.css)
- [x] JavaScript base (main.js, landing.js, forms.js)
- [x] Topbar inicial
- [x] Navbar responsive
- [x] Footer completo
- [x] .htaccess con optimizaciones
- [x] robots.txt y sitemap.xml
- [x] Meta tags SEO y Open Graph
- [x] Schema.org structured data

### 🔄 En Desarrollo
- [ ] Hero Section con Swiper.js (DÍA 3-4)
- [ ] Social Proof / Aliados (DÍA 5)
- [ ] Services Cards (DÍA 6)
- [ ] Productos Destacados (DÍA 7)
- [ ] Newsletter (DÍA 8)
- [ ] Formulario de Contacto (DÍA 9)
- [ ] Optimizaciones finales (DÍA 10)

---

## 🛠️ Stack Tecnológico

### Frontend
- **HTML5** - Estructura semántica
- **CSS3** - Variables CSS, Flexbox, Grid
- **JavaScript ES6+** - Vanilla JS modular
- **Bootstrap 5.3.2** - Framework CSS
- **Swiper.js 11** - Sliders/Carruseles
- **AOS** - Animaciones on scroll

### Backend
- **PHP 8+** - Lenguaje servidor
- **MySQL 8** - Base de datos
- **PDO** - Conexión segura a BD

### Librerías
- **PHPMailer** - Envío de emails
- **Google reCAPTCHA v3** - Anti-spam

---

## 📁 Estructura del Proyecto

```
/aramed/
├── /public_html/               # Raíz pública
│   ├── index.php              # Landing page principal
│   ├── .htaccess              # Configuración Apache
│   ├── robots.txt             # SEO
│   ├── sitemap.xml            # SEO
│   │
│   ├── /assets/               # Recursos estáticos
│   │   ├── /css/              # Estilos
│   │   ├── /js/               # JavaScript
│   │   └── /img/              # Imágenes
│   │
│   ├── /pages/                # Páginas adicionales (Fase 1 completa)
│   └── /api/                  # Endpoints AJAX
│
├── /includes/                 # PHP reutilizable
│   ├── config.php             # Configuración
│   ├── connection.php         # Conexión BD
│   ├── functions.php          # Funciones auxiliares
│   ├── topbar.php             # Topbar
│   ├── navbar.php             # Navbar
│   └── footer.php             # Footer
│
├── /library/                  # Librerías externas
├── /database/                 # BD y migraciones
├── /logs/                     # Logs del sistema
└── /DOCS/                     # Documentación
```

---

## ⚙️ Configuración

### Requisitos del Servidor
- PHP 8.0 o superior
- MySQL 8.0 o superior
- Apache 2.4 con mod_rewrite
- SSL/HTTPS
- 256MB RAM mínimo
- 1GB espacio en disco

### Instalación Local

1. **Clonar el proyecto**
```bash
git clone [repository-url]
cd aramed
```

2. **Configurar base de datos**
- Crear base de datos: `aramed_db`
- Importar: `/database/aramed.sql` (cuando esté disponible)

3. **Configurar PHP**
- Editar `/includes/config.php`
- Ajustar credenciales de BD
- Configurar SMTP

4. **Configurar Apache**
- Virtual host apuntando a `/public_html`
- Habilitar mod_rewrite

5. **Permisos**
```bash
chmod 755 -R public_html
chmod 777 -R logs
```

---

## 🔐 Seguridad

### Implementado
- [x] Prepared statements (PDO)
- [x] Sanitización de inputs
- [x] Headers de seguridad
- [x] CSRF protection
- [x] XSS prevention
- [x] SQL Injection prevention
- [x] Rate limiting (formularios)

### Pendiente
- [ ] reCAPTCHA configurado
- [ ] SSL forzado (producción)
- [ ] Backups automáticos
- [ ] Firewall WAF

---

## 📊 SEO Implementado

- [x] Meta tags (title, description, keywords)
- [x] Open Graph (Facebook, LinkedIn)
- [x] Twitter Cards
- [x] Schema.org markup
- [x] Sitemap.xml
- [x] Robots.txt
- [x] Canonical URLs
- [x] Alt text en imágenes
- [x] URLs semánticas

---

## 🎨 Diseño

### Paleta de Colores
- **Primary:** #0066CC (Azul corporativo)
- **Secondary:** #2C3E50 (Gris oscuro)
- **Accent:** #FF6B35 (Naranja)

### Tipografía
- **Títulos:** Montserrat (700/800)
- **Cuerpo:** Open Sans (400/600)

### Responsive Breakpoints
- **xs:** < 576px (móviles)
- **sm:** ≥ 576px (móviles)
- **md:** ≥ 768px (tablets)
- **lg:** ≥ 992px (laptops)
- **xl:** ≥ 1200px (desktops)
- **xxl:** ≥ 1400px (desktops grandes)

---

## 📧 Contacto

### Cliente
- **Email:** marketing@aramedylaboratorio.com
- **Atención:** atencionacliente@aramedylaboratorio.com
- **Tel:** (800) 999-0407

### Desarrollador
- **IDEAMIA Tech**
- **Email:** soporte@ideamia.com.mx
- **Responsable:** Ing. Jorge Alberto Plascencia Correa

---

## 📝 Changelog

### [1.0.0] - 2025-10-13
#### Added
- Estructura base del proyecto
- Configuración PHP completa
- HTML5 semántico
- Sistema CSS modular
- JavaScript modular
- Topbar, Navbar, Footer
- SEO básico implementado
- Seguridad básica

---

## 📄 Licencia

© 2025 Aramed y Laboratorios. Todos los derechos reservados.  
Desarrollado por IDEAMIA – Tech.

---

## 🔗 Links Útiles

- **Documentación:** `/DOCS/`
- **Plan de Desarrollo:** `/DOCS/Plan_Desarrollo_Landing_Fase1.md`
- **Propuesta Cliente:** `/DOCS/Propuesta_sitio_web_Aramed_y_Laboratorio.md`

---

**Última actualización:** 13 de octubre de 2025

