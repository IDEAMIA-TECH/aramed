<!DOCTYPE html>
<html lang="<?=$languaje?>">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#">
  <meta charset="utf-8">
  <title><?=$title?></title>
  <meta name="description" content="<?=$description?>">
  
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?=$title?>">
  <meta property="og:description" content="<?=$description?>">
  <meta property="og:url" content="<?=$rutaEstaPagina?>">
  <meta property="og:image" content="<?=$ruta?>img/design/logo-og.jpg">
  <meta property="fb:app_id" content="<?=$appID?>">

  <?=$headGNRL?>

</head>
<body>
  
<?=$header?>

<div class="uk-container padding-v-50" style="min-height: 50vh;">
  <h1 class="uk-text-center">Mapa del sitio</h1>
  <div class="uk-flex uk-flex-center">
    <ul uk-nav class="uk-nav-default uk-nav-parent-icon" style="width:500px">
      <li><a href="<?=$ruta?>" class="text-lg color-primary uk-text-bold">Inicio</a></li>
      <li><a href="<?=$rutaAbout?>" class="text-lg color-primary uk-text-bold">Acerca de Aramed</a></li>
      <li class="uk-parent">
        <a href="#" class="text-lg color-primary uk-text-bold">Productos</a>
        <ul class="uk-nav-sub">
  <?php
  $Consulta1 = $CONEXION -> query("SELECT * FROM productoscat WHERE parent = 0 AND estatus = 1 ORDER BY orden,txt");
  while ($row_Consulta1 = $Consulta1 -> fetch_assoc()) {
    $cat1=$row_Consulta1['id'];
    $link=$cat1.'_'.urlencode(str_replace($caracteres_no_validos,$caracteres_si_validos,html_entity_decode(strtolower($row_Consulta1['txt'])))).'_.php';;

    echo '
          <li>
            <a href="'.$link.'" class="color-primary" style="font-size:1.2em;">'.$row_Consulta1['txt'].'</a>
            <ul class="">';
    $Consulta2 = $CONEXION -> query("SELECT * FROM productoscat WHERE parent = $cat1 AND estatus = 1 ORDER BY orden,txt");
    while ($row_Consulta2 = $Consulta2 -> fetch_assoc()) {
      $cat2=$row_Consulta2['id'];
      $link=$cat2.'_0_'.urlencode(str_replace($caracteres_no_validos,$caracteres_si_validos,html_entity_decode(strtolower($row_Consulta2['txt'])))).'-.php';;
      echo '
              <li>
                <a href="'.$link.'" class="color-primary" style="font-size:1.1em;"><i class="fas fa-angle-right"></i> &nbsp; '.$row_Consulta2['txt'].'</a>';
      $Consulta3 = $CONEXION -> query("SELECT * FROM productos WHERE categoria = $cat2 AND estatus = 1 ORDER BY orden,titulo");
      while ($row_Consulta3 = $Consulta3 -> fetch_assoc()) {
        $itemId=$row_Consulta3['id'];
        $link=$itemId.'_'.urlencode(str_replace($caracteres_no_validos,$caracteres_si_validos,html_entity_decode(strtolower($row_Consulta3['titulo'])))).'-.html';;
        echo '
                <li><a href="'.$link.'" class="color-primary">&nbsp;&nbsp; <i class="fas fa-angle-double-right"></i> &nbsp; '.$row_Consulta3['titulo'].'</a></li>';
      }
    echo '

              </li>
';
  }
    echo '
            </ul>
          </li>
    ';
  }
  ?>
        </ul>
      </li>
      <li class="uk-parent">
        <a href="#" class="text-lg color-primary uk-text-bold">Proyectos</a>
        <ul class="uk-nav-sub">
  <?php
  $Consulta1 = $CONEXION -> query("SELECT * FROM proyectos WHERE estatus = 1 ORDER BY orden,titulo");
  while ($row_Consulta1 = $Consulta1 -> fetch_assoc()) {
    $thisid=$row_Consulta1['id'];
    $link=$thisid.'_'.urlencode(str_replace($caracteres_no_validos,$caracteres_si_validos,html_entity_decode(strtolower($row_Consulta1['titulo'])))).'_aramed';;
    echo '
          <li><a href="'.$link.'" class="text-lg color-primary">'.$row_Consulta1['titulo'].'</a><li>';
  }
  ?>

        </ul>
      </li>
      <li class="uk-parent">
        <a href="#" class="text-lg color-primary uk-text-bold">Noticias</a>
        <ul class="uk-nav-sub">
  <?php
  $Consulta1 = $CONEXION -> query("SELECT * FROM noticias ORDER BY orden,titulo");
  while ($row_Consulta1 = $Consulta1 -> fetch_assoc()) {
    $thisid=$row_Consulta1['id'];
    $link=$thisid.'_'.urlencode(str_replace($caracteres_no_validos,$caracteres_si_validos,html_entity_decode(strtolower($row_Consulta1['titulo'])))).'_news';;
    echo '
          <li><a href="'.$link.'" class="text-lg color-primary">'.$row_Consulta1['titulo'].'</a><li>';
  }
  ?>

        </ul>
      </li>
      <li><a href="<?=$rutaServicios?>" class="text-lg color-primary uk-text-bold">Servicios</a></li>
      <li><a href="<?=$rutaContacto?>" class="text-lg color-primary uk-text-bold">Contacto</a></li>
    </ul>
  </div>
</div>

<?=$footer?>

<?=$scriptGNRL?>

</body>
</html>