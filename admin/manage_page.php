<?php
require_once 'config/db.php';

$page_target = isset($_GET['page']) ? $_GET['page'] : 'home';
$pageTitle = 'Manage ' . ucfirst($page_target) . ' Page';
$currentPage = 'page_' . $page_target;

include 'includes/header.php';
include 'includes/sidebar.php';

$success_msg = '';
$error_msg = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_content'])) {
    $success = true;
    
    $check_stmt = $conn->prepare("SELECT 1 FROM page_content WHERE page_name = ? AND section_key = ?");
    $update_stmt = $conn->prepare("UPDATE page_content SET content_value = ? WHERE page_name = ? AND section_key = ?");
    $insert_stmt = $conn->prepare("INSERT INTO page_content (page_name, section_key, content_value) VALUES (?, ?, ?)");

    // Process text content
    if (isset($_POST['content']) && is_array($_POST['content'])) {
        foreach ($_POST['content'] as $section_key => $content_value) {
            $check_stmt->bind_param("ss", $page_target, $section_key);
            $check_stmt->execute();
            $res = $check_stmt->get_result();
            if ($res->num_rows > 0) {
                $update_stmt->bind_param("sss", $content_value, $page_target, $section_key);
                if (!$update_stmt->execute()) $success = false;
            } else {
                $insert_stmt->bind_param("sss", $page_target, $section_key, $content_value);
                if (!$insert_stmt->execute()) $success = false;
            }
        }
    }
    
    // Process file uploads
        // Process reset default
    if (isset($_POST['reset_default'])) {
        $reset_stmt = $conn->prepare("DELETE FROM page_content WHERE page_name = ?");
        $reset_stmt->bind_param("s", $page_target);
        if ($reset_stmt->execute()) {
            $success_msg = ucfirst($page_target) . " page reset to defaults successfully!";
        } else {
            $error_msg = "Error resetting content.";
        }
        $reset_stmt->close();
        
        // Refresh content data after reset
        $content_data = [];
        $stmt = $conn->prepare("SELECT section_key, content_value FROM page_content WHERE page_name = ?");
        $stmt->bind_param("s", $page_target);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $content_data[$row['section_key']] = $row['content_value'];
        }
        $stmt->close();
    } else if (isset($_FILES['media']) && is_array($_FILES['media']['name'])) {
        $upload_dir = '../uploads/media/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        foreach ($_FILES['media']['name'] as $section_key => $filename) {
            if ($_FILES['media']['error'][$section_key] == 0) {
                $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'ogg'];
                
                if (in_array($file_extension, $allowed_exts)) {
                    $new_filename = 'about_' . $section_key . '_' . time() . '.' . $file_extension;
                    $target_file = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['media']['tmp_name'][$section_key], $target_file)) {
                        $db_path = '../uploads/media/' . $new_filename;
                        
                        $check_stmt->bind_param("ss", $page_target, $section_key);
                        $check_stmt->execute();
                        $res = $check_stmt->get_result();
                        if ($res->num_rows > 0) {
                            $update_stmt->bind_param("sss", $db_path, $page_target, $section_key);
                            if (!$update_stmt->execute()) $success = false;
                        } else {
                            $insert_stmt->bind_param("sss", $page_target, $section_key, $db_path);
                            if (!$insert_stmt->execute()) $success = false;
                        }
                    } else {
                        $success = false;
                    }
                }
            }
        }
    }
    
    $check_stmt->close();
    $update_stmt->close();
    $insert_stmt->close();

    if ($success) {
        $success_msg = ucfirst($page_target) . " page content updated successfully!";
    } else {
        $error_msg = "Error updating some content. Please try again.";
    }
}

// Fetch current content
$content_data = [];
$stmt = $conn->prepare("SELECT section_key, content_value FROM page_content WHERE page_name = ?");
$stmt->bind_param("s", $page_target);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $content_data[$row['section_key']] = $row['content_value'];
}
$stmt->close();

