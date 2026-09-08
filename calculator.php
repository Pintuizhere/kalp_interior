<?php 
$currentPage = 'calculator';
$pageTitle = "Get an Interior Design Estimate | Kalp Interior Studio";
$pageDescription = "Use Kalp Interior Design Studio's online calculator to get a quick estimate for your residential or commercial interior design project in Ranchi.";
include 'includes/header.php'; 
?>

<main style="background-color: #334C40;"> <!-- Use a dark green background for this specific page to match the calculator design -->
    <!-- Page Banner -->
    <section class="page-banner">
        <div class="container">
            <h1 class="banner-title">Get Estimate</h1>
            <div class="breadcrumbs">
                <a href="index">Home</a> <span class="divider">/</span> <span class="current">Get Estimate</span>
            </div>
        </div>
    </section>

    <!-- Calculator Component -->
    <?php 
    $exclude_category = 'modular-kitchen';
    include 'includes/components/calculator.php'; 
    ?>
</main>

<?php include 'includes/footer.php'; ?>

