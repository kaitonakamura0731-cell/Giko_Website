<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
checkAuth();

$id = $_GET['id'] ?? null;
$copy_from = $_GET['copy_from'] ?? null;
$product = null;
$error = '';
$success = '';

// Default data
$default_product = [
    'name' => '',
    'price' => 0,
    'shipping_fee' => 1000,
    'short_description' => '',
    'lead_text' => '',
    'product_summary_json' => '[]',
    'compatible_models' => '',
    'model_code' => '',
    'vehicle_type' => '',
    'detail_image_path' => '',
    'images' => '[]',
    'options' => '[]',
    'option_detail_image' => '',
    'stock_status' => 1,
    'vehicle_tags' => '',
    'trade_in_discount' => 10000
];

if ($id) {
    // Edit mode
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if (!$product) {
            header("Location: index.php");
            exit;
        }
    } catch (PDOException $e) {
        $error = "DB Error: " . $e->getMessage();
    }
} elseif ($copy_from) {
    // Copy mode - load data from existing product
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$copy_from]);
        $source_product = $stmt->fetch();
        if ($source_product) {
            $product = $source_product;
            $product['id'] = null; // Clear ID for new product
            $product['name'] = $source_product['name'] . ' (コピー)';
            // Clear images to avoid duplication (user can re-upload if needed)
            // Or keep them if you want to copy images too
            $success = "商品データをコピーしました。必要に応じて編集してください。";
        } else {
            $product = $default_product;
            $error = "コピー元の商品が見つかりませんでした。";
        }
    } catch (PDOException $e) {
        $product = $default_product;
        $error = "DB Error: " . $e->getMessage();
    }
} else {
    // New mode
    $product = $default_product;
}

