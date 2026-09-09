<?php 
$currentPage = 'shipping';
require_once 'admin/config/db.php';
include 'includes/header.php'; 
?>
<main>
    <section class="page-banner">
        <div class="container">
            <h1 class="banner-title">Shipping Information</h1>
            <div class="breadcrumbs">
                <a href="index">Home</a> <span class="divider">/</span> <span class="current">Shipping Information</span>
            </div>
        </div>
    </section>
    
    <section class="content-section" style="padding: 80px 0; background-color: var(--bg-light);">
        <div class="container">
            <div class="content-wrapper" style="background: var(--bg-white); padding: 50px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
                <h2 style="margin-bottom: 30px;">Shipping & Delivery Policy</h2>
                <p>We are committed to delivering your furniture and decor items in perfect condition and as quickly as possible. Please read our shipping policies below for details on delivery times, costs, and processes.</p>
                
                <h3 style="margin-top: 30px; margin-bottom: 15px;">1. Delivery Times</h3>
                <p>Most in-stock items are shipped within 2-3 business days. Depending on your location, standard delivery takes between 5 to 10 business days. Custom orders or out-of-stock items may take longer, and we will provide an estimated delivery date at the time of purchase.</p>
                
                <h3 style="margin-top: 30px; margin-bottom: 15px;">2. Shipping Costs</h3>
                <p>We offer free standard shipping on all orders over ₹10,000. For orders under this amount, shipping charges will be calculated and displayed at checkout based on the weight and dimensions of the items and your delivery location.</p>
                
                <h3 style="margin-top: 30px; margin-bottom: 15px;">3. White Glove Delivery</h3>
                <p>For large furniture items, we offer a White Glove Delivery service. This includes inside delivery, room of choice placement, assembly, and removal of all packaging materials. This premium service is available for an additional fee during checkout.</p>
            </div>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>
