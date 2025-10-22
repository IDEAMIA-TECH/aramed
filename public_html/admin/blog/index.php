<?php
/**
 * ========================================
 * ADMIN - GESTIÓN DEL BLOG
 * ========================================
 * 
 * Panel de administración para gestionar artículos del blog
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/connection.php';

// Verificar autenticación (simplificado para demo)
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['admin_logged_in'] = true; // Para demo, siempre logueado
}

// Obtener parámetros
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Procesar acciones
switch ($action) {
    case 'create':
        $this->handleCreate();
        break;
    case 'edit':
        $this->handleEdit($id);
        break;
    case 'delete':
        $this->handleDelete($id);
        break;
    case 'toggle_status':
        $this->handleToggleStatus($id);
        break;
    default:
        $this->showList();
        break;
}

/**
 * Mostrar lista de artículos
 */
function showList() {
    global $pdo;
    
    // Obtener artículos
    $sql = "
        SELECT a.*, c.nombre as categoria_nombre, c.color as categoria_color
        FROM blog_articulos a
        LEFT JOIN blog_categorias c ON a.categoria_id = c.id
        ORDER BY a.created_at DESC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $articulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener categorías para filtros
    $sql_categorias = "SELECT * FROM blog_categorias WHERE estado = 'activo' ORDER BY nombre";
    $stmt_categorias = $pdo->prepare($sql_categorias);
    $stmt_categorias->execute();
    $categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);
    
    include 'views/list.php';
}

/**
 * Manejar creación de artículo
 */
function handleCreate() {
    global $pdo;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $data = [
                'titulo' => sanitizeInput($_POST['titulo']),
                'slug' => generateSlug($_POST['titulo']),
                'resumen' => sanitizeInput($_POST['resumen']),
                'contenido' => $_POST['contenido'], // No sanitizar para mantener HTML
                'imagen_principal' => sanitizeInput($_POST['imagen_principal']),
                'imagen_og' => sanitizeInput($_POST['imagen_og']),
                'categoria_id' => (int)$_POST['categoria_id'],
                'autor' => sanitizeInput($_POST['autor']),
                'autor_email' => sanitizeEmail($_POST['autor_email']),
                'tags' => json_encode(explode(',', $_POST['tags'])),
                'meta_title' => sanitizeInput($_POST['meta_title']),
                'meta_description' => sanitizeInput($_POST['meta_description']),
                'meta_keywords' => sanitizeInput($_POST['meta_keywords']),
                'estado' => $_POST['estado'],
                'destacado' => isset($_POST['destacado']) ? 1 : 0,
                'fecha_publicacion' => $_POST['fecha_publicacion'] ?: null
            ];
            
            $sql = "
                INSERT INTO blog_articulos (
                    titulo, slug, resumen, contenido, imagen_principal, imagen_og,
                    categoria_id, autor, autor_email, tags, meta_title, meta_description,
                    meta_keywords, estado, destacado, fecha_publicacion, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute(array_values($data));
            
            if ($result) {
                $_SESSION['success_message'] = 'Artículo creado correctamente';
                header('Location: index.php');
                exit;
            } else {
                throw new Exception('Error al crear el artículo');
            }
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }
    }
    
    // Obtener categorías
    $sql_categorias = "SELECT * FROM blog_categorias WHERE estado = 'activo' ORDER BY nombre";
    $stmt_categorias = $pdo->prepare($sql_categorias);
    $stmt_categorias->execute();
    $categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);
    
    include 'views/create.php';
}

/**
 * Manejar edición de artículo
 */
function handleEdit($id) {
    global $pdo;
    
    // Obtener artículo
    $sql = "SELECT * FROM blog_articulos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $articulo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$articulo) {
        $_SESSION['error_message'] = 'Artículo no encontrado';
        header('Location: index.php');
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $data = [
                'titulo' => sanitizeInput($_POST['titulo']),
                'slug' => generateSlug($_POST['titulo']),
                'resumen' => sanitizeInput($_POST['resumen']),
                'contenido' => $_POST['contenido'],
                'imagen_principal' => sanitizeInput($_POST['imagen_principal']),
                'imagen_og' => sanitizeInput($_POST['imagen_og']),
                'categoria_id' => (int)$_POST['categoria_id'],
                'autor' => sanitizeInput($_POST['autor']),
                'autor_email' => sanitizeEmail($_POST['autor_email']),
                'tags' => json_encode(explode(',', $_POST['tags'])),
                'meta_title' => sanitizeInput($_POST['meta_title']),
                'meta_description' => sanitizeInput($_POST['meta_description']),
                'meta_keywords' => sanitizeInput($_POST['meta_keywords']),
                'estado' => $_POST['estado'],
                'destacado' => isset($_POST['destacado']) ? 1 : 0,
                'fecha_publicacion' => $_POST['fecha_publicacion'] ?: null,
                'id' => $id
            ];
            
            $sql = "
                UPDATE blog_articulos SET
                    titulo = ?, slug = ?, resumen = ?, contenido = ?, imagen_principal = ?, imagen_og = ?,
                    categoria_id = ?, autor = ?, autor_email = ?, tags = ?, meta_title = ?, meta_description = ?,
                    meta_keywords = ?, estado = ?, destacado = ?, fecha_publicacion = ?, updated_at = NOW()
                WHERE id = ?
            ";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute(array_values($data));
            
            if ($result) {
                $_SESSION['success_message'] = 'Artículo actualizado correctamente';
                header('Location: index.php');
                exit;
            } else {
                throw new Exception('Error al actualizar el artículo');
            }
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }
    }
    
    // Obtener categorías
    $sql_categorias = "SELECT * FROM blog_categorias WHERE estado = 'activo' ORDER BY nombre";
    $stmt_categorias = $pdo->prepare($sql_categorias);
    $stmt_categorias->execute();
    $categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);
    
    include 'views/edit.php';
}

/**
 * Manejar eliminación de artículo
 */
function handleDelete($id) {
    global $pdo;
    
    try {
        $sql = "DELETE FROM blog_articulos WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$id]);
        
        if ($result) {
            $_SESSION['success_message'] = 'Artículo eliminado correctamente';
        } else {
            throw new Exception('Error al eliminar el artículo');
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }
    
    header('Location: index.php');
    exit;
}

/**
 * Manejar cambio de estado
 */
function handleToggleStatus($id) {
    global $pdo;
    
    try {
        // Obtener estado actual
        $sql = "SELECT estado FROM blog_articulos WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $articulo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($articulo) {
            $nuevo_estado = $articulo['estado'] === 'publicado' ? 'borrador' : 'publicado';
            
            $sql = "UPDATE blog_articulos SET estado = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([$nuevo_estado, $id]);
            
            if ($result) {
                $_SESSION['success_message'] = 'Estado actualizado correctamente';
            } else {
                throw new Exception('Error al actualizar el estado');
            }
        } else {
            throw new Exception('Artículo no encontrado');
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }
    
    header('Location: index.php');
    exit;
}

/**
 * Generar slug único
 */
function generateSlug($titulo) {
    global $pdo;
    
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo)));
    $original_slug = $slug;
    $counter = 1;
    
    // Verificar si el slug ya existe
    while (true) {
        $sql = "SELECT id FROM blog_articulos WHERE slug = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$slug]);
        
        if (!$stmt->fetch()) {
            break;
        }
        
        $slug = $original_slug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}

// Función para procesar tags
function processTags($tags_string) {
    $tags = array_map('trim', explode(',', $tags_string));
    $tags = array_filter($tags, function($tag) {
        return !empty($tag);
    });
    return array_values($tags);
}
?>
