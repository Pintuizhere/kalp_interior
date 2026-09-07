<?php 
$currentPage = 'services';
require_once 'admin/config/db.php';

// Validate input
if (isset($_GET['slug'])) {
    $slug = $conn->real_escape_string($_GET['slug']);
    $res = $conn->query("SELECT * FROM services WHERE slug = '$slug' AND status = 'Active'");
} else if (isset($_GET['id'])) {
    $service_id = (int)$_GET['id'];
    $res = $conn->query("SELECT * FROM services WHERE id = $service_id AND status = 'Active'");
} else {
    header("Location: services.php");
    exit();
}

if (!$res || $res->num_rows === 0) {
    header("Location: services.php");
    exit();
}
$service = $res->fetch_assoc();
$service_id = $service['id']; // Ensure $service_id is set for page_content query

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
$sd_why_img = !empty($service_content['sd_why_img']) ? $service_content['sd_why_img'] : 'uploads/media/3d_rendering_cover.png';

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

include 'includes/header.php'; 
?>

<?php $b_url = (strpos($sd_banner_img, 'http') === 0) ? $sd_banner_img : $sd_banner_img; ?>
<main class="sd-page" style="background-color: var(--bg-white);">
    
    <!-- Page Banner -->
    <section class="page-banner" style="background-image: linear-gradient(rgba(26, 38, 30, 0.85), rgba(26, 38, 30, 0.85)), url('<?php echo $b_url; ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="container">
            <h1 class="banner-title">SERVICES</h1>
            <div class="breadcrumbs">
                <a href="index">Home</a> <span class="divider">/</span> <a href="services.php">Services</a> <span class="divider">/</span> <span class="current"><?php echo htmlspecialchars($service['name']); ?></span>
            </div>
        </div>
    </section>

    <!-- 1. Hero Section -->
    <section class="sd-redesign-hero" style="padding: 60px 0; background-color: var(--bg-white);">
        <div class="container" style="max-width: 1200px;">
            
            <div class="sd-hero-split">
                <div class="sd-hero-text">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                        <p class="section-subtitle" style="margin-bottom: 0;">OUR SERVICE</p>
                    </div>
                    <h1 class="sd-hero-title"><?php echo $sd_hero_title; ?></h1>
                    <h2 class="sd-hero-signature signature-text"><?php echo $sd_hero_signature; ?></h2>
                    
                    <p class="sd-hero-desc">
                        <?php echo $sd_hero_desc; ?>
                    </p>
                    
                    <div class="sd-hero-buttons">
                        <a href="calculator.php" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 10px;">Get Estimate <span class="icon-circle" style="background: var(--text-dark); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px;"></i></span></a>
                        
                        <a href="projects.php" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 10px; border: 1px solid #ccc; border-radius: 30px; padding: 12px 25px; color: var(--text-dark); text-decoration: none;">View Our Projects <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 12px;"></i></a>
                    </div>
                </div>
                <div class="sd-hero-img-box">
                    <img src="<?php echo $sd_hero_img; ?>" alt="<?php echo htmlspecialchars(strip_tags($sd_hero_title)); ?>">
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
                    <h4><?php echo $sd_f1_title; ?></h4>
                    <p><?php echo $sd_f1_desc; ?></p>
                </div>
                <div class="sd-f-col">
                    <div class="sd-f-icon"><i class="fa-solid fa-layer-group"></i></div>
                    <h4><?php echo $sd_f2_title; ?></h4>
                    <p><?php echo $sd_f2_desc; ?></p>
                </div>
                <div class="sd-f-col">
                    <div class="sd-f-icon"><i class="fa-solid fa-couch"></i></div>
                    <h4><?php echo $sd_f3_title; ?></h4>
                    <p><?php echo $sd_f3_desc; ?></p>
                </div>
                <div class="sd-f-col">
                    <div class="sd-f-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                    <h4><?php echo $sd_f4_title; ?></h4>
                    <p><?php echo $sd_f4_desc; ?></p>
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
                    <h2 class="sd-why-title"><?php echo $sd_why_title; ?><br><span class="signature-text" style="color: var(--accent-color); font-weight: 400; text-transform: none;"><?php echo $sd_why_signature; ?></span></h2>
                    
                    <p class="sd-why-desc">
                        <?php echo $sd_why_desc; ?>
                    </p>
                    
                    <div class="sd-why-img">
                        <img src="<?php echo $sd_why_img; ?>" alt="Why Choose Us">
                    </div>
                </div>
                <div class="sd-why-right">
                    <div class="why-list-item">
                        <div class="why-check"><i class="fa-solid fa-check"></i></div>
                        <div class="why-text">
                            <h4><?php echo $sd_why_l1_title; ?></h4>
                            <p><?php echo $sd_why_l1_desc; ?></p>
                        </div>
                    </div>
                    <div class="why-list-item">
                        <div class="why-check"><i class="fa-solid fa-check"></i></div>
                        <div class="why-text">
                            <h4><?php echo $sd_why_l2_title; ?></h4>
                            <p><?php echo $sd_why_l2_desc; ?></p>
                        </div>
                    </div>
                    <div class="why-list-item">
                        <div class="why-check"><i class="fa-solid fa-check"></i></div>
                        <div class="why-text">
                            <h4><?php echo $sd_why_l3_title; ?></h4>
                            <p><?php echo $sd_why_l3_desc; ?></p>
                        </div>
                    </div>
                    <div class="why-list-item">
                        <div class="why-check"><i class="fa-solid fa-check"></i></div>
                        <div class="why-text">
                            <h4><?php echo $sd_why_l4_title; ?></h4>
                            <p><?php echo $sd_why_l4_desc; ?></p>
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
            <h2 class="sd-process-title"><?php echo $sd_process_title; ?> <span class="signature-text" style="color: var(--accent-color); font-weight: 400; text-transform: none;"><?php echo $sd_process_signature; ?></span></h2>
            
            <div class="timeline-horizontal">
                <!-- Step 1 -->
                <div class="timeline-step">
                    <div class="tl-icon-box">
                        <i class="fa-solid fa-clipboard-list"></i>
                        <span class="tl-number">1</span>
                    </div>
                    <h4><?php echo $sd_p1_title; ?></h4>
                    <p><?php echo $sd_p1_desc; ?></p>
                </div>
                
                <div class="tl-arrow"><i class="fa-solid fa-arrow-right-long" style="color: #df916b; opacity: 0.5;"></i></div>
                
                <!-- Step 2 -->
                <div class="timeline-step">
                    <div class="tl-icon-box">
                        <i class="fa-solid fa-compass-drafting"></i>
                        <span class="tl-number">2</span>
                    </div>
                    <h4><?php echo $sd_p2_title; ?></h4>
                    <p><?php echo $sd_p2_desc; ?></p>
                </div>
                
                <div class="tl-arrow"><i class="fa-solid fa-arrow-right-long" style="color: #df916b; opacity: 0.5;"></i></div>
                
                <!-- Step 3 -->
                <div class="timeline-step">
                    <div class="tl-icon-box">
                        <i class="fa-solid fa-pen-nib"></i>
                        <span class="tl-number">3</span>
                    </div>
                    <h4><?php echo $sd_p3_title; ?></h4>
                    <p><?php echo $sd_p3_desc; ?></p>
                </div>
                
                <div class="tl-arrow"><i class="fa-solid fa-arrow-right-long" style="color: #df916b; opacity: 0.5;"></i></div>
                
                <!-- Step 4 -->
                <div class="timeline-step">
                    <div class="tl-icon-box">
                        <i class="fa-solid fa-hammer"></i>
                        <span class="tl-number">4</span>
                    </div>
                    <h4><?php echo $sd_p4_title; ?></h4>
                    <p><?php echo $sd_p4_desc; ?></p>
                </div>
                
                <div class="tl-arrow"><i class="fa-solid fa-arrow-right-long" style="color: #df916b; opacity: 0.5;"></i></div>
                
                <!-- Step 5 -->
                <div class="timeline-step">
                    <div class="tl-icon-box">
                        <i class="fa-solid fa-star"></i>
                        <span class="tl-number">5</span>
                    </div>
                    <h4><?php echo $sd_p5_title; ?></h4>
                    <p><?php echo $sd_p5_desc; ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. More Interior Design Projects -->
    <section class="more-projects-section" style="padding: 80px 0 20px; background-color: var(--bg-white);">
        <div class="container" style="max-width: 1200px;">
            <div class="more-projects-header">
                <div class="mp-header-left">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                        <p class="section-subtitle" style="margin-bottom: 0;">EXPLORE OUR WORK</p>
                    </div>
                    <h2 class="section-title">More Interior Design <span class="accent-text signature-text" style="font-family: var(--font-accent); color: var(--accent-color); font-weight: 400; text-transform: none;">Projects</span></h2>
                </div>
                <div class="mp-header-right">
                    <a href="projects.php" class="btn btn-dark-pill">
                        <span class="icon-circle-yellow"><i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 14px;"></i></span> View All Projects
                    </a>
                </div>
            </div>

            <div class="more-projects-grid sd-three-cols">
                <?php
                if (!isset($conn)) {
                    require_once __DIR__ . '/admin/config/db.php';
                }
                $proj_res = $conn->query("SELECT id, title, location, category, cover_image, area FROM projects ORDER BY id DESC LIMIT 3");
                
                $fallback_images = [
                    "uploads/media/after_1785707371_620.webp",
                    "uploads/media/kitchen_design_cover.png",
                    "uploads/media/3d_rendering_cover.png"
                ];
                $img_idx = 0;

                if ($proj_res && $proj_res->num_rows > 0) {
                    while($p = $proj_res->fetch_assoc()) {
                        // Use DB cover image if available, otherwise use a fallback
                        if (!empty($p['cover_image'])) {
                            $p_img = 'uploads/projects/' . htmlspecialchars($p['cover_image']);
                        } else {
                            $p_img = $fallback_images[$img_idx % 3];
                        }
                        
                        $p_title = htmlspecialchars($p['title']);
                        $p_location = !empty($p['location']) ? htmlspecialchars($p['location']) : 'India';
                        $p_cat = !empty($p['category']) ? htmlspecialchars($p['category']) : 'Design';
                        $p_area = !empty($p['area']) ? htmlspecialchars($p['area']) : $p_cat;
                        $p_id = $p['id'];
                        $img_idx++;
                ?>
                <div class="mp-card">
                    <img src="<?php echo $p_img; ?>" alt="<?php echo $p_title; ?>" class="mp-card-bg">
                    <div class="mp-card-top">
                        <div class="mp-tag"><?php echo $p_cat; ?></div>
                    </div>
                    <div class="mp-card-bottom">
                        <div class="mp-card-title-row" style="margin-bottom: 10px;">
                            <a href="project-details.php?slug=<?php echo !empty($proj['slug']) ? urlencode($proj['slug']) : $p_id; ?>" class="mp-link-btn" style="width: 35px; height: 35px;"><i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 12px;"></i></a>
                            <div class="mp-title-col">
                                <h3 style="font-size: 14px;"><?php echo strtoupper($p_title); ?></h3>
                                <p style="font-size: 11px;"><i class="fa-solid fa-location-dot"></i> <?php echo $p_location; ?></p>
                            </div>
                        </div>
                        <div class="mp-tags-row">
                            <span class="mp-pill" style="font-size: 10px; padding: 4px 10px;"><?php echo $p_area; ?></span>
                            <span class="mp-pill" style="font-size: 10px; padding: 4px 10px;"><?php echo $p_cat; ?></span>
                        </div>
                    </div>
                </div>
                <?php 
                    }
                } else {
                    echo "<p>No projects found.</p>";
                }
                ?>
            </div>
        </div>
    </section>

    <!-- 6. Dark CTA Banner -->
    <div class="container" style="max-width: 1200px; padding-bottom: 60px;">
        <div class="project-dark-cta" style="margin-top: 20px;">
            <div class="cta-content">
                <div class="cta-icon-wrapper"><i class="fa-solid fa-pen-ruler"></i></div>
                <div class="cta-text">
                    <h3>Have a project in mind?</h3>
                    <p>Let's create a space that's uniquely yours.</p>
                </div>
            </div>
            <a href="calculator.php" class="btn btn-primary" style="display: flex; align-items: center; gap: 10px;">Get Estimate <span class="icon-circle" style="background: transparent; border: 1px solid rgba(0,0,0,0.3); color: var(--text-dark); width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px;"></i></span></a>
        </div>
    </div>

    <section class="services-features" style="background-color: var(--bg-light); padding: 80px 0;">
        <div class="container" style="max-width: 1300px;">
            <!-- Features block -->
            <div class="portfolio-features new-features-bar" style="display: flex; justify-content: space-between; align-items: center; padding: 30px 40px; background-color: white; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.03);">
                <div class="pf-item" style="display: flex; align-items: center; gap: 15px;">
                    <div class="pf-icon" style="width: 50px; height: 50px; background-color: #fcf1db; color: #EAB136; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <div class="pf-text">
                        <strong style="display: block; font-size: 16px; color: var(--text-dark); margin-bottom: 2px;">Client Focused</strong>
                        <span style="font-size: 13px; color: var(--text-muted);">Your vision, our priority.</span>
                    </div>
                </div>
                
                <div style="width: 1px; height: 40px; background-color: rgba(0,0,0,0.05);"></div>
                
                <div class="pf-item" style="display: flex; align-items: center; gap: 15px;">
                    <div class="pf-icon" style="width: 50px; height: 50px; background-color: #fcf1db; color: #EAB136; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                        <i class="fa-regular fa-lightbulb"></i>
                    </div>
                    <div class="pf-text">
                        <strong style="display: block; font-size: 16px; color: var(--text-dark); margin-bottom: 2px;">Innovative Solutions</strong>
                        <span style="font-size: 13px; color: var(--text-muted);">Creative ideas for unique spaces.</span>
                    </div>
                </div>

                <div style="width: 1px; height: 40px; background-color: rgba(0,0,0,0.05);"></div>

                <div class="pf-item" style="display: flex; align-items: center; gap: 15px;">
                    <div class="pf-icon" style="width: 50px; height: 50px; background-color: #fcf1db; color: #EAB136; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <div class="pf-text">
                        <strong style="display: block; font-size: 16px; color: var(--text-dark); margin-bottom: 2px;">Quality & Excellence</strong>
                        <span style="font-size: 13px; color: var(--text-muted);">Commitment to highest standards.</span>
                    </div>
                </div>

                <div style="width: 1px; height: 40px; background-color: rgba(0,0,0,0.05);"></div>

                <div class="pf-item" style="display: flex; align-items: center; gap: 15px;">
                    <div class="pf-icon" style="width: 50px; height: 50px; background-color: #fcf1db; color: #EAB136; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                        <i class="fa-regular fa-clock"></i>
                    </div>
                    <div class="pf-text">
                        <strong style="display: block; font-size: 16px; color: var(--text-dark); margin-bottom: 2px;">On-Time Delivery</strong>
                        <span style="font-size: 13px; color: var(--text-muted);">Reliable service, every time.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>
