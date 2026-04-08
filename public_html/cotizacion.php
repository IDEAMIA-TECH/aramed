<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Carrito de Cotización
 * ========================================
 * 
 * Página para ver el carrito y completar la solicitud de cotización
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar configuración
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/connection.php';
require_once __DIR__ . '/includes/cart_functions.php';

// Obtener productos del carrito
$cart_products = getCartProductsInfo();
$cart_count = getCartCount();

// Si el carrito está vacío, redirigir al catálogo
if (empty($cart_products)) {
    header('Location: catalogo.php');
    exit;
}

// Variables para meta tags
$pageTitle = 'Solicitar Cotización - ' . SITE_NAME;
$pageDescription = 'Completa tu solicitud de cotización para los productos seleccionados. Nuestro equipo se pondrá en contacto contigo a la brevedad.';
$pageKeywords = 'cotización, solicitar cotización, productos médicos, simuladores';
$pageUrl = SITE_URL . '/cotizacion.php';

?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <?php include INCLUDES_PATH . '/analytics.php'; ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    
    <title><?php echo esc($pageTitle); ?></title>
    <meta name="description" content="<?php echo esc($pageDescription); ?>">
    <meta name="keywords" content="<?php echo esc($pageKeywords); ?>">
    <link rel="canonical" href="<?php echo esc($pageUrl); ?>">
    
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo esc(SITE_NAME); ?>">
    <meta property="og:title" content="<?php echo esc($pageTitle); ?>">
    <meta property="og:description" content="<?php echo esc($pageDescription); ?>">
    <meta property="og:url" content="<?php echo esc($pageUrl); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo imageUrl('design/favicon.ico'); ?>">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo assetUrl('css/main.css'); ?>?v=<?php echo time(); ?>">

    <?php if (defined('RECAPTCHA_ENABLED') && RECAPTCHA_ENABLED && !empty(RECAPTCHA_SITE_KEY)): ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc(RECAPTCHA_SITE_KEY); ?>"></script>
    <?php endif; ?>
    
    <style>
        .cart-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            background: white;
        }
        .cart-item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }
        .quantity-input {
            width: 80px;
            text-align: center;
        }
    </style>
