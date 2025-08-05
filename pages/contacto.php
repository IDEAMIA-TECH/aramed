<!DOCTYPE html>
<html lang="<?=$languaje?>">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#">
  <meta charset="utf-8">
  <title><?=$title?></title>
  <meta name="description" content="<?=$description?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?=$title?>">
  <meta property="og:description" content="<?=$description?>">
  <meta property="og:url" content="<?=$rutaEstaPagina?>">
  <meta property="og:image" content="<?=$ruta?>img/design/logo-og.jpg">
  <meta property="fb:app_id" content="<?=$appID?>">

  <?=$headGNRL?>

  <style>
    /* Estilos modernos para la página de contacto */
    .contact-hero {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 100px 0 60px;
      text-align: center;
    }
    
    .contact-hero h1 {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 1rem;
    }
    
    .contact-hero p {
      font-size: 1.2rem;
      opacity: 0.9;
    }
    
    .contact-section {
      padding: 80px 0;
      background: #f8f9fa;
    }
    
    .contact-form {
      background: white;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      margin-bottom: 40px;
    }
    
    .form-group {
      margin-bottom: 25px;
    }
    
    .form-label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
    }
    
    .form-input {
      width: 100%;
      padding: 15px 20px;
      border: 2px solid #e1e5e9;
      border-radius: 10px;
      font-size: 16px;
      transition: all 0.3s ease;
      background: #f8f9fa;
    }
    
    .form-input:focus {
      outline: none;
      border-color: #667eea;
      background: white;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .form-textarea {
      min-height: 120px;
      resize: vertical;
    }
    
    .btn-submit {
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
      border: none;
      padding: 15px 40px;
      border-radius: 25px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      width: 100%;
    }
    
    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }
    
    .contact-info {
      background: white;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      height: 100%;
    }
    
    .info-item {
      display: flex;
      align-items: center;
      margin-bottom: 30px;
      padding: 20px;
      background: #f8f9fa;
      border-radius: 15px;
      transition: all 0.3s ease;
    }
    
    .info-item:hover {
      transform: translateX(5px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .info-icon {
      width: 50px;
      height: 50px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.2rem;
      margin-right: 20px;
    }
    
    .info-content h3 {
      margin: 0 0 5px 0;
      color: #333;
      font-size: 1.1rem;
    }
    
    .info-content p {
      margin: 0;
      color: #666;
    }
    
    .schedule-section {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 60px 0;
      text-align: center;
    }
    
    .schedule-card {
      background: rgba(255,255,255,0.1);
      border-radius: 15px;
      padding: 30px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.2);
      margin-bottom: 20px;
    }
    
    .schedule-card h3 {
      margin-bottom: 15px;
      font-size: 1.5rem;
    }
    
    .schedule-card p {
      margin: 0;
      opacity: 0.9;
    }
    
    .cta-section {
      background: #f8f9fa;
      padding: 60px 0;
      text-align: center;
    }
    
    .cta-button {
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
      padding: 15px 40px;
      border-radius: 25px;
      text-decoration: none;
      font-weight: 600;
      display: inline-block;
      transition: all 0.3s ease;
      margin: 10px;
    }
    
    .cta-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
      color: white;
      text-decoration: none;
    }
    
    @media (max-width: 768px) {
      .contact-hero h1 {
        font-size: 2rem;
      }
      
      .contact-form,
      .contact-info {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>
  
<?=$header?>

<!-- Hero Section -->
<section class="contact-hero">
  <div class="uk-container">
    <h1>¿Tienes alguna duda?</h1>
    <p>Estamos para servirte y brindarte la mejor atención</p>
  </div>
</section>

<!-- Contact Section -->
<section class="contact-section">
  <div class="uk-container">
    <div uk-grid class="uk-child-width-1-2@m uk-child-width-1-1">
      
      <!-- Contact Form -->
      <div>
        <div class="contact-form">
          <h2 style="margin-bottom: 30px; color: #333; text-align: center;">Envíanos un mensaje</h2>
          
          <form id="contactForm">
            <div uk-grid class="uk-child-width-1-2@s uk-child-width-1-1">
              <div>
                <div class="form-group">
                  <label class="form-label">Nombre *</label>
                  <input type="text" id="footernombre" class="form-input" placeholder="Tu nombre" required>
                </div>
              </div>
              <div>
                <div class="form-group">
                  <label class="form-label">Apellido *</label>
                  <input type="text" id="footerapellido" class="form-input" placeholder="Tu apellido" required>
                </div>
              </div>
            </div>
            
            <div uk-grid class="uk-child-width-1-2@s uk-child-width-1-1">
              <div>
                <div class="form-group">
                  <label class="form-label">Email *</label>
                  <input type="email" id="footeremail" class="form-input" placeholder="tu@email.com" required>
                </div>
              </div>
              <div>
                <div class="form-group">
                  <label class="form-label">Teléfono *</label>
                  <input type="tel" id="footertelefono" class="form-input" placeholder="Tu teléfono" required>
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <label class="form-label">Mensaje *</label>
              <textarea id="footercomentarios" class="form-input form-textarea" placeholder="Escribe tu mensaje aquí..." required></textarea>
            </div>
            
            <button type="submit" class="btn-submit" id="footersend">
              <i class="fas fa-paper-plane" style="margin-right: 10px;"></i>Enviar Mensaje
            </button>
          </form>
        </div>
      </div>
      
      <!-- Contact Info -->
      <div>
        <div class="contact-info">
          <h2 style="margin-bottom: 30px; color: #333; text-align: center;">Información de Contacto</h2>
          
          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-phone"></i>
            </div>
            <div class="info-content">
              <h3>Teléfono</h3>
              <p><?=$telefono?></p>
            </div>
          </div>
          
          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-envelope"></i>
            </div>
            <div class="info-content">
              <h3>Email</h3>
              <p>info@aramed.com</p>
            </div>
          </div>
          
          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="info-content">
              <h3>Dirección</h3>
              <p>Ciudad de México, México</p>
            </div>
          </div>
          
          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-clock"></i>
            </div>
            <div class="info-content">
              <h3>Horario</h3>
              <p>Lun - Vie: 9:00 AM - 7:00 PM<br>Sábados: 10:00 AM - 2:00 PM</p>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</section>

<!-- Schedule Section -->
<section class="schedule-section">
  <div class="uk-container">
    <h2 style="margin-bottom: 40px; text-align: center;">Horario de Oficinas</h2>
    
    <div uk-grid class="uk-child-width-1-2@m uk-child-width-1-1">
      <div>
        <div class="schedule-card">
          <h3>Días Laborales</h3>
          <p>Lunes - Viernes<br>9:00 AM - 7:00 PM</p>
        </div>
      </div>
      <div>
        <div class="schedule-card">
          <h3>Fines de Semana</h3>
          <p>Sábados<br>10:00 AM - 2:00 PM</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
  <div class="uk-container">
    <h2 style="margin-bottom: 30px; color: #333;">¿Necesitas una cotización?</h2>
    <p style="font-size: 1.2rem; margin-bottom: 30px; color: #666;">Obtén una cotización personalizada para tus necesidades</p>
    
    <a href="<?=$ruta?>cotizar.php" class="cta-button">
      <i class="fas fa-calculator" style="margin-right: 10px;"></i>Cotizar en Línea
    </a>
    
    <a href="tel:<?=$telefono?>" class="cta-button" style="background: linear-gradient(135deg, #28a745, #20c997);">
      <i class="fas fa-phone" style="margin-right: 10px;"></i>Llamar Ahora
    </a>
  </div>
</section>

<?=$footer?>

<?=$scriptGNRL?>

<script>
// Mejorar la funcionalidad del formulario de contacto
$(document).ready(function() {
  $('#contactForm').on('submit', function(e) {
    e.preventDefault();
    
    // Validación básica
    var nombre = $('#footernombre').val();
    var apellido = $('#footerapellido').val();
    var email = $('#footeremail').val();
    var telefono = $('#footertelefono').val();
    var comentarios = $('#footercomentarios').val();
    
    if (!nombre || !apellido || !email || !telefono || !comentarios) {
      UIkit.notification({
        message: '<div class="uk-text-center color-blanco bg-danger padding-10 text-lg"><i class="fa fa-exclamation-triangle fa-lg"></i> &nbsp; Por favor completa todos los campos requeridos</div>',
        status: 'danger',
        pos: 'top-center'
      });
      return;
    }
    
    // Validación de email
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      UIkit.notification({
        message: '<div class="uk-text-center color-blanco bg-danger padding-10 text-lg"><i class="fa fa-exclamation-triangle fa-lg"></i> &nbsp; Por favor ingresa un email válido</div>',
        status: 'danger',
        pos: 'top-center'
      });
      return;
    }
    
    // Enviar formulario
    $('#footersend').html('<i class="fas fa-spinner fa-spin" style="margin-right: 10px;"></i>Enviando...');
    $('#footersend').prop('disabled', true);
    
    $.ajax({
      data: {
        "enviarcontacto": 1,
        "nombre": nombre,
        "apellido": apellido,
        "email": email,
        "telefono": telefono,
        "comentarios": comentarios
      },
      url: "includes/acciones.php",
      type: "post",
      success: function(response) {
        $('#footersend').html('<i class="fas fa-paper-plane" style="margin-right: 10px;"></i>Enviar Mensaje');
        $('#footersend').prop('disabled', false);
        
        try {
          var datos = JSON.parse(response);
          UIkit.notification({
            message: datos.msj,
            status: datos.estatus == 0 ? 'success' : 'danger',
            pos: 'top-center'
          });
          
          if (datos.estatus == 0) {
            $('#contactForm')[0].reset();
          }
        } catch(e) {
          UIkit.notification({
            message: '<div class="uk-text-center color-blanco bg-danger padding-10 text-lg"><i class="fa fa-exclamation-triangle fa-lg"></i> &nbsp; Error al procesar la solicitud</div>',
            status: 'danger',
            pos: 'top-center'
          });
        }
      },
      error: function() {
        $('#footersend').html('<i class="fas fa-paper-plane" style="margin-right: 10px;"></i>Enviar Mensaje');
        $('#footersend').prop('disabled', false);
        
        UIkit.notification({
          message: '<div class="uk-text-center color-blanco bg-danger padding-10 text-lg"><i class="fa fa-exclamation-triangle fa-lg"></i> &nbsp; Error de conexión</div>',
          status: 'danger',
          pos: 'top-center'
        });
      }
    });
  });
});
</script>

</body>
</html>