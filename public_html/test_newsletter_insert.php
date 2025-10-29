<?php
/**
 * Script de Diagnóstico - Newsletter INSERT
 * 
 * Verifica si los datos del formulario se están insertando correctamente
 */

define('ARAMED_SITE', true);
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/connection.php';
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>Diagnóstico Newsletter</title>";
echo "<style>body{font-family:Arial,sans-serif;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} pre{background:#f5f5f5;padding:10px;border-radius:5px;}</style>";
echo "</head><body>";
echo "<h1>🔍 Diagnóstico: Inserción de Newsletter</h1>";

try {
    $pdo = getDB();
    if (!$pdo) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    echo "<p class='success'>✅ Conexión a base de datos exitosa</p>";
    
    // Verificar si la tabla existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'newsletter_subscriptions'");
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "<p class='error'>❌ La tabla 'newsletter_subscriptions' NO existe</p>";
        echo "<p>Necesitas ejecutar el script de creación de tablas primero.</p>";
        exit;
    }
    
    echo "<p class='success'>✅ La tabla 'newsletter_subscriptions' existe</p>";
    
    // Mostrar estructura de la tabla
    echo "<h2>Estructura de la tabla:</h2>";
    $stmt = $pdo->query("DESCRIBE newsletter_subscriptions");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='10' style='border-collapse:collapse;width:100%;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td><strong>{$col['Field']}</strong></td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Mostrar registros existentes
    echo "<h2>Registros existentes (últimos 10):</h2>";
    $stmt = $pdo->query("SELECT id, email_oficial, nombre, institucion, status, created_at FROM newsletter_subscriptions ORDER BY id DESC LIMIT 10");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($records) > 0) {
        echo "<table border='1' cellpadding='10' style='border-collapse:collapse;width:100%;'>";
        echo "<tr><th>ID</th><th>Email</th><th>Nombre</th><th>Institución</th><th>Status</th><th>Creado</th></tr>";
        foreach ($records as $record) {
            echo "<tr>";
            echo "<td>{$record['id']}</td>";
            echo "<td>{$record['email_oficial']}</td>";
            echo "<td>{$record['nombre']}</td>";
            echo "<td>{$record['institucion']}</td>";
            echo "<td>{$record['status']}</td>";
            echo "<td>{$record['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='info'>ℹ️ No hay registros en la tabla aún</p>";
    }
    
    // Contar totales
    $stmt = $pdo->query("SELECT COUNT(*) as total, status, COUNT(*) as count FROM newsletter_subscriptions GROUP BY status");
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Estadísticas:</h2>";
    echo "<ul>";
    foreach ($stats as $stat) {
        echo "<li>Status '{$stat['status']}': {$stat['count']} registros</li>";
    }
    echo "</ul>";
    
    // Verificar si hay UNIQUE KEY en email_oficial
    echo "<h2>🔍 Verificación de Restricciones:</h2>";
    $indexStmt = $pdo->query("SHOW INDEXES FROM newsletter_subscriptions WHERE Column_name = 'email_oficial'");
    $indexes = $indexStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='10' style='border-collapse:collapse;width:100%;'>";
    echo "<tr><th>Índice</th><th>Columna</th><th>Único</th><th>Acción</th></tr>";
    foreach ($indexes as $idx) {
        $isUnique = ($idx['Non_unique'] == 0) ? 'SÍ (BLOQUEA MÚLTIPLES INSERTs)' : 'NO';
        $color = ($idx['Non_unique'] == 0) ? 'red' : 'green';
        echo "<tr style='color:{$color}'>";
        echo "<td>{$idx['Key_name']}</td>";
        echo "<td>{$idx['Column_name']}</td>";
        echo "<td><strong>{$isUnique}</strong></td>";
        echo "<td>" . (($idx['Non_unique'] == 0) ? "⚠️ Necesita eliminarse para permitir múltiples solicitudes" : "✅ OK") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if (!empty($indexes) && $indexes[0]['Non_unique'] == 0) {
        echo "<div style='background:#fff3cd;border:2px solid #ffc107;padding:15px;margin:20px 0;border-radius:5px;'>";
        echo "<h3 style='color:#856404;margin-top:0;'>⚠️ PROBLEMA DETECTADO</h3>";
        echo "<p><strong>La tabla tiene un UNIQUE KEY en email_oficial que impide múltiples solicitudes del mismo correo.</strong></p>";
        echo "<p>Para solucionarlo, ejecuta este SQL:</p>";
        echo "<pre style='background:#000;color:#0f0;padding:10px;border-radius:5px;'>ALTER TABLE `newsletter_subscriptions` DROP INDEX `{$indexes[0]['Key_name']}`;\nALTER TABLE `newsletter_subscriptions` ADD INDEX `idx_email_oficial` (`email_oficial`);</pre>";
        echo "<p>O ejecuta el script: <code>database/remove_unique_email_cotizador.sql</code></p>";
        echo "</div>";
    }
    
    // Prueba de INSERT (simulado)
    echo "<h2>🧪 Prueba de INSERT (Simulado):</h2>";
    
    $testEmail = 'test_' . time() . '@example.com';
    $testData = [
        'institucion' => 'Hospital de Prueba',
        'tipo_institucion' => 'Hospital',
        'estado' => 'Ciudad de México',
        'ciudad' => 'CDMX',
        'nombre' => 'Dr. Prueba',
        'puesto' => 'Director',
        'email_oficial' => $testEmail,
        'telefono_oficina' => '5555-1234',
    ];
    
    echo "<p>Intentando INSERT con email: <strong>{$testEmail}</strong></p>";
    
    // Verificar si existe
    $checkStmt = $pdo->prepare("SELECT id FROM newsletter_subscriptions WHERE email_oficial = ?");
    $checkStmt->execute([$testEmail]);
    $exists = $checkStmt->fetch();
    
    if ($exists) {
        echo "<p class='error'>❌ El email de prueba ya existe (ID: {$exists['id']})</p>";
    } else {
        echo "<p class='success'>✅ El email de prueba NO existe, se puede insertar</p>";
        
        // Intentar INSERT
        $sql = "INSERT INTO newsletter_subscriptions (
            institucion, tipo_institucion, estado, ciudad,
            nombre, puesto, email_oficial, telefono_oficina,
            status, created_at
        ) VALUES (
            :institucion, :tipo_institucion, :estado, :ciudad,
            :nombre, :puesto, :email_oficial, :telefono_oficina,
            'active', NOW()
        )";
        
        $insertStmt = $pdo->prepare($sql);
        $insertResult = $insertStmt->execute([
            ':institucion' => $testData['institucion'],
            ':tipo_institucion' => $testData['tipo_institucion'],
            ':estado' => $testData['estado'],
            ':ciudad' => $testData['ciudad'],
            ':nombre' => $testData['nombre'],
            ':puesto' => $testData['puesto'],
            ':email_oficial' => $testData['email_oficial'],
            ':telefono_oficina' => $testData['telefono_oficina']
        ]);
        
        if ($insertResult) {
            $insertId = $pdo->lastInsertId();
            echo "<p class='success'>✅ INSERT EXITOSO! ID insertado: <strong>{$insertId}</strong></p>";
            
            // Verificar que se insertó
            $verifyStmt = $pdo->prepare("SELECT * FROM newsletter_subscriptions WHERE id = ?");
            $verifyStmt->execute([$insertId]);
            $verifyRecord = $verifyStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($verifyRecord) {
                echo "<p class='success'>✅ Verificación exitosa: El registro está en la BD</p>";
                echo "<pre>";
                print_r($verifyRecord);
                echo "</pre>";
                
                // Eliminar el registro de prueba
                $deleteStmt = $pdo->prepare("DELETE FROM newsletter_subscriptions WHERE id = ?");
                $deleteStmt->execute([$insertId]);
                echo "<p class='info'>ℹ️ Registro de prueba eliminado</p>";
            } else {
                echo "<p class='error'>❌ CRÍTICO: INSERT exitoso pero no se encontró el registro!</p>";
            }
        } else {
            $errorInfo = $insertStmt->errorInfo();
            echo "<p class='error'>❌ INSERT FALLÓ</p>";
            echo "<pre>";
            print_r($errorInfo);
            echo "</pre>";
        }
    }
    
    echo "<hr>";
    echo "<h2>📋 Revisar logs:</h2>";
    echo "<p>Para ver los logs detallados, revisa:</p>";
    echo "<ul>";
    echo "<li><code>/logs/debug.log</code> (si ENVIRONMENT es 'development')</li>";
    echo "<li><code>/logs/php-errors.log</code></li>";
    echo "<li>Error Log de PHP en cPanel</li>";
    echo "</ul>";
    
    echo "<h2>🔧 Próximos pasos:</h2>";
    echo "<ol>";
    echo "<li>Enviar un formulario desde el sitio web</li>";
    echo "<li>Revisar los logs para ver exactamente qué está pasando</li>";
    echo "<li>Verificar en la base de datos si se creó el registro</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</body></html>";
?>

