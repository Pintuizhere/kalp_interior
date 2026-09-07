<?php 
session_start();
$currentPage = 'services';
require_once 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: services.php");
    exit();
}
$service_id = (int)$_GET['id'];

// Get service base info
$res = $conn->query("SELECT * FROM services WHERE id = $service_id");
if (!$res || $res->num_rows === 0) {
    header("Location: services.php");
    exit();
}
$service = $res->fetch_assoc();

// Fetch dynamic content for this service
$page_name = 'service_' . $service_id;
$service_content = [];
$stmt = $conn->prepare("SELECT section_key, content_value FROM page_content WHERE page_name = ?");
$stmt->bind_param("s", $page_name);
$stmt->execute();
$result = $stmt->get_result();
while($row = $result->fetch_assoc()) {
    $service_content[$row['section_key']] = $row['content_value'];
}
$stmt->close();

// Fallbacks
$cover_img_fallback = (strpos($service['cover_image'], 'http') === 0) ? $service['cover_image'] : $service['cover_image'];
$sd_hero_title = !empty($service_content['sd_hero_title']) ? $service_content['sd_hero_title'] : strtoupper($service['name']);
$sd_hero_signature = !empty($service_content['sd_hero_signature']) ? $service_content['sd_hero_signature'] : 'Designed Around You';
$sd_hero_desc = !empty($service_content['sd_hero_desc']) ? $service_content['sd_hero_desc'] : 'We create beautiful, functional interiors that reflect your personality and lifestyle. From concept to completion, we handle every detail to deliver spaces that inspire.';
$sd_hero_img = !empty($service_content['sd_hero_img']) ? $service_content['sd_hero_img'] : $cover_img_fallback;

$sd_banner_img = !empty($service_content['sd_banner_img']) ? $service_content['sd_banner_img'] : 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';

$sd_f1_title = !empty($service_content['sd_f1_title']) ? $service_content['sd_f1_title'] : 'Personalized Approach';
$sd_f1_desc = !empty($service_content['sd_f1_desc']) ? $service_content['sd_f1_desc'] : 'Tailored designs that reflect your style and needs.';
$sd_f2_title = !empty($service_content['sd_f2_title']) ? $service_content['sd_f2_title'] : 'Smart Space Planning';
$sd_f2_desc = !empty($service_content['sd_f2_desc']) ? $service_content['sd_f2_desc'] : 'Maximizing space, functionality and natural flow.';
$sd_f3_title = !empty($service_content['sd_f3_title']) ? $service_content['sd_f3_title'] : 'Premium Materials';
$sd_f3_desc = !empty($service_content['sd_f3_desc']) ? $service_content['sd_f3_desc'] : 'High-quality finishes and carefully curated materials.';
$sd_f4_title = !empty($service_content['sd_f4_title']) ? $service_content['sd_f4_title'] : 'End-to-End Service';
$sd_f4_desc = !empty($service_content['sd_f4_desc']) ? $service_content['sd_f4_desc'] : 'From concept and design to execution and styling.';

$sd_why_title = !empty($service_content['sd_why_title']) ? $service_content['sd_why_title'] : 'What Makes Our';
$sd_why_signature = !empty($service_content['sd_why_signature']) ? $service_content['sd_why_signature'] : 'Service Unique?';
$sd_why_desc = !empty($service_content['sd_why_desc']) ? $service_content['sd_why_desc'] : 'We blend creativity with functionality to design spaces that are not only beautiful but also practical and timeless. Every project is managed with attention to detail, ensuring seamless execution and complete client satisfaction.';
$sd_why_img = !empty($service_content['sd_why_img']) ? $service_content['sd_why_img'] : '../uploads/media/3d_rendering_cover.png';

