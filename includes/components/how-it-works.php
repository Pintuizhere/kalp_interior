<!-- How It Works Section -->
<section class="how-it-works-section" style="padding: 100px 0; background-color: var(--bg-white);">
    <div class="container">
        
        <div class="text-center" style="margin-bottom: 60px;">
            <p class="section-subtitle" style="justify-content: center; margin-bottom: 15px;">HOW IT WORK</p>
            <h2 class="section-title hiw-main-heading" style="text-transform: uppercase;"><?php echo isset($about_content['hiw_main_heading']) ? $about_content['hiw_main_heading'] : "HOW WE HANDLE YOUR HOME'S<br>RENOVATIONS"; ?></h2>
        </div>

        <style>
            .hiw-pane {
                position: relative;
                overflow: hidden;
                border-radius: 0 0 20px 20px;
            }
            .hiw-desc-overlay {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                background: rgba(26, 36, 33, 0.9);
                backdrop-filter: blur(8px);
                color: white;
                padding: 30px 40px;
                transform: translateY(10px);
                opacity: 0;
                transition: all 0.5s ease 0.2s;
                text-align: left;
                border-top: 2px solid var(--accent-color);
            }
            .hiw-pane.active .hiw-desc-overlay {
                transform: translateY(0);
                opacity: 1;
            }
            .hiw-desc-overlay h4 {
                color: var(--accent-color);
                font-size: 1.6rem;
                margin-bottom: 10px;
                font-family: var(--font-primary);
            }
            .hiw-desc-overlay p {
                margin: 0;
                font-size: 1.05rem;
                line-height: 1.6;
                color: #e0e0e0;
            }
            @media (max-width: 768px) {
                .hiw-desc-overlay {
                    padding: 20px;
                }
                .hiw-desc-overlay h4 {
                    font-size: 1.3rem;
                }
                .hiw-desc-overlay p {
                    font-size: 0.9rem;
                }
            }
        </style>

        <div class="how-it-works-container">
            <!-- Tabs -->
            <div class="hiw-tabs">
                <button class="hiw-tab active" data-tab="1">
                    <span class="hiw-num">01.</span> <span class="hiw-tab-title-1"><?php echo isset($about_content['hiw_tab1_title']) ? htmlspecialchars($about_content['hiw_tab1_title']) : 'Consultation'; ?></span>
                </button>
                <button class="hiw-tab" data-tab="2">
                    <span class="hiw-num">02.</span> <span class="hiw-tab-title-2"><?php echo isset($about_content['hiw_tab2_title']) ? htmlspecialchars($about_content['hiw_tab2_title']) : 'Design'; ?></span>
                </button>
                <button class="hiw-tab" data-tab="3">
                    <span class="hiw-num">03.</span> <span class="hiw-tab-title-3"><?php echo isset($about_content['hiw_tab3_title']) ? htmlspecialchars($about_content['hiw_tab3_title']) : 'Construction'; ?></span>
                </button>
                <button class="hiw-tab" data-tab="4">
                    <span class="hiw-num">04.</span> <span class="hiw-tab-title-4"><?php echo isset($about_content['hiw_tab4_title']) ? htmlspecialchars($about_content['hiw_tab4_title']) : 'Final Touch'; ?></span>
                </button>
            </div>

            <!-- Tab Content (Images) -->
            <?php
            if (!function_exists('get_hiw_image')) {
                function get_hiw_image($key, $default, $content, $page) {
                    $src = isset($content[$key]) ? $content[$key] : $default;
                    if (isset($page) && strpos($page, 'editor_') === 0 && strpos($src, 'http') !== 0 && strpos($src, '../') !== 0) {
                        return '../' . $src;
                    }
                    return $src;
                }
            }
            ?>
            <div class="hiw-content">
                <div class="hiw-pane active" id="hiw-pane-1">
                    <img src="<?php echo htmlspecialchars(get_hiw_image('hiw_tab1_image', 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=1200&q=80', $about_content, isset($currentPage) ? $currentPage : null)); ?>" alt="Consultation" class="hiw-tab-img-1">
                    <div class="hiw-desc-overlay">
                        <h4 class="hiw-tab-heading-1"><?php echo isset($about_content['hiw_tab1_heading']) ? $about_content['hiw_tab1_heading'] : 'Consultation'; ?></h4>
                        <p class="hiw-tab-desc-1"><?php echo isset($about_content['hiw_tab1_desc']) ? $about_content['hiw_tab1_desc'] : 'We begin with a detailed discussion to deeply understand your vision, functional requirements, style preferences, and budget constraints.'; ?></p>
                    </div>
                </div>
                <div class="hiw-pane" id="hiw-pane-2">
                    <img src="<?php echo htmlspecialchars(get_hiw_image('hiw_tab2_image', 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=1200&q=80', $about_content, isset($currentPage) ? $currentPage : null)); ?>" alt="Design" class="hiw-tab-img-2">
                    <div class="hiw-desc-overlay">
                        <h4 class="hiw-tab-heading-2"><?php echo isset($about_content['hiw_tab2_heading']) ? $about_content['hiw_tab2_heading'] : 'Design & Planning'; ?></h4>
                        <p class="hiw-tab-desc-2"><?php echo isset($about_content['hiw_tab2_desc']) ? $about_content['hiw_tab2_desc'] : 'Our experts create comprehensive 2D layouts and 3D renderings, bringing your ideas to life with precise material and lighting detailing.'; ?></p>
                    </div>
                </div>
                <div class="hiw-pane" id="hiw-pane-3">
                    <img src="<?php echo htmlspecialchars(get_hiw_image('hiw_tab3_image', 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=1200&q=80', $about_content, isset($currentPage) ? $currentPage : null)); ?>" alt="Construction" class="hiw-tab-img-3">
                    <div class="hiw-desc-overlay">
                        <h4 class="hiw-tab-heading-3"><?php echo isset($about_content['hiw_tab3_heading']) ? $about_content['hiw_tab3_heading'] : 'Construction & Execution'; ?></h4>
                        <p class="hiw-tab-desc-3"><?php echo isset($about_content['hiw_tab3_desc']) ? $about_content['hiw_tab3_desc'] : 'Our skilled contractors and project managers execute the build with premium materials, rigorous quality control, and strict timelines.'; ?></p>
                    </div>
                </div>
                <div class="hiw-pane" id="hiw-pane-4">
                    <img src="<?php echo htmlspecialchars(get_hiw_image('hiw_tab4_image', 'https://images.unsplash.com/photo-1600607686527-6fb886090705?w=1200&q=80', $about_content, isset($currentPage) ? $currentPage : null)); ?>" alt="Final Touch" class="hiw-tab-img-4">
                    <div class="hiw-desc-overlay">
                        <h4 class="hiw-tab-heading-4"><?php echo isset($about_content['hiw_tab4_heading']) ? $about_content['hiw_tab4_heading'] : 'The Final Touch'; ?></h4>
                        <p class="hiw-tab-desc-4"><?php echo isset($about_content['hiw_tab4_desc']) ? $about_content['hiw_tab4_desc'] : 'We add the perfect styling, artwork, and décor elements, handing over a beautifully finished space that is ready for you to enjoy.'; ?></p>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.hiw-tab');
        const panes = document.querySelectorAll('.hiw-pane');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active state from all tabs and panes
                tabs.forEach(t => t.classList.remove('active'));
                panes.forEach(p => p.classList.remove('active'));

                // Add active state to the clicked tab
                tab.classList.add('active');
                
                // Show the corresponding pane
                const targetId = 'hiw-pane-' + tab.getAttribute('data-tab');
                document.getElementById(targetId).classList.add('active');
            });
        });
    });
</script>
