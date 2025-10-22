<?php
/**
 * Página de prueba para el newsletter del footer
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/includes/config.php';

// Cargar funciones
require_once INCLUDES_PATH . '/functions.php';
?>

<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba Newsletter Footer - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .test-section {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .status-success { background-color: #28a745; }
        .status-error { background-color: #dc3545; }
        .status-warning { background-color: #ffc107; }
        .status-info { background-color: #17a2b8; }
    </style>
</head>

<body>
    <div class="test-container">
        <h1 class="text-center mb-5">
            <i class="bi bi-bug-fill text-primary me-2"></i>
            Prueba del Newsletter del Footer
        </h1>
        
        <!-- Estado del Sistema -->
        <div class="test-section">
            <h3><i class="bi bi-gear-fill me-2"></i>Estado del Sistema</h3>
            
            <?php
            // Verificar archivos
            $files_to_check = [
                'includes/newsletter_simple_handler.php' => 'Handler del newsletter simple',
                'includes/footer.php' => 'Footer con formulario modificado',
                'includes/config.php' => 'Configuración de la base de datos',
                'includes/functions.php' => 'Funciones auxiliares'
            ];
            
            echo "<div class='row'>";
            foreach ($files_to_check as $file => $description) {
                $exists = file_exists(__DIR__ . '/' . $file);
                $status_class = $exists ? 'status-success' : 'status-error';
                $status_text = $exists ? 'Existe' : 'No existe';
                
                echo "<div class='col-md-6 mb-2'>";
                echo "<span class='$status_class'></span>";
                echo "<strong>$description:</strong> $status_text<br>";
                echo "<small class='text-muted'>$file</small>";
                echo "</div>";
            }
            echo "</div>";
            ?>
        </div>
        
        <!-- Prueba de Conexión a BD -->
        <div class="test-section">
            <h3><i class="bi bi-database-fill me-2"></i>Prueba de Base de Datos</h3>
            
            <?php
            try {
                $pdo = getDB();
                if ($pdo) {
                    echo "<span class='status-success'></span><strong>Conexión a BD:</strong> Exitosa<br>";
                    
                    // Verificar tabla newsletter_simple
                    $stmt = $pdo->query("SHOW TABLES LIKE 'newsletter_simple'");
                    $table_exists = $stmt->fetch();
                    
                    if ($table_exists) {
                        echo "<span class='status-success'></span><strong>Tabla newsletter_simple:</strong> Existe<br>";
                        
                        // Contar registros
                        $stmt = $pdo->query("SELECT COUNT(*) as total FROM newsletter_simple");
                        $count = $stmt->fetch(PDO::FETCH_ASSOC);
                        echo "<span class='status-info'></span><strong>Registros en newsletter_simple:</strong> " . $count['total'] . "<br>";
                        
                        // Mostrar últimos registros
                        if ($count['total'] > 0) {
                            $stmt = $pdo->query("SELECT email, source, created_at FROM newsletter_simple ORDER BY created_at DESC LIMIT 3");
                            $recent = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            echo "<br><strong>Últimos 3 registros:</strong><br>";
                            echo "<table class='table table-sm'>";
                            echo "<tr><th>Email</th><th>Fuente</th><th>Fecha</th></tr>";
                            foreach ($recent as $record) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($record['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($record['source']) . "</td>";
                                echo "<td>" . $record['created_at'] . "</td>";
                                echo "</tr>";
                            }
                            echo "</table>";
                        }
                    } else {
                        echo "<span class='status-error'></span><strong>Tabla newsletter_simple:</strong> NO existe<br>";
                        echo "<p class='text-warning'>Ejecuta: <a href='crear_tabla_boletin.php' target='_blank'>crear_tabla_boletin.php</a></p>";
                    }
                } else {
                    echo "<span class='status-error'></span><strong>Conexión a BD:</strong> Falló<br>";
                }
            } catch (Exception $e) {
                echo "<span class='status-error'></span><strong>Error de BD:</strong> " . $e->getMessage() . "<br>";
            }
            ?>
        </div>
        
        <!-- Prueba del Formulario -->
        <div class="test-section">
            <h3><i class="bi bi-form-check me-2"></i>Prueba del Formulario</h3>
            
            <div class="alert alert-info">
                <strong>Instrucciones:</strong> Usa el formulario del footer que aparece abajo para probar la suscripción al boletín.
            </div>
            
            <!-- Simular el footer -->
            <div class="bg-primary text-white p-4 rounded">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-3 mb-lg-0">
                        <h5 class="mb-1 fw-bold">¿Quieres recibir nuestras novedades?</h5>
                        <p class="mb-0 small text-white-75">Suscríbete a nuestro boletín informativo</p>
                    </div>
                    <div class="col-lg-6">
                        <!-- Mensajes de respuesta -->
                        <div id="footer-newsletter-success" class="alert alert-success d-none mb-3" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>¡Gracias!</strong> <span id="footer-newsletter-success-message"></span>
                        </div>
                        
                        <div id="footer-newsletter-error" class="alert alert-danger d-none mb-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Error:</strong> <span id="footer-newsletter-error-message"></span>
                        </div>
                        
                        <form class="newsletter-form d-flex gap-2" id="footerNewsletterForm" action="includes/newsletter_simple_handler.php" method="POST">
                            <input type="email" 
                                   class="form-control" 
                                   name="email"
                                   id="footer-newsletter-email"
                                   placeholder="Tu correo electrónico" 
                                   required 
                                   aria-label="Email">
                            <input type="hidden" name="source" value="test">
                            <button type="submit" class="btn btn-light text-primary fw-semibold px-4 flex-shrink-0" id="footer-newsletter-submit">
                                Suscribirse
                            </button>
                            <button type="button" class="btn btn-light text-primary fw-semibold px-4 flex-shrink-0 d-none" id="footer-newsletter-loading" disabled>
                                <span class="spinner-border spinner-border-sm me-2"></span>
                                Suscribiendo...
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Enlaces Útiles -->
        <div class="test-section">
            <h3><i class="bi bi-link-45deg me-2"></i>Enlaces Útiles</h3>
            
            <div class="row">
                <div class="col-md-6">
                    <h5>Scripts de Creación</h5>
                    <ul>
                        <li><a href="crear_tabla_boletin.php" target="_blank">Crear tabla newsletter_simple</a></li>
                        <li><a href="crear_tablas_correcto.php" target="_blank">Crear todas las tablas</a></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Páginas del Sitio</h5>
                    <ul>
                        <li><a href="index.php" target="_blank">Página Principal</a></li>
                        <li><a href="catalogo.php" target="_blank">Catálogo</a></li>
                        <li><a href="producto.php?id=1" target="_blank">Producto de Prueba</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript para el formulario -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const footerNewsletterForm = document.getElementById('footerNewsletterForm');
        if (footerNewsletterForm) {
            footerNewsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitBtn = document.getElementById('footer-newsletter-submit');
                const loadingBtn = document.getElementById('footer-newsletter-loading');
                const successAlert = document.getElementById('footer-newsletter-success');
                const errorAlert = document.getElementById('footer-newsletter-error');
                const successMessage = document.getElementById('footer-newsletter-success-message');
                const errorMessage = document.getElementById('footer-newsletter-error-message');
                
                // Ocultar mensajes anteriores
                successAlert.classList.add('d-none');
                errorAlert.classList.add('d-none');
                
                // Mostrar loading
                submitBtn.classList.add('d-none');
                loadingBtn.classList.remove('d-none');
                
                // Enviar petición
                fetch('includes/newsletter_simple_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        successMessage.textContent = data.message;
                        successAlert.classList.remove('d-none');
                        this.reset();
                    } else {
                        errorMessage.textContent = data.message;
                        errorAlert.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    errorMessage.textContent = 'Error de conexión. Por favor, intenta de nuevo.';
                    errorAlert.classList.remove('d-none');
                })
                .finally(() => {
                    submitBtn.classList.remove('d-none');
                    loadingBtn.classList.add('d-none');
                });
            });
        }
    });
    </script>
</body>
</html>
