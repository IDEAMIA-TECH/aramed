# 🎨 GUÍA DE MARCA - ARAMED Y LABORATORIO
## Brand Guidelines & Visual Identity

**Actualizado:** 13 de Octubre, 2025  
**Versión:** 1.0

---

## 📋 ÍNDICE

1. [Logo](#logo)
2. [Paleta de Colores](#paleta-de-colores)
3. [Tipografía](#tipografía)
4. [Uso del Logo](#uso-del-logo)
5. [Elementos Visuales](#elementos-visuales)

---

## 🏷️ LOGO

### Descripción
El logo de **Aramed y Laboratorio** representa la fusión entre la medicina y el alcance global de la empresa. Consta de:
- **Elemento principal**: Estetoscopio estilizado
- **Elemento secundario**: Globo terráqueo con líneas de latitud/longitud
- **Tipografía**: "ARAMED" en serif negro, "Y LABORATORIO" en serif azul

### Archivos Disponibles

| Archivo | Dimensiones | Uso |
|---------|-------------|-----|
| `logo.png` | 1310x497px | Principal (web, impresión) |
| `logo-og.png` | 1310x497px | Open Graph / Social Media |
| `logo-square.png` | 500x500px | *Pendiente - Avatar, favicon* |
| `logo-white.png` | 1310x497px | *Pendiente - Fondos oscuros* |

### Especificaciones Técnicas
```
Formato: PNG con transparencia (RGBA)
Resolución original: 1310 x 497 pixels
Aspect Ratio: 2.64:1 (horizontal)
Tamaño de archivo: ~154KB
Color depth: 8-bit/color
```

---

## 🎨 PALETA DE COLORES

### Colores Primarios

#### Azul Aramed (Primary)
```css
/* Azul del globo terráqueo y "Y LABORATORIO" */
Color: #0B9FD9 (aproximado)
RGB: 11, 159, 217
HSL: 194°, 90%, 45%
CMYK: 95%, 27%, 0%, 15%

Uso:
- Elementos de marca principales
- CTAs primarios
- Enlaces
- Iconos destacados
- Globo terráqueo en logo
```

#### Negro Aramed (Secondary)
```css
/* Negro del estetoscopio y "ARAMED" */
Color: #000000 / #1A1A1A
RGB: 26, 26, 26
HSL: 0°, 0%, 10%

Uso:
- Texto principal
- Títulos
- Estetoscopio en logo
- Tipografía "ARAMED"
```

#### Gris Aramed (Tertiary)
```css
/* Gris del estetoscopio (sombreado) */
Color: #808080 / #999999
RGB: 153, 153, 153
HSL: 0°, 0%, 60%

Uso:
- Sombras del estetoscopio
- Texto secundario
- Bordes sutiles
```

### Colores de Soporte

#### Blanco
```css
Color: #FFFFFF
Uso: Fondos, texto sobre fondos oscuros
```

#### Azul Claro (Tints)
```css
/* Variaciones para UI */
Azul 90%: #E5F6FC
Azul 80%: #CCE dF9
Azul 60%: #66CBE9
Azul 40%: #3DB5E0
```

#### Azul Oscuro (Shades)
```css
/* Para hover states y profundidad */
Azul 120%: #0988BA
Azul 140%: #07709B
```

### Colores Funcionales

#### Success (Éxito)
```css
Color: #28A745
RGB: 40, 167, 69
Uso: Mensajes de éxito, confirmaciones
```

#### Warning (Advertencia)
```css
Color: #FFC107
RGB: 255, 193, 7
Uso: Alertas, advertencias
```

#### Error (Error)
```css
Color: #DC3545
RGB: 220, 53, 69
Uso: Errores, validaciones fallidas
```

#### Info (Información)
```css
Color: #17A2B8
RGB: 23, 162, 184
Uso: Mensajes informativos
```

---

## 📝 TIPOGRAFÍA

### Fuentes del Logo

#### "ARAMED" (Título Principal)
```
Familia: Serif (Trajan Pro / Similar)
Peso: Bold/Heavy
Color: Negro (#000000)
Características: Mayúsculas, espaciado amplio
```

#### "Y LABORATORIO" (Subtítulo)
```
Familia: Serif (Trajan Pro / Similar)
Peso: Regular/Medium
Color: Azul Aramed (#0B9FD9)
Características: Mayúsculas, más delgado que "ARAMED"
```

### Fuentes Web (Recomendadas)

#### Títulos
```css
font-family: 'Playfair Display', 'Georgia', serif;
/* Alternativa: 'Cinzel', 'Cormorant', 'Libre Baskerville' */
```

#### Cuerpo de Texto
```css
font-family: 'Inter', 'Roboto', -apple-system, sans-serif;
```

#### Alternativa Moderna
```css
Títulos: 'Montserrat', sans-serif (Bold 700)
Cuerpo: 'Open Sans', sans-serif (Regular 400, Medium 500)
```

---

## 🎯 USO DEL LOGO

### Espacio Mínimo de Protección

```
Espacio = Altura de la "A" en "ARAMED"
Mantener este espacio libre alrededor del logo en todos los lados
```

### Tamaño Mínimo

```
Digital: 180px de ancho mínimo
Impresión: 3cm de ancho mínimo
```

### Fondos Permitidos

✅ **Recomendado:**
- Fondo blanco (#FFFFFF)
- Fondo gris muy claro (#F8F9FA)
- Fondo azul muy claro (#E5F6FC)

⚠️ **Con precaución:**
- Fondos de color (usar versión en blanco si se crea)
- Fotografías (colocar en área con poco detalle)

❌ **No permitido:**
- Fondos muy oscuros (sin versión blanca del logo)
- Fondos con mucho detalle que dificulten legibilidad
- Fondos con colores que compitan con el azul del logo

### Lo Que NO Se Debe Hacer

❌ No cambiar los colores del logo  
❌ No distorsionar las proporciones  
❌ No agregar efectos (sombras, brillos, degradados)  
❌ No rotar el logo  
❌ No separar elementos (estetoscopio / globo / texto)  
❌ No usar versiones de baja resolución  
❌ No colocar sobre fondos que dificulten la legibilidad  

---

## 🖼️ ELEMENTOS VISUALES

### Iconografía

**Estilo:**
- Line icons con grosor de 2px
- Color: Azul Aramed (#0B9FD9) o Negro (#1A1A1A)
- Esquinas: Redondeadas (border-radius: 2px)

**Fuentes Recomendadas:**
- Bootstrap Icons
- Feather Icons
- Font Awesome (outline style)

### Fotografía

**Características:**
- Imágenes limpias y profesionales
- Enfoque en productos y tecnología médica
- Tonos: Azules y neutros
- Iluminación: Brillante, clínica
- Estilo: Moderno, técnico, confiable

**Filtros/Tratamiento:**
- Saturación: Normal a ligeramente aumentada
- Contraste: Medio-Alto
- Overlay azul sutil cuando sea apropiado (opacity: 10-20%)

### Ilustraciones

**Estilo:**
- Flat design / Semi-flat
- Uso de la paleta de colores de marca
- Énfasis en el azul Aramed
- Líneas limpias y formas geométricas simples

---

## 📐 ESPECIFICACIONES TÉCNICAS

### Logo para Web

```html
<!-- Navbar (altura ~50-60px) -->
<img src="/assets/images/design/logo.png" 
     alt="Aramed y Laboratorio" 
     width="200" 
     height="76">

<!-- Hero/Header (altura ~80-100px) -->
<img src="/assets/images/design/logo.png" 
     alt="Aramed y Laboratorio" 
     width="328" 
     height="124">

<!-- Footer (altura ~40px) -->
<img src="/assets/images/design/logo.png" 
     alt="Aramed y Laboratorio" 
     width="164" 
     height="62">
```

### CSS Variables

```css
:root {
    /* Colores de marca */
    --color-primary: #0B9FD9;
    --color-primary-light: #3DB5E0;
    --color-primary-dark: #0988BA;
    --color-secondary: #1A1A1A;
    --color-tertiary: #999999;
    
    /* Backgrounds */
    --bg-white: #FFFFFF;
    --bg-light: #F8F9FA;
    --bg-primary-light: #E5F6FC;
    
    /* Texto */
    --text-primary: #1A1A1A;
    --text-secondary: #6C757D;
    --text-light: #999999;
    
    /* Estados */
    --color-success: #28A745;
    --color-warning: #FFC107;
    --color-error: #DC3545;
    --color-info: #17A2B8;
}
```

---

## 📱 IMPLEMENTACIÓN

### Checklist de Integración

- [x] Logo principal copiado a `/assets/images/design/logo.png`
- [x] Logo OG copiado a `/assets/images/design/logo-og.png`
- [ ] Crear logo cuadrado para favicon (500x500px)
- [ ] Crear favicon.ico (32x32, 16x16)
- [ ] Crear versión del logo en blanco para fondos oscuros
- [ ] Actualizar referencias en navbar.php
- [ ] Actualizar referencias en footer.php
- [ ] Actualizar meta tags OG
- [ ] Implementar CSS variables con colores de marca
- [ ] Documentar en style guide del proyecto

---

## 📦 ASSETS NECESARIOS (Pendientes)

### Para Crear:

1. **Logo Cuadrado (500x500px)**
   - Usar solo el icono del estetoscopio + globo
   - Sin texto
   - Para: Favicon, avatar, redes sociales cuadradas

2. **Logo Vertical**
   - Icono arriba, texto abajo
   - Para: Banners verticales, stories

3. **Logo Horizontal Compacto**
   - Versión más comprimida
   - Para: Headers móviles

4. **Logo Monocromático**
   - Todo en negro
   - Para: Documentos oficiales, fax, monocromo

5. **Logo en Blanco**
   - Todo en blanco
   - Para: Fondos oscuros, videos

6. **Favicon Package**
   - favicon.ico (multi-size: 16x16, 32x32, 48x48)
   - apple-touch-icon.png (180x180)
   - android-chrome-192x192.png
   - android-chrome-512x512.png

---

## 🔗 RECURSOS

### Herramientas Online

- **Favicon Generator**: https://realfavicongenerator.net/
- **Image Optimizer**: https://tinypng.com/
- **Color Picker**: https://imagecolorpicker.com/
- **Contrast Checker**: https://webaim.org/resources/contrastchecker/

### Referencias de Diseño

- Material Design: https://material.io/design
- Brand Guidelines Gallery: https://brandguidelines.io/

---

## ✅ APROBACIONES

| Versión | Fecha | Aprobado por | Notas |
|---------|-------|--------------|-------|
| 1.0 | 2025-10-13 | IDEAMIA Tech | Versión inicial |
| - | - | Cliente | *Pendiente aprobación* |

---

**Contacto para dudas sobre la marca:**  
IDEAMIA Tech - soporte@ideamia.com.mx

---

*Este documento es una guía viva y debe actualizarse conforme evolucione la identidad visual de Aramed y Laboratorio.*


