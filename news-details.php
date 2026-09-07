<?php
require_once 'admin/config/db.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
if (empty($slug)) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM news_offers WHERE slug = ? AND status = 'Published'");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit;
}
$news_item = $result->fetch_assoc();
$news_item['content'] = str_replace('../uploads/', 'uploads/', $news_item['content']);

$currentPage = 'news-details';
$pageTitle = !empty($news_item['meta_title']) ? $news_item['meta_title'] : $news_item['title'];

include 'includes/header.php';

$cat = strtolower($news_item['category']);
$badgeBg = 'var(--text-dark)';
$badgeColor = 'white';
$badgeIcon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>';

if ($cat == 'news') {
    $badgeBg = 'var(--accent-color)';
    $badgeColor = 'var(--text-dark)';
    $badgeIcon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>';
} elseif ($cat == 'notifications') {
    $badgeBg = '#00a32a';
    $badgeColor = 'white';
    $badgeIcon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>';
}
?>
<style>
.news-details-section {
    padding: 20px 0 var(--section-padding);
    background-color: var(--bg-light);
}
.news-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0 30px 0;
    border-bottom: 1px solid #e0e0e0;
    margin-bottom: 40px;
    font-size: 0.95rem;
    color: var(--text-muted);
}
.back-link {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-muted);
    font-weight: 500;
    transition: color 0.3s ease;
}
.back-link:hover {
    color: var(--text-dark);
}
.breadcrumbs strong {
    color: var(--text-dark);
    font-weight: 600;
}
.news-details-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 40px;
}
.main-content {
    background: transparent;
}
.meta-badges {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
}
.meta-badge {
    background: var(--text-dark);
    color: white;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}
.meta-date {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--text-muted);
    font-size: 0.9rem;
    font-weight: 500;
}
.main-title {
    font-family: var(--font-headline);
    font-size: 3rem;
    line-height: 1.2;
    color: var(--text-dark);
    margin-bottom: 15px;
    text-transform: none;
}
.main-subtitle {
    color: var(--text-muted);
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 40px;
    max-width: 90%;
}
.featured-image {
    width: 100%;
    border-radius: 20px;
    margin-bottom: 40px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}
.content-body p {
    color: var(--text-muted);
    font-size: 1.05rem;
    line-height: 1.8;
    margin-bottom: 20px;
}
.whats-included {
    margin-top: 50px;
    margin-bottom: 40px;
}
.whats-included-title {
    font-family: var(--font-headline);
    font-size: 1.5rem;
    color: var(--text-dark);
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
    text-transform: none;
}
.included-list {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px 30px;
    list-style: none;
    padding: 0;
}
.included-list li {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text-muted);
    font-weight: 500;
}
.included-list li svg {
    color: var(--accent-color);
}
.cta-box {
    background: rgba(234, 177, 54, 0.1);
    border-radius: 12px;
    padding: 25px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}