$sd_why_l1_title = !empty($service_content['sd_why_l1_title']) ? $service_content['sd_why_l1_title'] : 'Creative & Functional Designs';
$sd_why_l1_desc = !empty($service_content['sd_why_l1_desc']) ? $service_content['sd_why_l1_desc'] : 'Spaces that look stunning and work beautifully for everyday living.';
$sd_why_l2_title = !empty($service_content['sd_why_l2_title']) ? $service_content['sd_why_l2_title'] : 'Client-Centric Process';
$sd_why_l2_desc = !empty($service_content['sd_why_l2_desc']) ? $service_content['sd_why_l2_desc'] : 'Your vision is our priority. We listen, collaborate and deliver.';
$sd_why_l3_title = !empty($service_content['sd_why_l3_title']) ? $service_content['sd_why_l3_title'] : 'Timely Delivery';
$sd_why_l3_desc = !empty($service_content['sd_why_l3_desc']) ? $service_content['sd_why_l3_desc'] : 'On-time project completion with transparency at every step.';
$sd_why_l4_title = !empty($service_content['sd_why_l4_title']) ? $service_content['sd_why_l4_title'] : 'Quality You Can Trust';
$sd_why_l4_desc = !empty($service_content['sd_why_l4_desc']) ? $service_content['sd_why_l4_desc'] : 'We use top-grade materials and partner with skilled craftsmen.';

$sd_process_title = !empty($service_content['sd_process_title']) ? $service_content['sd_process_title'] : 'Simple Steps to Your';
$sd_process_signature = !empty($service_content['sd_process_signature']) ? $service_content['sd_process_signature'] : 'Dream Space';

$sd_p1_title = !empty($service_content['sd_p1_title']) ? $service_content['sd_p1_title'] : 'Consultation';
$sd_p1_desc = !empty($service_content['sd_p1_desc']) ? $service_content['sd_p1_desc'] : 'We understand your needs, style, and budget.';
$sd_p2_title = !empty($service_content['sd_p2_title']) ? $service_content['sd_p2_title'] : 'Concept & Planning';
$sd_p2_desc = !empty($service_content['sd_p2_desc']) ? $service_content['sd_p2_desc'] : 'Our team creates layouts, mood boards & 3D visuals.';
$sd_p3_title = !empty($service_content['sd_p3_title']) ? $service_content['sd_p3_title'] : 'Design Development';
$sd_p3_desc = !empty($service_content['sd_p3_desc']) ? $service_content['sd_p3_desc'] : 'Finalizing materials, colors, furniture & finishes.';
$sd_p4_title = !empty($service_content['sd_p4_title']) ? $service_content['sd_p4_title'] : 'Execution';
$sd_p4_desc = !empty($service_content['sd_p4_desc']) ? $service_content['sd_p4_desc'] : 'We manage the entire process with precision.';
$sd_p5_title = !empty($service_content['sd_p5_title']) ? $service_content['sd_p5_title'] : 'Final Styling';
$sd_p5_desc = !empty($service_content['sd_p5_desc']) ? $service_content['sd_p5_desc'] : 'Adding the perfect finishing touches to bring it all together.';

