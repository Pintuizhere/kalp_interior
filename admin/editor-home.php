<?php 
$currentPage = 'page_home';
require_once 'config/db.php';
include 'includes/header.php'; 

// Fetch current content
$home_content = [];
$stmt = $conn->prepare("SELECT section_key, content_value FROM page_content WHERE page_name = 'home'");
$stmt->execute();
$result = $stmt->get_result();
while($row = $result->fetch_assoc()) {
    $home_content[$row['section_key']] = $row['content_value'];
}
$stmt->close();

$bg_image = !empty($home_content['hero_bg_image']) ? htmlspecialchars($home_content['hero_bg_image']) : 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';
$rating_text = !empty($home_content['hero_rating_text']) ? htmlspecialchars($home_content['hero_rating_text']) : '4.9/5 Rating - 15,000 Reviews';
$hero_title = !empty($home_content['hero_title']) ? $home_content['hero_title'] : 'Elevate <span class="accent-text" style="font-family: var(--font-accent); font-weight: bold; display: inline-block; position: relative; z-index: 10;">Your Space</span> with Exceptional Interior Design';
$hero_desc = !empty($home_content['hero_desc']) ? $home_content['hero_desc'] : 'Kalp Interiors specializes in modern, luxurious, and personalized interior experiences.';
$btn1_text = !empty($home_content['hero_btn1_text']) ? htmlspecialchars($home_content['hero_btn1_text']) : 'Contact us';
$btn1_link = !empty($home_content['hero_btn1_link']) ? htmlspecialchars($home_content['hero_btn1_link']) : 'contact.php';
$btn2_text = !empty($home_content['hero_btn2_text']) ? htmlspecialchars($home_content['hero_btn2_text']) : 'View Projects';
$btn2_link = !empty($home_content['hero_btn2_link']) ? htmlspecialchars($home_content['hero_btn2_link']) : 'projects.php';
$stat_projects = !empty($home_content['stat_projects']) ? $home_content['stat_projects'] : '500+';
$stat_projects_label = !empty($home_content['stat_projects_label']) ? $home_content['stat_projects_label'] : 'Projects Completed';
$stat_experience = !empty($home_content['stat_experience']) ? $home_content['stat_experience'] : '18+';
$stat_experience_label = !empty($home_content['stat_experience_label']) ? $home_content['stat_experience_label'] : 'Years of Experience';
$stat_clients = !empty($home_content['stat_clients']) ? $home_content['stat_clients'] : '300+';
$stat_clients_label = !empty($home_content['stat_clients_label']) ? $home_content['stat_clients_label'] : 'Happy Clients';
$stat_satisfaction = !empty($home_content['stat_satisfaction']) ? $home_content['stat_satisfaction'] : '98%';
$stat_satisfaction_label = !empty($home_content['stat_satisfaction_label']) ? $home_content['stat_satisfaction_label'] : 'Client Satisfaction';

$avatar_1 = !empty($home_content['hero_avatar_1']) ? htmlspecialchars($home_content['hero_avatar_1']) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80';
$avatar_2 = !empty($home_content['hero_avatar_2']) ? htmlspecialchars($home_content['hero_avatar_2']) : 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80';
$avatar_3 = !empty($home_content['hero_avatar_3']) ? htmlspecialchars($home_content['hero_avatar_3']) : 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80';
$avatar_4 = !empty($home_content['hero_avatar_4']) ? htmlspecialchars($home_content['hero_avatar_4']) : 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80';

?>
<style>
    /* Force include frontend CSS */
    @import url('../assets/css/style.css');

    /* Hide frontend navigation and footer elements that might come from style.css */
    .navbar, footer, .main-footer, .footer-bottom, .cta-banner { display: none !important; }
    
    /* Fix topbar padding overridden by frontend CSS */
    header.topbar { padding: 0 30px !important; }
    
    /* Make the editor content flush */
    .main-content { padding: 0 !important; overflow-x: hidden; }
    
    /* Live Editor Styles */
    [contenteditable="true"] {
        outline: 1px dashed transparent;
        transition: all 0.3s ease;
        padding: 2px;
        border-radius: 4px;
        position: relative;
        z-index: 100;
    }
    [contenteditable="true"]:hover {
        outline: 2px dashed #eab136;
        background: rgba(234, 177, 54, 0.1);
        cursor: text;
    }
    [contenteditable="true"]:focus {
        outline: 2px solid #eab136;
        background: rgba(0,0,0,0.5);
    }
    
    .editable-img-wrapper {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 1000;
    }
    .editable-img-btn {
        background: #eab136;
        color: #fff;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
    }
    
    .editable-avatar {
        transition: transform 0.2s, outline 0.2s;
        cursor: pointer;
    }
    .editable-avatar:hover {
        transform: scale(1.1);
        outline: 2px dashed #eab136;
        z-index: 10;
    }

    .save-bar {
        position: fixed;
        bottom: 0;
        left: 250px; /* Sidebar width */
        right: 0;
        background: #fff;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 -4px 15px rgba(0,0,0,0.1);
        z-index: 9999;
    }
    .save-btn {
        background: #eab136;
        color: #000;
        border: none;
        padding: 10px 25px;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        font-size: 16px;
    }
    .save-btn:hover {
        background: #d49c25;
    }
    
    /* Prevent links from breaking layout */
    .new-hero-actions a {
        pointer-events: none;
    }
    
    /* Fix responsive save bar */
    @media (max-width: 992px) {
        .save-bar { left: 0; }
    }
