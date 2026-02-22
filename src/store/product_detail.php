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
function adjustPath($path)
{
    if (empty($path))
        return '';
    if (strpos($path, '../assets') === 0)
        return $path;
    if (strpos($path, 'assets/') === 0)
        return '../' . $path;
    return $path;
}

$images = json_decode($product['images'] ?? '[]', true);
$options = json_decode($product['options'] ?? '[]', true);

$main_image = !empty($images[0]) ? adjustPath($images[0]) : '../assets/images/no_image.png';

$default_swatch = '../assets/images/no_image.png';
?>
<!DOCTYPE html>
<html lang="ja" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> | 技巧 -Giko-</title>
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
                <img src="../assets/images/logo_new.png" alt="GIKO"
                    class="h-10 group-hover:opacity-80 transition-opacity">
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
                <button id="lang-toggle-mobile"
                    class="mt-8 flex items-center justify-center gap-4 text-sm font-bold font-en tracking-widest">
                    <span class="text-primary">JP</span>
                    <span class="text-white/30">/</span>
                    <span class="text-white">EN</span>
                </button>
            </nav>
        </div>
    </header>

    <section class="min-h-screen pt-32 pb-20">
        <div class="container mx-auto px-6">
            <a href="index.php"
                class="inline-flex items-center gap-2 text-gray-500 hover:text-primary transition-colors mb-8 text-sm font-en">
                <i class="fas fa-arrow-left"></i> BACK TO STORE
            </a>

            <div class="flex flex-col lg:flex-row gap-12 items-start">
                <!-- 左カラム: 画像セクション（sticky） -->
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

                <!-- 右カラム: 商品情報（スクロール可能） -->
                <div class="lg:w-1/2 space-y-8">
                    <!-- 商品名・価格 -->
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold mb-4 leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($product['name'])); ?>
                        </h1>
                        <div class="text-2xl font-en font-bold text-primary mb-2">
                            ¥<?php echo number_format($product['price']); ?> <span
                                class="text-sm text-gray-400 font-normal">税込</span></div>
                        <div class="text-sm text-gray-400">
                            送料が別途¥<?php echo number_format($product['shipping_fee']); ?>かかります。</div>
                    </div>

                    <!-- PRODUCT DETAILS -->
                    <div class="border-t border-white/10 pt-8">
                        <h3 class="text-sm font-bold font-en tracking-widest text-primary mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle"></i> PRODUCT DETAILS
                        </h3>

                        <!-- リード文 -->
                        <?php if (!empty($product['lead_text'])): ?>
                            <div class="text-sm leading-relaxed text-gray-300 mb-6">
                                <?php echo nl2br(htmlspecialchars($product['lead_text'])); ?>
                            </div>
                        <?php endif; ?>

                        <!-- 商品概要（リスト形式） -->
                        <?php
                        $product_summary = json_decode($product['product_summary_json'] ?? '[]', true);
                        if (!empty($product_summary) && is_array($product_summary)):
                            ?>
                            <div class="bg-secondary p-5 rounded-sm border border-white/10 mb-6">
                                <h4 class="font-bold text-white mb-4 text-base border-l-2 border-primary pl-3">商品概要</h4>
                                <div class="space-y-4">
                                    <?php foreach ($product_summary as $item): ?>
                                        <?php if (!empty($item['title']) || !empty($item['text'])): ?>
                                            <div class="text-sm">
                                                <?php if (!empty($item['title'])): ?>
                                                    <p class="text-primary font-bold mb-1">
                                                        <?php echo htmlspecialchars($item['title']); ?></p>
                                                <?php endif; ?>
                                                <?php if (!empty($item['text'])): ?>
                                                    <p class="text-gray-400 leading-relaxed">
                                                        <?php echo nl2br(htmlspecialchars($item['text'])); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 車両情報 -->
                    <?php if (!empty($product['compatible_models']) || !empty($product['vehicle_type'])): ?>
                        <div class="bg-secondary p-4 rounded-sm border border-white/5">
                            <?php if (!empty($product['compatible_models'])): ?>
                                <p class="mb-2">
                                    <span class="text-primary font-bold">《適合車種》</span>
                                    <?php echo htmlspecialchars($product['compatible_models']); ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($product['vehicle_type'])): ?>
                                <p>
                                    <span class="text-primary font-bold">《車両型式》</span>
                                    <?php echo htmlspecialchars($product['vehicle_type']); ?>
                                </p>
                            <?php elseif (!empty($product['model_code'])): ?>
                                <!-- 互換性のため旧フィールドも確認 -->
                                <p>
                                    <span class="text-primary font-bold">《車両型式》</span>
                                    <?php echo htmlspecialchars($product['model_code']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- 詳細画像 (削除済み) -->

                    <!-- オプション詳細画像 (PARTS DETAILへ統合) -->
                    <?php
                    $option_detail_img = $product['option_detail_image'] ?? '';
                    if (!empty($option_detail_img)) {
                        $option_detail_img = adjustPath($option_detail_img);
                    }
                    ?>
                    <?php if (!empty($option_detail_img) && false): // Hide standalone display ?>
                        <div>
                            <img src="<?php echo htmlspecialchars($option_detail_img); ?>"
                                class="w-full h-auto rounded-sm border border-white/10" alt="Option Details">
                        </div>
                    <?php endif; ?>

                    <!-- パーツ詳細セクション (PARTS DETAIL) -->
                    <?php if (!empty($option_detail_img)): ?>
                        <div>
                            <div class="text-center mb-6">
                                <h3
                                    class="text-sm font-bold font-en tracking-widest text-primary border-b border-primary/30 pb-2 inline-block">
                                    PARTS DETAIL</h3>
                            </div>
                            <div class="bg-white/5 p-2 rounded-sm border border-white/10 group overflow-hidden">
                                <div class="overflow-hidden rounded-sm">
                                    <img src="<?php echo htmlspecialchars($option_detail_img); ?>"
                                        class="w-full h-auto object-cover transition-transform duration-500 group-hover:scale-105"
                                        alt="Parts Detail">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- オプション選択フォーム -->
                    <div class="bg-white/5 p-6 rounded-sm border border-white/10">
                        <h3 class="text-sm font-bold font-en tracking-widest mb-6 text-center text-primary">SELECT
                            OPTIONS</h3>

                        <form class="space-y-6" id="orderForm">
                            <?php if (is_array($options)): ?>
                                <?php foreach ($options as $idx => $opt): ?>
                                    <div>
                                        <label
                                            class="block text-sm font-bold font-en tracking-widest text-gray-400 mb-3"><?php echo htmlspecialchars($opt['label']); ?></label>
                                        <div class="flex items-center gap-4">
                                            <div class="flex-1">
                                                <select name="option_<?php echo $idx; ?>"
                                                    data-label="<?php echo htmlspecialchars($opt['label']); ?>"
                                                    data-group-index="<?php echo $idx; ?>"
                                                    class="option-select w-full bg-black border border-white/20 rounded-sm px-4 py-3 focus:outline-none focus:border-primary text-sm transition-colors"
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
                                            <!-- オプション画像プレビュー -->
                                            <div id="option-image-<?php echo $idx; ?>"
                                                class="w-16 h-16 bg-white/5 rounded-sm border border-white/10 overflow-hidden flex-shrink-0 hidden">
                                                <img src="" class="w-full h-full object-cover">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- 下取り（買取）オプション -->
                            <?php
                            $trade_in_discount = (int)($product['trade_in_discount'] ?? 10000);
                            if ($trade_in_discount > 0):
                            ?>
                            <div class="bg-white/5 border border-white/10 rounded-sm p-4">
                                <label class="block text-sm font-bold tracking-widest text-gray-300 mb-1">お車から外した元パーツについて</label>
                                <p class="text-xs text-primary font-bold mb-3">割引金額：¥<?php echo number_format($trade_in_discount); ?></p>
                                <div class="space-y-2">
                                    <label class="flex items-center gap-3 cursor-pointer p-3 rounded-sm border border-primary/50 bg-primary/10 hover:bg-primary/20 transition-colors">
                                        <input type="radio" name="option_trade_in" data-label="下取り交換" value="あり" class="accent-primary w-4 h-4" checked>
                                        <span class="text-sm text-primary font-bold">技巧に買取を依頼（割引あり）</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer p-3 rounded-sm border border-white/10 hover:border-white/30 transition-colors">
                                        <input type="radio" name="option_trade_in" data-label="下取り交換" value="なし" class="accent-primary w-4 h-4">
                                        <span class="text-sm text-gray-400">買取依頼しない（割引なし／手元に保管）</span>
                                    </label>
                                </div>
                                <p class="text-[11px] text-gray-500 mt-3 leading-relaxed">
                                    <i class="fas fa-info-circle text-primary mr-1"></i>
                                    張り替え済みパーツをお届け後、取り外した旧パーツをご返送いただくと割引になります。旧パーツを手元に残したい場合は"買取依頼しない"を選択してください。
                                </p>
                            </div>
                            <?php else: ?>
                            <input type="hidden" name="option_trade_in" data-label="下取り交換" value="なし">
                            <?php endif; ?>

                            <!-- 送料案内 -->
                            <div class="pt-4 border-t border-white/10 mt-4">
                                <div class="flex items-center gap-2 text-xs text-gray-400">
                                    <i class="fas fa-truck text-primary"></i>
                                    <span>送料: <strong class="text-white">¥1,000</strong>（銀行振込は<span
                                            class="text-green-400 font-bold">送料無料</span>）</span>
                                </div>
                            </div>

                            <div class="pt-6 border-t border-white/10 mt-6">
                                <button type="button" onclick="addToCartDynamic()"
                                    class="w-full bg-primary text-black font-bold py-4 rounded-sm hover:bg-white hover:text-black transition-colors font-en tracking-widest text-base shadow-[0_0_20px_rgba(255,215,0,0.3)]">
                                    ADD TO CART
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-secondary pt-24 pb-12 border-t border-white/5 text-white">
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
                        <li><a href="../pages/before_after.html" class="hover:text-white transition-colors">BEFORE &
                                AFTER</a></li>
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

        // 初期ロード時にオプション画像を設定
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.option-select').forEach(select => {
                onOptionChange(select);
            });
        });

        function onOptionChange(selectEl) {
            const idx = selectEl.getAttribute('data-group-index');
            const selectedOption = selectEl.options[selectEl.selectedIndex];
            const imgSrc = selectedOption.getAttribute('data-image');

            const imgContainer = document.getElementById(`option-image-${idx}`);
            const imgEl = imgContainer.querySelector('img');

            if (imgSrc) {
                imgEl.src = imgSrc;
                imgContainer.classList.remove('hidden');
            } else {
                imgContainer.classList.add('hidden');
            }
        }

        function addToCartDynamic() {
            const productName = <?php echo json_encode($product['name']); ?>;
            const price = <?php echo json_encode($product['price']); ?>;
            const image = <?php echo json_encode($main_image); ?>;
            const id = 'product_' + <?php echo $product['id']; ?>;
            const tradeInDiscount = <?php echo (int)($product['trade_in_discount'] ?? 10000); ?>;

            const form = document.getElementById('orderForm');
            const selects = form.querySelectorAll('select');
            const options = {};

            selects.forEach(select => {
                const label = select.getAttribute('data-label');
                const val = select.options[select.selectedIndex].text;
                if (label) {
                    options[label] = val;
                }
            });

            // Radio buttons (for trade-in)
            const radios = form.querySelectorAll('input[type="radio"]:checked');
            radios.forEach(radio => {
                const label = radio.getAttribute('data-label');
                if (label) {
                    options[label] = radio.value;
                }
            });

            // Hidden inputs with data-label (for hidden trade-in when discount=0)
            const hiddens = form.querySelectorAll('input[type="hidden"][data-label]');
            hiddens.forEach(hidden => {
                const label = hidden.getAttribute('data-label');
                if (label) {
                    options[label] = hidden.value;
                }
            });

            Cart.addItem({
                id: id,
                name: productName,
                price: price,
                image: image,
                options: options,
                tradeInDiscount: tradeInDiscount
            });

            window.location.href = 'cart.html';
        }
    </script>
</body>

</html>