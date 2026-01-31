<?php
require_once 'includes/db.php';

try {
    // Site Settings
    $pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(50) NOT NULL UNIQUE,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "Checked/Created table 'site_settings'.<br>";

    // News
    $pdo->exec("CREATE TABLE IF NOT EXISTS news (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        thumbnail VARCHAR(255),
        published_date DATE,
        status TINYINT DEFAULT 1 COMMENT '1:Published, 0:Draft',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "Checked/Created table 'news'.<br>";

    echo "CMS Schema update complete.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
