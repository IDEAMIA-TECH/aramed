<?php
/**
 * Script simple para crear tablas del blog
 */

// Cargar configuración
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/connection.php';

echo "🔧 Creando tablas del blog...\n";

try {
    // 1. Crear tabla de categorías del blog
    $sql_categorias = "
    CREATE TABLE IF NOT EXISTS `blog_categorias` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `nombre` VARCHAR(100) NOT NULL,
      `slug` VARCHAR(120) NOT NULL UNIQUE,
      `descripcion` TEXT DEFAULT NULL,
      `color` VARCHAR(7) DEFAULT '#0066cc',
      `icono` VARCHAR(50) DEFAULT 'bi-folder',
      `estado` ENUM('activo', 'inactivo') DEFAULT 'activo',
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_slug` (`slug`),
      KEY `idx_estado` (`estado`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $pdo->exec($sql_categorias);
    echo "✅ Tabla 'blog_categorias' creada correctamente\n";

    // 2. Crear tabla de artículos del blog
    $sql_articulos = "
    CREATE TABLE IF NOT EXISTS `blog_articulos` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `titulo` VARCHAR(255) NOT NULL,
      `slug` VARCHAR(300) NOT NULL UNIQUE,
      `resumen` TEXT DEFAULT NULL,
      `contenido` LONGTEXT NOT NULL,
      `imagen_principal` VARCHAR(500) DEFAULT NULL,
      `imagen_og` VARCHAR(500) DEFAULT NULL,
      `categoria_id` INT(11) UNSIGNED DEFAULT NULL,
      `autor` VARCHAR(100) DEFAULT 'Aramed y Laboratorios',
      `autor_email` VARCHAR(255) DEFAULT NULL,
      `tags` JSON DEFAULT NULL,
      `meta_title` VARCHAR(255) DEFAULT NULL,
      `meta_description` TEXT DEFAULT NULL,
      `meta_keywords` TEXT DEFAULT NULL,
      `estado` ENUM('borrador', 'publicado', 'archivado') DEFAULT 'borrador',
      `destacado` TINYINT(1) DEFAULT 0,
      `vistas` INT(11) UNSIGNED DEFAULT 0,
      `fecha_publicacion` DATETIME DEFAULT NULL,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_slug` (`slug`),
      KEY `idx_estado` (`estado`),
      KEY `idx_destacado` (`destacado`),
      KEY `idx_fecha_publicacion` (`fecha_publicacion`),
      KEY `idx_categoria` (`categoria_id`),
      FULLTEXT KEY `idx_busqueda` (`titulo`, `resumen`, `contenido`),
      FOREIGN KEY (`categoria_id`) REFERENCES `blog_categorias`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $pdo->exec($sql_articulos);
    echo "✅ Tabla 'blog_articulos' creada correctamente\n";

    // 3. Crear tabla de comentarios del blog
    $sql_comentarios = "
    CREATE TABLE IF NOT EXISTS `blog_comentarios` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `articulo_id` INT(11) UNSIGNED NOT NULL,
      `nombre` VARCHAR(100) NOT NULL,
      `email` VARCHAR(255) NOT NULL,
      `comentario` TEXT NOT NULL,
      `estado` ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente',
      `ip_address` VARCHAR(45) DEFAULT NULL,
      `user_agent` TEXT DEFAULT NULL,
      `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_articulo` (`articulo_id`),
      KEY `idx_estado` (`estado`),
      KEY `idx_created_at` (`created_at`),
      FOREIGN KEY (`articulo_id`) REFERENCES `blog_articulos`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $pdo->exec($sql_comentarios);
    echo "✅ Tabla 'blog_comentarios' creada correctamente\n";

    // 4. Insertar categorías por defecto
    $categorias_default = [
        ['Simulación Médica', 'simulacion-medica', 'Artículos sobre tecnología de simulación médica', '#0066cc', 'bi-cpu'],
        ['Educación en Salud', 'educacion-salud', 'Contenido educativo para profesionales de la salud', '#28a745', 'bi-book'],
        ['Tecnología', 'tecnologia', 'Avances tecnológicos en el sector salud', '#17a2b8', 'bi-gear'],
        ['Noticias', 'noticias', 'Noticias y actualizaciones del sector', '#ffc107', 'bi-newspaper'],
        ['Casos de Éxito', 'casos-exito', 'Historias de éxito de nuestros clientes', '#6f42c1', 'bi-trophy']
    ];

    $stmt_categoria = $pdo->prepare("
        INSERT IGNORE INTO blog_categorias (nombre, slug, descripcion, color, icono, estado) 
        VALUES (?, ?, ?, ?, ?, 'activo')
    ");

    foreach ($categorias_default as $cat) {
        $stmt_categoria->execute($cat);
    }
    echo "✅ Categorías por defecto insertadas\n";

    // 5. Insertar artículos de ejemplo
    $articulos_ejemplo = [
        [
            'La Importancia de la Simulación Médica en la Educación Actual',
            'importancia-simulacion-medica-educacion-actual',
            'La simulación médica ha revolucionado la forma en que los profesionales de la salud se preparan para enfrentar situaciones reales. Descubre por qué es fundamental en la educación médica moderna.',
            '<p>La simulación médica se ha convertido en una herramienta fundamental en la educación médica moderna. A través de maniquíes de alta fidelidad, simuladores virtuales y entornos de realidad aumentada, los estudiantes pueden practicar procedimientos complejos sin poner en riesgo a pacientes reales.</p>

<p><strong>Beneficios principales de la simulación médica:</strong></p>
<ul>
<li><strong>Seguridad del paciente:</strong> Los estudiantes pueden cometer errores y aprender de ellos sin consecuencias reales.</li>
<li><strong>Repetición:</strong> Permite practicar el mismo procedimiento múltiples veces hasta dominarlo.</li>
<li><strong>Escenarios controlados:</strong> Se pueden crear situaciones específicas y raras para entrenar respuestas apropiadas.</li>
<li><strong>Evaluación objetiva:</strong> Los instructores pueden evaluar el desempeño de manera sistemática.</li>
</ul>

<p>En Aramed y Laboratorios, ofrecemos una amplia gama de simuladores médicos que van desde maniquíes básicos hasta sistemas de simulación de última generación como el Anatomage Table y los simuladores de Gaumard Scientific.</p>

<p>La inversión en tecnología de simulación no solo mejora la calidad de la educación médica, sino que también contribuye a la seguridad del paciente y al desarrollo de profesionales más competentes.</p>',
            '/assets/images/blog/simulacion-medica-1.jpg',
            '/assets/images/blog/simulacion-medica-1-og.jpg',
            1,
            'Dr. María González',
            'maria.gonzalez@aramedylaboratorio.com',
            '["simulación médica", "educación", "maniquíes", "tecnología"]',
            'Simulación Médica en Educación: Beneficios y Tecnologías',
            'Descubre cómo la simulación médica está transformando la educación en salud. Beneficios, tecnologías y casos de éxito en instituciones educativas.',
            'simulación médica, educación médica, maniquíes, tecnología, Anatomage, Gaumard',
            'publicado',
            1,
            0,
            '2025-01-15 10:00:00'
        ]
    ];

    $stmt_articulo = $pdo->prepare("
        INSERT IGNORE INTO blog_articulos (
            titulo, slug, resumen, contenido, imagen_principal, imagen_og,
            categoria_id, autor, autor_email, tags, meta_title, meta_description,
            meta_keywords, estado, destacado, vistas, fecha_publicacion
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($articulos_ejemplo as $art) {
        $stmt_articulo->execute($art);
    }
    echo "✅ Artículos de ejemplo insertados\n";

    echo "\n🎉 ¡Tablas del blog creadas exitosamente!\n";
    echo "Se han creado las siguientes tablas:\n";
    echo "- blog_categorias: Categorías de artículos\n";
    echo "- blog_articulos: Artículos del blog\n";
    echo "- blog_comentarios: Comentarios de los artículos\n";
    echo "\nTambién se insertaron 5 categorías por defecto y 1 artículo de ejemplo.\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Configuración intentada:\n";
    echo "Host: " . DB_HOST . "\n";
    echo "Base de datos: " . DB_NAME . "\n";
    echo "Usuario: " . DB_USER . "\n";
}
?>
