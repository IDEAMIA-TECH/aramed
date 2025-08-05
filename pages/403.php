<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Acceso Denegado - ARAmed</title>
  <meta name="description" content="No tienes permisos para acceder a esta página">
  
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
        <div class="error-number">403</div>
        <h1 class="error-title">Acceso Denegado</h1>
        <p class="error-message">
          Lo sentimos, no tienes permisos para acceder a esta página.
        </p>
        
        <div class="error-illustration">
          <i class="fas fa-lock" style="font-size: 8rem; opacity: 0.3;"></i>
        </div>
        
        <div class="error-actions">
          <a href="index.php" class="btn-modern">
            <i class="fas fa-home" style="margin-right: 10px;"></i>Ir al Inicio
          </a>
          
          <a href="contacto.php" class="btn-modern btn-accent">
            <i class="fas fa-envelope" style="margin-right: 10px;"></i>Contactar
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
              <i class="fas fa-home"></i>
            </div>
            <h3>Ir al Inicio</h3>
            <p>Regresa a la página principal y navega por nuestro sitio web.</p>
            <a href="index.php" class="btn-modern">Ir al Inicio</a>
          </div>
        </div>
        
        <div>
          <div class="card-modern text-center">
            <div class="icon-modern">
              <i class="fas fa-search"></i>
            </div>
            <h3>Buscar Productos</h3>
            <p>Explora nuestro catálogo de simuladores médicos disponibles.</p>
            <a href="productos" class="btn-modern">Explorar Productos</a>
          </div>
        </div>
        
        <div>
          <div class="card-modern text-center">
            <div class="icon-modern">
              <i class="fas fa-phone"></i>
            </div>
            <h3>Contactar Soporte</h3>
            <p>Si crees que esto es un error, contacta a nuestro equipo.</p>
            <a href="contacto.php" class="btn-modern">Contactar</a>
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
      </div>
    </div>
  </footer>

  <!-- UIkit JS -->
  <script src="https://cdn.jsdelivr.net/npm/uikit@3.17.11/dist/js/uikit.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/uikit@3.17.11/dist/js/uikit-icons.min.js"></script>

</body>
</html> 