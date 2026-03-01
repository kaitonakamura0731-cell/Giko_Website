<?php
/**
 * process_transfer.php
 * 銀行振込注文のサーバーサイド処理
 *
 * 1. CSRF トークン検証
 * 2. 商品価格をDBと照合（改ざん防止）
 * 3. 注文確認メール送信（管理者 + ユーザー）
 * 4. 注文完了ページへリダイレクト
 */

// セッションセキュリティ設定
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Lax');
session_start();

require_once __DIR__ . '/../admin/includes/db.php';
require_once __DIR__ . '/validate_cart.php';

// ---------------------------------------------------------
// 1. POSTのみ許可
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

// ---------------------------------------------------------
// 2. CSRF Token Validation
// ---------------------------------------------------------
$postedToken = $_POST['csrf_token'] ?? '';
if (empty($postedToken) || !hash_equals($_SESSION['csrf_token'] ?? '', $postedToken)) {
    header('Location: checkout.php?error=' . urlencode('不正なリクエストです。ページを再読み込みしてお試しください。'));
    exit;
}

// ---------------------------------------------------------
// 3. 必須フィールド検証
// ---------------------------------------------------------
$required_fields = ['name', 'email', 'phone', 'amount', 'cart_items'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        header('Location: checkout.php?error=' . urlencode('必須項目が不足しています。'));
        exit;
    }
}

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$zip = $_POST['zip'] ?? '';
$address = $_POST['address'] ?? '';
$clientAmount = (int) $_POST['amount'];
$cartItemsJson = $_POST['cart_items'];

// メールバリデーション
$cleanEmail = str_replace(["\r", "\n"], '', $email);
if (!filter_var($cleanEmail, FILTER_VALIDATE_EMAIL)) {
    header('Location: checkout.php?error=' . urlencode('メールアドレスが無効です。'));
    exit;
}

// ---------------------------------------------------------
// 4. サーバーサイドで商品価格をDB照合（改ざん防止）
// ---------------------------------------------------------
try {
    $cartItems = json_decode($cartItemsJson, true);
    if (!is_array($cartItems) || empty($cartItems)) {
        throw new Exception('カート情報が無効です。');
    }
    $validated = validateCartPrices($pdo, $cartItems);

    // 銀行振込 = 送料無料
    $shipping = 0;
    $serverAmount = $validated['subtotal'] + $shipping;

    // クライアント金額とサーバー計算金額を照合
    if ($serverAmount !== $clientAmount) {
        throw new Exception('金額に不整合があります。カートを確認のうえ再度お試しください。');
    }
} catch (Exception $e) {
    header('Location: checkout.php?error=' . urlencode($e->getMessage()));
    exit;
}

// ---------------------------------------------------------
// 5. 注文ID生成
// ---------------------------------------------------------
$orderId = 'ORD-' . date('Ymd') . '-' . bin2hex(random_bytes(4));
$orderAmount = '¥' . number_format($serverAmount);
$payMethod = 'BANK TRANSFER';

// ---------------------------------------------------------
// 6. メール送信
// ---------------------------------------------------------
$ADMIN_EMAIL = 'kaitonakamura0731@gmail.com';
$ADMIN_CC = 'info@giko-official.com';
$SERVER_DOMAIN = 'giko-official.com';
$NOREPLY_EMAIL = "noreply@{$SERVER_DOMAIN}";
$CONTACT_EMAIL = 'info@giko-official.com';

// 銀行振込先情報（order_complete.html / mail_order.php と合わせて更新してください）
$BANK_INFO = [
    'bank_name'      => 'テスト銀行',
    'branch_name'    => 'サンプル支店（999）',
    'account_type'   => '普通',
    'account_no'     => '1234567',
    'account_holder' => 'カ）テストカイシャ',
];

