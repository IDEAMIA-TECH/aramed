<?php
// Definir variables necesarias directamente
$ruta = '';
$telefono = '+52 55 1234 5678';
$uikitVersion = '3.17.11';
$mensaje = '';
$mensajeClase = '';

// Variables de navegación
$nav1 = 'uk-active';
$nav2 = '';
$nav3 = '';
$nav5 = '';
$nav6 = '';
$nav7 = '';
$nav8 = '';
$nav9 = '';

// Variables de redes sociales
$socialFace = '#';
$socialInst = '#';
$socialYout = '#';

// Definir variables de rutas
$rutaInicio = $ruta;
$rutaAbout = $ruta.'about';
$rutaCategoria = $ruta.'categorias';
$rutaCatalogos = $ruta.'catalogos';
$rutaCotizar = $ruta.'cotizar';
$rutaServicios = $ruta.'servicios';
$rutaProyectos = $ruta.'proyectos';
$rutaNoticias = $ruta.'noticias';
$rutaContacto = $ruta.'contacto';
$rutaProducto = $ruta.'productos';
$rutaDetProducto = $ruta.'detalle_producto';
$rutaDetNoticias = $ruta.'detalle_noticias';
$rutaDetProyecto = $ruta.'detalle_proyecto';
$rutaLogin = $ruta.'login';
$rutaSubcategoria = $ruta.'subcategoria';

// Definir menú
$menu = '
<li class="'.$nav1.'"><a href="'.$rutaInicio.'">INICIO</a></li>
<li class="'.$nav2.'"><a href="'.$rutaAbout.'">ACERCA DE</a></li>
<li class="'.$nav3.'"><a href="'.$rutaCategoria.'">PRODUCTOS</a></li>
<li class="'.$nav9.'"><a href="'.$rutaCatalogos.'">CATÁLOGOS</a></li>
<li class="'.$nav5.'"><a href="'.$rutaServicios.'">SERVICIOS</a></li>
<li class="'.$nav6.'"><a href="'.$rutaProyectos.'">PROYECTOS</a></li>
<li class="'.$nav7.'"><a href="'.$rutaNoticias.'">NOTICIAS</a></li>
<li class="'.$nav8.'"><a href="'.$rutaContacto.'">CONTACTO</a></li>
';

$menuMovil = '
<li><a class="'.$nav1.'" href="'.$rutaInicio.'">Inicio</a></li>
<li><a class="'.$nav2.'" href="'.$rutaAbout.'">Acerca de Aramed</a></li>
<li><a class="'.$nav3.'" href="'.$rutaCategoria.'">Productos</a></li>
<li><a class="'.$nav9.'" href="'.$rutaCatalogos.'">Catálogos</a></li>
<li><a class="'.$nav5.'" href="'.$rutaServicios.'">Servicios</a></li>
<li><a class="'.$nav6.'" href="'.$rutaProyectos.'">Proyectos</a></li>
<li><a class="'.$nav7.'" href="'.$rutaNoticias.'">Noticias</a></li>
<li><a class="'.$nav8.'" href="'.$rutaContacto.'">Contacto</a></li>
';

// Definir header moderno
$header = '
<header class="uk-section uk-section-small uk-background-default" uk-sticky="show-on-up: true; animation: uk-animation-slide-top">
  <div class="uk-container">
    <nav class="uk-navbar-container uk-navbar-transparent" uk-navbar>
      <div class="uk-navbar-left">
        <a class="uk-navbar-item uk-logo" href="'.$rutaInicio.'">
          <img src="img/design/logo.png" style="max-height: 60px; padding: 10px 0;">
        </a>
      </div>
      
      <div class="uk-navbar-right uk-visible@m">
        <ul class="uk-navbar-nav">
          '.$menu.'
        </ul>
        <div class="uk-navbar-item">
          <div class="uk-text-right">
            <div style="font-size: 0.9rem; color: #666;">Teléfono</div>
            <div style="font-weight: 600; color: #00a2e8;">'.$telefono.'</div>
          </div>
        </div>
      </div>
      
      <div class="uk-navbar-right uk-hidden@m">
        <a class="uk-navbar-toggle" uk-navbar-toggle-icon href="#" uk-toggle="target: #mobile-menu"></a>
      </div>
    </nav>
  </div>
  
  <!-- Mobile Menu -->
  <div id="mobile-menu" uk-offcanvas="mode: push; overlay: true">
    <div class="uk-offcanvas-bar">
      <button class="uk-offcanvas-close" type="button" uk-close></button>
      <ul class="uk-nav uk-nav-primary uk-nav-center uk-margin-auto-vertical" uk-nav>
        '.$menuMovil.'
      </ul>
    </div>
  </div>
