<?php 
$currentPage = 'contact';
include 'includes/header.php'; 
?>

<main>
    <!-- Page Banner -->
    <section class="page-banner">
        <div class="container">
            <h1 class="banner-title">Contact Us</h1>
            <div class="breadcrumbs">
                <a href="index">Home</a> <span class="divider">/</span> <span class="current">Contact Us</span>
            </div>
        </div>
    </section>

    <!-- Main Contact Area -->
    <div style="padding: 100px 0; background-color: var(--bg-white);">
        <?php include 'includes/components/contact.php'; ?>
    </div>

    <!-- Map Section -->
    <section class="map-section" style="line-height: 0;">
        <iframe src="https://maps.google.com/maps?q=KALP%20INTERIOR%20DESIGN%20STUDIO,%20ISM%20ROAD,%20opp.%20SRDAV,%20Pundag,%20Ranchi,%20Jharkhand%20834001&t=&z=15&ie=UTF8&iwloc=&output=embed" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </section>

    <!-- Features Info Bar -->
    <section class="features-info-section" style="padding: 60px 0; background-color: var(--bg-white);">
        <div class="container">
            <div class="features-info-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">
                
                <div class="feature-info-item" style="display: flex; align-items: center; gap: 20px; justify-content: center;">
                    <div class="feature-info-icon" style="font-size: 35px; color: var(--accent-color); position: relative;">
                        <i class="fa-solid fa-box-open" style="position: relative; z-index: 2; color: var(--primary-color);"></i>
                        <span style="position: absolute; top: 10px; right: -10px; width: 15px; height: 15px; background: var(--accent-color); border-radius: 50%; z-index: 1;"></span>
                    </div>
                    <div class="feature-info-text">
                        <h4 style="font-size: 16px; margin-bottom: 5px; color: var(--text-dark);">Reasonable Prices</h4>
                        <p style="font-size: 12px; color: var(--text-muted); margin: 0;">Quality design at affordable rates.</p>
                    </div>
                </div>

                <div class="feature-info-item" style="display: flex; align-items: center; gap: 20px; justify-content: center;">
                    <div class="feature-info-icon" style="font-size: 35px; color: var(--accent-color); position: relative;">
                        <i class="fa-solid fa-wallet" style="position: relative; z-index: 2; color: var(--primary-color);"></i>
                        <span style="position: absolute; top: 10px; right: -10px; width: 15px; height: 15px; background: var(--accent-color); border-radius: 50%; z-index: 1;"></span>
                    </div>
                    <div class="feature-info-text">
                        <h4 style="font-size: 16px; margin-bottom: 5px; color: var(--text-dark);">Timely Project Delivery</h4>
                        <p style="font-size: 12px; color: var(--text-muted); margin: 0;">On-time project completion.</p>
                    </div>
                </div>

                <div class="feature-info-item" style="display: flex; align-items: center; gap: 20px; justify-content: center;">
                    <div class="feature-info-icon" style="font-size: 35px; color: var(--accent-color); position: relative;">
                        <i class="fa-solid fa-users-gear" style="position: relative; z-index: 2; color: var(--primary-color);"></i>
                        <span style="position: absolute; top: 10px; right: -10px; width: 15px; height: 15px; background: var(--accent-color); border-radius: 50%; z-index: 1;"></span>
                    </div>
                    <div class="feature-info-text">
                        <h4 style="font-size: 16px; margin-bottom: 5px; color: var(--text-dark);">Professional Team</h4>
                        <p style="font-size: 12px; color: var(--text-muted); margin: 0;">Expert architects, top results.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Marquee -->


</main>

<?php include 'includes/footer.php'; ?>
