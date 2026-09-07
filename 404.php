<?php 
// 404 Not Found Page
http_response_code(404);
include 'includes/header.php'; 
?>

<style>
    .error-section {
        padding: 150px 0;
        background-color: #f9f9f9;
        text-align: center;
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .error-content {
        max-width: 600px;
        margin: 0 auto;
        padding: 0 20px;
    }
    .error-code {
        font-size: 150px;
        font-weight: 800;
        color: var(--text-dark);
        line-height: 1;
        margin-bottom: 20px;
        position: relative;
        display: inline-block;
    }
    .error-code::after {
        content: '';
        position: absolute;
        bottom: 20px;
        right: -10px;
        width: 40px;
        height: 40px;
        background-color: var(--accent-color);
        border-radius: 50%;
        z-index: -1;
    }
    .error-title {
        font-size: 2.5rem;
        color: var(--text-dark);
        margin-bottom: 20px;
    }
    .error-desc {
        color: var(--text-muted);
        font-size: 1.1rem;
        margin-bottom: 40px;
        line-height: 1.6;
    }
    .back-home-btn {
        display: inline-flex;
        align-items: center;
        gap: 15px;
        background-color: var(--text-dark);
        color: white;
        padding: 15px 35px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .back-home-btn i {
        background-color: var(--accent-color);
        color: var(--text-dark);
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .back-home-btn:hover {
        background-color: var(--accent-color);
        color: var(--text-dark);
    }
    .back-home-btn:hover i {
        background-color: white;
    }
</style>

<main>
    <section class="error-section">
        <div class="container">
            <div class="error-content">
                <div class="error-code">404</div>
                <h1 class="error-title">Oops! Page Not Found</h1>
                <p class="error-desc">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable. Let's get you back on track.</p>
                <a href="index" class="back-home-btn">
                    <i class="fa-solid fa-house"></i> Back To Home
                </a>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
