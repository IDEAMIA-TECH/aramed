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
  <meta property="og:image" content="<?=$ruta.$picOg?>">
  <meta property="fb:app_id" content="<?=$appID?>">

  <?=$headGNRL?>

</head>
<body>
  
<?=$header?>

<div class="padding-top-20">
  <div uk-grid class="uk-child-width-1-2@m uk-grid-match">
    <div>
      <div class="uk-cover-container" style="height: 500px;">
        <img src="<?=$picOg?>" uk-cover>
      </div>
    </div>
    <div class="container-det-noticia">
      <div class="padding-20">
        <div style="font-size: 14px;"><b>ARAMED Y LABORATORIO PRESENTA</b></div>
        <div class="uk-width-auto linea-azul"></div>
        <div>
          <div class="titulo-detalle-noticia">
            <b><?=$rowCONSULTA['titulo']?></b>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="uk-flex uk-flex-center">
  <div class="uk-text-center subtitulo-detalle-noticia">
    <b>IMAGENES DE LA REALIZACIÓN DEL PROYECTO</b>
    <div class="line-black"></div>
  </div>
</div>
<div uk-grid class="uk-child-width-1-3 uk-child-width-1-4@m uk-grid-collapse" uk-lightbox>
<?php
  while ($row_consultaPIC = $CONSULTAPIC -> fetch_assoc()) {
    $picTxt='img/design/camara.jpg';
    $pic='img/contenido/proyectos/'.$row_consultaPIC['id'].'-lg.jpg';
    if(file_exists($pic)){
      $picTxt=$pic;
    }
    echo '
      <div>
        <a href="'.$picTxt.'">
          <div class="uk-cover-container pic-sq">
            <img src="'.$picTxt.'" uk-cover>
          </div>
        </a>
      </div>';
  }
  
?>
</div>

<div class="uk-container">
  <div uk-grid class="uk-flex uk-flex-center">
    <div class="uk-width-2-3@m padding-v-50">
      <div class="color-negro">
        <div style="font-size: 18px;">
          <b>RESEÑA DEL PROYECTO</b>
        </div>
        <div class="line-black" style="width: 80px;"></div>
      </div>
      <div class="uk-column-1-2@s uk-text-justify padding-top-20" style="font-size: 15px;">
        <?=$rowCONSULTA['txt']?>
      </div>
    </div>
  </div>
</div>

<?php
  if (strlen($rowCONSULTA['video'])>0) {
    $picLg=$rowCONSULTA['video'];
    $url=$rowCONSULTA['video'];
    if (strpos($url, 'youtube')) {
      $pos=strpos($url, 'v');
      $url=substr($url, ($pos+2));
    }elseif (strpos($url, 'youtu.be')) {
      $pos=strrpos($url, '/');
      $url=substr($url, ($pos+1));
    }
    $pic='https://img.youtube.com/vi/'.$url.'/0.jpg';
    $play='<img src="img/design/play.png">';
    echo  '
        <div uk-lightbox class="uk-position-relative">
          <a href="'.$picLg.'" target="_blank">
            <div class="uk-position-center z2">
              '.$play.'
            </div>
            <div class="uk-cover-container margin-bottom-20" style="height: 90vh;">
              <img src="'.$pic.'" uk-cover>
              '.$play.'
            </div>
          </a>
        </div>';
  }
?>

<?=$footer?>

<?=$scriptGNRL?>

</body>
</html>