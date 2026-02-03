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
    'category' => 'full-order',
    'main_image' => '',
    'description' => '',
    'concept_text' => '',
    'specs' => json_encode(['seat' => '', 'material' => '', 'color' => '', 'period' => ''], JSON_UNESCAPED_UNICODE),
    'data_info' => json_encode(['model' => '', 'menu' => '', 'material' => '', 'content' => '', 'price' => ''], JSON_UNESCAPED_UNICODE)
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
        'menu' => $_POST['data_menu'] ?? '',
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
                        <select name="category" class="form-input">
                            <option value="full-order" <?php if ($work['category'] == 'full-order')
                                echo 'selected'; ?>>
                                Full Order</option>
                            <option value="semi-order" <?php if ($work['category'] == 'semi-order')
                                echo 'selected'; ?>>
                                Semi Order</option>
                            <option value="repair" <?php if ($work['category'] == 'repair')
                                echo 'selected'; ?>>Repair
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Images & Description -->
            <div>
                <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-700 pb-2">画像・説明</h3>
                <div class="form-group mb-4">
                    <label class="form-label">メイン画像 (Main Image)</label>
                    <div class="flex flex-col gap-2">
                        <!-- Preview -->
                        <?php if ($work['main_image']): ?>
                            <div class="mb-2">
                                <img src="<?php echo '../../' . htmlspecialchars($work['main_image']); ?>"
                                    class="h-32 object-cover border border-gray-600 rounded">
                            </div>
                        <?php endif; ?>

                        <input type="text" name="main_image" class="form-input text-xs text-gray-500"
                            value="<?php echo htmlspecialchars($work['main_image']); ?>"
                            placeholder="assets/images/...">

                        <div class="mt-2">
                            <label class="text-xs text-gray-400 mb-1 block">新規アップロード:</label>
                            <input type="file" name="main_image_file" class="text-gray-300 text-sm">
                        </div>
                    </div>
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
                        <p class="text-xs text-gray-500 mb-2">※1行に1つの画像パスを入力（assets/...）</p>
                        <?php
                        $gallery_lines = '';
                        $gallery_json = $work['gallery_images'] ?? '[]';
                        $gallery_arr = json_decode($gallery_json, true);
                        if (is_array($gallery_arr)) {
                            $gallery_lines = implode("\n", $gallery_arr);
                        }
                        ?>
                        <textarea name="gallery_images" class="form-input h-32 mb-2"
                            placeholder="assets/images/example1.jpg&#10;assets/images/example2.jpg"><?php echo htmlspecialchars($gallery_lines); ?></textarea>

                        <div class="mt-4 p-4 bg-gray-800 rounded border border-gray-700">
                            <label class="text-sm font-bold text-gray-300 mb-2 block">画像追加アップロード (複数可)</label>
                            <input type="file" name="gallery_files[]" multiple class="text-gray-300 text-sm w-full">
                        </div>

                        <!-- Gallery Preview -->
                        <?php if (is_array($gallery_arr) && count($gallery_arr) > 0): ?>
                            <div class="mt-4 grid grid-cols-4 gap-2">
                                <?php foreach ($gallery_arr as $img): ?>
                                    <a href="<?php echo '../../' . htmlspecialchars($img); ?>" target="_blank"
                                        class="block border border-gray-600 rounded overflow-hidden hover:opacity-75 transition-opacity">
                                        <img src="<?php echo '../../' . htmlspecialchars($img); ?>"
                                            class="w-full h-20 object-cover">
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Specs -->
                <div>
                    <h3 class="text-lg font-bold text-primary mb-4 border-b border-gray-700 pb-2">スペック (Specs)</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="form-group">
                            <label class="form-label text-xs">SEAT</label>
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
                                value="<?php echo htmlspecialchars(getJsonVal($work['data_info'], 'model')); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">MENU</label>
                            <input type="text" name="data_menu" class="form-input"
                                value="<?php echo htmlspecialchars(getJsonVal($work['data_info'], 'menu')); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">MATERIAL Details</label>
                            <input type="text" name="data_material" class="form-input"
                                value="<?php echo htmlspecialchars(getJsonVal($work['data_info'], 'material')); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">PRICE</label>
                            <input type="text" name="data_price" class="form-input"
                                value="<?php echo htmlspecialchars(getJsonVal($work['data_info'], 'price')); ?>">
                        </div>
                        <div class="form-group md:col-span-2">
                            <label class="form-label">CONTENT (施工内容)</label>
                            <textarea name="data_content"
                                class="form-input h-24"><?php echo htmlspecialchars(getJsonVal($work['data_info'], 'content')); ?></textarea>
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