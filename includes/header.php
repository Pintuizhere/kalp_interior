<?php 
require_once __DIR__ . '/../admin/config/db.php'; 

// Default SEO Tags
$defaultTitle = "Kalp Interior Design Studio | Best Interior Designer in Ranchi";
$defaultDescription = "Kalp Interior Design Studio offers professional residential and commercial interior design services in Ranchi, creating stylish and functional spaces.";

$seoTitle = isset($pageTitle) ? $pageTitle : $defaultTitle;
$seoDescription = isset($pageDescription) ? $pageDescription : $defaultDescription;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($seoTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($seoDescription); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    
    <!-- Base URL for relative links -->
    <?php
    // Define base URL dynamically to work on both local XAMPP and live Hostinger server
    $baseUrl = (strpos($_SERVER['SCRIPT_NAME'], '/kalp_interior/') === 0) ? '/kalp_interior/' : '/';
    ?>
    <base href="<?php echo $baseUrl; ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    
    <style>
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 26px;
            cursor: pointer;
            padding: 5px;
        }

        .mobile-only-li { display: none; }

        @media (max-width: 1199px) {
            .absolute-header .navbar {
                padding: 12px 20px !important;
                margin-top: 15px !important;
                border-radius: 40px !important;
                position: relative;
                z-index: 1002;
            }
            .mobile-menu-toggle {
                display: block !important;
            }
            .nav-links {
                position: absolute;
                top: calc(100% + 15px);
                left: 0;
                right: 0;
                width: 100%;
                height: auto;
                max-width: none;
                background: #111111;
                border-radius: 30px;
                border: 1px solid rgba(255, 255, 255, 0.1);
                display: flex !important;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 15px;
                transition: all 0.3s ease-in-out;
                z-index: 1001;
                box-shadow: 0 20px 40px rgba(0,0,0,0.6);
                margin: 0;
                padding: 40px 20px;
                
                /* Hidden state */
                opacity: 0;
                visibility: hidden;
                transform: translateY(-20px);
                pointer-events: none;
            }
            .nav-links.active {
                /* Active state */
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
                pointer-events: auto;
            }
            .nav-links li {
                width: 100%;
                text-align: center;
            }
            .nav-links a:not(.mobile-book-btn) {
                font-size: 20px;
                font-weight: 700;
                color: rgba(255, 255, 255, 0.9);
                display: block;
                padding: 12px;
                letter-spacing: 0.5px;
                text-transform: uppercase;
            }
            .nav-book-call-btn {
                display: none !important;
            }
            .close-btn-wrapper {
                display: none !important;
            }
            .mobile-only-li {
                display: block !important;
                width: 100%;
                margin-top: 15px;
            }
        }
        @media (max-width: 768px) {
            .absolute-header .container {
                padding: 0 15px !important;
            }
            .absolute-header .navbar {
                margin-top: 15px !important;
                border-radius: 40px !important;
                padding: 12px 20px !important;
                background: rgba(30, 30, 30, 0.8) !important;
                border: 1px solid rgba(255, 255, 255, 0.15) !important;
                position: relative;
            }
            .logo img {
                max-height: 25px !important;
            }
        }
        @media (min-width: 1200px) {
            .close-btn-wrapper {
                display: none;
            }
        }
        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .mobile-overlay.active {
            display: block;
            opacity: 1;
        }
    </style>
</head>
<body>

    <header class="absolute-header">
        <div class="container">

            
            <div class="mobile-overlay"></div>
            <nav class="navbar">
                <a href="index.php" class="logo" style="display: flex; align-items: center; text-decoration: none;">
                    <img src="assets/images/logo.png" alt="Kalp Interior Studio" style="max-height: 35px; width: auto; object-fit: contain;">
                </a>
                
                <ul class="nav-links">
                    <li class="close-btn-wrapper"><button class="nav-close-btn" aria-label="Close menu"><i class="fa-solid fa-xmark"></i></button></li>
                    <li><a href="index.php" class="<?php echo (isset($currentPage) && $currentPage == 'home') ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="about.php" class="<?php echo (isset($currentPage) && $currentPage == 'about') ? 'active' : ''; ?>">About Us</a></li>
                    <li><a href="services.php" class="<?php echo (isset($currentPage) && $currentPage == 'services') ? 'active' : ''; ?>">Services</a></li>
                    <li><a href="projects.php" class="<?php echo (isset($currentPage) && $currentPage == 'projects') ? 'active' : ''; ?>">Projects</a></li>
                    <li><a href="our-brand.php" class="<?php echo (isset($currentPage) && $currentPage == 'our-brand') ? 'active' : ''; ?>">Our Brand</a></li>
                    <li><a href="blog.php" class="<?php echo (isset($currentPage) && $currentPage == 'blog') ? 'active' : ''; ?>">Blog</a></li>
                    <li><a href="contact.php" class="<?php echo (isset($currentPage) && $currentPage == 'contact') ? 'active' : ''; ?>">Contact Us</a></li>
                    <li class="mobile-only-li">
                        <a href="calculator.php" class="mobile-book-btn" style="background: var(--accent-color); color: var(--text-dark); font-weight: 700; padding: 15px 30px; border-radius: 40px; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 16px; text-transform: uppercase;">
                            Get Estimate <i class="fa-solid fa-calculator" style="margin-left: 10px;"></i>
                        </a>
                    </li>
                </ul>
                
                <div class="nav-right" style="display: flex; align-items: center; gap: 15px;">
                    <a href="calculator.php" class="btn nav-book-call-btn" style="background: var(--accent-color); color: var(--text-dark); font-weight: 600; padding: 8px 8px 8px 24px; border-radius: 40px; display: inline-flex; align-items: center; gap: 15px; font-size: 16px;">
                        Get Estimate 
                        <span class="nav-btn-circle" style="background: var(--text-dark); color: var(--text-light); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-calculator nav-btn-arrow" style="font-size: 14px; transition: transform 0.3s ease;"></i>
                        </span>
                    </a>
                    <style>
                        .nav-book-call-btn:hover {
                            color: white !important;
                        }
                    </style>
                    
                    <button class="mobile-menu-toggle" aria-label="Toggle mobile menu">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </div>
            </nav>
        </div>
    </header>

    <script>
        (function() {
            const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
            const navLinks = document.querySelector('.nav-links');
            const navCloseBtn = document.querySelector('.nav-close-btn');
            const mobileOverlay = document.querySelector('.mobile-overlay');

            if (!mobileMenuToggle || !navLinks) return;

            function openMenu() {
                navLinks.classList.add('active');
                if (mobileOverlay) mobileOverlay.classList.add('active');
                mobileMenuToggle.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                document.body.style.overflow = 'hidden';
            }

            function closeMenu() {
                navLinks.classList.remove('active');
                if (mobileOverlay) mobileOverlay.classList.remove('active');
                mobileMenuToggle.innerHTML = '<i class="fa-solid fa-bars"></i>';
                document.body.style.overflow = '';
            }

            mobileMenuToggle.addEventListener('click', () => {
                if (navLinks.classList.contains('active')) {
                    closeMenu();
                } else {
                    openMenu();
                }
            });

            if (navCloseBtn) navCloseBtn.addEventListener('click', closeMenu);
            if (mobileOverlay) mobileOverlay.addEventListener('click', closeMenu);
        })();
    </script>
