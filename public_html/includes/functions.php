<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Funciones Auxiliares
 * ========================================
 * 
 * Funciones helper para el sitio
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Prevenir acceso directo
if (!defined('ARAMED_SITE')) {
    die('Acceso directo no permitido');
}

/**
 * Escapar HTML para prevenir XSS
 */
function esc($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Generar URL del sitio
 */
function siteUrl($path = '') {
    $url = SITE_URL;
    if (!empty($path)) {
        $url .= '/' . ltrim($path, '/');
    }
    return $url;
}

/**
 * Generar URL de assets
 */
function assetUrl($path) {
    return ASSETS_URL . '/' . ltrim($path, '/');
}

/**
 * Generar URL de imagen
 */
function imageUrl($path) {
    return IMAGES_URL . '/' . ltrim($path, '/');
}

/**
 * Incluir vista
 */
function view($viewName, $data = []) {
    extract($data);
    $viewPath = PAGES_PATH . '/' . $viewName . '.php';
    
    if (file_exists($viewPath)) {
        include $viewPath;
    } else {
        die("Vista no encontrada: $viewName");
    }
}

/**
 * Incluir componente
 */
function component($componentName, $data = []) {
    extract($data);
    $componentPath = INCLUDES_PATH . '/' . $componentName . '.php';
    
    if (file_exists($componentPath)) {
        include $componentPath;
    } else {
        if (ENVIRONMENT === 'development') {
            echo "<!-- Componente no encontrado: $componentName -->";
        }
    }
}

/**
 * Redireccionar
 */
function redirect($path = '', $statusCode = 302) {
    $url = empty($path) ? SITE_URL : siteUrl($path);
    header("Location: $url", true, $statusCode);
    exit;
}

/**
 * Generar token CSRF
 */
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verificar token CSRF
 */
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Formatear fecha
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    return date($format, $timestamp);
}

/**
 * Formatear fecha en español
 */
function formatDateES($date) {
    if (empty($date)) return '';
    
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    
    $months = [
        'January' => 'enero', 'February' => 'febrero', 'March' => 'marzo',
        'April' => 'abril', 'May' => 'mayo', 'June' => 'junio',
        'July' => 'julio', 'August' => 'agosto', 'September' => 'septiembre',
        'October' => 'octubre', 'November' => 'noviembre', 'December' => 'diciembre'
    ];
    
    $days = [
        'Monday' => 'lunes', 'Tuesday' => 'martes', 'Wednesday' => 'miércoles',
        'Thursday' => 'jueves', 'Friday' => 'viernes', 'Saturday' => 'sábado',
        'Sunday' => 'domingo'
    ];
    
    $dateStr = date('d \d\e F \d\e Y', $timestamp);
    return str_replace(array_keys($months), array_values($months), $dateStr);
}

/**
 * Truncar texto
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $text = substr($text, 0, $length);
    $text = substr($text, 0, strrpos($text, ' '));
    
    return $text . $suffix;
}

/**
 * Generar slug amigable para URLs
 */
function slugify($text) {
    // Reemplazar caracteres especiales
    $text = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], strtolower($text));
    // Remover caracteres no alfanuméricos
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
    // Remover guiones múltiples
    $text = preg_replace('/-+/', '-', $text);
    // Remover guiones al inicio y final
    return trim($text, '-');
}

/**
 * Validar reCAPTCHA v3
 */
