<?php
/**
 * ========================================
 * ADMIN - SIN PERMISO
 * ========================================
 * 
 * Página mostrada cuando un usuario no tiene permisos para acceder
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración y verificar autenticación
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/connection.php';
require_once __DIR__ . '/auth_check.php';

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Obtener módulo y acción desde GET (si están disponibles)
$modulo = $_GET['modulo'] ?? 'desconocido';
$accion = $_GET['accion'] ?? 'acceder';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sin Permiso - Admin <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .error-container {
            max-width: 600px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            text-align: center;
        }
        
        .error-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 1.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0066cc 0%, #004499 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-shield-exclamation"></i>
        </div>
        
        <h1 class="mb-3">Acceso Denegado</h1>
        
        <p class="lead text-muted mb-4">
            No tienes permisos para <?php echo esc($accion); ?> en el módulo <strong><?php echo esc($modulo); ?></strong>.
        </p>
        
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Si necesitas acceso a esta funcionalidad, contacta al administrador del sistema.
        </div>
        
        <div class="mt-4">
            <a href="index.php" class="btn btn-primary">
                <i class="bi bi-house me-2"></i>Volver al Dashboard
            </a>
        </div>
        
        <div class="mt-3">
            <small class="text-muted">
                Usuario: <strong><?php echo esc($current_user['nombre']); ?></strong> 
                (<?php echo esc($current_user['rol']); ?>)
            </small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

