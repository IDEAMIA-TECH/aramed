<?php
/**
 * ========================================
 * ADMIN - UPLOAD DE DOCUMENTOS DE PRODUCTO
 * ========================================
 * 
 * Handler AJAX para subir documentos de productos
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración y verificar autenticación
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/connection.php';
require_once __DIR__ . '/../../auth_check.php';

// Verificar permisos RBAC
if (function_exists('checkPermission')) {
    checkPermission('catalogo', 'editar');
}

// Headers para JSON
header('Content-Type: application/json');

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Obtener ID del producto
$producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;

if ($producto_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID de producto inválido']);
    exit;
}

// Verificar que el producto existe
$pdo = getDB();
if (!$pdo) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM catalogo_productos WHERE id = ?");
$stmt->execute([$producto_id]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
    exit;
}

// Configuración de upload
$upload_dir = __DIR__ . '/../../../assets/documents/catalogo/';
$allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$allowed_extensions = ['pdf', 'doc', 'docx'];
$max_size = 10 * 1024 * 1024; // 10MB

// Crear directorio si no existe
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$uploaded_documents = [];
$errors = [];

// Procesar cada archivo
if (isset($_FILES['documentos']) && is_array($_FILES['documentos']['name'])) {
    $file_count = count($_FILES['documentos']['name']);
    
    for ($i = 0; $i < $file_count; $i++) {
        if ($_FILES['documentos']['error'][$i] !== UPLOAD_ERR_OK) {
            $errors[] = "Error al subir el archivo " . ($i + 1);
            continue;
        }
        
        $file = [
            'name' => $_FILES['documentos']['name'][$i],
            'type' => $_FILES['documentos']['type'][$i],
            'tmp_name' => $_FILES['documentos']['tmp_name'][$i],
            'size' => $_FILES['documentos']['size'][$i]
        ];
        
        // Validar extensión
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowed_extensions)) {
            $errors[] = "El archivo '{$file['name']}' no tiene una extensión permitida (PDF, DOC, DOCX)";
            continue;
        }
        
        // Validar tipo MIME (opcional, algunos servidores no lo reportan correctamente)
        if (!empty($file['type']) && !in_array($file['type'], $allowed_types)) {
            // Permitir si la extensión es válida (algunos servidores reportan tipos incorrectos)
            if (!in_array($extension, $allowed_extensions)) {
                $errors[] = "El archivo '{$file['name']}' no es un tipo de documento válido";
                continue;
            }
        }
        
        // Validar tamaño
        if ($file['size'] > $max_size) {
            $errors[] = "El archivo '{$file['name']}' es demasiado grande (máx. 10MB)";
            continue;
        }
        
        // Generar nombre único
        $filename = 'producto-' . $producto_id . '-' . time() . '-' . $i . '.' . $extension;
        $filepath = $upload_dir . $filename;
        
        // Mover archivo
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            $errors[] = "Error al guardar '{$file['name']}'";
            continue;
        }
        
        // Generar URL relativa
        $archivo_url = 'assets/documents/catalogo/' . $filename;
        
        // Determinar tipo de documento basado en nombre o extensión
        $nombre_archivo = strtolower($file['name']);
        $tipo = 'ficha_tecnica'; // Por defecto
        
        if (strpos($nombre_archivo, 'manual') !== false) {
            $tipo = 'manual';
        } elseif (strpos($nombre_archivo, 'certificado') !== false || strpos($nombre_archivo, 'cert') !== false) {
            $tipo = 'certificado';
        } elseif (strpos($nombre_archivo, 'brochure') !== false || strpos($nombre_archivo, 'folleto') !== false) {
            $tipo = 'brochure';
        }
        
        // Obtener orden máximo
        $stmt = $pdo->prepare("SELECT COALESCE(MAX(orden), 0) FROM catalogo_producto_documentos WHERE producto_id = ?");
        $stmt->execute([$producto_id]);
        $orden = $stmt->fetchColumn() + 1;
        
        // Insertar en base de datos
        $stmt = $pdo->prepare("
            INSERT INTO catalogo_producto_documentos 
            (producto_id, nombre, archivo_url, tipo, tamaño, formato, idioma, orden, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 'es', ?, NOW())
        ");
        
        $nombre = pathinfo($file['name'], PATHINFO_FILENAME);
        $stmt->execute([$producto_id, $nombre, $archivo_url, $tipo, $file['size'], strtoupper($extension), $orden]);
        
        $doc_id = $pdo->lastInsertId();
        
        $uploaded_documents[] = [
            'id' => $doc_id,
            'nombre' => $nombre,
            'url' => SITE_URL . '/' . $archivo_url,
            'tipo' => $tipo,
            'formato' => strtoupper($extension),
            'tamaño' => $file['size']
        ];
    }
}

// Registrar actividad
if (function_exists('logActivity') && !empty($uploaded_documents)) {
    $current_user = getCurrentUser();
    logActivity($current_user['id'], 'editar', 'catalogo', $producto_id, 'producto', [
        'accion' => 'subir_documentos',
        'documentos_subidos' => count($uploaded_documents)
    ]);
}

// Respuesta
if (!empty($uploaded_documents)) {
    echo json_encode([
        'success' => true,
        'message' => count($uploaded_documents) . ' documento(s) subido(s) exitosamente',
        'documents' => $uploaded_documents,
        'errors' => $errors
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => !empty($errors) ? implode(', ', $errors) : 'No se subieron documentos'
    ]);
}

