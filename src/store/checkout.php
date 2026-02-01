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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <a href="../index.php" class="flex items-center gap-3 group">
                <img src="../assets/images/logo_new.png" alt="GIKO" class="h-8 w-auto object-contain">
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
            <!-- CSRF Token (Not strictly used for API calls, but good practice for form submissions) -->
            <input type="hidden" name="csrf_token"
                value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                <!-- Left Column: Form (Span 7) -->
                <div class="lg:col-span-7 space-y-12">
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

                    <!-- Payment Method -->
                    <section>
                        <div class="flex items-center gap-4 mb-8">
                            <div
                                class="w-8 h-8 rounded-full bg-white/20 text-white flex items-center justify-center font-bold font-en">
                                2</div>
                            <h2 class="text-xl font-bold font-en tracking-widest text-white">PAYMENT METHOD</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Card Option -->
                            <label class="relative cursor-pointer group">
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
                                    <div class="font-bold font-en tracking-wide mb-1">CREDIT CARD</div>
                                    <p class="text-[10px] text-gray-400 font-en">VISA, Master, JCB, AMEX, Diners</p>
                                </div>
                            </label>

                            <!-- Transfer Option -->
                            <label class="relative cursor-pointer group">
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
                                                <span class="italic">LIGHTNING</span> FREE SHIPPING
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
                                    <div class="font-bold font-en tracking-wide mb-1">BANK TRANSFER</div>
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
                <div class="lg:col-span-5 lg:sticky lg:top-32 h-fit">
                    <!-- (Summary content same as before) -->
                    <div class="bg-secondary p-8 border border-white/10 rounded-sm relative overflow-hidden shadow-2xl">
                        <div class="absolute -top-20 -right-20 w-60 h-60 bg-primary/10 rounded-full blur-[80px]"></div>

                        <h2
                            class="text-sm font-bold text-gray-400 mb-8 tracking-widest font-en border-b border-white/10 pb-4">
                            ORDER SUMMARY</h2>

                        <div id="checkout-items"
                            class="space-y-6 mb-8 max-h-[400px] overflow-y-auto pr-2 scrollbar-hide">
                            <!-- Items injected via JS -->
                        </div>

                        <div class="border-t border-white/10 pt-6 space-y-3">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-400 font-en tracking-wider">SUBTOTAL</span>
                                <span id="display-subtotal" class="font-en font-bold">¥0</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-400 font-en tracking-wider">SHIPPING</span>
                                <span class="font-en font-bold">¥0</span>
                            </div>
                            <div class="flex justify-between items-end pt-4 border-t border-white/10 mt-4">
                                <span class="text-sm font-bold text-gray-400 font-en tracking-widest">TOTAL</span>
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
                                    <label
                                        class="block text-xs font-bold font-en tracking-widest mb-3 text-gray-400">CARD
                                        INFORMATION <span class="text-primary">*</span></label>
                                    <div id="v2-card-element"
                                        class="bg-black/50 border border-white/10 p-3 rounded-sm min-h-[48px]">
                                    </div>
                                </div>

                                <button type="button" onclick="handleCardPayment()"
                                    class="w-full bg-[#C0A062] text-black font-bold py-4 rounded-sm hover:bg-white transition-all duration-300 tracking-widest font-en shadow-xl uppercase">
                                    COMPLETE ORDER
                                </button>
                                <p class="text-[10px] text-gray-500 text-center">
                                    <i class="fas fa-lock mr-1"></i> SSL通信と3Dセキュア認証で安全に保護されます
                                </p>
                            </div>

                            <!-- Transfer Payment -->
                            <div id="payment-transfer" class="hidden">
                                <button type="button" onclick="handleBankTransfer()"
                                    class="w-full bg-white text-black font-bold py-4 rounded-sm hover:bg-primary hover:text-white transition-all duration-300 tracking-widest font-en shadow-xl uppercase">
                                    Place Order
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
    <!-- (Using strict structure same as before) -->
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
                    <!-- Shipping Info -->
                    <div>
                        <h4 class="text-gold-400 font-serif mb-4 border-b border-gray-700 pb-2">お届け先情報</h4>
                        <div id="confirm-shipping-info" class="space-y-2 text-gray-300 text-sm">
                        </div>
                    </div>
                    <!-- Order Items -->
                    <div>
                        <h4 class="text-gold-400 font-serif mb-4 border-b border-gray-700 pb-2">注文商品</h4>
                        <div id="confirm-items-list" class="space-y-3">
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
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

                <!-- Security Note -->
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

    <!-- <script src="../assets/js/email_handler.js"></script> -->
    <script src="https://js.pay.jp/v2/payjp.js"></script>
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

            const total = Cart.getTotal();
            if (total === 0) {
                alert('カートが空です');
                window.location.href = 'purchase.html';
                return;
            }
            // Render items
            const items = Cart.getItems();
            renderCartItems(items);

            const formattedTotal = '¥' + total.toLocaleString();
            document.getElementById('checkout-total').innerText = formattedTotal;
            document.getElementById('display-subtotal').innerText = formattedTotal;
            document.getElementById('form-amount').value = total;
            document.getElementById('cart-items-input').value = JSON.stringify(items);

            // Check for error query param from 3DS redirect/callback
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            if (error) {
                showError(decodeURIComponent(error));
            }

            // Initialize PAY.JP V2
            initPayjpV2();
        });

        function renderCartItems(items) {
            const itemsContainer = document.getElementById('checkout-items');
            let itemsHtml = '';
            items.forEach(item => {
                itemsHtml += `
                     <div class="flex gap-4 items-start">
                          <div class="w-16 h-16 bg-white/5 rounded-sm flex-shrink-0 overflow-hidden border border-white/5">
                             ${item.image ? `<img src="${item.image}" class="w-full h-full object-cover">` : ''}
                         </div>
                         <div class="flex-1 min-w-0">
                             <h4 class="font-bold text-sm truncate pr-4">${item.name}</h4>
                             <p class="text-xs text-gray-400 mt-1 font-en">${Object.values(item.options || {}).join(' / ')}</p>
                         </div>
                         <div class="font-en font-bold text-sm">¥${item.price.toLocaleString()}</div>
                     </div>
                 `;
            });
            itemsContainer.innerHTML = itemsHtml;
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
        }

        function initPayjpV2() {
            if (typeof Payjp === 'undefined') {
                console.error("PAY.JP SDK not loaded. Check internet connection.");
                showError("決済システムの読み込みに失敗しました。ページを再読み込みしてください。");
                return;
            }
            try {
                payjp = Payjp(PUBLIC_KEY);
                elements = payjp.elements();

                // Style for the embedded card input
                const style = {
                    base: {
                        color: '#ffffff',
                        fontFamily: '"Montserrat", "Noto Sans JP", sans-serif',
                        fontSize: '16px',
                        lineHeight: '1.5',
                        '::placeholder': {
                            color: '#6b7280', // gray-500
                        }
                    },
                    invalid: {
                        color: '#fca5a5', // red-300
                        iconColor: '#fca5a5'
                    }
                };

                cardElement = elements.create('card', { style: style, hideCardIcons: false });
                cardElement.mount('#v2-card-element');
            } catch (e) {
                console.error("PAY.JP Init Error:", e);
                showError("カード入力フォームの表示に失敗しました。");
            }
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
            const total = Cart.getTotal();

            // 3. Populate Modal
            // Shipping Info
            const shippingHtml = `
                <p><span class="text-gray-500 w-24 inline-block">お名前:</span> ${formData.get('name')}</p>
                <p><span class="text-gray-500 w-24 inline-block">Email:</span> ${formData.get('email')}</p>
                <p><span class="text-gray-500 w-24 inline-block">電話番号:</span> ${formData.get('phone')}</p>
                <p><span class="text-gray-500 w-24 inline-block">郵便番号:</span> ${formData.get('zip')}</p>
                <p><span class="text-gray-500 w-24 inline-block">住所:</span> ${formData.get('address')}</p>
            `;
            document.getElementById('confirm-shipping-info').innerHTML = shippingHtml;

            // Items
            let itemsListHtml = '';
            items.forEach(item => {
                itemsListHtml += `
                    <div class="flex justify-between text-sm py-2 border-b border-gray-800 last:border-0">
                        <span class="text-gray-300 truncate w-2/3">${item.name} <span class="text-xs text-gray-500">(${Object.values(item.options || {}).join('/')})</span></span>
                        <span class="text-white font-en">¥${item.price.toLocaleString()}</span>
                    </div>
                `;
            });
            document.getElementById('confirm-items-list').innerHTML = itemsListHtml;

            // Payment Method & Total
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
            // Close Confirm Modal, Show Loading
            closeConfirmationModal();
            const loadingOverlay = document.getElementById('loading-overlay');
            const errDiv = document.getElementById('error-message');

            if(errDiv) errDiv.classList.add('hidden');
            if(loadingOverlay) loadingOverlay.classList.remove('hidden');

            try {
                if (currentPaymentMethod === 'card') {
                    await processCardPayment();
                } else {
                    await processBankTransfer();
                }
            } catch (err) {
                if(loadingOverlay) loadingOverlay.classList.add('hidden');
                showError(err.message || '決済処理中にエラーが発生しました。');
            }
        }

        async function processCardPayment() {
            const form = document.getElementById('checkout-form');
            const formData = new FormData(form);

            // 1. Create Token (Client-side 3DS)
            const result = await payjp.createToken(cardElement, {
                three_d_secure: true,
                card: {
                    name: formData.get('name'),
                    email: formData.get('email'),
                    phone: formData.get('phone')
                }
            });

            if (result.error) {
                throw new Error(result.error.message);
            }

            // 2. Send to Backend (payment.php)
            // Note: Creating ID for idempotency key
            const uniqueId = 'req_' + Date.now() + Math.random().toString(36).substring(2, 9);

            const response = await fetch('./payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Idempotency-Key': uniqueId
                },
                body: JSON.stringify({
                    token: result.id,
                    amount: formData.get('amount')
                })
            });

            // Handle non-JSON or error responses gracefully
            let data;
            try {
                data = await response.json();
            } catch (e) {
                throw new Error('サーバーからの応答が無効です。');
            }

            if (!response.ok || !data.success) {
                throw new Error(data.error || '決済処理に失敗しました。');
            }

            // 3. Success -> Send Email & Redirect
            // Pass the order ID from server if available
            await finalizeOrder('CREDIT CARD', { orderId: data.id || uniqueId });
        }

        async function processBankTransfer() {
            // Simulate processing
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

            // EmailJS
            // Send Email via PHP
            try {
                await fetch('./mail_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(orderData)
                });
            } catch (e) {
                console.error("Email sending warning:", e);
                // Do not block flow
            }

            // Clean Redirect
            Cart.clear();
            const pmParam = paymentMethod === 'CREDIT CARD' ? 'card' : 'transfer';
            window.location.href = `order_complete.html?order_id=${orderId}&payment_method=${pmParam}`;
        }
    </script>
</body>

</html>

