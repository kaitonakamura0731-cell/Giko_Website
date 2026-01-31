<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
checkAuth();

// Fetch works
try {
    $stmt = $pdo->query("SELECT * FROM works ORDER BY created_at DESC");
    $works = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

require_once '../includes/header.php';
?>

<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold font-en tracking-widest text-white">WORKS 管理</h1>
    <a href="edit.php"
        class="bg-primary hover:bg-yellow-500 text-black font-bold py-2 px-4 rounded transition-colors tracking-widest font-en text-sm">
        <i class="fas fa-plus mr-2"></i> 新規追加
    </a>
</div>

<div class="admin-card">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-700 text-gray-400 text-xs uppercase tracking-wider">
                    <th class="p-4 font-bold border-b border-gray-600">ID</th>
                    <th class="p-4 font-bold border-b border-gray-600">画像</th>
                    <th class="p-4 font-bold border-b border-gray-600">タイトル / サブタイトル</th>
                    <th class="p-4 font-bold border-b border-gray-600">カテゴリー</th>
                    <th class="p-4 font-bold border-b border-gray-600">登録日</th>
                    <th class="p-4 font-bold border-b border-gray-600 text-right">操作</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                <?php if (count($works) > 0): ?>
                    <?php foreach ($works as $work): ?>
                        <tr class="hover:bg-gray-750 transition-colors">
                            <td class="p-4 text-gray-300">#<?php echo $work['id']; ?></td>
                            <td class="p-4">
                                <?php if ($work['main_image']): ?>
                                    <img src="<?php echo htmlspecialchars($work['main_image']); ?>" alt="img"
                                        class="w-12 h-8 object-cover rounded border border-gray-600">
                                <?php else: ?>
                                    <div
                                        class="w-12 h-8 bg-gray-700 rounded flex items-center justify-center text-gray-500 text-xs">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-white"><?php echo htmlspecialchars($work['title']); ?></div>
                                <div class="text-xs text-gray-400"><?php echo htmlspecialchars($work['subtitle']); ?></div>
                            </td>
                            <td class="p-4">
                                <span class="bg-gray-700 py-1 px-2 rounded text-xs text-primary font-en tracking-wide">
                                    <?php echo htmlspecialchars($work['category']); ?>
                                </span>
                            </td>
                            <td class="p-4 text-gray-400 text-sm">
                                <?php echo date('Y-m-d', strtotime($work['created_at'])); ?>
                            </td>
                            <td class="p-4 text-right">
                                <a href="edit.php?id=<?php echo $work['id']; ?>"
                                    class="text-blue-400 hover:text-blue-300 mr-3 transition-colors"><i
                                        class="fas fa-edit"></i></a>
                                <a href="delete.php?id=<?php echo $work['id']; ?>"
                                    class="text-red-400 hover:text-red-300 transition-colors"
                                    onclick="return confirm('削除してもよろしいですか？');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500">
                            登録された実績はありません。<br><a href="edit.php" class="text-primary hover:underline">新規追加する</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>