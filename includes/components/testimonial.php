<?php 
if (!isset($conn)) {
    if (file_exists('admin/config/db.php')) require_once 'admin/config/db.php';
    elseif (file_exists('config/db.php')) require_once 'config/db.php';
    elseif (file_exists('../admin/config/db.php')) require_once '../admin/config/db.php';
}

// Fetch Testimonial Stats
$testi_stats_content = [];
$testi_stats_stmt = $conn->prepare("SELECT section_key, content_value FROM page_content WHERE page_name = 'testimonial_stats'");
$testi_stats_stmt->execute();
$testi_stats_res = $testi_stats_stmt->get_result();
while($row = $testi_stats_res->fetch_assoc()) {
    $testi_stats_content[$row['section_key']] = $row['content_value'];
}
$testi_stats_stmt->close();
?>
<!-- Testimonial Section -->
<style>
    @media (max-width: 992px) {
        .testi-slide { min-width: calc(50% - 15px) !important; }
        .testi-stats-bar { flex-wrap: wrap !important; justify-content: center !important; gap: 30px !important; }
        .testi-stats-bar > div[style*="width: 1px"] { display: none !important; }
    }
    @media (max-width: 768px) {
        .testimonial-section.new-testi-design { padding: 60px 0 !important; }
        .testi-top-area { flex-direction: column !important; align-items: flex-start !important; gap: 15px !important; }
        .testi-top-area > div { max-width: 100% !important; }
        .testi-top-area h2.section-title { font-size: 2.2rem !important; }
        .testi-top-area p { font-size: 1rem !important; }
        .testi-slider-wrapper { margin-bottom: 40px !important; }
        .testi-slide { min-width: 100% !important; padding: 25px !important; }
        .testi-slide p { font-size: 14px !important; margin-bottom: 20px !important; }
        .testi-stats-bar { flex-direction: column !important; padding: 30px 20px !important; gap: 30px !important; }
        .stat-item { flex-direction: column !important; text-align: center !important; gap: 12px !important; width: 100% !important; }
        .testi-nav-arrow { width: 40px !important; height: 40px !important; }
    }
