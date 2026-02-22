<?php
session_start();

// Generate CSRF Token for future use or validation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="ja" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHECKOUT | 技巧 -Giko-</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../tailwind_config.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="../assets/js/cart.js"></script>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-black text-white antialiased selection:bg-primary selection:text-white">

    <!-- Header -->
    <header class="fixed w-full z-50 transition-all duration-300 bg-black/80 backdrop-blur-md border-b border-white/5"
        id="header">
        <div class="container mx-auto px-6 h-20 flex justify-between items-center">
            <a href="../index.php" class="flex items-center group">
                <img src="../assets/images/logo_new.png" alt="GIKO" class="h-10 group-hover:opacity-80 transition-opacity">
            </a>

            <div class="flex items-center gap-6">
                <a href="../store/cart.html"
                    class="text-xs font-bold font-en tracking-widest hover:text-primary transition-colors flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> BACK TO CART
                </a>
            </div>
        </div>
    </header>

    <!-- Page Title Hero -->
    <section class="relative pt-32 pb-12 bg-secondary/30">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-3xl md:text-5xl font-bold font-en tracking-widest mb-4">CHECKOUT</h1>
            <p class="text-gray-400 text-xs md:text-sm tracking-widest font-en uppercase">決済・配送情報の入力</p>
        </div>
    </section>

    <main class="container mx-auto px-6 py-12 max-w-6xl">

        <!-- Error Message Display -->
        <div id="error-message"
            class="hidden bg-red-900/20 border border-red-500/50 text-red-200 p-6 mb-12 text-sm rounded-sm backdrop-blur-sm flex items-start gap-4 animate-pulse">
            <i class="fas fa-exclamation-circle text-xl mt-0.5"></i>
            <div>
                <h3 class="font-bold mb-1">PAYMENT ERROR</h3>
                <p id="error-text"></p>
            </div>
        </div>

        <form action="order_complete.html" method="GET" id="checkout-form">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token"
                value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                <!-- Left Column: Form (Span 7) -->
                <div class="lg:col-span-7 space-y-12 order-2 lg:order-1">
                    <!-- Shipping Info -->
                    <section>
                        <div class="flex items-center gap-4 mb-8">
                            <div
                                class="w-8 h-8 rounded-full bg-primary text-black flex items-center justify-center font-bold font-en">
                                1</div>
                            <h2 class="text-xl font-bold font-en tracking-widest text-white">SHIPPING INFO</h2>
                        </div>

                        <div class="bg-white/5 backdrop-blur-md p-8 border border-white/10 rounded-sm space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-xs font-bold font-en tracking-widest mb-2 text-gray-400">NAME
                                        <span class="text-primary">*</span></label>
                                    <input type="text" name="name" required
                                        class="w-full bg-black border border-white/10 px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all duration-300 text-white placeholder-gray-700"
                                        placeholder="山田 太郎">
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold font-en tracking-widest mb-2 text-gray-400">EMAIL
                                        <span class="text-primary">*</span></label>
                                    <input type="email" name="email" required
                                        class="w-full bg-black border border-white/10 px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all duration-300 text-white placeholder-gray-700"
                                        placeholder="example@giko.jp">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold font-en tracking-widest mb-2 text-gray-400">PHONE
                                    <span class="text-primary">*</span></label>
                                <input type="tel" name="phone" required
                                    class="w-full bg-black border border-white/10 px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all duration-300 text-white placeholder-gray-700"
                                    placeholder="090-1234-5678">
                            </div>

                            <div class="grid grid-cols-12 gap-6">
                                <div class="col-span-4">
                                    <label
                                        class="block text-xs font-bold font-en tracking-widest mb-2 text-gray-400">POSTAL
                                        CODE <span class="text-primary">*</span></label>
                                    <input type="text" name="zip" required
                                        class="w-full bg-black border border-white/10 px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all duration-300 text-white placeholder-gray-700"
                                        placeholder="123-4567">
                                </div>
                                <div class="col-span-8">
                                    <label
                                        class="block text-xs font-bold font-en tracking-widest mb-2 text-gray-400">ADDRESS
                                        <span class="text-primary">*</span></label>
                                    <input type="text" name="address" required
                                        class="w-full bg-black border border-white/10 px-4 py-3 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all duration-300 text-white placeholder-gray-700"
                                        placeholder="都道府県 市区町村 番地">
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 購入後フロー -->
                    <section>
                        <div class="flex items-center gap-4 mb-8">
                            <div
                                class="w-8 h-8 rounded-full bg-white/20 text-white flex items-center justify-center font-bold font-en">
                                2</div>
                            <h2 class="text-xl font-bold font-en tracking-widest text-white">購入後フロー</h2>
                        </div>

                        <div class="bg-white/5 backdrop-blur-md p-8 border border-white/10 rounded-sm space-y-8">
                            <!-- ステップ1: 商品発送 -->
                            <div class="flex gap-4 items-start">
                                <div class="w-10 h-10 rounded-full bg-primary/20 border border-primary/50 flex items-center justify-center flex-shrink-0">
                                    <span class="text-primary font-bold font-en">1</span>
                                </div>
                                <div>
                                    <h3 class="font-bold text-white mb-1">購入後、商品送信</h3>
                                    <p class="text-sm text-gray-400">ご購入確定後、技巧より商品をお客様のご住所へ発送いたします。</p>
                                </div>
                            </div>

                            <!-- ステップ2: 脱着 -->
                            <div class="flex gap-4 items-start">
                                <div class="w-10 h-10 rounded-full bg-primary/20 border border-primary/50 flex items-center justify-center flex-shrink-0">
                                    <span class="text-primary font-bold font-en">2</span>
                                </div>
                                <div>
                                    <h3 class="font-bold text-white mb-1">脱着</h3>
                                    <p class="text-sm text-gray-400 mb-2">商品が届きましたら、お客様ご自身で現在のパーツを取り外し、届いた商品を取り付けてください。</p>
                                    <p class="text-sm text-gray-400">
                                        脱着作業を依頼したい場合は
                                        <button type="button" onclick="openInstallModal()" class="text-primary font-bold underline underline-offset-4 hover:text-white transition-colors">こちら</button>
                                    </p>
                                </div>
                            </div>

                            <!-- ステップ3: 取り外した部品の返送 -->
                            <div class="flex gap-4 items-start">
                                <div class="w-10 h-10 rounded-full bg-primary/20 border border-primary/50 flex items-center justify-center flex-shrink-0">
                                    <span class="text-primary font-bold font-en">3</span>
                                </div>
                                <div>
                                    <h3 class="font-bold text-white mb-2">取り外した部品の返送</h3>
                                    <div class="space-y-3">
                                        <div class="bg-black/40 border border-white/10 p-4 rounded-sm">
                                            <div class="flex items-center gap-2 mb-1">
                                                <i class="fas fa-exchange-alt text-primary text-sm"></i>
                                                <span class="font-bold text-sm text-white">返送（下取り）する場合</span>
                                            </div>
                                            <p class="text-xs text-gray-400 ml-6">取り外したパーツを技巧へ返送いただくと、下取り買取として商品代が割引になります。</p>
                                        </div>
                                        <div class="bg-black/40 border border-white/10 p-4 rounded-sm">
                                            <div class="flex items-center gap-2 mb-1">
                                                <i class="fas fa-box text-gray-400 text-sm"></i>
                                                <span class="font-bold text-sm text-white">返送しない場合</span>
                                            </div>
                                            <p class="text-xs text-gray-400 ml-6">取り外したパーツをお手元に残される場合は、下取り割引は適用されません。</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 注意事項 -->
                            <div class="border-t border-white/10 pt-6">
                                <h4 class="text-sm font-bold text-yellow-400 mb-3"><i class="fas fa-exclamation-triangle mr-1"></i> 注意事項</h4>
                                <p class="text-sm text-gray-300 mb-4">取り外した部品を返送する場合は、商品到着後 <span class="text-primary font-bold">2週間以内</span> にご返送をお願いいたします。</p>

                                <label class="flex items-start gap-3 cursor-pointer group" id="confirm-flow-label">
                                    <input type="checkbox" id="confirm-flow-checkbox"
                                        class="mt-1 w-5 h-5 rounded border-2 border-white/30 bg-transparent accent-primary cursor-pointer flex-shrink-0"
                                        onchange="togglePaymentButtons()">
                                    <span class="text-sm text-gray-300 group-hover:text-white transition-colors">
                                        上記の購入後フローおよび注意事項を確認しました。<br>
                                        <span class="text-[11px] text-gray-500">※取り外した部品がない場合・返送しない場合もチェックしてください</span>
                                    </span>
                                </label>
                                <p id="checkbox-warning" class="text-xs text-red-400 mt-2 ml-8 hidden">
                                    <i class="fas fa-info-circle mr-1"></i> チェックしないと決済ボタンが押せません
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Payment Method -->
                    <section>
                        <div class="flex items-center gap-4 mb-8">
                            <div
                                class="w-8 h-8 rounded-full bg-white/20 text-white flex items-center justify-center font-bold font-en">
                                3</div>
                            <h2 class="text-xl font-bold font-en tracking-widest text-white">PAYMENT METHOD</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="payment-method-container">
                            <!-- Card Option -->
                            <label class="relative cursor-pointer group payment-option-label" style="pointer-events: none; opacity: 0.4;">
                                <input type="radio" name="payment_method" value="card" checked
                                    onchange="togglePayment('card')" class="peer sr-only">
                                <div
                                    class="bg-white/5 border border-white/10 p-6 rounded-sm h-full peer-checked:border-primary peer-checked:bg-primary/5 transition-all duration-300 hover:bg-white/10 peer-checked:[&_.indicator-circle]:border-primary peer-checked:[&_.indicator-circle]:bg-primary peer-checked:[&_.indicator-circle]:shadow-[0_0_15px_#0055FF] peer-checked:[&_.indicator-icon]:text-primary">
                                    <div class="flex justify-between items-start mb-4">
                                        <div
                                            class="indicator-circle w-4 h-4 rounded-full border border-gray-500 relative transition-all duration-300">
                                        </div>
                                        <i
                                            class="indicator-icon fas fa-credit-card text-2xl text-gray-400 transition-colors"></i>
                                    </div>
                                    <div class="font-bold tracking-wide mb-1">クレジットカード</div>
                                    <p class="text-[10px] text-gray-400 font-en">VISA, Master, JCB, AMEX, Diners</p>
                                </div>
                            </label>

                            <!-- Transfer Option -->
                            <label class="relative cursor-pointer group payment-option-label" style="pointer-events: none; opacity: 0.4;">
                                <input type="radio" name="payment_method" value="transfer"
                                    onchange="togglePayment('transfer')" class="peer sr-only">
                                <div
                                    class="bg-white/5 border border-white/10 p-6 rounded-sm h-full peer-checked:border-primary peer-checked:bg-primary/5 transition-all duration-300 hover:bg-white/10 peer-checked:[&_.indicator-circle]:border-primary peer-checked:[&_.indicator-circle]:bg-primary peer-checked:[&_.indicator-circle]:shadow-[0_0_15px_#0055FF] peer-checked:[&_.indicator-icon]:text-primary">
                                    <div class="absolute -top-4 -right-4 z-20">
                                        <div
                                            class="relative bg-[#FFD700] border-2 border-[#FFFF00] text-black text-xs font-black px-4 py-1.5 rounded-full shadow-[0_0_25px_#FFD700] overflow-hidden transform hover:scale-110 transition-transform duration-300">
                                            <div
                                                class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-transparent via-white to-transparent -translate-x-full animate-[shimmer_1s_infinite]">
                                            </div>
                                            <span class="relative z-10 flex items-center gap-1 tracking-tighter">
                                                <i class="fas fa-bolt text-black animate-pulse"></i>
                                                <span class="italic">送料無料</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-start mb-4">
                                        <div
                                            class="indicator-circle w-4 h-4 rounded-full border border-gray-500 relative transition-all duration-300">
                                        </div>
                                        <i
                                            class="indicator-icon fas fa-university text-2xl text-gray-400 transition-colors"></i>
                                    </div>
                                    <div class="font-bold tracking-wide mb-1">銀行振込</div>
                                    <p class="text-[10px] text-gray-400">銀行振込（前払い）</p>
                                    <p class="text-xs text-primary font-bold mt-2">
                                        <i class="fas fa-truck-fast mr-1"></i> 送料無料対象
                                    </p>
                                </div>
                            </label>
                        </div>
                    </section>
                </div>

                <!-- Right Column: Order Summary (Span 5) -->
                <div class="lg:col-span-5 lg:sticky lg:top-32 h-fit order-1 lg:order-2">
                    <div class="bg-secondary p-8 border border-white/10 rounded-sm relative overflow-hidden shadow-2xl">
                        <div class="absolute -top-20 -right-20 w-60 h-60 bg-primary/10 rounded-full blur-[80px]"></div>

                        <h2
                            class="text-sm font-bold text-gray-400 mb-8 tracking-widest border-b border-white/10 pb-4">
                            ご注文内容</h2>

                        <div id="checkout-items"
                            class="space-y-6 mb-8 max-h-[400px] overflow-y-auto pr-2 scrollbar-hide">
                            <!-- Items injected via JS -->
                        </div>

                        <div class="border-t border-white/10 pt-6 space-y-3">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-400 tracking-wider">小計（割引前）</span>
                                <span id="display-subtotal-before" class="font-en font-bold">¥0</span>
                            </div>
                            <div id="discount-row" class="flex justify-between items-center text-sm hidden">
                                <span class="text-green-400 tracking-wider"><i class="fas fa-tag mr-2"></i>下取り割引</span>
                                <span id="display-discount" class="font-en font-bold text-green-400">-¥0</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-400 tracking-wider">小計</span>
                                <span id="display-subtotal" class="font-en font-bold">¥0</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-400 tracking-wider">送料</span>
                                <span id="display-shipping" class="font-en font-bold">¥1,000</span>
                            </div>
                            <div class="flex justify-between items-end pt-4 border-t border-white/10 mt-4">
                                <span class="text-sm font-bold text-gray-400 tracking-widest">合計</span>
                                <span id="checkout-total"
                                    class="text-3xl font-bold text-primary font-en tracking-tighter">¥0</span>
                            </div>
                        </div>

                        <input type="hidden" name="amount" id="form-amount" value="">
                        <input type="hidden" name="cart_items" id="cart-items-input" value="">

                        <div class="mt-8">
                            <!-- Card Payment -->
                            <div id="payment-card" class="block space-y-6">
                                <div class="bg-white/5 border border-white/10 p-4 rounded-sm">
                                    <p class="text-sm text-gray-300">
                                        「決済へ進む」をクリック後、クレジットカード入力画面へ移動します。
                                    </p>
                                </div>

                                <button type="button" id="btn-card-pay" onclick="handleCardPayment()"
                                    class="w-full bg-[#C0A062] text-black font-bold py-4 rounded-sm hover:bg-white transition-all duration-300 tracking-widest font-en shadow-xl uppercase disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-[#C0A062]"
                                    disabled>
                                    決済へ進む
                                </button>
                                <p class="text-[10px] text-gray-500 text-center">
                                    <i class="fas fa-lock mr-1"></i> SSL通信と3Dセキュア認証で安全に保護されます
                                </p>
                            </div>

                            <!-- Transfer Payment -->
                            <div id="payment-transfer" class="hidden">
                                <button type="button" id="btn-transfer-pay" onclick="handleBankTransfer()"
                                    class="w-full bg-white text-black font-bold py-4 rounded-sm hover:bg-primary hover:text-white transition-all duration-300 tracking-widest font-en shadow-xl uppercase disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-black"
                                    disabled>
                                    注文を確定する
                                </button>
                                <p class="text-[10px] text-gray-500 mt-4 text-center">
                                    注文確定後、振込先をご案内します
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 text-center text-[10px] text-gray-600">
                        <a href="../legal/tokusho.html" target="_blank"
                            class="hover:text-gray-400 underline decoration-gray-600">特定商取引法に基づく表記</a>
                        <span class="mx-2">|</span>
                        <a href="../legal/privacy.html" target="_blank"
                            class="hover:text-gray-400 underline decoration-gray-600">プライバシーポリシー</a>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <!-- 脱着作業依頼ポップアップ -->
    <div id="install-modal"
        class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4"
        onclick="if(event.target===this)closeInstallModal()">
        <div class="bg-gray-900 border border-primary/30 rounded-sm max-w-md w-full shadow-2xl overflow-hidden transform transition-all">
            <!-- モーダルヘッダー -->
            <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 border-b border-primary/30 p-6 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white tracking-wide">
                    <i class="fas fa-wrench text-primary mr-2"></i>脱着作業依頼
                </h3>
                <button type="button" onclick="closeInstallModal()"
                    class="text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <!-- モーダル内容 -->
            <div class="p-8 space-y-6">
                <p class="text-sm text-gray-300 leading-relaxed">
                    脱着作業をご自身で行うことが難しい場合、技巧にて作業を承ります。<br>
                    以下の連絡先までお問い合わせください。
                </p>

                <!-- 連絡先 -->
                <div class="bg-black/50 border border-white/10 p-6 rounded-sm text-center">
                    <p class="text-xs text-gray-500 font-en tracking-widest mb-2">CONTACT</p>
                    <a href="mailto:giko.artisan@gmail.com"
                        class="text-primary font-bold text-lg hover:text-white transition-colors break-all">
                        <i class="fas fa-envelope mr-2"></i>giko.artisan@gmail.com
                    </a>
                </div>

                <!-- 対応可能地域 -->
                <div>
                    <p class="text-xs text-gray-500 font-en tracking-widest mb-3">対応可能地域</p>
                    <div class="flex gap-3 justify-center">
                        <span class="bg-primary/10 border border-primary/30 text-primary px-4 py-2 rounded-sm text-sm font-bold">
                            <i class="fas fa-map-marker-alt mr-1"></i> 東京
                        </span>
                        <span class="bg-primary/10 border border-primary/30 text-primary px-4 py-2 rounded-sm text-sm font-bold">
                            <i class="fas fa-map-marker-alt mr-1"></i> 北海道
                        </span>
                        <span class="bg-primary/10 border border-primary/30 text-primary px-4 py-2 rounded-sm text-sm font-bold">
                            <i class="fas fa-map-marker-alt mr-1"></i> 愛知
                        </span>
                    </div>
                </div>


            </div>
            <!-- モーダルフッター -->
            <div class="bg-gray-900 p-4 border-t border-white/10 text-center">
                <button type="button" onclick="closeInstallModal()"
                    class="px-8 py-3 bg-white/10 border border-white/20 text-white font-bold tracking-widest font-en hover:bg-primary hover:border-primary transition-all duration-300 rounded-sm text-sm">
                    閉じる
                </button>
            </div>
        </div>
    </div>

    <div id="loading-overlay"
        class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] hidden flex items-center justify-center">
        <div class="text-center">
            <div class="w-16 h-16 border-4 border-white/20 border-t-primary rounded-full animate-spin mx-auto mb-4">
            </div>
            <p class="text-white font-en tracking-widest animate-pulse">PROCESSING...</p>
            <p class="text-xs text-gray-400 mt-2">決済処理中...画面を閉じないでください</p>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmation-modal"
        class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm">
        <div
            class="bg-gray-900 border border-gold-500/30 rounded-lg max-w-2xl w-full shadow-2xl overflow-hidden transform transition-all scale-100">
            <!-- Modal Header -->
            <div
                class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 border-b border-gold-500/30 p-6 flex justify-between items-center">
                <h3 class="text-2xl font-serif text-white tracking-wider"><span class="text-gold-400">❖</span> 注文内容の確認
                </h3>
                <button type="button" onclick="closeConfirmationModal()"
                    class="text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h4 class="text-gold-400 font-serif mb-4 border-b border-gray-700 pb-2">お届け先情報</h4>
                        <div id="confirm-shipping-info" class="space-y-2 text-gray-300 text-sm">
                        </div>
                    </div>
                    <div>
                        <h4 class="text-gold-400 font-serif mb-4 border-b border-gray-700 pb-2">注文商品</h4>
                        <div id="confirm-items-list" class="space-y-3">
                        </div>
                    </div>
                </div>

                <div class="mt-8 bg-gray-800/50 p-4 rounded border border-gray-700">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-400">お支払い方法</span>
                        <span id="confirm-payment-method" class="text-white font-medium">クレジットカード</span>
                    </div>
                    <div
                        class="flex justify-between items-center text-xl font-serif border-t border-gray-700 pt-3 mt-2">
                        <span class="text-gold-400">お支払い合計</span>
                        <span id="confirm-total-amount" class="text-white font-bold tracking-wider">¥0</span>
                    </div>
                </div>

                <div class="mt-6 text-xs text-gray-500 text-center">
                    <p><i class="fas fa-lock text-gold-500/50 mr-1"></i> SSL暗号化通信により、お客様の情報は安全に送信されます。</p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-900 p-6 border-t border-gold-500/30 flex justify-end gap-4">
                <button type="button" onclick="closeConfirmationModal()"
                    class="px-6 py-3 rounded border border-gray-600 text-gray-400 hover:text-white hover:border-gray-400 transition-all font-serif">
                    修正する
                </button>
                <button type="button" onclick="processFinalPayment()"
                    class="px-8 py-3 rounded bg-gradient-to-r from-gold-600 to-gold-400 text-black font-bold shadow-lg hover:shadow-gold-500/30 hover:scale-[1.02] transition-all transform flex items-center gap-2">
                    <span>注文を確定する</span>
                    <i class="fas fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Init total amount
        let payjp, elements, cardElement;
        const PUBLIC_KEY = 'pk_test_445533d2d990496d0d9266f0';
        let currentPaymentMethod = 'card';

        document.addEventListener('DOMContentLoaded', () => {
            // 1. Sync Payment Method state with UI
            const checkedRadio = document.querySelector('input[name="payment_method"]:checked');
            if (checkedRadio) {
                currentPaymentMethod = checkedRadio.value;
                togglePayment(currentPaymentMethod);
            }

            const items = Cart.getItems();

            // Calculate subtotal and discount
            let subtotalBefore = 0;
            let totalDiscount = 0;
            items.forEach(item => {
                const originalPrice = typeof item.price === 'string' ? parseInt(item.price.replace(/,/g, '')) : item.price;
                const discount = Cart.getItemDiscount(item);
                subtotalBefore += originalPrice;
                totalDiscount += discount;
            });

            const subtotal = Cart.getTotal();

            if (subtotal === 0 && items.length === 0) {
                alert('カートが空です');
                window.location.href = 'purchase.html';
                return;
            }

            // Render items
            renderCartItems(items);

            // Display subtotal before discount
            document.getElementById('display-subtotal-before').innerText = '¥' + subtotalBefore.toLocaleString();

            // Display discount if applicable
            if (totalDiscount > 0) {
                document.getElementById('discount-row').classList.remove('hidden');
                document.getElementById('display-discount').innerText = '-¥' + totalDiscount.toLocaleString();
            }

            // Display subtotal after discount
            document.getElementById('display-subtotal').innerText = '¥' + subtotal.toLocaleString();
            document.getElementById('cart-items-input').value = JSON.stringify(items);

            // 合計を計算（送料込み）
            updateOrderTotal();

            // Check for error query param from 3DS redirect/callback
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            if (error) {
                showError(decodeURIComponent(error));
            }

            // チェックボックスの初期状態を反映
            togglePaymentButtons();

            // Initialize PAY.JP V2
            initPayjpV2();
        });

        function renderCartItems(items) {
            const itemsContainer = document.getElementById('checkout-items');
            let itemsHtml = '';
            items.forEach(item => {
                const discount = Cart.getItemDiscount(item);
                const finalPrice = Cart.getItemPrice(item);
                const originalPrice = typeof item.price === 'string' ? parseInt(item.price.replace(/,/g, '')) : item.price;
                const hasDiscount = discount > 0;

                itemsHtml += `
                     <div class="flex gap-4 items-start">
                          <div class="w-16 h-16 bg-white/5 rounded-sm flex-shrink-0 overflow-hidden border border-white/5">
                             ${item.image ? `<img src="${item.image}" class="w-full h-full object-cover">` : ''}
                         </div>
                         <div class="flex-1 min-w-0">
                             <h4 class="font-bold text-sm truncate pr-4">${item.name}</h4>
                             <p class="text-xs text-gray-400 mt-1 font-en">${Object.values(item.options || {}).join(' / ')}</p>
                             ${hasDiscount ? `<p class="text-xs text-green-400 mt-1"><i class="fas fa-tag mr-1"></i>下取り割引：-¥${discount.toLocaleString()}</p>` : ''}
                         </div>
                         <div class="text-right">
                             ${hasDiscount ? `
                                 <div class="text-xs text-gray-500 line-through font-en">¥${originalPrice.toLocaleString()}</div>
                                 <div class="font-en font-bold text-sm text-primary">¥${finalPrice.toLocaleString()}</div>
                             ` : `
                                 <div class="font-en font-bold text-sm">¥${finalPrice.toLocaleString()}</div>
                             `}
                         </div>
                     </div>
                 `;
            });
            itemsContainer.innerHTML = itemsHtml;
        }

        // 送料を含む合計を計算して表示
        function updateOrderTotal() {
            const subtotal = Cart.getTotal();
            const shipping = (currentPaymentMethod === 'transfer') ? 0 : 1000;
            const total = subtotal + shipping;

            // 送料表示
            const shippingEl = document.getElementById('display-shipping');
            if (shippingEl) {
                shippingEl.innerText = (shipping === 0) ? '¥0（無料）' : '¥' + shipping.toLocaleString();
                shippingEl.className = (shipping === 0)
                    ? 'font-en font-bold text-green-400'
                    : 'font-en font-bold';
            }

            // 合計表示
            document.getElementById('checkout-total').innerText = '¥' + total.toLocaleString();
            document.getElementById('form-amount').value = total;
        }

        function togglePayment(method) {
            currentPaymentMethod = method;
            const cardDiv = document.getElementById('payment-card');
            const transferDiv = document.getElementById('payment-transfer');

            if (method === 'card') {
                cardDiv.classList.remove('hidden');
                transferDiv.classList.add('hidden');
            } else {
                cardDiv.classList.add('hidden');
                transferDiv.classList.remove('hidden');
            }

            // 送料・合計を再計算
            updateOrderTotal();
        }

        // チェックボックスによる決済ボタンの有効/無効切り替え
        function togglePaymentButtons() {
            const checkbox = document.getElementById('confirm-flow-checkbox');
            const isChecked = checkbox.checked;
            const warning = document.getElementById('checkbox-warning');
            const paymentLabels = document.querySelectorAll('.payment-option-label');
            const btnCard = document.getElementById('btn-card-pay');
            const btnTransfer = document.getElementById('btn-transfer-pay');

            if (isChecked) {
                // 決済ボタンを有効化
                paymentLabels.forEach(label => {
                    label.style.pointerEvents = 'auto';
                    label.style.opacity = '1';
                });
                if (btnCard) btnCard.disabled = false;
                if (btnTransfer) btnTransfer.disabled = false;
                if (warning) warning.classList.add('hidden');
            } else {
                // 決済ボタンを無効化
                paymentLabels.forEach(label => {
                    label.style.pointerEvents = 'none';
                    label.style.opacity = '0.4';
                });
                if (btnCard) btnCard.disabled = true;
                if (btnTransfer) btnTransfer.disabled = true;
                if (warning) warning.classList.remove('hidden');
            }
        }

        // 脱着作業依頼ポップアップ
        function openInstallModal() {
            document.getElementById('install-modal').classList.remove('hidden');
        }
        function closeInstallModal() {
            document.getElementById('install-modal').classList.add('hidden');
        }

        function initPayjpV2() {
            // No initialization needed on this page anymore
        }

        function showError(msg) {
            const errDiv = document.getElementById('error-message');
            const errText = document.getElementById('error-text');
            if (errText) errText.innerText = msg;
            if (errDiv) errDiv.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // --- Confirmation Modal Logic ---

        function handleCardPayment() {
            currentPaymentMethod = 'card';
            openConfirmationModal();
        }

        function handleBankTransfer() {
            currentPaymentMethod = 'transfer';
            openConfirmationModal();
        }

        function openConfirmationModal() {
            // 1. Validate Form
            const form = document.getElementById('checkout-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // 2. Gather Data
            const formData = new FormData(form);
            const items = Cart.getItems();
            const subtotal = Cart.getTotal();
            const shipping = (currentPaymentMethod === 'transfer') ? 0 : 1000;
            const total = subtotal + shipping;

            // 3. Populate Modal
            const shippingHtml = `
                <p><span class="text-gray-500 w-24 inline-block">お名前:</span> ${formData.get('name')}</p>
                <p><span class="text-gray-500 w-24 inline-block">Email:</span> ${formData.get('email')}</p>
                <p><span class="text-gray-500 w-24 inline-block">電話番号:</span> ${formData.get('phone')}</p>
                <p><span class="text-gray-500 w-24 inline-block">郵便番号:</span> ${formData.get('zip')}</p>
                <p><span class="text-gray-500 w-24 inline-block">住所:</span> ${formData.get('address')}</p>
            `;
            document.getElementById('confirm-shipping-info').innerHTML = shippingHtml;

            let itemsListHtml = '';
            items.forEach(item => {
                const discount = Cart.getItemDiscount(item);
                const finalPrice = Cart.getItemPrice(item);
                const originalPrice = typeof item.price === 'string' ? parseInt(item.price.replace(/,/g, '')) : item.price;
                const hasDiscount = discount > 0;

                itemsListHtml += `
                    <div class="py-2 border-b border-gray-800 last:border-0">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-300 truncate w-2/3">${item.name} <span class="text-xs text-gray-500">(${Object.values(item.options || {}).join('/')})</span></span>
                            <span class="text-white font-en">¥${originalPrice.toLocaleString()}</span>
                        </div>
                        ${hasDiscount ? `
                            <div class="flex justify-between text-xs mt-1">
                                <span class="text-green-400"><i class="fas fa-tag mr-1"></i>下取り割引</span>
                                <span class="text-green-400 font-en">-¥${discount.toLocaleString()}</span>
                            </div>
                        ` : ''}
                    </div>
                `;
            });
            // 送料行
            itemsListHtml += `
                <div class="flex justify-between text-sm py-2 border-b border-gray-800">
                    <span class="text-gray-500">送料</span>
                    <span class="text-white font-en">${shipping === 0 ? '¥0（無料）' : '¥' + shipping.toLocaleString()}</span>
                </div>
            `;
            document.getElementById('confirm-items-list').innerHTML = itemsListHtml;

            const methodText = currentPaymentMethod === 'card' ? 'クレジットカード' : '銀行振込';
            document.getElementById('confirm-payment-method').innerText = methodText;
            document.getElementById('confirm-total-amount').innerText = '¥' + total.toLocaleString();

            // 4. Show Modal
            document.getElementById('confirmation-modal').classList.remove('hidden');
        }

        function closeConfirmationModal() {
            document.getElementById('confirmation-modal').classList.add('hidden');
        }

        // --- Final Payment Processing ---

        async function processFinalPayment() {
            closeConfirmationModal();
            const loadingOverlay = document.getElementById('loading-overlay');
            const errDiv = document.getElementById('error-message');

            if (errDiv) errDiv.classList.add('hidden');
            if (loadingOverlay) loadingOverlay.classList.remove('hidden');

            try {
                if (currentPaymentMethod === 'card') {
                    await processCardPayment();
                } else {
                    await processBankTransfer();
                }
            } catch (err) {
                if (loadingOverlay) loadingOverlay.classList.add('hidden');
                showError(err.message || '決済処理中にエラーが発生しました。');
            }
        }

        async function processCardPayment() {
            const form = document.getElementById('checkout-form');
            form.action = "card_entry.php";
            form.method = "POST";
            form.submit();
        }

        async function processBankTransfer() {
            await new Promise(r => setTimeout(r, 1000));
            await finalizeOrder('BANK TRANSFER');
        }

        async function finalizeOrder(paymentMethod, extraData = {}) {
            const form = document.getElementById('checkout-form');
            const data = new FormData(form);
            const items = Cart.getItems();

            const orderId = extraData.orderId || 'ORD-' + Date.now();
            const orderAmount = '¥' + parseInt(data.get('amount')).toLocaleString();

            const orderData = {
                name: data.get('name'),
                email: data.get('email'),
                orderId: orderId,
                amount: orderAmount,
                paymentMethod: paymentMethod,
                items: items
            };

            try {
                await fetch('./mail_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(orderData)
                });
            } catch (e) {
                console.error("Email sending warning:", e);
            }

            Cart.clear();
            const pmParam = paymentMethod === 'CREDIT CARD' ? 'card' : 'transfer';
            window.location.href = `order_complete.html?order_id=${orderId}&payment_method=${pmParam}`;
        }

        function formatPhoneNumberToE164(phone) {
            if (!phone) return '';
            let cleaned = phone.replace(/[-()\s]/g, '');
            if (cleaned.startsWith('0')) {
                cleaned = cleaned.substring(1);
                return '+81' + cleaned;
            }
            if (cleaned.startsWith('81')) {
                return '+' + cleaned;
            }
            if (cleaned.startsWith('+')) {
                return cleaned;
            }
            return '+81' + cleaned;
        }
    </script>
</body>

</html>