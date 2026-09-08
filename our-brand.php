<?php 
$currentPage = 'our-brand';
require_once 'admin/config/db.php';
$pageTitle = "Our Brand & Values | Kalp Interior Design Studio Ranchi";
$pageDescription = "Discover the story and values behind Kalp Interior Design Studio. We are Ranchi's trusted name for premium, innovative, and functional interior design.";
include 'includes/header.php'; 
?>

<style>
    /* Our Brand Page - Shopping Card Style */
    :root {
        --brand-bg: #111111;
        --brand-card-bg: #1a1a1a;
        --brand-border: #333333;
        --brand-gold: #c99a44;
        --brand-text: #f2f2f0;
        --brand-muted: #9c9c9c;
    }

    body {
        background-color: var(--brand-bg);
    }

    .brand-container {
        max-width: 1200px;
        margin: 80px auto 120px;
        padding: 0 5%;
    }

    .brand-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .brand-header h2 {
        font-family: var(--font-primary, 'Archivo Black', sans-serif);
        font-size: clamp(2rem, 4vw, 3rem);
        color: var(--brand-text);
        text-transform: uppercase;
        margin-bottom: 15px;
    }

    .brand-header p {
        color: var(--brand-muted);
        font-family: var(--font-secondary, 'Inter', sans-serif);
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto;
    }

    .brand-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 40px;
    }

    .product-card {
        background-color: var(--brand-card-bg);
        border: 1px solid var(--brand-border);
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.4);
        border-color: var(--brand-gold);
    }

    .product-image {
        width: 100%;
        aspect-ratio: 4 / 3;
        overflow: hidden;
        position: relative;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.05);
    }

    .product-info {
        padding: 30px;
        text-align: center;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-title {
        font-family: var(--font-primary, 'Archivo Black', sans-serif);
        font-size: 1.5rem;
        color: var(--brand-text);
        text-transform: uppercase;
        margin-bottom: 15px;
        letter-spacing: 0.05em;
    }

    .product-desc {
        color: var(--brand-muted);
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 30px;
        flex-grow: 1;
    }

    .product-btn {
        display: inline-block;
        background-color: transparent;
        color: var(--brand-gold);
        border: 1px solid var(--brand-gold);
        padding: 12px 30px;
        border-radius: 30px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-size: 0.85rem;
        text-decoration: none;
        transition: background-color 0.3s ease, color 0.3s ease;
        align-self: center;
    }

    .product-card:hover .product-btn {
        background-color: var(--brand-gold);
        color: #111;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .brand-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }
        .brand-container {
            margin: 60px auto 80px;
        }
    }
</style>

<main>
    <!-- Page Banner -->
    <section class="page-banner">
        <div class="container">
            <h1 class="banner-title">Our Brand</h1>
            <div class="breadcrumbs">
                <a href="index.php">Home</a> <span class="divider">/</span> <span class="current">Our Brand</span>
            </div>
        </div>
    </section>

    <!-- Shopping Cards Section -->
    <section class="brand-container">
        <div class="brand-header">
            <h2>Explore Our Collections</h2>
            <p>Discover our premium selection of modular kitchens and bespoke luxury furniture, crafted for the modern home.</p>
        </div>

        <div class="brand-grid">
            
            <!-- Modular Kitchen Card -->
            <div class="product-card">
                <div class="product-image">
                    <img src="assets/images/modular_kitchen.jpg" alt="Modular Kitchen">
                </div>
                <div class="product-info">
                    <h3 class="product-title">Modular Kitchens</h3>
                    <p class="product-desc">Sleek, highly functional, and fully customized layouts for modern homes.</p>
                    <a href="modular-kitchen-estimate.php" class="product-btn">
                        <i class="fa-solid fa-calculator"></i> Get an Estimate
                    </a>
                </div>
            </div>

            <!-- Premium Furniture Card -->
            <div class="product-card">
                <div class="product-image">
                    <img src="assets/images/luxury_furniture.jpg" alt="Premium Furniture">
                </div>
                <div class="product-info">
                    <h3 class="product-title">Premium Furniture</h3>
                    <p class="product-desc">Elevate your living spaces with our curated selection of bespoke furniture. We combine rich textures with sleek metallic accents to create timeless, comfortable pieces.</p>
                    <a href="shop-furniture.php" class="product-btn"><i class="fa-solid fa-cart-shopping" style="margin-right: 8px;"></i> Shop Now</a>
                </div>
            </div>

        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>
