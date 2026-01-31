<?php
require_once '../includes/auth.php';
require_once '../includes/header.php';
require_once '../includes/db.php';
require_once '../includes/upload_helper.php';

$id = $_GET['id'] ?? null;
$item = [
    'title' => '',
    'content' => '',
    'thumbnail' => '',
    'published_date' => date('Y-m-d'),
    'status' => 1
];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $fetched = $stmt->fetch();
    if ($fetched) {
        $item = $fetched;
    } else {
        die("News not found");
    }
}

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic Sanitation
    $title = trim($_POST['title']);
    $content = $_POST['content'];
    $published_date = $_POST['published_date'];
    $status = (int) $_POST['status'];

    // Handle File Upload
    $uploaded_thumb = handleUpload('thumbnail_file', '../../assets/images/uploads/');
    $thumbnail = $item['thumbnail']; // Default to existing
    if ($uploaded_thumb) {
        $thumbnail = str_replace('../../assets', '../assets', $uploaded_thumb);
    }

    try {
        if ($id) {
            // Update
            $sql = "UPDATE news SET title=?, content=?, thumbnail=?, published_date=?, status=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $content, $thumbnail, $published_date, $status, $id]);
            $success_msg = "記事を更新しました。";
        } else {
            // Insert
            $sql = "INSERT INTO news (title, content, thumbnail, published_date, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $content, $thumbnail, $published_date, $status]);
            $id = $pdo->lastInsertId();
            $success_msg = "記事を作成しました。";
            header("Location: edit.php?id=" . $id . "&created=1");
            exit;
        }

        // Reload
        $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();

    } catch (PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}
?>

<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold font-en tracking-widest text-white">
        <?php echo $id ? 'EDIT NEWS' : 'NEW POST'; ?>
    </h1>
    <a href="index.php" class="text-xs text-gray-400 hover:text-white transition-colors">
        <i class="fas fa-arrow-left mr-1"></i> 一覧に戻る
    </a>
</div>

<?php if (isset($_GET['created'])): ?>
    <div class="bg-green-900/50 border border-green-500 text-green-100 px-4 py-3 rounded mb-6">
        記事を作成しました。
    </div>
<?php endif; ?>
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

<form method="POST" class="admin-card" enctype="multipart/form-data">
    <div class="admin-card-body space-y-8">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="md:col-span-2 space-y-6">
                <div class="form-group">
                    <label class="form-label">タイトル (Title)</label>
                    <input type="text" name="title" class="form-input text-lg font-bold"
                        value="<?php echo htmlspecialchars($item['title']); ?>" required placeholder="記事のタイトルを入力">
                </div>

                <div class="form-group">
                    <label class="form-label">本文 (Content)</label>
                    <textarea name="content"
                        class="form-input h-[500px] font-mono text-sm leading-relaxed"><?php echo htmlspecialchars($item['content']); ?></textarea>
                    <p class="text-xs text-gray-500 mt-2">※HTMLタグが使用可能です。画像はアップロード後にパスを指定してください。</p>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publish Settings -->
                <div class="bg-gray-800/50 p-4 rounded border border-gray-700">
                    <label class="text-xs font-bold text-gray-400 block mb-3">公開設定</label>

                    <div class="form-group mb-4">
                        <label class="form-label text-xs">ステータス</label>
                        <select name="status" class="form-input text-sm">
                            <option value="1" <?php echo ($item['status'] == 1) ? 'selected' : ''; ?>>公開 (Published)
                            </option>
                            <option value="0" <?php echo ($item['status'] == 0) ? 'selected' : ''; ?>>下書き (Draft)
                            </option>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label text-xs">公開日</label>
                        <input type="date" name="published_date" class="form-input text-sm"
                            value="<?php echo htmlspecialchars($item['published_date']); ?>">
                    </div>

                    <button type="submit"
                        class="w-full bg-primary text-black font-bold py-2 rounded hover:bg-yellow-500 transition-colors text-sm">
                        <i class="fas fa-save mr-1"></i> 保 存
                    </button>
                    <?php if ($id): ?>
                        <a href="delete.php?id=<?php echo $id; ?>" onclick="return confirm('この記事を削除しますか？');"
                            class="block w-full text-center text-red-500 text-xs mt-3 hover:underline">
                            ゴミ箱へ移動
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Thumbnail -->
                <div class="bg-gray-800/50 p-4 rounded border border-gray-700">
                    <label class="text-xs font-bold text-gray-400 block mb-3">サムネイル画像</label>

                    <?php if ($item['thumbnail']): ?>
                        <div class="mb-3">
                            <img src="<?php echo htmlspecialchars($item['thumbnail']); ?>"
                                class="w-full h-auto rounded border border-gray-600">
                            <p class="text-[10px] text-gray-500 mt-1 break-all">
                                <?php echo htmlspecialchars($item['thumbnail']); ?></p>
                        </div>
                    <?php endif; ?>

                    <input type="file" name="thumbnail_file" class="text-gray-300 text-xs w-full">
                </div>
            </div>
        </div>

    </div>
</form>

<?php require_once '../includes/footer.php'; ?>