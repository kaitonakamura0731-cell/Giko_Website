<?php
// Load Configuration
$config_path = dirname(__DIR__, 2) . '/db_config.php';
// ユーザー環境による設定ミスを防ぐため、強制的に本番設定を使用
// if (file_exists($config_path)) {
//     require_once $config_path;
// } else {
    // Fallback for production (ConoHa Server)
    $host = 'mysql1007.conoha.ne.jp';
    $db = '1lq8c_detabase';
    $user = '1lq8c_admin';
    $pass = 'password123!';
    $charset = 'utf8mb4';
    $port = '3306';
// }

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