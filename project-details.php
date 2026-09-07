<?php 
require_once 'admin/config/db.php';
$currentPage = 'projects';
include 'includes/header.php'; 

// Fetch 3 recent projects for More Projects section
$more_projects_query = "
    SELECT p.*, c.icon as cat_icon 
    FROM projects p 
    LEFT JOIN categories c ON p.category COLLATE utf8mb4_unicode_ci = c.name COLLATE utf8mb4_unicode_ci
    ORDER BY p.created_at DESC 
    LIMIT 3
";
$more_projects_result = $conn->query($more_projects_query);

// Fetch Gallery Categories for dynamic filter buttons
$gallery_cat_query = "SELECT * FROM gallery_categories ORDER BY order_index ASC, name ASC";
$gallery_categories = $conn->query($gallery_cat_query);
?>

<main>
    <!-- Page Banner -->
    <section class="page-banner">
        <div class="container">
            <h1 class="banner-title">Projects</h1>
            <div class="breadcrumbs">
                <a href="index">Home</a> <span class="divider">/</span> <span class="current">Project Details</span>
            </div>
        </div>
    </section>

    <!-- Project Details Redesign -->
    <section class="project-details-redesign" style="padding: 60px 0 0px; background-color: var(--bg-white);">
        <div class="container" style="max-width: 1200px;">
            
            <!-- 1. Hero Split Section -->
            <div class="project-hero-split">
                <!-- Left: Image Slider -->
                <div class="hero-left-slider">
                    <div class="hero-main-img-wrapper">
                        <span class="hero-tag"><i class="fa-solid fa-house" style="margin-right: 5px;"></i> Residential Design</span>
                        <img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?w=1200&q=80" alt="Main Room" class="hero-main-img">
                    </div>
                </div>

                <!-- Right: Details Box -->
                <div class="hero-right-details">
                    <div class="hero-details-header">
                        <h2 class="project-title">MODERN 4 BHK<br>APARTMENT</h2>
                        <div class="project-actions">
                            <button class="icon-btn" onclick="shareProject()"><i class="fa-solid fa-share-nodes"></i></button>
                        </div>
                        <script>
                        function shareProject() {
                            if (navigator.share) {
                                navigator.share({
                                    title: document.title,
                                    url: window.location.href
                                }).catch(console.error);
                            } else {
                                navigator.clipboard.writeText(window.location.href);
                                alert('Link copied to clipboard!');
                            }
                        }
                        </script>
                    </div>
                    <p class="location-pin"><i class="fa-solid fa-location-dot" style="color: var(--accent-color); margin-right: 8px;"></i> Mumbai, India</p>
                    
                    <p class="short-desc">A perfect blend of modern aesthetics and functional luxury. This 4 BHK apartment is designed to reflect warmth, simplicity, and sophisticated living.</p>
                    
                    <div class="project-meta-list">
                        <div class="meta-row">
                            <span class="meta-icon"><i class="fa-solid fa-building-user"></i></span>
                            <span class="meta-key">Project Type</span>
                            <span class="meta-value">Residential</span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-icon"><i class="fa-solid fa-house-chimney"></i></span>
                            <span class="meta-key">Property Type</span>
                            <span class="meta-value">Apartment</span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-icon"><i class="fa-solid fa-expand"></i></span>
                            <span class="meta-key">Area</span>
                            <span class="meta-value">2,350 sq. ft.</span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-icon"><i class="fa-regular fa-calendar-check"></i></span>
                            <span class="meta-key">Year of Completion</span>
                            <span class="meta-value">2024</span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-icon"><i class="fa-solid fa-pen-ruler"></i></span>
                            <span class="meta-key">Design Style</span>
                            <span class="meta-value">Modern Minimal</span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-icon"><i class="fa-solid fa-list-check"></i></span>
                            <span class="meta-key">Scope of Work</span>
                            <span class="meta-value">Full Interior Design</span>
                        </div>
                    </div>

                    <div class="hero-cta-buttons">
                        <a href="contact.php" class="btn btn-primary" style="display: flex; align-items: center; justify-content: space-between; gap: 15px;">Get Estimate <span class="icon-circle" style="background: var(--text-dark); color: var(--accent-color); width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 12px;"></i></span></a>
                        <a href="#" class="btn btn-outline" style="border: 1px solid #ccc; background: white; color: var(--text-dark); padding: 12px 30px; border-radius: 30px; display: flex; align-items: center; gap: 10px; font-weight: 500; text-decoration: none;">Share Project <i class="fa-solid fa-share-nodes"></i></a>
                    </div>
                </div>
            </div>

            <!-- 2. Top Features Row -->
            <div class="feature-row-grid">
                <div class="feature-col">
                    <div class="feature-icon-circle"><i class="fa-solid fa-compass-drafting"></i></div>
                    <h4>Thoughtful Design</h4>
                    <p>Every space is planned with purpose and precision.</p>
                </div>
                <div class="feature-col">
                    <div class="feature-icon-circle"><i class="fa-solid fa-gem"></i></div>
                    <h4>Premium Materials</h4>
                    <p>We use high-quality finishes and durable materials.</p>
                </div>
                <div class="feature-col">
                    <div class="feature-icon-circle"><i class="fa-regular fa-clock"></i></div>
                    <h4>Timely Delivery</h4>
                    <p>On-time completion with attention to every detail.</p>
                </div>
                <div class="feature-col">
                    <div class="feature-icon-circle"><i class="fa-solid fa-handshake"></i></div>
                    <h4>Client Satisfaction</h4>
                    <p>Designs that reflect our client's lifestyle and vision.</p>
                </div>
            </div>

            <!-- 3. About The Project Section -->
            <div class="project-about-split">
                <div class="about-left">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                        <p class="section-subtitle" style="margin-bottom: 0;">ABOUT THE PROJECT</p>
                    </div>
                    <h2 class="section-title">Crafted for Comfort.<br><span class="accent-text signature-text" style="color: var(--accent-color); font-weight: 400; text-transform: none;">Designed for Living.</span></h2>
                    
                    <p style="color: #666; line-height: 1.8; margin-bottom: 20px;">This modern 4 BHK apartment is designed for a young family seeking a balance between style and functionality.</p>
                    <p style="color: #666; line-height: 1.8; margin-bottom: 20px;">The interiors feature a neutral palette, clean lines, and custom elements that create a calm and cohesive environment.</p>
                    <p style="color: #666; line-height: 1.8;">From the spacious living area to the cozy bedrooms, each space is crafted to enhance everyday living.</p>
                </div>
                <div class="about-right">
                    <div class="project-highlight-card">
                        <div class="highlight-item">
                            <div class="hi-icon"><i class="fa-solid fa-maximize"></i></div>
                            <div class="hi-text">
                                <h5>Spacious Layout</h5>
                                <p>Optimized floor plan for natural light and ventilation.</p>
                            </div>
                        </div>
                        <div class="highlight-item">
                            <div class="hi-icon"><i class="fa-solid fa-couch"></i></div>
                            <div class="hi-text">
                                <h5>Elegant Interiors</h5>
                                <p>Modern furniture, soft textures, and warm tones.</p>
                            </div>
                        </div>
                        <div class="highlight-item">
                            <div class="hi-icon"><i class="fa-solid fa-box-archive"></i></div>
                            <div class="hi-text">
                                <h5>Smart Storage</h5>
                                <p>Intelligent storage solutions for a clutter-free home.</p>
                            </div>
                        </div>
                        <div class="highlight-item">
                            <div class="hi-icon"><i class="fa-solid fa-palette"></i></div>
                            <div class="hi-text">
                                <h5>Personalized Touch</h5>
                                <p>Custom décor and design elements that reflect the client's personality.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Project Gallery -->
            <style>
                @media (max-width: 992px) {
                    .masonry-gallery-grid .item-large { grid-column: span 12 !important; height: 300px !important; }
                    .masonry-gallery-grid .item-medium { grid-column: span 12 !important; height: 300px !important; }
                    .masonry-gallery-grid .item-small { grid-column: span 6 !important; height: 200px !important; }
                }
                @media (max-width: 576px) {
                    .masonry-gallery-grid .item-small { grid-column: span 12 !important; height: 250px !important; }
                    .gallery-header { flex-direction: column; align-items: flex-start !important; }
                }
                .gallery-filter-btn { transition: all 0.3s ease; font-family: var(--font-primary); }
                .gallery-filter-btn:hover { background: var(--primary-color) !important; color: white !important; border-color: var(--primary-color) !important; }
            </style>
            
            <div class="project-gallery-section" style="margin-bottom: 60px;">
                <div class="gallery-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 30px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <h2 class="section-title" style="margin-bottom: 0; font-size: 2.5rem;">Gallery</h2>
                    </div>
                    
                    <div class="gallery-filters" style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button class="gallery-filter-btn active" style="background: var(--primary-color); color: white; border: 1px solid var(--primary-color); padding: 8px 20px; border-radius: 8px; font-size: 14px; cursor: pointer;"><i class="fa-solid fa-layer-group" style="margin-right: 5px;"></i> All</button>
                        <?php 
                        if ($gallery_categories && $gallery_categories->num_rows > 0): 
                            while($cat = $gallery_categories->fetch_assoc()):
                        ?>
                        <button class="gallery-filter-btn" style="background: white; color: var(--text-dark); border: 1px solid rgba(0,0,0,0.15); padding: 8px 20px; border-radius: 8px; font-size: 14px; cursor: pointer;">
                            <?php if(!empty($cat['icon'])): ?><i class="<?php echo htmlspecialchars($cat['icon']); ?>" style="margin-right: 5px;"></i><?php endif; ?> <?php echo htmlspecialchars($cat['name']); ?>
                        </button>
                        <?php 
                            endwhile;
                            $gallery_categories->data_seek(0);
                        endif; 
                        ?>
                    </div>
                </div>

                <div class="masonry-gallery-grid" style="display: grid; grid-template-columns: repeat(12, 1fr); gap: 15px;">
                    <!-- Top Left Image (spans 7 columns) -->
                    <div class="gallery-item item-large" style="grid-column: span 7; position: relative; border-radius: 12px; overflow: hidden; height: 450px;">
                        <img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?w=1200&q=80" alt="Living Room" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;">
                        <div class="play-button-overlay" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 70px; height: 70px; background: rgba(0,0,0,0.4); border: 2px solid var(--accent-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.3s ease;">
                            <i class="fa-solid fa-play" style="color: var(--accent-color); font-size: 24px; margin-left: 5px;"></i>
                        </div>
                    </div>
                    
                    <!-- Top Right Image (spans 5 columns) -->
                    <div class="gallery-item item-medium" style="grid-column: span 5; border-radius: 12px; overflow: hidden; height: 450px;">
                        <img src="https://images.unsplash.com/photo-1616594039964-ae9021a400a0?w=800&q=80" alt="Dining Room" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;">
                    </div>
                    
                    <!-- Bottom Row: 4 images (span 3 columns each) -->
                    <div class="gallery-item item-small" style="grid-column: span 3; border-radius: 12px; overflow: hidden; height: 260px;">
                        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=600&q=80" alt="Kitchen" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;">
                    </div>
                    <div class="gallery-item item-small" style="grid-column: span 3; border-radius: 12px; overflow: hidden; height: 260px;">
                        <img src="https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?w=600&q=80" alt="Bedroom" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;">
                    </div>
                    <div class="gallery-item item-small" style="grid-column: span 3; border-radius: 12px; overflow: hidden; height: 260px;">
                        <img src="https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=600&q=80" alt="Bathroom" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;">
                    </div>
                    <div class="gallery-item item-small" style="grid-column: span 3; border-radius: 12px; overflow: hidden; height: 260px;">
                        <img src="https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?w=600&q=80" alt="Balcony" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;">
                    </div>
                </div>
            </div>

            <!-- 5. Dark CTA Banner -->
            <div class="project-dark-cta">
                <div class="cta-content">
                    <div class="cta-icon-wrapper"><i class="fa-solid fa-pen-ruler"></i></div>
                    <div class="cta-text">
                        <h3>Have a project in mind?</h3>
                        <p>Let's create a space that's uniquely yours.</p>
                    </div>
                </div>
                <a href="calculator.php" class="btn btn-primary" style="display: flex; align-items: center; gap: 10px;">Get Estimate <span class="icon-circle" style="background: transparent; border: 1px solid rgba(0,0,0,0.3); color: var(--text-dark); width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px;"></i></span></a>
            </div>

            <!-- 6. Bottom Features Row -->
            <div class="feature-row-grid bottom-features" style="margin-bottom: 40px;">
                <div class="feature-col">
                    <div class="feature-icon-square"><i class="fa-solid fa-medal"></i></div>
                    <h4>10+ Years Experience</h4>
                    <p>Delivering excellence in interior design.</p>
                </div>
                <div class="feature-col">
                    <div class="feature-icon-square"><i class="fa-solid fa-check-double"></i></div>
                    <h4>100+ Projects Completed</h4>
                    <p>Successfully completed residential & commercial projects.</p>
                </div>
                <div class="feature-col">
                    <div class="feature-icon-square"><i class="fa-solid fa-gears"></i></div>
                    <h4>End-to-End Solutions</h4>
                    <p>From concept to completion, we handle it all.</p>
                </div>
                <div class="feature-col">
                    <div class="feature-icon-square"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                    <h4>Custom Design Approach</h4>
                    <p>Tailored designs that suit your lifestyle and needs.</p>
                </div>
            </div>

        </div>
    </section>

    <!-- More Projects Section -->
    <section class="more-projects-section" style="padding: 40px 0 20px; background-color: var(--bg-white);">
        <div class="container" style="max-width: 1200px;">
            <div class="more-projects-header">
                <div class="mp-header-left">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                        <p class="section-subtitle" style="margin-bottom: 0;">MORE PROJECTS</p>
                    </div>
                    <h2 class="section-title">Explore More <span class="accent-text signature-text" style="color: var(--accent-color); font-weight: 400; text-transform: none;">Inspiring Spaces</span></h2>
                </div>
                <div class="mp-header-right">
                    <a href="projects.php" class="btn btn-dark-pill">
                        <span class="icon-circle-yellow"><i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 14px;"></i></span> View All Projects
                    </a>
                </div>
            </div>

            <div class="more-projects-grid">
                <?php if($more_projects_result && $more_projects_result->num_rows > 0): ?>
                    <?php while($proj = $more_projects_result->fetch_assoc()): ?>
                    <!-- Card -->
                    <div class="mp-card">
                        <img src="<?php echo !empty($proj['cover_image']) ? htmlspecialchars($proj['cover_image']) : 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&q=80'; ?>" alt="<?php echo htmlspecialchars($proj['title']); ?>" class="mp-card-bg">
                        <div class="mp-card-top">
                            <div class="mp-tag">
                                <?php if(!empty($proj['cat_icon'])): ?><i class="<?php echo htmlspecialchars($proj['cat_icon']); ?>"></i> <?php endif; ?>
                                <?php echo htmlspecialchars($proj['category'] ?: 'Project'); ?>
                            </div>
                        </div>
                        <div class="mp-card-bottom">
                            <div class="mp-card-title-row">
                                <a href="project-details.php?slug=<?php echo !empty($proj['slug']) ? urlencode($proj['slug']) : $proj['id']; ?>" class="mp-link-btn"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                <div class="mp-title-col">
                                    <h3><?php echo htmlspecialchars(strtoupper($proj['title'] ?: 'Untitled')); ?></h3>
                                    <p><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($proj['location'] ?: 'N/A'); ?></p>
                                </div>
                            </div>
                            <div class="mp-tags-row">
                                <?php if(!empty($proj['property_type'])): ?>
                                    <span class="mp-pill"><?php echo htmlspecialchars($proj['property_type']); ?></span>
                                <?php endif; ?>
                                <?php if(!empty($proj['category'])): ?>
                                    <span class="mp-pill"><?php echo htmlspecialchars($proj['category']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="grid-column: 1 / -1; text-align: center; color: #888;">No projects found.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- Contact Form -->
    <?php include 'includes/components/contact.php'; ?>

</main>

<?php include 'includes/footer.php'; ?>
