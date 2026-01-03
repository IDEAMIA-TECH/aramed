<?php
/**
 * ========================================
 * ADMIN - UPLOAD DE IMÁGENES DE PROYECTO
 * ========================================
 * 
 * Handler AJAX para subir imágenes de proyectos
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración y verificar autenticación
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/connection.php';
require_once __DIR__ . '/../auth_check.php';

// Verificar permisos RBAC
if (function_exists('checkPermission')) {
    checkPermission('proyectos', 'editar');
}

// Headers para JSON
header('Content-Type: application/json');

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Obtener ID del proyecto
$proyecto_id = isset($_POST['proyecto_id']) ? (int)$_POST['proyecto_id'] : 0;

if ($proyecto_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID de proyecto inválido']);
    exit;
}

// Verificar que el proyecto existe
$pdo = getDB();
if (!$pdo) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM proyectos WHERE id = ?");
$stmt->execute([$proyecto_id]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Proyecto no encontrado']);
    exit;
}

// Configuración de upload
$upload_dir = __DIR__ . '/../../assets/images/proyectos/';
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$max_size = 5 * 1024 * 1024; // 5MB
$max_width = 2000;
$max_height = 2000;

// Crear directorio si no existe
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$uploaded_images = [];
$errors = [];

// Procesar cada archivo
if (isset($_FILES['imagenes']) && is_array($_FILES['imagenes']['name'])) {
    $file_count = count($_FILES['imagenes']['name']);
    
    for ($i = 0; $i < $file_count; $i++) {
        if ($_FILES['imagenes']['error'][$i] !== UPLOAD_ERR_OK) {
            $errors[] = "Error al subir el archivo " . ($i + 1);
            continue;
        }
        
        $file = [
            'name' => $_FILES['imagenes']['name'][$i],
            'type' => $_FILES['imagenes']['type'][$i],
            'tmp_name' => $_FILES['imagenes']['tmp_name'][$i],
            'size' => $_FILES['imagenes']['size'][$i]
        ];
        
        // Validar tipo
        if (!in_array($file['type'], $allowed_types)) {
            $errors[] = "El archivo '{$file['name']}' no es una imagen válida (JPG, PNG, GIF, WebP)";
            continue;
        }
        
        // Validar tamaño
        if ($file['size'] > $max_size) {
            $errors[] = "El archivo '{$file['name']}' es demasiado grande (máx. 5MB)";
            continue;
        }
        
        // Validar dimensiones
        $image_info = getimagesize($file['tmp_name']);
        if (!$image_info) {
            $errors[] = "El archivo '{$file['name']}' no es una imagen válida";
            continue;
        }
        
        $width = $image_info[0];
        $height = $image_info[1];
        
        if ($width > $max_width || $height > $max_height) {
            // Redimensionar si es necesario
            $resized = resizeImage($file['tmp_name'], $max_width, $max_height);
            if (!$resized) {
                $errors[] = "Error al redimensionar '{$file['name']}'";
                continue;
            }
            $file['tmp_name'] = $resized;
        }
        
        // Generar nombre único
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'proyecto-' . $proyecto_id . '-' . time() . '-' . $i . '.' . $extension;
        $filepath = $upload_dir . $filename;
        
        // Mover archivo
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            $errors[] = "Error al guardar '{$file['name']}'";
            continue;
        }
        
        // Generar URL relativa
        $image_url = 'assets/images/proyectos/' . $filename;
        
        // Determinar si es principal (primera imagen o si no hay ninguna principal)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM proyecto_imagenes WHERE proyecto_id = ? AND es_principal = 1");
        $stmt->execute([$proyecto_id]);
        $has_main = $stmt->fetchColumn() > 0;
        $es_principal = !$has_main && $i === 0;
        
        // Obtener orden máximo
        $stmt = $pdo->prepare("SELECT COALESCE(MAX(orden), 0) FROM proyecto_imagenes WHERE proyecto_id = ?");
        $stmt->execute([$proyecto_id]);
        $orden = $stmt->fetchColumn() + 1;
        
        // Insertar en base de datos
        $stmt = $pdo->prepare("
            INSERT INTO proyecto_imagenes 
            (proyecto_id, imagen_url, titulo, orden, es_principal, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        $titulo = pathinfo($file['name'], PATHINFO_FILENAME);
        $stmt->execute([$proyecto_id, $image_url, $titulo, $orden, $es_principal ? 1 : 0]);
        
        $image_id = $pdo->lastInsertId();
        
        // Si es principal, actualizar imagen_principal en proyecto
        if ($es_principal) {
            $stmt = $pdo->prepare("UPDATE proyectos SET imagen_principal = ? WHERE id = ?");
            $stmt->execute([$image_url, $proyecto_id]);
        }
        
        $uploaded_images[] = [
            'id' => $image_id,
            'url' => SITE_URL . '/' . $image_url,
            'titulo' => $titulo,
            'es_principal' => $es_principal
        ];
    }
}

// Registrar actividad
if (function_exists('logActivity') && !empty($uploaded_images)) {
    $current_user = getCurrentUser();
    logActivity($current_user['id'], 'editar', 'proyectos', $proyecto_id, 'proyecto', [
        'accion' => 'subir_imagenes',
        'imagenes_subidas' => count($uploaded_images)
    ]);
}

// Respuesta
if (!empty($uploaded_images)) {
    echo json_encode([
        'success' => true,
        'message' => count($uploaded_images) . ' imagen(es) subida(s) exitosamente',
        'images' => $uploaded_images,
        'errors' => $errors
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => !empty($errors) ? implode(', ', $errors) : 'No se subieron imágenes'
    ]);
}

/**
 * Redimensionar imagen manteniendo proporción
 */
