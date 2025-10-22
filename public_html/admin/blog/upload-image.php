<?php
/**
 * ========================================
 * ADMIN - SUBIR IMAGEN
 * ========================================
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Configuración de subida
$upload_dir = __DIR__ . '/../../assets/images/blog/';
$max_file_size = 5 * 1024 * 1024; // 5MB
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];

// Crear directorio si no existe
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Función para generar nombre único
function generateUniqueFilename($original_name, $upload_dir) {
    $extension = pathinfo($original_name, PATHINFO_EXTENSION);
    $basename = pathinfo($original_name, PATHINFO_FILENAME);
    $basename = preg_replace('/[^a-zA-Z0-9_-]/', '', $basename);
    
    $filename = $basename . '_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
    
    // Verificar que no exista
    $counter = 1;
    while (file_exists($upload_dir . $filename)) {
        $filename = $basename . '_' . time() . '_' . rand(1000, 9999) . '_' . $counter . '.' . $extension;
        $counter++;
    }
    
    return $filename;
}

// Función para redimensionar imagen
function resizeImage($source_path, $destination_path, $max_width = 1200, $max_height = 800, $quality = 85) {
    $image_info = getimagesize($source_path);
    if (!$image_info) return false;
    
    $original_width = $image_info[0];
    $original_height = $image_info[1];
    $mime_type = $image_info['mime'];
    
    // Calcular nuevas dimensiones manteniendo proporción
    $ratio = min($max_width / $original_width, $max_height / $original_height);
    $new_width = round($original_width * $ratio);
    $new_height = round($original_height * $ratio);
    
    // Crear imagen desde archivo
    switch ($mime_type) {
        case 'image/jpeg':
            $source_image = imagecreatefromjpeg($source_path);
            break;
        case 'image/png':
            $source_image = imagecreatefrompng($source_path);
            break;
        case 'image/webp':
            $source_image = imagecreatefromwebp($source_path);
            break;
        case 'image/gif':
            $source_image = imagecreatefromgif($source_path);
            break;
        default:
            return false;
    }
    
    if (!$source_image) return false;
    
    // Crear imagen redimensionada
    $resized_image = imagecreatetruecolor($new_width, $new_height);
    
    // Preservar transparencia para PNG y GIF
    if ($mime_type == 'image/png' || $mime_type == 'image/gif') {
        imagealphablending($resized_image, false);
        imagesavealpha($resized_image, true);
        $transparent = imagecolorallocatealpha($resized_image, 255, 255, 255, 127);
        imagefilledrectangle($resized_image, 0, 0, $new_width, $new_height, $transparent);
    }
    
    // Redimensionar
    imagecopyresampled($resized_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
    
    // Guardar imagen
    $result = false;
    switch ($mime_type) {
        case 'image/jpeg':
            $result = imagejpeg($resized_image, $destination_path, $quality);
            break;
        case 'image/png':
            $result = imagepng($resized_image, $destination_path, 9);
            break;
        case 'image/webp':
            $result = imagewebp($resized_image, $destination_path, $quality);
            break;
        case 'image/gif':
            $result = imagegif($resized_image, $destination_path);
            break;
    }
    
    // Liberar memoria
    imagedestroy($source_image);
    imagedestroy($resized_image);
    
    return $result;
}

// Procesar subida
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    
    // Validaciones
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Error al subir el archivo']);
        exit;
    }
    
    if ($file['size'] > $max_file_size) {
        echo json_encode(['success' => false, 'message' => 'El archivo es demasiado grande. Máximo 5MB']);
        exit;
    }
    
    $file_type = mime_content_type($file['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido. Solo JPG, PNG, WebP y GIF']);
        exit;
    }
    
    // Generar nombre único
    $filename = generateUniqueFilename($file['name'], $upload_dir);
    $file_path = $upload_dir . $filename;
    
    // Subir archivo
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        // Redimensionar imagen
        $resized_path = $upload_dir . 'resized_' . $filename;
        if (resizeImage($file_path, $resized_path)) {
            // Reemplazar original con redimensionada
            unlink($file_path);
            rename($resized_path, $file_path);
        }
        
        // Generar URL
        $image_url = SITE_URL . '/assets/images/blog/' . $filename;
        
        echo json_encode([
            'success' => true,
            'message' => 'Imagen subida correctamente',
            'filename' => $filename,
            'url' => $image_url,
            'size' => filesize($file_path)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar el archivo']);
    }
    exit;
}

// Obtener lista de imágenes existentes
if (isset($_GET['action']) && $_GET['action'] === 'list') {
    $images = [];
    if (is_dir($upload_dir)) {
        $files = scandir($upload_dir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $file)) {
                $file_path = $upload_dir . $file;
                $images[] = [
                    'filename' => $file,
                    'url' => SITE_URL . '/assets/images/blog/' . $file,
                    'size' => filesize($file_path),
                    'modified' => filemtime($file_path)
                ];
            }
        }
    }
    
    // Ordenar por fecha de modificación (más recientes primero)
    usort($images, function($a, $b) {
        return $b['modified'] - $a['modified'];
    });
    
    echo json_encode(['success' => true, 'images' => $images]);
    exit;
}

// Eliminar imagen
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['filename'])) {
    $filename = basename($_GET['filename']);
    $file_path = $upload_dir . $filename;
    
    if (file_exists($file_path) && unlink($file_path)) {
        echo json_encode(['success' => true, 'message' => 'Imagen eliminada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar la imagen']);
    }
    exit;
}

// Si no es POST ni GET válido, mostrar error
echo json_encode(['success' => false, 'message' => 'Método no permitido']);
?>
