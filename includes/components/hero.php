<?php
$bg_image = !empty($home_content['hero_bg_image']) ? htmlspecialchars($home_content['hero_bg_image']) : 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';
$rating_text = !empty($home_content['hero_rating_text']) ? htmlspecialchars($home_content['hero_rating_text']) : '4.9/5 Rating - 15,000 Reviews';
$btn1_text = !empty($home_content['hero_btn1_text']) ? htmlspecialchars($home_content['hero_btn1_text']) : 'Contact us';
$btn1_link = !empty($home_content['hero_btn1_link']) ? htmlspecialchars($home_content['hero_btn1_link']) : 'contact.php';
$btn2_text = !empty($home_content['hero_btn2_text']) ? htmlspecialchars($home_content['hero_btn2_text']) : 'View Projects';
$btn2_link = !empty($home_content['hero_btn2_link']) ? htmlspecialchars($home_content['hero_btn2_link']) : 'projects.php';
$stat_projects = !empty($home_content['stat_projects']) ? $home_content['stat_projects'] : '500+';
$stat_projects_label = !empty($home_content['stat_projects_label']) ? $home_content['stat_projects_label'] : 'Projects Completed';
$stat_experience = !empty($home_content['stat_experience']) ? $home_content['stat_experience'] : '18+';
$stat_experience_label = !empty($home_content['stat_experience_label']) ? $home_content['stat_experience_label'] : 'Years of Experience';
$stat_clients = !empty($home_content['stat_clients']) ? $home_content['stat_clients'] : '300+';
$stat_clients_label = !empty($home_content['stat_clients_label']) ? $home_content['stat_clients_label'] : 'Happy Clients';
$stat_satisfaction = !empty($home_content['stat_satisfaction']) ? $home_content['stat_satisfaction'] : '98%';
$stat_satisfaction_label = !empty($home_content['stat_satisfaction_label']) ? $home_content['stat_satisfaction_label'] : 'Client Satisfaction';