.cta-content {
    display: flex;
    align-items: flex-start;
    gap: 15px;
}
.cta-icon {
    color: var(--accent-color);
    background: white;
    padding: 10px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.cta-text p {
    margin: 0;
    color: var(--text-dark);
    font-size: 0.95rem;
    line-height: 1.5;
}
.sidebar {
    display: flex;
    flex-direction: column;
    gap: 30px;
}
.widget {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.03);
}
.widget-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-dark);
    letter-spacing: 1px;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 25px;
    text-transform: uppercase;
}
.widget-title::before {
    content: '';
    display: block;
    width: 25px;
    height: 2px;
    background: var(--accent-color);
}
.related-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.related-item {
    display: flex;
    gap: 15px;
    align-items: flex-start;
    padding-bottom: 20px;
    border-bottom: 1px solid #f0f0f0;
    text-decoration: none;
    transition: all 0.3s ease;
}
.related-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.related-item:hover .related-title {
    color: var(--accent-color);
}
.related-img {
    width: 80px;
    height: 60px;
    border-radius: 8px;
    object-fit: cover;
}
.related-content {
    flex: 1;
}
.related-date {
    display: flex;
    align-items: center;
    gap: 5px;
    color: var(--text-muted);
    font-size: 0.75rem;
    margin-bottom: 5px;
}
.related-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text-dark);
    line-height: 1.4;
    transition: color 0.3s ease;
}
.related-arrow {
    margin-top: 5px;
    color: var(--accent-color);
}
.category-list {
    list-style: none;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.category-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 15px;
    border-radius: 8px;
    color: var(--text-dark);
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
}
.category-item:hover, .category-item.active {
    background: #fcf8f0; /* very light accent color background */
}
.category-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.category-icon {
    color: var(--accent-color);
}
.estimate-widget {
    position: relative;
    border-radius: 40px 10px 40px 10px;
    overflow: hidden;
    padding: 40px 20px;
    text-align: center;
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 380px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}
.estimate-widget::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: url('https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&q=80&w=600') center/cover no-repeat;
    z-index: 1;
}
.estimate-widget::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(to bottom, rgba(15,15,15,0.7), rgba(15,15,15,0.9));
    z-index: 2;
}
.estimate-content {
    position: relative;
    z-index: 3;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
}
.estimate-subtitle {
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 2px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    text-transform: uppercase;
}
.estimate-subtitle::before {
    content: '';
    width: 25px;
    height: 2px;
    background: white;
}
.estimate-title {
    font-family: var(--font-primary);
    font-size: 1.4rem;
    font-weight: 800;
    line-height: 1.6;
    margin-bottom: 35px;
    text-transform: uppercase;
    text-shadow: 0 2px 10px rgba(0,0,0,0.5);
}
.estimate-title .accent-script {
    font-family: var(--font-accent);
    color: var(--accent-color);
    font-size: 2.8rem;
    text-transform: none;
    font-weight: 400;
    display: inline-block;
    margin: 0 5px;
    line-height: 0.5;
    vertical-align: middle;
}
.estimate-btn {
    background: var(--accent-color);
    color: var(--text-dark);
    font-weight: 600;
    padding: 14px 35px;
    border-radius: 30px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    font-size: 1.05rem;
    box-shadow: 0 5px 15px rgba(234, 177, 54, 0.3);
}
.estimate-btn:hover {
    background: white;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
}

