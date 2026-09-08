<?php 
$currentPage = 'shop-furniture';
require_once 'admin/config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id <= 0) {
    header("Location: shop-furniture.php");
    exit;
}

// Fetch Product
$query = "SELECT p.*, c.name as category_name, 
          (SELECT image_url FROM shop_product_images WHERE product_id = p.id AND is_primary=1 LIMIT 1) as main_image 
          FROM shop_products p 
          LEFT JOIN shop_categories c ON p.category_id = c.id 
          WHERE p.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if(!$product) {
    header("Location: shop-furniture.php");
    exit;
}

// Fetch All Product Images for Gallery
$gallery_query = "SELECT image_url, is_primary FROM shop_product_images WHERE product_id = ? ORDER BY is_primary DESC, id ASC";
$g_stmt = $conn->prepare($gallery_query);
$g_stmt->bind_param("i", $id);
$g_stmt->execute();
$gallery_result = $g_stmt->get_result();
$gallery_images = [];
while($row = $gallery_result->fetch_assoc()) {
    $gallery_images[] = $row['image_url'];
}

// If no gallery images, use the main image or default
if (empty($gallery_images)) {
    $gallery_images[] = $product['main_image'] ? $product['main_image'] : 'furniture_sofa.jpg';
}

include 'includes/header.php'; 
?>

