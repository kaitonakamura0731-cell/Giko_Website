<?php
require_once '../admin/includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        header("Location: index.php");
        exit;
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Function to adjust image paths
function adjustPath($path) {
    if (empty($path)) return '';
    if (strpos($path, '../assets') === 0) return $path;
    if (strpos($path, 'assets/') === 0) return '../' . $path;
    return $path;
}

$images = json_decode($product['images'] ?? '[]', true);
$options = json_decode($product['options'] ?? '[]', true);

// Subdirectory fix: prepend ../ if path starts with assets/
function adjustPath($path) {
    if (empty($path)) return '';
    if (strpos($path, 'assets/') === 0) {
        return '../' . $path;
    }
    return $path;
}

$main_image = !empty($images[0]) ? adjustPath($images[0]) : '../assets/images/no_image.png';

$default_swatch = '../assets/images/no_image.png';
?>
<!DOCTYPE html>
<html lang="ja" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> | 技巧 -Giko-</title>
    <!-- OGP etc omitted for brevity -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../tailwind_config.js"></script>
    <script src="../assets/js/cart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    </header>

    <section class="min-h-screen pt-32 pb-20">
        <div class="container mx-auto px-6">
            <a href="index.php"
                class="inline-flex items-center gap-2 text-gray-500 hover:text-primary transition-colors mb-8 text-sm font-en">
                <i class="fas fa-arrow-left"></i> BACK TO STORE
            </a>

            <div class="flex flex-col lg:flex-row gap-12 items-start">
                <!-- Image Section -->
                <div class="lg:w-1/2 lg:sticky lg:top-32 lg:self-start">
                    <div
                        class="bg-white/5 rounded-sm p-4 border border-white/10 mb-4 h-[60vh] flex items-center justify-center">
                        <img id="mainImage" src="<?php echo htmlspecialchars($main_image); ?>"
                            class="w-full h-full object-contain rounded-sm transition-opacity duration-300">
                    </div>
                    <div class="grid grid-cols-4 gap-2">
                        <?php foreach ($images as $img): ?>
                            <?php $info_img = adjustPath($img); ?>
                            <button onclick="changeMainImage('<?php echo htmlspecialchars($info_img); ?>')"
                                class="border border-white/10 hover:border-primary rounded-sm overflow-hidden transition-colors aspect-video">
                                <img src="<?php echo htmlspecialchars($info_img); ?>" class="w-full h-full object-cover">
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Info Section (Simplified) -->
                <div class="lg:w-1/2">
                    <h1 class="text-2xl md:text-3xl font-bold mb-4 leading-relaxed">
                        <?php echo nl2br(htmlspecialchars($product['name'])); ?>
                    </h1>
                    <div class="text-2xl font-en font-bold text-primary mb-2">
                        ¥<?php echo number_format($product['price']); ?> <span
                            class="text-sm text-gray-400 font-normal">税込</span></div>
                    <div class="text-sm text-gray-400 mb-8">
                        送料が別途¥<?php echo number_format($product['shipping_fee']); ?>かかります。</div>
                </div>
            </div>

            <!-- Description & Options Area (Full Width below) -->
            <div class="max-w-4xl mx-auto mt-16">
                
                <!-- Detailed Description -->
                <div class="space-y-6 mb-12 text-sm md:text-base leading-relaxed text-gray-300">
                    <?php
                    // 説明文を整形して表示
                    $desc = $product['description'];
                    
                    // 【】で囲まれた見出しをスタイリング
                    $desc = preg_replace(
                        '/【([^】]+)】/',
                        '</div><div class="bg-secondary p-5 rounded-sm border border-white/10 mt-6"><h3 class="font-bold text-white mb-3 text-base border-l-2 border-primary pl-3">$1</h3><div class="text-gray-400 text-sm leading-loose">',
                        $desc
                    );
                    
                    // ☆で始まる行をサブ見出しとして整形
                    $desc = preg_replace(
                        '/☆([^\n<]+)/',
                        '<p class="mt-4 mb-2"><span class="text-primary font-bold">☆$1</span></p>',
                        $desc
                    );
                    
                    // ・で始まる行をリストアイテムとして整形
                    $desc = preg_replace(
                        '/・([^\n<]+)/',
                        '<li class="ml-4 text-gray-400">・$1</li>',
                        $desc
                    );
                    
                    // 改行を<br>に変換
                    $desc = nl2br($desc);
                    
                    // 不要な空のdivを削除
                    $desc = preg_replace('/<div class="bg-secondary[^>]*>\s*<\/div>/', '', $desc);
                    
                    echo $desc;
                    ?>
                    </div>
                    <!-- Models -->
                    <?php if ($product['compatible_models']): ?>
                        <div class="bg-secondary p-4 rounded-sm border border-white/5">
                            <p class="mb-2"><span
                                    class="text-primary font-bold">《適合車種》</span><?php echo htmlspecialchars($product['compatible_models']); ?>
                            </p>
                            <p><span
                                    class="text-primary font-bold">《車両型式》</span><?php echo htmlspecialchars($product['model_code']); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Option Detail Image (New Feature) -->
                <?php 
                // Adjust path for checking
                $detail_img = $product['option_detail_image'] ?? '';
                if (!empty($detail_img)) {
                     // Check if path starts with assets/ (older data might need fix)
                     // But our admin code saves with ../assets if uploaded there.
                     // The adjustPath function handles assets/ prefix.
                     $detail_img = adjustPath($detail_img);
                }
                ?>
                <?php if (!empty($detail_img)): ?>
                    <div class="mb-12">
                         <img src="<?php echo htmlspecialchars($detail_img); ?>" class="w-full h-auto rounded-sm border border-white/10" alt="Option Details">
                    </div>
                <?php endif; ?>

                <!-- Parts Detail Section (Test Implementation) -->
                <div class="mb-16">
                    <div class="text-center mb-8">
                        <h3 class="text-lg font-bold font-en tracking-widest text-primary border-b border-primary/30 pb-2 inline-block">PARTS DETAIL</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Test Image 1 -->
                        <div class="bg-white/5 p-2 rounded-sm border border-white/10 group overflow-hidden">
                            <div class="overflow-hidden rounded-sm h-64">
                                <img src="../assets/images/hero.png" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="Parts Detail 1">
                            </div>
                            <p class="text-xs text-gray-400 mt-3 text-center">パーツ詳細画像エリア 1</p>
                        </div>
                        
                        <!-- Test Image 2 -->
                        <div class="bg-white/5 p-2 rounded-sm border border-white/10 group overflow-hidden">
                            <div class="overflow-hidden rounded-sm h-64">
                                <img src="../assets/images/hero.png" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="Parts Detail 2">
                            </div>
                            <p class="text-xs text-gray-400 mt-3 text-center">パーツ詳細画像エリア 2</p>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Options Form -->
                <div class="bg-white/5 p-6 md:p-10 rounded-sm border border-white/10">
                    <h3 class="text-lg font-bold font-en tracking-widest mb-8 text-center text-primary">SELECT OPTIONS</h3>
                    
                    <form class="space-y-8" id="orderForm">
                        <?php if (is_array($options)): ?>
                            <?php foreach ($options as $idx => $opt): ?>
                                <div>
                                    <label
                                        class="block text-sm font-bold font-en tracking-widest text-gray-400 mb-3"><?php echo htmlspecialchars($opt['label']); ?></label>
                                    <div class="flex items-center gap-6">
                                        <div class="flex-1">
                                            <select name="option_<?php echo $idx; ?>"
                                                data-label="<?php echo htmlspecialchars($opt['label']); ?>"
                                                data-group-index="<?php echo $idx; ?>"
                                                class="option-select w-full bg-black border border-white/20 rounded-sm px-6 py-4 focus:outline-none focus:border-primary text-base transition-colors"
                                                onchange="onOptionChange(this)">
                                                <?php
                                                $choices = $opt['choices'] ?? [];
                                                foreach ($choices as $cIdx => $choice):
                                                    $val = '';
                                                    $txt = '';
                                                    $img = '';
                                                    if (is_array($choice)) {
                                                        $val = $choice['value'];
                                                        $txt = $choice['label'];
                                                        $img = $choice['image'] ?? '';
                                                    } else {
                                                        $val = $choice;
                                                        $txt = $choice;
                                                    }
                                                    if (!empty($img)) {
                                                        $img = adjustPath($img);
                                                    }
                                                    ?>
                                                    <option value="<?php echo htmlspecialchars($val); ?>"
                                                        data-image="<?php echo htmlspecialchars($img); ?>">
                                                        <?php echo htmlspecialchars($txt); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <!-- Dynamic Image Display Area -->
                                        <div id="option-image-<?php echo $idx; ?>"
                                            class="w-20 h-20 bg-white/5 rounded-sm border border-white/10 overflow-hidden flex-shrink-0 hidden">
                                            <img src="" class="w-full h-full object-cover">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <div class="pt-8 border-t border-white/10 mt-8">
                            <button type="button" onclick="addToCartDynamic()"
                                class="w-full md:w-2/3 mx-auto block bg-primary text-black font-bold py-5 rounded-sm hover:bg-white hover:text-black transition-colors font-en tracking-widest text-lg shadow-[0_0_20px_rgba(255,215,0,0.3)]">
                                ADD TO CART
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black border-t border-white/10 pt-10 pb-10 text-center">
        <p class="text-xs text-gray-600 font-en">© 2025 GIKO. All Rights Reserved.</p>
    </footer>

    <script src="../assets/js/main.js"></script>
    <script>
        function changeMainImage(src) {
            const mainImage = document.getElementById('mainImage');
            mainImage.style.opacity = '0';
            setTimeout(() => {
                mainImage.src = src;
                mainImage.style.opacity = '1';
            }, 150);
        }

        // Initialize options on load
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.option-select').forEach(select => {
                onOptionChange(select);
            });
        });

        function onOptionChange(selectEl) {
            const idx = selectEl.getAttribute('data-group-index');
            const selectedOption = selectEl.options[selectEl.selectedIndex];
            const imgSrc = selectedOption.getAttribute('data-image');

            // 1. Update Option Review Image (next to select box)
            const imgContainer = document.getElementById(`option-image-${idx}`);
            const imgEl = imgContainer.querySelector('img');

            if (imgSrc) {
                imgEl.src = imgSrc;
                imgContainer.classList.remove('hidden');

                // 2. Also update Main Image? 
                // Requirement: "色の名前と画像を紐づける為に画像フィールド追加して画像も動的に出力させたい"
                // Usually this means updating the main product view too.
            } else {
                imgContainer.classList.add('hidden');
            }
        }

        function addToCartDynamic() {
            const productName = <?php echo json_encode($product['name']); ?>;
            const price = <?php echo json_encode($product['price']); ?>;
            const image = <?php echo json_encode($main_image); ?>;
            const id = 'product_' + <?php echo $product['id']; ?>;

            const form = document.getElementById('orderForm');
            const selects = form.querySelectorAll('select');
            const options = {};

            selects.forEach(select => {
                const label = select.getAttribute('data-label');
                const val = select.options[select.selectedIndex].text; // Store human readable text
                options[label] = val;
            });

            Cart.addItem({
                id: id,
                name: productName,
                price: price,
                image: image,
                options: options
            });

            window.location.href = 'cart.html';
        }
    </script>
</body>

</html>

