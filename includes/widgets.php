<?php
// CARRO DE COMPRA       
  //unset($_SESSION['carro']);
  if (isset($_POST['emptycart'])) {
    unset($_SESSION['carro']);
  }

  // Si ya hay productos en la variable de sesión
  if(isset($_SESSION['carro'])){
    $arreglo=$_SESSION['carro'];
  }

  // Remover artículos del carro
  if (isset($_POST['removefromcart'])) {
    $id=$_POST['id'];
    $arregloAux=$_SESSION['carro'];
    unset($arreglo);
    $num=0;
    foreach ($arregloAux as $key => $value) {
      if ($id!=$value['Id']) {
        $arreglo[]=array('Id'=>$arregloAux[$num]['Id']);
      }
      $num++;
    }
    $_SESSION['carro']=$arreglo;
  }

  // Agregar artículos al carro
  if (isset($_POST['addtocart'])) {
    $id=$_POST['id'];

    $msjClass='danger';
    $msjIcon='exclamation-triangle';
    $msjTxt='Ocurrió un error';

    $arregloNuevo[]=array('Id'=>$id);

    if (!isset($arreglo)) {
      $arreglo=$arregloNuevo;
      $msjClass='success';
      $msjIcon='check';
      $msjTxt='Agregado a la cotización';
    }else{
      $arregloAux=$arreglo;
      unset($arreglo);
      $num=0;
      foreach ($arregloAux as $key => $value) {
        if ($id!=$arregloAux[$num]['Id']) {
          $arreglo[]=array('Id'=>$arregloAux[$num]['Id']);
        }
        $num++;
      }
      $arreglo[]=array('Id'=>$id);
      $msjClass='success';
      $msjIcon='check';
      $msjTxt='Agregado a la cotización';
    }
    $_SESSION['carro']=$arreglo;
    foreach ($arreglo as $key => $value) {
      if (isset($ids)) {
        $ids.=','.$value['Id'];
      }else{
        $ids=$value['Id'];
      }
    }
    $mensaje='{ "msj":"<div class=\'uk-text-center color-blanco bg-'.$msjClass.' padding-10 text-lg\'><i class=\'fa fa-'.$msjIcon.'\'></i> &nbsp; '.$msjTxt.'</div>","xtras":"'.$ids.'" }';
  }

  if (isset($_POST['actualizarcarro'])) {
    $arregloAux=$_SESSION['carro'];
    unset($arreglo);
    $num=0;
    foreach ($arregloAux as $key => $value) {
      if ($id!=$value['Id']) {
        $arreglo[]=array('Id'=>$arregloAux[$num]['Id']);
      }
    $num++;
    }
    $_SESSION['carro']=$arreglo;
  }

// LIMITAR PALABRAS      
  function wordlimit($string, $length , $ellipsis)
  {
    $words = explode(' ', strip_tags($string));
    if (count($words) > $length)
    {
      return implode(' ', array_slice($words, 0, $length)) ." ". $ellipsis;
    }
    else
    {
      return $string;
    }
  }

