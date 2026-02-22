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
    // Fallback: try to get from URL query
    $chargeId = $_GET['payjp_charge_id'] ?? null;
}

if (!$chargeId) {
    header('Location: checkout.php?error=' . urlencode('決済情報が見つかりません。もう一度お試しください。'));
    exit;
}

// ---------------------------------------------------------
// 2. Retrieve charge from PAY.JP API to check 3DS status
// ---------------------------------------------------------

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.pay.jp/v1/charges/' . $chargeId);
curl_setopt($ch, CURLOPT_USERPWD, $PAYJP_SECRET_KEY . ':');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError || $httpStatus !== 200) {
    header('Location: checkout.php?error=' . urlencode('決済状態の確認に失敗しました。お問い合わせください。'));
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
    // Send order confirmation email
    $emailPayload = [
        'name' => $orderData['name'] ?? '',
        'email' => $orderData['email'] ?? '',
        'orderId' => $orderId,
        'amount' => '¥' . number_format($charge['amount']),
        'paymentMethod' => 'CREDIT CARD (3D Secure)',
        'items' => json_decode($orderData['cart_items'] ?? '[]', true) ?: []
    ];

    // Internal request to mail_order.php
    $mailCh = curl_init();
    $mailUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
               . '://' . $_SERVER['HTTP_HOST']
               . dirname($_SERVER['REQUEST_URI']) . '/mail_order.php';

    curl_setopt($mailCh, CURLOPT_URL, $mailUrl);
    curl_setopt($mailCh, CURLOPT_POST, true);
    curl_setopt($mailCh, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($mailCh, CURLOPT_POSTFIELDS, json_encode($emailPayload));
    curl_setopt($mailCh, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($mailCh, CURLOPT_TIMEOUT, 10);
    curl_exec($mailCh);
    curl_close($mailCh);
}

// ---------------------------------------------------------
// 5. Clean up session and redirect to order complete
// ---------------------------------------------------------

unset($_SESSION['pending_charge_id']);
unset($_SESSION['pending_order']);

header('Location: order_complete.html?order_id=' . urlencode($orderId) . '&payment_method=card');
exit;
?>
