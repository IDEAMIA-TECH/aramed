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
    // Si la ruta ya incluye 'assets/images/', removerlo para evitar duplicación
    $path = preg_replace('#^assets/images/#', '', $path);
    // Si la ruta ya es una URL completa, devolverla tal cual
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }
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

/**
 * Truncar texto a una longitud específica
 * 
 * @param string $text Texto a truncar
 * @param int $length Longitud máxima
 * @param string $suffix Sufijo a agregar (por defecto '...')
 * @return string Texto truncado
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}

/**
 * Generar slug URL-friendly a partir de un texto
 * 
 * @param string $text Texto a convertir
 * @return string Slug generado
 */
function generateSlug($text) {
    if (empty($text)) {
        return '';
    }
    
    // Convertir a minúsculas
    $slug = strtolower($text);
    
    // Reemplazar caracteres especiales
    $slug = str_replace(
        ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü', 'ç'],
        ['a', 'e', 'i', 'o', 'u', 'n', 'u', 'c'],
        $slug
    );
    
    // Eliminar caracteres no alfanuméricos excepto espacios y guiones
    $slug = preg_replace('/[^a-z0-9\s\-]/', '', $slug);
    
    // Reemplazar espacios y múltiples guiones con un solo guión
    $slug = preg_replace('/[\s\-]+/', '-', $slug);
    
    // Eliminar guiones al inicio y final
    $slug = trim($slug, '-');
    
    return $slug;
}

/**
 * Registrar actividad en la bitácora de auditoría
 * 
 * @param int $usuario_id ID del usuario que realiza la acción
 * @param string $accion Tipo de acción (login, logout, crear, editar, eliminar, etc.)
 * @param string $modulo Módulo donde se realizó la acción (opcional)
 * @param int $entidad_id ID de la entidad afectada (opcional)
 * @param string $entidad_tipo Tipo de entidad (opcional)
 * @param array $detalles Detalles adicionales (opcional)
 * @return bool True si se registró correctamente
 */
function logActivity($usuario_id, $accion, $modulo = null, $entidad_id = null, $entidad_tipo = null, $detalles = null) {
    $pdo = getDB();
    if (!$pdo) {
        return false;
    }
    
    // Verificar si existe la tabla audit_logs
    try {
        $sql = "
            INSERT INTO audit_logs 
            (usuario_id, accion, modulo, entidad_id, entidad_tipo, detalles, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ";
        
        $detalles_json = $detalles ? json_encode($detalles, JSON_UNESCAPED_UNICODE) : null;
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $usuario_id,
            $accion,
            $modulo,
            $entidad_id,
            $entidad_tipo,
            $detalles_json,
            $ip_address,
            $user_agent
        ]);
        
        return true;
    } catch (Exception $e) {
        // Si la tabla no existe aún, no hacer nada (evitar errores durante desarrollo)
        if (ENVIRONMENT === 'development') {
            error_log("Error en logActivity: " . $e->getMessage());
        }
        return false;
    }
}

/**
 * Obtiene un valor de configuración desde la base de datos
 * @param string $clave Clave de la configuración
 * @param mixed $default Valor por defecto si no existe
 * @return mixed Valor de la configuración
 */
