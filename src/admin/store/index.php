<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
checkAuth();

try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "DB Error: " . $e->getMessage();
}

require_once '../includes/header.php';
?>

<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold font-en tracking-widest text-white">STORE 管理</h1>
    <a href="edit.php"
        class="bg-primary hover:bg-yellow-500 text-black font-bold py-2 px-4 rounded transition-colors tracking-widest font-en text-sm">
        <i class="fas fa-plus mr-2"></i> 新規商品追加
    </a>
</div>

<div class="admin-card">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-700 text-gray-400 text-xs uppercase tracking-wider">
                    <th class="p-4 font-bold border-b border-gray-600">ID</th>
                    <th class="p-4 font-bold border-b border-gray-600">商品名</th>
                    <th class="p-4 font-bold border-b border-gray-600">価格</th>
                    <th class="p-4 font-bold border-b border-gray-600">在庫</th>
                    <th class="p-4 font-bold border-b border-gray-600 text-right">操作</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <tr class="hover:bg-gray-750 transition-colors">
                            <td class="p-4 text-gray-300">#<?php echo $product['id']; ?></td>
                            <td class="p-4 font-bold text-white"><?php echo htmlspecialchars($product['name']); ?></td>
                            <td class="p-4">¥<?php echo number_format($product['price']); ?></td>
                            <td class="p-4">
                                <?php if ($product['stock_status']): ?>
                                    <span class="text-green-400 text-xs border border-green-400 px-2 py-0.5 rounded">在庫あり</span>
                                <?php else: ?>
                                    <span class="text-red-400 text-xs border border-red-400 px-2 py-0.5 rounded">SOLD OUT</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-right">
                                <a href="edit.php?id=<?php echo $product['id']; ?>"
                                    class="text-blue-400 hover:text-blue-300 mr-3 transition-colors"><i
                                        class="fas fa-edit"></i></a>
                                <a href="delete.php?id=<?php echo $product['id']; ?>"
                                    class="text-red-400 hover:text-red-300 transition-colors"
                                    onclick="return confirm('削除してもよろしいですか？');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500">
                            登録された商品はありません。
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>