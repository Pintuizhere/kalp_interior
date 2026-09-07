    <!-- Press & Achievements Accordion Section -->
    <style>
        /* Modal Styles */
        .press-modal {
            display: none; 
            position: fixed; 
            z-index: 10000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            background-color: rgba(0,0,0,0.9);
            backdrop-filter: blur(5px);
            align-items: center;
            justify-content: center;
        }
        .press-modal-content {
            display: block;
            max-width: 90%;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            animation: pressZoomIn 0.3s ease;
        }
        @keyframes pressZoomIn {
            from {transform:scale(0.9); opacity:0;}
            to {transform:scale(1); opacity:1;}
        }
        .press-modal-close {
            position: absolute;
            top: 20px;
            right: 30px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
            z-index: 10001;
        }
        .press-modal-close:hover,
        .press-modal-close:focus {
            color: var(--accent-color);
            text-decoration: none;
        }
        .press-panel.active {
            cursor: zoom-in;
        }
        
        @media (max-width: 768px) {
            .awards-section { padding: 60px 15px !important; }
            .awards-header .section-title { font-size: 2.2rem !important; margin-bottom: 30px !important; }
            .awards-header .section-subtitle { font-size: 13px !important; margin-bottom: 15px !important; }
            .press-panel-content { padding: 20px !important; }
            .press-title { font-size: 1.4rem !important; }
            .press-brand { font-size: 14px !important; }
            .press-date { font-size: 12px !important; }
        }
    </style>
    <section class="awards-section" style="background-color: #111111; padding: 100px 0;">
        <div class="container" style="max-width: 1400px;">
            <div class="awards-header">
                <p class="section-subtitle" style="justify-content: center; margin-bottom: 20px; color: rgba(255,255,255,0.5);">
                    PRESS & ACHIEVEMENTS
                    <span style="display: inline-block; width: 40px; height: 1px; background-color: var(--accent-color); margin-left: 15px;"></span>
                </p>
                <h2 class="section-title" style="text-align: center; color: white; margin-bottom: 50px;">Featured <span class="accent-text">In The Press</span></h2>
            </div>
            
            <div class="press-accordion-container">
                <?php
                if (!isset($conn)) {
                    require_once __DIR__ . '/../../admin/config/db.php';
                }
                $awards_query = $conn->query("SELECT * FROM awards ORDER BY display_order ASC, id ASC");
                $is_first = true;
                
                if ($awards_query && $awards_query->num_rows > 0):
                    while($award = $awards_query->fetch_assoc()):
                        $active_class = $is_first ? 'active' : '';
                        $is_first = false;
                        
                        // Ensure image path is correct relative to frontend
                        $bg_image = $award['image'];
                        if (strpos($bg_image, 'uploads/') === 0) {
                            $bg_image = $bg_image;
                        }
                ?>
                <div class="press-panel <?php echo $active_class; ?>" style="background-image: url('<?php echo htmlspecialchars($bg_image); ?>');">
                    <div class="press-panel-overlay"></div>
                    <div class="press-panel-content">
                        <div class="press-brand">
                            <i class="<?php echo htmlspecialchars($award['icon']); ?>" style="color: var(--accent-color);"></i> <?php echo htmlspecialchars($award['brand']); ?>
                        </div>
                        <div class="press-bottom">
                            <h3 class="press-title"><?php echo htmlspecialchars($award['title']); ?></h3>
                            <div class="press-date">
                                <span><?php echo htmlspecialchars($award['day_text']); ?></span>
                                <span><?php echo htmlspecialchars($award['date_text']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                    endwhile;
                else: 
                ?>
                <div style="width: 100%; text-align: center; color: white; padding: 50px;">
                    <p>No awards to display. Please add them from the admin panel.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Image Modal -->
    <div id="pressImageModal" class="press-modal">
        <span class="press-modal-close">&times;</span>
        <img class="press-modal-content" id="pressModalImg">
    </div>

    <!-- JavaScript for Accordion Interactivity & Modal -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const panels = document.querySelectorAll('.press-panel');
            const modal = document.getElementById('pressImageModal');
            const modalImg = document.getElementById('pressModalImg');
            const closeBtn = document.querySelector('.press-modal-close');
            
            panels.forEach(panel => {
                panel.addEventListener('click', () => {
                    if (panel.classList.contains('active')) {
                        // Open full image popup
                        const bgImage = window.getComputedStyle(panel).backgroundImage;
                        const url = bgImage.slice(4, -1).replace(/"/g, "").replace(/'/g, "");
                        if(url && url !== 'none') {
                            modal.style.display = "flex";
                            modalImg.src = url;
                        }
                    } else {
                        // Remove active class from all panels
                        panels.forEach(p => p.classList.remove('active'));
                        // Add active class to the clicked panel
                        panel.classList.add('active');
                    }
                });
            });

            // Close modal when clicking X
            closeBtn.onclick = function() {
                modal.style.display = "none";
            }
            // Close modal when clicking outside image
            modal.onclick = function(e) {
                if (e.target !== modalImg) {
                    modal.style.display = "none";
                }
            }
        });
    </script>
