<?php
require_once 'admin/includes/db.php';
require_once 'admin/includes/settings_helper.php';

// Fetch Settings
$company_name = get_setting('company_name', 'GIKO307合同会社');
$company_address = get_setting('company_address', '〒483-8013 愛知県江南市般若町南山307');
$company_tel = get_setting('company_tel', '080-8887-2116');
$company_email = get_setting('company_email', 'info@giko-official.com');
$instagram_url = get_setting('social_instagram', 'https://www.instagram.com/giko_artisan?igsh=MWRuenVqMzBkNzA3bw==');
$twitter_url = get_setting('social_twitter', 'https://x.com/giko_0203?s=21&t=wv4xW-XScSAbmdHqDnc6jA');
$youtube_url = get_setting('social_youtube', 'https://www.youtube.com/@GIKO-307');
$tiktok_url = get_setting('social_tiktok', 'https://www.tiktok.com/@giko_artisan?_r=1&_t=ZS-946uu1grw5U');
$line_url = get_setting('social_line', 'https://lin.ee/hmaVDuG');

// Fetch News
try {
    $news_stmt = $pdo->prepare("SELECT * FROM news WHERE status = 1 ORDER BY published_date DESC, created_at DESC LIMIT 3");
    $news_stmt->execute();
    $news_items = $news_stmt->fetchAll();
} catch (PDOException $e) {
    $news_items = [];
}

// Fetch Works
try {
    $works_stmt = $pdo->prepare("SELECT * FROM works ORDER BY created_at DESC LIMIT 3");
    $works_stmt->execute();
    $latest_works = $works_stmt->fetchAll();
} catch (PDOException $e) {
    $latest_works = [];
}

// Fetch Products (Latest 3)
try {
    $products_stmt = $pdo->prepare("SELECT * FROM products WHERE stock_status = 1 ORDER BY created_at DESC LIMIT 3");
    $products_stmt->execute();
    $latest_products = $products_stmt->fetchAll();
} catch (PDOException $e) {
    $latest_products = [];
}
?>
<!DOCTYPE html>
<html lang="ja" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(get_setting('site_title', '技巧 -Giko- | Automotive Interior Specialist')); ?>
    </title>
    <meta name="description"
        content="<?php echo htmlspecialchars(get_setting('site_description', '職人の手による最高級本革シート張り替え。愛車に感動と喜びを。')); ?>">
    <!-- OGP -->
    <meta property="og:title"
        content="<?php echo htmlspecialchars(get_setting('site_title', '技巧 -Giko- | Automotive Interior Specialist')); ?>">
    <meta property="og:description"
        content="<?php echo htmlspecialchars(get_setting('site_description', '職人の手による最高級本革シート張り替え。愛車に感動と喜びを。')); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://giko-official.com/">
    <meta property="og:image" content="https://giko-official.com/assets/images/ogp.jpg">
    <meta name="twitter:card" content="summary_large_image">
    <!-- <link rel="icon" href="./assets/images/favicon.ico"> -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="./tailwind_config.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>document.documentElement.classList.add('js-loading');</script>
    <link rel="stylesheet" href="./css/style.css">
</head>

