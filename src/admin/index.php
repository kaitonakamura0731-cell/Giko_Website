<?php
require_once 'includes/auth.php'; // Checks auth or redirects to login
checkAuth();
require_once 'includes/header.php';
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- WORKS Card -->
    <div class="admin-card group hover:border-primary transition-colors">
        <div class="admin-card-header">
            <h3 class="admin-card-title"><i class="fas fa-tools mr-2 text-primary"></i> WORKS 管理</h3>
            <span class="bg-gray-700 text-xs font-bold px-2 py-1 rounded text-gray-300">管理</span>
        </div>
        <div class="admin-card-body">
            <p class="text-gray-400 mb-6 text-sm">施工事例（WORKS）の管理を行います。新規追加や編集が可能です。</p>
            <div class="flex flex-col gap-3">
                <a href="works/index.php"
                    class="w-full text-center border border-gray-600 hover:border-primary hover:text-primary text-white py-2 rounded transition-colors text-sm font-bold">
                    一覧を表示
                </a>
                <a href="works/edit.php"
                    class="w-full text-center bg-primary hover:bg-yellow-500 text-black py-2 rounded transition-colors text-sm font-bold">
                    新規追加
                </a>
            </div>
        </div>
    </div>

    <!-- STORE Card -->
    <div class="admin-card group hover:border-primary transition-colors">
        <div class="admin-card-header">
            <h3 class="admin-card-title"><i class="fas fa-shopping-cart mr-2 text-primary"></i> STORE 商品確認</h3>
            <span class="bg-gray-700 text-xs font-bold px-2 py-1 rounded text-gray-300">閲覧</span>
        </div>
        <div class="admin-card-body">
            <p class="text-gray-400 mb-6 text-sm">商品情報の閲覧や在庫状態の確認を行います。</p>
            <div class="flex flex-col gap-3">
                <a href="store/index.php"
                    class="w-full text-center border border-gray-600 hover:border-primary hover:text-primary text-white py-2 rounded transition-colors text-sm font-bold">
                    商品一覧を表示
                </a>
            </div>
        </div>
    </div>

    <!-- NEWS Card -->
    <div class="admin-card group hover:border-primary transition-colors">
        <div class="admin-card-header">
            <h3 class="admin-card-title"><i class="fas fa-newspaper mr-2 text-primary"></i> NEWS 管理</h3>
            <span class="bg-gray-700 text-xs font-bold px-2 py-1 rounded text-gray-300">投稿</span>
        </div>
        <div class="admin-card-body">
            <p class="text-gray-400 mb-6 text-sm">お知らせやブログ記事の投稿・管理ができます。</p>
            <div class="flex flex-col gap-3">
                <a href="news/index.php"
                    class="w-full text-center border border-gray-600 hover:border-primary hover:text-primary text-white py-2 rounded transition-colors text-sm font-bold">
                    記事一覧
                </a>
                <a href="news/edit.php"
                    class="w-full text-center bg-gray-700 hover:bg-gray-600 text-white py-2 rounded transition-colors text-sm font-bold">
                    新規投稿
                </a>
            </div>
        </div>
    </div>

    <!-- SETTINGS Card -->
    <div class="admin-card group hover:border-primary transition-colors">
        <div class="admin-card-header">
            <h3 class="admin-card-title"><i class="fas fa-cog mr-2 text-primary"></i> 設定</h3>
            <span class="bg-gray-700 text-xs font-bold px-2 py-1 rounded text-gray-300">管理</span>
        </div>
        <div class="admin-card-body">
            <p class="text-gray-400 mb-6 text-sm">システム設定や管理者アカウントの管理を行います。</p>
            <div class="flex flex-col gap-3">
                <a href="settings/index.php"
                    class="block w-full text-center border border-gray-600 hover:border-primary hover:text-primary text-white py-2 rounded transition-colors text-sm font-bold">
                    サイト設定
                </a>
                <a href="account/index.php"
                    class="block w-full text-center border border-gray-600 hover:border-primary hover:text-primary text-white py-2 rounded transition-colors text-sm font-bold">
                    アカウント設定
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>