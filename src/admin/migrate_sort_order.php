<?php
/**
 * sort_order カラムを products / works テーブルに追加するマイグレーション
 * ブラウザから1回だけ実行: /admin/migrate_sort_order.php
 */
require_once 'includes/auth.php';
require_once 'includes/db.php';
checkAuth();

$results = [];

// products テーブル
try {
    $check = $pdo->query("SHOW COLUMNS FROM products LIKE 'sort_order'");
    if ($check->rowCount() === 0) {
        $pdo->exec("ALTER TABLE products ADD COLUMN sort_order INT NOT NULL DEFAULT 0");
        // 既存データに連番を振る（ID昇順）
        $pdo->exec("SET @r = 0");
        $pdo->exec("UPDATE products SET sort_order = (@r := @r + 1) ORDER BY id ASC");
        $results[] = "✅ products テーブルに sort_order カラムを追加しました";
    } else {
        $results[] = "⏭ products テーブルには既に sort_order カラムが存在します";
    }
} catch (PDOException $e) {
    $results[] = "❌ products エラー: " . $e->getMessage();
}

// works テーブル
try {
    $check = $pdo->query("SHOW COLUMNS FROM works LIKE 'sort_order'");
    if ($check->rowCount() === 0) {
        $pdo->exec("ALTER TABLE works ADD COLUMN sort_order INT NOT NULL DEFAULT 0");
        // 既存データに連番を振る（作成日降順 = 新しい順に1,2,3...）
        $pdo->exec("SET @r = 0");
        $pdo->exec("UPDATE works SET sort_order = (@r := @r + 1) ORDER BY created_at DESC");
        $results[] = "✅ works テーブルに sort_order カラムを追加しました";
    } else {
        $results[] = "⏭ works テーブルには既に sort_order カラムが存在します";
    }
} catch (PDOException $e) {
    $results[] = "❌ works エラー: " . $e->getMessage();
}

echo "<h2>マイグレーション結果</h2><ul>";
foreach ($results as $r) {
    echo "<li style='margin:8px 0;font-size:16px;'>$r</li>";
}
echo "</ul><p><a href='/admin/index.php'>← 管理画面に戻る</a></p>";
