<section class="news-offers-section" style="padding: var(--section-padding); background-color: var(--bg-light);">
    <div class="container">
        <div class="section-header text-center" style="margin-bottom: 50px; display: flex; flex-direction: column; align-items: center;">
            <div class="section-subtitle" style="justify-content: center; color: var(--accent-color);">
                WHAT'S NEW AT KALP INTERIOR DESIGN STUDIO
            </div>
            <h2 class="section-title" style="margin-bottom: 15px; text-transform: none;">
                News, Offers & Notifications
            </h2>
            <p style="color: var(--text-muted); font-size: 1.1rem; max-width: 600px; margin: 0 auto;">
                Stay updated with our latest news, exclusive offers and important notifications.
            </p>
        </div>

        <div class="news-tabs-wrapper" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; border-bottom: 1px solid #e0e0e0; padding-bottom: 15px;">
            <div class="news-tabs" style="display: flex; gap: 15px;">
                <button class="news-tab active" data-target="all">
                    ALL
                </button>
                <button class="news-tab" data-target="offers">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                    OFFERS
                </button>
                <button class="news-tab" data-target="news">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    NEWS
                </button>
                <button class="news-tab" data-target="notifications">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    NOTIFICATIONS
                </button>
            </div>
            <a href="news-offers.php" class="view-all-link" style="color: var(--text-dark); font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 5px; position: relative; z-index: 50; cursor: pointer;">
                View All <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
        </div>

        <div class="news-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">
            <?php
            // Ensure DB connection is available
            if (!isset($conn)) {
                require_once __DIR__ . '/../../admin/config/db.php';
            }
            
            // Fetch published items
            $no_query = "SELECT * FROM news_offers WHERE status = 'Published' ORDER BY created_at DESC LIMIT 3";
            $no_result = $conn->query($no_query);

            if ($no_result && $no_result->num_rows > 0) {
                while ($item = $no_result->fetch_assoc()) {
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
            <div class="news-card" data-category="<?php echo $cat; ?>" style="background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); position: relative;">
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
                    <a href="news-details.php?slug=<?php echo urlencode($item['slug']); ?>" style="color: var(--accent-color); font-weight: 600; display: flex; align-items: center; gap: 5px; font-size: 0.95rem;">
                        <?php echo $linkText; ?> <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                </div>
            </div>
            <?php 
                }
            } else {
                echo "<p style='grid-column: 1 / -1; text-align: center; color: var(--text-muted);'>No items found.</p>";
            }
            ?>
        </div>
    </div>
</section>

<style>

.news-tab {
    background: transparent;
    border: none;
    color: var(--text-muted);
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 8px;
    transition: all 0.3s ease;
}
.news-tab:hover {
    color: var(--text-dark);
}
.news-tab.active {
    background: var(--text-dark);
    color: white;
}
.news-tab.active:hover {
    color: white;
}
.news-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.news-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.1) !important;
}

@media (max-width: 991px) {
    .news-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}
@media (max-width: 767px) {
    .news-grid {
        grid-template-columns: 1fr !important;
    }
    .news-tabs-wrapper {
        flex-direction: column;
        gap: 20px;
        align-items: flex-start !important;
    }
    .news-tabs {
        flex-wrap: wrap;
    }
    .section-title {
        font-size: 2.5rem !important;
        text-align: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.news-tab');
    const cards = document.querySelectorAll('.news-card');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            tabs.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');

            const target = this.getAttribute('data-target');

            cards.forEach(card => {
                if (target === 'all' || card.getAttribute('data-category') === target) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>
