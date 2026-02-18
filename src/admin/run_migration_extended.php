<?php
/**
 * Migration Runner: Add extended fields to products table
 * Run this file once to execute the migration
 */

require_once 'includes/db.php';

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Migration</title></head><body>";
echo "<h1>Database Migration - Extended Products Fields</h1>";
echo "<pre>";

try {
    // Read SQL file
    $sql_file = __DIR__ . '/migrate_products_extended.sql';

    if (!file_exists($sql_file)) {
        throw new Exception("Migration file not found: {$sql_file}");
    }

    $sql = file_get_contents($sql_file);

    // Remove the USE statement (we're already connected to the database)
    $sql = preg_replace('/USE\s+[\w_]+;/i', '', $sql);

    // Remove comments and split by semicolon
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );

    echo "Starting migration...\n\n";

    $success_count = 0;
    $error_count = 0;

    foreach ($statements as $statement) {
        if (empty(trim($statement))) continue;

        try {
            $pdo->exec($statement);
            echo "✓ Executed: " . substr($statement, 0, 80) . "...\n";
            $success_count++;
        } catch (PDOException $e) {
            // Check if error is "Duplicate column" - that's OK
            if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                echo "⚠ Column already exists (skipped): " . substr($statement, 0, 60) . "...\n";
            } else {
                echo "✗ Error: " . $e->getMessage() . "\n";
                echo "  Statement: " . substr($statement, 0, 100) . "...\n";
                $error_count++;
            }
        }
    }

    echo "\n========================================\n";
    echo "Migration completed!\n";
    echo "Success: {$success_count} statements\n";
    echo "Errors: {$error_count} statements\n";
    echo "========================================\n\n";

    // Verify columns
    echo "Verifying products table structure:\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM products");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "\nColumns in products table:\n";
    foreach ($columns as $col) {
        echo "  - {$col['Field']} ({$col['Type']}) {$col['Null']}\n";
    }

    echo "\n✓ Migration verification complete.\n";
    echo "\n<strong>IMPORTANT:</strong> For security, delete this file after running it once.\n";

} catch (Exception $e) {
    echo "\n✗ FATAL ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "</pre>";
echo "<p><a href='store/index.php'>Go to Store Admin</a></p>";
echo "</body></html>";
?>
