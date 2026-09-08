<?php
require_once __DIR__ . '/../admin/config/db.php';

$queries = [
    // 1. Categories
    "CREATE TABLE IF NOT EXISTS shop_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        image_url VARCHAR(255) NULL,
        type ENUM('category', 'room') DEFAULT 'category',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    // 2. Products
    "CREATE TABLE IF NOT EXISTS shop_products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NULL,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        short_desc TEXT NULL,
        regular_price DECIMAL(10,2) NOT NULL,
        sale_price DECIMAL(10,2) NULL,
        sku VARCHAR(100) NULL,
        rating DECIMAL(3,2) DEFAULT 5.0,
        reviews_count INT DEFAULT 0,
        stock_status ENUM('instock', 'outofstock') DEFAULT 'instock',
        is_featured BOOLEAN DEFAULT FALSE,
        is_bestseller BOOLEAN DEFAULT FALSE,
        is_new_arrival BOOLEAN DEFAULT FALSE,
        is_on_sale BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES shop_categories(id) ON DELETE SET NULL
    )",

    // 3. Product Images
    "CREATE TABLE IF NOT EXISTS shop_product_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        image_url VARCHAR(255) NOT NULL,
        is_primary BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (product_id) REFERENCES shop_products(id) ON DELETE CASCADE
    )",

    // 4. Orders
    "CREATE TABLE IF NOT EXISTS shop_orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_number VARCHAR(100) NOT NULL UNIQUE,
        customer_name VARCHAR(255) NOT NULL,
        customer_email VARCHAR(255) NULL,
        customer_phone VARCHAR(50) NOT NULL,
        billing_address TEXT NOT NULL,
        shipping_address TEXT NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL,
        discount DECIMAL(10,2) DEFAULT 0,
        shipping_charges DECIMAL(10,2) DEFAULT 0,
        total_amount DECIMAL(10,2) NOT NULL,
        payment_method VARCHAR(100) NOT NULL,
        status ENUM('Pending', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    // 5. Order Items
    "CREATE TABLE IF NOT EXISTS shop_order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NULL,
        product_name VARCHAR(255) NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES shop_orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES shop_products(id) ON DELETE SET NULL
    )"
];

foreach ($queries as $index => $query) {
    if ($conn->query($query) === TRUE) {
        echo "Table " . ($index + 1) . " created successfully.\n";
    } else {
        echo "Error creating table " . ($index + 1) . ": " . $conn->error . "\n";
    }
}

// Insert some dummy data if empty
$checkCat = $conn->query("SELECT COUNT(*) as count FROM shop_categories");
$row = $checkCat->fetch_assoc();
if ($row['count'] == 0) {
    // Insert Categories
    $conn->query("INSERT INTO shop_categories (name, slug, type) VALUES 
        ('Sofas', 'sofas', 'category'),
        ('Beds', 'beds', 'category'),
        ('Dining Table', 'dining-table', 'category'),
        ('Chairs', 'chairs', 'category'),
        ('Wardrobes', 'wardrobes', 'category'),
        ('TV Units', 'tv-units', 'category'),
        ('Coffee Tables', 'coffee-tables', 'category'),
        ('Office Furniture', 'office-furniture', 'category'),
        ('Living Room', 'living-room', 'room'),
        ('Bedroom', 'bedroom', 'room'),
        ('Dining Room', 'dining-room', 'room'),
        ('Home Office', 'home-office', 'room')
    ");
    echo "Dummy categories inserted.\n";

    // Insert Products
    $conn->query("INSERT INTO shop_products (category_id, name, slug, regular_price, sale_price, sku, rating, reviews_count, is_bestseller, is_on_sale) VALUES 
        (1, 'Modern Fabric Sofa', 'modern-fabric-sofa', 31999, 24999, 'SOFA-3B-001', 4.8, 124, 1, 1),
        (2, 'Queen Size Bed', 'queen-size-bed', 33499, 28499, 'BED-Q-001', 4.9, 98, 1, 1),
        (3, '6 Seater Dining Table', '6-seater-dining-table', 39999, 32999, 'DT-6S-001', 4.7, 76, 1, 1),
        (4, 'Accent Chair', 'accent-chair', 13999, 12499, 'CH-AC-001', 4.6, 54, 1, 1),
        (5, 'Wooden Wardrobe', 'wooden-wardrobe', 21499, 18999, 'WD-W-001', 4.8, 89, 1, 1)
    ");
    echo "Dummy products inserted.\n";

    // Get product IDs to insert images
    $result = $conn->query("SELECT id, slug FROM shop_products");
    while($prod = $result->fetch_assoc()) {
        $img = 'furniture_' . str_replace('-', '_', $prod['slug']) . '.jpg';
        // Just generic placeholder matching
        $conn->query("INSERT INTO shop_product_images (product_id, image_url, is_primary) VALUES (".$prod['id'].", 'furniture_sofa.jpg', 1)");
    }
    echo "Dummy images inserted.\n";
}

$conn->close();
?>