</header>
'.$mensajes;

// Definir headGNRL
$headGNRL = '
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="'.$ruta.'img/design/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="img/design/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/'.$uikitVersion.'/css/uikit.min.css" />
<link rel="stylesheet" href="css/general.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:200,300,400,600,700,800,900">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.6/css/all.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/'.$uikitVersion.'/js/uikit.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/'.$uikitVersion.'/js/uikit-icons.min.js"></script>
';

// Definir scriptGNRL
$scriptGNRL = '
<script src="js/general.js"></script>
';

// Definir footer moderno
$footer = '
<footer class="uk-section uk-section-secondary uk-light" style="background: #2c3e50;">
  <div class="uk-container">
    <div uk-grid class="uk-child-width-1-4@m uk-child-width-1-2@s">
      <div>
        <h4 style="color: #00a2e8; margin-bottom: 20px;">ARAMed</h4>
        <p style="color: #bdc3c7; line-height: 1.6;">Simuladores médicos para la enseñanza y formación profesional. Comprometidos con la excelencia en la educación médica.</p>
        <div class="uk-margin-top">
          <a href="'.$socialFace.'" class="uk-icon-button uk-margin-small-right" uk-icon="facebook" style="background: #00a2e8; color: white;"></a>
          <a href="'.$socialInst.'" class="uk-icon-button uk-margin-small-right" uk-icon="instagram" style="background: #00a2e8; color: white;"></a>
          <a href="'.$socialYout.'" class="uk-icon-button uk-margin-small-right" uk-icon="youtube" style="background: #00a2e8; color: white;"></a>
        </div>
      </div>
      <div>
        <h4 style="color: #00a2e8; margin-bottom: 20px;">Contacto</h4>
        <p style="color: #bdc3c7;"><i class="fas fa-phone" style="color: #00a2e8; margin-right: 10px;"></i> '.$telefono.'</p>
        <p style="color: #bdc3c7;"><i class="fas fa-envelope" style="color: #00a2e8; margin-right: 10px;"></i> info@aramed.com</p>
        <p style="color: #bdc3c7;"><i class="fas fa-map-marker-alt" style="color: #00a2e8; margin-right: 10px;"></i> Ciudad de México, México</p>
      </div>
      <div>
        <h4 style="color: #00a2e8; margin-bottom: 20px;">Enlaces</h4>
        <ul class="uk-list" style="color: #bdc3c7;">
          <li><a href="'.$rutaProducto.'" style="color: #bdc3c7; text-decoration: none;">Productos</a></li>
          <li><a href="'.$rutaServicios.'" style="color: #bdc3c7; text-decoration: none;">Servicios</a></li>
          <li><a href="'.$rutaCategoria.'" style="color: #bdc3c7; text-decoration: none;">Categorías</a></li>
          <li><a href="'.$rutaContacto.'" style="color: #bdc3c7; text-decoration: none;">Contacto</a></li>
        </ul>
      </div>
      <div>
        <h4 style="color: #00a2e8; margin-bottom: 20px;">Horarios</h4>
        <p style="color: #bdc3c7;">Lunes - Viernes: 9:00 - 18:00</p>
        <p style="color: #bdc3c7;">Sábado: 9:00 - 14:00</p>
        <p style="color: #bdc3c7;">Domingo: Cerrado</p>
      </div>
    </div>
    <div class="uk-text-center uk-margin-large-top" style="border-top: 1px solid #34495e; padding-top: 20px;">
      <p style="color: #95a5a6;">&copy; '.date('Y').' ARAmed. Todos los derechos reservados.</p>
    </div>
  </div>
</footer>
';

