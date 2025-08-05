<?php
$pdf='img/contenido/productospdf/'.$rowCONSULTA['pdf'];
$ficha=(strlen($rowCONSULTA['pdf'])>0 AND file_exists($pdf))?'<a href="'.$pdf.'" target="_blank" class="boton-det-pro" style="margin-right: 20px;">FICHA TÉCNICA</a>':'';
$video=(strlen($rowCONSULTA['video'])>0)?'<span uk-lightbox><a href="'.$rowCONSULTA['video'].'" class="boton-det-pro" style="margin-right: 20px;">VIDEO</a></span>':'';
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
  <meta property="og:image" content="<?=$ruta.$picOg?>">
  <meta property="fb:app_id" content="<?=$appID?>">

  <?=$headGNRL?>

</head>
<body>
  
<?=$header?>

<div class="uk-text-center titulo-detalle">
  <b><?=$rowCONSULTA['titulo']?></b>
</div>

<div class="container-detalle">
  <div class="uk-container">
    <div uk-grid class="uk-child-width-1-2@m padding-top-40">
      <div>
        <div uk-lightbox>
          <a href="<?=$picOg?>">
            <div class="uk-width-1-1 uk-cover-container detalle-producto">
              <img src="<?=$picOg?>" uk-cover>
            </div>
          </a>
          <div uk-grid class="uk-child-width-1-4 uk-flex-center padding-v-20 uk-hidden@m" style="margin-left: -15px;">
<?php
while($row_consultaPIC = $CONSULTAPIC -> fetch_assoc()){
  $picSm='img/design/camara.jpg';
  $pic='img/contenido/productos/'.$row_consultaPIC['id'].'-sm.jpg';
  $picLg='img/contenido/productos/'.$row_consultaPIC['id'].'-lg.jpg';
  if(file_exists($pic)){
    $picSm=$pic;
  }
  $pics[]=$picSm;
  $picsLgArray[]=$picLg;
  echo '
          <div>
            <a href="'.$picLg.'">
              <div class="uk-cover-container uk-width-1-1 pic-sq">
                <img src="'.$picSm.'" uk-cover>
              </div>
            </a>
          </div>';
}
?>
          </div>
        </div>
      </div>
      <div class="padding-bottom-20">
        <div class="titulo-detalle-2">
          <b><?=$rowCONSULTA['titulo1']?></b>
        </div>
        <div class="uk-text-justify">
          <?=$rowCONSULTA['txt']?>
          <!--<span class="uk-text-muted">Marca:</span>-->
          <?php
            $marcaId=$rowCONSULTA['marca'];
            $CONSULTAX = $CONEXION -> query("SELECT * FROM empresas WHERE id = $marcaId");
            while ($row_CONSULTAX = $CONSULTAX -> fetch_assoc()) {
              //echo $row_CONSULTAX['titulo'].'<br><br>';

              $pic='img/contenido/empresas/'.$row_CONSULTAX['id'].'.png';
              if(file_exists($pic)){
                echo '<img src="'.$pic.'" alt="'.$row_CONSULTAX['titulo'].'" class="padding-v-20">';
              }
            }
          ?>
        </div>
        <div class="uk-flex uk-flex-center padding-v-20">
          <div>
            <?=$video?>
            <?=$ficha?>
            <a href="#cotizamodal" uk-toggle class="boton-det-pro buybutton" data-id="<?=$id?>">PEDIR COTIZACIÓN</a>
          </div>
        </div>
      </div>
    </div>
    <div uk-grid class="uk-child-width-1-4@l uk-child-width-1-3@m uk-child-width-1-2@s padding-bottom-50 uk-visible@m" uk-lightbox>
<?php
if (isset($pics)) {
  foreach ($pics as $key => $value) {
    echo '
      <div>
        <a href="'.$picsLgArray[$key].'">
          <div class="uk-cover-container uk-width-1-1 " style="height:270px;">
            <img src="'.$value.'" uk-cover>
          </div>
        </a>
      </div>';
  }
  echo '<a href="'.$picOg.'"></a>';
}
?>
    </div>
  </div>
</div>

<div class="uk-text-center titulo-interes" >
  Otros productos que pueden ser de tu interés
</div>

