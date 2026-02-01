<?php
// Load Configuration
$config_path = dirname(__DIR__, 2) . '/db_config.php';
if (file_exists($config_path)) {
    require_once $config_path;
} else {
    // Fallback for safety (MAMP default)
    $host = 'localhost';
    $db = 'giko_db';
    $user = 'root';
    $pass = 'root';
    $charset = 'utf8mb4';
    $port = '8889';
}

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
}
?>