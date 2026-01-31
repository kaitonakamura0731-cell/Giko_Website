<?php
require_once 'includes/db.php';

// Works Data
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
        'hero_image' => '../assets/images/gtr32/GTR32_TOP.jpg',
        'description' => 'Legendary Sports Car Interior Restoration. Reviving the original quality.',
        'concept_text' => '伝説の名車、R32 GT-Rのインテリアを当時の質感そのままに復元。経年劣化したシートや内張りを、オリジナルに近い質感のレザーとファブリックで張り替えました。',
        'specs' => json_encode(['seat' => 'Restoration', 'material' => 'Original Style Fabric', 'color' => 'Black / Grey', 'period' => '4 Weeks'], JSON_UNESCAPED_UNICODE),
        'data_info' => json_encode(['model' => 'NISSAN GT-R (BNR32)', 'menu' => 'Full Restoration', 'material' => 'Genuine Leather / Fabric', 'price' => '¥1,500,000 ~', 'content' => "全席シート張り替え\nダッシュボード補修\nドアトリム張り替え\n天井張替え"], JSON_UNESCAPED_UNICODE),
        'gallery_images' => json_encode([
            '../assets/images/gtr32/GTR32_Seat.jpg',
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

// Products Data
$products_data = [
    [
        'name' => '【40系アルファード/ヴェルファイア専用】ステアリング',
        'price' => 66000,
        'shipping_fee' => 1000,
        'description' => '<p>
                            40系アルファード/ヴェルファイア専用のレザーパッケージです。<br>
                            純正では物足りない方に向けた、ハイエンドクラスです。<br>
                            生地は本革からウルトラスエードを選択可能。
                        </p>

                        <div class="space-y-8 mb-12 mt-8">
                            <div>
                                <h3 class="font-bold text-white mb-2 border-l-2 border-primary pl-3">商品概要</h3>
                                <p class="mb-4">
                                    <span class="text-primary">☆専用設計</span><br>
                                    純正交換が可能なので、スムーズに取り付け可能<br>
                                    純正ステアリングを施工するため、ハンドルヒーター/ステアリングスイッチはそのままお使い頂けます。
                                </p>
                                <p class="mb-4">
                                    <span class="text-primary">☆デザイン</span><br>
                                    シンプルかつ個性を表現できる洗練されたデザインに仕立てています。難燃性の素材にこだわり耐久性にも優れています！カラーバリエーションも豊富なので、お好みに合わせて選べる楽しさがあります。
                                </p>
                                <p>
                                    <span class="text-primary">☆付属部品</span><br>
                                    ステアリングのスポーク部分のみの販売になります。<br>
                                    ※ステアリング裏のカバーや木目パネルは付属しません。
                                </p>
                            </div>

                            <div>
                                <h3 class="font-bold text-white mb-2 border-l-2 border-primary pl-3">本革使用に伴う注意事項</h3>
                                <p class="text-xs text-gray-400 leading-relaxed">
                                    本製品はイタリア産の天然皮革（本革）を使用しております。<br>
                                    天然皮革ならではの特徴として、<br>
                                    ・シワや血筋<br>
                                    ・小さな傷やニキビ跡<br>
                                    ・色味やシボ感の個体差<br>
                                    が見られる場合がございます。<br>
                                    ※できる限り傷や表面状態の良い部分を選んで使用しておりますが、部品に沿って張り込む工程では革を伸ばす必要があり、その過程でニキビ跡など革本来の表情が現れる場合がございます。<br>
                                    天然素材ならではの特性として、ご理解頂けますと幸いです。
                                </p>
                            </div>

                            <div>
                                <h3 class="font-bold text-white mb-2 border-l-2 border-primary pl-3">メッキパーツについて</h3>
                                <p class="text-xs text-gray-400">
                                    画像にあるメッキパーツはオプションの”塗装”を追加した状態になります。<br>
                                    オプション追加がない場合は純正同様シルバーメッキを装着しての納品になります。
                                </p>
                            </div>

                            <div>
                                <h3 class="font-bold text-white mb-2 border-l-2 border-primary pl-3">下取り交換に関する注意事項</h3>
                                <p class="text-xs text-gray-400 leading-relaxed">
                                    下取り交換とは、本製品とお客様のお手元にある中古部品を交換するサービスになります。<br>
                                    下取り交換を選択されたお客様は、本製品が届いてから１週間以内に中古部品を下記住所までお送り頂きますようご協力お願い致します。<br>
                                    万が一ご返却が確認できない場合や、正当な理由なく返却に応じて頂けない場合には、法的措置を含む対応を取らせて頂く場合がございますので、予めご了承下さい。
                                </p>
                                <div class="mt-2 text-xs text-gray-400 bg-white/5 p-3 rounded-sm">
                                    <span class="block font-bold text-white mb-1">【下取り品返却住所】</span>
                                    〒483-8013<br>
                                    愛知県江南市般若町南山307<br>
                                    GIKO307合同会社<br>
                                    0587-22-7344
                                </div>
                            </div>
                        </div>

                        <!-- Parts Explanation -->
                        <div class="mb-8">
                            <p class="text-xs font-bold font-en tracking-widest text-gray-500 mb-2">パーツ解説</p>
                            <div class="bg-white/5 rounded-sm p-4 border border-white/10">
                                <img src="../assets/images/items/steering_explanation.png"
                                    alt="Steering Parts Explanation" class="w-full h-auto rounded-sm">
                            </div>
                        </div>',
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
        'description' => "40系アルファード/ヴェルファイア専用のレザーパッケージです。\n純正では物足りない方に向けた、ハイエンドクラスです。\n生地は本革からウルトラスエードを選択可能。\n\n【商品概要】\n☆専用設計\n純正交換が可能なので、スムーズに取り付け可能\n\n☆デザイン\nシンプルかつ個性を表現できる洗練されたデザインに仕立てています。難燃性の素材にこだわり耐久性にも優れています！カラーバリエーションも豊富なので、お好みに合わせて選べる楽しさがあります。\n\n☆取付方法\nナビカバーの脱着にはナビを外す必要があります。\n\n【本革使用に伴う注意事項】\n本製品はイタリア産の天然皮革（本革）を使用しております。\n天然皮革ならではの特徴として、\n・シワや血筋\n・小さな傷やニキビ跡\n・色味やシボ感の個体差\nが見られる場合がございます。\n※できる限り傷や表面状態の良い部分を選んで使用しておりますが、部品に沿って張り込む工程では革を伸ばす必要があり、その過程でニキビ跡など革本来の表情が現れる場合がございます。\n天然素材ならではの特性として、ご理解頂けますと幸いです。\n\n【下取り交換に関する注意事項】\n下取り交換とは、本製品とお客様のお手元にある中古部品を交換するサービスになります。\n下取り交換を選択されたお客様は、本製品が届いてから１週間以内に中古部品を下記住所までお送り頂きますようご協力お願い致します。\n万が一ご返却が確認できない場合や、正当な理由なく返却に応じて頂けない場合には、法的措置を含む対応を取らせて頂く場合がございますので、予めご了承下さい。\n\n【下取り品返却住所】\n〒483-8013\n愛知県江南市般若町南山307\nGIKO307合同会社\n0587-22-7344",
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
    // -----------------------------------------------------
    // WORKS MIGRATION
    // -----------------------------------------------------
    $pdo->exec("TRUNCATE TABLE works");
    echo "<h3>WORKS Table Truncated.</h3>";

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
        echo "Works Imported: " . $work['title'] . "<br>";
    }

    // -----------------------------------------------------
    // PRODUCTS MIGRATION
    // -----------------------------------------------------
    $pdo->exec("TRUNCATE TABLE products");
    echo "<h3>PRODUCTS Table Truncated.</h3>";

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
        echo "Products Imported: " . $p['name'] . "<br>";
    }

    echo "<hr><strong>All Migration Completed Successfully.</strong><br>";
    echo "<a href='index.php'>Go to Dashboard</a>";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
