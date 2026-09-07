<?php
session_start();

$upload_dir = '../uploads/media/';

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    
    $file_extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
    
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($file_extension, $allowed_exts)) {
        header("HTTP/1.1 400 Invalid file format");
        exit();
    }
    
    $image_name = 'summernote_' . time() . '_' . rand(100, 999) . '.' . $file_extension;
    $target_file = $upload_dir . $image_name;
    
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
        $base_dir = dirname($_SERVER['SCRIPT_NAME']); // e.g. /kalp_interior/admin
        $base_url = str_replace('/admin', '', $base_dir) . '/';
        echo $base_url . 'uploads/media/' . $image_name;
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
