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
    /* Estilos modernos para la página de cotización */
    .quote-hero {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 100px 0 60px;
      text-align: center;
    }
    
    .quote-hero h1 {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 1rem;
    }
    
    .quote-hero p {
      font-size: 1.2rem;
      opacity: 0.9;
      max-width: 600px;
      margin: 0 auto;
    }
    
    .quote-section {
      padding: 80px 0;
      background: #f8f9fa;
    }
    
    .quote-form {
      background: white;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      margin-bottom: 40px;
    }
    
    .form-section {
      margin-bottom: 40px;
    }
    
    .section-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: #333;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid #e1e5e9;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
    }
    
    .form-label.required::after {
      content: ' *';
      color: #e74c3c;
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
    
    .products-section {
      background: white;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .product-category {
      background: #f8f9fa;
      border-radius: 15px;
      padding: 30px;
      margin-bottom: 30px;
      border-left: 4px solid #667eea;
    }
    
    .category-title {
      font-size: 1.3rem;
      font-weight: 600;
      color: #333;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
    }
    
    .category-title i {
      margin-right: 10px;
      color: #667eea;
    }
    
    .checkbox-group {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 15px;
      margin-top: 20px;
    }
    
    .checkbox-item {
      display: flex;
      align-items: center;
      padding: 10px;
      background: white;
      border-radius: 8px;
      border: 1px solid #e1e5e9;
      transition: all 0.3s ease;
    }
    
    .checkbox-item:hover {
      border-color: #667eea;
      box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
    }
    
    .checkbox-item input[type="checkbox"] {
      margin-right: 10px;
      transform: scale(1.2);
    }
    
    .checkbox-item label {
      margin: 0;
      cursor: pointer;
      font-weight: 500;
    }
    
    .subcategory {
      margin-left: 30px;
      margin-top: 10px;
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
      margin-top: 20px;
    }
    
    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }
    
    .progress-bar {
      background: #e1e5e9;
      border-radius: 10px;
      height: 8px;
      margin: 30px 0;
      overflow: hidden;
    }
    
    .progress-fill {
      background: linear-gradient(135deg, #667eea, #764ba2);
      height: 100%;
      width: 0%;
      transition: width 0.3s ease;
    }
    
    .step-indicator {
      display: flex;
      justify-content: space-between;
      margin-bottom: 30px;
    }
    
    .step {
      display: flex;
      flex-direction: column;
      align-items: center;
      flex: 1;
    }
    
    .step-number {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #e1e5e9;
      color: #666;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      margin-bottom: 10px;
      transition: all 0.3s ease;
    }
    
    .step.active .step-number {
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
    }
    
    .step.completed .step-number {
      background: #28a745;
      color: white;
    }
    
    .step-label {
      font-size: 0.9rem;
      color: #666;
      text-align: center;
    }
    
    .step.active .step-label {
      color: #667eea;
      font-weight: 600;
    }
    
    @media (max-width: 768px) {
      .quote-hero h1 {
        font-size: 2rem;
      }
      
      .quote-form,
      .products-section {
        padding: 30px 20px;
      }
      
      .checkbox-group {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  
<?=$header?>

<!-- Hero Section -->
<section class="quote-hero">
  <div class="uk-container">
    <h1>Solicita tu Cotización</h1>
    <p>Completa el formulario y te enviaremos una cotización personalizada para tus necesidades de simuladores médicos</p>
  </div>
</section>

<!-- Quote Section -->
<section class="quote-section">
  <div class="uk-container">
    
    <!-- Progress Bar -->
    <div class="step-indicator">
      <div class="step active">
        <div class="step-number">1</div>
        <div class="step-label">Información</div>
      </div>
      <div class="step">
        <div class="step-number">2</div>
        <div class="step-label">Productos</div>
      </div>
      <div class="step">
        <div class="step-number">3</div>
        <div class="step-label">Enviar</div>
      </div>
    </div>
    
    <div class="progress-bar">
      <div class="progress-fill" id="progressFill"></div>
    </div>
    
    <form id="quoteForm">
      
      <!-- Información de Contacto -->
      <div class="quote-form" id="step1">
        <div class="form-section">
          <h2 class="section-title">
            <i class="fas fa-user"></i> Información de Contacto
          </h2>
          
          <div uk-grid class="uk-child-width-1-2@s uk-child-width-1-1">
            <div>
              <div class="form-group">
                <label class="form-label required">Institución</label>
                <input type="text" name="institucion" class="form-input" placeholder="Nombre de la institución" required>
              </div>
            </div>
            <div>
              <div class="form-group">
                <label class="form-label required">Escuela/Facultad</label>
                <input type="text" name="escuela" class="form-input" placeholder="Escuela o facultad" required>
              </div>
            </div>
            <div>
              <div class="form-group">
                <label class="form-label required">Campus</label>
                <input type="text" name="campus" class="form-input" placeholder="Campus" required>
              </div>
            </div>
            <div>
              <div class="form-group">
                <label class="form-label required">Cargo</label>
                <input type="text" name="cargo" class="form-input" placeholder="Tu cargo o puesto" required>
              </div>
            </div>
            <div>
              <div class="form-group">
                <label class="form-label required">Nombre</label>
                <input type="text" name="nombre" class="form-input" placeholder="Tu nombre completo" required>
              </div>
            </div>
            <div>
              <div class="form-group">
                <label class="form-label required">Correo Electrónico</label>
                <input type="email" name="email" class="form-input" placeholder="tu@email.com" required>
              </div>
            </div>
            <div>
              <div class="form-group">
                <label class="form-label required">Teléfono</label>
                <input type="tel" name="telefono" class="form-input" placeholder="Tu teléfono" required>
              </div>
            </div>
            <div>
              <div class="form-group">
                <label class="form-label">Extensión</label>
                <input type="text" name="ext" class="form-input" placeholder="Extensión (opcional)">
              </div>
            </div>
            <div>
              <div class="form-group">
                <label class="form-label">Ciudad</label>
                <input type="text" name="ciudad" class="form-input" placeholder="Ciudad">
              </div>
            </div>
          </div>
        </div>
        
        <div class="uk-text-right">
          <button type="button" class="btn-submit" onclick="nextStep()">
            Siguiente <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
          </button>
        </div>
      </div>
      
      <!-- Productos de Interés -->
      <div class="products-section" id="step2" style="display: none;">
        <div class="form-section">
          <h2 class="section-title">
            <i class="fas fa-medical-kit"></i> Productos de tu Interés
          </h2>
          
          <!-- Simuladores -->
          <div class="product-category">
            <h3 class="category-title">
              <i class="fas fa-user-md"></i> Simuladores
            </h3>
            <div class="checkbox-group">
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="habilidades_medicas" id="habilidades">
                <label for="habilidades">Habilidades Médicas</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="obstetricia" id="obstetricia">
                <label for="obstetricia">Obstetricia</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="neonatologia" id="neonatologia">
                <label for="neonatologia">Neonatología</label>
              </div>
              <div class="checkbox-item subcategory">
                <input type="checkbox" name="productos[]" value="recien_nacidos" id="recien_nacidos">
                <label for="recien_nacidos">Recién Nacidos</label>
              </div>
              <div class="checkbox-item subcategory">
                <input type="checkbox" name="productos[]" value="prematuros" id="prematuros">
                <label for="prematuros">Prematuros</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="pediatria" id="pediatria">
                <label for="pediatria">Pediatría</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="emergencia" id="emergencia">
                <label for="emergencia">Emergencia/Soporte de vida avanzado</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="trauma" id="trauma">
                <label for="trauma">Trauma</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="medica_tactica" id="medica_tactica">
                <label for="medica_tactica">Médica Táctica</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="cirugia" id="cirugia">
                <label for="cirugia">Cirugía</label>
              </div>
              <div class="checkbox-item subcategory">
                <input type="checkbox" name="productos[]" value="cirugia_general" id="cirugia_general">
                <label for="cirugia_general">Cirugía General</label>
              </div>
              <div class="checkbox-item subcategory">
                <input type="checkbox" name="productos[]" value="laparoscopia" id="laparoscopia">
                <label for="laparoscopia">Laparoscopia / Endoscopia</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="imagenologia" id="imagenologia">
                <label for="imagenologia">Imagenología</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="realidad_virtual" id="realidad_virtual">
                <label for="realidad_virtual">Realidad Virtual</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="mesa_diseccion" id="mesa_diseccion">
                <label for="mesa_diseccion">Mesa Disección Virtual</label>
              </div>
            </div>
          </div>
          
          <!-- Modelos Anatómicos -->
          <div class="product-category">
            <h3 class="category-title">
              <i class="fas fa-bone"></i> Modelos Anatómicos
            </h3>
            <div class="checkbox-group">
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="modelos_articulaciones" id="articulaciones">
                <label for="articulaciones">Modelos Articulaciones</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="modelos_corazon" id="corazon">
                <label for="corazon">Modelos de Corazón</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="modelos_huesos" id="huesos">
                <label for="huesos">Modelos Huesos/Esqueleto</label>
              </div>
              <div class="checkbox-item subcategory">
                <input type="checkbox" name="productos[]" value="vertebras" id="vertebras">
                <label for="vertebras">Vértebras</label>
              </div>
              <div class="checkbox-item subcategory">
                <input type="checkbox" name="productos[]" value="columna" id="columna">
                <label for="columna">Columna Vertebral</label>
              </div>
              <div class="checkbox-item subcategory">
                <input type="checkbox" name="productos[]" value="craneo" id="craneo">
                <label for="craneo">Cráneo</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="modelos_musculatura" id="musculatura">
                <label for="musculatura">Modelos de Musculatura</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="modelos_torso" id="torso">
                <label for="torso">Modelos de Torso</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="modelos_piel" id="piel">
                <label for="piel">Modelos de Piel</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="modelos_cabeza" id="cabeza">
                <label for="cabeza">Modelos de Cabeza y Encéfalo</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="modelos_nervioso" id="nervioso">
                <label for="nervioso">Modelos de Sistema Nervioso</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="modelos_ojos" id="ojos">
                <label for="ojos">Modelos de Ojos</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="modelos_oreja" id="oreja">
                <label for="oreja">Modelos de Oreja/Nariz/Garganta</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="modelos_dientes" id="dientes">
                <label for="dientes">Modelos de Dientes</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="modelos_pulmon" id="pulmon">
                <label for="pulmon">Modelos de Pulmón</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="modelos_digestivo" id="digestivo">
                <label for="digestivo">Modelos Sistema Digestivo/Urinario</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="modelos_sexual" id="sexual">
                <label for="sexual">Modelos de Educación Sexual</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="modelos_gestacion" id="gestacion">
                <label for="gestacion">Modelos de Gestación y Parto</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="productos[]" value="modelos_diabetes" id="diabetes">
                <label for="diabetes">Modelos de Diabetes</label>
              </div>
            </div>
          </div>
        </div>
        
        <div class="uk-text-right">
          <button type="button" class="btn-submit" style="background: #6c757d; margin-right: 10px;" onclick="prevStep()">
            <i class="fas fa-arrow-left" style="margin-right: 10px;"></i> Anterior
          </button>
          <button type="button" class="btn-submit" onclick="nextStep()">
            Siguiente <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
          </button>
        </div>
      </div>
      
      <!-- Comentarios y Envío -->
      <div class="quote-form" id="step3" style="display: none;">
        <div class="form-section">
          <h2 class="section-title">
            <i class="fas fa-comments"></i> Comentarios Adicionales
          </h2>
          
          <div class="form-group">
            <label class="form-label">Comentarios</label>
            <textarea name="comentarios" class="form-input form-textarea" placeholder="Describe tus necesidades específicas, presupuesto, fechas de entrega, o cualquier información adicional que consideres importante..."></textarea>
          </div>
          
          <div class="uk-alert uk-alert-info">
            <p><i class="fas fa-info-circle"></i> <strong>Importante:</strong> Una vez enviado el formulario, nuestro equipo se pondrá en contacto contigo en un máximo de 24 horas hábiles para brindarte una cotización personalizada.</p>
          </div>
        </div>
        
        <div class="uk-text-right">
          <button type="button" class="btn-submit" style="background: #6c757d; margin-right: 10px;" onclick="prevStep()">
            <i class="fas fa-arrow-left" style="margin-right: 10px;"></i> Anterior
          </button>
          <button type="submit" class="btn-submit">
            <i class="fas fa-paper-plane" style="margin-right: 10px;"></i> Enviar Cotización
          </button>
        </div>
      </div>
      
    </form>
  </div>
</section>

<?=$footer?>

<?=$scriptGNRL?>

<script>
let currentStep = 1;
const totalSteps = 3;

function updateProgress() {
  const progress = (currentStep / totalSteps) * 100;
  document.getElementById('progressFill').style.width = progress + '%';
  
  // Update step indicators
  document.querySelectorAll('.step').forEach((step, index) => {
    step.classList.remove('active', 'completed');
    if (index + 1 < currentStep) {
      step.classList.add('completed');
    } else if (index + 1 === currentStep) {
      step.classList.add('active');
    }
  });
}

function showStep(step) {
  document.querySelectorAll('[id^="step"]').forEach(el => {
    el.style.display = 'none';
  });
  document.getElementById('step' + step).style.display = 'block';
  updateProgress();
}

function nextStep() {
  if (currentStep < totalSteps) {
    currentStep++;
    showStep(currentStep);
  }
}

function prevStep() {
  if (currentStep > 1) {
    currentStep--;
    showStep(currentStep);
  }
}

// Form validation and submission
$(document).ready(function() {
  $('#quoteForm').on('submit', function(e) {
    e.preventDefault();
    
    // Validate required fields
    const requiredFields = ['institucion', 'escuela', 'campus', 'cargo', 'nombre', 'email', 'telefono'];
    let isValid = true;
    
    requiredFields.forEach(field => {
      const value = $(`[name="${field}"]`).val();
      if (!value) {
        isValid = false;
        $(`[name="${field}"]`).addClass('uk-form-danger');
      } else {
        $(`[name="${field}"]`).removeClass('uk-form-danger');
      }
    });
    
    // Validate email
    const email = $('[name="email"]').val();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email && !emailRegex.test(email)) {
      isValid = false;
      $('[name="email"]').addClass('uk-form-danger');
      UIkit.notification({
        message: '<div class="uk-text-center color-blanco bg-danger padding-10 text-lg"><i class="fa fa-exclamation-triangle fa-lg"></i> &nbsp; Por favor ingresa un email válido</div>',
        status: 'danger',
        pos: 'top-center'
      });
      return;
    }
    
    if (!isValid) {
      UIkit.notification({
        message: '<div class="uk-text-center color-blanco bg-danger padding-10 text-lg"><i class="fa fa-exclamation-triangle fa-lg"></i> &nbsp; Por favor completa todos los campos requeridos</div>',
        status: 'danger',
        pos: 'top-center'
      });
      return;
    }
    
    // Collect form data
    const formData = new FormData(this);
    formData.append('enviarcotizacion', '1');
    
    // Show loading state
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin" style="margin-right: 10px;"></i>Enviando...');
    submitBtn.prop('disabled', true);
    
    // Submit form
    $.ajax({
      data: formData,
      url: "includes/acciones.php",
      type: "post",
      processData: false,
      contentType: false,
      success: function(response) {
        submitBtn.html(originalText);
        submitBtn.prop('disabled', false);
        
        try {
          const datos = JSON.parse(response);
          UIkit.notification({
            message: datos.msj,
            status: datos.estatus == 0 ? 'success' : 'danger',
            pos: 'top-center'
          });
          
          if (datos.estatus == 0) {
            // Reset form and go to step 1
            $('#quoteForm')[0].reset();
            currentStep = 1;
            showStep(1);
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
        submitBtn.html(originalText);
        submitBtn.prop('disabled', false);
        
        UIkit.notification({
          message: '<div class="uk-text-center color-blanco bg-danger padding-10 text-lg"><i class="fa fa-exclamation-triangle fa-lg"></i> &nbsp; Error de conexión</div>',
          status: 'danger',
          pos: 'top-center'
        });
      }
    });
  });
  
  // Initialize progress
  updateProgress();
});
</script>

</body>
</html>