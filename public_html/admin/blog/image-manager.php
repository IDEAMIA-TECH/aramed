<?php
/**
 * ========================================
 * ADMIN - GESTOR DE IMÁGENES
 * ========================================
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
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
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }
        .image-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .image-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .image-card.selected {
            border-color: #0066cc;
            box-shadow: 0 0 0 2px rgba(0, 102, 204, 0.25);
        }
        .image-preview {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .image-info {
            padding: 0.75rem;
            font-size: 0.875rem;
        }
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .upload-area:hover {
            border-color: #0066cc;
            background-color: rgba(0, 102, 204, 0.05);
        }
        .upload-area.dragover {
            border-color: #0066cc;
            background-color: rgba(0, 102, 204, 0.1);
        }
        .progress-container {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 bg-light p-0" style="min-height: 100vh;">
                <div class="p-3">
                    <h5 class="text-primary mb-4">
                        <i class="bi bi-images me-2"></i>Gestor de Imágenes
                    </h5>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-list-ul me-2"></i>Artículos
                        </a>
                        <a class="nav-link" href="create.php">
                            <i class="bi bi-plus-circle me-2"></i>Nuevo Artículo
                        </a>
                        <a class="nav-link active" href="image-manager.php">
                            <i class="bi bi-images me-2"></i>Imágenes
                        </a>
                        <hr>
                        <a class="nav-link" href="../../blog.php" target="_blank">
                            <i class="bi bi-eye me-2"></i>Ver Blog
                        </a>
                        <a class="nav-link" href="../../index.php">
                            <i class="bi bi-house me-2"></i>Volver al Sitio
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="col-md-9 col-lg-10 p-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>
                        <i class="bi bi-images me-2"></i>Gestor de Imágenes
                    </h2>
                    <div>
                        <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="bi bi-cloud-upload me-2"></i>Subir Imagen
                        </button>
                        <button class="btn btn-success" id="selectImageBtn" style="display: none;">
                            <i class="bi bi-check me-2"></i>Seleccionar Imagen
                        </button>
                    </div>
                </div>

                <!-- Área de subida -->
                <div class="upload-area mb-4" id="uploadArea">
                    <i class="bi bi-cloud-upload display-4 text-muted mb-3"></i>
                    <h5>Arrastra y suelta imágenes aquí</h5>
                    <p class="text-muted">o haz clic para seleccionar archivos</p>
                    <small class="text-muted">JPG, PNG, WebP, GIF - Máximo 5MB</small>
                </div>

                <!-- Barra de progreso -->
                <div class="progress-container mb-4">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <small class="text-muted">Subiendo imagen...</small>
                </div>

                <!-- Filtros -->
                <div class="row mb-4">
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

                <!-- Grid de imágenes -->
                <div class="image-grid" id="imageGrid">
                    <!-- Las imágenes se cargarán aquí -->
                </div>

                <!-- Mensaje cuando no hay imágenes -->
                <div class="text-center py-5 d-none" id="noImages">
                    <i class="bi bi-images display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">No hay imágenes</h4>
                    <p class="text-muted">Sube tu primera imagen para comenzar</p>
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
            grid.innerHTML = images.map(image => `
                <div class="image-card" data-filename="${image.filename}">
                    <img src="${image.url}" alt="${image.filename}" class="image-preview">
                    <div class="image-info">
                        <div class="fw-bold text-truncate" title="${image.filename}">${image.filename}</div>
                        <div class="text-muted small">${formatFileSize(image.size)}</div>
                        <div class="text-muted small">${formatDate(image.modified)}</div>
                    </div>
                </div>
            `).join('');
            
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
