<?php
/**
 * ========================================
 * VERIFICAR ARCHIVO usuarios.php
 * ========================================
 * 
 * Script para verificar el archivo usuarios.php línea por línea
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

echo "<h2>🔍 VERIFICANDO ARCHIVO usuarios.php</h2><hr>";

$archivo = __DIR__ . '/admin/usuarios.php';

if (!file_exists($archivo)) {
    echo "❌ <strong>El archivo usuarios.php no existe</strong><br>";
    exit;
}

echo "✅ <strong>Archivo usuarios.php encontrado</strong><br><br>";

// Leer el archivo línea por línea
$lineas = file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

echo "<h3>📋 ANÁLISIS DEL ARCHIVO</h3>";
echo "<strong>Total de líneas:</strong> " . count($lineas) . "<br><br>";

// Verificar sintaxis PHP
echo "<h3>🔧 VERIFICANDO SINTAXIS PHP</h3>";

$sintaxis_ok = true;
$errores_sintaxis = [];

// Verificar si hay errores de sintaxis
$output = [];
$return_var = 0;
exec("php -l " . escapeshellarg($archivo) . " 2>&1", $output, $return_var);

if ($return_var === 0) {
    echo "✅ <strong>Sintaxis PHP correcta</strong><br>";
} else {
    echo "❌ <strong>Errores de sintaxis encontrados:</strong><br>";
    foreach ($output as $error) {
        echo "&nbsp;&nbsp;• " . esc($error) . "<br>";
    }
    $sintaxis_ok = false;
}

echo "<br>";

// Verificar includes
echo "<h3>📁 VERIFICANDO INCLUDES</h3>";

$includes_encontrados = [];
foreach ($lineas as $num_linea => $linea) {
    if (preg_match('/require_once|include_once|require|include/', $linea)) {
        $includes_encontrados[] = [
            'linea' => $num_linea + 1,
            'contenido' => trim($linea)
        ];
    }
}

echo "<strong>Includes encontrados:</strong><br>";
foreach ($includes_encontrados as $include) {
    echo "&nbsp;&nbsp;• Línea {$include['linea']}: " . esc($include['contenido']) . "<br>";
}

echo "<br>";

// Verificar funciones
echo "<h3>🔧 VERIFICANDO FUNCIONES</h3>";

$funciones_encontradas = [];
foreach ($lineas as $num_linea => $linea) {
    if (preg_match('/function\s+(\w+)/', $linea, $matches)) {
        $funciones_encontradas[] = [
            'linea' => $num_linea + 1,
            'funcion' => $matches[1]
        ];
    }
}

if (empty($funciones_encontradas)) {
    echo "ℹ️ <strong>No se encontraron definiciones de funciones en el archivo</strong><br>";
} else {
    echo "<strong>Funciones encontradas:</strong><br>";
    foreach ($funciones_encontradas as $funcion) {
        echo "&nbsp;&nbsp;• Línea {$funcion['linea']}: {$funcion['funcion']}<br>";
    }
}

echo "<br>";

// Verificar clases
echo "<h3>🏗️ VERIFICANDO CLASES</h3>";

$clases_encontradas = [];
foreach ($lineas as $num_linea => $linea) {
    if (preg_match('/class\s+(\w+)/', $linea, $matches)) {
        $clases_encontradas[] = [
            'linea' => $num_linea + 1,
            'clase' => $matches[1]
        ];
    }
}

if (empty($clases_encontradas)) {
    echo "ℹ️ <strong>No se encontraron definiciones de clases en el archivo</strong><br>";
} else {
    echo "<strong>Clases encontradas:</strong><br>";
    foreach ($clases_encontradas as $clase) {
        echo "&nbsp;&nbsp;• Línea {$clase['linea']}: {$clase['clase']}<br>";
    }
}

echo "<br>";

// Verificar etiquetas PHP
echo "<h3>🏷️ VERIFICANDO ETIQUETAS PHP</h3>";

$etiquetas_php = [];
foreach ($lineas as $num_linea => $linea) {
    if (preg_match('/<\?php|<\?=|\?>/', $linea)) {
        $etiquetas_php[] = [
            'linea' => $num_linea + 1,
            'contenido' => trim($linea)
        ];
    }
}

echo "<strong>Etiquetas PHP encontradas:</strong> " . count($etiquetas_php) . "<br>";

// Mostrar algunas etiquetas como ejemplo
$ejemplos = array_slice($etiquetas_php, 0, 5);
foreach ($ejemplos as $etiqueta) {
    echo "&nbsp;&nbsp;• Línea {$etiqueta['linea']}: " . esc($etiqueta['contenido']) . "<br>";
}

if (count($etiquetas_php) > 5) {
    echo "&nbsp;&nbsp;• ... y " . (count($etiquetas_php) - 5) . " más<br>";
}

echo "<br>";

// Verificar si hay HTML
echo "<h3>🌐 VERIFICANDO CONTENIDO HTML</h3>";

$html_encontrado = false;
$lineas_html = [];

foreach ($lineas as $num_linea => $linea) {
    if (preg_match('/<html|<head|<body|<div|<table|<form/', $linea)) {
        $html_encontrado = true;
        $lineas_html[] = [
            'linea' => $num_linea + 1,
            'contenido' => trim($linea)
        ];
    }
}

if ($html_encontrado) {
    echo "✅ <strong>Contenido HTML encontrado</strong><br>";
    echo "<strong>Primeras líneas HTML:</strong><br>";
    $ejemplos_html = array_slice($lineas_html, 0, 5);
    foreach ($ejemplos_html as $html) {
        echo "&nbsp;&nbsp;• Línea {$html['linea']}: " . esc($html['contenido']) . "<br>";
    }
} else {
    echo "❌ <strong>No se encontró contenido HTML</strong><br>";
}

echo "<br>";

// Verificar si hay errores comunes
echo "<h3>⚠️ VERIFICANDO ERRORES COMUNES</h3>";

$errores_comunes = [];

foreach ($lineas as $num_linea => $linea) {
    $num_linea_real = $num_linea + 1;
    
    // Verificar paréntesis no balanceados
    $abiertos = substr_count($linea, '(');
    $cerrados = substr_count($linea, ')');
    if ($abiertos !== $cerrados) {
        $errores_comunes[] = "Línea $num_linea_real: Paréntesis no balanceados";
    }
    
    // Verificar llaves no balanceadas
    $abiertos = substr_count($linea, '{');
    $cerrados = substr_count($linea, '}');
    if ($abiertos !== $cerrados) {
        $errores_comunes[] = "Línea $num_linea_real: Llaves no balanceadas";
    }
    
    // Verificar comillas no cerradas
    if (preg_match('/"[^"]*$/', $linea) || preg_match("/'[^']*$/", $linea)) {
        $errores_comunes[] = "Línea $num_linea_real: Posible comilla no cerrada";
    }
    
    // Verificar punto y coma faltante
    if (preg_match('/\$\w+\s*=\s*[^;]+$/', $linea) && !preg_match('/\?>$/', $linea)) {
        $errores_comunes[] = "Línea $num_linea_real: Posible punto y coma faltante";
    }
}

if (empty($errores_comunes)) {
    echo "✅ <strong>No se encontraron errores comunes</strong><br>";
} else {
    echo "⚠️ <strong>Errores comunes encontrados:</strong><br>";
    foreach ($errores_comunes as $error) {
        echo "&nbsp;&nbsp;• $error<br>";
    }
}

echo "<br>";

// Resumen
echo "<h3>📊 RESUMEN</h3>";
echo "<strong>Archivo:</strong> usuarios.php<br>";
echo "<strong>Líneas totales:</strong> " . count($lineas) . "<br>";
echo "<strong>Sintaxis PHP:</strong> " . ($sintaxis_ok ? "✅ Correcta" : "❌ Con errores") . "<br>";
echo "<strong>Includes:</strong> " . count($includes_encontrados) . "<br>";
echo "<strong>Funciones:</strong> " . count($funciones_encontradas) . "<br>";
echo "<strong>Clases:</strong> " . count($clases_encontradas) . "<br>";
echo "<strong>Etiquetas PHP:</strong> " . count($etiquetas_php) . "<br>";
echo "<strong>Contenido HTML:</strong> " . ($html_encontrado ? "✅ Sí" : "❌ No") . "<br>";
echo "<strong>Errores comunes:</strong> " . count($errores_comunes) . "<br>";

echo "<hr>";
echo "<p><strong>Nota:</strong> Si todo parece correcto aquí, el problema podría estar en la ejecución del archivo o en los includes.</p>";
?>
