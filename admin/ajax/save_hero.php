<?php
require_once '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    if ($_POST['action'] === 'reset_hero') {
        $keys_to_reset = [
            'hero_bg_image', 'hero_rating_text', 'hero_title', 'hero_desc',
            'hero_btn1_text', 'hero_btn2_text', 'stat_projects', 'stat_projects_label',
            'stat_experience', 'stat_experience_label', 'stat_clients', 'stat_clients_label',
            'stat_satisfaction', 'stat_satisfaction_label',
            'hero_avatar_1', 'hero_avatar_2', 'hero_avatar_3', 'hero_avatar_4'
        ];
        
        $stmt = $conn->prepare("UPDATE page_content SET content_value = '' WHERE page_name = 'home' AND section_key = ?");
        $success = true;
        foreach ($keys_to_reset as $key) {
            $stmt->bind_param("s", $key);
            if (!$stmt->execute()) {
                $success = false;
            }
        }
        $stmt->close();
        echo json_encode(['success' => $success]);
        exit;
    }

    if ($_POST['action'] === 'save_hero') {
        
    function processBase64Image($base64_val) {
        if (strpos($base64_val, 'data:image/') === 0) {
            $img_parts = explode(";base64,", $base64_val);
            $img_type_aux = explode("image/", $img_parts[0]);
            $img_type = $img_type_aux[1];
            $img_base64 = base64_decode($img_parts[1]);
            $filename = uniqid() . '.png';
            $file_dir = '../../uploads/';
            $file_path = $file_dir . $filename;
            
            if (!is_dir($file_dir)) {
                mkdir($file_dir, 0755, true);
            }
            file_put_contents($file_path, $img_base64);
            return 'uploads/' . $filename;
        }
        return $base64_val;
    }
    
    $bg_image_val = processBase64Image($_POST['hero_bg_image'] ?? '');
    $avatar_1_val = processBase64Image($_POST['hero_avatar_1'] ?? '');
    $avatar_2_val = processBase64Image($_POST['hero_avatar_2'] ?? '');
    $avatar_3_val = processBase64Image($_POST['hero_avatar_3'] ?? '');
    $avatar_4_val = processBase64Image($_POST['hero_avatar_4'] ?? '');

    $content = [
        'hero_bg_image' => $bg_image_val,
        'hero_avatar_1' => $avatar_1_val,
        'hero_avatar_2' => $avatar_2_val,
        'hero_avatar_3' => $avatar_3_val,
        'hero_avatar_4' => $avatar_4_val,
        'hero_rating_text' => $_POST['hero_rating_text'] ?? '',
        'hero_title' => $_POST['hero_title'] ?? '',
        'hero_desc' => $_POST['hero_desc'] ?? '',
        'hero_btn1_text' => $_POST['hero_btn1_text'] ?? '',
        'hero_btn2_text' => $_POST['hero_btn2_text'] ?? '',
        'stat_projects' => $_POST['stat_projects'] ?? '',
        'stat_projects_label' => $_POST['stat_projects_label'] ?? '',
        'stat_experience' => $_POST['stat_experience'] ?? '',
        'stat_experience_label' => $_POST['stat_experience_label'] ?? '',
        'stat_clients' => $_POST['stat_clients'] ?? '',
        'stat_clients_label' => $_POST['stat_clients_label'] ?? '',
        'stat_satisfaction' => $_POST['stat_satisfaction'] ?? '',
        'stat_satisfaction_label' => $_POST['stat_satisfaction_label'] ?? ''
    ];

    $success = true;
    foreach ($content as $key => $val) {
        if ($val === '') continue; // Skip empty updates
        
        // Check if row exists
        $check_stmt = $conn->prepare("SELECT id FROM page_content WHERE page_name = 'home' AND section_key = ?");
        $check_stmt->bind_param("s", $key);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Update
            $upd_stmt = $conn->prepare("UPDATE page_content SET content_value = ? WHERE page_name = 'home' AND section_key = ?");
            $upd_stmt->bind_param("ss", $val, $key);
            if (!$upd_stmt->execute()) {
                $success = false;
            }
            $upd_stmt->close();
        } else {
            // Insert
            $ins_stmt = $conn->prepare("INSERT INTO page_content (page_name, section_key, content_value) VALUES ('home', ?, ?)");
            $ins_stmt->bind_param("ss", $key, $val);
            if (!$ins_stmt->execute()) {
                $success = false;
            }
            $ins_stmt->close();
        }
        $check_stmt->close();
    }
    
    echo json_encode(['success' => $success]);
    exit;
}
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