// FECHA                 
  // FECHA DIA
    function fechaDisplayDia($fechaSQL){
      $fechaSegundos=strtotime($fechaSQL);
      $fechaY=date('Y',$fechaSegundos);
      $fechaM=date('m',$fechaSegundos);
      $fechaD=date('d',$fechaSegundos);
      $fechaDay=strtolower(date('D',$fechaSegundos));

      switch ($fechaDay) {
        case 'mon':
        $fechaDia='Lunes';
        break;
        case 'tue':
        $fechaDia='Martes';
        break;
        case 'wed':
        $fechaDia='Miércoles';
        break;
        case 'thu':
        $fechaDia='Jueves';
        break;
        case 'fri':
        $fechaDia='Viernes';
        break;
        case 'sat':
        $fechaDia='Sábado';
        break;
        default:
        $fechaDia='Domingo';
        break;
      }

      return $fechaDia;
    }

  // FECHA MES
    function fechaDisplayMes($fechaSQL){
      $fechaSegundos=strtotime($fechaSQL);
      $fechaY=date('Y',$fechaSegundos);
      $fechaM=date('m',$fechaSegundos);
      $fechaD=date('d',$fechaSegundos);
      $fechaDay=strtolower(date('D',$fechaSegundos));

      switch ($fechaM) {
        case 1:
        $mes='enero';
        break;
        
        case 2:
        $mes='febrero';
        break;
        
        case 3:
        $mes='marzo';
        break;
        
        case 4:
        $mes='abril';
        break;
        
        case 5:
        $mes='mayo';
        break;
        
        case 6:
        $mes='junio';
        break;
        
        case 7:
        $mes='julio';
        break;
        
        case 8:
        $mes='agosto';
        break;
        
        case 9:
        $mes='septiembre';
        break;
        
        case 10:
        $mes='octubre';
        break;
        
        case 11:
        $mes='noviembre';
        break;
        
        default:
        $mes='diciembre';
        break;
      }

      return $mes;
    }

  // FECHA LARGA
    function fechaDisplay($fechaSQL){
      $fechaSegundos=strtotime($fechaSQL);
      $fechaY=date('Y',$fechaSegundos);
      $fechaM=date('m',$fechaSegundos);
      $fechaD=date('d',$fechaSegundos);
      $fechaDay=strtolower(date('D',$fechaSegundos));

      switch ($fechaM) {
        case 1:
        $mes='enero';
        break;
        
        case 2:
        $mes='febrero';
        break;
        
        case 3:
        $mes='marzo';
        break;
        
        case 4:
        $mes='abril';
        break;
        
        case 5:
        $mes='mayo';
        break;
        
        case 6:
        $mes='junio';
        break;
        
        case 7:
        $mes='julio';
        break;
        
        case 8:
        $mes='agosto';
        break;
        
        case 9:
        $mes='septiembre';
        break;
        
        case 10:
        $mes='octubre';
        break;
        
        case 11:
        $mes='noviembre';
        break;
        
        default:
        $mes='diciembre';
        break;
      }

      switch ($fechaDay) {
        case 'mon':
        $fechaDia='Lunes';
        break;
        case 'tue':
        $fechaDia='Martes';
        break;
        case 'wed':
        $fechaDia='Miércoles';
        break;
        case 'thu':
        $fechaDia='Jueves';
        break;
        case 'fri':
        $fechaDia='Viernes';
        break;
        case 'sat':
        $fechaDia='Sábado';
        break;
        default:
        $fechaDia='Domingo';
        break;
      }

      return $fechaDia.' '.$fechaD.' de '.$mes.' de '.$fechaY;
    }

  // FECHA CORTA
    function fechaCorta($fechaSQL){
      $fechaSegundos=strtotime($fechaSQL);
      $fechaY=date('Y',$fechaSegundos);
      $fechaM=date('m',$fechaSegundos);
      $fechaD=date('d',$fechaSegundos);
      $fechaDay=strtolower(date('D',$fechaSegundos));

      return $fechaD.'-'.$fechaM.'-'.$fechaY;
    }
    
  // FECHA Y HORA
    function fechaHora($fechaSQL){
      $fechaSegundos=strtotime($fechaSQL);
      $fechaY=date('Y',$fechaSegundos);
      $fechaM=date('m',$fechaSegundos);
      $fechaD=date('d',$fechaSegundos);
      $fechaH=date('H',$fechaSegundos);
      $fechaI=date('i',$fechaSegundos);
      $fechaDay=strtolower(date('D',$fechaSegundos));

      return $fechaD.'-'.$fechaM.'-'.$fechaY.'<br>'.$fechaH.':'.$fechaI;
    }

  // FECHA SQL
    function fechaSQL($fechaSQL){
      $fechaSegundos=strtotime($fechaSQL);

      $fechaY=date('Y',$fechaSegundos);
      $fechaM=date('m',$fechaSegundos);
      $fechaD=date('d',$fechaSegundos);
     
      return $fechaY.'/'.$fechaM.'/'.$fechaD;
    }
  
  // FECHA MES COMLETO
    function fechaMesCompleto($fechaSQL){
      $fechaSegundos=strtotime($fechaSQL);

      $fechaY=date('Y',$fechaSegundos);
      $fechaM=date('m',$fechaSegundos);
      $fechaD=date('d',$fechaSegundos);
     
      switch ($fechaM) {
        case 1:
        $mes='enero';
        break;
        
        case 2:
        $mes='febrero';
        break;
        
        case 3:
        $mes='marzo';
        break;
        
        case 4:
        $mes='abril';
        break;
        
        case 5:
        $mes='mayo';
        break;
        
        case 6:
        $mes='junio';
        break;
        
        case 7:
        $mes='julio';
        break;
        
        case 8:
        $mes='agosto';
        break;
        
        case 9:
        $mes='septiembre';
        break;
        
        case 10:
        $mes='octubre';
        break;
        
        case 11:
        $mes='noviembre';
        break;
        
        default:
        $mes='diciembre';
        break;
      }

      return $fechaD.'/'.$mes.'/'.$fechaY;
    }

