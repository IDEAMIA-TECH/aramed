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
<?php 
$consulta = $CONEXION -> query("SELECT * FROM servicios WHERE estatus = 1 ORDER BY orden");
$rowConsulta = $consulta -> fetch_assoc();
  $picTxt='img/design/blank.png';
  $pic='img/contenido/servicios/'.$rowConsulta['imagen2'];
  if(file_exists($pic) AND strlen($rowConsulta['imagen2'])>0){
    $picTxt=$pic;
  }
  echo '
<!-- Seccion 1 -->
<div class="margin-top-20 padding-20 uk-text-center color-blanco" style="background: #0972be;">
  <div class="padding-v-20 uk-position-relative">
    <div>
      <img src="'.$picTxt.'" style="max-height:100px">
    </div>
    <div class="font-30">
      '.$rowConsulta['titulo'].'
    </div>
    <div class="small-white-line uk-position-bottom-center"></div>
  </div>
  <div class="uk-flex uk-flex-center padding-v-20">
    <div style="max-width: 720px;">
      '.$rowConsulta['txt'].'
    </div>
  </div>
</div>
<!-- Fin seccion 1 -->';

?>
<!-- Inicio seccion 2 -->
<div class="uk-text-center servicios-2">
  <div class="uk-position-relative padding-20">
    SERVICIOS QUE FORMAN EL PROYECTO LLAVE EN MANO
    <div class="large-blue-line uk-position-bottom-center"></div>
  </div>
</div>
<!-- Fin seccion 2 -->

<?php
while($rowConsulta = $consulta -> fetch_assoc()){
  $picTxt='img/design/blank.png';
  $pic='img/contenido/servicios/'.$rowConsulta['imagen2'];
  if(file_exists($pic) AND strlen($rowConsulta['imagen2'])>0){
    $picTxt=$pic;
  }
  echo '
<!-- Seccion X -->
<div class="margin-top-20 padding-20 uk-text-center color-blanco" style="background: #0972be;">
  <div class="padding-v-20 uk-position-relative">
    <div>
      <img src="'.$picTxt.'" style="max-height:100px">
    </div>
    <div class="font-30">
      '.$rowConsulta['subtitulo'].'
    </div>
    <div class="small-white-line uk-position-bottom-center"></div>
  </div>
  <div class="uk-flex uk-flex-center padding-v-20">
    <div style="max-width: 720px;">
      '.$rowConsulta['txt'].'
    </div>
  </div>
</div>
<!-- Fin seccion X -->';
}
?>

<?=$footer?>

<?=$scriptGNRL?>

</body>
</html>