<?php
/**
 * 並び替え順序を更新する API
 * POST: { table: "products"|"works", order: [id1, id2, id3, ...] }
 */
require_once '../includes/auth.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

// 認証チェック（auth.php で session_start() 済み）
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// POST データ取得
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['table']) || !isset($input['order'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$table = $input['table'];
$order = $input['order'];

// テーブル名のホワイトリスト（SQLインジェクション防止）
if (!in_array($table, ['products', 'works'], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid table name']);
    exit;
}

// IDが全て数値であることを確認
foreach ($order as $id) {
    if (!is_numeric($id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid ID in order']);
        exit;
    }
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("UPDATE $table SET sort_order = ? WHERE id = ?");
    foreach ($order as $index => $id) {
        $stmt->execute([$index + 1, (int)$id]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => '並び順を更新しました']);
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