// Variable mensajes
$mensajes = '';
?>
<!DOCTYPE html>
<html lang="<?=$languaje?>">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#">
  <meta charset="utf-8">
  <title><?=$title?></title>
  <meta name="description" content="<?=$description?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?=$title?>">
  <meta property="og:description" content="<?=$description?>">
  <meta property="og:url" content="<?=$rutaEstaPagina?>">
  <meta property="og:image" content="<?=$ruta?>img/design/logo-og.jpg">
  <meta property="fb:app_id" content="<?=$appID?>">

  <?=$headGNRL?>
  
  <!-- CSS Moderno para ARAmed -->
  <link rel="stylesheet" href="css/modern-styles.css">

  <style>
    /* Estilos modernos inspirados en el template médico */
    :root {
      --primary-color: #00a2e8;
      --secondary-color: #0066ad;
      --accent-color: #f39c12;
      --text-dark: #2c3e50;
      --text-light: #7f8c8d;
      --bg-light: #f8f9fa;
      --white: #ffffff;
    }
    
    body {
      font-family: 'Montserrat', sans-serif;
      color: var(--text-dark);
      line-height: 1.6;
    }
    
    /* Hero Section Moderno */
    .hero-section {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
      color: var(--white);
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
      border: 2px solid var(--white);
      color: var(--white);
      padding: 15px 40px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      display: inline-block;
    }
    
    .hero-cta:hover {
      background: var(--white);
      color: var(--primary-color);
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    
    /* Info Cards */
    .info-cards {
      margin-top: -50px;
      position: relative;
      z-index: 10;
    }
    
    .info-card {
      background: var(--white);
      border-radius: 10px;
      padding: 30px;
      text-align: center;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      height: 100%;
    }
    
    .info-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .info-card.emergency {
      background: var(--primary-color);
      color: var(--white);
    }
    
    .info-card.timetable {
      background: var(--secondary-color);
      color: var(--white);
    }
    
    .info-card.hours {
      background: var(--white);
      color: var(--text-dark);
    }
    
    .info-card h3 {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 15px;
    }
    
    .info-card p {
      font-size: 0.9rem;
      margin-bottom: 20px;
      opacity: 0.9;
    }
    
    .info-card .btn {
      background: var(--accent-color);
      color: var(--white);
      padding: 8px 20px;
      border-radius: 25px;
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    
    .info-card .btn:hover {
      background: #e67e22;
      transform: translateY(-2px);
    }
    
    /* Features Section */
    .features-section {
      padding: 80px 0;
      background: var(--bg-light);
    }
    
    .feature-card {
      background: var(--white);
      border-radius: 15px;
      padding: 40px 30px;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      height: 100%;
      position: relative;
    }
    
    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    
    .feature-icon {
      width: 80px;
      height: 80px;
      margin: 0 auto 25px;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--white);
      font-size: 2rem;
    }
    
    .feature-card h3 {
      color: var(--text-dark);
      margin-bottom: 15px;
      font-weight: 600;
    }
    
    .feature-card p {
      color: var(--text-light);
      line-height: 1.6;
    }
    
    /* Categories Section */
    .categories-section {
      padding: 80px 0;
      background: var(--white);
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
      color: var(--white);
      padding: 30px 20px 20px;
      text-align: center;
    }
    
    /* Video Section */
    .video-section {
      position: relative;
      min-height: 500px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--white);
      text-align: center;
    }
    
    .video-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.6);
      z-index: 1;
    }
    
    .video-content {
      position: relative;
      z-index: 2;
    }
    
    /* Products Section */
    .products-section {
      padding: 80px 0;
      background: var(--secondary-color);
      color: var(--white);
    }
    
    .product-card {
      background: rgba(255,255,255,0.1);
      border-radius: 15px;
      padding: 20px;
      text-align: center;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.2);
      transition: all 0.3s ease;
    }
    
    .product-card:hover {
      background: rgba(255,255,255,0.2);
      transform: translateY(-3px);
    }
    
    /* Brands Section */
    .brands-section {
      padding: 80px 0;
      background: var(--bg-light);
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
    
    /* Testimonials Section */
    .testimonials-section {
      padding: 80px 0;
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
      color: var(--white);
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
    
    /* Section Titles */
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
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      border-radius: 2px;
    }
    
    .section-title.white::after {
      background: var(--white);
    }
    
    /* Buttons */
    .btn-primary-modern {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      border: none;
      color: var(--white);
      padding: 12px 30px;
      border-radius: 25px;
      font-weight: 600;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }
    
    .btn-primary-modern:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(0, 162, 232, 0.4);
      color: var(--white);
      text-decoration: none;
    }
    
    /* Header y Navegación */
    .uk-navbar-container {
      background: rgba(255, 255, 255, 0.95) !important;
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(0, 0, 0, 0.1);
      width: 100%;
    }
    
    .uk-navbar-nav {
      width: 100%;
      justify-content: flex-end;
      margin-right: 20px;
    }
    
    .uk-navbar-nav > li > a {
      color: #333 !important;
      font-weight: 500;
      text-transform: uppercase;
      font-size: 0.9rem;
      padding: 0 15px;
      transition: all 0.3s ease;
      white-space: nowrap;
    }
    
    .uk-navbar-nav > li > a:hover {
      color: #00a2e8 !important;
      background: rgba(0, 162, 232, 0.1);
    }
    
    .uk-navbar-nav > li.uk-active > a {
      color: #00a2e8 !important;
      background: rgba(0, 162, 232, 0.1);
      border-radius: 5px;
    }
    
    .uk-navbar-right {
      flex: 1;
      justify-content: flex-end;
    }
    
    .uk-logo img {
      max-height: 60px;
      width: auto;
    }
    
    /* Mobile Menu */
    .uk-offcanvas-bar {
      background: #2c3e50;
    }
    
    .uk-offcanvas-bar .uk-nav-primary > li > a {
      color: #fff;
      font-size: 1.1rem;
      padding: 15px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .uk-offcanvas-bar .uk-nav-primary > li > a:hover {
      color: #00a2e8;
      background: rgba(0, 162, 232, 0.1);
    }
    
    /* Responsive */
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
      
      .info-cards {
        margin-top: 0;
      }
    }
  </style>
