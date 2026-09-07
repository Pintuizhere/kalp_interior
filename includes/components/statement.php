<!-- Statement Section -->
<style>
    @media (max-width: 768px) {
        .statement-section { padding: 60px 15px !important; }
        .statement-container { padding: 40px 20px !important; border-radius: 15px !important; }
        .statement-heading { font-size: 1.35rem !important; line-height: 1.7 !important; }
        .statement-capsule { 
            font-size: 1.6rem !important; 
            padding: 2px 12px !important; 
            margin: 5px !important; 
            transform: translateY(2px) !important;
        }
        .statement-quote { font-size: 1em !important; transform: none !important; margin: 0 5px !important; }
    }
</style>
<section class="statement-section" style="padding: 100px 20px; background-color: var(--bg-dark); text-align: center;">
    <div class="container statement-container" style="max-width: 1100px; margin: 0 auto; padding: 80px 40px; border: 1px solid var(--accent-color); border-radius: 20px; position: relative; overflow: hidden;">
        
        <!-- Background Image with Overlay -->
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(rgba(17, 17, 17, 0.9), rgba(17, 17, 17, 0.9)), url('https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80') center/cover no-repeat; z-index: 0;"></div>
        
        <!-- Dot Grids -->
        <div style="position: absolute; top: 40px; right: 40px; width: 80px; height: 60px; background-image: radial-gradient(var(--accent-color) 2px, transparent 2px); background-size: 15px 15px; opacity: 0.3; z-index: 1;"></div>
        <div style="position: absolute; bottom: 40px; left: 40px; width: 80px; height: 60px; background-image: radial-gradient(var(--accent-color) 2px, transparent 2px); background-size: 15px 15px; opacity: 0.3; z-index: 1;"></div>
        
        <!-- Content -->
        <div style="position: relative; z-index: 2;">
            <h2 class="statement-heading" style="font-family: 'Poppins', sans-serif; font-weight: 500; line-height: 1.6; text-transform: lowercase;">
                <i class="fa-solid fa-quote-left statement-quote" style="margin-right: 15px; font-size: 1.2em; vertical-align: text-top; transform: translateY(-10px);"></i>
                kalp interior is the <span style="color: var(--accent-color);">dedicated discipline</span> of design focused on 
                <span class="statement-capsule" style="background-image: url('https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'); border: 1px solid var(--accent-color); margin: 0 10px;">transforming</span> 
                spaces where life happens. we serve homeowners and developers who understand that a beautiful environment 
                <span class="statement-capsule" style="background-image: url('https://images.unsplash.com/photo-1513694203232-719a280e022f?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'); border: 1px solid var(--accent-color); margin: 0 10px;">elevates</span> 
                experience.<i class="fa-solid fa-quote-right statement-quote" style="margin-left: 15px; font-size: 1.2em; vertical-align: text-bottom; transform: translateY(10px);"></i>
            </h2>
            <div style="width: 60px; height: 2px; background-color: var(--accent-color); margin: 40px auto 0;"></div>
        </div>
    </div>
</section>
