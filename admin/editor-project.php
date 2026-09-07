<?php 
$currentPage = 'projects';
include '../includes/header.php'; 

// Fetch Gallery Categories for dynamic filter buttons
$gallery_cat_query = "SELECT * FROM gallery_categories ORDER BY order_index ASC, name ASC";
$gallery_categories = $conn->query($gallery_cat_query);

$project = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0) {
            $project = $res->fetch_assoc();
        }
        $stmt->close();
    }
}

// Variables for display
$title = $project['title'] ?? 'MODERN 4 BHK<br>APARTMENT';
$location = $project['location'] ?? 'Mumbai, India';
$short_desc = $project['short_desc'] ?? 'A perfect blend of modern aesthetics and functional luxury. This 4 BHK apartment is designed to reflect warmth, simplicity, and sophisticated living.';
$category = $project['category'] ?? 'Residential';
$property_type = $project['property_type'] ?? 'Apartment';
$area = $project['area'] ?? '2,350 sq. ft.';
$year = $project['year'] ?? '2024';
$style = $project['style'] ?? 'Modern Minimal';
$scope = $project['scope'] ?? 'Full Interior Design';
$about_title = $project['about_title'] ?? 'Crafted for Comfort.';
$about_subtitle = $project['about_subtitle'] ?? 'Designed for Living.';
$long_desc = $project['long_desc'] ?? '<p style="color: #666; line-height: 1.8; margin-bottom: 20px;">This modern 4 BHK apartment is designed for a young family seeking a balance between style and functionality.</p><p style="color: #666; line-height: 1.8; margin-bottom: 20px;">The interiors feature a neutral palette, clean lines, and custom elements that create a calm and cohesive environment.</p><p style="color: #666; line-height: 1.8;">From the spacious living area to the cozy bedrooms, each space is crafted to enhance everyday living.</p>';
$cover_image = !empty($project['cover_image']) ? '../' . $project['cover_image'] : 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?w=1200&q=80';
?>
<style>
    /* Force include frontend CSS since relative path in header fails in admin folder */
    @import url('../assets/css/style.css');

    /* Hide frontend navigation and footer */
    .navbar, .page-banner, footer, .main-footer, .footer-bottom, .cta-banner { display: none !important; }
    
    /* Live Editor Styles */
    [contenteditable="true"] {
        outline: 1px dashed transparent;
        transition: all 0.3s ease;
        padding: 2px;
        border-radius: 4px;
    }
    [contenteditable="true"]:hover {
        outline: 2px dashed var(--accent-color);
        background: rgba(234, 177, 54, 0.05);
        cursor: text;
    }
    [contenteditable="true"]:focus {
        outline: 2px solid var(--accent-color);
        background: #fff;
    }
    .editable-img {
        transition: opacity 0.3s, outline 0.3s;
    }
    .editable-img:hover {
        opacity: 0.7;
        cursor: pointer;
        outline: 3px dashed var(--accent-color);
        outline-offset: -3px;
    }
</style>

