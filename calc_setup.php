<?php
require_once 'admin/config/db.php';

$queries = [
    "CREATE TABLE IF NOT EXISTS `calc_categories` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) NOT NULL,
      `slug` varchar(100) NOT NULL,
      `icon` varchar(100) NOT NULL,
      `status` tinyint(1) DEFAULT 1,
      PRIMARY KEY (`id`)
    )",
    "CREATE TABLE IF NOT EXISTS `calc_types` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `category_slug` varchar(100) NOT NULL,
      `name` varchar(100) NOT NULL,
      `icon` varchar(100) NOT NULL,
      `sqft` int(11) NOT NULL,
      `status` tinyint(1) DEFAULT 1,
      PRIMARY KEY (`id`)
    )",
    "CREATE TABLE IF NOT EXISTS `calc_styles` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) NOT NULL,
      `icon` varchar(100) NOT NULL,
      `percent_value` decimal(5,2) NOT NULL,
      `status` tinyint(1) DEFAULT 1,
      PRIMARY KEY (`id`)
    )",
    "CREATE TABLE IF NOT EXISTS `calc_packages` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `category_slug` varchar(100) NOT NULL,
      `name` varchar(100) NOT NULL,
      `price_per_sqft` int(11) NOT NULL,
      `icon_svg` text,
      `description` text,
      `pdf_specs` text,
      `status` tinyint(1) DEFAULT 1,
      PRIMARY KEY (`id`)
    )",
    "CREATE TABLE IF NOT EXISTS `calc_addons` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) NOT NULL,
      `percent_value` decimal(5,2) NOT NULL,
      `status` tinyint(1) DEFAULT 1,
      PRIMARY KEY (`id`)
    )"
];

foreach ($queries as $q) {
    if (!$conn->query($q)) {
        echo "Error: " . $conn->error . "\n";
    }
}

// Truncate to avoid duplicates on re-run
$conn->query("TRUNCATE TABLE calc_categories");
$conn->query("TRUNCATE TABLE calc_types");
$conn->query("TRUNCATE TABLE calc_styles");
$conn->query("TRUNCATE TABLE calc_packages");
$conn->query("TRUNCATE TABLE calc_addons");

