<?php 
$currentPage = 'news_offers';
include 'includes/header.php'; 
require_once 'admin/config/db.php';
?>

<main>
    <!-- Page Banner -->
    <section class="page-banner">
        <div class="container">
            <h1 class="banner-title">News & Offers</h1>
            <div class="breadcrumbs">
                <a href="index.php">Home</a> <span class="divider">/</span> <span class="current">News & Offers</span>
            </div>
        </div>
    </section>

    <!-- News & Offers Section -->
    <section class="news-offers-page-section" style="padding: 80px 0; background-color: var(--bg-light);">
        <div class="container">
            <div class="text-center" style="margin-bottom: 50px; text-align: center;">
                <p class="section-subtitle" style="justify-content: center;">UPDATES</p>
                <h2 class="section-title">Latest <span class="accent-text" style="font-family: var(--font-accent); font-style: italic; font-weight: 400;">News & Offers</span></h2>
            </div>
            
            <style>
                .no-filter-wrapper { position: relative; width: 100%; }
                .mobile-scroll-indicator-no { display: none; }
                .no-filter-container {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: flex-start;
                    gap: 10px;
                }
                .no-filter-container .filter-tag {
                    white-space: nowrap;
                    display: inline-flex;
                    align-items: center;
                }
                
                @media (max-width: 768px) {
                    .no-filter-container {
                        flex-wrap: nowrap !important;
                        overflow-x: auto;
                        padding-bottom: 15px; 
                        margin-bottom: 10px; 
                        -webkit-overflow-scrolling: touch;
                        scrollbar-width: none; 
                    }
                    .no-filter-container::-webkit-scrollbar {
                        display: none;
                    }
                    .mobile-scroll-indicator-no {
                        display: flex;
                        position: absolute;
                        right: 0;
                        top: 0;
                        height: 42px;
                        width: 60px;
                        background: linear-gradient(to right, rgba(255,255,255,0) 0%, var(--bg-light) 80%);
                        align-items: center;
                        justify-content: flex-end;
                        padding-right: 5px;
                        color: var(--text-muted);
                        pointer-events: none;
                        z-index: 5;
                        transition: opacity 0.3s ease;
                    }
                }
                @keyframes pulse-horizontal-no {
                    0% { transform: translateX(0); opacity: 0.5; }
                    50% { transform: translateX(4px); opacity: 1; }
                    100% { transform: translateX(0); opacity: 0.5; }
                }

                .news-grid {
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 30px;
                }
                @media (max-width: 992px) {
                    .news-grid { grid-template-columns: repeat(2, 1fr); }
                }
                @media (max-width: 768px) {
                    .news-grid { grid-template-columns: 1fr; }
                }

                .news-card {
                    background: white; 
                    border-radius: 20px; 
                    overflow: hidden; 
                    box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
                    position: relative;
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                }
                .news-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 15px 40px rgba(0,0,0,0.1);
                }
                
                .filter-tag {
                    background-color: transparent;
                    border: none;
                    padding: 10px 24px;
                    border-radius: 8px;
                    font-weight: 700;
                    font-size: 14px;
                    color: #555;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    display: inline-flex;
                    align-items: center;
                    gap: 10px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }
                .filter-tag:hover {
                    color: var(--primary-color);
                }
                .filter-tag.active {
                    background-color: var(--primary-color);
                    color: white;
                }
                .filter-tag.active svg,
                .filter-tag.active i {
                    display: none;
                }
            </style>
            
            <div class="blog-filters-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; flex-wrap: wrap; gap: 20px;">
                <div class="no-filter-wrapper">
                    <div class="filter-tags no-filter-container" id="no-category-filters">
                        <span class="filter-tag active" data-filter="all">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                            All
                        </span>
                        <span class="filter-tag" data-filter="offers">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                            Offers
                        </span>
                        <span class="filter-tag" data-filter="news">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            News
                        </span>
                        <span class="filter-tag" data-filter="notifications">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                            Notifications
                        </span>
                    </div>
                    <div class="mobile-scroll-indicator-no" id="no-scroll-indicator">
                        <i class="fa-solid fa-chevron-right" style="animation: pulse-horizontal-no 1.5s infinite;"></i>
                    </div>
                </div>
            </div>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const noFilterContainer = document.getElementById('no-category-filters');
                    const noIndicator = document.getElementById('no-scroll-indicator');
                    
                    if(noFilterContainer && noIndicator) {
                        const updateNoIndicator = () => {
                            const isAtEnd = noFilterContainer.scrollLeft + noFilterContainer.clientWidth >= noFilterContainer.scrollWidth - 5;
                            const hasOverflow = noFilterContainer.scrollWidth > noFilterContainer.clientWidth;
                            
                            if(isAtEnd || !hasOverflow) {
                                noIndicator.style.opacity = '0';
                                noIndicator.style.pointerEvents = 'none';
                            } else {
                                noIndicator.style.opacity = '1';
                                noIndicator.style.pointerEvents = 'auto';
                            }
                        };
                        
                        noFilterContainer.addEventListener('scroll', updateNoIndicator);
                        window.addEventListener('resize', updateNoIndicator);
                        
                        setTimeout(updateNoIndicator, 100);
                    }

                    // Client-side filtering logic
                    const filterTags = document.querySelectorAll('#no-category-filters .filter-tag');
                    const newsCards = document.querySelectorAll('.news-card');

                    filterTags.forEach(tag => {
                        tag.addEventListener('click', function() {
                            // Update active state
                            filterTags.forEach(t => t.classList.remove('active'));
                            this.classList.add('active');

                            const filterValue = this.getAttribute('data-filter');

                            newsCards.forEach(card => {
                                if (filterValue === 'all' || card.getAttribute('data-category') === filterValue) {
                                    card.style.display = 'block';
                                } else {
                                    card.style.display = 'none';
                                }
                            });
                        });
                    });

                    // Read URL parameters for filtering
                    const urlParams = new URLSearchParams(window.location.search);
                    const initialFilter = urlParams.get('filter');
                    
                    if (initialFilter) {
                        const targetTag = document.querySelector(`.filter-tag[data-filter="${initialFilter.toLowerCase()}"]`);
                        if (targetTag) {
                            targetTag.click();
                        }
                    }
                });
            </script>
            
            <div class="news-grid" style="margin-bottom: 50px;">
                <?php
                $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                if ($page < 1) $page = 1;
                $limit = 9;
                $offset = ($page - 1) * $limit;

                // Count total
                $count_query = "SELECT COUNT(id) as total FROM news_offers WHERE status = 'Published'";
                $count_result = $conn->query($count_query);
                $total_items = $count_result->fetch_assoc()['total'];
                $total_pages = ceil($total_items / $limit);

                $no_query = "SELECT * FROM news_offers WHERE status = 'Published' ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
                $no_result = $conn->query($no_query);
                
                if($no_result && $no_result->num_rows > 0):
                    while($item = $no_result->fetch_assoc()):
                        $cat = strtolower($item['category']);
                        $date = date('d M Y', strtotime($item['created_at']));
                        
                        // Style adjustments based on category
                        if ($cat == 'offers') {
                            $badgeBg = 'var(--text-dark)';
                            $badgeColor = 'white';
                            $linkText = 'Explore Offer';
                        } elseif ($cat == 'news') {
                            $badgeBg = 'var(--accent-color)';
                            $badgeColor = 'var(--text-dark)';
                            $linkText = 'Read More';
                        } else { // notifications
                            $badgeBg = '#00a32a'; // Green color for notifications
                            $badgeColor = 'white';
                            $linkText = 'Know More';
                        }

                        $imagePath = !empty($item['image']) ? 'uploads/news/' . htmlspecialchars($item['image']) : 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&q=80&w=800';
                ?>
                <div class="news-card" data-category="<?php echo $cat; ?>">
                    <div class="card-image" style="position: relative; height: 240px;">
                        <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <span class="badge" style="position: absolute; top: 20px; left: 20px; background: <?php echo $badgeBg; ?>; color: <?php echo $badgeColor; ?>; padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform:uppercase;">
                            <?php echo htmlspecialchars($item['category']); ?>
                        </span>
                    </div>
                    <div class="card-content" style="padding: 30px; position: relative;">
                        <?php if(!empty($item['offer_badge_text'])): ?>
                        <div style="position: absolute; top: -35px; right: 20px; width: 75px; height: 75px; background-color: #faebd7; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-align: center; font-weight: 700; color: var(--text-dark); font-size: 0.9rem; line-height: 1.2; box-shadow: 0 5px 15px rgba(0,0,0,0.08); border: 3px solid #fff;">
                            <?php echo str_replace(' ', '<br>', htmlspecialchars($item['offer_badge_text'])); ?>
                        </div>
                        <?php endif; ?>

                        <div class="date" style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 15px; display: flex; align-items: center; gap: 5px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <?php echo $date; ?>
                        </div>
                        <h3 style="font-size: 1.25rem; margin-bottom: 15px; font-weight: 700; color: var(--text-dark); padding-right: <?php echo !empty($item['offer_badge_text']) ? '60px' : '0'; ?>;"><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 25px; line-height: 1.6;"><?php echo htmlspecialchars($item['short_description']); ?></p>
                        <a href="news-details.php?slug=<?php echo urlencode($item['slug']); ?>" style="color: var(--accent-color); font-weight: 600; display: flex; align-items: center; gap: 5px; font-size: 0.95rem; text-decoration: none;">
                            <?php echo $linkText; ?> <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </a>
                    </div>
                </div>
                <?php 
                    endwhile;
                else:
                ?>
                    <p style="grid-column: 1 / -1; text-align: center; color: var(--text-muted);">No news or offers found.</p>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination" style="display: flex; justify-content: center; gap: 10px;">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="page-nav" style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #eaeaea; display: flex; align-items: center; justify-content: center; color: var(--text-dark); text-decoration: none; transition: all 0.3s ease;"><i class="fa-solid fa-angle-left"></i></a>
                <?php endif; ?>
                
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="page-nav <?php echo $i == $page ? 'active' : ''; ?>" style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #eaeaea; display: flex; align-items: center; justify-content: center; color: var(--text-dark); text-decoration: none; transition: all 0.3s ease; <?php echo $i == $page ? 'background-color: var(--accent-color); border-color: var(--accent-color);' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="page-nav" style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #eaeaea; display: flex; align-items: center; justify-content: center; color: var(--text-dark); text-decoration: none; transition: all 0.3s ease;"><i class="fa-solid fa-angle-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
