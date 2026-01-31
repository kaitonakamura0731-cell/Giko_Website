<?php
require_once 'includes/db.php';

$products_data = [
    [
        'name' => '【40系アルファード/ヴェルファイア専用】ステアリング',
        'price' => 66000,
        'shipping_fee' => 1000,
        'description' => '純正交換タイプ。最高級本革またはウルトラスエードから選択可能。純正では物足りない方に向けた、ハイエンドクラスです。',
        'compatible_models' => 'アルファード/ヴェルファイア',
        'model_code' => '3BA/5BA/6AA',
        'images' => json_encode([
            '../assets/images/items/steering_main.png',
            '../assets/images/items/steering_explanation.png',
            '../assets/images/items/steering_sub1.png'
        ], JSON_UNESCAPED_UNICODE),
        'options' => json_encode([
            [
                'label' => 'カラーA 【40AL/VEL-S】',
                'type' => 'select',
                'choices' => [
                    'sunset_brown' => 'サンセットブラウン',
                    'black_shibo' => 'ブラックシボ',
                    'black_smooth' => 'ブラックスムース'
                ]
            ],
            [
                'label' => 'カラーB 【40AL/VEL-S】',
                'type' => 'select',
                'choices' => [
                    'sunset_brown' => 'サンセットブラウン',
                    'black_shibo' => 'ブラックシボ',
                    'black_smooth' => 'ブラックスムース'
                ]
            ],
            [
                'label' => 'ステッチカラー',
                'type' => 'select',
                'choices' => ['純正カラー', '指定なし']
            ],
            [
                'label' => '下取り交換',
                'type' => 'select',
                'choices' => ['あり（注意事項を要確認）', 'なし (要別途費用)']
            ],
            [
                'label' => '脱着依頼',
                'type' => 'select',
                'choices' => ['なし', 'あり (要別途予約)']
            ]
        ], JSON_UNESCAPED_UNICODE),
        'stock_status' => 1
    ],
    [
        'name' => '【40系アルファード/ヴェルファイア専用】ナビカバー',
        'price' => 38500,
        'shipping_fee' => 1000,
        'description' => 'インテリアの質感を高める専用設計ナビカバー。上質な空間を演出。',
        'compatible_models' => 'アルファード/ヴェルファイア',
        'model_code' => '3BA/5BA/6AA',
        'images' => json_encode([
            '../assets/images/items/navicover_main.png',
            '../assets/images/items/navicover_sub1.png'
        ], JSON_UNESCAPED_UNICODE),
        'options' => json_encode([
            [
                'label' => 'カラー',
                'type' => 'select',
                'choices' => [
                    'sunset_brown' => 'サンセットブラウン',
                    'black' => 'ブラック'
                ]
            ],
            [
                'label' => 'ステッチカラー',
                'type' => 'select',
                'choices' => ['純正カラー', 'シルバー', 'ブラック', 'レッド']
            ],
            [
                'label' => '下取り交換',
                'type' => 'select',
                'choices' => ['あり（注意事項を要確認）', 'なし (要別途費用)']
            ],
            [
                'label' => '脱着依頼',
                'type' => 'select',
                'choices' => ['なし', 'あり (要別途予約)']
            ]
        ], JSON_UNESCAPED_UNICODE),
        'stock_status' => 1
    ]
];

try {
    // Reset table
    $pdo->exec("TRUNCATE TABLE products");
    echo "Table truncated.<br>";

    $stmt = $pdo->prepare("INSERT INTO products (name, price, shipping_fee, description, compatible_models, model_code, images, options, stock_status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    foreach ($products_data as $p) {
        $stmt->execute([
            $p['name'],
            $p['price'],
            $p['shipping_fee'],
            $p['description'],
            $p['compatible_models'],
            $p['model_code'],
            $p['images'],
            $p['options'],
            $p['stock_status']
        ]);
        echo "Imported: " . $p['name'] . "<br>";
    }

    echo "Migration Completed Successfully.<br>";
    echo "<a href='index.php'>Go to Dashboard</a>";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
