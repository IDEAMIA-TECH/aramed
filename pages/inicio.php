<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ARAMed - Simuladores Médicos</title>
  <meta name="description" content="Simuladores médicos para la enseñanza y formación profesional">
  
  <!-- UIkit CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.17.11/dist/css/uikit.min.css" />
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- CSS Moderno -->
  <link rel="stylesheet" href="css/modern-styles.css">
  
  <style>
    /* Estilos modernos para la página de inicio */
    .hero-section {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 120px 0 80px;
      position: relative;
      overflow: hidden;
    }
    
    .hero-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.1)" points="0,1000 1000,0 1000,1000"/></svg>');
      background-size: cover;
    }
    
    .hero-content {
      position: relative;
      z-index: 2;
    }
    
    .hero-title {
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 1rem;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    .hero-subtitle {
      font-size: 1.5rem;
      font-weight: 300;
      margin-bottom: 2rem;
      opacity: 0.9;
    }
    
    .hero-cta {
      background: rgba(255,255,255,0.2);
      border: 2px solid white;
      color: white;
      padding: 15px 40px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      display: inline-block;
    }
    
    .hero-cta:hover {
      background: white;
      color: #667eea;
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    
    .features-section {
      padding: 80px 0;
      background: #f8f9fa;
    }
    
    .feature-card {
      background: white;
      border-radius: 15px;
      padding: 30px;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      height: 100%;
    }
    
    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    
    .feature-icon {
      width: 80px;
      height: 80px;
      margin: 0 auto 20px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 2rem;
    }
    
    .categories-section {
      padding: 80px 0;
      background: white;
    }
    
    .category-card {
      position: relative;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      height: 300px;
    }
    
    .category-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }
    
    .category-card img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .category-overlay {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(transparent, rgba(0,0,0,0.8));
      color: white;
      padding: 30px 20px 20px;
      text-align: center;
    }
    
    .brands-section {
      padding: 80px 0;
      background: #f8f9fa;
    }
    
    .brand-logo {
      filter: grayscale(100%);
      transition: all 0.3s ease;
      max-height: 60px;
      opacity: 0.6;
    }
    
    .brand-logo:hover {
      filter: grayscale(0%);
      opacity: 1;
      transform: scale(1.1);
    }
    
    .testimonials-section {
      padding: 80px 0;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }
    
    .testimonial-card {
      background: rgba(255,255,255,0.1);
      border-radius: 15px;
      padding: 30px;
      text-align: center;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.2);
    }
    
    .testimonial-avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      margin: 0 auto 20px;
      border: 3px solid rgba(255,255,255,0.3);
    }
    
    .section-title {
      font-size: 2.5rem;
      font-weight: 700;
      text-align: center;
      margin-bottom: 3rem;
      position: relative;
    }
    
    .section-title::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      border-radius: 2px;
    }
    
    .btn-primary-modern {
      background: linear-gradient(135deg, #667eea, #764ba2);
      border: none;
      color: white;
      padding: 12px 30px;
      border-radius: 25px;
      font-weight: 600;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }
    
    .btn-primary-modern:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
      color: white;
      text-decoration: none;
    }
    
    @media (max-width: 768px) {
      .hero-title {
        font-size: 2.5rem;
      }
      
      .hero-subtitle {
        font-size: 1.2rem;
      }
      
      .section-title {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body>

<!-- Header -->
<nav class="uk-navbar-container" uk-navbar>
  <div class="uk-container">
    <div class="uk-navbar-left">
      <ul class="uk-navbar-nav">
        <li><a href="index.php">ARAMed</a></li>
      </ul>
    </div>
    <div class="uk-navbar-right">
      <ul class="uk-navbar-nav">
        <li><a href="pages/productos-root.php">Productos</a></li>
        <li><a href="pages/servicios.php">Servicios</a></li>
        <li><a href="pages/contacto.php">Contacto</a></li>
        <li><a href="pages/cotizar.php">Cotizar</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
  <div class="uk-container">
    <div class="hero-content uk-text-center">
      <h1 class="hero-title">SIMULADORES MÉDICOS</h1>
      <p class="hero-subtitle">Para la enseñanza y formación profesional</p>
      <p class="hero-subtitle" style="font-style: italic; font-size: 1.2rem;">"Por la dignidad del paciente, reduciendo el error humano."</p>
      <a href="#productos" class="hero-cta">Explorar Productos</a>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="features-section">
  <div class="uk-container">
    <h2 class="section-title">Nuestros Servicios</h2>
    <div uk-grid class="uk-child-width-1-3@m uk-child-width-1-2@s uk-child-width-1-1">
      <div>
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-medical-kit"></i>
          </div>
          <h3 style="color: #333; margin-bottom: 15px;">Simuladores de Pacientes</h3>
          <p style="color: #666; line-height: 1.6;">Simuladores avanzados para entrenamiento médico con tecnología de última generación.</p>
        </div>
      </div>
      <div>
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-graduation-cap"></i>
          </div>
          <h3 style="color: #333; margin-bottom: 15px;">Capacitación Profesional</h3>
          <p style="color: #666; line-height: 1.6;">Programas de entrenamiento especializados para profesionales de la salud.</p>
        </div>
      </div>
      <div>
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-tools"></i>
          </div>
          <h3 style="color: #333; margin-bottom: 15px;">Soporte Técnico</h3>
          <p style="color: #666; line-height: 1.6;">Mantenimiento y soporte técnico especializado para todos nuestros equipos.</p>
        </div>
      </div>
    </div>
    <div class="uk-text-center" style="margin-top: 50px;">
      <a href="pages/servicios.php" class="btn-primary-modern">Ver Todos los Servicios</a>
    </div>
  </div>
</section>

<!-- Categories Section -->
<section class="categories-section" id="productos">
  <div class="uk-container">
    <h2 class="section-title">Categorías de Productos</h2>
    <div uk-grid class="uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l">
      <div>
        <div class="category-card">
          <img src="img/design/blank.png" alt="Simuladores de Emergencia">
          <div class="category-overlay">
            <h3>Simuladores de Emergencia</h3>
            <p>Equipos para entrenamiento en situaciones críticas</p>
          </div>
        </div>
      </div>
      <div>
        <div class="category-card">
          <img src="img/design/blank.png" alt="Simuladores de Cirugía">
          <div class="category-overlay">
            <h3>Simuladores de Cirugía</h3>
            <p>Entrenamiento especializado en procedimientos quirúrgicos</p>
          </div>
        </div>
      </div>
      <div>
        <div class="category-card">
          <img src="img/design/blank.png" alt="Simuladores de Parto">
          <div class="category-overlay">
            <h3>Simuladores de Parto</h3>
            <p>Equipos para obstetricia y ginecología</p>
          </div>
        </div>
      </div>
      <div>
        <div class="category-card">
          <img src="img/design/blank.png" alt="Simuladores de Pediatría">
          <div class="category-overlay">
            <h3>Simuladores de Pediatría</h3>
            <p>Entrenamiento especializado en atención pediátrica</p>
          </div>
        </div>
      </div>
    </div>
    <div class="uk-text-center" style="margin-top: 30px;">
      <a href="pages/productos-root.php" class="btn-primary-modern">Ver Todas las Categorías</a>
    </div>
  </div>
</section>

<!-- Video Section -->
<section style="background: url('img/design/blank.png') no-repeat center; background-size: cover; min-height: 600px; position: relative;">
  <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6);"></div>
  <div class="uk-container uk-height-1-1 uk-flex uk-flex-middle uk-flex-center">
    <div class="uk-text-center" style="position: relative; z-index: 2; color: white;">
      <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Conoce Nuestros Simuladores</h2>
      <a href="#" class="hero-cta" style="background: rgba(255,255,255,0.2);">
        <i class="fas fa-play" style="margin-right: 10px;"></i>Ver Video
      </a>
    </div>
  </div>
</section>

<!-- Best Sellers Section -->
<section style="background: #2d4e76; color: white; padding: 80px 0;">
  <div class="uk-container">
    <h2 class="section-title" style="color: white;">Productos Destacados</h2>
    <p class="uk-text-center" style="font-size: 1.2rem; margin-bottom: 50px;">Ofrecemos una solución integral con productos de calidad.</p>
    
    <div uk-grid class="uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l">
      <div>
        <div class="feature-card" style="background: rgba(255,255,255,0.1); color: white;">
          <div class="feature-icon">
            <i class="fas fa-heartbeat"></i>
          </div>
          <h3 style="margin-bottom: 15px;">Simulador de RCP</h3>
          <p>Entrenamiento en reanimación cardiopulmonar</p>
        </div>
      </div>
      <div>
        <div class="feature-card" style="background: rgba(255,255,255,0.1); color: white;">
          <div class="feature-icon">
            <i class="fas fa-stethoscope"></i>
          </div>
          <h3 style="margin-bottom: 15px;">Simulador de Auscultación</h3>
          <p>Práctica de auscultación cardiaca y pulmonar</p>
        </div>
      </div>
      <div>
        <div class="feature-card" style="background: rgba(255,255,255,0.1); color: white;">
          <div class="feature-icon">
            <i class="fas fa-baby"></i>
          </div>
          <h3 style="margin-bottom: 15px;">Simulador de Parto</h3>
          <p>Entrenamiento en obstetricia</p>
        </div>
      </div>
      <div>
        <div class="feature-card" style="background: rgba(255,255,255,0.1); color: white;">
          <div class="feature-icon">
            <i class="fas fa-user-md"></i>
          </div>
          <h3 style="margin-bottom: 15px;">Simulador de Cirugía</h3>
          <p>Práctica de procedimientos quirúrgicos</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Brands Section -->
<section class="brands-section">
  <div class="uk-container">
    <h2 class="section-title">Marcas que Representamos</h2>
    <div uk-slider="autoplay: true;autoplay-interval:3000;" class="uk-margin-large">
      <div class="uk-position-relative uk-visible-toggle uk-light">
        <ul class="uk-slider-items uk-child-width-1-4@m uk-child-width-1-2@s uk-grid uk-flex-middle">
          <li class="uk-flex uk-flex-center uk-flex-middle" style="height: 100px;">
            <img src="img/design/blank.png" class="brand-logo" alt="Marca 1">
          </li>
          <li class="uk-flex uk-flex-center uk-flex-middle" style="height: 100px;">
            <img src="img/design/blank.png" class="brand-logo" alt="Marca 2">
          </li>
          <li class="uk-flex uk-flex-center uk-flex-middle" style="height: 100px;">
            <img src="img/design/blank.png" class="brand-logo" alt="Marca 3">
          </li>
          <li class="uk-flex uk-flex-center uk-flex-middle" style="height: 100px;">
            <img src="img/design/blank.png" class="brand-logo" alt="Marca 4">
          </li>
        </ul>
        <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
        <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slider-item="next"></a>
      </div>
    </div>
  </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section">
  <div class="uk-container">
    <h2 class="section-title" style="color: white;">Lo que Dicen Nuestros Clientes</h2>
    <div uk-slider="autoplay:true;autoplay-interval:4000;" class="uk-margin-large">
      <ul class="uk-slider-items uk-child-width-1-2@s uk-child-width-1-3@m">
        <li>
          <div class="testimonial-card">
            <img src="img/design/blank.png" class="testimonial-avatar" alt="Cliente 1">
            <h3 style="margin-bottom: 15px;">Dr. Juan Pérez</h3>
            <p style="font-style: italic; margin-bottom: 10px;">"Excelente calidad en los simuladores, han mejorado significativamente nuestro programa de entrenamiento."</p>
            <small style="opacity: 0.8;">Hospital General</small>
          </div>
        </li>
        <li>
          <div class="testimonial-card">
            <img src="img/design/blank.png" class="testimonial-avatar" alt="Cliente 2">
            <h3 style="margin-bottom: 15px;">Dra. María García</h3>
            <p style="font-style: italic; margin-bottom: 10px;">"El soporte técnico es excepcional y los equipos son muy confiables para nuestro centro de simulación."</p>
            <small style="opacity: 0.8;">Universidad Médica</small>
          </div>
        </li>
        <li>
          <div class="testimonial-card">
            <img src="img/design/blank.png" class="testimonial-avatar" alt="Cliente 3">
            <h3 style="margin-bottom: 15px;">Dr. Carlos López</h3>
            <p style="font-style: italic; margin-bottom: 10px;">"Los simuladores han transformado la forma en que entrenamos a nuestros residentes."</p>
            <small style="opacity: 0.8;">Centro Médico</small>
          </div>
        </li>
      </ul>
      <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="uk-section uk-section-secondary uk-light">
  <div class="uk-container">
    <div uk-grid class="uk-child-width-1-4@m uk-child-width-1-2@s">
      <div>
        <h4>ARAMed</h4>
        <p>Simuladores médicos para la enseñanza y formación profesional.</p>
      </div>
      <div>
        <h4>Contacto</h4>
        <p><i class="fas fa-phone"></i> +52 55 1234 5678</p>
        <p><i class="fas fa-envelope"></i> info@aramed.com</p>
      </div>
      <div>
        <h4>Enlaces</h4>
        <ul class="uk-list">
          <li><a href="pages/productos-root.php">Productos</a></li>
          <li><a href="pages/servicios.php">Servicios</a></li>
          <li><a href="pages/contacto.php">Contacto</a></li>
        </ul>
      </div>
      <div>
        <h4>Síguenos</h4>
        <div class="uk-margin">
          <a href="#" class="uk-icon-button uk-margin-small-right" uk-icon="facebook"></a>
          <a href="#" class="uk-icon-button uk-margin-small-right" uk-icon="twitter"></a>
          <a href="#" class="uk-icon-button uk-margin-small-right" uk-icon="instagram"></a>
        </div>
      </div>
    </div>
    <div class="uk-text-center uk-margin-large-top">
      <p>&copy; 2024 ARAmed. Todos los derechos reservados.</p>
    </div>
  </div>
</footer>

<!-- UIkit JS -->
<script src="https://cdn.jsdelivr.net/npm/uikit@3.17.11/dist/js/uikit.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/uikit@3.17.11/dist/js/uikit-icons.min.js"></script>

</body>
</html>