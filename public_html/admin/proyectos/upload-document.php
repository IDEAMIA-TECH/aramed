<?php
/**
 * ========================================
 * ADMIN - UPLOAD DE DOCUMENTOS DE PROYECTO
 * ========================================
 * 
 * Handler AJAX para subir documentos de proyectos
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
$upload_dir = __DIR__ . '/../../assets/documents/proyectos/';
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
        
        // Validar tamaño
        if ($file['size'] > $max_size) {
            $errors[] = "El archivo '{$file['name']}' es demasiado grande (máx. 10MB)";
            continue;
        }
        
        // Generar nombre único
        $filename = 'proyecto-' . $proyecto_id . '-' . time() . '-' . $i . '.' . $extension;
        $filepath = $upload_dir . $filename;
        
        // Mover archivo
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            $errors[] = "Error al guardar '{$file['name']}'";
            continue;
        }
        
        // Generar URL relativa
        $archivo_url = 'assets/documents/proyectos/' . $filename;
        
        // Obtener orden máximo
        $stmt = $pdo->prepare("SELECT COALESCE(MAX(orden), 0) FROM proyecto_documentos WHERE proyecto_id = ?");
        $stmt->execute([$proyecto_id]);
        $orden = $stmt->fetchColumn() + 1;
        
        // Insertar en base de datos
        $stmt = $pdo->prepare("
            INSERT INTO proyecto_documentos 
            (proyecto_id, archivo_url, nombre, tipo, tamaño, orden, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $nombre = pathinfo($file['name'], PATHINFO_FILENAME);
        $tipo = strtolower($extension);
        $stmt->execute([$proyecto_id, $archivo_url, $nombre, $tipo, $file['size'], $orden]);
        
        $doc_id = $pdo->lastInsertId();
        
        $uploaded_documents[] = [
            'id' => $doc_id,
            'nombre' => $nombre,
            'url' => SITE_URL . '/' . $archivo_url,
            'tipo' => $tipo,
            'tamaño' => $file['size']
        ];
    }
}

// Registrar actividad
if (function_exists('logActivity') && !empty($uploaded_documents)) {
    $current_user = getCurrentUser();
    logActivity($current_user['id'], 'editar', 'proyectos', $proyecto_id, 'proyecto', [
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

