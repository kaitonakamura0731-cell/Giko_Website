<?php
session_start();

// 1. Receive POST data from checkout.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

// 2. Validate essential data
$required_fields = ['name', 'email', 'phone', 'amount', 'cart_items'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        header('Location: checkout.php?error=' . urlencode('必須項目が不足しています'));
        exit;
    }
}

$name = htmlspecialchars($_POST['name']);
$email = htmlspecialchars($_POST['email']);
$phone = htmlspecialchars($_POST['phone']);
$zip = htmlspecialchars($_POST['zip'] ?? '');
$address = htmlspecialchars($_POST['address'] ?? '');
$amount = (int)$_POST['amount'];
$cart_items = $_POST['cart_items'];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CREDIT CARD PAYMENT | 技巧 -Giko-</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../tailwind_config.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-black text-white antialiased">
    
    <header class="fixed w-full z-50 bg-black/80 backdrop-blur-md border-b border-white/5">
        <div class="container mx-auto px-6 h-20 flex justify-between items-center">
             <a href="../index.php"><img src="../assets/images/logo_new.png" alt="GIKO" class="h-8"></a>
            <div class="text-xs font-bold font-en tracking-widest text-gray-400"><i class="fas fa-lock mr-2"></i>SECURE PAYMENT</div>
        </div>
    </header>

    <main class="container mx-auto px-6 pt-32 pb-20 max-w-lg">
        
        <div class="mb-10 text-center">
             <h1 class="text-2xl font-bold font-en tracking-widest mb-2">PAYMENT AMOUNT</h1>
             <div class="text-4xl font-bold text-primary font-en">¥<?php echo number_format($amount); ?></div>
             <p class="text-xs text-gray-400 mt-2 font-en">TOTAL (TAX INCLUDED)</p>
        </div>

        <div id="error-message" class="hidden bg-red-900/20 border border-red-500/50 text-red-200 p-4 mb-8 text-sm rounded-sm flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-lg mt-0.5"></i>
            <span id="error-text"></span>
        </div>

        <div class="bg-white/5 border border-white/10 p-8 rounded-sm shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary/50 via-primary to-primary/50"></div>
            
            <form id="payment-form" class="space-y-6">
                <!-- Cardholder Name -->
                <div>
                    <label class="block text-xs font-bold font-en tracking-widest mb-3 text-gray-300">
                        CARDHOLDER NAME <span class="text-primary">*</span>
                    </label>
                    <input type="text" id="cardholder-name" required
                        class="w-full bg-black/50 border border-white/10 px-4 py-3 rounded-sm text-white placeholder-gray-500 focus:border-primary outline-none"
                        placeholder="TARO YAMADA">
                    <p class="text-[10px] text-gray-500 mt-1">※カードに記載の名義をローマ字で入力</p>
                </div>

                <!-- Combined Card Element (More Stable) -->
                <div>
                    <label class="block text-xs font-bold font-en tracking-widest mb-3 text-gray-300">
                        CARD DETAILS <span class="text-primary">*</span>
                    </label>
                    <div id="card-element" class="bg-black/50 border border-white/10 p-4 rounded-sm min-h-[50px]"></div>
                    <p class="text-[10px] text-gray-500 mt-1">カード番号・有効期限・セキュリティコード</p>
                </div>

                <button type="button" id="submit-button" onclick="handlePayment()" 
                    class="w-full bg-[#C0A062] text-black font-bold py-4 rounded-sm hover:bg-white transition-all tracking-widest font-en shadow-lg mt-8">
                    PAY & COMPLETE
                </button>
                
                <p class="text-[10px] text-gray-500 text-center mt-4">
                    <i class="fas fa-lock mr-1"></i> SSL通信と3Dセキュア認証で安全に保護されます
                </p>
            </form>
        </div>

        <div class="text-center mt-8">
             <button onclick="history.back()" class="text-xs text-gray-500 hover:text-white">
                <i class="fas fa-arrow-left mr-1"></i> 戻る (修正する)
            </button>
        </div>
    </main>

    <div id="loading-overlay" class="fixed inset-0 bg-black/90 z-[100] hidden flex items-center justify-center">
        <div class="text-center">
            <div class="w-16 h-16 border-4 border-white/20 border-t-primary rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-white font-en tracking-widest animate-pulse">PROCESSING...</p>
            <p class="text-xs text-gray-400 mt-2">決済処理中...画面を閉じないでください</p>
        </div>
    </div>

    <input type="hidden" id="data-name" value="<?php echo $name; ?>">
    <input type="hidden" id="data-email" value="<?php echo $email; ?>">
    <input type="hidden" id="data-phone" value="<?php echo $phone; ?>">
    <input type="hidden" id="data-amount" value="<?php echo $amount; ?>">
    <input type="hidden" id="data-items" value="<?php echo htmlspecialchars($cart_items, ENT_QUOTES, 'UTF-8'); ?>">

    <script src="https://js.pay.jp/v2/pay.js"></script>
    <script>
        const PUBLIC_KEY = 'pk_test_9deadd0cb5a5d94b4cd785dc'; // ユーザーの正しいテスト公開鍵
        let payjp, elements, cardElement;

        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Payjp === 'undefined') {
                showError("決済システムの読み込みに失敗しました。ページを再読み込みしてください。");
                return;
            }
            
            payjp = Payjp(PUBLIC_KEY);
            elements = payjp.elements();
            
            // Use combined card element (more stable)
            cardElement = elements.create('card', {
                style: {
                    base: {
                        color: '#ffffff',
                        fontSize: '16px',
                        '::placeholder': { color: '#6b7280' }
                    },
                    invalid: { color: '#fca5a5' }
                }
            });
            cardElement.mount('#card-element');
        });

        async function handlePayment() {
            const loading = document.getElementById('loading-overlay');
            const btn = document.getElementById('submit-button');
            const cardholderName = document.getElementById('cardholder-name').value.trim().toUpperCase();
            
            if (!cardholderName || cardholderName.length < 2) {
                showError("カード名義人を入力してください");
                return;
            }
            if (!/^[A-Za-z\s.\-]+$/.test(cardholderName)) {
                showError("カード名義人は半角英字で入力してください（例: TARO YAMADA）");
                return;
            }

            loading.classList.remove('hidden');
            btn.disabled = true;
            hideError();

            try {
                const email = document.getElementById('data-email').value;
                const phone = document.getElementById('data-phone').value;
                const amount = document.getElementById('data-amount').value;
                const formattedPhone = formatPhoneToE164(phone);

                // Create token (3D Secure有効)
                const result = await payjp.createToken(cardElement, {
                    three_d_secure: true,
                    card: {
                        name: cardholderName,
                        email: email,
                        phone: formattedPhone
                    }
                });

                console.log("PAY.JP Result:", result);

                if (result.error) {
                    throw new Error(result.error.message);
                }

                // Send to backend
                const uniqueId = 'req_' + Date.now() + Math.random().toString(36).substr(2, 9);
                const response = await fetch('./payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Idempotency-Key': uniqueId },
                    body: JSON.stringify({ token: result.id, amount: amount })
                });

                const data = await response.json().catch(() => { throw new Error('サーバー応答エラー'); });
                if (!response.ok || !data.success) throw new Error(data.error || '決済処理に失敗しました。');

                await finalizeOrder(uniqueId, data.id);

            } catch (error) {
                loading.classList.add('hidden');
                btn.disabled = false;
                showError(error.message);
            }
        }

        async function finalizeOrder(uniqueId, payjpId) {
            const orderId = payjpId || uniqueId;
            try {
                await fetch('./mail_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        name: document.getElementById('data-name').value,
                        email: document.getElementById('data-email').value,
                        orderId: orderId,
                        amount: '¥' + parseInt(document.getElementById('data-amount').value).toLocaleString(),
                        paymentMethod: 'CREDIT CARD',
                        items: JSON.parse(document.getElementById('data-items').value)
                    })
                });
            } catch (e) { console.warn(e); }
            localStorage.removeItem('giko_cart');
            window.location.href = `order_complete.html?order_id=${orderId}&payment_method=card`;
        }

        function showError(msg) {
            document.getElementById('error-text').innerText = msg;
            document.getElementById('error-message').classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        function hideError() {
            document.getElementById('error-message').classList.add('hidden');
        }
        function formatPhoneToE164(phone) {
            if (!phone) return '';
            let c = phone.replace(/[-()\s]/g, '');
            if (c.startsWith('0')) return '+81' + c.substring(1);
            if (c.startsWith('81')) return '+' + c;
            if (c.startsWith('+')) return c;
            return '+81' + c;
        }
    </script>
</body>
</html>