$avatar_1 = !empty($home_content['hero_avatar_1']) ? htmlspecialchars($home_content['hero_avatar_1']) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80';
$avatar_2 = !empty($home_content['hero_avatar_2']) ? htmlspecialchars($home_content['hero_avatar_2']) : 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80';
$avatar_3 = !empty($home_content['hero_avatar_3']) ? htmlspecialchars($home_content['hero_avatar_3']) : 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80';
$avatar_4 = !empty($home_content['hero_avatar_4']) ? htmlspecialchars($home_content['hero_avatar_4']) : 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80';
?>
    <!-- Hero Section -->
    <section class="new-hero-section">
        <div class="new-hero-wrapper">
            <div class="hero-bg-image" style="background-image: url('<?php echo $bg_image; ?>');"></div>
            <div class="hero-bg-overlay"></div>
        <div class="container" style="position: relative; z-index: 3; width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center;">
            <div class="new-hero-content">
                
                <div class="hero-rating-widget">
                    <div class="rating-avatars">
                        <img src="<?php echo $avatar_1; ?>" alt="User">
                        <img src="<?php echo $avatar_2; ?>" alt="User">
                        <img src="<?php echo $avatar_3; ?>" alt="User">
                        <img src="<?php echo $avatar_4; ?>" alt="User">
                    </div>
                    <div class="rating-text-block">
                        <div class="rating-stars">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <span class="rating-text"><?php echo $rating_text; ?></span>
                    </div>
                </div>

                <h1 class="new-hero-title"><?php echo isset($home_content['hero_title']) ? $home_content['hero_title'] : 'Elevate <span class="accent-text" style="font-family: var(--font-accent); font-weight: 700; display: inline-block; position: relative; z-index: 10;">Your Space</span> with Exceptional Interior Design'; ?></h1>
                <p class="new-hero-desc"><?php echo isset($home_content['hero_desc']) ? htmlspecialchars($home_content['hero_desc']) : 'Kalp Interiors specializes in modern, luxurious, and personalized interior experiences.'; ?></p>
                
                <div class="new-hero-actions">
                    <a href="<?php echo $btn2_link; ?>" class="btn hero-yellow-btn">
                        <?php echo $btn2_text; ?> <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i>
                    </a>
                    <a href="<?php echo $btn1_link; ?>" class="view-projects-link">
                        <?php echo $btn1_text; ?> <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

            </div>

            <!-- Bottom Stats Grid -->
            <div class="new-hero-stats">
                <div class="hero-stat-col">
                    <h3><?php echo isset($home_content['stat_projects']) ? htmlspecialchars($home_content['stat_projects']) : '500+'; ?></h3>
                    <p><?php echo isset($home_content['stat_projects_label']) ? htmlspecialchars($home_content['stat_projects_label']) : 'Projects Completed'; ?></p>
                </div>
                <div class="hero-stat-col">
                    <h3><?php echo isset($home_content['stat_experience']) ? htmlspecialchars($home_content['stat_experience']) : '18+'; ?></h3>
                    <p><?php echo isset($home_content['stat_experience_label']) ? htmlspecialchars($home_content['stat_experience_label']) : 'Years of Experience'; ?></p>
                </div>
                <div class="hero-stat-col">
                    <h3><?php echo isset($home_content['stat_clients']) ? htmlspecialchars($home_content['stat_clients']) : '300+'; ?></h3>
                    <p><?php echo isset($home_content['stat_clients_label']) ? htmlspecialchars($home_content['stat_clients_label']) : 'Happy Clients'; ?></p>
                </div>
                <div class="hero-stat-col border-none">
                    <h3><?php echo isset($home_content['stat_satisfaction']) ? htmlspecialchars($home_content['stat_satisfaction']) : '98%'; ?></h3>
                    <p><?php echo isset($home_content['stat_satisfaction_label']) ? htmlspecialchars($home_content['stat_satisfaction_label']) : 'Client Satisfaction'; ?></p>
                </div>
            </div>
        </div>

        </div>
    </section>

    <style>
    .hero-yellow-btn {
        background-color: var(--accent-color);
        color: var(--text-dark);
        border: none;
        font-weight: 600;
        padding: 15px 30px;
        transition: color 0.3s ease;
    }
    .hero-yellow-btn:hover {
        color: #ffffff !important;
    }
    .hero-line-1 {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        column-gap: 15px;
        min-height: 80px;
        margin-bottom: 5px;
    }
    .hero-line-sub {
        display: block;
    }
    .new-hero-title .accent-text {
        display: inline-block;
        white-space: nowrap;
        width: 350px;
        text-align: left;
    }
    @media (max-width: 768px) {
        .hero-line-1 {
            min-height: 140px;
        }
        .new-hero-title .accent-text {
            width: 220px;
        }
    }
    </style>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // We include "Your Space" so it loops back gracefully
        const texts = ["Your Space", "Your Home", "Your Office", "Your Business", "Your Lifestyle"];
        let currentIndex = 0;
        const accentEl = document.querySelector('.new-hero-title .accent-text');
        
        if(accentEl) {
            const typeSpeed = 100;
            const eraseSpeed = 50;
            const delayBetweenTexts = 2000;

            let charIndex = accentEl.innerText.length;
            let isDeleting = true; // start by deleting the initial text

            function typeWriter() {
                const currentText = texts[currentIndex];

                if (isDeleting) {
                    accentEl.innerText = currentText.substring(0, charIndex - 1);
                    charIndex--;
                } else {
                    accentEl.innerText = currentText.substring(0, charIndex + 1);
                    charIndex++;
                }

                let typeDelay = isDeleting ? eraseSpeed : typeSpeed;

                if (!isDeleting && charIndex === currentText.length) {
                    typeDelay = delayBetweenTexts;
                    isDeleting = true;
                } else if (isDeleting && charIndex === 0) {
                    isDeleting = false;
                    currentIndex = (currentIndex + 1) % texts.length;
                    typeDelay = 400; 
                }

                setTimeout(typeWriter, typeDelay);
            }

            setTimeout(() => {
                charIndex = accentEl.innerText.length;
                typeWriter();
            }, delayBetweenTexts);
        }

        // Count up animation for stats
        const statElements = document.querySelectorAll('.hero-stat-col h3, .stat-item h3');
        
        statElements.forEach(el => {
            const text = el.innerText.trim();
            const match = text.match(/^(\d+)(.*)$/);
            if (match) {
                el.dataset.target = match[1];
                el.dataset.suffix = match[2] || '';
                el.innerText = '0' + (match[2] || '');
            } else {
                el.dataset.target = 0;
            }
        });

        const animateCountUp = (el) => {
            const target = parseInt(el.dataset.target);
            if (isNaN(target) || target === 0) return;
            const suffix = el.dataset.suffix;
            const duration = 2000; // 2 seconds
            let startTimestamp = null;
            
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                // Ease out function for smoother ending
                const easeOutProgress = progress * (2 - progress);
                const current = Math.floor(easeOutProgress * target);
                
                el.innerText = current + suffix;
                
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    el.innerText = target + suffix;
                }
            };
            window.requestAnimationFrame(step);
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCountUp(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        statElements.forEach(el => {
            if (el.dataset.target > 0) {
                observer.observe(el);
            }
        });
    });
    </script>
