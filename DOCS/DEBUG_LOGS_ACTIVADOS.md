# 🔍 LOGS DE DEBUG ACTIVADOS PARA EMAIL

**Fecha:** 13 de Octubre 2025 - 22:30 hrs  
**Estado:** ✅ LOGS COMPLETOS IMPLEMENTADOS

---

## 🎯 OBJETIVO

Agregar logging exhaustivo para identificar exactamente dónde está fallando el envío de emails del formulario de newsletter.

---

## 📝 ARCHIVOS MODIFICADOS CON LOGS

### 1. `includes/email_functions.php`

**Logs agregados:**
- ✅ Configuración SMTP (Host, Port, User, Password Set)
- ✅ Método de envío utilizado (PHPMailer vs mail() nativo)
- ✅ Headers del email
- ✅ Parámetros adicionales
- ✅ Resultado de mail() (TRUE/FALSE)
- ✅ Errores de PHP capturados
- ✅ Debug de PHPMailer (nivel 2 - completo)
- ✅ Excepciones con stack trace

**Líneas agregadas:** ~50 líneas de logging

---

### 2. `includes/newsletter_handler.php`

**Logs agregados:**
- ✅ Inicio del request (método, timestamp)
- ✅ Datos recibidos (institución, email, nombre, privacidad)
- ✅ Validación de campos obligatorios
- ✅ Validación de email
- ✅ Validación de política de privacidad
- ✅ Verificación de suscripción existente
- ✅ Operaciones de base de datos (INSERT)
- ✅ ID de registro insertado
- ✅ Envío de email de notificación
- ✅ Excepciones detalladas (mensaje, archivo, línea, stack trace)

**Líneas agregadas:** ~40 líneas de logging

---

### 3. `public_html/test-email-debug.php` ⭐ NUEVO

**Script de testing visual:**
- ✅ Verificación de configuración SMTP
- ✅ Validación de constantes
- ✅ Estado de PHPMailer
- ✅ Test de conexión SMTP
- ✅ Variables de entorno PHP
- ✅ Funciones disponibles (mail(), fsockopen, etc.)
- ✅ Interfaz visual tipo terminal

**⚠️ IMPORTANTE:** Este archivo debe ser **ELIMINADO después del debugging**

---

## 🚀 CÓMO USAR

### PASO 1: Subir Archivos al Servidor

Subir estos archivos via FTP/cPanel:

```
✅ includes/email_functions.php (con logs)
✅ includes/newsletter_handler.php (con logs)
✅ includes/contact_handler.php (actualizado)
✅ public_html/test-email-debug.php (NUEVO - temporal)
```

---

### PASO 2: Ejecutar Script de Debug

1. Abrir en el navegador:
   ```
   https://aramedylaboratorio.com/NUEVO/aramed/public_html/test-email-debug.php
   ```

2. **Revisar cada sección:**
   - ✅ Configuración SMTP (debe mostrar todos los valores)
   - ✅ Validación (debe estar OK)
   - ✅ PHPMailer (indicará si está disponible o no)
   - ✅ PHP Functions (mail() debe estar OK)
   - ✅ Test de Conexión SMTP (¡IMPORTANTE!)

3. **Captura de pantalla** de la página completa

---

### PASO 3: Probar el Formulario

1. Ir a la página principal
2. Scroll a "Mantente Informado"
3. **Abrir la consola del navegador** (F12)
4. **Llenar el formulario completo**
5. **Click en "Enviar"**
6. **Observar la consola** para ver errores JavaScript

---

### PASO 4: Revisar Logs del Servidor

#### Opción A: cPanel

1. Ir a cPanel
2. Buscar "Errors" o "Error Log"
3. Abrir el error log más reciente
4. Buscar estas líneas:

```
===== EMAIL SEND ATTEMPT =====
NEWSLETTER HANDLER - START
--- Data Sanitization ---
--- Field Validation ---
--- Database Operations ---
--- Email Sending ---
```

