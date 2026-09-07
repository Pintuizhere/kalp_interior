<style>
.pre-footer-cta {
    position: relative;
    background-color: #EAEAEA;
    padding: 120px 20px;
    text-align: center;
    overflow: hidden;
    font-family: var(--font-primary);
}

.pfc-badge {
    display: inline-block;
    background: white;
    color: var(--text-dark);
    padding: 8px 24px;
    border-radius: 30px;
    font-weight: 500;
    font-size: 15px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}

.pfc-heading {
    font-family: var(--font-headline);
    font-size: clamp(40px, 6vw, 72px);
    font-weight: 900;
    color: var(--text-dark);
    line-height: 0.95;
    margin-bottom: 25px;
    text-transform: uppercase;
    position: relative;
    z-index: 2;
    max-width: 900px;
    margin-left: auto;
    margin-right: auto;
    letter-spacing: -1px;
}

.pfc-subtitle {
    font-size: 18px;
    color: var(--text-muted);
    max-width: 600px;
    margin: 0 auto 40px auto;
    line-height: 1.5;
    position: relative;
    z-index: 2;
}

.pfc-actions {
    display: flex;
    gap: 20px;
    justify-content: center;
    align-items: center;
    position: relative;
    z-index: 2;
}

.pfc-btn-primary {
    background-color: var(--text-dark);
    color: white;
    padding: 16px 32px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    transition: var(--transition);
}

.pfc-btn-primary i {
    font-size: 14px;
}

.pfc-btn-primary:hover {
    background-color: var(--primary-color);
    transform: translateY(-2px);
    color: white;
}

.pfc-btn-secondary {
    color: var(--text-dark);
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    transition: var(--transition);
}

.pfc-btn-secondary:hover {
    color: var(--primary-color);
}

.pfc-btn-secondary .icon-circle {
    background: white;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 14px;
    transition: var(--transition);
}

.pfc-btn-secondary:hover .icon-circle {
    transform: translateX(4px);
    background: var(--text-dark);
    color: white;
}

/* Floating Images */
.pfc-floating-img {
    width: 100%;
    height: 100%;
    border-radius: 30px;
    object-fit: cover;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

/* Container for rotating elements to allow custom orbits */
.pfc-img-wrapper {
    position: absolute;
    z-index: 1;
    animation: floatSemiCircle ease-in-out infinite alternate;
}

@keyframes floatSemiCircle {
    0% { transform: translate(-30px, 15px); }
    50% { transform: translate(0px, -15px); }
    100% { transform: translate(30px, 15px); }
}

/* Positioning */
.pfc-wrap-1 { top: 15%; left: 15%; width: 150px; height: 150px; animation-duration: 8s; }
.pfc-wrap-2 { top: 5%; left: 38%; width: 120px; height: 120px; animation-duration: 12s; animation-delay: -2s; }
.pfc-wrap-3 { top: 10%; right: 38%; width: 130px; height: 130px; animation-duration: 9s; animation-delay: -4s; }
.pfc-wrap-4 { top: 22%; right: 12%; width: 160px; height: 160px; animation-duration: 11s; animation-delay: -6s; }
.pfc-wrap-5 { bottom: 15%; left: 8%; width: 140px; height: 140px; animation-duration: 10s; animation-delay: -3s; }
.pfc-wrap-6 { bottom: 10%; right: 10%; width: 150px; height: 150px; animation-duration: 13s; animation-delay: -1s; }

@media (max-width: 991px) {
    .pfc-img-wrapper { display: none; }
    .pfc-heading { font-size: 32px; }
}
</style>

<section class="pre-footer-cta">
    <!-- Floating Images using Unsplash Interior design placeholders -->
    <div class="pfc-img-wrapper pfc-wrap-1">
        <img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=400&q=80" alt="Interior Details" class="pfc-floating-img">
    </div>
    <div class="pfc-img-wrapper pfc-wrap-2">
        <img src="https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=400&q=80" alt="Interior Details" class="pfc-floating-img">
    </div>
    <div class="pfc-img-wrapper pfc-wrap-3">
        <img src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=400&q=80" alt="Interior Details" class="pfc-floating-img">
    </div>
    <div class="pfc-img-wrapper pfc-wrap-4">
        <img src="https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=400&q=80" alt="Interior Details" class="pfc-floating-img">
    </div>
    <div class="pfc-img-wrapper pfc-wrap-5">
        <img src="https://images.unsplash.com/photo-1600607686527-6fb886090705?auto=format&fit=crop&w=400&q=80" alt="Interior Details" class="pfc-floating-img">
    </div>
    <div class="pfc-img-wrapper pfc-wrap-6">
        <img src="https://images.unsplash.com/photo-1615529182904-14819c35db37?auto=format&fit=crop&w=400&q=80" alt="Interior Details" class="pfc-floating-img">
    </div>

    <div class="pfc-badge">Ready?</div>
    <h2 class="pfc-heading"><span style="font-family: var(--font-accent); color: var(--accent-color); text-transform: lowercase; font-weight: bold;">beautiful spaces</span> HAPPEN.<br>WE MAKE THEM YOURS.</h2>
    <p class="pfc-subtitle">Book a free design consultation. We'll review your space and show you exactly where the potential lies—no pressure.</p>
    
    <div class="pfc-actions">
        <a href="contact.php" class="pfc-btn-primary">
            <i class="fa-regular fa-calendar"></i> Book a free call
        </a>
        <a href="projects.php" class="pfc-btn-secondary">
            See our work first <span class="icon-circle"><i class="fa-solid fa-arrow-right"></i></span>
        </a>
    </div>
</section>
