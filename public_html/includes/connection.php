<?php
/**
 * ========================================
 * ARAMED Y LABORATORIOS - Conexión BD
 * ========================================
 * 
 * Conexión a base de datos usando PDO
 * 
 * @package    Aramed
 * @author     IDEAMIA Tech
 * @copyright  2025 Aramed y Laboratorios
 */

// Prevenir acceso directo
if (!defined('ARAMED_SITE')) {
    die('Acceso directo no permitido');
}

// Cargar configuración
require_once __DIR__ . '/config.php';

/**
 * Clase Database - Singleton para conexión PDO
 */
class Database {
    private static $instance = null;
    private $connection;
    
    /**
     * Constructor privado (Singleton)
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            if (ENVIRONMENT === 'development') {
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            
        } catch (PDOException $e) {
            if (ENVIRONMENT === 'development') {
                die("Error de conexión: " . $e->getMessage());
            } else {
                error_log("Database Connection Error: " . $e->getMessage());
                die("Error al conectar con la base de datos. Por favor, contacte al administrador.");
            }
        }
    }
    
    /**
     * Obtener instancia única de la conexión
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Obtener conexión PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Prevenir clonación
     */
    private function __clone() {}
    
    /**
     * Prevenir deserialización
     */
    public function __wakeup() {
        throw new Exception("No se puede deserializar un singleton.");
    }
}

/**
 * Función helper para obtener la conexión rápidamente
 */
function getDB() {
    return Database::getInstance()->getConnection();
}

/**
 * Función para ejecutar queries preparadas de forma segura
 */
function dbQuery($sql, $params = []) {
    try {
        $db = getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        if (ENVIRONMENT === 'development') {
            die("Query Error: " . $e->getMessage() . "<br>SQL: " . $sql);
        } else {
            error_log("Query Error: " . $e->getMessage() . " | SQL: " . $sql);
            return false;
        }
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
        return getDB()->lastInsertId();
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

/**
 * Función para sanitizar strings
 */
function sanitizeString($str) {
    return htmlspecialchars(strip_tags(trim($str)), ENT_QUOTES, 'UTF-8');
}

/**
 * Función para sanitizar email
 */
function sanitizeEmail($email) {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

/**
 * Función para validar email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

