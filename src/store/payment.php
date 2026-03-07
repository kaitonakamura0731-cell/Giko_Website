<?php
// payment.php
// PAY.JP Payment Processing Backend with 3D Secure Support

session_start();

// ---------------------------------------------------------
// CONFIGURATION
// ---------------------------------------------------------

require_once __DIR__ . '/../config/api_keys.php';
$PAYJP_SECRET_KEY = PAYJP_SECRET_KEY;

// ---------------------------------------------------------
// HELPERS
// ---------------------------------------------------------

function sendResponse($success, $data = [], $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode(array_merge(['success' => $success], $data));
    exit;
}

if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

// ---------------------------------------------------------
// MAIN LOGIC
// ---------------------------------------------------------

try {
    // 1. Only allow POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method Not Allowed', 405);
    }

    // 2. Get Input Data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        throw new Exception('Invalid JSON data', 400);
    }

    // 3. Validation
    $token = $data['token'] ?? null;
    $clientAmount = filter_var($data['amount'] ?? 0, FILTER_VALIDATE_INT);

    if (!$token || !$clientAmount || $clientAmount < 50) {
        throw new Exception('Invalid parameters: Token or Amount (Min 50 JPY) is missing/invalid.', 400);
    }

    // 3b. セッションの検証済み金額（card_entry.php でDB照合済み）を使用
    //     クライアント側の金額改ざんを完全に防止
    if (empty($_SESSION['pending_order']['amount'])) {
        throw new Exception('不正なリクエストです。もう一度お試しください。', 403);
    }
    $sessionAmount = (int)$_SESSION['pending_order']['amount'];
    if ($sessionAmount !== $clientAmount) {
        throw new Exception('金額が一致しません。もう一度お試しください。', 400);
    }
    // DB検証済みのセッション金額を決済に使用（クライアント金額は信用しない）
    $amount = $sessionAmount;

    // 3c. 二重課金防止：既にこのセッションで決済処理中の場合はブロック
    if (!empty($_SESSION['payment_processing'])) {
        throw new Exception('決済処理中です。画面を閉じずにお待ちください。', 409);
    }
    $_SESSION['payment_processing'] = true;

    // 4. セッションの order_data はクライアントからの上書きを許可しない
    //    card_entry.php で検証・設定済みのデータのみ使用する

    // 5. Prepare Idempotency Key（セッション単位で固定し、リトライでも同一キーを使用）
    if (empty($_SESSION['payment_idempotency_key'])) {
        $_SESSION['payment_idempotency_key'] = 'order_' . bin2hex(random_bytes(16));
    }
    $idempotencyKey = $_SESSION['payment_idempotency_key'];

    // 6. Customer作成は削除（一回限りの購入にはCustomer不要）
    //    トークンで直接Chargeを作成する（不要なAPIコールと¥11謎課金の原因排除）

    // 7. Create Charge with 3D Secure（トークン直接利用）
    $url = 'https://api.pay.jp/v1/charges';
    $fields = [
        'amount' => $amount,
        'currency' => 'jpy',
        'capture' => 'true',
        'three_d_secure' => 'true',
        'card' => $token
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERPWD, $PAYJP_SECRET_KEY . ':');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Idempotency-Key: ' . $idempotencyKey
    ]);

    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // 7. Handle Errors
    if ($curlError) {
        throw new Exception('Connection Error: ' . $curlError, 503);
    }

    $result = json_decode($response, true);

    if ($httpStatus < 200 || $httpStatus >= 300) {
        $msg = $result['error']['message'] ?? 'Unknown payment error';
        throw new Exception($msg, 400);
    }

    // 8. Check 3D Secure Status
    $tdsStatus = $result['three_d_secure_status'] ?? null;
    $chargeId = $result['id'];
    $paid = $result['paid'] ?? false;

    if ($paid && ($tdsStatus === 'verified' || $tdsStatus === null)) {
        // 3DS not required or already verified → Charge complete
        sendResponse(true, [
            'id' => $chargeId,
            'amount' => $result['amount'],
            'status' => 'captured',
            'requires_3ds' => false
        ]);
    } else {
        // 3DS authentication required → Need redirect
        // Store charge ID in session
        $_SESSION['pending_charge_id'] = $chargeId;

        // Build 3DS redirect URL
        // back_urlはJWS (HS256) で署名が必要（PAY.JP仕様）
        $rawBackUrl = 'https://giko-official.com/store/tds_callback.php';

        // JWS署名を生成
        $jwsHeader = rtrim(strtr(base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT'])), '+/', '-_'), '=');
        $jwsPayload = rtrim(strtr(base64_encode(json_encode(['url' => $rawBackUrl])), '+/', '-_'), '=');
        $jwsSignature = rtrim(strtr(base64_encode(hash_hmac('sha256', "$jwsHeader.$jwsPayload", $PAYJP_SECRET_KEY, true)), '+/', '-_'), '=');
        $jwsBackUrl = "$jwsHeader.$jwsPayload.$jwsSignature";

        $tdsUrl = 'https://api.pay.jp/v1/tds/' . $chargeId . '/start'
                . '?publickey=' . urlencode(PAYJP_PUBLIC_KEY)
                . '&back_url=' . urlencode($jwsBackUrl);

        sendResponse(true, [
            'id' => $chargeId,
            'requires_3ds' => true,
            'tds_url' => $tdsUrl
        ]);
    }

} catch (Exception $e) {
    // 決済失敗時はフラグをリセット（再試行を許可）
    unset($_SESSION['payment_processing']);
    unset($_SESSION['payment_idempotency_key']);

    if ($e->getCode() === 405) {
        sendResponse(false, ['error' => $e->getMessage()], 405);
    } else {
        $status = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
        sendResponse(false, ['error' => $e->getMessage()], $status);
    }
}
?>
