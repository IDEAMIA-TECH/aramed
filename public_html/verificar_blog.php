<?php
require_once 'includes/config.php';
require_once 'includes/connection.php';

echo "Verificando tablas del blog...\n";

$stmt = $pdo->query("SHOW TABLES LIKE 'blog_%'");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($tables)) {
    echo "No se encontraron tablas del blog. Creando...\n";
    
    // Crear tablas
    $pdo->exec("CREATE TABLE blog_categorias (
        id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        nombre VARCHAR(100) NOT NULL,
        slug VARCHAR(120) NOT NULL UNIQUE,
        descripcion TEXT DEFAULT NULL,
        color VARCHAR(7) DEFAULT '#0066cc',
        icono VARCHAR(50) DEFAULT 'bi-folder',
        estado ENUM('activo', 'inactivo') DEFAULT 'activo',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("CREATE TABLE blog_articulos (
        id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        titulo VARCHAR(255) NOT NULL,
        slug VARCHAR(300) NOT NULL UNIQUE,
        resumen TEXT DEFAULT NULL,
        contenido LONGTEXT NOT NULL,
        imagen_principal VARCHAR(500) DEFAULT NULL,
        imagen_og VARCHAR(500) DEFAULT NULL,
        categoria_id INT(11) UNSIGNED DEFAULT NULL,
        autor VARCHAR(100) DEFAULT 'Aramed y Laboratorios',
        autor_email VARCHAR(255) DEFAULT NULL,
        tags JSON DEFAULT NULL,
        meta_title VARCHAR(255) DEFAULT NULL,
        meta_description TEXT DEFAULT NULL,
        meta_keywords TEXT DEFAULT NULL,
        estado ENUM('borrador', 'publicado', 'archivado') DEFAULT 'borrador',
        destacado TINYINT(1) DEFAULT 0,
        vistas INT(11) UNSIGNED DEFAULT 0,
        fecha_publicacion DATETIME DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("CREATE TABLE blog_comentarios (
        id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        articulo_id INT(11) UNSIGNED NOT NULL,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL,
        comentario TEXT NOT NULL,
        estado ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente',
        ip_address VARCHAR(45) DEFAULT NULL,
        user_agent TEXT DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    echo "Tablas creadas exitosamente\n";
    
    // Insertar categorías
    $pdo->exec("INSERT INTO blog_categorias (nombre, slug, descripcion, color, icono) VALUES
        ('Simulación Médica', 'simulacion-medica', 'Artículos sobre tecnología de simulación médica', '#0066cc', 'bi-cpu'),
        ('Educación en Salud', 'educacion-salud', 'Contenido educativo para profesionales de la salud', '#28a745', 'bi-book'),
        ('Tecnología', 'tecnologia', 'Avances tecnológicos en el sector salud', '#17a2b8', 'bi-gear'),
        ('Noticias', 'noticias', 'Noticias y actualizaciones del sector', '#ffc107', 'bi-newspaper'),
        ('Casos de Éxito', 'casos-exito', 'Historias de éxito de nuestros clientes', '#6f42c1', 'bi-trophy')");
    
    echo "Categorías insertadas\n";
    
} else {
    echo "Tablas encontradas: " . implode(', ', $tables) . "\n";
}

echo "Verificación completada\n";
?>
