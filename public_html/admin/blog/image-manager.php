<?php
/**
 * ========================================
 * ADMIN - GESTOR DE IMÁGENES
 * ========================================
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración y verificar autenticación
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth_check.php';
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Imágenes - Blog Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Variables CSS para consistencia */
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-radius: 8px;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-hover: 0 4px 20px rgba(0,0,0,0.15);
        }

        /* Estilos base */
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .admin-sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
            border-right: 1px solid #e9ecef;
            box-shadow: var(--shadow);
        }

        .admin-content {
            min-height: 100vh;
            background: transparent;
        }

        /* Header elegante */
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="10" cy="50" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .page-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .page-header .header-info {
            margin-top: 0.5rem;
            opacity: 0.9;
            font-size: 1rem;
            position: relative;
            z-index: 1;
        }

        .page-header .header-actions {
            position: absolute;
            top: 2rem;
            right: 2rem;
            z-index: 1;
        }

        /* Tarjetas de contenido */
        .content-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .content-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .content-card-header {
            background: linear-gradient(135deg, var(--light-color) 0%, #e9ecef 100%);
            padding: 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .content-card-header h5 {
            margin: 0;
            color: var(--dark-color);
            font-weight: 600;
            font-size: 1.1rem;
        }

        .content-card-header h5 i {
            color: var(--primary-color);
            margin-right: 0.5rem;
        }

        .content-card-body {
            padding: 2rem;
        }

        /* Área de subida mejorada */
        .upload-area {
            border: 3px dashed #dee2e6;
            border-radius: var(--border-radius);
            padding: 3rem 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            position: relative;
            overflow: hidden;
        }

        .upload-area::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(0, 102, 204, 0.05) 50%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .upload-area:hover {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .upload-area:hover::before {
            opacity: 1;
        }

        .upload-area.dragover {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            transform: scale(1.02);
            box-shadow: var(--shadow-hover);
        }

        .upload-area i {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .upload-area h5 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .upload-area p {
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }

        .upload-area small {
            color: var(--secondary-color);
            font-size: 0.8rem;
        }

        /* Grid de imágenes mejorado */
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .image-card {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            box-shadow: var(--shadow);
        }

        .image-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary-color);
        }

        .image-card.selected {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.25);
            transform: translateY(-2px);
        }

        .image-preview {
            width: 100%;
            height: 160px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .image-card:hover .image-preview {
            transform: scale(1.05);
        }

        .image-info {
            padding: 1rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .image-info h6 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .image-info p {
            color: var(--secondary-color);
            font-size: 0.8rem;
            margin-bottom: 0.25rem;
        }

        .image-actions {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .image-card:hover .image-actions {
            opacity: 1;
        }

        .image-actions .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Botones mejorados */
        .btn {
            border-radius: var(--border-radius);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            box-shadow: 0 2px 10px rgba(0, 102, 204, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0056b3 0%, #004494 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 102, 204, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #1e7e34 100%);
            color: white;
            box-shadow: 0 2px 10px rgba(40, 167, 69, 0.3);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #1e7e34 0%, #155724 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(40, 167, 69, 0.4);
        }

        .btn-outline-secondary {
            border: 2px solid var(--secondary-color);
            color: var(--secondary-color);
        }

        .btn-outline-secondary:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
        }

        .btn-outline-danger {
            border: 2px solid var(--danger-color);
            color: var(--danger-color);
        }

        .btn-outline-danger:hover {
            background: var(--danger-color);
            color: white;
            transform: translateY(-2px);
        }

        /* Barra de progreso mejorada */
        .progress-container {
            display: none;
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .progress {
            height: 8px;
            border-radius: 4px;
            background: #e9ecef;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            transition: width 0.3s ease;
        }

        /* Filtros mejorados */
        .filters-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
        }

        .input-group-text {
            background: linear-gradient(135deg, var(--light-color) 0%, #e9ecef 100%);
            border: 2px solid #e9ecef;
            border-right: none;
            color: var(--primary-color);
        }

        /* Modal mejorado */
        .modal-content {
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-hover);
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .modal-header .btn-close {
            filter: invert(1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                padding: 1.5rem;
                text-align: center;
            }

            .page-header .header-actions {
                position: static;
                margin-top: 1rem;
            }

            .image-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 1rem;
            }

            .upload-area {
                padding: 2rem 1rem;
            }
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .content-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .content-card:nth-child(2) { animation-delay: 0.1s; }
        .content-card:nth-child(3) { animation-delay: 0.2s; }
        .content-card:nth-child(4) { animation-delay: 0.3s; }

        /* Estado vacío */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--light-color);
            margin-bottom: 1rem;
        }

        .empty-state h5 {
            color: var(--dark-color);
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/../includes/admin_menu.php'; ?>

            <!-- Contenido principal -->
            <div class="col-md-9 col-lg-9 admin-content p-4">
                <!-- Header elegante -->
                <div class="page-header">
                    <h1>
                        <i class="bi bi-images me-2"></i>Gestor de Imágenes
                    </h1>
                    <div class="header-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Administra y organiza las imágenes del blog con herramientas avanzadas
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="bi bi-cloud-upload me-2"></i>Subir Imagen
                        </button>
                        <button class="btn btn-success" id="selectImageBtn" style="display: none;">
                            <i class="bi bi-check me-2"></i>Seleccionar Imagen
                        </button>
                    </div>
                </div>

                <!-- Área de subida -->
                <div class="content-card">
                    <div class="content-card-header">
                        <h5><i class="bi bi-cloud-upload me-2"></i>Subir Imágenes</h5>
                    </div>
                    <div class="content-card-body">
                        <div class="upload-area" id="uploadArea">
                            <i class="bi bi-cloud-upload display-4 mb-3"></i>
                            <h5>Arrastra y suelta imágenes aquí</h5>
                            <p>o haz clic para seleccionar archivos</p>
                            <small>JPG, PNG, WebP, GIF - Máximo 5MB</small>
                        </div>

                        <!-- Barra de progreso -->
                        <div class="progress-container">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small>Subiendo imagen...</small>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="filters-card">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="searchImages" placeholder="Buscar imágenes...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-secondary" id="selectAll">
                                    <i class="bi bi-check-square me-1"></i>Seleccionar Todo
                                </button>
                                <button type="button" class="btn btn-outline-danger" id="deleteSelected" disabled>
                                    <i class="bi bi-trash me-1"></i>Eliminar Seleccionadas
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid de imágenes -->
                <div class="image-grid" id="imageGrid">
                    <!-- Las imágenes se cargarán aquí -->
                </div>

                <!-- Mensaje cuando no hay imágenes -->
                <div class="content-card d-none" id="noImages">
                    <div class="content-card-body">
                        <div class="empty-state">
                            <i class="bi bi-images"></i>
                            <h5>No hay imágenes</h5>
                            <p>Sube tu primera imagen para comenzar a organizar tu galería</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de subida -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-cloud-upload me-2"></i>Subir Imagen
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="imageFile" class="form-label">Seleccionar archivo</label>
                            <input type="file" class="form-control" id="imageFile" name="image" accept="image/*" required>
                            <div class="form-text">JPG, PNG, WebP, GIF - Máximo 5MB</div>
                        </div>
                        <div class="mb-3">
                            <label for="imageAlt" class="form-label">Texto alternativo (opcional)</label>
                            <input type="text" class="form-control" id="imageAlt" placeholder="Descripción de la imagen">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="uploadBtn">
                        <i class="bi bi-cloud-upload me-2"></i>Subir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de vista previa -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vista Previa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="previewImage" class="img-fluid" style="max-height: 500px;">
                    <div class="mt-3">
                        <p class="text-muted mb-2" id="previewFilename"></p>
                        <div class="btn-group">
                            <button class="btn btn-outline-primary" id="copyUrlBtn">
                                <i class="bi bi-clipboard me-1"></i>Copiar URL
                            </button>
                            <button class="btn btn-outline-danger" id="deleteImageBtn">
                                <i class="bi bi-trash me-1"></i>Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let images = [];
        let selectedImages = new Set();
        let selectedImage = null;
        let isPopupMode = false;

        // Cargar imágenes al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar si es modo popup
            const urlParams = new URLSearchParams(window.location.search);
            isPopupMode = urlParams.has('field');
            
            if (isPopupMode) {
                // Ocultar sidebar en modo popup
                const sidebar = document.querySelector('.col-md-3');
                const mainContent = document.querySelector('.col-md-9');
                
                if (sidebar) {
                    sidebar.style.display = 'none';
                }
                
                if (mainContent) {
                    mainContent.classList.remove('col-md-9');
                    mainContent.classList.add('col-12');
                }
                
                // Mostrar botón de selección
                const selectBtn = document.getElementById('selectImageBtn');
                if (selectBtn) {
                    selectBtn.style.display = 'inline-block';
                }
            }
            
            loadImages();
            setupEventListeners();
        });

        function loadImages() {
            fetch('upload-image.php?action=list')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        images = data.images;
                        renderImages();
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function renderImages() {
            const grid = document.getElementById('imageGrid');
            const noImages = document.getElementById('noImages');
            
            if (images.length === 0) {
                grid.innerHTML = '';
                noImages.classList.remove('d-none');
                return;
            }
            
            noImages.classList.add('d-none');
            grid.innerHTML = images.map(image => {
                // Convertir URL relativa a absoluta
                let imageUrl = image.url;
                if (imageUrl.startsWith('/')) {
                    imageUrl = window.location.origin + imageUrl;
                } else if (!imageUrl.startsWith('http')) {
                    imageUrl = window.location.origin + '/' + imageUrl;
                }
                
                return `
                <div class="image-card" data-filename="${image.filename}">
                    <img src="${imageUrl}" alt="${image.filename}" class="image-preview" onerror="this.src='${window.location.origin}/assets/images/blog/default-article.jpg'">
                    <div class="image-info">
                        <div class="fw-bold text-truncate" title="${image.filename}">${image.filename}</div>
                        <div class="text-muted small">${formatFileSize(image.size)}</div>
                        <div class="text-muted small">${formatDate(image.modified)}</div>
                    </div>
                </div>
            `;
            }).join('');
            
            // Agregar event listeners a las tarjetas
            document.querySelectorAll('.image-card').forEach(card => {
                card.addEventListener('click', function() {
                    const filename = this.dataset.filename;
                    
                    if (isPopupMode) {
                        // Modo popup: seleccionar una sola imagen
                        document.querySelectorAll('.image-card').forEach(c => c.classList.remove('selected'));
                        this.classList.add('selected');
                        selectedImage = images.find(img => img.filename === filename);
                    } else {
                        // Modo normal: selección múltiple
                        if (selectedImages.has(filename)) {
                            selectedImages.delete(filename);
                            this.classList.remove('selected');
                        } else {
                            selectedImages.add(filename);
                            this.classList.add('selected');
                        }
                        updateDeleteButton();
                    }
                });
                
                // Doble clic para vista previa
                card.addEventListener('dblclick', function() {
                    const filename = this.dataset.filename;
                    const image = images.find(img => img.filename === filename);
                    if (image) {
                        showPreview(image);
                    }
                });
            });
        }

        function setupEventListeners() {
            // Área de subida
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('imageFile');
            
            if (uploadArea && fileInput) {
                uploadArea.addEventListener('click', () => fileInput.click());
                uploadArea.addEventListener('dragover', handleDragOver);
                uploadArea.addEventListener('dragleave', handleDragLeave);
                uploadArea.addEventListener('drop', handleDrop);
            }
            
            // Formulario de subida
            const uploadBtn = document.getElementById('uploadBtn');
            if (uploadBtn) {
                console.log('Upload button found, adding event listener');
                uploadBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Upload button clicked');
                    uploadImage();
                });
            } else {
                console.error('Upload button not found');
            }
            
            // Botones de acción
            const selectAllBtn = document.getElementById('selectAll');
            const deleteSelectedBtn = document.getElementById('deleteSelected');
            
            if (selectAllBtn) {
                selectAllBtn.addEventListener('click', selectAll);
            }
            
            if (deleteSelectedBtn) {
                deleteSelectedBtn.addEventListener('click', deleteSelected);
            }
            
            // Búsqueda
            const searchInput = document.getElementById('searchImages');
            if (searchInput) {
                searchInput.addEventListener('input', filterImages);
            }
            
            // Botón de selección (modo popup)
            const selectImageBtn = document.getElementById('selectImageBtn');
            if (selectImageBtn) {
                selectImageBtn.addEventListener('click', selectImage);
            }
            
            // Listener adicional para el formulario de subida
            const uploadForm = document.getElementById('uploadForm');
            if (uploadForm) {
                uploadForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('Form submit event triggered');
                    uploadImage();
                });
            }
        }
        
        function selectImage() {
            if (selectedImage && isPopupMode) {
                // Enviar mensaje al padre
                if (window.opener) {
                    window.opener.postMessage({
                        type: 'imageSelected',
                        url: selectedImage.url,
                        filename: selectedImage.filename
                    }, '*');
                }
                window.close();
            } else {
                alert('Por favor selecciona una imagen');
            }
        }
        
        function showPreview(image) {
            // Convertir URL relativa a absoluta
            let imageUrl = image.url;
            if (imageUrl.startsWith('/')) {
                imageUrl = window.location.origin + imageUrl;
            } else if (!imageUrl.startsWith('http')) {
                imageUrl = window.location.origin + '/' + imageUrl;
            }
            
            document.getElementById('previewImage').src = imageUrl;
            document.getElementById('previewFilename').textContent = image.filename;
            
            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.currentTarget.classList.add('dragover');
        }

        function handleDragLeave(e) {
            e.preventDefault();
            e.currentTarget.classList.remove('dragover');
        }

        function handleDrop(e) {
            e.preventDefault();
            e.currentTarget.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                uploadFile(files[0]);
            }
        }

        function uploadImage() {
            console.log('uploadImage() called');
            const fileInput = document.getElementById('imageFile');
            
            if (!fileInput) {
                console.error('File input not found');
                showAlert('Error: Campo de archivo no encontrado', 'danger');
                return;
            }
            
            if (fileInput.files.length === 0) {
                console.log('No files selected');
                showAlert('Por favor selecciona una imagen', 'warning');
                return;
            }
            
            console.log('File selected:', fileInput.files[0].name);
            uploadFile(fileInput.files[0]);
        }

        function uploadFile(file) {
            console.log('uploadFile() called with:', file.name, file.size, file.type);
            
            const formData = new FormData();
            formData.append('image', file);
            
            const progressContainer = document.querySelector('.progress-container');
            const progressBar = document.querySelector('.progress-bar');
            
            if (!progressContainer || !progressBar) {
                console.error('Progress elements not found');
                showAlert('Error: Elementos de progreso no encontrados', 'danger');
                return;
            }
            
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';
            
            console.log('Sending request to upload-image.php');
            
            fetch('upload-image.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                progressContainer.style.display = 'none';
                
                if (data.success) {
                    // Cerrar modal y recargar imágenes
                    const modalElement = document.getElementById('uploadModal');
                    if (modalElement) {
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                        }
                    }
                    
                    const form = document.getElementById('uploadForm');
                    if (form) {
                        form.reset();
                    }
                    
                    loadImages();
                    
                    // Mostrar mensaje de éxito
                    showAlert('Imagen subida correctamente', 'success');
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                progressContainer.style.display = 'none';
                showAlert('Error al subir la imagen', 'danger');
                console.error('Error:', error);
            });
        }

        function selectAll() {
            const allSelected = selectedImages.size === images.length;
            selectedImages.clear();
            
            if (!allSelected) {
                images.forEach(image => selectedImages.add(image.filename));
            }
            
            document.querySelectorAll('.image-card').forEach(card => {
                card.classList.toggle('selected', !allSelected);
            });
            
            updateDeleteButton();
        }

        function deleteSelected() {
            if (selectedImages.size === 0) return;
            
            if (confirm(`¿Eliminar ${selectedImages.size} imagen(es) seleccionada(s)?`)) {
                const promises = Array.from(selectedImages).map(filename => 
                    fetch(`upload-image.php?action=delete&filename=${filename}`)
                        .then(response => response.json())
                );
                
                Promise.all(promises).then(() => {
                    selectedImages.clear();
                    loadImages();
                    showAlert('Imágenes eliminadas correctamente', 'success');
                });
            }
        }

        function updateDeleteButton() {
            const deleteBtn = document.getElementById('deleteSelected');
            if (deleteBtn) {
                deleteBtn.disabled = selectedImages.size === 0;
                deleteBtn.innerHTML = `<i class="bi bi-trash me-1"></i>Eliminar (${selectedImages.size})`;
            }
        }

        function filterImages() {
            const searchInput = document.getElementById('searchImages');
            if (!searchInput) return;
            
            const searchTerm = searchInput.value.toLowerCase();
            const cards = document.querySelectorAll('.image-card');
            
            cards.forEach(card => {
                const filename = card.dataset.filename.toLowerCase();
                const matches = filename.includes(searchTerm);
                card.style.display = matches ? 'block' : 'none';
            });
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function formatDate(timestamp) {
            return new Date(timestamp * 1000).toLocaleDateString('es-MX');
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 5000);
        }
    </script>
</body>
</html>
