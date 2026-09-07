<?php 
$currentPage = 'blog';
include 'includes/header.php'; 
require_once 'admin/config/db.php';
?>

<main>
    <!-- Page Banner -->
    <section class="page-banner">
        <div class="container">
            <h1 class="banner-title">Blog</h1>
            <div class="breadcrumbs">
                <a href="index">Home</a> <span class="divider">/</span> <span class="current">Blog</span>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section class="blog-section" style="padding: 80px 0; background-color: var(--bg-white);">
        <div class="container">
            <div class="text-center" style="margin-bottom: 50px; text-align: center;">
                <p class="section-subtitle" style="justify-content: center;">NEWS & BLOG</p>
                <h2 class="section-title">Our Latest <span class="accent-text" style="font-family: var(--font-accent); font-style: italic; font-weight: 400;">News & Blogs</span></h2>
            </div>
            
            <style>
                .blog-filter-wrapper { position: relative; width: 100%; }
                .mobile-scroll-indicator-blog { display: none; }
                .blog-filter-container {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: flex-start;
                    gap: 10px;
                }
                .blog-filter-container .filter-tag {
                    white-space: nowrap;
                    display: inline-flex;
                    align-items: center;
                }
                
                @media (max-width: 768px) {
                    .blog-filter-container {
                        flex-wrap: nowrap !important;
                        overflow-x: auto;
                        padding-bottom: 15px; 
                        margin-bottom: 10px; 
                        -webkit-overflow-scrolling: touch;
                        scrollbar-width: none; 
                    }
                    .blog-filter-container::-webkit-scrollbar {
                        display: none;
                    }
                    .mobile-scroll-indicator-blog {
                        display: flex;
                        position: absolute;
                        right: 0;
                        top: 0;
                        height: 42px; /* Approx height of tag */
                        width: 60px;
                        background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 80%);
                        align-items: center;
                        justify-content: flex-end;
                        padding-right: 5px;
                        color: var(--text-muted);
                        pointer-events: none;
                        z-index: 5;
                        transition: opacity 0.3s ease;
                    }
                }
                @keyframes pulse-horizontal-blog {
                    0% { transform: translateX(0); opacity: 0.5; }
                    50% { transform: translateX(4px); opacity: 1; }
                    100% { transform: translateX(0); opacity: 0.5; }
                }
            </style>
            <div class="blog-filters-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; flex-wrap: wrap; gap: 20px;">
                <div class="blog-filter-wrapper">
                    <div class="filter-tags blog-filter-container" id="blog-category-filters">
                        <span class="filter-tag active" style="background-color: var(--accent-color); color: var(--text-dark); border: none; padding: 10px 20px; border-radius: 25px; font-weight: 500; font-size: 14px; display: inline-flex; align-items: center; cursor: pointer; transition: all 0.3s ease;"><i class="fa-solid fa-border-all" style="margin-right: 8px;"></i> All Posts</span>
                        <span class="filter-tag" style="background-color: white; border: 1px solid rgba(0,0,0,0.05); padding: 10px 20px; border-radius: 25px; font-weight: 500; font-size: 14px; color: var(--text-dark); cursor: pointer; transition: all 0.3s ease;">Design Tips</span>
                        <span class="filter-tag" style="background-color: white; border: 1px solid rgba(0,0,0,0.05); padding: 10px 20px; border-radius: 25px; font-weight: 500; font-size: 14px; color: var(--text-dark); cursor: pointer; transition: all 0.3s ease;">Trends</span>
                        <span class="filter-tag" style="background-color: white; border: 1px solid rgba(0,0,0,0.05); padding: 10px 20px; border-radius: 25px; font-weight: 500; font-size: 14px; color: var(--text-dark); cursor: pointer; transition: all 0.3s ease;">Ideas & Inspiration</span>
                        <span class="filter-tag" style="background-color: white; border: 1px solid rgba(0,0,0,0.05); padding: 10px 20px; border-radius: 25px; font-weight: 500; font-size: 14px; color: var(--text-dark); cursor: pointer; transition: all 0.3s ease;">News</span>
                        <span class="filter-tag" style="background-color: white; border: 1px solid rgba(0,0,0,0.05); padding: 10px 20px; border-radius: 25px; font-weight: 500; font-size: 14px; color: var(--text-dark); cursor: pointer; transition: all 0.3s ease;">Projects</span>
                        <span class="filter-tag" style="background-color: white; border: 1px solid rgba(0,0,0,0.05); padding: 10px 20px; border-radius: 25px; font-weight: 500; font-size: 14px; color: var(--text-dark); cursor: pointer; transition: all 0.3s ease;">Lifestyle</span>
                    </div>
                    <div class="mobile-scroll-indicator-blog" id="blog-scroll-indicator">
                        <i class="fa-solid fa-chevron-right" style="animation: pulse-horizontal-blog 1.5s infinite;"></i>
                    </div>
                </div>
            </div>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const blogFilterContainer = document.getElementById('blog-category-filters');
                    const blogIndicator = document.getElementById('blog-scroll-indicator');
                    
                    if(blogFilterContainer && blogIndicator) {
                        const updateBlogIndicator = () => {
                            const isAtEnd = blogFilterContainer.scrollLeft + blogFilterContainer.clientWidth >= blogFilterContainer.scrollWidth - 5;
                            const hasOverflow = blogFilterContainer.scrollWidth > blogFilterContainer.clientWidth;
                            
                            if(isAtEnd || !hasOverflow) {
                                blogIndicator.style.opacity = '0';
                                blogIndicator.style.pointerEvents = 'none';
                            } else {
                                blogIndicator.style.opacity = '1';
                                blogIndicator.style.pointerEvents = 'auto';
                            }
                        };
                        
                        blogFilterContainer.addEventListener('scroll', updateBlogIndicator);
                        window.addEventListener('resize', updateBlogIndicator);
                        
                        setTimeout(updateBlogIndicator, 100);
                    }
                });
            </script>
            
            <div class="blog-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-bottom: 50px;">
                <?php
                $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                if ($page < 1) $page = 1;
                $limit = 12;
                $offset = ($page - 1) * $limit;

                // Count total
                $count_query = "SELECT COUNT(id) as total FROM blogs WHERE status = 'Published'";
                $count_result = $conn->query($count_query);
                $total_posts = $count_result->fetch_assoc()['total'];
                $total_pages = ceil($total_posts / $limit);

                $blog_query = "SELECT * FROM blogs WHERE status = 'Published' ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
                $blog_result = $conn->query($blog_query);
                
                if($blog_result && $blog_result->num_rows > 0):
                    while($row = $blog_result->fetch_assoc()):
                        // Format date
                        $date = date("d F Y", strtotime($row['created_at']));
                        // Clean up excerpt
                        $excerpt = substr(strip_tags($row['content']), 0, 80) . '...';
                        $image_path = !empty($row['image']) ? 'uploads/blogs/' . htmlspecialchars($row['image']) : 'assets/images/placeholder.jpg';
                ?>
                <div class="blog-card">
                    <div class="blog-img-wrapper">
                        <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" style="width: 100%; height: 250px; object-fit: cover;">
                        <div class="blog-tags">
                            <span><?php echo $date; ?></span>
                            <span><?php echo htmlspecialchars($row['category']); ?></span>
                        </div>
                    </div>
                    <div class="blog-content">
                        <h3><?php echo htmlspecialchars(substr($row['title'], 0, 50)); ?><?php echo strlen($row['title']) > 50 ? '...' : ''; ?></h3>
                        <p><?php echo htmlspecialchars($excerpt); ?></p>
                        <a href="blog-details.php?slug=<?php echo urlencode($row['slug']); ?>" class="read-more">Read More</a>
                    </div>
                </div>
                <?php 
                    endwhile;
                else:
                ?>
                    <p style="grid-column: 1 / -1; text-align: center; color: var(--text-muted);">No blog posts found.</p>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="page-nav"><i class="fa-solid fa-angle-left"></i></a>
                <?php else: ?>
                    <span class="page-nav disabled" style="opacity:0.5; cursor:not-allowed;"><i class="fa-solid fa-angle-left"></i></span>
                <?php endif; ?>

                <?php
                $start = max(1, $page - 1);
                $end = min($total_pages, $page + 1);
                
                if ($start > 1) {
                    echo '<a href="?page=1" class="page-num">1</a>';
                    if ($start > 2) {
                        echo '<span class="page-dots" style="margin: 0 10px; color: var(--text-muted);">...</span>';
                    }
                }
                
                for ($i = $start; $i <= $end; $i++) {
                    $activeClass = ($i == $page) ? 'active' : '';
                    echo '<a href="?page='.$i.'" class="page-num '.$activeClass.'">'.$i.'</a>';
                }
                
                if ($end < $total_pages) {
                    if ($end < $total_pages - 1) {
                        echo '<span class="page-dots" style="margin: 0 10px; color: var(--text-muted);">...</span>';
                    }
                    echo '<a href="?page='.$total_pages.'" class="page-num">'.$total_pages.'</a>';
                }
                ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="page-nav"><i class="fa-solid fa-angle-right"></i></a>
                <?php else: ?>
                    <span class="page-nav disabled" style="opacity:0.5; cursor:not-allowed;"><i class="fa-solid fa-angle-right"></i></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- First Marquee -->


    <!-- CTA Section -->
    <section class="cta-section" style="padding: 100px 0; background-color: var(--bg-white); text-align: center;">
        <div class="container">
            <h2 style="font-size: 42px; margin-bottom: 50px;">The Dream Project: <span class="accent-text" style="font-family: var(--font-accent); font-style: italic; font-weight: 400;">Your<br>Journey Begins Here!</span></h2>
            
            <a href="calculator.php" class="rotating-badge">
                <svg viewBox="0 0 150 150" class="rotating-text">
                    <path id="badge-curve" d="M 25, 75 a 50,50 0 1,1 100,0 a 50,50 0 1,1 -100,0" fill="transparent" />
                    <text font-size="16" letter-spacing="4" fill="var(--text-light)" font-weight="500">
                        <textPath href="#badge-curve">GET ESTIMATE • GET ESTIMATE • </textPath>
                    </text>
                </svg>
                <div class="badge-icon">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </div>
            </a>
        </div>
    </section>

    <!-- Second Marquee -->


</main>

<?php include 'includes/footer.php'; ?>
