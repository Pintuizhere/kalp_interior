<?php 
require_once 'admin/config/db.php';
$currentPage = 'projects';

// Fetch Categories
$cat_query = "SELECT * FROM categories ORDER BY order_index ASC, name ASC";
$categories = $conn->query($cat_query);

// Pagination Setup
$limit = 9;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Fetch Total Projects Count
$total_query = "SELECT COUNT(*) as count FROM projects WHERE status != 'Archived'";
$total_result = $conn->query($total_query);
$total_projects = $total_result->fetch_assoc()['count'];
$total_pages = ceil($total_projects / $limit);

// Fetch Projects with Limit
$proj_query = "SELECT * FROM projects WHERE status != 'Archived' ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$projects = $conn->query($proj_query);

include 'includes/header.php'; 
?>


<main>
    <!-- Page Banner -->
    <section class="page-banner">
        <div class="container">
            <h1 class="banner-title">Projects</h1>
            <div class="breadcrumbs">
                <a href="index">Home</a> <span class="divider">/</span> <span class="current">Projects</span>
            </div>
        </div>
    </section>
    <section class="projects-section" style="background-color: #F6F6F6; padding: 100px 0;">
        <div class="container" style="max-width: 1300px;">
            <div class="projects-header" style="align-items: flex-start;">
                <div>
                    <p class="section-subtitle" style="justify-content: flex-start; margin-bottom: 20px;">OUR PROJECTS</p>
                    <h2 class="section-title" style="font-size: 3.5rem; color: var(--text-dark);">Explore <span class="accent-text" style="font-family: var(--font-accent); font-style: italic; font-weight: 400;">Our Portfolio</span></h2>
                    <p style="color: var(--text-muted); max-width: 500px; font-size: 1.1rem; line-height: 1.6; margin-top: 15px;">Discover a selection of our finest interior design projects crafted with creativity, functionality, and elegance.</p>
                </div>

            </div>
            
            <style>
                .projects-filter-wrapper {
                    position: relative;
                }
                .mobile-scroll-indicator {
                    display: none;
                }
                .projects-filter-container {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: flex-start;
                    gap: 15px;
                    margin-bottom: 50px;
                }
                .projects-filter-container .filter-tag {
                    white-space: nowrap;
                    display: inline-flex;
                    align-items: center;
                }
                
                @media (max-width: 768px) {
                    .projects-filter-container {
                        flex-wrap: nowrap;
                        overflow-x: auto;
                        padding-bottom: 10px; 
                        margin-bottom: 40px; 
                        -webkit-overflow-scrolling: touch;
                        scrollbar-width: none; 
                    }
                    .projects-filter-container::-webkit-scrollbar {
                        display: none;
                    }
                    .mobile-scroll-indicator {
                        display: flex;
                        position: absolute;
                        right: 0;
                        top: 0;
                        height: 48px; /* Approx height of tag */
                        width: 60px;
                        background: linear-gradient(to right, rgba(246,246,246,0) 0%, rgba(246,246,246,1) 70%);
                        align-items: center;
                        justify-content: flex-end;
                        padding-right: 5px;
                        color: var(--text-muted);
                        pointer-events: none;
                        z-index: 5;
                        transition: opacity 0.3s ease;
                    }
                }
                @keyframes pulse-horizontal {
                    0% { transform: translateX(0); opacity: 0.5; }
                    50% { transform: translateX(4px); opacity: 1; }
                    100% { transform: translateX(0); opacity: 0.5; }
                }
            </style>
            
            <div class="projects-filter-wrapper">
                <div class="filter-tags projects-filter-container" id="category-filters">
                    <span class="filter-tag active" style="background-color: #fcebdc; color: var(--text-dark); border: none; padding: 12px 25px;"><i class="fa-solid fa-border-all" style="margin-right: 8px;"></i> All</span>
                    <?php if($categories && $categories->num_rows > 0): ?>
                        <?php while($cat = $categories->fetch_assoc()): ?>
                            <span class="filter-tag" style="background-color: white; border: 1px solid rgba(0,0,0,0.05); padding: 12px 25px; border-radius: 25px;">
                                <?php if(!empty($cat['icon'])): ?><i class="<?php echo htmlspecialchars($cat['icon']); ?>" style="margin-right: 8px;"></i><?php endif; ?>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </span>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
                <div class="mobile-scroll-indicator">
                    <i class="fa-solid fa-chevron-right" style="animation: pulse-horizontal 1.5s infinite;"></i>
                </div>
            </div>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const filterContainer = document.getElementById('category-filters');
                    const indicator = document.querySelector('.mobile-scroll-indicator');
                    
                    if(filterContainer && indicator) {
                        const updateIndicator = () => {
                            // Check if scrolled to the end (within 5px margin)
                            const isAtEnd = filterContainer.scrollLeft + filterContainer.clientWidth >= filterContainer.scrollWidth - 5;
                            // Check if there is anything to scroll
                            const hasOverflow = filterContainer.scrollWidth > filterContainer.clientWidth;
                            
                            if(isAtEnd || !hasOverflow) {
                                indicator.style.opacity = '0';
                                indicator.style.pointerEvents = 'none';
                            } else {
                                indicator.style.opacity = '1';
                                indicator.style.pointerEvents = 'auto';
                            }
                        };
                        
                        filterContainer.addEventListener('scroll', updateIndicator);
                        window.addEventListener('resize', updateIndicator);
                        
                        // Initial check with slight delay to ensure fonts/layout rendered
                        setTimeout(updateIndicator, 100);
                    }
                });
            </script>
            
            <div class="projects-grid" style="grid-template-columns: repeat(3, 1fr); gap: 30px; margin-top: 50px;">

                <?php if($projects && $projects->num_rows > 0): ?>
                    <?php while($proj = $projects->fetch_assoc()): ?>
                    <div class="project-card new-design" style="position: relative;">
                        <a href="project-details.php?slug=<?php echo !empty($proj['slug']) ? urlencode($proj['slug']) : $proj['id']; ?>" style="display: block; overflow: hidden;">
                            <img src="<?php echo !empty($proj['cover_image']) ? htmlspecialchars($proj['cover_image']) : 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?w=800&q=80'; ?>" alt="<?php echo htmlspecialchars($proj['title']); ?>" style="transition: transform 0.5s ease; width: 100%; height: 100%; object-fit: cover;">
                        </a>
                        <div class="project-top-badges" style="position: absolute; top: 20px; left: 20px; right: 20px; display: flex; justify-content: space-between; align-items: center; z-index: 2;">
                            <span class="project-badge left" style="background: white; color: var(--text-dark); padding: 8px 15px; border-radius: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                <?php
                                $cat_icon = 'fa-solid fa-house'; 
                                if($categories && $categories->num_rows > 0) {
                                    $categories->data_seek(0);
                                    while($c = $categories->fetch_assoc()) {
                                        if(strtolower($c['name']) == strtolower($proj['category'])) {
                                            $cat_icon = $c['icon'];
                                            break;
                                        }
                                    }
                                }
                                ?>
                                <i class="<?php echo htmlspecialchars($cat_icon); ?>" style="color: #24352a;"></i> <?php echo htmlspecialchars($proj['category'] ?: 'Project'); ?>
                            </span>
                        </div>
                        <div class="project-bottom-content">
                            <div class="project-bottom-main">
                                <a href="project-details.php?slug=<?php echo !empty($proj['slug']) ? urlencode($proj['slug']) : $proj['id']; ?>" class="project-action-btn"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                <div class="project-bottom-info">
                                    <h3><a href="project-details.php?slug=<?php echo !empty($proj['slug']) ? urlencode($proj['slug']) : $proj['id']; ?>" style="color: inherit; text-decoration: none;"><?php echo htmlspecialchars($proj['title'] ?: 'Untitled Project'); ?></a></h3>
                                    <p><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($proj['location'] ?: 'N/A'); ?></p>
                                </div>
                            </div>
                            <div class="project-bottom-tags">
                                <span class="p-tag"><?php echo htmlspecialchars($proj['property_type'] ?: 'N/A'); ?></span>
                                <span class="p-tag"><?php echo htmlspecialchars($proj['category'] ?: 'N/A'); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                        <p style="color: var(--text-muted); font-size: 18px;">No projects found. Please add projects from the admin panel.</p>
                    </div>
                <?php endif; ?>

            </div>
            
            <?php if ($total_pages > 1): ?>
            <div class="projects-pagination" style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 50px;">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="page-link prev-page"><i class="fa-solid fa-angle-left"></i></a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <a href="?page=<?php echo $i; ?>" class="page-link active" style="background-color: var(--accent-color); color: var(--text-dark); border-color: var(--accent-color);"><?php echo $i; ?></a>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="page-link next-page"><i class="fa-solid fa-angle-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Features block -->
            <div class="portfolio-features new-features-bar" style="display: flex; justify-content: space-between; align-items: center; margin-top: 80px; padding: 30px 40px; background-color: white; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.03);">
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
            
            <!-- Dark CTA -->
            <style>
                .projects-cta-box {
                    background: linear-gradient(to right, rgba(26, 34, 31, 0.4) 0%, rgba(26, 34, 31, 1) 70%), url('https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80') left center / cover no-repeat;
                    border-radius: 15px;
                    padding: 40px 50px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-top: 50px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                }
                .projects-cta-content {
                    display: flex;
                    align-items: center;
                    gap: 20px;
                }
                .projects-cta-btn {
                    white-space: nowrap;
                    flex-shrink: 0;
                }
                @media (max-width: 768px) {
                    .projects-cta-box {
                        flex-direction: column;
                        text-align: center;
                        padding: 30px 20px;
                        gap: 25px;
                        background: linear-gradient(to bottom, rgba(26, 34, 31, 0.4) 0%, rgba(26, 34, 31, 1) 70%), url('https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80') center center / cover no-repeat;
                    }
                    .projects-cta-content {
                        flex-direction: column;
                        text-align: center;
                        gap: 15px;
                    }
                    .projects-cta-btn {
                        width: 100%;
                        justify-content: center;
                    }
                }
            </style>
            <div class="projects-cta projects-cta-box">
                <div class="projects-cta-content">
                    <div class="cta-icon" style="background-color: var(--accent-color); width: 60px; height: 60px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: var(--text-dark); flex-shrink: 0;">
                        <i class="fa-regular fa-calendar-days"></i>
                    </div>
                    <div>
                        <h3 style="color: white; font-size: 24px; margin-bottom: 5px;">Have a project in mind?</h3>
                        <p style="color: rgba(255,255,255,0.7); margin: 0; font-size: 15px;">Let's create a space that's uniquely yours.</p>
                    </div>
                </div>
                <a href="calculator.php" class="btn projects-cta-btn" style="background-color: var(--accent-color); color: var(--text-dark); padding: 12px 35px; border-radius: 30px; font-weight: 600; display: inline-flex; align-items: center; gap: 10px; text-decoration: none;">Get Estimate <i class="fa-solid fa-arrow-right" style="background-color: var(--text-dark); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; transform: rotate(-45deg);"></i></a>
            </div>
            
        </div>
    </section>
</main>


<?php include 'includes/footer.php'; ?>
