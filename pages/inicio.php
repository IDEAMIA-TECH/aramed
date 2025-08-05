<?php
// Definir variables necesarias antes de incluir includes.php
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

// Incluir archivo de variables y funciones
require_once('includes/includes.php');
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
            <h3 style="color: #333; margin-bottom: 15px;">'.$rowConsulta['titulo'].'</h3>
            <p style="color: #666; line-height: 1.6;">'.substr($rowConsulta['txt'], 0, 150).'...</p>
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
  <section style="background: url('.$pic.') no-repeat center; background-size: cover; min-height: 600px; position: relative;">
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6);"></div>
    <div class="uk-container uk-height-1-1 uk-flex uk-flex-middle uk-flex-center">
      <div class="uk-text-center" style="position: relative; z-index: 2; color: white;">
        <h2 style="font-size: 2.5rem; margin-bottom: 20px;">'.$rowConsulta['videotxt'].'</h2>
        <div uk-lightbox>
          <a href="'.$videoUrl.'" class="hero-cta" style="background: rgba(255,255,255,0.2);">
            <i class="fas fa-play" style="margin-right: 10px;"></i>Reproducir Video
          </a>
        </div>
      </div>
    </div>
  </section>';
}
?>

<!-- Best Sellers Section -->
<section style="background: #2d4e76; color: white; padding: 80px 0;">
  <div class="uk-container">
    <h2 class="section-title" style="color: white;">Productos Destacados</h2>
    <p class="uk-text-center" style="font-size: 1.2rem; margin-bottom: 50px;">Ofrecemos una solución integral con productos de calidad.</p>
    
    <div uk-slider="sets: true" class="uk-margin-large">
      <ul class="uk-slider-items uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l uk-light uk-text-center">
        <?php
        $consulta = $CONEXION -> query("SELECT id FROM productos WHERE destacado = 1 AND estatus = 1 ORDER BY orden,id");
        while ($rowConsulta = $consulta -> fetch_assoc()) {
          echo '
          <li>
            '.itemInicio($rowConsulta['id']).'
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
    <h2 class="section-title" style="color: white;">Lo que Dicen Nuestros Clientes</h2>
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

<?=$footer?>

<?=$scriptGNRL?>

</body>
</html>