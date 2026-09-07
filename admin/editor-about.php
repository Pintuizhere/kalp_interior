<?php
require_once 'config/db.php';
$currentPage = 'editor_about';

// Handle AJAX Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'save_about_live') {
    header('Content-Type: application/json');
    
    $check_stmt = $conn->prepare("SELECT 1 FROM page_content WHERE page_name = 'about' AND section_key = ?");
    $update_stmt = $conn->prepare("UPDATE page_content SET content_value = ? WHERE page_name = 'about' AND section_key = ?");
    $insert_stmt = $conn->prepare("INSERT INTO page_content (page_name, section_key, content_value) VALUES ('about', ?, ?)");
    
    // Save text fields
    if (isset($_POST['content_data'])) {
        $content = json_decode($_POST['content_data'], true);
        if (is_array($content)) {
            foreach ($content as $key => $val) {
                $check_stmt->bind_param("s", $key);
                $check_stmt->execute();
                if ($check_stmt->get_result()->num_rows > 0) {
                    $update_stmt->bind_param("ss", $val, $key);
                    $update_stmt->execute();
                } else {
                    $insert_stmt->bind_param("ss", $key, $val);
                    $insert_stmt->execute();
                }
            }
        }
    }
    
    // Save images
    if (!empty($_FILES)) {
        $upload_dir = '../uploads/media/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        foreach ($_FILES as $key => $file) {
            if ($file['error'] == 0) {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($ext, $allowed)) {
                    $new_filename = 'about_' . $key . '_' . time() . '.' . $ext;
                    if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
                        $db_path = 'uploads/media/' . $new_filename;
                        
                        $check_stmt->bind_param("s", $key);
                        $check_stmt->execute();
                        if ($check_stmt->get_result()->num_rows > 0) {
                            $update_stmt->bind_param("ss", $db_path, $key);
                            $update_stmt->execute();
                        } else {
                            $insert_stmt->bind_param("ss", $key, $db_path);
                            $insert_stmt->execute();
                        }
                    }
                }
            }
        }
    }
    
    echo json_encode(['status' => 'success']);
    exit;
}

// Fetch current content for rendering the preview
$about_content = [];
$stmt = $conn->prepare("SELECT section_key, content_value FROM page_content WHERE page_name = 'about'");
$stmt->execute();
$res = $stmt->get_result();
while($row = $res->fetch_assoc()) {
    $about_content[$row['section_key']] = $row['content_value'];
}
$stmt->close();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Include Frontend CSS -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

<style>
    /* Reset some admin styles that might conflict with frontend CSS */
    .main-content {
        padding: 0;
        background: #f8f9fa;
    }
    
    .editor-topbar {
        background: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--border-color);
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        flex-wrap: wrap;
        gap: 15px;
    }
    
    /* Protect admin topbar from frontend CSS overrides */
    header.topbar { padding: 0 30px !important; }
    
    @media (max-width: 768px) {
        header.topbar { padding: 0 15px !important; }
        .editor-topbar {
            padding: 15px;
        }
        .editor-container {
            padding: 15px;
        }
    }
    
    .editor-container {
        padding: 30px;
        background: #f8f9fa;
        min-height: calc(100vh - 70px);
    }
    
    .live-preview-wrapper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 30px rgba(0,0,0,0.08);
        overflow: hidden;
        margin: 0 auto;
        position: relative;
    }

    /* Hide Frontend Elements we don't want */
    .navbar, .page-banner, footer, .main-footer, .footer-bottom, .cta-banner { display: none !important; }
    
    /* Live Editor Styles */
    [contenteditable="true"] {
        outline: 1px dashed rgba(234, 177, 54, 0.5);
        transition: all 0.3s ease;
        padding: 2px 4px;
        border-radius: 4px;
        min-width: 20px;
        display: inline-block;
    }
    [contenteditable="true"]:hover {
        outline: 2px dashed var(--accent-color);
        background: rgba(234, 177, 54, 0.05);
        cursor: text;
    }
    [contenteditable="true"]:focus {
        outline: 2px solid var(--accent-color);
        background: rgba(255, 255, 255, 0.1);
    }
    
    .editable-img-wrapper {
        position: relative;
        cursor: pointer;
        transition: all 0.3s;
    }
    .editable-img-wrapper:hover::after {
        content: '📸 Click to change image';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        z-index: 10;
        border-radius: inherit;
    }
    
    /* Toast Notification */
    .toast {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: #10b981;
        color: white;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 500;
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        z-index: 9999;
    }
    .toast.show {
        transform: translateY(0);
        opacity: 1;
    }
</style>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        
        <div class="editor-topbar">
            <div>
                <h2 style="margin: 0; font-size: 20px;">About Page Live Editor</h2>
                <p style="margin: 5px 0 0; color: #666; font-size: 13px;">Click on text to edit. Click on images to replace them.</p>
            </div>
            <div style="display: flex; gap: 15px;">
                <a href="manage_page.php?page=about" class="btn-secondary" style="padding: 10px 20px; text-decoration: none; border-radius: 5px; color: #333; background: #e5e7eb;">Back to Standard Editor</a>
                <button type="button" class="btn-primary" id="saveLiveBtn" style="padding: 10px 25px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
                </button>
            </div>
        </div>

        <div class="editor-container">
            <div class="live-preview-wrapper" id="livePreviewContainer">
                
                <!-- INCLUDE THE ACTUAL COMPONENTS -->
                <?php include '../includes/components/about.php'; ?>
                <?php include '../includes/components/how-it-works.php'; ?>
                
            </div>
        </div>
    </div>
