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
<li class="'.$nav1.'"><a href="'.$rutaInicio.'">Inicio</a></li>
<li class="'.$nav2.'"><a href="'.$rutaAbout.'">Acerca de Aramed</a></li>
<li class="'.$nav3.'"><a href="'.$rutaCategoria.'">Productos</a></li>
<li class="'.$nav9.'"><a href="'.$rutaCatalogos.'">Catálogos</a></li>
<li class="'.$nav5.'"><a href="'.$rutaServicios.'">Servicios</a></li>
<li class="'.$nav6.'"><a href="'.$rutaProyectos.'">Proyectos</a></li>
<li class="'.$nav7.'"><a href="'.$rutaNoticias.'">Noticias</a></li>
<li class="'.$nav8.'"><a href="'.$rutaContacto.'">Contacto</a></li>
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

// Definir header
$header = '
<header class="padding-v-5">
  <div uk-grid class="uk-grid-collapse">
    <div class="uk-width-expand" style="margin-left: 40px;">
      <img src="img/design/logo.png" style="max-height: 100px; padding: 10px 0;">
    </div>
    <div class="uk-width-auto uk-hidden@s" style="padding: 20px 20px 0 0;">
      <button type="button" uk-toggle="target: #offcanvas-flip" uk-icon="icon: menu; ratio: 2;"></button>
    </div>
    <div class="uk-width-auto@s uk-flex uk-text-right@s uk-text-center">
      <div class="uk-width-1-1 login-padding">
        <div class="padding-v-20 uk-width-1-1">
          <div class="padding-top-10 color-primary text-lg">
            Tel: '.$telefono.'
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="uk-width-1-1 uk-visible@s" style="background: #0066ad;" uk-sticky>
    <nav class="uk-navbar-container uk-navbar-transparent" uk-navbar>
      <div class="uk-navbar-right ">
        <ul class="uk-navbar-nav">
          '.$menu.'
        </ul>
      </div>
    </nav>
  </div>
  <div id="offcanvas-flip" uk-offcanvas="flip: true; overlay: true">
    <div class="uk-offcanvas-bar">
      <button class="uk-offcanvas-close" type="button" uk-close></button>
      <ul class="uk-nav uk-nav-primary uk-nav-parent-icon uk-nav-center uk-margin-auto-vertical" style="padding-top: 20px;" uk-nav>
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

// Definir footer
$footer = '
<footer class="uk-section uk-section-secondary uk-light">
  <div class="uk-container">
    <div uk-grid class="uk-child-width-1-4@m uk-child-width-1-2@s">
      <div>
        <h4>ARAMed</h4>
        <p>Simuladores médicos para la enseñanza y formación profesional.</p>
      </div>
      <div>
        <h4>Contacto</h4>
        <p><i class="fas fa-phone"></i> '.$telefono.'</p>
        <p><i class="fas fa-envelope"></i> info@aramed.com</p>
      </div>
      <div>
        <h4>Enlaces</h4>
        <ul class="uk-list">
          <li><a href="'.$rutaProducto.'">Productos</a></li>
          <li><a href="'.$rutaServicios.'">Servicios</a></li>
          <li><a href="'.$rutaContacto.'">Contacto</a></li>
        </ul>
      </div>
      <div>
        <h4>Síguenos</h4>
        <div class="uk-margin">
          <a href="'.$socialFace.'" class="uk-icon-button uk-margin-small-right" uk-icon="facebook"></a>
          <a href="'.$socialInst.'" class="uk-icon-button uk-margin-small-right" uk-icon="instagram"></a>
          <a href="'.$socialYout.'" class="uk-icon-button uk-margin-small-right" uk-icon="youtube"></a>
        </div>
      </div>
    </div>
    <div class="uk-text-center uk-margin-large-top">
      <p>&copy; '.date('Y').' ARAmed. Todos los derechos reservados.</p>
    </div>
  </div>
</footer>
';

// Variable mensajes
$mensajes = '';

// Verificar que las variables estén definidas
echo "Variables definidas:<br>";
echo "footer: " . (isset($footer) ? "SÍ" : "NO") . "<br>";
echo "scriptGNRL: " . (isset($scriptGNRL) ? "SÍ" : "NO") . "<br>";
echo "header: " . (isset($header) ? "SÍ" : "NO") . "<br>";
echo "headGNRL: " . (isset($headGNRL) ? "SÍ" : "NO") . "<br>";
?> 