// Helper to safely get value
function val($key, $data) {
    return isset($data[$key]) ? htmlspecialchars($data[$key]) : '';
}
?>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        
        <?php if(!empty($success_msg)): ?>
            <div style="background:#d4edda; color:#155724; padding:15px; border-radius:5px; margin-bottom:20px;">
                <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        <?php if(!empty($error_msg)): ?>
            <div style="background:#f8d7da; color:#721c24; padding:15px; border-radius:5px; margin-bottom:20px;">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="page-header" style="margin-bottom: 30px;">
            <h1 style="margin:0;">Manage <?php echo ucfirst($page_target); ?> Page</h1>
            <p style="color:var(--text-muted); margin-top:5px;">Edit the text content that appears on the frontend <?php echo $page_target; ?> page.</p>
        </div>

        <div class="table-wrapper" style="padding: 30px;">
            <form action="manage_page.php?page=<?php echo $page_target; ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="save_content" value="1">
                
                <?php if ($page_target == 'home'): ?>
                    
                    <h3 style="margin-bottom: 20px; color: var(--primary-color); border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Hero Section</h3>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; font-weight:500;">Hero Background Image URL</label>
                        <input type="text" name="content[hero_bg_image]" value="<?php echo val('hero_bg_image', $content_data); ?>" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;" placeholder="https://images.unsplash.com/...">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; font-weight:500;">Hero Title <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="content[hero_title]" value="<?php echo val('hero_title', $content_data); ?>" required style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; font-weight:500;">Hero Description <span style="color:#ef4444;">*</span></label>
                        <textarea name="content[hero_desc]" rows="3" required style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px; font-family:inherit;"><?php echo val('hero_desc', $content_data); ?></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; font-weight:500;">Rating Text</label>
                        <input type="text" name="content[hero_rating_text]" value="<?php echo val('hero_rating_text', $content_data); ?>" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;" placeholder="4.9/5 Rating - 15,000 Reviews">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Primary Button Text</label>
                            <input type="text" name="content[hero_btn1_text]" value="<?php echo val('hero_btn1_text', $content_data); ?>" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;" placeholder="Contact us">
                        </div>
                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Primary Button Link</label>
                            <input type="text" name="content[hero_btn1_link]" value="<?php echo val('hero_btn1_link', $content_data); ?>" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;" placeholder="contact.php">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Secondary Button Text</label>
                            <input type="text" name="content[hero_btn2_text]" value="<?php echo val('hero_btn2_text', $content_data); ?>" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;" placeholder="View Projects">
                        </div>
                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Secondary Button Link</label>
                            <input type="text" name="content[hero_btn2_link]" value="<?php echo val('hero_btn2_link', $content_data); ?>" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;" placeholder="projects.php">
                        </div>
                    </div>

                    <h3 style="margin-bottom: 20px; color: var(--primary-color); border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Statistics Section</h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Stat 1 Value</label>
                            <input type="text" name="content[stat_projects]" value="<?php echo val('stat_projects', $content_data); ?>" required style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;">
                        </div>
                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Stat 1 Label</label>
                            <input type="text" name="content[stat_projects_label]" value="<?php echo val('stat_projects_label', $content_data); ?>" required style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Stat 2 Value</label>
                            <input type="text" name="content[stat_experience]" value="<?php echo val('stat_experience', $content_data); ?>" required style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;">
                        </div>
                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Stat 2 Label</label>
                            <input type="text" name="content[stat_experience_label]" value="<?php echo val('stat_experience_label', $content_data); ?>" required style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;">
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Stat 3 Value</label>
                            <input type="text" name="content[stat_clients]" value="<?php echo val('stat_clients', $content_data); ?>" required style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;">
                        </div>
                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Stat 3 Label</label>
                            <input type="text" name="content[stat_clients_label]" value="<?php echo val('stat_clients_label', $content_data); ?>" required style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Stat 4 Value</label>
                            <input type="text" name="content[stat_satisfaction]" value="<?php echo val('stat_satisfaction', $content_data); ?>" required style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;">
                        </div>
                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Stat 4 Label</label>
                            <input type="text" name="content[stat_satisfaction_label]" value="<?php echo val('stat_satisfaction_label', $content_data); ?>" required style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;">
                        </div>
                    </div>

                <?php elseif ($page_target == 'about'): ?>
