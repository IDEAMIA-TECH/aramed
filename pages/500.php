<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Error del Servidor - ARAmed</title>
  <meta name="description" content="Error interno del servidor">
  
  <!-- UIkit CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.17.11/dist/css/uikit.min.css" />
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- CSS Moderno -->
  <link rel="stylesheet" href="css/modern-styles.css">
  
  <style>
    .error-hero {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 120px 0 80px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    
    .error-hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.1)" points="0,1000 1000,0 1000,1000"/></svg>');
      background-size: cover;
    }
    
    .error-content {
      position: relative;
      z-index: 2;
    }
    
    .error-number {
      font-size: 8rem;
      font-weight: 700;
      margin-bottom: 1rem;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
      opacity: 0.8;
    }
    
    .error-title {
      font-size: 2.5rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }
    
    .error-message {
      font-size: 1.2rem;
      opacity: 0.9;
      margin-bottom: 2rem;
    }
    
    .error-actions {
      margin-top: 3rem;
    }
    
    .error-actions .btn-modern {
      margin: 0 10px;
    }
    
    .error-illustration {
      max-width: 400px;
      margin: 2rem auto;
      opacity: 0.7;
    }
    
    .status-info {
      background: rgba(255,255,255,0.1);
      border-radius: 10px;
      padding: 20px;
      margin: 20px 0;
      backdrop-filter: blur(10px);
    }
    
    @media (max-width: 768px) {
      .error-number {
        font-size: 6rem;
      }
      
      .error-title {
        font-size: 2rem;
      }
      
      .error-message {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  
  <!-- Header simplificado -->
  <nav class="uk-navbar-container" uk-navbar>
    <div class="uk-navbar-left">
      <ul class="uk-navbar-nav">
        <li><a href="index.php">ARAMed</a></li>
      </ul>
    </div>
  </nav>

  <!-- Error Hero Section -->
  <section class="error-hero">
    <div class="uk-container">
      <div class="error-content">
        <div class="error-number">500</div>
        <h1 class="error-title">Error del Servidor</h1>
        <p class="error-message">
          Lo sentimos, ha ocurrido un error interno en nuestro servidor.
        </p>
        
        <div class="error-illustration">
          <i class="fas fa-tools" style="font-size: 8rem; opacity: 0.3;"></i>
        </div>
        
        <div class="status-info">
          <p><i class="fas fa-info-circle"></i> Nuestro equipo técnico ha sido notificado y está trabajando para solucionar el problema.</p>
        </div>
        
        <div class="error-actions">
          <a href="index.php" class="btn-modern">
            <i class="fas fa-home" style="margin-right: 10px;"></i>Ir al Inicio
          </a>
          
          <a href="contacto.php" class="btn-modern btn-accent">
            <i class="fas fa-envelope" style="margin-right: 10px;"></i>Reportar Problema
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Sección de ayuda -->
  <section class="section-modern bg-light">
    <div class="uk-container">
      <h2 class="section-title">¿Qué puedes hacer?</h2>
      
      <div uk-grid class="uk-child-width-1-3@m uk-child-width-1-2@s uk-child-width-1-1">
        <div>
          <div class="card-modern text-center">
            <div class="icon-modern">
              <i class="fas fa-redo"></i>
            </div>
            <h3>Recargar la Página</h3>
            <p>Intenta recargar la página presionando F5 o Ctrl+R en tu navegador.</p>
            <button onclick="location.reload()" class="btn-modern">Recargar</button>
          </div>
        </div>
        
        <div>
          <div class="card-modern text-center">
            <div class="icon-modern">
              <i class="fas fa-clock"></i>
            </div>
            <h3>Esperar un Momento</h3>
            <p>El problema puede ser temporal. Intenta acceder nuevamente en unos minutos.</p>
            <a href="index.php" class="btn-modern">Intentar de Nuevo</a>
          </div>
        </div>
        
        <div>
          <div class="card-modern text-center">
            <div class="icon-modern">
              <i class="fas fa-phone"></i>
            </div>
            <h3>Contactar Soporte</h3>
            <p>Si el problema persiste, contacta a nuestro equipo de soporte técnico.</p>
            <a href="contacto.php" class="btn-modern">Contactar</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Información adicional -->
  <section class="section-modern">
    <div class="uk-container">
      <div class="card-modern">
        <h3 class="text-center mb-4">Información Técnica</h3>
        <div uk-grid class="uk-child-width-1-2@s">
          <div>
            <h4><i class="fas fa-exclamation-triangle"></i> Error 500</h4>
            <p>Este error indica un problema interno del servidor. Puede ser causado por:</p>
            <ul>
              <li>Problemas temporales del servidor</li>
              <li>Errores en la configuración</li>
              <li>Problemas de base de datos</li>
              <li>Errores en el código de la aplicación</li>
            </ul>
          </div>
          <div>
            <h4><i class="fas fa-lightbulb"></i> Soluciones</h4>
            <p>Nuestro equipo está trabajando para:</p>
            <ul>
              <li>Identificar y corregir el problema</li>
              <li>Restaurar el servicio lo antes posible</li>
              <li>Prevenir futuros errores similares</li>
              <li>Mantener informados a nuestros usuarios</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer simplificado -->
  <footer class="uk-section uk-section-secondary uk-light">
    <div class="uk-container">
      <div class="uk-text-center">
        <p>&copy; 2024 ARAmed. Todos los derechos reservados.</p>
        <p><small>Si este problema persiste, contacta a soporte@aramed.com</small></p>
      </div>
    </div>
  </footer>

  <!-- UIkit JS -->
  <script src="https://cdn.jsdelivr.net/npm/uikit@3.17.11/dist/js/uikit.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/uikit@3.17.11/dist/js/uikit-icons.min.js"></script>

</body>
</html> 