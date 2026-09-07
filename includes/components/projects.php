<?php
// Fetch Categories
$comp_cat_query = "SELECT * FROM categories ORDER BY order_index ASC, name ASC";
$comp_categories = $conn->query($comp_cat_query);

// Fetch Projects (Limit 6 for home page)
$comp_proj_query = "SELECT * FROM projects WHERE status != 'Archived' ORDER BY created_at DESC LIMIT 6";
$comp_projects = $conn->query($comp_proj_query);
?>
<!-- Projects Section -->
<style>
    /* Projects Section Mobile Styles */
    .mobile-view-all { display: none; }
    .projects-filter-wrapper { position: relative; }
    .mobile-scroll-indicator { display: none; }
    @media (max-width: 992px) {
        .projects-grid { grid-template-columns: repeat(2, 1fr) !important; }
        .portfolio-features { flex-wrap: wrap; justify-content: center !important; gap: 30px; }
    }
    @media (max-width: 768px) {
        .projects-section { padding: 60px 0 !important; }
        .projects-header { flex-direction: column !important; align-items: flex-start !important; }
        .projects-header .section-title { font-size: 2.2rem !important; }
        .projects-header p { font-size: 1rem !important; margin-bottom: 20px; }
        .desktop-view-all { display: none !important; }
        .mobile-view-all { display: flex; justify-content: center; margin-top: 40px; }
        
        .filter-tags { 
            display: flex;
            flex-wrap: nowrap !important; 
            overflow-x: auto; 
            padding-bottom: 15px; 
            justify-content: flex-start !important;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none; /* Firefox */
        }
        .filter-tags::-webkit-scrollbar { display: none; /* Chrome/Safari */ }
        .filter-tag { white-space: nowrap; }
        
        .mobile-scroll-indicator {
            display: flex;
            position: absolute;
            right: 0;
            top: 0;
            height: 48px; /* Approx height of tag */
            width: 60px;
            background: linear-gradient(to right, rgba(246,246,246,0) 0%, rgba(246,246,246,1) 70%);
            align-items: center;
            justify-content: flex-end;
            padding-right: 5px;
            color: var(--text-muted);
            pointer-events: none;
            z-index: 5;
            transition: opacity 0.3s ease;
        }
        
        .projects-grid { grid-template-columns: 1fr !important; gap: 20px !important; margin-top: 30px !important; }
        
        .portfolio-features { flex-direction: column !important; padding: 30px 20px !important; gap: 25px !important; align-items: flex-start !important; }
        .portfolio-features > div[style*="width: 1px"] { display: none !important; }
        .pf-item { width: 100%; justify-content: flex-start; }
    }
    @keyframes pulse-horizontal-home {
        0% { transform: translateX(0); opacity: 0.5; }
        50% { transform: translateX(4px); opacity: 1; }
        100% { transform: translateX(0); opacity: 0.5; }
    }
