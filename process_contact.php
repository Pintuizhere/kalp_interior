<?php
require_once 'admin/config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $service = $_POST['service'] ?? 'General Inquiry';
    $message = $_POST['message'] ?? '';
    
    if(!empty($name) && !empty($email) && !empty($message)) {
        // Use prepared statements to insert data
        $stmt = $conn->prepare("INSERT INTO leads (name, email, service, message, status) VALUES (?, ?, ?, ?, 'New')");
        if($stmt) {
            $stmt->bind_param("ssss", $name, $email, $service, $message);
            $stmt->execute();
            $stmt->close();
            
            // Redirect back with success message
            header("Location: contact.php?success=1#contact-section");
            exit;
        } else {
            // Error handling
            header("Location: contact.php?error=1#contact-section");
            exit;
        }
    } else {
        // Validation failed
        header("Location: contact.php?error=validation#contact-section");
        exit;
    }
} else {
    // Direct access not allowed
    header("Location: contact.php");
    exit;
}
?>
