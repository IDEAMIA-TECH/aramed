# 🔐 CONFIGURACIÓN DE VARIABLES DE ENTORNO
## Aramed y Laboratorios - Landing Page

---

## 📋 CREDENCIALES DE BASE DE DATOS

### Producción
```
Host:     173.231.22.109
Database: aramed2025_produccion
User:     aramed2025_prod
Password: pmDLi&PB$zntrzJ4
Charset:  utf8mb4
```

### Local (Desarrollo)
```
Host:     localhost
Database: aramed_local
User:     root
Password: [tu-password-local]
Charset:  utf8mb4
```

---

## 🌐 CONFIGURACIÓN DEL SITIO

```php
SITE_URL     = https://www.aramedylaboratorio.com
SITE_DOMAIN  = aramedylaboratorio.com
SITE_ENV     = production
```

---

## 📧 CONFIGURACIÓN DE EMAILS

### Emails de Contacto
```php
CONTACT_EMAIL   = atencionacliente@aramedylaboratorio.com
MARKETING_EMAIL = marketing@aramedylaboratorio.com
SUPPORT_EMAIL   = soporte@ideamia.com.mx
```

### SMTP (Opcional - para envío robusto)
```php
SMTP_HOST     = smtp.gmail.com
SMTP_PORT     = 587
SMTP_SECURE   = tls
SMTP_USERNAME = your-email@gmail.com
SMTP_PASSWORD = your-app-password
```

**Nota:** Para Gmail, necesitas crear una "App Password":
1. Ve a Google Account Settings
2. Security > 2-Step Verification
3. App passwords > Generate

---

## 🔒 GOOGLE RECAPTCHA v3 (Opcional)

Para protección contra spam en formularios:

```php
RECAPTCHA_SITE_KEY   = your_site_key_here
RECAPTCHA_SECRET_KEY = your_secret_key_here
```

**Obtener claves en:** https://www.google.com/recaptcha/admin

---

## 📊 ANALYTICS & TRACKING

### Google Analytics
```php
GA_TRACKING_ID = G-XXXXXXXXXX
```

### Facebook Pixel (Opcional)
```php
FB_PIXEL_ID = your_pixel_id_here
```

---

## ⚙️ CONFIGURACIÓN DE SEGURIDAD

### Session
```php
SESSION_TIMEOUT = 3600  // En segundos (1 hora)
```

### Debug Mode
```php
DEBUG_MODE = false  // true para desarrollo, false para producción
```

---

## 📝 INSTRUCCIONES DE IMPLEMENTACIÓN

### 1. Actualizar `includes/config.php`

```php
<?php
// ========================================
// CONFIGURACIÓN DE BASE DE DATOS
// ========================================

define('DB_HOST', '173.231.22.109');
define('DB_NAME', 'aramed2025_produccion');
define('DB_USER', 'aramed2025_prod');
define('DB_PASS', 'pmDLi&PB$zntrzJ4');
define('DB_CHARSET', 'utf8mb4');
```

### 2. Importar Base de Datos

```bash
# Conectar al servidor
mysql -h 173.231.22.109 -u aramed2025_prod -p aramed2025_produccion

# Importar tablas
mysql -h 173.231.22.109 -u aramed2025_prod -p aramed2025_produccion < database/landing_tables.sql
```

### 3. Verificar Conexión

Crear archivo `test-connection.php` en raíz:

```php
<?php
require_once 'includes/config.php';
require_once 'includes/connection.php';

echo "Testing database connection...\n";

try {
    $pdo->query("SELECT 1");
    echo "✅ Conexión exitosa!\n";
    echo "Database: " . DB_NAME . "\n";
    echo "Host: " . DB_HOST . "\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
```

Ejecutar: `php test-connection.php`

---

## 🚨 IMPORTANTE - SEGURIDAD

### ⚠️ NUNCA SUBIR ESTAS CREDENCIALES A GIT

1. **Asegurar que `config.php` esté en `.gitignore`**
   ```
   includes/config.php
   .env
   test-connection.php
   ```

2. **Para múltiples entornos, usar variables de entorno PHP**
   ```php
   define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
   define('DB_NAME', getenv('DB_NAME') ?: 'aramed_local');
   define('DB_USER', getenv('DB_USER') ?: 'root');
   define('DB_PASS', getenv('DB_PASS') ?: '');
   ```

3. **Backup regular de base de datos**
   ```bash
   # Exportar backup
   mysqldump -h 173.231.22.109 -u aramed2025_prod -p aramed2025_produccion > backup_$(date +%Y%m%d).sql
   ```

---

## 🔍 TROUBLESHOOTING

### Error: "SQLSTATE[HY000] [2002] Connection refused"
- Verificar que el host permite conexiones remotas
- Revisar firewall del servidor
- Confirmar credenciales

### Error: "Access denied for user"
- Verificar usuario y contraseña
- Confirmar que el usuario tiene permisos en la base de datos
- Revisar que el host tiene permisos de acceso remoto

### Error: "Unknown database"
- Confirmar que la base de datos existe
- Verificar nombre exacto (case-sensitive en algunos servidores)

---

## 📞 SOPORTE

Para problemas con la configuración:
- **Hosting:** Contactar soporte del servidor
- **Desarrollo:** IDEAMIA Tech
- **Email:** soporte@ideamia.com.mx

---

**Última actualización:** 13 de Octubre, 2025  
**Versión:** 1.0