// 商品一覧テキスト
$itemsText = "";
foreach ($validated['items'] as $item) {
    $opt = !empty($item['options']) ? " (" . implode('/', array_values($item['options'])) . ")" : "";
    $price = number_format($item['price']);
    $itemsText .= "- {$item['name']}{$opt} x {$item['quantity']} : ¥{$price}\n";
    if ($item['additionalCost'] > 0) {
        $itemsText .= "  ※元パーツ未買取追加費用: +¥" . number_format($item['additionalCost']) . "\n";
    }
}

// お客様情報テキスト
$customerInfo = "お名前: {$name}\n";
$customerInfo .= "メール: {$cleanEmail}\n";
if (!empty($phone)) $customerInfo .= "電話番号: {$phone}\n";
if (!empty($zip)) $customerInfo .= "郵便番号: {$zip}\n";
if (!empty($address)) $customerInfo .= "ご住所: {$address}\n";

mb_language("Japanese");
mb_internal_encoding("UTF-8");

// --- 管理者向けメール ---
$admin_subject = "【技巧 -Giko-】新規注文受信（銀行振込） ({$orderId})";
$admin_body = <<<EOT
新規の注文が入りました（銀行振込）。

【注文ID】 {$orderId}
【決済方法】 {$payMethod}
【合計金額】 {$orderAmount}

【お客様情報】
名前: {$name}
Email: {$cleanEmail}
電話番号: {$phone}
郵便番号: {$zip}
住所: {$address}

【注文商品】
--------------------------------------------------
{$itemsText}--------------------------------------------------

※入金確認後、発送手続きを開始してください。
EOT;

$admin_headers = "From: {$NOREPLY_EMAIL}\r\n";
$admin_headers .= "Reply-To: {$cleanEmail}\r\n";
$admin_headers .= "Cc: {$ADMIN_CC}\r\n";
mb_send_mail($ADMIN_EMAIL, $admin_subject, $admin_body, $admin_headers, "-f{$NOREPLY_EMAIL}");

// --- お客様向けメール（振込先情報付き） ---
$bankInfoBlock = <<<BANK

【お振込先】
--------------------------------------------------
銀行名:   {$BANK_INFO['bank_name']}
支店名:   {$BANK_INFO['branch_name']}
口座種別: {$BANK_INFO['account_type']}
口座番号: {$BANK_INFO['account_no']}
口座名義: {$BANK_INFO['account_holder']}
--------------------------------------------------

※7日以内にお振込みをお願いいたします。
※振込手数料はお客様のご負担となります。
※ご注文者様と異なる名義でお振込みの場合は、事前にご連絡ください。
※ご入金確認後、発送手続きを開始いたします。
BANK;

$user_subject = "【技巧 -Giko-】ご注文ありがとうございます ({$orderId})";
$user_body = <<<EOT
{$name} 様

この度は「技巧 -Giko-」にてご注文いただき、誠にありがとうございます。
以下の内容で承りました。

【注文ID】 {$orderId}
【決済方法】 {$payMethod}
【合計金額】 {$orderAmount}

【お客様情報】
--------------------------------------------------
{$customerInfo}--------------------------------------------------

【ご注文内容】
--------------------------------------------------
{$itemsText}--------------------------------------------------
{$bankInfoBlock}
商品の発送準備が整い次第、改めてご連絡させていただきます。
万が一、ご注文内容に誤りがある場合は、以下のメールにてご連絡ください。
{$CONTACT_EMAIL}

--------------------------------------------------
技巧 -Giko-
https://giko-official.com
--------------------------------------------------
EOT;

$user_headers = "From: {$NOREPLY_EMAIL}\r\n";
$user_headers .= "Reply-To: {$ADMIN_EMAIL}\r\n";
mb_send_mail($cleanEmail, $user_subject, $user_body, $user_headers, "-f{$NOREPLY_EMAIL}");

// ---------------------------------------------------------
// 7. 注文完了ページへリダイレクト
// ---------------------------------------------------------
header('Location: order_complete.html?order_id=' . urlencode($orderId) . '&payment_method=transfer');
exit;
