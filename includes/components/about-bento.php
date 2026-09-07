<?php
if (!function_exists('get_bento_val')) {
    function get_bento_val($key, $data, $default) {
        $val = isset($data[$key]) && !empty($data[$key]) ? $data[$key] : $default;
        // Fix paths coming from admin panel which have '../'
        if (strpos($val, '../') === 0) {
            $val = substr($val, 3);
        }
        return $val;
    }
}
?>
<!-- Bento Grid Section -->
<section class="bento-section">
    <div class="container">
        <div class="bento-grid">
            
            <!-- Card 1: Large Image Quote -->
            <div class="bento-card bento-card-quote">
                <img src="<?php echo get_bento_val('about_b1_image', $about_content, 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'); ?>" alt="Interior Design" class="bento-bg-img">
                <div class="bento-quote-overlay">
                    <p class="bento-quote-text"><?php echo get_bento_val('about_b1_quote', $about_content, '"In design, we find the delicate balance between function and beauty, where every space whispers the stories of those who dwell within. At Kalp, our passion lies in crafting these narratives with elegance and purpose."'); ?></p>
                    <div class="bento-author">
                        <img src="<?php echo get_bento_val('about_b1_avatar', $about_content, 'assets/images/founder.jpeg'); ?>" alt="<?php echo get_bento_val('about_b1_name', $about_content, 'Reedam Kumar'); ?> - Founder">
                        <div class="bento-author-info">
                            <strong><?php echo get_bento_val('about_b1_name', $about_content, 'Reedam Kumar'); ?></strong>
                            <span><?php echo get_bento_val('about_b1_role', $about_content, 'Kalp Interior Design Studio, Founder'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Logo Text -->
            <div class="bento-card bento-card-logo" <?php $logo_bg = get_bento_val('about_b2_image', $about_content, ''); if($logo_bg && $logo_bg !== 'none'): ?>style="background-image: url('<?php echo $logo_bg; ?>'); background-size: contain; background-repeat: no-repeat; background-position: center;"<?php endif; ?>>
                <?php if(!$logo_bg || $logo_bg === 'none'): ?>
                <h2 class="bento-logo-text">Kalp Group</h2>
                <?php endif; ?>
            </div>

            <!-- Card 3: Happy Clients -->
            <div class="bento-card bento-card-clients">
                <h3 class="bento-stat-num"><?php echo get_bento_val('about_b3_value', $about_content, '150+'); ?></h3>
                <p class="bento-stat-desc"><?php echo get_bento_val('about_b3_label', $about_content, 'Happy Clients'); ?></p>
            </div>

            <!-- Card 4: Video Overlay -->
            <div class="bento-card bento-card-video">
                <img src="<?php echo get_bento_val('about_b4_image', $about_content, 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'); ?>" alt="Video Background" class="bento-bg-img">
                <div class="bento-video-overlay">
                    <?php 
                    $video_url = get_bento_val('about_b4_video', $about_content, '');
                    $play_href = $video_url ? $video_url : '#';
                    ?>
                    <a href="<?php echo $play_href; ?>" class="bento-play-btn" <?php if($video_url) echo 'target="_blank"'; ?>><i class="fa-solid fa-play"></i></a>
                    <p class="bento-video-text"><?php echo get_bento_val('about_b4_text', $about_content, 'Learn more<br>About Kalp Design Studio'); ?></p>
                </div>
            </div>

            <!-- Card 5: Projects -->
            <div class="bento-card bento-card-projects">
                <h3 class="bento-stat-num"><?php echo get_bento_val('about_b5_value', $about_content, '200+'); ?></h3>
                <div class="bento-bottom-text">
                    <strong><?php echo get_bento_val('about_b5_label', $about_content, 'Projects'); ?></strong>
                    <p><?php echo get_bento_val('about_b5_desc', $about_content, 'Over 200 successful projects completed'); ?></p>
                </div>
            </div>

            <!-- Card 6: Avatars -->
            <div class="bento-card bento-card-team">
                <div class="bento-avatars">
                    <?php 
                    $default_avatars = [
                        'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
                        'https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
                        'https://images.unsplash.com/photo-1534528741775-53994a69daeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
                        'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
                        'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80'
                    ];
                    for($i=1; $i<=5; $i++): 
                    ?>
                    <img src="<?php echo get_bento_val('about_b6_avatar_'.$i, $about_content, $default_avatars[$i-1]); ?>" alt="Team <?php echo $i; ?>">
                    <?php endfor; ?>
                </div>
                <p class="bento-team-text"><?php echo get_bento_val('about_b6_text', $about_content, '18 Creative Masterminds'); ?></p>
            </div>

            <!-- Card 7: Awards -->
            <div class="bento-card bento-card-awards">
                <h3 class="bento-stat-num"><?php echo get_bento_val('about_b7_value', $about_content, '8+'); ?></h3>
                <div class="bento-bottom-text">
                    <strong><?php echo get_bento_val('about_b7_label', $about_content, 'Prestigious Awards'); ?></strong>
                    <p><?php echo get_bento_val('about_b7_desc', $about_content, 'Over 8 Awards won showcasing extensive experience and portfolio.'); ?></p>
                </div>
            </div>

        </div>
    </div>
</section>
