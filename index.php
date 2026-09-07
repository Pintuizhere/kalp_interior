<?php 
require_once 'admin/config/db.php';
$home_content = [];
$stmt = $conn->prepare("SELECT section_key, content_value FROM page_content WHERE page_name = 'home'");
$stmt->execute();
$result = $stmt->get_result();
while($row = $result->fetch_assoc()) {
    $home_content[$row['section_key']] = $row['content_value'];
}
$stmt->close();
include 'includes/header.php'; 
?>

<main>
<?php
    include 'includes/components/hero.php';
    include 'includes/components/partners-stripe.php';
    include 'includes/components/about.php';
    include 'includes/components/news-offers.php';
    include 'includes/components/projects-stripe.php';
    include 'includes/components/estimate.php';
    include 'includes/components/services.php';
    include 'includes/components/process.php';
    include 'includes/components/projects.php';
    include 'includes/components/before-after.php';
    include 'includes/components/end-to-end-services.php';
    include 'includes/components/testimonial.php';
    include 'includes/components/awards.php';
    include 'includes/components/faq.php';
    include 'includes/components/contact.php';
    include 'includes/components/floating-menu.php';
?>
</main>

<?php include 'includes/footer.php'; ?>
