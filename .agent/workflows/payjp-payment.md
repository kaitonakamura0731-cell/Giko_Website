---
description: PAY.JP決済機能の実装手順（3Dセキュア対応）
---

# PAY.JP 決済機能実装ガイド

## 概要
ECサイトにPAY.JP V2 APIを使用したクレジットカード決済機能を追加する手順。
3Dセキュア認証に対応。

---

## 必要なファイル構成

```
store/
├── checkout.php      # 配送情報入力ページ
├── card_entry.php    # カード情報入力ページ（PAY.JP Elements）
├── payment.php       # バックエンド決済処理API
├── mail_order.php    # 注文完了メール送信
└── order_complete.html # 完了ページ
```

---

## Step 1: PAY.JP APIキーの取得

1. [PAY.JP](https://pay.jp/) にアカウント作成
2. ダッシュボードから取得:
   - **公開鍵 (pk_test_xxx / pk_live_xxx)** → フロントエンドで使用
   - **秘密鍵 (sk_test_xxx / sk_live_xxx)** → バックエンドで使用

---

## Step 2: card_entry.php の作成

```php
<?php
session_start();
// POSTデータ受信・検証
$name = htmlspecialchars($_POST['name']);
$email = htmlspecialchars($_POST['email']);
$amount = (int)$_POST['amount'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <script src="https://js.pay.jp/v2/pay.js"></script>
</head>
<body>
    <!-- カード入力フォーム -->
    <input type="text" id="cardholder-name" placeholder="TARO YAMADA">
    <div id="card-element"></div>
    <button onclick="handlePayment()">決済する</button>

    <input type="hidden" id="data-email" value="<?php echo $email; ?>">
    <input type="hidden" id="data-amount" value="<?php echo $amount; ?>">

    <script>
        const PUBLIC_KEY = 'pk_test_xxxxxxxx'; // ←公開鍵を設定
        let payjp, cardElement;

        document.addEventListener('DOMContentLoaded', () => {
            payjp = Payjp(PUBLIC_KEY);
            const elements = payjp.elements();
            cardElement = elements.create('card', {
                style: { base: { color: '#333', fontSize: '16px' } }
            });
            cardElement.mount('#card-element');
        });

        async function handlePayment() {
            const cardholderName = document.getElementById('cardholder-name').value.trim().toUpperCase();
            const email = document.getElementById('data-email').value;
            const amount = document.getElementById('data-amount').value;

            const result = await payjp.createToken(cardElement, {
                three_d_secure: true,
                card: { name: cardholderName, email: email }
            });

            if (result.error) {
                alert(result.error.message);
                return;
            }

            // バックエンドに送信
            const response = await fetch('./payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ token: result.id, amount: amount })
            });
            const data = await response.json();
            if (data.success) {
                window.location.href = 'order_complete.html?order_id=' + data.id;
            } else {
                alert(data.error);
            }
        }
    </script>
</body>
</html>
```

---

## Step 3: payment.php の作成

```php
<?php
header('Content-Type: application/json');

// 秘密鍵
$SECRET_KEY = 'sk_test_xxxxxxxx'; // ←秘密鍵を設定

$input = json_decode(file_get_contents('php://input'), true);
$token = $input['token'] ?? '';
$amount = (int)($input['amount'] ?? 0);

if (!$token || $amount <= 0) {
    echo json_encode(['success' => false, 'error' => '無効なリクエスト']);
    exit;
}

$ch = curl_init('https://api.pay.jp/v1/charges');
curl_setopt_array($ch, [
    CURLOPT_USERPWD => $SECRET_KEY . ':',
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'amount' => $amount,
        'currency' => 'jpy',
        'card' => $token,
        'description' => 'Order from Website'
    ]),
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);

if ($httpCode === 200 && isset($result['id'])) {
    echo json_encode(['success' => true, 'id' => $result['id']]);
} else {
    $errorMsg = $result['error']['message'] ?? '決済に失敗しました';
    echo json_encode(['success' => false, 'error' => $errorMsg]);
}
```

---

## Step 4: PAY.JP ダッシュボード設定

### 3Dセキュア設定（必須）
1. PAY.JP管理画面 → 「設定」→「3Dセキュア」
2. リダイレクトURL登録:
   ```
   https://your-domain.com/store/order_complete.html
   ```
3. テスト・本番両方に設定

---

## Step 5: テスト

### テストカード番号

| カード番号 | 用途 |
|---|---|
| `4242 4242 4242 4242` | 通常決済 |
| `4000 0000 0000 2535` | 3Dセキュア成功 |
| `4000 0000 0000 2543` | 3Dセキュア失敗 |
| `5555 5555 5555 4444` | Mastercard |

- 有効期限: 未来の日付
- CVC: 任意の3桁
- 名義: 英字

---

## Step 6: 本番切り替え

1. `card_entry.php` の `PUBLIC_KEY` を `pk_live_xxx` に変更
2. `payment.php` の `SECRET_KEY` を `sk_live_xxx` に変更
3. ファイルをサーバーにアップロード

---

## トラブルシューティング

| エラー | 原因 | 対処 |
|---|---|---|
| `three_d_secure_failed` | 3DS非対応カード使用 | `4000 0000 0000 2535` を使用 |
| `incorrect_card_data` | カード番号が不正 | PAY.JPテストカードを使用 |
| `card_declined` | カード拒否 | 別のカード番号を試す |
| 決済がダッシュボードに表示されない | APIキー不一致 | キーを確認して再設定 |

---

## 参考リンク
- [PAY.JP API ドキュメント](https://pay.jp/docs/api/)
- [PAY.JP Elements V2](https://pay.jp/docs/payjs-v2)
- [3Dセキュア設定ガイド](https://pay.jp/docs/3dsecure)