function getConfig($clave, $default = null) {
    static $config_cache = [];
    static $cache_loaded = false;
    
    // Verificar que getDB() esté disponible
    if (!function_exists('getDB')) {
        return $default;
    }
    
    // Cargar todo el cache de una vez si no se ha cargado
    if (!$cache_loaded) {
        try {
            // Intentar obtener conexión - puede fallar si connection.php no se ha cargado
            $pdo = getDB();
            if ($pdo) {
                try {
                    $stmt = $pdo->query("SELECT clave, valor, tipo FROM configuracion");
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    
                    foreach ($results as $row) {
                        $valor = $row['valor'];
                        
                        // Convertir según tipo
                        switch ($row['tipo']) {
                            case 'boolean':
                                $valor = (bool)$valor;
                                break;
                            case 'number':
                                $valor = is_numeric($valor) ? (float)$valor : 0;
                                break;
                            case 'json':
                                $valor = json_decode($valor, true);
                                break;
                        }
                        
                        $config_cache[$row['clave']] = $valor;
                    }
                    
                    $cache_loaded = true;
                } catch (Exception $e) {
                    // Si la tabla no existe o hay error, continuar sin cache
                    error_log("Error en getConfig al cargar cache: " . $e->getMessage());
                }
            }
        } catch (Error $e) {
            // Si getDB() no está disponible o falla
            error_log("Error en getConfig: getDB() no disponible - " . $e->getMessage());
            return $default;
        } catch (Exception $e) {
            // Error al obtener conexión
            error_log("Error en getConfig al obtener conexión: " . $e->getMessage());
            return $default;
        }
    }
    
    // Verificar cache
    if (isset($config_cache[$clave])) {
        return $config_cache[$clave];
    }
    
    // Si no está en cache y no se ha cargado, intentar cargar individualmente
    if (!$cache_loaded) {
        // Verificar que getDB() esté disponible
        if (!function_exists('getDB')) {
            return $default;
        }
        
        try {
            $pdo = getDB();
            if (!$pdo) {
                return $default;
            }
            
            $stmt = $pdo->prepare("SELECT valor, tipo FROM configuracion WHERE clave = ?");
            $stmt->execute([$clave]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            $stmt->closeCursor();
            
            if (!empty($result)) {
                $valor = $result[0]['valor'];
                
                // Convertir según tipo
                switch ($result[0]['tipo']) {
                    case 'boolean':
                        $valor = (bool)$valor;
                        break;
                    case 'number':
                        $valor = is_numeric($valor) ? (float)$valor : $default;
                        break;
                    case 'json':
                        $valor = json_decode($valor, true);
                        break;
                }
                
                $config_cache[$clave] = $valor;
                return $valor;
            }
        } catch (Error $e) {
            // Si getDB() no está disponible o falla
            error_log("Error en getConfig (individual): " . $e->getMessage());
            return $default;
        } catch (Exception $e) {
            // Si la tabla no existe o hay otro error
            error_log("Error en getConfig (individual): " . $e->getMessage());
            return $default;
        }
    }
    
    return $default;
}

/**
 * Limpia el cache de configuración (útil después de actualizar valores)
 */
function clearConfigCache() {
    // No hay forma directa de limpiar static variables, pero podemos forzar recarga
    // Esto se hace reiniciando el proceso o usando un flag
    // Por ahora, la función setConfig actualizará el cache automáticamente
}

/**
 * Establece un valor de configuración en la base de datos
 * @param string $clave Clave de la configuración
 * @param mixed $valor Valor a guardar
 * @param string $tipo Tipo de dato (text, number, boolean, json, html)
 * @param string $categoria Categoría de la configuración
 * @return bool True si se guardó correctamente
 */
function setConfig($clave, $valor, $tipo = 'text', $categoria = 'general') {
    // Verificar que getDB() esté disponible
    if (!function_exists('getDB')) {
        return false;
    }
    
    $pdo = getDB();
    if (!$pdo) {
        return false;
    }
    
    try {
        // Convertir valor según tipo
        $valor_original = $valor;
        if ($tipo === 'json' && is_array($valor)) {
            $valor = json_encode($valor);
        } elseif ($tipo === 'boolean') {
            $valor = $valor ? '1' : '0';
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO configuracion (clave, valor, tipo, categoria, updated_at)
            VALUES (?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
                valor = VALUES(valor),
                tipo = VALUES(tipo),
                categoria = VALUES(categoria),
                updated_at = NOW()
        ");
        
        $result = $stmt->execute([$clave, $valor, $tipo, $categoria]);
        
        // Actualizar cache estático si existe
        if ($result) {
            static $config_cache = [];
            // Convertir para cache
            $cache_valor = $valor_original;
            if ($tipo === 'boolean') {
                $cache_valor = (bool)$valor;
            } elseif ($tipo === 'number') {
                $cache_valor = is_numeric($valor) ? (float)$valor : 0;
            } elseif ($tipo === 'json') {
                $cache_valor = json_decode($valor, true);
            }
            $config_cache[$clave] = $cache_valor;
        }
        
        return $result;
    } catch (Exception $e) {
        error_log("Error al guardar configuración: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene todas las configuraciones de una categoría
 * @param string $categoria Categoría de configuración
 * @return array Array asociativo clave => valor
 */
function getConfigByCategory($categoria) {
    // Verificar que getDB() esté disponible
    if (!function_exists('getDB')) {
        return [];
    }
    
    try {
        $pdo = getDB();
        if (!$pdo) {
            return [];
        }
        $stmt = $pdo->prepare("SELECT clave, valor, tipo FROM configuracion WHERE categoria = ?");
        $stmt->execute([$categoria]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $config = [];
        foreach ($results as $row) {
            $valor = $row['valor'];
            
            switch ($row['tipo']) {
                case 'boolean':
                    $valor = (bool)$valor;
                    break;
                case 'number':
                    $valor = is_numeric($valor) ? (float)$valor : 0;
                    break;
                case 'json':
                    $valor = json_decode($valor, true);
                    break;
            }
            
            $config[$row['clave']] = $valor;
        }
        
        return $config;
    } catch (Error $e) {
        // Si getDB() no está disponible o falla
        error_log("Error en getConfigByCategory: getDB() no disponible - " . $e->getMessage());
        return [];
    } catch (Exception $e) {
        error_log("Error en getConfigByCategory: " . $e->getMessage());
        return [];
    }
}

/**
 * Publica automáticamente artículos programados cuya fecha_programada haya llegado
 * @return int Número de artículos publicados
 */
function publicarArticulosProgramados() {
    // Verificar que getDB() esté disponible
    if (!function_exists('getDB')) {
        return 0;
    }
    
    try {
        $pdo = getDB();
        if (!$pdo) {
            return 0;
        }
        // Buscar artículos programados cuya fecha ya llegó
        $sql = "
            UPDATE blog_articulos 
            SET estado = 'publicado', 
                fecha_publicacion = COALESCE(fecha_publicacion, NOW()),
                updated_at = NOW()
            WHERE estado = 'programado' 
            AND fecha_programada IS NOT NULL 
            AND fecha_programada <= NOW()
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->rowCount();
    } catch (Exception $e) {
        error_log("Error en publicarArticulosProgramados: " . $e->getMessage());
        return 0;
    } catch (Error $e) {
        // Si getDB() no está disponible o falla
        error_log("Error en publicarArticulosProgramados: getDB() no disponible - " . $e->getMessage());
        return 0;
    }
}

/**
 * Generar meta tags SEO usando configuraciones por defecto
 * @param string $title Título de la página (sin prefijo/sufijo)
 * @param string $description Descripción de la página (opcional, usa default si no se proporciona)
 * @param string $keywords Palabras clave (opcional, usa default si no se proporciona)
 * @param string $image URL de imagen Open Graph (opcional, usa default si no se proporciona)
 * @param string $url URL canónica (opcional, usa SITE_URL si no se proporciona)
 * @return array Array con todos los meta tags formateados
 */
function getSEOMetaTags($title = '', $description = '', $keywords = '', $image = '', $url = '') {
    // Obtener configuraciones SEO desde BD, con fallback a constantes
    $title_prefix = function_exists('getConfig') ? getConfig('seo_title_prefix', 'Aramed y Laboratorios - ') : 'Aramed y Laboratorios - ';
    $title_suffix = function_exists('getConfig') ? getConfig('seo_title_suffix', '') : '';
    $default_description = function_exists('getConfig') ? getConfig('seo_default_description', SITE_DESCRIPTION) : SITE_DESCRIPTION;
    $default_keywords = function_exists('getConfig') ? getConfig('seo_default_keywords', SITE_KEYWORDS) : SITE_KEYWORDS;
    $default_og_image = function_exists('getConfig') ? getConfig('seo_og_image', 'assets/images/design/logo-og.jpg') : 'assets/images/design/logo-og.jpg';
    
    // Construir título completo
    $full_title = $title_prefix . $title . $title_suffix;
    
    // Usar valores proporcionados o defaults
    $final_description = !empty($description) ? $description : $default_description;
    $final_keywords = !empty($keywords) ? $keywords : $default_keywords;
    $final_image = !empty($image) ? $image : imageUrl($default_og_image);
    $final_url = !empty($url) ? $url : SITE_URL;
    
    // Asegurar que la URL de la imagen sea absoluta
    if (strpos($final_image, 'http') !== 0) {
        $final_image = SITE_URL . '/' . ltrim($final_image, '/');
    }
    
    return [
        'title' => $full_title,
        'description' => $final_description,
        'keywords' => $final_keywords,
        'og_image' => $final_image,
        'url' => $final_url
    ];
}

/**
 * Renderizar meta tags SEO en HTML
 * @param string $title Título de la página
 * @param string $description Descripción de la página
 * @param string $keywords Palabras clave
 * @param string $image URL de imagen Open Graph
 * @param string $url URL canónica
 * @return string HTML con todos los meta tags
 */
function renderSEOMetaTags($title = '', $description = '', $keywords = '', $image = '', $url = '') {
    $meta = getSEOMetaTags($title, $description, $keywords, $image, $url);
    
    $html = '';
    $html .= '<title>' . esc($meta['title']) . '</title>' . "\n";
    $html .= '<meta name="description" content="' . esc($meta['description']) . '">' . "\n";
    $html .= '<meta name="keywords" content="' . esc($meta['keywords']) . '">' . "\n";
    $html .= '<link rel="canonical" href="' . esc($meta['url']) . '">' . "\n";
    $html .= '<!-- Open Graph / Facebook -->' . "\n";
    $html .= '<meta property="og:type" content="website">' . "\n";
    $html .= '<meta property="og:url" content="' . esc($meta['url']) . '">' . "\n";
    $html .= '<meta property="og:title" content="' . esc($meta['title']) . '">' . "\n";
    $html .= '<meta property="og:description" content="' . esc($meta['description']) . '">' . "\n";
    $html .= '<meta property="og:image" content="' . esc($meta['og_image']) . '">' . "\n";
    $html .= '<!-- Twitter -->' . "\n";
    $html .= '<meta property="twitter:card" content="summary_large_image">' . "\n";
    $html .= '<meta property="twitter:url" content="' . esc($meta['url']) . '">' . "\n";
    $html .= '<meta property="twitter:title" content="' . esc($meta['title']) . '">' . "\n";
    $html .= '<meta property="twitter:description" content="' . esc($meta['description']) . '">' . "\n";
    $html .= '<meta property="twitter:image" content="' . esc($meta['og_image']) . '">' . "\n";
    
    return $html;
}

/**
 * IP del cliente (Cloudflare / proxy / REMOTE_ADDR)
 */
function getClientIpAddress() {
    $candidates = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($candidates as $key) {
        if (empty($_SERVER[$key])) {
            continue;
        }
        $raw = $_SERVER[$key];
        if (strpos($raw, ',') !== false) {
            $raw = trim(explode(',', $raw)[0]);
        }
        if (filter_var($raw, FILTER_VALIDATE_IP)) {
            return $raw;
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '';
}

