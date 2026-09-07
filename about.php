<?php 
$currentPage = 'about';
require_once 'admin/config/db.php';
$about_content = [];
$stmt = $conn->prepare("SELECT section_key, content_value FROM page_content WHERE page_name = 'about'");
$stmt->execute();
$result = $stmt->get_result();
while($row = $result->fetch_assoc()) {
    $about_content[$row['section_key']] = $row['content_value'];
}
$stmt->close();
include 'includes/header.php'; 
?>

<main>
    <!-- Page Banner -->
    <section class="page-banner">
        <div class="container">
            <h1 class="banner-title">About Us</h1>
            <div class="breadcrumbs">
                <a href="index.php">Home</a> <span class="divider">/</span> <span class="current">About Us</span>
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <?php include 'includes/components/our-story.php'; ?>

    <!-- About Bento Section -->
    <?php include 'includes/components/about-bento.php'; ?>

    <!-- About Content Section -->
    <?php include 'includes/components/about.php'; ?>

    <!-- How It Works Section -->
    <?php include 'includes/components/how-it-works.php'; ?>

    <!-- Core Principles (Vision, Mission, Values) -->
    <?php include 'includes/components/core-principles.php'; ?>

    <!-- Founder Section -->
    <?php include 'includes/components/founder.php'; ?>

    <!-- Meet Our Team Section -->
    <?php include 'includes/components/team.php'; ?>

    <!-- Awards & Press Section -->
    <?php include 'includes/components/awards.php'; ?>

    <!-- Trusted Partners Stripe -->
    <?php include 'includes/components/partners-stripe.php'; ?>





    <!-- CTA Section -->
    <section class="cta-section" style="padding: 100px 0; background-color: var(--bg-white); text-align: center;">
        <div class="container">
            <h2 style="font-size: 42px; margin-bottom: 50px;">The Dream Project: <span class="accent-text" style="font-weight: 400; color: var(--accent-color);">Your<br>Journey Begins Here!</span></h2>
            
            <a href="calculator.php" class="rotating-badge" style="display: inline-block; margin: 0 auto; position: relative; width: 150px; height: 150px; border-radius: 50%; background: var(--primary-color);">
                <svg viewBox="0 0 150 150" class="rotating-text" style="animation: rotate 15s linear infinite;">
                    <path id="badge-curve" d="M 25, 75 a 50,50 0 1,1 100,0 a 50,50 0 1,1 -100,0" fill="transparent" />
                    <text font-size="14" letter-spacing="3" fill="var(--text-light)" font-weight="500">
                        <textPath href="#badge-curve">GET ESTIMATE • GET ESTIMATE • </textPath>
                    </text>
                </svg>
                <div class="badge-icon" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: var(--accent-color); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary-color); font-size: 20px;">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </div>
            </a>
        </div>
    </section>



</main>

<?php include 'includes/footer.php'; ?>
