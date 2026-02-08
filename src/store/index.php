<?php
require_once '../admin/includes/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM products WHERE stock_status = 1 ORDER BY id ASC");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>document.documentElement.classList.add('js-loading');</script>
    <link rel="stylesheet" href="../css/style.css">
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
                </div>
                <span id="cart-badge-mobile-btn"
                    class="cart-badge absolute -top-2 -right-2 bg-primary text-black text-[10px] font-bold px-1.5 rounded-full hidden">0</span>
            </button>
        </div>
        <!-- Mobile Menu Omitted for brevity, assuming standard include or similar logic -->
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($products as $product): ?>
                    <article class="group fade-in">
                        <a href="product_detail.php?id=<?php echo $product['id']; ?>"
                            class="block bg-secondary overflow-hidden relative border border-white/5 hover:border-primary/50 transition-colors duration-300">
                            <div class="overflow-hidden aspect-square">
                                <img src="<?php echo htmlspecialchars(getFirstImage($product['images'])); ?>"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                                    class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                            </div>
                            <div class="p-8">
                                <div class="text-primary text-[10px] font-bold tracking-widest mb-3 font-en">PRODUCT</div>
                                <h3 class="text-lg font-bold mb-2"><?php echo nl2br(htmlspecialchars($product['name'])); ?>
                                </h3>
                                <p class="text-xs text-gray-400 mb-4 line-clamp-2">
                                    <?php
                                    $desc = $product['short_description'] ?? '';
                                    if (!$desc) {
                                        // Fallback to strip_tags of description
                                        $desc = strip_tags($product['description']);
                                    }
                                    echo htmlspecialchars($desc);
                                    ?>
                                </p>
                                <div class="flex justify-between items-center border-t border-white/10 pt-4">
                                    <span class="font-en font-bold text-lg">¥<?php echo number_format($product['price']); ?>
                                        <span class="text-xs font-normal text-gray-500">~</span></span>
                                    <span
                                        class="text-xs font-bold font-en tracking-widest group-hover:text-primary transition-colors">VIEW
                                        DETAILS <i class="fas fa-arrow-right ml-1"></i></span>
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
    <footer class="bg-black border-t border-white/10 pt-20 pb-10">
        <div class="container mx-auto px-6">
            <div class="border-t border-white/10 pt-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-xs text-gray-600 font-en">© 2025 GIKO. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="../assets/js/main.js"></script>
</body>

</html>