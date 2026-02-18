<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/db.php';

echo "<h1>Simple Database Repair Tool</h1>";

try {
    echo "<p>Connected to database. Checking columns...</p>";

    // Array of column definitions to add
    // Key: Column Name, Value: Full Definition including AFTER
    $columns_to_add = [
        'lead_text' => "ADD COLUMN lead_text TEXT COMMENT 'リード文' AFTER description",
        'product_summary_json' => "ADD COLUMN product_summary_json JSON COMMENT '商品概要（リスト形式）' AFTER lead_text",
        'vehicle_type' => "ADD COLUMN vehicle_type VARCHAR(255) COMMENT '車両型式（追加）' AFTER model_code",
        'detail_image_path' => "ADD COLUMN detail_image_path VARCHAR(255) COMMENT '詳細画像パス' AFTER vehicle_type",
        'option_detail_image' => "ADD COLUMN option_detail_image VARCHAR(255) COMMENT 'オプション詳細画像' AFTER detail_image_path",
        'vehicle_tags' => "ADD COLUMN vehicle_tags VARCHAR(500) COMMENT '車種タグ（カンマ区切り）' AFTER option_detail_image"
    ];

    // Get existing columns
    $stmt = $pdo->query("SHOW COLUMNS FROM products");
    $existing_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "<ul>";
    foreach ($columns_to_add as $col_name => $sql_segment) {
        if (in_array($col_name, $existing_columns)) {
            echo "<li style='color: gray;'>Column <strong>{$col_name}</strong> already exists. Skipped.</li>";
        } else {
            // Check dependency (AFTER clause)
            if (preg_match('/AFTER\s+(\w+)/', $sql_segment, $matches)) {
                $after_col = $matches[1];
                if (!in_array($after_col, $existing_columns)) {
                    // If dependency missing, try to add without AFTER or handle it?
                    // But if we are running sequentially, we might have just added it?
                    // Let's re-fetch columns? No, better is to blindly try ALTER or check carefully.
                    // Actually, if 'lead_text' is added, it's not in $existing_columns yet (fetched at start).
                    // So we should verify dependency exists OR just run it and catch error.
                    // BUT: 'product_summary_json' depends on 'lead_text'.
                    // If we add 'lead_text' successfully, the next query should work.
                }
            }

            try {
                $sql = "ALTER TABLE products " . $sql_segment;
                $pdo->exec($sql);
                echo "<li style='color: green;'><strong>Success:</strong> Added column <strong>{$col_name}</strong>.</li>";
                // Add to existing checks for next iterations in this loop (if logic depended on it)
                $existing_columns[] = $col_name;
            } catch (PDOException $e) {
                echo "<li style='color: red;'><strong>Error</strong> adding {$col_name}: " . htmlspecialchars($e->getMessage()) . "</li>";
            }
        }
    }
    echo "</ul>";

    echo "<p style='margin-top:20px; font-weight:bold;'>Repair process completed.</p>";
    echo "<p><a href='store/index.php'>Go to Store Admin</a></p>";

} catch (PDOException $e) {
    echo "<div style='color: red; border: 1px solid red; padding: 10px;'>Fatal Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>