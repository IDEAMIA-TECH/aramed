<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Proyectos
 * ========================================
 * 
 * Página de listado de proyectos con filtros
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Definir constante del sitio
define('ARAMED_SITE', true);

// Cargar configuración
require_once __DIR__ . '/includes/config.php';

// Cargar funciones
require_once INCLUDES_PATH . '/functions.php';

// Cargar conexión a la base de datos
require_once INCLUDES_PATH . '/connection.php';

// Variables para meta tags
$pageTitle = 'Proyectos - ' . SITE_NAME;
$pageDescription = 'Conoce nuestros proyectos realizados en instituciones de salud y educación. Instalaciones, capacitaciones y soluciones implementadas.';
$pageKeywords = 'proyectos, instalaciones, capacitaciones, casos de éxito, simuladores médicos';
$pageUrl = SITE_URL . '/proyectos.php';

// Obtener parámetros de filtro
$ano = isset($_GET['ano']) ? (int)$_GET['ano'] : 0;
$sector = isset($_GET['sector']) ? sanitizeInput($_GET['sector']) : '';
$categoria = isset($_GET['categoria']) ? sanitizeInput($_GET['categoria']) : '';
$busqueda = isset($_GET['busqueda']) ? sanitizeInput($_GET['busqueda']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Construir consulta de proyectos
$where_conditions = ['p.estado = "publicado"'];
$params = [];

if ($ano > 0) {
    $where_conditions[] = 'p.ano = ?';
    $params[] = $ano;
}

if (!empty($sector)) {
    $where_conditions[] = 'p.sector = ?';
    $params[] = $sector;
}

if (!empty($categoria)) {
    $where_conditions[] = 'p.categoria = ?';
    $params[] = $categoria;
}

if (!empty($busqueda)) {
    $where_conditions[] = '(p.titulo LIKE ? OR p.descripcion_corta LIKE ? OR p.descripcion_larga LIKE ?)';
    $search_term = "%{$busqueda}%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$where_clause = implode(' AND ', $where_conditions);

// Obtener proyectos
try {
    $pdo = getDB();
    
    // Contar total de proyectos
    $count_sql = "
        SELECT COUNT(*) as total 
        FROM proyectos p 
        WHERE {$where_clause}
    ";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_proyectos = $count_stmt->fetch()['total'];
    $total_pages = ceil($total_proyectos / $per_page);
    
    // Obtener proyectos con paginación
    $proyectos_sql = "
        SELECT p.*
        FROM proyectos p
        WHERE {$where_clause}
        ORDER BY p.ano DESC, p.created_at DESC
        LIMIT {$per_page} OFFSET {$offset}
    ";
    $proyectos_stmt = $pdo->prepare($proyectos_sql);
    $proyectos_stmt->execute($params);
    $proyectos = $proyectos_stmt->fetchAll();
    
    // Obtener años únicos para filtro
    $anos_sql = "SELECT DISTINCT ano FROM proyectos WHERE estado = 'publicado' AND ano IS NOT NULL ORDER BY ano DESC";
    $anos_stmt = $pdo->query($anos_sql);
    $anos = $anos_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Obtener sectores únicos para filtro
    $sectores_sql = "SELECT DISTINCT sector FROM proyectos WHERE estado = 'publicado' AND sector IS NOT NULL ORDER BY sector";
    $sectores_stmt = $pdo->query($sectores_sql);
    $sectores = $sectores_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Obtener categorías únicas para filtro
    $categorias_sql = "SELECT DISTINCT categoria FROM proyectos WHERE estado = 'publicado' AND categoria IS NOT NULL ORDER BY categoria";
    $categorias_stmt = $pdo->query($categorias_sql);
    $categorias = $categorias_stmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (Exception $e) {
    error_log("Error en proyectos.php: " . $e->getMessage());
    $proyectos = [];
    $total_proyectos = 0;
    $total_pages = 0;
    $anos = [];
    $sectores = [];
    $categorias = [];
}

// Incluir header
include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="hero-section-proyectos py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">Nuestros Proyectos</h1>
                <p class="lead">Conoce los proyectos que hemos realizado en instituciones de salud y educación, transformando la enseñanza médica con tecnología de vanguardia.</p>
            </div>
        </div>
    </div>
</section>

<!-- Filtros -->
<section class="py-4 bg-light">
    <div class="container">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Año</label>
                <select class="form-select" name="ano">
                    <option value="">Todos los años</option>
                    <?php foreach ($anos as $a): ?>
                    <option value="<?php echo $a; ?>" <?php echo $ano == $a ? 'selected' : ''; ?>>
                        <?php echo $a; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Sector</label>
                <select class="form-select" name="sector">
                    <option value="">Todos los sectores</option>
                    <?php foreach ($sectores as $s): ?>
                    <option value="<?php echo esc($s); ?>" <?php echo $sector === $s ? 'selected' : ''; ?>>
                        <?php echo esc($s); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Categoría</label>
                <select class="form-select" name="categoria">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $c): ?>
                    <option value="<?php echo esc($c); ?>" <?php echo $categoria === $c ? 'selected' : ''; ?>>
                        <?php echo esc($c); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Búsqueda</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="busqueda" value="<?php echo esc($busqueda); ?>" placeholder="Buscar...">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Listado de Proyectos -->
<section class="py-5">
    <div class="container">
        <?php if (empty($proyectos)): ?>
        <div class="text-center py-5">
            <i class="bi bi-folder-x display-1 text-muted"></i>
            <h3 class="mt-3">No se encontraron proyectos</h3>
            <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
        </div>
        <?php else: ?>
        <div class="row">
            <?php foreach ($proyectos as $proyecto): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm project-card">
                    <?php if ($proyecto['imagen_principal']): ?>
                    <img src="<?php echo SITE_URL . '/' . esc($proyecto['imagen_principal']); ?>" 
                         class="card-img-top" 
                         alt="<?php echo esc($proyecto['titulo']); ?>"
                         style="height: 250px; object-fit: cover;">
                    <?php else: ?>
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <a href="proyecto.php?slug=<?php echo esc($proyecto['slug']); ?>" class="text-decoration-none">
                                <?php echo esc($proyecto['titulo']); ?>
                            </a>
                        </h5>
                        
                        <?php if ($proyecto['descripcion_corta']): ?>
                        <p class="card-text text-muted flex-grow-1">
                            <?php echo esc(truncateText($proyecto['descripcion_corta'], 120)); ?>
                        </p>
                        <?php endif; ?>
                        
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <?php if ($proyecto['ano']): ?>
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i><?php echo $proyecto['ano']; ?>
                                </small>
                                <?php endif; ?>
                                <?php if ($proyecto['pais']): ?>
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt me-1"></i><?php echo esc($proyecto['pais']); ?>
                                </small>
                                <?php endif; ?>
                            </div>
                            <a href="proyecto.php?slug=<?php echo esc($proyecto['slug']); ?>" class="btn btn-primary btn-sm w-100">
                                Ver Proyecto <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Paginación -->
        <?php if ($total_pages > 1): ?>
        <nav aria-label="Paginación de proyectos" class="mt-5">
            <ul class="pagination justify-content-center">
                <?php
                $query_params = $_GET;
                unset($query_params['page']);
                $base_url = 'proyectos.php?' . http_build_query($query_params) . '&page=';
                ?>
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo $base_url . ($page - 1); ?>">Anterior</a>
                </li>
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo $base_url . $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo $base_url . ($page + 1); ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<style>
.hero-section-proyectos {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.project-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.project-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
}
</style>

<?php
// Incluir footer
include INCLUDES_PATH . '/footer.php';
?>

