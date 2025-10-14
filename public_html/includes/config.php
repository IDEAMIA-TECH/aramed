<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Configuración
 * ========================================
 * 
 * Archivo de configuración principal del sitio
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Prevenir acceso directo
if (!defined('ARAMED_SITE')) {
    define('ARAMED_SITE', true);
}

// ========================================
// CONFIGURACIÓN DE ENTORNO
// ========================================

// Entorno: 'development' o 'production'
define('ENVIRONMENT', 'production');

// Mostrar errores en desarrollo
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php-errors.log');
}

// ========================================
// CONFIGURACIÓN DE BASE DE DATOS
// ========================================

define('DB_HOST', '173.231.22.109');
define('DB_NAME', 'aramed2025_produccion');
define('DB_USER', 'aramed2025_prod');
define('DB_PASS', 'pmDLi&PB$zntrzJ4');
define('DB_CHARSET', 'utf8mb4');

// ========================================
// CONFIGURACIÓN DEL SITIO
// ========================================

// URLs
define('SITE_URL', (ENVIRONMENT === 'development') 
    ? 'http://localhost/aramed' 
    : 'https://aramedylaboratorio.com/NUEVO/aramed/public_html'
);
define('ASSETS_URL', SITE_URL . '/assets');
define('IMAGES_URL', ASSETS_URL . '/images');

// Rutas del servidor
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('PAGES_PATH', ROOT_PATH . '/public_html/pages');
define('ASSETS_PATH', ROOT_PATH . '/public_html/assets');
define('UPLOADS_PATH', ROOT_PATH . '/public_html/uploads');

// Información del sitio
define('SITE_NAME', 'Aramed y Laboratorios');
define('SITE_DOMAIN', 'aramedylaboratorio.com'); // Dominio del sitio
define('SITE_TAGLINE', 'Simuladores médicos para la enseñanza');
define('SITE_DESCRIPTION', 'Distribuidores líderes de tecnología educativa en salud. Simuladores médicos de alta fidelidad para instituciones educativas y de salud.');
define('SITE_KEYWORDS', 'simuladores médicos, educación médica, simulación clínica, tecnología educativa, maniquíes médicos, entrenamiento médico');

// ========================================
// CONFIGURACIÓN DE CONTACTO
// ========================================

// Emails
define('CONTACT_EMAIL', 'atencionacliente@aramedylaboratorio.com');
define('CONTACT_PHONE', '+52 (55) 1234-5678'); // Teléfono de contacto principal
define('MARKETING_EMAIL', 'marketing@aramedylaboratorio.com');
define('SUPPORT_EMAIL', 'soporte@ideamia.com.mx');

// Teléfonos
define('PHONE_MAIN', '(800) 999-0407');
define('PHONE_FORMATTED', '8009990407');

// Horarios
define('SCHEDULE_WEEKDAY', 'Lunes a Viernes: 9:00–14:00 y 16:00–19:00');
define('SCHEDULE_SATURDAY', 'Sábados: 10:00–14:00');

// Dirección
define('ADDRESS_STREET', 'Club de Golf Atlas 535 Int 20');
define('ADDRESS_CITY', 'Tlaquepaque, Jalisco');
define('ADDRESS_ZIP', '45623');
define('ADDRESS_COUNTRY', 'México');

// ========================================
// REDES SOCIALES
// ========================================

define('SOCIAL_FACEBOOK', 'https://www.facebook.com/aramedylaboratorio');
define('SOCIAL_INSTAGRAM', 'https://www.instagram.com/aramedylaboratorio');
define('SOCIAL_LINKEDIN', 'https://www.linkedin.com/company/aramedylaboratorio');
define('SOCIAL_TWITTER', 'https://twitter.com/aramedylab');

// ========================================
// CONFIGURACIÓN DE EMAIL (SMTP)
// ========================================

define('SMTP_HOST', 'mail.aramedylaboratorio.com'); // Servidor SMTP del cliente
define('SMTP_PORT', 465);
define('SMTP_SECURE', 'ssl'); // 'tls' o 'ssl'
define('SMTP_AUTH', true);
define('SMTP_USERNAME', 'web@aramedylaboratorio.com');
define('SMTP_PASSWORD', 'xpC5OS67rVMNvU2('); // Contraseña del correo

// Configuración de envío
define('MAIL_FROM_EMAIL', 'web@aramedylaboratorio.com');
define('MAIL_FROM_NAME', SITE_NAME);

// ========================================
// GOOGLE reCAPTCHA v3
// ========================================

define('RECAPTCHA_SITE_KEY', ''); // Configurar
define('RECAPTCHA_SECRET_KEY', ''); // Configurar
define('RECAPTCHA_ENABLED', false); // Activar cuando esté configurado

// ========================================
// CONFIGURACIÓN DE SESIONES
// ========================================

define('SESSION_NAME', 'aramed_session');
define('SESSION_LIFETIME', 7200); // 2 horas

// ========================================
// TIMEZONE
// ========================================

date_default_timezone_set('America/Mexico_City');

// ========================================
// CONSTANTES ADICIONALES
// ========================================

// Paginación
define('ITEMS_PER_PAGE', 12);
define('BLOG_PER_PAGE', 9);
define('PROJECTS_PER_PAGE', 12);

// Uploads
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_DOC_TYPES', ['application/pdf']);

// ========================================
// AUTOLOAD DE FUNCIONES
// ========================================

// Cargar funciones auxiliares
if (file_exists(INCLUDES_PATH . '/functions.php')) {
    require_once INCLUDES_PATH . '/functions.php';
}

// ========================================
// INICIO DE SESIÓN (si es necesario)
// ========================================

// Descomentar si se necesitan sesiones
// if (session_status() === PHP_SESSION_NONE) {
//     session_name(SESSION_NAME);
//     session_start();
// }