</head>
<body>
    
    <!-- Navbar -->
    <?php include INCLUDES_PATH . '/navbar.php'; ?>
    
    <!-- Breadcrumb -->
    <section class="breadcrumb-section py-3 bg-light border-bottom">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="<?php echo siteUrl(); ?>" class="text-decoration-none">
                            <i class="bi bi-house-fill me-1"></i>Inicio
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="catalogo.php" class="text-decoration-none">Catálogo</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Solicitar Cotización</li>
                </ol>
            </nav>
        </div>
    </section>
    
    <!-- Contenido Principal -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <h1 class="mb-4">
                        <i class="bi bi-cart-check me-2"></i>Solicitar Cotización
                    </h1>
                    
                    <!-- Productos en el Carrito -->
                    <div class="cart-items mb-4">
                        <h3 class="h5 mb-3">Productos Seleccionados (<?php echo $cart_count; ?>)</h3>
                        
                        <div id="cart-items-container">
                            <?php foreach ($cart_products as $product): ?>
                            <div class="cart-item" data-product-id="<?php echo $product['id']; ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <?php
                                        // Usar imagen real de la base de datos si existe
                                        if (!empty($product['imagen_url'])) {
                                            $imagen_real = $product['imagen_url'];
                                            // Convertir ruta relativa a URL completa
                                            if (strpos($imagen_real, '/assets/') === 0) {
                                                $imagen_real = SITE_URL . $imagen_real;
                                            } elseif (strpos($imagen_real, 'http://') === 0 || strpos($imagen_real, 'https://') === 0) {
                                                // Ya es una URL completa
                                                // No hacer nada
                                            } else {
                                                // Usar imageUrl para rutas relativas
                                                $imagen_real = imageUrl($imagen_real);
                                            }
                                            echo '<img src="' . esc($imagen_real) . '" 
                                                     alt="' . esc($product['nombre']) . '" 
                                                     class="cart-item-image"
                                                     loading="lazy"
                                                     onerror="this.src=\'' . imageUrl('design/placeholder-product.jpg') . '\'">';
                                        } else {
                                            // Fallback: usar imagen placeholder
                                            echo '<img src="' . imageUrl('design/placeholder-product.jpg') . '" 
                                                     alt="Producto" 
                                                     class="cart-item-image"
                                                     loading="lazy">';
                                        }
                                        ?>
                                    </div>
                                    <div class="col-md-6">
                                        <h5 class="mb-1"><?php echo esc($product['nombre']); ?></h5>
                                        <p class="text-muted mb-1 small">
                                            <strong>Marca:</strong> <?php echo esc($product['marca_nombre'] ?? 'N/A'); ?><br>
                                            <strong>Código:</strong> <?php echo esc($product['codigo'] ?? 'N/A'); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">Cantidad</label>
                                        <input type="number" 
                                               class="form-control quantity-input" 
                                               value="<?php echo $product['cantidad']; ?>" 
                                               min="1" 
                                               data-product-id="<?php echo $product['id']; ?>">
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button class="btn btn-sm btn-outline-danger remove-item" 
                                                data-product-id="<?php echo $product['id']; ?>"
                                                title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="text-end mb-4">
                            <a href="catalogo.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Seguir Agregando Productos
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Formulario de Cotización -->
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-file-text me-2"></i>Información de Contacto
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="quote-form" method="POST"<?php if (defined('RECAPTCHA_ENABLED') && RECAPTCHA_ENABLED && !empty(RECAPTCHA_SITE_KEY)): ?> data-recaptcha-site-key="<?php echo esc(RECAPTCHA_SITE_KEY); ?>"<?php endif; ?>>
                                <input type="text" name="website_url" id="quote_website_url" value="" tabindex="-1" autocomplete="off" class="position-absolute" style="left:-9999px;width:1px;height:1px;opacity:0;" aria-hidden="true">
                                <input type="hidden" name="form_timestamp" id="quote_form_timestamp" value="<?php echo (int) time(); ?>">
                                
                                <!-- Institución -->
                                <div class="mb-3">
                                    <label class="form-label">Institución <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="institucion" required>
                                </div>
                                
                                <!-- Tipo de Institución -->
                                <div class="mb-3">
                                    <label class="form-label">Tipo de Institución <span class="text-danger">*</span></label>
                                    <select class="form-select" name="tipo_institucion" required>
                                        <option value="">Seleccione...</option>
                                        <option value="Hospital">Hospital</option>
                                        <option value="Universidad">Universidad</option>
                                        <option value="Instituto">Instituto</option>
                                        <option value="Clínica">Clínica</option>
                                        <option value="Centro de Salud">Centro de Salud</option>
                                        <option value="Gobierno">Gobierno</option>
                                        <option value="Privado">Privado</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                                
                                <!-- Estado -->
                                <div class="mb-3">
                                    <label class="form-label">Estado <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="estado" required>
                                </div>
                                
                                <!-- Ciudad -->
                                <div class="mb-3">
                                    <label class="form-label">Ciudad <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="ciudad" required>
                                </div>
                                
                                <!-- Nombre -->
                                <div class="mb-3">
                                    <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre" required>
                                </div>
                                
                                <!-- Puesto -->
                                <div class="mb-3">
                                    <label class="form-label">Puesto <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="puesto" required>
                                </div>
                                
                                <!-- Email Oficial -->
                                <div class="mb-3">
                                    <label class="form-label">Correo Oficial <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email_oficial" required>
                                </div>
                                
                                <!-- Email Alterno -->
                                <div class="mb-3">
                                    <label class="form-label">Correo Alterno</label>
                                    <input type="email" class="form-control" name="email_alterno">
                                </div>
                                
                                <!-- Teléfono Oficina -->
                                <div class="mb-3">
                                    <label class="form-label">Teléfono de Oficina <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="telefono_oficina" required>
                                </div>
                                
                                <!-- Extensión -->
                                <div class="mb-3">
                                    <label class="form-label">Extensión</label>
                                    <input type="text" class="form-control" name="extension">
                                </div>
                                
                                <!-- Teléfono Celular -->
                                <div class="mb-3">
                                    <label class="form-label">Teléfono Celular</label>
                                    <input type="tel" class="form-control" name="telefono_celular">
                                </div>
                                
                                <!-- Fecha Aproximada de Compra -->
                                <div class="mb-3">
                                    <label class="form-label">Fecha Aproximada de Compra</label>
                                    <input type="date" class="form-control" name="fecha_compra_aprox">
                                </div>
                                
                                <!-- Presupuesto Estimado -->
                                <div class="mb-3">
                                    <label class="form-label">Presupuesto Estimado (MXN)</label>
                                    <input type="number" class="form-control" name="presupuesto_estimado" step="0.01" min="0">
                                </div>
                                
                                <!-- Observaciones -->
                                <div class="mb-3">
                                    <label class="form-label">Observaciones o Comentarios</label>
                                    <textarea class="form-control" name="observaciones" rows="4"></textarea>
                                </div>
                                
                                <!-- Privacidad -->
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" name="privacidad" id="privacidad" required>
                                    <label class="form-check-label" for="privacidad">
                                        Acepto la <a href="privacidad.php" target="_blank">Política de Privacidad</a> <span class="text-danger">*</span>
                                    </label>
                                </div>
                                
                                <!-- Botón Enviar -->
                                <button type="submit" class="btn btn-primary w-100 btn-lg">
                                    <i class="bi bi-send me-2"></i>Enviar Solicitud de Cotización
                                </button>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <?php include INCLUDES_PATH . '/footer.php'; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Actualizar cantidad
        document.querySelectorAll('.quantity-input').forEach(function(input) {
            input.addEventListener('change', function() {
                const productId = this.dataset.productId;
                const cantidad = parseInt(this.value) || 1;
                
                fetch('includes/cart_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'update',
                        producto_id: productId,
                        cantidad: cantidad
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Recargar página para actualizar
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            });
        });
        
        // Remover producto
        document.querySelectorAll('.remove-item').forEach(function(btn) {
            btn.addEventListener('click', function() {
                if (!confirm('¿Deseas eliminar este producto del carrito?')) {
                    return;
                }
                
                const productId = this.dataset.productId;
                
                fetch('includes/cart_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'remove',
                        producto_id: productId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remover elemento del DOM
                        this.closest('.cart-item').remove();
                        
                        // Si no hay más items, redirigir
                        if (document.querySelectorAll('.cart-item').length === 0) {
                            window.location.href = 'catalogo.php';
                        }
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            });
        });
        
        (function() {
            var quoteTs = document.getElementById('quote_form_timestamp');
            if (quoteTs) {
                quoteTs.value = Math.floor(Date.now() / 1000);
            }
        })();

        document.getElementById('quote-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalHTML = submitBtn.innerHTML;
            const siteKey = form.getAttribute('data-recaptcha-site-key');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Enviando...';

            function postQuote(formData) {
                return fetch('includes/quote_handler.php', {
                    method: 'POST',
                    body: formData
                }).then(function(response) { return response.json(); });
            }

            var formData = new FormData(form);

            function onResult(data) {
                if (data.success) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.href = 'cotizacion-enviada.php?folio=' + encodeURIComponent(data.folio || '');
                    }
                } else {
                    alert('Error: ' + data.message);
                    submitBtn.innerHTML = originalHTML;
                    submitBtn.disabled = false;
                }
            }

            function onFail() {
                alert('Error al enviar la cotización. Por favor, intenta nuevamente.');
                submitBtn.innerHTML = originalHTML;
                submitBtn.disabled = false;
            }

            if (siteKey && typeof grecaptcha !== 'undefined') {
                grecaptcha.ready(function() {
                    grecaptcha.execute(siteKey, { action: 'quote_cart' }).then(function(token) {
                        if (typeof formData.set === 'function') {
                            formData.set('g-recaptcha-response', token);
                        } else {
                            formData.append('g-recaptcha-response', token);
                        }
                        return postQuote(formData);
                    }).then(onResult).catch(function(err) {
                        console.error(err);
                        onFail();
                    });
                });
            } else {
                postQuote(formData).then(onResult).catch(function(err) {
                    console.error(err);
                    onFail();
                });
            }
        });
    </script>
</body>
</html>