</style>
<section class="projects-section" style="background-color: #F6F6F6; padding: 100px 0;">
    <div class="container" style="max-width: 1300px;">
        <div class="projects-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <p class="section-subtitle" style="justify-content: flex-start; margin-bottom: 20px;">OUR PROJECTS</p>
                <h2 class="section-title" style="font-size: 3.5rem; color: var(--text-dark);">Explore <span class="accent-text">Our Portfolio</span></h2>
                <p style="color: var(--text-muted); max-width: 500px; font-size: 1.1rem; line-height: 1.6; margin-top: 15px;">Discover a selection of our finest interior design projects crafted with creativity, functionality, and elegance.</p>
            </div>
            <a href="projects.php" class="btn hero-btn desktop-view-all" style="background: var(--text-dark); padding: 8px 30px 8px 8px; border-radius: 40px; align-self: center;">
                <span class="btn-icon" style="background: var(--accent-color); color: var(--text-dark); width: 40px; height: 40px;"><i class="fa-solid fa-arrow-right" style="transform: rotate(-45deg);"></i></span>
                <span class="btn-text" style="background: transparent; color: white; padding: 0 10px; font-weight: 500;">View All Projects</span>
            </a>
        </div>
        
        <div class="projects-filter-wrapper">
            <div class="filter-tags" id="home-category-filters" style="display: flex; justify-content: flex-start; gap: 15px; margin-bottom: 50px;">
                <span class="filter-tag active" style="background-color: #fcebdc; color: var(--text-dark); border: none; padding: 12px 25px;"><i class="fa-solid fa-border-all" style="margin-right: 8px;"></i> All</span>
                <?php if(isset($comp_categories) && $comp_categories->num_rows > 0): ?>
                    <?php 
                    $comp_categories->data_seek(0);
                    while($cat = $comp_categories->fetch_assoc()): 
                    ?>
                        <span class="filter-tag" style="background-color: white; border: 1px solid rgba(0,0,0,0.05); padding: 12px 25px; border-radius: 25px;">
                            <?php if(!empty($cat['icon'])): ?><i class="<?php echo htmlspecialchars($cat['icon']); ?>" style="margin-right: 8px;"></i><?php endif; ?>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </span>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
            <div class="mobile-scroll-indicator" id="home-scroll-indicator">
                <i class="fa-solid fa-chevron-right" style="animation: pulse-horizontal-home 1.5s infinite;"></i>
            </div>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const homeFilterContainer = document.getElementById('home-category-filters');
                const homeIndicator = document.getElementById('home-scroll-indicator');
                
                if(homeFilterContainer && homeIndicator) {
                    const updateHomeIndicator = () => {
                        const isAtEnd = homeFilterContainer.scrollLeft + homeFilterContainer.clientWidth >= homeFilterContainer.scrollWidth - 5;
                        const hasOverflow = homeFilterContainer.scrollWidth > homeFilterContainer.clientWidth;
                        
                        if(isAtEnd || !hasOverflow) {
                            homeIndicator.style.opacity = '0';
                            homeIndicator.style.pointerEvents = 'none';
                        } else {
                            homeIndicator.style.opacity = '1';
                            homeIndicator.style.pointerEvents = 'auto';
                        }
                    };
                    
                    homeFilterContainer.addEventListener('scroll', updateHomeIndicator);
                    window.addEventListener('resize', updateHomeIndicator);
                    
                    setTimeout(updateHomeIndicator, 100);
                }
            });
        </script>
        
        <div class="projects-grid" style="grid-template-columns: repeat(3, 1fr); gap: 30px; margin-top: 50px;">
            <?php if(isset($comp_projects) && $comp_projects->num_rows > 0): ?>
                <?php while($proj = $comp_projects->fetch_assoc()): ?>
                <div class="project-card new-design" style="position: relative;">
                    <a href="project-details.php?slug=<?php echo !empty($proj['slug']) ? urlencode($proj['slug']) : $proj['id']; ?>" style="display: block; overflow: hidden; height: 100%;">
                        <img src="<?php echo !empty($proj['cover_image']) ? htmlspecialchars($proj['cover_image']) : 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?w=800&q=80'; ?>" alt="<?php echo htmlspecialchars($proj['title']); ?>" style="transition: transform 0.5s ease; width: 100%; height: 100%; object-fit: cover;">
                    </a>
                    <div class="project-top-badges" style="position: absolute; top: 20px; left: 20px; right: 20px; display: flex; justify-content: space-between; align-items: center; z-index: 2;">
                        <span class="project-badge left" style="background: white; color: var(--text-dark); padding: 8px 15px; border-radius: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                            <?php
                            $cat_icon = 'fa-solid fa-house'; 
                            if(isset($comp_categories) && $comp_categories->num_rows > 0) {
                                $comp_categories->data_seek(0);
                                while($c = $comp_categories->fetch_assoc()) {
                                    if(strtolower($c['name']) == strtolower($proj['category'])) {
                                        $cat_icon = $c['icon'];
                                        break;
                                    }
                                }
                            }
                            ?>
                            <i class="<?php echo htmlspecialchars($cat_icon); ?>" style="color: #24352a;"></i> <?php echo htmlspecialchars($proj['category'] ?: 'Project'); ?>
                        </span>
                    </div>
                    <div class="project-bottom-content">
                        <div class="project-bottom-main">
                            <a href="project-details.php?slug=<?php echo !empty($proj['slug']) ? urlencode($proj['slug']) : $proj['id']; ?>" class="project-action-btn"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                            <div class="project-bottom-info">
                                <h3><a href="project-details.php?slug=<?php echo !empty($proj['slug']) ? urlencode($proj['slug']) : $proj['id']; ?>" style="color: inherit; text-decoration: none;"><?php echo htmlspecialchars($proj['title'] ?: 'Untitled Project'); ?></a></h3>
                                <p><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($proj['location'] ?: 'N/A'); ?></p>
                            </div>
                        </div>
                        <div class="project-bottom-tags">
                            <span class="p-tag"><?php echo htmlspecialchars($proj['property_type'] ?: 'N/A'); ?></span>
                            <span class="p-tag"><?php echo htmlspecialchars($proj['category'] ?: 'N/A'); ?></span>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                    <p style="color: var(--text-muted); font-size: 18px;">No projects found.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Mobile View All Button -->
        <div class="mobile-view-all">
            <a href="projects.php" class="btn hero-btn" style="background: var(--text-dark); padding: 8px 30px 8px 8px; border-radius: 40px;">
                <span class="btn-icon" style="background: var(--accent-color); color: var(--text-dark); width: 40px; height: 40px;"><i class="fa-solid fa-arrow-right" style="transform: rotate(-45deg);"></i></span>
                <span class="btn-text" style="background: transparent; color: white; padding: 0 10px; font-weight: 500;">View All Projects</span>
            </a>
        </div>
        <!-- Portfolio Footer Features -->
        <div class="portfolio-features" style="display: flex; justify-content: space-between; align-items: center; background-color: var(--text-light); padding: 35px 50px; border-radius: 20px; margin-top: 50px;">
            <div class="pf-item" style="display: flex; align-items: center; gap: 15px;">
                <div class="pf-icon" style="width: 50px; height: 50px; background-color: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-award"></i>
                </div>
                <div class="pf-text">
                    <strong style="display: block; font-size: 15px; color: var(--text-dark);">Quality & Craftsmanship</strong>
                    <span style="font-size: 13px; color: var(--text-muted);">Precision in every detail</span>
                </div>
            </div>
            
            <div style="width: 1px; height: 40px; background-color: rgba(0,0,0,0.1);"></div>
            
            <div class="pf-item" style="display: flex; align-items: center; gap: 15px;">
                <div class="pf-icon" style="width: 50px; height: 50px; background-color: var(--accent-color); color: var(--text-dark); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-regular fa-lightbulb"></i>
                </div>
                <div class="pf-text">
                    <strong style="display: block; font-size: 15px; color: var(--text-dark);">Innovative Design</strong>
                    <span style="font-size: 13px; color: var(--text-muted);">Creative solutions for every space</span>
                </div>
            </div>

            <div style="width: 1px; height: 40px; background-color: rgba(0,0,0,0.1);"></div>

            <div class="pf-item" style="display: flex; align-items: center; gap: 15px;">
                <div class="pf-icon" style="width: 50px; height: 50px; background-color: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-user-group"></i>
                </div>
                <div class="pf-text">
                    <strong style="display: block; font-size: 15px; color: var(--text-dark);">Client Focused</strong>
                    <span style="font-size: 13px; color: var(--text-muted);">Your vision, our priority</span>
                </div>
            </div>

            <div style="width: 1px; height: 40px; background-color: rgba(0,0,0,0.1);"></div>

            <div class="pf-item" style="display: flex; align-items: center; gap: 15px;">
                <div class="pf-icon" style="width: 50px; height: 50px; background-color: var(--accent-color); color: var(--text-dark); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <div class="pf-text">
                    <strong style="display: block; font-size: 15px; color: var(--text-dark);">On-Time Delivery</strong>
                    <span style="font-size: 13px; color: var(--text-muted);">Committed to timelines</span>
                </div>
            </div>
        </div>
    </div>
</section>

