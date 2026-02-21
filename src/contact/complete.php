<?php
session_start();

// ---------------------------------------------------------
// CONFIGURATION
// ---------------------------------------------------------
// Set your admin email address here
$ADMIN_EMAIL = 'kaitonakamura0731@gmail.com';

// ---------------------------------------------------------
// SECURITY CHECKS
// ---------------------------------------------------------

// 1. CSRF Token Validation
if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: contact.php?error=csrf');
    exit;
}

// 2. Request Method Check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

// ---------------------------------------------------------
// DATA RETRIEVAL & SANITIZATION (Strict)
// ---------------------------------------------------------
// *Note*: Data was already htmlspecialchar'd for display in confirm.php, 
// but passing it via hidden fields means we should treat it as raw input again if we didn't decode it.
// However, since we used `clean()` (htmlspecialchars) before outputting to hidden value, 
// the value posted here IS safe HTML entities. 
// For email sending, we usually want "Raw" text (not &lt; &gt;), 
// so strictly speaking we should html_entity_decode for the MAIL BODY, 
// but ensure headers are absolutely clean (no newlines).

function clean_header($str)
{
    // Remove newlines to prevent Header Injection
    return str_replace(["\r", "\n"], '', $str);
}

// Retrieve rawPOST data and re-sanitize for context
$name = isset($_POST['name']) ? $_POST['name'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$tel = isset($_POST['tel']) ? $_POST['tel'] : '';
$subject_in = isset($_POST['subject']) ? $_POST['subject'] : '';
$message_in = isset($_POST['message']) ? $_POST['message'] : '';

// Decode entities for email body (readability)
$name_txt = html_entity_decode($name, ENT_QUOTES, 'UTF-8');
$email_txt = html_entity_decode($email, ENT_QUOTES, 'UTF-8');
$subject_txt = html_entity_decode($subject_in, ENT_QUOTES, 'UTF-8');
$message_txt = html_entity_decode($message_in, ENT_QUOTES, 'UTF-8');

// Strict Mail Header sanitization
$email_safe = clean_header($email_txt);
if (!filter_var($email_safe, FILTER_VALIDATE_EMAIL)) {
    header('Location: contact.php?error=email');
    exit;
}

// ---------------------------------------------------------
// ---------------------------------------------------------
// SEND EMAILS with Better SPF/DKIM Compliance
// ---------------------------------------------------------

// Best Practice: 
// 1. "From" should be an address on THIS server (e.g. your ConoHa username or specific domain).
// 2. "Reply-To" is the user's email.
// * Since we don't know the server domain, we try to use ADMIN_EMAIL or a generated one.
// * Often 'kaitonakamura0731@gmail.com' as FROM will fail SPF if sent from ConoHa.
// * We need to set envelope sender (-f) for sendmail.

$server_domain = 'giko-official.com'; // Explicitly set user's domain
$noreply_email = "noreply@{$server_domain}";

// A. Notification to Admin
$admin_subject = "【技巧 -Giko-】お問い合わせ: " . mb_substr($subject_txt, 0, 20);
$admin_body = <<<EOT
ウェブサイトからお問い合わせがありました。

【名前】 {$name_txt}
【Email】 {$email_safe}
【電話】 {$tel}
【件名】 {$subject_txt}

【内容】
--------------------------------------------------
{$message_txt}
--------------------------------------------------
EOT;

// Use User's email as From only if SPF allows (Risky on shared hosts).
// Safer: From: noreply@server, Reply-To: user@email
$admin_headers = "From: {$noreply_email}\r\n";
$admin_headers .= "Reply-To: {$email_safe}\r\n";
$admin_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$admin_headers .= "X-Mailer: PHP/" . phpversion();

// Try sending
$mail_sent = mb_send_mail($ADMIN_EMAIL, $admin_subject, $admin_body, $admin_headers, "-f{$noreply_email}");

// Debugging: If failed, check error
if (!$mail_sent) {
    $err = error_get_last();
    // Log this error to a file or show (for now user just wants it to work)
    // error_log("Mail Error: " . print_r($err, true));
}

// B. Auto-Reply to User
if ($mail_sent) {
    $user_subject = "【技巧 -Giko-】お問い合わせありがとうございます";
    $user_body = <<<EOT
{$name_txt} 様

この度は「技巧 -Giko-」へお問い合わせいただき、誠にありがとうございます。
以下の内容で受け付けいたしました。

【件名】 {$subject_txt}
【内容】
--------------------------------------------------
{$message_txt}
--------------------------------------------------

担当者より折り返しご連絡させていただきます。

【今後のご連絡先】
ご質問やご相談がございましたら、下記までお気軽にご連絡ください。
Email: {$ADMIN_EMAIL}

--------------------------------------------------
技巧 -Giko-
https://giko-official.com
--------------------------------------------------
EOT;

    $user_headers = "From: {$noreply_email}\r\n";
    $user_headers .= "Reply-To: {$ADMIN_EMAIL}\r\n";
    $user_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $user_headers .= "X-Mailer: PHP/" . phpversion();

    @mb_send_mail($email_safe, $user_subject, $user_body, $user_headers, "-f{$noreply_email}");

    // Regenerate Token
    unset($_SESSION['csrf_token']);
} else {
    // Redirect with debug info if possible or just generic error
    header('Location: contact.php?error=send_failed');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENT | 技巧 -Giko-</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../tailwind_config.js"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-black text-white antialiased">

    <!-- Header -->
    <header class="fixed w-full z-50 transition-all duration-300 bg-black/80 backdrop-blur-md border-b border-white/5" id="header">
        <div class="container mx-auto px-6 h-20 flex justify-between items-center">
            <a href="../index.php" class="flex items-center group">
                <img src="../assets/images/logo_new.png" alt="GIKO" class="h-10 group-hover:opacity-80 transition-opacity">
            </a>
            <a href="../index.php"
                class="text-xs font-bold font-en tracking-widest hover:text-primary transition-colors flex items-center gap-2">
                <i class="fas fa-home"></i> HOME
            </a>
        </div>
    </header>

    <div class="text-center p-8 max-w-lg mx-auto flex flex-col items-center justify-center min-h-screen" style="padding-top: 6rem;">
        <div
            class="w-24 h-24 bg-primary rounded-full flex items-center justify-center text-black text-4xl mx-auto mb-8 shadow-lg shadow-primary/30">
            <i class="fas fa-paper-plane"></i>
        </div>

        <h1 class="text-3xl font-bold font-en tracking-widest mb-4">MESSAGE SENT</h1>
        <p class="text-xl mb-6">送信が完了しました。</p>
        <p class="text-gray-400 text-sm mb-8 leading-relaxed">
            お問い合わせありがとうございます。<br>
            ご入力いただいたメールアドレスへ<br>自動返信メールを送信しました。
        </p>

        <a href="../index.php"
            class="inline-block border border-white/20 px-10 py-4 hover:bg-white hover:text-black transition-colors rounded-sm text-sm tracking-widest font-en">
            TOPに戻る
        </a>
    </div>

    <!-- Footer -->
    <footer class="bg-secondary pt-24 pb-12 border-t border-white/5 text-white">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-20">
                <div>
                    <img src="../assets/images/logo_new.png" alt="GIKO" class="h-8 mb-6">
                    <p class="text-xs text-gray-500 leading-loose mb-6">最高級の素材と技術で、カーライフに彩りを。</p>
                    <div class="flex space-x-4">
                        <a href="https://www.instagram.com/giko_artisan?igsh=MWRuenVqMzBkNzA3bw==" target="_blank"
                            class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary transition-colors"><i
                                class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-bold font-en tracking-widest mb-6 border-b border-primary/30 inline-block pb-2">MENU</h3>
                    <ul class="space-y-4 text-xs tracking-wider text-gray-400">
                        <li><a href="../index.php#concept" class="hover:text-white transition-colors">CONCEPT</a></li>
                        <li><a href="../pages/works.php" class="hover:text-white transition-colors">WORKS</a></li>
                        <li><a href="../pages/before_after.html" class="hover:text-white transition-colors">BEFORE & AFTER</a></li>
                        <li><a href="../index.php#flow" class="hover:text-white transition-colors">FLOW</a></li>
                        <li><a href="../index.php#company" class="hover:text-white transition-colors">COMPANY</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-bold font-en tracking-widest mb-6 border-b border-primary/30 inline-block pb-2">CONTACT</h3>
                    <ul class="space-y-4 text-xs tracking-wider text-gray-400">
                        <li><a href="index.php" class="hover:text-white transition-colors">お問い合わせフォーム</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-bold font-en tracking-widest mb-6 border-b border-primary/30 inline-block pb-2">LEGAL</h3>
                    <ul class="space-y-4 text-xs tracking-wider text-gray-400">
                        <li><a href="../legal/privacy.html" class="hover:text-white transition-colors">プライバシーポリシー</a></li>
                        <li><a href="../legal/tokusho.html" class="hover:text-white transition-colors">特定商取引法に基づく表記</a></li>
                        <li><a href="../legal/terms.html" class="hover:text-white transition-colors">利用規約</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/5 pt-8 flex justify-between items-center text-[10px] text-gray-600 font-en tracking-widest">
                <p>&copy; 2025 GIKO. ALL RIGHTS RESERVED.</p>
                <div>DESIGNED BY ATLASSHIFT</div>
            </div>
        </div>
    </footer>
    <script src="../assets/js/main.js"></script>

</body>

</html>
