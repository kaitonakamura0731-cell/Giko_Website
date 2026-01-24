const express = require('express');
const Payjp = require('payjp');
const app = express();

// 自分の秘密鍵を設定
const payjp = Payjp('sk_test_63be8260adb3bec84d5319e9');

// フォームデータを受け取るための設定
app.use(express.urlencoded({ extended: true }));

// 決済ページ (index.html) の表示
app.get('/', (req, res) => {
    res.sendFile(__dirname + '/index.html');
});

// 決済処理の実行
app.post('/pay', async (req, res) => {
    try {
        // フロントから送られてきたトークン(card_token)を取得
        const token = req.body['payjp-token'];

        // Pay.jpへ支払い確定リクエスト
        const charge = await payjp.charges.create({
            amount: 1000,     // 1000円
            currency: 'jpy',  // 日本円
            card: token,      // カードトークン
        });

        res.send(`決済成功！ ID: ${charge.id}`);
    } catch (e) {
        res.send(`決済失敗... ${e.message}`);
    }
});

app.listen(3000, () => {
    console.log('サーバー起動中: http://localhost:3000');
});
