<?php
  $rutaFinal='img/contenido/varios/';

  $CONSULTA          = $CONEXION -> query("SELECT * FROM configuracion WHERE id = 1");
  $row_CONSULTA      = $CONSULTA -> fetch_assoc();

  for ($i=2; $i < 5; $i++) { 
    $pic='pic'.$i;
    $$pic=$rutaFinal.$row_CONSULTA['imagen'.$i];
    if(strlen($row_CONSULTA['imagen'.$i])>0 AND file_exists($$pic)){
      $$pic = $$pic;
    }else{
      $$pic = 'img/design/acerca-de-'.($i-1).'.jpg';
    }
  }
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

  <style>
    /* Estilos modernos para la página Acerca de */
    .about-hero {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 120px 0 80px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    
    .about-hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.1)" points="0,1000 1000,0 1000,1000"/></svg>');
      background-size: cover;
    }
    
    .about-hero-content {
      position: relative;
      z-index: 2;
    }
    
    .about-hero h1 {
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 1rem;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    .about-hero p {
      font-size: 1.3rem;
      opacity: 0.9;
      max-width: 600px;
      margin: 0 auto;
    }
    
    .about-section {
      padding: 80px 0;
      background: white;
    }
    
    .about-card {
      background: white;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      margin-bottom: 40px;
      transition: all 0.3s ease;
    }
    
    .about-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .about-image {
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }
    
    .about-image:hover {
      transform: scale(1.02);
    }
    
    .about-image img {
      width: 100%;
      height: 400px;
      object-fit: cover;
    }
    
    .about-content h2 {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 20px;
      color: #333;
    }
    
    .about-content h3 {
      font-size: 1.8rem;
      font-weight: 600;
      margin-bottom: 15px;
      color: #667eea;
    }
    
    .about-content p {
      font-size: 1.1rem;
      line-height: 1.8;
      color: #666;
      margin-bottom: 20px;
    }
    
    .stats-section {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 80px 0;
      text-align: center;
    }
    
    .stat-item {
      text-align: center;
      padding: 30px 20px;
    }
    
    .stat-number {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 10px;
      display: block;
    }
    
    .stat-label {
      font-size: 1.2rem;
      opacity: 0.9;
    }
    
    .values-section {
      padding: 80px 0;
      background: #f8f9fa;
    }
    
    .value-card {
      background: white;
      border-radius: 15px;
      padding: 30px;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      height: 100%;
    }
    
    .value-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    
    .value-icon {
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
    
    .value-card h3 {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 15px;
      color: #333;
    }
    
    .value-card p {
      color: #666;
      line-height: 1.6;
    }
    
    .mission-vision-section {
      padding: 80px 0;
      background: white;
    }
    
    .mission-vision-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 20px;
      padding: 40px;
      margin-bottom: 30px;
      position: relative;
      overflow: hidden;
    }
    
    .mission-vision-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.1)" points="0,0 1000,1000 1000,0"/></svg>');
      background-size: cover;
    }
    
    .mission-vision-content {
      position: relative;
      z-index: 2;
    }
    
    .mission-vision-card h2 {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 20px;
    }
    
    .mission-vision-card p {
      font-size: 1.1rem;
      line-height: 1.8;
      opacity: 0.9;
    }
    
    .cta-section {
      background: #f8f9fa;
      padding: 80px 0;
      text-align: center;
    }
    
    .cta-button {
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
      padding: 15px 40px;
      border-radius: 25px;
      text-decoration: none;
      font-weight: 600;
      display: inline-block;
      transition: all 0.3s ease;
      margin: 10px;
    }
    
    .cta-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
      color: white;
      text-decoration: none;
    }
    
    @media (max-width: 768px) {
      .about-hero h1 {
        font-size: 2.5rem;
      }
      
      .about-content h2 {
        font-size: 2rem;
      }
      
      .stat-number {
        font-size: 2.5rem;
      }
      
      .about-card {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>
  
<?=$header?>

<!-- Hero Section -->
<section class="about-hero">
  <div class="uk-container">
    <div class="about-hero-content">
      <h1>ARAMED</h1>
      <p><strong>30 AÑOS DE EXPERIENCIA</strong> en simuladores médicos</p>
      <p style="font-style: italic; font-size: 1.1rem; margin-top: 20px;">"Por la dignidad del paciente, reduciendo el error humano."</p>
    </div>
  </div>
</section>

<!-- About Section -->
<section class="about-section">
  <div class="uk-container">
    <div uk-grid class="uk-child-width-1-2@m uk-child-width-1-1">
      
      <!-- Historia -->
      <div>
        <div class="about-card">
          <div class="about-image">
            <img src="<?=$pic2?>" alt="Historia de ARAMED">
          </div>
        </div>
      </div>
      
      <div>
        <div class="about-card">
          <div class="about-content">
            <h2>Nuestra Historia</h2>
            <p><?=$row_CONSULTA['about1']?></p>
            <p>Con más de tres décadas de experiencia, hemos sido pioneros en la introducción de tecnología avanzada en la educación médica, contribuyendo significativamente al desarrollo de profesionales de la salud más competentes y seguros.</p>
          </div>
        </div>
      </div>
      
      <!-- Valores -->
      <div>
        <div class="about-card">
          <div class="about-content">
            <h2>Nuestros Valores</h2>
            <p><?=$row_CONSULTA['about2']?></p>
            <p>Nos guiamos por principios fundamentales que han sido la base de nuestro éxito y crecimiento continuo en el sector de la educación médica.</p>
          </div>
        </div>
      </div>
      
      <div>
        <div class="about-card">
          <div class="about-image">
            <img src="<?=$pic3?>" alt="Valores de ARAMED">
          </div>
        </div>
      </div>
      
      <!-- Misión y Visión -->
      <div>
        <div class="about-card">
          <div class="about-image">
            <img src="<?=$pic4?>" alt="Misión y Visión de ARAMED">
          </div>
        </div>
      </div>
      
      <div>
        <div class="about-card">
          <div class="about-content">
            <h3>MISIÓN</h3>
            <p><?=$row_CONSULTA['about3']?></p>
            <h3>VISIÓN</h3>
            <p><?=$row_CONSULTA['about4']?></p>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
  <div class="uk-container">
    <h2 style="margin-bottom: 50px; text-align: center;">Nuestros Números</h2>
    
    <div uk-grid class="uk-child-width-1-4@m uk-child-width-1-2@s uk-child-width-1-1">
      <div class="stat-item">
        <span class="stat-number">30+</span>
        <div class="stat-label">Años de Experiencia</div>
      </div>
      <div class="stat-item">
        <span class="stat-number">500+</span>
        <div class="stat-label">Instituciones Atendidas</div>
      </div>
      <div class="stat-item">
        <span class="stat-number">1000+</span>
        <div class="stat-label">Simuladores Entregados</div>
      </div>
      <div class="stat-item">
        <span class="stat-number">50+</span>
        <div class="stat-label">Marcas Representadas</div>
      </div>
    </div>
  </div>
</section>

<!-- Values Section -->
<section class="values-section">
  <div class="uk-container">
    <h2 style="text-align: center; margin-bottom: 50px; color: #333;">Nuestros Valores Fundamentales</h2>
    
    <div uk-grid class="uk-child-width-1-3@m uk-child-width-1-2@s uk-child-width-1-1">
      <div>
        <div class="value-card">
          <div class="value-icon">
            <i class="fas fa-star"></i>
          </div>
          <h3>Excelencia</h3>
          <p>Buscamos la excelencia en cada producto y servicio que ofrecemos, garantizando la más alta calidad en simuladores médicos.</p>
        </div>
      </div>
      
      <div>
        <div class="value-card">
          <div class="value-icon">
            <i class="fas fa-handshake"></i>
          </div>
          <h3>Confianza</h3>
          <p>Construimos relaciones duraderas basadas en la confianza, la transparencia y el compromiso con nuestros clientes.</p>
        </div>
      </div>
      
      <div>
        <div class="value-card">
          <div class="value-icon">
            <i class="fas fa-lightbulb"></i>
          </div>
          <h3>Innovación</h3>
          <p>Nos mantenemos a la vanguardia de la tecnología, incorporando las últimas innovaciones en educación médica.</p>
        </div>
      </div>
      
      <div>
        <div class="value-card">
          <div class="value-icon">
            <i class="fas fa-users"></i>
          </div>
          <h3>Servicio</h3>
          <p>Brindamos un servicio integral y personalizado, acompañando a nuestros clientes en cada etapa del proceso.</p>
        </div>
      </div>
      
      <div>
        <div class="value-card">
          <div class="value-icon">
            <i class="fas fa-heart"></i>
          </div>
          <h3>Compromiso</h3>
          <p>Estamos comprometidos con la mejora de la educación médica y la seguridad del paciente.</p>
        </div>
      </div>
      
      <div>
        <div class="value-card">
          <div class="value-icon">
            <i class="fas fa-globe"></i>
          </div>
          <h3>Impacto</h3>
          <p>Buscamos generar un impacto positivo en la formación de profesionales de la salud en México y Latinoamérica.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Mission Vision Section -->
<section class="mission-vision-section">
  <div class="uk-container">
    <div uk-grid class="uk-child-width-1-2@m uk-child-width-1-1">
      <div>
        <div class="mission-vision-card">
          <div class="mission-vision-content">
            <h2>Nuestra Misión</h2>
            <p>Proporcionar soluciones integrales en simuladores médicos de la más alta calidad, contribuyendo al desarrollo de profesionales de la salud competentes y seguros, mediante tecnología innovadora y un servicio excepcional.</p>
          </div>
        </div>
      </div>
      
      <div>
        <div class="mission-vision-card">
          <div class="mission-vision-content">
            <h2>Nuestra Visión</h2>
            <p>Ser la empresa líder en México y Latinoamérica en la provisión de simuladores médicos, reconocida por nuestra innovación, calidad y compromiso con la excelencia en la educación médica.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
  <div class="uk-container">
    <h2 style="margin-bottom: 30px; color: #333;">¿Listo para comenzar?</h2>
    <p style="font-size: 1.2rem; margin-bottom: 30px; color: #666;">Descubre cómo podemos ayudarte a mejorar la educación médica en tu institución</p>
    
    <a href="<?=$ruta?>contacto.php" class="cta-button">
      <i class="fas fa-envelope" style="margin-right: 10px;"></i>Contáctanos
    </a>
    
    <a href="<?=$ruta?>cotizar.php" class="cta-button" style="background: linear-gradient(135deg, #28a745, #20c997);">
      <i class="fas fa-calculator" style="margin-right: 10px;"></i>Solicitar Cotización
    </a>
  </div>
</section>

<?=$footer?>

<?=$scriptGNRL?>

</body>
</html>