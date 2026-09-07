<?php
// Fetch global settings for footer if not already fetched
if (!isset($global_settings)) {
    $global_settings = [];
    if (isset($conn)) {
        $res_g = $conn->query("SELECT setting_key, setting_value FROM site_settings");
        if ($res_g) {
            while($row_g = $res_g->fetch_assoc()) {
                $global_settings[$row_g['setting_key']] = $row_g['setting_value'];
            }
        }
    }
}
if (!function_exists('gval')) {
    function gval($key, $data, $default = '') {
        return (isset($data[$key]) && $data[$key] !== '') ? htmlspecialchars($data[$key]) : $default;
    }
}
?>
    <?php include __DIR__ . '/components/cta-banner.php'; ?>
    
    <footer class="site-footer" style="position: relative;">
        <!-- Footer Wave Divider -->
        <div class="footer-wave" style="position: absolute; top: -79px; left: 0; width: 100%; overflow: hidden; line-height: 0; z-index: 1;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" style="width: 100%; height: 80px; display: block;">
                <path fill="var(--primary-color)" fill-opacity="1" d="M0,160L48,144C96,128,192,96,288,106.7C384,117,480,171,576,165.3C672,160,768,96,864,96C960,96,1056,160,1152,176C1248,192,1344,160,1392,144L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            </svg>
        </div>
        
        <div class="container" style="position: relative; z-index: 2;">
            <div class="footer-top">
                <div class="footer-col">
                    <a href="index.php" class="footer-logo" style="display: block; margin-bottom: 20px;">
                        <img src="assets/images/logo.png" alt="Kalp Interior Studio" style="max-height: 50px; width: auto; object-fit: contain;">
                    </a>
                    <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 25px; line-height: 1.6;"><?php echo gval('footer_text', $global_settings, 'A home is built with emotions before it is built with materials.'); ?></p>
                    
                    <div class="social-links" style="display: flex; gap: 10px;">
                        <?php if (gval('social_twitter', $global_settings)): ?>
                        <a href="<?php echo gval('social_twitter', $global_settings); ?>" style="width: 35px; height: 35px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white;"><i class="fa-brands fa-x-twitter"></i></a>
                        <?php endif; ?>
                        
                        <?php if (gval('social_facebook', $global_settings)): ?>
                        <a href="<?php echo gval('social_facebook', $global_settings); ?>" style="width: 35px; height: 35px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white;"><i class="fa-brands fa-facebook-f"></i></a>
                        <?php endif; ?>
                        
                        <?php if (gval('social_linkedin', $global_settings)): ?>
                        <a href="<?php echo gval('social_linkedin', $global_settings); ?>" style="width: 35px; height: 35px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white;"><i class="fa-brands fa-linkedin-in"></i></a>
                        <?php endif; ?>
                        
                        <?php if (gval('social_instagram', $global_settings)): ?>
                        <a href="<?php echo gval('social_instagram', $global_settings); ?>" style="width: 35px; height: 35px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white;"><i class="fa-brands fa-instagram"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <div class="footer-links">
                        <a href="index.php">Home</a>
                        <a href="about.php">About Us</a>
                        <a href="services.php">Services</a>
                        <a href="projects.php">Projects</a>
                        <a href="contact.php">Contact Us</a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>Projects</h4>
                    <div class="footer-links">
                        <a href="projects.php">Residential Design</a>
                        <a href="projects.php">Commercial Design</a>
                        <a href="projects.php">Hospitality Design</a>
                        <a href="projects.php">Furniture Design</a>
                    </div>
                </div>
                
                <div class="footer-widget">
                    <h4 style="font-size: 16px; margin-bottom: 25px; color: white;">Contact Us</h4>
                    <ul class="footer-links">
                        <li><?php echo gval('contact_phone', $global_settings, '+91 9234772288'); ?></li>
                        <li><?php echo gval('contact_email', $global_settings, 'info@kalpinteriors.com'); ?></li>
                        <li style="line-height: 1.6;"><?php echo nl2br(gval('contact_address', $global_settings, "KALP INTERIOR DESIGN STUDIO,\nISM ROAD, opp. SRDAV, Pundag,\nRanchi, Jharkhand 834001")); ?></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>Copyright &copy; <?php echo date("Y"); ?> <span style="color: var(--accent-color);"><?php echo gval('footer_copyright_name', $global_settings, 'Kalp Studio.'); ?></span> All Rights Reserved.</p>
                <div class="footer-bottom-links">
                    <?php 
                        $terms_url = gval('footer_terms_url', $global_settings);
                        $terms_url = ($terms_url == '#' || $terms_url == '') ? 'terms-conditions.php' : $terms_url;
                        
                        $privacy_url = gval('footer_privacy_url', $global_settings);
                        $privacy_url = ($privacy_url == '#' || $privacy_url == '') ? 'privacy-policy.php' : $privacy_url;
                    ?>
                    <a href="<?php echo $terms_url; ?>">User Terms & Conditions</a>
                    <span class="divider">|</span>
                    <a href="<?php echo $privacy_url; ?>">Privacy Policy</a>
                </div>
            </div>
        </div>
    </footer>



    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
</body>
</html>
