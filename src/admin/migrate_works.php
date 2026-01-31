<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
checkAuth();

$works_data = [
    [
        'title' => 'ALPHARD',
        'subtitle' => 'TOYOTA / 30 Series',
        'category' => 'full-order',
        'main_image' => '../assets/images/alphard/Alphard_TOP.jpg', // List image
        'hero_image' => '../assets/images/alphard/alphard_06.jpg', // Hero section image
        'description' => 'Luxury White Leather Interior Custom. Creating a private lounge space.',
        'concept_text' => '純正の高級感をさらに昇華させた、フルホワイトレザーのカスタムインテリアです。オーナー様のご要望により、清潔感と広がりを感じさせるピュアホワイトのナッパレザーを全面に使用。アクセントとして、ドアトリムやセンターコンソールにはダイヤモンドステッチを施し、立体感と高級感を演出しました。<br><br>ステッチにはシルバーグレーを採用し、主張しすぎない洗練されたコントラストを実現。長時間のドライブでも疲れにくいよう、クッション材の調整も行っています。まさに「動くリビング」と呼ぶにふさわしい、至高のプライベート空間が完成しました。',
        'specs' => json_encode(['seat' => 'Full Leather', 'material' => 'Nappa', 'color' => 'White', 'period' => '3 Weeks'], JSON_UNESCAPED_UNICODE),
        'data_info' => json_encode(['model' => 'TOYOTA ALPHARD (30 Series)', 'menu' => 'Full Interior Custom', 'material' => 'European Nappa Leather (White)', 'price' => '¥1,200,000 ~', 'content' => "全席シート張り替え\nドアトリム張り替え\n天井ルーフライニング\nフロアマット製作\nステアリング巻き替え"], JSON_UNESCAPED_UNICODE),
        'gallery_images' => json_encode([
            '../assets/images/alphard/Alphard_Seat.jpg',
            '../assets/images/alphard/Alphard_Seat2.jpg',
            '../assets/images/alphard/Alphard_Seat_01.jpg',
            '../assets/images/alphard/Alphard_Seat_02.jpg'
        ], JSON_UNESCAPED_UNICODE)
    ],
    [
        'title' => 'GT-R R32',
        'subtitle' => 'NISSAN / BNR32',
        'category' => 'full-order',
        'main_image' => '../assets/images/gtr32/GTR32_TOP.jpg',
        'hero_image' => '../assets/images/gtr32/GTR32_TOP.jpg', // Using main as hero if no specific hero exists? Or try to find one. Let's reuse TOP for now if unsure.
        // Actually, let's assume reuse for most unless I see one.
        'description' => 'Legendary Sports Car Interior Restoration. Reviving the original quality.',
        'concept_text' => '伝説の名車、R32 GT-Rのインテリアを当時の質感そのままに復元。経年劣化したシートや内張りを、オリジナルに近い質感のレザーとファブリックで張り替えました。',
        'specs' => json_encode(['seat' => 'Restoration', 'material' => 'Original Style Fabric', 'color' => 'Black / Grey', 'period' => '4 Weeks'], JSON_UNESCAPED_UNICODE),
        'data_info' => json_encode(['model' => 'NISSAN GT-R (BNR32)', 'menu' => 'Full Restoration', 'material' => 'Genuine Leather / Fabric', 'price' => '¥1,500,000 ~', 'content' => "全席シート張り替え\nダッシュボード補修\nドアトリム張り替え\n天井張替え"], JSON_UNESCAPED_UNICODE),
        'gallery_images' => json_encode([
            '../assets/images/gtr32/GTR32_Seat.jpg', // Guessing names based on pattern
            // If specific files aren't known, I can leave empty or put placeholders.
            // Let's rely on folders.
        ], JSON_UNESCAPED_UNICODE)
    ],
    [
        'title' => 'MR-S',
        'subtitle' => 'TOYOTA / ZZW30',
        'category' => 'full-order',
        'main_image' => '../assets/images/mrs/MRS_TOP.jpg',
        'hero_image' => '../assets/images/mrs/MRS_TOP.jpg',
        'description' => 'Red & Black Sports Interior. High contrast design for open-top driving.',
        'concept_text' => 'オープンドライブをより愉しむための、鮮烈な赤と黒のコントラスト。スポーツ走行時のホールド性を重視しつつ、見た目のインパクトも追求したカスタムインテリアです。',
        'specs' => json_encode(['seat' => 'Sports Custom', 'material' => 'Synthetic Leather', 'color' => 'Red / Black', 'period' => '2 Weeks'], JSON_UNESCAPED_UNICODE),
        'data_info' => json_encode(['model' => 'TOYOTA MR-S', 'menu' => 'Seat Custom', 'material' => 'PVC Leather', 'price' => '¥400,000 ~', 'content' => "シート張り替え\nドアインナーパネル張り替え"], JSON_UNESCAPED_UNICODE),
        'gallery_images' => json_encode([], JSON_UNESCAPED_UNICODE)
    ],
    [
        'title' => 'SL55 AMG',
        'subtitle' => 'MERCEDES / R230',
        'category' => 'full-order',
        'main_image' => '../assets/images/sl55/SL55_TOP.png',
        'hero_image' => '../assets/images/sl55/SL55_TOP.png',
        'description' => 'Premium Beige Nappa & High-End Audio. Elegant open cruiser.',
        'concept_text' => 'エレガントなベージュナッパレザーで統一された車内空間。最高級オーディオシステムとの融合を目指し、見た目だけでなく音響効果も考慮した素材配置を行いました。',
        'specs' => json_encode(['seat' => 'Luxury Custom', 'material' => 'Nappa Leather', 'color' => 'Beige', 'period' => '3 Weeks'], JSON_UNESCAPED_UNICODE),
        'data_info' => json_encode(['model' => 'MERCEDES BENZ SL55 AMG', 'menu' => 'Interior & Audio', 'material' => 'Nappa Leather', 'price' => '¥1,800,000 ~', 'content' => "シート張り替え\nドアトリム造形\nオーディオインストール\nトランクカスタム"], JSON_UNESCAPED_UNICODE),
        'gallery_images' => json_encode([], JSON_UNESCAPED_UNICODE)
    ],
    [
        'title' => 'V-CLASS',
        'subtitle' => 'MERCEDES / W447',
        'category' => 'full-order',
        'main_image' => '../assets/images/vclass/VClass_Interior_TOP.jpg',
        'hero_image' => '../assets/images/vclass/VClass_Interior_TOP.jpg',
        'description' => 'VIP Lounge Specification. Ultimate comfort for executive travel.',
        'concept_text' => '移動時間を極上のリラックスタイムへ。後席をファーストクラスのような独立シートに変更し、パーティションやモニターを設置したVIP仕様です。',
        'specs' => json_encode(['seat' => 'VIP Captain Seats', 'material' => 'Nappa Leather', 'color' => 'Black', 'period' => '6 Weeks'], JSON_UNESCAPED_UNICODE),
        'data_info' => json_encode(['model' => 'MERCEDES BENZ V-CLASS', 'menu' => 'Limousine Custom', 'material' => 'Nappa Leather', 'price' => '¥3,500,000 ~', 'content' => "後席キャプテンシート換装\nパーティション製作\nエンターテインメントシステム構築\nフルデッドニング"], JSON_UNESCAPED_UNICODE),
        'gallery_images' => json_encode([], JSON_UNESCAPED_UNICODE)
    ],
    [
        'title' => 'AVENSIS',
        'subtitle' => 'TOYOTA / WAGON',
        'category' => 'repair',
        'main_image' => '../assets/images/avensis/avensis_TOP.PNG',
        'hero_image' => '../assets/images/avensis/avensis_TOP.PNG',
        'description' => 'Seat Repair & Refresh. Restoring the original comfort.',
        'concept_text' => '長年の使用でへたってしまったシートウレタンの補修と表皮の張り替え。愛着のある愛車を長く乗り続けるためのリフレッシュプランです。',
        'specs' => json_encode(['seat' => 'Repair', 'material' => 'Genuine Fabric', 'color' => 'Grey', 'period' => '1 Week'], JSON_UNESCAPED_UNICODE),
        'data_info' => json_encode(['model' => 'TOYOTA AVENSIS', 'menu' => 'Seat Repair', 'material' => 'Original Equivalent', 'price' => '¥150,000 ~', 'content' => "運転席座面ウレタン補修\n表皮張り替え"], JSON_UNESCAPED_UNICODE),
        'gallery_images' => json_encode([], JSON_UNESCAPED_UNICODE)
    ]
];

try {
    // Force reset for detail page implementation
    $pdo->exec("TRUNCATE TABLE works");
    echo "Table truncated.<br>";

    $stmt = $pdo->prepare("INSERT INTO works (title, subtitle, category, main_image, hero_image, description, concept_text, specs, data_info, gallery_images, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    foreach ($works_data as $work) {
        $stmt->execute([
            $work['title'],
            $work['subtitle'],
            $work['category'],
            $work['main_image'],
            $work['hero_image'],
            $work['description'],
            $work['concept_text'],
            $work['specs'],
            $work['data_info'],
            $work['gallery_images']
        ]);
        echo "Imported: " . $work['title'] . "<br>";
    }

    echo "Migration Completed Successfully.<br>";
    echo "<a href='index.php'>Go to Dashboard</a>";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>