<?php
$host = 'localhost';
$user = 'root';
$pass = 'root';
$port = '8889';

try {
    $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to MySQL successfully.<br>";

    $sql = file_get_contents(__DIR__ . '/db_setup.sql');

    // Execute multiple queries
    // PDO::exec sometimes has issues with multiple queries in prepared statements, but direct exec might work if emulation is on or driver supports it.
    // Ideally split by ';'.

    $pdo->exec($sql);
    echo "Database and tables created successfully.<br>";

    // Create a default admin user if not exists
    $pdo->exec("USE giko_db");
    $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
    if ($stmt->fetchColumn() == 0) {
        // Create default user: admin / admin123
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $insert = $pdo->prepare("INSERT INTO admin_users (username, password_hash) VALUES (?, ?)");
        $insert->execute(['admin', $password]);
        echo "Default admin user created (admin / admin123).<br>";
    }

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
?>