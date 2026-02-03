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

    <!-- NEWS Card (Locked) -->
    <div class="admin-card group hover:border-primary transition-colors relative overflow-hidden">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-gray-900/90 backdrop-blur-[2px] z-10 flex flex-col items-center justify-center p-6 text-center" id="news-overlay">
            <div class="mb-4">
                <i class="fas fa-lock text-3xl text-gray-500 mb-2"></i>
                <h4 class="text-gray-300 font-bold tracking-widest">PREMIUM FEATURE</h4>
            </div>
            <p class="text-gray-400 text-xs mb-6">この機能を利用するには<br>有効化が必要です。</p>
            <button onclick="requestActivation(this)" 
                class="bg-primary hover:bg-yellow-500 text-black font-bold py-2 px-6 rounded-full text-xs tracking-widest transition-all transform hover:scale-105 shadow-lg shadow-primary/20">
                有効化をリクエストする
            </button>
        </div>

        <!-- Content (Blurred visually by overlay) -->
        <div class="filter blur-[1px] opacity-50 pointer-events-none">
            <div class="admin-card-header">
                <h3 class="admin-card-title"><i class="fas fa-newspaper mr-2 text-primary"></i> NEWS 管理</h3>
                <span class="bg-gray-700 text-xs font-bold px-2 py-1 rounded text-gray-300">投稿</span>
            </div>
            <div class="admin-card-body">
                <p class="text-gray-400 mb-6 text-sm">お知らせやブログ記事の投稿・管理ができます。</p>
                <div class="flex flex-col gap-3">
                    <a href="#" class="w-full text-center border border-gray-600 text-white py-2 rounded text-sm font-bold">記事一覧</a>
                    <a href="#" class="w-full text-center bg-gray-700 text-white py-2 rounded text-sm font-bold">新規投稿</a>
                </div>
            </div>
        </div>
    </div>

    <script>
    function requestActivation(btn) {
        // Change button state
        btn.textContent = 'リクエストを送信しました';
        btn.classList.remove('bg-primary', 'hover:bg-yellow-500', 'text-black', 'shadow-primary/20');
        btn.classList.add('bg-green-500', 'text-white', 'cursor-default');
        btn.onclick = null; // Disable click
        
        // Optional: Send async request to backend if needed in future
        // console.log('Activation requested');
    }
    </script>

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
                    サイト・アカウント設定
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>