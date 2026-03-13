<?php
require_once '../admin/includes/db.php';

$id = $_GET['id'] ?? null;

// IDを整数として検証（SQLインジェクション対策）
if (!$id || !is_numeric($id)) {
    header("Location: works.php");
    exit;
}
$id = (int)$id;

try {
    $stmt = $pdo->prepare("SELECT * FROM works WHERE id = ?");
    $stmt->execute([$id]);
    $work = $stmt->fetch();

    if (!$work) {
        header("Location: works.php");
        exit;
    }

    // Get Next/Prev（プリペアドステートメント使用、サムネイル・タイトル含む）
    $prev_stmt = $pdo->prepare("SELECT id, title, main_image FROM works WHERE id < ? ORDER BY id DESC LIMIT 1");
    $prev_stmt->execute([$id]);
    $prev = $prev_stmt->fetch();

    $next_stmt = $pdo->prepare("SELECT id, title, main_image FROM works WHERE id > ? ORDER BY id ASC LIMIT 1");
    $next_stmt->execute([$id]);
    $next = $next_stmt->fetch();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Helpers
function getJson($json, $key)
{
    $data = json_decode($json ?? '{}', true);
    return $data[$key] ?? '';
}

function getGallery($json)
{
    return json_decode($json ?? '[]', true) ?: [];
}

$hero_image = $work['hero_image'] ?: $work['main_image']; // Fallback
?>
<!DOCTYPE html>
<html lang="ja" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/images/favicon.ico">
    <title><?php echo htmlspecialchars($work['title']); ?> | Custom Works | 技巧 -GIKO-</title>
    <!-- OGP -->
    <meta property="og:title" content="<?php echo htmlspecialchars($work['title']); ?> | Custom Works | 技巧 -GIKO-">
    <meta property="og:description" content="<?php echo htmlspecialchars($work['description']); ?>">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://giko-official.com/pages/work_detail.php?id=<?php echo $id; ?>">
    <meta property="og:image" content="<?php echo '../' . htmlspecialchars($work['main_image']); ?>">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../tailwind_config.js"></script>
    <script src="../assets/js/cart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
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
                    <span class="block w-8 h-0.5 bg-white"></span>
                </div>
                <span id="cart-badge-mobile-btn"
                    class="cart-badge absolute -top-2 -right-2 bg-primary text-black text-[10px] font-bold px-1.5 rounded-full hidden">0</span>
            </button>
        </div>
        <div class="lg:hidden hidden bg-secondary border-t border-white/10 absolute w-full top-20 left-0 h-[calc(100vh-5rem)] overflow-y-auto"
            id="mobile-menu">
            <nav class="flex flex-col p-10 pb-24 space-y-8 text-center text-lg">
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

    <!-- Work Hero -->
    <section class="relative h-[60vh] min-h-[500px] flex items-end justify-start overflow-hidden">
        <div class="absolute inset-0 z-0">
            <?php if ($hero_image): ?>
                <img src="<?php echo '../' . htmlspecialchars($hero_image); ?>" alt="Hero"
                    class="w-full h-full object-cover opacity-60">
            <?php else: ?>
                <div class="w-full h-full bg-gray-800 opacity-60"></div>
            <?php endif; ?>
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
        </div>
        <div class="relative z-10 container mx-auto px-6 pb-20">
            <a href="works.php"
                class="inline-flex items-center gap-2 text-primary font-bold tracking-widest text-xs font-en mb-6 hover:text-white transition-colors">
                <i class="fas fa-arrow-left"></i> BACK TO WORKS
            </a>
            <div
                class="inline-block px-3 py-1 border border-white/30 text-[10px] tracking-[0.2em] font-en mb-4 uppercase">
                <?php echo htmlspecialchars($work['category']); ?>
            </div>
            <h1 class="text-4xl md:text-7xl font-bold font-en tracking-tighter mb-4">
                <?php echo htmlspecialchars($work['title']); ?>
            </h1>
            <p class="text-gray-400 text-sm md:text-base tracking-widest font-en uppercase">
                <?php echo htmlspecialchars($work['description']); ?>
            </p>
        </div>
    </section>

    <!-- Concept, Specs & Gallery + DATA -->
    <?php $gallery = getGallery($work['gallery_images']); ?>
    <section class="py-24 bg-black border-b border-white/5">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row gap-20">

                <!-- 左カラム: CONCEPT → スペックカード → ギャラリー（スクロール可能） -->
                <div class="lg:w-2/3">
                    <!-- CONCEPT -->
                    <h2 class="text-2xl font-bold font-en tracking-widest mb-8 text-white">CONCEPT</h2>
                    <p class="text-gray-400 leading-loose text-justify mb-10 text-sm md:text-base">
                        <?php echo nl2br(htmlspecialchars($work['concept_text'])); ?>
                    </p>

                    <!-- スペックカード（SEAT→PRICEに変更） -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                        <div class="bg-secondary p-4 border border-white/5 text-center">
                            <i class="fas fa-yen-sign text-primary text-2xl mb-2"></i>
                            <div class="text-[10px] text-gray-500 font-en tracking-widest">PRICE</div>
                            <div class="font-bold text-sm">
                                <?php echo htmlspecialchars(getJson($work['specs'], 'seat')); ?>
                            </div>
                        </div>
                        <div class="bg-secondary p-4 border border-white/5 text-center">
                            <i class="fas fa-layer-group text-primary text-2xl mb-2"></i>
                            <div class="text-[10px] text-gray-500 font-en tracking-widest">MATERIAL</div>
                            <div class="font-bold text-sm">
                                <?php echo htmlspecialchars(getJson($work['specs'], 'material')); ?>
                            </div>
                        </div>
                        <div class="bg-secondary p-4 border border-white/5 text-center">
                            <i class="fas fa-palette text-primary text-2xl mb-2"></i>
                            <div class="text-[10px] text-gray-500 font-en tracking-widest">COLOR</div>
                            <div class="font-bold text-sm">
                                <?php echo htmlspecialchars(getJson($work['specs'], 'color')); ?>
                            </div>
                        </div>
                        <div class="bg-secondary p-4 border border-white/5 text-center">
                            <i class="far fa-clock text-primary text-2xl mb-2"></i>
                            <div class="text-[10px] text-gray-500 font-en tracking-widest">PERIOD</div>
                            <div class="font-bold text-sm">
                                <?php echo htmlspecialchars(getJson($work['specs'], 'period')); ?>
                            </div>
                        </div>
                    </div>

                    <!-- ギャラリー（左カラム内、CONCEPTのすぐ下） -->
                    <?php if (count($gallery) > 0): ?>
                        <div class="mt-16">
                            <h2 class="text-2xl font-bold font-en tracking-widest mb-8 text-white">GALLERY</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php foreach ($gallery as $img): ?>
                                    <div class="aspect-square bg-secondary overflow-hidden relative group">
                                        <img src="<?php echo '../' . htmlspecialchars($img); ?>" alt="Gallery"
                                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 右カラム: DATA（スティッキー固定） -->
                <div class="lg:w-1/3">
                    <div class="lg:sticky lg:top-28">
                        <h2
                            class="text-lg font-bold font-en tracking-widest mb-8 text-gray-500 border-b border-white/10 pb-2">
                            DATA</h2>
                        <dl class="space-y-6 text-sm">
                            <div>
                                <dt class="text-[10px] text-primary font-en tracking-widest mb-1">CAR MODEL</dt>
                                <dd class="font-medium">
                                    <?php echo htmlspecialchars(getJson($work['data_info'], 'model')); ?>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-[10px] text-primary font-en tracking-widest mb-1">型式</dt>
                                <dd class="font-medium">
                                    <?php echo htmlspecialchars(getJson($work['data_info'], 'model_code')); ?>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-[10px] text-primary font-en tracking-widest mb-1">MATERIAL</dt>
                                <dd class="font-medium">
                                    <?php echo htmlspecialchars(getJson($work['data_info'], 'material')); ?>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-[10px] text-primary font-en tracking-widest mb-1">CONTENT</dt>
                                <dd class="font-medium text-gray-400 leading-relaxed">
                                    <?php echo nl2br(htmlspecialchars(getJson($work['data_info'], 'content'))); ?>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-[10px] text-primary font-en tracking-widest mb-1">PRICE</dt>
                                <dd class="font-medium text-xl font-en">
                                    <?php echo htmlspecialchars(getJson($work['data_info'], 'price')); ?>
                                </dd>
                                <dd class="text-[10px] text-gray-600 mt-1">※参考価格</dd>
                            </div>
                        </dl>
                        <a href="../contact/index.php"
                            class="block w-full mt-10 text-center bg-white text-black py-4 font-bold tracking-widest font-en hover:bg-primary transition-colors">
                            ASK US
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Next/Prev Navigation -->
    <section class="py-12 md:py-20 bg-black border-t border-white/5">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-3 items-center gap-4">
                <!-- PREV -->
                <?php if ($prev): ?>
                    <a href="work_detail.php?id=<?php echo $prev['id']; ?>"
                        class="group flex items-center gap-2 md:gap-3 hover:opacity-80 transition-all duration-300">
                        <i class="fas fa-chevron-left text-xs text-gray-500 group-hover:text-primary transition-colors flex-shrink-0"></i>
                        <div class="w-10 h-10 md:w-16 md:h-16 rounded-sm overflow-hidden flex-shrink-0 border border-white/10">
                            <img src="../<?php echo htmlspecialchars($prev['main_image']); ?>" alt="" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-bold text-gray-500 font-en tracking-widest mb-0.5">PREV</span>
                            <div class="text-xs md:text-sm font-bold text-white group-hover:text-primary transition-colors truncate max-w-[80px] md:max-w-none"><?php echo htmlspecialchars($prev['title']); ?></div>
                        </div>
                    </a>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>

                <!-- ALL -->
                <a href="works.php"
                    class="flex items-center justify-center py-4 hover:bg-white/5 rounded-sm transition-all group">
                    <div class="flex items-center gap-1.5">
                        <div class="w-1.5 h-1.5 bg-primary rounded-full group-hover:scale-150 transition-transform"></div>
                        <div class="w-1.5 h-1.5 bg-primary rounded-full group-hover:scale-150 transition-transform delay-75"></div>
                        <div class="w-1.5 h-1.5 bg-primary rounded-full group-hover:scale-150 transition-transform delay-150"></div>
                    </div>
                </a>

                <!-- NEXT -->
                <?php if ($next): ?>
                    <a href="work_detail.php?id=<?php echo $next['id']; ?>"
                        class="group flex items-center gap-2 md:gap-3 justify-end hover:opacity-80 transition-all duration-300">
                        <div class="min-w-0 text-right">
                            <span class="block text-[10px] font-bold text-gray-500 font-en tracking-widest mb-0.5">NEXT</span>
                            <div class="text-xs md:text-sm font-bold text-white group-hover:text-primary transition-colors truncate max-w-[80px] md:max-w-none"><?php echo htmlspecialchars($next['title']); ?></div>
                        </div>
                        <div class="w-10 h-10 md:w-16 md:h-16 rounded-sm overflow-hidden flex-shrink-0 border border-white/10">
                            <img src="../<?php echo htmlspecialchars($next['main_image']); ?>" alt="" class="w-full h-full object-cover">
                        </div>
                        <i class="fas fa-chevron-right text-xs text-gray-500 group-hover:text-primary transition-colors flex-shrink-0"></i>
                    </a>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-secondary pt-12 pb-12 border-t border-white/5 text-white text-center">
        <p class="text-[10px] text-gray-600 font-en tracking-widest">&copy; 2025 GIKO. ALL RIGHTS RESERVED.</p>
    </footer>

    <script src="../assets/js/main.js"></script>
</body>

</html>