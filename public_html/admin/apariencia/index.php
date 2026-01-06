<?php
/**
 * ========================================
 * ADMIN - APARIENCIA & MÓDULOS - DASHBOARD
 * ========================================
 * 
 * Dashboard principal del módulo de Apariencia
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

// Verificar que el usuario sea admin (Apariencia es solo para admin)
$user_role = $_SESSION['admin_rol'] ?? 'editor';
if ($user_role !== 'admin') {
    header('Location: ../sin-permiso.php?modulo=configuracion&accion=ver');
    exit;
}

// Verificar permisos RBAC
if (function_exists('checkPermission')) {
    checkPermission('configuracion', 'ver');
}

// Obtener conexión PDO
$pdo = getDB();
if (!$pdo) {
    die('Error de conexión a la base de datos');
}

// Obtener información del usuario actual
$current_user = getCurrentUser();

// Obtener estadísticas
$stats = [
    'secciones_activas' => 0,
    'secciones_inactivas' => 0,
    'paginas_publicadas' => 0,
    'paginas_borrador' => 0
];

try {
    // Estadísticas de secciones
    $stmt = $pdo->query("SELECT 
        SUM(CASE WHEN activa = 1 THEN 1 ELSE 0 END) as activas,
        SUM(CASE WHEN activa = 0 THEN 1 ELSE 0 END) as inactivas
        FROM home_secciones");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['secciones_activas'] = (int)($result['activas'] ?? 0);
    $stats['secciones_inactivas'] = (int)($result['inactivas'] ?? 0);
    
    // Estadísticas de páginas
    $stmt = $pdo->query("SELECT 
        SUM(CASE WHEN estado = 'publicado' THEN 1 ELSE 0 END) as publicadas,
        SUM(CASE WHEN estado = 'borrador' THEN 1 ELSE 0 END) as borrador
        FROM paginas_estaticas");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['paginas_publicadas'] = (int)($result['publicadas'] ?? 0);
    $stats['paginas_borrador'] = (int)($result['borrador'] ?? 0);
} catch (Exception $e) {
    // Tablas pueden no existir aún
}

// El menú calculará automáticamente $current_dir y $current_page desde $_SERVER['PHP_SELF']
// No es necesario definirlos aquí, pero podemos hacerlo si queremos forzar valores
$current_page = 'index.php';
// $current_dir se calculará automáticamente en admin_menu.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apariencia & Módulos - Aramed Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --border-radius: 8px;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .admin-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            box-shadow: var(--shadow);
        }

        .module-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            height: 100%;
            border-left: 4px solid var(--primary-color);
        }

        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .module-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            opacity: 0.9;
        }

        .module-card h4 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .module-card p {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }

        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            border-left: 4px solid var(--primary-color);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .admin-content {
            background: transparent;
            padding: 2rem;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (se incluye automáticamente desde admin_menu.php) -->
            <?php include __DIR__ . '/../includes/admin_menu.php'; ?>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-9 admin-content">
                <!-- Header -->
                <div class="admin-header">
                    <div class="container-fluid">
                        <h1><i class="bi bi-palette me-3"></i>Apariencia & Módulos</h1>
                        <p>Gestiona las secciones del Home y páginas estáticas del sitio</p>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number text-success"><?php echo $stats['secciones_activas']; ?></div>
                            <div class="stats-label">Secciones Activas</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number text-warning"><?php echo $stats['secciones_inactivas']; ?></div>
                            <div class="stats-label">Secciones Inactivas</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number text-primary"><?php echo $stats['paginas_publicadas']; ?></div>
                            <div class="stats-label">Páginas Publicadas</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number text-secondary"><?php echo $stats['paginas_borrador']; ?></div>
                            <div class="stats-label">Páginas en Borrador</div>
                        </div>
                    </div>
                </div>

                <!-- Modules -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <a href="secciones.php" class="text-decoration-none">
                            <div class="module-card">
                                <div class="module-icon">
                                    <i class="bi bi-layout-text-window"></i>
                                </div>
                                <h4>Gestión de Secciones</h4>
                                <p>Activa, desactiva y reordena las secciones del Home. Controla qué secciones se muestran en la página principal.</p>
                                <div class="d-flex align-items-center text-primary">
                                    <span>Gestionar secciones</span>
                                    <i class="bi bi-arrow-right ms-2"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <a href="paginas.php" class="text-decoration-none">
                            <div class="module-card">
                                <div class="module-icon" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <h4>Páginas Estáticas</h4>
                                <p>Crea y gestiona páginas estáticas personalizadas como "Sobre Nosotros", "Política de Privacidad", etc.</p>
                                <div class="d-flex align-items-center text-primary">
                                    <span>Gestionar páginas</span>
                                    <i class="bi bi-arrow-right ms-2"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <a href="vista-previa.php" class="text-decoration-none">
                            <div class="module-card">
                                <div class="module-icon" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
                                    <i class="bi bi-eye"></i>
                                </div>
                                <h4>Vista Previa</h4>
                                <p>Visualiza cómo se verá el Home con los cambios aplicados antes de publicarlos.</p>
                                <div class="d-flex align-items-center text-primary">
                                    <span>Ver vista previa</span>
                                    <i class="bi bi-arrow-right ms-2"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

