<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'admin/config/db.php';

// Real login/register logic
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'register') {
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $check = $conn->query("SELECT id FROM shop_users WHERE email='$email'");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO shop_users (name, email, password) VALUES ('$name', '$email', '$password')");
            $user_id = $conn->insert_id;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
        } else {
            $_SESSION['auth_error'] = "Email already exists.";
        }
    } elseif ($_POST['action'] === 'login') {
        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'];
        
        $res = $conn->query("SELECT * FROM shop_users WHERE email='$email'");
        if ($res->num_rows > 0) {
            $user = $res->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
            } else {
                $_SESSION['auth_error'] = "Incorrect password.";
            }
        } else {
            $_SESSION['auth_error'] = "User not found.";
        }
    } elseif ($_POST['action'] === 'logout') {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
    }
}

$currentPage = 'shop-furniture';
include 'includes/header.php'; 

// Fetch Categories
$cat_query = "SELECT * FROM shop_categories WHERE type='category' ORDER BY position ASC LIMIT 8";
$categories = $conn->query($cat_query);

// Sorting Logic
$order_by = "";
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
if ($sort == 'price_low') {
    $order_by = "ORDER BY COALESCE(p.sale_price, p.regular_price) ASC";
} elseif ($sort == 'price_high') {
    $order_by = "ORDER BY COALESCE(p.sale_price, p.regular_price) DESC";
} else {
    $order_by = "ORDER BY p.id DESC"; // Default
}

// Fetch Featured Products
$feat_query = "SELECT p.*, (SELECT image_url FROM shop_product_images WHERE product_id = p.id AND is_primary=1 LIMIT 1) as image FROM shop_products p WHERE is_bestseller = 1 $order_by LIMIT 8";
$featured_products = $conn->query($feat_query);
?>

