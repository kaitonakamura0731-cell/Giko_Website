<?php
require_once '../includes/auth.php';
require_once '../includes/header.php';
require_once '../includes/db.php';

// Fetch News
try {
    $stmt = $pdo->query("SELECT * FROM news ORDER BY published_date DESC, created_at DESC");
    $news_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold font-en tracking-widest text-white">NEWS MANAGEMENT</h1>
        <p class="text-xs text-gray-400 mt-1">お知らせ・ニュースの管理</p>
    </div>
    <div class="flex gap-4">
        <a href="../index.php" class="text-xs text-gray-400 hover:text-white transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-1"></i> ダッシュボード
        </a>
        <a href="edit.php"
            class="bg-primary text-black text-sm font-bold px-4 py-2 rounded hover:bg-yellow-500 transition-colors">
            <i class="fas fa-plus mr-1"></i> 新規投稿
        </a>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="bg-red-900/50 border border-red-500 text-red-100 px-4 py-3 rounded mb-6">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

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
                <?php if (empty($news_list)): ?>
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-500 text-sm">現在、お知らせはありません。</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($news_list as $news): ?>
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors group">
                            <td class="p-4">
                                <?php if ($news['status'] == 1): ?>
                                    <span
                                        class="bg-primary/20 text-primary text-[10px] px-2 py-0.5 rounded border border-primary/30">公開中</span>
                                <?php else: ?>
                                    <span class="bg-gray-700 text-gray-300 text-[10px] px-2 py-0.5 rounded">下書き</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-sm font-en text-gray-400">
                                <?php echo htmlspecialchars($news['published_date']); ?>
                            </td>
                            <td class="p-4">
                                <a href="edit.php?id=<?php echo $news['id']; ?>"
                                    class="font-bold hover:text-primary transition-colors block">
                                    <?php echo htmlspecialchars($news['title']); ?>
                                </a>
                            </td>
                            <td class="p-4 text-right">
                                <a href="edit.php?id=<?php echo $news['id']; ?>" class="text-gray-400 hover:text-white mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $news['id']; ?>" class="text-gray-400 hover:text-red-500"
                                    onclick="return confirm('本当に削除しますか？');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>