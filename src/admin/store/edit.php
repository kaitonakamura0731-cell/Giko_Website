<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
checkAuth();

$id = $_GET['id'] ?? null;
$product = null;
$error = '';
$success = '';

// Default data
$default_product = [
    'name' => '',
    'price' => 0,
    'shipping_fee' => 1000,
    'description' => '',
    'compatible_models' => '',
    'model_code' => '',
    'images' => '[]',
    'options' => '[]',
    'option_detail_image' => '',
    'stock_status' => 1
];

if ($id) {
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
} else {
    $product = $default_product;
}

require_once '../includes/upload_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic Sanitation
    $name = trim($_POST['name']);
    $price = (int) $_POST['price'];
    $shipping_fee = (int) $_POST['shipping_fee'];
    $short_description = $_POST['short_description']; // New
    $description = $_POST['description'];
    $compatible_models = $_POST['compatible_models'];
    $model_code = $_POST['model_code'];
    
    // Feature Image for Options
    $option_detail_image = $_POST['option_detail_image_current'] ?? '';
    // Handle Upload
    $uploaded_opt_img = handleUpload('option_detail_image_file', '../../assets/images/uploads/');
    if ($uploaded_opt_img) {
        $option_detail_image = str_replace('../../assets', '../assets', $uploaded_opt_img);
    }
    $stock_status = (int) ($_POST['stock_status'] ?? 1);

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

    // Handle New Image Upload
    $uploaded_img = handleUpload('new_image_file', '../../assets/images/uploads/');
    if ($uploaded_img) {
        // Convert to frontend-friendly path
        $db_path = str_replace('../../assets', '../assets', $uploaded_img);
        array_unshift($images_clean, $db_path); // Add to top
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
            $sql = "UPDATE products SET name=?, price=?, shipping_fee=?, short_description=?, description=?, compatible_models=?, model_code=?, images=?, options=?, option_detail_image=?, stock_status=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $price, $shipping_fee, $short_description, $description, $compatible_models, $model_code, $images_json, $options_json, $option_detail_image, $stock_status, $id]);
            $success = "商品情報を更新しました。";
        } else {
            // Insert
            $sql = "INSERT INTO products (name, price, shipping_fee, short_description, description, compatible_models, model_code, images, options, option_detail_image, stock_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $price, $shipping_fee, $short_description, $description, $compatible_models, $model_code, $images_json, $options_json, $option_detail_image, $stock_status]);
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
        <a href="index.php" class="text-gray-400 hover:text-white transition-colors text-sm">
            <i class="fas fa-arrow-left mr-1"></i> 一覧に戻る
        </a>
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
                        <input type="text" name="name" class="form-input"
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
                <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-700 pb-2">適合・説明</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="form-label">適合車種 (Compatible Models)</label>
                        <input type="text" name="compatible_models" class="form-input"
                            value="<?php echo htmlspecialchars($product['compatible_models']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">車両型式 (Model Code)</label>
                        <input type="text" name="model_code" class="form-input"
                            value="<?php echo htmlspecialchars($product['model_code']); ?>">
                    </div>

                    <div class="form-group md:col-span-2">
                        <label class="form-label">一覧用説明文 (List Short Description)</label>
                        <textarea name="short_description" class="form-input h-20"
                            placeholder="一覧ページに表示される短い説明文"><?php echo htmlspecialchars($product['short_description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group md:col-span-2">
                        <label class="form-label">詳細説明文 (Detail Description)</label>
                        <textarea name="description"
                            class="form-input h-64 font-mono text-sm leading-relaxed"><?php echo htmlspecialchars($product['description']); ?></textarea>
                        <p class="text-xs text-gray-500 mt-1">※改行は自動的に反映されます。HTMLタグは使用しないでください。</p>
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
                        <?php if (!empty($product['option_detail_image'])): ?>
                            <div class="relative group">
                                <img src="<?php echo htmlspecialchars($product['option_detail_image']); ?>" class="h-32 w-auto rounded border border-gray-600">
                                <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <p class="text-xs text-white">現在の画像</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="h-32 w-32 bg-gray-800 rounded border border-gray-700 flex items-center justify-center text-gray-500 text-xs">
                                未設定
                            </div>
                        <?php endif; ?>
                        
                        <div class="flex-1">
                            <input type="hidden" name="option_detail_image_current" value="<?php echo htmlspecialchars($product['option_detail_image'] ?? ''); ?>">
                            <input type="file" name="option_detail_image_file" class="form-input text-sm">
                            <p class="text-xs text-gray-500 mt-2">新しい画像をアップロードすると上書きされます。</p>
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

                    <!-- New Upload -->
                    <div class="bg-gray-800 p-4 rounded border border-gray-700">
                        <label class="text-xs font-bold text-primary block mb-2">新規画像追加 (Upload New)</label>
                        <input type="file" name="new_image_file" id="new-image-input"
                            class="text-gray-300 text-sm w-full">
                        <div id="new-image-preview" class="mt-2 hidden">
                            <p class="text-[10px] text-gray-400 mb-1">プレビュー (保存後にリストに追加されます)</p>
                            <img src="" class="h-20 w-auto rounded border border-gray-600">
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
                                const div = document.createElement('div');
                                div.className = 'relative group bg-gray-900 rounded border border-gray-700 p-2 flex flex-col items-center';
                                div.innerHTML = `
                                    <div class="w-full aspect-video overflow-hidden rounded bg-black mb-2 relative">
                                        <img src="${(img.startsWith('../assets') ? '../' + img : img)}" class="w-full h-full object-cover">
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

                        // Local Preview for new upload
                        newImageInput.addEventListener('change', (e) => {
                            const file = e.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (ev) => {
                                    newImagePreview.querySelector('img').src = ev.target.result;
                                    newImagePreview.classList.remove('hidden');
                                };
                                reader.readAsDataURL(file);
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
                    const optionsContainer = document.getElementById('options-container');
                    let optionsData = [];
                    try {
                        optionsData = JSON.parse(document.getElementById('options-data').value);
                    } catch (e) {
                        optionsData = [];
                    }

                    // Initial Render
                    function renderOptions() {
                        optionsContainer.innerHTML = '';
                        optionsData.forEach((opt, idx) => {
                            renderOptionGroup(opt, idx);
                        });
                    }

                    function renderOptionGroup(opt, idx) {
                        const groupDiv = document.createElement('div');
                        groupDiv.className = 'option-group bg-black/40 p-4 rounded border border-gray-600 relative';
                        groupDiv.dataset.index = idx;

                        // Identify choices: could be old format or new
                        // Old: {"key":"val", ...}
                        // New: [{"label":"l","value":"v","image":"..."}, ...]
                        let choicesHtml = '';
                        const choices = opt.choices || [];

                        // Helper to normalize choices to array
                        let normalizedChoices = [];
                        if (Array.isArray(choices)) {
                            normalizedChoices = choices;
                        } else if (typeof choices === 'object') {
                            // Convert old Obj to Array
                            for (const [val, label] of Object.entries(choices)) {
                                normalizedChoices.push({ value: val, label: label, image: '' });
                            }
                        }

                        groupDiv.innerHTML = `
                            <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-300" onclick="removeOptionGroup(${idx})">
                                <i class="fas fa-times"></i>
                            </button>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="md:col-span-2">
                                    <label class="text-xs text-gray-400 block mb-1">オプション名 (Label)</label>
                                    <input type="text" name="options[${idx}][label]" class="form-input text-sm py-1" 
                                           value="${escapeHtml(opt.label || '')}" required placeholder="例: カラー">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-400 block mb-1">タイプ (Type)</label>
                                    <select name="options[${idx}][type]" class="form-input text-sm py-1">
                                        <option value="select" ${opt.type === 'select' ? 'selected' : ''}>Select Box</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="choices-section bg-gray-800/50 p-3 rounded">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="text-xs text-gray-400">選択肢 (Choices)</label>
                                    <button type="button" class="text-primary text-xs hover:text-yellow-300" onclick="addChoice(${idx})">
                                        <i class="fas fa-plus"></i> 追加
                                    </button>
                                </div>
                                <div id="choices-container-${idx}" class="space-y-2">
                                    <!-- Choices render here -->
                                </div>
                            </div>
                        `;
                        optionsContainer.appendChild(groupDiv);

                        const choicesContainer = groupDiv.querySelector(`#choices-container-${idx}`);
                        normalizedChoices.forEach((choice, cIdx) => {
                            renderChoiceRow(choicesContainer, idx, cIdx, choice);
                        });
                    }

                    function renderChoiceRow(container, groupIdx, choiceIdx, choice) {
                        const row = document.createElement('div');
                        row.className = 'grid grid-cols-12 gap-2 items-center bg-black/20 p-2 rounded border border-gray-700';
                        row.innerHTML = `
                            <div class="col-span-3">
                                <label class="text-[10px] text-gray-500 block">表示名 (Label)</label>
                                <input type="text" name="options[${groupIdx}][choices][${choiceIdx}][label]" 
                                       class="form-input text-xs py-1" value="${escapeHtml(choice.label || '')}" placeholder="表示名">
                            </div>
                            <div class="col-span-3">
                                <label class="text-[10px] text-gray-500 block">値 (Value)</label>
                                <input type="text" name="options[${groupIdx}][choices][${choiceIdx}][value]" 
                                       class="form-input text-xs py-1" value="${escapeHtml(choice.value || '')}" placeholder="値 (空白なら表示名)">
                            </div>
                            <div class="col-span-5">
                                <label class="text-[10px] text-gray-500 block">画像 (Image)</label>
                                <div class="flex items-center gap-2">
                                    ${choice.image ? `<img src="${(choice.image.startsWith('../assets') ? '../' + choice.image : choice.image)}" class="h-8 w-8 object-cover rounded border border-gray-600">` : ''}
                                    <input type="hidden" name="options[${groupIdx}][choices][${choiceIdx}][image_current]" value="${escapeHtml(choice.image || '')}">
                                    <input type="file" name="options[${groupIdx}][choices][${choiceIdx}][image_file]" class="text-gray-400 text-[10px] w-full file:py-0 file:px-2 file:rounded file:bg-gray-700 file:text-gray-200">
                                </div>
                            </div>
                            <div class="col-span-1 text-right">
                                <button type="button" class="text-red-500 hover:text-red-300" onclick="this.closest('.grid').remove()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                        container.appendChild(row);
                    }

                    function addOptionGroup() {
                        const idx = document.querySelectorAll('.option-group').length;
                        // Just append a new empty group (simplification: re-rendering everything is safer actually for indices, but let's try direct append for performance)
                        // Actually, if we delete groups, indices might get messed up in PHP if we rely on sequential keys?
                        // PHP $_POST arrays will have gaps if we remove elements. `array_values` or `foreach` handles it.
                        // But my JS `addChoice` relies on `idx`.
                        // Easier strategy: Just push to `optionsData` and re-render all.
                        optionsData.push({ label: '', type: 'select', choices: [] });
                        renderOptions();
                    }

                    function removeOptionGroup(idx) {
                        if (confirm('このオプショングループを削除しますか？')) {
                            optionsData.splice(idx, 1);
                            renderOptions();
                        }
                    }

                    // Since inputs are dynamic, we need a way to add choices without full re-render or carefully manage state.
                    // Full re-render clears inputs...
                    // Let's attach event to Add Button to just append HTML with correct Index.
                    // IMPORTANT: If I delete a group in the middle, indices shift. 
                    // So "removeOptionGroup" MUST Re-render.
                    // "addChoice" can just append.
                    // BUT: We need to sync Input values back to `optionsData` before re-render if we mix strategies.

                    // Revised Strategy:
                    // 1. Render all from `optionsData`.
                    // 2. When interacting (typing), we rely on default form behavior.
                    // 3. When Adding/Removing Groups, we need to SAVE current form state to `optionsData` then Re-render.
                    // 4. When Adding components, we can just Append HTML because indices for THAT group don't change if we just append.

                    window.addChoice = function (groupIdx) {
                        const container = document.getElementById(`choices-container-${groupIdx}`);
                        const cIdx = container.children.length; // rough index
                        // We need to find a unique index or just use length? 
                        // If we delete choices, we might want to re-index choices too?
                        // For simplicity, let's just append. PHP handles `choices` array even if indices are non-sequential or gaps?
                        // Actually PHP `choices` index in `name` doesn't strictly matter for `foreach` if we don't assume continuous.
                        // But for `javascript`, standardizing is cleaner.

                        renderChoiceRow(container, groupIdx, cIdx + Date.now(), {}); // use timestamp to avoid collision if simple length fails
                    };

                    function escapeHtml(text) {
                        if (!text) return '';
                        return text
                            .replace(/&/g, "&amp;")
                            .replace(/</g, "&lt;")
                            .replace(/>/g, "&gt;")
                            .replace(/"/g, "&quot;")
                            .replace(/'/g, "&#039;");
                    }

                    // On Load
                    document.addEventListener('DOMContentLoaded', renderOptions);

                    // Sync before Add/Remove Group to persist text inputs
                    function syncState() {
                        const groups = document.querySelectorAll('.option-group');
                        const newData = [];
                        groups.forEach((g, i) => {
                            const label = g.querySelector(`input[name^="options[${g.dataset.index}][label]"]`).value;
                            const choices = [];
                            // This is getting complex to grab all deep inputs.
                            // Maybe just Re-rendering is heavy-handed but ensures indices align?
                            // Actually, simply relying on Form Submission is enough for PHP.
                            // The only issue is UI management (Adding/Removing).

                            // Let's start simple: 
                            // For Add Group -> Re-render (indices update). BUT we lose text unless we sync.
                            // Maybe just append the group at the bottom and don't re-render existing?
                        });
                    }

                    // Override `renderOptions` to NOT clear everything if we can help it?
                    // No, let's stick to "Append Group" only appends. "Remove" removes element. 
                    // Indices in `name` attributes: `options[${idx}]` -- if we remove options[0], we have options[1]. PHP receives array index 1. `foreach` works fine.
                    // So we don't need to re-index!

                    // Redefine functions to be simpler DOM manipulations

                    window.renderOptions = function () {
                        optionsContainer.innerHTML = '';
                        optionsData.forEach((opt, idx) => {
                            // Use a unique ID for the group to avoid index collisions if we were to delete/add
                            // But here we are rendering initial state.
                            appendOptionGroupHtml(opt, idx);
                        });
                    };

                    let groupCounter = 0;

                    function appendOptionGroupHtml(opt, idx) {
                        // idx is used for initial uniqueness. Subsequent adds use increments.
                        const currentId = groupCounter++;

                        const groupDiv = document.createElement('div');
                        groupDiv.className = 'option-group bg-black/40 p-4 rounded border border-gray-600 relative';

                        // Parse choices
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
                            <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-300" onclick="this.parentElement.remove()">
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
                                    <button type="button" class="text-primary text-xs hover:text-yellow-300" onclick="addChoice(${currentId})">
                                        <i class="fas fa-plus"></i> 追加
                                    </button>
                                </div>
                                <div id="choices-container-${currentId}" class="space-y-2">
                                    <!-- Choices render here -->
                                </div>
                            </div>
                        `;
                        optionsContainer.appendChild(groupDiv);

                        const choicesContainer = groupDiv.querySelector(`#choices-container-${currentId}`);
                        normalizedChoices.forEach((choice) => {
                            // Use timestamp/random for choice Index to avoid collision in same group
                            appendChoiceHtml(choicesContainer, currentId, choice);
                        });
                    }

                    function appendChoiceHtml(container, groupParamsId, choice) {
                        const cId = Date.now() + Math.floor(Math.random() * 1000);
                        const row = document.createElement('div');
                        row.className = 'grid grid-cols-12 gap-2 items-center bg-black/20 p-2 rounded border border-gray-700';
                        row.innerHTML = `
                            <div class="col-span-3">
                                <label class="text-[10px] text-gray-500 block">表示名 (Label)</label>
                                <input type="text" name="options[${groupParamsId}][choices][${cId}][label]" 
                                       class="form-input text-xs py-1" value="${escapeHtml(choice.label || '')}" placeholder="表示名">
                            </div>
                            <div class="col-span-3">
                                <label class="text-[10px] text-gray-500 block">値 (Value)</label>
                                <input type="text" name="options[${groupParamsId}][choices][${cId}][value]" 
                                       class="form-input text-xs py-1" value="${escapeHtml(choice.value || '')}" placeholder="値 (空白なら表示名)">
                            </div>
                            <div class="col-span-5">
                                <label class="text-[10px] text-gray-500 block">画像 (Image)</label>
                                <div class="flex items-center gap-2">
                                    ${choice.image ? `<img src="${(choice.image.startsWith('../assets') ? '../' + choice.image : choice.image)}" class="h-8 w-8 object-cover rounded border border-gray-600">` : ''}
                                    <input type="hidden" name="options[${groupParamsId}][choices][${cId}][image_current]" value="${escapeHtml(choice.image || '')}">
                                    <input type="file" name="options[${groupParamsId}][choices][${cId}][image_file]" class="text-gray-400 text-[10px] w-full file:py-0 file:px-2 file:rounded file:bg-gray-700 file:text-gray-200">
                                </div>
                            </div>
                            <div class="col-span-1 text-right">
                                <button type="button" class="text-red-500 hover:text-red-300" onclick="this.closest('.grid').remove()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                        container.appendChild(row);
                    }

                    window.addOptionGroup = function () {
                        appendOptionGroupHtml({ label: '', type: 'select', choices: [] });
                    };

                    window.addChoice = function (groupId) {
                        const container = document.getElementById(`choices-container-${groupId}`);
                        appendChoiceHtml(container, groupId, {});
                    };

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