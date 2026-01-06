<?php
/**
 * ========================================
 * ARAMED - VALIDACIONES DE SEGURIDAD PARA UPLOADS
 * ========================================
 * 
 * Funciones helper para validar y procesar uploads de forma segura
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
 * Valida un archivo subido antes de procesarlo
 * 
 * @param array $file Array $_FILES del archivo
 * @param array $allowed_types Tipos MIME permitidos
 * @param int $max_size Tamaño máximo en bytes
 * @param array $allowed_extensions Extensiones permitidas (opcional)
 * @return array ['valid' => bool, 'error' => string|null, 'safe_name' => string|null]
 */
function validateUploadedFile($file, $allowed_types = [], $max_size = 10485760, $allowed_extensions = []) {
    // Verificar que el archivo fue subido correctamente
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por PHP',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido por el formulario',
            UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
            UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta el directorio temporal',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en disco',
            UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida del archivo'
        ];
        
        $error = $error_messages[$file['error']] ?? 'Error desconocido al subir el archivo';
        return ['valid' => false, 'error' => $error, 'safe_name' => null];
    }
    
    // Verificar tamaño
    if ($file['size'] > $max_size) {
        $max_size_mb = round($max_size / 1024 / 1024, 2);
        return ['valid' => false, 'error' => "El archivo es demasiado grande. Máximo {$max_size_mb}MB", 'safe_name' => null];
    }
    
    // Verificar tipo MIME
    $file_type = $file['type'] ?? '';
    if (!empty($allowed_types) && !in_array($file_type, $allowed_types)) {
        return ['valid' => false, 'error' => 'Tipo de archivo no permitido', 'safe_name' => null];
    }
    
    // Verificar extensión
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!empty($allowed_extensions) && !in_array($file_extension, $allowed_extensions)) {
        return ['valid' => false, 'error' => 'Extensión de archivo no permitida', 'safe_name' => null];
    }
    
    // Validar extensión contra tipo MIME (prevenir spoofing)
    $extension_mime_map = [
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'gif' => ['image/gif'],
        'webp' => ['image/webp'],
        'pdf' => ['application/pdf']
    ];
    
    if (isset($extension_mime_map[$file_extension])) {
        $valid_mimes = $extension_mime_map[$file_extension];
        if (!in_array($file_type, $valid_mimes)) {
            return ['valid' => false, 'error' => 'El tipo MIME no coincide con la extensión del archivo', 'safe_name' => null];
        }
    }
    
    // Validar contenido real del archivo (magic bytes)
    $real_mime = getRealMimeType($file['tmp_name']);
    if ($real_mime && !empty($allowed_types) && !in_array($real_mime, $allowed_types)) {
        return ['valid' => false, 'error' => 'El contenido del archivo no coincide con su tipo', 'safe_name' => null];
    }
    
    // Generar nombre seguro
    $safe_name = generateSafeFileName($file['name'], $file_extension);
    
    return ['valid' => true, 'error' => null, 'safe_name' => $safe_name];
}

/**
 * Obtiene el tipo MIME real del archivo usando magic bytes
 * 
 * @param string $file_path Ruta al archivo temporal
 * @return string|null Tipo MIME real o null si no se puede determinar
 */
function getRealMimeType($file_path) {
    if (!file_exists($file_path)) {
        return null;
    }
    
    // Usar finfo si está disponible (más confiable)
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file_path);
        finfo_close($finfo);
        return $mime;
    }
    
    // Fallback a mime_content_type
    if (function_exists('mime_content_type')) {
        return mime_content_type($file_path);
    }
    
    return null;
}

/**
 * Genera un nombre de archivo seguro
 * 
 * @param string $original_name Nombre original del archivo
 * @param string $extension Extensión del archivo
 * @return string Nombre seguro
 */
function generateSafeFileName($original_name, $extension) {
    // Remover caracteres peligrosos
    $name = pathinfo($original_name, PATHINFO_FILENAME);
    $name = preg_replace('/[^a-zA-Z0-9_-]/', '', $name);
    $name = substr($name, 0, 50); // Limitar longitud
    
    // Agregar timestamp y uniqid para evitar colisiones
    $safe_name = $name . '-' . time() . '-' . uniqid() . '.' . strtolower($extension);
    
    return $safe_name;
}

/**
 * Valida que una imagen sea realmente una imagen válida
 * 
 * @param string $file_path Ruta al archivo
 * @return bool True si es una imagen válida
 */
function validateImageFile($file_path) {
    if (!file_exists($file_path)) {
        return false;
    }
    
    // Intentar abrir como imagen
    $image_info = @getimagesize($file_path);
    if ($image_info === false) {
        return false;
    }
    
    // Verificar que sea un tipo de imagen válido
    $valid_types = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
    return in_array($image_info[2], $valid_types);
}

/**
 * Sanitiza el nombre de un directorio
 * 
 * @param string $dir_name Nombre del directorio
 * @return string Nombre sanitizado
 */
function sanitizeDirectoryName($dir_name) {
    // Remover caracteres peligrosos
    $dir_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $dir_name);
    $dir_name = substr($dir_name, 0, 50);
    
    // Prevenir directorios peligrosos
    $dangerous = ['..', '.', 'etc', 'proc', 'sys', 'dev'];
    if (in_array(strtolower($dir_name), $dangerous)) {
        $dir_name = 'uploads';
    }
    
    return $dir_name;
}

/**
 * Verifica que un directorio sea seguro para escribir
 * 
 * @param string $dir_path Ruta al directorio
 * @return array ['safe' => bool, 'error' => string|null]
 */
function validateUploadDirectory($dir_path) {
    // Verificar que no esté fuera del directorio permitido
    $real_path = realpath($dir_path);
    $root_path = defined('ROOT_PATH') ? realpath(ROOT_PATH) : realpath(__DIR__ . '/..');
    
    if (!$real_path || strpos($real_path, $root_path) !== 0) {
        return ['safe' => false, 'error' => 'Directorio fuera del área permitida'];
    }
    
    // Verificar permisos
    if (!is_dir($real_path)) {
        return ['safe' => false, 'error' => 'El directorio no existe'];
    }
    
    if (!is_writable($real_path)) {
        return ['safe' => false, 'error' => 'El directorio no tiene permisos de escritura'];
    }
    
    return ['safe' => true, 'error' => null];
}

