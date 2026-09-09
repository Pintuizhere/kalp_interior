<aside class="sidebar">
    <div class="sidebar-header">
        <img src="../assets/images/logo.png" alt="Kalp Interior" class="sidebar-logo">
    </div>
    
    <div class="sidebar-menu-wrapper">
        <ul class="menu-list">
            <li>
                <a href="index.php" class="dashboard-link <?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-house"></i> Dashboard
                </a>
            </li>
        </ul>

        <p class="menu-label">MANAGEMENT</p>
        <ul class="menu-list">
            <li>
                <a href="projects.php" class="<?php echo ($currentPage == 'projects') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-briefcase"></i> Projects
                </a>
            </li>
            <li>
                <a href="services.php" class="<?php echo ($currentPage == 'services') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-layer-group"></i> Services
                </a>
            </li>
            <li>
                <a href="leads.php" class="<?php echo ($currentPage == 'leads') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users-line"></i> Leads
                </a>
            </li>
            <li>
                <a href="estimate_requests.php" class="<?php echo ($currentPage == 'estimate_requests') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Estimate Requests
                </a>
            </li>
            <li>
                <a href="testimonials.php" class="<?php echo ($currentPage == 'testimonials') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-star"></i> Testimonials
                </a>
            </li>
        </ul>
        
        <p class="menu-label">CALCULATOR</p>
        <ul class="menu-list">
            <li>
                <a href="calculator_settings.php" class="<?php echo ($currentPage == 'calculator') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-calculator"></i> Calculator Settings
                </a>
            </li>
            <li>
                <a href="free_numbers.php" class="<?php echo ($currentPage == 'free_numbers') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-phone-slash"></i> Free Numbers
                </a>
            </li>
            <li>
                <a href="security.php" class="<?php echo ($currentPage == 'security') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-shield-halved"></i> Security
                </a>
            </li>
        </ul>

        <p class="menu-label">E-COMMERCE</p>
        <ul class="menu-list">
            <li>
                <a href="manage_shop_users.php" class="<?php echo ($currentPage == 'shop_users') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users"></i> Shop Users
                </a>
            </li>
            <li>
                <a href="manage_shop_orders.php" class="<?php echo ($currentPage == 'shop_orders') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-cart-shopping"></i> Orders
                </a>
            </li>
            <li>
                <a href="manage_shop_products.php" class="<?php echo ($currentPage == 'shop_products') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-couch"></i> Products
                </a>
            </li>
            <li>
                <a href="manage_shop_categories.php" class="<?php echo ($currentPage == 'shop_categories') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-tags"></i> Shop Categories
                </a>
            </li>
            <li>
                <a href="manage_brand_collections.php" class="<?php echo ($currentPage == 'brand_collections') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-gem"></i> Brand Collections
                </a>
            </li>
        </ul>

        <p class="menu-label">CONTENT</p>
        <ul class="menu-list">
            <li>
                <a href="blog.php" class="<?php echo ($currentPage == 'blog') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-pen-to-square"></i> Blog Posts
                </a>
            </li>
            <li>
                <a href="manage_news_offers.php" class="<?php echo ($currentPage == 'news_offers') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-bullhorn"></i> News & Offers
                </a>
            </li>
            <li>
                <a href="categories.php" class="<?php echo ($currentPage == 'categories') ? 'active' : ''; ?>">
                    <i class="fa-regular fa-folder-open"></i> Categories
                </a>
            </li>
            <li>
                <a href="media.php" class="<?php echo ($currentPage == 'media') ? 'active' : ''; ?>">
                    <i class="fa-regular fa-image"></i> Media Library
                </a>
            </li>
            <li class="menu-label" style="margin-top: 15px; font-size: 11px; color: var(--text-muted); padding: 0 15px; font-weight: 600; letter-spacing: 1px;">FRONTEND PAGES</li>
            <li>
                <a href="editor-home.php" class="<?php echo ($currentPage == 'page_home') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-house"></i> Home Page
                </a>
            </li>
            <li>
                <a href="editor-about.php" class="<?php echo ($currentPage == 'editor_about') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users"></i> About Live Editor
                </a>
            </li>
            <li>
                <a href="manage_page.php?page=about" class="<?php echo ($currentPage == 'page_about') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users"></i> About Us
                </a>
            </li>
            <li>
                <a href="manage_team.php" class="<?php echo ($currentPage == 'manage_team') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-user-group"></i> Manage Team
                </a>
            </li>
            <li>
                <a href="stripe_logos.php" class="<?php echo ($currentPage == 'stripe_logos') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-rectangle-ad"></i> Stripe Logos
                </a>
            </li>
            <li>
                <a href="manage_before_after.php" class="<?php echo ($currentPage == 'manage_before_after') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-images"></i> Before & After
                </a>
            </li>
            <li>
                <a href="manage_awards.php" class="<?php echo ($currentPage == 'manage_awards') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-award"></i> Awards & Press
                </a>
            </li>
        </ul>

        <p class="menu-label">OTHERS</p>
        <ul class="menu-list">
            <li>
                <a href="users.php" class="<?php echo ($currentPage == 'users') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-user-shield"></i> Super Admin
                </a>
            </li>
            <li>
                <a href="settings.php" class="<?php echo ($currentPage == 'settings') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-gear"></i> Settings
                </a>
            </li>
            <li>
                <a href="notifications.php" class="<?php echo ($currentPage == 'notifications') ? 'active' : ''; ?>">
                    <i class="fa-regular fa-bell"></i> Notifications
                </a>
            </li>
            <li>
                <a href="profile.php" class="<?php echo ($currentPage == 'profile') ? 'active' : ''; ?>">
                    <i class="fa-regular fa-circle-user"></i> Profile
                </a>
            </li>
        </ul>

        <ul class="menu-list" style="margin-top: auto; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.05);">
            <li>
                <a href="logout.php" style="color: #ef4444; font-weight: 500;">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
                </a>
            </li>
        </ul>
    </div>
</aside>