</head>
<body>

<?=$header?>

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

<!-- Info Cards -->
<div class="uk-container info-cards">
  <div uk-grid class="uk-child-width-1-3@m uk-child-width-1-1@s">
    <div>
      <div class="info-card emergency">
        <h3>Servicio de Emergencia</h3>
        <p>Si necesitas asistencia técnica urgente fuera del horario normal, contáctanos inmediatamente.</p>
        <a href="<?=$rutaContacto?>" class="btn">Contactar →</a>
      </div>
    </div>
    <div>
      <div class="info-card timetable">
        <h3>Horarios de Atención</h3>
        <p>Estamos disponibles para brindarte el mejor servicio y asesoría técnica especializada.</p>
        <a href="<?=$rutaContacto?>" class="btn">Ver Horarios →</a>
      </div>
    </div>
    <div>
      <div class="info-card hours">
        <h3>Horarios de Oficina</h3>
        <p>Lunes - Viernes: 9:00 - 18:00<br>
        Sábado: 9:00 - 14:00<br>
        Domingo: Cerrado</p>
        <a href="<?=$rutaContacto?>" class="btn">Contactar →</a>
      </div>
    </div>
  </div>
</div>

<!-- Carousel Section -->
<div class="uk-position-relative">
  <?=carouselInicio("carousel")?>
</div>

<!-- Features Section -->
<section class="features-section">
  <div class="uk-container">
    <h2 class="section-title">Nuestros Servicios</h2>
    <div uk-grid class="uk-child-width-1-3@m uk-child-width-1-2@s uk-child-width-1-1">
      <?php
      $consulta = $CONEXION -> query("SELECT * FROM servicios WHERE estatus = 1 ORDER BY orden");
      while($rowConsulta = $consulta -> fetch_assoc()){
        $picTxt='img/design/blank.png';
        $pic='img/contenido/servicios/'.$rowConsulta['imagen1'];
        if(file_exists($pic) AND strlen($rowConsulta['imagen1'])>0){
          $picTxt=$pic;
        }
        echo '
        <div>
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-medical-kit"></i>
            </div>
            <h3>'.$rowConsulta['titulo'].'</h3>
            <p>'.substr($rowConsulta['txt'], 0, 150).'...</p>
          </div>
        </div>';
      }
      ?>
    </div>
    <div class="uk-text-center" style="margin-top: 50px;">
      <a href="<?=$rutaServicios?>" class="btn-primary-modern">Ver Todos los Servicios</a>
    </div>
  </div>
