<?php
session_start();
require '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? '';
$page_name = $_POST['page_name'] ?? '';

if (empty($page_name)) {
    echo json_encode(['success' => false, 'error' => 'Missing page identifier']);
    exit();
}

if ($action === 'save_service') {
    $service_id = str_replace('service_', '', $page_name);
    
    // Update SEO settings in services table
    $meta_title = $_POST['meta_title'] ?? '';
    $meta_keywords = $_POST['meta_keywords'] ?? '';
    $meta_description = $_POST['meta_description'] ?? '';
    $slug = $_POST['slug'] ?? '';
    
    $update_seo = $conn->prepare("UPDATE services SET meta_title=?, meta_keywords=?, meta_description=?, slug=? WHERE id=?");
    $update_seo->bind_param("ssssi", $meta_title, $meta_keywords, $meta_description, $slug, $service_id);
    $update_seo->execute();
    $update_seo->close();

    // Delete existing keys for this service
    $stmt = $conn->prepare("DELETE FROM page_content WHERE page_name = ?");
    $stmt->bind_param("s", $page_name);
    $stmt->execute();
    $stmt->close();
    
    // Insert new keys
    $insert_stmt = $conn->prepare("INSERT INTO page_content (page_name, section_key, content_value) VALUES (?, ?, ?)");
    
    $allowed_keys = [
        'sd_banner_img', 'sd_hero_title', 'sd_hero_signature', 'sd_hero_desc', 'sd_hero_img',
        'sd_f1_title', 'sd_f1_desc', 'sd_f2_title', 'sd_f2_desc',
        'sd_f3_title', 'sd_f3_desc', 'sd_f4_title', 'sd_f4_desc',
        'sd_why_title', 'sd_why_signature', 'sd_why_desc', 'sd_why_img',
        'sd_why_l1_title', 'sd_why_l1_desc', 'sd_why_l2_title', 'sd_why_l2_desc',
        'sd_why_l3_title', 'sd_why_l3_desc', 'sd_why_l4_title', 'sd_why_l4_desc',
        'sd_process_title', 'sd_process_signature',
        'sd_p1_title', 'sd_p1_desc', 'sd_p2_title', 'sd_p2_desc',
        'sd_p3_title', 'sd_p3_desc', 'sd_p4_title', 'sd_p4_desc', 'sd_p5_title', 'sd_p5_desc'
    ];
    
    foreach ($allowed_keys as $key) {
        if (isset($_POST[$key])) {
            $val = $_POST[$key];
            $insert_stmt->bind_param("sss", $page_name, $key, $val);
            $insert_stmt->execute();
        }
    }
    $insert_stmt->close();
    
    echo json_encode(['success' => true]);
    exit();
}

if ($action === 'reset_service') {
    $stmt = $conn->prepare("DELETE FROM page_content WHERE page_name = ?");
    $stmt->bind_param("s", $page_name);
    if($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
    exit();
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);
