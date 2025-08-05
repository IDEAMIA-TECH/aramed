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

<div class="uk-container padding-bottom-50">
  <div class="uk-text-center inicio-proyectos">
    <b>Nuestros Proyectos</b>
  </div>
  <div class="uk-text-center padding-top-10 font-25" style="color: #666;">
    Trabajos de Aramed y Laboratorio
  </div>
  <div class="uk-text-center font-25" style="color:black">
    con nuestros principales clientes
  </div>
</div>

<?php
  $num=0;
  $productos = $CONEXION -> query("SELECT * FROM proyectos ORDER BY orden");
  while ($row_productos = $productos -> fetch_assoc()) {
    
    $prodID=$row_productos['id'];
    $link=$prodID.'_'.urlencode(str_replace($caracteres_no_validos,$caracteres_si_validos,html_entity_decode(strtolower($row_productos['titulo'])))).'_aramed';

    $CONSULTA3 = $CONEXION -> query("SELECT * FROM proyectospic WHERE producto = $prodID ORDER BY orden");
    $row_CONSULTA3 = $CONSULTA3 -> fetch_assoc();

    $picTxt='img/design/camara.jpg';
    $pic='img/contenido/proyectos/'.$row_CONSULTA3['id'].'-lg.jpg';
    if(file_exists($pic)){
      $picTxt=$pic;
    }


    $lateralidad='';
    if ($num==0) {
      $num=1;
      $lateralidad='uk-flex-right';
    }else{
      $num=0;
    }
    echo '
      <div class="uk-widht-1-1 uk-position-relative" style="background: url('.$picTxt.') no-repeat center; background-size: cover; height: 700px;">
        <div class="bg-opacity oscuro transicion">
          <div class="uk-container uk-height-1-1 uk-flex uk-flex-middle '.$lateralidad.'"">
            <div class="uk-text-center uk-position-relative z2">
              <div class="uk-flex uk-flex-center '.$lateralidad.'">
                <div class="titulo-proyecto">
                  '.$row_productos['titulo'].'
                </div>
              </div>
              <div class="subtitulo-proyecto color-blanco">
                '.$row_productos['subtitulo'].'<br>
                <span class="uk-text-uppercase">'.fechaMesCompleto($row_productos['fecha']).'</span>
              </div>
              <a href="'.$link.'" class="button-proyecto">Ver Proyecto</a>
            </div>
          </div>
        </div>
      </div>';
  }
?>


<div class="uk-text-center padding-v-100 testimonios">
  <b>TESTIMONIOS</b>
</div>

<div class="uk-container">
  <div class="uk-position-relative uk-visible-toggle uk-dark" tabindex="-1" uk-slider="autoplay:true;autoplay-interval:3000;">
    <ul class="uk-slider-items uk-child-width-1-2 uk-child-width-1-3@s uk-child-width-1-4@m">

<?php 
$productos = $CONEXION -> query("SELECT * FROM testimonios ORDER BY orden");
while ($row_productos = $productos -> fetch_assoc()) {
  $prodID=$row_productos['id'];
  $picTxt='img/contenido/varios/default.jpg';
  $pic='img/contenido/testimonios/'.$row_productos['imagen'];
  if(file_exists($pic) AND strlen($row_productos['imagen'])>0){
    $picTxt=$pic;
  }
  echo '
    <li>
      <div class="uk-flex uk-flex-center">
        <div class="uk-text-center" style="max-width: 270px; color: #084b7a;">
          <div class="uk-cover-container" style="min-height:350px;">
            <img src="'.$picTxt.'" uk-cover>
          </div>
          <div class="padding-top-20" style="font-size:25px;">
            <b>'.$row_productos['titulo'].'</b>
          </div>
          <div class="testimonio_link">
            '.$row_productos['email'].'
          </div>
          <div class="uk-text-left padding-top-10" style="font-size:18px;">
            '.$row_productos['txt'].'
          </div>
        </div>
      </div>
    </li>';
}
?>
    </ul>

    <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>

  </div>
</div>

<?=$footer?>

<?=$scriptGNRL?>

</body>
</html>