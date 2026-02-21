<?php
require_once '../admin/includes/db.php';

// カテゴリ定義（ラベル、英語名、背景画像）
$categories = [
    'all'       => ['label' => 'ALL',              'en' => 'ALL WORKS',        'image' => '../assets/images/hero.png'],
    'partial'   => ['label' => 'Partial Interior',  'en' => 'PARTIAL INTERIOR', 'image' => '../assets/images/hero.png'],
    'full'      => ['label' => 'Full Interior',     'en' => 'FULL INTERIOR',    'image' => '../assets/images/hero.png'],
    'package'   => ['label' => 'Package',           'en' => 'PACKAGE',          'image' => '../assets/images/hero.png'],
    'ambient'   => ['label' => 'Ambient Light',     'en' => 'AMBIENT LIGHT',    'image' => '../assets/images/hero.png'],
    'starlight' => ['label' => 'Starlight',         'en' => 'STARLIGHT',        'image' => '../assets/images/hero.png'],
    'newbiz'    => ['label' => 'New Business',      'en' => 'NEW BUSINESS',     'image' => '../assets/images/hero.png'],
];

// URLパラメータからカテゴリを取得
$activeCategory = isset($_GET['category']) ? $_GET['category'] : 'all';
if (!array_key_exists($activeCategory, $categories)) {
    $activeCategory = 'all';
}