<style>
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

    .pd-container {
        max-width: 1300px;
        margin: 60px auto 100px;
        padding: 0 5%;
    }

    .breadcrumb {
        font-size: 0.9rem;
        color: var(--text-gray);
        margin-bottom: 40px;
    }
    .breadcrumb a {
        color: var(--text-dark);
        text-decoration: none;
        transition: color 0.3s;
    }
    .breadcrumb a:hover {
        color: var(--brand-green);
    }
    .breadcrumb span { margin: 0 10px; }

    .product-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
    }

    .pd-image-wrapper {
        display: flex;
        flex-direction: column;
        gap: 20px;
        position: sticky;
        top: 100px;
        align-self: start;
    }
    .pd-image-col {
        background: transparent;
        border-radius: 8px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        aspect-ratio: 4/3;
        overflow: hidden;
    }
    .pd-image-col img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .pd-gallery {
        display: flex;
        gap: 15px;
        overflow-x: auto;
        padding-bottom: 5px;
    }
    .pd-gallery-thumb {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border-color 0.3s;
    }
    .pd-gallery-thumb.active {
        border-color: #df7b3e;
    }
    .pd-gallery-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .pd-discount {
        position: absolute;
        top: 30px;
        left: 30px;
        background: var(--brand-green);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.9rem;
    }
    .pd-wishlist {
        position: absolute;
        top: 30px;
        right: 30px;
        background: white;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        color: var(--text-dark);
        font-size: 1.2rem;
        cursor: pointer;
        transition: 0.3s;
    }
    .pd-wishlist:hover { color: #ef4444; }

    .pd-info-col {
        padding-top: 20px;
    }

    .pd-category {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: var(--text-gray);
        font-weight: 600;
        margin-bottom: 10px;
    }

    .pd-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0 0 15px;
        line-height: 1.2;
    }

    .pd-rating {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 25px;
        color: #f59e0b;
        font-size: 1rem;
    }
    .pd-rating span {
        color: var(--text-gray);
        font-size: 0.9rem;
    }

    .pd-price {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 30px;
    }
    .pd-current-price {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-dark);
    }
    .pd-old-price {
        font-size: 1.2rem;
        color: var(--text-gray);
        text-decoration: line-through;
    }

    .pd-desc {
        font-size: 1.05rem;
        color: var(--text-gray);
        line-height: 1.7;
        margin-bottom: 20px;
    }

    .pd-actions-wrapper {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 40px;
        padding-top: 20px;
    }

    .qty-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .qty-label {
        font-size: 1.1rem;
        color: var(--text-gray);
    }
    .qty-select {
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 8px 15px;
        font-size: 1.1rem;
        outline: none;
        background: white;
        min-width: 70px;
        font-family: inherit;
        cursor: pointer;
    }

    .tax-text {
        font-size: 0.9rem;
        color: var(--text-gray);
    }

    .btn-add-huge {
        background: #df7b3e; /* Orange from reference */
        color: white;
        border: none;
        border-radius: 30px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        padding: 12px 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: 0.3s;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .btn-add-huge:hover {
        background: #c6662e;
        transform: translateY(-2px);
    }

    .qty-selector {
        display: flex;
        align-items: center;
        border: 1px solid var(--border-color);
        border-radius: 30px;
        padding: 5px 15px;
        background: white;
    }
    .qty-btn {
        background: none;
        border: none;
        font-size: 1.2rem;
        color: var(--text-dark);
        cursor: pointer;
        padding: 5px 10px;
    }
    .qty-input {
        width: 40px;
        text-align: center;
        border: none;
        font-size: 1.1rem;
        font-weight: 600;
        outline: none;
        font-family: inherit;
    }

    .btn-add-huge {
        flex: 1;
        background: var(--brand-green);
        color: white;
        border: none;
        border-radius: 30px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: 0.3s;
        text-decoration: none;
    }
    .btn-add-huge:hover {
        background: var(--brand-light-green);
        transform: translateY(-2px);
    }

    .pd-meta {
        border-top: 1px solid var(--border-color);
        padding-top: 30px;
    }
    .pd-meta p {
        margin: 5px 0;
        font-size: 0.95rem;
        color: var(--text-gray);
    }
    .pd-meta span {
        font-weight: 600;
        color: var(--text-dark);
        min-width: 100px;
        display: inline-block;
    }

    .features-row {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin: 30px 0 40px;
        padding-bottom: 30px;
        border-bottom: 1px solid var(--border-color);
    }
    .feature-box {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .feature-circle {
        background: #fdf5f1;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #df7b3e;
        font-size: 1.2rem;
    }
    .feature-box p {
        margin: 0;
        font-size: 0.95rem;
        line-height: 1.4;
        color: var(--text-dark);
    }

    .specs-section {
        margin-top: 20px;
    }
    .specs-title {
        font-size: 1.5rem;
        font-weight: 500;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-dark);
    }
    .specs-table {
        width: 100%;
        border-collapse: collapse;
    }
    .specs-table td {
        padding: 12px 0;
        vertical-align: top;
        font-size: 1rem;
    }
    .specs-table td:first-child {
        font-weight: 600;
        color: var(--text-dark);
        width: 35%;
    }
    .specs-table td:nth-child(2) {
        width: 5%;
        color: var(--text-gray);
    }
    .specs-table td:last-child {
        color: var(--text-gray);
    }

    .help-widget {
        margin-top: 40px;
        padding-top: 30px;
        border-top: 1px solid var(--border-color);
    }
    .help-title {
        font-size: 1.5rem;
        font-weight: 500;
        color: var(--text-dark);
        margin-bottom: 25px;
    }
    .help-contact {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .help-icon {
        font-size: 2.2rem;
        color: #df7b3e; 
    }
    .help-subtitle {
        display: block;
        font-size: 1.05rem;
        color: var(--text-gray);
        margin-bottom: 5px;
    }
    .help-phone {
        display: block;
        font-size: 1.6rem;
        font-weight: 500;
        color: var(--text-dark);
        text-decoration: none;
    }

    @media (max-width: 991px) {
        .product-layout { grid-template-columns: 1fr; }
        .pd-image-col { padding: 20px; }
        .features-row { flex-direction: column; gap: 20px; }
        .pd-actions-wrapper { flex-wrap: wrap; }
    }
</style>

<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<main class="pd-container">
    
    <div class="breadcrumb">
        <a href="index.php">Home</a> <span>/</span> <a href="shop-furniture.php">Shop</a> <span>/</span> <?php echo htmlspecialchars($product['category_name'] ?? 'Category'); ?> <span>/</span> <span style="color:var(--text-dark); font-weight:500;"><?php echo htmlspecialchars($product['name']); ?></span>
    </div>

    <div class="product-layout">
        <div class="pd-image-wrapper">
            <div class="pd-image-col">
                <?php 
                    $discount = 0;
                    if($product['regular_price'] > $product['sale_price'] && $product['sale_price'] > 0) {
                        $discount = round((($product['regular_price'] - $product['sale_price']) / $product['regular_price']) * 100);
                    }
                ?>
                <?php if($discount > 0): ?>
                    <div class="pd-discount">-<?php echo $discount; ?>%</div>
                <?php endif; ?>
                
                <div class="pd-wishlist"><i class="fa-regular fa-heart"></i></div>
                
                <img id="mainProductImage" src="assets/images/products/<?php echo htmlspecialchars($gallery_images[0]); ?>" onerror="this.src='https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=800&q=80'" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            
            <?php if (count($gallery_images) > 1): ?>
            <div class="pd-gallery">
                <?php foreach($gallery_images as $index => $img): ?>
                    <div class="pd-gallery-thumb <?php echo $index === 0 ? 'active' : ''; ?>" onclick="changeMainImage(this, 'assets/images/products/<?php echo htmlspecialchars($img); ?>')">
                        <img src="assets/images/products/<?php echo htmlspecialchars($img); ?>" onerror="this.src='https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=200&q=80'">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="pd-info-col">
            <div class="pd-category"><?php echo htmlspecialchars($product['category_name'] ?? 'Furniture'); ?></div>
            <h1 class="pd-title"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div class="pd-rating">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star-half-stroke"></i>
                <span>(<?php echo $product['reviews_count']; ?> Reviews)</span>
            </div>

            <div class="pd-price" style="margin-bottom: 10px;">
                <div class="pd-current-price">₹<?php echo number_format($product['sale_price'] ? $product['sale_price'] : $product['regular_price']); ?></div>
                <?php if($product['sale_price'] > 0): ?>
                    <div class="pd-old-price">₹<?php echo number_format($product['regular_price']); ?></div>
                <?php endif; ?>
            </div>

            <p class="pd-desc">
                <?php echo nl2br(htmlspecialchars($product['description'] ? $product['description'] : 'Experience premium comfort and timeless style with our thoughtfully designed furniture. Built to last with high-quality materials to elevate your living space.')); ?>
            </p>

            <div class="pd-actions-wrapper">
                <div class="qty-wrapper">
                    <span class="qty-label">Qty:</span>
                    <select id="qty" class="qty-select">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                
                <span class="tax-text">(incl. of all taxes)</span>
                
                <a href="#" onclick="window.location.href='cart_action.php?action=add&id=<?php echo $product['id']; ?>&qty=' + document.getElementById('qty').value" class="btn-add-huge">
                    ADD TO CART
                </a>
            </div>

            <div class="features-row">
                <div class="feature-box">
                    <div class="feature-circle"><i class="fa-solid fa-truck-fast"></i></div>
                    <p>Free Delivery<br>& Installation*</p>
                </div>
                <div class="feature-box">
                    <div class="feature-circle"><i class="fa-solid fa-shield-check"></i></div>
                    <p>12 Month<br>Warranty*</p>
                </div>
                <div class="feature-box">
                    <div class="feature-circle"><i class="fa-solid fa-credit-card"></i></div>
                    <p>Safe &<br>Secure Payment</p>
                </div>
            </div>

            <div class="specs-section">
                <h2 class="specs-title">Product Specifications</h2>
                <table class="specs-table">
                    <tr>
                        <td>Material</td>
                        <td>:</td>
                        <td>Teak Wood + Fabric</td>
                    </tr>
                    <tr>
                        <td>Color</td>
                        <td>:</td>
                        <td>Beige</td>
                    </tr>
                    <tr>
                        <td>Seater</td>
                        <td>:</td>
                        <td>1 Seater</td>
                    </tr>
                    <tr>
                        <td>Dimensions (inches)</td>
                        <td>:</td>
                        <td>41 L x 86 W x 76 H</td>
                    </tr>
                    <tr>
                        <td>Dimensions (cm)</td>
                        <td>:</td>
                        <td>104.1 L x 218.4 W x 193 H</td>
                    </tr>
                    <tr>
                        <td>Pack Content</td>
                        <td>:</td>
                        <td>3 - Seater Recliner</td>
                    </tr>
                    <tr>
                        <td>Finish / Texture</td>
                        <td>:</td>
                        <td>High-Quality Smooth, Elegant Fabric Finish</td>
                    </tr>
                </table>
            </div>

            <div class="help-widget">
                <h3 class="help-title">Need Help in Buying?</h3>
                <div class="help-contact">
                    <i class="fa-solid fa-phone-volume help-icon"></i>
                    <div>
                        <span class="help-subtitle">Call Us</span>
                        <a href="tel:+919314444747" class="help-phone">+91-9314444747</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

</main>

<script>
function changeMainImage(thumbElement, imageUrl) {
    // Change main image source
    document.getElementById('mainProductImage').src = imageUrl;
    
    // Update active state on thumbnails
    let thumbs = document.querySelectorAll('.pd-gallery-thumb');
    thumbs.forEach(function(thumb) {
        thumb.classList.remove('active');
    });
    thumbElement.classList.add('active');
}
</script>

<?php include 'includes/footer.php'; ?>