<style>
.bento-editor-wrapper {
    background-color: #2a3d36;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
    font-family: 'Inter', sans-serif;
}
.bento-grid {
    display: grid;
    grid-template-columns: 1.6fr 1fr 1fr;
    grid-template-rows: 200px 200px 180px;
    gap: 20px;
}
.bento-item {
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    background-color: #375245; /* default dark green inner */
    display: flex;
    flex-direction: column;
    justify-content: center;
    transition: all 0.3s ease;
}
.bento-item:hover {
    box-shadow: 0 0 0 2px var(--primary-color);
}
.upload-overlay {
    position: absolute;
    top: 10px; right: 10px;
    background: rgba(0,0,0,0.6);
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 12px;
    cursor: pointer;
    z-index: 10;
    opacity: 0;
    transition: opacity 0.3s;
}
.bento-item:hover .upload-overlay {
    opacity: 1;
}
.editable-text {
    outline: none;
    border: 1px dashed transparent;
    transition: border 0.3s;
}
.editable-text:hover, .editable-text:focus {
    border-color: rgba(255,255,255,0.5);
    background: rgba(255,255,255,0.1);
}

/* Specific Items */
.item-1 { grid-column: 1; grid-row: 1 / 3; background-size: cover; background-position: center; justify-content: flex-end; padding: 25px; color: white; position:relative; }
.item-1::before { content: ''; position: absolute; inset:0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); pointer-events:none; }
.item-1 > * { position: relative; z-index: 1; }
.item-1-quote { font-size: 1.2rem; font-weight: 500; margin-bottom: 20px; line-height: 1.4; }
.item-1-author { display: flex; align-items: center; gap: 15px; }
.item-1-avatar { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; cursor: pointer; border: 2px solid white; }
.item-1-name { font-weight: bold; font-size: 1.1rem; margin:0;}
.item-1-role { font-size: 0.8rem; opacity: 0.8; margin:0;}

