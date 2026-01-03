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
    $pdo = getDB();
    if (!$pdo) {
        return [];
    }
    
    try {
        // Verificar si la tabla existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'home_banners'");
        if ($stmt->rowCount() === 0) {
            return [];
        }
        
        $stmt = $pdo->query("
            SELECT * FROM home_banners 
            WHERE estado = 'publicado' 
            AND (fecha_inicio IS NULL OR fecha_inicio <= NOW())
            AND (fecha_fin IS NULL OR fecha_fin >= NOW())
            ORDER BY orden ASC, created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Si la tabla no existe o hay error, retornar array vacío
        error_log("Error en getHomeBanners: " . $e->getMessage());
        return [];
    } catch (Exception $e) {
        error_log("Error en getHomeBanners: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene los productos destacados
 * @return array
 */
function getHomeProductosDestacados() {
    $pdo = getDB();
    if (!$pdo) {
        return [];
    }
    
    try {
        // Verificar si la tabla existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'home_productos_destacados'");
        if ($stmt->rowCount() === 0) {
            return [];
        }
        
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en getHomeProductosDestacados: " . $e->getMessage());
        return [];
    } catch (Exception $e) {
        error_log("Error en getHomeProductosDestacados: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene los servicios activos
 * @return array
 */
function getHomeServicios() {
    $pdo = getDB();
    if (!$pdo) {
        return [];
    }
    
    try {
        // Verificar si la tabla existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'home_servicios'");
        if ($stmt->rowCount() === 0) {
            return [];
        }
        
        $stmt = $pdo->query("
            SELECT * FROM home_servicios 
            WHERE estado = 'activo'
            ORDER BY orden ASC, created_at DESC
            LIMIT 6
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en getHomeServicios: " . $e->getMessage());
        return [];
    } catch (Exception $e) {
        error_log("Error en getHomeServicios: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene misión y visión
 * @return array ['mision' => [...], 'vision' => [...]]
 */
function getHomeMisionVision() {
    $pdo = getDB();
    if (!$pdo) {
        return ['mision' => null, 'vision' => null];
    }
    
    try {
        // Verificar si la tabla existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'home_mision_vision'");
        if ($stmt->rowCount() === 0) {
            return ['mision' => null, 'vision' => null];
        }
        
        $stmt = $pdo->query("SELECT * FROM home_mision_vision WHERE tipo = 'mision'");
        $mision = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->query("SELECT * FROM home_mision_vision WHERE tipo = 'vision'");
        $vision = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'mision' => $mision ?: null,
            'vision' => $vision ?: null
        ];
    } catch (PDOException $e) {
        error_log("Error en getHomeMisionVision: " . $e->getMessage());
        return ['mision' => null, 'vision' => null];
    } catch (Exception $e) {
        error_log("Error en getHomeMisionVision: " . $e->getMessage());
        return ['mision' => null, 'vision' => null];
    }
}

/**
 * Obtiene las categorías destacadas
 * @return array
 */
function getHomeCategoriasDestacadas() {
    $pdo = getDB();
    if (!$pdo) {
        return [];
    }
    
    try {
        // Verificar si la tabla existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'home_categorias_destacadas'");
        if ($stmt->rowCount() === 0) {
            return [];
        }
        
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en getHomeCategoriasDestacadas: " . $e->getMessage());
        return [];
    } catch (Exception $e) {
        error_log("Error en getHomeCategoriasDestacadas: " . $e->getMessage());
        return [];
    }
}

