<?php 
require_once 'admin/config/db.php';

if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    header("Location: blog.php");
    exit;
}

$slug = $_GET['slug'];
$stmt = $conn->prepare("SELECT * FROM blogs WHERE slug = ? AND status = 'Published'");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: blog.php");
    exit;
}

$post = $result->fetch_assoc();
$stmt->close();

$currentPage = 'blog';
$pageTitle = $post['title'];
include 'includes/header.php'; 

// Format data
$date = date("d F Y", strtotime($post['created_at']));
$image_path = !empty($post['image']) ? 'uploads/blogs/' . htmlspecialchars($post['image']) : 'assets/images/placeholder.jpg';
$tags = !empty($post['tags']) ? explode(',', $post['tags']) : [];

// Extract headings for TOC and inject IDs
$toc_headings = [];
$post['content'] = preg_replace_callback(
    '/<(h[23])([^>]*)>(.*?)<\/\1>/i',
    function($matches) use (&$toc_headings) {
        $tag = strtolower($matches[1]);
        $attributes = $matches[2];
        $text = $matches[3];
        $clean_text = strip_tags($text);
        $id = 'heading-' . count($toc_headings);
        
        $toc_headings[] = [
            'tag' => $tag,
            'text' => $clean_text,
            'id' => $id
        ];
        
        // Return heading with injected ID
        return "<{$tag} id=\"{$id}\"{$attributes}>{$text}</{$tag}>";
    },
    $post['content']
);

// Fix relative image paths from the editor
$post['content'] = str_replace('../uploads/', 'uploads/', $post['content']);

// Fetch all categories for the sidebar
$categories_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$all_categories = [];
if ($categories_result && $categories_result->num_rows > 0) {
    while($cat = $categories_result->fetch_assoc()) {
        $all_categories[] = $cat;
    }
}
?>