</style>
<section class="testimonial-section new-testi-design" style="background-color: #F6F6F6; padding: 100px 0;">
    <div class="container" style="max-width: 1200px;">
        <div class="testi-top-area" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
            <div style="max-width: 50%;">
                <p class="section-subtitle" style="justify-content: flex-start; margin-bottom: 15px;">TESTIMONIAL</p>
                <h2 class="section-title" style="font-size: 3rem; color: var(--text-dark); line-height: 1.2; margin: 0;">Real experiences from<br>satisfied homeowners.</h2>
            </div>
            <div style="max-width: 35%;">
                <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.6; margin: 0;">We take pride in delivering renovation and construction projects that exceed expectations from start to finish.</p>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 15px; margin-bottom: 30px;">
            <button class="testi-nav-arrow prev-slide" style="width: 45px; height: 45px; background-color: white; color: var(--text-dark); border-radius: 50%; border: 1px solid rgba(0,0,0,0.1); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; transition: var(--transition);"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="testi-nav-arrow next-slide" style="width: 45px; height: 45px; background-color: var(--text-dark); color: white; border-radius: 50%; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; transition: var(--transition);"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
        
        <div class="testi-slider-wrapper" style="position: relative; overflow: hidden; margin-bottom: 80px;">
            <div class="testi-slides" style="display: flex; gap: 30px; transition: transform 0.6s ease-in-out;">
                <?php
                $testi_query = "SELECT * FROM testimonials WHERE status = 'Published' ORDER BY created_at DESC";
                $testi_result = $conn->query($testi_query);
                $testi_count = 0;
                if ($testi_result && $testi_result->num_rows > 0) {
                    while ($row = $testi_result->fetch_assoc()) {
                        $img_src = !empty($row['client_image']) ? 'uploads/testimonials/' . htmlspecialchars($row['client_image']) : 'https://ui-avatars.com/api/?name='.urlencode($row['client_name']);
                ?>
                <!-- Slide -->
                <div class="testi-slide" style="min-width: calc(33.333% - 20px); background: white; border-radius: 10px; padding: 40px; box-shadow: 0 5px 20px rgba(0,0,0,0.03);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                        <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($row['client_name']); ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                        <?php if(!empty($row['company_name']) || !empty($row['company_logo'])): ?>
                        <span style="font-weight: 700; color: var(--text-dark); font-size: 16px; display: flex; align-items: center; gap: 5px;">
                            <?php if(!empty($row['company_logo'])): ?>
                            <img src="uploads/testimonials/<?php echo htmlspecialchars($row['company_logo']); ?>" style="max-height: <?php echo !empty($row['company_logo_size']) ? (int)$row['company_logo_size'] : 40; ?>px;">
                            <?php elseif(!empty($row['company_icon'])): ?>
                            <i class="<?php echo htmlspecialchars($row['company_icon']); ?>" style="color: #EAB136; font-size: <?php echo !empty($row['company_logo_size']) ? (int)$row['company_logo_size'] : 40; ?>px;"></i> 
                            <?php endif; ?>
                            <?php echo !empty($row['company_name']) ? htmlspecialchars($row['company_name']) : ''; ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <div style="font-size: 45px; color: #334C40; line-height: 1; margin-bottom: 20px;">
                        <i class="fa-solid fa-quote-left"></i>
                    </div>
                    <p style="color: var(--text-muted); line-height: 1.6; margin-bottom: 30px; font-size: 15px;">
                        <?php echo htmlspecialchars($row['content']); ?>
                    </p>
                    <div style="border-left: 3px solid #334C40; padding-left: 15px;">
                        <h4 style="color: var(--text-dark); margin: 0 0 3px 0; font-size: 16px; text-transform: uppercase;"><?php echo htmlspecialchars($row['client_name']); ?></h4>
                        <p style="color: var(--text-muted); font-size: 13px; margin: 0;"><?php echo htmlspecialchars($row['client_role']); ?></p>
                    </div>
                </div>
                <?php 
                        $testi_count++;
                    } 
                } else {
                    echo "<p>No testimonials found.</p>";
                }
                ?>

            </div>
        </div>

        <div class="testi-pagination" style="display: flex; justify-content: center; gap: 10px; margin-bottom: 50px;">
            <?php for($i=0; $i<$testi_count; $i++): ?>
            <div class="dot <?php echo $i===0?'active':''; ?>" data-index="<?php echo $i; ?>" style="width: 10px; height: 10px; border-radius: 50%; background: <?php echo $i===0?'var(--text-dark)':'#ddd'; ?>; cursor: pointer; transition: 0.3s;"></div>
            <?php endfor; ?>
        </div>

        <!-- Testimonial Stats Bar -->
        <div class="testi-stats-bar" style="background: white; border-radius: 20px; padding: 30px 50px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 30px rgba(0,0,0,0.03);">
            
            <div class="stat-item" style="display: flex; align-items: center; gap: 20px;">
                <div class="stat-icon" style="width: 50px; height: 50px; background: #fbf3ec; color: #EAB136; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;"><i class="fa-solid fa-users"></i></div>
                <div>
                    <?php $is_testi_editor = (isset($currentPage) && $currentPage == 'testimonials'); ?>
                    <h3 class="testi-stat1-val" <?php echo $is_testi_editor ? 'contenteditable="true"' : ''; ?> style="font-size: 24px; margin: 0 0 5px 0; color: var(--text-dark);"><?php echo isset($testi_stats_content['testi_stat1_value']) ? $testi_stats_content['testi_stat1_value'] : '250+'; ?></h3>
                    <p class="testi-stat1-label" <?php echo $is_testi_editor ? 'contenteditable="true"' : ''; ?> style="margin: 0; color: var(--text-muted); font-size: 13px;"><?php echo isset($testi_stats_content['testi_stat1_label']) ? $testi_stats_content['testi_stat1_label'] : 'Happy Clients'; ?></p>
                </div>
            </div>
            
            <div style="width: 1px; height: 50px; background: rgba(0,0,0,0.1);"></div>

            <div class="stat-item" style="display: flex; align-items: center; gap: 20px;">
                <div class="stat-icon" style="width: 50px; height: 50px; background: #fbf3ec; color: #EAB136; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;"><i class="fa-solid fa-comment-dots"></i></div>
                <div>
                    <h3 class="testi-stat2-val" <?php echo $is_testi_editor ? 'contenteditable="true"' : ''; ?> style="font-size: 24px; margin: 0 0 5px 0; color: var(--text-dark);"><?php echo isset($testi_stats_content['testi_stat2_value']) ? $testi_stats_content['testi_stat2_value'] : '4.9/5'; ?></h3>
                    <p class="testi-stat2-label" <?php echo $is_testi_editor ? 'contenteditable="true"' : ''; ?> style="margin: 0; color: var(--text-muted); font-size: 13px;"><?php echo isset($testi_stats_content['testi_stat2_label']) ? $testi_stats_content['testi_stat2_label'] : 'Average Rating'; ?></p>
                </div>
            </div>

            <div style="width: 1px; height: 50px; background: rgba(0,0,0,0.1);"></div>

            <div class="stat-item" style="display: flex; align-items: center; gap: 20px;">
                <div class="stat-icon" style="width: 50px; height: 50px; background: #fbf3ec; color: #EAB136; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;"><i class="fa-solid fa-medal"></i></div>
                <div>
                    <h3 class="testi-stat3-val" <?php echo $is_testi_editor ? 'contenteditable="true"' : ''; ?> style="font-size: 24px; margin: 0 0 5px 0; color: var(--text-dark);"><?php echo isset($testi_stats_content['testi_stat3_value']) ? $testi_stats_content['testi_stat3_value'] : '150+'; ?></h3>
                    <p class="testi-stat3-label" <?php echo $is_testi_editor ? 'contenteditable="true"' : ''; ?> style="margin: 0; color: var(--text-muted); font-size: 13px;"><?php echo isset($testi_stats_content['testi_stat3_label']) ? $testi_stats_content['testi_stat3_label'] : '5 Star Reviews'; ?></p>
                </div>
            </div>

            <div style="width: 1px; height: 50px; background: rgba(0,0,0,0.1);"></div>

            <div class="stat-item" style="display: flex; align-items: center; gap: 20px;">
                <div class="stat-icon" style="width: 50px; height: 50px; background: #fbf3ec; color: #EAB136; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;"><i class="fa-regular fa-face-smile"></i></div>
                <div>
                    <h3 class="testi-stat4-val" <?php echo $is_testi_editor ? 'contenteditable="true"' : ''; ?> style="font-size: 24px; margin: 0 0 5px 0; color: var(--text-dark);"><?php echo isset($testi_stats_content['testi_stat4_value']) ? $testi_stats_content['testi_stat4_value'] : '98%'; ?></h3>
                    <p class="testi-stat4-label" <?php echo $is_testi_editor ? 'contenteditable="true"' : ''; ?> style="margin: 0; color: var(--text-muted); font-size: 13px;"><?php echo isset($testi_stats_content['testi_stat4_label']) ? $testi_stats_content['testi_stat4_label'] : 'Client Satisfaction'; ?></p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Slider Logic -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const slidesContainer = document.querySelector('.testi-slides');
    const slides = document.querySelectorAll('.testi-slide');
    const dots = document.querySelectorAll('.testi-pagination .dot');
    const prevBtn = document.querySelector('.testi-nav-arrow.prev-slide');
    const nextBtn = document.querySelector('.testi-nav-arrow.next-slide');
    const sliderWrapper = document.querySelector('.testi-slider-wrapper');
    
    let currentIndex = 0;
    
    function getVisibleSlides() {
        if (window.innerWidth <= 768) return 1;
        if (window.innerWidth <= 992) return 2;
        return 3;
    }
    
    function getMaxIndex() {
        return Math.max(0, slides.length - getVisibleSlides());
    }

    function goToSlide(index) {
        const maxIndex = getMaxIndex();
        
        if (index < 0) index = maxIndex;
        if (index > maxIndex) index = 0;
        
        currentIndex = index;
        
        // Calculate offset based on a single slide's width + gap
        const slideWidth = slides[0].offsetWidth;
        const gap = 30; // 30px gap defined in CSS
        const offset = currentIndex * (slideWidth + gap);
        
        slidesContainer.style.transform = `translateX(-${offset}px)`;
        
        // Update dots
        dots.forEach(dot => {
            dot.style.background = '#ddd';
            dot.classList.remove('active');
        });
        
        // Safety check for dots in case of resize changing maxIndex
        if(dots[currentIndex]) {
            dots[currentIndex].style.background = 'var(--text-dark)';
            dots[currentIndex].classList.add('active');
        } else if (dots[0]) {
            dots[0].style.background = 'var(--text-dark)';
            dots[0].classList.add('active');
        }
    }

    // Manual Controls
    prevBtn.addEventListener('click', () => {
        goToSlide(currentIndex - 1);
        resetAutoScroll();
    });

    nextBtn.addEventListener('click', () => {
        goToSlide(currentIndex + 1);
        resetAutoScroll();
    });

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            goToSlide(index);
            resetAutoScroll();
        });
    });

    // Auto Scroll Logic (2 seconds as requested)
    let autoScrollInterval;
    function startAutoScroll() {
        autoScrollInterval = setInterval(() => {
            goToSlide(currentIndex + 1);
        }, 2000);
    }

    function stopAutoScroll() {
        clearInterval(autoScrollInterval);
    }

    function resetAutoScroll() {
        stopAutoScroll();
        startAutoScroll();
    }

    // Pause on hover
    sliderWrapper.addEventListener('mouseenter', stopAutoScroll);
    sliderWrapper.addEventListener('mouseleave', startAutoScroll);

    // Initialize
    startAutoScroll();
    
    // Handle window resize
    window.addEventListener('resize', () => {
        goToSlide(currentIndex);
    });
});
</script>