require_once '../includes/upload_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic Sanitation
    $name = trim($_POST['name']);
    $price = (int) $_POST['price'];
    $shipping_fee = (int) $_POST['shipping_fee'];
    $short_description = $_POST['short_description'] ?? '';

    // New Extended Fields
    $lead_text = $_POST['lead_text'] ?? '';
    $compatible_models = $_POST['compatible_models'] ?? '';
    $model_code = $_POST['model_code'] ?? '';
    $vehicle_type = $_POST['vehicle_type'] ?? '';

    // Product Summary JSON (repeatable fields: title + text)
    $product_summary = [];
    if (isset($_POST['summary_items']) && is_array($_POST['summary_items'])) {
        foreach ($_POST['summary_items'] as $item) {
            $title = trim($item['title'] ?? '');
            $text = trim($item['text'] ?? '');
            if (!empty($title) || !empty($text)) {
                $product_summary[] = [
                    'title' => $title,
                    'text' => $text
                ];
            }
        }
    }
    $product_summary_json = json_encode($product_summary, JSON_UNESCAPED_UNICODE);

    // Detail Image Upload
    $detail_image_path = $_POST['detail_image_current'] ?? '';
    $uploaded_detail_img = handleUpload('detail_image_file', '../../assets/images/uploads/');
    if ($uploaded_detail_img) {
        $detail_image_path = str_replace('../../assets', '../assets', $uploaded_detail_img);
    }

    // Feature Image for Options
    $option_detail_image = $_POST['option_detail_image_current'] ?? '';
    // Handle Upload
    $uploaded_opt_img = handleUpload('option_detail_image_file', '../../assets/images/uploads/');
    if ($uploaded_opt_img) {
        $option_detail_image = str_replace('../../assets', '../assets', $uploaded_opt_img);
    }
    $stock_status = (int) ($_POST['stock_status'] ?? 1);
    $vehicle_tags = trim($_POST['vehicle_tags'] ?? '');
    $trade_in_discount = (int) ($_POST['trade_in_discount'] ?? 10000);

    // Images (Lines to JSON)
    $images_raw = $_POST['images'] ?? '';
    $images_lines = preg_split('/\r\n|\r|\n/', $images_raw);
    $images_clean = [];
    foreach ($images_lines as $line) {
        $line = trim($line);
        if ($line) {
            $images_clean[] = $line;
        }
    }

    // Handle New Image Upload (Multiple)
    if (isset($_FILES['new_image_files']) && is_array($_FILES['new_image_files']['name'])) {
        $upload_dir = '../../assets/images/uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $new_uploaded = [];
        for ($fi = 0; $fi < count($_FILES['new_image_files']['name']); $fi++) {
            if ($_FILES['new_image_files']['error'][$fi] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['new_image_files']['tmp_name'][$fi];
                $fname = $_FILES['new_image_files']['name'][$fi];
                $ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
                $new_name = time() . '_' . uniqid() . '.' . $ext;
                $dest = $upload_dir . $new_name;
                if (move_uploaded_file($tmp, $dest)) {
                    $db_path = '../assets/images/uploads/' . $new_name;
                    $new_uploaded[] = $db_path;
                }
            }
        }
        // Add new images to top of list
        $images_clean = array_merge($new_uploaded, $images_clean);
    }

    $images_json = json_encode($images_clean, JSON_UNESCAPED_UNICODE);

    // Options Builder Logic (New Structure)
    // Structure: $_POST['options'][index] => {label, type, choices: [{label, value, image_current, ...}]}
    // Files: $_FILES['options']['name'][index]['choices'][cIndex]['image_file'] ...

    $options_arr = [];
    $posted_options = $_POST['options'] ?? [];

    if (is_array($posted_options)) {
        foreach ($posted_options as $i => $opt) {
            $label = trim($opt['label'] ?? '');
            if (!$label)
                continue;

            $type = $opt['type'] ?? 'select';
            $choices = [];

            if (isset($opt['choices']) && is_array($opt['choices'])) {
                foreach ($opt['choices'] as $j => $choice) {
                    $c_label = trim($choice['label'] ?? '');
                    $c_value = trim($choice['value'] ?? '');
                    // If value is empty, use label
                    if ($c_value === '')
                        $c_value = $c_label;

                    if ($c_label === '' && $c_value === '')
                        continue;

                    $image_path = $choice['image_current'] ?? '';

                    // Handle File Upload for this choice
                    // $_FILES['options'] is structured weirdly: ['name'][i]['choices'][j]['image_file']
                    if (
                        isset($_FILES['options']['name'][$i]['choices'][$j]['image_file']) &&
                        $_FILES['options']['error'][$i]['choices'][$j]['image_file'] === UPLOAD_ERR_OK
                    ) {

                        $tmp_name = $_FILES['options']['tmp_name'][$i]['choices'][$j]['image_file'];
                        $name = $_FILES['options']['name'][$i]['choices'][$j]['image_file'];
                        $upload_dir = '../../assets/images/uploads/';

                        // Ensure dir exists
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }

                        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        $new_filename = uniqid('opt_') . '.' . $ext;
                        $dest = $upload_dir . $new_filename;

                        if (move_uploaded_file($tmp_name, $dest)) {
                            $image_path = '../assets/images/uploads/' . $new_filename;
                        }
                    }

                    $choices[] = [
                        'label' => $c_label,
                        'value' => $c_value,
                        'image' => $image_path
                    ];
                }
            }

            $options_arr[] = [
                'label' => $label,
                'type' => $type,
                'choices' => $choices
            ];
        }
    }
    $options_json = json_encode($options_arr, JSON_UNESCAPED_UNICODE);

    try {
        if ($id) {
            // Update
            $sql = "UPDATE products SET name=?, price=?, shipping_fee=?, short_description=?, lead_text=?, product_summary_json=?, compatible_models=?, model_code=?, vehicle_type=?, detail_image_path=?, images=?, options=?, option_detail_image=?, stock_status=?, vehicle_tags=?, trade_in_discount=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $price, $shipping_fee, $short_description, $lead_text, $product_summary_json, $compatible_models, $model_code, $vehicle_type, $detail_image_path, $images_json, $options_json, $option_detail_image, $stock_status, $vehicle_tags, $trade_in_discount, $id]);
            $success = "商品情報を更新しました。";
        } else {
            // Insert
            $sql = "INSERT INTO products (name, price, shipping_fee, short_description, lead_text, product_summary_json, compatible_models, model_code, vehicle_type, detail_image_path, images, options, option_detail_image, stock_status, vehicle_tags, trade_in_discount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $price, $shipping_fee, $short_description, $lead_text, $product_summary_json, $compatible_models, $model_code, $vehicle_type, $detail_image_path, $images_json, $options_json, $option_detail_image, $stock_status, $vehicle_tags, $trade_in_discount]);
            $id = $pdo->lastInsertId();
            $success = "商品を新規作成しました。";
            header("Location: edit.php?id=" . $id . "&created=1");
            exit;
        }

        // Reload
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

require_once '../includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold font-en tracking-widest text-white">
            <?php echo $id ? 'EDIT PRODUCT' : 'NEW PRODUCT'; ?>
        </h1>
        <div class="flex gap-3">
            <?php if ($id): ?>
                <a href="edit.php?copy_from=<?php echo $id; ?>"
                   class="bg-gray-700 hover:bg-gray-600 text-white text-sm px-4 py-2 rounded transition-colors">
                    <i class="fas fa-copy mr-1"></i> この商品をコピーして新規作成
                </a>
            <?php endif; ?>
            <a href="index.php" class="text-gray-400 hover:text-white transition-colors text-sm">
                <i class="fas fa-arrow-left mr-1"></i> 一覧に戻る
            </a>
        </div>
    </div>

    <?php if ($success || isset($_GET['created'])): ?>
        <div class="bg-green-900 border border-green-700 text-green-300 px-4 py-3 rounded mb-6">
            保存しました。
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded mb-6">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="admin-card" enctype="multipart/form-data">
        <div class="admin-card-body space-y-8">

            <!-- General Info -->
            <div>
                <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-700 pb-2">基本情報</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="form-label">商品名 (Name)</label>
                        <input type="text" name="name" id="product-name-input" class="form-input product-name-field"
                            value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">価格 (Price)</label>
                        <input type="number" name="price" class="form-input"
                            value="<?php echo htmlspecialchars($product['price']); ?>" required min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">送料 (Shipping Fee)</label>
                        <input type="number" name="shipping_fee" class="form-input"
                            value="<?php echo htmlspecialchars($product['shipping_fee']); ?>" required min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">下取り割引金額 (Trade-in Discount)</label>
                        <input type="number" name="trade_in_discount" class="form-input"
                            value="<?php echo htmlspecialchars($product['trade_in_discount'] ?? 10000); ?>" min="0">
                        <p class="text-xs text-gray-500 mt-1">※買取依頼（下取り）時に適用される割引金額（円）。0で下取りオプション非表示。</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">在庫状況 (Stock Status)</label>
                        <select name="stock_status" class="form-input">
                            <option value="1" <?php echo ($product['stock_status'] == 1) ? 'selected' : ''; ?>>在庫あり
                            </option>
                            <option value="0" <?php echo ($product['stock_status'] == 0) ? 'selected' : ''; ?>>在庫なし
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div>
                <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-700 pb-2">商品詳細情報</h3>
                <div class="space-y-6">
                    <div class="form-group">
                        <label class="form-label">一覧用説明文 (List Short Description)</label>
                        <textarea name="short_description" class="form-input h-20"
                            placeholder="一覧ページに表示される短い説明文"><?php echo htmlspecialchars($product['short_description'] ?? ''); ?></textarea>
                    </div>

                    <!-- PRODUCT DETAILS Section -->
                    <div class="bg-gray-900 p-6 rounded border border-gray-700">
                        <h4 class="text-sm font-bold text-primary mb-4 font-en tracking-widest">PRODUCT DETAILS</h4>
                        <div class="form-group">
                            <label class="form-label">リード文 (Lead Text)</label>
                            <textarea name="lead_text" class="form-input h-32"
                                placeholder="商品タイトル直下に表示されるリード文"><?php echo htmlspecialchars($product['lead_text'] ?? ''); ?></textarea>
                            <p class="text-xs text-gray-500 mt-1">※商品の概要を簡潔に説明する文章を入力してください。</p>
                        </div>
                    </div>

                    <!-- 商品概要 (Repeatable Fields) -->
                    <div class="bg-gray-900 p-6 rounded border border-gray-700">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-sm font-bold text-primary font-en tracking-widest">商品概要 (PRODUCT SUMMARY)
                            </h4>
                            <button type="button" onclick="addSummaryItem()"
                                class="bg-gray-700 hover:bg-gray-600 text-white text-xs px-3 py-1 rounded">
                                <i class="fas fa-plus mr-1"></i> 項目追加
                            </button>
                        </div>
                        <div id="summary-container" class="space-y-4">
                            <!-- JS renders items here -->
                        </div>
                        <textarea id="summary-data"
                            class="hidden"><?php echo htmlspecialchars($product['product_summary_json'] ?? '[]'); ?></textarea>
                    </div>

                    <!-- 車両情報 -->
                    <div class="bg-gray-900 p-6 rounded border border-gray-700">
                        <h4 class="text-sm font-bold text-primary mb-4 font-en tracking-widest">車両情報 (VEHICLE INFO)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">適合車種 (Compatible Models)</label>
                                <input type="text" name="compatible_models" class="form-input"
                                    value="<?php echo htmlspecialchars($product['compatible_models'] ?? ''); ?>"
                                    placeholder="例: アルファード、ヴェルファイア">
                            </div>
                            <div class="form-group">
                                <label class="form-label">車両型式 (Vehicle Type)</label>
                                <input type="text" name="vehicle_type" class="form-input"
                                    value="<?php echo htmlspecialchars($product['vehicle_type'] ?? ''); ?>"
                                    placeholder="例: AGH30W, GGH30W">
                            </div>
                            <div class="form-group md:col-span-2">
                                <label class="form-label">旧・車両型式 (Model Code) - 互換性のため残す</label>
                                <input type="text" name="model_code" class="form-input"
                                    value="<?php echo htmlspecialchars($product['model_code'] ?? ''); ?>">
                            </div>
                            <div class="form-group md:col-span-2">
                                <label class="form-label">車種タグ (Vehicle Tags)</label>
                                <input type="text" name="vehicle_tags" class="form-input"
                                    value="<?php echo htmlspecialchars($product['vehicle_tags'] ?? ''); ?>"
                                    placeholder="例: アルファード, ヴェルファイア, プリウス">
                                <p class="text-xs text-gray-500 mt-1">※カンマ区切りで複数の車種を入力できます。ストアのフィルターに使用されます。</p>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Option Detail Image (New) -->
            <div>
                <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-700 pb-2">オプション詳細画像</h3>
                <div class="form-group">
                    <label class="form-label">画像 (Image)</label>
                    <p class="text-xs text-gray-500 mb-2">※オプション選択欄の上に表示される説明用画像です。</p>

                    <div class="flex items-center gap-4 bg-gray-900 p-4 rounded border border-gray-700">
                        <?php
                        $option_image_path = $product['option_detail_image'] ?? '';
                        // Convert relative path for admin viewing
                        $option_image_display = $option_image_path;
                        if ($option_image_path && strpos($option_image_path, '../assets') === 0) {
                            $option_image_display = '../../assets' . substr($option_image_path, strlen('../assets'));
                        }
                        ?>
                        <?php if (!empty($option_image_path) && file_exists(__DIR__ . '/' . $option_image_display)): ?>
                            <div class="relative group">
                                <img src="<?php echo htmlspecialchars($option_image_display); ?>"
                                    class="h-32 w-auto rounded border border-gray-600 object-cover"
                                    onerror="this.parentElement.innerHTML='<div class=\'h-32 w-32 bg-red-900/20 rounded border border-red-700 flex items-center justify-center text-red-400 text-xs\'>画像読込失敗</div>'">
                                <div
                                    class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <p class="text-xs text-white">現在の画像</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div
                                class="h-32 w-32 bg-gray-800 rounded border border-gray-700 flex items-center justify-center text-gray-500 text-xs">
                                <div class="text-center">
                                    <i class="fas fa-image text-2xl mb-2"></i>
                                    <p>未設定</p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="flex-1">
                            <input type="hidden" name="option_detail_image_current"
                                value="<?php echo htmlspecialchars($product['option_detail_image'] ?? ''); ?>">
                            <input type="file" name="option_detail_image_file" class="form-input text-sm" accept="image/*">
                            <p class="text-xs text-gray-500 mt-2">新しい画像をアップロードすると上書きされます。</p>
                            <?php if (!empty($option_image_path)): ?>
                                <p class="text-xs text-gray-600 mt-1">現在のパス: <?php echo htmlspecialchars($option_image_path); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Images & Options -->
            <div>
                <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-700 pb-2">画像・オプション</h3>
                <!-- Images -->
                <div class="form-group mb-8">
                    <label class="form-label">商品画像 (Images)</label>
                    <p class="text-xs text-gray-500 mb-2">※ドラッグ＆ドロップで並び替えはできませんが、矢印ボタンで順序変更可能です。</p>

                    <!-- Hidden input to store the lines -->
                    <?php
                    $img_lines = '';
                    $img_arr = json_decode($product['images'] ?? '[]', true);
                    if (is_array($img_arr)) {
                        $img_lines = implode("\n", $img_arr);
                    }
                    ?>
                    <textarea name="images" id="images-input"
                        class="hidden"><?php echo htmlspecialchars($img_lines); ?></textarea>

                    <!-- Visual Image List -->
                    <div id="image-list" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <!-- JS renders here -->
                    </div>

                    <!-- New Upload (Multiple) -->
                    <div class="bg-gray-800 p-4 rounded border border-gray-700">
                        <label class="text-xs font-bold text-primary block mb-2">新規画像追加 (Upload New) - 複数選択可</label>
                        <input type="file" name="new_image_files[]" id="new-image-input" multiple accept="image/*"
                            class="text-gray-300 text-sm w-full">
                        <p class="text-xs text-gray-500 mt-1">※Ctrl/Cmdキーを押しながら複数画像を選択できます。</p>
                        <div id="new-image-preview" class="mt-3 hidden">
                            <p class="text-[10px] text-gray-400 mb-2">プレビュー (保存後にリストに追加されます)</p>
                            <div id="new-image-preview-grid" class="grid grid-cols-4 md:grid-cols-6 gap-2"></div>
                        </div>
                    </div>
                </div>

                <script>
                    // Image Manager Logic
                    document.addEventListener('DOMContentLoaded', () => {
                        const imagesInput = document.getElementById('images-input');
                        const imageList = document.getElementById('image-list');
                        const newImageInput = document.getElementById('new-image-input');
                        const newImagePreview = document.getElementById('new-image-preview');

                        // Parse existing images
                        let images = imagesInput.value.split('\n').filter(line => line.trim() !== '');

                        function renderImages() {
                            imageList.innerHTML = '';
                            images.forEach((img, index) => {
                                // Convert path for admin viewing (src/admin/store/ to src/assets/)
                                let displayPath = img;
                                if (img.startsWith('../assets')) {
                                    displayPath = '../../assets' + img.substring('../assets'.length);
                                }

                                const div = document.createElement('div');
                                div.className = 'relative group bg-gray-900 rounded border border-gray-700 p-2 flex flex-col items-center';
                                div.innerHTML = `
                                    <div class="w-full aspect-video overflow-hidden rounded bg-black mb-2 relative">
                                        <img src="${displayPath}" class="w-full h-full object-cover"
                                             onerror="this.parentElement.innerHTML='<div class=\\'h-full w-full bg-red-900/20 flex items-center justify-center text-red-400 text-xs\\'>読込失敗</div>'">
                                    </div>
                                    <p class="text-[10px] text-gray-500 truncate w-full mb-2">${img}</p>
                                    <div class="flex gap-2 w-full justify-center">
                                        <button type="button" class="bg-gray-700 hover:bg-gray-600 text-white p-1 rounded text-xs w-8" onclick="moveImage(${index}, -1)" ${index === 0 ? 'disabled' : ''}>
                                            <i class="fas fa-arrow-left"></i>
                                        </button>
                                        <button type="button" class="bg-gray-700 hover:bg-gray-600 text-white p-1 rounded text-xs w-8" onclick="moveImage(${index}, 1)" ${index === images.length - 1 ? 'disabled' : ''}>
                                            <i class="fas fa-arrow-right"></i>
                                        </button>
                                        <button type="button" class="bg-red-900/50 hover:bg-red-700 text-red-200 p-1 rounded text-xs w-8" onclick="removeImage(${index})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    ${index === 0 ? '<span class="absolute top-1 left-1 bg-primary text-black text-[10px] font-bold px-1 rounded">MAIN</span>' : ''}
                                `;
                                imageList.appendChild(div);
                            });
                            syncInput();
                        }

                        window.moveImage = (index, direction) => {
                            const newIndex = index + direction;
                            if (newIndex >= 0 && newIndex < images.length) {
                                const temp = images[index];
                                images[index] = images[newIndex];
                                images[newIndex] = temp;
                                renderImages();
                            }
                        };

                        window.removeImage = (index) => {
                            if (confirm('この画像をリストから削除しますか？')) {
                                images.splice(index, 1);
                                renderImages();
                            }
                        };

                        function syncInput() {
                            imagesInput.value = images.join('\n');
                        }

                        // Local Preview for new upload (Multiple)
                        newImageInput.addEventListener('change', (e) => {
                            const files = e.target.files;
                            const previewGrid = document.getElementById('new-image-preview-grid');
                            previewGrid.innerHTML = '';
                            if (files.length > 0) {
                                newImagePreview.classList.remove('hidden');
                                Array.from(files).forEach((file, idx) => {
                                    const reader = new FileReader();
                                    reader.onload = (ev) => {
                                        const div = document.createElement('div');
                                        div.className = 'relative';
                                        div.innerHTML = `
                                            <img src="${ev.target.result}" class="h-20 w-full object-cover rounded border border-gray-600">
                                            <span class="absolute top-0 right-0 bg-black/70 text-[10px] text-white px-1 rounded-bl">${idx + 1}</span>
                                        `;
                                        previewGrid.appendChild(div);
                                    };
                                    reader.readAsDataURL(file);
                                });
                            } else {
                                newImagePreview.classList.add('hidden');
                            }
                        });

                        renderImages();
                    });
                </script>

                <!-- Options Builder -->
                <div class="form-group bg-gray-900 p-4 rounded border border-gray-700">
                    <div class="flex justify-between items-center mb-4">
                        <label class="form-label mb-0">オプション設定 (Options)</label>
                        <button type="button" onclick="addOptionGroup()"
                            class="bg-gray-700 hover:bg-gray-600 text-white text-xs px-3 py-1 rounded">
                            <i class="fas fa-plus mr-1"></i> オプショングループ追加
                        </button>
                    </div>

                    <div id="options-container" class="space-y-6">
                        <!-- JS renders groups here -->
                    </div>
                </div>

                <!-- Template for JS -->
                <textarea id="options-data"
                    class="hidden"><?php echo htmlspecialchars($product['options'] ?? '[]'); ?></textarea>

                <script>
                    // Options Builder JavaScript
                    const optionsContainer = document.getElementById('options-container');
                    let optionsData = [];
                    let groupCounter = 0;

                    try {
                        optionsData = JSON.parse(document.getElementById('options-data').value);
                    } catch (e) {
                        optionsData = [];
                    }

                    // Helper function to escape HTML
                    function escapeHtml(text) {
                        if (!text) return '';
                        return String(text)
                            .replace(/&/g, "&amp;")
                            .replace(/</g, "&lt;")
                            .replace(/>/g, "&gt;")
                            .replace(/"/g, "&quot;")
                            .replace(/'/g, "&#039;");
                    }

                    // Render all option groups from optionsData
                    function renderOptions() {
                        optionsContainer.innerHTML = '';
                        groupCounter = 0; // Reset counter
                        optionsData.forEach((opt) => {
                            appendOptionGroupHtml(opt);
                        });
                    }

                    // Append a single option group
                    function appendOptionGroupHtml(opt) {
                        const currentId = groupCounter++;
                        const groupDiv = document.createElement('div');
                        groupDiv.className = 'option-group bg-black/40 p-4 rounded border border-gray-600 relative';
                        groupDiv.dataset.groupId = currentId;

                        // Parse choices - normalize to array format
                        let normalizedChoices = [];
                        const choices = opt.choices || [];
                        if (Array.isArray(choices)) {
                            normalizedChoices = choices;
                        } else if (typeof choices === 'object') {
                            for (const [val, label] of Object.entries(choices)) {
                                normalizedChoices.push({ value: val, label: label, image: '' });
                            }
                        }

                        groupDiv.innerHTML = `
                            <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-300" onclick="removeOptionGroup(this)">
                                <i class="fas fa-times"></i>
                            </button>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="md:col-span-2">
                                    <label class="text-xs text-gray-400 block mb-1">オプション名 (Label)</label>
                                    <input type="text" name="options[${currentId}][label]" class="form-input text-sm py-1"
                                           value="${escapeHtml(opt.label || '')}" required placeholder="例: カラー">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-400 block mb-1">タイプ (Type)</label>
                                    <select name="options[${currentId}][type]" class="form-input text-sm py-1">
                                        <option value="select" ${opt.type === 'select' ? 'selected' : ''}>Select Box</option>
                                    </select>
                                </div>
                            </div>

                            <div class="choices-section bg-gray-800/50 p-3 rounded">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="text-xs text-gray-400">選択肢 (Choices)</label>
                                    <button type="button" class="text-primary text-xs hover:text-yellow-300" onclick="addChoiceToGroup(this)">
                                        <i class="fas fa-plus"></i> 追加
                                    </button>
                                </div>
                                <div class="choices-container-${currentId} space-y-2">
                                    <!-- Choices render here -->
                                </div>
                            </div>
                        `;
                        optionsContainer.appendChild(groupDiv);

                        // Render existing choices for this group
                        const choicesContainer = groupDiv.querySelector(`.choices-container-${currentId}`);
                        normalizedChoices.forEach((choice) => {
                            appendChoiceHtml(choicesContainer, currentId, choice || {});
                        });
                    }

                    // Append a single choice row to a specific group
                    function appendChoiceHtml(container, groupId, choice) {
                        // Ensure choice is an object with default values
                        const safeChoice = {
                            label: choice?.label || '',
                            value: choice?.value || '',
                            image: choice?.image || ''
                        };

                        // Use timestamp + random to create unique choice ID
                        const cId = Date.now() + Math.floor(Math.random() * 10000);
                        const row = document.createElement('div');
                        row.className = 'choice-row grid grid-cols-12 gap-2 items-center bg-black/20 p-2 rounded border border-gray-700';
                        row.dataset.choiceId = cId;

                        row.innerHTML = `
                            <div class="col-span-3">
                                <label class="text-[10px] text-gray-500 block">表示名 (Label)</label>
                                <input type="text" name="options[${groupId}][choices][${cId}][label]"
                                       class="choice-label-input form-input text-xs py-1" value="${escapeHtml(safeChoice.label)}" placeholder="表示名">
                            </div>
                            <div class="col-span-3">
                                <label class="text-[10px] text-gray-500 block">値 (Value)</label>
                                <input type="text" name="options[${groupId}][choices][${cId}][value]"
                                       class="choice-value-input form-input text-xs py-1" value="${escapeHtml(safeChoice.value)}" placeholder="値 (空白なら表示名)">
                            </div>
                            <div class="col-span-5">
                                <label class="text-[10px] text-gray-500 block">画像 (Image)</label>
                                <div class="flex items-center gap-2">
                                    <div class="choice-image-preview" style="display: ${safeChoice.image ? 'block' : 'none'}">
                                        ${safeChoice.image ? `<img src="${safeChoice.image.startsWith('../assets') ? '../../assets' + safeChoice.image.substring('../assets'.length) : safeChoice.image}" class="h-8 w-8 object-cover rounded border border-gray-600" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2732%27 height=%2732%27%3E%3Crect fill=%27%23333%27 width=%2732%27 height=%2732%27/%3E%3Ctext x=%2716%27 y=%2716%27 text-anchor=%27middle%27 dominant-baseline=%27middle%27 fill=%27%23999%27 font-size=%2710%27%3E?%3C/text%3E%3C/svg%3E'">` : ''}
                                    </div>
                                    <input type="hidden" name="options[${groupId}][choices][${cId}][image_current]" class="choice-image-current" value="${escapeHtml(safeChoice.image)}">
                                    <input type="file" name="options[${groupId}][choices][${cId}][image_file]" class="choice-image-input text-gray-400 text-[10px] w-full file:py-0 file:px-2 file:rounded file:bg-gray-700 file:text-gray-200" accept="image/*">
                                </div>
                            </div>
                            <div class="col-span-1 text-right">
                                <button type="button" class="text-red-500 hover:text-red-300" onclick="removeChoice(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                        container.appendChild(row);

                        // Add event listener for image preview (scoped to this specific row)
                        const fileInput = row.querySelector('.choice-image-input');
                        if (fileInput) {
                            fileInput.addEventListener('change', function(e) {
                                e.stopPropagation(); // Prevent event bubbling

                                // Get elements within THIS specific row only
                                const currentRow = this.closest('.choice-row');
                                const previewContainer = currentRow.querySelector('.choice-image-preview');
                                const file = this.files[0];

                                if (file && previewContainer) {
                                    const reader = new FileReader();
                                    reader.onload = function(event) {
                                        previewContainer.innerHTML = `<img src="${event.target.result}" class="h-8 w-8 object-cover rounded border border-gray-600">`;
                                        previewContainer.style.display = 'block';
                                    };
                                    reader.readAsDataURL(file);
                                } else if (previewContainer) {
                                    previewContainer.style.display = 'none';
                                    previewContainer.innerHTML = '';
                                }
                            });
                        }
                    }

                    // Add new option group
                    window.addOptionGroup = function () {
                        appendOptionGroupHtml({ label: '', type: 'select', choices: [] });
                    };

                    // Remove option group
                    window.removeOptionGroup = function (btn) {
                        if (confirm('このオプショングループを削除しますか？')) {
                            btn.closest('.option-group').remove();
                        }
                    };

                    // Add choice to a specific group (called from button within group)
                    window.addChoiceToGroup = function (btn) {
                        const groupDiv = btn.closest('.option-group');
                        const groupId = groupDiv.dataset.groupId;
                        const choicesContainer = groupDiv.querySelector(`.choices-container-${groupId}`);

                        // Always pass an empty object to ensure blank fields
                        appendChoiceHtml(choicesContainer, groupId, {});
                    };

                    // Remove choice
                    window.removeChoice = function (btn) {
                        btn.closest('.choice-row').remove();
                    };

                    // Initialize on page load
                    document.addEventListener('DOMContentLoaded', renderOptions);
                </script>

                <!-- 商品名フィールド保護スクリプト（最優先で実行） -->
                <script>
                    (function() {
                        'use strict';

                        // 商品名フィールドの保護
                        let productNameOriginalValue = '';
                        let isInitialized = false;

                        function initProductNameProtection() {
                            const productNameField = document.getElementById('product-name-input');
                            if (!productNameField) {
                                setTimeout(initProductNameProtection, 100);
                                return;
                            }

                            if (isInitialized) return;
                            isInitialized = true;

                            // 初期値を保存
                            productNameOriginalValue = productNameField.value;

                            console.log('[商品名保護] 初期化完了。初期値:', productNameOriginalValue);

                            // Method 1: Input event listener (ユーザーの入力のみ許可)
                            let lastUserValue = productNameOriginalValue;
                            let isUserInput = false;

                            productNameField.addEventListener('input', function(e) {
                                isUserInput = true;
                                lastUserValue = this.value;
                                console.log('[商品名保護] ユーザー入力を検知:', lastUserValue);
                            }, true);

                            productNameField.addEventListener('focus', function() {
                                isUserInput = true;
                            });

                            productNameField.addEventListener('blur', function() {
                                setTimeout(() => { isUserInput = false; }, 100);
                            });

                            // Method 2: MutationObserver (DOM変更を監視)
                            const observer = new MutationObserver(function(mutations) {
                                mutations.forEach(function(mutation) {
                                    if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                                        const currentValue = productNameField.value;
                                        if (!isUserInput && currentValue !== lastUserValue) {
                                            console.warn('[商品名保護] 不正な変更を検知。復元します:', lastUserValue);
                                            productNameField.value = lastUserValue;
                                        }
                                    }
                                });
                            });

                            observer.observe(productNameField, {
                                attributes: true,
                                attributeFilter: ['value']
                            });

                            // Method 3: Periodic check (定期チェック)
                            setInterval(function() {
                                const currentValue = productNameField.value;
                                if (!isUserInput && currentValue !== lastUserValue) {
                                    // ファイル名のパターンをチェック（.jpg, .png, .gif等）
                                    const fileNamePattern = /\.(jpg|jpeg|png|gif|bmp|webp|svg)$/i;
                                    if (fileNamePattern.test(currentValue)) {
                                        console.error('[商品名保護] ファイル名が商品名に設定されました！ 復元します。');
                                        productNameField.value = lastUserValue;
                                        alert('エラー: 画像ファイル名が商品名に設定されました。元の値に復元しました。');
                                    }
                                }
                            }, 200);

                            // Method 4: Global file input change listener
                            document.addEventListener('change', function(e) {
                                if (e.target.type === 'file') {
                                    // ファイル選択後、商品名フィールドをチェック
                                    setTimeout(function() {
                                        const currentValue = productNameField.value;
                                        if (currentValue !== lastUserValue) {
                                            console.error('[商品名保護] ファイル選択後に商品名が変更されました。復元します。');
                                            productNameField.value = lastUserValue;
                                        }
                                    }, 50);
                                }
                            }, true);

                            // Method 5: Object.defineProperty (値の直接設定を監視)
                            const descriptor = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value');
                            const originalSet = descriptor.set;

                            Object.defineProperty(productNameField, 'value', {
                                get: function() {
                                    return descriptor.get.call(this);
                                },
                                set: function(val) {
                                    if (this === productNameField && !isUserInput) {
                                        const fileNamePattern = /\.(jpg|jpeg|png|gif|bmp|webp|svg)$/i;
                                        if (fileNamePattern.test(val)) {
                                            console.error('[商品名保護] ファイル名の直接設定をブロックしました:', val);
                                            return; // ブロック
                                        }
                                    }
                                    originalSet.call(this, val);
                                },
                                configurable: true
                            });

                            console.log('[商品名保護] 全ての保護メカニズムが有効化されました。');
                        }

                        // ページロード後すぐに初期化
                        if (document.readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', initProductNameProtection);
                        } else {
                            initProductNameProtection();
                        }
                    })();
                </script>

                <!-- Summary Items JavaScript -->
                <script>
                    // Product Summary Repeatable Fields Manager
                    const summaryContainer = document.getElementById('summary-container');
                    const summaryData = document.getElementById('summary-data');
                    let summaryItems = [];

                    try {
                        summaryItems = JSON.parse(summaryData.value);
                    } catch (e) {
                        summaryItems = [];
                    }

                    function renderSummaryItems() {
                        summaryContainer.innerHTML = '';
                        if (summaryItems.length === 0) {
                            summaryContainer.innerHTML = '<p class="text-gray-500 text-sm">項目がありません。「項目追加」ボタンで追加してください。</p>';
                            return;
                        }
                        summaryItems.forEach((item, index) => {
                            appendSummaryItemHtml(item, index);
                        });
                    }

                    function appendSummaryItemHtml(item, index) {
                        const div = document.createElement('div');
                        div.className = 'bg-black/40 p-4 rounded border border-gray-600 relative';
                        div.innerHTML = `
                            <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-300" onclick="removeSummaryItem(${index})">
                                <i class="fas fa-times"></i>
                            </button>
                            <div class="space-y-3">
                                <div class="form-group">
                                    <label class="text-xs text-gray-400 block mb-1">タイトル (Title)</label>
                                    <input type="text" name="summary_items[${index}][title]" class="form-input text-sm py-2"
                                           value="${escapeHtml(item.title || '')}" placeholder="例: 素材">
                                </div>
                                <div class="form-group">
                                    <label class="text-xs text-gray-400 block mb-1">テキスト (Text)</label>
                                    <textarea name="summary_items[${index}][text]" class="form-input text-sm py-2 h-20"
                                              placeholder="例: 本革（イタリア製）を使用しています。">${escapeHtml(item.text || '')}</textarea>
                                </div>
                            </div>
                        `;
                        summaryContainer.appendChild(div);
                    }

                    window.addSummaryItem = function () {
                        const index = summaryItems.length;
                        summaryItems.push({ title: '', text: '' });
                        appendSummaryItemHtml({ title: '', text: '' }, index);
                    };

                    window.removeSummaryItem = function (index) {
                        if (confirm('この項目を削除しますか？')) {
                            summaryItems.splice(index, 1);
                            renderSummaryItems();
                        }
                    };

                    // Helper function (if not already defined)
                    if (typeof escapeHtml === 'undefined') {
                        function escapeHtml(text) {
                            if (!text) return '';
                            return text
                                .replace(/&/g, "&amp;")
                                .replace(/</g, "&lt;")
                                .replace(/>/g, "&gt;")
                                .replace(/"/g, "&quot;")
                                .replace(/'/g, "&#039;");
                        }
                    }

                    // Initialize
                    document.addEventListener('DOMContentLoaded', () => {
                        renderSummaryItems();
                    });
                </script>
            </div>
        </div>

        <div class="pt-6 border-t border-gray-700">
            <button type="submit"
                class="bg-primary hover:bg-yellow-500 text-black font-bold py-3 px-8 rounded transition-colors w-full md:w-auto tracking-widest font-en">
                SAVE PRODUCT
            </button>
        </div>
</div>
</form>
</div>

<?php require_once '../includes/footer.php'; ?>