@media (max-width: 991px) {
    .news-details-grid {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 767px) {
    .news-topbar {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    .main-title {
        font-size: 2.2rem;
    }
    .included-list {
        grid-template-columns: 1fr;
    }
    .cta-box {
        flex-direction: column;
        text-align: center;
        align-items: stretch;
    }
    .cta-content {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<main class="news-details-section" style="padding-top: 0;">
    <?php $heroImg = !empty($news_item['image']) ? "uploads/news/" . htmlspecialchars($news_item['image']) : "https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&q=80&w=1600"; ?>
    <section class="news-details-hero" style="background: url('<?php echo $heroImg; ?>') center/cover no-repeat; position: relative; padding: 120px 0; display: flex; align-items: center; justify-content: center; text-align: center; color: white; margin-bottom: 50px;">
        <div style="position: absolute; top:0; left:0; right:0; bottom:0; background: rgba(0,0,0,0.65); z-index: 1;"></div>
        <div class="container" style="position: relative; z-index: 2; padding: 0 20px;">
            <span style="display: inline-flex; align-items: center; gap: 8px; background: <?php echo $badgeBg; ?>; color: <?php echo $badgeColor; ?>; padding: 8px 20px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; letter-spacing: 1px; text-transform:uppercase; margin-bottom: 20px;">
                <?php echo $badgeIcon; ?>
                <?php echo htmlspecialchars($news_item['category']); ?>
            </span>
            <h1 style="font-family: var(--font-headline); font-size: clamp(2.5rem, 5vw, 4rem); line-height: 1.2; margin-bottom: 20px; max-width: 1000px; margin-left: auto; margin-right: auto; text-transform: none;"><?php echo htmlspecialchars($news_item['title']); ?></h1>
            <div style="font-size: 0.95rem; font-weight: 500; letter-spacing: 1px; color: #eee; text-transform: uppercase; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <?php echo strtoupper(date('F d, Y', strtotime($news_item['created_at']))); ?>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="news-details-grid">
            <!-- Left Column: Main Content -->
            <div class="main-content">
                
                <div class="content-body">
                    <?php echo $news_item['content']; ?>
                </div>

            </div>

            <!-- Right Column: Sidebar -->
            <div class="sidebar">
                
                <!-- Related News Widget -->
                <div class="widget">
                    <h4 class="widget-title">RELATED <?php echo strtoupper(htmlspecialchars($news_item['category'])); ?></h4>
                    <div class="related-list">
                        <?php
                        $related_query = $conn->prepare("SELECT * FROM news_offers WHERE category = ? AND id != ? AND status = 'Published' ORDER BY created_at DESC LIMIT 4");
                        $related_query->bind_param("si", $news_item['category'], $news_item['id']);
                        $related_query->execute();
                        $related_res = $related_query->get_result();
                        
                        if ($related_res->num_rows > 0) {
                            while ($related = $related_res->fetch_assoc()) {
                                $relImg = !empty($related['image']) ? "uploads/news/" . htmlspecialchars($related['image']) : "https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=200";
                        ?>
                        <a href="news-details.php?slug=<?php echo urlencode($related['slug']); ?>" class="related-item">
                            <img src="<?php echo $relImg; ?>" alt="<?php echo htmlspecialchars($related['title']); ?>" class="related-img">
                            <div class="related-content">
                                <div class="related-date">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    <?php echo strtoupper(date('d M Y', strtotime($related['created_at']))); ?>
                                </div>
                                <div class="related-title"><?php echo htmlspecialchars($related['title']); ?></div>
                                <div class="related-arrow"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></div>
                            </div>
                        </a>
                        <?php 
                            }
                        } else {
                            echo "<p style='color: var(--text-muted); font-size: 0.9rem;'>No related items found.</p>";
                        }
                        $related_query->close();
                        ?>
                    </div>
                </div>

                <!-- Categories Widget -->
                <div class="widget">
                    <h4 class="widget-title">CATEGORIES</h4>
                    <ul class="category-list">
                        <li>
                            <a href="news-offers.php?filter=all" class="category-item <?php echo $cat == '' ? 'active' : ''; ?>">
                                <div class="category-left">
                                    <svg class="category-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                                    All
                                </div>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </li>
                        <li>
                            <a href="news-offers.php?filter=offers" class="category-item <?php echo $cat == 'offers' ? 'active' : ''; ?>">
                                <div class="category-left">
                                    <svg class="category-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                                    Offers
                                </div>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </li>
                        <li>
                            <a href="news-offers.php?filter=news" class="category-item <?php echo $cat == 'news' ? 'active' : ''; ?>">
                                <div class="category-left">
                                    <svg class="category-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                    News
                                </div>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </li>
                        <li>
                            <a href="news-offers.php?filter=notifications" class="category-item <?php echo $cat == 'notifications' ? 'active' : ''; ?>">
                                <div class="category-left">
                                    <svg class="category-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                                    Notifications
                                </div>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Estimate Widget -->
                <div class="estimate-widget">
                    <div class="estimate-content">
                        <div class="estimate-subtitle">GET ESTIMATE</div>
                        <h3 class="estimate-title">
                            CELEBRATE <span class="accent-script">Your Dream</span><br>
                            <span class="accent-script" style="margin-right: 5px;">Project</span> WITH OUR EXPERTISE
                        </h3>
                        <a href="calculator.php" class="estimate-btn">Get Estimate</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