<body class="bg-black text-white antialiased selection:bg-primary selection:text-white">

    <!-- Header -->
    <header class="fixed w-full z-50 transition-all duration-300 bg-black/80 backdrop-blur-md border-b border-white/5"
        id="header">
        <div class="container mx-auto px-6 h-20 flex justify-between items-center">
            <a href="index.php" class="flex items-center group">
                <img src="./assets/images/logo_new.png" alt="GIKO" class="h-10 group-hover:opacity-80 transition-opacity">
            </a>

            <nav class="hidden lg:flex space-x-10 text-xs font-bold tracking-widest">
                <a href="#concept" class="hover:text-primary transition-colors font-en relative group">
                    CONCEPT
                    <span
                        class="absolute -bottom-2 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span>
                </a>
                <a href="pages/works.php" class="hover:text-primary transition-colors font-en relative group">
                    WORKS
                    <span
                        class="absolute -bottom-2 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span>
                </a>
                <a href="store/index.php" class="hover:text-primary transition-colors font-en relative group">
                    STORE
                    <span
                        class="absolute -bottom-2 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span>
                </a>
                <a href="pages/before_after.html" class="hover:text-primary transition-colors font-en relative group">
                    BEFORE & AFTER
                    <span
                        class="absolute -bottom-2 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span>
                </a>
                <a href="#material" class="hover:text-primary transition-colors font-en relative group">
                    MATERIAL
                    <span
                        class="absolute -bottom-2 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span>
                </a>
                <a href="#flow" class="hover:text-primary transition-colors font-en relative group">
                    FLOW
                    <span
                        class="absolute -bottom-2 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span>
                </a>
                <a href="#company" class="hover:text-primary transition-colors font-en relative group">
                    COMPANY
                    <span
                        class="absolute -bottom-2 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span>
                </a>
            </nav>

            <a href="contact/index.php"
                class="hidden lg:inline-flex items-center justify-center px-8 py-2.5 border border-white/20 text-xs font-bold tracking-widest hover:bg-primary hover:border-primary hover:text-white transition-all duration-300 font-en">
                CONTACT
            </a>

            <!-- SNS Icons Desktop -->
            <div class="hidden lg:flex ml-4 items-center gap-4">
                <a href="<?php echo htmlspecialchars($tiktok_url); ?>" target="_blank" class="w-5 flex items-center justify-center text-white/60 hover:text-primary transition-colors text-lg"><i class="fab fa-tiktok"></i></a>
                <a href="<?php echo htmlspecialchars($twitter_url); ?>" target="_blank" class="w-5 flex items-center justify-center text-white/60 hover:text-primary transition-colors text-lg"><i class="fab fa-x-twitter"></i></a>
                <a href="<?php echo htmlspecialchars($youtube_url); ?>" target="_blank" class="w-5 flex items-center justify-center text-white/60 hover:text-primary transition-colors text-lg"><i class="fab fa-youtube"></i></a>
                <a href="<?php echo htmlspecialchars($instagram_url); ?>" target="_blank" class="w-5 flex items-center justify-center text-white/60 hover:text-primary transition-colors text-lg"><i class="fab fa-instagram"></i></a>
                <a href="<?php echo htmlspecialchars($line_url); ?>" target="_blank" class="w-5 flex items-center justify-center text-white/60 hover:text-[#06C755] transition-colors text-lg"><i class="fab fa-line"></i></a>
            </div>

            <!-- Language Switcher Desktop -->
            <button id="lang-toggle-desktop"
                class="hidden lg:flex ml-6 items-center gap-2 text-xs font-bold font-en tracking-widest opacity-80 hover:opacity-100 transition-opacity">
                <span class="text-primary">JP</span>
                <span class="text-white/30">/</span>
                <span class="text-white">EN</span>
            </button>

            <!-- Cart Icon Desktop -->
            <a href="store/cart.html" class="hidden lg:flex ml-6 relative group">
                <i class="fas fa-shopping-cart text-white text-lg group-hover:text-primary transition-colors"></i>
                <span id="cart-badge-desktop"
                    class="cart-badge absolute -top-2 -right-2 bg-primary text-black text-[10px] font-bold px-1.5 rounded-full hidden">0</span>
            </a>

            <!-- Mobile Menu Button -->
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

        <!-- Mobile Menu -->
        <div class="lg:hidden hidden bg-secondary border-t border-white/10 absolute w-full top-20 left-0 h-screen"
            id="mobile-menu">
            <nav class="flex flex-col p-10 space-y-8 text-center text-lg">
                <a href="store/cart.html"
                    class="text-white hover:text-primary font-en tracking-widest flex items-center justify-center gap-2">
                    <i class="fas fa-shopping-cart"></i> CART <span id="cart-badge-menu"
                        class="cart-badge bg-primary text-black text-xs font-bold px-2 py-0.5 rounded-full hidden">0</span>
                </a>
                <a href="#concept" class="text-white hover:text-primary font-en tracking-widest">CONCEPT</a>
                <a href="pages/works.php" class="text-white hover:text-primary font-en tracking-widest">WORKS</a>
                <a href="store/index.php" class="text-white hover:text-primary font-en tracking-widest">STORE</a>
                <a href="pages/before_after.php" class="text-white hover:text-primary font-en tracking-widest">BEFORE &
                    AFTER</a>
                <a href="#material" class="text-white hover:text-primary font-en tracking-widest">MATERIAL</a>
                <a href="#flow" class="text-white hover:text-primary font-en tracking-widest">FLOW</a>
                <a href="#company" class="text-white hover:text-primary font-en tracking-widest">COMPANY</a>
                <a href="contact/index.php" class="text-primary font-bold font-en tracking-widest mt-8">CONTACT</a>
                <!-- Language Switcher Mobile -->
                <button id="lang-toggle-mobile"
                    class="mt-8 flex items-center justify-center gap-4 text-sm font-bold font-en tracking-widest">
                    <span class="text-primary">JP</span>
                    <span class="text-white/30">/</span>
                    <span class="text-white">EN</span>
                </button>
            </nav>
        </div>

        <!-- Load Cart JS to update badge -->
        <script src="./assets/js/cart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Cart.updateBadge(); // Initial update
            });
        </script>
    </header>

    <!-- Hero -->
    <section class="relative h-screen min-h-[700px] flex items-center justify-center overflow-hidden group">
        <div class="absolute inset-0 z-0 overflow-hidden">
            <img src="./assets/images/hero.jpg" alt="Hero"
                class="w-full h-full object-cover opacity-60 scale-100 group-hover:scale-110 transition-transform duration-[10s] ease-out">
            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-black/50"></div>
            <div class="absolute inset-0 bg-black/20"></div>
        </div>
        <div class="relative z-10 text-center px-6 max-w-5xl mx-auto">
            <div
                class="inline-block mb-8 px-4 py-1 border border-white/30 text-[10px] tracking-[0.4em] font-en uppercase fade-in bg-black/20 backdrop-blur-sm">
                The Art of Automotive Interior
            </div>
            <h1 class="text-6xl md:text-9xl font-bold mb-10 tracking-tighter font-en leading-none fade-in drop-shadow-2xl"
                style="transition-delay: 0.1s;">
                CRAFTED<br><span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-white via-gray-200 to-gray-500">PERFECTION</span>
            </h1>
            <p class="text-sm md:text-lg text-gray-200 tracking-[0.2em] mb-16 font-light fade-in uppercase"
                style="transition-delay: 0.2s;" data-i18n="hero_sub">
                Beyond the Genuine Quality
            </p>
            <div class="fade-in flex flex-col items-center gap-6" style="transition-delay: 0.3s;">
                <a href="contact/index.php"
                    class="relative px-12 py-4 border border-white/20 overflow-hidden group/btn">
                    <span
                        class="absolute inset-0 w-full h-full bg-white/5 group-hover/btn:bg-primary/80 transition-all duration-500 ease-out transform translate-y-full group-hover/btn:translate-y-0"></span>
                    <span class="relative text-sm font-bold tracking-widest font-en z-10">CONTACT US</span>
                </a>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div
            class="absolute bottom-10 left-1/2 transform -translate-x-1/2 z-20 flex flex-col items-center gap-2 animate-bounce opacity-50">
            <span class="text-[10px] tracking-widest font-en">SCROLL</span>
            <div class="w-px h-12 bg-white"></div>
        </div>
    </section>

    <!-- News Section (New) -->
    <?php if (!empty($news_items)): ?>
        <section id="news" class="py-20 bg-black border-b border-white/5">
            <div class="container mx-auto px-6 max-w-4xl">
                <div class="text-center mb-12">
                    <h2 class="text-2xl font-bold font-en tracking-widest mb-2">NEWS</h2>
                    <p class="text-xs text-textLight tracking-wider">お知らせ</p>
                </div>
                <div class="space-y-4">
                    <?php foreach ($news_items as $news): ?>
                        <article class="flex flex-col md:flex-row gap-4 md:items-center border-b border-white/10 pb-4">
                            <div class="text-xs text-primary font-en tracking-widest w-32">
                                <?php echo date('Y.m.d', strtotime($news['published_date'])); ?>
                            </div>
                            <h3 class="flex-1 text-sm font-bold hover:text-primary transition-colors">
                                <a href="news/detail.php?id=<?php echo $news['id']; ?>">
                                    <?php echo htmlspecialchars($news['title']); ?>
                                </a>
                            </h3>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Concept -->
    <section id="concept" class="py-32 bg-black relative">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-end gap-20">
                <div class="lg:w-1/2 fade-in">
                    <h2
                        class="text-6xl md:text-9xl font-bold text-white/5 font-en absolute -top-20 -left-10 select-none">
                        CONCEPT</h2>
                    <div class="relative">
                        <div class="text-primary font-bold tracking-widest font-en mb-4 text-xs">PHILOSOPHY</div>
                        <h2 class="text-3xl md:text-4xl font-bold mb-8 leading-relaxed font-serif"
                            data-i18n="concept_main">
                            「純正超え」の<br>感動品質を。
                        </h2>
                        <p class="text-gray-400 leading-loose mb-8 text-justify" data-i18n="concept_text">
                            量産車にはない「あなただけ」の理想を叶える。<br>
                            私たちは、素材選びからステッチひとつに至るまで、妥協なきクラフトマンシップで理想の空間を提供します。<br>
                            最新の欧州車に見られるトレンドを取り入れつつ、日本の職人技で仕上げる。<br>
                            それが、技巧 -GIKO- の流儀です。
                        </p>
                        <a href="pages/concept.html"
                            class="inline-block border-b border-primary pb-1 text-sm tracking-widest hover:text-primary transition-colors">READ
                            MORE</a>
                    </div>
                </div>
                <div class="lg:w-1/2 relative lg:-mb-20 fade-in" style="transition-delay: 0.2s;">
                    <img src="./assets/images/alphard.jpg" alt="Craftsmanship"
                        class="w-full h-auto rounded-sm shadow-2xl filter brightness-75 hover:brightness-100 transition-all duration-700">
                    <div
                        class="absolute -bottom-10 -right-10 w-40 h-40 border border-primary/30 z-[-1] hidden lg:block">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Works, Store, etc sections omitted/truncated for brevity in this prompt logic but included in file -->
    <!-- (I will include the full content from index.html but modified, assuming short diff for brevity here isn't allowed, so I'll write full file content based on index.html but with replaced sections) -->

    <!-- Recopying Works/Store/BeforeAfter/Material/Flow from index.html but updating links to .php -->

    <!-- Works Category Menu -->
    <section id="works" class="py-32 bg-secondary">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-3xl md:text-5xl font-bold font-en tracking-widest mb-4">WORKS</h2>
                <p class="text-xs text-textLight tracking-wider">施工実績</p>
            </div>

            <!-- カテゴリタイルメニュー (2行×3列) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 max-w-5xl mx-auto fade-in">

                <!-- 部分張替え -->
                <a href="pages/works.php?category=partial" class="works-tile group relative overflow-hidden aspect-[16/10] flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900 border border-white/10 hover:border-primary/50 transition-all duration-500">
                    <div class="works-tile-bg absolute inset-0 bg-cover bg-center opacity-40 group-hover:opacity-60 group-hover:scale-110 transition-all duration-700" style="background-image: url('./assets/images/tile_partial.jpg');"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <div class="relative z-10 text-center">
                        <span class="text-xl md:text-2xl font-bold tracking-wider group-hover:text-primary transition-colors duration-300">部分張替え</span>
                    </div>
                </a>

                <!-- 全内装張替え -->
                <a href="pages/works.php?category=full" class="works-tile group relative overflow-hidden aspect-[16/10] flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900 border border-white/10 hover:border-primary/50 transition-all duration-500">
                    <div class="works-tile-bg absolute inset-0 bg-cover bg-center opacity-40 group-hover:opacity-60 group-hover:scale-110 transition-all duration-700" style="background-image: url('./assets/images/tile_full.jpg');"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <div class="relative z-10 text-center">
                        <span class="text-xl md:text-2xl font-bold tracking-wider group-hover:text-primary transition-colors duration-300">全内装張替え</span>
                    </div>
                </a>

                <!-- 補修/リペア -->
                <a href="pages/works.php?category=package" class="works-tile group relative overflow-hidden aspect-[16/10] flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900 border border-white/10 hover:border-primary/50 transition-all duration-500">
                    <div class="works-tile-bg absolute inset-0 bg-cover bg-center opacity-40 group-hover:opacity-60 group-hover:scale-110 transition-all duration-700" style="background-image: url('./assets/images/tile_repair.jpg');"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <div class="relative z-10 text-center">
                        <span class="text-xl md:text-2xl font-bold tracking-wider group-hover:text-primary transition-colors duration-300">補修/リペア</span>
                    </div>
                </a>

                <!-- アンビエントライト -->
                <a href="pages/works.php?category=ambient" class="works-tile group relative overflow-hidden aspect-[16/10] flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900 border border-white/10 hover:border-primary/50 transition-all duration-500">
                    <div class="works-tile-bg absolute inset-0 bg-cover bg-center opacity-40 group-hover:opacity-60 group-hover:scale-110 transition-all duration-700" style="background-image: url('./assets/images/tile_ambient.jpg');"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <div class="relative z-10 text-center">
                        <span class="text-lg md:text-xl font-bold tracking-wider group-hover:text-primary transition-colors duration-300">アンビエントライト</span>
                    </div>
                </a>

                <!-- スターライト -->
                <a href="pages/works.php?category=starlight" class="works-tile group relative overflow-hidden aspect-[16/10] flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900 border border-white/10 hover:border-primary/50 transition-all duration-500">
                    <div class="works-tile-bg absolute inset-0 bg-cover bg-center opacity-40 group-hover:opacity-60 group-hover:scale-110 transition-all duration-700" style="background-image: url('./assets/images/tile_starlight.jpg');"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <div class="relative z-10 text-center">
                        <span class="text-xl md:text-2xl font-bold tracking-wider group-hover:text-primary transition-colors duration-300">スターライト</span>
                    </div>
                </a>

                <!-- 新ブランド -->
                <a href="pages/works.php?category=newbiz" class="works-tile group relative overflow-hidden aspect-[16/10] flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900 border border-white/10 hover:border-primary/50 transition-all duration-500">
                    <div class="works-tile-bg absolute inset-0 bg-cover bg-center opacity-40 group-hover:opacity-60 group-hover:scale-110 transition-all duration-700" style="background-image: url('./assets/images/tile_newbiz.jpg');"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <div class="relative z-10 text-center">
                        <span class="text-xl md:text-2xl font-bold tracking-wider group-hover:text-primary transition-colors duration-300">新ブランド</span>
                    </div>
                </a>

            </div>

            <div class="text-center mt-16 fade-in">
                <a href="pages/works.php"
                    class="inline-flex items-center gap-2 text-sm tracking-widest border border-white/20 px-10 py-4 hover:bg-primary hover:text-black hover:border-primary transition-all duration-300 font-en font-bold">
                    VIEW ALL WORKS <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Store (Using Store Index) -->
    <?php
    // 商品データを取得（タグ抽出のため全件取得）
    try {
        $products_stmt = $pdo->prepare("SELECT * FROM products WHERE stock_status = 1 ORDER BY id ASC");
        $products_stmt->execute();
        $latest_products = $products_stmt->fetchAll();
    } catch (PDOException $e) {
        $latest_products = [];
    }

    // 車種タグのユニークリストを取得
    $allTags = [];
    foreach ($latest_products as $p) {
        $tags = $p['vehicle_tags'] ?? '';
        if ($tags) {
            foreach (explode(',', $tags) as $tag) {
                $tag = trim($tag);
                if ($tag && !in_array($tag, $allTags)) {
                    $allTags[] = $tag;
                }
            }
        }
    }
    // アルファードタグを強制追加（ユーザー要望）
    if (!in_array('Alphard', $allTags)) {
        array_unshift($allTags, 'Alphard');
    }
    sort($allTags);
    
    // 画像を取得するヘルパー関数
    // 画像を取得するヘルパー関数
    function getProductImage($json) {
        if (empty($json)) return null;
        $images = json_decode($json, true);
        $img = (!empty($images) && is_array($images)) ? $images[0] : null;
        // パス調整: ../assets -> ./assets
        if ($img && strpos($img, '../') === 0) {
            $img = substr($img, 1); // Remove first dot: ./assets...
        }
        return $img;
    }
    ?>
    <section id="store" class="py-32 bg-black border-t border-white/5">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-3xl md:text-5xl font-bold font-en tracking-widest mb-4">ONLINE STORE</h2>
                <p class="text-xs text-textLight tracking-wider">公式オンラインストア</p>
            </div>
            
            <!-- カテゴリタイル カルーセル -->
            <div class="mb-16 relative max-w-5xl mx-auto">
                <!-- 左ナビボタン -->
                <button type="button" id="index-store-prev"
                    class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-5 z-20 w-10 h-10 bg-black/80 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-primary hover:border-primary transition-all duration-300 backdrop-blur-sm opacity-0 pointer-events-none"
                    style="transition: opacity 0.3s;">
                    <i class="fas fa-chevron-left text-sm"></i>
                </button>

                <!-- 右ナビボタン -->
                <button type="button" id="index-store-next"
                    class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-5 z-20 w-10 h-10 bg-black/80 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-primary hover:border-primary transition-all duration-300 backdrop-blur-sm opacity-0 pointer-events-none"
                    style="transition: opacity 0.3s;">
                    <i class="fas fa-chevron-right text-sm"></i>
                </button>

                <!-- スクロールコンテナ -->
                <div id="index-store-carousel" class="flex gap-3 overflow-x-auto scroll-smooth px-1 py-2 justify-center">
                    <!-- ALL タイル -->
                    <a href="store/index.php"
                        class="group relative overflow-hidden flex-shrink-0 w-[200px] md:w-[260px] aspect-[4/3] flex items-center justify-center border transition-all duration-500 cursor-pointer border-primary">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/50 to-black/30"></div>
                        <div class="active-overlay absolute inset-0 bg-primary/20 border-2 border-primary"></div>
                        <div class="relative z-10 text-center">
                            <span class="text-sm md:text-base font-bold tracking-wider text-primary transition-colors duration-300 font-en">ALL</span>
                            <div class="text-[8px] md:text-[9px] font-en tracking-widest text-gray-400 mt-1">ALL PRODUCTS</div>
                        </div>
                    </a>
                    
                    <?php foreach ($allTags as $tag): 
                        // このタグに対応する最初の商品画像を取得
                        $tagImage = '';
                        // アルファードの場合は固定画像
                        if (strtolower($tag) === 'alphard' || $tag === 'アルファード') {
                            $tagImage = './assets/images/alphard.jpg';
                        } else {
                            foreach ($latest_products as $p) {
                                $pTags = array_map('trim', explode(',', $p['vehicle_tags'] ?? ''));
                                if (in_array($tag, $pTags)) {
                                    $tagImage = getProductImage($p['images']);
                                    break;
                                }
                            }
                        }
                    ?>
                    <a href="store/index.php?tag=<?php echo htmlspecialchars($tag); ?>"
                        class="group relative overflow-hidden flex-shrink-0 w-[200px] md:w-[260px] aspect-[4/3] flex items-center justify-center border border-white/10 hover:border-primary/50 transition-all duration-500 cursor-pointer">
                        <!-- 背景画像 -->
                        <?php if ($tagImage): ?>
                        <div class="absolute inset-0 bg-cover bg-center opacity-30 group-hover:opacity-50 group-hover:scale-110 transition-all duration-700" style="background-image: url('<?php echo htmlspecialchars($tagImage); ?>');"></div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-black/20"></div>
                        <!-- テキスト -->
                        <div class="relative z-10 text-center px-2">
                            <div class="text-sm md:text-base font-bold tracking-wider text-white group-hover:text-primary transition-colors duration-300 font-en mb-1">
                                <?php echo htmlspecialchars($tag); ?>
                            </div>
                            <!-- ロゴ透かし的な装飾 -->
                            <div class="text-[10px] font-en font-bold text-white/10 tracking-[0.2em] transform scale-150 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none -z-10 w-full overflow-hidden whitespace-nowrap">
                                -GIKO-
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="text-center fade-in mt-12">
                <a href="store/index.php"
                    class="inline-flex items-center gap-2 text-sm tracking-widest border border-white/20 px-10 py-4 hover:bg-primary hover:text-black hover:border-primary transition-all duration-300 font-en">
                    VIEW ONLINE STORE <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        // Store Carousel Control (Top Page)
        document.addEventListener('DOMContentLoaded', () => {
            const carousel = document.getElementById('index-store-carousel');
            const prevBtn = document.getElementById('index-store-prev');
            const nextBtn = document.getElementById('index-store-next');
            if (!carousel || !prevBtn || !nextBtn) return;

            const scrollAmount = 200;

            function updateNavButtons() {
                const canScrollLeft = carousel.scrollLeft > 10;
                const canScrollRight = carousel.scrollLeft < (carousel.scrollWidth - carousel.clientWidth - 10);
                prevBtn.style.opacity = canScrollLeft ? '1' : '0';
                prevBtn.style.pointerEvents = canScrollLeft ? 'auto' : 'none';
                nextBtn.style.opacity = canScrollRight ? '1' : '0';
                nextBtn.style.pointerEvents = canScrollRight ? 'auto' : 'none';
            }

            prevBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
            nextBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });

            carousel.addEventListener('scroll', updateNavButtons);
            updateNavButtons();
            
            // カルーセルスクロールバー非表示スタイル
            const style = document.createElement('style');
            style.textContent = `
                #index-store-carousel::-webkit-scrollbar { display: none; }
                #index-store-carousel { scrollbar-width: none; -ms-overflow-style: none; }
            `;
            document.head.appendChild(style);
        });
    </script>

    <!-- Material -->
    <section id="material" class="py-32 bg-secondary relative overflow-hidden">
        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col lg:flex-row gap-16 items-center">
                <div class="lg:w-1/2 fade-in">
                    <img src="./assets/images/material_leather.jpg"
                        class="w-full h-auto rounded-sm shadow-2xl">
                </div>
                <div class="lg:w-1/2 fade-in">
                    <h2
                        class="text-3xl md:text-5xl font-bold font-en tracking-widest mb-8 text-white/10 absolute -right-0 -top-20 select-none">
                        MATERIAL</h2>
                    <h2 class="text-3xl font-bold mb-8 leading-relaxed font-serif" data-i18n="material_main">
                        手に触れる全てに、<br>最高級の悦びを。
                    </h2>

                    <div class="space-y-8">
                        <div>
                            <h3 class="text-primary font-bold tracking-widest font-en mb-2 text-sm"
                                data-i18n="material_sub1_title">厳選された本革</h3>
                            <p class="text-sm text-gray-400 leading-loose" data-i18n="material_sub1_text">
                                欧州の高級車にも採用されるナッパレザーをはじめ、耐久性と質感に優れた最高ランクの原皮のみを厳選。<br>
                                時を経るごとに馴染み、深みを増す本物の質感をお楽しみください。
                            </p>
                        </div>
                        <div>
                            <h3 class="text-primary font-bold tracking-widest font-en mb-2 text-sm"
                                data-i18n="material_sub2_title">多種多様なマテリアル</h3>
                            <p class="text-sm text-gray-400 leading-loose" data-i18n="material_sub2_text">
                                アルカンターラ、パンチングレザー、カーボンレザーなど、デザイン性と機能性を両立する多彩な素材をご用意。<br>
                                ステッチの糸一本の色に至るまで、数千通りの組み合わせが可能です。
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Flow -->
    <section id="flow" class="py-32 bg-black border-t border-white/5">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-3xl md:text-5xl font-bold font-en tracking-widest mb-4">FLOW</h2>
                <p class="text-xs text-textLight tracking-wider" data-i18n="flow_sub">フルオーダーの流れ</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div
                    class="bg-secondary p-8 border border-white/5 hover:border-primary/50 transition-colors duration-300 group fade-in">
                    <div
                        class="text-4xl font-bold font-en text-white/5 mb-4 group-hover:text-primary/20 transition-colors">
                        01</div>
                    <div class="mb-6 aspect-[4/3] overflow-hidden rounded-sm">
                        <img src="./assets/images/flow_inquiry.png"
                            class="w-full h-full object-cover opacity-50 group-hover:opacity-100 transition-opacity">
                    </div>
                    <h3 class="text-lg font-bold tracking-widest mb-4" data-i18n="flow_step1_title">お問い合わせ</h3>
                    <p class="text-xs text-gray-400 leading-relaxed" data-i18n="flow_step1_text">お問い合わせフォームまたはお電話にて、ご希望の内容をお知らせください。車種・施工箇所・イメージなど、お決まりの範囲で構いません。
                    </p>
                </div>
                <!-- Step 2 -->
                <div class="bg-secondary p-8 border border-white/5 hover:border-primary/50 transition-colors duration-300 group fade-in"
                    style="transition-delay: 0.1s;">
                    <div
                        class="text-4xl font-bold font-en text-white/5 mb-4 group-hover:text-primary/20 transition-colors">
                        02</div>
                    <div class="mb-6 aspect-[4/3] overflow-hidden rounded-sm">
                        <img src="./assets/images/flow_planning.png"
                            class="w-full h-full object-cover opacity-50 group-hover:opacity-100 transition-opacity">
                    </div>
                    <h3 class="text-lg font-bold tracking-widest mb-4" data-i18n="flow_step2_title">打ち合わせ</h3>
                    <p class="text-xs text-gray-400 leading-relaxed" data-i18n="flow_step2_text">素材サンプルをご覧いただきながら、デザイン・仕様・ステッチの色などを決定。お見積もりをご提示いたします。</p>
                </div>
                <!-- Step 3 -->
                <div class="bg-secondary p-8 border border-white/5 hover:border-primary/50 transition-colors duration-300 group fade-in"
                    style="transition-delay: 0.2s;">
                    <div
                        class="text-4xl font-bold font-en text-white/5 mb-4 group-hover:text-primary/20 transition-colors">
                        03</div>
                    <div class="mb-6 aspect-[4/3] overflow-hidden rounded-sm">
                        <img src="./assets/images/flow_construction.png"
                            class="w-full h-full object-cover opacity-50 group-hover:opacity-100 transition-opacity">
                    </div>
                    <h3 class="text-lg font-bold tracking-widest mb-4" data-i18n="flow_step3_title">製作</h3>
                    <p class="text-xs text-gray-400 leading-relaxed" data-i18n="flow_step3_text">熟練の職人が一つひとつ丁寧に製作。製作期間は内容により1日〜2ヶ月程度です。</p>
                </div>
                <!-- Step 4 -->
                <div class="bg-secondary p-8 border border-white/5 hover:border-primary/50 transition-colors duration-300 group fade-in"
                    style="transition-delay: 0.3s;">
                    <div
                        class="text-4xl font-bold font-en text-white/5 mb-4 group-hover:text-primary/20 transition-colors">
                        04</div>
                    <div class="mb-6 aspect-[4/3] overflow-hidden rounded-sm">
                        <img src="./assets/images/flow_delivery.png"
                            class="w-full h-full object-cover opacity-50 group-hover:opacity-100 transition-opacity">
                    </div>
                    <h3 class="text-lg font-bold tracking-widest mb-4" data-i18n="flow_step4_title">納車</h3>
                    <p class="text-xs text-gray-400 leading-relaxed" data-i18n="flow_step4_text">仕上がりをご確認いただき、お引き渡し。アフターフォローもご説明いたします。
                    </p>
                </div>
            </div>
        </div>
    </section>


    <!-- Company -->
    <section id="company" class="py-32 bg-black border-t border-white/5">
        <div class="container mx-auto px-6 max-w-4xl">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-3xl md:text-5xl font-bold font-en tracking-widest mb-4">COMPANY</h2>
                <p class="text-xs text-textLight tracking-wider" data-i18n="company_sub">会社概要</p>
            </div>
            <div class="border-t border-white/10 fade-in">
                <div class="flex flex-col md:flex-row py-6 border-b border-white/10">
                    <div class="md:w-1/3 text-gray-400 font-bold text-sm tracking-widest py-2">COMPANY NAME</div>
                    <div class="md:w-2/3 py-2">GIKO307合同会社</div>
                </div>
                <div class="flex flex-col md:flex-row py-6 border-b border-white/10">
                    <div class="md:w-1/3 text-gray-400 font-bold text-sm tracking-widest py-2">BRAND</div>
                    <div class="md:w-2/3 py-2">技巧　-GIKO-</div>
                </div>
                <div class="flex flex-col md:flex-row py-6 border-b border-white/10">
                    <div class="md:w-1/3 text-gray-400 font-bold text-sm tracking-widest py-2">ADDRESS</div>
                    <div class="md:w-2/3 py-2" data-i18n="company_address">〒483-8013 愛知県江南市般若町南山307</div>
                </div>
                <div class="flex flex-col md:flex-row py-6 border-b border-white/10">
                    <div class="md:w-1/3 text-gray-400 font-bold text-sm tracking-widest py-2">BUSINESS</div>
                    <div class="md:w-2/3 py-2" data-i18n="company_business">自動車内装のカスタム/補修リペア<br>シート/ステアリング/天井張替え<br>車種専用インテリアパーツ販売</div>
                </div>
                <div class="flex flex-col md:flex-row py-6 border-b border-white/10">
                    <div class="md:w-1/3 text-gray-400 font-bold text-sm tracking-widest py-2">TEL</div>
                    <div class="md:w-2/3 py-2">会社TEL：0587-22-7344<br>FAX：0587-22-7158<br>代表TEL：080-8887-2116（直通）</div>
                </div>
                <div class="flex flex-col md:flex-row py-6 border-b border-white/10">
                    <div class="md:w-1/3 text-gray-400 font-bold text-sm tracking-widest py-2">OPEN</div>
                    <div class="md:w-2/3 py-2" data-i18n="company_open">10:00 - 20:00<br><span class="text-red-400">定休日：不定休　※ご来店時は事前にご連絡願います</span></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-secondary pt-24 pb-12 border-t border-white/5 text-white">
        <!-- Google Maps -->
        <div class="w-full mb-16 px-6 max-w-6xl mx-auto">
            <h3 class="text-xs font-bold font-en tracking-widest mb-4 text-gray-400 border-b border-white/10 pb-3">
                <i class="fas fa-map-marker-alt text-primary mr-2"></i>ACCESS
            </h3>
            <p class="text-xs text-gray-500 mb-4">〒483-8013 愛知県江南市般若町南山307</p>
            <div class="overflow-hidden rounded-sm border border-white/10" style="height:280px;">
                <iframe
                    src="https://maps.google.com/maps?q=愛知県江南市般若町南山307&output=embed&hl=ja&z=15"
                    width="100%" height="280" style="border:0; filter: grayscale(0.3) invert(0.05);"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-20">
                <!-- Col 1: Logo & SNS -->
                <div>
                    <img src="./assets/images/logo_new.png" alt="GIKO" class="h-8 mb-6">
                    <p class="text-xs text-gray-500 leading-loose mb-6">最高級の素材と技術で、カーライフに彩りを。</p>
                    <div class="flex space-x-3">
                        <a href="<?php echo htmlspecialchars($tiktok_url); ?>" target="_blank" class="w-11 h-11 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary transition-colors text-base"><i class="fab fa-tiktok"></i></a>
                        <a href="<?php echo htmlspecialchars($twitter_url); ?>" target="_blank" class="w-11 h-11 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary transition-colors text-base"><i class="fab fa-x-twitter"></i></a>
                        <a href="<?php echo htmlspecialchars($youtube_url); ?>" target="_blank" class="w-11 h-11 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary transition-colors text-base"><i class="fab fa-youtube"></i></a>
                        <a href="<?php echo htmlspecialchars($instagram_url); ?>" target="_blank" class="w-11 h-11 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary transition-colors text-base"><i class="fab fa-instagram"></i></a>
                        <a href="<?php echo htmlspecialchars($line_url); ?>" target="_blank" class="w-11 h-11 rounded-full bg-white/5 flex items-center justify-center hover:bg-[#06C755] transition-colors text-base"><i class="fab fa-line"></i></a>
                    </div>
                </div>
                <!-- Col 2: Menu -->
                <div>
                    <h3
                        class="text-sm font-bold font-en tracking-widest mb-6 border-b border-primary/30 inline-block pb-2">
                        MENU</h3>
                    <ul class="space-y-4 text-xs tracking-wider text-gray-400">
                        <li><a href="#concept" class="hover:text-white transition-colors">CONCEPT</a></li>
                        <li><a href="pages/works.php" class="hover:text-white transition-colors">WORKS</a></li>
                        <li><a href="store/index.php" class="hover:text-white transition-colors">STORE</a></li>
                        <li><a href="pages/before_after.html" class="hover:text-white transition-colors">BEFORE & AFTER</a></li>
                        <li><a href="#material" class="hover:text-white transition-colors">MATERIAL</a></li>
                        <li><a href="#flow" class="hover:text-white transition-colors">FLOW</a></li>
                        <li><a href="#company" class="hover:text-white transition-colors">COMPANY</a></li>
                    </ul>
                </div>
                <!-- Col 3: Contact -->
                <div>
                    <h3
                        class="text-sm font-bold font-en tracking-widest mb-6 border-b border-primary/30 inline-block pb-2">
                        CONTACT</h3>
                    <ul class="space-y-4 text-xs tracking-wider text-gray-400">
                        <li class="flex items-start gap-4">
                            <a href="contact/index.php" class="hover:text-white transition-colors">お問い合わせフォーム</a>
                        </li>
                    </ul>
                </div>
                <!-- Col 4: Legal -->
                <div>
                    <h3
                        class="text-sm font-bold font-en tracking-widest mb-6 border-b border-primary/30 inline-block pb-2">
                        LEGAL</h3>
                    <ul class="space-y-4 text-xs tracking-wider text-gray-400">
                        <li><a href="legal/privacy.html" class="hover:text-white transition-colors">プライバシーポリシー</a></li>
                        <li><a href="legal/tokusho.html" class="hover:text-white transition-colors">特定商取引法に基づく表記</a></li>
                        <li><a href="legal/terms.html" class="hover:text-white transition-colors">利用規約</a></li>
                    </ul>
                </div>
            </div>
            <div
                class="border-t border-white/5 pt-8 flex justify-between items-center text-[10px] text-gray-600 font-en tracking-widest">
                <p>&copy; 2025 <?php echo htmlspecialchars($company_name); ?>. ALL RIGHTS RESERVED.</p>
                <div>DESIGNED BY ATLASSHIFT</div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollToTopBtn"
        class="fixed bottom-8 right-8 bg-primary text-white w-12 h-12 rounded-full flex items-center justify-center shadow-lg translate-y-20 opacity-0 transition-all duration-300 z-50 hover:bg-white hover:text-black">
        <i class="fas fa-chevron-up"></i>
    </button>
    <script src="assets/js/main.js"></script>
</body>

</html>