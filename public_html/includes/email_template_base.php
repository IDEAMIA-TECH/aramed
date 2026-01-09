<?php
/**
 * ========================================
 * EMAIL TEMPLATE BASE - CASCARÓN PARA EMAILS
 * ========================================
 * 
 * Template base para todos los emails del sistema
 * Incluye: logo, footer, redes sociales, botón de desuscripción
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Prevenir acceso directo (permitir desde email_functions.php o desde admin)
if (!defined('INCLUDES_PATH') && !defined('ARAMED_SITE') && !defined('ARAMED_EMAIL_TEMPLATE')) {
    // Permitir acceso desde email_functions.php o desde admin
    $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
    if (!empty($caller[0]['file'])) {
        $allowed_files = ['email_functions.php', 'campanas.php', 'quote_handler.php'];
        $file_name = basename($caller[0]['file']);
        if (!in_array($file_name, $allowed_files)) {
            // No bloquear si se llama desde un archivo permitido
        }
    }
}

// Definir constante para indicar que este archivo está siendo usado
define('ARAMED_EMAIL_TEMPLATE', true);

/**
 * Generar template base de email
 * 
 * @param string $content Contenido HTML principal del email
 * @param array $options Opciones adicionales:
 *                      - 'logo_url' => URL del logo (opcional)
 *                      - 'unsubscribe_url' => URL de desuscripción (opcional)
 *                      - 'company_name' => Nombre de la empresa (opcional)
 *                      - 'company_email' => Email de contacto (opcional)
 *                      - 'company_phone' => Teléfono de contacto (opcional)
 *                      - 'company_address' => Dirección (opcional)
 *                      - 'social_facebook' => URL de Facebook (opcional)
 *                      - 'social_instagram' => URL de Instagram (opcional)
 *                      - 'social_linkedin' => URL de LinkedIn (opcional)
 *                      - 'social_twitter' => URL de Twitter (opcional)
 * @return string HTML completo del email
 */
