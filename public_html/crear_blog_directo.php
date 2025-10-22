<?php
// Script directo para crear tablas del blog
$host = '173.231.22.109';
$dbname = 'aramed2025_produccion';
$username = 'aramed2025_prod';
$password = 'pmDLi&PB$zntrzJ4';

echo "🔧 Conectando a la base de datos...\n";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexión exitosa\n\n";

    // 1. Crear tabla de categorías
    echo "1. Creando tabla blog_categorias...\n";
    $sql = "CREATE TABLE IF NOT EXISTS blog_categorias (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "   ✅ Tabla blog_categorias creada\n";

    // 2. Crear tabla de artículos
    echo "2. Creando tabla blog_articulos...\n";
    $sql = "CREATE TABLE IF NOT EXISTS blog_articulos (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "   ✅ Tabla blog_articulos creada\n";

    // 3. Crear tabla de comentarios
    echo "3. Creando tabla blog_comentarios...\n";
    $sql = "CREATE TABLE IF NOT EXISTS blog_comentarios (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "   ✅ Tabla blog_comentarios creada\n";

    // 4. Insertar categorías
    echo "4. Insertando categorías...\n";
    $sql = "INSERT IGNORE INTO blog_categorias (nombre, slug, descripcion, color, icono) VALUES
        ('Simulación Médica', 'simulacion-medica', 'Artículos sobre tecnología de simulación médica', '#0066cc', 'bi-cpu'),
        ('Educación en Salud', 'educacion-salud', 'Contenido educativo para profesionales de la salud', '#28a745', 'bi-book'),
        ('Tecnología', 'tecnologia', 'Avances tecnológicos en el sector salud', '#17a2b8', 'bi-gear'),
        ('Noticias', 'noticias', 'Noticias y actualizaciones del sector', '#ffc107', 'bi-newspaper'),
        ('Casos de Éxito', 'casos-exito', 'Historias de éxito de nuestros clientes', '#6f42c1', 'bi-trophy')";
    
    $pdo->exec($sql);
    echo "   ✅ 5 categorías insertadas\n";

    // 5. Insertar artículo de ejemplo
    echo "5. Insertando artículo de ejemplo...\n";
    $sql = "INSERT IGNORE INTO blog_articulos (
        titulo, slug, resumen, contenido, categoria_id, autor, autor_email, 
        tags, meta_title, meta_description, meta_keywords, estado, destacado, 
        fecha_publicacion
    ) VALUES (
        'La Importancia de la Simulación Médica en la Educación Actual',
        'importancia-simulacion-medica-educacion-actual',
        'La simulación médica ha revolucionado la forma en que los profesionales de la salud se preparan para enfrentar situaciones reales.',
        '<p>La simulación médica se ha convertido en una herramienta fundamental en la educación médica moderna. A través de maniquíes de alta fidelidad, simuladores virtuales y entornos de realidad aumentada, los estudiantes pueden practicar procedimientos complejos sin poner en riesgo a pacientes reales.</p><p><strong>Beneficios principales:</strong></p><ul><li>Seguridad del paciente</li><li>Repetición de procedimientos</li><li>Escenarios controlados</li><li>Evaluación objetiva</li></ul>',
        1,
        'Dr. María González',
        'maria.gonzalez@aramedylaboratorio.com',
        '[\"simulación médica\", \"educación\", \"maniquíes\"]',
        'Simulación Médica en Educación: Beneficios y Tecnologías',
        'Descubre cómo la simulación médica está transformando la educación en salud.',
        'simulación médica, educación médica, maniquíes, tecnología',
        'publicado',
        1,
        '2025-01-15 10:00:00'
    )";
    
    $pdo->exec($sql);
    echo "   ✅ Artículo de ejemplo insertado\n";

    echo "\n🎉 ¡Blog instalado exitosamente!\n";
    echo "Puedes acceder a:\n";
    echo "- Blog: https://aramedylaboratorio.com/NUEVO/aramed/public_html/blog.php\n";
    echo "- Admin: https://aramedylaboratorio.com/NUEVO/aramed/public_html/admin/blog/index.php\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Código de error: " . $e->getCode() . "\n";
}
?>
