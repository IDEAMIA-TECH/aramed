<?php
/**
 * Script simple para instalar el blog
 */

// Cargar configuración
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/connection.php';

echo "🔧 Instalando Blog de Aramed...\n\n";

try {
    // 1. Crear tabla de categorías
    echo "1. Creando tabla de categorías...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS blog_categorias (
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
    echo "   ✅ Tabla blog_categorias creada\n";

    // 2. Crear tabla de artículos
    echo "2. Creando tabla de artículos...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS blog_articulos (
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
    echo "   ✅ Tabla blog_articulos creada\n";

    // 3. Crear tabla de comentarios
    echo "3. Creando tabla de comentarios...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS blog_comentarios (
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
    echo "   ✅ Tabla blog_comentarios creada\n";

    // 4. Insertar categorías por defecto
    echo "4. Insertando categorías por defecto...\n";
    $pdo->exec("INSERT IGNORE INTO blog_categorias (nombre, slug, descripcion, color, icono) VALUES
        ('Simulación Médica', 'simulacion-medica', 'Artículos sobre tecnología de simulación médica', '#0066cc', 'bi-cpu'),
        ('Educación en Salud', 'educacion-salud', 'Contenido educativo para profesionales de la salud', '#28a745', 'bi-book'),
        ('Tecnología', 'tecnologia', 'Avances tecnológicos en el sector salud', '#17a2b8', 'bi-gear'),
        ('Noticias', 'noticias', 'Noticias y actualizaciones del sector', '#ffc107', 'bi-newspaper'),
        ('Casos de Éxito', 'casos-exito', 'Historias de éxito de nuestros clientes', '#6f42c1', 'bi-trophy')");
    echo "   ✅ 5 categorías insertadas\n";

    // 5. Insertar artículo de ejemplo
    echo "5. Insertando artículo de ejemplo...\n";
    $pdo->exec("INSERT IGNORE INTO blog_articulos (
        titulo, slug, resumen, contenido, categoria_id, autor, autor_email, 
        tags, meta_title, meta_description, meta_keywords, estado, destacado, 
        fecha_publicacion
    ) VALUES (
        'La Importancia de la Simulación Médica en la Educación Actual',
        'importancia-simulacion-medica-educacion-actual',
        'La simulación médica ha revolucionado la forma en que los profesionales de la salud se preparan para enfrentar situaciones reales.',
        '<p>La simulación médica se ha convertido en una herramienta fundamental en la educación médica moderna...</p>',
        1,
        'Dr. María González',
        'maria.gonzalez@aramedylaboratorio.com',
        '[\"simulación médica\", \"educación\", \"maniquíes\", \"tecnología\"]',
        'Simulación Médica en Educación: Beneficios y Tecnologías',
        'Descubre cómo la simulación médica está transformando la educación en salud.',
        'simulación médica, educación médica, maniquíes, tecnología',
        'publicado',
        1,
        '2025-01-15 10:00:00'
    )");
    echo "   ✅ Artículo de ejemplo insertado\n";

    echo "\n🎉 ¡Blog instalado exitosamente!\n\n";
    echo "Puedes acceder a:\n";
    echo "- Blog público: https://aramedylaboratorio.com/NUEVO/aramed/public_html/blog.php\n";
    echo "- Panel admin: https://aramedylaboratorio.com/NUEVO/aramed/public_html/admin/blog/index.php\n\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Verifica la conexión a la base de datos.\n";
}
?>