// Insert Data
$conn->query("INSERT INTO calc_categories (name, slug, icon) VALUES 
('Residential', 'residential', 'fa-solid fa-house'),
('Commercial', 'commercial', 'fa-solid fa-building'),
('Modular Kitchen', 'kitchen', 'fa-solid fa-kitchen-set')");

$conn->query("INSERT INTO calc_types (category_slug, name, icon, sqft) VALUES 
('residential', '1 BHK', 'fa-solid fa-house-chimney', 700),
('residential', '2 BHK', 'fa-solid fa-building', 1200),
('residential', '3 BHK', 'fa-solid fa-building-user', 1350),
('commercial', 'Office', 'fa-solid fa-briefcase', 1000),
('commercial', 'Retail Shop', 'fa-solid fa-store', 1500),
('commercial', 'Restaurant', 'fa-solid fa-utensils', 2000),
('commercial', 'Clinic', 'fa-solid fa-stethoscope', 800)");

$conn->query("INSERT INTO calc_styles (name, icon, percent_value) VALUES 
('Minimalist', 'fa-solid fa-leaf', -8.00),
('Scandinavian', 'fa-solid fa-snowflake', -5.00),
('Contemporary', 'fa-solid fa-chair', 0.00),
('Modern', 'fa-solid fa-couch', 8.00),
('Traditional', 'fa-solid fa-chess-rook', 10.00),
('Boho', 'fa-solid fa-campground', 15.00),
('Japandi', 'fa-brands fa-pagelines', 20.00)");

// Addons
$conn->query("INSERT INTO calc_addons (name, percent_value) VALUES 
('Civil work', 8.00),
('Flooring', 10.00),
('Curtain/Soft Furnishing', 4.00)");

// Let's migrate Packages and PDFs from calculator_settings table
$pkg_essential_price = $conn->query("SELECT setting_value FROM calculator_settings WHERE setting_key='pkg_essential_price'")->fetch_assoc()['setting_value'] ?? 1200;
$pkg_premium_price = $conn->query("SELECT setting_value FROM calculator_settings WHERE setting_key='pkg_premium_price'")->fetch_assoc()['setting_value'] ?? 1450;
$pkg_luxury_price = $conn->query("SELECT setting_value FROM calculator_settings WHERE setting_key='pkg_luxury_price'")->fetch_assoc()['setting_value'] ?? 1650;

$k_pkg_essential_price = $conn->query("SELECT setting_value FROM calculator_settings WHERE setting_key='k_pkg_essential_price'")->fetch_assoc()['setting_value'] ?? 9500;
$k_pkg_premium_price = $conn->query("SELECT setting_value FROM calculator_settings WHERE setting_key='k_pkg_premium_price'")->fetch_assoc()['setting_value'] ?? 13000;
$k_pkg_luxury_price = $conn->query("SELECT setting_value FROM calculator_settings WHERE setting_key='k_pkg_luxury_price'")->fetch_assoc()['setting_value'] ?? 18000;

$pdf_specs_essential = $conn->query("SELECT setting_value FROM calculator_settings WHERE setting_key='pdf_specs_essential'")->fetch_assoc()['setting_value'] ?? '';
$pdf_specs_premium = $conn->query("SELECT setting_value FROM calculator_settings WHERE setting_key='pdf_specs_premium'")->fetch_assoc()['setting_value'] ?? '';
$pdf_specs_luxury = $conn->query("SELECT setting_value FROM calculator_settings WHERE setting_key='pdf_specs_luxury'")->fetch_assoc()['setting_value'] ?? '';

// Insert Packages
$stmt = $conn->prepare("INSERT INTO calc_packages (category_slug, name, price_per_sqft, icon_svg, description, pdf_specs) VALUES (?, ?, ?, ?, ?, ?)");

// Residential / Commercial packages
$essential_svg = '<svg width="32" height="32" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 0; margin-right: 15px; flex-shrink: 0;"><path d="M 15 20 L 50 10 L 85 20 L 85 50 C 85 75 50 90 50 90 C 50 90 15 75 15 50 Z" fill="#E67E22" stroke="#0F3D64" stroke-width="4" stroke-linejoin="round"/><path d="M 50 10 L 85 20 L 85 50 C 85 75 50 90 50 90 Z" fill="#D35400"/><path d="M 15 20 L 50 10 L 85 20 L 85 50 C 85 75 50 90 50 90 C 50 90 15 75 15 50 Z" fill="none" stroke="#0F3D64" stroke-width="4" stroke-linejoin="round"/><path d="M 35 50 L 45 60 L 65 40" fill="none" stroke="#FFF" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$premium_svg = '<svg width="32" height="32" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 0; margin-right: 15px; flex-shrink: 0;"><polygon points="20,25 35,40 5,40" fill="#E8DAEF"/><polygon points="20,25 50,25 35,40" fill="#C39BD3"/><polygon points="35,40 50,25 65,40" fill="#E8DAEF"/><polygon points="50,25 80,25 65,40" fill="#9B59B6"/><polygon points="80,25 95,40 65,40" fill="#6C3483"/><polygon points="5,40 35,40 50,80" fill="#C39BD3"/><polygon points="35,40 65,40 50,80" fill="#9B59B6"/><polygon points="65,40 95,40 50,80" fill="#6C3483"/><g stroke="#0F3D64" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polygon points="20,25 80,25 95,40 50,80 5,40" fill="none"/><line x1="5" y1="40" x2="95" y2="40"/><polyline points="20,25 35,40 50,25 65,40 80,25" fill="none"/><line x1="35" y1="40" x2="50" y2="80"/><line x1="65" y1="40" x2="50" y2="80"/></g></svg>';
$luxury_svg = '<svg width="32" height="32" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 0; margin-right: 15px; flex-shrink: 0;"><path d="M 32 38 L 22 12 L 38 24 L 50 6 L 62 24 L 78 12 L 68 38 Z" fill="#F4B41A" stroke="#7A5214" stroke-width="4" stroke-linejoin="round"/><polygon points="20,40 35,55 5,55" fill="#C5E1F5"/><polygon points="20,40 50,40 35,55" fill="#8ECAE6"/><polygon points="35,55 50,40 65,55" fill="#C5E1F5"/><polygon points="50,40 80,40 65,55" fill="#4B95C4"/><polygon points="80,40 95,55 65,55" fill="#28699E"/><polygon points="5,55 35,55 50,95" fill="#8ECAE6"/><polygon points="35,55 65,55 50,95" fill="#4B95C4"/><polygon points="65,55 95,55 50,95" fill="#28699E"/><g stroke="#0F3D64" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polygon points="20,40 80,40 95,55 50,95 5,55" fill="none"/><line x1="5" y1="55" x2="95" y2="55"/><polyline points="20,40 35,55 50,40 65,55 80,40" fill="none"/><line x1="35" y1="55" x2="50" y2="95"/><line x1="65" y1="55" x2="50" y2="95"/></g></svg>';

$c = 'standard'; // 'standard' applies to both residential and commercial
$name = 'Essential';
$desc = '';
$stmt->bind_param("ssisss", $c, $name, $pkg_essential_price, $essential_svg, $desc, $pdf_specs_essential);
$stmt->execute();

$name = 'Premium';
$stmt->bind_param("ssisss", $c, $name, $pkg_premium_price, $premium_svg, $desc, $pdf_specs_premium);
$stmt->execute();

$name = 'Luxury';
$stmt->bind_param("ssisss", $c, $name, $pkg_luxury_price, $luxury_svg, $desc, $pdf_specs_luxury);
$stmt->execute();

// Kitchen packages
$c = 'kitchen';
$desc_ess = '<ul style="font-size: 11px; padding-left: 15px; opacity: 0.9; margin-bottom: 0;"><li style="margin-bottom: 5px;"><strong>Plywood:</strong> BWR Grade</li><li style="margin-bottom: 5px;"><strong>Shutters:</strong> 0.8 mm laminate</li><li style="margin-bottom: 5px;"><strong>Edge Band:</strong> PVC</li><li style="margin-bottom: 5px;"><strong>Back Panel:</strong> 6 mm</li><li style="margin-bottom: 5px;"><strong>Hardware:</strong> Standard</li><li style="margin-bottom: 5px;"><strong>Handles:</strong> SS profile</li></ul>';
$name = 'Essential Kitchen';
$stmt->bind_param("ssisss", $c, $name, $k_pkg_essential_price, $desc, $desc_ess, $desc);
$stmt->execute();

$desc_prem = '<ul style="font-size: 11px; padding-left: 15px; opacity: 0.9; margin-bottom: 0;"><li style="margin-bottom: 5px;"><strong>Plywood:</strong> BWP Grade</li><li style="margin-bottom: 5px;"><strong>Shutters:</strong> Premium laminate</li><li style="margin-bottom: 5px;"><strong>Edge Band:</strong> 1 mm PVC</li><li style="margin-bottom: 5px;"><strong>Back Panel:</strong> 6 mm BWP</li><li style="margin-bottom: 5px;"><strong>Hardware:</strong> Soft-close</li><li style="margin-bottom: 5px;"><strong>Handles:</strong> G-profile</li></ul>';
$name = 'Premium Kitchen';
$stmt->bind_param("ssisss", $c, $name, $k_pkg_premium_price, $desc, $desc_prem, $desc);
$stmt->execute();

$desc_lux = '<ul style="font-size: 11px; padding-left: 15px; opacity: 0.9; margin-bottom: 0;"><li style="margin-bottom: 5px;"><strong>Plywood:</strong> 710 Grade BWP</li><li style="margin-bottom: 5px;"><strong>Shutters:</strong> Acrylic/PU</li><li style="margin-bottom: 5px;"><strong>Edge Band:</strong> Rehau</li><li style="margin-bottom: 5px;"><strong>Back Panel:</strong> 6 mm 710</li><li style="margin-bottom: 5px;"><strong>Hardware:</strong> Blum</li><li style="margin-bottom: 5px;"><strong>Handles:</strong> G-profile</li></ul>';
$name = 'Luxury Kitchen';
$stmt->bind_param("ssisss", $c, $name, $k_pkg_luxury_price, $desc, $desc_lux, $desc);
$stmt->execute();

echo "Done\n";
?>
