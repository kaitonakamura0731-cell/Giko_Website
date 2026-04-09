<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/settings_helper.php';
require_once '../includes/upload_helper.php';
checkAuth();

// Auto-create vehicle_tags table if not exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS vehicle_tags (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        image VARCHAR(255) DEFAULT '',
        sort_order INT DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (PDOException $e) {
    // Table might already exist
}

// Migrate existing data from settings and products on first run
try {
    $count = $pdo->query("SELECT COUNT(*) FROM vehicle_tags")->fetchColumn();
    if ($count == 0) {
        // Collect tags from products
        $tagStmt = $pdo->query("SELECT vehicle_tags FROM products WHERE vehicle_tags IS NOT NULL AND vehicle_tags != ''");
        $tagRows = $tagStmt->fetchAll();
        $existingTags = [];
        foreach ($tagRows as $row) {
            foreach (explode(',', $row['vehicle_tags']) as $t) {
                $t = trim($t);
                if ($t && !in_array($t, $existingTags)) $existingTags[] = $t;
            }
        }
        // Get images from settings
        $tagImages = json_decode(get_setting('vehicle_tag_images', '{}'), true) ?: [];
        // Insert
        $insertStmt = $pdo->prepare("INSERT IGNORE INTO vehicle_tags (name, image, sort_order) VALUES (?, ?, ?)");
        foreach ($existingTags as $i => $tag) {
            $img = $tagImages[$tag] ?? '';
            $insertStmt->execute([$tag, $img, $i]);
        }
    }
} catch (PDOException $e) {
    // Migration failed silently
}

$success_msg = '';
$error_msg = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim($_POST['tag_name'] ?? '');
        if ($name === '') {
            $error_msg = 'タグ名を入力してください。';
        } else {
            try {
                $image_path = '';
                if (isset($_FILES['tag_image']) && $_FILES['tag_image']['error'] === UPLOAD_ERR_OK) {
                    $uploaded = handleUpload('tag_image', '../../assets/images/uploads/');
                    if ($uploaded) {
                        $image_path = str_replace('../../assets', '../assets', $uploaded);
                    }
                }
                $maxOrder = $pdo->query("SELECT COALESCE(MAX(sort_order), 0) FROM vehicle_tags")->fetchColumn();
                $stmt = $pdo->prepare("INSERT INTO vehicle_tags (name, image, sort_order) VALUES (?, ?, ?)");
                $stmt->execute([$name, $image_path, $maxOrder + 1]);
                $success_msg = "タグ「{$name}」を追加しました。";
                syncTagImages($pdo);
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $error_msg = "タグ「{$name}」は既に存在します。";
                } else {
                    $error_msg = "エラー: " . $e->getMessage();
                }
            }
        }
    } elseif ($action === 'edit') {
        $id = (int)($_POST['tag_id'] ?? 0);
        $name = trim($_POST['tag_name'] ?? '');
        if ($id && $name !== '') {
            try {
                // Handle image upload
                if (isset($_FILES['tag_image']) && $_FILES['tag_image']['error'] === UPLOAD_ERR_OK) {
                    $uploaded = handleUpload('tag_image', '../../assets/images/uploads/');
                    if ($uploaded) {
                        $image_path = str_replace('../../assets', '../assets', $uploaded);
                        $stmt = $pdo->prepare("UPDATE vehicle_tags SET name=?, image=? WHERE id=?");
                        $stmt->execute([$name, $image_path, $id]);
                    }
                } else {
                    $stmt = $pdo->prepare("UPDATE vehicle_tags SET name=? WHERE id=?");
                    $stmt->execute([$name, $id]);
                }
                // Handle image delete
                if (!empty($_POST['delete_image'])) {
                    $stmt = $pdo->prepare("UPDATE vehicle_tags SET image='' WHERE id=?");
                    $stmt->execute([$id]);
                }
                $success_msg = "タグを更新しました。";
                syncTagImages($pdo);
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $error_msg = "タグ「{$name}」は既に存在します。";
                } else {
                    $error_msg = "エラー: " . $e->getMessage();
                }
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['tag_id'] ?? 0);
        if ($id) {
            try {
                $stmt = $pdo->prepare("SELECT name FROM vehicle_tags WHERE id=?");
                $stmt->execute([$id]);
                $tag = $stmt->fetch();
                if ($tag) {
                    $pdo->prepare("DELETE FROM vehicle_tags WHERE id=?")->execute([$id]);
                    $success_msg = "タグ「{$tag['name']}」を削除しました。";
                    syncTagImages($pdo);
                }
            } catch (PDOException $e) {
                $error_msg = "削除エラー: " . $e->getMessage();
            }
        }
    }
}

// Sync vehicle_tag_images setting for frontend compatibility
function syncTagImages($pdo) {
    $tags = $pdo->query("SELECT name, image FROM vehicle_tags WHERE image != ''")->fetchAll();
    $images = [];
    foreach ($tags as $t) {
        $images[$t['name']] = $t['image'];
    }
    update_setting('vehicle_tag_images', json_encode($images, JSON_UNESCAPED_UNICODE));
}

// Fetch all tags
$tags = $pdo->query("SELECT * FROM vehicle_tags ORDER BY sort_order ASC, id ASC")->fetchAll();

require_once '../includes/header.php';
?>

<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold font-en tracking-widest text-white">VEHICLE TAGS</h1>
    <a href="../index.php" class="text-xs text-gray-400 hover:text-white transition-colors">
        <i class="fas fa-arrow-left mr-1"></i> ダッシュボードに戻る
    </a>
</div>

