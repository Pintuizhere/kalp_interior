<!-- Services Section -->
<style>
    .mobile-svc-view-all { display: none; }
    @media (max-width: 992px) {
        .new-services-grid { grid-template-columns: repeat(2, 1fr) !important; }
        .portfolio-features { flex-wrap: wrap; justify-content: center !important; gap: 30px; }
    }
    @media (max-width: 768px) {
        .services-section { padding: 60px 0 !important; }
        .services-header { flex-direction: column !important; align-items: flex-start !important; margin-bottom: 30px !important; }
        .services-header .section-title { font-size: 2.2rem !important; }
        .services-header p { font-size: 1rem !important; margin-bottom: 20px; }
        .desktop-svc-view-all { display: none !important; }
        .mobile-svc-view-all { display: flex; justify-content: center; margin-top: 20px; margin-bottom: 40px; }
        
        .new-services-grid { grid-template-columns: 1fr !important; gap: 20px !important; margin-bottom: 0 !important; }
        
        .portfolio-features { flex-direction: column !important; padding: 30px 20px !important; gap: 25px !important; align-items: flex-start !important; }
        .portfolio-features > div[style*="width: 1px"] { display: none !important; }
        .pf-item { width: 100%; justify-content: flex-start; }
    }
</style>
<section class="services-section new-services-design" style="background-color: #F6F6F6; padding: 100px 0;">
    <div class="container" style="max-width: 1300px;">
        <div class="services-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 50px;">
            <div>
                <p class="section-subtitle" style="justify-content: flex-start; margin-bottom: 20px;">OUR SERVICES</p>
                <h2 class="section-title" style="font-size: 3.5rem; color: var(--text-dark);">Explore <span class="accent-text">Our Services</span><br>Your Path to Success</h2>
                <p style="color: var(--text-muted); max-width: 500px; font-size: 1.1rem; line-height: 1.6; margin-top: 15px;">Innovative design solutions crafted to bring your vision to life and create lasting impact.</p>
            </div>
            <a href="services.php" class="btn hero-btn desktop-svc-view-all" style="background: var(--text-dark); padding: 8px 30px 8px 8px; border-radius: 40px; align-self: center;">
                <span class="btn-icon" style="background: var(--accent-color); color: var(--text-dark); width: 40px; height: 40px;"><i class="fa-solid fa-arrow-right" style="transform: rotate(-45deg);"></i></span>
                <span class="btn-text" style="background: transparent; color: white; padding: 0 10px; font-weight: 500;">View All Services</span>
            </a>
        </div>
        
        <div class="new-services-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 50px;">
            
            <?php
            require_once 'admin/config/db.php';
            $services = $conn->query("SELECT * FROM services WHERE status = 'Active' ORDER BY display_order ASC, id ASC LIMIT 4");
            if ($services && $services->num_rows > 0):
                $count = 1;
                while($srv = $services->fetch_assoc()):
                    $srv_img = (strpos($srv['cover_image'], 'http') === 0) ? $srv['cover_image'] : $srv['cover_image'];
                    $num = str_pad($count, 2, '0', STR_PAD_LEFT);
            ?>
            <!-- Service Card -->
            <div class="hp-service-card">
                <a href="service-details.php?slug=<?php echo !empty($srv['slug']) ? urlencode($srv['slug']) : $srv['id']; ?>" style="display: block; text-decoration: none; color: inherit;">
                    <div class="hp-sc-image">
                        <img src="<?php echo htmlspecialchars($srv_img); ?>" alt="<?php echo htmlspecialchars($srv['name']); ?>">
                        <div class="hp-sc-icon"><i class="<?php echo htmlspecialchars($srv['icon']); ?>"></i></div>
                    </div>
                </a>
                <div class="hp-sc-content">
                    <div class="hp-sc-number"><?php echo $num; ?></div>
                    <a href="service-details.php?slug=<?php echo !empty($srv['slug']) ? urlencode($srv['slug']) : $srv['id']; ?>" style="text-decoration: none; color: inherit;">
                        <h3><?php echo htmlspecialchars(strtoupper($srv['name'])); ?></h3>
                    </a>
                    <p><?php echo htmlspecialchars($srv['short_desc']); ?></p>
                    <a href="service-details.php?slug=<?php echo !empty($srv['slug']) ? urlencode($srv['slug']) : $srv['id']; ?>" class="hp-sc-link">Explore Service <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
            <?php 
                $count++;
                endwhile;
            else:
            ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: #f9f9f9; border-radius: 10px;">
                <p style="color: #666;">No services available at the moment.</p>
            </div>
            <?php endif; ?>

        </div>

        <!-- Mobile View All Services Button -->
        <div class="mobile-svc-view-all">
            <a href="services.php" class="btn hero-btn" style="background: var(--text-dark); padding: 8px 30px 8px 8px; border-radius: 40px;">
                <span class="btn-icon" style="background: var(--accent-color); color: var(--text-dark); width: 40px; height: 40px;"><i class="fa-solid fa-arrow-right" style="transform: rotate(-45deg);"></i></span>
                <span class="btn-text" style="background: transparent; color: white; padding: 0 10px; font-weight: 500;">View All Services</span>
            </a>
        </div>

        <!-- Services Footer Features -->
        <div class="portfolio-features" style="display: flex; justify-content: space-between; align-items: center; background-color: white; padding: 35px 50px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03);">
            <div class="pf-item" style="display: flex; align-items: center; gap: 15px;">
                <div class="pf-icon" style="width: 50px; height: 50px; background-color: rgba(234, 177, 54, 0.15); color: var(--accent-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
                <div class="pf-text">
                    <strong style="display: block; font-size: 15px; color: var(--text-dark);">Client Focused</strong>
                    <span style="font-size: 13px; color: var(--text-muted);">Your vision, our priority.</span>
                </div>
            </div>
            
            <div style="width: 1px; height: 40px; background-color: rgba(0,0,0,0.1);"></div>
            
            <div class="pf-item" style="display: flex; align-items: center; gap: 15px;">
                <div class="pf-icon" style="width: 50px; height: 50px; background-color: rgba(234, 177, 54, 0.15); color: var(--accent-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-regular fa-lightbulb"></i>
                </div>
                <div class="pf-text">
                    <strong style="display: block; font-size: 15px; color: var(--text-dark);">Innovative Solutions</strong>
                    <span style="font-size: 13px; color: var(--text-muted);">Creative ideas for unique spaces.</span>
                </div>
            </div>

            <div style="width: 1px; height: 40px; background-color: rgba(0,0,0,0.1);"></div>

            <div class="pf-item" style="display: flex; align-items: center; gap: 15px;">
                <div class="pf-icon" style="width: 50px; height: 50px; background-color: rgba(234, 177, 54, 0.15); color: var(--accent-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-award"></i>
                </div>
                <div class="pf-text">
                    <strong style="display: block; font-size: 15px; color: var(--text-dark);">Quality & Excellence</strong>
                    <span style="font-size: 13px; color: var(--text-muted);">Commitment to highest standards.</span>
                </div>
            </div>

            <div style="width: 1px; height: 40px; background-color: rgba(0,0,0,0.1);"></div>

            <div class="pf-item" style="display: flex; align-items: center; gap: 15px;">
                <div class="pf-icon" style="width: 50px; height: 50px; background-color: rgba(234, 177, 54, 0.15); color: var(--accent-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <div class="pf-text">
                    <strong style="display: block; font-size: 15px; color: var(--text-dark);">On-Time Delivery</strong>
                    <span style="font-size: 13px; color: var(--text-muted);">Reliable service, every time.</span>
                </div>
            </div>
        </div>

    </div>
</section>