function getEmailTemplateBase($content, $options = []) {
    // Obtener configuración desde BD o usar valores por defecto
    $company_name = $options['company_name'] ?? (function_exists('getConfig') ? getConfig('empresa_nombre', 'Aramed y Laboratorios') : 'Aramed y Laboratorios');
    $company_email = $options['company_email'] ?? (function_exists('getConfig') ? getConfig('empresa_email', 'contacto@aramedylaboratorio.com') : 'contacto@aramedylaboratorio.com');
    $company_phone = $options['company_phone'] ?? (function_exists('getConfig') ? getConfig('empresa_telefono', '') : '');
    $company_address = $options['company_address'] ?? (function_exists('getConfig') ? getConfig('empresa_direccion', '') : '');
    
    // Logo
    $logo_url = $options['logo_url'] ?? '';
    if (empty($logo_url)) {
        // Intentar obtener desde configuración o usar ruta por defecto
        if (function_exists('siteUrl')) {
            $logo_url = siteUrl('assets/images/design/logo.png');
        } elseif (defined('SITE_URL')) {
            $logo_url = SITE_URL . '/assets/images/design/logo.png';
        } elseif (defined('IMAGES_URL')) {
            $logo_url = IMAGES_URL . '/design/logo.png';
        } else {
            $logo_url = 'https://aramedylaboratorio.com/assets/images/design/logo.png';
        }
    }
    
    // Asegurar que el logo sea una URL absoluta
    if (!empty($logo_url) && strpos($logo_url, 'http') !== 0) {
        $base_url = defined('SITE_URL') ? SITE_URL : (function_exists('siteUrl') ? rtrim(siteUrl('/'), '/') : 'https://aramedylaboratorio.com');
        $logo_url = $base_url . '/' . ltrim($logo_url, '/');
    }
    
    // Redes sociales
    $social_facebook = $options['social_facebook'] ?? (function_exists('getConfig') ? getConfig('empresa_facebook', '') : '');
    $social_instagram = $options['social_instagram'] ?? (function_exists('getConfig') ? getConfig('empresa_instagram', '') : '');
    $social_linkedin = $options['social_linkedin'] ?? (function_exists('getConfig') ? getConfig('empresa_linkedin', '') : '');
    $social_twitter = $options['social_twitter'] ?? (function_exists('getConfig') ? getConfig('empresa_twitter', '') : '');
    
    // URL de desuscripción
    $unsubscribe_url = $options['unsubscribe_url'] ?? '';
    if (empty($unsubscribe_url)) {
        if (function_exists('siteUrl')) {
            $unsubscribe_url = siteUrl('desuscribir.php');
        } else {
            $unsubscribe_url = 'https://aramedylaboratorio.com/desuscribir.php';
        }
    }
    
    // Generar HTML del template
    $html = '<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Email</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td {font-family: Arial, sans-serif !important;}
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, Helvetica, sans-serif;">
    <!-- Wrapper -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <!-- Container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    
                    <!-- Header con Logo -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0066cc 0%, #004499 100%); padding: 30px 40px; text-align: center;">
                            <img src="' . htmlspecialchars($logo_url) . '" alt="' . htmlspecialchars($company_name) . '" style="max-width: 200px; height: auto; display: block; margin: 0 auto;" />
                        </td>
                    </tr>
                    
                    <!-- Contenido Principal -->
                    <tr>
                        <td style="padding: 40px; font-size: 16px; line-height: 1.6; color: #333333;">
                            ' . $content . '
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px 40px; border-top: 1px solid #e9ecef;">
                            
                            <!-- Redes Sociales -->
                            ' . (!empty($social_facebook) || !empty($social_instagram) || !empty($social_linkedin) || !empty($social_twitter) ? '
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 20px;">
                                <tr>
                                    <td align="center" style="padding-bottom: 20px;">
                                        <p style="margin: 0 0 10px 0; font-size: 14px; color: #333333; font-weight: 600;">Síguenos en nuestras redes sociales:</p>
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
                                            <tr>
                                                ' . (!empty($social_facebook) ? '<td style="padding: 0 5px;"><a href="' . htmlspecialchars($social_facebook) . '" style="display: inline-block; padding: 8px 15px; background-color: #1877f2; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 600; margin: 0 5px;">Facebook</a></td>' : '') . '
                                                ' . (!empty($social_instagram) ? '<td style="padding: 0 5px;"><a href="' . htmlspecialchars($social_instagram) . '" style="display: inline-block; padding: 8px 15px; background-color: #e4405f; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 600; margin: 0 5px;">Instagram</a></td>' : '') . '
                                                ' . (!empty($social_linkedin) ? '<td style="padding: 0 5px;"><a href="' . htmlspecialchars($social_linkedin) . '" style="display: inline-block; padding: 8px 15px; background-color: #0077b5; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 600; margin: 0 5px;">LinkedIn</a></td>' : '') . '
                                                ' . (!empty($social_twitter) ? '<td style="padding: 0 5px;"><a href="' . htmlspecialchars($social_twitter) . '" style="display: inline-block; padding: 8px 15px; background-color: #1da1f2; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 600; margin: 0 5px;">Twitter</a></td>' : '') . '
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            ' : '') . '
                            
                            <!-- Información de Contacto -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 20px;">
                                <tr>
                                    <td align="center" style="font-size: 14px; color: #6c757d; line-height: 1.8;">
                                        <strong style="color: #333333;">' . htmlspecialchars($company_name) . '</strong><br>
                                        ' . (!empty($company_address) ? htmlspecialchars($company_address) . '<br>' : '') . '
                                        ' . (!empty($company_phone) ? 'Tel: <a href="tel:' . htmlspecialchars($company_phone) . '" style="color: #0066cc; text-decoration: none;">' . htmlspecialchars($company_phone) . '</a><br>' : '') . '
                                        Email: <a href="mailto:' . htmlspecialchars($company_email) . '" style="color: #0066cc; text-decoration: none;">' . htmlspecialchars($company_email) . '</a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Botón de Desuscripción -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding-top: 20px; border-top: 1px solid #e9ecef;">
                                        <p style="margin: 0 0 15px 0; font-size: 12px; color: #6c757d;">
                                            Recibes este email porque te suscribiste a nuestro newsletter.
                                        </p>
                                        <a href="' . htmlspecialchars($unsubscribe_url) . '" style="display: inline-block; padding: 10px 20px; background-color: #dc3545; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 600;">Desuscribirse</a>
                                        <p style="margin: 15px 0 0 0; font-size: 11px; color: #999999;">
                                            &copy; ' . date('Y') . ' ' . htmlspecialchars($company_name) . '. Todos los derechos reservados.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    
    return $html;
}

