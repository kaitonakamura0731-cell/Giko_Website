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
    $amount = filter_var($data['amount'] ?? 0, FILTER_VALIDATE_INT);

    if (!$token || !$amount || $amount < 50) {
        throw new Exception('Invalid parameters: Token or Amount (Min 50 JPY) is missing/invalid.', 400);
    }

    // 4. Store order data in session for 3DS callback
    if (!empty($data['order_data'])) {
        $_SESSION['pending_order'] = $data['order_data'];
    }

    // 5. Prepare Idempotency Key
    $idempotencyKey = null;
    $headers = getallheaders();
    foreach ($headers as $key => $value) {
        if (strtolower($key) === 'x-idempotency-key') {
            $idempotencyKey = $value;
            break;
        }
    }

    // 6. Create Charge with 3D Secure
    $url = 'https://api.pay.jp/v1/charges';
    $fields = [
        'amount' => $amount,
        'currency' => 'jpy',
        'card' => $token,
        'capture' => 'true',
        'three_d_secure' => 'true'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERPWD, $PAYJP_SECRET_KEY . ':');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    if ($idempotencyKey) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Idempotency-Key: ' . $idempotencyKey
        ]);
    }

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
        $backUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
                   . '://' . $_SERVER['HTTP_HOST']
                   . dirname($_SERVER['REQUEST_URI']) . '/tds_callback.php';

        $tdsUrl = 'https://api.pay.jp/v1/tds/' . $chargeId . '/start'
                . '?publickey=' . urlencode(PAYJP_PUBLIC_KEY)
                . '&back_url=' . urlencode($backUrl);

        sendResponse(true, [
            'id' => $chargeId,
            'requires_3ds' => true,
            'tds_url' => $tdsUrl
        ]);
    }

} catch (Exception $e) {
    if ($e->getCode() === 405) {
        sendResponse(false, ['error' => $e->getMessage()], 405);
    } else {
        $status = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
        sendResponse(false, ['error' => $e->getMessage()], $status);
    }
}
?>
