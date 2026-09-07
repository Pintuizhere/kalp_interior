<?php 
$currentPage = 'services';
include 'includes/header.php'; 
?>

<main>
    <!-- Page Banner -->
    <section class="page-banner">
        <div class="container">
            <h1 class="banner-title">Services</h1>
            <div class="breadcrumbs">
                <a href="index">Home</a> <span class="divider">/</span> <span class="current">Services</span>
            </div>
        </div>
    </section>

    <!-- Services Grid Section -->
    <section class="services-page-section" style="padding: 80px 0; background-color: var(--bg-white);">
        <div class="container">
            <div class="text-center" style="margin-bottom: 50px; text-align: center;">
                <p class="section-subtitle" style="justify-content: center;">OUR SERVICES</p>
                <h2 class="section-title">Explore Our Services:<br><span class="accent-text" style="font-family: var(--font-accent); font-style: italic; font-weight: 400;">Your Path to Success</span></h2>
            </div>
            <div class="new-services-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 50px;">
                
                <?php
                require_once 'admin/config/db.php';
                $services = $conn->query("SELECT * FROM services WHERE status = 'Active' ORDER BY display_order ASC, id ASC");
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
    </section>
    <section class="services-features" style="background-color: var(--bg-light); padding-bottom: 80px;">
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
    <!-- Include Contact and Marquee from components -->
    <?php include 'includes/components/contact.php'; ?>


</main>

<?php include 'includes/footer.php'; ?>