<div class="uk-container uk-container-large padding-v-20">
  <div uk-slider="autoplay:true;sets: true ">
    <ul class="uk-slider-items uk-child-width-1-1 uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l uk-grid" style="margin-left: 0px; height: 420px;">
<?php
if (strlen($rowCONSULTA['rel']>0)) {
  $array=explode(',', $rowCONSULTA['rel']);
  foreach ($array as $key => $value) {
    if ($id!=$value){
      $consultaVerify = $CONEXION -> query("SELECT * FROM productos WHERE id = $value AND estatus = 1");
      $numItems=$consultaVerify->num_rows;
      if ($numItems>0) {
        echo '
        <li>
          '.itemRel($value).'
        </li>';
      } 
    }
  }
}
?>

    </ul>
    <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
  </div>
</div>

<!-- This is the modal with the default close button -->
<div id="cotizamodal" uk-modal class="modal uk-modal-container">
  <div class="uk-modal-dialog uk-modal-body">
    <button class="uk-modal-close-default" type="button" uk-close></button>

    <div style="color: #37629c; font-size: 40px;">
      <b>Cotización</b>
    </div>
    
    <div class="uk-container" style="max-width:400px;">
      <div class="uk-text-center" style="color: #37629c; font-size: 30px;">
        <b>Productos</b>
      </div>
      <ul class="uk-list uk-list-striped" id="productoscotizar">
        <li class="uk-flex uk-flex-center uk-flex-middle uk-height-small"><div uk-spinner></div></li>
      </ul>
      <div class="uk-text-center">
        <button class="uk-modal-close uk-button uk-button-personal" type="button">Agregar más productos</button>
      </div>
    </div>
    
    <div class="padding-v-20">
      Debido al cuidado de nuestra información, te pedimos nos ayudes llenando estos datos para poder brindarte la información solicitada.
    </div>

    <div class="uk-text-center" style="color: #37629c; font-size: 30px;">
      <b>Información de contacto</b>
    </div>
    <div uk-grid class="uk-grid-small" style="color: #37629c;">
      <div class="uk-width-1-2@s">
        INSTITUCION <span style="color: red;">*</span>
        <input id="cotizarinstitucion" class="uk-input" type="text">
      </div>
      <div class="uk-width-1-2@s">
        ESCUELA/FACULTAD <span style="color: red;">*</span>
        <input id="cotizarescuela" class="uk-input" type="text">
      </div>
      <div class="uk-width-1-2@s">
        CAMPUS <span style="color: red;">*</span>
        <input id="cotizarcampus" class="uk-input" type="text">
      </div>
      <div class="uk-width-1-2@s">
        CARGO <span style="color: red;">*</span>
        <input id="cotizarcargo" class="uk-input" type="text">
      </div>
      <div class="uk-width-1-2@s">
        NOMBRE <span style="color: red;">*</span>
        <input id="cotizarnombre" class="uk-input" type="text">
      </div>
      <div class="uk-width-1-2@s">
        CORREO ELECTRONICO <span style="color: red;">*</span>
        <input id="cotizaremail" class="uk-input" type="text">
      </div>
      <div class="uk-width-1-3@s">
        TELEFONO <span style="color: red;">*</span>
        <input id="cotizartelefono" class="uk-input" type="text">
      </div>
      <div class="uk-width-1-3@s">
        EXT
        <input id="cotizarext" class="uk-input" type="text">
      </div>
      <div class="uk-width-1-3@s">
        CIUDAD
        <input id="cotizarciudad" class="uk-input" type="text">
      </div>
      <div class="uk-width-1-1">
        COMENTARIOS
        <textarea id="cotizarcomentarios" class="textarea-personal uk-width-1-1"></textarea>
      </div>
    </div>
    <div class="uk-text-center">
      <button class="uk-button uk-button-personal" id="cotizarsend">ENVIAR</button>
    </div>

  </div>
</div>


<?=$footer?>

<?=$scriptGNRL?>


