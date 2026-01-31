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

$images = json_decode($product['images'] ?? '[]', true);
$options = json_decode($product['options'] ?? '[]', true);
$main_image = $images[0] ?? '../assets/images/no_image.png';

// Swatch Config (Hardcoded for now as it relies on specific file naming convetions)
$swatches = [
    'sunset_brown' => '../assets/images/items/swatch_sunset_brown.png',
    'black_shibo' => '../assets/images/items/swatch_black_shibo.png',
    'black_smooth' => '../assets/images/items/swatch_black_smooth.png',
    'black' => '../assets/images/items/swatch_black_shibo.png' // Default black
];

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
    <!-- Header (Simplified) -->
    <header class="fixed w-full z-50 transition-all duration-300 bg-black/80 backdrop-blur-md border-b border-white/5">
        <div class="container mx-auto px-6 h-20 flex justify-between items-center">
            <a href="../index.html" class="flex items-center gap-3">
                <div
                    class="w-8 h-8 bg-primary rounded-sm flex items-center justify-center text-black font-bold font-en text-lg">
                    G</div>
                <span class="text-xl font-bold tracking-widest font-en">GIKO</span>
            </a>
            <nav class="hidden lg:flex space-x-10 text-xs font-bold tracking-widest">
                <a href="../index.html" class="hover:text-primary transition-colors font-en">HOME</a>
                <a href="index.php" class="text-primary font-en">STORE</a>
            </nav>
            <a href="cart.html" class="flex ml-6 relative group">
                <i class="fas fa-shopping-cart text-white text-lg group-hover:text-primary transition-colors"></i>
                <span id="cart-badge-desktop"
                    class="cart-badge absolute -top-2 -right-2 bg-primary text-black text-[10px] font-bold px-1.5 rounded-full hidden">0</span>
            </a>
        </div>
    </header>

    <section class="min-h-screen pt-32 pb-20">
        <div class="container mx-auto px-6">
            <a href="index.php"
                class="inline-flex items-center gap-2 text-gray-500 hover:text-primary transition-colors mb-8 text-sm font-en">
                <i class="fas fa-arrow-left"></i> BACK TO STORE
            </a>

            <div class="flex flex-col lg:flex-row gap-12">
                <!-- Image Section -->
                <div class="lg:w-1/2 lg:sticky lg:top-32 lg:self-start">
                    <div class="bg-white/5 rounded-sm p-4 border border-white/10 mb-4">
                        <img id="mainImage" src="<?php echo htmlspecialchars($main_image); ?>"
                            class="w-full h-auto object-cover rounded-sm transition-opacity duration-300">
                    </div>
                    <div class="grid grid-cols-4 gap-2">
                        <?php foreach ($images as $img): ?>
                            <button onclick="changeMainImage('<?php echo htmlspecialchars($img); ?>')"
                                class="border border-white/10 hover:border-primary rounded-sm overflow-hidden transition-colors aspect-video">
                                <img src="<?php echo htmlspecialchars($img); ?>" class="w-full h-full object-cover">
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Info Section -->
                <div class="lg:w-1/2">
                    <h1 class="text-2xl md:text-3xl font-bold mb-4 leading-relaxed">
                        <?php echo nl2br(htmlspecialchars($product['name'])); ?>
                    </h1>
                    <div class="text-2xl font-en font-bold text-primary mb-2">
                        ¥<?php echo number_format($product['price']); ?> <span
                            class="text-sm text-gray-400 font-normal">税込</span></div>
                    <div class="text-sm text-gray-400 mb-8">
                        送料が別途¥<?php echo number_format($product['shipping_fee']); ?>かかります。</div>

                    <div class="space-y-6 mb-10 text-sm md:text-base leading-relaxed text-gray-300">
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
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

                    <!-- Dynamic Options Form -->
                    <form class="space-y-6 border-t border-white/10 pt-8 mb-12" id="orderForm">
                        <?php if (is_array($options)): ?>
                            <?php foreach ($options as $idx => $opt): ?>
                                <div>
                                    <label
                                        class="block text-xs font-bold font-en tracking-widest text-gray-500 mb-2"><?php echo htmlspecialchars($opt['label']); ?></label>
                                    <div class="flex items-end gap-4">
                                        <div class="flex-1">
                                            <?php if ($opt['type'] === 'select'): ?>
                                                <select name="option_<?php echo $idx; ?>"
                                                    data-label="<?php echo htmlspecialchars($opt['label']); ?>"
                                                    class="w-full bg-secondary border border-white/10 rounded-sm px-4 py-3 focus:outline-none focus:border-primary text-sm transition-colors"
                                                    <?php if (strpos($opt['label'], 'カラー') !== false): ?>
                                                        onchange="updateSwatch(this)" <?php endif; ?>>
                                                    <?php foreach ($opt['choices'] as $val => $txt):
                                                        // Handle array choices (value=index or value=text) vs assoc array
                                                        // Our migration uses assoc for colors, indexed for others.
                                                        $optVal = is_string($val) ? $val : $txt;
                                                        $optTxt = $txt;
                                                        ?>
                                                        <option value="<?php echo htmlspecialchars($optVal); ?>">
                                                            <?php echo htmlspecialchars($optTxt); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </div>
                                        <!-- Swatch placeholder if needed -->
                                        <?php if (strpos($opt['label'], 'カラー') !== false): ?>
                                            <div
                                                class="w-32 h-24 bg-white/5 rounded-sm border border-white/10 overflow-hidden mb-1 flex-shrink-0">
                                                <img src="../assets/images/items/swatch_sunset_brown.png"
                                                    class="w-full h-full object-cover swatch-img">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <button type="button" onclick="addToCartDynamic()"
                            class="w-full bg-primary text-white font-bold py-4 rounded-sm hover:opacity-90 transition-opacity font-en tracking-widest mt-4">
                            ADD TO CART
                        </button>
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

        const swatches = <?php echo json_encode($swatches); ?>;

        function updateSwatch(selectEl) {
            // Find sibling swatch img
            const container = selectEl.closest('.flex');
            if (!container) return;
            const img = container.querySelector('.swatch-img');
            if (!img) return;

            const val = selectEl.value;
            if (swatches[val]) {
                img.src = swatches[val];
            } else {
                img.src = '../assets/images/items/swatch_sunset_brown.png'; // Fallback
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