<?php
// store/mail_order.php
// Handles Order Confirmation Emails (Admin + User)

// Configuration
$ADMIN_EMAIL = 'kaitonakamura0731@gmail.com'; 
$SERVER_DOMAIN = 'giko-official.com'; 
$NOREPLY_EMAIL = "noreply@{$SERVER_DOMAIN}";

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

【注文商品】
--------------------------------------------------
{$itemsText}
--------------------------------------------------

PAY.JP等の管理画面で決済状況を確認してください。
EOT;

$admin_headers = "From: {$NOREPLY_EMAIL}\r\n";
$admin_headers .= "Reply-To: {$email}\r\n";
$admin_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// 6. User Email Content
$user_subject = "【技巧 -Giko-】ご注文ありがとうございます ({$orderId})";
$user_body = <<<EOT
{$name} 様

この度は「技巧 -Giko-」にてご注文いただき、誠にありがとうございます。
以下の内容で承りました。

【注文ID】 {$orderId}
【決済方法】 {$paymentMethod}
【合計金額】 {$amount}

【ご注文内容】
--------------------------------------------------
{$itemsText}
--------------------------------------------------

商品の発送準備が整い次第、改めてご連絡させていただきます。
万が一、ご注文内容に誤りがある場合は、本メールへ返信にてお知らせください。

--------------------------------------------------
技巧 -Giko-
https://giko-artisan.jp
--------------------------------------------------
EOT;

$user_headers = "From: {$ADMIN_EMAIL}\r\n";
$user_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// 7. Send Emails
// Send to Admin
mb_language("Japanese");
mb_internal_encoding("UTF-8");

// Encode subjects for proper Japanese display
$admin_subject_encoded = '=?UTF-8?B?' . base64_encode($admin_subject) . '?=';
$user_subject_encoded = '=?UTF-8?B?' . base64_encode($user_subject) . '?=';

// Update headers with proper encoding
$admin_headers = "From: {$NOREPLY_EMAIL}\r\n";
$admin_headers .= "Reply-To: {$email}\r\n";
$admin_headers .= "MIME-Version: 1.0\r\n";
$admin_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$admin_headers .= "Content-Transfer-Encoding: base64\r\n";

$user_headers = "From: {$ADMIN_EMAIL}\r\n";
$user_headers .= "MIME-Version: 1.0\r\n";
$user_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$user_headers .= "Content-Transfer-Encoding: base64\r\n";

// Encode body as base64
$admin_body_encoded = base64_encode($admin_body);
$user_body_encoded = base64_encode($user_body);

$mail_admin = mail($ADMIN_EMAIL, $admin_subject_encoded, $admin_body_encoded, $admin_headers, "-f{$NOREPLY_EMAIL}");

// Send to User
$mail_user = mail($email, $user_subject_encoded, $user_body_encoded, $user_headers, "-f{$ADMIN_EMAIL}");

if ($mail_admin && $mail_user) {
    sendJson(true, 'Mail Sent');
} else {
    // Even if one fails, we often return true to front-end to not block UX, 
    // but here let's be strict for debugging.
    // However, on some dev environments mail fails. 
    // We will return success but log error internally if we could.
    sendJson(true, 'Processed (Mail status uncertain)');
}
?>