<main>
    <!-- Page Banner -->
    <section class="page-banner">
        <div class="container">
            <h1 class="banner-title">Blog</h1>
            <div class="breadcrumbs">
                <a href="index">Home</a> <span class="divider">/</span> <span class="current">Blog Details</span>
            </div>
        </div>
    </section>

    <!-- Blog Details Content -->
    <section class="blog-details-section" style="padding: 80px 0; background-color: var(--bg-white);">
        <div class="container" style="max-width: 1100px;">
            
            <!-- Hero Header -->
            <div class="blog-hero" style="text-align: center; margin-bottom: 50px;">
                <div class="blog-feature-wrapper" style="margin-bottom: 30px;">
                    <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" style="width: 100%; height: 500px; object-fit: cover; border-radius: 80px 20px 80px 20px; display: block;">
                </div>
                
                <div class="blog-header-tags" style="display: flex; justify-content: center; gap: 10px; margin-bottom: 25px;">
                    <span class="tag-pill"><?php echo htmlspecialchars($post['category']); ?></span>
                    <?php foreach($tags as $tag): if(trim($tag) != ''): ?>
                        <span class="tag-pill"><?php echo htmlspecialchars(trim($tag)); ?></span>
                    <?php endif; endforeach; ?>
                </div>
                
                <h1 style="font-size: 36px; line-height: 1.3; margin-bottom: 25px; max-width: 900px; margin-left: auto; margin-right: auto;"><?php echo htmlspecialchars($post['title']); ?></h1>
                
                <div class="author-meta" style="display: flex; align-items: center; justify-content: center; gap: 15px;">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--accent-color); color: var(--text-dark); display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold;">
                        <?php echo substr(htmlspecialchars($post['author']), 0, 1); ?>
                    </div>
                    <div style="text-align: left;">
                        <div style="font-weight: 600; font-size: 14px; color: var(--text-dark);">Written by <?php echo htmlspecialchars($post['author']); ?></div>
                        <div style="font-size: 13px; color: var(--text-muted);"><?php echo $date; ?></div>
                    </div>
                </div>
            </div>

            <!-- Content Grid Layout -->
            <div class="blog-content-layout" style="display: grid; grid-template-columns: 80px 1fr 300px; gap: 40px;">
                
                <!-- Left Sidebar (Share) -->
                <div class="blog-share-sidebar">
                    <div style="position: sticky; top: 100px;">
                        <h4 style="font-size: 12px; letter-spacing: 2px; color: var(--text-muted); margin-bottom: 20px; text-transform: uppercase;">Share</h4>
                        <div class="share-icons-vertical" style="display: flex; flex-direction: column; gap: 15px;">
                            <a href="#" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.location.href), 'facebook-share', 'width=800,height=600'); return false;" class="share-icon" title="Share on Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#" onclick="window.open('https://twitter.com/intent/tweet?url=' + encodeURIComponent(window.location.href) + '&text=' + encodeURIComponent(document.title), 'twitter-share', 'width=800,height=600'); return false;" class="share-icon" title="Share on Twitter"><i class="fa-brands fa-twitter"></i></a>
                            <a href="#" onclick="window.open('https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(window.location.href), 'linkedin-share', 'width=800,height=600'); return false;" class="share-icon" title="Share on LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <style>
                    .blog-main-content h1, .blog-main-content h2, .blog-main-content h3 { font-family: 'League Spartan', sans-serif; font-weight: 700; margin-top: 1.5em; margin-bottom: 0.5em; }
                    .blog-main-content p { margin-bottom: 15px; }
                    .blog-main-content .drop-cap-paragraph::first-letter {
                        float: left;
                        width: 45px;
                        height: 45px;
                        background-color: var(--accent-color);
                        color: #000;
                        border-radius: 50%;
                        font-size: 24px;
                        font-weight: 700;
                        margin-right: 15px;
                        margin-top: 5px;
                        line-height: 45px;
                        text-align: center;
                    }
                    .blog-main-content blockquote.also-read-block {
                        background: #395244;
                        padding: 30px;
                        border-radius: 10px;
                        margin: 35px 0;
                        color: #fff;
                        border-left: none;
                    }
                    .blog-main-content blockquote.also-read-block::before {
                        content: "Also Read :\A";
                        white-space: pre;
                        color: var(--accent-color);
                        font-weight: 600;
                        display: block;
                        margin-bottom: 5px;
                    }
                    .blog-main-content .image-grid-2col {
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 20px;
                        margin: 35px 0;
                    }
                    .blog-main-content .image-grid-2col img {
                        width: 100%;
                        border-radius: 10px;
                        object-fit: cover;
                    }
                    .blog-main-content ul.custom-bullet-list {
                        list-style: none;
                        padding-left: 0;
                    }
                    .blog-main-content ul.custom-bullet-list li {
                        position: relative;
                        padding-left: 25px;
                        margin-bottom: 10px;
                    }
                    .blog-main-content ul.custom-bullet-list li::before {
                        content: "";
                        position: absolute;
                        left: 0;
                        top: 8px;
                        width: 10px;
                        height: 10px;
                        background-color: var(--accent-color);
                        border-radius: 50%;
                    }

                    /* Sidebar & Header UI Styles to match layout */
                    .tag-pill {
                        background-color: var(--accent-color);
                        color: var(--text-dark);
                        font-size: 11px;
                        font-weight: 700;
                        text-transform: uppercase;
                        padding: 6px 15px;
                        border-radius: 20px;
                        display: inline-block;
                    }
                    
                    .share-icon {
                        width: 40px;
                        height: 40px;
                        border-radius: 50%;
                        background-color: #222;
                        color: #fff;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        text-decoration: none;
                        font-size: 16px;
                        transition: 0.3s;
                    }
                    .share-icon:hover {
                        background-color: var(--accent-color);
                        color: #000;
                    }

                    .widget-title {
                        font-size: 13px;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                        font-weight: 700;
                        margin-bottom: 20px;
                    }

                    .category-pills {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 8px;
                    }
                    .category-pills a {
                        display: inline-block;
                        padding: 8px 16px;
                        background-color: #f9f9f9;
                        border: 1px solid #eee;
                        color: #555;
                        font-size: 11px;
                        text-decoration: none;
                        border-radius: 20px;
                        transition: 0.3s;
                    }
                    .category-pills a:hover {
                        background-color: var(--accent-color);
                        color: #000;
                        border-color: var(--accent-color);
                    }

                    .toc-list {
                        list-style: none;
                        padding: 0;
                        margin: 0;
                    }
                    .toc-list li {
                        margin-bottom: 12px;
                    }
                    .toc-list li a {
                        color: #666;
                        text-decoration: none;
                        font-size: 13px;
                        transition: 0.3s;
                    }
                    .toc-list li a:hover {
                        color: var(--accent-color);
                    }
                    @media (max-width: 991px) {
                        .toc-widget {
                            display: none !important;
                        }
                    }
                </style>
                <div class="blog-main-content">
                    <?php echo $post['content']; ?>
                </div>

                <!-- Right Sidebar -->
                <div class="blog-right-sidebar">
                    <div style="position: sticky; top: 100px;">
                        
                        <div class="sidebar-widget">
                            <h4 class="widget-title">Filter by Categories</h4>
                            <div class="category-pills">
                                <?php foreach($all_categories as $cat): ?>
                                    <a href="blog.php?category=<?php echo urlencode($cat['name']); ?>"><?php echo htmlspecialchars($cat['name']); ?></a>
                                <?php endforeach; ?>
                                <?php if(empty($all_categories)): ?>
                                    <span class="text-muted" style="font-size: 13px;">No categories found.</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="sidebar-widget toc-widget">
                            <h4 class="widget-title">Table of Content</h4>
                            <ul class="toc-list">
                                <?php foreach($toc_headings as $heading): ?>
                                    <li style="margin-left: <?php echo $heading['tag'] == 'h3' ? '15px' : '0'; ?>">
                                        <a href="#<?php echo $heading['id']; ?>">
                                            <i class="fa-solid fa-angle-right" style="margin-right: 8px; font-size: 10px; color: var(--accent-color);"></i>
                                            <?php echo $heading['text']; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                                <?php if(empty($toc_headings)): ?>
                                    <li><span class="text-muted">No headings found.</span></li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="sidebar-promo" style="background-image: url('assets/images/sidebar_promo_bg.png');">
                            <div class="promo-overlay"></div>
                            <div class="promo-content">
                                <h4>— Get Estimate</h4>
                                <h3>Celebrate <span class="accent-text" style="font-family: var(--font-accent); font-style: italic; font-weight: 400; color: var(--accent-color);">Your Dream<br>Project</span> with Our Expertise</h3>
                                <a href="calculator.php" class="btn-primary" style="padding: 10px 25px; font-size: 13px;">Get Estimate</a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- First Marquee -->


    <!-- Related News & Blog Section -->
    <section class="related-blog-section" style="padding: 80px 0; background-color: var(--bg-white);">
        <div class="container">
            <div class="text-center" style="margin-bottom: 50px; text-align: center;">
                <p class="section-subtitle" style="justify-content: center;">RELATED NEWS & BLOG</p>
                <h2 class="section-title">Latest Related <span class="accent-text" style="font-family: var(--font-accent); font-style: italic; font-weight: 400;">News & Blogs</span></h2>
            </div>
            
            <div class="blog-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">
                <?php
                $current_id = isset($post['id']) ? (int)$post['id'] : 0;
                $related_query = "SELECT * FROM blogs WHERE status = 'Published' AND id != $current_id ORDER BY created_at DESC LIMIT 3";
                $related_result = $conn->query($related_query);

                if ($related_result && $related_result->num_rows > 0):
                    while($rel_blog = $related_result->fetch_assoc()):
                        $rel_date = date("d F Y", strtotime($rel_blog['created_at']));
                        $rel_image = !empty($rel_blog['image']) ? 'uploads/blogs/' . htmlspecialchars($rel_blog['image']) : 'assets/images/placeholder.jpg';
                        
                        $rel_tags = !empty($rel_blog['tags']) ? explode(',', $rel_blog['tags']) : ['Blog'];
                        $rel_primary_tag = trim($rel_tags[0]);
                        
                        $rel_desc = !empty($rel_blog['excerpt']) ? $rel_blog['excerpt'] : substr(strip_tags($rel_blog['content']), 0, 80) . '...';
                        $rel_title = strlen($rel_blog['title']) > 50 ? substr($rel_blog['title'], 0, 47) . '...' : $rel_blog['title'];
                ?>
                <div class="blog-card">
                    <div class="blog-img-wrapper">
                        <a href="blog-details.php?slug=<?php echo urlencode($rel_blog['slug']); ?>" style="display: block; height: 100%;">
                            <img src="<?php echo $rel_image; ?>" alt="<?php echo htmlspecialchars($rel_blog['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </a>
                        <div class="blog-tags">
                            <span><?php echo $rel_date; ?></span>
                            <span><?php echo htmlspecialchars($rel_primary_tag); ?></span>
                        </div>
                    </div>
                    <div class="blog-content">
                        <h3><a href="blog-details.php?slug=<?php echo urlencode($rel_blog['slug']); ?>" style="color: inherit; text-decoration: none;"><?php echo htmlspecialchars($rel_title); ?></a></h3>
                        <p><?php echo htmlspecialchars($rel_desc); ?></p>
                        <a href="blog-details.php?slug=<?php echo urlencode($rel_blog['slug']); ?>" class="read-more">Read More</a>
                    </div>
                </div>
                <?php 
                    endwhile; 
                else: 
                ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 30px;">
                        <p style="color: var(--text-muted);">No related blogs found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Second Marquee -->

    
    <!-- Contact Form -->
    <?php include 'includes/components/contact.php'; ?>

</main>

<?php include 'includes/footer.php'; ?>
