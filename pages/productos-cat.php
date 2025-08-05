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
  <div uk-grid class="uk-child-width-1-2@s">
<?php 
$productos_cat = $CONEXION -> query("SELECT id FROM productos WHERE categoria = $id AND estatus = 1 ");
$numItems   = $productos_cat->num_rows;
$prodInicial= $prodsPagina*$pag;

$productos_cat = $CONEXION -> query("SELECT id FROM productos WHERE categoria = $id AND estatus = 1 ORDER BY orden,titulo LIMIT $prodInicial, $prodsPagina");
while ($row_productos_cat = $productos_cat -> fetch_assoc()) {
  echo item($row_productos_cat['id']);
}
?>
  </div>
</div>



<!-- PAGINATION -->
<?php
$pagTotal=intval($numItems/$prodsPagina);
$modulo=$numItems % $prodsPagina;
if (($modulo) == 0){
  $pagTotal=($numItems/$prodsPagina)-1;
}
if($pagTotal>0){
  echo '
<div class="bg-bottom">
  <div class="padding-v-50">
    <ul class="uk-pagination uk-flex-center uk-text-center">';
      $txt=urlencode(str_replace($caracteres_no_validos,$caracteres_si_validos,html_entity_decode(strtolower($catName))));
      if ($pag!=0) {
        $link=$id.'_'.($pag-1).'_'.$txt.'-.php';
        echo'
        <li><a href="'.$link.'" class="pagination-arrows"><i class="fa fa-lg fa-angle-left"></i> &nbsp;&nbsp; Anterior</a></li>';
      }
      for ($i=0; $i <= $pagTotal; $i++) { 
        $clase='';
        if ($pag==$i) {
          $clase='uk-active';
        }
        $link=$id.'_'.$i.'_'.$txt.'-.php';
        echo '<li><a href="'.$link.'" class="'.$clase.'">'.($i+1).'</a></li>';
      }
      if ($pag!=$pagTotal AND $numItems!=0) {
        $link=$id.'_'.($pag+1).'_'.$txt.'-.php';
        echo'
        <li><a href="'.$link.'" class="pagination-arrows">Siguiente &nbsp;&nbsp; <i class="fa fa-lg fa-angle-right"></i></a></li>
        ';
      }
  echo '
    </ul>
  </div>
</div><!-- PAGINATION -->';
}
?>



<?=$footer?>

<?=$scriptGNRL?>

</body>
</html>