<?php
/**
 * Topbar - Mensajes de noticias y avisos con scroll automático
 */
if (!defined('ARAMED_SITE')) die('Acceso directo no permitido');

// Mensajes rotativos (pueden venir de BD en el futuro)
$topbar_messages = [
    [
        'icon' => 'megaphone-fill',
        'text' => 'Nuevo catálogo 2025: Simuladores de última generación disponibles',
        'link' => '#catalogos'
    ],
    [
        'icon' => 'calendar-event',
        'text' => 'Próximo curso de simulación médica avanzada - ¡Inscripciones abiertas!',
        'link' => '#contacto'
    ],
    [
        'icon' => 'award-fill',
        'text' => 'Más de 20 años equipando instituciones de salud en México',
        'link' => '#servicios'
    ],
    [
        'icon' => 'truck',
        'text' => 'Envíos a toda la República Mexicana - Instalación incluida',
        'link' => '#contacto'
    ]
];
?>

<div class="topbar bg-gradient-dark text-white py-2">
    <div class="container">
        <div class="topbar-content position-relative overflow-hidden">
            <!-- Swiper para mensajes rotativos -->
            <div class="swiper topbar-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($topbar_messages as $message): ?>
                    <div class="swiper-slide">
                        <div class="topbar-message text-center">
                            <?php if (!empty($message['link'])): ?>
                            <a href="<?php echo esc($message['link']); ?>" class="text-white text-decoration-none d-flex align-items-center justify-content-center">
                                <i class="bi bi-<?php echo esc($message['icon']); ?> me-2"></i>
                                <span><?php echo esc($message['text']); ?></span>
                                <i class="bi bi-arrow-right ms-2 small"></i>
                            </a>
                            <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="bi bi-<?php echo esc($message['icon']); ?> me-2"></i>
                                <span><?php echo esc($message['text']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Botón cerrar (opcional) -->
            <button type="button" class="topbar-close btn-close btn-close-white position-absolute end-0 top-50 translate-middle-y me-3 d-none d-md-block" aria-label="Cerrar" style="font-size: 0.7rem;"></button>
        </div>
    </div>
</div>

<style>
/* ========================================
   TOPBAR STYLES
   ======================================== */

.topbar {
    background: linear-gradient(135deg, #1a252f 0%, #2c3e50 100%);
    font-size: 0.875rem;
    position: relative;
    z-index: 1021;
}

.topbar-content {
    min-height: 32px;
}

.topbar-swiper {
    width: 100%;
    height: 32px;
}

.topbar-swiper .swiper-slide {
    display: flex;
    align-items: center;
    justify-content: center;
}

.topbar-message {
    font-weight: 500;
    line-height: 1.5;
}

.topbar-message a {
    transition: all 0.3s ease;
}

.topbar-message a:hover {
    opacity: 0.8;
}

.topbar-message a:hover i.bi-arrow-right {
    transform: translateX(3px);
}

.topbar-message i {
    transition: transform 0.3s ease;
}

.topbar-close {
    opacity: 0.6;
    transition: opacity 0.3s ease;
    z-index: 10;
}

.topbar-close:hover {
    opacity: 1;
}

/* Ocultar topbar cuando se cierra */
.topbar.hidden {
    display: none !important;
}

/* Responsive */
@media (max-width: 767.98px) {
    .topbar {
        font-size: 0.75rem;
    }
    
    .topbar-message span {
        font-size: 0.75rem;
    }
    
    .topbar-message i {
        font-size: 0.875rem;
    }
}
</style>

<script>
// Inicializar Swiper del Topbar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Solo inicializar si existe el elemento
    if (document.querySelector('.topbar-swiper')) {
        const topbarSwiper = new Swiper('.topbar-swiper', {
            loop: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            speed: 500,
            allowTouchMove: false, // Deshabilitar swipe manual en topbar
        });
        
        // Botón cerrar topbar
        const closeBtn = document.querySelector('.topbar-close');
        const topbar = document.querySelector('.topbar');
        
        if (closeBtn && topbar) {
            closeBtn.addEventListener('click', function() {
                topbar.classList.add('hidden');
                // Guardar preferencia en localStorage
                localStorage.setItem('topbar_hidden', 'true');
            });
            
            // Verificar si el usuario cerró el topbar anteriormente
            if (localStorage.getItem('topbar_hidden') === 'true') {
                topbar.classList.add('hidden');
            }
        }
    }
});
</script>

