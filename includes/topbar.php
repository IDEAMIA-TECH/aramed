<?php
/**
 * Topbar - Mensajes de noticias y avisos
 */
if (!defined('ARAMED_SITE')) die('Acceso directo no permitido');
?>

<div class="topbar bg-dark text-white py-2">
    <div class="container">
        <div class="topbar-content d-flex align-items-center justify-content-center">
            <div class="topbar-message text-center small">
                <i class="bi bi-megaphone-fill me-2"></i>
                <span>Próximamente: Curso de simulación médica avanzada - Regístrate ahora</span>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos temporales para el topbar */
.topbar {
    font-size: 0.875rem;
}

.topbar-message {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>