// CARRUSEL              
  // Carousel Inicio
    function carouselInicio($carousel){
      global $CONEXION;
      global $dominio;

      $CONSULTA= $CONEXION -> query("SELECT * FROM configuracion WHERE id = 1");
      $row_CONSULTA = $CONSULTA -> fetch_assoc();
      switch ($row_CONSULTA['slideranim']) {
        case 0:
          $animation='fade';
          break;
        case 1:
          $animation='slide';
          break;
        case 2:
          $animation='scale';
          break;
        case 3:
          $animation='pull';
          break;
        case 4:
          $animation='push';
          break;
        default:
          $animation='fade';
          break;
      }
      $CAROUSEL = $CONEXION -> query("SELECT * FROM $carousel ORDER BY orden");
      $numPics=$CAROUSEL->num_rows;
      if ($numPics>0) {
        echo '
            <!-- Start Carousel -->
            <div uk-slideshow="autoplay:true;ratio:'.$row_CONSULTA['sliderproporcion'].';animation:'.$animation.';min-height:'.$row_CONSULTA['sliderhmin'].';max-height:'.$row_CONSULTA['sliderhmax'].';" class="uk-grid-collapse" uk-grid>
              <div class="uk-visible-toggle uk-width-1-1 uk-flex-first">
                <div class="uk-position-relative">
                  
                    <div class="uk-position-bottom-center" style="z-index:20; margin-bottom:15px;">
                        <ul class="uk-slideshow-nav uk-dotnav"></ul>
                    </div>

                  <ul class="uk-slideshow-items">';
                    $num=0;
                    $activo=' active';
                    while ($row_CAROUSEL = $CAROUSEL -> fetch_assoc()) {
                      $caption='';
                      if (strlen($row_CAROUSEL['url'])>0) {
                        $pos=strpos($row_CAROUSEL['url'], $dominio);
                        $target=($pos>0)?'':'target="_blank"';
                        if ($row_CONSULTA['slidertextos']==1 AND strlen($row_CAROUSEL['titulo'])>0 AND strlen($row_CAROUSEL['url'])>0) {
                          $caption='
                            <div class="uk-width-auto uk-height-1-1 padding-5 ver-mas">
                                <a href="'.$row_CAROUSEL['url'].'" '.$target.'><button class="uk-button uk-button-default" style="color:white; padding-bottom:1px">'.$row_CAROUSEL['titulo'].'</button></a>
                            </div>
                          ';
                        }
                      }
                      echo '
                          <li>
                            <img src="img/contenido/'.$carousel.'/'.$row_CAROUSEL['id'].'.jpg" uk-cover>
                            '.$caption.'
                          </li>';
                    }

                    echo '
                  </ul>
                </div>

              </div>

            </div>
            <!-- End Carousel -->
            ';
      }else{
        echo '
            <!-- Start Carousel -->
            <div uk-slideshow="autoplay:true;ratio:'.$row_CONSULTA['sliderproporcion'].';animation:'.$animation.';min-height:'.$row_CONSULTA['sliderhmin'].';max-height:'.$row_CONSULTA['sliderhmax'].';" class="uk-grid-collapse" uk-grid>
              <div class="uk-visible-toggle uk-width-1-1 uk-flex-first">
                <div class="uk-position-relative">
                  <ul class="uk-slideshow-items">
                    <li>
                      <img src="img/design/camara.jpg" uk-cover>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <!-- End Carousel -->
            ';
      }
      mysqli_free_result($CAROUSEL);
    }

