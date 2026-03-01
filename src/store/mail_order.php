<?php
// store/mail_order.php
// Handles Order Confirmation Emails (Admin + User)

session_start();

// セッションに保留中の注文がない場合は不正リクエストとして拒否
if (empty($_SESSION['pending_order'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Configuration
$ADMIN_EMAIL = 'kaitonakamura0731@gmail.com';
$ADMIN_CC = 'info@giko-official.com';
$SERVER_DOMAIN = 'giko-official.com';
$NOREPLY_EMAIL = "noreply@{$SERVER_DOMAIN}";

// 銀行振込先情報（order_complete.html と合わせて更新してください）
$BANK_INFO = [
    'bank_name'    => 'テスト銀行',
    'branch_name'  => 'サンプル支店（999）',
    'account_type' => '普通',
    'account_no'   => '1234567',
    'account_holder' => 'カ）テストカイシャ',
];

// Headers helpers
function clean_header($str) {
    return str_replace(["\r", "\n"], '', $str);
}

// Response helper
function sendJson($success, $message = '') {
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

// 1. Check Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(false, 'Method Not Allowed');
}

// 2. Get Data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    sendJson(false, 'Invalid JSON');
}

// 3. Extract & Sanitize
$name = isset($data['name']) ? $data['name'] : '';
$email = isset($data['email']) ? clean_header($data['email']) : '';
$phone = isset($data['phone']) ? $data['phone'] : '';
$zip = isset($data['zip']) ? $data['zip'] : '';
$address = isset($data['address']) ? $data['address'] : '';
$orderId = isset($data['orderId']) ? $data['orderId'] : '';
$amount = isset($data['amount']) ? $data['amount'] : '';
$paymentMethod = isset($data['paymentMethod']) ? $data['paymentMethod'] : '';
$items = isset($data['items']) ? $data['items'] : [];

if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJson(false, 'Invalid Input');
}

// 4. Build Item List String
$itemsText = "";
foreach ($items as $item) {
    $opt = isset($item['options']) ? " (" . implode('/', $item['options']) . ")" : "";
    $price = number_format($item['price']);
    $qty = isset($item['quantity']) ? $item['quantity'] : 1;
    $itemsText .= "- {$item['name']}{$opt} x {$qty} : ¥{$price}\n";
}

// 5. Admin Email Content
$admin_subject = "【技巧 -Giko-】新規注文受信 ({$orderId})";
$admin_body = <<<EOT
新規の注文が入りました。

【注文ID】 {$orderId}
【決済方法】 {$paymentMethod}
【合計金額】 {$amount}

【お客様情報】
名前: {$name}
Email: {$email}
電話番号: {$phone}
郵便番号: {$zip}
住所: {$address}

【注文商品】
--------------------------------------------------
{$itemsText}
--------------------------------------------------

PAY.JP等の管理画面で決済状況を確認してください。
EOT;

// 6. User Email Content
$CONTACT_EMAIL = 'info@giko-official.com';

// Build customer info block
$customerInfo = "お名前: {$name}\n";
$customerInfo .= "メール: {$email}\n";
if (!empty($phone)) $customerInfo .= "電話番号: {$phone}\n";
if (!empty($zip)) $customerInfo .= "郵便番号: {$zip}\n";
if (!empty($address)) $customerInfo .= "ご住所: {$address}\n";

// 銀行振込の場合は振込先情報ブロックを生成
$bankInfoBlock = '';
if (strtoupper($paymentMethod) === 'BANK TRANSFER') {
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
}

$user_subject = "【技巧 -Giko-】ご注文ありがとうございます ({$orderId})";
$user_body = <<<EOT
{$name} 様

この度は「技巧 -Giko-」にてご注文いただき、誠にありがとうございます。
以下の内容で承りました。

【注文ID】 {$orderId}
【決済方法】 {$paymentMethod}
【合計金額】 {$amount}

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

// 7. Send Emails
mb_language("Japanese");
mb_internal_encoding("UTF-8");

// Admin headers (mb_send_mailがContent-Typeを自動設定)
$admin_headers = "From: {$NOREPLY_EMAIL}\r\n";
$admin_headers .= "Reply-To: {$email}\r\n";
$admin_headers .= "Cc: {$ADMIN_CC}\r\n";

// User headers
$user_headers = "From: {$NOREPLY_EMAIL}\r\n";
$user_headers .= "Reply-To: {$ADMIN_EMAIL}\r\n";

// Send to Admin
$mail_admin = mb_send_mail($ADMIN_EMAIL, $admin_subject, $admin_body, $admin_headers, "-f{$NOREPLY_EMAIL}");

// Send to User
$mail_user = mb_send_mail($email, $user_subject, $user_body, $user_headers, "-f{$NOREPLY_EMAIL}");

// メール送信後にセッションの注文データをクリア（再利用防止）
unset($_SESSION['pending_order']);

if ($mail_admin && $mail_user) {
    sendJson(true, 'Mail Sent');
} else {
    sendJson(true, 'Processed (Mail status uncertain)');
}
?>