<script>
  // Agregar al carro                  
    $(".buybutton").click(function(){
      var id=$(this).attr("data-id");
      $.post("addtocart",
      {
        addtocart: 1,
        id: id
      },
      function( response ){
        console.log( response );
        datos = JSON.parse(response);
        UIkit.notification.closeAll();
        if (datos.estatus==0) {
          //UIkit.notification(datos.msj);
          $("#cotizacion-fixed").removeClass("uk-hidden");
        }else{
          //UIkit.notification(datos.msj);
        }
      });
      setTimeout(function(){
        cargarProductos();
      },500);
    });

  // Cargar información del producto   
    function cargarProductos(){
      $.post("includes/acciones.php",
      {
        cargarproductos: 1
      },
      function( response ){
        console.log( response );
        datos = JSON.parse(response);
        UIkit.notification.closeAll();
        if (datos.estatus==0) {
          $("#productoscotizar").html(datos.xtras);
        }else{
          UIkit.notification(datos.msj);
        }
      });
    }

  // Enviar correo de cotización.      
    $("#cotizarsend").click(function(){
      var institucion = $("#cotizarinstitucion").val();
      var escuela = $("#cotizarescuela").val();
      var campus = $("#cotizarcampus").val();
      var cargo = $("#cotizarcargo").val();
      var nombre = $("#cotizarnombre").val();
      var email = $("#cotizaremail").val();
      var telefono = $("#cotizartelefono").val();
      var ext = $("#cotizarext").val();
      var ciudad = $("#cotizarciudad").val();
      var comentarios = $("#cotizarcomentarios").val();

      var fallo = 0;
      var alerta = "";
      
      $("input").removeClass("uk-form-danger");
      
      if (telefono=="") { fallo=1; alerta="Falta telefono"; id="cotizartelefono"; }

      if (email=="") { 
        fallo=1; alerta="Falta email"; id="cotizaremail";
      }else{
        var n = email.indexOf("@");
        var l = email.indexOf(".");
        if ((n*l)<2) { 
          fallo=1; alerta="Proporcione un email válido"; id="cotizaremail";
        } 
      }

      if (nombre=="") { fallo=1; alerta="Falta nombre"; id="cotizarnombre"; }
      if (cargo=="") { fallo=1; alerta="Falta cargo"; id="cotizarcargo"; }
      if (campus=="") { fallo=1; alerta="Falta campus"; id="cotizarcampus"; }
      if (escuela=="") { fallo=1; alerta="Falta escuela"; id="cotizarescuela"; }
      if (institucion=="") { fallo=1; alerta="Falta institucion"; id="cotizarinstitucion"; }

      var parametros = {
        "enviarcotizacion" : 1,
        "institucion" : institucion,
        "escuela" : escuela,
        "campus" : campus,
        "cargo" : cargo,
        "nombre" : nombre,
        "email" : email,
        "telefono" : telefono,
        "ext" : ext,
        "ciudad" : ciudad,
        "comentarios" : comentarios
      };
      if (fallo == 0) {
        $.ajax({
          data:  parametros,
          url:   "includes/acciones.php",
          type:  "post",
          beforeSend: function () {
            $("#cotizarsend").html("<div uk-spinner></div>");
            $("#cotizarsend").prop("disabled",true);
            $("#cotizarsend").disabled = true;
            UIkit.notification.closeAll();
            UIkit.notification('<div class="uk-text-center color-blanco bg-blue padding-10 text-lg"><i  uk-spinner></i> Espere...</div>');
          },
          success:  function (response) {
            $("#cotizarsend").html("Volver a enviar");
            $("#cotizarsend").disabled = false;
            $("#cotizarsend").prop("disabled",false);
            console.log( response );
            datos = JSON.parse(response);
            UIkit.notification.closeAll();
            UIkit.notification(datos.msj);
            setTimeout(function(){
              location.reload();
            },2000);
          }
        })
      }else{
        UIkit.notification.closeAll();
        console.log(id);
        UIkit.notification('<div class="uk-text-center color-blanco bg-danger padding-10 text-lg"><i class="fa fa-exclamation-triangle fa-lg"></i> &nbsp; '+alerta+'</div>');
        $("#"+id).focus();
        $("#"+id).addClass("uk-form-danger");
      }
    });

  // Vaciar el carro                   
    $(".emptycart").click(function(){
      $.post("emptycart",
      {
        emptycart: 1
      })
      .done(function() {
        location.reload();
      });
    })

  // Quitar del carro                  
    function quitar(id){
      $("#producto"+id).fadeOut();
      $.ajax({
        method: "POST",
        url: "addtocart",
        data: { 
          id: id,
          removefromcart: 1
        }
      })
      .done(function( response ) {
        console.log( response );
      });
    }

</script>

</body>
</html>