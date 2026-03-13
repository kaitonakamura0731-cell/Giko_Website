<?php
/**
 * tds_callback.php
 * PAY.JP 3Dセキュア認証後のコールバックページ
 *
 * 3DS認証完了後、PAY.JPがユーザーをこのURLにリダイレクトします。
 * チャージの状態を確認し、成功なら注文完了、失敗ならエラーを返します。
 */

session_start();
require_once __DIR__ . '/../config/api_keys.php';

$PAYJP_SECRET_KEY = PAYJP_SECRET_KEY;

// ---------------------------------------------------------
// 1. Get charge ID from session or query parameter
// ---------------------------------------------------------

$chargeId = $_SESSION['pending_charge_id'] ?? null;

if (!$chargeId) {
    header('Location: checkout.php?error=' . urlencode('決済情報が見つかりません。もう一度お試しください。'));
    exit;
}

// ---------------------------------------------------------
// 2. Finalize 3DS authentication (tds_finish)
// ---------------------------------------------------------

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.pay.jp/v1/charges/' . $chargeId . '/tds_finish');
curl_setopt($ch, CURLOPT_USERPWD, $PAYJP_SECRET_KEY . ':');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, '');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError || $httpStatus < 200 || $httpStatus >= 300) {
    header('Location: checkout.php?error=' . urlencode('決済の完了処理に失敗しました。お問い合わせください。'));
    exit;
}

$charge = json_decode($response, true);

// ---------------------------------------------------------
// 3. Check payment status
// ---------------------------------------------------------

$tdsStatus = $charge['three_d_secure_status'] ?? null;
$paid = $charge['paid'] ?? false;

if (!$paid || $tdsStatus !== 'verified') {
    // 3DS failed or not verified
    // Clean up session
    unset($_SESSION['pending_charge_id']);

    $errorMsg = '3Dセキュア認証に失敗しました。別のカードでお試しいただくか、銀行振込をご利用ください。';
    header('Location: checkout.php?error=' . urlencode($errorMsg));
    exit;
}

// ---------------------------------------------------------
// 4. Payment successful! Send order confirmation email
// ---------------------------------------------------------

$orderData = $_SESSION['pending_order'] ?? null;
$orderId = $chargeId;

if ($orderData) {
    $ADMIN_EMAIL = 'kaitonakamura0731@gmail.com';
    $ADMIN_CC = 'info@giko-official.com';
    $SERVER_DOMAIN = 'giko-official.com';
    $NOREPLY_EMAIL = "noreply@{$SERVER_DOMAIN}";

    $custName = $orderData['name'] ?? '';
    $custEmail = str_replace(["\r", "\n"], '', $orderData['email'] ?? '');
    $custPhone = $orderData['phone'] ?? '';
    $custZip = $orderData['zip'] ?? '';
    $custAddress = $orderData['address'] ?? '';
    $orderAmount = '¥' . number_format($charge['amount']);
    $payMethod = 'CREDIT CARD (3D Secure)';
    $CONTACT_EMAIL = 'info@giko-official.com';
    $items = json_decode($orderData['cart_items'] ?? '[]', true) ?: [];

    // Build item list
    $itemsText = "";
    foreach ($items as $item) {
        $opt = isset($item['options']) ? " (" . implode('/', $item['options']) . ")" : "";
        $price = number_format($item['price'] ?? 0);
        $qty = $item['quantity'] ?? 1;
        $itemsText .= "- {$item['name']}{$opt} x {$qty} : ¥{$price}\n";
    }

    // Build customer info
    $customerInfo = "お名前: {$custName}\n";
    $customerInfo .= "メール: {$custEmail}\n";
    if (!empty($custPhone)) $customerInfo .= "電話番号: {$custPhone}\n";
    if (!empty($custZip)) $customerInfo .= "郵便番号: {$custZip}\n";
    if (!empty($custAddress)) $customerInfo .= "ご住所: {$custAddress}\n";

    mb_language("Japanese");
    mb_internal_encoding("UTF-8");

    // Admin email
    $admin_subject = "【技巧 -GIKO-】新規注文受信 ({$orderId})";
    $admin_body = "新規の注文が入りました。\n\n【注文ID】 {$orderId}\n【決済方法】 {$payMethod}\n【合計金額】 {$orderAmount}\n\n【お客様情報】\n名前: {$custName}\nEmail: {$custEmail}\n電話番号: {$custPhone}\n郵便番号: {$custZip}\n住所: {$custAddress}\n\n【注文商品】\n--------------------------------------------------\n{$itemsText}--------------------------------------------------\n\nPAY.JP管理画面で決済状況を確認してください。";
    $admin_headers = "From: {$NOREPLY_EMAIL}\r\nReply-To: {$custEmail}\r\n";
    $admin_to = "{$ADMIN_EMAIL}, {$ADMIN_CC}";
    mb_send_mail($admin_to, $admin_subject, $admin_body, $admin_headers, "-f{$NOREPLY_EMAIL}");

    // User email
    if ($custEmail && filter_var($custEmail, FILTER_VALIDATE_EMAIL)) {
        $user_subject = "【技巧 -GIKO-】ご注文ありがとうございます ({$orderId})";
        $user_body = "{$custName} 様\n\nこの度は「技巧 -GIKO-」にてご注文いただき、誠にありがとうございます。\n以下の内容で承りました。\n\n【注文ID】 {$orderId}\n【決済方法】 {$payMethod}\n【合計金額】 {$orderAmount}\n\n【お客様情報】\n--------------------------------------------------\n{$customerInfo}--------------------------------------------------\n\n【ご注文内容】\n--------------------------------------------------\n{$itemsText}--------------------------------------------------\n\n商品の発送準備が整い次第、改めてご連絡させていただきます。\n万が一、ご注文内容に誤りがある場合は、以下のメールにてご連絡ください。\n{$CONTACT_EMAIL}\n\n※デビットカード・プリペイドカードをご利用の場合、カード有効性確認のため11円の少額認証が一時的に発生しますが、後日自動的に返金されます。\n\n--------------------------------------------------\n技巧 -GIKO-\nhttps://giko-official.com\n--------------------------------------------------";
        $user_headers = "From: {$NOREPLY_EMAIL}\r\nReply-To: {$ADMIN_EMAIL}, {$ADMIN_CC}\r\n";
        mb_send_mail($custEmail, $user_subject, $user_body, $user_headers, "-f{$NOREPLY_EMAIL}");
    }
}

// ---------------------------------------------------------
// 5. Clean up session and redirect to order complete
// ---------------------------------------------------------

unset($_SESSION['pending_charge_id']);
unset($_SESSION['pending_order']);
unset($_SESSION['payment_processing']);
unset($_SESSION['payment_idempotency_key']);

header('Location: order_complete.html?order_id=' . urlencode($orderId) . '&payment_method=card');
exit;
?>
