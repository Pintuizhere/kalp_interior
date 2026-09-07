    <!-- Before & After Section -->
    <section class="before-after-section">
        <div class="container">
            
            <!-- Header -->
            <div class="ba-top-header">
                <div class="ba-title-area">
                    <p class="section-subtitle" style="color: white; font-weight: 600; letter-spacing: 2px;">BEFORE & AFTER</p>
                    <h2 class="section-title ba-section-title">
                        See Our <span class="accent-text">Design</span><br>Transformations
                    </h2>
                </div>
            </div>
            
            <?php
            $ba_pairs = $conn->query("SELECT * FROM before_after ORDER BY display_order ASC, id DESC");
            $total_pairs = $ba_pairs->num_rows;
            ?>
            
            <div class="ba-carousel-wrapper" style="position: relative; overflow: hidden; padding-bottom: 60px;">
                <div class="ba-carousel-inner" id="baCarouselInner" style="display: flex; transition: transform 0.5s ease; width: 100%;">
                    
                    <?php if ($total_pairs > 0): ?>
                        <?php while($pair = $ba_pairs->fetch_assoc()): ?>
                        <div class="ba-carousel-slide" style="min-width: 100%; flex-shrink: 0;">
                            <!-- Slider Area -->
                            <div class="ba-slider-area" style="margin-bottom: 0;">
                                <div class="ba-slider-container">
                                    <!-- After Image (Background) -->
                                    <div class="ba-image ba-image-after" style="background-image: url('<?php echo htmlspecialchars($pair['after_image']); ?>');">
                                        <span class="ba-label">After</span>
                                    </div>
                                    
                                    <!-- Before Image (Foreground overlay) -->
                                    <div class="ba-image ba-image-before" style="background-image: url('<?php echo htmlspecialchars($pair['before_image']); ?>');">
                                        <span class="ba-label">Before</span>
                                    </div>
                                    
                                    <!-- Slider Input -->
                                    <input type="range" min="0" max="100" value="50" class="ba-slider-input" id="ba-slider_<?php echo $pair['id']; ?>" style="touch-action: pan-y;">
                                    
                                    <!-- Slider Handle -->
                                    <div class="ba-slider-handle" id="ba-slider-handle_<?php echo $pair['id']; ?>">
                                        <i class="fa-solid fa-angle-left"></i>
                                        <i class="fa-solid fa-angle-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="ba-carousel-slide" style="min-width: 100%; flex-shrink: 0;">
                            <!-- Fallback if no pairs -->
                            <div class="ba-slider-area">
                                <div class="ba-slider-container">
                                    <div class="ba-image ba-image-after" style="background-image: url('assets/images/before.webp');">
                                        <span class="ba-label">Before</span>
                                    </div>
                                    <div class="ba-image ba-image-before" style="background-image: url('assets/images/after.webp');">
                                        <span class="ba-label">After</span>
                                    </div>
                                    <input type="range" min="0" max="100" value="50" class="ba-slider-input" style="touch-action: pan-y;">
                                    <div class="ba-slider-handle">
                                        <i class="fa-solid fa-angle-left"></i>
                                        <i class="fa-solid fa-angle-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                </div>
                
                <?php if ($total_pairs > 1): ?>
                <!-- Navigation -->
                <div class="ba-carousel-nav" style="display: flex; justify-content: center; align-items: center; gap: 30px; position: absolute; bottom: 0; left: 0; width: 100%;">
                    <button class="ba-prev" onclick="moveBaCarousel(-1)" style="background: var(--accent-color); border: none; color: #333; width: 45px; height: 45px; border-radius: 50%; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s; box-shadow: 0 4px 10px rgba(0,0,0,0.2);"><i class="fa-solid fa-angle-left"></i></button>
                    
                    <div class="ba-dots" style="display: flex; gap: 15px;">
                        <?php for($i=0; $i<$total_pairs; $i++): ?>
                        <span class="ba-dot <?php echo $i==0 ? 'active' : ''; ?>" onclick="goToBaSlide(<?php echo $i; ?>)" style="width: 14px; height: 14px; border-radius: 50%; background-color: <?php echo $i==0 ? 'var(--accent-color)' : 'rgba(255,255,255,0.4)'; ?>; cursor: pointer; transition: 0.3s;"></span>
                        <?php endfor; ?>
                    </div>
                    
                    <button class="ba-next" onclick="moveBaCarousel(1)" style="background: var(--accent-color); border: none; color: #333; width: 45px; height: 45px; border-radius: 50%; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s; box-shadow: 0 4px 10px rgba(0,0,0,0.2);"><i class="fa-solid fa-angle-right"></i></button>
                </div>
                <?php endif; ?>
                
            </div>
            
            <script>
                let currentBaSlide = 0;
                const totalBaSlides = <?php echo $total_pairs; ?>;
                
                function moveBaCarousel(dir) {
                    if (totalBaSlides <= 1) return;
                    currentBaSlide += dir;
                    if (currentBaSlide < 0) currentBaSlide = totalBaSlides - 1;
                    if (currentBaSlide >= totalBaSlides) currentBaSlide = 0;
                    updateBaCarousel();
                }
                
                function goToBaSlide(index) {
                    if (totalBaSlides <= 1) return;
                    currentBaSlide = index;
                    updateBaCarousel();
                }
                
                function updateBaCarousel() {
                    const inner = document.getElementById('baCarouselInner');
                    if(inner) {
                        inner.style.transform = `translateX(-${currentBaSlide * 100}%)`;
                        
                        // Update dots
                        const dots = document.querySelectorAll('.ba-dot');
                        dots.forEach((dot, index) => {
                            if(index === currentBaSlide) {
                                dot.style.backgroundColor = 'var(--accent-color)';
                                dot.classList.add('active');
                            } else {
                                dot.style.backgroundColor = 'rgba(255,255,255,0.4)';
                                dot.classList.remove('active');
                            }
                        });
                    }
                }

                // Explicitly bind slider logic to ensure it works
                document.addEventListener('DOMContentLoaded', () => {
                    const baContainers = document.querySelectorAll('.ba-slider-container');
                    baContainers.forEach(container => {
                        const baSlider = container.querySelector('.ba-slider-input');
                        const beforeImage = container.querySelector('.ba-image-before');
                        const sliderHandle = container.querySelector('.ba-slider-handle');
                        
                        if (baSlider && beforeImage && sliderHandle) {
                            baSlider.addEventListener('input', function(e) {
                                const sliderValue = e.target.value;
                                beforeImage.style.width = sliderValue + "%";
                                sliderHandle.style.left = sliderValue + "%";
                            });
                        }
                    });
                });
            </script>
            
        </div>
    </section>