<main>
    <!-- Project Details Live Editor -->
    <section class="project-details-redesign" style="padding: 60px 0 0px; background-color: var(--bg-white);">
        <div class="container" style="max-width: 1200px;">
            
            <!-- 1. Hero Split Section -->
            <div class="project-hero-split">
                <!-- Left: Image Slider -->
                <div class="hero-left-slider">
                    <div class="hero-main-img-wrapper">
                        <span class="hero-tag"><i class="fa-solid fa-house" style="margin-right: 5px;"></i> <?php echo htmlspecialchars($category); ?></span>
                        <img src="<?php echo htmlspecialchars($cover_image); ?>" alt="Main Room" class="hero-main-img">
                    </div>
                </div>

                <!-- Right: Details Box -->
                <div class="hero-right-details">
                    <div class="hero-details-header">
                        <h2 class="project-title" contenteditable="true"><?php echo htmlspecialchars($title); ?></h2>
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
                    <p class="location-pin" contenteditable="true"><i class="fa-solid fa-location-dot" style="color: var(--accent-color); margin-right: 8px;"></i> <?php echo htmlspecialchars($location); ?></p>
                    
                    <p class="short-desc" contenteditable="true"><?php echo htmlspecialchars($short_desc); ?></p>
                    
                    <div class="project-meta-list">
                        <div class="meta-row">
                            <span class="meta-icon"><i class="fa-solid fa-building-user"></i></span>
                            <span class="meta-key">Project Type</span>
                            <span class="meta-value" contenteditable="true"><?php echo htmlspecialchars($category); ?></span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-icon"><i class="fa-solid fa-house-chimney"></i></span>
                            <span class="meta-key">Property Type</span>
                            <span class="meta-value" contenteditable="true"><?php echo htmlspecialchars($property_type); ?></span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-icon"><i class="fa-solid fa-expand"></i></span>
                            <span class="meta-key">Area</span>
                            <span class="meta-value" contenteditable="true"><?php echo htmlspecialchars($area); ?></span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-icon"><i class="fa-regular fa-calendar-check"></i></span>
                            <span class="meta-key">Year of Completion</span>
                            <span class="meta-value" contenteditable="true"><?php echo htmlspecialchars($year); ?></span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-icon"><i class="fa-solid fa-pen-ruler"></i></span>
                            <span class="meta-key">Design Style</span>
                            <span class="meta-value" contenteditable="true"><?php echo htmlspecialchars($style); ?></span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-icon"><i class="fa-solid fa-list-check"></i></span>
                            <span class="meta-key">Scope of Work</span>
                            <span class="meta-value" contenteditable="true"><?php echo htmlspecialchars($scope); ?></span>
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
                    <h2 class="section-title" contenteditable="true"><?php echo htmlspecialchars($about_title); ?><br><span class="accent-text signature-text" style="color: var(--accent-color); font-weight: 400; text-transform: none;"><?php echo htmlspecialchars($about_subtitle); ?></span></h2>
                    
                    <div class="long-desc-container" contenteditable="true">
                        <?php echo $long_desc; ?>
                    </div>
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
                
                /* Deletable Wrapper Styles */
                .deletable-wrapper { position: relative; border: 2px solid transparent; transition: all 0.3s; }
                .deletable-wrapper:hover { border: 2px dashed #ef4444; border-radius: 8px; }
                .delete-btn {
                    position: absolute; top: 10px; right: 10px;
                    background: #ef4444; color: white; border: none;
                    border-radius: 50%; width: 32px; height: 32px;
                    display: none; align-items: center; justify-content: center;
                    cursor: pointer; z-index: 100; box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                }
                .deletable-wrapper:hover .delete-btn { display: flex; }

                /* Image Edit Overlay */
                .image-edit-overlay {
                    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
                    background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;
                    opacity: 0; transition: opacity 0.3s ease; pointer-events: none; z-index: 10;
                }
                .gallery-item:hover .image-edit-overlay { opacity: 1; }
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
                    <div class="gallery-item item-large" data-category="Living Room" style="grid-column: span 7; position: relative; border-radius: 12px; overflow: hidden; height: 450px;">
                        <img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?w=1200&q=80" alt="Living Room" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;">
                        <div class="image-edit-overlay"><div style="width:50px;height:50px;background:var(--accent-color);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:20px;"><i class="fa-solid fa-camera"></i></div></div>
                    </div>
                    
                    <!-- Top Right Image (spans 5 columns) -->
                    <div class="gallery-item item-medium" data-category="Dining" style="grid-column: span 5; position: relative; border-radius: 12px; overflow: hidden; height: 450px;">
                        <img src="https://images.unsplash.com/photo-1616594039964-ae9021a400a0?w=800&q=80" alt="Dining Room" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;">
                        <div class="image-edit-overlay"><div style="width:50px;height:50px;background:var(--accent-color);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:20px;"><i class="fa-solid fa-camera"></i></div></div>
                    </div>
                    
                    <!-- Bottom Row: 4 images (span 3 columns each) -->
                    <div class="gallery-item item-small" data-category="Kitchen" style="grid-column: span 3; position: relative; border-radius: 12px; overflow: hidden; height: 260px;">
                        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=600&q=80" alt="Kitchen" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;">
                        <div class="image-edit-overlay"><div style="width:50px;height:50px;background:var(--accent-color);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:20px;"><i class="fa-solid fa-camera"></i></div></div>
                    </div>
                    <div class="gallery-item item-small" data-category="Bedroom" style="grid-column: span 3; position: relative; border-radius: 12px; overflow: hidden; height: 260px;">
                        <img src="https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?w=600&q=80" alt="Bedroom" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;">
                        <div class="image-edit-overlay"><div style="width:50px;height:50px;background:var(--accent-color);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:20px;"><i class="fa-solid fa-camera"></i></div></div>
                    </div>
                    <div class="gallery-item item-small" data-category="Bathroom" style="grid-column: span 3; position: relative; border-radius: 12px; overflow: hidden; height: 260px;">
                        <img src="https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=600&q=80" alt="Bathroom" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;">
                        <div class="image-edit-overlay"><div style="width:50px;height:50px;background:var(--accent-color);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:20px;"><i class="fa-solid fa-camera"></i></div></div>
                    </div>
                    <div class="gallery-item item-small" data-category="Other Spaces" style="grid-column: span 3; position: relative; border-radius: 12px; overflow: hidden; height: 260px;">
                        <img src="https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?w=600&q=80" alt="Balcony" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;">
                        <div class="image-edit-overlay"><div style="width:50px;height:50px;background:var(--accent-color);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:20px;"><i class="fa-solid fa-camera"></i></div></div>
                    </div>
                    
                    <!-- Add Image Card -->
                    <div id="add-image-card" class="item-small" style="grid-column: span 3; border-radius: 12px; height: 260px; border: 2px dashed rgba(0,0,0,0.2); display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; color: #888; background: #f8f9fa; transition: all 0.3s ease;">
                        <i class="fa-solid fa-plus" style="font-size: 32px; margin-bottom: 10px; color: var(--accent-color);"></i>
                        <span style="font-weight: 500;">Add Image</span>
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


</main>

<!-- Hidden inputs for file selecting -->
<input type="file" id="local-gallery-adder" accept="image/*" multiple style="display:none">
<input type="file" id="local-gallery-replacer" accept="image/*" style="display:none">

<script>
    // Initialize Live Editor
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Make text editable
        const textSelectors = [
            '.project-title', '.location-pin', '.short-desc', '.meta-value', 
            '.feature-col h4', '.feature-col p', '.section-title', '.long-desc-container', 
            '.hi-text h5', '.hi-text p', '.hero-tag', '.section-subtitle'
        ];
        
        textSelectors.forEach(selector => {
            document.querySelectorAll(selector).forEach(el => {
                el.contentEditable = true;
                // Prevent links from navigating
                if(el.tagName === 'A') {
                    el.addEventListener('click', e => e.preventDefault());
                }
            });
        });

        // 2. Setup Deletable Sections
        const deletableSelectors = ['.feature-col', '.highlight-item', '.gallery-item'];
        
        function makeDeletable(el) {
            el.classList.add('deletable-wrapper');
            const btn = document.createElement('button');
            btn.className = 'delete-btn';
            btn.innerHTML = '<i class="fa-solid fa-trash"></i>';
            btn.title = 'Delete this section';
            btn.onclick = function(e) {
                e.stopPropagation();
                el.remove();
            };
            el.appendChild(btn);
        }

        deletableSelectors.forEach(selector => {
            document.querySelectorAll(selector).forEach(makeDeletable);
        });

        // 3. Gallery Image Swapping
        let currentTargetImg = null;
        const replacerInput = document.getElementById('local-gallery-replacer');
        
        document.querySelector('.masonry-gallery-grid').addEventListener('click', (e) => {
            if (e.target.tagName === 'IMG') {
                currentTargetImg = e.target;
                replacerInput.click();
            }
        });

        replacerInput.addEventListener('change', function() {
            if (this.files && this.files[0] && currentTargetImg) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    currentTargetImg.src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // 4. Gallery Image Adding
        const adderInput = document.getElementById('local-gallery-adder');
        const addCard = document.getElementById('add-image-card');
        
        addCard.addEventListener('mouseover', () => { addCard.style.background = 'rgba(234, 177, 54, 0.1)'; addCard.style.borderColor = 'var(--accent-color)'; });
        addCard.addEventListener('mouseout', () => { addCard.style.background = '#f8f9fa'; addCard.style.borderColor = 'rgba(0,0,0,0.2)'; });
        
        addCard.addEventListener('click', (e) => {
            e.preventDefault();
            adderInput.click();
        });

        adderInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                const grid = document.querySelector('.masonry-gallery-grid');
                
                // Determine category from active filter button
                let activeCat = 'Living Room'; // default
                const activeBtn = document.querySelector('.gallery-filter-btn.active');
                if (activeBtn) {
                    const text = activeBtn.innerText.trim();
                    if (text && text !== 'All') activeCat = text;
                }
                
                Array.from(this.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'gallery-item item-small deletable-wrapper';
                        div.dataset.category = activeCat;
                        div.style.gridColumn = 'span 3';
                        div.style.borderRadius = '12px';
                        div.style.overflow = 'hidden';
                        div.style.height = '260px';
                        div.style.position = 'relative'; // Required for overlay
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover';
                        img.style.display = 'block';
                        img.style.transition = 'transform 0.5s ease';
                        
                        // Edit overlay
                        const overlay = document.createElement('div');
                        overlay.className = 'image-edit-overlay';
                        overlay.innerHTML = '<div style="width:50px;height:50px;background:var(--accent-color);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:20px;"><i class="fa-solid fa-camera"></i></div>';
                        
                        div.appendChild(img);
                        div.appendChild(overlay);
                        
                        // Add delete button
                        makeDeletable(div);
                        
                        // Insert right before the Add Image card
                        grid.insertBefore(div, document.getElementById('add-image-card'));
                    }
                    reader.readAsDataURL(file);
                });
            }
        });

        // 5. Gallery Filter Button Logic
        const filterBtns = document.querySelectorAll('.gallery-filter-btn');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active class and reset styles
                filterBtns.forEach(b => {
                    b.classList.remove('active');
                    b.style.background = 'white';
                    b.style.color = 'var(--text-dark)';
                });
                // Set active style
                btn.classList.add('active');
                btn.style.background = 'var(--primary-color)';
                btn.style.color = 'white';
                
                const filter = btn.innerText.trim();
                const items = document.querySelectorAll('.gallery-item');
                
                items.forEach(item => {
                    if (filter === 'All' || item.dataset.category === filter) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

    });
</script>

<?php include '../includes/footer.php'; ?>
