<?php
// payment.php
// PAY.JP Payment Processing Backend (v2 SDK Compatible)

// ---------------------------------------------------------
// CONFIGURATION
// ---------------------------------------------------------

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// IMPORTANT: Replace this with your actual Secret Key
// PAY.JP Dashboard -> Settings -> API Keys -> Secret Key
// Start with 'sk_...'
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
$PAYJP_SECRET_KEY = 'sk_test_1a1ce5f8922f49b299eb99d4'; // Test Secret Key

// ---------------------------------------------------------
// HELPERS
// ---------------------------------------------------------

function sendResponse($success, $data = [], $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode(array_merge(['success' => $success], $data));
    exit;
}

// Fallback for getting headers (Nginx/FPM sometimes misses getallheaders)
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
    
    if (!$token || !$amount || $amount < 50) { // PAY.JP min amount is 50
        throw new Exception('Invalid parameters: Token or Amount (Min 50 JPY) is missing/invalid.', 400);
    }

    // 4. Prepare Idempotency Key (Prevent double billing)
    $idempotencyKey = null;
    $headers = getallheaders();
    foreach ($headers as $key => $value) {
        if (strtolower($key) === 'x-idempotency-key') {
            $idempotencyKey = $value;
            break;
        }
    }

    // 5. Make Request to PAY.JP API
    // Since we use v2 JS SDK, the token is already 3DS verified if needed.
    // We just need to capture the charge.
    $url = 'https://api.pay.jp/v1/charges';
    $fields = [
        'amount' => $amount,
        'currency' => 'jpy',
        'card' => $token,
        'capture' => 'true'
    ];
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERPWD, $PAYJP_SECRET_KEY . ':'); // Basic Auth
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout
    
    // Set Idempotency Key if valid
    if ($idempotencyKey) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Idempotency-Key: ' . $idempotencyKey
        ]);
    }

    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    
    curl_close($ch);

    // 6. Handle Error / Success
    if ($curlError) {
        throw new Exception('Connection Error: ' . $curlError, 503);
    }

    $result = json_decode($response, true);

    if ($httpStatus >= 200 && $httpStatus < 300) {
        // Success
        sendResponse(true, [
            'id' => $result['id'],
            'amount' => $result['amount'],
            'status' => 'captured' // v2 immediate capture
        ]);
    } else {
        // API Returned Error
        $msg = $result['error']['message'] ?? 'Unknown payment error';
        $code = $result['error']['code'] ?? 'unknown_error';
        
        // Log error internally if needed
        // error_log("PAY.JP Error: $msg ($code)");
        
        throw new Exception($msg, 400); // Return 400 for bad request logic
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
