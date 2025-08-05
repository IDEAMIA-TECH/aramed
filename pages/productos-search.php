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

<?=searchBar()?>

<div class="uk-text-center titulo-subproducto">
  <b><?=$row_CONSULTA1['txt']?></b>
</div>

<div class="uk-container padding-v-50">
  <div uk-grid class="uk-child-width-1-2@s uk-flex-center">
<?php 
$productos_cat = $CONEXION -> query("SELECT id FROM productos WHERE titulo like '%$cadena%' AND estatus = 1 ORDER BY orden,titulo");
while ($row_productos_cat = $productos_cat -> fetch_assoc()) {
  echo item($row_productos_cat['id']);
}
?>
  </div>
</div>
<?=$footer?>

<?=$scriptGNRL?>

</body>
</html>