</section>

<!-- Categories Section -->
<section class="categories-section" id="productos">
  <div class="uk-container">
    <h2 class="section-title">Categorías de Productos</h2>
    <div uk-slider="sets:true;" class="uk-margin-large">
      <ul class="uk-slider-items uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l uk-light uk-text-center">
        <?php
        $consulta = $CONEXION -> query("SELECT id FROM productoscat WHERE parent = 0 AND estatus = 1 ORDER BY orden,id");
        while ($rowConsulta = $consulta -> fetch_assoc()) {
          echo '
          <li>
            <div class="category-card">
              '.catInicio($rowConsulta['id']).'
            </div>
          </li>
          ';
        }
        ?>
      </ul>
      <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
    </div>
    <div class="uk-text-center" style="margin-top: 30px;">
      <a href="<?=$rutaCategoria?>" class="btn-primary-modern">Ver Todas las Categorías</a>
    </div>
  </div>
</section>

<!-- Video Section -->
<?php
$CONSULTA = $CONEXION -> query("SELECT video,videotxt,imagen5 FROM configuracion WHERE id = 1");
$rowConsulta = $CONSULTA -> fetch_assoc();
if (strlen($rowConsulta['video'])>0) {
  $videoUrl=$rowConsulta['video'];
  $videoPic=$videoUrl;
  if (strpos($videoPic, 'youtube')) {
    $pos=strpos($videoPic, 'v');
    $videoPic=substr($videoPic, ($pos+2));
  }elseif (strpos($videoPic, 'youtu.be')) {
    $pos=strrpos($videoPic, '/');
    $videoPic=substr($videoPic, ($pos+1));
  }
  $pic='https://img.youtube.com/vi/'.$videoPic.'/0.jpg';
  $picPersonal='img/contenido/varios/'.$rowConsulta['imagen5'];
  if (strlen($rowConsulta['imagen5'])>0 AND file_exists($picPersonal)) {
    $pic=$picPersonal;
  }
  echo '
  <section class="video-section" style="background: url('.$pic.') no-repeat center; background-size: cover;">
    <div class="video-content">
      <h2 style="font-size: 2.5rem; margin-bottom: 20px;">'.$rowConsulta['videotxt'].'</h2>
      <div uk-lightbox>
        <a href="'.$videoUrl.'" class="hero-cta" style="background: rgba(255,255,255,0.2);">
          <i class="fas fa-play" style="margin-right: 10px;"></i>Reproducir Video
        </a>
      </div>
    </div>
  </section>';
}
?>

<!-- Best Sellers Section -->
<section class="products-section">
  <div class="uk-container">
    <h2 class="section-title white">Productos Destacados</h2>
    <p class="uk-text-center" style="font-size: 1.2rem; margin-bottom: 50px;">Ofrecemos una solución integral con productos de calidad.</p>
    
    <div uk-slider="sets: true" class="uk-margin-large">
      <ul class="uk-slider-items uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l uk-light uk-text-center">
        <?php
        $consulta = $CONEXION -> query("SELECT id FROM productos WHERE destacado = 1 AND estatus = 1 ORDER BY orden,id");
        while ($rowConsulta = $consulta -> fetch_assoc()) {
          echo '
          <li>
            <div class="product-card">
              '.itemInicio($rowConsulta['id']).'
            </div>
          </li>
          ';
        }
        ?>
      </ul>
      <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
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
          <?php  
          $consulta = $CONEXION -> query("SELECT id FROM empresas WHERE estatus=0 ORDER BY orden");
          while ($rowConsulta = $consulta -> fetch_assoc()){
            $pic='img/contenido/empresas/'.$rowConsulta['id'].'.png';
            if(strlen($rowConsulta['id'])>0){
              echo '
              <li class="uk-flex uk-flex-center uk-flex-middle" style="height: 100px;">
                <img src="'.$pic.'" class="brand-logo">
              </li>
              ';
            }
          }
          ?>
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
    <h2 class="section-title white">Lo que Dicen Nuestros Clientes</h2>
    <div uk-slider="autoplay:true;autoplay-interval:4000;" class="uk-margin-large">
      <ul class="uk-slider-items uk-child-width-1-2@s uk-child-width-1-3@m">
        <?php 
        $productos = $CONEXION -> query("SELECT * FROM testimonios ORDER BY orden LIMIT 6");
        while ($row_productos = $productos -> fetch_assoc()) {
          $prodID=$row_productos['id'];
          $picTxt='img/contenido/varios/default.jpg';
          $pic='img/contenido/testimonios/'.$row_productos['imagen'];
          if(file_exists($pic) AND strlen($row_productos['imagen'])>0){
            $picTxt=$pic;
          }
          echo '
          <li>
            <div class="testimonial-card">
              <img src="'.$picTxt.'" class="testimonial-avatar" alt="'.$row_productos['titulo'].'">
              <h3 style="margin-bottom: 15px;">'.$row_productos['titulo'].'</h3>
              <p style="font-style: italic; margin-bottom: 10px;">"'.$row_productos['txt'].'"</p>
              <small style="opacity: 0.8;">'.$row_productos['email'].'</small>
            </div>
          </li>';
        }
        ?>
      </ul>
      <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
    </div>
  </div>
