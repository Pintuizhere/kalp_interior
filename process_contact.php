<?php
require_once 'admin/config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $service = $_POST['service'] ?? 'General Inquiry';
    $message = $_POST['message'] ?? '';
    
    if(!empty($name) && !empty($email) && !empty($message)) {
        // Use standard fields that exist on both local and live databases
        $stmt = $conn->prepare("INSERT INTO leads (`name`, `email`, `service`, `message`, `status`) VALUES (?, ?, ?, ?, 'New')");
        
        $referer = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'contact.php';
        
        // Remove existing query parameters safely
        $base_url = strtok($referer, '?');

        if($stmt) {
            $stmt->bind_param("ssss", $name, $email, $service, $message);
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: " . $base_url . "?success=1#contact-section");
                exit;
            } else {
                $err = urlencode($stmt->error);
                $stmt->close();
                header("Location: " . $base_url . "?error=1&db_err=" . $err . "#contact-section");
                exit;
            }
        } else {
            // Error handling
            $err = urlencode($conn->error);
            header("Location: " . $base_url . "?error=1&db_err=" . $err . "#contact-section");
            exit;
        }
    } else {
        // Validation failed
        $referer = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'contact.php';
        $base_url = strtok($referer, '?');
        header("Location: " . $base_url . "?error=validation#contact-section");
        exit;
    }
} else {
    // Direct access not allowed
    header("Location: contact.php");
    exit;
}
?>
