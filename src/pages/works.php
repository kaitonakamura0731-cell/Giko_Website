<?php
require_once '../admin/includes/db.php';

// カテゴリ定義
$categories = [
    'all' => 'ALL',
    'partial' => '部分内装',
    'full' => '全内装',
    'package' => 'パッケージ',
    'ambient' => 'アンビエントライト',
    'starlight' => 'スターライト',
    'newbiz' => '新事業',
];

// URLパラメータからカテゴリを取得
$activeCategory = isset($_GET['category']) ? $_GET['category'] : 'all';
if (!array_key_exists($activeCategory, $categories)) {
    $activeCategory = 'all';
}

// Fetch works
try {
    $stmt = $pdo->query("SELECT * FROM works ORDER BY created_at DESC");
    $works = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// カテゴリバッジ設定
$categoryBadges = [
    'partial' => ['label' => '部分内装', 'color' => 'bg-blue-600/90'],
    'full' => ['label' => '全内装', 'color' => 'bg-primary/90'],
    'package' => ['label' => 'パッケージ', 'color' => 'bg-purple-600/90'],
    'ambient' => ['label' => 'アンビエントライト', 'color' => 'bg-green-600/90'],
    'starlight' => ['label' => 'スターライト', 'color' => 'bg-indigo-500/90'],
    'newbiz' => ['label' => '新事業', 'color' => 'bg-red-600/90'],
];
?>
<!DOCTYPE html>
<html lang="ja" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WORKS | 技巧 -Giko-</title>
    <!-- OGP -->
    <meta property="og:title" content="WORKS | 技巧 -Giko-">
    <meta property="og:description" content="施工実績一覧。最高級の技術で仕上げた作品をご覧ください。">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://giko-official.com/pages/works.php">
    <meta property="og:image" content="https://giko-official.com/assets/images/ogp.jpg">
    <meta name="twitter:card" content="summary_large_image">
    <!-- <link rel="icon" href="../assets/images/favicon.ico"> -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../tailwind_config.js"></script>
    <script src="../assets/js/cart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* フィルターアニメーション */
        .work-item {
            transition: opacity 0.4s ease, transform 0.4s ease;
        }
        .work-item.hidden-by-filter {
            opacity: 0;
            transform: scale(0.95);
            position: absolute;
            pointer-events: none;
            width: 0;
            height: 0;
            overflow: hidden;
            margin: 0;
            padding: 0;
            border: 0;
        }
        .work-item.visible-by-filter {
            opacity: 1;
            transform: scale(1);
        }
    </style>
</head>

<body class="bg-black text-white antialiased">
    <!-- Header -->
    <header class="fixed w-full z-50 transition-all duration-300 bg-black/80 backdrop-blur-md border-b border-white/5"
        id="header">
        <div class="container mx-auto px-6 h-20 flex justify-between items-center">
            <a href="../index.php" class="flex items-center group">
                <img src="../assets/images/logo_new.png" alt="GIKO" class="h-10 group-hover:opacity-80 transition-opacity">
            </a>
            <nav class="hidden lg:flex space-x-10 text-xs font-bold tracking-widest">
                <a href="../index.php#concept"
                    class="hover:text-primary transition-colors font-en relative group">CONCEPT</a>
                <a href="../pages/works.php" class="text-primary font-en relative group">WORKS</a>
                <a href="../store/index.php"
                    class="hover:text-primary transition-colors font-en relative group">STORE</a>
                <a href="../pages/before_after.html"
                    class="hover:text-primary transition-colors font-en relative group">BEFORE &
                    AFTER</a>
                <a href="../index.php#material"
                    class="hover:text-primary transition-colors font-en relative group">MATERIAL</a>
                <a href="../index.php#flow" class="hover:text-primary transition-colors font-en relative group">FLOW</a>
                <a href="../index.php#company"
                    class="hover:text-primary transition-colors font-en relative group">COMPANY</a>
            </nav>
            <a href="../contact/index.php"
                class="hidden lg:inline-flex items-center justify-center px-8 py-2.5 border border-white/20 text-xs font-bold tracking-widest hover:bg-primary hover:border-primary hover:text-white transition-all duration-300 font-en">CONTACT</a>
            <!-- Language Switcher Desktop -->
            <button id="lang-toggle-desktop"
                class="hidden lg:flex ml-6 items-center gap-2 text-xs font-bold font-en tracking-widest opacity-80 hover:opacity-100 transition-opacity">
                <span class="text-primary">JP</span>
                <span class="text-white/30">/</span>
                <span class="text-white">EN</span>
            </button>
            <!-- Cart Icon Desktop -->
            <a href="../store/cart.html" class="hidden lg:flex ml-6 relative group">
                <i class="fas fa-shopping-cart text-white text-lg group-hover:text-primary transition-colors"></i>
                <span id="cart-badge-desktop"
                    class="cart-badge absolute -top-2 -right-2 bg-primary text-black text-[10px] font-bold px-1.5 rounded-full hidden">0</span>
            </a>
            <button class="lg:hidden text-white focus:outline-none ml-4 relative" id="mobile-menu-btn">
                <div class="space-y-2">
                    <span class="block w-8 h-0.5 bg-white"></span>
                    <span class="block w-8 h-0.5 bg-white"></span>
                </div>
                <!-- Cart Badge Mobile -->
                <span id="cart-badge-mobile-btn"
                    class="cart-badge absolute -top-2 -right-2 bg-primary text-black text-[10px] font-bold px-1.5 rounded-full hidden">0</span>
            </button>
        </div>
        <div class="lg:hidden hidden bg-secondary border-t border-white/10 absolute w-full top-20 left-0 h-screen"
            id="mobile-menu">
            <nav class="flex flex-col p-10 space-y-8 text-center text-lg">
                <a href="../store/cart.html"
                    class="text-white hover:text-primary font-en tracking-widest flex items-center justify-center gap-2">
                    <i class="fas fa-shopping-cart"></i> CART <span id="cart-badge-menu"
                        class="cart-badge bg-primary text-black text-xs font-bold px-2 py-0.5 rounded-full hidden">0</span>
                </a>
                <a href="../index.php#concept" class="text-white hover:text-primary font-en tracking-widest">CONCEPT</a>
                <a href="../pages/works.php" class="text-primary font-en tracking-widest">WORKS</a>
                <a href="../store/index.php" class="text-white hover:text-primary font-en tracking-widest">STORE</a>
                <a href="../pages/before_after.html"
                    class="text-white hover:text-primary font-en tracking-widest">BEFORE &
                    AFTER</a>
                <a href="../index.php#material"
                    class="text-white hover:text-primary font-en tracking-widest">MATERIAL</a>
                <a href="../index.php#flow" class="text-white hover:text-primary font-en tracking-widest">FLOW</a>
                <a href="../index.php#company" class="text-white hover:text-primary font-en tracking-widest">COMPANY</a>
                <a href="../contact/index.php" class="text-primary font-bold font-en tracking-widest mt-8">CONTACT</a>
                <!-- Language Switcher Mobile -->
                <button id="lang-toggle-mobile"
                    class="mt-8 flex items-center justify-center gap-4 text-sm font-bold font-en tracking-widest">
                    <span class="text-primary">JP</span>
                    <span class="text-white/30">/</span>
                    <span class="text-white">EN</span>
                </button>
            </nav>
        </div>
    </header>

    <!-- Page Title -->
    <section class="relative h-[40vh] min-h-[300px] flex items-center justify-center bg-secondary">
        <div class="text-center z-10">
            <h1 class="text-4xl md:text-6xl font-bold font-en tracking-widest mb-4">WORKS</h1>
            <p class="text-gray-400 text-sm tracking-widest font-en uppercase">Our Masterpieces</p>
        </div>
        <div class="absolute inset-0 bg-[url('../assets/images/hero.png')] bg-cover bg-center opacity-20"></div>
    </section>

    <!-- Works Grid -->
    <section class="py-24 bg-black">
        <div class="container mx-auto px-6">

            <!-- カテゴリフィルターボタン -->
            <div class="mb-16">
                <div class="flex flex-wrap justify-center gap-3 max-w-5xl mx-auto">
                    <?php foreach ($categories as $catId => $catLabel): ?>
                        <button
                            data-filter="<?php echo $catId; ?>"
                            class="works-filter-btn px-6 py-3 text-sm font-bold tracking-wider border transition-all duration-300
                                <?php if ($activeCategory === $catId): ?>
                                    border-primary bg-primary text-black
                                <?php else: ?>
                                    border-white/20 text-white hover:bg-primary hover:text-black hover:border-primary
                                <?php endif; ?>
                            ">
                            <?php echo $catLabel; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ワークス一覧グリッド -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="works-grid">
                <?php foreach ($works as $work): ?>
                    <?php
                    $category = $work['category'] ?? 'full';
                    $badge = $categoryBadges[$category] ?? ['label' => 'OTHER', 'color' => 'bg-gray-700/90'];
                    $link = 'work_detail.php?id=' . $work['id'];
                    ?>
                    <!-- Work Item -->
                    <a href="<?php echo $link; ?>" class="group block relative overflow-hidden bg-secondary work-item visible-by-filter"
                        data-category="<?php echo htmlspecialchars($category); ?>">
                        <div class="relative aspect-[16/10] overflow-hidden">
                            <?php if ($work['main_image']): ?>
                                <img src="<?php echo '../' . htmlspecialchars($work['main_image']); ?>"
                                    alt="<?php echo htmlspecialchars($work['title']); ?>"
                                    class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                            <?php else: ?>
                                <div class="w-full h-full bg-gray-800 flex items-center justify-center text-gray-500">No Image</div>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors duration-300"></div>
                            <div class="absolute top-4 left-4">
                                <span class="<?php echo $badge['color']; ?> text-white text-[10px] font-bold px-3 py-1 font-en tracking-widest">
                                    <?php echo $badge['label']; ?>
                                </span>
                            </div>
                        </div>
                        <div class="p-6 border-b border-white/10 group-hover:border-primary transition-colors duration-300">
                            <h3 class="text-xl font-bold font-en tracking-widest mb-2 group-hover:text-primary transition-colors">
                                <?php echo htmlspecialchars($work['title']); ?>
                            </h3>
                            <p class="text-xs text-gray-500 font-en tracking-wider mb-4">
                                <?php echo htmlspecialchars($work['subtitle']); ?>
                            </p>
                            <div class="text-xs text-gray-400 leading-relaxed line-clamp-2">
                                <?php echo htmlspecialchars($work['description']); ?>
                            </div>
                            <div class="mt-6 flex items-center text-[10px] font-bold tracking-widest font-en text-white group-hover:text-primary transition-colors">
                                VIEW DETAILS <i class="fas fa-arrow-right ml-2 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300"></i>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- フィルタ結果が0件の時 -->
            <div id="no-results" class="hidden text-center py-20">
                <i class="fas fa-search text-gray-700 text-4xl mb-4"></i>
                <p class="text-gray-500 text-lg">該当するワークスが見つかりませんでした。</p>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-secondary pt-24 pb-12 border-t border-white/5 text-white">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-20">
                <div>
                    <img src="../assets/images/logo_new.png" alt="GIKO" class="h-8 mb-6">
                    <p class="text-xs text-gray-500 leading-loose mb-6">最高級の素材と技術で、カーライフに彩りを。</p>
                    <div class="flex space-x-4">
                        <a href="https://www.instagram.com/giko_artisan?igsh=MWRuenVqMzBkNzA3bw==" target="_blank"
                            class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary transition-colors"><i
                                class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div>
                    <h3
                        class="text-sm font-bold font-en tracking-widest mb-6 border-b border-primary/30 inline-block pb-2">
                        MENU</h3>
                    <ul class="space-y-4 text-xs tracking-wider text-gray-400">
                        <li><a href="../index.php#concept" class="hover:text-white transition-colors">CONCEPT</a></li>
                        <li><a href="../pages/works.php" class="hover:text-white transition-colors">WORKS</a></li>
                        <li><a href="before_after.html" class="hover:text-white transition-colors">BEFORE &
                                AFTER</a>
                        </li>
                        <li><a href="../index.php#material" class="hover:text-white transition-colors">MATERIAL</a>
                        </li>
                        <li><a href="../index.php#flow" class="hover:text-white transition-colors">FLOW</a></li>
                        <li><a href="../index.php#company" class="hover:text-white transition-colors">COMPANY</a></li>
                    </ul>
                </div>
                <div>
                    <h3
                        class="text-sm font-bold font-en tracking-widest mb-6 border-b border-primary/30 inline-block pb-2">
                        CONTACT</h3>
                    <ul class="space-y-4 text-xs tracking-wider text-gray-400">
                        <li class="flex items-start gap-4">
                            <a href="../contact/index.php" class="hover:text-white transition-colors">お問い合わせフォーム</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3
                        class="text-sm font-bold font-en tracking-widest mb-6 border-b border-primary/30 inline-block pb-2">
                        LEGAL</h3>
                    <ul class="space-y-4 text-xs tracking-wider text-gray-400">
                        <li><a href="../legal/privacy.html" class="hover:text-white transition-colors">プライバシーポリシー</a>
                        </li>
                        <li><a href="../legal/tokusho.html" class="hover:text-white transition-colors">特定商取引法に基づく表記</a>
                        </li>
                        <li><a href="../legal/terms.html" class="hover:text-white transition-colors">利用規約</a></li>
                    </ul>
                </div>
            </div>
            <div
                class="border-t border-white/5 pt-8 flex justify-between items-center text-[10px] text-gray-600 font-en tracking-widest">
                <p>&copy; 2025 GIKO. ALL RIGHTS RESERVED.</p>
                <div>DESIGNED BY ATLASSHIFT</div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollToTopBtn"
        class="fixed bottom-8 right-8 bg-primary text-white w-12 h-12 rounded-full flex items-center justify-center shadow-lg translate-y-20 opacity-0 transition-all duration-300 z-50 hover:bg-white hover:text-black">
        <i class="fas fa-chevron-up"></i>
    </button>

    <script src="../assets/js/main.js"></script>

    <!-- カテゴリフィルタリング JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterBtns = document.querySelectorAll('.works-filter-btn');
            const workItems = document.querySelectorAll('.work-item');
            const noResults = document.getElementById('no-results');
            const grid = document.getElementById('works-grid');

            // URLパラメータから初期カテゴリを取得
            const urlParams = new URLSearchParams(window.location.search);
            const initialCategory = urlParams.get('category') || 'all';

            // フィルタリング関数
            function filterWorks(category) {
                let visibleCount = 0;

                workItems.forEach(item => {
                    const itemCats = item.getAttribute('data-category').split(',').map(c => c.trim());

                    if (category === 'all' || itemCats.includes(category)) {
                        item.classList.remove('hidden-by-filter');
                        item.classList.add('visible-by-filter');
                        visibleCount++;
                    } else {
                        item.classList.remove('visible-by-filter');
                        item.classList.add('hidden-by-filter');
                    }
                });

                // 結果が0件の場合
                if (visibleCount === 0) {
                    noResults.classList.remove('hidden');
                    grid.classList.add('hidden');
                } else {
                    noResults.classList.add('hidden');
                    grid.classList.remove('hidden');
                }

                // ボタンのアクティブ状態を更新
                filterBtns.forEach(btn => {
                    if (btn.getAttribute('data-filter') === category) {
                        btn.classList.add('border-primary', 'bg-primary', 'text-black');
                        btn.classList.remove('border-white/20', 'text-white');
                    } else {
                        btn.classList.remove('border-primary', 'bg-primary', 'text-black');
                        btn.classList.add('border-white/20', 'text-white');
                    }
                });

                // URLを更新（ブラウザ履歴に追加しない）
                const newUrl = category === 'all'
                    ? window.location.pathname
                    : `${window.location.pathname}?category=${category}`;
                window.history.replaceState({}, '', newUrl);
            }

            // ボタンクリックイベント
            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const filter = btn.getAttribute('data-filter');
                    filterWorks(filter);
                });
            });

            // 初期フィルタリング実行
            if (initialCategory !== 'all') {
                filterWorks(initialCategory);
            }
        });
    </script>

</body>

</html>