</div>

<!-- Hidden File Inputs -->
<input type="file" id="file_about_intro_image" style="display:none;" accept="image/*">
<input type="file" id="file_hiw_tab1_image" style="display:none;" accept="image/*">
<input type="file" id="file_hiw_tab2_image" style="display:none;" accept="image/*">
<input type="file" id="file_hiw_tab3_image" style="display:none;" accept="image/*">
<input type="file" id="file_hiw_tab4_image" style="display:none;" accept="image/*">

<div class="toast" id="saveToast"><i class="fa-solid fa-check-circle"></i> Changes saved successfully!</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Map classes to database keys
    const textMappings = {
        '.about-intro-award': 'about_intro_award_title',
        '.about-stat1-val': 'about_intro_stat1_value',
        '.about-stat1-label': 'about_intro_stat1_label',
        '.about-stat2-val': 'about_intro_stat2_value',
        '.about-stat2-label': 'about_intro_stat2_label',
        '.about-stat3-val': 'about_intro_stat3_value',
        '.about-stat3-label': 'about_intro_stat3_label',
        '.about-sig-name': 'about_intro_signature_name',
        '.about-sig-role': 'about_intro_signature_role',
        '.hiw-main-heading': 'hiw_main_heading',
        '.hiw-tab-title-1': 'hiw_tab1_title',
        '.hiw-tab-title-2': 'hiw_tab2_title',
        '.hiw-tab-title-3': 'hiw_tab3_title',
        '.hiw-tab-title-4': 'hiw_tab4_title',
        '.hiw-tab-heading-1': 'hiw_tab1_heading',
        '.hiw-tab-desc-1': 'hiw_tab1_desc',
        '.hiw-tab-heading-2': 'hiw_tab2_heading',
        '.hiw-tab-desc-2': 'hiw_tab2_desc',
        '.hiw-tab-heading-3': 'hiw_tab3_heading',
        '.hiw-tab-desc-3': 'hiw_tab3_desc',
        '.hiw-tab-heading-4': 'hiw_tab4_heading',
        '.hiw-tab-desc-4': 'hiw_tab4_desc'
    };

    // Make text elements editable
    Object.keys(textMappings).forEach(selector => {
        const els = document.querySelectorAll(selector);
        els.forEach(el => {
            el.setAttribute('contenteditable', 'true');
            // Prevent link clicks inside contenteditable
            el.addEventListener('click', e => e.preventDefault());
        });
    });

    // Image Mappings
    const imageMappings = {
        '.about-intro-bg': 'about_intro_image',
        '.hiw-tab-img-1': 'hiw_tab1_image',
        '.hiw-tab-img-2': 'hiw_tab2_image',
        '.hiw-tab-img-3': 'hiw_tab3_image',
        '.hiw-tab-img-4': 'hiw_tab4_image'
    };

    let pendingFiles = new FormData();

    Object.keys(imageMappings).forEach(selector => {
        const els = document.querySelectorAll(selector);
        els.forEach(el => {
            // Create wrapper if not exists
            if (!el.parentNode.classList.contains('editable-img-wrapper')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'editable-img-wrapper';
                // Move styles that affect positioning from img to wrapper
                if(el.style.position === 'absolute') {
                    wrapper.style.position = 'absolute';
                    wrapper.style.top = el.style.top;
                    wrapper.style.left = el.style.left;
                    wrapper.style.width = el.style.width;
                    wrapper.style.height = el.style.height;
                    wrapper.style.zIndex = el.style.zIndex;
                    wrapper.style.borderRadius = el.style.borderRadius || 'inherit';
                } else {
                    wrapper.style.display = 'inline-block';
                    wrapper.style.width = '100%';
                    wrapper.style.height = '100%';
                }
                el.parentNode.insertBefore(wrapper, el);
                wrapper.appendChild(el);
            }

            const wrapper = el.parentNode;
            const fileInputId = 'file_' + imageMappings[selector];
            const fileInput = document.getElementById(fileInputId);

            wrapper.addEventListener('click', function(e) {
                e.preventDefault();
                fileInput.click();
            });

            fileInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        el.src = e.target.result;
                    }
                    reader.readAsDataURL(this.files[0]);
                    pendingFiles.set(imageMappings[selector], this.files[0]);
                }
            });
        });
    });

    // Save Button Logic
    document.getElementById('saveLiveBtn').addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
        btn.disabled = true;

        const contentData = {};
        Object.keys(textMappings).forEach(selector => {
            const el = document.querySelector(selector);
            if (el) {
                // Remove HTML tags for clean save
                contentData[textMappings[selector]] = el.innerHTML.trim();
            }
        });

        pendingFiles.set('ajax_action', 'save_about_live');
        pendingFiles.set('content_data', JSON.stringify(contentData));

        fetch('editor-about.php', {
            method: 'POST',
            body: pendingFiles
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const toast = document.getElementById('saveToast');
                toast.classList.add('show');
                setTimeout(() => toast.classList.remove('show'), 3000);
                pendingFiles = new FormData(); // Clear pending files
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred while saving.');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
