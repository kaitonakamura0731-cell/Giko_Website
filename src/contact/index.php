<?php
session_start();

// Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Check for errors (e.g. from complete page if mail failed)
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') : '';

require_once '../admin/includes/db.php';
require_once '../admin/includes/settings_helper.php';

// Fetch Settings
$company_name = get_setting('company_name', 'GIKO307合同会社');
$company_address = get_setting('company_address', '〒483-8013 愛知県江南市般若町南山307');
$company_tel = get_setting('company_tel', '080-8887-2116');
$company_email = get_setting('company_email', 'info@giko-artisan.jp');
$instagram_url = get_setting('instagram_url', 'https://www.instagram.com/giko_artisan?igsh=MWRuenVqMzBkNzA3bw==');
$twitter_url = get_setting('twitter_url', '#');
?>
<!DOCTYPE html>
<html lang="ja" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CONTACT | 技巧 -Giko-</title>
    <!-- OGP -->
    <meta property="og:title" content="CONTACT | 技巧 -Giko-">
    <meta property="og:description" content="お問い合わせ。お見積もりやご相談はこちらから。">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://giko-artisan.jp/contact.php">
    <meta property="og:image" content="https://giko-artisan.jp/assets/images/ogp.jpg">
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
</head>

<body class="bg-black text-white antialiased">
    <!-- Header -->
    <header class="fixed w-full z-50 transition-all duration-300 bg-black/80 backdrop-blur-md border-b border-white/5"
        id="header">
        <div class="container mx-auto px-6 h-20 flex justify-between items-center">
            <a href="../index.html" class="flex items-center gap-3 group">
                <div
                    class="w-8 h-8 bg-primary rounded-sm flex items-center justify-center text-black font-bold font-en text-lg">
                    G</div>
                <span
                    class="text-xl font-bold tracking-widest font-en group-hover:text-primary transition-colors">GIKO</span>
            </a>
            <nav class="hidden lg:flex space-x-10 text-xs font-bold tracking-widest">
                <a href="../index.html#concept"
                    class="hover:text-primary transition-colors font-en relative group">CONCEPT</a>
                <a href="../pages/works.php"
                    class="hover:text-primary transition-colors font-en relative group">WORKS</a>
                <a href="../store/purchase.html"
                    class="hover:text-primary transition-colors font-en relative group">STORE</a>
                <a href="../index.html#before-after"
                    class="hover:text-primary transition-colors font-en relative group">BEFORE &
                    AFTER</a>
                <a href="../index.html#material"
                    class="hover:text-primary transition-colors font-en relative group">MATERIAL</a>
                <a href="../index.html#flow"
                    class="hover:text-primary transition-colors font-en relative group">FLOW</a>
                <a href="../index.html#company"
                    class="hover:text-primary transition-colors font-en relative group">COMPANY</a>
            </nav>
            <a href="../contact/index.php"
                class="hidden lg:inline-flex items-center justify-center px-8 py-2.5 border border-white/20 text-xs font-bold tracking-widest bg-primary text-white font-en">CONTACT</a>
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
                <a href="../index.html#concept"
                    class="text-white hover:text-primary font-en tracking-widest">CONCEPT</a>
                <a href="../pages/works.php" class="text-white hover:text-primary font-en tracking-widest">WORKS</a>
                <a href="../store/purchase.html" class="text-white hover:text-primary font-en tracking-widest">STORE</a>
                <a href="../index.html#before-after"
                    class="text-white hover:text-primary font-en tracking-widest">BEFORE &
                    AFTER</a>
                <a href="../index.html#material"
                    class="text-white hover:text-primary font-en tracking-widest">MATERIAL</a>
                <a href="../index.html#flow" class="text-white hover:text-primary font-en tracking-widest">FLOW</a>
                <a href="../index.html#company"
                    class="text-white hover:text-primary font-en tracking-widest">COMPANY</a>
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
            <h1 class="text-4xl md:text-6xl font-bold font-en tracking-widest mb-4">CONTACT</h1>
            <p class="text-gray-400 text-sm tracking-widest font-en uppercase">Start Your Project</p>
        </div>
        <div class="absolute inset-0 bg-[url('./assets/images/hero.png')] bg-cover bg-center opacity-20"></div>
    </section>

    <!-- Contact Form -->
    <section class="py-24 bg-black">
        <div class="container mx-auto px-6 max-w-4xl">
            <?php if (!empty($error) && $error === 'send'): ?>
                <div class="bg-red-900/30 border border-red-500/50 text-red-200 p-4 mb-8 text-center rounded-sm">
                    送信に失敗しました。時間をおいて再度お試しください。
                </div>
            <?php endif; ?>

            <p class="text-gray-400 text-sm leading-loose mb-12 text-center" data-i18n="contact_intro">
                お見積もりのご依頼、施工に関するご相談など、お気軽にお問い合わせください。<br>
                内容を確認次第、担当者よりご連絡させていただきます。
            </p>

            <form action="confirm.php" method="POST" class="space-y-8">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token"
                    value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label for="name"
                            class="block text-xs font-bold font-en tracking-widest mb-2 text-primary">NAME</label>
                        <input type="text" id="name" name="name"
                            class="w-full bg-secondary border border-white/10 text-white px-4 py-3 focus:outline-none focus:border-primary transition-colors"
                            placeholder="お名前" required>
                    </div>
                    <div>
                        <label for="email"
                            class="block text-xs font-bold font-en tracking-widest mb-2 text-primary">EMAIL</label>
                        <input type="email" id="email" name="email"
                            class="w-full bg-secondary border border-white/10 text-white px-4 py-3 focus:outline-none focus:border-primary transition-colors"
                            placeholder="メールアドレス" required>
                    </div>
                </div>
                <div>
                    <label for="tel"
                        class="block text-xs font-bold font-en tracking-widest mb-2 text-primary">PHONE</label>
                    <input type="tel" id="tel" name="tel"
                        class="w-full bg-secondary border border-white/10 text-white px-4 py-3 focus:outline-none focus:border-primary transition-colors"
                        placeholder="電話番号">
                </div>

                <!-- Added Subject field based on requirements, but hidden/merged in design if preferred. 
                     For now let's add it as explicit field to meet requirement. -->
                <div>
                    <label for="subject"
                        class="block text-xs font-bold font-en tracking-widest mb-2 text-primary">SUBJECT</label>
                    <input type="text" id="subject" name="subject"
                        class="w-full bg-secondary border border-white/10 text-white px-4 py-3 focus:outline-none focus:border-primary transition-colors"
                        placeholder="件名" required>
                </div>

                <div>
                    <label for="message"
                        class="block text-xs font-bold font-en tracking-widest mb-2 text-primary">MESSAGE</label>
                    <textarea id="message" name="message" rows="6"
                        class="w-full bg-secondary border border-white/10 text-white px-4 py-3 focus:outline-none focus:border-primary transition-colors"
                        placeholder="お問い合わせ内容" required></textarea>
                </div>

                <div class="text-center pt-8">
                    <button type="submit"
                        class="inline-block bg-primary text-white font-bold font-en tracking-widest px-12 py-4 hover:bg-white hover:text-black transition-all duration-300">
                        CONFIRM
                    </button>
                    <p class="text-[10px] text-gray-500 mt-4">確認画面へ進みます</p>
                </div>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-secondary pt-24 pb-12 border-t border-white/5 text-white">
        <!-- (Same footer as index) -->
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-20">
                <div>
                    <div class="text-3xl font-bold font-en tracking-widest mb-6">GIKO</div>
                    <p class="text-xs text-gray-500 leading-loose mb-6">最高級の素材と技術で、カーライフに彩りを。</p>
                    <div class="flex space-x-4">
                        <a href="<?php echo htmlspecialchars($instagram_url); ?>" target="_blank"
                            class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary transition-colors"><i
                                class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div>
                    <h3
                        class="text-sm font-bold font-en tracking-widest mb-6 border-b border-primary/30 inline-block pb-2">
                        MENU</h3>
                    <ul class="space-y-4 text-xs tracking-wider text-gray-400">
                        <li><a href="../index.html#concept" class="hover:text-white transition-colors">CONCEPT</a></li>
                        <li><a href="../pages/works.php" class="hover:text-white transition-colors">WORKS</a></li>
                        <li><a href="../index.html#before-after" class="hover:text-white transition-colors">BEFORE &
                                AFTER</a>
                        </li>
                        <li><a href="../index.html#material" class="hover:text-white transition-colors">MATERIAL</a>
                        </li>
                        <li><a href="../index.html#flow" class="hover:text-white transition-colors">FLOW</a></li>
                        <li><a href="../index.html#company" class="hover:text-white transition-colors">COMPANY</a></li>
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
                        <li class="flex items-start gap-4">
                            <i class="fas fa-phone mt-1 text-primary"></i>
                            <div>
                                <div class="text-white font-bold text-lg font-en">
                                    <?php echo htmlspecialchars($company_tel); ?></div>
                            </div>
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

    <script src="../assets/js/main.js"></script>

</body>

</html>