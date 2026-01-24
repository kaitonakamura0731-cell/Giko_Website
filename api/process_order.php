<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load .env
function loadEnv($path) {
    if (!file_exists($path)) return false;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
    return true;
}

loadEnv(__DIR__ . '/../.env');

// Settings
$payjpSecretKey = $_ENV['PAYJP_SECRET_KEY'] ?? '';
$adminEmail = 'info@giko-artisan.jp'; // Change to actual admin email
$emailFrom = 'noreply@giko-artisan.jp';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.html');
    exit;
}

// 1. Get Inputs
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
$zip = filter_input(INPUT_POST, 'zip', FILTER_SANITIZE_STRING);
$amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_INT);
$paymentMethod = $_POST['payment_method'] ?? 'card';
$cartItemsJson = $_POST['cart_items'] ?? '[]';
$cartItems = json_decode($cartItemsJson, true);

// Basic Validation
if (!$name || !$email || !$amount || empty($cartItems)) {
    die("Error: Missing required fields.");
}

// 2. Process Payment
$paymentId = null; // PAY.JP Charge ID or 'BANK_TRANSFER'

if ($paymentMethod === 'card') {
    if (empty($payjpSecretKey)) die("Error: Server Configuration Error (Key missing).");
    
    $token = $_POST['payjp-token'] ?? '';
    if (!$token) die("Error: Card token missing.");

    // Call PAY.JP
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.pay.jp/v1/charges');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERPWD, $payjpSecretKey . ':');
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'amount' => $amount,
        'currency' => 'jpy',
        'card' => $token,
        'description' => "Order by $name ($email)",
        'capture' => 'true'
    ]));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($httpCode === 200 && isset($result['id'])) {
        $paymentId = $result['id'];
    } else {
        $errorMsg = $result['error']['message'] ?? 'Payment Failed';
        header('Location: ../checkout.html?error=' . urlencode($errorMsg));
        exit;
    }

} else {
    // Bank Transfer
    $paymentId = 'BANK_TRANSFER';
}

// 3. Send Emails
mb_language("Japanese");
mb_internal_encoding("UTF-8");

// Prepare Item List String
$itemListStr = "";
foreach ($cartItems as $item) {
    $opts = isset($item['options']) ? " (" . implode(', ', $item['options']) . ")" : "";
    $itemListStr .= "・{$item['name']}{$opts} x 1 - ¥" . number_format($item['price']) . "\n";
}

$subject = "【技巧 -Giko-】ご注文ありがとうございます";
$paymentMethodStr = ($paymentMethod === 'card') ? "クレジットカード決済" : "銀行振込";

$bankInfo = "";
if ($paymentMethod === 'transfer') {
    $bankInfo = "\n【お振込先】\n○○銀行 ××支店\n普通 1234567\nギコ サンマルナナゴウドウガイシャ\n※7日以内にお振込みください。\n";
}

$body = <<<EOD
{$name} 様

この度は「技巧 -Giko-」をご利用いただき誠にありがとうございます。
以下の内容でご注文を承りました。

■ご注文内容
{$itemListStr}
--------------------------------------------------
合計金額 (税込): ¥{number_format($amount)}

■お支払い方法
{$paymentMethodStr}
{$bankInfo}
■お届け先
〒{$zip}
{$address}
{$name} 様
{$phone}

ご不明な点がございましたら、お気軽にお問い合わせください。

--------------------------------------------------
技巧 -Giko- (GIKO307合同会社)
URL: https://giko-artisan.jp/
E-mail: info@giko-artisan.jp
--------------------------------------------------
EOD;

$headers = "From: {$emailFrom}";

// Send to User
@mb_send_mail($email, $subject, $body, $headers);

// Send to Admin
$adminSubject = "【新規注文】{$name}様より注文が入りました";
$adminBody = "新規注文がありました。\n\n" . $body;
@mb_send_mail($adminEmail, $adminSubject, $adminBody, $headers);

// 4. Redirect to Success
header('Location: ../order_complete.html?tid=' . $paymentId);
exit;
?>
