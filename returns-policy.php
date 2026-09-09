<?php 
$currentPage = 'returns';
require_once 'admin/config/db.php';
include 'includes/header.php'; 
?>
<main>
    <section class="page-banner">
        <div class="container">
            <h1 class="banner-title">Returns Policy</h1>
            <div class="breadcrumbs">
                <a href="index">Home</a> <span class="divider">/</span> <span class="current">Returns Policy</span>
            </div>
        </div>
    </section>
    
    <section class="content-section" style="padding: 80px 0; background-color: var(--bg-light);">
        <div class="container">
            <div class="content-wrapper" style="background: var(--bg-white); padding: 50px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
                <h2 style="margin-bottom: 30px;">Returns & Refund Policy</h2>
                <p>At Kalp Interior Design Studio, we want you to be completely satisfied with your purchase. If you are not entirely happy with your furniture or home decor items, we're here to help.</p>
                
                <h3 style="margin-top: 30px; margin-bottom: 15px;">1. Returns</h3>
                <p>You have 30 calendar days to return an item from the date you received it. To be eligible for a return, your item must be unused, in the same condition that you received it, and in the original packaging. You will need to provide the receipt or proof of purchase.</p>
                
                <h3 style="margin-top: 30px; margin-bottom: 15px;">2. Refunds</h3>
                <p>Once we receive your item, we will inspect it and notify you that we have received your returned item. We will immediately notify you on the status of your refund after inspecting the item. If your return is approved, we will initiate a refund to your credit card (or original method of payment). You will receive the credit within a certain amount of days, depending on your card issuer's policies.</p>
                
                <h3 style="margin-top: 30px; margin-bottom: 15px;">3. Shipping Returns</h3>
                <p>You will be responsible for paying for your own shipping costs for returning your item. Shipping costs are non-refundable. If you receive a refund, the cost of return shipping will be deducted from your refund.</p>
            </div>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>
