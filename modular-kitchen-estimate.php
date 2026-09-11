<?php 
$currentPage = 'modular-kitchen-estimate';
include 'includes/header.php'; 
?>

<main style="background-color: #334C40;"> <!-- Match calculator design -->
    <!-- Page Banner -->
    <section class="page-banner">
        <div class="container">
            <h1 class="banner-title">Modular Kitchen Estimate</h1>
            <div class="breadcrumbs">
                <a href="index.php">Home</a> <span class="divider">/</span>

                <span class="current">Modular Kitchen Estimate</span>
            </div>
        </div>
    </section>

    <!-- Embedded Modular Kitchen Calculator -->
    <section id="kitchen-calculator" style="background-color: var(--primary-color);">
        <?php 
        $force_category = 'modular-kitchen';
        include 'includes/components/calculator.php'; 
        ?>
    </section>

</main>

<?php include 'includes/footer.php'; ?>
