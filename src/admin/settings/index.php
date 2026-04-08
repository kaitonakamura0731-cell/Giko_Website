<?php
require_once '../includes/auth.php';
require_once '../includes/header.php';
require_once '../includes/settings_helper.php';
require_once '../includes/upload_helper.php';
require_once '../includes/db.php'; // Required for password update

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- 0. Vehicle Tag Images Upload ---
    if (isset($_FILES['tag_images'])) {
        $existing = json_decode(get_setting('vehicle_tag_images', '{}'), true) ?: [];
        foreach ($_FILES['tag_images']['name'] as $tagName => $fileName) {
            if ($_FILES['tag_images']['error'][$tagName] === UPLOAD_ERR_OK && $fileName) {
                $tmpName = $_FILES['tag_images']['tmp_name'][$tagName];
                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                $newName = time() . '_' . uniqid() . '.' . $ext;
                $targetDir = '../../assets/images/uploads/';
                $targetPath = $targetDir . $newName;
                if (move_uploaded_file($tmpName, $targetPath)) {
                    $existing[$tagName] = '../assets/images/uploads/' . $newName;
                }
            }
        }
        // Handle delete requests
        $deleteTags = $_POST['delete_tag_image'] ?? [];
        foreach ($deleteTags as $tagName => $val) {
            unset($existing[$tagName]);
        }
        update_setting('vehicle_tag_images', json_encode($existing, JSON_UNESCAPED_UNICODE));
        $success_msg = "車種タグ画像を更新しました。";
    }

    // --- 1. Password Update Logic ---
    if (!empty($_POST['new_password'])) {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'] ?? '';
        $user_id = $_SESSION['user_id'];

        if (empty($current_password)) {
            $error_msg = "現在のパスワードを入力してください。";
        } elseif ($new_password !== $confirm_password) {
            $error_msg = "新しいパスワードが一致しません。";
        } else {
            // Verify Current Password
            try {
                $stmt = $pdo->prepare("SELECT password_hash FROM admin_users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();

                if ($user && password_verify($current_password, $user['password_hash'])) {
                    // Update Password
                    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_stmt = $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?");
                    $update_stmt->execute([$new_hash, $user_id]);
                    $success_msg = "パスワードを変更しました。";
                } else {
                    $error_msg = "現在のパスワードが正しくありません。";
                }
            } catch (PDOException $e) {
                $error_msg = "エラーが発生しました: " . $e->getMessage();
            }
        }
    }

    // --- 2. Settings Update Logic ---
    // Only proceed if no error from password update (or independent? Let's process independent but accumulate msgs)
    if (empty($error_msg) && isset($_POST['site_title'])) {
        $settings_to_update = [
            'site_title',
            'site_description',
            'company_name',
            'company_address',
            'company_tel',
            'company_email',
            'company_hours',
            'social_instagram',
            'social_twitter',
            'social_youtube',
            'social_tiktok',
            'social_line'
        ];

        $updated_count = 0;
        foreach ($settings_to_update as $key) {
            if (isset($_POST[$key])) {
                if (update_setting($key, $_POST[$key])) {
                    $updated_count++;
                }
            }
        }

        if ($updated_count > 0 || !empty($success_msg)) {
            if (empty($success_msg))
                $success_msg = "設定を保存しました。";
            else
                $success_msg .= " 設定も保存しました。";
        } elseif (empty($success_msg)) {
            // Only error if password wasn't the main action
            // $error_msg = "設定の保存に失敗したか、変更がありません。";
        }
    }
}
?>

<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold font-en tracking-widest text-white">SITE SETTINGS</h1>
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

