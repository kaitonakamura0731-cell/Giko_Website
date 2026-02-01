<?php
require_once '../admin/includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: works.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM works WHERE id = ?");
    $stmt->execute([$id]);
    $work = $stmt->fetch();

    if (!$work) {
        header("Location: works.php");
        exit;
    }

    // Get Next/Prev
    $prev = $pdo->query("SELECT id FROM works WHERE id < $id ORDER BY id DESC LIMIT 1")->fetch();
    $next = $pdo->query("SELECT id FROM works WHERE id > $id ORDER BY id ASC LIMIT 1")->fetch();

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
    <title><?php echo htmlspecialchars($work['title']); ?> | Custom Works | 技巧 -Giko-</title>
    <!-- OGP -->
    <meta property="og:title" content="<?php echo htmlspecialchars($work['title']); ?> | Custom Works | 技巧 -Giko-">
    <meta property="og:description" content="<?php echo htmlspecialchars($work['description']); ?>">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://giko-artisan.jp/pages/work_detail.php?id=<?php echo $id; ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($work['main_image']); ?>">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../tailwind_config.js"></script>
    <script src="../assets/js/cart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-black text-white antialiased">
    <!-- Header (Same as works.php) -->
    <header class="fixed w-full z-50 transition-all duration-300 bg-black/80 backdrop-blur-md border-b border-white/5"
        id="header">
        <div class="container mx-auto px-6 h-20 flex justify-between items-center">
            <a href="../index.php" class="flex items-center gap-3 group">
                <img src="../assets/images/logo_new.png" alt="GIKO" class="h-8 w-auto object-contain">
            </a>
            <nav class="hidden lg:flex space-x-10 text-xs font-bold tracking-widest">
                <a href="../index.html#concept"
                    class="hover:text-primary transition-colors font-en relative group">CONCEPT</a>
                <a href="../pages/works.php" class="text-primary font-en relative group">WORKS</a>
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
                class="hidden lg:inline-flex items-center justify-center px-8 py-2.5 border border-white/20 text-xs font-bold tracking-widest hover:bg-primary hover:border-primary hover:text-white transition-all duration-300 font-en">CONTACT</a>
        </div>
    </header>

    <!-- Work Hero -->
    <section class="relative h-[60vh] min-h-[500px] flex items-end justify-start overflow-hidden">
        <div class="absolute inset-0 z-0">
            <?php if ($hero_image): ?>
                <img src="<?php echo htmlspecialchars($hero_image); ?>" alt="Hero"
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
                <?php echo htmlspecialchars($work['title']); ?></h1>
            <p class="text-gray-400 text-sm md:text-base tracking-widest font-en uppercase">
                <?php echo htmlspecialchars($work['description']); ?></p>
        </div>
    </section>

    <!-- Specs & Concept -->
    <section class="py-24 bg-black border-b border-white/5">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row gap-20">
                <div class="lg:w-2/3">
                    <h2 class="text-2xl font-bold font-en tracking-widest mb-8 text-white">CONCEPT</h2>
                    <p class="text-gray-400 leading-loose text-justify mb-10 text-sm md:text-base">
                        <?php echo nl2br(htmlspecialchars($work['concept_text'])); ?>
                    </p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                        <div class="bg-secondary p-4 border border-white/5 text-center">
                            <i class="fas fa-couch text-primary text-2xl mb-2"></i>
                            <div class="text-[10px] text-gray-500 font-en tracking-widest">SEAT</div>
                            <div class="font-bold text-sm">
                                <?php echo htmlspecialchars(getJson($work['specs'], 'seat')); ?></div>
                        </div>
                        <div class="bg-secondary p-4 border border-white/5 text-center">
                            <i class="fas fa-layer-group text-primary text-2xl mb-2"></i>
                            <div class="text-[10px] text-gray-500 font-en tracking-widest">MATERIAL</div>
                            <div class="font-bold text-sm">
                                <?php echo htmlspecialchars(getJson($work['specs'], 'material')); ?></div>
                        </div>
                        <div class="bg-secondary p-4 border border-white/5 text-center">
                            <i class="fas fa-palette text-primary text-2xl mb-2"></i>
                            <div class="text-[10px] text-gray-500 font-en tracking-widest">COLOR</div>
                            <div class="font-bold text-sm">
                                <?php echo htmlspecialchars(getJson($work['specs'], 'color')); ?></div>
                        </div>
                        <div class="bg-secondary p-4 border border-white/5 text-center">
                            <i class="far fa-clock text-primary text-2xl mb-2"></i>
                            <div class="text-[10px] text-gray-500 font-en tracking-widest">PERIOD</div>
                            <div class="font-bold text-sm">
                                <?php echo htmlspecialchars(getJson($work['specs'], 'period')); ?></div>
                        </div>
                    </div>
                </div>
                <div class="lg:w-1/3">
                    <h2
                        class="text-lg font-bold font-en tracking-widest mb-8 text-gray-500 border-b border-white/10 pb-2">
                        DATA</h2>
                    <dl class="space-y-6 text-sm">
                        <div>
                            <dt class="text-[10px] text-primary font-en tracking-widest mb-1">CAR MODEL</dt>
                            <dd class="font-medium">
                                <?php echo htmlspecialchars(getJson($work['data_info'], 'model')); ?></dd>
                        </div>
                        <div>
                            <dt class="text-[10px] text-primary font-en tracking-widest mb-1">MENU</dt>
                            <dd class="font-medium"><?php echo htmlspecialchars(getJson($work['data_info'], 'menu')); ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[10px] text-primary font-en tracking-widest mb-1">MATERIAL</dt>
                            <dd class="font-medium">
                                <?php echo htmlspecialchars(getJson($work['data_info'], 'material')); ?></dd>
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
                                <?php echo htmlspecialchars(getJson($work['data_info'], 'price')); ?></dd>
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
    </section>

    <!-- Gallery -->
    <?php $gallery = getGallery($work['gallery_images']); ?>
    <?php if (count($gallery) > 0): ?>
        <section class="py-24 bg-secondary">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl font-bold font-en tracking-widest mb-12 text-center">GALLERY</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($gallery as $img): ?>
                        <div class="aspect-square bg-black overflow-hidden relative group">
                            <img src="<?php echo htmlspecialchars($img); ?>" alt="Gallery"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Next/Prev Navigation -->
    <section class="py-20 bg-black border-t border-white/5">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-stretch gap-8 md:gap-0">
                <!-- PREV -->
                <?php if ($prev): ?>
                    <a href="work_detail.php?id=<?php echo $prev['id']; ?>"
                        class="group flex-1 md:py-8 md:pr-4 md:border-r border-white/10 hover:bg-white/5 transition-all duration-500 flex items-center justify-start gap-6 md:pl-10">
                        <div class="text-left">
                            <span class="block text-[10px] font-bold text-primary font-en tracking-widest mb-1">PREV</span>
                            <div class="text-xl font-bold font-en text-white group-hover:text-primary transition-colors">
                                PREV WORK</div>
                        </div>
                    </a>
                <?php else: ?>
                    <div class="flex-1"></div>
                <?php endif; ?>

                <!-- ALL -->
                <a href="works.php"
                    class="flex-none px-12 py-8 flex items-center justify-center hover:bg-white/5 transition-all group">
                    <div class="w-2 h-2 bg-primary rounded-full group-hover:scale-150 transition-transform"></div>
                    <div
                        class="w-2 h-2 bg-primary rounded-full mx-2 group-hover:scale-150 transition-transform delay-75">
                    </div>
                    <div class="w-2 h-2 bg-primary rounded-full group-hover:scale-150 transition-transform delay-150">
                    </div>
                </a>

                <!-- NEXT -->
                <?php if ($next): ?>
                    <a href="work_detail.php?id=<?php echo $next['id']; ?>"
                        class="group flex-1 md:py-8 md:pl-4 md:border-l border-white/10 hover:bg-white/5 transition-all duration-500 flex items-center justify-end gap-6 md:pr-10">
                        <div class="text-right">
                            <span class="block text-[10px] font-bold text-primary font-en tracking-widest mb-1">NEXT</span>
                            <div class="text-xl font-bold font-en text-white group-hover:text-primary transition-colors">
                                NEXT WORK</div>
                        </div>
                    </a>
                <?php else: ?>
                    <div class="flex-1"></div>
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

