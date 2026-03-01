<?php
/**
 * validate_cart.php
 * サーバーサイドでカート内商品の価格をDBと照合し、正しい合計金額を算出する
 * クライアント側（localStorage）の価格改ざんを防止するための検証ロジック
 */

/**
 * カート内の商品価格をデータベースと照合し、正確な合計金額を返す
 *
 * @param PDO   $pdo       データベース接続
 * @param array $cartItems カート内商品の配列（JSON デコード済み）
 * @return array ['subtotal' => int, 'items' => array]
 * @throws Exception 無効な商品やDB不整合がある場合
 */
function validateCartPrices($pdo, $cartItems)
{
    $subtotal = 0;
    $verifiedItems = [];

    foreach ($cartItems as $item) {
        $productId = $item['id'] ?? null;
        if (!$productId) {
            throw new Exception('商品IDが無効です。');
        }

        // "product_123" → "123"
        $numericId = preg_replace('/[^0-9]/', '', $productId);
        if (!$numericId) {
            throw new Exception('商品IDの形式が無効です。');
        }

        $stmt = $pdo->prepare("SELECT id, name, price, trade_in_discount FROM products WHERE id = ?");
        $stmt->execute([$numericId]);
        $dbProduct = $stmt->fetch();

        if (!$dbProduct) {
            throw new Exception('商品が見つかりません。カートを確認してください。');
        }

        // DB の価格を整数に変換（カンマ区切り文字列の場合にも対応）
        $dbPrice = (int) str_replace(',', '', (string) $dbProduct['price']);
        $tradeInDiscount = (int) ($dbProduct['trade_in_discount'] ?? 10000);

        // 下取りオプションの判定（cart.js のロジックをサーバー側で再現）
        $hasTradeInOption = false;
        $hasTradeIn = false;
        $options = $item['options'] ?? [];
        $tradeInKeys = ['下取り交換', '下取り', 'トレードイン', '下取交換'];
        $tradeInValues = ['あり', 'する', 'yes', 'true', '有'];

        foreach ($tradeInKeys as $key) {
            if (isset($options[$key])) {
                $hasTradeInOption = true;
                $val = mb_strtolower((string) $options[$key]);
                foreach ($tradeInValues as $tv) {
                    if (mb_strpos($val, mb_strtolower($tv)) !== false) {
                        $hasTradeIn = true;
                        break 2;
                    }
                }
            }
        }

        // 下取りオプションがあるのに買取を選んでいない場合、追加費用が発生
        $additionalCost = ($hasTradeInOption && !$hasTradeIn) ? $tradeInDiscount : 0;
        $itemTotal = $dbPrice + $additionalCost;

        $verifiedItems[] = [
            'name'           => $dbProduct['name'],
            'price'          => $dbPrice,
            'options'        => $options,
            'quantity'       => 1,
            'additionalCost' => $additionalCost,
        ];

        $subtotal += $itemTotal;
    }

    if (empty($verifiedItems)) {
        throw new Exception('有効な商品がありません。');
    }

    return ['subtotal' => $subtotal, 'items' => $verifiedItems];
}