<style>
    /* Premium Furniture Shop Styles - Redesign */
    :root {
        --brand-green: #0d3b2e;
        --brand-light-green: #1a5944;
        --brand-beige: #f9f6f0;
        --text-dark: #1a1a1a;
        --text-gray: #666666;
        --border-color: #e5e5e5;
        --font-primary: 'Outfit', sans-serif;
    }

    body {
        background-color: #ffffff;
        font-family: var(--font-primary);
        color: var(--text-dark);
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 5%;
    }

    /* Section Spacing */
    .section-padding {
        padding: 80px 0;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 40px;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
    }

    .view-all-link {
        color: var(--text-gray);
        text-decoration: none;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: color 0.3s;
    }
    .view-all-link:hover { color: var(--brand-green); }

    /* Hero Section */
    .hero-section {
        background-color: var(--brand-beige);
        padding: 60px 5%;
        margin-bottom: 40px;
    }

    .hero-inner {
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        gap: 50px;
    }

    .hero-content {
        flex: 1;
    }

    .hero-subtitle {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: var(--text-gray);
        margin-bottom: 15px;
        display: block;
        font-weight: 600;
    }

    .hero-title {
        font-size: 4rem;
        font-weight: 800;
        line-height: 1.1;
        color: var(--brand-green);
        margin-bottom: 25px;
    }

    .hero-desc {
        font-size: 1.1rem;
        color: var(--text-gray);
        margin-bottom: 35px;
        max-width: 450px;
        line-height: 1.6;
    }

    .btn-brand {
        background-color: var(--brand-green);
        color: white;
        padding: 15px 35px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: background-color 0.3s, transform 0.3s;
        border: none;
        cursor: pointer;
    }
    .btn-brand:hover {
        background-color: var(--brand-light-green);
        transform: translateY(-2px);
    }

    .hero-image {
        flex: 1.2;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    
    .hero-image img {
        width: 100%;
        height: auto;
        display: block;
        object-fit: cover;
    }

    /* Shop by Category */
    .category-grid {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 20px;
    }

    .category-item {
        text-align: center;
        text-decoration: none;
        color: var(--text-dark);
    }

    .category-icon-box {
        background-color: #f8f8f8;
        border-radius: 16px;
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        transition: transform 0.3s, background-color 0.3s;
        padding: 20px;
    }

    .category-item:hover .category-icon-box {
        transform: translateY(-5px);
        background-color: var(--brand-beige);
    }

    .category-icon-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .category-name {
        font-size: 0.9rem;
        font-weight: 600;
    }

    /* Featured Products */
    .tabs {
        display: flex;
        gap: 30px;
    }

    .tab-link {
        background: none;
        border: none;
        font-size: 1rem;
        color: var(--text-gray);
        font-weight: 600;
        cursor: pointer;
        padding-bottom: 5px;
        border-bottom: 2px solid transparent;
        transition: all 0.3s;
    }
    .tab-link.active {
        color: var(--brand-green);
        border-bottom-color: var(--brand-green);
    }

    .sort-select {
        padding: 8px 15px;
        border: 1px solid var(--border-color);
        border-radius: 20px;
        font-family: var(--font-primary);
        color: var(--text-dark);
        background: white;
        outline: none;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 500;
    }
    .sort-select:focus {
        border-color: var(--brand-green);
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
    }

    .product-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        transition: box-shadow 0.3s, transform 0.3s;
        position: relative;
    }
    .product-card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transform: translateY(-5px);
    }

    .badge-discount {
        position: absolute;
        top: 15px;
        left: 15px;
        background-color: var(--brand-green);
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        z-index: 2;
    }

    .btn-wishlist {
        position: absolute;
        top: 15px;
        right: 15px;
        background: white;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-gray);
        cursor: pointer;
        z-index: 2;
        transition: color 0.3s, border-color 0.3s;
    }
    .btn-wishlist:hover {
        color: #ef4444;
        border-color: #ef4444;
    }

    .product-img-box {
        aspect-ratio: 4/3;
        background-color: #f8f8f8;
        overflow: hidden;
        position: relative;
    }
    .product-img-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .product-card:hover .product-img-box img {
        transform: scale(1.08);
    }

    .product-info {
        padding: 20px;
    }

    .product-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0 0 5px;
        color: var(--text-dark);
        text-transform: uppercase;
        text-decoration: none;
    }

    .product-rating {
        color: #f59e0b;
        font-size: 0.8rem;
        margin-bottom: 10px;
    }
    .product-rating span {
        color: var(--text-gray);
        margin-left: 5px;
    }

    .product-price {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }
    .price-current {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-dark);
    }
    .price-old {
        font-size: 0.95rem;
        color: var(--text-gray);
        text-decoration: line-through;
    }

    .btn-add-cart {
        display: block;
        width: 100%;
        background-color: transparent;
        color: var(--text-dark);
        border: 1px solid var(--border-color);
        padding: 10px;
        border-radius: 25px;
        text-align: center;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
        box-sizing: border-box;
        position: relative;
        z-index: 10;
        cursor: pointer;
    }
    .btn-add-cart:hover {
        background-color: var(--brand-green);
        color: white;
        border-color: var(--brand-green);
    }

    /* Best Deals Banner */
    .best-deals-banner {
        background: linear-gradient(90deg, var(--brand-green) 0%, var(--brand-light-green) 100%);
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        color: white;
        margin: 40px 0;
    }

    .banner-content {
        padding: 60px;
        flex: 1;
    }
    .banner-content h2 {
        font-size: 2.5rem;
        margin-bottom: 10px;
    }
    .banner-content p {
        font-size: 1.1rem;
        margin-bottom: 30px;
        opacity: 0.9;
    }

    .banner-image {
        flex: 1;
        position: relative;
    }
    .banner-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .discount-circle {
        position: absolute;
        top: 30px;
        right: 30px;
        background: var(--brand-green);
        color: white;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        border: 4px solid white;
    }
    .discount-circle span:first-child { font-size: 0.8rem; }
    .discount-circle span:nth-child(2) { font-size: 1.8rem; line-height: 1; }
    .discount-circle span:last-child { font-size: 0.8rem; }

    .banner-features {
        flex: 1;
        background: white;
        color: var(--text-dark);
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 30px;
    }
    .feature-item {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .feature-icon {
        font-size: 1.8rem;
        color: var(--brand-green);
    }
    .feature-text h4 {
        margin: 0 0 5px;
        font-size: 1rem;
    }
    .feature-text p {
        margin: 0;
        font-size: 0.85rem;
        color: var(--text-gray);
    }

    /* Shop by Room */
    .room-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    .room-card {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        text-decoration: none;
        color: var(--text-dark);
        transition: transform 0.3s;
    }
    .room-card:hover {
        transform: translateY(-5px);
    }
    .room-img {
        aspect-ratio: 16/10;
        overflow: hidden;
    }
    .room-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }
    .room-card:hover .room-img img {
        transform: scale(1.05);
    }
    .room-info {
        padding: 20px;
    }
    .room-info h3 {
        margin: 0 0 5px;
        font-size: 1.2rem;
    }
    .room-info p {
        margin: 0;
        font-size: 0.85rem;
        color: var(--text-gray);
    }

    /* Bottom CTA */
    .bottom-cta {
        background-color: #fdfaf6;
        border-radius: 20px;
        padding: 40px 60px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 80px;
        border: 1px solid #f0ebe1;
    }
    .bottom-cta-info h2 {
        font-size: 1.8rem;
        margin: 0 0 10px;
        color: var(--text-dark);
    }
    .bottom-cta-info p {
        margin: 0;
        color: var(--text-gray);
        font-size: 1.1rem;
    }

    @media (max-width: 1200px) {
        .category-grid { grid-template-columns: repeat(4, 1fr); }
        .product-grid, .room-grid { grid-template-columns: repeat(2, 1fr); }
        .best-deals-banner { flex-direction: column; }
        .banner-image { aspect-ratio: 16/9; }
    }
    @media (max-width: 768px) {
        .hero-inner { flex-direction: column; text-align: center; }
        .hero-title { font-size: 2.5rem; }
        .category-grid { grid-template-columns: repeat(2, 1fr); }
        .product-grid, .room-grid { grid-template-columns: 1fr; }
        .bottom-cta { flex-direction: column; text-align: center; gap: 20px; }
    }
