<?php
echo "PHP funciona en el directorio raíz";
echo "<br>Fecha: " . date('Y-m-d H:i:s');
echo "<br>Servidor: " . $_SERVER['SERVER_NAME'];
echo "<br>Directorio actual: " . __DIR__;
echo "<br>Archivos en el directorio:";
echo "<ul>";
$files = scandir('.');
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo "<li>$file</li>";
    }
}
echo "</ul>";
?> 