<?php if ($success_msg): ?>
    <div class="bg-green-900/50 border border-green-500 text-green-100 px-4 py-3 rounded mb-6">
        <?php echo htmlspecialchars($success_msg); ?>
    </div>
<?php endif; ?>
<?php if ($error_msg): ?>
    <div class="bg-red-900/50 border border-red-500 text-red-100 px-4 py-3 rounded mb-6">
        <?php echo htmlspecialchars($error_msg); ?>
    </div>
<?php endif; ?>

<!-- Add New Tag -->
<div class="admin-card mb-6">
    <div class="admin-card-body">
        <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-700 pb-2">
            <i class="fas fa-plus mr-2"></i>新規タグ追加
        </h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="form-label">タグ名</label>
                    <input type="text" name="tag_name" class="form-input" placeholder="例: アルファード" required>
                </div>
                <div class="flex-1">
                    <label class="form-label">画像</label>
                    <input type="file" name="tag_image" class="form-input text-sm" accept="image/*">
                </div>
                <button type="submit"
                    class="bg-primary hover:bg-yellow-500 text-black font-bold py-2 px-6 rounded transition-colors text-sm whitespace-nowrap">
                    <i class="fas fa-plus mr-1"></i> 追加
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tags List -->
<div class="admin-card">
    <div class="admin-card-body">
        <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-700 pb-2">
            <i class="fas fa-tags mr-2"></i>登録済みタグ一覧
        </h3>

        <?php if (empty($tags)): ?>
            <p class="text-gray-500 text-sm py-8 text-center">登録されたタグはありません。</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($tags as $tag):
                    $displayImg = $tag['image'];
                    if ($displayImg && strpos($displayImg, '../assets') === 0) {
                        $displayImg = '../../assets' . substr($displayImg, strlen('../assets'));
                    }
                ?>
                    <div class="bg-gray-900 p-4 rounded border border-gray-700" id="tag-<?php echo $tag['id']; ?>">
                        <!-- View Mode -->
                        <div class="tag-view flex items-center gap-4">
                            <?php if (!empty($tag['image']) && $displayImg && file_exists(__DIR__ . '/' . $displayImg)): ?>
                                <img src="<?php echo htmlspecialchars($displayImg); ?>"
                                    class="h-16 w-24 rounded border border-gray-600 object-cover flex-shrink-0"
                                    onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2796%27 height=%2764%27%3E%3Crect fill=%27%23333%27 width=%2796%27 height=%2764%27/%3E%3Ctext x=%2748%27 y=%2732%27 text-anchor=%27middle%27 dominant-baseline=%27middle%27 fill=%27%23999%27 font-size=%2712%27%3E?%3C/text%3E%3C/svg%3E'">
                            <?php else: ?>
                                <div class="h-16 w-24 bg-gray-800 rounded border border-gray-700 flex items-center justify-center text-gray-500 flex-shrink-0">
                                    <i class="fas fa-car text-lg"></i>
                                </div>
                            <?php endif; ?>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-white text-sm"><?php echo htmlspecialchars($tag['name']); ?></p>
                                <?php if (!empty($tag['image'])): ?>
                                    <p class="text-xs text-gray-500 mt-1 truncate"><?php echo htmlspecialchars($tag['image']); ?></p>
                                <?php else: ?>
                                    <p class="text-xs text-gray-600 mt-1">画像未設定</p>
                                <?php endif; ?>
                            </div>
                            <div class="flex gap-2 flex-shrink-0">
                                <button type="button" onclick="toggleEdit(<?php echo $tag['id']; ?>)"
                                    class="text-blue-400 hover:text-blue-300 transition-colors text-sm px-2 py-1">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" class="inline" onsubmit="return confirm('タグ「<?php echo htmlspecialchars($tag['name']); ?>」を削除しますか？');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="tag_id" value="<?php echo $tag['id']; ?>">
                                    <button type="submit" class="text-red-400 hover:text-red-300 transition-colors text-sm px-2 py-1">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Edit Mode (hidden by default) -->
                        <form method="POST" enctype="multipart/form-data" class="tag-edit hidden mt-3 pt-3 border-t border-gray-700">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="tag_id" value="<?php echo $tag['id']; ?>">
                            <div class="space-y-3">
                                <div>
                                    <label class="text-xs text-gray-400 block mb-1">タグ名</label>
                                    <input type="text" name="tag_name" class="form-input text-sm py-1"
                                        value="<?php echo htmlspecialchars($tag['name']); ?>" required>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-400 block mb-1">画像を変更</label>
                                    <input type="file" name="tag_image" class="form-input text-xs py-1" accept="image/*">
                                </div>
                                <?php if (!empty($tag['image'])): ?>
                                    <label class="flex items-center gap-2 text-xs text-red-400 cursor-pointer">
                                        <input type="checkbox" name="delete_image" value="1">
                                        画像を削除
                                    </label>
                                <?php endif; ?>
                                <div class="flex gap-2">
                                    <button type="submit"
                                        class="bg-primary hover:bg-yellow-500 text-black font-bold py-1 px-4 rounded text-xs transition-colors">
                                        保存
                                    </button>
                                    <button type="button" onclick="toggleEdit(<?php echo $tag['id']; ?>)"
                                        class="border border-gray-600 hover:border-gray-400 text-gray-300 py-1 px-4 rounded text-xs transition-colors">
                                        キャンセル
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleEdit(id) {
    const card = document.getElementById('tag-' + id);
    const view = card.querySelector('.tag-view');
    const edit = card.querySelector('.tag-edit');
    view.classList.toggle('hidden');
    edit.classList.toggle('hidden');
}
</script>

<?php require_once '../includes/footer.php'; ?>
