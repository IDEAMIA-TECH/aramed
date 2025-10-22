<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Tablas del Blog - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">
                            <i class="bi bi-database me-2"></i>Creación de Tablas del Blog
                        </h2>
                    </div>
                    <div class="card-body">
                        <?php
                        // Cargar configuración
                        require_once __DIR__ . '/includes/config.php';
                        require_once __DIR__ . '/includes/connection.php';

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
                            echo '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Tabla "blog_categorias" creada correctamente</div>';

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
                            echo '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Tabla "blog_articulos" creada correctamente</div>';

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
                            echo '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Tabla "blog_comentarios" creada correctamente</div>';

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
                            echo '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Categorías por defecto insertadas</div>';

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
                                ],
                                [
                                    'Tecnologías Emergentes en Simulación de Pacientes',
                                    'tecnologias-emergentes-simulacion-pacientes',
                                    'Exploramos las últimas innovaciones en simulación de pacientes, desde realidad virtual hasta inteligencia artificial aplicada a la educación médica.',
                                    '<p>El mundo de la simulación médica está experimentando una revolución tecnológica sin precedentes. Las nuevas tecnologías están permitiendo crear experiencias de aprendizaje más inmersivas y realistas que nunca antes.</p>

<p><strong>Principales tecnologías emergentes:</strong></p>

<h3>1. Realidad Virtual (VR)</h3>
<p>La realidad virtual permite a los estudiantes sumergirse en entornos médicos completamente virtuales, desde quirófanos hasta salas de emergencia. Esta tecnología es especialmente valiosa para entrenar en procedimientos de alta complejidad.</p>

<h3>2. Realidad Aumentada (AR)</h3>
<p>La realidad aumentada superpone información digital sobre el mundo real, permitiendo a los estudiantes ver estructuras anatómicas en tiempo real o recibir guías paso a paso durante procedimientos.</p>

<h3>3. Inteligencia Artificial</h3>
<p>Los sistemas de IA pueden adaptar los escenarios de simulación en tiempo real, respondiendo a las acciones del estudiante y creando experiencias de aprendizaje personalizadas.</p>

<h3>4. Simuladores Híbridos</h3>
<p>La combinación de maniquíes físicos con tecnología digital está creando simuladores híbridos que ofrecen lo mejor de ambos mundos: la sensación táctil real y la flexibilidad de la simulación digital.</p>

<p>En Aramed y Laboratorios, estamos constantemente evaluando e integrando estas nuevas tecnologías para ofrecer a nuestros clientes las soluciones más avanzadas del mercado.</p>',
                                    '/assets/images/blog/tecnologias-emergentes-1.jpg',
                                    '/assets/images/blog/tecnologias-emergentes-1-og.jpg',
                                    3,
                                    'Ing. Carlos Mendoza',
                                    'carlos.mendoza@aramedylaboratorio.com',
                                    '["realidad virtual", "inteligencia artificial", "tecnología", "innovación"]',
                                    'Tecnologías Emergentes en Simulación Médica 2025',
                                    'Descubre las últimas innovaciones en simulación de pacientes: VR, AR, IA y simuladores híbridos que están revolucionando la educación médica.',
                                    'realidad virtual, realidad aumentada, inteligencia artificial, simuladores híbridos, innovación médica',
                                    'publicado',
                                    1,
                                    0,
                                    '2025-01-10 14:30:00'
                                ],
                                [
                                    'Casos de Éxito: Universidad Implementa Simuladores Anatomage',
                                    'casos-exito-universidad-anatomage',
                                    'Conoce cómo la Universidad de Ciencias Médicas de Guadalajara implementó con éxito los simuladores Anatomage Table, mejorando significativamente el aprendizaje de anatomía.',
                                    '<p>La Universidad de Ciencias Médicas de Guadalajara ha sido pionera en la implementación de tecnología de simulación avanzada en sus programas de medicina. Su experiencia con los simuladores Anatomage Table demuestra el impacto transformador que puede tener la tecnología adecuada en la educación médica.</p>

<p><strong>Desafío inicial:</strong></p>
<p>La universidad enfrentaba el reto de enseñar anatomía compleja a más de 500 estudiantes de medicina cada semestre, con recursos limitados de cadáveres y modelos anatómicos tradicionales.</p>

<p><strong>Solución implementada:</strong></p>
<p>Se instalaron 4 estaciones Anatomage Table en el laboratorio de anatomía, permitiendo a los estudiantes explorar la anatomía humana en 3D de manera interactiva y detallada.</p>

<p><strong>Resultados obtenidos:</strong></p>
<ul>
<li><strong>Mejora del 40% en calificaciones:</strong> Los estudiantes mostraron una mejora significativa en sus exámenes de anatomía.</li>
<li><strong>Mayor participación:</strong> El 95% de los estudiantes reportó mayor interés y participación en las clases de anatomía.</li>
<li><strong>Eficiencia docente:</strong> Los profesores pudieron explicar conceptos complejos de manera más clara y visual.</li>
<li><strong>Acceso 24/7:</strong> Los estudiantes pueden practicar fuera del horario de clases.</li>
</ul>

<p><strong>Testimonio del Dr. Roberto Silva, Director del Departamento de Anatomía:</strong></p>
<blockquote>"Los simuladores Anatomage han revolucionado completamente nuestra forma de enseñar anatomía. Los estudiantes ahora pueden ver y manipular estructuras anatómicas en 3D, lo que les permite comprender mejor las relaciones espaciales y la complejidad del cuerpo humano."</blockquote>

<p>Este caso de éxito demuestra cómo la inversión en tecnología de simulación puede transformar la educación médica y mejorar significativamente los resultados de aprendizaje.</p>',
                                    '/assets/images/blog/caso-exito-anatomage-1.jpg',
                                    '/assets/images/blog/caso-exito-anatomage-1-og.jpg',
                                    5,
                                    'Aramed y Laboratorios',
                                    'marketing@aramedylaboratorio.com',
                                    '["casos de éxito", "Anatomage", "universidad", "anatomía", "educación"]',
                                    'Caso de Éxito: Universidad Implementa Anatomage Table',
                                    'Conoce cómo la Universidad de Ciencias Médicas implementó Anatomage Table, mejorando 40% las calificaciones de anatomía de sus estudiantes.',
                                    'Anatomage Table, caso de éxito, universidad, anatomía, educación médica, simulación 3D',
                                    'publicado',
                                    1,
                                    0,
                                    '2025-01-05 09:15:00'
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
                            echo '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Artículos de ejemplo insertados</div>';

                            echo '<div class="alert alert-success">
                                    <h4><i class="bi bi-check-circle-fill me-2"></i>¡Tablas del blog creadas exitosamente!</h4>
                                    <p>Se han creado las siguientes tablas:</p>
                                    <ul>
                                        <li><strong>blog_categorias:</strong> Categorías de artículos</li>
                                        <li><strong>blog_articulos:</strong> Artículos del blog</li>
                                        <li><strong>blog_comentarios:</strong> Comentarios de los artículos</li>
                                    </ul>
                                    <p>También se insertaron 5 categorías por defecto y 3 artículos de ejemplo.</p>
                                    <div class="mt-3">
                                        <a href="blog.php" class="btn btn-primary me-2">
                                            <i class="bi bi-newspaper me-2"></i>Ver Blog
                                        </a>
                                        <a href="admin/blog/index.php" class="btn btn-outline-primary">
                                            <i class="bi bi-gear me-2"></i>Panel Admin
                                        </a>
                                    </div>
                                  </div>';

                        } catch (PDOException $e) {
                            echo '<div class="alert alert-danger">
                                    <h4><i class="bi bi-exclamation-triangle me-2"></i>Error de conexión</h4>
                                    <p><strong>Error:</strong> ' . $e->getMessage() . '</p>
                                    <p><strong>Configuración intentada:</strong></p>
                                    <ul>
                                        <li>Host: ' . DB_HOST . '</li>
                                        <li>Base de datos: ' . DB_NAME . '</li>
                                        <li>Usuario: ' . DB_USER . '</li>
                                    </ul>
                                    <p><strong>Verifica que:</strong></p>
                                    <ul>
                                        <li>La base de datos existe</li>
                                        <li>Las credenciales son correctas</li>
                                        <li>El usuario tiene permisos para crear tablas</li>
                                    </ul>
                                  </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>