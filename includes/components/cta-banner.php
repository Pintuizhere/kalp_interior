<!-- CTA Banner Section -->
<section class="cta-banner" style="position: relative; padding: 140px 20px; background-image: url('https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center; background-attachment: fixed; text-align: center; color: white;">
    <!-- Dark Overlay for Readability -->
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(17, 17, 17, 0.75); z-index: 1;"></div>
    
    <div class="container" style="position: relative; z-index: 2; max-width: 900px; margin: 0 auto;">
        <h2 style="font-family: var(--font-headline); font-size: 4.5rem; line-height: 1.1; font-weight: 800; text-transform: uppercase; margin-bottom: 25px; position: relative; z-index: 2;">
            <span class="beautiful-spaces-text" style="display: inline-block; font-family: var(--font-accent); color: var(--accent-color); text-transform: lowercase; font-weight: 700; z-index: 3; text-shadow: 1px 1px 2px rgba(0,0,0,0.2); transform: rotate(-5deg); margin-bottom: -25px;">beautiful spaces</span><br>
            <span style="position: relative; z-index: 1;">HAPPEN.<br>WE MAKE THEM YOURS.</span>
        </h2>
        
        <p style="font-size: 1.2rem; line-height: 1.6; opacity: 0.9; margin-bottom: 40px; max-width: 700px; margin-left: auto; margin-right: auto;">
            Book a free design consultation. We'll review your space and show you exactly where the potential lies—no pressure.
        </p>
        
        <div class="cta-buttons" style="display: flex; align-items: center; justify-content: center; gap: 30px; flex-wrap: wrap;">
            <a href="calculator.php" style="background: var(--accent-color); color: var(--text-dark); padding: 16px 32px; border-radius: 50px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 12px; transition: transform 0.3s ease, background 0.3s ease;">
                <i class="fa-solid fa-calculator"></i> Get estimate
            </a>
            
            <a href="contact.php" style="color: white; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 10px; transition: color 0.3s ease;">
                Book a free call 
                <span style="background: white; color: var(--text-dark); width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                    <i class="fa-solid fa-arrow-right"></i>
                </span>
            </a>
        </div>
    </div>
</section>

<style>
    .beautiful-spaces-text {
        font-size: 6rem;
    }
    
    /* Mobile Responsiveness for CTA Banner */
    @media (max-width: 768px) {
        .beautiful-spaces-text {
            font-size: 4rem !important;
            margin-bottom: -15px !important;
        }
        .cta-banner {
            padding: 100px 20px !important;
            background-attachment: scroll !important; /* Fix for iOS parallax bug */
        }
        .cta-banner h2 {
            font-size: 2.5rem !important;
        }
        .cta-banner p {
            font-size: 1.1rem !important;
        }
        .cta-banner .cta-buttons {
            gap: 20px !important;
            flex-direction: column;
        }
        .cta-banner .cta-buttons a {
            width: 100%;
            justify-content: center;
        }
    }
</style>
