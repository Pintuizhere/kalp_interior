<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp'] ?? '';
    
    if (empty($otp)) {
        echo json_encode(['success' => false, 'message' => 'OTP is required']);
        exit;
    }
    
    if (isset($_SESSION['calc_otp']) && $_SESSION['calc_otp'] == $otp) {
        // Check expiration (10 minutes = 600 seconds)
        if (isset($_SESSION['otp_time']) && (time() - $_SESSION['otp_time']) > 600) {
            unset($_SESSION['calc_otp']);
            echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new one.']);
            exit;
        }

        unset($_SESSION['calc_otp']); // Clear OTP after successful verification
        echo json_encode(['success' => true, 'message' => 'OTP verified']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
