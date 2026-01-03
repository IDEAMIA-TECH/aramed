<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Conexión BD
 * ========================================
 * 
 * Conexión simple a base de datos usando PDO
 * Sin Singleton, sin die()
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Prevenir acceso directo
if (!defined('ARAMED_SITE')) {
    die('Acceso directo no permitido');
}

// Variable global para la conexión
$GLOBALS['db_connection'] = null;

/**
 * Función para obtener la conexión PDO
 */
function getDB() {
    error_log("getDB() - INICIO");
    
    // Si ya existe una conexión, devolverla
    if ($GLOBALS['db_connection'] !== null) {
        error_log("getDB() - Reutilizando conexión existente");
        return $GLOBALS['db_connection'];
    }
    
    try {
        error_log("getDB() - Creando nueva conexión...");
        error_log("getDB() - DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NO DEFINIDO'));
        error_log("getDB() - DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NO DEFINIDO'));
        error_log("getDB() - DB_USER: " . (defined('DB_USER') ? DB_USER : 'NO DEFINIDO'));
        error_log("getDB() - DB_CHARSET: " . (defined('DB_CHARSET') ? DB_CHARSET : 'NO DEFINIDO'));
        
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        error_log("getDB() - DSN: " . $dsn);
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
        ];
        
        error_log("getDB() - Intentando conectar...");
        $GLOBALS['db_connection'] = new PDO($dsn, DB_USER, DB_PASS, $options);
        error_log("getDB() - Conexión exitosa");
        
        return $GLOBALS['db_connection'];
        
    } catch (PDOException $e) {
        error_log("getDB() - ERROR PDOException: " . $e->getMessage());
        error_log("getDB() - Código: " . $e->getCode());
        error_log("getDB() - Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
        error_log("getDB() - Stack trace: " . $e->getTraceAsString());
        
        // Retornar false en lugar de die()
        return false;
    } catch (Exception $e) {
        error_log("getDB() - ERROR Exception: " . $e->getMessage());
        error_log("getDB() - Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
        return false;
    }
}

/**
 * Función para ejecutar queries preparadas de forma segura
 */
function dbQuery($sql, $params = []) {
    try {
        $db = getDB();
        if (!$db) {
            return false;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Query Error: " . $e->getMessage() . " | SQL: " . $sql);
        return false;
    }
}

/**
 * Función para obtener un solo registro
 */
function dbFetchOne($sql, $params = []) {
    $stmt = dbQuery($sql, $params);
    return $stmt ? $stmt->fetch() : false;
}

/**
 * Función para obtener múltiples registros
 */
function dbFetchAll($sql, $params = []) {
    $stmt = dbQuery($sql, $params);
    return $stmt ? $stmt->fetchAll() : false;
}

/**
 * Función para insertar y obtener el último ID
 */
function dbInsert($sql, $params = []) {
    $stmt = dbQuery($sql, $params);
    if ($stmt) {
        $db = getDB();
        return $db ? $db->lastInsertId() : false;
    }
    return false;
}

/**
 * Función para contar registros
 */
function dbCount($table, $where = '', $params = []) {
    $sql = "SELECT COUNT(*) as total FROM `$table`";
    if (!empty($where)) {
        $sql .= " WHERE $where";
    }
    $result = dbFetchOne($sql, $params);
    return $result ? (int)$result['total'] : 0;
}

