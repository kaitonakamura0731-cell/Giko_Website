<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
checkAuth();

$id = $_GET['id'] ?? null;
$work = null;
$error = '';
$success = '';

// Default data
$default_work = [
    'title' => '',
    'subtitle' => '',
    'category' => 'full',
    'main_image' => '',
    'description' => '',
    'concept_text' => '',
    'specs' => json_encode(['seat' => '', 'material' => '', 'color' => '', 'period' => ''], JSON_UNESCAPED_UNICODE),
    'data_info' => json_encode(['model' => '', 'model_code' => '', 'material' => '', 'content' => '', 'price' => ''], JSON_UNESCAPED_UNICODE)
];

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM works WHERE id = ?");
        $stmt->execute([$id]);
        $work = $stmt->fetch();
        if (!$work) {
            header("Location: index.php");
            exit;
        }
    } catch (PDOException $e) {
        $error = "DB Error: " . $e->getMessage();
    }
} else {
    $work = $default_work;
}

require_once '../includes/upload_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic Sanitation & Validation
    $title = trim($_POST['title']);
    $subtitle = trim($_POST['subtitle']);
    $category = $_POST['category'];

    // Image Upload Logic
    $main_image = $_POST['main_image']; // Default to text input

    // 1. Main Image Upload
    $uploaded_main = handleUpload('main_image_file', '../../assets/images/uploads/');
    if ($uploaded_main) {
        // Store as "assets/..." (Relative to src root)
        $main_image = str_replace('../../', '', $uploaded_main);
    }

    $description = $_POST['description'];
    $concept_text = $_POST['concept_text'];

    // JSON Data
    $specs = json_encode([
        'seat' => $_POST['spec_seat'] ?? '',
        'material' => $_POST['spec_material'] ?? '',
        'color' => $_POST['spec_color'] ?? '',
        'period' => $_POST['spec_period'] ?? ''
    ], JSON_UNESCAPED_UNICODE);

    $data_info = json_encode([
        'model' => $_POST['data_model'] ?? '',
        'model_code' => $_POST['data_model_code'] ?? '',
        'material' => $_POST['data_material'] ?? '',
        'content' => $_POST['data_content'] ?? '',
        'price' => $_POST['data_price'] ?? ''
    ], JSON_UNESCAPED_UNICODE);

    // Gallery Images Handling
    // 1. Text Area Input (Existing Paths)
    $gallery_raw = $_POST['gallery_images'] ?? '';
    $gallery_lines = preg_split('/\r\n|\r|\n/', $gallery_raw);
    $gallery_clean = [];
    foreach ($gallery_lines as $line) {
        $line = trim($line);
        if ($line) {
            $gallery_clean[] = $line;
        }
    }

    // 2. Multi-File Upload
    if (isset($_FILES['gallery_files'])) {
        $file_count = count($_FILES['gallery_files']['name']);
        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['gallery_files']['error'][$i] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['gallery_files']['tmp_name'][$i];
                $name = basename($_FILES['gallery_files']['name'][$i]);
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $newName = time() . '_' . uniqid() . '.' . $ext;

                $targetDir = '../../assets/images/uploads/';
                $targetPath = $targetDir . $newName; // Physical path

                if (move_uploaded_file($tmpName, $targetPath)) {
                    // DB Path: assets/images/uploads/...
                    $gallery_clean[] = 'assets/images/uploads/' . $newName;
                }
            }
        }
    }

    $gallery_images = json_encode($gallery_clean, JSON_UNESCAPED_UNICODE);

    try {
        if ($id) {
            // Update
            $sql = "UPDATE works SET title=?, subtitle=?, category=?, main_image=?, description=?, concept_text=?, specs=?, data_info=?, gallery_images=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $subtitle, $category, $main_image, $description, $concept_text, $specs, $data_info, $gallery_images, $id]);
            $success = "更新しました。";
        } else {
            // Insert
            $sql = "INSERT INTO works (title, subtitle, category, main_image, description, concept_text, specs, data_info, gallery_images) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $subtitle, $category, $main_image, $description, $concept_text, $specs, $data_info, $gallery_images]);
            $id = $pdo->lastInsertId();
            $success = "作成しました。";
            // Refresh to get edit view
            header("Location: edit.php?id=" . $id . "&created=1");
            exit;
        }

        // Reload work data
        $stmt = $pdo->prepare("SELECT * FROM works WHERE id = ?");
        $stmt->execute([$id]);
        $work = $stmt->fetch();

    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Helper to decode JSON safely
function getJsonVal($json, $key)
{
    $data = json_decode($json, true);
    return $data[$key] ?? '';
}

require_once '../includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold font-en tracking-widest text-white">
            <?php echo $id ? 'EDIT WORK' : 'NEW WORK'; ?>
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

            <!-- Basic Info -->
            <div>
                <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-700 pb-2">基本情報</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="form-label">タイトル (Title)</label>
                        <input type="text" name="title" class="form-input"
                            value="<?php echo htmlspecialchars($work['title']); ?>" required placeholder="例: ALPHARD">
                    </div>
                    <div class="form-group">
                        <label class="form-label">サブタイトル (Subtitle)</label>
                        <input type="text" name="subtitle" class="form-input"
                            value="<?php echo htmlspecialchars($work['subtitle']); ?>"
                            placeholder="例: TOYOTA / 30 Series">
                    </div>
                    <div class="form-group">
                        <label class="form-label">カテゴリー (Category)</label>
                        <select name="category" class="form-input" required>
                            <option value="partial" <?php if ($work['category'] == 'partial') echo 'selected'; ?>>部分張替え</option>
                            <option value="full" <?php if ($work['category'] == 'full') echo 'selected'; ?>>全内装張替え</option>
                            <option value="package" <?php if ($work['category'] == 'package') echo 'selected'; ?>>補修/リペア</option>
                            <option value="ambient" <?php if ($work['category'] == 'ambient') echo 'selected'; ?>>アンビエントライト</option>
                            <option value="starlight" <?php if ($work['category'] == 'starlight') echo 'selected'; ?>>スターライト</option>
                            <option value="newbiz" <?php if ($work['category'] == 'newbiz') echo 'selected'; ?>>新ブランド</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Images & Description -->
            <div>
                <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-700 pb-2">画像・説明</h3>
                <div class="form-group mb-4">
                    <label class="form-label">メイン画像 (Main Image)</label>
                    <!-- Hidden field to keep current path -->
                    <input type="hidden" name="main_image" value="<?php echo htmlspecialchars($work['main_image']); ?>">

                    <?php if ($work['main_image']): ?>
                        <!-- Current Image Preview -->
                        <div class="mb-3 relative inline-block group">
                            <img src="<?php echo '../../' . htmlspecialchars($work['main_image']); ?>"
                                class="h-40 object-cover rounded-lg border border-gray-700"
                                onerror="this.parentElement.innerHTML='<div class=\'h-40 w-60 bg-gray-800 rounded-lg border border-gray-700 flex items-center justify-center text-gray-600\'><i class=\'fas fa-image text-3xl\'></i></div>'">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                <span class="text-xs text-white font-en">CURRENT IMAGE</span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Upload Area -->
                    <div class="border-2 border-dashed border-gray-700 hover:border-primary/50 rounded-lg p-6 text-center transition-colors cursor-pointer"
                        onclick="document.getElementById('main-image-upload').click()">
                        <i class="fas fa-cloud-upload-alt text-2xl text-gray-500 mb-2"></i>
                        <p class="text-sm text-gray-400"><?php echo $work['main_image'] ? '新しい画像に差し替える' : 'クリックして画像をアップロード'; ?></p>
                        <p class="text-[10px] text-gray-600 mt-1">JPG, PNG, WebP</p>
                        <input type="file" id="main-image-upload" name="main_image_file" accept="image/*"
                            class="hidden" onchange="previewMainImage(this)">
                    </div>

                    <!-- New image preview -->
                    <div id="main-image-new-preview" class="hidden mt-3">
                        <p class="text-xs text-green-400 mb-1"><i class="fas fa-check-circle mr-1"></i>新しい画像が選択されました</p>
                        <img id="main-image-new-img" class="h-40 object-cover rounded-lg border border-green-700/50">
                    </div>

                    <script>
                    function previewMainImage(input) {
                        const preview = document.getElementById('main-image-new-preview');
                        const img = document.getElementById('main-image-new-img');
                        if (input.files && input.files[0]) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                img.src = e.target.result;
                                preview.classList.remove('hidden');
                            };
                            reader.readAsDataURL(input.files[0]);
                        }
                    }
                    </script>
                </div>
                <!-- Description -->
                <div class="form-group">
                    <div class="form-group">
                        <label class="form-label">一覧用説明文 (Short Description)</label>
                        <textarea name="description"
                            class="form-input h-20"><?php echo htmlspecialchars($work['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">詳細コンセプト (Concept Text)</label>
                        <textarea name="concept_text"
                            class="form-input h-32"><?php echo htmlspecialchars($work['concept_text']); ?></textarea>
                    </div>

                    <!-- Gallery Images -->
                    <div class="form-group">
                        <label class="form-label">ギャラリー画像 (Gallery Images)</label>

                        <?php
                        $gallery_json = $work['gallery_images'] ?? '[]';
                        $gallery_arr = json_decode($gallery_json, true);
                        if (!is_array($gallery_arr)) $gallery_arr = [];
                        $gallery_lines = implode("\n", $gallery_arr);
                        ?>

                        <!-- Hidden textarea (keeps form submission working) -->
                        <textarea name="gallery_images" id="gallery-paths" class="hidden"><?php echo htmlspecialchars($gallery_lines); ?></textarea>

                        <!-- Current Images Preview -->
                        <?php if (count($gallery_arr) > 0): ?>
                            <div class="mb-4">
                                <p class="text-xs text-gray-400 mb-2"><i class="fas fa-images mr-1"></i>登録済み画像 (<?php echo count($gallery_arr); ?>枚)</p>
                                <div id="gallery-preview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                    <?php foreach ($gallery_arr as $idx => $img):
                                        $imgPath = htmlspecialchars($img);
                                        // Handle both "assets/..." and "../assets/..." patterns
                                        $displayPath = (strpos($img, '../') === 0) ? '../../' . substr($img, 3) : '../../' . $img;
                                    ?>
                                        <div class="gallery-item group relative" data-path="<?php echo $imgPath; ?>">
                                            <a href="<?php echo $displayPath; ?>" target="_blank" class="block">
                                                <div class="aspect-[4/3] bg-gray-800 rounded-lg border border-gray-700 overflow-hidden group-hover:border-primary/50 transition-colors">
                                                    <img src="<?php echo $displayPath; ?>"
                                                        class="w-full h-full object-cover"
                                                        onerror="this.parentElement.innerHTML='<div class=\'flex items-center justify-center h-full text-gray-600\'><i class=\'fas fa-image text-2xl\'></i></div>'">
                                                </div>
                                            </a>
                                            <button type="button" onclick="removeGalleryImage(this)"
                                                class="absolute -top-2 -right-2 w-6 h-6 bg-red-600 hover:bg-red-500 text-white rounded-full text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-lg"
                                                title="削除">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <p class="text-[10px] text-gray-500 mt-1 truncate"><?php echo basename($img); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div id="gallery-preview" class="mb-4">
                                <p class="text-xs text-gray-500 py-4 text-center"><i class="fas fa-image mr-1"></i>ギャラリー画像はまだ登録されていません</p>
                            </div>
                        <?php endif; ?>

                        <!-- Upload Area -->
                        <div class="border-2 border-dashed border-gray-700 hover:border-primary/50 rounded-lg p-6 text-center transition-colors cursor-pointer"
                            onclick="document.getElementById('gallery-upload').click()">
                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-500 mb-2"></i>
                            <p class="text-sm text-gray-400">クリックして画像を追加</p>
                            <p class="text-[10px] text-gray-600 mt-1">JPG, PNG, WebP（複数選択可）</p>
                            <input type="file" id="gallery-upload" name="gallery_files[]" multiple accept="image/*"
                                class="hidden" onchange="previewNewFiles(this)">
                        </div>

                        <!-- New uploads preview -->
                        <div id="new-uploads-preview" class="hidden mt-3">
                            <p class="text-xs text-green-400 mb-2"><i class="fas fa-plus-circle mr-1"></i>追加予定</p>
                            <div id="new-uploads-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"></div>
                        </div>

                        <!-- Manual Path Input (collapsible) -->
                        <details class="mt-3">
                            <summary class="text-xs text-gray-500 cursor-pointer hover:text-gray-300 transition-colors">
                                <i class="fas fa-code mr-1"></i>パスを直接入力する（上級者向け）
                            </summary>
                            <textarea id="gallery-paths-manual" class="form-input h-24 mt-2 text-xs font-mono"
                                placeholder="assets/images/example1.jpg&#10;assets/images/example2.jpg"
                                onchange="syncGalleryPaths()"><?php echo htmlspecialchars($gallery_lines); ?></textarea>
                        </details>
                    </div>

                    <script>
                    function removeGalleryImage(btn) {
                        const item = btn.closest('.gallery-item');
                        const path = item.dataset.path;
                        item.remove();
                        updateGalleryHiddenField();
                        // Update count text
                        const remaining = document.querySelectorAll('.gallery-item').length;
                        const countEl = document.querySelector('#gallery-preview')?.previousElementSibling;
                        if (countEl && countEl.querySelector('p')) {
                            if (remaining > 0) {
                                countEl.querySelector('p').innerHTML = '<i class="fas fa-images mr-1"></i>登録済み画像 (' + remaining + '枚)';
                            } else {
                                countEl.querySelector('p').innerHTML = '<i class="fas fa-image mr-1"></i>ギャラリー画像はまだ登録されていません';
                            }
                        }
                    }

                    function updateGalleryHiddenField() {
                        const items = document.querySelectorAll('.gallery-item');
                        const paths = Array.from(items).map(el => el.dataset.path);
                        document.getElementById('gallery-paths').value = paths.join('\n');
                        document.getElementById('gallery-paths-manual').value = paths.join('\n');
                    }

                    function syncGalleryPaths() {
                        document.getElementById('gallery-paths').value = document.getElementById('gallery-paths-manual').value;
                    }

                    function previewNewFiles(input) {
                        const container = document.getElementById('new-uploads-preview');
                        const grid = document.getElementById('new-uploads-grid');
                        if (input.files.length === 0) {
                            container.classList.add('hidden');
                            return;
                        }
                        container.classList.remove('hidden');
                        grid.innerHTML = '';
                        Array.from(input.files).forEach(file => {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                const div = document.createElement('div');
                                div.className = 'relative';
                                div.innerHTML = `
                                    <div class="aspect-[4/3] bg-gray-800 rounded-lg border border-green-700/50 overflow-hidden">
                                        <img src="${e.target.result}" class="w-full h-full object-cover">
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-1 truncate">${file.name}</p>
                                `;
                                grid.appendChild(div);
                            };
                            reader.readAsDataURL(file);
                        });
                    }
                    </script>
                </div>

                <!-- Specs -->
                <div>
                    <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-700 pb-2">スペック (Specs)</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="form-group">
                            <label class="form-label text-xs">PRICE</label>
                            <input type="text" name="spec_seat" class="form-input"
                                value="<?php echo htmlspecialchars(getJsonVal($work['specs'], 'seat')); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label text-xs">MATERIAL</label>
                            <input type="text" name="spec_material" class="form-input"
                                value="<?php echo htmlspecialchars(getJsonVal($work['specs'], 'material')); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label text-xs">COLOR</label>
                            <input type="text" name="spec_color" class="form-input"
                                value="<?php echo htmlspecialchars(getJsonVal($work['specs'], 'color')); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label text-xs">PERIOD</label>
                            <input type="text" name="spec_period" class="form-input"
                                value="<?php echo htmlspecialchars(getJsonVal($work['specs'], 'period')); ?>">
                        </div>
                    </div>
                </div>

                <!-- Data Info -->
                <div>
                    <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-700 pb-2">施工データ (Data)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label">CAR MODEL</label>
                            <input type="text" name="data_model" class="form-input"
                                value="<?php echo htmlspecialchars(getJsonVal($work['data_info'], 'model')); ?>"
                                placeholder="例: NISSAN GT-R (BNR32)">
                        </div>
                        <div class="form-group">
                            <label class="form-label">型式 (Model Code)</label>
                            <input type="text" name="data_model_code" class="form-input"
                                value="<?php echo htmlspecialchars(getJsonVal($work['data_info'], 'model_code')); ?>"
                                placeholder="例: BNR32">
                        </div>
                        <div class="form-group">
                            <label class="form-label">MATERIAL</label>
                            <input type="text" name="data_material" class="form-input"
                                value="<?php echo htmlspecialchars(getJsonVal($work['data_info'], 'material')); ?>"
                                placeholder="例: Genuine Leather / Fabric">
                        </div>
                        <div class="form-group">
                            <label class="form-label">PRICE</label>
                            <input type="text" name="data_price" class="form-input"
                                value="<?php echo htmlspecialchars(getJsonVal($work['data_info'], 'price')); ?>"
                                placeholder="例: ¥1,500,000 ~">
                        </div>
                        <div class="form-group md:col-span-2">
                            <label class="form-label">CONTENT (施工内容)</label>
                            <textarea name="data_content"
                                class="form-input h-24"
                                placeholder="例:&#10;全席シート張り替え&#10;ダッシュボード補修"><?php echo htmlspecialchars(getJsonVal($work['data_info'], 'content')); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="pt-6 border-t border-gray-700">
                    <button type="submit"
                        class="bg-primary hover:bg-yellow-500 text-black font-bold py-3 px-8 rounded transition-colors w-full md:w-auto tracking-widest font-en">
                        SAVE WORK
                    </button>
                </div>

            </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>