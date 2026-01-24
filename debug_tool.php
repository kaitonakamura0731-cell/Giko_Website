<?php
// debug_tool.php
// サーバー環境とメール送信のテストを行うツール
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$results = [];

// 1. PHP Version
$results['php_version'] = phpversion();

// 2. Mail Test
$to = 'kaitonakamura0731@gmail.com'; // Admin Email
$subject = 'Test Mail from Server';
$message = 'This is a test mail.';
$headers = 'From: noreply@' . $_SERVER['SERVER_NAME'];

// Try basic mail()
$mail_basic = mail($to, $subject . ' (Basic)', $message, $headers);
$results['mail_basic'] = $mail_basic ? 'OK' : 'Failed';

// Try mb_send_mail()
mb_language("Japanese");
mb_internal_encoding("UTF-8");
$mail_mb = mb_send_mail($to, $subject . ' (MB)', $message, $headers);
$results['mail_mb'] = $mail_mb ? 'OK' : 'Failed';

// 3. Check for specific extensions
$results['curl'] = extension_loaded('curl') ? 'Enabled' : 'Disabled';
$results['openssl'] = extension_loaded('openssl') ? 'Enabled' : 'Disabled';

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Server Debug Tool</title>
    <style>body{font-family:sans-serif;padding:20px;background:#f0f0f0;} .card{background:white;padding:20px;margin-bottom:20px;border-radius:8px;} h2{margin-top:0;} .ok{color:green;font-weight:bold;} .fail{color:red;font-weight:bold;}</style>
</head>
<body>
    <h1>サーバー診断ツール</h1>
    
    <div class="card">
        <h2>1. メール送信テスト</h2>
        <p>送信先: <?php echo htmlspecialchars($to); ?></p>
        <ul>
            <li>mail()関数: <span class="<?php echo $results['mail_basic'] == 'OK' ? 'ok' : 'fail'; ?>"><?php echo $results['mail_basic']; ?></span></li>
            <li>mb_send_mail()関数: <span class="<?php echo $results['mail_mb'] == 'OK' ? 'ok' : 'fail'; ?>"><?php echo $results['mail_mb']; ?></span></li>
        </ul>
        <p>※「OK」でも届かない場合、迷惑メールフォルダか、ConoHaのメール設定（SPFレコード）の問題です。</p>
    </div>

    <div class="card">
        <h2>2. Pay.jp 表示確認</h2>
        <p>カード入力欄が表示されない場合、以下の公開鍵が間違っている可能性が高いです。</p>
        <p>現在の公開鍵設定（確認用）:</p>
        <div id="v2-card-element" style="border:1px solid #ccc; padding:10px; min-height:50px; background:white;"></div>
        <script src="https://js.pay.jp/v2/payjp.js"></script>
        <script>
            // ここにあなたが設定した公開鍵を入れてテストします
            const PUBLIC_KEY = 'pk_test_445533d2d990496d0d9266f0'; // ★ここが原因の可能性大
            
            try {
                const payjp = Payjp(PUBLIC_KEY);
                const elements = payjp.elements();
                const cardElement = elements.create('card');
                cardElement.mount('#v2-card-element');
                document.write('<p class="ok">Pay.jpライブラリ読み込み成功（入力欄が表示されていればKeyは正しい）</p>');
            } catch (e) {
                document.write('<p class="fail">Pay.jpエラー: ' + e.message + '</p>');
                document.write('<p><strong>公開鍵 (pk_test_...) が無効か、間違っています。</strong></p>');
            }
        </script>
    </div>
</body>
</html>
