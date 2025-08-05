<?php
  $consulta       = $CONEXION -> query("SELECT noticias1, noticias2 FROM configuracion WHERE id = 1");
  $rowConsulta    = $consulta -> fetch_assoc();
  $noticiasTitulo = $rowConsulta['noticias1'];
  $noticiasTxt    = $rowConsulta['noticias2'];
  mysqli_free_result($consulta);
?>
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

<div class="uk-container padding-v-50" style="min-height: 80vh;">
  <div uk-grid class="uk-child-width-1-3@m uk-child-width-1-2@s">
    <!--
    <div class="uk-flex uk-flex-center uk-text-justify">
      <div class="" style="max-width: 450px;">
        <div class="uk-text-center" style="font-size: 35px;color: #0086df;">
          <b><?=$noticiasTitulo?></b>
        </div>
        <div class="texto padding-top-20" style="font-size: 18px;"> 
          <?=$noticiasTxt?>
        </div>
      </div>
    </div>
    -->
    
<?php
  $productos = $CONEXION -> query("SELECT * FROM noticias ORDER BY orden");
  while ($row_productos = $productos -> fetch_assoc()) {

    $prodID=$row_productos['id'];
    $link=$prodID.'_'.urlencode(str_replace($caracteres_no_validos,$caracteres_si_validos,html_entity_decode(strtolower($row_productos['titulo'])))).'_news';
    
    $lugaryfecha=$row_productos['lugaryfecha'];

    $label1='';
    $label2='';
    $numero=80;

    if(strlen($row_productos['label1title'])>0){
      $label1='
      <div class="uk-position-relative uk-width-auto">
          '.$row_productos['label1title'].'
          <div class="subrayado-verde"></div>
      </div>';
    }

    if(strlen($row_productos['label2title'])>0){
      $label2='
      <div class="uk-position-relative uk-width-auto uk-text-right">
          '.$row_productos['label2title'].'
          <div class="subrayado-verde"></div>
      </div>';
    }

    if(strlen($row_productos['label1title'])>0 AND strlen($row_productos['label1link'])>0){
      $label1='
      <div class="uk-position-relative uk-width-auto">
        <a href="'.$row_productos['label1link'].'" target="_blank">
          '.$row_productos['label1title'].'
          <div class="subrayado-verde"></div>
        </a>
      </div>';
    }

    if(strlen($row_productos['label2title'])>0 AND strlen($row_productos['label2link'])>0){
      $label2='
      <div class="uk-position-relative uk-width-auto uk-text-right">
        <a href="'.$row_productos['label2link'].'" target="_blank">
          '.$row_productos['label2title'].'
          <div class="subrayado-verde"></div>
        </a>
      </div>';
    }

    $CONSULTA3 = $CONEXION -> query("SELECT * FROM noticiaspic WHERE producto = $prodID ORDER BY orden");
    $row_CONSULTA3 = $CONSULTA3 -> fetch_assoc();

    $picTxt='';
    $pic='img/contenido/noticias/'.$row_CONSULTA3['id'].'-sm.jpg';
    if(file_exists($pic)){
      $numero=40;
      $picTxt='
          <div class="uk-cover-container uk-child-width-1-1" style="height:300px;">
            <img src="'.$pic.'" uk-cover>
          </div>';
    }

    echo '
      <div class="uk-flex uk-flex-center">
        <div class="" style="max-width: 400px;">
          '.$picTxt.'
          <div class="texto">
            <div class="uk-text-center padding-v-10" style="font-size: 35px;color: #0086df;">
              <a href="'.$link.'"><b>'.$row_productos['titulo'].'</b></a>
            </div>
            <div class="uk-text-center padding-bottom-20">
              '.$lugaryfecha.'
            </div>
            <div class="uk-text-justify padding-bottom-10 ">
              '.nl2br(wordlimit($row_productos['txt'],$numero,'...')).'
            </div>
          </div>
          <div uk-grid>
            '.$label1.'
            '.$label2.'
          </div>
        </div>
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