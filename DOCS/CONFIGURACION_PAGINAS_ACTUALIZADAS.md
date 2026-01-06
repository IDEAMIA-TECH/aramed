# Configuración del Admin - Páginas Actualizadas

## Resumen

La página de configuración en `https://aramedylaboratorio.com/admin/configuracion/index.php` permite actualizar diferentes aspectos del sitio web. Este documento explica qué páginas del frontend se ven afectadas por cada configuración.

---

## 1. Tab: Empresa

### Configuraciones Disponibles:
- **Información de la Empresa**: Nombre, Razón Social, Dirección, Teléfonos, Emails
- **Redes Sociales**: Facebook, Instagram, LinkedIn, Twitter

### Páginas que se Actualizan:

#### ✅ **Footer** (`includes/footer.php`)
- **Contacto**: Teléfono y Email principal
- **Redes Sociales**: Enlaces a Facebook, Instagram, LinkedIn, Twitter
- **Ubicación**: Aparece en todas las páginas del sitio (footer global)

#### ✅ **Navbar** (`includes/navbar.php`)
- **Redes Sociales**: Enlaces en el menú móvil
- **Ubicación**: Aparece en todas las páginas del sitio (navbar global)

#### ✅ **Implementado**:
El footer y navbar ahora usan `getConfig()` para leer las configuraciones desde la base de datos, con fallback a las constantes de `config.php` si no hay configuración en la BD.

**Archivos modificados:**
- `includes/footer.php`: Usa `getConfig('empresa_telefono')`, `getConfig('empresa_email')`, `getConfig('empresa_facebook')`, etc.
- `includes/navbar.php`: Usa `getConfig()` para redes sociales en el menú móvil

---

## 2. Tab: SMTP

### Configuraciones Disponibles:
- Servidor SMTP, Puerto, Usuario, Contraseña, Encriptación
- Email y Nombre del Remitente

### Páginas/Sistemas que se Actualizan:

#### ✅ **Formulario de Contacto** (`includes/contact_handler.php`)
- Usa la configuración SMTP para enviar emails cuando alguien envía el formulario de contacto

#### ✅ **Newsletter** (`includes/newsletter_handler.php` y `newsletter_simple_handler.php`)
- Usa la configuración SMTP para enviar emails de confirmación de suscripción

#### ✅ **Comentarios del Blog** (`includes/blog_comment_handler.php`)
- Usa la configuración SMTP para enviar notificaciones de nuevos comentarios

#### ✅ **Test SMTP** (`admin/configuracion/test-smtp.php`)
- Permite probar la configuración SMTP desde el panel de administración

---

## 3. Tab: Integraciones

### Configuraciones Disponibles:
- **Google Analytics**: ID de medición y estado (activo/inactivo)

### Páginas que se Actualizan:

#### ✅ **Todas las Páginas del Sitio** (`includes/analytics.php`)
- El archivo `analytics.php` se incluye en todas las páginas del frontend
- Lee la configuración `analytics_measurement_id` y `analytics_activar_tracking` desde la base de datos
- Si el tracking está activo, inserta el código de Google Analytics (gtag.js) en el `<head>` de todas las páginas

**Páginas afectadas:**
- `index.php` (Home)
- `catalogo.php`
- `producto.php`
- `blog.php`
- `blog-detalle.php`
- `proyectos.php`
- `proyecto.php`
- `privacidad.php`
- `terminos.php`
- `cookies.php`
- Y cualquier otra página que incluya `analytics.php`

---

## 4. Tab: Textos Legales

### Configuraciones Disponibles:
- **Política de Privacidad** (`legal_privacidad`)
- **Términos y Condiciones** (`legal_terminos`)
- **Política de Cookies** (`legal_cookies`)

### Páginas que se Actualizan:

#### ✅ **Implementado**

Las páginas legales ahora leen el contenido desde la base de datos usando `getConfig()`, con fallback al contenido hardcodeado si no hay configuración en la BD.

**Archivos modificados:**
- `privacidad.php`: Muestra `getConfig('legal_privacidad')` si está disponible
- `terminos.php`: Muestra `getConfig('legal_terminos')` si está disponible
- `cookies.php`: Muestra `getConfig('legal_cookies')` si está disponible

