<?php
require_once '../includes/auth.php';
require_once '../includes/header.php';
require_once '../includes/settings_helper.php';
require_once '../includes/db.php'; // Required for password update

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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
            'social_youtube'
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

<form method="POST" class="admin-card">
    <div class="admin-card-body space-y-10">

        <!-- General Settings -->
        <div>
            <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-700 pb-2">基本設定 (General)</h3>
            <div class="grid grid-cols-1 gap-6">
                <div class="form-group">
                    <label class="form-label">サイトタイトル (Site Title)</label>
                    <input type="text" name="site_title" class="form-input"
                        value="<?php echo htmlspecialchars(get_setting('site_title')); ?>" placeholder="技巧 -Giko-">
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
            </div>
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