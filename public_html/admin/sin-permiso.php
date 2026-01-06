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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .error-container {
            max-width: 650px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 4rem 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .error-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }
        
        .error-icon {
            font-size: 6rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 2rem;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .lead {
            font-size: 1.2rem;
            color: #5a6c7d;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .alert {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: none;
            border-left: 4px solid #2196f3;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        
        .alert i {
            color: #2196f3;
            font-size: 1.3rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 14px 35px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        
        .user-info {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e0e0e0;
        }
        
        .user-info small {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        
        .module-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            margin: 0.5rem 0;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-shield-exclamation"></i>
        </div>
        
        <h1>Acceso Denegado</h1>
        
        <p class="lead">
            No tienes los permisos necesarios para realizar esta acción.
        </p>
        
        <div class="mb-4">
            <span class="module-badge">
                <i class="bi bi-folder me-2"></i>
                Módulo: <?php echo esc(ucfirst(str_replace('_', ' ', $modulo))); ?>
            </span>
            <br>
            <span class="module-badge" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <i class="bi bi-gear me-2"></i>
                Acción: <?php echo esc(ucfirst($accion)); ?>
            </span>
        </div>
        
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>¿Necesitas acceso?</strong><br>
            Si crees que deberías tener acceso a esta funcionalidad, contacta al administrador del sistema para solicitar los permisos correspondientes.
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

