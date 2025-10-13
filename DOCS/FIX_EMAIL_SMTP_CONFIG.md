# 🔧 FIX - CONFIGURACIÓN DE EMAIL SMTP

**Fecha:** 13 de Octubre 2025 - 22:00 hrs  
**Prioridad:** CRÍTICA  
**Estado:** ✅ CONFIGURADO

---

## 🐛 PROBLEMA REPORTADO

### Error
```
Error: Error de conexión. Por favor, intenta de nuevo.
```

### Causa
El sistema intentaba enviar emails sin configuración SMTP válida.

---

## ✅ SOLUCIÓN APLICADA

### 1. Configuración SMTP Actualizada

**Archivo:** `includes/config.php`

#### Credenciales del Servidor de Correo

```php
define('SMTP_HOST', 'mail.aramedylaboratorio.com');
define('SMTP_PORT', 465);
define('SMTP_SECURE', 'ssl');
define('SMTP_AUTH', true);
define('SMTP_USERNAME', 'web@aramedylaboratorio.com');
define('SMTP_PASSWORD', 'xpC5OS67rVMNvU2(');

define('MAIL_FROM_EMAIL', 'web@aramedylaboratorio.com');
define('MAIL_FROM_NAME', SITE_NAME);
```

#### Detalles del Servidor
- **Correo:** web@aramedylaboratorio.com
- **Contraseña:** xpC5OS67rVMNvU2(
- **Servidor Entrante:** mail.aramedylaboratorio.com
  - IMAP Port: 993
  - POP3 Port: 995
- **Servidor Saliente:** mail.aramedylaboratorio.com
  - SMTP Port: 465 (SSL)

---

### 2. Sistema de Envío de Emails Creado

**Nuevo Archivo:** `includes/email_functions.php`

#### Características

✅ **Dual Mode:** 
- Usa PHPMailer si está disponible
- Fallback a mail() nativo con headers SMTP

✅ **Funciones Principales:**
- `sendEmail()` - Enviar email individual
- `sendMultipleEmails()` - Enviar múltiples emails
- `validateSMTPConfig()` - Validar configuración
- `testSMTPConnection()` - Probar conexión (debugging)

#### Ejemplo de Uso

```php
$result = sendEmail(
    'destinatario@example.com',
    'Asunto del mensaje',
    '<html><body>Contenido HTML</body></html>',
    'Nombre Destinatario'
);

if ($result['success']) {
    echo "Email enviado!";
} else {
    echo "Error: " . $result['message'];
}
```

---

### 3. Handlers Actualizados

#### Newsletter Handler (`includes/newsletter_handler.php`)

**Cambios:**
- Agregado `require_once` para `email_functions.php`
- Reemplazado `mail()` con `sendEmail()`
- Agregado logging de errores

```php
// ANTES
@mail($to, $subject, $message, $headers);

// DESPUÉS
$emailResult = sendEmail($to, $subject, $message);
if (!$emailResult['success']) {
    error_log("Newsletter Email Error: " . $emailResult['message']);
}
```

#### Contact Handler (`includes/contact_handler.php`)

**Cambios:**
- Agregado `require_once` para `email_functions.php`
- Reemplazados 2 llamadas a `mail()` con `sendEmail()`
- Agregado logging de errores para ambos emails (admin y cliente)

```php
// Email al admin
$emailResult = sendEmail($to, $subject, $message);

// Email de confirmación al cliente
$clientEmailResult = sendEmail(
    $data['email'], 
    $clientSubject, 
    $clientMessage, 
    $data['nombre']
);
```

---

## 📊 ARCHIVOS MODIFICADOS

| Archivo | Cambios | Líneas |
|---------|---------|--------|
| `includes/config.php` | Configuración SMTP | 111-120 |
| `includes/email_functions.php` | **NUEVO** - Sistema de emails | 232 |
| `includes/newsletter_handler.php` | Integración email_functions | 16, 20, 204-210 |
| `includes/contact_handler.php` | Integración email_functions | 16, 20, 172-178, 233-239 |

---

## 🚀 CONFIGURACIÓN DEL SERVIDOR

### Opción 1: PHPMailer (Recomendado)

#### Instalar PHPMailer

```bash
# Via Composer (si está disponible)
composer require phpmailer/phpmailer

# O subir manualmente
# 1. Descargar: https://github.com/PHPMailer/PHPMailer/releases
# 2. Subir a: includes/library/phpmailer/
#    - class.phpmailer.php
#    - class.smtp.php
```

#### Estructura de Carpetas

```
/includes/
  /library/
    /phpmailer/
      - class.phpmailer.php
      - class.smtp.php
```

---

### Opción 2: Configurar PHP mail() con SMTP

Si no puedes instalar PHPMailer, configura el servidor para usar SMTP:

#### En cPanel

1. **Email Routing:**
   - cPanel > Email > Email Routing
   - Seleccionar: "Local Mail Exchanger"

2. **SMTP Configuration:**
   - Verificar que el servidor SMTP esté habilitado
   - Puerto 465 (SSL) o 587 (TLS) abierto

#### En php.ini

```ini
[mail function]
SMTP = mail.aramedylaboratorio.com
smtp_port = 465
sendmail_from = web@aramedylaboratorio.com
```

---

## 🔍 PRUEBAS

### 1. Test de Configuración

Crear archivo temporal: `test-email-config.php` en `public_html/`

```php
<?php
require_once '../includes/config.php';
require_once '../includes/email_functions.php';

// Validar configuración
$validation = validateSMTPConfig();
echo "<h2>Configuración SMTP</h2>";
echo "<pre>";
print_r($validation);
echo "</pre>";

// Test de conexión
$connection = testSMTPConnection();
echo "<h2>Test de Conexión</h2>";
echo "<pre>";
print_r($connection);
echo "</pre>";

// Test de envío
$result = sendEmail(
    'tu-email@test.com',  // Cambiar a tu email
    'Test Email - Aramed',
    '<h1>Test Email</h1><p>Si recibes esto, el sistema funciona correctamente.</p>'
);

echo "<h2>Test de Envío</h2>";
echo "<pre>";
print_r($result);
echo "</pre>";
?>
```

**Uso:**
1. Subir archivo al servidor
2. Acceder: `https://aramedylaboratorio.com/NUEVO/aramed/public_html/test-email-config.php`
3. Revisar resultados
4. **ELIMINAR** el archivo después de probar

---

### 2. Test desde Formulario

1. Ir a la página: `https://aramedylaboratorio.com/NUEVO/aramed/public_html/`
2. Scroll a "Mantente Informado"
3. Llenar formulario completo
4. Click "Enviar"
5. Verificar:
   - ✅ Mensaje de éxito en pantalla
   - ✅ Email recibido en CONTACT_EMAIL
   - ✅ No hay errores en consola

---

## 🐛 TROUBLESHOOTING

### Error: "Class 'PHPMailer' not found"

**Causa:** PHPMailer no está instalado

**Solución:**
1. Subir carpeta `phpmailer/` a `includes/library/`
2. O el sistema usará `mail()` automáticamente

---

### Error: "SMTP connect() failed"

**Causa:** No puede conectarse al servidor SMTP

**Solución:**
1. Verificar credenciales en `config.php`
2. Verificar que puerto 465 esté abierto
3. Contactar al proveedor de hosting
4. Probar con puerto 587 y TLS:
   ```php
   define('SMTP_PORT', 587);
   define('SMTP_SECURE', 'tls');
   ```

---

### Error: "Could not instantiate mail function"

**Causa:** `mail()` de PHP no está configurado

**Solución:**
1. Instalar PHPMailer (Opción 1)
2. O contactar hosting para configurar SMTP

---

### Emails no se reciben

**Verificar:**
1. ✅ Carpeta de Spam
2. ✅ Configuración de firewall
3. ✅ Límites de envío del servidor
4. ✅ Logs del servidor: `/home/usuario/logs/`

---

## 📝 LOGS Y DEBUGGING

### Ver Errores de Email

```bash
# En el servidor
tail -f /path/to/php_error.log | grep "Email Error"
tail -f /path/to/php_error.log | grep "PHPMailer"
```

### Habilitar Debugging (Temporal)

En `email_functions.php` línea 89-98, cambiar:

```php
// ANTES
$mail->SMTPDebug  = 0;  // Sin debug

// DURANTE PRUEBAS
$mail->SMTPDebug  = 2;  // Ver toda la comunicación SMTP

// DESPUÉS DE ARREGLAR
$mail->SMTPDebug  = 0;  // Desactivar debug
```

---

## ✅ CHECKLIST FINAL

### Configuración
- [x] SMTP credentials en `config.php`
- [ ] PHPMailer instalado (opcional)
- [x] `email_functions.php` creado
- [x] Handlers actualizados
- [ ] Archivos subidos al servidor

### Testing
- [ ] Test de configuración ejecutado
- [ ] Test de conexión SMTP exitoso
- [ ] Email de prueba enviado
- [ ] Email recibido correctamente
- [ ] Formulario newsletter probado
- [ ] Formulario contacto probado

### Seguridad
- [ ] Archivo de test eliminado
- [ ] Debugging desactivado
- [ ] Contraseña SMTP segura
- [ ] Acceso a `config.php` restringido

---

## 🔐 SEGURIDAD

### Proteger Credenciales

**`.htaccess` en `/includes/`:**

```apache
# Proteger archivos PHP sensibles
<Files "config.php">
    Order Allow,Deny
    Deny from all
</Files>

<Files "email_functions.php">
    Order Allow,Deny
    Deny from all
</Files>
```

### Cambiar Contraseña Regularmente

Actualizar `SMTP_PASSWORD` en `config.php` cada 3-6 meses.

---

## 📞 SOPORTE

### Si el problema persiste:

1. **Revisar logs del servidor**
2. **Contactar al hosting:** Verificar configuración SMTP
3. **Verificar cortafuegos:** Puerto 465/587 abierto
4. **Revisar límites:** Algunos hostings limitan envíos por hora

### Contacto Técnico

- **Hosting:** cPanel de aramedylaboratorio.com
- **Email técnico:** web@aramedylaboratorio.com
- **Documentación:** Este archivo

---

## 🎉 RESULTADO ESPERADO

✅ Formularios envían emails correctamente  
✅ Usuario recibe confirmación  
✅ Admin recibe notificación  
✅ Sin errores de conexión  
✅ Logs limpios  

---

**Última actualización:** 13 de Octubre 2025 - 22:00 hrs