.item-2 { grid-column: 2; grid-row: 1; display: flex; align-items: center; justify-content: center; background-size: cover; background-position: center; }
.item-3 { grid-column: 2; grid-row: 2; background-color: #ffffff; color: #333; text-align: center; padding: 30px; }
.item-3-val { font-size: 3rem; font-weight: 900; margin: 0; color: #1a1a1a; }
.item-3-label { font-size: 1rem; color: #666; margin: 5px 0 0 0; }

.item-4 { grid-column: 3; grid-row: 1 / 3; background-size: cover; background-position: center; color: white; text-align: center; justify-content: center; position:relative;}
.item-4::before { content: ''; position: absolute; inset:0; background: rgba(0,0,0,0.3); pointer-events:none; }
.item-4 > * { position: relative; z-index: 1; }
.play-btn { width: 60px; height: 60px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: black; font-size: 20px; cursor: pointer; }
.item-4-text { font-size: 1.1rem; font-weight: 500; }

.item-5 { grid-column: 1; grid-row: 3; background-color: #f1b947; color: #333; padding: 25px; display: flex; flex-direction: column; justify-content: space-between;}
.item-5-val { font-size: 3rem; font-weight: 900; margin: 0; color: #1a1a1a; }
.item-5-label { font-weight: bold; font-size: 1.1rem; margin-bottom: 5px; }
.item-5-desc { font-size: 0.85rem; opacity: 0.8; }

.item-6 { grid-column: 2; grid-row: 3; text-align: center; padding: 20px; justify-content: center;}
.avatars-container { display: flex; justify-content: center; margin-bottom: 10px; }
.avatar-stack { width: 40px; height: 40px; border-radius: 50%; border: 2px solid #375245; margin-left: -15px; object-fit: cover; cursor: pointer; }
.avatar-stack:first-child { margin-left: 0; }
.item-6-text { color: white; font-size: 0.9rem; }

.item-7 { grid-column: 3; grid-row: 3; color: white; padding: 25px; display: flex; flex-direction: column; justify-content: space-between; }
.item-7-val { font-size: 3rem; font-weight: 900; margin: 0; }
.item-7-label { font-weight: bold; font-size: 1.1rem; margin-bottom: 5px; }
.item-7-desc { font-size: 0.85rem; opacity: 0.8; }

@media (max-width: 768px) {
    .bento-grid {
        grid-template-columns: 1fr;
        grid-template-rows: auto;
    }
    .item-1, .item-2, .item-3, .item-4, .item-5, .item-6, .item-7 {
        grid-column: 1 !important;
        grid-row: auto !important;
        min-height: 200px;
    }
}
</style>

<div class="bento-editor-wrapper">
    <div class="bento-grid">
        
        <!-- Block 1 -->
        <div class="bento-item item-1" id="preview_b1_image" style="background-image: url('<?php echo val('about_b1_image', $content_data) ?: 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'; ?>');">
            <label class="upload-overlay">
                <i class="fa fa-image"></i> Change BG
                <input type="file" name="media[about_b1_image]" style="display:none;" onchange="previewMedia(this, 'preview_b1_image', 'bg')">
            </label>
            <div class="editable-text item-1-quote" contenteditable="true" data-target="input_b1_quote"><?php echo val('about_b1_quote', $content_data) ?: '"In design, we find the delicate balance between function and beauty..."'; ?></div>
            <div class="item-1-author">
                <label>
                    <img src="<?php echo val('about_b1_avatar', $content_data) ?: '../assets/images/founder.jpeg'; ?>" class="item-1-avatar" id="preview_b1_avatar">
                    <input type="file" name="media[about_b1_avatar]" style="display:none;" onchange="previewMedia(this, 'preview_b1_avatar', 'img')">
                </label>
                <div>
                    <div class="editable-text item-1-name" contenteditable="true" data-target="input_b1_name"><?php echo val('about_b1_name', $content_data) ?: 'Reedam Kumar'; ?></div>
                    <div class="editable-text item-1-role" contenteditable="true" data-target="input_b1_role"><?php echo val('about_b1_role', $content_data) ?: 'Kalp Interior Design Studio, Founder'; ?></div>
                </div>
            </div>
            <input type="hidden" name="content[about_b1_quote]" id="input_b1_quote" value="<?php echo val('about_b1_quote', $content_data); ?>">
            <input type="hidden" name="content[about_b1_name]" id="input_b1_name" value="<?php echo val('about_b1_name', $content_data); ?>">
            <input type="hidden" name="content[about_b1_role]" id="input_b1_role" value="<?php echo val('about_b1_role', $content_data); ?>">
        </div>

        <!-- Block 2 -->
        <div class="bento-item item-2" id="preview_b2_image" style="background-image: url('<?php echo val('about_b2_image', $content_data) ?: 'none'; ?>'); background-size: contain; background-repeat: no-repeat;">
            <label class="upload-overlay">
                <i class="fa fa-image"></i> Change Logo
                <input type="file" name="media[about_b2_image]" style="display:none;" onchange="previewMedia(this, 'preview_b2_image', 'bg')">
            </label>
        </div>

        <!-- Block 3 -->
        <div class="bento-item item-3">
            <div class="editable-text item-3-val" contenteditable="true" data-target="input_b3_value"><?php echo val('about_b3_value', $content_data) ?: '150+'; ?></div>
            <div class="editable-text item-3-label" contenteditable="true" data-target="input_b3_label"><?php echo val('about_b3_label', $content_data) ?: 'Happy Clients'; ?></div>
            <input type="hidden" name="content[about_b3_value]" id="input_b3_value" value="<?php echo val('about_b3_value', $content_data); ?>">
            <input type="hidden" name="content[about_b3_label]" id="input_b3_label" value="<?php echo val('about_b3_label', $content_data); ?>">
        </div>

        <!-- Block 4 -->
        <div class="bento-item item-4" id="preview_b4_image" style="background-image: url('<?php echo val('about_b4_image', $content_data) ?: 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'; ?>');">
            <label class="upload-overlay">
                <i class="fa fa-image"></i> BG / Video
                <input type="file" name="media[about_b4_image]" style="display:none;" onchange="previewMedia(this, 'preview_b4_image', 'bg')">
            </label>
            <div class="play-btn">
                <i class="fa-solid fa-play"></i>
            </div>
            <div class="editable-text item-4-text" contenteditable="true" data-target="input_b4_text"><?php echo val('about_b4_text', $content_data) ?: 'Learn more<br>About Kalp Design Studio'; ?></div>
            <input type="hidden" name="content[about_b4_text]" id="input_b4_text" value="<?php echo val('about_b4_text', $content_data); ?>">
            <div style="position:absolute; bottom:10px; right:10px;">
                <label style="color:#aaa; font-size:10px; cursor:pointer;">Upload Video 
                   <input type="file" name="media[about_b4_video]" style="display:none;" onchange="alert('Video selected. Save to apply.')">
                </label>
            </div>
        </div>

        <!-- Block 5 -->
        <div class="bento-item item-5">
            <div class="editable-text item-5-val" contenteditable="true" data-target="input_b5_value"><?php echo val('about_b5_value', $content_data) ?: '200+'; ?></div>
            <div>
                <div class="editable-text item-5-label" contenteditable="true" data-target="input_b5_label"><?php echo val('about_b5_label', $content_data) ?: 'Projects'; ?></div>
                <div class="editable-text item-5-desc" contenteditable="true" data-target="input_b5_desc"><?php echo val('about_b5_desc', $content_data) ?: 'Over 200 successful projects completed'; ?></div>
            </div>
            <input type="hidden" name="content[about_b5_value]" id="input_b5_value" value="<?php echo val('about_b5_value', $content_data); ?>">
            <input type="hidden" name="content[about_b5_label]" id="input_b5_label" value="<?php echo val('about_b5_label', $content_data); ?>">
            <input type="hidden" name="content[about_b5_desc]" id="input_b5_desc" value="<?php echo val('about_b5_desc', $content_data); ?>">
        </div>

        <!-- Block 6 -->
        <div class="bento-item item-6">
            <div class="avatars-container">
                <?php 
                $default_avatars = [
                    'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
                    'https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
                    'https://images.unsplash.com/photo-1534528741775-53994a69daeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
                    'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
                    'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80'
                ];
                for($i=1; $i<=5; $i++): 
                ?>
                <label>
                    <img src="<?php echo val('about_b6_avatar_'.$i, $content_data) ?: $default_avatars[$i-1]; ?>" class="avatar-stack" id="preview_b6_a<?php echo $i; ?>" style="position:relative; z-index:<?php echo (6-$i); ?>;">
                    <input type="file" name="media[about_b6_avatar_<?php echo $i; ?>]" style="display:none;" onchange="previewMedia(this, 'preview_b6_a<?php echo $i; ?>', 'img')">
                </label>
                <?php endfor; ?>
            </div>
            <div class="editable-text item-6-text" contenteditable="true" data-target="input_b6_text"><?php echo val('about_b6_text', $content_data) ?: '18 Creative Masterminds'; ?></div>
            <input type="hidden" name="content[about_b6_text]" id="input_b6_text" value="<?php echo val('about_b6_text', $content_data); ?>">
        </div>

        <!-- Block 7 -->
        <div class="bento-item item-7">
            <div class="editable-text item-7-val" contenteditable="true" data-target="input_b7_value"><?php echo val('about_b7_value', $content_data) ?: '8+'; ?></div>
            <div>
                <div class="editable-text item-7-label" contenteditable="true" data-target="input_b7_label"><?php echo val('about_b7_label', $content_data) ?: 'Prestigious Awards'; ?></div>
                <div class="editable-text item-7-desc" contenteditable="true" data-target="input_b7_desc"><?php echo val('about_b7_desc', $content_data) ?: 'Over 8 Awards won showcasing extensive experience and portfolio.'; ?></div>
            </div>
            <input type="hidden" name="content[about_b7_value]" id="input_b7_value" value="<?php echo val('about_b7_value', $content_data); ?>">
            <input type="hidden" name="content[about_b7_label]" id="input_b7_label" value="<?php echo val('about_b7_label', $content_data); ?>">
            <input type="hidden" name="content[about_b7_desc]" id="input_b7_desc" value="<?php echo val('about_b7_desc', $content_data); ?>">
        </div>

    </div>
</div>

<script>
// Sync contenteditable to hidden inputs
document.querySelectorAll('.editable-text').forEach(el => {
    el.addEventListener('input', function() {
        const targetId = this.getAttribute('data-target');
        if(targetId) {
            document.getElementById(targetId).value = this.innerHTML;
        }
    });
});

// Preview image before upload
function previewMedia(input, targetId, type) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var target = document.getElementById(targetId);
            if (type === 'bg') {
                target.style.backgroundImage = 'url(' + e.target.result + ')';
            } else if (type === 'img') {
                target.src = e.target.result;
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

                <?php else: ?>
                    <p>Page configuration not found.</p>
                <?php endif; ?>

                <?php if(in_array($page_target, ['home', 'about', 'before_after'])): ?>
                <div style="display: flex; gap: 15px; margin-top: 20px;">
                    <button type="submit" class="btn-primary" style="padding: 10px 25px;">
                        <i class="fa-solid fa-floppy-disk"></i> Save Changes
                    </button>
                    <button type="submit" name="reset_default" value="1" class="btn-secondary" style="padding: 10px 25px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;" onclick="return confirm('Are you sure you want to reset all content for this page to default?');">
                        <i class="fa-solid fa-rotate-left"></i> Reset to Default
                    </button>
                </div>
                <?php endif; ?>
            </form>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
