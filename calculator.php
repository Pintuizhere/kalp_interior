<?php 
$currentPage = 'calculator';
$pageTitle = "Get an Interior Design Estimate | Kalp Interior Studio";
$pageDescription = "Use Kalp Interior Design Studio's online calculator to get a quick estimate for your residential or commercial interior design project in Ranchi.";
include 'includes/header.php'; 
?>

<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window,document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '4702202480051069');
fbq('track', 'PageView');
</script>
<noscript>
<img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=4702202480051069&ev=PageView&noscript=1"/>
</noscript>
<!-- End Facebook Pixel Code -->

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
    include 'includes/components/calculator.php'; 
    ?>
</main>

<?php include 'includes/footer.php'; ?>