function resizeImage($source_path, $max_width, $max_height) {
    $image_info = getimagesize($source_path);
    if (!$image_info) {
        return false;
    }
    
    $width = $image_info[0];
    $height = $image_info[1];
    $mime = $image_info['mime'];
    
    // Si no necesita redimensionar
    if ($width <= $max_width && $height <= $max_height) {
        return $source_path;
    }
    
    // Calcular nuevas dimensiones manteniendo proporción
    $ratio = min($max_width / $width, $max_height / $height);
    $new_width = (int)($width * $ratio);
    $new_height = (int)($height * $ratio);
    
    // Crear imagen desde fuente
    switch ($mime) {
        case 'image/jpeg':
            $source = imagecreatefromjpeg($source_path);
            break;
        case 'image/png':
            $source = imagecreatefrompng($source_path);
            break;
        case 'image/gif':
            $source = imagecreatefromgif($source_path);
            break;
        case 'image/webp':
            $source = imagecreatefromwebp($source_path);
            break;
        default:
            return false;
    }
    
    if (!$source) {
        return false;
    }
    
    // Crear imagen redimensionada
    $destination = imagecreatetruecolor($new_width, $new_height);
    
    // Preservar transparencia para PNG y GIF
    if ($mime === 'image/png' || $mime === 'image/gif') {
        imagealphablending($destination, false);
        imagesavealpha($destination, true);
        $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
        imagefilledrectangle($destination, 0, 0, $new_width, $new_height, $transparent);
    }
    
    // Redimensionar
    imagecopyresampled($destination, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    // Guardar en archivo temporal
    $temp_path = sys_get_temp_dir() . '/resized_' . uniqid() . '.jpg';
    
    switch ($mime) {
        case 'image/jpeg':
        case 'image/webp':
            imagejpeg($destination, $temp_path, 85);
            break;
        case 'image/png':
            imagepng($destination, $temp_path, 8);
            break;
        case 'image/gif':
            imagegif($destination, $temp_path);
            break;
    }
    
    imagedestroy($source);
    imagedestroy($destination);
    
    return $temp_path;
}

