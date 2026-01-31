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
    $stock_status = (int) $_POST['stock_status'];

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

    // Options Builder Logic
    // Input format: $_POST['opt_label'][], $_POST['opt_choices'][]
    // choices are newline separated strings.
    $options_arr = [];
    if (isset($_POST['opt_label']) && is_array($_POST['opt_label'])) {
        foreach ($_POST['opt_label'] as $i => $label) {
            $label = trim($label);
            if (!$label)
                continue;

            $choices_raw = $_POST['opt_choices'][$i] ?? '';
            $type = $_POST['opt_type'][$i] ?? 'select';

            // Parse choices
            $choices_lines = preg_split('/\r\n|\r|\n/', $choices_raw);
            $choices_arr = [];
            foreach ($choices_lines as $cline) {
                $cline = trim($cline);
                if (!$cline)
                    continue;

                // Support "value:Label" format, otherwise value=Label
                if (strpos($cline, ':') !== false) {
                    list($val, $txt) = explode(':', $cline, 2);
                    $choices_arr[trim($val)] = trim($txt);
                } else {
                    $choices_arr[$cline] = $cline; // Indexed or value=label
                }
            }

            $options_arr[] = [
                'label' => $label,
                'type' => $type,
                'choices' => $choices_arr
            ];
        }
    }
    $options_json = json_encode($options_arr, JSON_UNESCAPED_UNICODE);

    try {
        if ($id) {
            // Update
            $sql = "UPDATE products SET name=?, price=?, shipping_fee=?, short_description=?, description=?, compatible_models=?, model_code=?, images=?, options=?, stock_status=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $price, $shipping_fee, $short_description, $description, $compatible_models, $model_code, $images_json, $options_json, $stock_status, $id]);
            $success = "商品情報を更新しました。";
        } else {
            // Insert
            $sql = "INSERT INTO products (name, price, shipping_fee, short_description, description, compatible_models, model_code, images, options, stock_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $price, $shipping_fee, $short_description, $description, $compatible_models, $model_code, $images_json, $options_json, $stock_status]);
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
                                        <img src="${img}" class="w-full h-full object-cover">
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
                        <button type="button" onclick="addOptionRow()"
                            class="bg-gray-700 hover:bg-gray-600 text-white text-xs px-3 py-1 rounded">
                            <i class="fas fa-plus mr-1"></i> オプション追加
                        </button>
                    </div>

                    <div id="options-container" class="space-y-4">
                        <?php
                        $opts = json_decode($product['options'] ?? '[]', true);
                        if (!is_array($opts))
                            $opts = [];
                        foreach ($opts as $idx => $opt):
                            // Convert choices array for display (value:label or just value)
                            $choices_text = '';
                            $choices = $opt['choices'] ?? [];
                            foreach ($choices as $k => $v) {
                                if (is_string($k) && $k !== $v && $k !== (string) $idx) {
                                    // Assoc array logic is tricky if keys are numeric indices in PHP arrays.
                                    // migration used assoc for color codes.
                                    $choices_text .= "$k:$v\n";
                                } else {
                                    $choices_text .= "$v\n";
                                }
                            }
                            ?>
                            <div class="option-row bg-black/30 p-3 rounded border border-gray-700 relative">
                                <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-300"
                                    onclick="this.parentElement.remove()">
                                    <i class="fas fa-times"></i>
                                </button>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs text-gray-400 block mb-1">項目名 (Label)</label>
                                        <input type="text" name="opt_label[]" class="form-input text-sm py-1"
                                            value="<?php echo htmlspecialchars($opt['label'] ?? ''); ?>" required
                                            placeholder="例: カラー">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-400 block mb-1">タイプ (Type)</label>
                                        <select name="opt_type[]" class="form-input text-sm py-1">
                                            <option value="select">Select Box</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="text-xs text-gray-400 block mb-1">選択肢 (Choices) ※1行に1つ / "値:表示名"
                                            も可</label>
                                        <textarea name="opt_choices[]"
                                            class="form-input h-24 text-sm font-mono leading-tight"
                                            placeholder="Choice 1&#10;Choice 2&#10;val:Label"><?php echo htmlspecialchars(trim($choices_text)); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <script>
                        function addOptionRow() {
                            const container = document.getElementById('options-container');
                            const div = document.createElement('div');
                            div.className = 'option-row bg-black/30 p-3 rounded border border-gray-700 relative fade-in';
                            div.innerHTML = `
                            <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-300" onclick="this.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs text-gray-400 block mb-1">項目名 (Label)</label>
                                    <input type="text" name="opt_label[]" class="form-input text-sm py-1" required placeholder="例: カラー">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-400 block mb-1">タイプ (Type)</label>
                                    <select name="opt_type[]" class="form-input text-sm py-1">
                                        <option value="select">Select Box</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-xs text-gray-400 block mb-1">選択肢 (Choices)</label>
                                    <textarea name="opt_choices[]" class="form-input h-24 text-sm font-mono leading-tight" placeholder="Choice 1&#10;Choice 2&#10;value:Label"></textarea>
                                </div>
                            </div>
                        `;
                            container.appendChild(div);
                        }
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