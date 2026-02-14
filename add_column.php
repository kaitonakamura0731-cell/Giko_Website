<?php
// 本番DB設定
require_once __DIR__ . '/src/admin/includes/db.php';

try {
    echo "Adding 'option_detail_image' column to 'products' table...\n";
    
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'option_detail_image'");
    $exists = $stmt->fetch();
    
    if (!$exists) {
        // Add column
        $pdo->exec("ALTER TABLE products ADD COLUMN option_detail_image VARCHAR(255) DEFAULT NULL AFTER description");
        echo "Successfully added 'option_detail_image' column.\n";
    } else {
        echo "Column 'option_detail_image' already exists.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
