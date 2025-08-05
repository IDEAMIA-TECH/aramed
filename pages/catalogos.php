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


<div class="uk-container padding-top-100 uk-text-center">
  <div class="text-xl uk-text-bold">
    Descarga nuestros catálogos
  </div>

  <div uk-grid class="uk-child-width-1-4@s uk-text-center uk-flex-center">

<?php
$CONSULTA1 = $CONEXION -> query("SELECT * FROM catalogos WHERE estatus = 1 ORDER BY orden");
while($row_CONSULTA1 = $CONSULTA1 -> fetch_assoc()){
  $fallo=0;
  $fileRuta='img/contenido/catalogos/';

  $filePdf=$fileRuta.$row_CONSULTA1['pdf'];
  if(!file_exists($filePdf) or strlen($row_CONSULTA1['pdf'])==0){
    $fallo=1;
    $msj='No se encuentra el PDF';
  }

  $fileImg=$fileRuta.$row_CONSULTA1['imagen'];
  if(!file_exists($fileImg) or strlen($row_CONSULTA1['imagen'])==0){
    $fallo=1;
    $msj='No se encuentra la imagen';
  }

  if ($fallo==0) {
    echo '
    <div>
      <div class="padding-v-50">
        <h2><a href="'.$filePdf.'" download="'.trim(html_entity_decode($row_CONSULTA1['titulo'])).'.pdf" class="uk-button uk-button-personal"><i uk-icon="cloud-download"></i> &nbsp; Descarga</a></h2>
        <img src="'.$fileImg.'">
      </div>
    </div>';
  }else{
    echo '
    <div>
      <div class="padding-v-50">
        <div class="uk-alert-danger uk-text-center" uk-alert>
          <p>'.$msj.'</p>
        </div>
      </div>
    </div>';
  }
}
?>

  </div>
</div>
  </div>
</div>

<?=$footer?>

<?=$scriptGNRL?>

</body>
</html>