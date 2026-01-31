<?php
require_once 'includes/db.php';

try {
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'short_description'");
    $exists = $stmt->fetch();

    if (!$exists) {
        $pdo->exec("ALTER TABLE products ADD COLUMN short_description TEXT AFTER shipping_fee");
        echo "Added 'short_description' column to products table.<br>";
    } else {
        echo "'short_description' column already exists.<br>";
    }

    echo "Schema update check complete.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