**Funcionamiento:**
1. Si hay contenido en la BD → se muestra el contenido HTML desde la BD
2. Si no hay contenido en la BD → se muestra el contenido hardcodeado como fallback
3. Los datos de contacto en estas páginas también usan `getConfig('empresa_email')` y `getConfig('empresa_telefono')`

---

## 5. Tab: SEO

### Configuraciones Disponibles:
- Prefijo y Sufijo para Títulos
- Descripción por Defecto
- Palabras Clave por Defecto
- Imagen Open Graph por Defecto

### Páginas que se Actualizan:

#### ✅ **Sistema Implementado**

Se ha creado un sistema de meta tags SEO que usa las configuraciones por defecto del admin.

**Funciones creadas en `includes/functions.php`:**
- `getSEOMetaTags($title, $description, $keywords, $image, $url)`: Genera un array con todos los meta tags usando configuraciones por defecto
- `renderSEOMetaTags($title, $description, $keywords, $image, $url)`: Renderiza los meta tags en HTML

**Configuraciones usadas:**
- `seo_title_prefix`: Prefijo para títulos (ej: "Aramed y Laboratorios - ")
- `seo_title_suffix`: Sufijo para títulos
- `seo_default_description`: Descripción por defecto
- `seo_default_keywords`: Palabras clave por defecto
- `seo_og_image`: Imagen Open Graph por defecto

**Uso en páginas:**
```php
// Ejemplo de uso en cualquier página:
<?php
$meta = getSEOMetaTags('Mi Página', 'Descripción específica', 'palabras, clave');
?>
<title><?php echo esc($meta['title']); ?></title>
<meta name="description" content="<?php echo esc($meta['description']); ?>">
<!-- etc... -->

// O usar la función de renderizado completa:
<?php echo renderSEOMetaTags('Mi Página', 'Descripción', 'keywords'); ?>
```

**Páginas que pueden usar este sistema:**
- Todas las páginas del sitio pueden usar estas funciones
- Cada página puede sobrescribir los valores por defecto proporcionando sus propios valores

---

## Resumen de Estado

| Tab | Configuración | Estado | Páginas Afectadas |
|-----|---------------|--------|-------------------|
| **Empresa** | Información y Redes Sociales | ✅ Funcional | Footer y Navbar (todas las páginas) |
| **SMTP** | Configuración de Email | ✅ Funcional | Formularios de contacto, newsletter, comentarios |
| **Integraciones** | Google Analytics | ✅ Funcional | Todas las páginas del sitio |
| **Textos Legales** | Políticas y Términos | ✅ Funcional | `privacidad.php`, `terminos.php`, `cookies.php` |
| **SEO** | Meta Tags por Defecto | ✅ Funcional | Sistema disponible para todas las páginas |

---

## Estado de Implementación

✅ **Todas las funcionalidades han sido implementadas:**

1. ✅ **Footer y Navbar**: Ahora usan `getConfig()` para leer configuraciones desde la BD
2. ✅ **Textos Legales**: Las páginas legales leen contenido desde la BD con fallback
3. ✅ **Sistema SEO**: Funciones helper disponibles para generar meta tags usando configuraciones por defecto

## Próximos Pasos (Opcional)

1. **Aplicar sistema SEO en todas las páginas**: Usar `renderSEOMetaTags()` o `getSEOMetaTags()` en todas las páginas del sitio para consistencia
2. **Migrar contenido legal**: Copiar el contenido hardcodeado actual a la base de datos usando el panel de administración
3. **Personalizar meta tags por página**: Usar el sistema SEO en cada página con valores específicos cuando sea necesario

---

## Notas Técnicas

- Todas las configuraciones se almacenan en la tabla `configuracion` de la base de datos
- La función `getConfig($clave, $default)` se usa para leer configuraciones
- La función `setConfig($clave, $valor, $tipo, $categoria)` se usa para guardar configuraciones
- Las configuraciones se organizan por categorías: `empresa`, `smtp`, `integraciones`, `legal`, `seo`
- El sistema tiene un cache estático para mejorar el rendimiento

