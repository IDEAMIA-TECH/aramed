<?=$head?>

<div class="uk-flex uk-flex-center">
  <div style="width:300px;">
    <div class="uk-panel uk-panel-box">

      <div class="uk-text-center uk-width-1-1">
        <img src="../img/design/logo.png" class="margen-top-50 margen-bottom-50" style="max-width:250px">
      </div>

      <form action="index.php" method="post" name="login">
        
        <div class="uk-inline uk-width-1-1">
          <span class="uk-form-icon uk-form-icon-flip" href="" uk-icon="icon: user"></span>
          <input name="user" id="user" type="text" class="uk-input uk-margin uk-width-1-1 uk-form-large" autofocus>
        </div>
        
        <div class="uk-inline uk-width-1-1">
          <span class="uk-form-icon uk-form-icon-flip" href="" uk-icon="icon: lock"></span>
          <input name="pass" id="pass" type="password" class="pass uk-input uk-margin uk-width-1-1 uk-form-large" placeholder="Contraseña">
        </div>

        <span class="password-revelar uk-margin uk-text-muted">Revelar contraseña</span>
        <span class="password-ocultar uk-hidden uk-margin uk-text-muted">Ocultar contraseña</span>
        
        <button id="send" class="uk-width-1-1 uk-margin  uk-button uk-button-primary uk-button-large">Entrar</button>
      
      </form>

    </div>
  </div>
</div>

<?=$jquery?>

<script src="../js/form-password.js"></script>

<script>
  $('#send').click(function(){
    $(this).html("Espere...");
    setTimeout(function(){ 
      $(this).html("Espere...");
    }, 3000);
  });
</script>

<?=$footer?>
