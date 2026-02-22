<?php
require_once '../admin/includes/db.php';
require_once '../admin/includes/settings_helper.php';

$instagram_url = get_setting('social_instagram', 'https://www.instagram.com/giko_artisan?igsh=MWRuenVqMzBkNzA3bw==');
$twitter_url = get_setting('social_twitter', 'https://x.com/giko_0203?s=21&t=wv4xW-XScSAbmdHqDnc6jA');
$youtube_url = get_setting('social_youtube', 'https://www.youtube.com/@GIKO-307');
$tiktok_url = get_setting('social_tiktok', 'https://www.tiktok.com/@giko_artisan?_r=1&_t=ZS-946uu1grw5U');
$line_url = get_setting('social_line', 'https://lin.ee/hmaVDuG');

try {
    $stmt = $pdo->query("SELECT * FROM products WHERE stock_status = 1 ORDER BY id ASC");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// 車種タグのユニークリストを取得
$allTags = [];
foreach ($products as $p) {
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

function getFirstImage($json)
{
    $images = json_decode($json ?? '[]', true);
    return $images[0] ?? '../assets/images/no_image.png';
}
?>
<!DOCTYPE html>
<html lang="ja" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ONLINE STORE | 技巧 -Giko-</title>
    <meta name="description" content="技巧 -Giko- 公式オンラインストア。厳選された本革を使用したステアリング、ナビカバーなどのオリジナル製品を販売。">
    <!-- OGP -->
    <meta property="og:title" content="ONLINE STORE | 技巧 -Giko-">
    <meta property="og:description" content="技巧 -Giko- 公式オンラインストア。厳選された本革を使用したステアリング、ナビカバーなどのオリジナル製品を販売。">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://giko-official.com/store/index.php">
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>document.documentElement.classList.add('js-loading');</script>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* フィルターアニメーション */
        .product-card {
            transition: opacity 0.4s ease, transform 0.4s ease;
        }
        .product-card.hidden-by-filter {
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
        .product-card.visible-by-filter {
            opacity: 1;
            transform: scale(1);
        }
        /* カルーセルスクロールバー非表示 */
        #store-filter-carousel::-webkit-scrollbar { display: none; }
        #store-filter-carousel { scrollbar-width: none; -ms-overflow-style: none; }
    </style>
</head>

<body class="bg-black text-white antialiased selection:bg-primary selection:text-white">

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
                <a href="../pages/works.php"
                    class="hover:text-primary transition-colors font-en relative group">WORKS</a>
                <a href="index.php" class="text-primary font-en relative group">
                    STORE
                    <span class="absolute -bottom-2 left-0 w-full h-0.5 bg-primary"></span>
                </a>
                <a href="../pages/before_after.html"
                    class="hover:text-primary transition-colors font-en relative group">BEFORE & AFTER</a>
                <a href="../index.php#material"
                    class="hover:text-primary transition-colors font-en relative group">MATERIAL</a>
                <a href="../index.php#flow" class="hover:text-primary transition-colors font-en relative group">FLOW</a>
                <a href="../index.php#company"
                    class="hover:text-primary transition-colors font-en relative group">COMPANY</a>
            </nav>

            <a href="../contact/index.php"
                class="hidden lg:inline-flex items-center justify-center px-8 py-2.5 border border-white/20 text-xs font-bold tracking-widest hover:bg-primary hover:border-primary hover:text-white transition-all duration-300 font-en">
                CONTACT
            </a>

            <a href="cart.html" class="hidden lg:flex ml-6 relative group">
                <i class="fas fa-shopping-cart text-white text-lg group-hover:text-primary transition-colors"></i>
                <span id="cart-badge-desktop"
                    class="cart-badge absolute -top-2 -right-2 bg-primary text-black text-[10px] font-bold px-1.5 rounded-full hidden">0</span>
            </a>

            <button class="lg:hidden text-white focus:outline-none ml-4 relative" id="mobile-menu-btn">
                <div class="space-y-2">
                    <span class="block w-8 h-0.5 bg-white"></span>
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
                <a href="cart.html"
                    class="text-white hover:text-primary font-en tracking-widest flex items-center justify-center gap-2">
                    <i class="fas fa-shopping-cart"></i> CART <span id="cart-badge-menu"
                        class="cart-badge bg-primary text-black text-xs font-bold px-2 py-0.5 rounded-full hidden">0</span>
                </a>
                <a href="../index.php#concept" class="text-white hover:text-primary font-en tracking-widest">CONCEPT</a>
                <a href="../pages/works.php" class="text-white hover:text-primary font-en tracking-widest">WORKS</a>
                <a href="index.php" class="text-primary font-en tracking-widest">STORE</a>
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

    <!-- Hero Section -->
    <section class="relative py-40 bg-secondary overflow-hidden">
        <div class="absolute inset-0 bg-[url('../assets/images/hero.png')] bg-cover bg-center opacity-20"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-black/50 to-black"></div>
        <div class="container mx-auto px-6 relative z-10 text-center fade-in">
            <h1 class="text-4xl md:text-6xl font-bold font-en tracking-widest mb-6">ONLINE STORE</h1>
            <p class="text-sm md:text-base text-gray-400 tracking-[0.2em] font-light">SIGNATURE PRODUCTS</p>
        </div>
    </section>

    <!-- Product Grid -->
    <section class="py-20 bg-black">
        <div class="container mx-auto px-6">
            <!-- 車種フィルター タイルカルーセル -->
            <?php if (!empty($allTags)): ?>
            <div class="mb-16 relative max-w-5xl mx-auto">
                <!-- 左ナビボタン -->
                <button type="button" id="store-filter-prev"
                    class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-5 z-20 w-10 h-10 bg-black/80 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-primary hover:border-primary transition-all duration-300 backdrop-blur-sm opacity-0 pointer-events-none"
                    style="transition: opacity 0.3s;">
                    <i class="fas fa-chevron-left text-sm"></i>
                </button>

                <!-- スクロールコンテナ -->
                <div id="store-filter-carousel" class="flex gap-3 overflow-x-auto scroll-smooth px-1 py-2 justify-center">
                    <!-- ALL タイル -->
                    <button type="button" onclick="filterProducts('all')"
                        class="filter-btn group relative overflow-hidden flex-shrink-0 w-[140px] md:w-[180px] aspect-[16/10] flex items-center justify-center border transition-all duration-500 cursor-pointer border-primary"
                        data-tag="all">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/50 to-black/30"></div>
                        <div class="active-overlay absolute inset-0 bg-primary/20 border-2 border-primary"></div>
                        <div class="relative z-10 text-center">
                            <span class="filter-label text-sm md:text-base font-bold tracking-wider text-primary transition-colors duration-300 font-en">ALL</span>
                            <div class="text-[8px] md:text-[9px] font-en tracking-widest text-gray-400 mt-1">ALL PRODUCTS</div>
                        </div>
                    </button>
                    <?php foreach ($allTags as $tag): 
                        // このタグに対応する最初の商品画像を取得
                        $tagImage = '';
                        
                        // アルファードの場合は固定画像を使用（商品がなくても表示するため）
                        if (strtolower($tag) === 'alphard' || $tag === 'アルファード') {
                            $tagImage = '../assets/images/alphard.jpg';
                        } else {
                            // その他のタグは商品画像から取得
                            foreach ($products as $p) {
                                $pTags = array_map('trim', explode(',', $p['vehicle_tags'] ?? ''));
                                if (in_array($tag, $pTags)) {
                                    $tagImage = getFirstImage($p['images']);
                                    break;
                                }
                            }
                        }
                    ?>
                    <button type="button" onclick="filterProducts('<?php echo htmlspecialchars($tag); ?>')"
                        class="filter-btn group relative overflow-hidden flex-shrink-0 w-[140px] md:w-[180px] aspect-[16/10] flex items-center justify-center border border-white/10 hover:border-primary/50 transition-all duration-500 cursor-pointer"
                        data-tag="<?php echo htmlspecialchars($tag); ?>">
                        <!-- 背景画像 -->
                        <?php if ($tagImage): ?>
                        <div class="absolute inset-0 bg-cover bg-center opacity-30 group-hover:opacity-50 group-hover:scale-110 transition-all duration-700" style="background-image: url('<?php echo htmlspecialchars($tagImage); ?>');"></div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-black/20"></div>
                        <!-- テキスト -->
                        <div class="relative z-10 text-center">
                            <span class="filter-label text-sm md:text-base font-bold tracking-wider group-hover:text-primary transition-colors duration-300"><?php echo htmlspecialchars($tag); ?></span>
                        </div>
                    </button>
                    <?php endforeach; ?>
                </div>

                <!-- 右ナビボタン -->
                <button type="button" id="store-filter-next"
                    class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-5 z-20 w-10 h-10 bg-black/80 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-primary hover:border-primary transition-all duration-300 backdrop-blur-sm"
                    style="transition: opacity 0.3s;">
                    <i class="fas fa-chevron-right text-sm"></i>
                </button>
            </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 max-w-5xl mx-auto" id="product-grid">
                <?php foreach ($products as $product): 
                    $productTags = $product['vehicle_tags'] ?? '';
                    $img = getFirstImage($product['images']);
                ?>
                    <article class="product-card group visible-by-filter" data-tags="<?php echo htmlspecialchars($productTags); ?>">
                        <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="block">
                            <!-- 商品画像 -->
                            <div class="aspect-square w-full overflow-hidden bg-gray-900 border border-white/10 relative mb-4">
                                <?php if ($img): ?>
                                <img src="<?php echo htmlspecialchars($img); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                                <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-600 font-en text-xs tracking-widest">
                                    NO IMAGE
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- 商品情報 -->
                            <div class="text-center">
                                <h3 class="text-base md:text-lg font-bold font-en tracking-wider text-white group-hover:text-primary transition-colors duration-300 mb-2">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </h3>
                                <div class="text-sm font-en font-bold text-gray-400">
                                    ¥<?php echo number_format($product['price']); ?>
                                    <span class="text-[10px] font-normal text-gray-500 ml-1">~</span>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Order Made Link Section -->
    <section class="py-20 bg-secondary border-t border-white/5">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-2xl md:text-3xl font-bold font-en tracking-widest mb-6">FULL ORDER MADE</h2>
            <p class="text-gray-400 mb-8 max-w-2xl mx-auto leading-loose">
                既製品では満足できない方へ。<br>
                素材、デザイン、ステッチ...すべてをあなたの思い通りに。<br>
                世界に一つだけのインテリアを製作するフルオーダーメイドサービスも承っております。
            </p>
            <a href="ordermade.html"
                class="inline-block border border-white/20 px-12 py-4 hover:bg-white hover:text-black hover:border-white transition-all duration-300 font-en tracking-widest text-sm">
                VIEW ORDER MADE
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-secondary pt-24 pb-12 border-t border-white/5 text-white">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-20">
                <div>
                    <img src="../assets/images/logo_new.png" alt="GIKO" class="h-8 mb-6">
                    <p class="text-xs text-gray-500 leading-loose mb-6">最高級の素材と技術で、カーライフに彩りを。</p>
                    <div class="flex space-x-3">
                        <a href="<?php echo htmlspecialchars($tiktok_url); ?>" target="_blank" class="w-9 h-9 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary transition-colors text-sm"><i class="fab fa-tiktok"></i></a>
                        <a href="<?php echo htmlspecialchars($twitter_url); ?>" target="_blank" class="w-9 h-9 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary transition-colors text-sm"><i class="fab fa-x-twitter"></i></a>
                        <a href="<?php echo htmlspecialchars($youtube_url); ?>" target="_blank" class="w-9 h-9 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary transition-colors text-sm"><i class="fab fa-youtube"></i></a>
                        <a href="<?php echo htmlspecialchars($instagram_url); ?>" target="_blank" class="w-9 h-9 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary transition-colors text-sm"><i class="fab fa-instagram"></i></a>
                        <a href="<?php echo htmlspecialchars($line_url); ?>" target="_blank" class="w-9 h-9 rounded-full bg-white/5 flex items-center justify-center hover:bg-[#06C755] transition-colors text-sm"><i class="fab fa-line"></i></a>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-bold font-en tracking-widest mb-6 border-b border-primary/30 inline-block pb-2">MENU</h3>
                    <ul class="space-y-4 text-xs tracking-wider text-gray-400">
                        <li><a href="../index.php#concept" class="hover:text-white transition-colors">CONCEPT</a></li>
                        <li><a href="../pages/works.php" class="hover:text-white transition-colors">WORKS</a></li>
                        <li><a href="../pages/before_after.html" class="hover:text-white transition-colors">BEFORE & AFTER</a></li>
                        <li><a href="../index.php#flow" class="hover:text-white transition-colors">FLOW</a></li>
                        <li><a href="../index.php#company" class="hover:text-white transition-colors">COMPANY</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-bold font-en tracking-widest mb-6 border-b border-primary/30 inline-block pb-2">CONTACT</h3>
                    <ul class="space-y-4 text-xs tracking-wider text-gray-400">
                        <li class="flex items-start gap-4">
                            <a href="../contact/index.php" class="hover:text-white transition-colors">お問い合わせフォーム</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-bold font-en tracking-widest mb-6 border-b border-primary/30 inline-block pb-2">LEGAL</h3>
                    <ul class="space-y-4 text-xs tracking-wider text-gray-400">
                        <li><a href="../legal/privacy.html" class="hover:text-white transition-colors">プライバシーポリシー</a></li>
                        <li><a href="../legal/tokusho.html" class="hover:text-white transition-colors">特定商取引法に基づく表記</a></li>
                        <li><a href="../legal/terms.html" class="hover:text-white transition-colors">利用規約</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/5 pt-8 flex justify-between items-center text-[10px] text-gray-600 font-en tracking-widest">
                <p>&copy; 2025 GIKO. ALL RIGHTS RESERVED.</p>
                <div>DESIGNED BY ATLASSHIFT</div>
            </div>
        </div>
    </footer>

    <script>
        // 車種フィルター機能
        function filterProducts(tag) {
            const cards = document.querySelectorAll('.product-card');
            const buttons = document.querySelectorAll('.filter-btn');

            // タイルのアクティブ状態を更新
            buttons.forEach(btn => {
                const overlay = btn.querySelector('.active-overlay');
                const label = btn.querySelector('.filter-label');
                if (btn.getAttribute('data-tag') === tag) {
                    btn.classList.add('border-primary');
                    btn.classList.remove('border-white/10');
                    if (!overlay) {
                        const newOverlay = document.createElement('div');
                        newOverlay.className = 'active-overlay absolute inset-0 bg-primary/20 border-2 border-primary';
                        btn.insertBefore(newOverlay, btn.children[btn.children.length - 1]);
                    }
                    if (label) label.classList.add('text-primary');
                } else {
                    btn.classList.remove('border-primary');
                    btn.classList.add('border-white/10');
                    if (overlay) overlay.remove();
                    if (label) label.classList.remove('text-primary');
                }
            });

            // 商品カードをフィルタリング（アニメーション付き）
            cards.forEach(card => {
                if (tag === 'all') {
                    card.classList.remove('hidden-by-filter');
                    card.classList.add('visible-by-filter');
                    return;
                }
                const cardTags = (card.getAttribute('data-tags') || '').split(',').map(t => t.trim());
                if (cardTags.includes(tag)) {
                    card.classList.remove('hidden-by-filter');
                    card.classList.add('visible-by-filter');
                } else {
                    card.classList.remove('visible-by-filter');
                    card.classList.add('hidden-by-filter');
                }
            });
        }

        // カルーセルナビゲーション
        document.addEventListener('DOMContentLoaded', () => {
            const carousel = document.getElementById('store-filter-carousel');
            const prevBtn = document.getElementById('store-filter-prev');
            const nextBtn = document.getElementById('store-filter-next');
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

            // URLパラメータによる初期フィルタリング
            const urlParams = new URLSearchParams(window.location.search);
            const tag = urlParams.get('tag');
            if (tag) {
                // 少し遅延させて実行（要素描画待ち＆アニメーション見せるため）
                setTimeout(() => {
                    filterProducts(tag);
                    
                    // カルーセル内で該当ボタンを探してスクロール
                    const targetBtn = document.querySelector(`.filter-btn[data-tag="${tag}"]`);
                    if (targetBtn) {
                        targetBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                    }
                }, 300);
            }
        });
    </script>
    <script src="../assets/js/main.js"></script>
</body>

</html>