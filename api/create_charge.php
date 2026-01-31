<?php
// Enable error reporting for debugging (Disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
    return true;
}

loadEnv(__DIR__ . '/../.env');

$secretKey = $_ENV['PAYJP_SECRET_KEY'] ?? '';

if (empty($secretKey)) {
    die("Error: PAY.JP Secret Key is missing.");
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Get Token from PAY.JP Checkout
    $token = $_POST['payjp-token'] ?? '';
    
    // In a real app, you would calculate the total amount from the cart items on the server side
    // to prevent tampering. For this demo, we'll take it from a hidden field or calculate it.
    // WARNING: Trusting client-side amount is dangerous. 
    // ideally: $amount = calculateTotalFromCart($_POST['cart_data']);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_INT);

    if (!$token || !$amount) {
        die("Error: Invalid Payment Data.");
    }

    // 2. Call PAY.JP API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.pay.jp/v1/charges');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERPWD, $secretKey . ':');
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'amount' => $amount,
        'currency' => 'jpy',
        'card' => $token,
        'description' => 'Giko Online Store Order',
        'capture' => 'true'
    ]));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($httpCode === 200 && isset($result['id'])) {
        // 3. Payment Success
        
        // TODO: Send Email Notification to Admin & User
        // sendOrderEmail($_POST, $result['id']);

        // Redirect to Success Page
        header('Location: ../order_complete.html?tid=' . $result['id']);
        exit;
    } else {
        // 4. Payment Failed
        $errorMsg = $result['error']['message'] ?? 'Unknown error';
        // Redirect to Error Page (or back to cart)
        header('Location: ../checkout.html?error=' . urlencode($errorMsg));
        exit;
    }
}
?>