</style>

<!-- Add FontAwesome and Google Fonts if not present in header -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<main>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-inner">
            <div class="hero-content">
                <span class="hero-subtitle">MODERN • STYLISH • COMFORTABLE</span>
                <h1 class="hero-title">Make Your Home<br>More Beautiful</h1>
                <p class="hero-desc">Premium quality furniture for every room. Style, comfort and durability — all in one place.</p>
                <a href="#products" class="btn-brand">Shop Now <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="hero-image">
                <img src="assets/images/furniture_hero.jpg" onerror="this.src='https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=1200&q=80'" alt="Modern Living Room">
            </div>
        </div>
    </section>

    <div class="container">
        
        <!-- Shop by Category -->
        <section class="section-padding" style="padding-top: 0;">
            <div class="section-header">
                <h2 class="section-title">Shop by Category</h2>
                <a href="#" class="view-all-link">View All <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="category-grid">
                <?php 
                if($categories && $categories->num_rows > 0): 
                    while($cat = $categories->fetch_assoc()): 
                        // Fallback image if no image uploaded
                        $img = !empty($cat['image']) ? $cat['image'] : 'https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?auto=format&fit=crop&w=200&q=80';
                ?>
                <a href="#" class="category-item">
                    <div class="category-icon-box" style="padding: 0; overflow: hidden;">
                        <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 16px;">
                    </div>
                    <div class="category-name"><?php echo htmlspecialchars($cat['name']); ?></div>
                </a>
                <?php endwhile; endif; ?>
            </div>
        </section>

        <!-- Featured Products -->
        <section id="products" class="section-padding" style="padding-top: 0;">
            <div class="section-header" style="align-items: center;">
                <h2 class="section-title">Featured Products</h2>
                <div style="display: flex; gap: 20px; align-items: center;">
                    <div class="tabs" style="margin-right: 20px;">
                        <button class="tab-link active">Best Sellers</button>
                        <button class="tab-link">New Arrivals</button>
                        <button class="tab-link">On Sale</button>
                    </div>
                    <form action="shop-furniture.php#products" method="GET" id="sortForm">
                        <select name="sort" class="sort-select" onchange="document.getElementById('sortForm').submit();">
                            <option value="">Default Sorting</option>
                            <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        </select>
                    </form>
                </div>
            </div>
            
            <div class="product-grid">
                <?php if($featured_products && $featured_products->num_rows > 0): 
                    while($prod = $featured_products->fetch_assoc()): 
                        $discount = 0;
                        if($prod['regular_price'] > $prod['sale_price']) {
                            $discount = round((($prod['regular_price'] - $prod['sale_price']) / $prod['regular_price']) * 100);
                        }
                ?>
                <div class="product-card" style="border-radius: 20px; overflow: hidden; border: 1px solid var(--border-color); transition: transform 0.3s, box-shadow 0.3s; position: relative; background: white; padding-bottom: 25px;">
                    <?php if($discount > 0): ?>
                        <div class="badge-discount" style="position: absolute; top: 15px; left: 15px; background-color: var(--brand-green); color: white; font-size: 0.85rem; font-weight: 700; padding: 6px 14px; border-radius: 20px; z-index: 2;">-<?php echo $discount; ?>%</div>
                    <?php endif; ?>
                    <button class="btn-wishlist" style="position: absolute; top: 15px; right: 15px; background: white; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--text-dark); font-size: 1.1rem; cursor: pointer; z-index: 2; transition: color 0.3s;"><i class="fa-regular fa-heart"></i></button>
                    
                    <div class="product-img-box" style="aspect-ratio: 4/3; background-color: #f8f8f8; overflow: hidden; position: relative;">
                        <a href="product-details.php?id=<?php echo $prod['id']; ?>">
                            <img src="assets/images/products/<?php echo $prod['image'] ? htmlspecialchars($prod['image']) : 'furniture_sofa.jpg'; ?>" onerror="this.src='https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=400&q=80'" alt="<?php echo htmlspecialchars($prod['name']); ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                        </a>
                    </div>
                    
                    <div class="product-info" style="padding: 25px 25px 0;">
                        <a href="product-details.php?id=<?php echo $prod['id']; ?>" style="text-decoration: none;">
                            <h3 class="product-title" style="font-size: 1.2rem; font-weight: 800; margin: 0 0 10px; color: var(--text-dark); text-transform: uppercase; letter-spacing: 0.5px;"><?php echo htmlspecialchars($prod['name']); ?></h3>
                        </a>
                        <div class="product-rating" style="color: #f59e0b; font-size: 0.9rem; margin-bottom: 15px;">
                            <?php for($r=1; $r<=5; $r++) echo '<i class="fa-solid fa-star"></i>'; ?>
                            <span style="color: var(--text-gray); margin-left: 5px;">(<?php echo $prod['reviews_count'] ? $prod['reviews_count'] : '89'; ?>)</span>
                        </div>
                        <div class="product-price" style="display: flex; align-items: center; gap: 10px; margin-bottom: 25px;">
                            <span class="price-current" style="font-size: 1.5rem; font-weight: 800; color: var(--text-dark);">₹<?php echo number_format($prod['sale_price'] ? $prod['sale_price'] : $prod['regular_price']); ?></span>
                            <?php if($prod['sale_price']): ?>
                                <span class="price-old" style="font-size: 1.1rem; color: var(--text-gray); text-decoration: line-through;">₹<?php echo number_format($prod['regular_price']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div style="padding: 0;">
                            <a href="cart_action.php?action=add&id=<?php echo $prod['id']; ?>" class="btn-add-cart" style="display: flex; align-items: center; justify-content: center; width: 100%; background-color: transparent; color: var(--text-dark); border: 1px solid #e5e5e5; padding: 14px; border-radius: 30px; text-align: center; font-size: 1.1rem; font-weight: 600; text-decoration: none; transition: all 0.3s; box-sizing: border-box; cursor: pointer;"><i class="fa-solid fa-cart-plus" style="margin-right: 8px;"></i> Add to Cart</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; endif; ?>
            </div>
        </section>

        <!-- Best Deals Banner -->
        <section class="best-deals-banner">
            <div class="banner-content">
                <h2>Best Deals<br>For Your Dream Home</h2>
                <p>Up to 40% Off on Selected Furniture</p>
                <a href="#products" class="btn-brand" style="background: white; color: var(--brand-green);">Shop Now <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="banner-image">
                <img src="assets/images/furniture_dining.jpg" onerror="this.src='https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=800&q=80'" alt="Dining Room">
                <div class="discount-circle">
                    <span>UP TO</span>
                    <span>40%</span>
                    <span>OFF</span>
                </div>
            </div>
            <div class="banner-features">
                <div class="feature-item">
                    <i class="fa-solid fa-truck-fast feature-icon"></i>
                    <div class="feature-text">
                        <h4>Free Shipping</h4>
                        <p>On Orders Above ₹10,000</p>
                    </div>
                </div>
                <div class="feature-item">
                    <i class="fa-solid fa-rotate-left feature-icon"></i>
                    <div class="feature-text">
                        <h4>Easy Returns</h4>
                        <p>Within 30 Days</p>
                    </div>
                </div>
                <div class="feature-item">
                    <i class="fa-solid fa-shield-halved feature-icon"></i>
                    <div class="feature-text">
                        <h4>Secure Payments</h4>
                        <p>100% Safe & Secure</p>
                    </div>
                </div>
                <div class="feature-item">
                    <i class="fa-solid fa-headset feature-icon"></i>
                    <div class="feature-text">
                        <h4>24/7 Support</h4>
                        <p>We're Here to Help</p>
                    </div>
                </div>
            </div>
        </section>



        <!-- Bottom CTA -->
        <section class="bottom-cta">
            <div class="bottom-cta-info">
                <h2>Crafted for Better Living</h2>
                <p>Premium furniture. Timeless designs. For every home.</p>
            </div>
            <a href="#products" class="btn-brand">Explore Collection <i class="fa-solid fa-arrow-right"></i></a>
        </section>

    </div>

    <!-- Sticky Cart Button -->
    <?php 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $cart_count = 0;
    if (isset($_SESSION['shop_cart']) && is_array($_SESSION['shop_cart'])) {
        $cart_count = array_sum($_SESSION['shop_cart']);
    }
    ?>
    <style>
        /* User Panel Styles */
        .sticky-user-btn {
            position: fixed;
            bottom: 100px;
            right: 30px;
            background-color: white;
            color: var(--text-dark);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            z-index: 1000;
            transition: transform 0.3s, background-color 0.3s;
            cursor: pointer;
            border: 1px solid var(--border-color);
        }
        .sticky-user-btn:hover {
            transform: translateY(-5px) scale(1.05);
            background-color: var(--brand-beige);
        }

        .user-panel-backdrop {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 10000;
            display: none;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .user-panel-backdrop.active {
            display: block;
            opacity: 1;
        }

        .user-panel {
            position: fixed;
            top: 0; right: -400px; width: 400px; height: 100%;
            background: #f8f8f8;
            z-index: 10001;
            transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            box-shadow: -5px 0 30px rgba(0,0,0,0.1);
            overflow-y: auto;
        }
        .user-panel.active {
            right: 0;
        }

        @media (max-width: 480px) {
            .user-panel { width: 100%; right: -100%; }
        }

        /* Login UI */
        .up-login-section {
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
            position: relative;
        }
        .up-close-btn {
            position: absolute;
            top: 20px; right: 20px;
            background: none; border: none;
            font-size: 1.5rem; cursor: pointer; color: var(--text-dark);
        }
        .up-close-btn.white { color: white; }
        
        .up-login-content h2 { margin-bottom: 10px; font-size: 2rem; }
        .up-login-content p { color: var(--text-gray); margin-bottom: 30px; }
        
        .up-form-group { margin-bottom: 20px; }
        .up-form-group label { display: block; margin-bottom: 8px; font-weight: 500; font-size: 0.9rem; }
        .up-input { 
            width: 100%; padding: 12px 15px; border: 1px solid var(--border-color); 
            border-radius: 8px; font-family: var(--font-primary); box-sizing: border-box;
            outline: none; transition: border-color 0.3s;
        }
        .up-input:focus { border-color: var(--brand-green); }

        /* Logged In UI */
        .up-header {
            background-color: #000;
            color: white;
            padding: 20px 20px 0;
        }
        .up-header-top {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 30px; position: relative;
        }
        .up-brand { margin: 0; font-size: 1.8rem; font-weight: 700; }
        .up-avatar {
            width: 35px; height: 35px; border-radius: 50%;
            background: #333; display: flex; align-items: center; justify-content: center;
            font-weight: 600; color: white; margin-right: 20px;
        }
        
        .up-tabs { display: flex; gap: 20px; }
        .up-tab {
            background: none; border: none; color: #888;
            font-size: 1.1rem; font-weight: 600; padding-bottom: 15px; cursor: pointer;
            border-bottom: 3px solid transparent; transition: all 0.3s;
            font-family: var(--font-primary);
        }
        .up-tab:hover { color: #ddd; }
        .up-tab.active { color: white; border-bottom-color: white; }

        .up-body { padding: 30px 20px; flex: 1; }
        .up-tab-content { display: none; }
        .up-tab-content.active { display: block; }

        .up-section-title {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 15px;
        }
        .up-section-title h3 { margin: 0; font-size: 1.2rem; font-weight: 700; color: var(--text-dark); }
        .up-btn-small {
            background: white; border: 1px solid #ddd; padding: 5px 15px;
            border-radius: 20px; font-weight: 600; font-size: 0.85rem; cursor: pointer;
            font-family: var(--font-primary); color: var(--text-dark);
        }

        .up-card {
            background: white; border-radius: 16px; border: 1px solid #eee;
            overflow: hidden; margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }
        .up-card-row {
            padding: 20px; display: flex; justify-content: space-between; align-items: center;
        }
        .up-label { color: var(--text-gray); font-size: 0.95rem; }
        .up-value { font-weight: 500; font-size: 0.95rem; }

        /* Address List */
        .up-address-item {
            padding: 20px; display: flex; align-items: flex-start; gap: 15px;
            cursor: pointer; transition: background 0.2s;
        }
        .up-address-item:hover { background: #fafafa; }
        .up-address-icon {
            background: #f0f0f0; width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; color: var(--text-dark);
            flex-shrink: 0;
        }
        .up-address-details { flex: 1; }
        .up-address-details h4 { margin: 0 0 5px; font-size: 1rem; display: flex; align-items: center; gap: 10px; }
        .up-address-details h4 span {
            font-size: 0.7rem; font-weight: 700; background: #eee; padding: 2px 8px; border-radius: 10px;
        }
        .up-address-details p { margin: 0; font-size: 0.85rem; color: var(--text-gray); line-height: 1.5; }
        .up-address-arrow { color: #ccc; margin-top: 10px; }
        .up-address-divider { height: 1px; background: #eee; }

        /* Orders Tab */
        .order-card { padding: 20px; display: flex; gap: 20px; align-items: center; }
        .up-order-img {
            width: 80px; height: 80px; border-radius: 10px; overflow: hidden; background: #f5f5f5; flex-shrink: 0;
        }
        .up-order-img img { width: 100%; height: 100%; object-fit: cover; }
        .up-order-info { flex: 1; }
        .up-order-info h3 { margin: 0 0 8px; font-size: 1.2rem; }
        .up-order-meta { font-size: 0.85rem; color: var(--text-gray); display: flex; gap: 5px; flex-wrap: wrap; }
        .up-btn-icon {
            background: white; border: 1px solid #ddd; width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-dark);
        }

        .up-empty-state { text-align: center; padding: 50px 20px; color: var(--text-gray); }
        .up-empty-state i { font-size: 3rem; margin-bottom: 15px; opacity: 0.5; }

        .up-btn-logout {
            background: none; border: none; color: #e11d48; font-weight: 600;
            font-size: 1rem; cursor: pointer; padding: 10px 20px; font-family: var(--font-primary);
        }

        .up-footer {
            padding: 30px 20px; border-top: 1px solid #eee; display: flex; flex-wrap: wrap; gap: 15px; justify-content: center;
        }
        .up-footer a { color: #d81b60; text-decoration: none; font-size: 0.85rem; font-weight: 500; }
        .up-footer a:hover { text-decoration: underline; }

        .sticky-cart-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: var(--brand-green);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 10px 25px rgba(13, 59, 46, 0.4);
            text-decoration: none;
            z-index: 1000;
            transition: transform 0.3s, background-color 0.3s;
        }
        .sticky-cart-btn:hover {
            transform: translateY(-5px) scale(1.05);
            background-color: var(--brand-light-green);
            color: white;
        }
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ef4444;
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }
    </style>
    <a href="checkout.php" class="sticky-cart-btn">
        <i class="fa-solid fa-cart-shopping"></i>
        <?php if ($cart_count > 0): ?>
            <span class="cart-badge"><?php echo $cart_count; ?></span>
        <?php endif; ?>
    </a>

    <!-- User Panel -->
    <button class="sticky-user-btn" onclick="openUserPanel()">
        <i class="fa-regular fa-user"></i>
    </button>

    <div class="user-panel-backdrop" id="userPanelBackdrop" onclick="closeUserPanel()"></div>
    <div class="user-panel" id="userPanel">
        <?php if(!isset($_SESSION['user_id'])): ?>
        <?php include 'includes/components/user-login.php'; ?>
        <?php else: ?>
        <?php include 'includes/components/user-dashboard.php'; ?>
        <?php endif; ?>
    </div>

    <script>
        function openUserPanel() {
            document.getElementById('userPanel').classList.add('active');
            document.getElementById('userPanelBackdrop').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeUserPanel() {
            document.getElementById('userPanel').classList.remove('active');
            document.getElementById('userPanelBackdrop').classList.remove('active');
            document.body.style.overflow = '';
        }

        function switchUpTab(tabId) {
            // Remove active class from all tabs
            document.querySelectorAll('.up-tab').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.up-tab-content').forEach(el => el.classList.remove('active'));
            
            // Add active class to clicked tab
            document.getElementById('tab-' + tabId).classList.add('active');
            document.getElementById('content-' + tabId).classList.add('active');
        }

        <?php if(isset($_POST['action'])): ?>
        // Keep panel open after login/logout/register action
        window.addEventListener('DOMContentLoaded', () => {
            openUserPanel();
        });
        <?php endif; ?>
    </script>
</main>

<?php include 'includes/footer.php'; ?>
