<?php
/**
 * ========================================
 * FUNCIONES HELPER PARA SEO
 * ========================================
 * 
 * Funciones para generar meta tags, sitemap, redirects, etc.
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
 * Obtiene la configuración SEO para una página específica
 * @param string $pagina Identificador de la página (home, catalogo, blog, etc.)
 * @return array Configuración SEO
 */
function getSEOConfig($pagina = 'global') {
    $pdo = getDB();
    if (!$pdo) {
        return [];
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM seo_config WHERE tipo = 'pagina' AND pagina = ? LIMIT 1");
        $stmt->execute([$pagina]);
        $config = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si no hay configuración específica, usar global
        if (!$config) {
            $stmt = $pdo->prepare("SELECT * FROM seo_config WHERE tipo = 'global' LIMIT 1");
            $stmt->execute();
            $config = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return $config ?: [];
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Genera el título completo de una página
 * @param string $titulo Título base de la página
 * @param string $pagina Identificador de la página
 * @return string Título completo
 */
function generateSEOTitle($titulo, $pagina = 'global') {
    $config = getSEOConfig($pagina);
    $prefijo = $config['titulo_prefijo'] ?? '';
    $sufijo = $config['titulo_sufijo'] ?? '';
    
    return trim($prefijo . $titulo . $sufijo);
}

/**
 * Obtiene metadatos personalizados para una entidad
 * @param string $tipo_entidad Tipo de entidad (producto, articulo, proyecto, etc.)
 * @param int $entidad_id ID de la entidad
 * @return array Metadatos
 */
function getEntityMeta($tipo_entidad, $entidad_id) {
    $pdo = getDB();
    if (!$pdo) {
        return [];
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM seo_metadatos WHERE tipo_entidad = ? AND entidad_id = ? LIMIT 1");
        $stmt->execute([$tipo_entidad, $entidad_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Guarda o actualiza metadatos para una entidad
 * @param string $tipo_entidad Tipo de entidad
 * @param int $entidad_id ID de la entidad
 * @param array $data Datos de metadatos
 * @return bool True si se guardó correctamente
 */
function saveEntityMeta($tipo_entidad, $entidad_id, $data) {
    $pdo = getDB();
    if (!$pdo) {
        return false;
    }
    
    try {
        $sql = "
            INSERT INTO seo_metadatos (
                tipo_entidad, entidad_id, meta_titulo, meta_descripcion, meta_keywords,
                og_title, og_description, og_image, twitter_title, twitter_description,
                twitter_image, canonical_url, robots, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
                meta_titulo = VALUES(meta_titulo),
                meta_descripcion = VALUES(meta_descripcion),
                meta_keywords = VALUES(meta_keywords),
                og_title = VALUES(og_title),
                og_description = VALUES(og_description),
                og_image = VALUES(og_image),
                twitter_title = VALUES(twitter_title),
                twitter_description = VALUES(twitter_description),
                twitter_image = VALUES(twitter_image),
                canonical_url = VALUES(canonical_url),
                robots = VALUES(robots),
                updated_at = NOW()
        ";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $tipo_entidad, $entidad_id,
            $data['meta_titulo'] ?? null,
            $data['meta_descripcion'] ?? null,
            $data['meta_keywords'] ?? null,
            $data['og_title'] ?? null,
            $data['og_description'] ?? null,
            $data['og_image'] ?? null,
            $data['twitter_title'] ?? null,
            $data['twitter_description'] ?? null,
            $data['twitter_image'] ?? null,
            $data['canonical_url'] ?? null,
            $data['robots'] ?? 'index, follow'
        ]);
    } catch (Exception $e) {
        error_log("Error al guardar metadatos: " . $e->getMessage());
        return false;
    }
}

/**
 * Verifica y aplica redirecciones
 * @param string $url URL actual
 * @return bool True si se aplicó una redirección
 */
function checkRedirects($url) {
    $pdo = getDB();
    if (!$pdo) {
        return false;
    }
    
    try {
        // Normalizar URL
        $url = parse_url($url, PHP_URL_PATH);
        if (!$url) {
            return false;
        }
        
        // Buscar redirección activa
        $stmt = $pdo->prepare("SELECT url_nueva, tipo FROM redirects WHERE url_antigua = ? AND estado = 'activo' LIMIT 1");
        $stmt->execute([$url]);
        $redirect = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($redirect) {
            // Incrementar contador de hits
            $stmt = $pdo->prepare("UPDATE redirects SET hits = hits + 1 WHERE url_antigua = ?");
            $stmt->execute([$url]);
            
            // Aplicar redirección
            $code = $redirect['tipo'] === '301' ? 301 : 302;
            header("Location: " . $redirect['url_nueva'], true, $code);
            exit;
        }
        
        return false;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Genera meta tags HTML
 * @param array $meta Datos de metadatos
 * @param string $pagina Identificador de la página
 * @return string HTML con meta tags
 */
function generateMetaTags($meta = [], $pagina = 'global') {
    $config = getSEOConfig($pagina);
    
    $title = $meta['meta_titulo'] ?? $meta['titulo'] ?? SITE_NAME;
    $title = generateSEOTitle($title, $pagina);
    
    $description = $meta['meta_descripcion'] ?? $meta['descripcion'] ?? ($config['meta_descripcion_default'] ?? SITE_DESCRIPTION);
    $keywords = $meta['meta_keywords'] ?? $meta['keywords'] ?? ($config['meta_keywords_default'] ?? SITE_KEYWORDS);
    $og_image = $meta['og_image'] ?? $meta['imagen'] ?? ($config['og_image'] ?? SITE_URL . '/assets/images/design/logo-og.jpg');
    $canonical = $meta['canonical_url'] ?? $_SERVER['REQUEST_URI'] ?? SITE_URL;
    
    // Asegurar URL completa para canonical
    if (strpos($canonical, 'http') !== 0) {
        $canonical = SITE_URL . $canonical;
    }
    
    // Asegurar URL completa para og_image
    if (strpos($og_image, 'http') !== 0) {
        $og_image = SITE_URL . '/' . ltrim($og_image, '/');
    }
    
    $html = "\n";
    $html .= "    <!-- SEO Meta Tags -->\n";
    $html .= "    <title>" . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</title>\n";
    $html .= "    <meta name=\"description\" content=\"" . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . "\">\n";
    $html .= "    <meta name=\"keywords\" content=\"" . htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8') . "\">\n";
    $html .= "    <link rel=\"canonical\" href=\"" . htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8') . "\">\n";
    
    // Open Graph
    $og_title = $meta['og_title'] ?? $title;
    $og_description = $meta['og_description'] ?? $description;
    
    $html .= "\n    <!-- Open Graph -->\n";
    $html .= "    <meta property=\"og:type\" content=\"website\">\n";
    $html .= "    <meta property=\"og:title\" content=\"" . htmlspecialchars($og_title, ENT_QUOTES, 'UTF-8') . "\">\n";
    $html .= "    <meta property=\"og:description\" content=\"" . htmlspecialchars($og_description, ENT_QUOTES, 'UTF-8') . "\">\n";
    $html .= "    <meta property=\"og:image\" content=\"" . htmlspecialchars($og_image, ENT_QUOTES, 'UTF-8') . "\">\n";
    $html .= "    <meta property=\"og:url\" content=\"" . htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8') . "\">\n";
    
    // Twitter Card
    $twitter_card_type = $config['twitter_card_type'] ?? 'summary_large_image';
    $twitter_title = $meta['twitter_title'] ?? $og_title;
    $twitter_description = $meta['twitter_description'] ?? $og_description;
    $twitter_image = $meta['twitter_image'] ?? $og_image;
    
    $html .= "\n    <!-- Twitter Card -->\n";
    $html .= "    <meta name=\"twitter:card\" content=\"" . htmlspecialchars($twitter_card_type, ENT_QUOTES, 'UTF-8') . "\">\n";
    $html .= "    <meta name=\"twitter:title\" content=\"" . htmlspecialchars($twitter_title, ENT_QUOTES, 'UTF-8') . "\">\n";
    $html .= "    <meta name=\"twitter:description\" content=\"" . htmlspecialchars($twitter_description, ENT_QUOTES, 'UTF-8') . "\">\n";
    $html .= "    <meta name=\"twitter:image\" content=\"" . htmlspecialchars($twitter_image, ENT_QUOTES, 'UTF-8') . "\">\n";
    
    // Robots
    $robots = $meta['robots'] ?? 'index, follow';
    $html .= "\n    <!-- Robots -->\n";
    $html .= "    <meta name=\"robots\" content=\"" . htmlspecialchars($robots, ENT_QUOTES, 'UTF-8') . "\">\n";
    
    return $html;
}

