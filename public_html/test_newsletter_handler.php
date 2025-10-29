<?php
/**
 * Script de diagnóstico para newsletter_handler.php
 * Verifica todos los archivos requeridos y posibles errores
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Diagnóstico - Newsletter Handler</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .ok { color: green; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; }
        pre { background: #000; color: #0f0; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico: Newsletter Handler</h1>
    
    <?php
    define('ARAMED_SITE', true);
    $baseDir = __DIR__ . '/includes';
    $issues = [];
    $success = [];
    
    // 1. Verificar que el directorio includes existe
    echo '<div class="section">';
    echo '<h2>1. Verificación de Directorios</h2>';
    if (is_dir($baseDir)) {
        echo '<p class="ok">✅ El directorio <code>includes/</code> existe</p>';
        echo '<p><strong>Ruta completa:</strong> <code>' . $baseDir . '</code></p>';
        $success[] = 'Directorio includes existe';
    } else {
        echo '<p class="error">❌ El directorio <code>includes/</code> NO existe</p>';
        echo '<p><strong>Ruta esperada:</strong> <code>' . $baseDir . '</code></p>';
        $issues[] = 'Directorio includes no existe';
    }
    echo '</div>';
    
    // 2. Verificar archivos requeridos
    echo '<div class="section">';
    echo '<h2>2. Archivos Requeridos</h2>';
    $requiredFiles = [
        'config.php' => 'Configuración general',
        'connection.php' => 'Conexión a base de datos',
        'functions.php' => 'Funciones auxiliares',
        'email_functions.php' => 'Funciones de email',
        'debug_logger.php' => 'Logger (opcional)',
        'newsletter_handler.php' => 'Handler principal'
    ];
    
    echo '<table border="1" cellpadding="10" style="border-collapse: collapse; width: 100%;">';
    echo '<tr><th>Archivo</th><th>Descripción</th><th>Estado</th><th>Ruta</th></tr>';
    
    foreach ($requiredFiles as $file => $desc) {
        $filePath = $baseDir . '/' . $file;
        $exists = file_exists($filePath);
        $isOptional = ($file === 'debug_logger.php');
        
        echo '<tr>';
        echo '<td><code>' . $file . '</code></td>';
        echo '<td>' . $desc . '</td>';
        
        if ($exists) {
            $size = filesize($filePath);
            $modified = date('Y-m-d H:i:s', filemtime($filePath));
            echo '<td class="ok">✅ Existe (' . number_format($size) . ' bytes)</td>';
            echo '<td><small>' . $filePath . '<br>Modificado: ' . $modified . '</small></td>';
            $success[] = "Archivo {$file} existe";
        } else {
            if ($isOptional) {
                echo '<td class="warning">⚠️ Opcional (no existe)</td>';
            } else {
                echo '<td class="error">❌ FALTA</td>';
                $issues[] = "Archivo requerido faltante: {$file}";
            }
            echo '<td><code>' . $filePath . '</code></td>';
        }
        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';
    
    // 3. Intentar cargar config.php
    echo '<div class="section">';
    echo '<h2>3. Carga de Archivos</h2>';
    
    if (file_exists($baseDir . '/config.php')) {
        try {
            require_once $baseDir . '/config.php';
            echo '<p class="ok">✅ <code>config.php</code> cargado correctamente</p>';
            
            // Verificar constantes importantes
            echo '<h3>Constantes verificadas:</h3>';
            echo '<ul>';
            $constants = ['SITE_URL', 'DB_HOST', 'DB_NAME', 'ENVIRONMENT'];
            foreach ($constants as $const) {
                if (defined($const)) {
                    $value = constant($const);
                    // No mostrar valores sensibles completos
                    if (in_array($const, ['DB_PASS'])) {
                        $value = str_repeat('*', min(strlen($value), 10));
                    }
                    echo "<li class='ok'>✅ <code>{$const}</code> = <code>{$value}</code></li>";
                } else {
                    echo "<li class='error'>❌ <code>{$const}</code> NO definida</li>";
                    $issues[] = "Constante {$const} no definida";
                }
            }
            echo '</ul>';
            $success[] = 'Config.php cargado';
        } catch (Exception $e) {
            echo '<p class="error">❌ Error al cargar <code>config.php</code>: ' . $e->getMessage() . '</p>';
            $issues[] = 'Error en config.php: ' . $e->getMessage();
        } catch (Error $e) {
            echo '<p class="error">❌ Error fatal al cargar <code>config.php</code>: ' . $e->getMessage() . '</p>';
            echo '<p><strong>Archivo:</strong> ' . $e->getFile() . '</p>';
            echo '<p><strong>Línea:</strong> ' . $e->getLine() . '</p>';
            $issues[] = 'Error fatal en config.php: ' . $e->getMessage();
        }
    } else {
        echo '<p class="error">❌ No se puede cargar <code>config.php</code> porque no existe</p>';
    }
    echo '</div>';
    
    // 4. Verificar conexión a base de datos
    echo '<div class="section">';
    echo '<h2>4. Conexión a Base de Datos</h2>';
    
    if (file_exists($baseDir . '/connection.php')) {
        try {
            require_once $baseDir . '/connection.php';
            
            if (function_exists('getDB')) {
                echo '<p class="ok">✅ Función <code>getDB()</code> disponible</p>';
                try {
                    $pdo = getDB();
                    if ($pdo) {
                        echo '<p class="ok">✅ Conexión a base de datos exitosa</p>';
                        $success[] = 'Conexión BD exitosa';
                    } else {
                        echo '<p class="error">❌ <code>getDB()</code> retornó NULL</p>';
                        $issues[] = 'getDB() retornó NULL';
                    }
                } catch (Exception $e) {
                    echo '<p class="error">❌ Error al conectar: ' . $e->getMessage() . '</p>';
                    $issues[] = 'Error de conexión BD: ' . $e->getMessage();
                } catch (Error $e) {
                    echo '<p class="error">❌ Error fatal al conectar: ' . $e->getMessage() . '</p>';
                    $issues[] = 'Error fatal BD: ' . $e->getMessage();
                }
            } else {
                echo '<p class="error">❌ Función <code>getDB()</code> NO está disponible</p>';
                $issues[] = 'Función getDB() no existe';
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ Error al cargar <code>connection.php</code>: ' . $e->getMessage() . '</p>';
            $issues[] = 'Error en connection.php: ' . $e->getMessage();
        } catch (Error $e) {
            echo '<p class="error">❌ Error fatal al cargar <code>connection.php</code>: ' . $e->getMessage() . '</p>';
            echo '<p><strong>Archivo:</strong> ' . $e->getFile() . '</p>';
            echo '<p><strong>Línea:</strong> ' . $e->getLine() . '</p>';
            $issues[] = 'Error fatal en connection.php: ' . $e->getMessage();
        }
    } else {
        echo '<p class="error">❌ No se puede probar la conexión porque <code>connection.php</code> no existe</p>';
    }
    echo '</div>';
    
    // 5. Verificar permisos
    echo '<div class="section">';
    echo '<h2>5. Permisos de Archivos</h2>';
    
    $handlerPath = $baseDir . '/newsletter_handler.php';
    if (file_exists($handlerPath)) {
        $perms = fileperms($handlerPath);
        $readable = is_readable($handlerPath);
        $executable = is_executable($handlerPath);
        
        echo '<ul>';
        echo '<li><strong>Permisos:</strong> ' . substr(sprintf('%o', $perms), -4) . '</li>';
        echo '<li><strong>Legible:</strong> ' . ($readable ? '<span class="ok">✅ Sí</span>' : '<span class="error">❌ No</span>') . '</li>';
        echo '<li><strong>Ejecutable:</strong> ' . ($executable ? '<span class="ok">✅ Sí</span>' : '<span class="warning">⚠️ No (puede ser normal en PHP)</span>') . '</li>';
        echo '</ul>';
        
        if (!$readable) {
            $issues[] = 'newsletter_handler.php no es legible';
        }
    }
    echo '</div>';
    
    // 6. Resumen
    echo '<div class="section">';
    echo '<h2>6. Resumen</h2>';
    
    if (empty($issues)) {
        echo '<p style="color: green; font-size: 18px; font-weight: bold;">✅ NO SE ENCONTRARON PROBLEMAS</p>';
        echo '<p>El handler debería funcionar correctamente.</p>';
    } else {
        echo '<p style="color: red; font-size: 18px; font-weight: bold;">❌ PROBLEMAS ENCONTRADOS:</p>';
        echo '<ul>';
        foreach ($issues as $issue) {
            echo '<li class="error">' . $issue . '</li>';
        }
        echo '</ul>';
    }
    
    echo '<h3>✅ Verificaciones Exitosas (' . count($success) . '):</h3>';
    echo '<ul>';
    foreach ($success as $item) {
        echo '<li class="ok">' . $item . '</li>';
    }
    echo '</ul>';
    echo '</div>';
    
    // 7. Información del servidor
    echo '<div class="section">';
    echo '<h2>7. Información del Servidor</h2>';
    echo '<ul>';
    echo '<li><strong>PHP Version:</strong> ' . PHP_VERSION . '</li>';
    echo '<li><strong>Directorio actual:</strong> <code>' . __DIR__ . '</code></li>';
    echo '<li><strong>Directorio includes:</strong> <code>' . $baseDir . '</code></li>';
    echo '<li><strong>Document Root:</strong> <code>' . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . '</code></li>';
    echo '<li><strong>Script Name:</strong> <code>' . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . '</code></li>';
    echo '<li><strong>Request URI:</strong> <code>' . ($_SERVER['REQUEST_URI'] ?? 'N/A') . '</code></li>';
    echo '</ul>';
    echo '</div>';
    
    // 8. Prueba directa del handler (si no hay problemas críticos)
    if (empty($issues) && file_exists($handlerPath)) {
        echo '<div class="section">';
        echo '<h2>8. Prueba de Handler (Solo verificación de sintaxis)</h2>';
        
        // Usar php -l para verificar sintaxis
        $output = [];
        $returnVar = 0;
        exec('php -l ' . escapeshellarg($handlerPath) . ' 2>&1', $output, $returnVar);
        
        if ($returnVar === 0) {
            echo '<p class="ok">✅ La sintaxis de <code>newsletter_handler.php</code> es válida</p>';
        } else {
            echo '<p class="error">❌ Error de sintaxis en <code>newsletter_handler.php</code>:</p>';
            echo '<pre>' . htmlspecialchars(implode("\n", $output)) . '</pre>';
            $issues[] = 'Error de sintaxis en newsletter_handler.php';
        }
        echo '</div>';
    }
    ?>
    
    <div class="section">
        <h2>📋 Próximos Pasos</h2>
        <ol>
            <li>Si hay problemas, corrígelos según se indica arriba</li>
            <li>Prueba enviar el formulario desde el sitio web</li>
            <li>Revisa los logs del servidor si persisten los errores</li>
            <li>Verifica que la ruta del formulario sea correcta: <code>includes/newsletter_handler.php</code></li>
        </ol>
    </div>
</body>
</html>
