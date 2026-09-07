<!-- Trusted Partners Stripe -->
<style>
@keyframes scrollPartners {
    0% { transform: translateX(0); }
    100% { transform: translateX(calc(-50% - 30px)); } /* 50% shift minus half the gap */
}
.partners-marquee-wrapper {
    overflow: hidden;
    white-space: nowrap;
    width: 100%;
    position: relative;
    -webkit-mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
}
.partners-marquee-content {
    display: inline-flex;
    align-items: center;
    gap: 60px;
    animation: scrollPartners 25s linear infinite;
}
.partners-marquee-content:hover {
    animation-play-state: paused;
}
</style>
<section class="partners-stripe" style="background-color: transparent; padding: 25px 0; overflow: hidden;">
    <div class="container" style="display: flex; align-items: center; gap: 40px;">
        
        <!-- Text area replicating the Stripe UI style -->
        <div class="partners-text" style="flex-shrink: 0; position: relative; z-index: 2; border-left: 5px solid var(--accent-color); padding-left: 15px;">
            <p style="color: black; font-family: var(--font-primary); font-size: 15px; margin: 0; line-height: 1.4; font-weight: 700; text-transform: uppercase;">
                OUR TRUSTED<br>PARTNERS
            </p>
        </div>
        
        <!-- Logos Area with Smooth Continuous Scroll -->
        <div class="partners-marquee-wrapper">
            <div class="partners-marquee-content">
                <?php 
                // We need two identical sets of logos for the continuous scrolling effect
                $partners_logos_html = '';
                $res = $conn->query("SELECT * FROM stripe_logos WHERE stripe_type = 'partner' ORDER BY created_at ASC");
                if($res && $res->num_rows > 0) {
                    while($row = $res->fetch_assoc()) {
                        $img_path = htmlspecialchars($row['image_path']);
                        $alt = htmlspecialchars($row['alt_text']);
                        $partners_logos_html .= '<img src="'.$img_path.'" alt="'.$alt.'" style="height: 25px; width: auto; transition: transform 0.3s;" onmouseover="this.style.transform=\'scale(1.1)\'" onmouseout="this.style.transform=\'scale(1)\'"> ';
                    }
                } else {
                    // Fallback to placeholders if no logos in DB yet
                    $partners_logos_html = '
                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/a9/Amazon_logo.svg" alt="Amazon" style="height: 25px; width: auto;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/2f/Google_2015_logo.svg" alt="Google" style="height: 25px; width: auto;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b9/Slack_Technologies_Logo.svg" alt="Slack" style="height: 25px; width: auto;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/IBM_logo.svg" alt="IBM" style="height: 25px; width: auto;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/0/08/Netflix_2015_logo.svg" alt="Netflix" style="height: 25px; width: auto;">';
                }
                
                // Print Set 1
                echo $partners_logos_html;
                ?>
                
                <!-- Set 2 (Duplicate for continuous loop) -->
                <?php echo $partners_logos_html; ?>
            </div>
        </div>
        
    </div>
</section>