$pageTitle = 'Live Editor - ' . $service['name'];
include 'includes/header.php'; 
?>
<style>
    @import url('../assets/css/style.css');
    .navbar, footer, .main-footer, .footer-bottom, .cta-banner { display: none !important; }
    header.topbar { padding: 0 30px !important; }
    .main-content { padding: 0 !important; overflow-x: hidden; background: #fff; }
    
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
        color: #fff;
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
    
    .save-bar {
        position: fixed;
        bottom: 0;
        left: 250px;
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
    .save-btn:hover { background: #d49c25; }
    @media (max-width: 992px) { .save-bar { left: 0; } }

    .editable-image {
        cursor: pointer;
        transition: 0.3s;
        border: 2px dashed transparent;
    }
    .editable-image:hover {
        border: 2px dashed #eab136;
        opacity: 0.8;
    }
</style>

<?php include 'includes/sidebar.php'; ?>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        <div style="position: relative; width: 100%; min-height: calc(100vh - 70px); padding-bottom: 80px;">
            
            <!-- Editor Content (Service Details Layout) -->
            <main class="sd-page" style="background-color: var(--bg-white);">
                <?php $b_url = (strpos($sd_banner_img, 'http') === 0 || strpos($sd_banner_img, 'data:image') === 0) ? $sd_banner_img : '../' . $sd_banner_img; ?>
                <section class="page-banner" id="banner-section" style="position: relative; background-image: linear-gradient(rgba(26, 38, 30, 0.85), rgba(26, 38, 30, 0.85)), url('<?php echo $b_url; ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                    <div class="container">
                        <h1 class="banner-title">SERVICES</h1>
                        <div class="breadcrumbs">
                            <a href="#">Home</a> <span class="divider">/</span> <span class="current">Services</span> <span class="divider">/</span> <span class="current"><?php echo htmlspecialchars($service['name']); ?></span>
                        </div>
                    </div>
                    
                    <button class="editable-img-btn" onclick="document.getElementById('input-banner').click()" style="position:absolute; top: 20px; right: 20px; z-index: 10;">Click image to edit</button>
                    <input type="file" id="input-banner" accept="image/*" style="display:none">
                    <input type="hidden" id="v-sd_banner_img" value="<?php echo $sd_banner_img; ?>">
                </section>

                <!-- 1. Hero Section -->
                <section class="sd-redesign-hero" style="padding: 60px 0; background-color: var(--bg-white);">
                    <div class="container" style="max-width: 1200px;">
                        <div class="sd-hero-split">
                            <div class="sd-hero-text">
                                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                                    <p class="section-subtitle" style="margin-bottom: 0;">OUR SERVICE</p>
                                </div>
                                <h1 class="sd-hero-title" contenteditable="true" id="v-sd_hero_title"><?php echo $sd_hero_title; ?></h1>
                                <h2 class="sd-hero-signature signature-text" contenteditable="true" id="v-sd_hero_signature"><?php echo $sd_hero_signature; ?></h2>
                                
                                <p class="sd-hero-desc" contenteditable="true" id="v-sd_hero_desc"><?php echo $sd_hero_desc; ?></p>
                                
                                <div class="sd-hero-buttons">
                                    <a href="#" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 10px; pointer-events: none;">Get Estimate</a>
                                </div>
                            </div>
                            <div class="sd-hero-img-box" style="position: relative;">
                                <?php $h_url = (strpos($sd_hero_img, 'http') === 0 || strpos($sd_hero_img, 'data:image') === 0) ? $sd_hero_img : '../' . $sd_hero_img; ?>
                                <img src="<?php echo $h_url; ?>" alt="Hero" class="editable-image" id="img-hero" onclick="document.getElementById('input-hero').click()">
                                <input type="file" id="input-hero" accept="image/*" style="display:none">
                                <input type="hidden" id="v-sd_hero_img" value="<?php echo $sd_hero_img; ?>">
                                <div style="position:absolute; top: 10px; right:10px; background: rgba(0,0,0,0.7); color:white; padding: 4px 8px; border-radius: 4px; font-size:12px; pointer-events:none;">Click image to edit</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 2. Features Grid Row -->
                <section class="sd-features-grid-new" style="background-color: var(--bg-white); padding-bottom: 60px;">
                    <div class="container" style="max-width: 1200px;">
                        <div class="sd-features-wrapper">
                            <div class="sd-f-col">
                                <div class="sd-f-icon"><i class="fa-solid fa-pen-ruler"></i></div>
                                <h4 contenteditable="true" id="v-sd_f1_title"><?php echo $sd_f1_title; ?></h4>
                                <p contenteditable="true" id="v-sd_f1_desc"><?php echo $sd_f1_desc; ?></p>
                            </div>
                            <div class="sd-f-col">
                                <div class="sd-f-icon"><i class="fa-solid fa-layer-group"></i></div>
                                <h4 contenteditable="true" id="v-sd_f2_title"><?php echo $sd_f2_title; ?></h4>
                                <p contenteditable="true" id="v-sd_f2_desc"><?php echo $sd_f2_desc; ?></p>
                            </div>
                            <div class="sd-f-col">
                                <div class="sd-f-icon"><i class="fa-solid fa-couch"></i></div>
                                <h4 contenteditable="true" id="v-sd_f3_title"><?php echo $sd_f3_title; ?></h4>
                                <p contenteditable="true" id="v-sd_f3_desc"><?php echo $sd_f3_desc; ?></p>
                            </div>
                            <div class="sd-f-col">
                                <div class="sd-f-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                                <h4 contenteditable="true" id="v-sd_f4_title"><?php echo $sd_f4_title; ?></h4>
                                <p contenteditable="true" id="v-sd_f4_desc"><?php echo $sd_f4_desc; ?></p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 3. Why Choose Us Split -->
                <section class="sd-why-choose-split" style="padding: 80px 0; background-color: var(--bg-white);">
                    <div class="container" style="max-width: 1200px;">
                        <div class="sd-why-grid">
                            <div class="sd-why-left">
                                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                                    <p class="section-subtitle" style="margin-bottom: 0;">WHY CHOOSE US</p>
                                </div>
                                <h2 class="sd-why-title"><span contenteditable="true" id="v-sd_why_title"><?php echo $sd_why_title; ?></span><br><span class="signature-text" style="color: var(--accent-color); font-weight: 400; text-transform: none;" contenteditable="true" id="v-sd_why_signature"><?php echo $sd_why_signature; ?></span></h2>
                                
                                <p class="sd-why-desc" contenteditable="true" id="v-sd_why_desc"><?php echo $sd_why_desc; ?></p>
                                
                                <div class="sd-why-img" style="position:relative;">
                                    <?php $w_url = (strpos($sd_why_img, 'http') === 0 || strpos($sd_why_img, 'data:image') === 0) ? $sd_why_img : '../' . $sd_why_img; ?>
                                    <img src="<?php echo $w_url; ?>" alt="Why Choose" class="editable-image" id="img-why" onclick="document.getElementById('input-why').click()">
                                    <input type="file" id="input-why" accept="image/*" style="display:none">
                                    <input type="hidden" id="v-sd_why_img" value="<?php echo $sd_why_img; ?>">
                                    <div style="position:absolute; top: 10px; right:10px; background: rgba(0,0,0,0.7); color:white; padding: 4px 8px; border-radius: 4px; font-size:12px; pointer-events:none;">Click image to edit</div>
                                </div>
                            </div>
                            <div class="sd-why-right">
                                <div class="why-list-item">
                                    <div class="why-check"><i class="fa-solid fa-check"></i></div>
                                    <div class="why-text">
                                        <h4 contenteditable="true" id="v-sd_why_l1_title"><?php echo $sd_why_l1_title; ?></h4>
                                        <p contenteditable="true" id="v-sd_why_l1_desc"><?php echo $sd_why_l1_desc; ?></p>
                                    </div>
                                </div>
                                <div class="why-list-item">
                                    <div class="why-check"><i class="fa-solid fa-check"></i></div>
                                    <div class="why-text">
                                        <h4 contenteditable="true" id="v-sd_why_l2_title"><?php echo $sd_why_l2_title; ?></h4>
                                        <p contenteditable="true" id="v-sd_why_l2_desc"><?php echo $sd_why_l2_desc; ?></p>
                                    </div>
                                </div>
                                <div class="why-list-item">
                                    <div class="why-check"><i class="fa-solid fa-check"></i></div>
                                    <div class="why-text">
                                        <h4 contenteditable="true" id="v-sd_why_l3_title"><?php echo $sd_why_l3_title; ?></h4>
                                        <p contenteditable="true" id="v-sd_why_l3_desc"><?php echo $sd_why_l3_desc; ?></p>
                                    </div>
                                </div>
                                <div class="why-list-item">
                                    <div class="why-check"><i class="fa-solid fa-check"></i></div>
                                    <div class="why-text">
                                        <h4 contenteditable="true" id="v-sd_why_l4_title"><?php echo $sd_why_l4_title; ?></h4>
                                        <p contenteditable="true" id="v-sd_why_l4_desc"><?php echo $sd_why_l4_desc; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 4. Our Process -->
                <section class="sd-process-timeline" style="padding: 100px 0; background-color: #F8F5F0;">
                    <div class="container" style="max-width: 1200px; text-align: center;">
                        <p class="section-subtitle" style="margin-bottom: 10px; justify-content: center;">OUR PROCESS</p>
                        <h2 class="sd-process-title"><span contenteditable="true" id="v-sd_process_title"><?php echo $sd_process_title; ?></span> <span class="signature-text" style="color: var(--accent-color); font-weight: 400; text-transform: none;" contenteditable="true" id="v-sd_process_signature"><?php echo $sd_process_signature; ?></span></h2>
                        
                        <div class="timeline-horizontal">
                            <!-- Step 1 -->
                            <div class="timeline-step">
                                <div class="tl-icon-box"><i class="fa-solid fa-clipboard-list"></i><span class="tl-number">1</span></div>
                                <h4 contenteditable="true" id="v-sd_p1_title"><?php echo $sd_p1_title; ?></h4>
                                <p contenteditable="true" id="v-sd_p1_desc"><?php echo $sd_p1_desc; ?></p>
                            </div>
                            <div class="tl-arrow"><i class="fa-solid fa-arrow-right-long" style="color: #df916b; opacity: 0.5;"></i></div>
                            <!-- Step 2 -->
                            <div class="timeline-step">
                                <div class="tl-icon-box"><i class="fa-solid fa-compass-drafting"></i><span class="tl-number">2</span></div>
                                <h4 contenteditable="true" id="v-sd_p2_title"><?php echo $sd_p2_title; ?></h4>
                                <p contenteditable="true" id="v-sd_p2_desc"><?php echo $sd_p2_desc; ?></p>
                            </div>
                            <div class="tl-arrow"><i class="fa-solid fa-arrow-right-long" style="color: #df916b; opacity: 0.5;"></i></div>
                            <!-- Step 3 -->
                            <div class="timeline-step">
                                <div class="tl-icon-box"><i class="fa-solid fa-pen-nib"></i><span class="tl-number">3</span></div>
                                <h4 contenteditable="true" id="v-sd_p3_title"><?php echo $sd_p3_title; ?></h4>
                                <p contenteditable="true" id="v-sd_p3_desc"><?php echo $sd_p3_desc; ?></p>
                            </div>
                            <div class="tl-arrow"><i class="fa-solid fa-arrow-right-long" style="color: #df916b; opacity: 0.5;"></i></div>
                            <!-- Step 4 -->
                            <div class="timeline-step">
                                <div class="tl-icon-box"><i class="fa-solid fa-hammer"></i><span class="tl-number">4</span></div>
                                <h4 contenteditable="true" id="v-sd_p4_title"><?php echo $sd_p4_title; ?></h4>
                                <p contenteditable="true" id="v-sd_p4_desc"><?php echo $sd_p4_desc; ?></p>
                            </div>
                            <div class="tl-arrow"><i class="fa-solid fa-arrow-right-long" style="color: #df916b; opacity: 0.5;"></i></div>
                            <!-- Step 5 -->
                            <div class="timeline-step">
                                <div class="tl-icon-box"><i class="fa-solid fa-star"></i><span class="tl-number">5</span></div>
                                <h4 contenteditable="true" id="v-sd_p5_title"><?php echo $sd_p5_title; ?></h4>
                                <p contenteditable="true" id="v-sd_p5_desc"><?php echo $sd_p5_desc; ?></p>
                            </div>
                        </div>
                    </div>
                </section>
                
                <div style="padding: 40px; text-align: center; color: #777; background: #fff;">
                    <i>Note: The "More Projects" and "Contact Form" sections are globally shared and cannot be edited per-service.</i>
                </div>

                <!-- SEO Settings -->
                <section style="background: #f8f9fa; padding: 40px 20px; border-top: 1px solid #e2e8f0;">
                    <div class="container" style="max-width: 800px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin: 0 auto;">
                        <h3 style="margin-top: 0; margin-bottom: 20px; color: var(--text-dark); font-size: 18px;">SEO Settings</h3>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Meta Title</label>
                                <input type="text" id="v-meta_title" value="<?php echo htmlspecialchars($service['meta_title'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px;" placeholder="Meta Title">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Meta Keywords</label>
                                <input type="text" id="v-meta_keywords" value="<?php echo htmlspecialchars($service['meta_keywords'] ?? ''); ?>" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px;" placeholder="Meta Keywords">
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Meta Description</label>
                            <textarea id="v-meta_description" rows="2" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px;" placeholder="Meta Description"><?php echo htmlspecialchars($service['meta_description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0; display: flex; align-items: center; gap: 10px;">
                            <label style="margin-bottom: 0; font-weight: 700; font-size: 18px; color: var(--text-dark);">Permalink:</label>
                            <span style="font-size: 18px; color: #666;">/service-details.php?slug=</span>
                            <input type="text" id="v-slug" value="<?php echo htmlspecialchars($service['slug'] ?? ''); ?>" style="flex: 1; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 16px; font-family: monospace;" placeholder="service-slug">
                        </div>
                    </div>
                </section>

            </main>
        </div>
        
        <div class="save-bar">
            <div>
                <h3 style="margin: 0; font-size: 18px; color: #333;">Live Editor: <?php echo htmlspecialchars($service['name']); ?></h3>
                <p style="margin: 0; font-size: 13px; color: #666;">Click on any text or image to edit it directly on the page.</p>
            </div>
            <div>
                <button class="save-btn" id="reset-service-btn" style="background: #e2e8f0; color: #333; margin-right: 10px;"><i class="fa-solid fa-rotate-left"></i> Reset to Default</button>
                <button class="save-btn" id="save-service-btn"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const pageName = '<?php echo $page_name; ?>';

    // Image Upload Logic
    function bindImageUpload(inputId, imgId, hiddenId) {
        document.getElementById(inputId).addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(imgId).src = e.target.result;
                    document.getElementById(hiddenId).value = e.target.result; 
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    function bindBackgroundUpload(inputId, sectionId, hiddenId) {
        document.getElementById(inputId).addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(sectionId).style.backgroundImage = `linear-gradient(rgba(26, 38, 30, 0.85), rgba(26, 38, 30, 0.85)), url('${e.target.result}')`;
                    document.getElementById(sectionId).style.backgroundSize = 'cover';
                    document.getElementById(sectionId).style.backgroundPosition = 'center';
                    document.getElementById(sectionId).style.backgroundRepeat = 'no-repeat';
                    document.getElementById(hiddenId).value = e.target.result; 
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    bindImageUpload('input-hero', 'img-hero', 'v-sd_hero_img');
    bindImageUpload('input-why', 'img-why', 'v-sd_why_img');
    bindBackgroundUpload('input-banner', 'banner-section', 'v-sd_banner_img');

    // Reset Logic
    document.getElementById('reset-service-btn').addEventListener('click', function() {
        if (!confirm('Are you sure you want to reset this service page to the default layout?')) return;
        
        const btn = this;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Resetting...';
        
        const formData = new FormData();
        formData.append('action', 'reset_service');
        formData.append('page_name', pageName);

        fetch('ajax/save_service_details.php', {
            method: 'POST',
            body: formData
        }).then(r => r.json()).then(res => {
            if(res.success) window.location.reload();
            else { alert('Error'); btn.innerHTML = 'Reset to Default'; }
        });
    });

    // Save Logic
    document.getElementById('save-service-btn').addEventListener('click', function() {
        const btn = this;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
        
        const keys = [
            'sd_banner_img', 'sd_hero_title', 'sd_hero_signature', 'sd_hero_desc', 'sd_hero_img',
            'sd_f1_title', 'sd_f1_desc', 'sd_f2_title', 'sd_f2_desc',
            'sd_f3_title', 'sd_f3_desc', 'sd_f4_title', 'sd_f4_desc',
            'sd_why_title', 'sd_why_signature', 'sd_why_desc', 'sd_why_img',
            'sd_why_l1_title', 'sd_why_l1_desc', 'sd_why_l2_title', 'sd_why_l2_desc',
            'sd_why_l3_title', 'sd_why_l3_desc', 'sd_why_l4_title', 'sd_why_l4_desc',
            'sd_process_title', 'sd_process_signature',
            'sd_p1_title', 'sd_p1_desc', 'sd_p2_title', 'sd_p2_desc',
            'sd_p3_title', 'sd_p3_desc', 'sd_p4_title', 'sd_p4_desc', 'sd_p5_title', 'sd_p5_desc',
            'meta_title', 'meta_keywords', 'meta_description', 'slug'
        ];

        const formData = new FormData();
        formData.append('action', 'save_service');
        formData.append('page_name', pageName);

        keys.forEach(k => {
            const el = document.getElementById('v-' + k);
            if (el) {
                // If it's a hidden input, take value. If contenteditable, take innerText/innerHTML
                if (el.tagName === 'INPUT') formData.append(k, el.value);
                else formData.append(k, el.innerText.trim());
            }
        });

        fetch('ajax/save_service_details.php', {
            method: 'POST',
            body: formData
        }).then(r => r.json()).then(res => {
            if(res.success) {
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Saved!';
                setTimeout(() => btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Changes', 2000);
            } else {
                alert('Error saving data');
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Changes';
            }
        }).catch(err => {
            console.error(err);
            alert('Request failed');
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Changes';
        });
    });

    let slugEditedManually = false;
    const slugInput = document.getElementById('v-slug');
    if (slugInput) {
        slugInput.addEventListener('input', function() {
            slugEditedManually = true;
        });

        setInterval(() => {
            if (slugEditedManually) return;
            const titleEl = document.getElementById('v-sd_hero_title');
            if (titleEl) {
                const titleText = titleEl.innerText.trim();
                if (titleText && !titleText.includes('MODERN')) {
                    const generatedSlug = titleText.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
                    if (generatedSlug && slugInput.value === '') {
                        slugInput.value = generatedSlug;
                    }
                }
            }
        }, 1000);
    }
});
</script>

<?php include '../includes/footer.php'; ?>