function verifyRecaptcha($token) {
    if (!RECAPTCHA_ENABLED) {
        return true; // Si no está habilitado, permitir
    }
    
    $secretKey = RECAPTCHA_SECRET_KEY;
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    
    $data = [
        'secret' => $secretKey,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $resultJson = json_decode($result);
    
    return $resultJson->success && $resultJson->score >= 0.5;
}

/**
 * Respuesta JSON
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Respuesta JSON de éxito
 */
function jsonSuccess($message = 'Operación exitosa', $data = []) {
    jsonResponse([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
}

/**
 * Respuesta JSON de error
 */
function jsonError($message = 'Error en la operación', $statusCode = 400) {
    jsonResponse([
        'success' => false,
        'message' => $message
    ], $statusCode);
}

/**
 * Verificar si es petición AJAX
 */
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Verificar si es petición POST
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Verificar si es petición GET
 */
function isGet() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Obtener IP del cliente
 */
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

/**
 * Logging de errores personalizado
 */
function logError($message, $context = []) {
    $logFile = ROOT_PATH . '/logs/app-errors.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = getClientIP();
    $contextStr = !empty($context) ? json_encode($context) : '';
    
    $logMessage = "[$timestamp] [IP: $ip] $message";
    if ($contextStr) {
        $logMessage .= " | Context: $contextStr";
    }
    $logMessage .= PHP_EOL;
    
    error_log($logMessage, 3, $logFile);
}

/**
 * Dump and die (para debugging)
 */
function dd(...$vars) {
    if (ENVIRONMENT !== 'development') {
        die('Esta función solo está disponible en modo desarrollo');
    }
    
    echo '<pre style="background: #1e1e1e; color: #d4d4d4; padding: 20px; border-radius: 5px; margin: 20px; font-family: Consolas, Monaco, monospace;">';
    foreach ($vars as $var) {
        var_dump($var);
        echo PHP_EOL;
    }
    echo '</pre>';
    die();
}

/**
 * Generar breadcrumbs
 */
function breadcrumbs($items = []) {
    if (empty($items)) return '';
    
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    $count = count($items);
    $index = 0;
    
    foreach ($items as $label => $url) {
        $index++;
        $isLast = ($index === $count);
        
        if ($isLast) {
            $html .= '<li class="breadcrumb-item active" aria-current="page">' . esc($label) . '</li>';
        } else {
            $html .= '<li class="breadcrumb-item"><a href="' . esc($url) . '">' . esc($label) . '</a></li>';
        }
    }
    
    $html .= '</ol></nav>';
    
    return $html;
}

/**
 * Paginar resultados
 */
function paginate($totalItems, $itemsPerPage, $currentPage = 1, $baseUrl = '') {
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = max(1, min($totalPages, (int)$currentPage));
    
    $pagination = [
        'total_items' => $totalItems,
        'per_page' => $itemsPerPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => ($currentPage - 1) * $itemsPerPage,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages,
        'prev_page' => $currentPage - 1,
        'next_page' => $currentPage + 1,
        'base_url' => $baseUrl
    ];
    
    return $pagination;
}

/**
 * Renderizar paginación HTML
 */
function renderPagination($pagination) {
    if ($pagination['total_pages'] <= 1) return '';
    
    $html = '<nav aria-label="Navegación de páginas"><ul class="pagination justify-content-center">';
    
    // Botón anterior
    if ($pagination['has_prev']) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $pagination['base_url'] . '?page=' . $pagination['prev_page'] . '">Anterior</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Anterior</span></li>';
    }
    
    // Números de página
    for ($i = 1; $i <= $pagination['total_pages']; $i++) {
        if ($i === $pagination['current_page']) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . $pagination['base_url'] . '?page=' . $i . '">' . $i . '</a></li>';
        }
    }
    
    // Botón siguiente
    if ($pagination['has_next']) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $pagination['base_url'] . '?page=' . $pagination['next_page'] . '">Siguiente</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Siguiente</span></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

/**
 * Sanitizar input general (texto)
 * Elimina tags HTML y espacios extra
 */
function sanitizeInput($input) {
    if (is_null($input)) {
        return null;
    }
    
    // Eliminar tags HTML
    $input = strip_tags($input);
    
    // Trim espacios
    $input = trim($input);
    
    // Convertir caracteres especiales
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    
    return $input;
}

/**
 * Sanitizar y validar email
 * Retorna el email limpio o null si no es válido
 */
function sanitizeEmail($email) {
    if (empty($email)) {
        return null;
    }
    
    // Trim y lowercase
    $email = strtolower(trim($email));
    
    // Sanitizar
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    
    // Validar formato
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return null;
    }
    
    return $email;
}

/**
 * Sanitizar teléfono
 * Permite solo números, espacios, paréntesis y guiones
 */
function sanitizePhone($phone) {
    if (empty($phone)) {
        return null;
    }
    
    // Eliminar todo excepto números, espacios, paréntesis, guiones y el símbolo +
    $phone = preg_replace('/[^0-9\s\(\)\-\+]/', '', $phone);
    
    return trim($phone);
}

/**
 * Sanitizar URL
 */
function sanitizeUrl($url) {
    if (empty($url)) {
        return null;
    }
    
    $url = filter_var($url, FILTER_SANITIZE_URL);
    
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return null;
    }
    
    return $url;
}

/**
 * Validar si un string es una fecha válida
 */
function isValidDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Obtener el año actual
 * Usado en el footer para el copyright
 */
function getCurrentYear() {
    return date('Y');
}