// Proyecto              
  function proyecto($id){
    global $CONEXION;
    global $caracteres_si_validos;
    global $caracteres_no_validos;

    $CONSULTA1 = $CONEXION -> query("SELECT * FROM productos WHERE id = $id");
    $row_CONSULTA1 = $CONSULTA1 -> fetch_assoc();
    $link=$id.'_'.urlencode(str_replace($caracteres_no_validos,$caracteres_si_validos,html_entity_decode(strtolower($row_CONSULTA1['titulo'])))).'_.html';

    $noPic='img/design/camara.jpg';
    $pic='img/contenido/proyectoslogo/'.$row_CONSULTA1['logo'].'.jpg';
    $picHtml=(file_exists($pic) AND strlen($row_CONSULTA1['logo'])>0)?$pic:$noPic;

    $widget='
    <div>
      <div class="uk-transition-toggle uk-position-relative uk-text-center color-blanco" tabindex="0">
        <div class="uk-cover-container" style="height: 300px;">
          <img src="img/contenido/varios/detalleproyecto_1.jpg" uk-cover>
        </div>
        <div class="uk-position-top uk-transition-slide-bottom-small uk-width-1-1 uk-height-1-1 bg-dark uk-flex uk-flex-center uk-flex-middle">
          <div class="uk-flex uk-flex-center uk-flex-middle" style="width: 100%; height: 100%;">
            <div class="uk-flex uk-flex-center uk-flex-middle">
              <a href="'.$link.'" class="color-blanco">'.$row_CONSULTA1['titulo'].' Producto 1</a>
            </div>
          </div>
        </div>
      </div>
    </div>';

    return $widget;
  }

// Item Inicio           
  function itemInicio($id){
    global $CONEXION;
    global $caracteres_si_validos;
    global $caracteres_no_validos;

    $consulta = $CONEXION -> query("SELECT * FROM productos WHERE id = $id");
    $rowConsulta = $consulta -> fetch_assoc();

    $link=$id.'_'.urlencode(str_replace($caracteres_no_validos,$caracteres_si_validos,html_entity_decode(strtolower($rowConsulta['titulo'])))).'-.html';;

    $picTxt='img/design/camara.jpg';
    $CONSULTAPIC = $CONEXION -> query("SELECT * FROM productospic WHERE producto = $id ORDER BY orden,id LIMIT 1");
    $numProds=$CONSULTAPIC->num_rows;
    while ($rowConsultaPIC = $CONSULTAPIC -> fetch_assoc()) {
      $pic='img/contenido/productos/'.$rowConsultaPIC['id'].'-sm.jpg';
      if(file_exists($pic)){
        $picTxt=$pic;
      }
    }


    $widget='
      <a style="color: #5186d9;" href="'.$link.'">
        <div class="uk-transition-toggle uk-position-relative margin-10" tabindex="0">
          <div class="uk-cover-container" style="height: 300px;">
            <img src="'.$picTxt.'" uk-cover>
          </div>
          <div class="uk-position-bottom-center uk-flex uk-flex-center uk-flex-middle bg-claro padding-h-10" style="min-width: 115px; min-height:50px;">
            '.$rowConsulta['titulo'].'
          </div>
        </div>
      </a>
      ';

    return $widget;
  }

// Item Inicio           
  function catInicio($id){
    global $CONEXION;
    global $caracteres_si_validos;
    global $caracteres_no_validos;

    $consulta = $CONEXION -> query("SELECT * FROM productoscat WHERE id = $id");
    $rowConsulta = $consulta -> fetch_assoc();

    $link=$id.'_'.urlencode(str_replace($caracteres_no_validos,$caracteres_si_validos,html_entity_decode(strtolower($rowConsulta['txt'])))).'_.php';;

    $picTxt='img/design/camara.jpg';
    $pic='img/contenido/productoscat/'.$rowConsulta['imagen'];
    if(file_exists($pic) AND strlen($rowConsulta['imagen'])>0){
      $picTxt=$pic;
    }

    // En caso de tener más de un hijo
    $CatChild = $CONEXION -> query("SELECT * FROM productoscat WHERE parent = $id AND estatus = 1");
    $numChild = $CatChild->num_rows;
    if($numChild==1) {
      $row_CatChild = $CatChild -> fetch_assoc();
      $link=$row_CatChild['id'].'_0_'.urlencode(str_replace($caracteres_no_validos,$caracteres_si_validos,html_entity_decode(strtolower($rowConsulta['txt'])))).'-.php';;
    }

    $widget='
      <a style="color: #5186d9;" href="'.$link.'">
        <div class="uk-transition-toggle uk-position-relative margin-10" tabindex="0">
          <div class="uk-cover-container" style="height: 300px;">
            <img src="'.$picTxt.'" uk-cover>
          </div>
          <div class="uk-position-bottom-center uk-flex uk-flex-center uk-flex-middle bg-claro padding-h-10" style="min-width: 115px; min-height:50px;">
            '.$rowConsulta['txt'].'
          </div>
        </div>
      </a>
      ';

    return $widget;
  }

