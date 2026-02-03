<?php
require_once '../includes/auth.php';
require_once '../includes/header.php';

// Mock Email Logic
$req_success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_enable'])) {
    // mail('admin@example.com', 'Premium Feature Request', 'Client requested News feature activation.'); 
    $req_success = '有効化リクエストを送信しました。管理担当者よりご連絡いたします。';
}

// Fetch News (Disabled/Mock fetch or keep existing to show background)
// Just keeping it empty or sample for visual is fine, but let's keep logic so it looks real behind the mask
require_once '../includes/db.php';
try {
    $stmt = $pdo->query("SELECT * FROM news ORDER BY published_date DESC, created_at DESC");
    $news_list = $stmt->fetchAll();
} catch (PDOException $e) {
    // $error = "Error: " . $e->getMessage();
    $news_list = [];
}
?>

<div class="mb-6 flex justify-between items-center relative z-0">
    <div>
        <h1 class="text-2xl font-bold font-en tracking-widest text-white">NEWS MANAGEMENT</h1>
        <p class="text-xs text-gray-400 mt-1">お知らせ・ニュースの管理</p>
    </div>
    <div class="flex gap-4 opacity-50 pointer-events-none">
        <a href="#" class="text-xs text-gray-400 flex items-center">
            <i class="fas fa-arrow-left mr-1"></i> ダッシュボード
        </a>
        <a href="#" class="bg-primary text-black text-sm font-bold px-4 py-2 rounded">
            <i class="fas fa-plus mr-1"></i> 新規投稿
        </a>
    </div>
</div>

<?php if ($req_success): ?>
    <div
        class="bg-green-900/80 border border-green-500 text-green-100 px-6 py-4 rounded mb-6 text-center font-bold shadow-lg relative z-50">
        <i class="fas fa-check-circle mr-2"></i> <?php echo htmlspecialchars($req_success); ?>
    </div>
<?php endif; ?>

<!-- Premium Overlay Wrapper -->
<div class="relative w-full h-[600px] overflow-hidden rounded-lg border border-gray-700 bg-gray-900">

    <!-- The Overlay -->
    <div
        class="absolute inset-0 z-40 bg-black/60 backdrop-blur-sm flex flex-col items-center justify-center text-center p-6">
        <div class="bg-gray-800 p-8 rounded-xl border border-gray-600 shadow-2xl max-w-lg w-full">
            <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-400">
                <i class="fas fa-lock text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold font-en text-white mb-2">PREMIUM FEATURE</h2>
            <p class="text-sm text-gray-400 mb-8 leading-relaxed">
                ニュース・お知らせ配信機能は、スタンダードプラン以上でご利用いただけます。<br>
                導入をご希望の場合は、下記より有効化リクエストをお送りください。
            </p>

            <?php if (!$req_success): ?>
                <form method="POST">
                    <input type="hidden" name="request_enable" value="1">
                    <button type="submit"
                        class="bg-primary hover:bg-yellow-500 text-black font-bold py-3 px-8 rounded-full transition-all transform hover:scale-105 shadow-lg w-full">
                        <i class="fas fa-paper-plane mr-2"></i> 有効化をリクエストする
                    </button>
                    <p class="text-[10px] text-gray-500 mt-4">※担当者に通知メールが送信されます。</p>
                </form>
            <?php else: ?>
                <button disabled
                    class="bg-green-600 text-white font-bold py-3 px-8 rounded-full cursor-default w-full opacity-80">
                    <i class="fas fa-check mr-2"></i> リクエスト送信済み
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Background Content (Blurred & Unclickable) -->
    <div class="relative z-0 opacity-30 select-none pointer-events-none filter blur-[2px]">
        <div class="admin-card">
            <div class="admin-card-body p-0">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/10 text-xs text-gray-400">
                            <th class="p-4 font-normal">ステータス</th>
                            <th class="p-4 font-normal">日付</th>
                            <th class="p-4 font-normal">タイトル</th>
                            <th class="p-4 font-normal text-right">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Mock Data for Background Effect -->
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <tr class="border-b border-white/5">
                                <td class="p-4"><span
                                        class="bg-gray-700 text-gray-300 text-[10px] px-2 py-0.5 rounded">公開中</span></td>
                                <td class="p-4 text-sm font-en text-gray-400">2026.01.<?php echo 20 - $i; ?></td>
                                <td class="p-4 text-gray-500 font-bold">サンプルニュース記事 <?php echo $i + 1; ?></td>
                                <td class="p-4 text-right"><i class="fas fa-ellipsis-h text-gray-600"></i></td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>