#### Opción B: SSH (si tienes acceso)

```bash
# Ver últimas 100 líneas del log
tail -100 /path/to/php_error.log

# Ver logs en tiempo real
tail -f /path/to/php_error.log

# Buscar solo errores de email
grep "EMAIL" /path/to/php_error.log
grep "❌" /path/to/php_error.log
```

---

## 🔍 QUÉ BUSCAR EN LOS LOGS

### Secuencia EXITOSA esperada:

```
========================================
NEWSLETTER HANDLER - START
Request Method: POST
✅ POST request received
--- Data Sanitization ---
Received data:
  - Institución: [nombre]
  - Email: [email]
✅ All required fields present
✅ Privacy policy accepted
✅ Email validation passed
--- Database Operations ---
Checking for existing subscription...
✅ Email not subscribed yet
Preparing INSERT query...
Executing INSERT...
✅ INSERT successful. ID: 123
--- Email Sending ---
Sending notification email to: atencionacliente@aramedylaboratorio.com
===== EMAIL SEND ATTEMPT =====
To: atencionacliente@aramedylaboratorio.com
Subject: Nueva suscripción al Newsletter - [Institución]
PHPMailer Available: NO
SMTP Host: mail.aramedylaboratorio.com
SMTP Port: 465
SMTP User: web@aramedylaboratorio.com
SMTP Pass Set: YES
Using: Native mail()
Headers: [...]
Attempting mail() function...
mail() result: TRUE
✅ Email sent successfully via mail()
✅ Newsletter Email sent successfully
✅ Newsletter subscription completed successfully
========================================
```

### Errores COMUNES a buscar:

#### Error 1: Conexión SMTP Fallida
```
❌ mail() failed: SMTP connect() failed
```
**Solución:** Puerto 465 bloqueado o servidor SMTP no accesible

#### Error 2: Autenticación Fallida
```
❌ SMTP Error: Could not authenticate
```
**Solución:** Usuario o contraseña incorrectos

#### Error 3: Base de Datos
```
❌ INSERT failed
PDO Error: [...]
```
**Solución:** Tabla no existe o credenciales de BD incorrectas

#### Error 4: mail() no disponible
```
❌ mail() function not available
```
**Solución:** Función mail() deshabilitada en el servidor

---

## 📊 INFORMACIÓN QUE NECESITO

Si el problema persiste, envíame:

1. **Screenshot completo** de `test-email-debug.php`
2. **Últimas 50 líneas** del error log del servidor
3. **Screenshot** de la consola del navegador al enviar el formulario
4. **Mensaje de error** exacto que aparece en pantalla

---

## 🔧 SOLUCIONES RÁPIDAS

### Si el test de conexión SMTP falla:

**Opción 1:** Usar puerto 587 con TLS
```php
// En includes/config.php
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
```

**Opción 2:** Contactar hosting
- Verificar que puerto 465/587 esté abierto
- Verificar que mail() esté habilitado
- Verificar configuración SMTP del servidor

**Opción 3:** Instalar PHPMailer
- Más confiable que mail() nativo
- Mejor manejo de errores
- Ver: `DOCS/FIX_EMAIL_SMTP_CONFIG.md`

---

## ⚠️ IMPORTANTE - SEGURIDAD

### Después del debugging:

1. **ELIMINAR** `public_html/test-email-debug.php`
   ```bash
   rm public_html/test-email-debug.php
   ```

2. **OPCIONAL:** Desactivar logs excesivos (comentar error_log() no críticos)

3. **VERIFICAR:** Que `includes/config.php` no sea accesible vía web

---

## 📞 SOPORTE

Si necesitas ayuda para interpretar los logs, envíame:

1. La información mencionada arriba
2. Nombre de tu proveedor de hosting
3. Panel de control disponible (cPanel, Plesk, otro)

---

**Última actualización:** 13 de Octubre 2025 - 22:30 hrs
