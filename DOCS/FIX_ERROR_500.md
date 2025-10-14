# 🔧 FIX - ERROR 500 (Internal Server Error)

**Fecha:** 13 de Octubre 2025 - 23:55 hrs  
**Estado:** 🔍 EN DIAGNÓSTICO

---

## 🐛 PROBLEMA

```
GET https://aramedylaboratorio.com/NUEVO/aramed/public_html/ 
net::ERR_HTTP_RESPONSE_CODE_FAILURE 500 (Internal Server Error)
```

**Error 500** significa que hay un error PHP que impide que la página se cargue.

---

## 🔍 DIAGNÓSTICO

### PASO 1: Ejecutar Script de Diagnóstico

He creado un archivo especial para identificar el problema:

```
https://aramedylaboratorio.com/NUEVO/aramed/public_html/diagnose.php
```

**Este script verificará:**
- ✅ PHP está funcionando
- ✅ Archivos includes/ existen
- ✅ config.php se puede cargar
- ✅ functions.php se puede cargar
- ✅ Constantes están definidas
- ✅ Funciones existen

---

## 🎯 CAUSAS POSIBLES

### Causa 1: Archivos no subidos correctamente

**Síntoma:** 
```
❌ includes/config.php NO EXISTE
```

**Solución:**
- Subir la carpeta `includes/` completa al servidor
- Verificar que todos los archivos PHP estén en `public_html/includes/`

---

### Causa 2: Permisos incorrectos

**Síntoma:** 
```
Warning: require_once(includes/config.php): failed to open stream: Permission denied
```

**Solución:**
```bash
chmod 755 includes/
chmod 644 includes/*.php
```

---

### Causa 3: Error en config.php

**Síntoma:**
```
Parse error: syntax error, unexpected 'X' in config.php on line XX
```

**Solución:**
- Verificar que config.php no tenga errores de sintaxis
- Volver a subir el archivo

---

### Causa 4: Constantes no definidas

**Síntoma:**
```
Use of undefined constant SITE_NAME
```

**Solución:**
- Asegurar que config.php se carga correctamente
- Verificar que todas las constantes estén definidas

---

### Causa 5: ROOT_PATH incorrecto

**Síntoma:**
```
require_once(ROOT_PATH . '/includes/config.php'): failed to open stream
```

**Solución:**
- Ya corregido en los archivos
- `ROOT_PATH` ahora usa `__DIR__` en lugar de `dirname(__DIR__)`

---

## 🔧 PASOS DE SOLUCIÓN

### PASO 1: Ejecutar diagnose.php

```
https://aramedylaboratorio.com/NUEVO/aramed/public_html/diagnose.php
```

Tomar screenshot del resultado completo.

---

### PASO 2: Revisar Error Log del Servidor

En cPanel:
1. Ir a "Error Log" o "Logs"
2. Buscar el error más reciente
3. Copiar las últimas 20 líneas

---

### PASO 3: Verificar Archivos Subidos

En File Manager o FTP:
```
public_html/
├── index.php ✅
├── diagnose.php ✅ (NUEVO)
├── includes/ ✅
│   ├── config.php ✅
│   ├── connection.php ✅
│   ├── functions.php ✅
│   ├── topbar.php ✅
│   ├── navbar.php ✅
│   ├── footer.php ✅
│   ├── newsletter_handler.php ✅
│   ├── contact_handler.php ✅
│   ├── email_functions.php ✅
│   └── debug_logger.php ✅
└── logs/
    └── (vacío está bien)
```

---

### PASO 4: Verificar Permisos

```bash
# Via SSH
cd public_html
chmod 755 includes
chmod 755 logs
chmod 644 includes/*.php
chmod 644 *.php

# Via cPanel File Manager
# Seleccionar includes/ → Permisos → 755
# Seleccionar archivos .php → Permisos → 644
```

---

## 📊 INFORMACIÓN NECESARIA

Si el problema persiste, necesito:

1. **Screenshot de diagnose.php** (completo)
2. **Últimas 20 líneas del error log** del servidor
3. **Lista de archivos** en public_html/includes/
4. **Permisos** de las carpetas

---

## 🚀 SOLUCIÓN RÁPIDA

### Si no puedes acceder a nada:

1. **Crear archivo PHP simple:**

Crear `public_html/test.php`:
```php
<?php
echo "PHP funciona!";
phpinfo();
?>
```

2. **Acceder:**
```
https://aramedylaboratorio.com/NUEVO/aramed/public_html/test.php
```

Si esto funciona pero index.php no, el problema está en los includes.

---

## 💡 SOLUCIONES COMUNES

### Solución 1: Re-subir includes/

1. Eliminar carpeta `includes/` del servidor
2. Volver a subir carpeta `includes/` completa
3. Verificar permisos

### Solución 2: Verificar PHP version

El sitio requiere PHP 7.4 o superior.

En cPanel:
- MultiPHP Manager
- Seleccionar dominio
- Cambiar a PHP 8.1 o 8.2

### Solución 3: Deshabilitar includes temporalmente

En `index.php`, comentar los includes uno por uno para identificar cuál falla:

```php
<?php
// require_once 'includes/config.php';
echo "Test 1: Sin config.php";
exit;
?>
```

Luego descomentar y probar con el siguiente.

---

## 📞 PRÓXIMOS PASOS

1. **Ejecutar diagnose.php** y enviarme screenshot
2. **Revisar error log** del servidor
3. **Verificar archivos subidos**
4. Con esa información sabré exactamente qué está fallando

---

**Última actualización:** 13 de Octubre 2025 - 23:55 hrs
