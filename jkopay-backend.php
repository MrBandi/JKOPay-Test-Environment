<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 商店基本參數
define('STORE_ID', 'ENTER YOUR STORE_ID');
define('API_KEY', 'ENTER YOUR API_KEY');
define('SECRET_KEY', 'ENTER YOUR SECRET_KEY');
define('RESULT_URL', 'https://{YOUR DOMAIN}/jkopay-backend.php?action=result');
define('RESULT_DISPLAY_URL', 'https://{YOUR DOMAIN}/');

// 街口支付 API_URL
define('ENTRY_API_URL', 'https://uat-onlinepay.jkopay.app/platform/entry'); //創建訂單
define('REFUND_API_URL', 'https://uat-onlinepay.jkopay.app/platform/refund'); // 查詢訂單
define('INQUIRY_API_URL', 'https://uat-onlinepay.jkopay.app/platform/inquiry'); // 訂單退款

// Error Logs
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'jkopay_error.log');
}

// 加簽加密創建
function generateSignature($payload, $method = 'POST') {
    if ($method === 'GET') {
        $content = is_array($payload) ? http_build_query($payload) : $payload;
    } else {
        $content = json_encode($payload);
    }
    return hash_hmac('sha256', $content, SECRET_KEY);
}

// 發送API請求
function sendRequest($url, $payload, $method = 'POST') {
    $ch = curl_init();

    $signature = generateSignature($payload, $method);

    $headers = [
        'Content-Type: application/json',
        'API-KEY: ' . API_KEY,
        'DIGEST: ' . $signature
    ];
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    // SSL 選項
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    } else {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        $url .= '?' . http_build_query($payload);
        curl_setopt($ch, CURLOPT_URL, $url);
    }

    $response = curl_exec($ch);
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        logError('Curl error: ' . curl_error($ch));
        return ['result' => '999', 'message' => 'Curl error: ' . curl_error($ch)];
    }
    
    curl_close($ch);
    
    if ($httpCode != 200) {
        logError("HTTP Error: $httpCode, Response: $response");
        return ['result' => "$httpCode", 'message' => "HTTP Error: $httpCode"];
    }
    
    $decodedResponse = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        logError("JSON decode error: " . json_last_error_msg());
        return ['result' => '998', 'message' => 'Invalid JSON response'];
    }
    
    return $decodedResponse;
}

// 創建訂單
function createOrder($amount) {
    $orderId = 'TEST-ORDER-' . time();

    $payload = [
        'platform_order_id' => $orderId,
        'store_id' => STORE_ID,
        'currency' => 'TWD',
        'total_price' => $amount,
        'final_price' => $amount,
        'unredeem' => 0,
        'result_url' => RESULT_URL,
        'result_display_url' => RESULT_DISPLAY_URL,
        'payment_type' => 'onetime',
        'escrow' => false
    ];

    $response = sendRequest(ENTRY_API_URL, $payload);
    $response['platform_order_id'] = $orderId;
    error_log("Final Response: " . json_encode($response));

    return $response;
}

// 查詢訂單
function inquiryOrder($orderIds) {
    if (!is_array($orderIds)) {
        $orderIds = [$orderIds];
    }
    
    // 限制查詢單數最多只能到20筆訂單
    $orderIds = array_slice($orderIds, 0, 20);
    $payload = ['platform_order_ids' => implode(',', $orderIds)];  
    
    return sendRequest(INQUIRY_API_URL, $payload, 'GET');
}

// 訂單退款
function refundOrder($orderId, $amount, $refundOrderId = null) {
    $payload = [
        'platform_order_id' => $orderId,
        'refund_amount' => $amount,
        'store_id' => STORE_ID,
        'currency' => 'TWD'
    ];

    if ($refundOrderId) {
        $payload['refund_order_id'] = $refundOrderId;
    }

    return sendRequest(REFUND_API_URL, $payload);
}

// result_url 處理
function handleResultCallback() {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        logError("Invalid JSON received in result callback");
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
        return;
    }

    // 驗證必要欄位
    $requiredFields = ['platform_order_id', 'status', 'tradeNo', 'trans_time', 'currency', 'final_price', 'redeem_amount', 'debit_amount'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            logError("Missing required field: $field");
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
            return;
        }
    }

    // 處理支付結果
    // 這裡可以根據 $data 中的信息更新數據庫
    logError("Payment result received: " . json_encode($data));

    // 返回成功響應
    http_response_code(200);
    echo json_encode(['status' => 'success']);
}

// 處理前端請求
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['result' => '997', 'message' => 'Invalid action'];

try {
    switch ($action) {
        case 'createOrder':
            $amount = $_POST['amount'] ?? 100;
            $response = createOrder($amount);
            break;
        case 'inquiryOrder':
            $orderIds = isset($_POST['orderIds']) ? explode(',', $_POST['orderIds']) : [];
            $response = inquiryOrder($orderIds);
            break;
        case 'refundOrder':
            $orderId = $_POST['orderId'] ?? '';
            $amount = $_POST['amount'] ?? 0;
            $refundOrderId = $_POST['refundOrderId'] ?? null;
            $response = refundOrder($orderId, $amount, $refundOrderId);
            break;
        case 'result':
            handleResultCallback();
            exit; 
    }
} catch (Exception $e) {
    logError('Exception: ' . $e->getMessage());
    $response = ['result' => '996', 'message' => 'An error occurred: ' . $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
