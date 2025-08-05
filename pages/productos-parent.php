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

<div class="uk-text-center titulo-subcategoria">
  <b><?=$row_CONSULTA1['txt']?></b>
</div>

<div class="uk-container uk-container-large padding-v-50">
  <div uk-grid class=" uk-child-width-1-2@s uk-child-width-1-3@m">
<?php  
  $productos_cat = $CONEXION -> query("SELECT * FROM productoscat WHERE parent = $id AND estatus = 1 ORDER BY orden,txt");
  while ($row_productos_cat = $productos_cat -> fetch_assoc()) {
    $actual=$row_productos_cat['id'];
    $link=$actual.'_0_'.urlencode(str_replace($caracteres_no_validos,$caracteres_si_validos,html_entity_decode(strtolower(trim($row_productos_cat['txt']))))).'-.php';

    $picTxt='img/design/camara.jpg';
    $pic='img/contenido/productoscat/'.$row_productos_cat['imagen'];
    if(strlen($row_productos_cat['imagen'])>0 AND file_exists($pic)){
      $picTxt=$pic;
    }elseif(strlen($row_productos_cat['imagen'])>0 AND strpos($row_productos_cat['imagen'], 'ttp')>0){
      $picTxt=$row_productos_cat['imagen'];
    }

    echo '
    <div>
      <a href="'.$link.'">
        <div class="uk-cover-container" style="min-height:300px;">
          <img src="'.$picTxt.'" uk-cover>
        </div>
        <div class="uk-text-center padding-top-20" style="color: #005d9c; font-size:20px;">
          <b>'.$row_productos_cat['txt'].'</b>
        </div>
      </a>
    </div>
  ';
}

?>
  </div>
</div>

<?=$footer?>

<?=$scriptGNRL?>

</body>
</html>