</section>

<?php
// Definir footer si no está definido
if (!isset($footer)) {
  $footer = '
  <footer class="uk-section uk-section-secondary uk-light" style="background: #2c3e50;">
    <div class="uk-container">
      <div uk-grid class="uk-child-width-1-4@m uk-child-width-1-2@s">
        <div>
          <h4 style="color: #00a2e8; margin-bottom: 20px;">ARAMed</h4>
          <p style="color: #bdc3c7; line-height: 1.6;">Simuladores médicos para la enseñanza y formación profesional. Comprometidos con la excelencia en la educación médica.</p>
          <div class="uk-margin-top">
            <a href="#" class="uk-icon-button uk-margin-small-right" uk-icon="facebook" style="background: #00a2e8; color: white;"></a>
            <a href="#" class="uk-icon-button uk-margin-small-right" uk-icon="instagram" style="background: #00a2e8; color: white;"></a>
            <a href="#" class="uk-icon-button uk-margin-small-right" uk-icon="youtube" style="background: #00a2e8; color: white;"></a>
          </div>
        </div>
        <div>
          <h4 style="color: #00a2e8; margin-bottom: 20px;">Contacto</h4>
          <p style="color: #bdc3c7;"><i class="fas fa-phone" style="color: #00a2e8; margin-right: 10px;"></i> +52 55 1234 5678</p>
          <p style="color: #bdc3c7;"><i class="fas fa-envelope" style="color: #00a2e8; margin-right: 10px;"></i> info@aramed.com</p>
          <p style="color: #bdc3c7;"><i class="fas fa-map-marker-alt" style="color: #00a2e8; margin-right: 10px;"></i> Ciudad de México, México</p>
        </div>
        <div>
          <h4 style="color: #00a2e8; margin-bottom: 20px;">Enlaces</h4>
          <ul class="uk-list" style="color: #bdc3c7;">
            <li><a href="productos" style="color: #bdc3c7; text-decoration: none;">Productos</a></li>
            <li><a href="servicios" style="color: #bdc3c7; text-decoration: none;">Servicios</a></li>
            <li><a href="categorias" style="color: #bdc3c7; text-decoration: none;">Categorías</a></li>
            <li><a href="contacto" style="color: #bdc3c7; text-decoration: none;">Contacto</a></li>
          </ul>
        </div>
        <div>
          <h4 style="color: #00a2e8; margin-bottom: 20px;">Horarios</h4>
          <p style="color: #bdc3c7;">Lunes - Viernes: 9:00 - 18:00</p>
          <p style="color: #bdc3c7;">Sábado: 9:00 - 14:00</p>
          <p style="color: #bdc3c7;">Domingo: Cerrado</p>
        </div>
      </div>
      <div class="uk-text-center uk-margin-large-top" style="border-top: 1px solid #34495e; padding-top: 20px;">
        <p style="color: #95a5a6;">&copy; '.date('Y').' ARAmed. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>
  ';
}

// Definir scriptGNRL si no está definido
if (!isset($scriptGNRL)) {
  $scriptGNRL = '
  <script src="js/general.js"></script>
  ';
}

echo $footer;
echo $scriptGNRL;
?>

</body>
</html>