</style>

<?php include 'includes/sidebar.php'; ?>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        <div style="position: relative; width: 100%; min-height: calc(100vh - 70px); padding-bottom: 80px;">
            
            <div class="editable-img-wrapper">
                <button type="button" class="editable-img-btn" onclick="document.getElementById('bg-image-input').click()">
                    <i class="fa-solid fa-image"></i> Change Background
                </button>
                <input type="file" id="bg-image-input" accept="image/*" style="display:none">
                <input type="hidden" id="hero_bg_image_val" value="<?php echo $bg_image; ?>">
            </div>

            <!-- Hero Section Structure from frontend -->
            <section class="new-hero-section" style="min-height: 100vh; margin: 0; border-radius: 0;">
                <div class="new-hero-wrapper" style="border-radius: 0;">
                    <?php 
                    // Make sure relative path works if uploaded from admin
                    $bg_url = $bg_image;
                    if (strpos($bg_url, 'uploads/') === 0) {
                        $bg_url = '../' . $bg_url;
                    }
                    ?>
                    <div class="hero-bg-image" id="live-bg-img" style="background-image: url('<?php echo $bg_url; ?>');"></div>
                    <div class="hero-bg-overlay"></div>
                    <div class="container" style="position: relative; z-index: 3; width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center;">
                        <div class="new-hero-content">
                            
                            <div class="hero-rating-widget">
                                <div class="rating-avatars" id="editable-avatars-container">
                                    <?php
                                    $av_urls = [$avatar_1, $avatar_2, $avatar_3, $avatar_4];
                                    for ($i=1; $i<=4; $i++) {
                                        $url = $av_urls[$i-1];
                                        if (strpos($url, 'uploads/') === 0) {
                                            $url = '../' . $url;
                                        }
                                        echo '<img src="'.$url.'" alt="User" class="editable-avatar" data-index="'.$i.'" title="Click to change avatar">';
                                        echo '<input type="hidden" id="hero_avatar_'.$i.'_val" value="'.$av_urls[$i-1].'">';
                                    }
                                    ?>
                                    <input type="file" id="avatar-image-input" accept="image/*" style="display:none">
                                </div>
                                <div class="rating-text-block">
                                    <div class="rating-stars">
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                    <span class="rating-text" contenteditable="true" id="edit-rating-text"><?php echo $rating_text; ?></span>
                                </div>
                            </div>

                            <h1 class="new-hero-title" contenteditable="true" id="edit-hero-title"><?php echo $hero_title; ?></h1>
                            <p class="new-hero-desc" contenteditable="true" id="edit-hero-desc"><?php echo $hero_desc; ?></p>
                            
                            <div class="new-hero-actions">
                                <a href="#" class="btn" style="background-color: var(--accent-color); color: var(--text-dark); border: none; font-weight: 600; padding: 15px 30px;">
                                    <span contenteditable="true" id="edit-btn2-text"><?php echo $btn2_text; ?></span> <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i>
                                </a>
                                <a href="#" class="view-projects-link">
                                    <span contenteditable="true" id="edit-btn1-text"><?php echo $btn1_text; ?></span> <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>

                        </div>

                        <!-- Bottom Stats Grid -->
                        <div class="new-hero-stats">
                            <div class="hero-stat-col">
                                <h3 contenteditable="true" id="edit-stat-projects"><?php echo $stat_projects; ?></h3>
                                <p contenteditable="true" id="edit-stat-projects-label"><?php echo $stat_projects_label; ?></p>
                            </div>
                            <div class="hero-stat-col">
                                <h3 contenteditable="true" id="edit-stat-experience"><?php echo $stat_experience; ?></h3>
                                <p contenteditable="true" id="edit-stat-experience-label"><?php echo $stat_experience_label; ?></p>
                            </div>
                            <div class="hero-stat-col">
                                <h3 contenteditable="true" id="edit-stat-clients"><?php echo $stat_clients; ?></h3>
                                <p contenteditable="true" id="edit-stat-clients-label"><?php echo $stat_clients_label; ?></p>
                            </div>
                            <div class="hero-stat-col border-none">
                                <h3 contenteditable="true" id="edit-stat-satisfaction"><?php echo $stat_satisfaction; ?></h3>
                                <p contenteditable="true" id="edit-stat-satisfaction-label"><?php echo $stat_satisfaction_label; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
        <div class="save-bar">
            <div>
                <h3 style="margin: 0; font-size: 18px; color: #333;">Live Hero Editor</h3>
                <p style="margin: 0; font-size: 13px; color: #666;">Click on any text to edit. Click 'Change Background' to swap image.</p>
            </div>
            <div>
                <button class="save-btn" id="reset-hero-btn" style="background: #e2e8f0; color: #333; margin-right: 10px;"><i class="fa-solid fa-rotate-left"></i> Reset to Default</button>
                <button class="save-btn" id="save-hero-btn"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Background Image logic
    const bgInput = document.getElementById('bg-image-input');
    const liveBg = document.getElementById('live-bg-img');
    const bgVal = document.getElementById('hero_bg_image_val');
    
    bgInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                liveBg.style.backgroundImage = `url('${e.target.result}')`;
                bgVal.value = e.target.result; 
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Avatar Logic
    const avatarInput = document.getElementById('avatar-image-input');
    let currentAvatarIndex = null;
    let currentAvatarImg = null;

    document.querySelectorAll('.editable-avatar').forEach(img => {
        img.addEventListener('click', function() {
            currentAvatarIndex = this.getAttribute('data-index');
            currentAvatarImg = this;
            avatarInput.click();
        });
    });

    avatarInput.addEventListener('change', function() {
        if (this.files && this.files[0] && currentAvatarIndex) {
            const reader = new FileReader();
            reader.onload = function(e) {
                currentAvatarImg.src = e.target.result;
                document.getElementById('hero_avatar_' + currentAvatarIndex + '_val').value = e.target.result;
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Reset Logic
    document.getElementById('reset-hero-btn').addEventListener('click', function() {
        if (!confirm('Are you sure you want to reset all hero content to default? This cannot be undone.')) {
            return;
        }
        
        const btn = this;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Resetting...';
        
        const formData = new FormData();
        formData.append('action', 'reset_hero');

        fetch('ajax/save_hero.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(res => {
            if(res.success) {
                window.location.reload();
            } else {
                alert('Error resetting data');
                btn.innerHTML = '<i class="fa-solid fa-rotate-left"></i> Reset to Default';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Request failed');
            btn.innerHTML = '<i class="fa-solid fa-rotate-left"></i> Reset to Default';
        });
    });

    // Save Logic
    document.getElementById('save-hero-btn').addEventListener('click', function() {
        const btn = this;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
        
        const data = {
            action: 'save_hero',
            hero_bg_image: bgVal.value,
            hero_avatar_1: document.getElementById('hero_avatar_1_val').value,
            hero_avatar_2: document.getElementById('hero_avatar_2_val').value,
            hero_avatar_3: document.getElementById('hero_avatar_3_val').value,
            hero_avatar_4: document.getElementById('hero_avatar_4_val').value,
            hero_rating_text: document.getElementById('edit-rating-text').innerText.trim(),
            hero_title: document.getElementById('edit-hero-title').innerHTML.trim(), // Keep span HTML if any
            hero_desc: document.getElementById('edit-hero-desc').innerText.trim(),
            hero_btn1_text: document.getElementById('edit-btn1-text').innerText.trim(),
            hero_btn2_text: document.getElementById('edit-btn2-text').innerText.trim(),
            stat_projects: document.getElementById('edit-stat-projects').innerText.trim(),
            stat_projects_label: document.getElementById('edit-stat-projects-label').innerText.trim(),
            stat_experience: document.getElementById('edit-stat-experience').innerText.trim(),
            stat_experience_label: document.getElementById('edit-stat-experience-label').innerText.trim(),
            stat_clients: document.getElementById('edit-stat-clients').innerText.trim(),
            stat_clients_label: document.getElementById('edit-stat-clients-label').innerText.trim(),
            stat_satisfaction: document.getElementById('edit-stat-satisfaction').innerText.trim(),
            stat_satisfaction_label: document.getElementById('edit-stat-satisfaction-label').innerText.trim(),
        };

        const formData = new FormData();
        for (const key in data) {
            formData.append(key, data[key]);
        }

        fetch('ajax/save_hero.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(res => {
            if(res.success) {
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Saved!';
                setTimeout(() => {
                    btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Changes';
                }, 2000);
            } else {
                alert('Error saving data');
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Changes';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Request failed');
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Changes';
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