// データベースから実際に使用されているカテゴリを取得
try {
    $categoryStmt = $pdo->query("SELECT DISTINCT category FROM works WHERE category IS NOT NULL AND category != '' ORDER BY category");
    $dbCategories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Fetch works（カテゴリでフィルタリング）
try {
    if ($activeCategory !== 'all' && $activeCategory !== '') {
        $stmt = $pdo->prepare("SELECT * FROM works WHERE category = :category ORDER BY created_at DESC");
        $stmt->bindParam(':category', $activeCategory, PDO::PARAM_STR);
        $stmt->execute();
    } else {
        $stmt = $pdo->query("SELECT * FROM works ORDER BY created_at DESC");
    }
    $works = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// カテゴリバッジ設定
$categoryBadges = [
    'partial' => ['label' => 'Partial Interior', 'color' => 'bg-blue-600/90'],
    'full' => ['label' => 'Full Interior', 'color' => 'bg-primary/90'],
    'package' => ['label' => 'Package', 'color' => 'bg-purple-600/90'],
    'ambient' => ['label' => 'Ambient Light', 'color' => 'bg-green-600/90'],
    'starlight' => ['label' => 'Starlight', 'color' => 'bg-indigo-500/90'],
    'newbiz' => ['label' => 'New Business', 'color' => 'bg-red-600/90'],
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
        /* カルーセルスクロールバー非表示 */
        #filter-carousel::-webkit-scrollbar { display: none; }
        #filter-carousel { scrollbar-width: none; -ms-overflow-style: none; }
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

            <!-- カテゴリフィルターカルーセル -->
            <div class="mb-16 relative max-w-5xl mx-auto">
                <!-- 左ナビボタン -->
                <button type="button" id="filter-prev"
                    class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-5 z-20 w-10 h-10 bg-black/80 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-primary hover:border-primary transition-all duration-300 backdrop-blur-sm opacity-0 pointer-events-none"
                    style="transition: opacity 0.3s;">
                    <i class="fas fa-chevron-left text-sm"></i>
                </button>

                <!-- スクロールコンテナ -->
                <div id="filter-carousel" class="flex gap-3 overflow-x-auto scroll-smooth px-1 py-2">
                    <?php foreach ($categories as $catId => $catInfo): ?>
                        <a
                            href="works.php<?php echo ($catId === 'all') ? '' : '?category=' . urlencode($catId); ?>"
                            data-filter="<?php echo $catId; ?>"
                            class="works-filter-btn group relative overflow-hidden flex-shrink-0 w-[140px] md:w-[180px] aspect-[16/10] flex items-center justify-center border transition-all duration-500 cursor-pointer
                                <?php if ($activeCategory === $catId): ?>
                                    border-primary
                                <?php else: ?>
                                    border-white/10 hover:border-primary/50
                                <?php endif; ?>
                            ">
                            <!-- 背景画像 -->
                            <div class="absolute inset-0 bg-cover bg-center opacity-30 group-hover:opacity-50 group-hover:scale-110 transition-all duration-700" style="background-image: url('<?php echo $catInfo['image']; ?>');"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-black/20"></div>
                            <!-- アクティブオーバーレイ -->
                            <?php if ($activeCategory === $catId): ?>
                                <div class="active-overlay absolute inset-0 bg-primary/20 border-2 border-primary"></div>
                            <?php endif; ?>
                            <!-- テキスト -->
                            <div class="relative z-10 text-center">
                                <span class="filter-label text-sm md:text-base font-bold tracking-wider <?php echo ($activeCategory === $catId) ? 'text-primary' : 'group-hover:text-primary'; ?> transition-colors duration-300">
                                    <?php echo $catInfo['label']; ?>
                                </span>
                                <div class="text-[8px] md:text-[9px] font-en tracking-widest text-gray-400 mt-1">
                                    <?php echo $catInfo['en']; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- 右ナビボタン -->
                <button type="button" id="filter-next"
                    class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-5 z-20 w-10 h-10 bg-black/80 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-primary hover:border-primary transition-all duration-300 backdrop-blur-sm"
                    style="transition: opacity 0.3s;">
                    <i class="fas fa-chevron-right text-sm"></i>
                </button>
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
                    <a href="<?php echo $link; ?>" class="group block relative overflow-hidden bg-secondary work-item"
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

            <?php if (empty($works)): ?>
                <!-- DB にワークスが0件の時 -->
                <div class="text-center py-24">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/5 border border-white/10 mb-6">
                        <i class="fas fa-folder-open text-gray-600 text-3xl"></i>
                    </div>
                    <p class="text-gray-400 text-lg font-bold mb-2">このカテゴリの施工実績はまだありません</p>
                    <p class="text-gray-600 text-sm">他のカテゴリをご覧いただくか、<br class="md:hidden">新しい実績の追加をお待ちください。</p>
                    <a href="works.php" class="inline-flex items-center gap-2 mt-8 text-sm text-primary border border-primary/30 px-6 py-2.5 hover:bg-primary hover:text-black transition-all duration-300 font-en tracking-wider">
                        <i class="fas fa-th-large text-xs"></i> ALL WORKS を見る
                    </a>
                </div>
            <?php endif; ?>

            <!-- JSフィルタ結果が0件の時 -->
            <div id="no-results" class="hidden text-center py-24">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/5 border border-white/10 mb-6">
                    <i class="fas fa-folder-open text-gray-600 text-3xl"></i>
                </div>
                <p class="text-gray-400 text-lg font-bold mb-2">このカテゴリの施工実績はまだありません</p>
                <p class="text-gray-600 text-sm">他のカテゴリをご覧いただくか、<br class="md:hidden">新しい実績の追加をお待ちください。</p>
                <a href="works.php" class="inline-flex items-center gap-2 mt-8 text-sm text-primary border border-primary/30 px-6 py-2.5 hover:bg-primary hover:text-black transition-all duration-300 font-en tracking-wider">
                    <i class="fas fa-th-large text-xs"></i> ALL WORKS を見る
                </a>
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

    <!-- カルーセルナビゲーション JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // === カルーセルナビボタン制御 ===
            const carousel = document.getElementById('filter-carousel');
            const prevBtn = document.getElementById('filter-prev');
            const nextBtn = document.getElementById('filter-next');
            const scrollAmount = 200; // 1クリックあたりのスクロール量

            function updateNavButtons() {
                if (!carousel) return;
                const { scrollLeft, scrollWidth, clientWidth } = carousel;
                // 左ボタン表示/非表示
                if (scrollLeft > 5) {
                    prevBtn.style.opacity = '1';
                    prevBtn.style.pointerEvents = 'auto';
                } else {
                    prevBtn.style.opacity = '0';
                    prevBtn.style.pointerEvents = 'none';
                }
                // 右ボタン表示/非表示
                if (scrollLeft + clientWidth < scrollWidth - 5) {
                    nextBtn.style.opacity = '1';
                    nextBtn.style.pointerEvents = 'auto';
                } else {
                    nextBtn.style.opacity = '0';
                    nextBtn.style.pointerEvents = 'none';
                }
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                });
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                });
            }

            if (carousel) {
                carousel.addEventListener('scroll', updateNavButtons);
                // 初期状態を設定
                updateNavButtons();

                // アクティブなカテゴリボタンを中央にスクロール
                const activeBtn = carousel.querySelector('.works-filter-btn.border-primary');
                if (activeBtn) {
                    setTimeout(() => {
                        const carouselRect = carousel.getBoundingClientRect();
                        const btnRect = activeBtn.getBoundingClientRect();
                        const scrollTo = activeBtn.offsetLeft - carousel.offsetLeft - (carouselRect.width / 2) + (btnRect.width / 2);
                        carousel.scrollTo({ left: scrollTo, behavior: 'smooth' });
                    }, 100);
                }
            }
        });
    </script>

</body>

</html>