<form method="POST" class="admin-card" enctype="multipart/form-data">
    <div class="admin-card-body space-y-10">

        <!-- General Settings -->
        <div>
            <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-700 pb-2">基本設定 (General)</h3>
            <div class="grid grid-cols-1 gap-6">
                <div class="form-group">
                    <label class="form-label">サイトタイトル (Site Title)</label>
                    <input type="text" name="site_title" class="form-input"
                        value="<?php echo htmlspecialchars(get_setting('site_title')); ?>" placeholder="技巧 -GIKO-">
                </div>
                <div class="form-group">
                    <label class="form-label">サイト説明 (Description)</label>
                    <textarea name="site_description" class="form-input h-24"
                        placeholder="職人の手による最高級本革シート張り替え..."><?php echo htmlspecialchars(get_setting('site_description')); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Company Info -->
        <div>
            <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-700 pb-2">会社情報 (Company)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label class="form-label">会社名 (Company Name)</label>
                    <input type="text" name="company_name" class="form-input"
                        value="<?php echo htmlspecialchars(get_setting('company_name')); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">住所 (Address)</label>
                    <input type="text" name="company_address" class="form-input"
                        value="<?php echo htmlspecialchars(get_setting('company_address')); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">電話番号 (TEL)</label>
                    <input type="text" name="company_tel" class="form-input"
                        value="<?php echo htmlspecialchars(get_setting('company_tel')); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">メールアドレス (Email)</label>
                    <input type="email" name="company_email" class="form-input"
                        value="<?php echo htmlspecialchars(get_setting('company_email')); ?>">
                </div>
                <div class="form-group md:col-span-2">
                    <label class="form-label">営業時間 (Hours)</label>
                    <textarea name="company_hours"
                        class="form-input h-20"><?php echo htmlspecialchars(get_setting('company_hours')); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Social Media -->
        <div>
            <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-700 pb-2">SNSリンク</h3>
            <div class="grid grid-cols-1 gap-6">
                <div class="form-group">
                    <label class="form-label"><i class="fab fa-instagram mr-2"></i>Instagram URL</label>
                    <input type="text" name="social_instagram" class="form-input"
                        value="<?php echo htmlspecialchars(get_setting('social_instagram')); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fab fa-twitter mr-2"></i>X (Twitter) URL</label>
                    <input type="text" name="social_twitter" class="form-input"
                        value="<?php echo htmlspecialchars(get_setting('social_twitter')); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fab fa-youtube mr-2"></i>YouTube URL</label>
                    <input type="text" name="social_youtube" class="form-input"
                        value="<?php echo htmlspecialchars(get_setting('social_youtube')); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fab fa-tiktok mr-2"></i>TikTok URL</label>
                    <input type="text" name="social_tiktok" class="form-input"
                        value="<?php echo htmlspecialchars(get_setting('social_tiktok')); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fab fa-line mr-2"></i>LINE 公式 URL</label>
                    <input type="text" name="social_line" class="form-input"
                        value="<?php echo htmlspecialchars(get_setting('social_line')); ?>">
                </div>
            </div>
        </div>

        <!-- Vehicle Tag Images -->
        <div>
            <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-700 pb-2">車種タグ画像 (Vehicle Tag Images)</h3>
            <p class="text-xs text-gray-500 mb-4">※ストアのフィルターボタンに表示される背景画像を車種タグごとに設定できます。未設定の場合は商品画像が自動で使用されます。</p>
            <?php
            // Fetch all unique vehicle tags from products
            $tagStmt = $pdo->query("SELECT vehicle_tags FROM products WHERE vehicle_tags IS NOT NULL AND vehicle_tags != ''");
            $tagRows = $tagStmt->fetchAll();
            $uniqueTags = [];
            foreach ($tagRows as $row) {
                foreach (explode(',', $row['vehicle_tags']) as $t) {
                    $t = trim($t);
                    if ($t && !in_array($t, $uniqueTags)) $uniqueTags[] = $t;
                }
            }
            sort($uniqueTags);
            $tagImages = json_decode(get_setting('vehicle_tag_images', '{}'), true) ?: [];
            ?>
            <?php if (empty($uniqueTags)): ?>
                <p class="text-gray-500 text-sm">商品に車種タグが設定されていません。</p>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($uniqueTags as $tag):
                    $currentImg = $tagImages[$tag] ?? '';
                    $displayImg = $currentImg;
                    if ($currentImg && strpos($currentImg, '../assets') === 0) {
                        $displayImg = '../../assets' . substr($currentImg, strlen('../assets'));
                    }
                ?>
                    <div class="bg-gray-900 p-4 rounded border border-gray-700">
                        <div class="flex items-center gap-4">
                            <?php if ($currentImg && file_exists(__DIR__ . '/' . $displayImg)): ?>
                                <div class="relative group flex-shrink-0">
                                    <img src="<?php echo htmlspecialchars($displayImg); ?>"
                                        class="h-20 w-28 rounded border border-gray-600 object-cover"
                                        onerror="this.parentElement.innerHTML='<div class=\'h-20 w-28 bg-red-900/20 rounded border border-red-700 flex items-center justify-center text-red-400 text-xs\'>読込失敗</div>'">
                                </div>
                            <?php else: ?>
                                <div class="h-20 w-28 bg-gray-800 rounded border border-gray-700 flex items-center justify-center text-gray-500 text-xs flex-shrink-0">
                                    <div class="text-center">
                                        <i class="fas fa-car text-lg mb-1"></i>
                                        <p class="text-[10px]">未設定</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-sm mb-2"><?php echo htmlspecialchars($tag); ?></p>
                                <input type="file" name="tag_images[<?php echo htmlspecialchars($tag); ?>]"
                                    class="form-input text-xs py-1" accept="image/*">
                                <?php if ($currentImg): ?>
                                    <label class="flex items-center gap-2 mt-2 text-xs text-red-400 cursor-pointer">
                                        <input type="checkbox" name="delete_tag_image[<?php echo htmlspecialchars($tag); ?>]" value="1">
                                        画像を削除
                                    </label>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Account Settings -->
        <div class="bg-gray-800 p-6 rounded border border-gray-700">
            <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-600 pb-2">管理者アカウント設定 (Account)</h3>
            <p class="text-xs text-gray-400 mb-4">※パスワードを変更する場合のみ入力してください。</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="form-group">
                    <label class="form-label">現在のパスワード</label>
                    <input type="password" name="current_password" class="form-input" autocomplete="off">
                </div>
                <div class="form-group">
                    <label class="form-label">新しいパスワード</label>
                    <input type="password" name="new_password" class="form-input" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label class="form-label">新しいパスワード（確認）</label>
                    <input type="password" name="confirm_password" class="form-input" autocomplete="new-password">
                </div>
            </div>
        </div>

        <div class="pt-6 border-t border-gray-700">
            <button type="submit"
                class="bg-primary text-black font-bold py-3 px-8 rounded hover:bg-yellow-500 transition-colors w-full md:w-auto">
                <i class="fas fa-save mr-2"></i> 設定をすべて保存
            </button>
        </div>

    </div>
</form>

<?php require_once '../includes/footer.php'; ?>