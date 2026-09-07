<?php 
$currentPage = 'shop-furniture';
require_once 'admin/config/db.php';
include 'includes/header.php'; 
?>

<style>
    /* Premium Furniture Shop Styles */
    :root {
        --shop-bg: #111111;
        --shop-panel: #1a1a1a;
        --shop-border: #333333;
        --shop-gold: #c99a44;
        --shop-text: #f2f2f0;
        --shop-muted: #9c9c9c;
    }

    body {
        background-color: var(--shop-bg);
    }

    .shop-container {
        max-width: 1400px;
        margin: 60px auto 100px;
        padding: 0 5%;
        display: flex;
        gap: 50px;
        align-items: flex-start;
    }

    /* Sidebar Filters */
    .shop-sidebar {
        width: 280px;
        flex-shrink: 0;
        position: sticky;
        top: 100px;
        background-color: var(--shop-panel);
        padding: 30px;
        border-radius: 12px;
        border: 1px solid var(--shop-border);
    }

    .filter-group {
        margin-bottom: 30px;
    }

    .filter-group:last-child {
        margin-bottom: 0;
    }

    .filter-title {
        color: var(--shop-text);
        font-family: var(--font-primary, 'Archivo Black', sans-serif);
        text-transform: uppercase;
        font-size: 1.1rem;
        margin-bottom: 15px;
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--shop-border);
        padding-bottom: 10px;
    }

    .filter-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .filter-list li {
        margin-bottom: 10px;
    }

    .filter-list label {
        color: var(--shop-muted);
        font-size: 0.95rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: color 0.3s ease;
    }

    .filter-list label:hover {
        color: var(--shop-gold);
    }

    .filter-list input[type="checkbox"] {
        accent-color: var(--shop-gold);
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    /* Product Grid */
    .shop-main {
        flex-grow: 1;
    }

    .shop-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--shop-border);
    }

    .shop-header p {
        color: var(--shop-muted);
        margin: 0;
    }

    .sort-select {
        background-color: var(--shop-panel);
        color: var(--shop-text);
        border: 1px solid var(--shop-border);
        padding: 8px 15px;
        border-radius: 5px;
        font-family: var(--font-secondary, 'Inter', sans-serif);
        outline: none;
        cursor: pointer;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
    }

    .product-card {
        background: var(--shop-panel);
        border: 1px solid var(--shop-border);
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s ease, border-color 0.3s ease;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-5px);
        border-color: var(--shop-gold);
    }

    .product-image {
        position: relative;
        width: 100%;
        aspect-ratio: 4/3;
        overflow: hidden;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.08);
    }

    .product-overlay {
        position: absolute;
        inset: 0;
        background: rgba(17,17,17,0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .product-card:hover .product-overlay {
        opacity: 1;
    }

    .btn-quick-view {
        background-color: white;
        color: black;
        padding: 10px 20px;
        border-radius: 30px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.05em;
        text-decoration: none;
        transform: translateY(20px);
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .product-card:hover .btn-quick-view {
        transform: translateY(0);
    }

    .btn-quick-view:hover {
        background-color: var(--shop-gold);
    }

    .product-info {
        padding: 20px;
        text-align: center;
    }

    .product-category {
        color: var(--shop-gold);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 5px;
        display: block;
    }

    .product-title {
        color: var(--shop-text);
        font-family: var(--font-primary, 'Archivo Black', sans-serif);
        font-size: 1.2rem;
        margin: 0 0 10px;
    }

    .product-price {
        color: var(--shop-muted);
        font-size: 1.1rem;
        font-weight: 500;
        margin-bottom: 20px;
    }

    .btn-add-cart {
        display: block;
        width: 100%;
        background-color: #25D366;
        color: #ffffff;
        border: 1px solid #25D366;
        padding: 12px;
        border-radius: 5px;
        text-transform: uppercase;
        font-weight: 600;
        font-size: 0.85rem;
        letter-spacing: 0.05em;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        text-decoration: none;
        box-sizing: border-box;
    }

    .btn-add-cart:hover {
        background-color: #128C7E;
        border-color: #128C7E;
        color: #ffffff;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .shop-container {
            flex-direction: column;
        }
        .shop-sidebar {
            width: 100%;
            position: static;
        }
    }
</style>

<main>
    <!-- Page Banner -->
    <section class="page-banner">
        <div class="container">
            <h1 class="banner-title">Premium Furniture</h1>
            <div class="breadcrumbs">
                <a href="index.php">Home</a> <span class="divider">/</span> 
                <a href="our-brand.php">Our Brand</a> <span class="divider">/</span> 
                <span class="current">Shop</span>
            </div>
        </div>
    </section>

    <!-- Shop Content -->
    <section class="shop-container">
        <!-- Sidebar Filters -->
        <aside class="shop-sidebar">
            <div class="filter-group">
                <h3 class="filter-title">Categories</h3>
                <ul class="filter-list">
                    <li><label><input type="checkbox" checked> All Furniture (4)</label></li>
                    <li><label><input type="checkbox"> Sofas & Lounges (1)</label></li>
                    <li><label><input type="checkbox"> Chairs (1)</label></li>
                    <li><label><input type="checkbox"> Tables (1)</label></li>
                    <li><label><input type="checkbox"> Beds (1)</label></li>
                </ul>
            </div>
            
            <div class="filter-group">
                <h3 class="filter-title">Material</h3>
                <ul class="filter-list">
                    <li><label><input type="checkbox"> Premium Leather</label></li>
                    <li><label><input type="checkbox"> Italian Marble</label></li>
                    <li><label><input type="checkbox"> Velvet</label></li>
                    <li><label><input type="checkbox"> Solid Oak</label></li>
                </ul>
            </div>
            
            <div class="filter-group">
                <h3 class="filter-title">Price Range</h3>
                <ul class="filter-list">
                    <li><label><input type="checkbox"> Under ₹50,000</label></li>
                    <li><label><input type="checkbox"> ₹50,000 - ₹1,00,000</label></li>
                    <li><label><input type="checkbox"> ₹1,00,000 - ₹2,00,000</label></li>
                    <li><label><input type="checkbox"> Over ₹2,00,000</label></li>
                </ul>
            </div>
        </aside>

        <!-- Main Product Grid -->
        <div class="shop-main">
            <div class="shop-header">
                <p>Showing 1-4 of 4 results</p>
                <select class="sort-select">
                    <option>Default Sorting</option>
                    <option>Sort by Popularity</option>
                    <option>Sort by Latest</option>
                    <option>Sort by Price: Low to High</option>
                    <option>Sort by Price: High to Low</option>
                </select>
            </div>

            <div class="product-grid">
                
                <!-- Product 1 -->
                <div class="product-card">
                    <div class="product-image">
                        <img src="assets/images/products/<?php
                            $files = glob('assets/images/products/furniture_sofa_*.jpg');
                            echo $files ? basename($files[0]) : 'furniture_sofa.jpg';
                        ?>" alt="Emerald Velvet Sofa">
                        <div class="product-overlay">
                            <a href="#" class="btn-quick-view" onclick="event.preventDefault(); alert('Product details mockup!');">Quick View</a>
                        </div>
                    </div>
                    <div class="product-info">
                        <span class="product-category">Sofas & Lounges</span>
                        <h3 class="product-title">Emerald Velvet Sofa</h3>
                        <div class="product-price">₹1,45,000</div>
                        <a href="https://wa.me/919234772288?text=Hello,%20I'm%20interested%20in%20purchasing%20the%20Emerald%20Velvet%20Sofa%20(₹1,45,000)" target="_blank" class="btn-add-cart"><i class="fa-brands fa-whatsapp" style="margin-right: 8px;"></i> Order on WhatsApp</a>
                    </div>
                </div>

                <!-- Product 2 -->
                <div class="product-card">
                    <div class="product-image">
                        <img src="assets/images/products/<?php
                            $files = glob('assets/images/products/furniture_table_*.jpg');
                            echo $files ? basename($files[0]) : 'furniture_table.jpg';
                        ?>" alt="Marble & Gold Coffee Table">
                        <div class="product-overlay">
                            <a href="#" class="btn-quick-view" onclick="event.preventDefault(); alert('Product details mockup!');">Quick View</a>
                        </div>
                    </div>
                    <div class="product-info">
                        <span class="product-category">Tables</span>
                        <h3 class="product-title">Marble Coffee Table</h3>
                        <div class="product-price">₹75,000</div>
                        <a href="https://wa.me/919234772288?text=Hello,%20I'm%20interested%20in%20purchasing%20the%20Marble%20Coffee%20Table%20(₹75,000)" target="_blank" class="btn-add-cart"><i class="fa-brands fa-whatsapp" style="margin-right: 8px;"></i> Order on WhatsApp</a>
                    </div>
                </div>

                <!-- Product 3 -->
                <div class="product-card">
                    <div class="product-image">
                        <img src="assets/images/products/<?php
                            $files = glob('assets/images/products/furniture_chair_*.jpg');
                            echo $files ? basename($files[0]) : 'furniture_chair.jpg';
                        ?>" alt="Dark Leather Accent Chair">
                        <div class="product-overlay">
                            <a href="#" class="btn-quick-view" onclick="event.preventDefault(); alert('Product details mockup!');">Quick View</a>
                        </div>
                    </div>
                    <div class="product-info">
                        <span class="product-category">Chairs</span>
                        <h3 class="product-title">Leather Accent Chair</h3>
                        <div class="product-price">₹45,000</div>
                        <a href="https://wa.me/919234772288?text=Hello,%20I'm%20interested%20in%20purchasing%20the%20Leather%20Accent%20Chair%20(₹45,000)" target="_blank" class="btn-add-cart"><i class="fa-brands fa-whatsapp" style="margin-right: 8px;"></i> Order on WhatsApp</a>
                    </div>
                </div>

                <!-- Product 4 -->
                <div class="product-card">
                    <div class="product-image">
                        <img src="assets/images/products/<?php
                            $files = glob('assets/images/products/furniture_bed_*.jpg');
                            echo $files ? basename($files[0]) : 'furniture_bed.jpg';
                        ?>" alt="Tufted Platform Bed">
                        <div class="product-overlay">
                            <a href="#" class="btn-quick-view" onclick="event.preventDefault(); alert('Product details mockup!');">Quick View</a>
                        </div>
                    </div>
                    <div class="product-info">
                        <span class="product-category">Beds</span>
                        <h3 class="product-title">Tufted Platform Bed</h3>
                        <div class="product-price">₹1,25,000</div>
                        <a href="https://wa.me/919234772288?text=Hello,%20I'm%20interested%20in%20purchasing%20the%20Tufted%20Platform%20Bed%20(₹1,25,000)" target="_blank" class="btn-add-cart"><i class="fa-brands fa-whatsapp" style="margin-right: 8px;"></i> Order on WhatsApp</a>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>
