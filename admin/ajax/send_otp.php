<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'] ?? '';
    // 1. Strict Phone Number Validation (exactly 10 digits)
    if (empty($phone) || !preg_match('/^[0-9]{10}$/', $phone)) {
        echo json_encode(['success' => false, 'message' => 'A valid 10-digit phone number is required']);
        exit;
    }

    // --- CHECK FOR FREE NUMBER ---
    require_once '../config/db.php';
    $stmt = $conn->prepare("SELECT id FROM free_numbers WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->close();
        // It is a free number! Bypass OTP.
        echo json_encode(['success' => true, 'is_free_login' => true, 'message' => 'Free login successful']);
        exit;
    }
    $stmt->close();
    // -----------------------------

    // --- FETCH RATE LIMIT SETTINGS ---
    $cooldown = 60;
    $daily_limit = 5;
    $settings_res = $conn->query("SELECT setting_key, setting_value FROM calculator_settings WHERE setting_key IN ('otp_cooldown', 'otp_daily_limit')");
    if ($settings_res) {
        while ($row = $settings_res->fetch_assoc()) {
            if ($row['setting_key'] == 'otp_cooldown') $cooldown = (int)$row['setting_value'];
            if ($row['setting_key'] == 'otp_daily_limit') $daily_limit = (int)$row['setting_value'];
        }
    }

    // 2. Rate Limiting (Cooldown period between requests)
    if (isset($_SESSION['last_otp_time']) && (time() - $_SESSION['last_otp_time']) < $cooldown) {
        echo json_encode(['success' => false, 'message' => 'Please wait ' . $cooldown . ' seconds before requesting another OTP']);
        exit;
    }
    
    // 3. Rate Limiting (Max OTPs per session)
    if (!isset($_SESSION['otp_count'])) {
        $_SESSION['otp_count'] = 0;
        $_SESSION['otp_reset_time'] = time();
    }
    if ((time() - $_SESSION['otp_reset_time']) > 86400) {
        $_SESSION['otp_count'] = 0; // Reset after 24 hours
        $_SESSION['otp_reset_time'] = time();
    }
    if ($_SESSION['otp_count'] >= $daily_limit) {
        echo json_encode(['success' => false, 'message' => 'Maximum OTP requests reached for today']);
        exit;
    }

    $otp = rand(100000, 999999);
    $_SESSION['calc_otp'] = $otp;
    $_SESSION['calc_phone'] = $phone;
    $_SESSION['otp_time'] = time(); // 4. Store generation time for expiration
    $_SESSION['last_otp_time'] = time();
    $_SESSION['otp_count']++;


    $apiKey = "5PgmWeXxOBfKJz81wubCkhdv70SERoA6rLYQF4IlUtNjcDqMyZyLrXZ1PSiQHDI3z4C9hwpnEVbWujlA";
    
    // IMPORTANT: WhatsApp API requires an approved OTP template!
    // WhatsApp API configuration is handled in the postData array below

    $curl = curl_init();
    
    $postData = [
        'message_id' => '27929',
        'phone_number_id' => '133118786546114', // From your WABA details
        'numbers' => $phone,
        'variables_values' => $otp
    ];

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://www.fast2sms.com/dev/whatsapp",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_SSL_VERIFYHOST => 0,
      CURLOPT_SSL_VERIFYPEER => 0,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $postData,
      CURLOPT_HTTPHEADER => array(
        "authorization: " . $apiKey
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    
    if ($err) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $err]);
    } else {
        $res = json_decode($response, true);
        if (isset($res['return']) && $res['return'] == false) {
            echo json_encode(['success' => false, 'message' => $res['message'] ?? 'Failed to send OTP']);
        } else {
            echo json_encode(['success' => true, 'message' => 'OTP sent successfully']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
