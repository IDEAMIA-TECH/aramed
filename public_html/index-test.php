<?php
/**
 * Index de prueba minimalista
 */

// Inicializar sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once 'includes/config.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-success" role="alert">
            <h1>✅ PHP Funciona Correctamente</h1>
            <hr>
            <p><strong>Sitio:</strong> <?php echo SITE_NAME; ?></p>
            <p><strong>Año:</strong> <?php echo getCurrentYear(); ?></p>
            <p><strong>URL:</strong> <?php echo SITE_URL; ?></p>
            <hr>
            <h3>Próximo Paso:</h3>
            <p>Si ves este mensaje, el problema está en el contenido de <code>index.php</code> original.</p>
            <p>El archivo es muy grande (137K) y puede estar causando:</p>
            <ul>
                <li>Timeout de ejecución</li>
                <li>Límite de memoria</li>
                <li>Algún error de sintaxis en el HTML</li>
            </ul>
        </div>

        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h3>Test del Formulario</h3>
            </div>
            <div class="card-body">
                <div id="newsletter-success" class="alert alert-success d-none"></div>
                <div id="newsletter-error" class="alert alert-danger d-none"></div>
                
                <form id="newsletterForm" action="includes/newsletter_handler.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Institución *</label>
                        <input type="text" name="institucion" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tipo de Institución *</label>
                        <select name="tipo_institucion" class="form-select" required>
                            <option value="">Selecciona...</option>
                            <option value="Universidad">Universidad</option>
                            <option value="Hospital">Hospital</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Estado *</label>
                        <input type="text" name="estado" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ciudad *</label>
                        <input type="text" name="ciudad" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre Completo *</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Puesto *</label>
                        <input type="text" name="puesto" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email Oficial *</label>
                        <input type="email" name="email_oficial" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Teléfono de Oficina *</label>
                        <input type="tel" name="telefono_oficina" class="form-control" required>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input type="checkbox" name="privacidad" class="form-check-input" id="privacidad" required>
                        <label class="form-check-label" for="privacidad">
                            Acepto la política de privacidad *
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg">Enviar</button>
                </form>
                
                <hr class="my-4">
                
                <a href="view-debug-log.php" class="btn btn-secondary" target="_blank">
                    Ver Logs en Tiempo Real
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('newsletterForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const successDiv = document.getElementById('newsletter-success');
        const errorDiv = document.getElementById('newsletter-error');
        
        successDiv.classList.add('d-none');
        errorDiv.classList.add('d-none');
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                successDiv.textContent = data.message;
                successDiv.classList.remove('d-none');
                form.reset();
            } else {
                errorDiv.textContent = data.message;
                errorDiv.classList.remove('d-none');
            }
        } catch (error) {
            errorDiv.textContent = 'Error: ' + error.message;
            errorDiv.classList.remove('d-none');
        }
    });
    </script>
</body>
</html>

