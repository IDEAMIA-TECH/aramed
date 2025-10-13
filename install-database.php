<?php
/**
 * ========================================
 * INSTALADOR DE BASE DE DATOS - ARAMED
 * ========================================
 * 
 * Script para crear las tablas necesarias
 * EJECUTAR UNA SOLA VEZ y luego ELIMINAR
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Configuración de base de datos
define('DB_HOST', '173.231.22.109');
define('DB_NAME', 'aramed2025_produccion');
define('DB_USER', 'aramed2025_prod');
define('DB_PASS', 'pmDLi&PB$zntrzJ4');

// Configuración de seguridad
define('INSTALL_PASSWORD', 'Aramed2025!Install'); // Cambiar en producción

// Verificar contraseña
$password = $_GET['password'] ?? '';
if ($password !== INSTALL_PASSWORD) {
    die('❌ Acceso denegado. Proporciona la contraseña correcta: ?password=TU_PASSWORD');
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador de Base de Datos - Aramed</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0B9FD9 0%, #0988BA 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: #1A1A1A;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin-bottom: 10px;
            font-size: 28px;
        }
        .header p {
            opacity: 0.8;
            font-size: 14px;
        }
        .content {
            padding: 40px;
        }
        .step {
            background: #f8f9fa;
            border-left: 4px solid #0B9FD9;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .step h3 {
            color: #1A1A1A;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .step-number {
            background: #0B9FD9;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        .success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        .warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        .info {
            background: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }
        .log {
            background: #1A1A1A;
            color: #0B9FD9;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            max-height: 300px;
            overflow-y: auto;
            margin-top: 15px;
        }
        .log-line {
            margin-bottom: 5px;
        }
        .log-success { color: #28a745; }
        .log-error { color: #dc3545; }
        .log-warning { color: #ffc107; }
        .footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            font-size: 12px;
            color: #6c757d;
        }
        code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            color: #e83e8c;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #0B9FD9;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn:hover {
            background: #0988BA;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🗄️ Instalador de Base de Datos</h1>
            <p>Aramed y Laboratorio - Landing Page</p>
        </div>
        
        <div class="content">
            
            <?php
            // Array para almacenar logs
            $logs = [];
            $hasErrors = false;
            
            // Paso 1: Verificar conexión
            echo '<div class="step">';
            echo '<h3><span class="step-number">1</span>Verificando Conexión</h3>';
            
            try {
                $pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
                
                echo '<div class="success">';
                echo '✅ Conexión exitosa a la base de datos<br>';
                echo '📊 <strong>Base de datos:</strong> ' . DB_NAME . '<br>';
                echo '🌐 <strong>Host:</strong> ' . DB_HOST . '<br>';
                echo '👤 <strong>Usuario:</strong> ' . DB_USER;
                echo '</div>';
                
                $logs[] = ['type' => 'success', 'msg' => 'Conexión establecida correctamente'];
                
            } catch (PDOException $e) {
                echo '<div class="error">';
                echo '❌ Error de conexión: ' . htmlspecialchars($e->getMessage());
                echo '</div>';
                
                $logs[] = ['type' => 'error', 'msg' => 'Error de conexión: ' . $e->getMessage()];
                $hasErrors = true;
            }
            
            echo '</div>';
            
            // Si hay errores, detener aquí
            if ($hasErrors) {
                echo '<div class="step error">';
                echo '<h3>⚠️ No se puede continuar</h3>';
                echo '<p>Por favor verifica las credenciales de la base de datos y vuelve a intentar.</p>';
                echo '</div>';
            } else {
                
                // Paso 2: Verificar tablas existentes
                echo '<div class="step">';
                echo '<h3><span class="step-number">2</span>Verificando Tablas Existentes</h3>';
                
                try {
                    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                    
                    $requiredTables = ['newsletter_subscriptions', 'contact_messages', 'contact_quotes'];
                    $existingTables = array_intersect($requiredTables, $tables);
                    $missingTables = array_diff($requiredTables, $tables);
                    
                    if (count($existingTables) > 0) {
                        echo '<div class="warning">';
                        echo '⚠️ Tablas existentes encontradas:<br>';
                        echo '<ul style="margin-left: 20px; margin-top: 10px;">';
                        foreach ($existingTables as $table) {
                            echo '<li><code>' . $table . '</code></li>';
                        }
                        echo '</ul>';
                        echo '<p style="margin-top: 10px;">Estas tablas NO serán modificadas.</p>';
                        echo '</div>';
                        
                        $logs[] = ['type' => 'warning', 'msg' => 'Encontradas ' . count($existingTables) . ' tablas existentes'];
                    }
                    
                    if (count($missingTables) > 0) {
                        echo '<div class="info">';
                        echo 'ℹ️ Tablas a crear:<br>';
                        echo '<ul style="margin-left: 20px; margin-top: 10px;">';
                        foreach ($missingTables as $table) {
                            echo '<li><code>' . $table . '</code></li>';
                        }
                        echo '</ul>';
                        echo '</div>';
                        
                        $logs[] = ['type' => 'info', 'msg' => 'Se crearán ' . count($missingTables) . ' tablas'];
                    } else {
                        echo '<div class="success">';
                        echo '✅ Todas las tablas ya existen. No es necesario crear nada.';
                        echo '</div>';
                    }
                    
                } catch (PDOException $e) {
                    echo '<div class="error">';
                    echo '❌ Error al verificar tablas: ' . htmlspecialchars($e->getMessage());
                    echo '</div>';
                    
                    $logs[] = ['type' => 'error', 'msg' => 'Error al verificar tablas: ' . $e->getMessage()];
                    $hasErrors = true;
                }
                
                echo '</div>';
                
                // Paso 3: Crear tablas
                if (!$hasErrors && count($missingTables) > 0) {
                    echo '<div class="step">';
                    echo '<h3><span class="step-number">3</span>Creando Tablas</h3>';
                    
                    // SQL para crear tablas
                    $sqlStatements = [
                        'newsletter_subscriptions' => "
                            CREATE TABLE IF NOT EXISTS `newsletter_subscriptions` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `institucion` varchar(255) NOT NULL,
                              `tipo_institucion` varchar(100) NOT NULL,
                              `campo_adicional` varchar(255) DEFAULT NULL,
                              `estado` varchar(100) NOT NULL,
                              `ciudad` varchar(150) NOT NULL,
                              `nombre` varchar(255) NOT NULL,
                              `puesto` varchar(150) NOT NULL,
                              `email_oficial` varchar(255) NOT NULL,
                              `email_alterno` varchar(255) DEFAULT NULL,
                              `telefono_oficina` varchar(50) NOT NULL,
                              `extension` varchar(20) DEFAULT NULL,
                              `telefono_celular` varchar(50) DEFAULT NULL,
                              `producto_interes` varchar(150) DEFAULT NULL,
                              `fecha_compra_aprox` date DEFAULT NULL,
                              `observaciones` text DEFAULT NULL,
                              `ip_address` varchar(45) DEFAULT NULL,
                              `user_agent` varchar(500) DEFAULT NULL,
                              `status` enum('active','inactive','unsubscribed') DEFAULT 'active',
                              `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                              `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
                              PRIMARY KEY (`id`),
                              UNIQUE KEY `email_oficial` (`email_oficial`),
                              KEY `idx_status` (`status`),
                              KEY `idx_estado` (`estado`),
                              KEY `idx_tipo_institucion` (`tipo_institucion`),
                              KEY `idx_created_at` (`created_at`),
                              KEY `idx_producto_interes` (`producto_interes`),
                              KEY `idx_fecha_compra` (`fecha_compra_aprox`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",
                        'contact_messages' => "
                            CREATE TABLE IF NOT EXISTS `contact_messages` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `nombre` varchar(255) NOT NULL,
                              `email` varchar(255) NOT NULL,
                              `telefono` varchar(50) NOT NULL,
                              `institucion` varchar(255) DEFAULT NULL,
                              `asunto` varchar(150) NOT NULL,
                              `mensaje` text NOT NULL,
                              `ip_address` varchar(45) DEFAULT NULL,
                              `user_agent` varchar(500) DEFAULT NULL,
                              `status` enum('nuevo','en_proceso','respondido','cerrado') DEFAULT 'nuevo',
                              `assigned_to` int(11) DEFAULT NULL,
                              `notes` text DEFAULT NULL,
                              `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                              `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
                              PRIMARY KEY (`id`),
                              KEY `idx_status` (`status`),
                              KEY `idx_email` (`email`),
                              KEY `idx_asunto` (`asunto`),
                              KEY `idx_created_at` (`created_at`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ",
                        'contact_quotes' => "
                            CREATE TABLE IF NOT EXISTS `contact_quotes` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `nombre` varchar(255) NOT NULL,
                              `email` varchar(255) NOT NULL,
                              `telefono` varchar(50) NOT NULL,
                              `institucion` varchar(255) NOT NULL,
                              `tipo_institucion` varchar(100) NOT NULL,
                              `estado` varchar(100) NOT NULL,
                              `ciudad` varchar(150) NOT NULL,
                              `productos` text NOT NULL,
                              `presupuesto_estimado` varchar(100) DEFAULT NULL,
                              `fecha_requerida` date DEFAULT NULL,
                              `mensaje` text DEFAULT NULL,
                              `ip_address` varchar(45) DEFAULT NULL,
                              `user_agent` varchar(500) DEFAULT NULL,
                              `status` enum('nueva','revisada','cotizada','enviada','cerrada') DEFAULT 'nueva',
                              `cotizacion_enviada` tinyint(1) DEFAULT 0,
                              `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                              `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
                              PRIMARY KEY (`id`),
                              KEY `idx_status` (`status`),
                              KEY `idx_email` (`email`),
                              KEY `idx_created_at` (`created_at`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        "
                    ];
                    
                    $createdCount = 0;
                    foreach ($sqlStatements as $tableName => $sql) {
                        if (in_array($tableName, $missingTables)) {
                            try {
                                $pdo->exec($sql);
                                echo '<div class="success">';
                                echo '✅ Tabla <code>' . $tableName . '</code> creada exitosamente';
                                echo '</div>';
                                
                                $logs[] = ['type' => 'success', 'msg' => "Tabla $tableName creada"];
                                $createdCount++;
                                
                            } catch (PDOException $e) {
                                echo '<div class="error">';
                                echo '❌ Error al crear tabla <code>' . $tableName . '</code>: ' . htmlspecialchars($e->getMessage());
                                echo '</div>';
                                
                                $logs[] = ['type' => 'error', 'msg' => "Error en $tableName: " . $e->getMessage()];
                                $hasErrors = true;
                            }
                        }
                    }
                    
                    echo '</div>';
                    
                    // Paso 4: Resumen
                    echo '<div class="step ' . ($hasErrors ? 'warning' : 'success') . '">';
                    echo '<h3><span class="step-number">4</span>Resumen de Instalación</h3>';
                    
                    if (!$hasErrors && $createdCount > 0) {
                        echo '<p><strong>✅ Instalación completada exitosamente!</strong></p>';
                        echo '<p>Se crearon <strong>' . $createdCount . '</strong> tabla(s) correctamente.</p>';
                        echo '<ul style="margin-left: 20px; margin-top: 15px;">';
                        echo '<li>✅ Base de datos lista para recibir datos</li>';
                        echo '<li>✅ Formularios web pueden funcionar</li>';
                        echo '<li>✅ Newsletter y contacto operativos</li>';
                        echo '</ul>';
                        
                        echo '<div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 5px;">';
                        echo '<strong>⚠️ IMPORTANTE:</strong><br>';
                        echo 'Por seguridad, <strong>ELIMINA este archivo</strong> después de la instalación:<br>';
                        echo '<code>rm install-database.php</code>';
                        echo '</div>';
                        
                    } elseif ($createdCount === 0 && !$hasErrors) {
                        echo '<p><strong>ℹ️ No fue necesario crear tablas</strong></p>';
                        echo '<p>Todas las tablas requeridas ya existen en la base de datos.</p>';
                        
                    } else {
                        echo '<p><strong>⚠️ Instalación completada con advertencias</strong></p>';
                        echo '<p>Revisa los errores arriba y corrige antes de usar el sistema.</p>';
                    }
                    
                    echo '</div>';
                }
            }
            
            // Log completo
            echo '<div class="step">';
            echo '<h3><span class="step-number">📋</span>Log de Instalación</h3>';
            echo '<div class="log">';
            foreach ($logs as $log) {
                $class = 'log-' . $log['type'];
                echo '<div class="log-line ' . $class . '">[' . date('H:i:s') . '] ' . htmlspecialchars($log['msg']) . '</div>';
            }
            echo '</div>';
            echo '</div>';
            
            // Botón para ir al sitio
            if (!$hasErrors) {
                echo '<div style="text-align: center; margin-top: 30px;">';
                echo '<a href="/" class="btn">🚀 Ir al Sitio Web</a>';
                echo '</div>';
            }
            ?>
            
        </div>
        
        <div class="footer">
            <p><strong>Aramed y Laboratorio</strong> - Instalador de Base de Datos v1.0</p>
            <p>Desarrollado por IDEAMIA Tech | 2025</p>
        </div>
    </div>
</body>
</html>