// ITEM                  
  function item($id){
    global $CONEXION;
    global $caracteres_si_validos;
    global $caracteres_no_validos;

    $productos_cat = $CONEXION -> query("SELECT * FROM productos WHERE id = $id");
    $row_productos_cat = $productos_cat -> fetch_assoc();

    $link=$id.'_'.urlencode(str_replace($caracteres_no_validos,$caracteres_si_validos,html_entity_decode(strtolower(trim($row_productos_cat['titulo']))))).'-.html';;

    $picTxt='img/design/camara.jpg';
    $CONSULTAPIC = $CONEXION -> query("SELECT * FROM productospic WHERE producto = $id ORDER BY orden,id LIMIT 1");
    while ($row_consultaPIC = $CONSULTAPIC -> fetch_assoc()) {
      $pic='img/contenido/productos/'.$row_consultaPIC['id'].'-sm.jpg';
      if(file_exists($pic)){
        $picTxt=$pic;
      }
    }

    $widget = '
      <div class="responsive-center">
        <div class="padding-v-50">
          <div uk-grid class="uk-child-width-1-2@m">
            <div>
              <img src="'.$picTxt.'">
            </div>
            <div>
              <div class="titulo-producto">
                <b>'.$row_productos_cat['titulo'].'</b>
              </div>
              <div class="padding-top-10" style="color: #999;">
                '.wordlimit($row_productos_cat['txt'],20,'...').'
              </div>
              <div class="padding-top-10"  style="color: #1d72a8;">
                Item code: '.$row_productos_cat['titulo1'].'
              </div>
              <div class="padding-top-20">
                <a href="'.$link.'" class="boton-detalles-producto"> Ver detalles</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    ';
    return $widget;
  }

// ITEM  relacionado     
  function itemRel($id){
    global $CONEXION;
    global $caracteres_si_validos;
    global $caracteres_no_validos;

    $productos_cat = $CONEXION -> query("SELECT * FROM productos WHERE id = $id");
    $row_productos_cat = $productos_cat -> fetch_assoc();

    $link=$id.'_'.urlencode(str_replace($caracteres_no_validos,$caracteres_si_validos,html_entity_decode(strtolower(trim($row_productos_cat['titulo']))))).'-.html';;

    $picTxt='img/design/camara.jpg';
    $CONSULTAPIC = $CONEXION -> query("SELECT * FROM productospic WHERE producto = $id ORDER BY orden,id LIMIT 1");
    while ($row_consultaPIC = $CONSULTAPIC -> fetch_assoc()) {
      $pic='img/contenido/productos/'.$row_consultaPIC['id'].'-sm.jpg';
      if(file_exists($pic)){
        $picTxt=$pic;
      }
    }

    $widget = '
      <div>
        <div class="uk-text-center">
          <div class="uk-cover-container" style="height:250px;">
            <img src="'.$picTxt.'" uk-cover>
          </div>
          <div class="titulo-producto">
            <b>'.$row_productos_cat['titulo'].'</b>
          </div>
          <div class="padding-top-10"  style="color: #1d72a8;">
            Item code: '.$row_productos_cat['titulo'].'
          </div>
          <div class="padding-top-20">
            <a href="'.$link.'" class="boton-detalles-producto"> Ver detalles</a>
          </div>
        </div>
      </div>
    ';
    return $widget;
  }

// Search Bar            
  function searchBar(){
    $widget = '
      <div class="uk-text-center uk-flex uk-flex-center uk-flex-middle titulo-categoria">
        <div class="uk-container">
          <div class="uk-width-1-1 font-30">
            Encuentra tu producto 
          </div>
          <div class="uk-width-1-1 padding-v-20">
            o utiliza el scroll para ver nuestros productos
          </div>
          <div class="uk-flex uk-flex-center uk-position-relative div-search">
            <input type="text" class="uk-input" id="search">
            <div class="boton-buscar">
              <button class="uk-button uk-button-search" id="search-button"><i uk-icon="icon: search; ratio: 1.8"></i></button>
            </div>
          </div>
        </div>
      </div>';

    return $widget;
  }




