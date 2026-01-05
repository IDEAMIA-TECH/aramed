<?php
/**
 * ========================================
 * HOME DATA HELPER
 * ========================================
 * 
 * Carga los datos del home desde la base de datos
 * con fallback a contenido hardcodeado
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Prevenir acceso directo
if (!defined('ARAMED_SITE')) {
    die('Acceso directo no permitido');
}

/**
 * Obtiene los banners activos del home
 * @return array
 */
function getHomeBanners() {
    error_log("getHomeBanners() - INICIO");
    $pdo = getDB();
    if (!$pdo) {
        error_log("getHomeBanners() - ERROR: No hay conexión PDO");
        return [];
    }
    error_log("getHomeBanners() - Conexión PDO obtenida");
    
    try {
        // Verificar si la tabla existe
        error_log("getHomeBanners() - Verificando existencia de tabla...");
        $stmt = $pdo->query("SHOW TABLES LIKE 'home_banners'");
        $tableExists = $stmt->rowCount() > 0;
        error_log("getHomeBanners() - Tabla existe: " . ($tableExists ? 'SÍ' : 'NO'));
        
        if (!$tableExists) {
            error_log("getHomeBanners() - Tabla no existe, retornando array vacío");
            return [];
        }
        
        error_log("getHomeBanners() - Ejecutando query...");
        $stmt = $pdo->query("
            SELECT * FROM home_banners 
            WHERE estado = 'publicado' 
            AND (fecha_inicio IS NULL OR fecha_inicio <= NOW())
            AND (fecha_fin IS NULL OR fecha_fin >= NOW())
            ORDER BY orden ASC, created_at DESC
        ");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("getHomeBanners() - Query exitosa, retornando " . count($result) . " banners");
        return $result;
    } catch (PDOException $e) {
        error_log("getHomeBanners() - PDOException: " . $e->getMessage());
        error_log("getHomeBanners() - Código: " . $e->getCode());
        error_log("getHomeBanners() - Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
        return [];
    } catch (Exception $e) {
        error_log("getHomeBanners() - Exception: " . $e->getMessage());
        error_log("getHomeBanners() - Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
        return [];
    }
}

/**
 * Obtiene los productos destacados
 * @return array
 */
function getHomeProductosDestacados() {
    error_log("getHomeProductosDestacados() - INICIO");
    $pdo = getDB();
    if (!$pdo) {
        error_log("getHomeProductosDestacados() - ERROR: No hay conexión PDO");
        return [];
    }
    
    try {
        error_log("getHomeProductosDestacados() - Verificando existencia de tabla...");
        $stmt = $pdo->query("SHOW TABLES LIKE 'home_productos_destacados'");
        if ($stmt->rowCount() === 0) {
            error_log("getHomeProductosDestacados() - Tabla no existe, retornando array vacío");
            return [];
        }
        
        error_log("getHomeProductosDestacados() - Ejecutando query...");
        $stmt = $pdo->query("
            SELECT hpd.*, 
                   p.id as producto_id,
                   p.nombre as producto_nombre,
                   p.codigo as producto_codigo,
                   p.descripcion_corta,
                   p.descripcion_larga,
                   p.imagen_principal,
                   p.slug,
                   m.nombre as marca_nombre,
                   m.logo as marca_logo
            FROM home_productos_destacados hpd
            LEFT JOIN catalogo_productos p ON hpd.producto_id = p.id
            LEFT JOIN catalogo_marcas m ON p.marca_id = m.id
            WHERE hpd.modo = 'manual' 
            AND hpd.estado = 'activo'
            AND p.estado = 'activo'
            ORDER BY hpd.orden ASC, hpd.created_at DESC
            LIMIT 6
        ");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("getHomeProductosDestacados() - Query exitosa, retornando " . count($result) . " productos");
        return $result;
    } catch (PDOException $e) {
        error_log("getHomeProductosDestacados() - PDOException: " . $e->getMessage());
        error_log("getHomeProductosDestacados() - Código: " . $e->getCode());
        error_log("getHomeProductosDestacados() - Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
        return [];
    } catch (Exception $e) {
        error_log("getHomeProductosDestacados() - Exception: " . $e->getMessage());
        error_log("getHomeProductosDestacados() - Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
        return [];
    }
}

/**
 * Obtiene los servicios activos
 * @return array
 */
function getHomeServicios() {
    error_log("getHomeServicios() - INICIO");
    $pdo = getDB();
    if (!$pdo) {
        error_log("getHomeServicios() - ERROR: No hay conexión PDO");
        return [];
    }
    
    try {
        error_log("getHomeServicios() - Verificando existencia de tabla...");
        $stmt = $pdo->query("SHOW TABLES LIKE 'home_servicios'");
        if ($stmt->rowCount() === 0) {
            error_log("getHomeServicios() - Tabla no existe, retornando array vacío");
            return [];
        }
        
        error_log("getHomeServicios() - Ejecutando query...");
        $stmt = $pdo->query("
            SELECT * FROM home_servicios 
            WHERE estado = 'activo'
            ORDER BY orden ASC, created_at DESC
            LIMIT 6
        ");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("getHomeServicios() - Query exitosa, retornando " . count($result) . " servicios");
        return $result;
    } catch (PDOException $e) {
        error_log("getHomeServicios() - PDOException: " . $e->getMessage());
        error_log("getHomeServicios() - Código: " . $e->getCode());
        error_log("getHomeServicios() - Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
        return [];
    } catch (Exception $e) {
        error_log("getHomeServicios() - Exception: " . $e->getMessage());
        error_log("getHomeServicios() - Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
        return [];
    }
}

/**
 * Obtiene misión y visión
 * @return array ['mision' => [...], 'vision' => [...]]
 */
function getHomeMisionVision() {
    error_log("getHomeMisionVision() - INICIO");
    $pdo = getDB();
    if (!$pdo) {
        error_log("getHomeMisionVision() - ERROR: No hay conexión PDO");
        return ['mision' => null, 'vision' => null];
    }
    
    try {
        error_log("getHomeMisionVision() - Verificando existencia de tabla...");
        $stmt = $pdo->query("SHOW TABLES LIKE 'home_mision_vision'");
        if ($stmt->rowCount() === 0) {
            error_log("getHomeMisionVision() - Tabla no existe, retornando valores null");
            return ['mision' => null, 'vision' => null];
        }
        
        error_log("getHomeMisionVision() - Obteniendo misión...");
        $stmt = $pdo->query("SELECT * FROM home_mision_vision WHERE tipo = 'mision'");
        $mision = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("getHomeMisionVision() - Obteniendo visión...");
        $stmt = $pdo->query("SELECT * FROM home_mision_vision WHERE tipo = 'vision'");
        $vision = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("getHomeMisionVision() - Completado. Misión: " . ($mision ? 'SÍ' : 'NO') . ", Visión: " . ($vision ? 'SÍ' : 'NO'));
        return [
            'mision' => $mision ?: null,
            'vision' => $vision ?: null
        ];
    } catch (PDOException $e) {
        error_log("getHomeMisionVision() - PDOException: " . $e->getMessage());
        error_log("getHomeMisionVision() - Código: " . $e->getCode());
        error_log("getHomeMisionVision() - Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
        return ['mision' => null, 'vision' => null];
    } catch (Exception $e) {
        error_log("getHomeMisionVision() - Exception: " . $e->getMessage());
        error_log("getHomeMisionVision() - Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
        return ['mision' => null, 'vision' => null];
    }
}

/**
 * Obtiene las categorías destacadas
 * @return array
 */
function getHomeCategoriasDestacadas() {
    error_log("getHomeCategoriasDestacadas() - INICIO");
    $pdo = getDB();
    if (!$pdo) {
        error_log("getHomeCategoriasDestacadas() - ERROR: No hay conexión PDO");
        return [];
    }
    
    try {
        error_log("getHomeCategoriasDestacadas() - Verificando existencia de tabla...");
        $stmt = $pdo->query("SHOW TABLES LIKE 'home_categorias_destacadas'");
        if ($stmt->rowCount() === 0) {
            error_log("getHomeCategoriasDestacadas() - Tabla no existe, retornando array vacío");
            return [];
        }
        
        error_log("getHomeCategoriasDestacadas() - Ejecutando query...");
        $stmt = $pdo->query("
            SELECT hcd.*, 
                   c.id as categoria_id,
                   c.nombre as categoria_nombre,
                   c.slug as categoria_slug,
                   c.icono as categoria_icono,
                   c.color as categoria_color,
                   COUNT(p.id) as productos_count
            FROM home_categorias_destacadas hcd
            LEFT JOIN catalogo_categorias c ON hcd.categoria_id = c.id
            LEFT JOIN catalogo_productos p ON c.id = p.categoria_id AND p.estado = 'activo'
            WHERE hcd.estado = 'activo'
            AND c.estado = 'activo'
            GROUP BY hcd.id
            ORDER BY hcd.orden ASC, hcd.created_at DESC
            LIMIT 6
        ");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("getHomeCategoriasDestacadas() - Query exitosa, retornando " . count($result) . " categorías");
        return $result;
    } catch (PDOException $e) {
        error_log("getHomeCategoriasDestacadas() - PDOException: " . $e->getMessage());
        error_log("getHomeCategoriasDestacadas() - Código: " . $e->getCode());
        error_log("getHomeCategoriasDestacadas() - Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
        return [];
    } catch (Exception $e) {
        error_log("getHomeCategoriasDestacadas() - Exception: " . $e->getMessage());
        error_log("getHomeCategoriasDestacadas() - Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
        return [];
    }
}

/**
 * Obtiene la configuración de secciones del Home
 * @return array ['seccion' => ['activa' => bool, 'orden' => int], ...]
 */
function getHomeSeccionesConfig() {
    error_log("getHomeSeccionesConfig() - INICIO");
    $pdo = getDB();
    if (!$pdo) {
        error_log("getHomeSeccionesConfig() - ERROR: No hay conexión PDO");
        return [];
    }
    
    try {
        error_log("getHomeSeccionesConfig() - Verificando existencia de tabla...");
        $stmt = $pdo->query("SHOW TABLES LIKE 'home_secciones'");
        if ($stmt->rowCount() === 0) {
            error_log("getHomeSeccionesConfig() - Tabla no existe, retornando array vacío");
            return [];
        }
        
        error_log("getHomeSeccionesConfig() - Ejecutando query...");
        $stmt = $pdo->query("SELECT seccion, activa, orden FROM home_secciones ORDER BY orden ASC");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Convertir a array asociativo por sección
        $config = [];
        foreach ($result as $row) {
            $config[$row['seccion']] = [
                'activa' => (bool)$row['activa'],
                'orden' => (int)$row['orden']
            ];
        }
        
        error_log("getHomeSeccionesConfig() - Query exitosa, retornando " . count($config) . " secciones");
        return $config;
    } catch (PDOException $e) {
        error_log("getHomeSeccionesConfig() - PDOException: " . $e->getMessage());
        return [];
    } catch (Exception $e) {
        error_log("getHomeSeccionesConfig() - Exception: " . $e->getMessage());
        return [];
    }
}

/**
 * Verifica si una sección está activa
 * @param string $seccion Nombre de la sección (hero, servicios, productos_destacados, etc.)
 * @return bool True si está activa, false si no. Si no hay configuración, retorna true por defecto.
 */
function isSeccionActiva($seccion) {
    static $config = null;
    
    if ($config === null) {
        $config = getHomeSeccionesConfig();
    }
    
    // Si no hay configuración, mostrar todas las secciones por defecto (compatibilidad)
    if (empty($config)) {
        return true;
    }
    
    // Verificar si la sección existe y está activa
    return isset($config[$seccion]) && $config[$seccion]['activa'] === true;
}

