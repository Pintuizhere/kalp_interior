<?php
// upload_handler.php - Handles image uploads from TinyMCE editor
session_start();

// Simple security check to ensure admin is logged in (uncomment if session is used)
// if (!isset($_SESSION['admin_id'])) {
//     header("HTTP/1.1 403 Forbidden");
//     exit();
// }

$upload_dir = '../uploads/media/';

// Ensure directory exists
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Check if a file was uploaded
if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    
    $file_extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
    
    // Validate file type
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($file_extension, $allowed_exts)) {
        header("HTTP/1.1 400 Invalid file format");
        exit();
    }
    
    // Generate unique name
    $image_name = 'inline_' . time() . '_' . rand(100, 999) . '.' . $file_extension;
    $target_file = $upload_dir . $image_name;
    
    // Move the uploaded file
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
        // Return JSON response expected by TinyMCE
        echo json_encode(['location' => '../uploads/media/' . $image_name]);
        exit();
    } else {
        header("HTTP/1.1 500 Server Error");
        exit();
    }
} else {
    header("HTTP/1.1 400 No file uploaded");
    exit();
}
?>
